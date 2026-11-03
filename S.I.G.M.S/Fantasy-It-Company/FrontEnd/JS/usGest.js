
var tbody = document.getElementById('usuarios-tbody');
var apiUrl = '../../API/Usuarios/Usuarios.php';

async function cargarUsuarios() {
    try {
        var response = await fetch(apiUrl);
        var data = await response.json();

        if (!response.ok || !data.ok) {
            tbody.innerHTML = '<tr><td colspan="4">No se pudieron cargar los usuarios.</td></tr>';
            return;
        }

        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No hay usuarios registrados.</td></tr>';
            return;
        }

        tbody.innerHTML = data.data.map(function (usuario) {
            return '<tr data-id="' + usuario.id + '">' +
                '<td>' + usuario.cedula + '</td>' +
                '<td>' + usuario.nombre + '</td>' +
                '<td><span class="role-tag">' + usuario.rol + '</span></td>' +
                '<td class="acciones-cell">' +
                '<div class="menu-acciones">' +
                '<button type="button" class="btn-puntos" aria-haspopup="true" aria-expanded="false">&#8942;</button>' +
                '<div class="dropdown-menu">' +
                '<button type="button" class="btn-eliminar">Eliminar</button>' +
                '</div>' +
                '</div>' +
                '</td>' +
                '</tr>';
        }).join('');
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="4">Error al conectar con el servidor.</td></tr>';
    }
}

function cerrarTodosLosMenus() {
    document.querySelectorAll('.dropdown-menu.is-open').forEach(function (menu) {
        menu.classList.remove('is-open');
    });
    document.querySelectorAll('.btn-puntos[aria-expanded="true"]').forEach(function (btn) {
        btn.setAttribute('aria-expanded', 'false');
    });
}

async function eliminarUsuario(id, fila) {
    try {
        var response = await fetch(apiUrl + '?id=' + encodeURIComponent(id), {
            method: 'DELETE',
        });
        var data = await response.json();

        if (!response.ok || !data.ok) {
            alert(data.mensaje || 'No se pudo eliminar el usuario.');
            return;
        }

        fila.remove();

        if (!tbody.querySelector('tr')) {
            tbody.innerHTML = '<tr><td colspan="4">No hay usuarios registrados.</td></tr>';
        }
    } catch (error) {
        alert('Error al conectar con el servidor.');
    }
}

tbody.addEventListener('click', function (event) {
    var puntosBtn = event.target.closest('.btn-puntos');
    if (puntosBtn) {
        var menu = puntosBtn.nextElementSibling;
        var yaAbierto = menu.classList.contains('is-open');
        cerrarTodosLosMenus();
        if (!yaAbierto) {
            menu.classList.add('is-open');
            puntosBtn.setAttribute('aria-expanded', 'true');
        }
        return;
    }

    var eliminarBtn = event.target.closest('.btn-eliminar');
    if (eliminarBtn) {
        var fila = eliminarBtn.closest('tr');
        var id = fila.getAttribute('data-id');
        var nombre = fila.querySelector('td:nth-child(2)').textContent;

        cerrarTodosLosMenus();

        if (!confirm('¿Eliminar a "' + nombre + '"? Esta acción no se puede deshacer.')) {
            return;
        }

        eliminarUsuario(id, fila);
    }
});

document.addEventListener('click', function (event) {
    if (!event.target.closest('.menu-acciones')) {
        cerrarTodosLosMenus();
    }
});

cargarUsuarios();