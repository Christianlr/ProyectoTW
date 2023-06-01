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

function camposCambiados($POST) {
    $camposCambiados = null;
    if (isset($_FILES['examinar']) && $_FILES['examinar']['error'] === UPLOAD_ERR_OK) {
        $foto_nueva = file_get_contents($_FILES['examinar']['tmp_name']);
        if (base64_encode($foto_nueva) !== $_SESSION['datosUsuario']['foto']) 
            $camposCambiados['foto'] = base64_encode($foto_nueva);
    }
    if (rtrim($POST['nombre']) != $_SESSION['datosUsuario']['nombre'] && $POST['nombre'] != '')
        $camposCambiados['nombre'] = rtrim($POST['nombre']);
    if (rtrim($POST['apellidos']) != $_SESSION['datosUsuario']['apellidos'])
        $camposCambiados['apellidos'] = rtrim($POST['apellidos']);
    if (rtrim($POST['email']) != $_SESSION['datosUsuario']['email'])
        $camposCambiados['email'] = rtrim($POST['email']);
    if (rtrim($POST['direccion']) != $_SESSION['datosUsuario']['direccion'])
        $camposCambiados['direccion'] = rtrim($_POST['direccion']);
    if (rtrim($POST['telefono']) != $_SESSION['datosUsuario']['telefono'])
        $camposCambiados['telefono'] = rtrim($POST['telefono']);
    if (rtrim(strtolower($POST['rol'])) != $_SESSION['datosUsuario']['rol']) 
        $camposCambiados['rol'] = rtrim(strtolower($POST['rol']));  
    if (rtrim(strtolower($POST['estado'])) != $_SESSION['datosUsuario']['estado'])
        $camposCambiados['estado'] = rtrim(strtolower($POST['estado'])); 
        
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

function addToDb($usuario, $campos) {
    foreach ($campos as $clave => $valor) {
        if ($clave != 'email') {
            $usuario->setDatos($_SESSION['datosUsuario']['email'], $clave, $valor);
            $_SESSION['datosUsuario'][$clave] = $campos[$clave];
        }
    }
    if (isset($campos['email'])) {
        $usuario->setDatos($_SESSION['datosUsuario']['email'], 'email', $campos['email']);
        $_SESSION['datosUsuario']['email'] = $campos['email'];
    }
}


//-------------------//

//Si se ha dado a modificar usuario
$confirmacion = null;
$camposCambiados = null;
$datosNuevos = null;
if (isset($_POST['modificarUsuario']) ) {
    //Comporobar si se han modificado campos
    $camposCambiados = camposCambiados($_POST);
    $fallos = comprobarFallos($camposCambiados);
    
    $datosNuevos = null;
    if ($camposCambiados != null) {
        $fallos = comprobarFallos($camposCambiados);

        foreach ($_SESSION['datosUsuario'] as $clave1 => $valor1) {
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
            addToDb($usuario, $camposCambiados);
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