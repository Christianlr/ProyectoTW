<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();


//Obtener datos de la incidencia a eliminar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$datosIncidencia = $incidencia->get($params['id']);

$archivoRender = 'nuevaIncidencia.html';
$dialogo = true; // Necesario para que se muestre el dialogo dee incidencia en el main
if (isset($_POST['borrarDatos'])) {
    $incidencia->borrarIncidencia($datosIncidencia['id']);
    $archivoRender = 'confirmacionesIncidencias.html';
    $dialogo = false; // Necesario para que se muestre el dialogo de borrado en el main
}

$extends = 'prueba.html';
echo $twig->render($archivoRender, [
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosIncidencia' => $datosIncidencia,
    'extends' => $extends,
    'tipo' => 'borrar',
    'incidencia' => $dialogo
]);
?>

