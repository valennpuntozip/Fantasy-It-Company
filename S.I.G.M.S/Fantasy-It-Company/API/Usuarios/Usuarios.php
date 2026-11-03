<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../BackEnd/config/datebase.php';
require_once __DIR__ . '/../../BackEnd/config/auth.php';
require_once __DIR__ . '/../../BackEnd/models/Usuarios.php';

$model = new Usuarios($pdo);
$method = $_SERVER['REQUEST_METHOD'];
$rolesValidos = ['Administrador', 'Supervisor', 'Operador'];

try {
    // Todo lo de este archivo es solo para Administradores
    $actual = requerirRol(['Administrador']);

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $usuario = $model->obtenerPorId((int) $_GET['id']);
            if (!$usuario) {
                responderError(404, 'Usuario no encontrado');
            }
            echo json_encode(['ok' => true, 'data' => $usuario]);
            exit;
        }

        echo json_encode(['ok' => true, 'data' => $model->listar()]);
        exit;
    }

    if ($method === 'POST' || $method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $cedula = trim((string) ($input['cedula'] ?? ''));
        $nombre = trim((string) ($input['nombre'] ?? ''));
        $contrasena = (string) ($input['contrasena'] ?? '');
        $repetir = (string) ($input['repetir'] ?? $contrasena);
        $rol = trim((string) ($input['rol'] ?? 'Operador'));

        if ($cedula === '' || $nombre === '') {
            responderError(400, 'Cédula y nombre son obligatorios');
        }

        if (!in_array($rol, $rolesValidos, true)) {
            responderError(400, 'Rol inválido');
        }

        if ($contrasena !== $repetir) {
            responderError(400, 'Las contraseñas no coinciden');
        }

        if ($method === 'POST') {
            if ($contrasena === '') {
                responderError(400, 'La contraseña es obligatoria');
            }

            if ($model->existeCedula($cedula)) {
                responderError(409, 'Ya existe un usuario con esa cédula');
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

        // PUT: modificar un usuario existente
        $id = (int) ($input['id'] ?? 0);

        if ($id <= 0 || !$model->obtenerPorId($id)) {
            responderError(404, 'Usuario no encontrado');
        }

        if ($model->existeCedulaEnOtro($cedula, $id)) {
            responderError(409, 'Ya existe otro usuario con esa cédula');
        }

        if ($id === (int) $actual['id'] && $rol !== 'Administrador') {
            responderError(400, 'No puede quitarse a sí mismo el rol de Administrador');
        }

        $model->actualizar($id, $cedula, $nombre, $rol, $contrasena);

        if ($id === (int) $actual['id']) {
            $_SESSION['usuario'] = $model->obtenerPorId($id);
        }

        echo json_encode(['ok' => true, 'mensaje' => 'Usuario actualizado correctamente']);
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
            responderError(400, 'Id inválido');
        }

        if ($id === (int) $actual['id']) {
            responderError(400, 'No puede eliminar su propio usuario');
        }

        if (!$model->eliminar($id)) {
            responderError(404, 'Usuario no encontrado');
        }

        echo json_encode(['ok' => true, 'mensaje' => 'Usuario eliminado correctamente']);
        exit;
    }

    responderError(405, 'Método no permitido');
} catch (Throwable $e) {
    responderError(500, 'Error interno del servidor');
}