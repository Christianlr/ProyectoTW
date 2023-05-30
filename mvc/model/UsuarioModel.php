<?php
require_once "AbstractModel.php";

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
        $r = $this->query("SELECT * FROM usuarios WHERE email='" . addslashes($email) ."'");
        return empty($r) ? null : $r;
    }

    //Devuelve un array con el nombre y los apellidos del usuario con el email dado
    public function getNombreApellidos($email) {
        $r = $this->query("select nombre, apellidos 
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r;
    }

    public function getTipoUsuario($email) {
        $r = $this->query("select rol 
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r;
    }

    public function setFoto($email, $foto) {
        $consulta = "UPDATE usuarios SET foto = ? WHERE email = ?";
        $sentencia = $this->db->prepare($consulta);
        $foto = file_get_contents(addslashes($foto));

        $email = addslashes($email);
        $sentencia->bindParam(1, $foto, PDO::PARAM_LOB);
        $sentencia->bindParam(2, $email);
        
        $sentencia->execute();    
    }

    public function getFoto($email) {
        $resultado = $this->query("SELECT foto FROM usuarios WHERE email = :email", ['email'=>addslashes($email)]);
        return base64_encode($resultado['foto']);
    }

    public function existeUsuario($email, $password) {
        $existe = $this->query("SELECT COUNT(*) as C 
                                FROM usuarios
                                WHERE email = '".addslashes($email)."' and 
                                password = SHA2('".addslashes($password)."', 256)");
        return ($existe[0]["C"]==0) ? false : true;
    }
}
?>