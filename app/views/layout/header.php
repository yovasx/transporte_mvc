<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MoviMap - <?php echo $title ?? 'Dashboard'; ?></title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <style>
    /* GENERAL */
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      background: #f4f6f9;
    }

    :root {
      --sidebar-width: 250px;
    }

    .wrapper {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* HEADER */
    .main-header {
      height: 57px;
      flex-shrink: 0;
      position: relative;
      z-index: 1000;
    }

    /* SIDEBAR */
    .main-sidebar {
      position: fixed;
      top: 57px;
      left: 0;
      width: var(--sidebar-width);
      height: calc(100vh - 57px);
      min-height: 100vh;
      z-index: 999;
      transition: transform 0.3s ease-in-out;
      overflow-y: auto;
    }

    /* CONTENIDO PRINCIPAL */
    .content-wrapper,
    .main-footer,
    .navbar {
      margin-left: var(--sidebar-width);
      transition: margin-left 0.3s ease-in-out;
    }

    /* SIDEBAR COLAPSADO */
    .sidebar-collapse .main-sidebar {
      transform: translateX(-250px);
    }

    .sidebar-collapse .content-wrapper,
    .sidebar-collapse .main-footer,
    .sidebar-collapse .navbar {
      margin-left: 60px;
    }

    /* CONTENIDO PRINCIPAL */
    .content-wrapper {
      margin-top: 57px;
      padding: 10px 15px;
      background: #f4f6f9;
      overflow-y: auto;
      flex: 1;
    }

    /* CABECERA */
    .content-header {
      padding: 10px 0;
      margin-bottom: 10px;
      border-bottom: 1px solid #dee2e6;
    }

    .content-header h1 {
      font-size: 1.4rem;
      margin: 0;
      font-weight: 600;
    }

    /* TARJETAS */
    .small-box {
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      transition: transform .2s;
    }

    .small-box:hover {
      transform: scale(1.02);
    }

    .small-box .inner {
      padding: 15px;
    }

    .small-box h3 {
      font-size: 1.8rem;
      margin: 0;
      font-weight: bold;
    }

    .small-box p {
      margin: 5px 0 0;
      font-size: 0.9rem;
    }

    /* CARDS */
    .card {
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .card-header {
      background: #fff;
      border-bottom: 1px solid #dee2e6;
    }

    .card-title {
      font-size: 1rem;
      font-weight: 600;
    }

    /* GRÁFICO Y MAPA */
    .chart-container {
      position: relative;
      height: 300px;
    }

    .map-container {
      height: 260px;
    }

    /* PANEL DERECHO */
    .users-container,
    .status-container {
      max-height: 320px;
      overflow-y: auto;
    }

    /* BOTÓN TOGGLE */
    .navbar .navbar-nav .nav-link[data-widget="pushmenu"] {
      cursor: pointer;
      padding: 0.75rem 1rem;
      font-size: 1.1rem;
      transition: color 0.3s ease;
    }

    .navbar .navbar-nav .nav-link[data-widget="pushmenu"]:hover {
      color: #007bff;
    }

    /* RESPONSIVO */
    @media (max-width: 991px) {
      .content-wrapper,
      .main-footer,
      .navbar {
        margin-left: 0;
      }

      .sidebar-collapse .main-sidebar {
        transform: translateX(0);
        box-shadow: 2px 0 10px rgba(0,0,0,0.2);
      }
    }

    @media (max-width: 576px) {
      .main-sidebar {
        width: 100%;
      }

      .sidebar-collapse .main-sidebar {
        position: fixed;
        top: 57px;
        width: 100%;
        transform: translateX(0);
        z-index: 1050;
      }

      .sidebar-collapse .content-wrapper {
        z-index: 1040;
      }
    }

    @media (min-width: 992px) {
      .sidebar-open .main-sidebar {
        margin-left: 0;
      }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper"> 
