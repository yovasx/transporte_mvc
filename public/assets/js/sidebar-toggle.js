/**
 * Script para manejar el toggle de la barra lateral.
 * Integrado con AdminLTE: escucha eventos "collapsed.lte.pushmenu" y "shown.lte.pushmenu"
 * Guarda el estado en localStorage y usa la clase en <body> para compatibilidad.
 */
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;

    // Cargar estado guardado del localStorage (valor 'true'|'false')
    const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isSidebarCollapsed) {
        body.classList.add('sidebar-collapse');
    } else {
        body.classList.remove('sidebar-collapse');
    }

    // Si jQuery + AdminLTE está disponible, usar eventos jQuery (se disparan en el DOM por AdminLTE)
    if (window.jQuery) {
        // sincronizar cuando AdminLTE dispare los eventos
        window.jQuery(document).on('collapsed.lte.pushmenu shown.lte.pushmenu', function(e) {
            const collapsed = e.type === 'collapsed.lte.pushmenu';
            localStorage.setItem('sidebarCollapsed', collapsed ? 'true' : 'false');
        });

        // Delegación: cuando se hace click en el botón pushmenu, dejar que AdminLTE haga el toggle y luego guardar el estado
        window.jQuery(document).on('click', '[data-widget="pushmenu"]', function() {
            // esperar un tick para que AdminLTE actualice clases
            setTimeout(function() {
                const collapsed = body.classList.contains('sidebar-collapse');
                // Si por alguna razón AdminLTE no hizo el toggle, forzarlo (fallback)
                if (typeof collapsed === 'undefined') {
                    body.classList.toggle('sidebar-collapse');
                }
                localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapse') ? 'true' : 'false');
            }, 50);
        });
    } else {
        // Fallback sin jQuery: delegación básica para el botón pushmenu
        document.addEventListener('click', function(e) {
            const t = e.target.closest ? e.target.closest('[data-widget="pushmenu"]') : null;
            if (!t) return;
            e.preventDefault();
            // Toggle manual
            const prev = body.classList.contains('sidebar-collapse');
            const collapsed = body.classList.toggle('sidebar-collapse');
            localStorage.setItem('sidebarCollapsed', collapsed ? 'true' : 'false');
            // Recovery: si por alguna razón el estado no cambió después de un breve delay, forzar persistencia
            setTimeout(function() {
                if (body.classList.contains('sidebar-collapse') === prev) {
                    // forzar toggle y persistir
                    body.classList.toggle('sidebar-collapse');
                    localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapse') ? 'true' : 'false');
                }
            }, 200);
        });
    }

    // Cerrar sidebar al hacer clic en un enlace de navegación (en móvil)
    // Cerrar sidebar al hacer clic en un enlace de navegación (en móvil)
    document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                if (window.jQuery && window.jQuery.fn && window.jQuery.fn.PushMenu) {
                    try {
                        // usar API para colapsar
                        window.jQuery('[data-widget="pushmenu"]').PushMenu('collapse');
                        return;
                    } catch (err) {
                        // ignore and fallback
                    }
                }
                body.classList.remove('sidebar-collapse');
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });
    });

    // Manejar redimensionamiento de ventana: en pantallas grandes forzar sidebar visible
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            body.classList.remove('sidebar-collapse');
            localStorage.setItem('sidebarCollapsed', 'false');
        }
    });
});
