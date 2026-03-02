<?php
// conexion.php


if (!function_exists('getDBConnection')) {
    function getDBConnection()
    {
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

        return $conn; // Retornar la conexión mysqli
    }
}