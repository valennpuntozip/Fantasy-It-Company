<?php

function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function usuarioActual(): ?array
{
    iniciarSesion();
    return $_SESSION['usuario'] ?? null;
}

function responderError(int $codigo, string $mensaje): void
{
    http_response_code($codigo);
    echo json_encode(['ok' => false, 'mensaje' => $mensaje]);
    exit;
}

function requerirLogin(): array
{
    $usuario = usuarioActual();
    if (!$usuario) {
        responderError(401, 'Debe iniciar sesión');
    }
    return $usuario;
}

function requerirRol(array $roles): array
{
    $usuario = requerirLogin();
    if (!in_array($usuario['rol'], $roles, true)) {
        responderError(403, 'No tiene permisos para esta acción');
    }
    return $usuario;
}