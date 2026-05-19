<?php

namespace Nosde\ProyectoIglesia\Modelos;

use Nosde\ProyectoIglesia\Config\ModeloBase;
use PDO;

class FamiliaModel extends ModeloBase
{
    protected $table = 'familias';

    public function getAll()
    {
        $query = "SELECT f.*, COUNT(m.miembro_id) as total_integrantes 
                  FROM {$this->table} f 
                  LEFT JOIN miembros m ON f.id = m.familia
                  GROUP BY f.id
                  ORDER BY f.nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            return $this->update($data['id'], $data);
        }
        return $this->create($data);
    }

    private function create($data)
    {
        $query = "INSERT INTO {$this->table} (nombre, descripcion, direccion, telefono_principal, celula_id) 
                  VALUES (:nombre, :descripcion, :direccion, :telefono_principal, :celula_id)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'direccion' => $data['direccion'] ?? null,
            'telefono_principal' => $data['telefono_principal'] ?? null,
            'celula_id' => !empty($data['celula_id']) ? $data['celula_id'] : null
        ]);
    }

    private function update($id, $data)
    {
        $query = "UPDATE {$this->table} SET 
                    nombre = :nombre, 
                    descripcion = :descripcion,
                    direccion = :direccion,
                    telefono_principal = :telefono_principal,
                    celula_id = :celula_id
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'direccion' => $data['direccion'] ?? null,
            'telefono_principal' => $data['telefono_principal'] ?? null,
            'celula_id' => !empty($data['celula_id']) ? $data['celula_id'] : null,
            'id' => $id
        ]);
    }

    public function deleteFamilia($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}
