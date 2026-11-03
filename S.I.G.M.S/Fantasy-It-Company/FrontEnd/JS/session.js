var API_SESION = '../../API/Usuarios/Sesion.php';
var API_LOGOUT = '../../API/Usuarios/Logout.php';

var PAGINAS_SOLO_ADMIN = ['usGest.html', 'Crearusuario.html'];
var ELEMENTOS_SOLO_ADMIN = ['#nav-usuarios', 'a.card[href="usGest.html"]'];

async function obtenerUsuarioActual() {
    try {
        var response = await fetch(API_SESION, { cache: 'no-store' });
        if (!response.ok) {
            return null;
        }
        var data = await response.json();
        return data.ok ? data.data : null;
    } catch (error) {
        return null;
    }
}

async function cerrarSesion() {
    try {
        await fetch(API_LOGOUT, { method: 'POST' });
    } catch (error) {
    }
    window.location.href = 'index.html';
}

function esPaginaSoloAdmin() {
    var actual = window.location.pathname.split('/').pop().toLowerCase();
    return PAGINAS_SOLO_ADMIN.some(function (pagina) {
        return pagina.toLowerCase() === actual;
    });
}

function aplicarRoles(usuario) {
    if (usuario.rol === 'Administrador') {
        return;
    }

    ELEMENTOS_SOLO_ADMIN.forEach(function (selector) {
        document.querySelectorAll(selector).forEach(function (elemento) {
            elemento.remove();
        });
    });
}

function configurarMenuUsuario(usuario) {
    var nombreEl = document.getElementById('user-menu-name');
    if (nombreEl) {
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
}

document.addEventListener('DOMContentLoaded', async function () {
    var usuario = await obtenerUsuarioActual();

    if (!usuario) {
        window.location.href = 'index.html';
        return;
    }

    if (usuario.rol !== 'Administrador' && esPaginaSoloAdmin()) {
        window.location.href = 'botones.html';
        return;
    }

    aplicarRoles(usuario);
    configurarMenuUsuario(usuario);
});