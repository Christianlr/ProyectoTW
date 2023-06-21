<?php
require_once "AbstractModel.php";

class IncidenciasModel extends AbstractModel {
    function __construct() { 
        parent::__construct();
        if (!$this->tableExists('incidencias')) 
            $this->createTable(); 
    }

    public function createTable() {
        if (!$this->tableExists('incidencias')) {

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
        return empty($r) ? null : $r[0];
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

    public function crearIncidencia($datos) {
        $consulta = "INSERT INTO incidencias (titulo, descripcion, fecha, lugar, keywords, id_usuario, estado)
                     VALUES (:titulo, :descripcion, :fecha, :lugar, :keywords, :id_usuario, :estado)";
    
        // Preparar los parámetros para la consulta
        $parametros = array(
            ':titulo' => $datos['titulo'],
            ':descripcion' => $datos['descripcion'],
            ':fecha' => $datos['fecha'],
            ':lugar' => $datos['lugar'],
            ':keywords' => $datos['keywords'],
            ':id_usuario' => $datos['usuario'],
            ':estado' => $datos['estado']
        );
    
        // Ejecutar la consulta preparada con los parámetros
        $r = $this->query($consulta, $parametros);
        return $this->lastInsertId();
    }
    
    public function modificarIncidencia($datos) {
        $consulta = "UPDATE incidencias SET titulo = :titulo, descripcion = :descripcion, lugar = :lugar, 
                                            keywords = :keywords WHERE id = :id";
    
        // Preparar los parámetros para la consulta
        $parametros = array(
            ':titulo' => $datos['titulo'],
            ':descripcion' => $datos['descripcion'],
            ':lugar' => $datos['lugar'],
            ':keywords' => $datos['keywords'],
            ':id' => $datos['id']
        );
    
        // Ejecutar la consulta preparada con los parámetros
        $this->query($consulta, $parametros);
    }
    
    public function borrarIncidencia($id) {
        $r = $this->query("DELETE FROM incidencias 
                            WHERE id = '".addslashes($id). "'"
                        );
    }

    public function getAll() {
        $r = $this->query("SELECT * FROM incidencias");
        return empty($r) ? null : $r;
    }

    public function getAllByUser($email) {
        $r = $this->query("SELECT * FROM incidencias WHERE id_usuario = '" . addslashes($email) . "'");
        return empty($r) ? null : $r;
    }

    private function filtradoOrden($condiciones) {
        $datosOrden = array();
        
        if (isset($condiciones['orden'])) {
            if($condiciones['orden'] == 'antiguedad') {
                $datosOrden['select'] = '* ';
                $datosOrden['from'] = 'incidencias ';
                $datosOrden['join'] = '';
                $datosOrden['orderBy'] = 'ORDER BY fecha desc ';
                $datosOrden['groupBy'] = '';
            } else {
                $datosOrden['from'] = 'incidencias i ';
                $datosOrden['join'] = 'LEFT JOIN valoraciones v ON i.id = v.id_incidencia ';
                $datosOrden['groupBy'] = 'GROUP BY i.id ';
                $datosOrden['orderBy'] = 'ORDER BY total_valoraciones DESC ';

                if ($condiciones['orden'] == 'positivos') 
                    $datosOrden['select'] = 'i.id AS id, titulo, descripcion, fecha, lugar, keywords, i.id_usuario, estado, COUNT(CASE WHEN v.valoracion = 1 THEN 1 END) AS total_valoraciones ';                   
                else
                    $datosOrden['select'] = 'i.id AS id, titulo, descripcion, fecha, lugar, keywords, i.id_usuario, estado, (COUNT(CASE WHEN v.valoracion = 1 THEN 1 END) - COUNT(CASE WHEN v.valoracion = 2 THEN 2 END)) AS total_valoraciones ';
            }
        } else {
            $datosOrden['select'] = '*';
            $datosOrden['from'] = 'incidencias';
            $datosOrden['join'] = '';
            $datosOrden['orderBy'] = '';
            $datosOrden['groupBy'] = '';
        }

        return $datosOrden;
    }

    private function filtradoTexto($condiciones) {
        $datosTexto = null;

        if (!empty($condiciones['textoBusqueda'])) {
            $datosTexto = '(';
            $keywords = explode(', ', $condiciones['textoBusqueda']);
            $primerElemento = true;
            
            foreach ($keywords as $parte) {
                if (!$primerElemento) 
                    $datosTexto .= " or ";
                else 
                    $primerElemento = false;
                
                $datosTexto .= " keywords LIKE '%" . trim($parte) . "%'";
            }
            
            $datosTexto .= ") ";
        } 

        return $datosTexto;
    } 

    private function filtradoLugar($condiciones) {
        $datosLugar = null;

        if (!empty($condiciones['lugar'])) {
            $datosLugar = '(';
            $lugares = explode(', ', $condiciones['lugar']);
            $primerElemento = true;
            
            foreach ($lugares as $parte) {
                if (!$primerElemento) 
                    $datosLugar .= " or ";
                else 
                    $primerElemento = false;
                
                $datosLugar .= " lugar LIKE '%" . trim($parte) . "%'";
            }
            
            $datosLugar .= ") ";
        } 

        return $datosLugar;
    } 

    private function filtradoEstado($condiciones) {
        $datosEstado = null;

        if (isset($condiciones['estado'])) {
            $datosEstado = "(";
            $primerElemento = true;

            foreach ($condiciones['estado'] as $estado) {
                if (!$primerElemento) 
                    $datosEstado .= " or ";
                else 
                    $primerElemento = false;

                $datosEstado .= "estado = '" . $estado . "'";
            }

            $datosEstado .= ") ";
        }

        return $datosEstado;
    }

    private function addWhereDatos($condiciones, $datosTexto, $datosLugar, $datosEstado, $propias, $id_usuario) {
        $totalDatosAux = [];
        if ($datosTexto != null) 
            $totalDatosAux[] = $datosTexto;
        if ($datosLugar != null) 
            $totalDatosAux[] = $datosLugar;
        if ($datosEstado != null) 
            $totalDatosAux[] = $datosEstado;
        
        $datosWhere = null;
        $primerElemento = true;
        foreach($totalDatosAux as $dato) {
            if (!$primerElemento) {
                if (strpos($dato, "(estado =") !== false)  
                    $datosWhere .= " AND ";
                else
                    $datosWhere .= " OR ";
            } else {
                $primerElemento = false;
            }

            $datosWhere .= $dato;  
        }
        if ($propias) {
            if (!$primerElemento) 
                $datosWhere .= " AND ";
            if (isset($condiciones['orden']) && $condiciones['orden'] != 'antiguedad')
                $datosWhere .= "i.";

            $datosWhere .= "id_usuario = '" . $id_usuario . "' ";
        }
        

        return $datosWhere;
    }

    private function addClausulaWhere($condiciones, $datosTexto, $datosLugar, $datosEstado, $propias, $id_usuario) {
        $datosWhere = $this->addWhereDatos($condiciones, $datosTexto, $datosLugar, $datosEstado, $propias, $id_usuario);
        if ($datosWhere != null) 
            return " WHERE " . $datosWhere;

        return '';
    }

    public function filtrado($condiciones, $propias, $id_usuario) {
        $datosOrden = $this->filtradoOrden($condiciones);
        $datosTexto = $this->filtradoTexto($condiciones);
        $datosLugar = $this->filtradoLugar($condiciones);
        $datosEstado = $this->filtradoEstado($condiciones);
        $clausulaWhere = $this->addClausulaWhere($condiciones, $datosTexto, $datosLugar, $datosEstado, $propias, $id_usuario);
        
        $consulta = "SELECT " . $datosOrden['select'] .
                    " FROM " . $datosOrden['from'] . 
                    $datosOrden['join'] . 
                    $clausulaWhere . 
                    $datosOrden['groupBy'] . 
                    $datosOrden['orderBy'] . ";" ;

        $r = $this->query($consulta);

        return $r;
    }
    
}