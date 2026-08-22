<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../BackEnd/config/datebase.php';
require_once __DIR__ . '/../../BackEnd/models/Usuarios.php';

$model = new Usuarios($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $usuarios = $model->listar();
        echo json_encode([
            'ok' => true,
            'data' => $usuarios,
        ]);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $cedula = trim((string) ($input['cedula'] ?? ''));
        $nombre = trim((string) ($input['nombre'] ?? ''));
        $contrasena = (string) ($input['contrasena'] ?? '');
        $repetir = (string) ($input['repetir'] ?? $input['repetir_contrasena'] ?? $contrasena);
        $rol = trim((string) ($input['rol'] ?? 'Operador'));

        if ($cedula === '' || $nombre === '' || $contrasena === '') {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Cédula, nombre y contraseña son obligatorios',
            ]);
            exit;
        }

        if ($contrasena !== $repetir) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Las contraseñas no coinciden',
            ]);
            exit;
        }

        if ($model->existeCedula($cedula)) {
            http_response_code(409);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Ya existe un usuario con esa cédula',
            ]);
            exit;
        }

        if ($rol === '') {
            $rol = 'Operador';
        }

        $usuario = $model->crear($cedula, $nombre, $contrasena, $rol);

        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'mensaje' => 'Usuario creado correctamente',
            'data' => $usuario,
        ]);
        exit;
    }

    if ($method === 'DELETE') {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (is_array($input) && isset($input['id'])) {
                $id = (int) $input['id'];
            }
        }

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Id inválido',
            ]);
            exit;
        }

        $eliminado = $model->eliminar($id);

        if (!$eliminado) {
            http_response_code(404);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Usuario no encontrado',
            ]);
            exit;
        }

        echo json_encode([
            'ok' => true,
            'mensaje' => 'Usuario eliminado correctamente',
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Método no permitido',
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error interno del servidor',
    ]);
}