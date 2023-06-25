<?php

require_once '../config/config.php';

/* Connect to database */
class Db {
    private static $instance;
    private static $host;
	private static $db;
	private static $user;
	private static $pass;
    private static $file;

	private function __construct() {}

    private static function connect() {
        self::$host = constant('DB_HOST');
		self::$db = constant('DB');
		self::$user = constant('DB_USER');
		self::$pass = constant('DB_PASS');
        self::$file = constant('DB_BACKUPFILE');

		try {
           $conection = new PDO('mysql:host='.self::$host.'; dbname='.self::$db, self::$user, self::$pass);
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

    public static function crearCopiaDeSeguridad() {
        self::$host = constant('DB_HOST');
		self::$db = constant('DB');
		self::$user = constant('DB_USER');
		self::$pass = constant('DB_PASS');
        self::$file = constant('DB_BACKUPFILE');
        $command = "mysqldump --opt --host=".self::$host. " --user=". self::$user. " --password=" .self::$pass. " " .self::$db. " > " . self::$file;
        exec($command, $output, $returnVar);
    
        if ($returnVar === 0) {
            return true;
        } else {
            return false;
        }
    }
}

?>