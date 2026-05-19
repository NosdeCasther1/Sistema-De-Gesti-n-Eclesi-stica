<?php

namespace Nosde\ProyectoIglesia\Controladores;

use Nosde\ProyectoIglesia\Modelos\MiembroModel;

class MiembroController
{
    private $miembroModel;

    public function __construct()
    {
        $this->miembroModel = new MiembroModel();
    }

    public function index()
    {
        $familia_id = $_GET['familia_id'] ?? null;
        $search = $_GET['q'] ?? null;
        $miembros = $this->miembroModel->getAll($familia_id, $search);
        return $this->jsonResponse('success', '', $miembros);
    }

    public function getFormData()
    {
        $familiaModel = new \Nosde\ProyectoIglesia\Modelos\FamiliaModel();
        $familias = $familiaModel->getAll();
        return $this->jsonResponse('success', '', [
            'familias' => $familias
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse('error', 'Método no permitido');
        }

        $data = [
            'miembro_id'       => $_POST['miembro_id'] ?? null,
            'nombres'          => trim($_POST['nombres']),
            'apellidos'        => trim($_POST['apellidos']),
            'no_dpi'           => $_POST['no_dpi'],
            'fecha_nacimiento' => $_POST['fecha_nacimiento'],
            'sexo'             => $_POST['sexo'],
            'estado_civil'     => $_POST['estado_civil'],
            'direccion'        => trim($_POST['direccion']),
            'ciudad'           => trim($_POST['ciudad'] ?? ''),
            'tel_celular'      => trim($_POST['tel_celular']),
            'tel_fijo'         => trim($_POST['tel_fijo'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'familia'          => $_POST['familia'],
            'nivel_estudio'    => $_POST['nivel_estudio'] ?? '',
            'profesion'        => trim($_POST['profesion'] ?? ''),
            'cargo'            => $_POST['cargo'] ?? '',
            'estado'           => $_POST['estado'] ?? 'Activo'
        ];

        // Validaciones básicas
        if (empty($data['nombres']) || empty($data['apellidos']) || empty($data['no_dpi'])) {
            return $this->jsonResponse('error', 'Nombres, Apellidos y DPI son obligatorios');
        }

        $savedId = $this->miembroModel->save($data);
        if ($savedId) {
            // Procesar foto si se subió una
            $foto = $this->handlePhoto($savedId);
            if ($foto) {
                $this->miembroModel->save([
                    'miembro_id' => $savedId,
                    'foto'       => $foto
                ]);
            }

            $msg = empty($data['miembro_id']) ? 'Miembro registrado correctamente' : 'Miembro actualizado correctamente';
            return $this->jsonResponse('success', $msg);
        }

        return $this->jsonResponse('error', 'Error al procesar la solicitud');
    }

    private function handlePhoto($miembroId)
    {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['foto'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        if ($file['size'] > $maxSize) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../assets/img/miembros/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = "MEMBER_{$miembroId}_" . time() . ".webp";
        $targetPath = $uploadDir . $filename;

        // Convertir a WebP
        $img = null;
        switch ($file['type']) {
            case 'image/jpeg':
                $img = @imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $img = @imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/webp':
                $img = @imagecreatefromwebp($file['tmp_name']);
                break;
        }

        if ($img) {
            // Mantener transparencia si es PNG/WebP
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
            
            if (imagewebp($img, $targetPath, 80)) {
                imagedestroy($img);
                return $filename;
            }
            imagedestroy($img);
        }

        return null;
    }

    public function delete()
    {
        $id = $_POST['miembro_id'] ?? null;
        if (!$id) return $this->jsonResponse('error', 'ID no proporcionado');

        if ($this->miembroModel->deleteMiembro($id)) {
            return $this->jsonResponse('success', 'Miembro eliminado correctamente');
        }
        return $this->jsonResponse('error', 'Error al eliminar el miembro');
    }

    public function contribuciones()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) return $this->jsonResponse('error', 'ID no proporcionado');

        $data = $this->miembroModel->getContribuciones($id);
        return $this->jsonResponse('success', '', $data);
    }

    public function generarCarnet()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("ID de miembro no proporcionado.");
        }

        $miembro = $this->miembroModel->getById($id);
        if (!$miembro) {
            die("Miembro no encontrado.");
        }

        // Obtener configuración del sistema
        $config = $this->miembroModel->getSystemConfig() ?: [
            'nombre_iglesia' => 'Nuestra Iglesia',
            'pastor_nombre' => 'Pastor Principal',
            'logo_url' => 'logo.png'
        ];

        // Preparar imágenes en Base64 para dompdf
        $baseDir = dirname(__DIR__, 2); // Raíz del proyecto
        $logoPath = $baseDir . '/assets/img/' . ($config['logo_url'] ?: 'logo.png');
        if (!file_exists($logoPath) || is_dir($logoPath)) {
            $logoPath = $baseDir . '/assets/img/logo.png';
        }
        $logoBase64 = $this->imageToBase64($logoPath);

        $fotoPath = $baseDir . '/assets/img/miembros/' . ($miembro['foto'] ?: 'default_avatar.png');
        if (!file_exists($fotoPath) || is_dir($fotoPath)) {
            $fotoPath = $baseDir . '/assets/img/miembros/default_avatar.png';
        }
        $fotoBase64 = $this->imageToBase64($fotoPath);

        // Capturar HTML de la plantilla
        ob_start();
        require_once __DIR__ . '/../../Vistas/html/pdf/carnet_pdf.php';
        $html = ob_get_clean();

        // Configuración de Dompdf
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        
        // Formato CR80 (85.6mm x 53.98mm)
        $dompdf->setPaper([0, 0, 242.65, 153.01], 'portrait');
        
        $dompdf->render();
        $dompdf->stream("Carnet_" . $miembro['nombres'] . ".pdf", ["Attachment" => false]);
    }

    private function imageToBase64($path)
    {
        if (!file_exists($path)) return '';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = @file_get_contents($path);
        if (!$data) return '';
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    private function jsonResponse($status, $message = '', $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}
