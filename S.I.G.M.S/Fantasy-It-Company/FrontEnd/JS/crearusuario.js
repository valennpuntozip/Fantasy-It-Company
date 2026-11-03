var form = document.getElementById('form-crear-usuario');
var mensaje = document.getElementById('form-mensaje');
var titulo = document.querySelector('.form-card h2');
var btnSubmit = document.querySelector('.btn-submit');
var apiUrl = '../../API/Usuarios/Usuarios.php';

var idEditar = new URLSearchParams(window.location.search).get('id');
var editando = idEditar !== null;

function mostrarMensaje(texto, ok) {
    mensaje.hidden = false;
    mensaje.textContent = texto;
    mensaje.style.color = ok ? '#166534' : '#b91c1c';
}

async function cargarUsuarioParaEditar() {
    titulo.textContent = 'Editar usuario';
    btnSubmit.textContent = 'Guardar cambios';
    document.getElementById('contrasena').required = false;
    document.getElementById('repetir').required = false;
    document.getElementById('contrasena').placeholder = 'Dejar vacío para no cambiarla';

    try {
        var response = await fetch(apiUrl + '?id=' + encodeURIComponent(idEditar));
        var data = await response.json();

        if (!response.ok || !data.ok) {
            mostrarMensaje(data.mensaje || 'No se pudo cargar el usuario.', false);
            return;
        }

        document.getElementById('cedula').value = data.data.cedula;
        document.getElementById('nombre').value = data.data.nombre;
        document.getElementById('rol').value = data.data.rol;
    } catch (error) {
        mostrarMensaje('Error al conectar con el servidor.', false);
    }
}

form.addEventListener('submit', async function (event) {
    event.preventDefault();

    var cedula = document.getElementById('cedula').value.trim();
    var nombre = document.getElementById('nombre').value.trim();
    var contrasena = document.getElementById('contrasena').value;
    var repetir = document.getElementById('repetir').value;
    var rol = document.getElementById('rol').value;

    if (!cedula || !nombre || (!editando && !contrasena)) {
        mostrarMensaje(editando ? 'Completá cédula y nombre.' : 'Completá cédula, nombre y contraseña.', false);
        return;
    }

    if (contrasena !== repetir) {
        mostrarMensaje('Las contraseñas no coinciden.', false);
        return;
    }

    var datos = {
        cedula: cedula,
        nombre: nombre,
        contrasena: contrasena,
        repetir: repetir,
        rol: rol
    };

    if (editando) {
        datos.id = parseInt(idEditar, 10);
    }

    try {
        var response = await fetch(apiUrl, {
            method: editando ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        var data = await response.json();

        if (!response.ok || !data.ok) {
            mostrarMensaje(data.mensaje || 'No se pudo guardar el usuario.', false);
            return;
        }

        mostrarMensaje(data.mensaje || 'Guardado correctamente.', true);

        if (editando) {
            setTimeout(function () {
                window.location.href = 'usGest.html';
            }, 900);
        } else {
            form.reset();
        }
    } catch (error) {
        mostrarMensaje('Error al conectar con el servidor. Revisá que PHP y MySQL estén activos.', false);
    }
});

if (editando) {
    cargarUsuarioParaEditar();
}