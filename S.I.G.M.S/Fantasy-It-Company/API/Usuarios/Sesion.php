<?php

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../BackEnd/config/auth.php';

$usuario = usuarioActual();

if (!$usuario) {
    responderError(401, 'No hay sesión activa');
}

echo json_encode(['ok' => true, 'data' => $usuario]);