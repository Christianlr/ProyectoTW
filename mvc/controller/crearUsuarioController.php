<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";

session_start();

function comprobarFallos($usuario, &$campos) {
    $fallos = false;
    if (empty($campos['nombre'])) {
        $campos['nombre'] = 'incorrecto';
        $fallos = true;
    }
    if (empty($campos['apellidos'])) {
        $campos['apellidos'] = 'incorrecto';
        $fallos = true;
    }

    if (!filter_var($campos['email'], FILTER_VALIDATE_EMAIL) || $usuario->existeCorreo($campos['email'])) {
        $campos['email'] = 'incorrecto';
        $fallos = true;
    }

    if (!preg_match("/^(\+34)?[ -]?(6|7|9)([0-9]){8}$/", $campos['telefono']) && !empty($campos['telefono'])) {
        $campos['telefono'] = 'incorrecto';
        $fallos = true;
    }

    if ((empty($campos['password']) || empty($_POST['claveConfirmacion'])) ||
        ($campos['password'] != $_POST['claveConfirmacion']) ||
        (strlen($campos['password']) < 8)) {
        $campos['password'] = 'mal';
        $fallos = true;
    }
    return $fallos;

}

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();

$confirmacion = null;
$campos = null;
$archivoRender = 'accionGestionUsuarios.html';
if (isset($_POST['crearUsuario'])) {
    foreach ($_POST as $clave => $valor) {
        if ($clave != 'crearUsuario' && $clave != 'claveConfirmacion') {
            if ($clave == 'claveNueva') 
                $campos['password'] = $_POST[$clave];
            else 
                $campos[$clave] = $_POST[$clave];
        }
    }
    if (isset($_FILES['examinar']) && $_FILES['examinar']['error'] === UPLOAD_ERR_OK) 
        $campos['foto'] = file_get_contents($_FILES['examinar']['tmp_name']);
    else 
        $campos['foto'] = file_get_contents('../view/img/defaultProfile.png');
    
    $fallos = comprobarFallos($usuario, $campos);

    if ($fallos) {
        $confirmacion = false;
    }
    else {
        $confirmacion = true;
        $usuario->crearUsuario($campos);
        $campos['foto'] = base64_encode($campos['foto']);
    }
}
else if (isset($_POST['confirmarCreacionUsuario'])) {
    $archivoRender = 'confirmacionesUsuario.html';
}


echo $twig->render($archivoRender, [
    'extends' => 'editarUsuario.html',
    'css' => '../view/css/editarUsuario.css',
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosNuevos' => $campos,
    'tipo' => 'crear',
    'confirmacion' => $confirmacion
    
]);
?>