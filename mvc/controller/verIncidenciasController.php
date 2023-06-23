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

//Si hay email o hemos avanzando con tipo propias quiere decir que estamos en ver nuestras propias incidencias
$propias = false;
$id_usuario = null;
if (isset($params['email']) || (isset($params['tipo']) && $params['tipo'] == 'propias')) {
    $propias = true;
    $id_usuario = $_SESSION['datosUsuario']['email'];
}

//Obtener los criterios
$criterios = getCriterios($propias);

// Si no hay ningun criterop ponemos que se muestren 3 incidencias por pagina. Los criterios dependen de si son incidencias propias o generales
if (isset($param['email']) || (!$criterios && $propias && !isset($_SESSION['incidenciasPropiasMaximo']))) {
    $_SESSION['incidenciasPropiasMaximo']['indice'] = 0;
    $_SESSION['incidenciasPropiasMaximo']['maximo'] = 3;
}
else if (!$criterios && !$propias && !isset($_SESSION['incidenciasMaximo'])) {
    $_SESSION['incidenciasMaximo']['indice'] = 0;
    $_SESSION['incidenciasMaximo']['maximo'] = 3;
}

//Si le hemos dado al boton de avanzar a las siguientes incidencias
if (isset($params['avanzar'])) {
    if ($params['avanzar'] == 'siguiente') {
        if ($propias && 
            ($_SESSION['incidenciasPropiasMaximo']['indice'] < ($incidencia->getCountAllByUser($_SESSION['datosUsuario']['email']) - $_SESSION['incidenciasPropiasMaximo']['maximo']))) {
            $_SESSION['incidenciasPropiasMaximo']['indice'] += $_SESSION['incidenciasPropiasMaximo']['maximo'];
        } else if ($_SESSION['incidenciasMaximo']['indice'] < ($incidencia->getCountAll()-$_SESSION['incidenciasMaximo']['maximo'])) {
            $_SESSION['incidenciasMaximo']['indice'] += $_SESSION['incidenciasMaximo']['maximo'];
        }
    } else if ($params['avanzar'] == 'final') {
        if ($propias)
            $_SESSION['incidenciasPropiasMaximo']['indice'] = $incidencia->getCountAllByUser($_SESSION['datosUsuario']['email']) - $_SESSION['incidenciasPropiasMaximo']['maximo'];
        else
            $_SESSION['incidenciasMaximo']['indice'] = $incidencia->getCountAll() - $_SESSION['incidenciasMaximo']['maximo'];
    } 
} else if (isset($params['retroceder'])) {
    if ($params['retroceder'] == 'anterior') {
        if ($propias) {
            $_SESSION['incidenciasPropiasMaximo']['indice'] -= $_SESSION['incidenciasPropiasMaximo']['maximo'];
            if ($_SESSION['incidenciasPropiasMaximo']['indice'] <= 0)
                $_SESSION['incidenciasPropiasMaximo']['indice'] = $_SESSION['incidenciasPropiasMaximo']['maximo'];
        } else {
            $_SESSION['incidenciasMaximo']['indice'] -= $_SESSION['incidenciasMaximo']['maximo'];
            if ($_SESSION['incidenciasMaximo']['indice'] <= 0)
                $_SESSION['incidenciasMaximo']['indice'] = 0;
        }
    } else if ($params['retroceder'] == 'inicio') {
        if ($propias)
            $_SESSION['incidenciasPropiasMaximo']['indice'] = 0;
        else
            $_SESSION['incidenciasMaximo']['indice'] = 0;
    } 
}

$limite = null;
$indice = null;
if ($propias) {
    $limite = $_SESSION['incidenciasPropiasMaximo']['maximo'];
    $indice = $_SESSION['incidenciasPropiasMaximo']['indice'];
}
else {
    $limite = $_SESSION['incidenciasMaximo']['maximo'];
    $indice = $_SESSION['incidenciasMaximo']['indice'];
}

$listaCriterios = null;
if ($criterios) {
    $todasIncidencias = $incidencia->filtrado($criterios, $propias, $id_usuario, $limite, $indice);
    
    if ($propias) {
        $listaCriterios = $_SESSION['criteriosBusquedaPropia'];
    } else {
        $listaCriterios = $_SESSION['criteriosBusqueda'];
    }
    
} else {
    if ($propias) {
        $todasIncidencias = $incidencia->getAllByUser($_SESSION['datosUsuario']['email'], $limite, $indice);
    } else {
        $todasIncidencias = $incidencia->getAll($limite, $indice);
    }
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
    'propias' => $propias,
    'listaCriterios' => $listaCriterios
]);
?>