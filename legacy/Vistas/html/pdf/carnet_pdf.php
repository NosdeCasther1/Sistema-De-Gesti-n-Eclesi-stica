<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 85.6mm 53.98mm;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 0;
            background: #fff;
        }

        /* ══════════════════════════
           ANVERSO
        ══════════════════════════ */
        .card-front {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            overflow: hidden;
            page-break-after: always;
            background: #fff;
        }

        /* Header como tabla para compatibilidad dompdf */
        .hdr-table {
            width: 85.6mm;
            height: 13mm;
            background: #8b0000;
            border-collapse: collapse;
        }
        .hdr-table td {
            vertical-align: middle;
            padding: 0;
        }
        .hdr-logo-cell {
            width: 13mm;
            text-align: center;
            padding-left: 2mm;
        }
        .hdr-logo-cell img {
            width: 10mm;
            height: 10mm;
            background: #fff;
            border-radius: 1mm;
            padding: 0.5mm;
        }
        .hdr-name-cell {
            font-size: 8.5pt;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            line-height: 1.1;
            padding-left: 1mm;
        }
        .hdr-badge-cell {
            width: 16mm;
            text-align: right;
            padding-right: 3mm;
        }
        .hdr-badge {
            display: inline-block;
            background: #c5a059;
            color: #ffffff;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 1mm 2.5mm;
            border-radius: 1mm;
        }

        /* Franja dorada */
        .gold-line {
            position: absolute;
            top: 13mm; left: 0; right: 0;
            height: 1.2mm;
            background: #c5a059;
        }

        /* Foto */
        .photo-box {
            position: absolute;
            top: 16.5mm; left: 4mm;
            width: 22mm; height: 28mm;
            border: 1.5pt solid #c5a059;
            border-radius: 1.5mm;
            overflow: hidden;
            background: #eee;
        }
        .photo-box img {
            width: 22mm; height: 28mm;
        }

        /* ID como marca de agua — derecha */
        .id-watermark {
            position: absolute;
            bottom: 9mm;
            right: 2mm;
            font-size: 20pt;
            font-weight: bold;
            color: rgba(139, 0, 0, 0.13);
            letter-spacing: 1pt;
            text-align: right;
            line-height: 1;
        }

        /* Bloque de información */
        .info {
            position: absolute;
            top: 16mm; left: 29mm; right: 2mm;
        }
        .m-name {
            font-size: 9pt;
            font-weight: bold;
            color: #8b0000;
            text-transform: uppercase;
            line-height: 1.15;
            margin-bottom: 2mm;
        }
        .lbl {
            font-size: 5.5pt;
            color: #999;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 1;
        }
        .val {
            font-size: 7.5pt;
            font-weight: bold;
            color: #111;
            line-height: 1.1;
            margin-bottom: 1.8mm;
        }

        /* Barra footer anverso */
        .front-footer {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 6mm;
            background: #8b0000;
            color: #fff;
            font-size: 6pt;
            font-weight: bold;
            text-align: center;
            line-height: 6mm;
            letter-spacing: 1.2pt;
            text-transform: uppercase;
        }

        /* ══════════════════════════
           REVERSO
        ══════════════════════════ */
        .card-back {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            overflow: hidden;
            background: #fff;
        }

        /* Header del reverso como tabla */
        .back-hdr-table {
            width: 85.6mm;
            height: 10mm;
            background: #8b0000;
            border-collapse: collapse;
        }
        .back-hdr-table td {
            font-size: 8pt;
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            vertical-align: middle;
            letter-spacing: 1.5pt;
            text-transform: uppercase;
        }

        /* Franja dorada reverso */
        .back-gold-line {
            position: absolute;
            top: 10mm; left: 0; right: 0;
            height: 1.2mm;
            background: #c5a059;
        }

        /* Texto 1 */
        .back-p1 {
            position: absolute;
            top: 13.5mm; left: 6mm; right: 6mm;
            font-size: 7pt;
            color: #444;
            text-align: center;
            line-height: 1.5;
        }

        /* Nombre de la iglesia en el reverso */
        .back-church {
            position: absolute;
            top: 23mm; left: 4mm; right: 4mm;
            font-size: 8.5pt;
            font-weight: bold;
            color: #8b0000;
            text-align: center;
            line-height: 1.2;
        }

        /* Texto 2 */
        .back-p2 {
            position: absolute;
            top: 28mm; left: 6mm; right: 6mm;
            font-size: 6.8pt;
            color: #444;
            text-align: center;
            line-height: 1.45;
        }

        /* Línea de firma — más abajo para dar espacio */
        .sig-line {
            position: absolute;
            top: 42mm;
            left: 18mm; right: 18mm;
            border-top: 0.7pt solid #555;
        }
        .sig-label {
            position: absolute;
            top: 43.5mm; left: 0; right: 0;
            font-size: 7pt;
            font-weight: bold;
            color: #8b0000;
            text-align: center;
        }
        .sig-pastor {
            position: absolute;
            top: 45.5mm; left: 0; right: 0;
            font-size: 6pt;
            color: #666;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        /* Footer reverso */
        .back-footer {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 6mm;
            background: #c5a059;
            color: #fff;
            font-size: 6pt;
            font-weight: bold;
            text-align: center;
            line-height: 6mm;
            letter-spacing: 1pt;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <!-- ═══ ANVERSO ═══ -->
    <div class="card-front">

        <!-- Header con tabla para dompdf -->
        <table class="hdr-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="hdr-logo-cell">
                    <img src="<?= $logoBase64 ?>" alt="Logo">
                </td>
                <td class="hdr-name-cell">
                    <?= htmlspecialchars($config['nombre_iglesia']) ?>
                </td>
                <td class="hdr-badge-cell">
                    <span class="hdr-badge"><?= htmlspecialchars($miembro['cargo'] ?: 'MIEMBRO') ?></span>
                </td>
            </tr>
        </table>

        <div class="gold-line"></div>

        <div class="photo-box">
            <img src="<?= $fotoBase64 ?>" alt="Foto">
        </div>

        <!-- ID como marca de agua en la derecha -->
        <div class="id-watermark"><?= str_pad($miembro['miembro_id'], 4, '0', STR_PAD_LEFT) ?></div>

        <div class="info">
            <div class="m-name"><?= htmlspecialchars($miembro['nombres'] . ' ' . $miembro['apellidos']) ?></div>

            <div class="lbl">DPI / Identificación</div>
            <div class="val"><?= htmlspecialchars($miembro['no_dpi']) ?></div>

            <div class="lbl">Ministerio</div>
            <div class="val"><?= htmlspecialchars($miembro['cargo'] ?: '—') ?></div>

            <div class="lbl">Fecha de Expedición</div>
            <div class="val"><?= date('d/m/Y') ?></div>

            <div class="lbl">ID</div>
            <div class="val" style="color:#8b0000; font-size:8pt;">#<?= str_pad($miembro['miembro_id'], 4, '0', STR_PAD_LEFT) ?></div>
        </div>

        <div class="front-footer">CREDENCIAL DE MEMBRESÍA OFICIAL</div>
    </div>

    <!-- ═══ REVERSO ═══ -->
    <div class="card-back">

        <!-- Header reverso con tabla -->
        <table class="back-hdr-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>CONSIDERACIONES IMPORTANTES</td>
            </tr>
        </table>

        <div class="back-gold-line"></div>

        <div class="back-p1">Esta credencial es personal e intransferible.<br>Identifica al portador como miembro activo de:</div>

        <div class="back-church"><?= htmlspecialchars($config['nombre_iglesia']) ?></div>

        <div class="back-p2">Se solicita a las autoridades brindar las consideraciones del caso al portador. En caso de extravío, devolver a la dirección de la iglesia.</div>

        <div class="sig-line"></div>
        <div class="sig-label">Firma del Pastor</div>
        <div class="sig-pastor"><?= htmlspecialchars($config['pastor_nombre'] ?? '') ?></div>

        <div class="back-footer">Válido solo con firma y sello oficial</div>
    </div>

</body>
</html>
