function validarCedula(cedula) {
    if (!/^[0-9]{6,10}$/.test(cedula)) {
        return 'La cédula debe tener solo números (entre 6 y 10 dígitos).';
    }
    return null;
}

function validarCedulaLogin(cedula) {
    if (!/^[0-9]{1,20}$/.test(cedula)) {
        return 'La cédula debe tener solo números.';
    }
    return null;
}

function validarNombre(nombre) {
    if (!/^\p{L}[\p{L} .'-]{1,119}$/u.test(nombre)) {
        return 'El nombre solo puede tener letras, espacios, punto, apóstrofe y guion (de 2 a 120 caracteres).';
    }
    return null;
}

function validarContrasena(contrasena) {
    if (contrasena.length < 6 || contrasena.length > 72) {
        return 'La contraseña debe tener entre 6 y 72 caracteres.';
    }
    return null;
}