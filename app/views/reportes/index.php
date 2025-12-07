<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Reportes</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Report Cards Row -->
        <div class="row">
            <!-- Rutas Más Consultadas -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $totalRutasConsultadas; ?></h3>
                        <p>Rutas Más Consultadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <a href="#rutasSection" class="small-box-footer" data-toggle="collapse" aria-expanded="false">
                        Ver Detalles <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Destinos Más Buscados -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $totalDestinosBuscados; ?></h3>
                        <p>Destinos Más Buscados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <a href="#destinosSection" class="small-box-footer" data-toggle="collapse" aria-expanded="false">
                        Ver Detalles <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Paradas Más Cercanas -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $totalParadasCercanas; ?></h3>
                        <p>Paradas Más Cercanas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-search-location"></i>
                    </div>
                    <a href="#paradasSection" class="small-box-footer" data-toggle="collapse" aria-expanded="false">
                        Ver Detalles <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Collapsible Sections -->
        <div class="row">
            <div class="col-12">

                <!-- Rutas Section -->
                <div id="rutasSection" class="collapse">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detalle: Rutas Más Consultadas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#rutasSection">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Tabla de datos -->
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre de Ruta</th>
                                            <th>Hora Inicio</th>
                                            <th>Hora Final</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rutasMasConsultadas as $ruta): ?>
                                        <tr>
                                            <td><?php echo $ruta['id_ruta']; ?></td>
                                            <td><?php echo $ruta['nombre_ruta']; ?></td>
                                            <td><?php echo $ruta['hora_inicio']; ?></td>
                                            <td><?php echo $ruta['hora_final']; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $ruta['estado'] == 'Activo' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($ruta['estado']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Gráfico -->
                            <div class="chart-container">
                                <canvas id="rutasChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destinos Section -->
                <div id="destinosSection" class="collapse">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detalle: Destinos Más Buscados</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#destinosSection">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Tabla de datos -->
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre de Parada</th>
                                            <th>Latitud</th>
                                            <th>Longitud</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($destinosMasBuscados as $parada): ?>
                                        <tr>
                                            <td><?php echo $parada['id_parada']; ?></td>
                                            <td><?php echo $parada['nombre_parada']; ?></td>
                                            <td><?php echo $parada['latitud']; ?></td>
                                            <td><?php echo $parada['longitud']; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $parada['estado'] == 'activo' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($parada['estado']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Gráfico -->
                            <div class="chart-container">
                                <canvas id="destinosChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paradas Section -->
                <div id="paradasSection" class="collapse">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detalle: Paradas Más Cercanas Usadas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#paradasSection">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Tabla de datos -->
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre de Parada</th>
                                            <th>Latitud</th>
                                            <th>Longitud</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paradasCercanasUsadas as $parada): ?>
                                        <tr>
                                            <td><?php echo $parada['id_parada']; ?></td>
                                            <td><?php echo $parada['nombre_parada']; ?></td>
                                            <td><?php echo $parada['latitud']; ?></td>
                                            <td><?php echo $parada['longitud']; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $parada['estado'] == 'activo' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($parada['estado']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Gráfico -->
                            <div class="chart-container">
                                <canvas id="paradasChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico para Rutas Más Consultadas
    var rutasCtx = document.getElementById('rutasChart');
    if (rutasCtx) {
        var rutasChart = new Chart(rutasCtx, {
            type: 'bar',
            data: {
                labels: ['Total Rutas'],
                datasets: [{
                    label: 'Número de Rutas',
                    data: [<?php echo $totalRutasConsultadas; ?>],
                    backgroundColor: ['rgba(60, 141, 188, 0.8)'],
                    borderColor: ['rgba(60, 141, 188, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Gráfico para Destinos Más Buscados
    var destinosCtx = document.getElementById('destinosChart');
    if (destinosCtx) {
        var destinosChart = new Chart(destinosCtx, {
            type: 'pie',
            data: {
                labels: ['Total Paradas'],
                datasets: [{
                    data: [<?php echo $totalDestinosBuscados; ?>],
                    backgroundColor: ['rgba(40, 167, 69, 0.8)'],
                    borderColor: ['rgba(40, 167, 69, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    // Gráfico para Paradas Más Cercanas Usadas
    var paradasCtx = document.getElementById('paradasChart');
    if (paradasCtx) {
        var paradasChart = new Chart(paradasCtx, {
            type: 'line',
            data: {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
                datasets: [{
                    label: 'Uso de Paradas Cercanas',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
