<?php
require_once __DIR__ . '/../Config/conexion.php';

class GruposUsuariosModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDBConnection();
    }

    public function obtenerGrupos()
    {
        $sql = "SELECT * FROM grupos_usuarios ORDER BY id ASC";
        $result = $this->conn->query($sql);
        $grupos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $grupos[] = $row;
            }
        }
        return $grupos;
    }

    public function obtenerGrupoPorId($id)
    {
        $sql = "SELECT * FROM grupos_usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public function crearGrupo($nombre, $descripcion)
    {
        $sql = "INSERT INTO grupos_usuarios (nombre, descripcion) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $nombre, $descripcion);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function actualizarGrupo($id, $nombre, $descripcion)
    {
        $sql = "UPDATE grupos_usuarios SET nombre = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
        return $stmt->execute();
    }

    public function eliminarGrupo($id)
    {
        // También eliminará los permisos por CASCADE en la BD
        $sql = "DELETE FROM grupos_usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // --- MANEJO DE PERMISOS --- //

    public function obtenerPermisosPorGrupo($grupo_id)
    {
        $sql = "SELECT * FROM permisos_grupos WHERE grupo_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $grupo_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $permisos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $permisos[$row['modulo']] = $row;
            }
        }
        return $permisos;
    }

    public function guardarPermisos($grupo_id, $permisos)
    {
        // 1. Limpiar permisos existentes para el grupo
        $deleteSql = "DELETE FROM permisos_grupos WHERE grupo_id = ?";
        $stmtDel = $this->conn->prepare($deleteSql);
        $stmtDel->bind_param("i", $grupo_id);
        $stmtDel->execute();

        // 2. Insertar nuevos permisos
        if (empty($permisos))
            return true;

        $sql = "INSERT INTO permisos_grupos (grupo_id, modulo, can_view, can_edit, can_delete) VALUES (?, ?, ?, ?, ?)";
        $stmtIns = $this->conn->prepare($sql);

        foreach ($permisos as $modulo => $acciones) {
            $can_view = isset($acciones['view']) && $acciones['view'] ? 1 : 0;
            $can_edit = isset($acciones['edit']) && $acciones['edit'] ? 1 : 0;
            $can_delete = isset($acciones['delete']) && $acciones['delete'] ? 1 : 0;

            $stmtIns->bind_param("isiii", $grupo_id, $modulo, $can_view, $can_edit, $can_delete);
            $stmtIns->execute();
        }
        return true;
    }
}
?>