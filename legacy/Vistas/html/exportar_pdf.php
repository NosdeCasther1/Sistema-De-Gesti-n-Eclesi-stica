<?php

// Incluir Composer's autoloader
require '/ProyectoIglesia/vendor/autoload.php';

// Importar clases necesarias de Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// Include de la conexión
include 'conexion.php';

// Obtener parámetros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$tipo_ingreso = $_GET['tipo_ingreso'] ?? '';
$categoria = $_GET['categoria'] ?? '';

// Consulta SQL
$sql = "SELECT 
            'DIEZMO' as tipo,
            d.id,
            d.referencia,
            CONCAT(m.nombres, ' ', m.apellidos) as miembro,
            'DIEZMO' as tipo_ingreso,
            'DIEZMO' as categoria,
            d.monto,
            d.fecha,
            d.modo_pago,
            m.tel_celular,
            m.email
        FROM diezmos d
        JOIN miembros m ON d.miembro = m.miembro_id
        WHERE d.fecha BETWEEN ? AND ?
        
        UNION ALL
        
        SELECT 
            'OFRENDA' as tipo,
            o.ofrenda_id as id,
            o.referencia,
            CONCAT(m.nombres, ' ', m.apellidos) as miembro,
            'OFRENDA' as tipo_ingreso,
            o.categoria,
            o.monto,
            o.fecha,
            o.modo_pago,
            m.tel_celular,
            m.email
        FROM ofrendas o
        JOIN miembros m ON o.miembro_id = m.miembro_id
        WHERE o.fecha BETWEEN ? AND ?";

$params = [$fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin];
$types = "ssss";

if (!empty($tipo_ingreso)) {
    $sql .= " HAVING tipo_ingreso = ?";
    $params[] = $tipo_ingreso;
    $types .= "s";
}

if (!empty($categoria)) {
    $sql .= empty($tipo_ingreso) ? " HAVING" : " AND";
    $sql .= " categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

$sql .= " ORDER BY fecha DESC";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calcular total
$total = 0;
$rows = [];
while ($row = $result->fetch_assoc()) {
    $total += $row['monto'];
    $rows[] = $row;
}

// Crear el contenido HTML para el PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #800080;
            margin: 0;
            padding: 10px 0;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #800080;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            margin-top: 20px;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }
        .badge-diezmo {
            background-color: #ff6b6b;
        }
        .badge-ofrenda {
            background-color: #4dabf7;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="img/logo.jpg" alt="Logo Iglesia">
        <h1>REPORTE DE INGRESOS</h1>
    </div>

    <div class="filters">
        <strong>Período:</strong> '.date('d/m/Y', strtotime($fecha_inicio)).' - '.date('d/m/Y', strtotime($fecha_fin)).'<br>
        '.(!empty($tipo_ingreso) ? '<strong>Tipo de Ingreso:</strong> '.$tipo_ingreso.'<br>' : '').'
        '.(!empty($categoria) ? '<strong>Categoría:</strong> '.$categoria.'<br>' : '').'
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Referencia</th>
                <th>Miembro</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Modo Pago</th>
            </tr>
        </thead>
        <tbody>';

foreach ($rows as $row) {
    $html .= '
            <tr>
                <td>'.$row['id'].'</td>
                <td>'.$row['referencia'].'</td>
                <td>'.$row['miembro'].'</td>
                <td><span class="badge badge-'.(strtolower($row['tipo_ingreso'])).'">'.$row['tipo_ingreso'].'</span></td>
                <td>'.$row['categoria'].'</td>
                <td>$'.number_format($row['monto'], 2).'</td>
                <td>'.date('d-m-Y', strtotime($row['fecha'])).'</td>
                <td>'.$row['modo_pago'].'</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="total">
        Total: $'.number_format($total, 2).'
    </div>
</body>
</html>';

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

// Cargar el HTML
$dompdf->loadHtml($html);

// Establecer el tamaño del papel y orientación
$dompdf->setPaper('A4', 'landscape');

// Renderizar el PDF
$dompdf->render();

// Generar nombre del archivo
$filename = 'Reporte_Ingresos_'.date('Y-m-d').'.pdf';

// Descargar el PDF
$dompdf->stream($filename, array('Attachment' => true));

// Cerrar conexiones
$stmt->close();
$conn->close();
?>