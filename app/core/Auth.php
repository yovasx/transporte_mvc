<?php
class Auth {
    /**
     * Verificar si el usuario está logueado
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Verificar si el usuario es admin
     */
    public static function isAdmin() {
        return self::isLoggedIn() && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
    }

    /**
     * Verificar si el usuario es usuario normal
     */
    public static function isUsuario() {
        return self::isLoggedIn() && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'usuario';
    }

    /**
     * Obtener el rol del usuario actual
     */
    public static function getRol() {
        return $_SESSION['usuario_rol'] ?? null;
    }

    /**
     * Obtener el ID del usuario actual
     */
    public static function getUserId() {
        return $_SESSION['usuario_id'] ?? null;
    }

    /**
     * Verificar permisos para una acción específica
     */
    public static function hasPermission($permission) {
        if (!self::isLoggedIn()) {
            return false;
        }

        $permissions = [
            'admin' => ['manage_users', 'manage_routes', 'manage_stops', 'view_reports', 'manage_system'],
            'usuario' => ['view_routes', 'view_stops', 'search_routes', 'view_map']
        ];

        $userRole = self::getRol();
        return isset($permissions[$userRole]) && in_array($permission, $permissions[$userRole]);
    }

    /**
     * Redirigir si no tiene permisos
     */
    public static function requirePermission($permission) {
        if (!self::hasPermission($permission)) {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    /**
     * Redirigir si no está logueado
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    /**
     * Redirigir si no es admin
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            $_SESSION['error'] = 'Acceso denegado. Se requieren permisos de administrador';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }
}
?>
