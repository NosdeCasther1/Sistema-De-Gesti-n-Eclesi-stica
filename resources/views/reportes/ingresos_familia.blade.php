<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2b2b2b; margin: 0; padding: 0; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 15px; margin-bottom: 25px; }
        .church-name { font-size: 24px; font-weight: bold; color: #0f172a; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 16px; font-weight: bold; color: #10b981; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        
        .family-block { margin-bottom: 25px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background-color: #ffffff; }
        .family-header { background-color: #0f172a; color: #ffffff; padding: 10px 12px; display: table; width: 100%; border-bottom: 1px solid #0f172a; }
        .family-name { font-weight: bold; font-size: 12px; display: table-cell; text-transform: uppercase; letter-spacing: 0.5px; }
        .family-total { text-align: right; color: #10b981; font-weight: bold; font-size: 12px; display: table-cell; }
        
        .member-table { width: 100%; border-collapse: collapse; }
        .member-table th { text-align: left; padding: 8px 12px; font-size: 10px; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; }
        .member-table td { padding: 8px 12px; font-size: 11px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .member-table tr:last-child td { border-bottom: none; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="church-name">AD REY DE REYES</h1>
        <div class="report-title">Consolidado de Ingresos por Familia</div>
        <div style="font-size: 11px;">Período: {{ date('d/m/Y', strtotime($desde)) }} al {{ date('d/m/Y', strtotime($hasta)) }}</div>
    </div>

    @foreach($familias as $fam)
        @php 
            $totalFamilia = 0;
            foreach($fam->miembros as $m) {
                $totalFamilia += $m->transacciones->sum('monto');
            }
        @endphp
        
        @if($totalFamilia > 0)
        <div class="family-block">
            <div class="family-header">
                <span class="family-name">Familia: {{ $fam->nombre }}</span>
                <span class="family-total">Total: Q{{ number_format($totalFamilia, 2) }}</span>
            </div>
            <table class="member-table">
                <thead>
                    <tr>
                        <th>Miembro</th>
                        <th>Conceptos / Movimientos</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fam->miembros as $m)
                        @php $subtotalMiembro = $m->transacciones->sum('monto'); @endphp
                        @if($subtotalMiembro > 0)
                        <tr>
                            <td style="width: 200px;">{{ $m->nombres }}</td>
                            <td>
                                @foreach($m->transacciones as $t)
                                    <small>{{ $t->categoria->nombre }}: Q{{ number_format($t->monto, 2) }}</small>@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td style="text-align: right; font-weight: bold;">Q{{ number_format($subtotalMiembro, 2) }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    @endforeach

    <div class="footer">
        Este reporte muestra el aporte consolidado de los núcleos familiares registrados.
    </div>
</body>
</html>
