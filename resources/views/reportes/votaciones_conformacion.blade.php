<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta Oficial de Conformación de Organización</title>
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
        
        .badge-status { padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; display: inline-block; }
        
        .signatures-container { margin-top: 60px; width: 100%; page-break-inside: avoid; }
        .signature-line { width: 30%; text-align: center; vertical-align: top; font-size: 10px; }
        .signature-line div { border-top: 1px solid #c9a227; margin-top: 50px; padding-top: 5px; font-weight: bold; color: #6d0d0d; text-transform: uppercase; font-size: 9px; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia ?? 'AD REY DE REYES' }}</h1>
        <div class="report-title">Acta de Conformación de Junta Directiva</div>
        <div style="font-size: 10px; color: #64748b;">Organización: {{ $organizacion->nombre }} | Generado: {{ now()->format('d/m/Y') }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Nombre de la Organización:</strong><br>{{ $organizacion->nombre }}</td>
            <td><strong>Proceso Electoral de Origen:</strong><br>{{ $eleccion->titulo }}</td>
        </tr>
        <tr>
            <td><strong>Fecha del Proceso:</strong><br>{{ $eleccion->fecha_fin ? $eleccion->fecha_fin->format('d/m/Y') : now()->format('d/m/Y') }}</td>
            <td><strong>Regla Aplicada:</strong><br>{{ $eleccion->tipo_mayoria === 'absoluta' ? 'Mayoría Absoluta' : 'Mayoría Simple' }}</td>
        </tr>
    </table>

    <div class="section-title">Directiva Electa que Conforma la Organización</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30%;">Cargo Directivo</th>
                <th style="width: 40%;">Miembro Asignado / Electo</th>
                <th style="width: 15%; text-align: center;">Votos Obtenidos</th>
                <th style="width: 15%; text-align: center;">Porcentaje (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($directivaConformada as $dir)
                <tr>
                    <td><strong>{{ strtoupper($dir['puesto']) }}</strong></td>
                    <td>
                        @if($dir['estado'] === 'Electo')
                            <span style="color: #6d0d0d; font-weight: bold;">{{ $dir['nombre'] }}</span>
                        @else
                            <span style="color: #64748b; font-style: italic;">{{ $dir['nombre'] }}</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $dir['votos'] }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($dir['porcentaje'] !== '-')
                            {{ $dir['porcentaje'] }}%
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">No hay información disponible para conformar la directiva.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p style="font-size: 10px; line-height: 1.5; margin-top: 30px;">
        Por la presente se hace constar que los miembros enlistados anteriormente han sido electos de conformidad con el quórum legal y directrices estatutarias vigentes de las Asambleas de Dios, quedando oficialmente integrados a la junta directiva y habilitados en sus funciones ministeriales y administrativas a partir de la fecha de suscripción de este documento.
    </p>

    <table class="signatures-container">
        <tr>
            <td class="signature-line">
                <div>Pastor General</div>
            </td>
            <td style="width: 5%;"></td>
            <td class="signature-line">
                <div>Presidente de Debates</div>
            </td>
            <td style="width: 5%;"></td>
            <td class="signature-line">
                <div>Secretario de Actas</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ $config->nombre_iglesia ?? 'AD REY DE REYES' }} - Acta de Conformación de Directiva Oficial de {{ $organizacion->nombre }}
    </div>
</body>
</html>
