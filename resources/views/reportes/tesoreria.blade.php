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
        .date-range { font-size: 11px; color: #64748b; }
        
        .summary-box { width: 100%; margin-bottom: 30px; border-collapse: separate; border-spacing: 12px 0; }
        .summary-box td { background-color: #fcfaf5; border: 1px solid #cbd5e1; border-radius: 6px; padding: 14px 18px; text-align: center; }
        .summary-label { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold; display: block; margin-bottom: 6px; letter-spacing: 0.5px; }
        .summary-value { font-size: 16px; font-weight: bold; color: #0f172a; }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
        .text-primary { color: #6d0d0d; }
 
        table.main-table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 40px; }
        table.main-table th { background-color: #6d0d0d; color: #ffffff; text-align: left; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #6d0d0d; }
        table.main-table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; font-size: 11px; vertical-align: middle; }
        table.main-table tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #c9a227; width: 200px; margin-left: auto; margin-right: auto; padding-top: 5px; font-weight: bold; font-size: 12px; color: #6d0d0d; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia }}</h1>
        <div class="report-title">Reporte de Tesorería</div>
        <div class="date-range">Período: {{ date('d/m/Y', strtotime($desde)) }} al {{ date('d/m/Y', strtotime($hasta)) }}</div>
    </div>

    <table class="summary-box">
        <tr>
            <td>
                <span class="summary-label">Total Ingresos</span>
                <span class="summary-value text-success">{{ $config->moneda }}{{ number_format($ingresos, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Total Egresos</span>
                <span class="summary-value text-danger">{{ $config->moneda }}{{ number_format($gastos, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Balance Neto</span>
                <span class="summary-value text-primary">{{ $config->moneda }}{{ number_format($balance, 2) }}</span>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Concepto / Categoría</th>
                <th>Descripción</th>
                <th style="text-align: right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transacciones as $t)
            <tr>
                <td>{{ date('d/m/Y', strtotime($t->fecha)) }}</td>
                <td>
                    <span style="font-weight: bold;">{{ $t->categoria->nombre }}</span><br>
                    <small style="color: #888;">{{ $t->categoria->tipo }} - {{ $t->metodo_pago }}</small>
                </td>
                <td>{{ $t->descripcion ?? '-' }}</td>
                <td style="text-align: right; font-weight: bold;" class="{{ $t->categoria->tipo == 'Ingreso' ? 'text-success' : 'text-danger' }}">
                    {{ $t->categoria->tipo == 'Ingreso' ? '+' : '-' }} {{ $config->moneda }}{{ number_format($t->monto, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-line">{{ $config->pastor_general ?? 'Pastor General' }}</div>
        <p>Este documento es un reporte oficial de {{ $config->nombre_iglesia }} generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
