<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Crear Nueva Ruta</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/rutas">Rutas</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Datos de la Ruta</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/rutas" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a la lista
                    </a>
                </div>
            </div>
            <form action="<?php echo BASE_URL; ?>/rutas/crear" method="post">
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><i class="icon fas fa-ban"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nombre_ruta">Nombre de la Ruta *</label>
                        <input type="text" class="form-control" id="nombre_ruta" name="nombre_ruta" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora_inicio">Hora Inicio *</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora_final">Hora Final *</label>
                                <input type="time" class="form-control" id="hora_final" name="hora_final" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_linea">Línea</label>
                        <select name="id_linea" id="id_linea" class="form-control">
                            <option value="">-- Seleccionar línea --</option>
                            <?php if(isset($lineas) && $lineas->rowCount() > 0): while($l = $lineas->fetch()): ?>
                                <option value="<?php echo $l['id_linea']; ?>"><?php echo htmlspecialchars($l['nombre_linea']); ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                     <div class="form-group">
                        <label>Trazado de la Ruta</label>
                        <div id="map" style="height: 400px; width: 100%;"></div>
                        <p class="text-muted small mt-2">
                            Utilice las herramientas de la izquierda para dibujar la ruta (línea). Solo se guardará la última ruta dibujada.
                        </p>
                        <input type="hidden" name="puntos" id="puntos">
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Crear Ruta</button>
                    <a href="<?php echo BASE_URL; ?>/rutas" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var map = L.map('map').setView([-16.5, -68.15], 13); // Coordenadas para La Paz, Bolivia

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems
        },
        draw: {
            polyline: {
                shapeOptions: {
                    color: '#f357a1',
                    weight: 4
                }
            },
            polygon: false,
            rectangle: false,
            circle: false,
            marker: false,
            circlemarker: false
        }
    });
    map.addControl(drawControl);

    map.on(L.Draw.Event.CREATED, function (event) {
        var layer = event.layer;
        
        // Limpiar capas anteriores para asegurar que solo haya una ruta
        drawnItems.clearLayers();
        drawnItems.addLayer(layer);

        var latlngs = layer.getLatLngs();
        var puntosArray = latlngs.map(function(latlng) {
            return [latlng.lat, latlng.lng];
        });

        // Actualizar el campo oculto con las coordenadas
        document.getElementById('puntos').value = JSON.stringify(puntosArray);
    });
     map.on(L.Draw.Event.EDITED, function (event) {
        var layers = event.layers;
        layers.eachLayer(function (layer) {
            // Extraer las coordenadas de la capa editada
            var latlngs = layer.getLatLngs();
             var puntosArray = latlngs.map(function(latlng) {
            return [latlng.lat, latlng.lng];
        });
             document.getElementById('puntos').value = JSON.stringify(puntosArray);
        });
    });

});
</script>

