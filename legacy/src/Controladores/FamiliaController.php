<?php

namespace Nosde\ProyectoIglesia\Controladores;

use Nosde\ProyectoIglesia\Modelos\FamiliaModel;

class FamiliaController extends Controller
{
    private $familiaModel;

    public function __construct()
    {
        $this->familiaModel = new FamiliaModel();
    }

    public function index()
    {
        $familias = $this->familiaModel->getAll();
        return $this->jsonResponse('success', '', $familias);
    }

    public function store()
    {
        $this->validateCSRF();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse('error', 'Método no permitido');
        }

        // Detect if JSON or Form Post
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $data = $this->getSanitizedJson();
        } else {
            $data = $this->sanitizeArray($_POST);
        }

        if (empty($data['nombre'])) {
            return $this->jsonResponse('error', 'El nombre de la familia es obligatorio');
        }

        if ($this->familiaModel->save($data)) {
            $msg = empty($data['id']) ? 'Familia creada exitosamente' : 'Familia actualizada exitosamente';
            return $this->jsonResponse('success', $msg);
        }

        return $this->jsonResponse('error', 'Error al guardar la familia');
    }

    public function delete()
    {
        $this->validateCSRF();
        $id = $_POST['id'] ?? null;
        if (!$id) return $this->jsonResponse('error', 'ID no proporcionado');

        try {
            if ($this->familiaModel->deleteFamilia($id)) {
                return $this->jsonResponse('success', 'Familia eliminada correctamente');
            }
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'No se puede eliminar la familia. Asegúrese de que no tenga miembros asociados.');
        }

        return $this->jsonResponse('error', 'Error al eliminar la familia');
    }

}
