<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', sans-serif; background-color: #0f172a; margin: 0; padding: 0; }
        .carnet-container {
            width: 242.65pt;
            height: 153.07pt;
            position: relative;
            overflow: hidden;
            border: 2px solid #fbbf24;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        .header {
            background-color: #fbbf24;
            color: #0f172a;
            padding: 5px;
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .photo-area {
            position: absolute;
            top: 40pt;
            left: 15pt;
            width: 60pt;
            height: 75pt;
            border: 2px solid #fbbf24;
            background-color: #334155;
        }
        .info-area {
            position: absolute;
            top: 35pt;
            left: 85pt;
            color: white;
        }
        .name { font-size: 12pt; font-weight: bold; margin-bottom: 2pt; color: #fbbf24; }
        .dpi { font-size: 8pt; color: #94a3b8; margin-bottom: 5pt; }
        .label { font-size: 7pt; color: #fbbf24; text-transform: uppercase; }
        .value { font-size: 9pt; margin-bottom: 5pt; }
        .qr-area {
            position: absolute;
            bottom: 10pt;
            right: 10pt;
            width: 45pt;
            height: 45pt;
            background: white;
            padding: 2pt;
        }
        .footer-line {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 5pt;
            background-color: #fbbf24;
        }
    </style>
</head>
<body>
    <div class="carnet-container">
        <div class="header">
            {{ $config->nombre_iglesia }} - Membresía
        </div>
        
        <div class="photo-area">
            @if(!empty($fotoBase64))
                <img src="{{ $fotoBase64 }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="text-align: center; padding-top: 25pt; color: #94a3b8; font-size: 20pt;">
                    <i class="fa fa-user"></i>
                </div>
            @endif
        </div>
        
        <div class="info-area">
            <div class="name">{{ $miembro->nombres }}</div>
            <div class="name" style="margin-top: -5pt;">{{ $miembro->apellidos }}</div>
            <div class="dpi">DPI: {{ $miembro->dpi ?? '---' }}</div>
            
            <div class="label">Miembro desde</div>
            <div class="value">{{ $miembro->fecha_integracion ? $miembro->fecha_integracion->format('d/m/Y') : '---' }}</div>
            
            <div class="label">Ministerio</div>
            <div class="value">{{ $miembro->ministerio ?? 'General' }}</div>
        </div>
        
        <div class="qr-area">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 100%; height: 100%;">
        </div>
        
        <div class="footer-line"></div>
    </div>
</body>
</html>
