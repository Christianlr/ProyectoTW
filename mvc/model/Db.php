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
            // Establecer la conexión con MySQL sin seleccionar la base de datos
            $connection = new PDO('mysql:host='.self::$host, self::$user, self::$pass);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Verificar si la base de datos existe
            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :database";
            $statement = $connection->prepare($query);
            $statement->bindParam(':database', self::$db);
            $statement->execute();
    
            if ($statement->rowCount() === 0) {
                // La base de datos no existe, se crea
                $createDatabaseQuery = "CREATE DATABASE ".self::$db." CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                $connection->exec($createDatabaseQuery);
            }
    
            // Cerrar la conexión sin seleccionar la base de datos
            $connection = null;
    
            // Establecer una nueva conexión con la base de datos ya existente
            $connection = new PDO('mysql:host='.self::$host.'; dbname='.self::$db, self::$user, self::$pass);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            return $connection;
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
        $command = "mysqldump --opt --host=".self::$host. " --user=". self::$user. " --password=" .self::$pass. " " .self::$db. " > " . self::$file;
        exec($command, $output, $returnVar);
    
        if ($returnVar === 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function restaurarCopiaDeSeguridad($sql) {
        try {
            $pdo = self::$instance;
    
            // Iniciar una transacción
            $pdo->beginTransaction();
    
            // Ejecutar las instrucciones SQL
            $pdo->exec($sql);
    
            // Confirmar la transacción
            $pdo->commit();
    
            return true;
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $pdo->rollBack();
    
            echo "Error al restaurar la base de datos: " . $e->getMessage();
            return false;
        }
    }

    public static function borrarDb() {

        try {
            // Establecer la conexión con la base de datos utilizando PDO
            $pdo = self::$instance;

            // Desactivar el modo estricto de SQL para evitar errores durante la eliminación de la base de datos
            $pdo->exec("SET sql_mode = ''");

            // Ejecutar la consulta para eliminar la base de datos
            $pdo->exec("DROP DATABASE IF EXISTS " . self::$db);

            echo "La base de datos ha sido borrada exitosamente";
            return true;
        } catch (PDOException $e) {
            echo "Error al borrar la base de datos: " . $e->getMessage();
            return false;
        }

        // Cerrar la conexión con la base de datos
        $pdo = null;
    }
}

?>