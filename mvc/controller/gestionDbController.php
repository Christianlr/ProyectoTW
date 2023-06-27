<?php
require '../twig/vendor/autoload.php';
require_once "../model/Db.php";
require_once "../model/LogsModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

//Si no es administrador se redirige al inicio
if ($_SESSION['datosUsuario']['rol'] != 'administrador') {
    header('Location: inicioController.php');
} 

$db = Db::getInstance();
$log = new LogsModel();

$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$tipo = '';
$archivoRestauracion = '';
if (isset($params['tipo'])) {
    $tipo = $params['tipo'];
    if ($tipo == 'restaurar2' && isset($_FILES['archivoSQL']) && $_FILES['archivoSQL']['error'] === UPLOAD_ERR_OK) {
        $archivoRestauracion = file_get_contents($_FILES['archivoSQL']['tmp_name']);
    }
}


$archivoRender = 'gestionBBDD.html';

if ($tipo != '') {
    $tipo = 'error';
    $archivoRender = 'confirmacionesBaseDatos.html';
}
if ($tipo == 'copia' && Db::crearCopiaDeSeguridad()) {
    // $log->setCopiaSeguridad(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email']);
    // $archivoRender = 'confirmacionesBaseDatos.html';
    // $tipo = 'crear';
} else if ($tipo == 'restaurar1') {
    // $tipo = 'restaurar';
} else if ($tipo == 'restaurar2' && Db::restaurarCopiaDeSeguridad($archivoRestauracion)) {
    // $log->setRestaurarCopiaSeguridad(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email']);
    // header('Location: inicioController.php');
    // $_SESSION = array();
} else if ($tipo == 'borrar1') {
    // $tipo = 'borrar';
} else if ($tipo == 'borrar2' && Db::borrarDb()) {
    // $log->setBorrarDb(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email']);
    // header('Location: inicioController.php');
    // $_SESSION = array();
}


echo $twig->render($archivoRender, [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'tipo' => $tipo
]);
?>