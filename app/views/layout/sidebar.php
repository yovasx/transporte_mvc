<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo BASE_URL; ?>/dashboard" class="brand-link">
        <i class="fas fa-bus brand-icon"></i>
        <span class="brand-text font-weight-light"><strong>Movi</strong>Map</span>
    </a>
<body class ="hold-transition sidebar-mini layout-fixed">
    

    <!-- Sidebar  -->
     
    <div class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION['usuario_nombre'] ?? 'Usuario'; ?></a>
                <small class="text-muted"><?php echo ucfirst($_SESSION['usuario_rol'] ?? 'usuario'); ?></small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/dashboard" class="nav-link <?php echo ($page == 'dashboard' || $page == 'usuario_dashboard') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
                <!-- Menú de Administrador -->
                <li class="nav-header">ADMINISTRACIÓN</li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/usuarios" class="nav-link <?php echo ($page == 'usuarios') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Usuarios <span class="badge badge-info right">1,254</span></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/rutas" class="nav-link <?php echo ($page == 'rutas') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-route"></i>
                        <p>Rutas <span class="badge badge-success right">48</span></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/paradas" class="nav-link <?php echo ($page == 'paradas') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Paradas <span class="badge badge-warning right">156</span></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/audit" class="nav-link <?php echo ($page == 'audit_logs') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Registros</p>
                    </a>
                </li>

                <!-- Trigger history removed from user menu to simplify UI -->

                <li class="nav-header">REPORTES</li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reportes</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Menú común para todos los usuarios -->
                <li class="nav-header">SERVICIOS</li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/mapa" class="nav-link <?php echo ($page == 'mapa') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-map"></i>
                        <p>Mapa de Rutas</p>
                    </a>
                </li>

                <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'usuario'): ?>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/usuario/buscar" class="nav-link <?php echo ($page == 'buscar_rutas') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-search"></i>
                        <p>Buscar Rutas</p>
                    </a>
                </li>
                <?php endif; ?>

            </ul>
        </nav>
    </div>

</aside>

</body>
