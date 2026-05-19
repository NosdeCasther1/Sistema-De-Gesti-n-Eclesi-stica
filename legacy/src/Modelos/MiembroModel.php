<?php

namespace Nosde\ProyectoIglesia\Modelos;

use Nosde\ProyectoIglesia\Config\ModeloBase;
use PDO;

class MiembroModel extends ModeloBase
{
    protected $table = 'miembros';

    public function getAll($familia_id = null, $searchTerm = null)
    {
        $where = [];
        $params = [];
        
        if ($familia_id) {
            $where[] = "m.familia = :familia_id";
            $params['familia_id'] = $familia_id;
        }

        if ($searchTerm !== null && $searchTerm !== '') {
            $search = "%" . strtolower($searchTerm) . "%";
            $where[] = "LOWER(CONCAT(
                IFNULL(m.nombres, ''), ' ', 
                IFNULL(m.apellidos, ''), ' ', 
                IFNULL(m.no_dpi, ''), ' ', 
                IFNULL(m.ciudad, ''), ' ', 
                IFNULL(f.nombre, '')
            )) LIKE :search";
            $params['search'] = $search;
        }

        $whereClause = count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT m.*, f.nombre as nombre_familia 
                  FROM {$this->table} m
                  LEFT JOIN familias f ON m.familia = f.id
                  $whereClause
                  ORDER BY m.miembro_id DESC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        if (isset($data['miembro_id']) && !empty($data['miembro_id'])) {
            $this->update($data['miembro_id'], $data);
            return $data['miembro_id'];
        }
        return $this->create($data);
    }

    private function create($data)
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);

        $query = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") 
                  VALUES (" . implode(',', $placeholders) . ")";
        
        $stmt = $this->db->prepare($query);
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    private function update($id, $data)
    {
        $id_val = $data['miembro_id'];
        unset($data['miembro_id']);
        
        $fields = array_map(fn($f) => "$f = :$f", array_keys($data));
        $query = "UPDATE {$this->table} SET " . implode(',', $fields) . " WHERE miembro_id = :id_val";
        
        $data['id_val'] = $id_val;
        $stmt = $this->db->prepare($query);
        return $stmt->execute($data);
    }

    public function deleteMiembro($id)
    {
        $query = "DELETE FROM {$this->table} WHERE miembro_id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function getContribuciones($id)
    {
        // Esta tabla puede variar según la base de datos real, 
        // pero basándonos en 'obtener_contribuciones.php' que se menciona en el JS
        // Simularemos o buscaremos la tabla de ofrendas/diezmos
        $query = "SELECT * FROM ofrendas WHERE miembro_id = :id ORDER BY fecha DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $query = "SELECT m.*, f.nombre as nombre_familia 
                  FROM {$this->table} m
                  LEFT JOIN familias f ON m.familia = f.id
                  WHERE m.miembro_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSystemConfig()
    {
        $query = "SELECT * FROM configuracion_sistema LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
