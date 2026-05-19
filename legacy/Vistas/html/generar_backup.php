<?php
// ─── Autenticación: solo usuarios con sesión activa ───────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Acceso no autorizado. Inicie sesión primero.']));
}

require_once __DIR__ . '/../../Config/conexion.php';
$conn = getDBConnection();

// Variables base de datos desde la config real interna para invocar mysqldump (si mysqldump está en PATH)
$config = require __DIR__ . '/../../Config/config.php';
$host = escapeshellarg($config['db_host']);
$user = escapeshellarg($config['db_user']);
$pass = escapeshellarg($config['db_pass']);
$name = escapeshellarg($config['db_name']);

$backup_file = 'backup_iglesia_' . date("Y-m-d-H-i-s") . '.sql';

// En Windows con XAMPP, mysqli o un script batch nativo puede funcionar, intentemos usar mysqldump:
$command = "mysqldump --opt -h $host -u $user " . ($config['db_pass'] ? "-p$pass " : "") . "$name > $backup_file";
system($command, $output);

if (file_exists($backup_file) && filesize($backup_file) > 0) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file));
    readfile($backup_file);
    unlink($backup_file);
    exit;
} else {
    // Fallback: Si mysqldump no está en el PATH, usamos un script PHP artesanal para exportar
    $tables = array();
    $result = mysqli_query($conn, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    $return = "SET FOREIGN_KEY_CHECKS=0;\n\n";
    foreach ($tables as $table) {
        $result = mysqli_query($conn, "SELECT * FROM " . $table);
        $num_fields = mysqli_num_fields($result);
        $return .= "DROP TABLE IF EXISTS " . $table . ";";
        $row2 = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE " . $table));
        $return .= "\n\n" . $row2[1] . ";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = mysqli_fetch_row($result)) {
                $return .= "INSERT INTO " . $table . " VALUES(";
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $return .= ");\n";
            }
        }
        $return .= "\n\n\n";
    }
    $return .= "SET FOREIGN_KEY_CHECKS=1;\n";

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    echo $return;
    exit;
}
