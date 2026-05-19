<?php
class Permisos
{
    public static function cargarPermisos($grupo_id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($grupo_id)) {
            $_SESSION['permisos'] = [];
            return;
        }

        require_once __DIR__ . '/../Config/conexion.php';
        $conn = getDBConnection();
        $sql = "SELECT modulo, can_view, can_edit, can_delete FROM permisos_grupos WHERE grupo_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $grupo_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $permisos = [];
        while ($row = $result->fetch_assoc()) {
            $permisos[$row['modulo']] = $row;
        }

        $_SESSION['permisos'] = $permisos;
    }

    public static function verificar($modulo)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Si no está logueado, redirige a login
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /ProyectoIglesia/login");
            exit;
        }

        $rol = $_SESSION['rol'] ?? '';
        $grupo_id = $_SESSION['grupo_id'] ?? null;

        // Admin o usuario sin grupo asignado (legacy) tienen acceso total
        if ($rol === 'administrador' || empty($grupo_id)) {
            return true;
        }

        $permisos = $_SESSION['permisos'] ?? [];

        // Si no tiene permiso de ver el módulo específico
        if (!isset($permisos[$modulo]) || empty($permisos[$modulo]['can_view'])) {
            header("Location: /ProyectoIglesia/acceso-denegado");
            exit;
        }

        return true;
    }

    public static function puede($accion, $modulo)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $rol = $_SESSION['rol'] ?? '';
        $grupo_id = $_SESSION['grupo_id'] ?? null;

        // Admin o usuario sin grupo asignado (legacy) tienen acceso total
        if ($rol === 'administrador' || empty($grupo_id)) {
            return true;
        }

        $permisos = $_SESSION['permisos'] ?? [];

        if (!isset($permisos[$modulo])) {
            return false;
        }

        switch ($accion) {
            case 'view':
                return !empty($permisos[$modulo]['can_view']);
            case 'edit':
                return !empty($permisos[$modulo]['can_edit']);
            case 'delete':
                return !empty($permisos[$modulo]['can_delete']);
            default:
                return false;
        }
    }
}
?>