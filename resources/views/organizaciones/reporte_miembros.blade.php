<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Miembros - {{ $organizacion->nombre }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 10px;
        }
        .logo {
            width: 80px;
            height: auto;
            position: absolute;
            left: 20px;
            top: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #2b6cb0;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 16px;
            color: #4a5568;
            margin: 0 0 5px 0;
        }
        .date {
            font-size: 10px;
            color: #718096;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #cbd5e0;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .puesto {
            font-weight: bold;
            color: #2c5282;
        }
    </style>
</head>
<body>
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
    @endif

    <div class="header">
        <h1 class="title">{{ $iglesia }}</h1>
        <h2 class="subtitle">Reporte de Miembros: {{ $organizacion->nombre }}</h2>
        <div class="date">Generado el {{ now()->format('d/m/Y H:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No.</th>
                <th width="30%">Nombre Completo</th>
                <th width="20%">Puesto / Cargo</th>
                <th width="15%">Teléfono</th>
                <th width="15%">DPI</th>
                <th width="15%">Fecha Asignación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($miembros as $index => $miembro)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $miembro->apellidos }}, {{ $miembro->nombres }}</td>
                <td class="puesto">{{ $miembro->pivot->puesto ?? 'Miembro' }}</td>
                <td>{{ $miembro->telefono ?: 'N/A' }}</td>
                <td>{{ $miembro->dpi }}</td>
                <td>{{ $miembro->pivot->fecha_asignacion ? \Carbon\Carbon::parse($miembro->pivot->fecha_asignacion)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">No hay miembros asignados a esta organización.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 10px; color: #718096;">
        Total de miembros: {{ $miembros->count() }}
    </div>
</body>
</html>
