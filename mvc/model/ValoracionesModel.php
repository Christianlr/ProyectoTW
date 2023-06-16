<?php
require_once "AbstractModel.php";

class ValoracionesModel extends AbstractModel {
    function __construct() { 
        parent::__construct();
        if (!$this->tableExists('valoraciones')) 
            $this->createTable(); 
    }

    public function createTable() {
        if (!$this->tableExists('valoraciones')) {

            $q = "CREATE TABLE valoraciones (
                id int(11) NOT NULL AUTO_INCREMENT,
                id_usuario varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
                id_incidencia int(11) NOT NULL,
                valoracion tinyint(1) DEFAULT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_valoraciones_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (email) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_valoraciones_incidencia FOREIGN KEY (id_incidencia) REFERENCES incidencias (id) ON DELETE CASCADE ON UPDATE CASCADE
            
            );";
            $rr = $this->db->query($q); 
        }
    }

    public function get($id) {
        $r = $this->query("SELECT * FROM valoraciones WHERE id='" . addslashes($id) ."'");
        return empty($r) ? null : $r[0];
    }

    public function getAllById($id_incidencia) {
        $r = $this->query("SELECT * FROM valoraciones WHERE id_incidencia='" . addslashes($id_incidencia) ."'");
        return empty($r) ? null : $r;
    }
    
    private function getValuationsById($id_incidencia, $valoracion) {
        $consulta = "SELECT COUNT(*) AS total
                     FROM valoraciones 
                     WHERE id_incidencia = :id_incidencia AND valoracion = :valoracion";
    
        $parametros = array(
            ':id_incidencia' => $id_incidencia,
            ':valoracion' => $valoracion
        );
    
        $val = $this->query($consulta, $parametros);
        return $val['total'];
    }
    
    public function getPosValById($id_incidencia) {
        return $this->getValuationsById($id_incidencia, 1);
    }

    public function getNegValById($id_incidencia) {
        return $this->getValuationsById($id_incidencia, 2);
    }
    
    public function getOpinion($id_incidencia, $id_usuario) {
        $consulta = "SELECT valoracion 
                     FROM valoraciones 
                     WHERE id_usuario=:id_usuario AND id_incidencia=:id_incidencia";

        $parametros = array(
            ':id_usuario' => $id_usuario,
            ':id_incidencia' => $id_incidencia
        );
        $val = $this->query($consulta, $parametros);
        return $val['valoracion'];
    }

    public function set($datos) {
        $consulta = "INSERT INTO valoraciones (id_incidencia, id_usuario, valoracion)
                     VALUES (:id_incidencia, :id_usuario, :valoracion)";
    
        // Preparar los parámetros para la consulta
        $parametros = array(
            ':id_incidencia' => $datos['id_incidencia'],
            ':id_usuario' => $datos['id_usuario'],
            ':valoracion' => $datos['valoracion'],
        );
    
        // Ejecutar la consulta preparada con los parámetros
        $r = $this->query($consulta, $parametros);
    }

}