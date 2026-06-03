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
        
        .puesto-section { margin-bottom: 40px; page-break-inside: avoid; }
        .puesto-header { background-color: #fcfaf5; border-left: 4px solid #c9a227; padding: 10px; margin-bottom: 15px; }
        .puesto-title { font-size: 14px; font-weight: bold; color: #6d0d0d; margin: 0; text-transform: uppercase; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .main-table th { background-color: #6d0d0d; color: #ffffff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #6d0d0d; }
        .main-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; font-size: 11px; vertical-align: middle; }
        .main-table tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; color: white; text-transform: uppercase; }
        .badge-activo { background-color: #10b981; }
        .badge-inactivo { background-color: #ef4444; }
 
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
        <div class="report-title">Directorio de Cargos y Puestos Organizacionales</div>
        <div style="font-size: 10px; color: #888; margin-top: 5px;">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="stats">
        Total de Cargos/Puestos Distintos: {{ count($asignacionesAgrupadas) }}
    </div>

    @foreach($asignacionesAgrupadas as $puesto => $asignaciones)
        <div class="puesto-section">
            <div class="puesto-header">
                <h3 class="puesto-title">{{ $puesto }}</h3>
                <div style="font-size: 10px; color: #555; margin-top: 5px; font-weight: bold;">
                    Total Personas con este Cargo: {{ count($asignaciones) }}
                </div>
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th width="5%"># ID</th>
                        <th width="35%">Nombre del Integrante</th>
                        <th width="20%">Organización</th>
                        <th width="20%">DPI / Teléfono</th>
                        <th width="10%">Fecha Asignación</th>
                        <th width="10%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asignaciones as $asig)
                    <tr>
                        <td>{{ $asig->miembro_id }}</td>
                        <td style="font-weight: bold;">{{ $asig->apellidos }}, {{ $asig->nombres }}</td>
                        <td>{{ $asig->organizacion_nombre }}</td>
                        <td>
                            <div>{{ $asig->dpi ?? 'N/A' }}</div>
                            <div style="font-size: 10px; color: #666;">Tel: {{ $asig->telefono ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $asig->fecha_asignacion ? \Carbon\Carbon::parse($asig->fecha_asignacion)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $asig->estado ? 'badge-activo' : 'badge-inactivo' }}">
                                {{ $asig->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        <p>Sistema de Gestión Eclesiástica - {{ $config->nombre_iglesia ?? 'AD REY DE REYES' }}</p>
    </div>
</body>
</html>
