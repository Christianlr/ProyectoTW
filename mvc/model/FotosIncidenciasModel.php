<?php
require_once "AbstractModel.php";

class FotosIncidenciasModel extends AbstractModel {
    function __construct() { 
        parent::__construct();
        if (!$this->tableExists('fotos')) 
            $this->createTable(); 
    }

    public function createTable() {
        if (!$this->tableExists('fotos')) {

            $q = "CREATE TABLE fotos (
                id int(11) NOT NULL AUTO_INCREMENT,
                fotografia MEDIUMBLOB,
                id_incidencia int(11) NOT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_fotos_incidencia FOREIGN KEY (id_incidencia) REFERENCES incidencias (id) ON DELETE CASCADE ON UPDATE CASCADE
                );";
            $rr = $this->db->query($q); 
        }
    }

    public function get($id) {
        $r = $this->query("SELECT * FROM fotos WHERE id='" . addslashes($id) ."'");
        return empty($r) ? null : $r[0];
    }

    public function set($foto, $id_incidencia) {
        // Verificar si la foto está en base64 y decodificarla si es necesario
        if (strpos($foto, 'data:image') !== false) {
            $foto = base64_decode($foto);
        }

        // Preparar la consulta SQL para insertar la foto
        $consulta = "INSERT INTO fotos (fotografia, id_incidencia)
                    VALUES (:fotografia, :id_incidencia)";

        // Preparar los parámetros para la consulta
        $parametros = array(
            ':fotografia' => $foto,
            ':id_incidencia' => addslashes($id_incidencia)
        );

        // Ejecutar la consulta preparada con los parámetros
        $r = $this->query($consulta, $parametros);
    }

    public function getFotosById($id_incidencia) {
        $r = $this->query("select id, fotografia from fotos where id_incidencia = '" . addslashes($id_incidencia) . "'");
        if (empty($r))
            return null;
        
        foreach ($r as &$foto) {
            $foto['fotografia'] = base64_encode($foto['fotografia']);
        }
        return empty($r) ? null : $r;
    }

    public function eliminarFoto($id) {
        $r = $this->query("delete from fotos where id = '" . addslashes($id) . "'");
    }
    
    public function getAll() {
        $r = $this->query("SELECT * FROM fotos");
        return empty($r) ? null : $r;
    }
    
}