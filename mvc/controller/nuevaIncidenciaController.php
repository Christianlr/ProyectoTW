<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();

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
        $_SESSION['incidenciaActual'] = $incidencia->get($id);
    }
}

$extends = 'prueba.html';
echo $twig->render('nuevaIncidencia.html', [
    'total' => $_SESSION['rankingAdd'][0],
    'nombresRanking' => $_SESSION['rankingAdd'][1],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'datosIncidencia' => $_POST,
    'confirmacion' => $confirmacion,
    'extends' => $extends,
    'tipo' => 'crear',
    'incidencia' => true
]);
?>