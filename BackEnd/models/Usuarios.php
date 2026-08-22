<?php

class Usuarios
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function listar(): array
    {
        $sql = 'SELECT id, cedula, nombre, rol, creado_en
                FROM usuarios
                ORDER BY id ASC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function existeCedula(string $cedula): bool
    {
        $sql = 'SELECT 1 FROM usuarios WHERE cedula = :cedula LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);
        return (bool) $stmt->fetchColumn();
    }
    
    public function eliminar(int $id): bool
{
    $sql = 'DELETE FROM usuarios WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount() > 0;
}

    public function crear(string $cedula, string $nombre, string $contrasena, string $rol = 'Operador'): array
    {
        $sql = 'INSERT INTO usuarios (cedula, nombre, contrasena, rol)
                VALUES (:cedula, :nombre, :contrasena, :rol)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'cedula' => $cedula,
            'nombre' => $nombre,
            'contrasena' => $contrasena,
            'rol' => $rol,
        ]);

        return [
            'id' => (int) $this->db->lastInsertId(),
            'cedula' => $cedula,
            'nombre' => $nombre,
            'rol' => $rol,
        ];
    }
}
