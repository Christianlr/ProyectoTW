<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/FotosIncidenciasModel.php";

session_start();
unset($_SESSION['incidenciaActual']);
/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

//Si no es administrador o usuario registrado se redirige al inicio
if ($_SESSION['datosUsuario']['rol'] == 'anonimo') {
    header('Location: inicioController.php');
}

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$fotos = new FotosIncidenciasModel();

$todasIncidencias = $incidencia->getAllByUser($_SESSION['datosUsuario']['email']);

foreach ($todasIncidencias as &$parte) {
    $nombreCompleto = $usuario->getNombreApellidos($parte['id_usuario']);
    $parte['nombreUsuario'] = $nombreCompleto['nombre'] . " " . $nombreCompleto['apellidos'];
    $parte['fotos'] = $fotos->getFotosById($parte['id']);    
}
$_SESSION['todasIncidencias'] = $todasIncidencias;

echo $twig->render('criteriosBusqueda.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todasIncidencias' => $todasIncidencias
]);
?>