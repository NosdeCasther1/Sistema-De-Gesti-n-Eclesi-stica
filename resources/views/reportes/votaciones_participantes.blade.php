<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Oficial de Participantes Votantes</title>
    <style>
        @page { margin: 30px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2b2b2b; margin: 0; padding: 0; font-size: 11px; line-height: 1.4; }
        
        .header { text-align: center; border-bottom: 3px solid #c9a227; padding-bottom: 15px; margin-bottom: 25px; position: relative; }
        .logo-img { max-height: 60px; margin-bottom: 10px; display: inline-block; }
        .church-name { font-size: 20px; font-weight: bold; color: #6d0d0d; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 14px; font-weight: bold; color: #6d0d0d; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 10px; }
        .meta-table tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .section-title { font-size: 11px; font-weight: bold; color: #ffffff; background-color: #6d0d0d; padding: 6px 10px; margin-top: 20px; text-transform: uppercase; border-left: 4px solid #c9a227; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 15px; }
        table.data-table th { background-color: #6d0d0d; color: #ffffff; text-align: left; padding: 8px 10px; font-size: 9px; text-transform: uppercase; font-weight: bold; border: 1px solid #6d0d0d; }
        table.data-table td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 10px; }
        table.data-table tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .confidentiality-note { background-color: #fdfbeb; border: 1px solid #c9a227; border-radius: 8px; padding: 12px; margin-top: 30px; font-size: 9px; color: #6d0d0d; line-height: 1.5; page-break-inside: avoid; }
        .confidentiality-note strong { text-transform: uppercase; display: block; margin-bottom: 4px; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia ?? 'AD REY DE REYES' }}</h1>
        <div class="report-title">Padrón de Participantes Votantes</div>
        <div style="font-size: 10px; color: #64748b;">ID Elección: #{{ $eleccion->id }} | Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Organización / Sociedad:</strong><br>{{ $organizacion->nombre }}</td>
            <td><strong>Proceso Electoral:</strong><br>{{ $eleccion->titulo }}</td>
        </tr>
        <tr>
            <td><strong>Participación Consolidada:</strong><br>{{ $totalVotantesUnicos }} de {{ $totalPadron }} Miembros ({{ $totalPadron > 0 ? round(($totalVotantesUnicos / $totalPadron) * 100, 2) : 0 }}%)</td>
            <td><strong>Desglose de Emisión:</strong><br>Digital: {{ $votosDigitales }} | Manual: {{ $votosManuales }}</td>
        </tr>
    </table>

    <div class="section-title">Detalle de Emisión de Votos (Orden de Participación)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 40%;">Nombre Completo</th>
                <th style="width: 25%;">Cargo Votado</th>
                <th style="width: 15%; text-align: center;">Modalidad</th>
                <th style="width: 15%; text-align: right;">Fecha/Hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participantes as $index => $part)
                <tr>
                    <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                    <td><strong>{{ $part->apellidos }}, {{ $part->nombres }}</strong></td>
                    <td>{{ $part->puesto_votado }}</td>
                    <td style="text-align: center;">
                        <span style="padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; background-color: {{ $part->modalidad === 'digital' ? '#eff6ff' : '#fef3c7' }}; color: {{ $part->modalidad === 'digital' ? '#1e40af' : '#92400e' }};">
                            {{ strtoupper($part->modalidad) }}
                        </span>
                    </td>
                    <td style="text-align: right; font-size: 9px; color: #64748b;">{{ \Carbon\Carbon::parse($part->created_at)->format('d/m H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b; padding: 20px;">No se registraron participaciones electorales para este proceso.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="confidentiality-note">
        <strong>Nota de Confidencialidad e Inmutabilidad Electoral</strong>
        En cumplimiento con los reglamentos de votación secreta y el control electoral de las Asambleas de Dios, este documento funciona como el padrón de verificación de asistencia y derecho de sufragio. Ninguna sección de este reporte vincula el nombre de los participantes con los candidatos o balotas de votación. Las bases de datos encriptan y segregan inmutablemente la auditoría de participación de los totales de escrutinio ciego.
    </div>

    <div class="footer">
        {{ $config->nombre_iglesia ?? 'AD REY DE REYES' }} - Registro Oficial de Participantes Votantes de {{ $organizacion->nombre }}
    </div>
</body>
</html>
