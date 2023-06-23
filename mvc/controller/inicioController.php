<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";
require_once "../model/ComentariosModel.php";

session_start();

/* Cargamos twig para usar el render */
$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
$comentarios = new ComentariosModel();


//---- FUNCIONES ----//

function obtenerRanking($datos, $usuario) {
    $ranking = [];

    foreach ($datos as $fila) {
        $nombreCompleto = $usuario->getNombreApellidos($fila["id_usuario"]);
        $ranking[] = [
            'nombre' => $nombreCompleto["nombre"] . " " . $nombreCompleto["apellidos"],
            'total' => $fila['count']
        ];
    }

    return $ranking;
}

//-------------------//

// Si es logout se quita todo lo relacionado con la sesion
if (isset($_POST['logout'])) 
    $_SESSION = array();

/* Obtencion de los rankings */
#---------------------------------------------#

$topIncidencias = $incidencia->getTopUsuarios();
$topComentarios = $comentarios->getTopUsuarios();

$rankingIncidencias = obtenerRanking($topIncidencias, $usuario);
$rankingComentarios = obtenerRanking($topComentarios, $usuario);

$_SESSION['ranking']['incidencias'] = $rankingIncidencias;
$_SESSION['ranking']['comentarios'] = $rankingComentarios;

#----------------------------------------------#


/* Comprobacion de usuario registrado */
#----------------------------------------------#

$datosUsuario;
$verificacion = true; //Si true todo esta bien

if (!empty($_POST['email']) && !empty($_POST['contrasenia'])) {
    $verificacion = $usuario->existeUsuario($_POST['email'], $_POST['contrasenia']);

    if ($verificacion) {
        $datosUsuario['email'] = htmlentities($_POST['email']);
        $datosUsuario['nombre'] = $usuario->getNombre($_POST['email']);
        $datosUsuario['apellidos'] = $usuario->getApellidos($_POST['email']);
        $datosUsuario['rol'] = $usuario->getTipoUsuario($_POST['email']);
        $datosUsuario['foto'] = $usuario->getFoto($_POST['email']);
        $datosUsuario['direccion'] = $usuario->getDireccion($_POST['email']);
        $datosUsuario['telefono'] = $usuario->getTelefono($_POST['email']);
        $datosUsuario['estado'] = $usuario->getEstado($_POST['email']);
        $datosUsuario['nombreCompleto'] = $datosUsuario['nombre'] . " " . $datosUsuario['apellidos'];
    } 
    else {
        $datosUsuario['nombre'] = 'incorrecto';
        $datosUsuario['rol'] = 'anonimo';
    }
}
else {
    if (!empty($_POST['email']) || !empty($_POST['contrasenia']))
        $datosUsuario['nombre'] = 'incorrecto';

    $datosUsuario['rol'] = 'anonimo';
}

/* Si vamos a la pagina de inicio mantenemos la sesion */
if (empty($_SESSION['datosUsuario']) || (isset($_SESSION['datosUsuario']) && $_SESSION['datosUsuario']['rol'] == 'anonimo'))
    $_SESSION['datosUsuario'] = $datosUsuario;


echo $twig->render('index.html', [
        'ranking' => $_SESSION['ranking'],
        'datosUsuario' => $_SESSION['datosUsuario']
    ]);
?>