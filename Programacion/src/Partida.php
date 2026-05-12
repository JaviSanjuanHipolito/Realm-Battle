<?php

require_once __DIR__ . '/Database.php';

class Partida {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    // Crea una partida nueva y devuelve su id
    public function crearPartida(): int {
        $this->pdo->query("INSERT INTO Partidas (estado) VALUES ('en curso')");
        return (int) $this->pdo->lastInsertId();
    }

    // Guarda qué usuario eligió qué personaje en la partida
    public function guardarEleccion(int $idPartida, int $idUsuario, string $nombrePersonaje): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO Personajes_Partidas (id_partida, nombre_personaje, id_usuario) 
             VALUES (:id_partida, :nombre_personaje, :id_usuario)"
        );
        return $stmt->execute([
            ':id_partida'       => $idPartida,
            ':nombre_personaje' => $nombrePersonaje,
            ':id_usuario'       => $idUsuario
        ]);
    }

    // Atacar: devuelve el daño hecho
    public function atacar(array $atacante, array $defensor): int {
        $dano = $atacante['ataque'] - $defensor['defensa'];
        return max(5, $dano); // mínimo 5 de daño siempre
    }

    // Curarse: recupera 20 de salud sin pasarse del máximo original
    public function curarse(int $saludActual, int $saludMaxima): int {
        $nueva = $saludActual + 20;
        return min($nueva, $saludMaxima); // no puede superar la salud máxima
    }

    // Subir defensa: suma 5 puntos de defensa permanentemente
    public function subirDefensa(int $defensaActual): int {
        return $defensaActual + 5;
    }

    // Comprueba si un personaje ha muerto
    public function estaKO(int $salud): bool {
        return $salud <= 0;
    }

    // Guarda el ganador y cierra la partida
    public function cerrarPartida(int $idPartida, int $idGanador): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE Partidas 
             SET estado = 'finalizada', id_ganador = :id_ganador 
             WHERE id_partida = :id_partida"
        );
        return $stmt->execute([
            ':id_ganador' => $idGanador,
            ':id_partida' => $idPartida
        ]);
    }
}