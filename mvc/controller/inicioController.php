<?php
require '../twig/vendor/autoload.php';
require_once "../model/UsuarioModel.php";
require_once "../model/IncidenciasModel.php";



session_start();
var_dump($_SESSION);


$loader = new \Twig\Loader\FilesystemLoader('../view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
#---------------------------------------------#

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();
#-------
//$usuario->setFoto('admin@admin.admin', 'view/img/enrique.png');
#-------

$top = $incidencia->getTopUsuarios();

$nombresRanking=[];
$counter = 0;
foreach ($top as $fila) {
    $nombresRanking[] = $usuario->getNombreApellidos($fila["id_usuario"]);

    $nombresRanking[$counter] = $nombresRanking[$counter][0]["nombre"] . " " . $nombresRanking[$counter][0]["apellidos"];
    $counter++;
}

$total = [];
foreach ($top as $t)
    $total[] = $t["count"];

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

$_SESSION['datosUsuario'] = $datosUsuario;
$_SESSION['rankingAdd'][0] = $total;
$_SESSION['rankingAdd'][1] = $nombresRanking;

echo $twig->render('index.html', [
        'total' => $total,
        'nombresRanking' => $nombresRanking,
        'datosUsuario' => $_SESSION['datosUsuario']
    ]);
?>