<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/LogsModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$log = new LogsModel();

//Obtener datos de la incidencia a eliminar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$datosIncidencia = $incidencia->get($params['id']);

$archivoRender = 'nuevaIncidencia.html';
$dialogo = true; // Necesario para que se muestre el dialogo dee incidencia en el main
if (isset($_POST['borrarDatos'])) {
    $incidencia->borrarIncidencia($datosIncidencia['id']);
    $log->setBorrarIncidencia(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $datosIncidencia['id']);
    $archivoRender = 'confirmacionesIncidencias.html';
    $dialogo = false; // Necesario para que se muestre el dialogo de borrado en el main
}

$extends = 'prueba.html';
echo $twig->render($archivoRender, [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosIncidencia' => $datosIncidencia,
    'extends' => $extends,
    'tipo' => 'borrar',
    'incidencia' => $dialogo
]);
?>

