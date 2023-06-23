<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/FotosIncidenciasModel.php";
require_once "../model/ComentariosModel.php";
require_once "../model/ValoracionesModel.php";

session_start();

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

$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);

$tipoCriterios = 'criteriosBusqueda';
if (isset($params['info']) && $params['info'] == 'propias') 
    $tipoCriterios = 'criteriosBusquedaPropia';
    

unset($_SESSION[$tipoCriterios]);

if(isset($_POST['ordenar'])) 
    $_SESSION[$tipoCriterios]['orden'] = $_POST['ordenar']; 
if(isset($_POST['textoBusqueda'])) 
    $_SESSION[$tipoCriterios]['textoBusqueda'] = $_POST['textoBusqueda'];
if(isset($_POST['lugar'])) 
    $_SESSION[$tipoCriterios]['lugar'] = $_POST['lugar'];
if(isset($_POST['estado'])) 
    $_SESSION[$tipoCriterios]['estado'] = $_POST['estado'];
if(isset($_POST['numeroIncidencias'])) 
    $_SESSION[$tipoCriterios]['numeroIncidencias'] = $_POST['numeroIncidencias'];
    if ($tipoCriterios == 'criteriosBusqueda') {
        $_SESSION['incidenciasMaximo']['maximo'] = $_POST['numeroIncidencias'];
        $_SESSION['incidenciasMaximo']['indice'] = 0; 
    } else {
        $_SESSION['incidenciasPropiasMaximo']['maximo'] = $_POST['numeroIncidencias'];
        $_SESSION['incidenciasPropiasMaximo']['indice'] = 0; 
    }

echo $twig->render('confirmacionesIncidencias.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'tipo' => 'criteriosBusqueda'
]);
?>