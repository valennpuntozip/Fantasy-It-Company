<?php

class Encuestas
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function crear(string $servicio, int $puntaje): array
    {
        $sql = 'INSERT INTO encuestas (servicio, puntaje) VALUES (:servicio, :puntaje)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'servicio' => $servicio,
            'puntaje' => $puntaje,
        ]);

        return [
            'id' => (int) $this->db->lastInsertId(),
            'servicio' => $servicio,
            'puntaje' => $puntaje,
        ];
    }

    public function estadisticas(): array
    {
        $sql = 'SELECT COUNT(*) AS total, AVG(puntaje) AS promedio FROM encuestas';
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();

        return [
            'total' => (int) $row['total'],
            'promedio' => $row['promedio'] !== null ? round((float) $row['promedio'], 1) : 0,
        ];
    }
}