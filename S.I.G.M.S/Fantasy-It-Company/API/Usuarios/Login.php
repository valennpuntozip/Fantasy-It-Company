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
require_once __DIR__ . '/../../BackEnd/config/auth.php';
require_once __DIR__ . '/../../BackEnd/config/validaciones.php';
require_once __DIR__ . '/../../BackEnd/models/Usuarios.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderError(405, 'Método no permitido');
}

try {
    $model = new Usuarios($pdo);

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $cedula = trim((string) ($input['cedula'] ?? ''));
    $contrasena = (string) ($input['contrasena'] ?? '');

    if ($cedula === '' || $contrasena === '') {
        responderError(400, 'Cédula y contraseña son obligatorias');
    }

    $error = validarCedulaLogin($cedula);
    if ($error !== null) {
        responderError(400, $error);
    }

    $usuario = $model->validarLogin($cedula, $contrasena);

    if (!$usuario) {
        responderError(401, 'Cédula o contraseña incorrectas');
    }

    iniciarSesion();
    session_regenerate_id(true);
    $_SESSION['usuario'] = $usuario;

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Inicio de sesión correcto',
        'data' => $usuario,
    ]);
} catch (Throwable $e) {
    responderError(500, 'Error interno del servidor');
}