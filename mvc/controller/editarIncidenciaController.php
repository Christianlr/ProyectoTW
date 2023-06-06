<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/FotosIncidenciasModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$fotos = new FotosIncidenciasModel();

//---- FUNCIONES ----//


//-------------------//

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


$confirmacion = null;
$archivoRender = 'nuevaIncidencia.html';
// Datos principales
if (isset($_POST['enviarDatos'])) {
    if (empty($_POST['titulo']) || empty($_POST['descripcion']) || empty($_POST['lugar']))
        $confirmacion = false;
    else {
        $confirmacion = true;
        $datos = $_POST;
        $datos['id'] = $id;
        $incidencia->modificarIncidencia($datos);
    }   
}
else if (isset($_POST['confirmarDatos'])) {
    $archivoRender = 'confirmacionesIncidencias.html';
}

//Datos asociados a las fotos

// Fotos adjuntas
if (isset($_FILES['examinar']) && $_FILES['examinar']['error'] === UPLOAD_ERR_OK) {
    $foto_nueva = file_get_contents($_FILES['examinar']['tmp_name']);
    echo 'holaaaa';
    $fotos->set($foto_nueva, $id);
}
//Eliminar foto
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
if (isset($params['foto'])) {
    $idFoto = $params['foto'];
    $fotos->eliminarFoto($idFoto);
}
$_SESSION['incidenciaActual'] = $incidencia->get($id);
$fotosIncidencia = $fotos->getFotosById($id);

echo $twig->render($archivoRender, [
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosIncidencia' => $_SESSION['incidenciaActual'],
    'tipo' => 'editar',
    'fotos' => $fotosIncidencia,
    'confirmacion' => $confirmacion,
    'extends' => 'editarIncidencia.html'
]);
?>