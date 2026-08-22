<?php

class Documentos
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function listar(): array
    {
        $sql = 'SELECT id, titulo, subtitulo, categoria, archivo, creado_en
                FROM documentos
                ORDER BY creado_en DESC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}