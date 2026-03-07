<?php
require_once __DIR__ . '/../Modelos/GruposUsuariosModel.php';

header('Content-Type: application/json');

/*
 * Manejo de Peticiones AJAX para Grupos de Usuarios
 */

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

$model = new GruposUsuariosModel();

try {
    switch ($action) {
        case 'get_grupos':
            $grupos = $model->obtenerGrupos();
            echo json_encode(['status' => 'success', 'data' => $grupos]);
            break;

        case 'save_grupo':
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

            if (empty($nombre)) {
                echo json_encode(['status' => 'error', 'message' => 'El nombre del grupo es obligatorio']);
                exit;
            }

            if ($id > 0) {
                // Actualizar
                $res = $model->actualizarGrupo($id, $nombre, $descripcion);
                echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'Grupo actualizado correctamente' : 'Error al actualizar']);
            } else {
                // Crear
                $resId = $model->crearGrupo($nombre, $descripcion);
                echo json_encode(['status' => $resId ? 'success' : 'error', 'message' => $resId ? 'Grupo creado correctamente' : 'Error al crear', 'id' => $resId]);
            }
            break;

        case 'delete_grupo':
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if ($id > 0) {
                $res = $model->eliminarGrupo($id);
                echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'Grupo eliminado correctamente' : 'Error al eliminar']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
            }
            break;

        case 'get_permisos':
            $grupo_id = isset($_GET['grupo_id']) ? intval($_GET['grupo_id']) : 0;
            if ($grupo_id > 0) {
                $permisos = $model->obtenerPermisosPorGrupo($grupo_id);
                echo json_encode(['status' => 'success', 'data' => $permisos]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de grupo inválido']);
            }
            break;

        case 'save_permisos':
            $grupo_id = isset($_POST['grupo_id']) ? intval($_POST['grupo_id']) : 0;
            $permisos = isset($_POST['permisos']) ? json_decode($_POST['permisos'], true) : [];

            if ($grupo_id > 0) {
                $res = $model->guardarPermisos($grupo_id, $permisos);
                echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'Permisos guardados correctamente' : 'Error al guardar permisos']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de grupo inválido']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>