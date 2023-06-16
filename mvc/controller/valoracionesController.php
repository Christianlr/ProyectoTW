<?php
require '../twig/vendor/autoload.php';
require_once "../model/ValoracionesModel.php";

session_start();

//---- FUNCIONES ----//

// Función para verificar si la valoración existe en la base de datos
function verificarValoracionBaseDatos($valoracion, $id_incidencia, $id_usuario) {
    $opinion = $valoracion->getOpinion($id_incidencia, $id_usuario);
    return $opinion;
}

// Función para verificar si la valoración existe en la cookie
function verificarValoracionCookie($valoracion, $id_incidencia) {
    $valoraciones = isset($_COOKIE['valoraciones']) ? json_decode($_COOKIE['valoraciones'], true) : [];

    foreach ($valoraciones as $valor) {
        if ($valor['id_incidencia'] == $id_incidencia) {
            return $valor['valoracion'];
        }
    }
    return null;
}

function opinionSeleccionada($val) {
    return $val == 1 ? 'opinionPositiva' : 'opinionNegativa';
}

function redireccionar($url) {
    header("Location: $url");
    exit;
}

//-------------------//


/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$valoracion = new ValoracionesModel();

// Obtener id de la incidencia a comentar
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $params);
$id_incidencia = $params['id_incidencia'];
$id_usuario = $params['id_usuario'];
$val = $params['val'];

$opinion = null;
if ($id_usuario != 'anonimo') {
    $opinion = verificarValoracionBaseDatos($valoracion, $id_incidencia, $id_usuario);

    if (!$opinion) {
        $datos['id_incidencia'] = $id_incidencia;
        $datos['id_usuario'] = $id_usuario;
        $datos['valoracion'] = $val;
        $valoracion->set($datos);
        $tipoOpinion = opinionSeleccionada($val);
    } else {
        $tipoOpinion = 'opinionUsuario';
    }
} else {
    $opinion = verificarValoracionCookie($valoracion, $id_incidencia);

    if (!$opinion) {
        $valoraciones = isset($_COOKIE['valoraciones']) ? json_decode($_COOKIE['valoraciones'], true) : [];
        
        $nuevaValoracion = [
            'id_incidencia' => $id_incidencia,
            'valoracion' => $val
        ];
        $valoraciones[] = $nuevaValoracion;

        setcookie('valoraciones', json_encode($valoraciones), 0, '/');

        $datos['id_incidencia'] = $id_incidencia;
        $datos['id_usuario'] = null;
        $datos['valoracion'] = $val;
        $valoracion->set($datos);
        $tipoOpinion = opinionSeleccionada($val);
    } else {
        $tipoOpinion = 'opinionAnonimo';
    }
}

echo $twig->render('confirmacionesIncidencias.html', [
    'ranking' => $_SESSION['ranking'],
    'datosUsuario' => $_SESSION['datosUsuario'],
    'tipo' => $tipoOpinion,
    'valoracion' => $opinion 
]);
?>