<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";

session_start();
var_dump($_SESSION);


$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();

echo $twig->render('editarUsuario.html', [
    'css' => '../view/css/editarUsuario.css',
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'nombreUsuario' => $_SESSION['datosUsuario'][1],
    'rolUsuario' => $_SESSION['datosUsuario'][2],
    'imgUsuario' => $_SESSION['datosUsuario'][3]
]);
?>