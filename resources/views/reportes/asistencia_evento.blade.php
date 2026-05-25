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
        .event-meta { font-size: 12px; color: #475569; margin-top: 5px; }
        
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
        <div class="report-title">Reporte de Asistencia a Evento / Culto</div>
        <div class="event-meta">
            <strong>Actividad:</strong> {{ $evento->titulo }} | <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($evento->fecha_inicio)->translatedFormat('d de F de Y') }}
            @if($evento->ubicacion) | <strong>Lugar:</strong> {{ $evento->ubicacion }} @endif
        </div>
        <div style="font-size: 10px; color: #888; margin-top: 5px;">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="stats">
        Total de Asistentes Registrados: {{ count($asistencias) }} personas
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 10%;"># ID</th>
                <th style="width: 45%;">Nombre del Asistente</th>
                <th style="width: 25%;">DPI / CUI</th>
                <th style="width: 20%;">Hora de Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asistencias as $asistencia)
            <tr>
                <td>{{ $asistencia->miembro->id ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $asistencia->miembro->apellidos ?? '' }}, {{ $asistencia->miembro->nombres ?? 'Miembro Eliminado' }}</td>
                <td>{{ $asistencia->miembro->dpi ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($asistencia->hora)->format('h:i A') }}</td>
            </tr>
            @empty
            <tr>
                <td colSpan="4" style="text-align: center; color: #64748b; padding: 20px;">No hay registros de asistencia para este evento.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>"No dejando de congregarnos, como ... sino exhortándonos; y tanto más, cuanto veis que aquel día se acerca." - Hebreos 10:25</p>
    </div>
</body>
</html>
