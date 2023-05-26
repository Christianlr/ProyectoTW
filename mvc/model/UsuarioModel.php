<?php
require_once "model/AbstractModel.php";

class UsuarioModel extends AbstractModel {
    function __construct() { 
        parent::__construct();
        if (!$this->tableExists('usuarios')) 
            $this->createTable(); 
    }

    public function createTable() {
        if (!$this->tableExists('usuarios')) {
            $q = "CREATE TABLE usuarios (
                    email varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
                    nombre varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    apellidos varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    password char(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    telefono varchar(11) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    direccion varchar(200) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    foto MEDIUMBLOB,
                    estado char(32) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    rol varchar(15) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    PRIMARY KEY (email)
                );";
            $rr = $this->db->query($q); 
        }
    }

    public function get($email) {
        $r = $this->query('SELECT * FROM usuarios WHERE email=:email', ['email'=>$email]);
        return empty($r) ? null : $r;
    }
}
?>