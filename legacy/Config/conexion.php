<?php
// conexion.php


if (!function_exists('getDBConnection')) {
    function getDBConnection()
    {
        static $conexion = null;

        if ($conexion !== null) {
            return $conexion;
        }

        // Cargar configuración
        $config = require_once __DIR__ . '/config.php'; // Archivo separado con configuraciones

        // Validar que la configuración sea correcta
        if (!is_array($config) || !isset($config['db_host'], $config['db_name'], $config['db_user'], $config['db_pass'])) {
            throw new Exception("Archivo de configuración inválido.");
        }

        // Crear conexión con mysqli
        $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

        // Verificar conexión
        if ($conn->connect_error) {
            error_log("Error de conexión: " . $conn->connect_error);
            throw new Exception("Error de conexión al servidor");
        }

        $conexion = $conn;
        return $conexion; // Retornar la conexión mysqli cacheada
    }
}

if (!function_exists('getPDOConnection')) {
    function getPDOConnection()
    {
        static $pdo = null;

        if ($pdo !== null) {
            return $pdo;
        }

        $config = require __DIR__ . '/config.php';
        
        try {
            $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new \PDO($dsn, $config['db_user'], $config['db_pass'], $options);
            return $pdo;
        } catch (\PDOException $e) {
            error_log("Error PDO: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos.");
        }
    }
}