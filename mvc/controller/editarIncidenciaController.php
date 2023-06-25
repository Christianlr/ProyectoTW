<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/FotosIncidenciasModel.php";
require_once "../model/LogsModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$fotos = new FotosIncidenciasModel();
$log = new LogsModel();

//Obtener id de la incidencia
$id = null;
if (isset($_SESSION['incidenciaActual'])) {
    $id = $_SESSION['incidenciaActual']['id'];
}
else {
    if (isset($_POST['id']))
        $id = $_POST['id'];
    else {
        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($queryString, $params);
        $id = $params['id'];
    }
}

$usuarioIncidencia = $incidencia->getUserById($id);

//Si no es administrador o se intenta editar una incidencia que no es suya se redirige a la pagina principal
if ($_SESSION['datosUsuario']['rol'] == 'anonimo' ||
    ($_SESSION['datosUsuario']['email']) != $usuarioIncidencia && $_SESSION['datosUsuario']['rol'] != 'administrador') {
    header('Location: inicioController.php');
}

// Datos principales
$confirmacion = null;
$archivoRender = 'nuevaIncidencia.html';
$tipo = 'editar';
if (isset($_POST['enviarDatos'])) {
    if (empty($_POST['titulo']) || empty($_POST['descripcion']) || empty($_POST['lugar']))
        $confirmacion = false;
    else {
        $confirmacion = true;
        $datos = $_POST;
        $datos['id'] = $id;
        $incidencia->modificarIncidencia($datos);
        $log->setEditarIncidencia(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $id);
    }   
} else if(isset($_POST['modificarEstado'])) {
    $incidencia->modificarEstado($_POST['estadoIncidencia'], $id);
    $log->setModificarEstadoIncidencia(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $id);

    $archivoRender = 'confirmacionesIncidencias.html';
    $tipo = 'editarEstado';
} else if (isset($_POST['confirmarDatos']) && !empty($_POST['id'])) {
    $archivoRender = 'confirmacionesIncidencias.html';
}


//Datos asociados a las fotos
// Fotos adjuntas
if (isset($_FILES['examinar']) && $_FILES['examinar']['error'] === UPLOAD_ERR_OK) {
    $foto_nueva = file_get_contents($_FILES['examinar']['tmp_name']);
    $fotos->set($foto_nueva, $id);
    $log->setAddFotoIncidencia(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $id);
}
//Eliminar foto
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
if (isset($params['foto'])) {
    $idFoto = $params['foto'];
    $fotos->eliminarFoto($idFoto);
    $log->setEliminarFotoIncidencia(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $id);
}
$_SESSION['incidenciaActual'] = $incidencia->get($id);
$fotosIncidencia = $fotos->getFotosById($id);


echo $twig->render($archivoRender, [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosIncidencia' => $_SESSION['incidenciaActual'],
    'tipo' => $tipo,
    'fotos' => $fotosIncidencia,
    'confirmacion' => $confirmacion,
    'extends' => 'editarIncidencia.html'
]);
?>