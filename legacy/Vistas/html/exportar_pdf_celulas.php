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

// Consulta SQL
$sql = "SELECT 
            id,
            nombre,
            lider_nombre,
            anfitrion,
            direccion,
            horario,
            estado,
            fecha_creacion
        FROM celulas_familiares 
        ORDER BY nombre ASC";

$result = $conn->query($sql);

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$total_celulas = count($rows);

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
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0;
            padding: 10px 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .info-bar {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
            border: 1px solid #dee2e6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #0d6efd;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 13px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            font-size: 12px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-activo { background-color: #d1e7dd; color: #0f5132; }
        .badge-inactivo { background-color: #f8d7da; color: #842029; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-weight: bold;
            color: #0d6efd;
        }
        .text-muted { color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte General de Células Familiares</h1>
    </div>

    <div class="info-bar">
        Fecha de Generación: ' . date('d/m/Y H:i') . ' | Total de Células: ' . $total_celulas . '
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Célula</th>
                <th style="width: 15%;">Líder</th>
                <th style="width: 15%;">Anfitrión</th>
                <th style="width: 25%;">Dirección</th>
                <th style="width: 15%;">Horario</th>
                <th style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>';

if ($total_celulas > 0) {
    foreach ($rows as $row) {
        $estadoClass = (strtolower($row['estado']) == 'activo') ? 'badge-activo' : 'badge-inactivo';
        $html .= '
            <tr>
                <td><strong>' . htmlspecialchars($row['nombre']) . '</strong></td>
                <td>' . htmlspecialchars($row['lider_nombre']) . '</td>
                <td>' . htmlspecialchars($row['anfitrion']) . '</td>
                <td>' . htmlspecialchars($row['direccion']) . '</td>
                <td>' . htmlspecialchars($row['horario']) . '</td>
                <td><span class="badge ' . $estadoClass . '">' . strtoupper($row['estado']) . '</span></td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align: center; padding: 20px;">No hay células registradas.</td></tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        Total de Registros: ' . $total_celulas . '
    </div>
</body>
</html>';

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = 'Reporte_Celulas_' . date('Y-m-d') . '.pdf';
$dompdf->stream($filename, array('Attachment' => true));

$conn->close();
