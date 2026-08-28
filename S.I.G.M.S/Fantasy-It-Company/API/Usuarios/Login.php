<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../BackEnd/config/datebase.php';
require_once __DIR__ . '/../../BackEnd/models/Usuarios.php';

$model = new Usuarios($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Método no permitido',
    ]);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $cedula = trim((string) ($input['cedula'] ?? ''));
    $nombre = trim((string) ($input['nombre'] ?? ''));

    if ($cedula === '' || $nombre === '') {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'mensaje' => 'Cédula y nombre son obligatorios',
        ]);
        exit;
    }

    $usuario = $model->validarLogin($cedula, $nombre);

    if (!$usuario) {
        http_response_code(401);
        echo json_encode([
            'ok' => false,
            'mensaje' => 'Cédula o nombre incorrectos',
        ]);
        exit;
    }

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Inicio de sesión correcto',
        'data' => $usuario,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'mensaje' => 'Error interno del servidor',
    ]);
}