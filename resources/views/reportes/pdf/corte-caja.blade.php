<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja - {{ $iglesia->nombre_iglesia }}</title>
    <style>
        @page {
            margin: 30px 40px 60px 40px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
        }
        /* Membrete */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #c9a227;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-cell {
            width: 80px;
        }
        .logo-img {
            max-height: 60px;
            width: auto;
            display: inline-block;
        }
        .title-cell {
            text-align: left;
            padding-left: 15px;
        }
        .church-name {
            font-size: 22px;
            font-weight: bold;
            color: #6d0d0d;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #6d0d0d;
            margin: 4px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-cell {
            text-align: right;
            font-size: 11px;
            color: #64748b;
        }
        .meta-cell strong {
            color: #334155;
        }

        /* Resumen de Saldos */
        .summary-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .summary-card {
            background-color: #fcfaf5;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 14px 18px;
            text-align: center;
            width: 25%;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 6px;
            display: block;
            letter-spacing: 0.5px;
        }
        .summary-val {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
        .text-primary { color: #6d0d0d; }

        /* Tabla Principal */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .main-table th {
            background-color: #6d0d0d;
            color: #ffffff;
            text-align: left;
            padding: 10px 12px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #6d0d0d;
        }
        .main-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            font-size: 11px;
            vertical-align: middle;
        }
        .main-table tr:nth-child(even) td {
            background-color: #fcfaf5;
        }
        .col-right {
            text-align: right;
        }
        .col-center {
            text-align: center;
        }

        /* Bloque de Firmas */
        .signatures-container {
            width: 100%;
            margin-top: 60px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 30px;
        }
        .signature-line {
            border-bottom: 1px solid #c9a227;
            height: 60px;
            margin-bottom: 8px;
        }
        .signature-title {
            font-weight: bold;
            font-size: 12px;
            color: #6d0d0d;
        }
        .signature-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Pie de página */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>
<body>

    <div class="footer">
        <table width="100%">
            <tr>
                <td align="left">Reporte Oficial - {{ $iglesia->nombre_iglesia }}</td>
                <td align="center">Generado el {{ now()->format('d/m/Y H:i') }}</td>
                <td align="right" class="page-number"></td>
            </tr>
        </table>
    </div>

    <!-- Membrete -->
    <table class="header-table">
        <tr>
            @if(!empty($logoBase64))
            <td class="logo-cell">
                <img src="{{ $logoBase64 }}" class="logo-img">
            </td>
            @endif
            <td class="title-cell">
                <h1 class="church-name">{{ $iglesia->nombre_iglesia }}</h1>
                <div class="report-title">Corte de Caja Oficial</div>
                <div style="font-size: 11px; color: #475569; margin-top: 2px;">
                    <strong>Caja:</strong> {{ $account ? $account->name : 'Todas las Cajas Consolidadas' }}
                </div>
            </td>
            <td class="meta-cell">
                <div><strong>Período Auditado:</strong></div>
                <div>{{ Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} al {{ Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</div>
                <div style="margin-top: 6px;"><strong>Filtro de Tipo:</strong></div>
                <div>
                    @if($selectedType === 'income') Solo Ingresos
                    @elseif($selectedType === 'expense') Solo Egresos
                    @else Ingresos y Egresos
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Resumen de Saldos -->
    <table class="summary-table">
        <tr>
            <td class="summary-card">
                <span class="summary-label">Saldo Anterior</span>
                <span class="summary-val">{{ $iglesia->moneda }}{{ number_format($saldoAnterior, 2) }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Total Ingresos</span>
                <span class="summary-val text-success">+{{ $iglesia->moneda }}{{ number_format($totalIngresos, 2) }}</span>
            </td>
            <td class="summary-card">
                <span class="summary-label">Total Egresos</span>
                <span class="summary-val text-danger">-{{ $iglesia->moneda }}{{ number_format($totalGastos, 2) }}</span>
            </td>
            <td class="summary-card" style="background-color: #6d0d0d; border-color: #6d0d0d;">
                <span class="summary-label" style="color: #ffffff;">Saldo Actual</span>
                <span class="summary-val" style="color: #c9a227; font-size: 18px;">{{ $iglesia->moneda }}{{ number_format($saldoActual, 2) }}</span>
            </td>
        </tr>
    </table>

    <!-- Tabla Principal de Movimientos -->
    <table class="main-table">
        <thead>
            <tr>
                <th width="12%">Fecha</th>
                <th width="16%">Referencia</th>
                <th width="32%">Descripción / Cuenta</th>
                <th width="20%" class="col-right">Ingreso</th>
                <th width="20%" class="col-right">Egreso</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $t->reference_number ?: 'S/N' }}</strong><br>
                    <span style="font-size: 9px; color: #64748b;">{{ $t->category->name ?? 'General' }}</span>
                </td>
                <td>
                    <div style="font-weight: bold; color: #1e293b;">{{ $t->description ?: 'Sin descripción' }}</div>
                    @if(!$account)
                    <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                        <i class="fas fa-wallet"></i> Caja: {{ $t->account->name ?? 'N/D' }}
                    </div>
                    @endif
                </td>
                <td class="col-right text-success font-bold">
                    @if($t->type === 'income')
                        {{ $iglesia->moneda }}{{ number_format($t->amount, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="col-right text-danger font-bold">
                    @if($t->type === 'expense')
                        {{ $iglesia->moneda }}{{ number_format($t->amount, 2) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="col-center" style="padding: 25px; color: #64748b;">
                    No se encontraron movimientos financieros en el período seleccionado.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bloque de Firmas -->
    <div class="signatures-container">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-title">Elaborado por</div>
                    <div class="signature-subtitle">Tesorero General</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-title">Visto Bueno</div>
                    <div class="signature-subtitle">{{ $iglesia->pastor_general ?: 'Pastor General' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
