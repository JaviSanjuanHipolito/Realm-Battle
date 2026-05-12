<?php

require_once __DIR__ . '/Database.php';

class Usuario {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function registro(string $nombre, string $contrasena): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO Usuarios (nombre, contrasena) VALUES (:nombre, :contrasena)"
        );
        return $stmt->execute([
            ':nombre'    => $nombre,
            ':contrasena' => password_hash($contrasena, PASSWORD_DEFAULT)
        ]);
    }

    public function login(string $nombre, string $contrasena): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM Usuarios WHERE nombre = :nombre"
        );
        $stmt->execute([':nombre' => $nombre]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            return $usuario;
        }
        return null;
    }

    public function getRanking(): array {
        $stmt = $this->pdo->query(
            "SELECT u.nombre, COUNT(p.id_ganador) AS victorias
             FROM Usuarios u
             LEFT JOIN Partidas p ON p.id_ganador = u.id_usuario
             GROUP BY u.id_usuario, u.nombre
             ORDER BY victorias DESC"
        );
        return $stmt->fetchAll();
    }
}