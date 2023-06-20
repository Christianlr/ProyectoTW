<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/FotosIncidenciasModel.php";
require_once "../model/ComentariosModel.php";
require_once "../model/ValoracionesModel.php";

session_start();
unset($_SESSION['criteriosBusqueda']);

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$fotos = new FotosIncidenciasModel();
$comentarios = new ComentariosModel();
$valoraciones = new ValoracionesModel();


if(isset($_POST['ordenar'])) 
    $_SESSION['criteriosBusqueda']['orden'] = $_POST['ordenar']; 
if(isset($_POST['textoBusqueda'])) 
    $_SESSION['criteriosBusqueda']['textoBusqueda'] = $_POST['textoBusqueda'];
if(isset($_POST['lugar'])) 
    $_SESSION['criteriosBusqueda']['lugar'] = $_POST['lugar'];
if(isset($_POST['estado'])) 
    $_SESSION['criteriosBusqueda']['estado'] = $_POST['estado'];
if(isset($_POST['numeroIncidencias'])) 
    $_SESSION['criteriosBusqueda']['numeroIncidencias'] = $_POST['numeroIncidencias'];

echo $twig->render('confirmacionesIncidencias.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'tipo' => 'criteriosBusqueda'
]);
?>