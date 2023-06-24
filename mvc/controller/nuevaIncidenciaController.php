<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/LogsModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$log = new LogsModel();

//---- FUNCIONES ----//


//-------------------//
$confirmacion = null;
$id = null;
if (isset($_POST['enviarDatos'])) {
    if (empty($_POST['titulo']) || empty($_POST['descripcion']) || empty($_POST['lugar']))
        $confirmacion = false;
    else {
        $confirmacion = true;
        $datos = $_POST;
        $datos['usuario'] = $_SESSION['datosUsuario']['email'];
        $datos['fecha'] = date('Y-m-d H:i:s');
        $datos['estado'] = 'pendiente';

        $id = $incidencia->crearIncidencia($datos);
        $log->setCrearIncidencia(date('Y-m-d H:i:s'), $_SESSION['datosUsuario']['email'], $id);
        $_SESSION['incidenciaActual'] = $incidencia->get($id);
    }
}

$extends = 'prueba.html';
echo $twig->render('nuevaIncidencia.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosIncidencia' => $_POST,
    'confirmacion' => $confirmacion,
    'extends' => $extends,
    'tipo' => 'crear',
    'incidencia' => true
]);
?>