<?php

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'ok' => true,
    'mensaje' => 'API S.I.G.S.M',
    'endpoints' => [
        'usuarios' => 'Usuarios/Usuarios.php',
    ],
]);
