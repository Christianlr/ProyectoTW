<?php
require '../twig/vendor/autoload.php';
require_once "../model/LogsModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$log = new LogsModel();

$todosLogs = $log->getAll();

echo $twig->render('verLogs.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todosLogs' => $todosLogs
]);
?>