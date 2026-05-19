<?php

namespace Nosde\ProyectoIglesia\Config;

use PDO;
use PDOException;
use Exception;

abstract class ModeloBase
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = $this->getPDOConnection();
    }

    private function getPDOConnection(): PDO
    {
        $config = require __DIR__ . '/../../Config/config.php';
        
        $host = $config['db_host'];
        $db   = $config['db_name'];
        $user = $config['db_user'];
        $pass = $config['db_pass'];
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
}
