<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();


//---- FUNCIONES ----//

function camposCambiados($datosUsuarioSeleccionado) {
    $camposCambiados = null;
    if (isset($_FILES['examinar']) && $_FILES['examinar']['error'] === UPLOAD_ERR_OK) {
        $foto_nueva = file_get_contents($_FILES['examinar']['tmp_name']);
        if (base64_encode($foto_nueva) !== $datosUsuarioSeleccionado['foto']) 
            $camposCambiados['foto'] = base64_encode($foto_nueva);
    }
    if (rtrim($_POST['nombre']) != $datosUsuarioSeleccionado['nombre'] && $_POST['nombre'] != '')
        $camposCambiados['nombre'] = rtrim($_POST['nombre']);
    if (rtrim($_POST['apellidos']) != $datosUsuarioSeleccionado['apellidos'])
        $camposCambiados['apellidos'] = rtrim($_POST['apellidos']);
    if (rtrim($_POST['email']) != $datosUsuarioSeleccionado['email'])
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
    return $fallos;

}

function addToDb($usuario, $datosUsuarioSeleccionado, $campos) {
    foreach ($campos as $clave => $valor) {
        if ($clave != 'email') {
            $usuario->setDatos($datosUsuarioSeleccionado['email'], $clave, $valor);
            $datosUsuarioSeleccionado[$clave] = $campos[$clave];
        }
    }
    if (isset($campos['email'])) {
        $usuario->setDatos($datosUsuarioSeleccionado['email'], 'email', $campos['email']);
        $datosUsuarioSeleccionado['email'] = $campos['email'];
    }
}

//-------------------//

//Obtener datos de la persona a modificar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$datosUsuarioSeleccionado = $usuario->get($params['email']);
$datosUsuarioSeleccionado['nombreCompleto'] = $datosUsuarioSeleccionado['nombre'] . " " . $datosUsuarioSeleccionado['apellidos'];

//Si se ha dado a modificar usuario
$confirmacion = null;
$camposCambiados = null;
$datosNuevos = $datosUsuarioSeleccionado;
if (isset($_POST['modificarUsuario']) ) {
    //Comporobar si se han modificado campos
    $camposCambiados = camposCambiados($datosUsuarioSeleccionado);
    $fallos = comprobarFallos($camposCambiados);
    if ($camposCambiados != null)
        echo "SE HAN CAMBIADO CAMPOS";

    $datosNuevos = null;
    if ($camposCambiados != null) {
        $fallos = comprobarFallos($camposCambiados);

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
            addToDb($usuario, $datosUsuarioSeleccionado, $camposCambiados);

            $datosNuevos['nombreCompleto'] = $datosNuevos['nombre'] . " " . $datosNuevos['apellidos'];
            if ($datosUsuarioSeleccionado['email'] == $_SESSION['datosUsuario']['email']) 
                $_SESSION['datosUsuario'] = $datosNuevos;
        }
    }
    else {
        $confirmacion = true;
    }
}


echo $twig->render('editarUsuario.html', [
    'css' => '../view/css/editarUsuario.css',
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosNuevos' => $datosNuevos,
    'confirmacion' =>$confirmacion
]);
?>