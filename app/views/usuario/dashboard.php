<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Buscar Rutas y Paradas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item active">Buscar Rutas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Filtros de búsqueda -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buscar Rutas</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="searchForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="origen">Parada de Origen</label>
                                <select class="form-control select2" id="origen" name="origen">
                                    <option value="">Seleccionar origen</option>
                                    <?php foreach($paradas as $parada): ?>
                                        <?php if($parada['estado'] == 'Activo'): ?>
                                            <option value="<?php echo $parada['id_parada']; ?>">
                                                <?php echo htmlspecialchars($parada['nombre_parada']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="destino">Parada de Destino</label>
                                <select class="form-control select2" id="destino" name="destino">
                                    <option value="">Seleccionar destino</option>
                                    <?php foreach($paradas as $parada): ?>
                                        <?php if($parada['estado'] == 'Activo'): ?>
                                            <option value="<?php echo $parada['id_parada']; ?>">
                                                <?php echo htmlspecialchars($parada['nombre_parada']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hora">Hora aproximada</label>
                                <input type="time" class="form-control" id="hora" name="hora">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="buscarRutas()">
                                <i class="fas fa-search mr-1"></i>Buscar Rutas
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="limpiarBusqueda()">
                                <i class="fas fa-eraser mr-1"></i>Limpiar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resultados de búsqueda -->
        <div class="card" id="resultadosCard" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Rutas Encontradas</h3>
            </div>
            <div class="card-body">
                <div id="resultados">
                    <!-- Los resultados se cargarán aquí dinámicamente -->
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mapa de Rutas</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
        </div>

    </div>
</section>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
// Inicializar Select2
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Seleccionar parada",
        allowClear: true
    });
});

// Inicializar mapa centrado en las coordenadas de las paradas
var map = L.map('map').setView([19.4326, -99.123], 13); // Ciudad de México - centro de las paradas

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Marcadores de paradas
var markers = [];
<?php foreach($paradas as $parada): ?>
    <?php if($parada['estado'] == 'Activo' && $parada['latitud'] && $parada['longitud']): ?>
        var marker = L.marker([<?php echo $parada['latitud']; ?>, <?php echo $parada['longitud']; ?>])
            .addTo(map)
            .bindPopup('<b><?php echo addslashes($parada['nombre_parada']); ?></b><br><small>Lat: <?php echo $parada['latitud']; ?>, Lng: <?php echo $parada['longitud']; ?></small>');
        markers.push(marker);
    <?php endif; ?>
<?php endforeach; ?>

// Ajustar vista del mapa para mostrar todos los marcadores si existen
if (markers.length > 0) {
    var group = new L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.1));
}

function buscarRutas() {
    var origen = $('#origen').val();
    var destino = $('#destino').val();
    var hora = $('#hora').val();

    if (!origen && !destino) {
        alert('Por favor selecciona al menos una parada de origen o destino');
        return;
    }

    // Mostrar loading
    $('#resultados').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando rutas...</div>');
    $('#resultadosCard').show();

    // Llamada AJAX para buscar rutas
    $.ajax({
        url: '<?php echo BASE_URL; ?>/usuario/buscarRutasAjax',
        type: 'GET',
        data: {
            origen: origen,
            destino: destino,
            hora: hora
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarResultados(response.rutas, response.mensaje);
            } else {
                $('#resultados').html(`
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Sin Resultados</h5>
                        <p>${response.mensaje}</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#resultados').html(`
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Error</h5>
                    <p>Error al buscar rutas. Por favor intenta nuevamente.</p>
                </div>
            `);
        }
    });
}

function mostrarResultados(rutas, mensaje) {
    if (rutas.length === 0) {
        $('#resultados').html(`
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> ${mensaje}</h5>
                <p>No se encontraron rutas que conecten las paradas seleccionadas.</p>
            </div>
        `);
        return;
    }

    var html = `
        <div class="alert alert-success">
            <h5><i class="icon fas fa-check"></i> ${mensaje}</h5>
        </div>
        <div class="row">
    `;

    rutas.forEach(function(ruta) {
        html += `
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${ruta.nombre_ruta}</h5>
                        <p class="card-text">
                            <i class="fas fa-clock mr-1"></i>
                            <strong>Horario:</strong> ${ruta.hora_inicio} - ${ruta.hora_final}
                        </p>
                        <button class="btn btn-primary btn-sm" onclick="verEnMapa(${ruta.id_ruta})">
                            <i class="fas fa-map-marker-alt mr-1"></i>Ver en Mapa
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    $('#resultados').html(html);
}

function verEnMapa(idRuta) {
    // Aquí iría la lógica para resaltar la ruta en el mapa
    alert('Funcionalidad para mostrar ruta ' + idRuta + ' en el mapa (pendiente de implementar)');
}

function limpiarBusqueda() {
    $('#searchForm')[0].reset();
    $('.select2').val(null).trigger('change');
    $('#resultadosCard').hide();
    $('#resultados').empty();
}

// Ajustar tamaño del mapa cuando se maximiza la card
$(document).on('maximized.lte.cardwidget', function(event) {
    setTimeout(function() {
        map.invalidateSize();
    }, 100);
});

$(document).on('minimized.lte.cardwidget', function(event) {
    setTimeout(function() {
        map.invalidateSize();
    }, 100);
});
</script>
