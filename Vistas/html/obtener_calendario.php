<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Config/conexion.php';

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$query = "SELECT evento_id as id, nombre_evento as title, fecha_inicio as start, fecha_fin as end, estado, lugar FROM eventos";
$result = mysqli_query($conn, $query);

$eventos = [];
// Mismos colores aleatorios basados en Hash de ID usados en el frontend
$colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#6f42c1'];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $color = $colors[crc32($row['id']) % count($colors)];

        $estado = strtolower($row['estado']);
        if ($estado == 'cancelado') {
            $color = '#e74a3b'; // Rojo
        } elseif ($estado == 'finalizado') {
            $color = '#858796'; // Gris
        } elseif ($estado == 'en curso') {
            $color = '#1cc88a'; // Verde
        }

        $eventos[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $row['start'], // Formato datetime SQL compatible con ISO8601
            'end' => $row['end'],
            'color' => $color,
            'extendedProps' => [
                'lugar' => $row['lugar'],
                'estado' => $row['estado']
            ]
        ];
    }
}

echo json_encode($eventos);
mysqli_close($conn);
