<?php

declare(strict_types=1);

namespace Nosde\ProyectoIglesia\Controladores;

use Nosde\ProyectoIglesia\Modelos\UsuarioModel;
use Exception;

class UsuarioController
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    private function jsonResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    /**
     * Listar todos los usuarios (API).
     */
    public function index(): void
    {
        try {
            $usuarios = $this->model->obtenerTodos();
            $this->jsonResponse(['status' => 'success', 'data' => $usuarios]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Guardar o actualizar un usuario.
     */
    public function store(): void
    {
        try {
            $id = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : null;
            $data = [
                'nombres' => trim($_POST['nombres'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'administrador',
                'grupo_id' => !empty($_POST['grupo_id']) ? (int)$_POST['grupo_id'] : null
            ];

            if (empty($data['nombres']) || empty($data['username']) || empty($data['email'])) {
                $this->jsonResponse(['status' => 'error', 'message' => 'Faltan campos obligatorios.'], 400);
            }

            // Verificar si ya existe
            if ($this->model->existeUsuario($data['username'], $data['email'], $id)) {
                $this->jsonResponse(['status' => 'error', 'message' => 'El nombre de usuario o email ya están en uso.'], 400);
            }

            if ($id) {
                $res = $this->model->actualizar($id, $data);
            } else {
                if (empty($data['password'])) {
                    $this->jsonResponse(['status' => 'error', 'message' => 'La contraseña es obligatoria para nuevos usuarios.'], 400);
                }
                $res = $this->model->crear($data);
            }

            $this->jsonResponse([
                'status' => $res ? 'success' : 'error',
                'message' => $res ? 'Usuario guardado correctamente' : 'Error al guardar usuario'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cambiar estado del usuario.
     */
    public function toggleStatus(): void
    {
        try {
            $id = (int)($_POST['id_usuario'] ?? 0);
            $currentStatus = $_POST['current_status'] ?? 'activo';
            $newStatus = ($currentStatus === 'activo') ? 'inactivo' : 'activo';

            $res = $this->model->cambiarEstado($id, $newStatus);
            $this->jsonResponse([
                'status' => $res ? 'success' : 'error',
                'message' => $res ? "Estado cambiado a $newStatus" : 'Error al cambiar estado'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un usuario (Soft Delete).
     */
    public function delete(): void
    {
        try {
            session_start();
            $id = (int)($_POST['id_usuario'] ?? 0);
            $currentUserId = $_SESSION['usuario_id'] ?? 0;

            if ($id === $currentUserId) {
                $this->jsonResponse(['status' => 'error', 'message' => 'No puedes eliminar tu propio usuario.'], 400);
            }

            $res = $this->model->eliminar($id);
            $this->jsonResponse([
                'status' => $res ? 'success' : 'error',
                'message' => $res ? 'Usuario eliminado correctamente' : 'Error al eliminar usuario'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar algunos usuarios (reemplaza get_users.php).
     */
    public function debugList(): void
    {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            // Reutilizamos el método de debug o lo adaptamos
            $sql = "SELECT id_usuario, username, email FROM usuarios LIMIT ?";
            $conn = getDBConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            echo "<h1>Lista de Usuarios (Debug)</h1><pre>";
            print_r($usuarios);
            echo "</pre>";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    /**
     * Resetear contraseña de un usuario (reemplaza reset_pass.php).
     */
    public function resetPassword(): void
    {
        try {
            $username = $_GET['user'] ?? 'admin';
            $newPass = $_GET['pass'] ?? '123456';

            $res = $this->model->actualizarPassword($username, $newPass);
            
            if ($res) {
                echo "Contraseña actualizada correctamente para el usuario: " . htmlspecialchars($username);
                echo "<br>Nueva contraseña (temporal): " . htmlspecialchars($newPass);
            } else {
                echo "Error al actualizar la contraseña.";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
