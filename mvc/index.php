<?php
require 'twig/vendor/autoload.php';
require_once "model/UsuarioModel.php";
require_once "model/IncidenciasModel.php";


$loader = new \Twig\Loader\FilesystemLoader('view/html');
$twig = new \Twig\Environment($loader);

$usuario = new UsuarioModel();
$incidencia = new IncidenciasModel();

$top = $incidencia->getTopUsuarios();


$nombre = [];
$counter = 0;
foreach ($top as $fila) {
    $nombre[] = $usuario->getNombreApellidos($fila["id_usuario"]);

    $nombre[$counter] = $nombre[$counter][0]["nombre"] . " " . $nombre[$counter][0]["apellidos"];
    $counter++;
}

$total = [];
foreach ($top as $t)
    $total[] = $t["count"];

echo $twig->render('default.html', [
                'nombre' => $nombre,
                'total' => $total
                ]);

?>