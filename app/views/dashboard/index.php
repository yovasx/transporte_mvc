<!-- Content Header - SUPER COMPACTO -->
<div class="content-header">
    <div class="container-fluid p-0">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right mb-0">
                    <li class="breadcrumb-item"><i class="fas fa-home"></i></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Content - SIN SCROLL -->
<section class="content">
    <div class="container-fluid p-0">
        <div class="dashboard-container">
            
            <!-- Stats Row -->
            <div class="row stats-row">
                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-info m-0">
                        <div class="inner">
                            <h3><?php echo $totalUsuarios ?? '0'; ?></h3>
                            <p>Usuarios</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/usuarios" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-success m-0">
                        <div class="inner">
                            <h3><?php echo $totalRutas ?? '0'; ?></h3>
                            <p>Rutas Activas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/rutas" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-warning m-0">
                        <div class="inner">
                            <h3><?php echo $totalParadas ?? '0'; ?></h3>
                            <p>Paradas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/paradas" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="small-box bg-danger m-0">
                        <div class="inner">
                            <h3><?php echo $rutasActivas ?? '0'; ?></h3>
                            <p>Viajes Hoy</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-bus"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Más info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row main-content-row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <div class="row h-100">
                        <div class="col-12 mb-3">
                            <div class="card h-100">
                                <div class="card-header py-2">
                                    <h3 class="card-title mb-0">
                                        <i class="fas fa-chart-line mr-2"></i>Estadísticas
                                    </h3>
                                </div>
                                <div class="card-body p-2 chart-container">
                                    <canvas id="visitors-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="card h-100">
                                <div class="card-header py-2">
                                    <h3 class="card-title mb-0">
                                        <i class="fas fa-map-marked-alt mr-2"></i>Mapa de Rutas
                                    </h3>
                                </div>
                                <div class="card-body p-2 map-container">
                                    <div id="map" style="height: 350px; width: 100%; border-radius: 5px; border: 1px solid #e3f2fd;"></div>
                                    <div class="mt-2 text-center">
                                        <a href="<?php echo BASE_URL; ?>/dashboard/mapa" class="btn btn-primary btn-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i>Ver Mapa Completo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <div class="row h-100">
                        <div class="col-12 mb-3">
                            <div class="card h-100">
                                <div class="card-header py-2">
                                    <h3 class="card-title mb-0">
                                        <i class="fas fa-user-clock mr-2"></i>Usuarios Recientes
                                    </h3>
                                </div>
                                <div class="card-body p-2 users-container" style="overflow-y: auto;">
                                    <div class="list-group list-group-flush">
                                        <?php if (!empty($usuariosRecientes)): ?>
                                            <?php foreach ($usuariosRecientes as $usuario): ?>
                                                <div class="list-group-item p-2">
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($usuario['nombre'] . ' ' . $usuario['apellido_paterno']); ?>&background=0D8ABC&color=fff" class="rounded-circle mr-2" style="width: 35px; height: 35px;">
                                                        <div class="flex-grow-1">
                                                            <strong class="d-block"><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido_paterno']); ?></strong>
                                                            <small class="text-muted"><?php echo htmlspecialchars($usuario['correo']); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="list-group-item p-2 text-center text-muted">No hay usuarios recientes.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="card h-100">
                                <div class="card-header py-2">
                                    <h3 class="card-title mb-0">
                                        <i class="fas fa-tasks mr-2"></i>Estado del Sistema
                                    </h3>
                                </div>
                                <div class="card-body p-2 status-container">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Rutas Activas</small>
                                            <small class="font-weight-bold"><?php echo $rutasActivas ?? '0'; ?>/<?php echo $totalRutas ?? '0'; ?></small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo $totalRutas > 0 ? ($rutasActivas / $totalRutas * 100) : 0; ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Capacidad</small>
                                            <small class="font-weight-bold">85%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-info" style="width: 85%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Satisfacción</small>
                                            <small class="font-weight-bold">92%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: 92%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Disponibilidad</small>
                                            <small class="text-success font-weight-bold">Excelente</small>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Rendimiento</small>
                                            <small class="text-primary font-weight-bold">Óptimo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/leaflet/leaflet.css" />
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Leaflet JS -->
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/leaflet/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico simplificado
    var ctx = document.getElementById('visitors-chart').getContext('2d');
    var visitorsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Viajes',
                backgroundColor: 'rgba(60, 141, 188, 0.1)',
                borderColor: '#3c8dbc',
                data: [65, 59, 80, 81, 56, 55, 40]
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { display: false } }
            }
        }
    });

    // Inicializar Leaflet
    if (document.getElementById('map')) {
        var map = L.map('map').setView([-16.50130554087592, -68.13281764642588], 12); // LA PAZ BOLIVIA
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    }
});
</script>