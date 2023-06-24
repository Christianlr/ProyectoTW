<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/ComentariosModel.php";
require_once "../model/LogsModel.php";

session_start();


//---- FUNCIONES ----//

function obtenerIncidencia($todasIncidencias, $id) {
    foreach ($todasIncidencias as $incidenciaComentada)
    if ($incidenciaComentada['id'] == $id)
        return $incidenciaComentada;
    
    return null;
}

//-------------------//


/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$comentario = new ComentariosModel();
$log = new LogsModel();

// Obtener id de la incidencia a comentar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$id = $params['id'];

$archivoRender = 'incidencias.html';
$incidenciaComentada = null;
if (isset($_POST['comentarioNuevo'])) {
    $datos['comentario'] = $_POST['comentarioNuevo'];
    $datos['id_incidencia'] = $id;
    if ($_SESSION['datosUsuario']['rol'] != 'anonimo')
        $datos['id_usuario'] = $_SESSION['datosUsuario']['email'];
    else 
        $datos['id_usuario'] = null;

    $datos['fecha'] = date('Y-m-d H:i:s');
    $comentario->set($datos);
    $log->setComentarioIncidencia(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $id);
    $archivoRender = 'confirmacionesIncidencias.html';
} else {
    $incidenciaComentada[] = obtenerIncidencia($_SESSION['todasIncidencias'], $id);
}


echo $twig->render($archivoRender, [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todasIncidencias' => $incidenciaComentada,
    'comentar' => $id,
    'tipo' => 'comentario'
]);
?>