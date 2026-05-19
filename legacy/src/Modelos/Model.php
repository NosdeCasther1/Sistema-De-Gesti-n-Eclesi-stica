<?php

namespace Nosde\ProyectoIglesia\Modelos;

use PDO;
use Exception;

require_once __DIR__ . '/../../Config/conexion.php';

/**
 * Base Model using PDO
 * Implements efficient pagination and common DB operations.
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = getPDOConnection();
    }

    /**
     * Efficient Pagination (Limit/Offset optimized with metadata)
     * @param string $sql The base query (without LIMIT)
     * @param array $params Query parameters
     * @param int $page Current page
     * @param int $limit Items per page
     * @return array [data => [], meta => []]
     */
    public function paginate(string $sql, array $params = [], int $page = 1, int $limit = 20): array
    {
        // 1. Calculate offset
        $page = max(1, $page);
        $limit = max(1, min(100, $limit)); // Cap at 100
        $offset = ($page - 1) * $limit;

        // 2. Get total count (optimized count query)
        $countSql = "SELECT COUNT(*) FROM (" . $sql . ") as total_records";
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($params);
        $totalRecords = (int)$stmtCount->fetchColumn();

        // 3. Get paginated data
        $paginatedSql = $sql . " LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($paginatedSql);
        
        // Bind parameters
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        // Bind pagination params as integers (critical for PDO)
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $data = $stmt->fetchAll();

        // 4. Build metadata
        $totalPages = ceil($totalRecords / $limit);
        
        return [
            'data' => $data,
            'meta' => [
                'total_records' => $totalRecords,
                'total_pages'   => $totalPages,
                'current_page'  => $page,
                'per_page'      => $limit,
                'has_next_page' => $page < $totalPages,
                'has_prev_page' => $page > 1
            ]
        ];
    }
}
