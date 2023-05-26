<?php
require_once "model/UsuarioModel.php";

$usuario = new UsuarioModel();
$tupla = $usuario->query('Select * from usuarios where nombre = "Enrique"');

var_dump($tupla);
?>
