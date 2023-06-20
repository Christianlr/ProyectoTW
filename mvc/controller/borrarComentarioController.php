<?php
require '../twig/vendor/autoload.php';
require_once "../model/ComentariosModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$comentarios = new ComentariosModel();

$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$id = $params['id'];

$comentarios->borrarComentario($id);

echo $twig->render('confirmacionesIncidencias.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'tipo' => 'borrarComentario',
]);

?>