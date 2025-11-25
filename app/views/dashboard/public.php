<!-- Header Público -->
<header class="public-header">
    <div class="container">
        <h1 class="display-4 font-weight-bold">MoviMap</h1>
        <p class="lead">Gestiona y optimiza tus rutas de transporte de manera eficiente</p>
        <?php if(!$isLoggedIn): ?>
            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>/registro" class="btn btn-light btn-lg mr-2">
                    <i class="fas fa-user-plus mr-2"></i>Comenzar Ahora
                </a>
                <a href="<?php echo BASE_URL; ?>/login" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                </a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt mr-2"></i>Ir al Panel
                </a>
            </div>
        <?php endif; ?>
    </div>
</header>

<!-- Main Content -->
<div class="content">
    <div class="container">
        
        <!-- Estadísticas Principales -->
        <section class="py-5">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="card feature-card border-primary">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h3 class="count"><?php echo $totalUsuarios; ?></h3>
                            <p class="card-text">Usuarios Registrados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card feature-card border-success">
                        <div class="card-body">
                            <i class="fas fa-route fa-3x text-success mb-3"></i>
                            <h3 class="count"><?php echo $totalRutas; ?></h3>
                            <p class="card-text">Rutas Activas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card feature-card border-warning">
                        <div class="card-body">
                            <i class="fas fa-map-marker-alt fa-3x text-warning mb-3"></i>
                            <h3 class="count"><?php echo $totalParadas; ?></h3>
                            <p class="card-text">Paradas Registradas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card feature-card border-danger">
                        <div class="card-body">
                            <i class="fas fa-bus fa-3x text-danger mb-3"></i>
                            <h3 class="count"><?php echo $rutasActivas; ?></h3>
                            <p class="card-text">Viajes Diarios</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Características -->
        <section class="py-5 bg-light" id="features">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-route fa-4x text-primary mb-3"></i>
                                <h4>Gestión de Rutas</h4>
                                <p>Administra y optimiza todas tus rutas de transporte desde un solo lugar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users-cog fa-4x text-success mb-3"></i>
                                <h4>Control de Usuarios</h4>
                                <p>Gestiona conductores, administradores y usuarios del sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

<script>
// Animación de contadores
$(document).ready(function() {
    $('.count').each(function() {
        var $this = $(this);
        var countTo = $this.text();
        
        $({ countNum: 0 }).animate({
            countNum: countTo
        }, {
            duration: 2000,
            easing: 'swing',
            step: function() {
                $this.text(Math.floor(this.countNum));
            },
            complete: function() {
                $this.text(this.countNum);
            }
        });
    });
});
</script>