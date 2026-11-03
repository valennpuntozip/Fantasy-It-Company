<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../BackEnd/config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderError(405, 'Método no permitido');
}

iniciarSesion();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

echo json_encode(['ok' => true, 'mensaje' => 'Sesión cerrada']);