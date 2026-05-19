<?php

declare(strict_types=1);

namespace Nosde\ProyectoIglesia\Controladores;

use Nosde\ProyectoIglesia\Modelos\GruposUsuariosModel;
use Exception;

class GruposUsuariosController
{
    private GruposUsuariosModel $model;

    public function __construct()
    {
        $this->model = new GruposUsuariosModel();
    }

    /**
     * Responde con JSON estandarizado.
     */
    private function jsonResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    /**
     * Obtener todos los grupos.
     */
    public function index(): void
    {
        try {
            $grupos = $this->model->obtenerGrupos();
            $this->jsonResponse(['status' => 'success', 'data' => $grupos]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Guardar o actualizar un grupo.
     */
    public function save(): void
    {
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

            if (empty($nombre)) {
                $this->jsonResponse(['status' => 'error', 'message' => 'El nombre del grupo es obligatorio'], 400);
            }

            if ($id > 0) {
                $res = $this->model->actualizarGrupo($id, $nombre, $descripcion);
                $this->jsonResponse([
                    'status' => $res ? 'success' : 'error',
                    'message' => $res ? 'Grupo actualizado correctamente' : 'Error al actualizar'
                ]);
            } else {
                $resId = $this->model->crearGrupo($nombre, $descripcion);
                $this->jsonResponse([
                    'status' => $resId ? 'success' : 'error',
                    'message' => $resId ? 'Grupo creado correctamente' : 'Error al crear',
                    'id' => $resId
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un grupo.
     */
    public function delete(): void
    {
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) {
                $this->jsonResponse(['status' => 'error', 'message' => 'ID inválido'], 400);
            }

            $res = $this->model->eliminarGrupo($id);
            $this->jsonResponse([
                'status' => $res ? 'success' : 'error',
                'message' => $res ? 'Grupo eliminado correctamente' : 'Error al eliminar'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener permisos de un grupo.
     */
    public function getPermisos(): void
    {
        try {
            $grupo_id = isset($_GET['grupo_id']) ? (int)$_GET['grupo_id'] : 0;
            if ($grupo_id <= 0) {
                $this->jsonResponse(['status' => 'error', 'message' => 'ID de grupo inválido'], 400);
            }

            $permisos = $this->model->obtenerPermisosPorGrupo($grupo_id);
            $this->jsonResponse(['status' => 'success', 'data' => $permisos]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Guardar permisos de un grupo.
     */
    public function savePermisos(): void
    {
        try {
            $grupo_id = isset($_POST['grupo_id']) ? (int)$_POST['grupo_id'] : 0;
            $permisos = isset($_POST['permisos']) ? json_decode($_POST['permisos'], true) : [];

            if ($grupo_id <= 0) {
                $this->jsonResponse(['status' => 'error', 'message' => 'ID de grupo inválido'], 400);
            }

            $res = $this->model->guardarPermisos($grupo_id, $permisos);
            $this->jsonResponse([
                'status' => $res ? 'success' : 'error',
                'message' => $res ? 'Permisos guardados correctamente' : 'Error al guardar permisos'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
