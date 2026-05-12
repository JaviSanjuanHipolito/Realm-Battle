<?php

class Database {
    private static $instancia = null;

    private string $host     = 'db';
    private string $dbname   = 'Proyecto_final';
    private string $user     = 'root';
    private string $password = 'root';

    private PDO $conexion;

    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";

        try {
            $this->conexion = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia->conexion;
    }
}