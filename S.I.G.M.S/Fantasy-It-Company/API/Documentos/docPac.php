<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../BackEnd/config/datebase.php';
require_once __DIR__ . '/../../BackEnd/models/Documentos.php';

$model = new Documentos($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $documentos = $model->listar();
        echo json_encode([
            'ok' => true,
            'data' => $documentos,
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