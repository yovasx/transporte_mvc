<!-- Mapa de Rutas (Leaflet) -->
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt mr-2"></i>Mapa de Rutas
                    </h3>
                </div>
                <div class="card-body p-2">
                    <div id="map-full" style="height: 500px; width: 100%; border-radius: 5px; border: 1px solid #e3f2fd;"></div>
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
        var map = L.map('map-full').setView([-16.50130554087592, -68.13281764642588], 12); // LA PAZA BOLIVIA
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    }
});
</script>
