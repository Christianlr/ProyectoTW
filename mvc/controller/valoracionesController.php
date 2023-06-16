<?php
require '../twig/vendor/autoload.php';
require_once "../model/ValoracionesModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$valoracion = new ValoracionesModel();

// Obtener id de la incidencia a comentar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$id_incidencia = $params['id_incidencia'];
$id_usuario = $params['id_usuario'];
$val = $params['val'];

$archivoRender = 'criteriosBusqueda.html';
$opinion = null;
if ($id_usuario != 'anonimo') {
    $opinion = $valoracion->getOpinion($id_incidencia, $id_usuario);
    if ($opinion == null) {
        $datos['id_incidencia'] = $id_incidencia;
        $datos['id_usuario'] = $id_usuario;
        $datos['valoracion'] = $val;
        $valoracion->set($datos);
        header("Location: verIncidenciasController.php"); //Para volver a reccargar la pagina con las valoraciones nuevas
    }
    else {
        $archivoRender = 'confirmacionesIncidencias.html';
        $tipoOpinion = 'opinionUsuario';
    }
} 
else {
    $datos['id_incidencia'] = $id_incidencia;
    $datos['id_usuario'] = null;
    $datos['valoracion'] = $val;
    $valoracion->set($datos);
    header("Location: verIncidenciasController.php"); //Para volver a reccargar la pagina con las valoraciones nuevas
}


echo $twig->render($archivoRender, [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todasIncidencias' => $_SESSION['todasIncidencias'],
    'tipo' => $tipoOpinion,
    'valoracion' => $opinion 
]);
?>