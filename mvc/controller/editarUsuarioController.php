<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/LogsModel.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$log = new LogsModel();

//---- FUNCIONES ----//

function camposCambiados($datosUsuarioSeleccionado) {
    $camposCambiados = null;
    if (isset($_FILES['examinar']) && $_FILES['examinar']['error'] === UPLOAD_ERR_OK) {
        $foto_nueva = file_get_contents($_FILES['examinar']['tmp_name']);
        if (base64_encode($foto_nueva) !== $datosUsuarioSeleccionado['foto']) 
            $camposCambiados['foto'] = base64_encode($foto_nueva);
    }
    if (isset($_POST['claveNueva']) && (hash('sha256',rtrim($_POST['claveNueva'])) != $datosUsuarioSeleccionado['password']) && ($_POST['claveNueva'] != '')) {
        $camposCambiados['password'] = rtrim($_POST['claveNueva']);
    }

        
    if (rtrim($_POST['nombre']) != $datosUsuarioSeleccionado['nombre'] && $_POST['nombre'] != '')
        $camposCambiados['nombre'] = rtrim($_POST['nombre']);
    if (rtrim($_POST['apellidos']) != $datosUsuarioSeleccionado['apellidos'])
        $camposCambiados['apellidos'] = rtrim($_POST['apellidos']);
    if (rtrim($_POST['email']) != $datosUsuarioSeleccionado['email'] && !empty($_POST['email']))
        $camposCambiados['email'] = rtrim($_POST['email']);
    if (rtrim($_POST['direccion']) != $datosUsuarioSeleccionado['direccion'])
        $camposCambiados['direccion'] = rtrim($_POST['direccion']);
    if (rtrim($_POST['telefono']) != $datosUsuarioSeleccionado['telefono'])
        $camposCambiados['telefono'] = rtrim($_POST['telefono']);
    if (rtrim(strtolower($_POST['rol'])) != $datosUsuarioSeleccionado['rol']) 
        $camposCambiados['rol'] = rtrim(strtolower($_POST['rol']));  
    if (rtrim(strtolower($_POST['estado'])) != $datosUsuarioSeleccionado['estado'])
        $camposCambiados['estado'] = rtrim(strtolower($_POST['estado'])); 
        
    return $camposCambiados;
}

function comprobarFallos(&$campos) {
    $fallos = false;
    if (isset($campos['email']) && !filter_var($campos['email'], FILTER_VALIDATE_EMAIL)) {
        $campos['email'] = 'incorrecto';
        $fallos = true;
    }
    if (isset($campos['telefono']) && !preg_match("/^(\+34)?[ -]?(6|7|9)([0-9]){8}$/", $campos['telefono']) && $campos['telefono'] != '') {
        $campos['telefono'] = 'incorrecto';
        $fallos = true;
    }

    if ((!empty($_POST['claveConfirmacion']) && !isset($campos['password'])) ||
        (isset($campos['password']) && empty($_POST['claveConfirmacion'])) ||
        (isset($campos['password']) && $campos['password'] != $_POST['claveConfirmacion']) ||
        (isset($campos['password']) && strlen($campos['password']) < 8)) {
        $campos['password'] = 'mal';
        $fallos = true;
    }
    return $fallos;

}

function addToDb($usuario, $datosUsuarioSeleccionado, $campos , $log) {
    // Si es un campo como foto o contraseña se modifica de una forma especial
    foreach ($campos as $clave => $valor) {
        if ($clave != 'email') {
            $usuario->setDatos($datosUsuarioSeleccionado['email'], $clave, $valor);
            $datosUsuarioSeleccionado[$clave] = $campos[$clave];
        }
    }
    // Si es email se modifica otros campos
    if (isset($campos['email'])) {
        $usuario->setDatos($datosUsuarioSeleccionado['email'], 'email', $campos['email']);
        $datosUsuarioSeleccionado['email'] = $campos['email'];
    }

    $log->setEditarUsuario(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $datosUsuarioSeleccionado['email']);
}

//-------------------//


//Obtener datos de la persona a modificar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$datosUsuarioSeleccionado = '';
if ($usuario->existeUsuarioById($params['email'])) {
    $datosUsuarioSeleccionado = $usuario->get($params['email']);
    $datosUsuarioSeleccionado['nombreCompleto'] = $datosUsuarioSeleccionado['nombre'] . " " . $datosUsuarioSeleccionado['apellidos'];
}


//Si no es administrador o el usuario a editar no puede hacer edicion de su ficha
if ($_SESSION['datosUsuario']['rol'] == 'anonimo' ||
    ($_SESSION['datosUsuario']['email']) != $params['email'] && $_SESSION['datosUsuario']['rol'] != 'administrador') {
    header('Location: inicioController.php');
}

//Si se ha dado a modificar usuario
$confirmacion = null;
$camposCambiados = null;
$datosNuevos = $datosUsuarioSeleccionado;
$archivoRender = 'editarUsuario.html';
if (isset($_POST['modificarUsuario']) ) {
    //Comporobar si se han modificado campos
    $camposCambiados = camposCambiados($datosUsuarioSeleccionado);
    $fallos = comprobarFallos($camposCambiados);

    if ($camposCambiados != null) {  
        foreach ($datosUsuarioSeleccionado as $clave1 => $valor1) {
            foreach ($camposCambiados as $clave2 => $valor2) 
                if ($clave1 == $clave2)
                    $datosNuevos[$clave2] = $valor2;             

            if (!isset($datosNuevos[$clave1]))
                $datosNuevos[$clave1] = $valor1; 
        }

        if ($fallos) {
            $confirmacion = false;
        } else {
            $confirmacion = true;
            addToDb($usuario, $datosUsuarioSeleccionado, $camposCambiados, $log);

            $datosNuevos['nombreCompleto'] = $datosNuevos['nombre'] . " " . $datosNuevos['apellidos'];
            if ($datosUsuarioSeleccionado['email'] == $_SESSION['datosUsuario']['email']) 
                $_SESSION['datosUsuario'] = $datosNuevos;
        }
    }
    else {
        $confirmacion = true;
    }
}
else if (isset($_POST['confirmarModificacionUsuario'])) {
    $archivoRender = 'confirmacionesUsuario.html';
}

echo $twig->render($archivoRender, [
    'css' => '../view/css/editarUsuario.css',
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosNuevos' => $datosNuevos,
    'tipo' => 'editar',
    'confirmacion' =>$confirmacion
]);
?>