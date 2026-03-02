<?php
function obtenerMiembros($conn) {
    $sql = "SELECT * FROM miembros";
    $result = $conn->query($sql);
    
    if ($result === false) {
        // La consulta falló
        error_log("Error en la consulta SQL: " . $conn->error);
        return null;
    }
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // No se encontraron resultados
        return [];
    }
}