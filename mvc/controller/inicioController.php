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

$nombre = null;
$tipoUsuario = null;
$foto = null;
$datosUsuario;
$verificacion = true; //Si true todo esta bien

if (!empty($_POST['email']) && !empty($_POST['contrasenia'])) {
    $verificacion = $usuario->existeUsuario($_POST['email'], $_POST['contrasenia']);

    if ($verificacion) {
        $nombre = $usuario->getNombreApellidos($_POST['email']);
        $nombre = $nombre[0]["nombre"] . " " . $nombre[0]["apellidos"];

        $tipoUsuario = $usuario->getTipoUsuario($_POST['email']);
        $tipoUsuario = $tipoUsuario[0]['rol'];
        $foto = $usuario->getFoto($_POST['email']);

        $datosUsuario[] = htmlentities($_POST['email']);
        $datosUsuario[] = $nombre;
        $datosUsuario[] = $tipoUsuario;
        $datosUsuario[] = $foto;
    } 
    else {
        $nombre = 'incorrecto';
        $tipoUsuario = 'anonimo';
        $datosUsuario[] = $tipoUsuario;
    }
}
else {
    if (!empty($_POST['email']) || !empty($_POST['contrasenia']))
        $nombre = 'incorrecto';

    $tipoUsuario = 'anonimo';
    $datosUsuario[] = $tipoUsuario;
}

$_SESSION['datosUsuario'] = $datosUsuario;
$_SESSION['rankingAdd'][0] = $total;
$_SESSION['rankingAdd'][1] = $nombresRanking;


echo $twig->render('index.html', [
        'total' => $total,
        'nombresRanking' => $nombresRanking,
        'nombreUsuario' => $nombre,
        'rolUsuario' => $tipoUsuario,
        'imgUsuario' => $foto
    ]);
?>