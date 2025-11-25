<!-- Mapa de Rutas Interactivo -->
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt mr-2"></i>Mapa de Rutas Interactivo
                    </h3>
                </div>
                <div class="card-body p-2">
                    <div class="buttons text-center mb-3">
                        <button id="btnParada" class="btn btn-primary mr-2">
                            <i class="fas fa-map-marker-alt"></i> Añadir Parada
                        </button>
                        <button id="btnRuta" class="btn btn-success mr-2">
                            <i class="fas fa-route"></i> Crear Ruta
                        </button>
                        <button id="btnRutaFollow" class="btn btn-outline-secondary mr-2" title="Generar ruta siguiendo calles">
                            <i class="fas fa-road"></i> Seguir Calles
                        </button>
                        <button id="btnUbicacion" class="btn btn-info mr-2">
                            <i class="fas fa-crosshairs"></i> Mi Ubicación
                        </button>
                        <button id="btnOptima" class="btn btn-warning">
                            <i class="fas fa-star"></i> Parada más cercana
                        </button>
                    </div>
                    <div id="map-full" style="height: 600px; width: 100%; border-radius: 5px; border: 1px solid #e3f2fd;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/leaflet/leaflet.css" />
<!-- Leaflet JS -->
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/leaflet/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('map-full')) {
        const map = L.map('map-full').setView([-16.5, -68.15], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let modoParada = false;
        let modoRuta = false;
        let puntosRuta = [];
        let paradas = [];
        let rutas = [];
        let userMarker = null;
        let rutaPolylines = [];
        // temporary layer used while drawing a new route (so we can remove it/replace it)
        let tempRouteLayer = null;

        // Cargar paradas existentes
        async function cargarParadas() {
            try {
                const res = await fetch('<?php echo BASE_URL; ?>/mapa/getParadas');
                const data = await res.json();
                paradas = data;
                data.forEach(p => {
                    L.marker([p.latitud, p.longitud]).addTo(map)
                        .bindPopup(`<b>${p.nombre_parada}</b><br>ID: ${p.id_parada}`);
                });
            } catch (error) {
                console.error('Error cargando paradas:', error);
            }
        }

        // Cargar rutas existentes
        async function cargarRutas() {
            try {
                const res = await fetch('<?php echo BASE_URL; ?>/mapa/getRutas');
                const data = await res.json();
                rutas = data;
                data.forEach(r => {
                    if (r.puntos) {
                        try {
                            const puntos = JSON.parse(r.puntos);
                            const polyline = L.polyline(puntos, { color: 'blue', weight: 3 }).addTo(map);
                            polyline.bindPopup(`<b>${r.nombre_ruta}</b>`);
                            rutaPolylines.push(polyline);
                        } catch (e) {
                            console.error('Error parseando puntos de ruta:', e);
                        }
                    }
                });
            } catch (error) {
                console.error('Error cargando rutas:', error);
            }
        }

        cargarParadas();
        cargarRutas();

                // Modal HTML for adding parada with optional route assignment
                const modalHtml = `
                <div class="modal fade" id="modalAddParada" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Añadir Parada</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nombre de la parada</label>
                                    <input id="modalParadaNombre" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Asignar a ruta (opcional)</label>
                                    <select id="modalParadaRuta" class="form-control">
                                        <option value="">-- Sin asignar --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button id="modalSaveParada" type="button" class="btn btn-primary">Guardar parada</button>
                            </div>
                        </div>
                    </div>
                </div>`;

                document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Botones
        document.getElementById('btnParada').addEventListener('click', () => {
            modoParada = !modoParada;
            modoRuta = false;
            puntosRuta = [];
            const btn = document.getElementById('btnParada');
            if (modoParada) {
                btn.classList.add('btn-danger');
                btn.innerHTML = '<i class="fas fa-times"></i> Cancelar Parada';
                alert('Haz clic en el mapa para añadir una parada');
            } else {
                btn.classList.remove('btn-danger');
                btn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Añadir Parada';
            }
        });

        document.getElementById('btnRuta').addEventListener('click', () => {
            modoRuta = !modoRuta;
            modoParada = false;
            if (!modoRuta) {
                puntosRuta = [];
            }
            const btn = document.getElementById('btnRuta');
            if (modoRuta) {
                btn.classList.add('btn-danger');
                btn.innerHTML = '<i class="fas fa-times"></i> Cancelar Ruta';
                alert('Haz clic en el mapa para trazar la ruta. Doble clic para guardar.');
            } else {
                btn.classList.remove('btn-danger');
                btn.innerHTML = '<i class="fas fa-route"></i> Crear Ruta';
            }
        });

        // Eventos del mapa
        map.on('click', async (e) => {
            if (modoParada) {
                const { lat, lng } = e.latlng;
                // Open modal to collect name and optional route assignment
                $('#modalParadaNombre').val('Parada sin nombre');
                // populate routes select
                const select = document.getElementById('modalParadaRuta');
                select.innerHTML = '<option value="">-- Sin asignar --</option>';
                rutas.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id_ruta;
                    opt.text = r.nombre_ruta;
                    select.appendChild(opt);
                });
                // show modal
                $('#modalAddParada').modal('show');

                // when save clicked
                document.getElementById('modalSaveParada').onclick = async function() {
                    const nombre = document.getElementById('modalParadaNombre').value || 'Parada sin nombre';
                    const id_ruta = document.getElementById('modalParadaRuta').value || '';
                    try {
                        const formData = new FormData();
                        formData.append('latitud', lat);
                        formData.append('longitud', lng);
                        formData.append('nombre_parada', nombre);
                        if (id_ruta) formData.append('id_ruta', id_ruta);

                        const res = await fetch('<?php echo BASE_URL; ?>/mapa/guardarParada', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await res.json();
                        if (result.status === 'ok') {
                            L.marker([lat, lng]).addTo(map)
                                .bindPopup(`<b>${nombre}</b>`);
                            paradas.push({ latitud: lat, longitud: lng, nombre_parada: nombre, id_ruta: id_ruta });
                            alert('Parada guardada correctamente');
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (error) {
                        alert('Error al guardar parada');
                    }
                    $('#modalAddParada').modal('hide');
                };
                modoParada = false;
                document.getElementById('btnParada').classList.remove('btn-danger');
                document.getElementById('btnParada').innerHTML = '<i class="fas fa-map-marker-alt"></i> Añadir Parada';
            }

            if (modoRuta) {
                puntosRuta.push([e.latlng.lat, e.latlng.lng]);
                if (puntosRuta.length > 1) {
                    // update single temporary polyline instead of creating multiple
                    if (tempRouteLayer) {
                        tempRouteLayer.setLatLngs(puntosRuta);
                    } else {
                        tempRouteLayer = L.polyline(puntosRuta, { color: 'red', weight: 3, dashArray: '5,10' }).addTo(map);
                    }
                }
            }
        });

        map.on('dblclick', async () => {
            if (modoRuta && puntosRuta.length > 1) {
                const nombre = prompt('Nombre de la ruta:');
                if (nombre) {
                    try {
                            // Before saving, remove any temporary drawing so only the final saved polyline remains
                            if (tempRouteLayer) {
                                map.removeLayer(tempRouteLayer);
                                tempRouteLayer = null;
                            }

                            const formData = new FormData();
                            formData.append('nombre', nombre);
                            formData.append('puntos', JSON.stringify(puntosRuta));

                            const res = await fetch('<?php echo BASE_URL; ?>/mapa/guardarRuta', {
                                method: 'POST',
                                body: formData
                            });
                            const result = await res.json();

                            if (result.status === 'ok') {
                                // add the final (saved) polyline to the map and to rutaPolylines
                                const polyline = L.polyline(puntosRuta, { color: 'blue', weight: 3 }).addTo(map);
                                polyline.bindPopup(`<b>${nombre}</b>`);
                                rutaPolylines.push(polyline);
                                alert('Ruta guardada correctamente');
                            } else {
                                alert('Error: ' + result.message);
                            }
                    } catch (error) {
                        alert('Error al guardar ruta');
                    }
                }
                puntosRuta = [];
                modoRuta = false;
                document.getElementById('btnRuta').classList.remove('btn-danger');
                document.getElementById('btnRuta').innerHTML = '<i class="fas fa-route"></i> Crear Ruta';
            }
        });

        // Generate route using OSRM (follow roads) from puntosRuta waypoints
        document.getElementById('btnRutaFollow').addEventListener('click', async () => {
            if (!modoRuta || puntosRuta.length < 2) {
                alert('Active "Crear Ruta" y marque al menos 2 puntos antes de generar la ruta por calles.');
                return;
            }

            try {
                // OSRM expects lon,lat;lon,lat ...
                const coords = puntosRuta.map(p => `${p[1]},${p[0]}`).join(';');
                const url = `https://router.project-osrm.org/route/v1/driving/${coords}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();
                if (data && data.routes && data.routes.length > 0) {
                    const coordsGeo = data.routes[0].geometry.coordinates; // [lon,lat]
                    // convert to [lat,lon]
                    const newPoints = coordsGeo.map(c => [c[1], c[0]]);
                    puntosRuta = newPoints;
                    // remove existing temp polylines (simple approach: clear and redraw)
                    rutaPolylines.forEach(pl => map.removeLayer(pl));
                    rutaPolylines = [];
                    const polyline = L.polyline(puntosRuta, { color: 'red', weight: 3, dashArray: '5,10' }).addTo(map);
                    rutaPolylines.push(polyline);
                    map.fitBounds(polyline.getBounds().pad(0.1));
                    alert('Ruta generada siguiendo calles. Revisa y dbl-click para guardar.');
                } else {
                    alert('No se pudo generar la ruta desde el servicio de enrutamiento.');
                }
            } catch (e) {
                console.error(e);
                alert('Error al generar la ruta por calles.');
            }
        });

        // Ubicación del usuario
        document.getElementById('btnUbicacion').addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('La geolocalización no es compatible en este navegador.');
                return;
            }
            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([lat, lng], { title: "Tú" }).addTo(map)
                    .bindPopup("Estás aquí").openPopup();
                map.setView([lat, lng], 15);
            });
        });

        // Parada más cercana
        document.getElementById('btnOptima').addEventListener('click', () => {
            if (!userMarker) {
                alert('Primero activa tu ubicación.');
                return;
            }
            if (paradas.length === 0) {
                alert('No hay paradas registradas.');
                return;
            }

            const userLatLng = userMarker.getLatLng();
            let mejorParada = null;
            let mejorDist = Infinity;

            paradas.forEach(p => {
                const d = distancia(userLatLng.lat, userLatLng.lng, p.latitud, p.longitud);
                if (d < mejorDist) {
                    mejorDist = d;
                    mejorParada = p;
                }
            });

            if (mejorParada) {
                const linea = L.polyline([
                    [userLatLng.lat, userLatLng.lng],
                    [mejorParada.latitud, mejorParada.longitud]
                ], { color: 'green', dashArray: '5,10' }).addTo(map);

                L.popup()
                    .setLatLng([mejorParada.latitud, mejorParada.longitud])
                    .setContent(`⭐ <b>Parada más cercana</b><br>${mejorParada.nombre_parada}<br>Distancia: ${mejorDist.toFixed(2)} km`)
                    .openOn(map);
            }
        });

        function distancia(lat1, lon1, lat2, lon2) {
            const R = 6371; // km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }
    }
});
</script>
