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

/* Obtencion de los rankings */
#---------------------------------------------#

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$fotos = new FotosIncidenciasModel();

$todasIncidencias = $incidencia->getAll();

foreach ($todasIncidencias as &$parte) {
    $nombreCompleto = $usuario->getNombreApellidos($parte['id_usuario']);
    $parte['nombreUsuario'] = $nombreCompleto['nombre'] . " " . $nombreCompleto['apellidos'];
    $parte['fotos'] = $fotos->getFotosById($parte['id']);
    
    
}

echo $twig->render('criteriosBusqueda.html', [
    'css' => '../view/css/editarUsuario.css',
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todasIncidencias' => $todasIncidencias
]);
?>