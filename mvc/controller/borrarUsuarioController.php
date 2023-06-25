<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/LogsModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

//Si no es administrador se redirige al inicio
if ($_SESSION['datosUsuario']['rol'] != 'administrador') {
    header('Location: inicioController.php');
}

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$log = new LogsModel();

//Obtener datos de la persona a eliminar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$datosUsuarioSeleccionado = $usuario->get($params['email']);
$datosUsuarioSeleccionado['nombreCompleto'] = $datosUsuarioSeleccionado['nombre'] . " " . $datosUsuarioSeleccionado['apellidos'];

$archivoRender = 'editarUsuario.html';
if (isset($_POST['borrarUsuario'])) {
    $usuario->borrarUsuario($datosUsuarioSeleccionado['email']);
    $log->setBorrarUsuario(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $datosUsuarioSeleccionado['email']);
    $archivoRender = 'confirmacionesUsuario.html';
}

echo $twig->render($archivoRender, [
    'css' => '../view/css/editarUsuario.css',
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosNuevos' => $datosUsuarioSeleccionado,
    'tipo' => 'borrar',
    'confirmacion' => true
]);
?>

