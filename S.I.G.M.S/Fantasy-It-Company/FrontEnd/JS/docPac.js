document.addEventListener('DOMContentLoaded', function () {
    var cardsGrid = document.querySelector('.cards-grid');
    var filterButtons = document.querySelectorAll('.filter-buttons .btn');
    var apiUrl = '../../API/Documentos/docPac.php';
    var todosLosDocumentos = [];
    var categoriaActual = 'todos';

    var etiquetasCategoria = {
        protocolos: 'Protocolos',
        guias: 'Guías',
        formularios: 'Formularios',
    };

    async function cargarDocumentos() {
        cardsGrid.innerHTML = '<p class="loading-msg">Cargando documentos...</p>';

        try {
            var response = await fetch(apiUrl);
            var data = await response.json();

            if (!response.ok || !data.ok) {
                cardsGrid.innerHTML = '<p class="loading-msg">No se pudieron cargar los documentos.</p>';
                return;
            }

            todosLosDocumentos = data.data || [];
            renderizarDocumentos();
        } catch (error) {
            cardsGrid.innerHTML = '<p class="loading-msg">Error al conectar con el servidor.</p>';
        }
    }

    function renderizarDocumentos() {
        var filtrados = categoriaActual === 'todos'
            ? todosLosDocumentos
            : todosLosDocumentos.filter(function (doc) {
                return doc.categoria === categoriaActual;
            });

        if (filtrados.length === 0) {
            cardsGrid.innerHTML = '<p class="loading-msg">No hay documentos en esta categoría.</p>';
            return;
        }

        cardsGrid.innerHTML = filtrados.map(function (doc) {
            var etiqueta = etiquetasCategoria[doc.categoria] || doc.categoria;
            return '<div class="card" data-id="' + doc.id + '" data-archivo="' + (doc.archivo || '') + '">' +
                '<div>' +
                '<div class="card-title">' + doc.titulo + '</div>' +
                '<div class="card-subtitle">' + doc.subtitulo + '</div>' +
                '<div class="card-date">' + doc.creado_en + ' &middot; ' + etiqueta + '</div>' +
                '</div>' +
                '<div class="card-actions">' +
                '<button class="action-btn-view-btn" title="Ver"><img src="../Assets/img/eye.png" alt="Ver"></button>' +
                '<button class="action-btn-share-btn" title="Compartir"><img src="../Assets/img/share.png" alt="Compartir"></button>' +
                '<button class="action-btn-download-btn" title="Descargar"><img src="../Assets/img/arrow-down-to-line.png" alt="Descargar"></button>' +
                '</div>' +
                '</div>';
        }).join('');
    }

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            var texto = btn.textContent.trim().toLowerCase();
            if (texto === 'todos') {
                categoriaActual = 'todos';
            } else if (texto === 'protocolos') {
                categoriaActual = 'protocolos';
            } else if (texto === 'guías' || texto === 'guias') {
                categoriaActual = 'guias';
            } else if (texto === 'formularios') {
                categoriaActual = 'formularios';
            }

            renderizarDocumentos();
        });
    });

    cardsGrid.addEventListener('click', function (event) {
        var card = event.target.closest('.card');
        if (!card) return;

        var archivo = card.getAttribute('data-archivo');
        var titulo = card.querySelector('.card-title').textContent;

        if (event.target.closest('.action-btn-view-btn')) {
            event.stopPropagation();
            if (!archivo) {
                alert('"' + titulo + '" es un documento de ejemplo y todavía no tiene un archivo cargado.');
                return;
            }
            window.open(archivo, '_blank');
        }

        if (event.target.closest('.action-btn-download-btn')) {
            event.stopPropagation();
            if (!archivo) {
                alert('"' + titulo + '" es un documento de ejemplo y todavía no tiene un archivo cargado.');
                return;
            }
            var link = document.createElement('a');
            link.href = archivo;
            link.download = '';
            link.click();
        }

        if (event.target.closest('.action-btn-share-btn')) {
            event.stopPropagation();
            var enlace = archivo
                ? window.location.origin + '/' + archivo
                : window.location.href + '?documento=' + card.getAttribute('data-id');

            navigator.clipboard.writeText(enlace).then(function () {
                alert('Enlace copiado al portapapeles.');
            }).catch(function () {
                alert('No se pudo copiar el enlace: ' + enlace);
            });
        }
    });

    cargarDocumentos();
});