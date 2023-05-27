<?php
require 'twig/vendor/autoload.php';
require_once "model/UsuarioModel.php";
require_once "model/IncidenciasModel.php";


session_start();

$loader = new \Twig\Loader\FilesystemLoader('view/html');
$twig = new \Twig\Environment($loader);

/* Obtencion de los rankings */
$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();

//---------


//---------

$top = $incidencia->getTopUsuarios();

//$nombresRanking=[];
$counter = 0;
foreach ($top as $fila) {
    $nombresRanking[] = $usuario->getNombreApellidos($fila["id_usuario"]);

    $nombresRanking[$counter] = $nombresRanking[$counter][0]["nombre"] . " " . $nombresRanking[$counter][0]["apellidos"];
    $counter++;
}

$total = [];
foreach ($top as $t)
    $total[] = $t["count"];

/* Comprobacion de usuario registrado */
$nombre = null;
$tipoUsuario = null;
$foto = null;

if (isset($_POST['email'])) {
    $nombre = $usuario->getNombreApellidos($_POST['email']);
    $nombre = $nombre[0]["nombre"] . " " . $nombre[0]["apellidos"];

    $tipoUsuario = $usuario->getTipoUsuario($_POST['email']);
    $tipoUsuario = $tipoUsuario[0]['rol'];
    $foto = $usuario->getFoto($_POST['email']);

    $_SESSION['rolUsuario'] = $tipoUsuario;
}
else {
    $tipoUsuario = 'anonimo';
    $_SESSION['rolUsuario'] = 'anonimo';
}


echo $twig->render('index.html', [
        'total' => $total,
        'nombresRanking' => $nombresRanking,
        'nombreUsuario' => $nombre,
        'rolUsuario' => $tipoUsuario,
        'imgUsuario' => $foto
    ]);
?>