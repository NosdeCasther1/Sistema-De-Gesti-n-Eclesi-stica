<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2b2b2b; margin: 0; padding: 0; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #fbbf24; padding-bottom: 15px; margin-bottom: 25px; }
        .church-name { font-size: 22px; font-weight: bold; color: #0f172a; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 16px; font-weight: bold; color: #fbbf24; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background-color: #0f172a; color: #ffffff; text-align: left; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #0f172a; }
        td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; font-size: 11px; vertical-align: middle; }
        tr:nth-child(even) td { background-color: #f8fafc; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" style="max-height: 50px; margin-bottom: 10px;">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia }}</h1>
        <div class="report-title">Censo General de Membresía</div>
        <div style="font-size: 10px;">Total Miembros: {{ count($miembros) }} | Generado: {{ now()->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>DPI / CUI</th>
                <th>Teléfono</th>
                <th>Ministerio</th>
                <th>Etapa</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($miembros as $m)
            <tr>
                <td><span style="font-weight: bold;">{{ $m->apellidos }}, {{ $m->nombres }}</span></td>
                <td>{{ $m->dpi }}</td>
                <td>{{ $m->telefono }}</td>
                <td>{{ $m->ministerio ?? 'General' }}</td>
                <td>{{ $m->etapa_consolidacion }}</td>
                <td>
                    <span class="badge" style="background-color: {{ $m->estado ? '#dcfce7' : '#fee2e2' }}; color: {{ $m->estado ? '#166534' : '#991b1b' }}; padding: 4px 8px;">
                        {{ $m->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento es para uso exclusivo de la administración pastoral de {{ $config->nombre_iglesia }}.
    </div>
</body>
</html>
