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

document.addEventListener('DOMContentLoaded', function () {
    var usuario = requerirSesion();

    var nombreEl = document.getElementById('user-menu-name');
    if (nombreEl && usuario) {
        nombreEl.textContent = usuario.nombre;
    }

    var toggleBtn = document.getElementById('user-menu-toggle');
    var dropdown = document.getElementById('user-menu-dropdown');

    if (toggleBtn && dropdown) {
        toggleBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            var yaAbierto = dropdown.classList.contains('is-open');
            dropdown.classList.remove('is-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
            if (!yaAbierto) {
                dropdown.classList.add('is-open');
                toggleBtn.setAttribute('aria-expanded', 'true');
            }
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.user-menu')) {
                dropdown.classList.remove('is-open');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    var btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', function (event) {
            event.preventDefault();
            cerrarSesion();
        });
    }
});