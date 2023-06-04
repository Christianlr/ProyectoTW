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


//Obtener datos de la persona a eliminar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$datosUsuarioSeleccionado = $usuario->get($params['email']);
$datosUsuarioSeleccionado['nombreCompleto'] = $datosUsuarioSeleccionado['nombre'] . " " . $datosUsuarioSeleccionado['apellidos'];

$archivoRender = 'editarUsuario.html';
if (isset($_POST['borrarUsuario'])) {
    $usuario->borrarUsuario($datosUsuarioSeleccionado['email']);
    $archivoRender = 'confirmacionesUsuario.html';
}

echo $twig->render($archivoRender, [
    'css' => '../view/css/editarUsuario.css',
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosNuevos' => $datosUsuarioSeleccionado,
    'tipo' => 'borrar',
    'confirmacion' => true
]);
?>

