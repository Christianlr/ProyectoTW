<?php
require_once "model/AbstractModel.php";

class IncidenciasModel extends AbstractModel {
    function __construct() { 
        parent::__construct();
        if (!$this->tableExists('incidencias')) 
            $this->createTable(); 
    }

    public function createTable() {
        if (!$this->tableExists('incidencias')) {
            echo "La estoy creando" . PHP_EOL;

            $q = "CREATE TABLE incidencias (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    titulo varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    descripcion text COLLATE utf8_spanish2_ci,
                    fecha datetime DEFAULT NULL,
                    lugar varchar(45) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    keywords varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    id_usuario varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
                    estado varchar(20) COLLATE utf8_spanish2_ci DEFAULT NULL,
                    PRIMARY KEY (id),
                    CONSTRAINT fk_incidencia_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (email) ON DELETE CASCADE ON UPDATE CASCADE
                );";
            $rr = $this->db->query($q); 
        }
    }

    public function get($id) {
        $r = $this->query("SELECT * FROM incidencias WHERE id='" . addslashes($id) ."'");
        return empty($r) ? null : $r;
    }

    // Devuelve el top 3 de los usuarios que mas incidencias han hecho (devuelve el email de cada uno)
    public function getTopUsuarios() {
        $r = $this->query("select id_usuario, count(*) as count 
                            from incidencias 
                            group by id_usuario
                            order by count desc
                            limit 3;");
        return empty($r) ? null : $r;
    }
}