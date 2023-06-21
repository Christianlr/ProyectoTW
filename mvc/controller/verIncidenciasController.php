<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/FotosIncidenciasModel.php";
require_once "../model/ComentariosModel.php";
require_once "../model/ValoracionesModel.php";

session_start();
unset($_SESSION['incidenciaActual']);

//---- FUNCIONES ----//

function getCriterios($propias) {
    $criterios =  $propias ? 'criteriosBusquedaPropia' : 'criteriosBusqueda';

    if (isset($_SESSION[$criterios]))
        return $_SESSION[$criterios];
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
$fotos = new FotosIncidenciasModel();
$comentarios = new ComentariosModel();
$valoraciones = new ValoracionesModel();

$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);


$propias = false;
$id_usuario = null;
if (isset($params['email'])) {
    $propias = true;
    $id_usuario = $params['email'];
}

$criterios = getCriterios($propias);
if ($criterios) {
    $todasIncidencias = $incidencia->filtrado($criterios, $propias, $id_usuario);

    if ($propias)
        $incidenciasMax = $_SESSION['criteriosBusquedaPropia']['numeroIncidencias'];
    else
        $incidenciasMax = $_SESSION['criteriosBusqueda']['numeroIncidencias'];
}
else {
    $incidenciasMax = 5;
    $todasIncidencias = $incidencia->getAll();
}


foreach ($todasIncidencias as &$parte) {
    $nombreCompleto = $usuario->getNombreApellidos($parte['id_usuario']);
    $parte['nombreUsuario'] = $nombreCompleto['nombre'] . " " . $nombreCompleto['apellidos'];
    $parte['fotos'] = $fotos->getFotosById($parte['id']);
    $parte['valoracion']['positiva'] = $valoraciones->getPosValById($parte['id']);
    $parte['valoracion']['negativa'] = $valoraciones->getNegValById($parte['id']);

    $parte['comentarios'] = $comentarios->getAllById($parte['id']);
    if (!empty($parte['comentarios']))
        foreach ($parte['comentarios'] as &$c) {
            if ($c['id_usuario'] != null) {
                $nombreCompleto = $usuario->getNombreApellidos($c['id_usuario']);
                $c['nombreUsuario'] = $nombreCompleto['nombre'] . " " . $nombreCompleto['apellidos'];
            } else {
                $c['nombreUsuario'] = 'Anónimo';
            }
        }
}
$_SESSION['todasIncidencias'] = $todasIncidencias;

echo $twig->render('criteriosBusqueda.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'todasIncidencias' => $todasIncidencias,
    'propias' => $propias
]);
?>