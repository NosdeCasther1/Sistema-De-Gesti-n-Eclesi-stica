<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2b2b2b; margin: 0; padding: 0; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 3px solid #c9a227; padding-bottom: 15px; margin-bottom: 25px; }
        .logo-img { max-height: 60px; margin-bottom: 10px; display: inline-block; }
        .church-name { font-size: 22px; font-weight: bold; color: #6d0d0d; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 16px; font-weight: bold; color: #6d0d0d; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background-color: #6d0d0d; color: #ffffff; text-align: left; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #6d0d0d; }
        td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; font-size: 11px; vertical-align: middle; }
        tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia }}</h1>
        <div class="report-title">Reporte General de Inventario</div>
        <div style="font-size: 10px;">Total Artículos Registrados: {{ count($inventarios) }} | Generado: {{ now()->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Artículo</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Ubicación</th>
                <th>Responsable</th>
                <th>Fecha Adq.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventarios as $i)
            <tr>
                <td><span style="font-weight: bold;">{{ $i->nombre }}</span></td>
                <td>{{ $i->cantidad }}</td>
                <td>
                    @php
                        $color = match($i->estado) {
                            'Nuevo' => '#1e3a8a',
                            'Bueno' => '#166534',
                            'Regular' => '#ca8a04',
                            'Malo' => '#991b1b',
                            default => '#333'
                        };
                    @endphp
                    <span style="color: {{ $color }}; font-weight: bold;">{{ $i->estado }}</span>
                </td>
                <td>{{ $i->ubicacion ?? 'N/A' }}</td>
                <td>{{ $i->responsable ? $i->responsable->nombres . ' ' . $i->responsable->apellidos : 'Sin asignar' }}</td>
                <td>{{ $i->fecha_adquisicion ? \Carbon\Carbon::parse($i->fecha_adquisicion)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento es para uso exclusivo de la administración pastoral y tesorería de {{ $config->nombre_iglesia }}.
    </div>
</body>
</html>
