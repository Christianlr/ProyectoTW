<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/ComentariosModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$comentario = new ComentariosModel();

// Obtener id de la incidencia a comentar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$id = $params['id'];

if (isset($_POST['comentarioNuevo'])) {
    $datos['comentario'] = $_POST['comentarioNuevo'];
    $datos['id_incidencia'] = $id;
    $datos['id_usuario'] = $_SESSION['datosUsuario']['email'];
    $datos['fecha'] = date('Y-m-d H:i:s');
    $comentario->set($datos);
    header("Location: verIncidenciasController.php"); //Para volver a reccargar la pagina con los comentarios nuevos
}


echo $twig->render('criteriosBusqueda.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todasIncidencias' => $_SESSION['todasIncidencias'],
    'comentar' => $id
]);
?>