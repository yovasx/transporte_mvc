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


    /* SIDEBAR - ESTILOS MEJORADOS */
    :root {
      --sidebar-width: 200px;
    }

    .main-sidebar {
      min-height: 100vh;
      position: fixed;
      left: 0;
      top: 57px;
      width: var(--sidebar-width);
      height: calc(100vh - 57px);
      z-index: 999;
      transition: transform 0.25s ease-in-out, width 0.25s ease-in-out;
      overflow-y: auto;
      will-change: transform;
    }

    /* Cuando la barra lateral está colapsada */
    .sidebar-collapse .main-sidebar {
      transform: translateX(calc(-1 * var(--sidebar-width)));
    }

    /* When collapsed on large screens, shrink to icons-only width instead of fully hiding */
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
      <!-- Leaflet -->
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

      <style>
        /* GENERAL */
        html, body {
          height: 100%;
          margin: 0;
          padding: 0;
          overflow-x: hidden;
          background: #f4f6f9;
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

        /* SIDEBAR - ESTILOS MEJORADOS */
        .main-sidebar {
          min-height: 100vh;
          position: fixed;
          left: 0;
          top: 57px;
          width: 250px;
          height: calc(100vh - 57px);
          z-index: 999;
          transition: transform 0.3s ease-in-out;
          overflow-y: auto;
        }

        /* Cuando la barra lateral está colapsada */
        .sidebar-collapse .main-sidebar {
          transform: translateX(-250px);
        }

        /* Overlay oscuro cuando está abierta en móvil */
        .sidebar-collapse .main-sidebar::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.5);
          z-index: -1;
        }

        /* CONTENIDO PRINCIPAL - RESPONSIVE */
        .content-wrapper {
          margin-left: 250px;
          margin-top: 57px;
          padding: 10px 15px;
          background: #f4f6f9;
          overflow-y: auto;
          flex: 1;
          transition: margin-left 0.3s ease-in-out;
        }

        .sidebar-collapse .content-wrapper {
          margin-left: 0;
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

        /* TARJETAS (small boxes) */
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

        /* SECCIONES PRINCIPALES */
        .main-content-row {
          margin-top: 15px;
        }

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

        /* GRÁFICO */
        .chart-container {
          position: relative;
          height: 300px;
        }

        /* MAPA */
        .map-container {
          height: 260px;
        }

        /* PANEL DERECHO */
        .users-container {
          max-height: 320px;
          overflow-y: auto;
        }

        .status-container {
          max-height: 280px;
          overflow-y: auto;
        }

        /* BOTÓN TOGGLE NAVBAR MEJORADO */
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
          .main-sidebar {
            width: 250px;
          }

          .content-wrapper {
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
      </style>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
