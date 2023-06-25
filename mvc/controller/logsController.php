<?php
require '../twig/vendor/autoload.php';
require_once "../model/LogsModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

//Si no es administrador se redirige al inicio
if ($_SESSION['datosUsuario']['rol'] != 'administrador') {
    header('Location: inicioController.php');
}

$log = new LogsModel();

$todosLogs = $log->getAll();

echo $twig->render('verLogs.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todosLogs' => $todosLogs
]);
?>