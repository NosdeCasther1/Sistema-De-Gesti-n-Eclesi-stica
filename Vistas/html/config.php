<?php
// Cargar variables de entorno (requiere phpdotenv)
//require_once __DIR__ . '/vendor/autoload.php';
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->load();

// Base path para la aplicación (útil si no está en la raíz del servidor)
define('APP_BASE_PATH', $_ENV['APP_BASE_PATH'] ?? '/ProyectoIglesia');

// Configuración de la base de datos
return [
    'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
    'db_name' => $_ENV['DB_NAME'] ?? 'iglesia_db',
    'db_user' => $_ENV['DB_USER'] ?? 'root',
    'db_pass' => $_ENV['DB_PASS'] ?? 'admin'
];