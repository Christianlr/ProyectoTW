<?php
require_once 'model/Db.php';

abstract class AbstractModel {
    protected $db;

    public function __construct() {
        $this->db = Db::getInstance();
    }

    public function getCurrentSchema() {
        $stmt = $this->db->query("SELECT DATABASE() AS current_schema");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['current_schema'];
    }

    public function query($select, $params=[]) {
        try {
            if (empty($params))
                $pq = $this->db->query($select);
            else {
                $pq = $this->db->prepare($select);
                $pq = execute($params);
            }
            $result = $pq->fetch(PDO::FETCH_ASSOC); 
            
        } catch (PDOException $e) {
            $result = null;
        }
        return $result;
    }

    public function tableExists($tab) {
        $existe = $this->query("SELECT COUNT(*) AS C FROM information_schema.tables 
                                WHERE table_schema='".$this->getCurrentSchema()."' AND
                                table_name='".$tab."'");
        return ($existe['C']==0) ? false : true;
    }

    public function columnCount($tableName) {
        $q = $this->query("SELECT COUNT(*) AS C FROM information_schema.columns WHERE table_name='$tableName'");
        return $q['C'];
    }

    public abstract function get($publicKey);
    public abstract function createTable();
}

?>