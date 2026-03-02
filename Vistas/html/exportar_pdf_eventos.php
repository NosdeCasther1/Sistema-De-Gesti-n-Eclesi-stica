<?php

// Incluir Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Importar clases necesarias de Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// Include de la conexión
require_once __DIR__ . '/../../Config/conexion.php';
$conn = getDBConnection();

if (!$conn) {
    die("La conexión a la base de datos no está disponible.");
}

// Obtener parámetros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d', strtotime('+30 days'));
$estado = $_GET['estado'] ?? '';
$lugar = $_GET['lugar'] ?? '';

// Construir la consulta SQL
$sql = "SELECT 
            evento_id,
            nombre_evento,
            descripcion,
            fecha_inicio,
            fecha_fin,
            lugar,
            estado,
            fecha_creacion
        FROM eventos 
        WHERE fecha_inicio BETWEEN ? AND ?";

$params = [$fecha_inicio, $fecha_fin];
$types = "ss";

if (!empty($estado)) {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= "s";
}

if (!empty($lugar)) {
    $sql .= " AND lugar = ?";
    $params[] = $lugar;
    $types .= "s";
}

$sql .= " ORDER BY fecha_inicio ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$total_eventos = count($rows);

// Crear el contenido HTML para el PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4e73df;
            margin: 0;
            padding: 10px 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fc;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #4e73df;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e3e6f0;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            margin-top: 20px;
            color: #4e73df;
        }
        .estado {
            font-weight: bold;
        }
        .estado-programado { color: #4e73df; }
        .estado-encurso { color: #1cc88a; }
        .estado-finalizado { color: #858796; }
        .estado-cancelado { color: #e74a3b; }
        .detail-text { color: #858796; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE EVENTOS</h1>
    </div>

    <div class="filters">
        <table style="border: none; margin: 0; padding: 0;">
            <tr style="background: none;">
                <td style="border: none; padding: 2px;"><strong>Período:</strong> ' . date('d/m/Y', strtotime($fecha_inicio)) . ' al ' . date('d/m/Y', strtotime($fecha_fin)) . '</td>
                <td style="border: none; padding: 2px;">' . (!empty($estado) ? '<strong>Estado:</strong> ' . $estado : '<strong>Estado:</strong> Todos') . '</td>
                <td style="border: none; padding: 2px;">' . (!empty($lugar) ? '<strong>Lugar:</strong> ' . $lugar : '<strong>Lugar:</strong> Todos') . '</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">Cód.</th>
                <th style="width: 35%;">Evento</th>
                <th style="width: 15%;">Inicio</th>
                <th style="width: 15%;">Fin</th>
                <th style="width: 20%;">Lugar</th>
                <th style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>';

if ($total_eventos > 0) {
    foreach ($rows as $row) {
        $estadoClass = '';
        $est = strtolower($row['estado']);
        if ($est == 'programado')
            $estadoClass = 'estado-programado';
        else if ($est == 'en curso')
            $estadoClass = 'estado-encurso';
        else if ($est == 'finalizado')
            $estadoClass = 'estado-finalizado';
        else
            $estadoClass = 'estado-cancelado';

        $html .= '
                <tr>
                    <td>#' . $row['evento_id'] . '</td>
                    <td>
                        <strong>' . $row['nombre_evento'] . '</strong><br>
                        <span class="detail-text">' . $row['descripcion'] . '</span>
                    </td>
                    <td>
                        ' . date('d/m/Y', strtotime($row['fecha_inicio'])) . '<br>
                        <span class="detail-text">' . date('H:i', strtotime($row['fecha_inicio'])) . '</span>
                    </td>
                    <td>
                         ' . date('d/m/Y', strtotime($row['fecha_fin'])) . '<br>
                         <span class="detail-text">' . date('H:i', strtotime($row['fecha_fin'])) . '</span>
                    </td>
                    <td>' . $row['lugar'] . '</td>
                    <td class="estado ' . $estadoClass . '">' . strtoupper($row['estado']) . '</td>
                </tr>';
    }
} else {
    $html .= '
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px;">No se encontraron eventos en este período.</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="total">
        Total de Eventos Encontrados: ' . $total_eventos . '
    </div>
</body>
</html>';

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);

// Cargar el HTML
$dompdf->loadHtml($html);

// Establecer el tamaño del papel y orientación
$dompdf->setPaper('A4', 'landscape');

// Renderizar el PDF
$dompdf->render();

// Generar nombre del archivo
$filename = 'Reporte_Eventos_' . date('Y-m-d') . '.pdf';

// Descargar el PDF
$dompdf->stream($filename, array('Attachment' => true));

// Cerrar conexiones
$stmt->close();
$conn->close();
?>