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
        // $command = "mysqldump --opt --host=".self::$host. " --user=". self::$user. " --password=" .self::$pass. " " .self::$db. " > " . self::$file;
        // exec($command, $output, $returnVar);
    
        // if ($returnVar === 0) {
        //     return true;
        // } else {
        //     return false;
        // }
        return true;
    }

    public static function restaurarCopiaDeSeguridad($sql) {
        // try {
        //     $pdo = self::$instance;
    
        //     // Iniciar una transacción
        //     $pdo->beginTransaction();
    
        //     // Ejecutar las instrucciones SQL
        //     $pdo->exec($sql);
    
        //     // Confirmar la transacción
        //     $pdo->commit();
    
        //     return true;
        // } catch (PDOException $e) {
        //     // Revertir la transacción en caso de error
        //     $pdo->rollBack();
    
        //     echo "Error al restaurar la base de datos: " . $e->getMessage();
        //     return false;
        // }
        return true;
    }

    public static function borrarDb() {
        // try {
        //     // Establecer la conexión con la base de datos utilizando PDO
        //     $pdo = self::$instance;
    
        //     // Obtener el nombre de la base de datos
        //     $database = self::$db;
    
        //     // Obtener las tablas de la base de datos
        //     $query = "SHOW TABLES";
        //     $statement = $pdo->query($query);
        //     $tables = $statement->fetchAll(PDO::FETCH_COLUMN);
    
        //     // Desactivar el modo estricto de SQL para evitar errores durante la eliminación de las tablas
        //     $pdo->exec("SET sql_mode = ''");
    
        //     // Eliminar cada tabla de la base de datos
        //     foreach ($tables as $table) {
        //         $pdo->exec("DROP TABLE IF EXISTS $table");
        //     }
        //     $pdo->exec("DROP TABLE IF EXISTS incidencias");
        //     $pdo->exec("DROP TABLE IF EXISTS usuarios");

        //     return true;
        // } catch (PDOException $e) {
        //     echo "Error al borrar las tablas de la base de datos: " . $e->getMessage();
        //     return false;
        // }
    
        // // Cerrar la conexión con la base de datos
        // $pdo = null;

        return true;
    }
}

?>