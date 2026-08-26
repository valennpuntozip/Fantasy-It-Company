<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../BackEnd/config/datebase.php';
require_once __DIR__ . '/../../BackEnd/models/Encuestas.php';

$model = new Encuestas($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $estadisticas = $model->estadisticas();
        echo json_encode([
            'ok' => true,
            'data' => $estadisticas,
        ]);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $servicio = trim((string) ($input['servicio'] ?? ''));
        $puntaje = (int) ($input['puntaje'] ?? 0);

        if ($servicio === '') {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'El servicio es obligatorio',
            ]);
            exit;
        }

        if ($puntaje < 1 || $puntaje > 5) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'mensaje' => 'El puntaje debe ser entre 1 y 5',
            ]);
            exit;
        }

        $encuesta = $model->crear($servicio, $puntaje);
        $estadisticas = $model->estadisticas();

        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'mensaje' => 'Encuesta enviada correctamente',
            'data' => $encuesta,
            'estadisticas' => $estadisticas,
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