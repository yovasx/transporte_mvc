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

        const HIGHLIGHT_ROUTE_ID = '<?php echo isset($highlight_route) ? addslashes($highlight_route) : ''; ?>';

        let paradas = [];
        let rutas = [];
        let userMarker = null;
        let rutaPolylines = [];
        let paradaMarkers = [];
        let navigationPolyline = null;
        let navInfoControl = null;
        let navAnimMarker = null;
        let navAnimInterval = null;

        // Custom icons
        const paradaIcon = L.icon({
            // use the requested filename in public/assets (typo-safe alias `paarda-de-autobus.svg`)
            iconUrl: '<?php echo BASE_URL; ?>/public/assets/images/paarda-de-autobus.svg',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });
        const userIcon = L.icon({
            // use the requested `mapas-de-google.svg` filename in public/assets
            iconUrl: '<?php echo BASE_URL; ?>/public/assets/images/mapas-de-google.svg',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        // Cargar paradas existentes
        async function cargarParadas(routeId = null) {
            try {
                let url = '<?php echo BASE_URL; ?>/mapa/getParadas';
                if (!routeId) routeId = HIGHLIGHT_ROUTE_ID;
                if (routeId) url += '?route=' + encodeURIComponent(routeId);
                const res = await fetch(url);
                const data = await res.json();
                // clear existing markers
                paradaMarkers.forEach(m => map.removeLayer(m));
                paradaMarkers = [];
                paradas = data;
                data.forEach(p => {
                    const m = L.marker([p.latitud, p.longitud], { icon: paradaIcon }).addTo(map)
                        .bindPopup(`<b>${p.nombre_parada}</b><br>ID: ${p.id_parada}`);
                    m.on('click', () => {
                        // show only the route that contains this parada
                        if (p.id_ruta) {
                            cargarRutas(p.id_ruta).then(() => {
                                cargarParadas(p.id_ruta);
                                highlightRouteById(p.id_ruta);
                            });
                        }
                    });
                    paradaMarkers.push(m);
                });
            } catch (error) {
                console.error('Error cargando paradas:', error);
            }
        }

        // Cargar rutas existentes
        async function cargarRutas(routeId = null) {
            try {
                const res = await fetch('<?php echo BASE_URL; ?>/mapa/getRutas');
                const data = await res.json();
                rutas = data;
                // clear existing polylines
                rutaPolylines.forEach(p => map.removeLayer(p));
                rutaPolylines = [];

                data.forEach(r => {
                    if (routeId === null) {
                        if (HIGHLIGHT_ROUTE_ID && String(r.id_ruta) !== String(HIGHLIGHT_ROUTE_ID)) return;
                    } else {
                        if (String(r.id_ruta) !== String(routeId)) return;
                    }
                    if (r.puntos) {
                        try {
                            const puntos = JSON.parse(r.puntos);
                            const polyline = L.polyline(puntos, { color: (routeId || HIGHLIGHT_ROUTE_ID) && String(r.id_ruta) === String(routeId || HIGHLIGHT_ROUTE_ID) ? 'red' : 'blue', weight: 3 }).addTo(map);
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
        function highlightRouteById(id) {
            if (!id) return;
            // find ruta data loaded via cargarRutas (rutas array)
            const r = rutas.find(rr => String(rr.id_ruta) === String(id));
            if (!r) return;
            if (r.puntos) {
                try {
                    const puntos = JSON.parse(r.puntos);
                    // remove previous highlights
                    rutaPolylines.forEach(p => map.removeLayer(p));
                    rutaPolylines = [];
                    const poly = L.polyline(puntos, { color: 'red', weight: 4 }).addTo(map);
                    poly.bindPopup(`<b>${r.nombre_ruta}</b>`).openPopup();
                    rutaPolylines.push(poly);
                    map.fitBounds(poly.getBounds().pad(0.1));
                } catch (e) {
                    console.error('Error parseando puntos para resaltar ruta:', e);
                }
            }
        }

        // call after small delay to allow cargarRutas to populate
        setTimeout(() => {
            if (HIGHLIGHT_ROUTE_ID) {
                // reload content restricted to that route
                cargarRutas(HIGHLIGHT_ROUTE_ID).then(() => {
                    cargarParadas(HIGHLIGHT_ROUTE_ID);
                    highlightRouteById(HIGHLIGHT_ROUTE_ID);
                });
            } else {
                cargarParadas();
                cargarRutas();
            }
        }, 200);

        // Show route and paradas for a given parada, and optionally draw navigation from user to parada
        async function showRouteForParada(p) {
            if (!p) return;
            if (p.id_ruta) {
                await cargarRutas(p.id_ruta);
                await cargarParadas(p.id_ruta);
                highlightRouteById(p.id_ruta);
            }
        }

        // Request OSRM route from start to end and draw it (replaces any previous navigation polyline)
        async function drawNavigationRoute(startLat, startLng, endLat, endLng) {
            try {
                if (navigationPolyline) {
                    map.removeLayer(navigationPolyline);
                    navigationPolyline = null;
                }
                const coords = `${startLng},${startLat};${endLng},${endLat}`;
                const url = `https://router.project-osrm.org/route/v1/driving/${coords}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();
                if (data && data.routes && data.routes.length > 0) {
                    const coordsGeo = data.routes[0].geometry.coordinates; // [lon,lat]
                    const newPoints = coordsGeo.map(c => [c[1], c[0]]);
                        navigationPolyline = L.polyline(newPoints, { color: 'green', weight: 4 }).addTo(map);

                    // show distance/duration info (but don't change map zoom)
                    const routeInfo = data.routes[0];
                    const meters = routeInfo.distance || 0;
                    const seconds = routeInfo.duration || 0;
                    const km = (meters/1000).toFixed(2);
                    const mins = Math.round(seconds/60);
                    if (!navInfoControl) {
                        navInfoControl = L.control({ position: 'topright' });
                        navInfoControl.onAdd = function() {
                            const div = L.DomUtil.create('div', 'nav-info control p-2 bg-white');
                            div.style.minWidth = '140px';
                            div.style.boxShadow = '0 1px 4px rgba(0,0,0,0.2)';
                            div.id = 'navInfoBox';
                            return div;
                        };
                        navInfoControl.addTo(map);
                    }
                    const infoBox = document.getElementById('navInfoBox');
                    if (infoBox) infoBox.innerHTML = `<b>Ruta</b><br>Dist: ${km} km<br>Tiempo: ~${mins} min`;

                    // start smooth animation of marker along polyline using OSRM duration
                    startNavigationAnimationSmooth(newPoints, (routeInfo.duration || 30) * 1000);
                } else {
                    console.warn('OSRM no devolvió ruta');
                }
            } catch (e) {
                console.error('Error al solicitar ruta de navegación:', e);
            }
        }

        // Smoothly animate a marker along the given array of [lat,lng] points over totalMs milliseconds
        function startNavigationAnimationSmooth(points, totalMs) {
            try {
                // stop existing animation
                if (navAnimInterval) {
                    cancelAnimationFrame(navAnimInterval);
                    navAnimInterval = null;
                }
                if (navAnimMarker) {
                    map.removeLayer(navAnimMarker);
                    navAnimMarker = null;
                }
                if (!points || points.length === 0) return;

                // create a flat array of distances between points to interpolate
                const segLengths = [];
                let totalLen = 0;
                for (let i = 0; i < points.length - 1; i++) {
                    const a = points[i];
                    const b = points[i+1];
                    const dx = (b[0] - a[0]);
                    const dy = (b[1] - a[1]);
                    const d = Math.sqrt(dx*dx + dy*dy);
                    segLengths.push(d);
                    totalLen += d;
                }
                if (totalLen === 0) return;

                // place marker at start (use default marker so the moving marker stays unchanged)
                navAnimMarker = L.marker(points[0]).addTo(map);
                navAnimMarker.bindPopup('Navegando');

                const startTime = performance.now();

                function step(now) {
                    const elapsed = now - startTime;
                    const t = Math.min(1, elapsed / totalMs);
                    // distance along polyline
                    const distAlong = t * totalLen;
                    // find which segment
                    let acc = 0;
                    let segIdx = 0;
                    while (segIdx < segLengths.length && acc + segLengths[segIdx] < distAlong) {
                        acc += segLengths[segIdx];
                        segIdx++;
                    }
                    if (segIdx >= segLengths.length) {
                        navAnimMarker.setLatLng(points[points.length - 1]);
                        navAnimMarker.bindPopup('Llegada').openPopup();
                        navAnimInterval = null;
                        return;
                    }
                    const segT = (distAlong - acc) / segLengths[segIdx];
                    const a = points[segIdx];
                    const b = points[segIdx + 1];
                    const lat = a[0] + (b[0] - a[0]) * segT;
                    const lng = a[1] + (b[1] - a[1]) * segT;
                    navAnimMarker.setLatLng([lat, lng]);
                    navAnimInterval = requestAnimationFrame(step);
                }

                navAnimInterval = requestAnimationFrame(step);
            } catch (err) {
                console.error('Error en animación de navegación:', err);
            }
        }

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
                userMarker = L.marker([lat, lng], { title: "Tú", icon: userIcon }).addTo(map)
                    .bindPopup("Estás aquí").openPopup();
                map.setView([lat, lng], 15);
            });
        });

        // Parada más cercana
        document.getElementById('btnOptima').addEventListener('click', async () => {
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
                // show only the route that has this parada
                if (mejorParada.id_ruta) {
                    await cargarRutas(mejorParada.id_ruta);
                    await cargarParadas(mejorParada.id_ruta);
                    highlightRouteById(mejorParada.id_ruta);
                }

                // draw navigation route following calles (OSRM) from user to parada
                await drawNavigationRoute(userLatLng.lat, userLatLng.lng, mejorParada.latitud, mejorParada.longitud);

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
