<?php
require_once "AbstractModel.php";

class LogsModel extends AbstractModel {
    function __construct() { 
        parent::__construct();
        if (!$this->tableExists('logs')) 
            $this->createTable(); 
    }

    public function createTable() {
        if (!$this->tableExists('logs')) {

            $q = "CREATE TABLE logs (
                id int(11) NOT NULL AUTO_INCREMENT,
                fecha datetime DEFAULT NULL,
                descripcion text COLLATE utf8_spanish2_ci,
                PRIMARY KEY (id)
                );";
            $rr = $this->db->query($q);
            
            $this->setTablaCreada(date('Y-m-d H:i:s'), 'logs');
        }
    }

    public function get($id) {
        $r = $this->query("SELECT * FROM logs WHERE id='" . addslashes($id) ."'");
        return empty($r) ? null : $r[0];
    }
    
    public function getAll() {
        $r = $this->query("SELECT * FROM logs ORDER BY fecha DESC");
        return empty($r) ? null : $r;
        
    }

    private function setLog($fecha, $descripcion) {
        $consulta = "INSERT INTO logs(fecha, descripcion) VALUES(:fecha, :descripcion)";

        $parametros = array(
            ':fecha' => $fecha,
            ':descripcion' => $descripcion
        );

        $r = $this->query($consulta, $parametros);
    }

    public function setTablaCreada($fecha, $tabla) {
        $descripcion = "INFO: Se ha creado la tabla de " . $tabla . " en el sistema";
        $this->setLog($fecha, $descripcion); 
    }

    public function setInicioSesion($fecha, $usuario) {
        if ($usuario == null) {
            $descripcion = "INFO: Un usuario anónimo ha accedido al sistema";
        } else {
            $descripcion = "INFO: El usuario " . $usuario . " ha accedido al sistema";
        }
        
        $this->setLog($fecha, $descripcion);    
    }

    public function setEditarIncidencia($fecha, $usuario, $id_incidencia) {
        $descripcion = "INFO: El usuario " . $usuario . " ha editado los datos de la incidencia
                        con id " . $id_incidencia;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setModificarEstadoIncidencia($fecha, $usuario, $id_incidencia) {
        $descripcion = "INFO: El administrador " . $usuario . " ha modificado el estado de la incidencia
                        con id " . $id_incidencia;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setAddFotoIncidencia($fecha, $usuario, $id_incidencia) {
        $descripcion = "INFO: El usuario " . $usuario . " ha añadido una nueva foto en la incidencia
                        con id " . $id_incidencia;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setEliminarFotoIncidencia($fecha, $usuario, $id_incidencia) {
        $descripcion = "INFO: El usuario " . $usuario . " ha eliminado una foto de la incidencia
                        con id " . $id_incidencia;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setCrearUsuario($fecha, $usuario, $usuarioCreado) {
        $descripcion = "INFO: El administrador " . $usuario . " ha creado al usuario " . $usuarioCreado;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setBorrarUsuario($fecha, $usuario, $usuarioEliminado) {
        $descripcion = "INFO: El administrador " . $usuario . " ha eliminado al usuario " . $usuarioEliminado;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setBorrarComentario($fecha, $usuario, $id) {
        $descripcion = "INFO: El administrador " . $usuario . " ha eliminado el comentario con id " . $id;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setBorrarIncidencia($fecha, $usuario, $id) {
        $descripcion = "INFO: El administrador " . $usuario . " ha eliminado la incidencia con id " . $id;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setComentarioIncidencia($fecha, $usuario, $id) {
        $descripcion = "INFO: El usuario " . $usuario . " ha hecho un comentario en la incidencia con id " . $id;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setCrearIncidencia($fecha, $usuario, $id) {
        $descripcion = "INFO: El usuario " . $usuario . " ha creado una incidencia con id " . $id;
        
        $this->setLog($fecha, $descripcion);
    }

    public function setEditarUsuario($fecha, $usuario, $usuarioEditado) {
        if ($usuario == $usuarioEditado) {
            $descripcion = "INFO: El usuario " . $usuario . " ha modificado sus datos ";
        } else {
            $descripcion = "INFO: El administrador " . $usuario . " ha modificado los datos del usuario " . $usuarioEditado;
        }
        
        
        $this->setLog($fecha, $descripcion);
    }

    public function setValoracion($fecha, $usuario, $id, $val) {
        if ($usuario == null) {
            if ($val == 1) {
                $descripcion = "INFO: Un usuario anonimo ha hecho una valoración positiva en la incidencia con id " . $id;
            } else {
                $descripcion = "INFO: Un usuario anonimo ha hecho una valoración negativa en la incidencia con id " . $id;
            }
        } else {
            if ($val == 1) {
                $descripcion = "INFO: El usuario " . $usuario . " ha hecho una valoración positiva en la incidencia con id " . $id;
            } else {
                $descripcion = "INFO: El usuario " . $usuario . " ha hecho una valoración negativa en la incidencia con id " . $id;
            }
        }
        
        
        $this->setLog($fecha, $descripcion);
    }
}