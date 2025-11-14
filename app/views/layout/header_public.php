<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?? 'MoviMap'; ?></title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .public-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .public-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .public-footer {
            background: #343a40;
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .btn-login-nav {
            border: 2px solid white;
            color: white;
            font-weight: 600;
        }
        .btn-login-nav:hover {
            background: white;
            color: #667eea;
        }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar Pública -->
    <nav class="main-header navbar navbar-expand-md navbar-light public-navbar">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>/dashboard" class="navbar-brand">
                <i class="fas fa-bus"></i>
                <span class="brand-text font-weight-bold text-white">MoviMap</span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/dashboard" class="nav-link text-white">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a href="#rutas" class="nav-link text-white">Rutas</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tarifas" class="nav-link text-white">Tarifas</a>
                    </li>
                    <li class="nav-item">
                        <a href="#contacto" class="nav-link text-white">Contacto</a>
                    </li>
                </ul>
            </div>

            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                <?php if(isset($isLoggedIn) && $isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white" data-toggle="dropdown" href="#">
                            <i class="fas fa-user-circle mr-1"></i>
                            <?php echo $usuario_nombre; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <a href="<?php echo BASE_URL; ?>/dashboard" class="dropdown-item">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo BASE_URL; ?>/login/logout" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/login" class="btn btn-login-nav">
                            <i class="fas fa-sign-in-alt mr-1"></i>Iniciar Sesión
                        </a>
                    </li>
                    <li class="nav-item ml-2">
                        <a href="<?php echo BASE_URL; ?>/registro" class="btn btn-success">
                            <i class="fas fa-user-plus mr-1"></i>Registrarse
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <!-- /.navbar -->