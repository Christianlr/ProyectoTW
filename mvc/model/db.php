<?php

require_once 'config/config.php';

/* Connect to database */
class Db {
    private static $instance;

    private static $host;
	private static $db;
	private static $user;
	private static $pass;

	private function __construct() {}

    private static function connect() {
        $this->host = constant('DB_HOST');
		$this->db = constant('DB');
		$this->user = constant('DB_USER');
		$this->pass = constant('DB_PASS');

		try {
           $conection = new PDO('mysql:host='.$this->host.'; dbname='.$this->db, $this->user, $this->pass);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        return $conection;
    }

    public static function getInstance() {
        if (!self::$instance)
            self::$instance = self::connect();
        
        return self::$instance;
    }
}

?>