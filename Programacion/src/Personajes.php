<?php

require_once __DIR__ . '/Database.php';

class Personajes {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    // Devuelve todos los personajes de la BD
    public function getTodos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Personajes");
        return $stmt->fetchAll();
    }

    // Devuelve un personaje por su nombre
    public function getPersonaje(string $nombre): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM Personajes WHERE nombre_personaje = :nombre"
        );
        $stmt->execute([':nombre' => $nombre]);
        return $stmt->fetch() ?: null;
    }
}