function obtenerUsuarioActual() {
    var data = sessionStorage.getItem('usuarioActual');
    return data ? JSON.parse(data) : null;
}

function requerirSesion() {
    var usuario = obtenerUsuarioActual();
    if (!usuario) {
        window.location.href = 'index.html';
    }
    return usuario;
}

function cerrarSesion() {
    sessionStorage.removeItem('usuarioActual');
    window.location.href = 'index.html';
}