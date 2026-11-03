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
            'contrasena' => password_hash($contrasena, PASSWORD_BCRYPT),
            'rol' => $rol,
        ]);

        return [
            'id' => (int) $this->db->lastInsertId(),
            'cedula' => $cedula,
            'nombre' => $nombre,
            'rol' => $rol,
        ];
    }
   public function validarLogin(string $cedula, string $contrasena): ?array
{
    $sql = 'SELECT id, cedula, nombre, contrasena, rol FROM usuarios WHERE cedula = :cedula LIMIT 1';
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['cedula' => $cedula]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        return null;
    }

    $guardada = $usuario['contrasena'];

    if (strpos($guardada, '$2y$') === 0) {
        $coincide = password_verify($contrasena, $guardada);
    } else {
        $coincide = hash_equals($guardada, $contrasena);
        if ($coincide) {
            $upd = $this->db->prepare('UPDATE usuarios SET contrasena = :hash WHERE id = :id');
            $upd->execute([
                'hash' => password_hash($contrasena, PASSWORD_BCRYPT),
                'id' => $usuario['id'],
            ]);
        }
    }

    if (!$coincide) {
        return null;
    }

    unset($usuario['contrasena']);
    return $usuario;
}

public function obtenerPorId(int $id): ?array
{
    $stmt = $this->db->prepare('SELECT id, cedula, nombre, rol FROM usuarios WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch();
    return $usuario ?: null;
}

public function existeCedulaEnOtro(string $cedula, int $id): bool
{
    $stmt = $this->db->prepare('SELECT 1 FROM usuarios WHERE cedula = :cedula AND id <> :id LIMIT 1');
    $stmt->execute(['cedula' => $cedula, 'id' => $id]);
    return (bool) $stmt->fetchColumn();
}

public function actualizar(int $id, string $cedula, string $nombre, string $rol, string $contrasena = ''): bool
{
    $params = [
        'id' => $id,
        'cedula' => $cedula,
        'nombre' => $nombre,
        'rol' => $rol,
    ];

    if ($contrasena !== '') {
        $sql = 'UPDATE usuarios SET cedula = :cedula, nombre = :nombre, rol = :rol, contrasena = :contrasena WHERE id = :id';
        $params['contrasena'] = password_hash($contrasena, PASSWORD_BCRYPT);
    } else {
        $sql = 'UPDATE usuarios SET cedula = :cedula, nombre = :nombre, rol = :rol WHERE id = :id';
    }

    $stmt = $this->db->prepare($sql);
    return $stmt->execute($params);
}
}