<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2b2b2b; margin: 0; padding: 0; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 3px solid #c9a227; padding-bottom: 15px; margin-bottom: 25px; }
        .logo-img { max-height: 60px; margin-bottom: 10px; display: inline-block; }
        .church-name { font-size: 24px; font-weight: bold; color: #6d0d0d; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 16px; font-weight: bold; color: #6d0d0d; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .main-table th { background-color: #6d0d0d; color: #ffffff; text-align: left; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #6d0d0d; }
        .main-table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; font-size: 11px; vertical-align: middle; }
        .main-table tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .stats { margin-bottom: 20px; font-size: 12px; font-weight: bold; color: #6d0d0d; border-left: 4px solid #c9a227; padding-left: 10px; background-color: #fcfaf5; padding-top: 8px; padding-bottom: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia ?? 'AD REY DE REYES' }}</h1>
        <div class="report-title">Registro de Miembros Bautizados</div>
        <div style="font-size: 10px; color: #888; margin-top: 5px;">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="stats">
        Total de Bautizados Activos: {{ count($miembros) }} personas
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 10%;"># ID</th>
                <th style="width: 40%;">Nombre Completo</th>
                <th style="width: 20%;">DPI / CUI</th>
                <th style="width: 15%;">Ministerio</th>
                <th style="width: 15%;">Fecha Integración</th>
            </tr>
        </thead>
        <tbody>
            @foreach($miembros as $m)
            <tr>
                <td>{{ $m->id }}</td>
                <td style="font-weight: bold;">{{ $m->apellidos }}, {{ $m->nombres }}</td>
                <td>{{ $m->dpi }}</td>
                <td>
                    @if($m->ministerios->isNotEmpty())
                        {{ $m->es_lider ? 'Líder - ' : '' }}{{ $m->ministerios->pluck('nombre')->implode(', ') }}
                    @else
                        {{ $m->es_lider ? 'Líder' : 'General' }}
                    @endif
                </td>
                <td>{{ $m->fecha_integracion ? $m->fecha_integracion->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>"Id, pues, y haced discípulos a todas las naciones, bautizándolos en el nombre del Padre, y del Hijo, y del Espíritu Santo" - Mateo 28:19</p>
    </div>
</body>
</html>
