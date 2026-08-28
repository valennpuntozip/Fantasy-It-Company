document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-admin');
    var btnLogin = document.getElementById('btn-login');
    var errorMsg = document.getElementById('login-error');
    var apiUrl = '../../API/Usuarios/Login.php';

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        errorMsg.textContent = '';

        var cedula = document.getElementById('cedula').value.trim();
        var nombre = document.getElementById('nombre').value.trim();

        if (cedula === '' || nombre === '') {
            errorMsg.textContent = 'Completá ambos campos.';
            return;
        }

        btnLogin.disabled = true;
        btnLogin.textContent = 'Verificando...';

        try {
            var response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cedula: cedula, nombre: nombre }),
            });
            var data = await response.json();

            if (!response.ok || !data.ok) {
                errorMsg.textContent = data.mensaje || 'No se pudo iniciar sesión.';
                return;
            }

            sessionStorage.setItem('usuarioActual', JSON.stringify(data.data));
            window.location.href = 'botones.html';
        } catch (error) {
            errorMsg.textContent = 'Error al conectar con el servidor.';
        } finally {
            btnLogin.disabled = false;
            btnLogin.textContent = 'Iniciar sesión';
        }
    });
});