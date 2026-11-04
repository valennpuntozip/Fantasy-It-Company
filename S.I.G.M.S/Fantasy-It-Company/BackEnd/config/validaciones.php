<?php

function validarCedula(string $cedula): ?string
{
    if (!preg_match('/^[0-9]{6,10}$/', $cedula)) {
        return 'La cédula debe tener solo números (entre 6 y 10 dígitos)';
    }
    return null;
}

function validarCedulaLogin(string $cedula): ?string
{
    if (!preg_match('/^[0-9]{1,20}$/', $cedula)) {
        return 'La cédula debe tener solo números';
    }
    return null;
}

function validarNombre(string $nombre): ?string
{
    if (!preg_match('/^\p{L}[\p{L} .\'-]{1,119}$/u', $nombre)) {
        return 'El nombre solo puede tener letras, espacios, punto, apóstrofe y guion (de 2 a 120 caracteres)';
    }
    return null;
}

function validarContrasena(string $contrasena): ?string
{
    $largo = strlen($contrasena);
    if ($largo < 6 || $largo > 72) {
        return 'La contraseña debe tener entre 6 y 72 caracteres';
    }
    return null;
}