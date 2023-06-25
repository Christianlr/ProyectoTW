<?php
require_once "AbstractModel.php";
require_once "LogsModel.php";

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

            $log = new LogsModel();
            $log->setTablaCreada(date('Y-m-d H:i:s') ,'usuarios');
        }
    }

    public function get($email) {
        $r = $this->query("SELECT * FROM usuarios WHERE email='" . addslashes($email) ."'");
        if (empty($r))
            return null;

        $r[0]['foto'] = base64_encode($r[0]['foto']);

        return $r[0];
    }

    public function getAll() {
        $r = $this->query("SELECT * FROM usuarios");
        if (empty($r)) 
            return null;
        
        // Recorrer los resultados y codificar la imagen en base64
        foreach ($r as &$row) 
            $row['foto'] = base64_encode($row['foto']);
        
        return $r;
    }

    public function getNombre($email) {
        $r = $this->query("select nombre
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r[0]['nombre'];
    }

    public function getApellidos($email) {
        $r = $this->query("select apellidos 
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r[0]['apellidos'];
    }
    
    public function getDireccion($email) {
        $r = $this->query("select direccion
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r[0]['direccion'];
    }

    public function getTelefono($email) {
        $r = $this->query("select telefono 
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r[0]['telefono'];
    }

    public function getEstado($email) {
        $r = $this->query("select estado 
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r[0]['estado'];
    }

    //Devuelve un array con el nombre y los apellidos del usuario con el email dado
    public function getNombreApellidos($email) {
        $r = $this->query("select nombre, apellidos 
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r[0];
    }

    public function getTipoUsuario($email) {
        $r = $this->query("select rol 
                            from usuarios 
                            where email= '" . addslashes($email). "'");
        return empty($r) ? null : $r[0]['rol'];
    }

    public function getFoto($email) {
            $resultado = $this->query("SELECT foto FROM usuarios WHERE email = :email", ['email'=>addslashes($email)]);
            return base64_encode($resultado['foto']);
    }

    public function setFoto($email, $foto) {
        $consulta = "UPDATE usuarios SET foto = ? WHERE email = ?";
        $sentencia = $this->db->prepare($consulta);

        if (base64_decode($foto, true)) {
            $foto = base64_decode($foto);
        } else {
            $foto = file_get_contents($foto);
        }

        $email = addslashes($email);
        $sentencia->bindParam(1, $foto, PDO::PARAM_LOB);
        $sentencia->bindParam(2, $email);

        $sentencia->execute();
  
    }
    
    public function setPassword($email, $password) {
        echo $email . $password;
        $query = "UPDATE usuarios SET password = SHA2(:password, 256) WHERE email = :email";
        $params = array(':password' => $password, ':email' => $email);
    
        $this->query($query, $params);
    }
    

    public function setDatos($email, $clave, $dato) {
        if ($clave == 'foto')
            $this->setFoto($email, $dato);
        else if ($clave == 'password')
            $this->setPassword($email, $dato);
        else {
            $r = $this->query("UPDATE usuarios 
                                SET " . $clave . " = '" . addslashes($dato) . "'
                                WHERE email = '".addslashes($email). "'"
                            );
        }
    }

    public function crearUsuario($campos) {
        // Preparar la consulta SQL con marcadores de posición
        $consulta = "INSERT INTO usuarios (email, nombre, apellidos, password, telefono, direccion, foto, estado, rol)
                     VALUES (:email, :nombre, :apellidos, SHA2(:password, 256), :telefono, :direccion, :foto, :estado, :rol)";
    
        // Preparar los parámetros para la consulta
        $parametros = array(
            ':email' => $campos['email'],
            ':nombre' => $campos['nombre'],
            ':apellidos' => $campos['apellidos'],
            ':password' => $campos['password'],
            ':telefono' => $campos['telefono'],
            ':direccion' => $campos['direccion'],
            ':foto' => $campos['foto'],
            ':estado' => strtolower($campos['estado']),
            ':rol' => strtolower($campos['rol'])
        );
    
        // Ejecutar la consulta preparada con los parámetros
        $r = $this->query($consulta, $parametros);
    }
    

    public function borrarUsuario($email) {
        $r = $this->query("DELETE FROM usuarios 
                            WHERE email = '".addslashes($email). "'"
                        );
    }

    public function existeCorreo($email) {
        $existe = $this->query("SELECT COUNT(*) as C 
                                FROM usuarios
                                WHERE email = '".addslashes($email)."'"
                                );
        return ($existe[0]["C"]==0) ? false : true;
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