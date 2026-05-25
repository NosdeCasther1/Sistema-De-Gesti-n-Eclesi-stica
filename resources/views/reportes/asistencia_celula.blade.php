<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2b2b2b; margin: 0; padding: 0; font-size: 9px; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #c9a227; padding-bottom: 15px; }
        .logo-img { max-height: 60px; margin-bottom: 10px; display: inline-block; }
        .church-name { font-size: 22px; font-weight: bold; color: #6d0d0d; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 16px; font-weight: bold; color: #6d0d0d; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        .date-info { font-size: 11px; color: #64748b; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 40px; }
        th, td { border: 1px solid #cbd5e1; padding: 4px; text-align: center; vertical-align: middle; }
        th { background-color: #6d0d0d; color: #ffffff; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .name-col { text-align: left; width: 180px; font-weight: bold; background-color: #ffffff; color: #1e293b; padding-left: 8px; }
        .day-col { width: 18px; }
        .total-col { background-color: #fcfaf5; font-weight: bold; width: 30px; color: #0f172a; }
        
        .present { background-color: #dcfce7; color: #166534; font-weight: bold; }
        .weekend { background-color: #fcfaf5; }
        
        .info-bar { margin-bottom: 15px; display: table; width: 100%; font-size: 11px; color: #334155; }
        .info-item { display: table-cell; width: 33%; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia }}</h1>
        <div class="report-title">Control de Asistencia Mensual - Célula {{ $celula->nombre }}</div>
        <div class="date-info">Mes: {{ $fechaInicio->translatedFormat('F Y') }}</div>
    </div>

    <div class="info-bar">
        <div class="info-item"><strong>Líder:</strong> {{ $celula->lider->nombres ?? 'N/A' }} {{ $celula->lider->apellidos ?? '' }}</div>
        <div class="info-item"><strong>Sector:</strong> {{ $celula->sector ?? 'General' }}</div>
        <div class="info-item" style="text-align: right;"><strong>Día de Reunión:</strong> {{ $celula->dia_reunion }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="name-col">Nombre del Integrante</th>
                <th colspan="{{ $diasDelMes }}">Días del Mes</th>
                <th rowspan="2" class="total-col">Total</th>
            </tr>
            <tr>
                @for($i = 1; $i <= $diasDelMes; $i++)
                    <th class="day-col">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($celula->miembros as $m)
            <tr>
                <td class="name-col">{{ $m->nombres }} {{ $m->apellidos }}</td>
                @php $count = 0; @endphp
                @for($i = 1; $i <= $diasDelMes; $i++)
                    @php 
                        $attended = isset($asistencias[$m->id][$i]);
                        if($attended) $count++;
                    @endphp
                    <td class="day-col {{ $attended ? 'present' : '' }}">
                        {{ $attended ? 'X' : '' }}
                    </td>
                @endfor
                <td class="total-col">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <table style="width: 300px; border: none;">
            <tr style="border: none;">
                <td style="border: none; border-top: 1px solid #333; padding-top: 5px;">Firma del Líder de Célula</td>
                <td style="border: none; width: 50px;"></td>
                <td style="border: none; border-top: 1px solid #333; padding-top: 5px;">Vo.Bo. {{ $config->pastor_general ?? 'Pastor' }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generado por ProyectoIglesia {{ $config->nombre_iglesia }} el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
