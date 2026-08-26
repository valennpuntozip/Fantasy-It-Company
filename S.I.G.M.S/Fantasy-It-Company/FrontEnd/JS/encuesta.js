document.addEventListener('DOMContentLoaded', function () {
    var apiUrl = '../../API/Encuestas/encuestas.php';
    var servicioSelect = document.getElementById('servicio');
    var ratingButtons = document.querySelectorAll('.rating-btn');
    var btnEnviar = document.getElementById('btn-enviar-encuesta');
    var feedback = document.getElementById('survey-feedback');
    var statSatisfaccion = document.getElementById('stat-satisfaccion');
    var statCompletadas = document.getElementById('stat-completadas');
    var puntajeSeleccionado = null;

    function actualizarEstadisticas(stats) {
        statSatisfaccion.textContent = stats.promedio > 0 ? stats.promedio + '/5' : '-/5';
        statCompletadas.textContent = stats.total;
    }

    async function cargarEstadisticas() {
        try {
            var response = await fetch(apiUrl);
            var data = await response.json();
            if (response.ok && data.ok) {
                actualizarEstadisticas(data.data);
            }
        } catch (error) {
            // si falla, se quedan los valores por defecto
        }
    }

    ratingButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            ratingButtons.forEach(function (b) { b.classList.remove('is-selected'); });
            btn.classList.add('is-selected');
            puntajeSeleccionado = parseInt(btn.getAttribute('data-puntaje'), 10);
        });
    });

    btnEnviar.addEventListener('click', async function () {
        feedback.textContent = '';
        feedback.className = 'survey-feedback';

        if (!puntajeSeleccionado) {
            feedback.textContent = 'Elegí una carita para calificar el servicio.';
            feedback.classList.add('survey-feedback--error');
            return;
        }

        var servicio = servicioSelect.value;
        btnEnviar.disabled = true;

        try {
            var response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ servicio: servicio, puntaje: puntajeSeleccionado }),
            });
            var data = await response.json();

            if (!response.ok || !data.ok) {
                feedback.textContent = data.mensaje || 'No se pudo enviar la encuesta.';
                feedback.classList.add('survey-feedback--error');
                return;
            }

            feedback.textContent = '¡Gracias por tu opinión!';
            feedback.classList.add('survey-feedback--success');

            ratingButtons.forEach(function (b) { b.classList.remove('is-selected'); });
            puntajeSeleccionado = null;
            servicioSelect.selectedIndex = 0;

            actualizarEstadisticas(data.estadisticas);
        } catch (error) {
            feedback.textContent = 'Error al conectar con el servidor.';
            feedback.classList.add('survey-feedback--error');
        } finally {
            btnEnviar.disabled = false;
        }
    });

    cargarEstadisticas();
});