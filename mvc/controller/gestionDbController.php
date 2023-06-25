<?php
require '../twig/vendor/autoload.php';
require_once "../model/Db.php";


session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

//Si no es administrador se redirige al inicio
if ($_SESSION['datosUsuario']['rol'] != 'administrador') {
    header('Location: inicioController.php');
}


$archivoRender = 'gestionBBDD.html';
$tipo = '';
if (Db::crearCopiaDeSeguridad()) {
    $archivoRender = 'confirmacionesBaseDatos.html';
    $tipo = 'crear';
}



echo $twig->render($archivoRender, [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'tipo' => $tipo
]);
?>