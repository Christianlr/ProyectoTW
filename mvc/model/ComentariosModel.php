<?php
require_once "AbstractModel.php";

class ComentariosModel extends AbstractModel {
    function __construct() { 
        parent::__construct();
        if (!$this->tableExists('comentarios')) 
            $this->createTable(); 
    }

    public function createTable() {
        if (!$this->tableExists('comentarios')) {

            $q = "CREATE TABLE comentarios(
                id int(11) NOT NULL AUTO_INCREMENT,
                id_usuario varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
                id_incidencia int(11) NOT NULL,
                comentario text COLLATE utf8_spanish2_ci,
                fecha datetime DEFAULT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_comentarios_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (email) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_comentarios_incidencia FOREIGN KEY (id_incidencia) REFERENCES incidencias (id) ON DELETE CASCADE ON UPDATE CASCADE
            );";
            $rr = $this->db->query($q); 
        }
    }

    public function get($id) {
        $r = $this->query("SELECT * FROM comentarios WHERE id='" . addslashes($id) ."'");
        return empty($r) ? null : $r[0];
    }

    public function getAllById($id_incidencia) {
        $r = $this->query("SELECT * FROM comentarios WHERE id_incidencia='" . addslashes($id_incidencia) ."'");
        return empty($r) ? null : $r;
    }

    public function set($datos) {
        $consulta = "INSERT INTO comentarios (id_incidencia, id_usuario, comentario, fecha)
                     VALUES (:id_incidencia, :id_usuario, :comentario, :fecha)";
    
        // Preparar los parámetros para la consulta
        $parametros = array(
            ':id_incidencia' => $datos['id_incidencia'],
            ':id_usuario' => $datos['id_usuario'],
            ':comentario' => $datos['comentario'],
            ':fecha' => $datos['fecha']
        );
    
        // Ejecutar la consulta preparada con los parámetros
        $r = $this->query($consulta, $parametros);
    }

}