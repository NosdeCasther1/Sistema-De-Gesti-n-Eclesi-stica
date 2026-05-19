<?php

declare(strict_types=1);

namespace Nosde\ProyectoIglesia\Modelos;

require_once __DIR__ . '/../../Config/conexion.php';

use mysqli;
use Exception;

class UsuarioModel
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    /**
     * Obtener todos los usuarios que no han sido eliminados.
     */
    public function obtenerTodos(): array
    {
        $sql = "SELECT u.id_usuario, u.nombres, u.username, u.email, u.role, u.status, u.created_at, u.ultimo_login, u.grupo_id, g.nombre AS nombre_grupo
                FROM usuarios u
                LEFT JOIN grupos_usuarios g ON u.grupo_id = g.id
                WHERE u.deleted_at IS NULL
                ORDER BY u.created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Verificar si un username o email ya existen.
     */
    public function existeUsuario(string $username, string $email, ?int $exceptId = null): bool
    {
        $sql = "SELECT id_usuario FROM usuarios WHERE (username = ? OR email = ?) AND deleted_at IS NULL";
        if ($exceptId) {
            $sql .= " AND id_usuario != ?";
        }
        $stmt = $this->conn->prepare($sql);
        if ($exceptId) {
            $stmt->bind_param("ssi", $username, $email, $exceptId);
        } else {
            $stmt->bind_param("ss", $username, $email);
        }
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Crear un nuevo usuario.
     */
    public function crear(array $data): bool
    {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (nombres, username, password, email, role, grupo_id, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'activo')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssi", $data['nombres'], $data['username'], $hashed_password, $data['email'], $data['role'], $data['grupo_id']);
        return $stmt->execute();
    }

    /**
     * Actualizar un usuario existente.
     */
    public function actualizar(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $query = "UPDATE usuarios SET nombres=?, username=?, email=?, role=?, grupo_id=?, password=? WHERE id_usuario=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssisi", $data['nombres'], $data['username'], $data['email'], $data['role'], $data['grupo_id'], $hashed_password, $id);
        } else {
            $query = "UPDATE usuarios SET nombres=?, username=?, email=?, role=?, grupo_id=? WHERE id_usuario=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssii", $data['nombres'], $data['username'], $data['email'], $data['role'], $data['grupo_id'], $id);
        }
        return $stmt->execute();
    }

    /**
     * Cambiar el estado (activo/inactivo).
     */
    public function cambiarEstado(int $id, string $nuevoEstado): bool
    {
        $query = "UPDATE usuarios SET status = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $nuevoEstado, $id);
        return $stmt->execute();
    }

    /**
     * Eliminación lógica (soft delete).
     */
    public function eliminar(int $id): bool
    {
        $query = "UPDATE usuarios SET deleted_at = NOW(), status = 'inactivo' WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Actualizar la contraseña de un usuario.
     */
    public function actualizarPassword(string $username, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ? WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $hash, $username);
        return $stmt->execute();
    }
}
