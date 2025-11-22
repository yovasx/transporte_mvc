<!-- Mapa de Rutas para Usuarios -->
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt mr-2"></i>Mapa de Rutas
                    </h3>
                </div>
                <div class="card-body p-2">
                    <div class="buttons text-center mb-3">
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

        let paradas = [];
        let rutas = [];
        let userMarker = null;
        let rutaPolylines = [];

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

        // If server injected highlight_route, zoom to and open popup for that route
        const HIGHLIGHT_ROUTE_ID = '<?php echo isset($highlight_route) ? addslashes($highlight_route) : ''; ?>';
        function highlightRouteById(id) {
            if (!id) return;
            // find ruta data loaded via cargarRutas (rutas array)
            const r = rutas.find(rr => String(rr.id_ruta) === String(id));
            if (!r) return;
            if (r.puntos) {
                try {
                    const puntos = JSON.parse(r.puntos);
                    const poly = L.polyline(puntos, { color: 'red', weight: 4 }).addTo(map);
                    map.fitBounds(poly.getBounds().pad(0.1));
                    poly.bindPopup(`<b>${r.nombre_ruta}</b>`).openPopup();
                } catch (e) {
                    console.error('Error parseando puntos para resaltar ruta:', e);
                }
            }
        }

        // call after small delay to allow cargarRutas to populate
        setTimeout(() => {
            if (HIGHLIGHT_ROUTE_ID) highlightRouteById(HIGHLIGHT_ROUTE_ID);
        }, 300);

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
