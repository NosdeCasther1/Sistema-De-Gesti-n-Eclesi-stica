<?php
// imprimir_recibo.php
if (!isset($_GET['nombre']) || !isset($_GET['monto'])) {
    die("Datos incompletos para generar el recibo.");
}

$nombre = htmlspecialchars($_GET['nombre'] ?? '');
$tipo = htmlspecialchars($_GET['tipo'] ?? 'Contribución');
$monto = number_format((float) ($_GET['monto'] ?? 0), 2);
$fecha = htmlspecialchars($_GET['fecha'] ?? date('Y-m-d'));
$metodo = htmlspecialchars($_GET['metodo'] ?? 'Efectivo');
$referencia = htmlspecialchars($_GET['ref'] ?? '');
$categoria = htmlspecialchars($_GET['cat'] ?? '');

$fechaArreglada = date('d/m/Y', strtotime($fecha));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo de
        <?php echo $tipo; ?>
    </title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fc;
        }

        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-top: 8px solid #1e293b;
            border-radius: 8px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .logo-area img {
            max-height: 55px;
            object-fit: contain;
        }

        .logo-area p {
            margin: 5px 0 0;
            color: #777;
            font-size: 14px;
        }

        .receipt-info {
            text-align: right;
        }

        .receipt-info h2 {
            margin: 0;
            color: #1e293b;
            font-size: 24px;
            text-transform: uppercase;
        }

        .receipt-info p {
            margin: 5px 0 0;
            color: #555;
        }

        .details-section {
            margin-bottom: 30px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-box {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #03a9f4;
        }

        .detail-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 18px;
            color: #333;
            font-weight: 600;
            margin: 0;
        }

        .amount-section {
            text-align: center;
            margin: 40px 0;
            padding: 20px;
            background: #e8eaf6;
            border-radius: 8px;
        }

        .amount-label {
            font-size: 14px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .amount-value {
            font-size: 48px;
            color: #1e293b;
            margin: 10px 0 0;
            font-weight: 700;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #888;
            font-size: 14px;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
        }

        .sig-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            text-align: center;
            color: #555;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                border-top: 4px solid #1e293b;
                padding: 20px;
            }

            .btn-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-bottom: 20px;" class="btn-print">
        <button onclick="window.print()"
            style="background: #1e293b; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; margin: 0 auto; gap: 10px;">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z" />
                <path
                    d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
            </svg>
            Imprimir Recibo
        </button>
    </div>

    <div class="receipt-container">
        <div class="header">
            <div class="logo-area">
                <img src="/ProyectoIglesia/img/logo.png" alt="Logo de la Congregación">
                <p>Comprobante Oficial de Ingreso</p>
            </div>
            <div class="receipt-info">
                <h2>RECIBO</h2>
                <p>Fecha:
                    <?php echo $fechaArreglada; ?>
                </p>
            </div>
        </div>

        <div class="details-section">
            <div class="detail-box" style="margin-bottom: 20px; border-left-color: #1e293b;">
                <div class="detail-label">Recibimos de</div>
                <div class="detail-value">
                    <?php echo $nombre; ?>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-box">
                    <div class="detail-label">Tipo de Ingreso</div>
                    <div class="detail-value">
                        <?php echo $tipo . ($categoria ? ' - ' . $categoria : ''); ?>
                    </div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Método de Pago</div>
                    <div class="detail-value">
                        <?php echo $metodo; ?>
                        <?php echo $referencia ? " (Ref: $referencia)" : ""; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-label">Por la cantidad de</div>
            <div class="amount-value">Q
                <?php echo $monto; ?>
            </div>
        </div>

        <div class="signatures">
            <div class="sig-line">Firma del Donante</div>
            <div class="sig-line">Tesorero / Receptor</div>
        </div>

        <div class="footer">
            <p>Este comprobante es un documento interno para control de ingresos.<br>Que Dios multiplique su ofrenda.
            </p>
        </div>
    </div>
</body>

</html>