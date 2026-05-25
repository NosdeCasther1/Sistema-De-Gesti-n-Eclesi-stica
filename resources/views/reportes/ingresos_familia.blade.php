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
        
        .family-block { margin-bottom: 25px; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; background-color: #ffffff; }
        .family-header { background-color: #6d0d0d; color: #ffffff; padding: 10px 12px; display: table; width: 100%; border-bottom: 2px solid #c9a227; }
        .family-name { font-weight: bold; font-size: 12px; display: table-cell; text-transform: uppercase; letter-spacing: 0.5px; color: #ffffff; }
        .family-total { text-align: right; color: #ffffff; font-weight: bold; font-size: 12px; display: table-cell; }
        
        .member-table { width: 100%; border-collapse: collapse; }
        .member-table th { text-align: left; padding: 8px 12px; font-size: 10px; color: #6d0d0d; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; background-color: #fcfaf5; font-weight: bold; }
        .member-table td { padding: 8px 12px; font-size: 11px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .member-table tr:last-child td { border-bottom: none; }
        .member-table tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia ?? 'AD REY DE REYES' }}</h1>
        <div class="report-title">Consolidado de Ingresos por Familia</div>
        <div style="font-size: 10px; color: #64748b; margin-top: 5px;">Período: {{ date('d/m/Y', strtotime($desde)) }} al {{ date('d/m/Y', strtotime($hasta)) }}</div>
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
                <span class="family-total">Total: {{ $config->moneda ?? 'Q' }}{{ number_format($totalFamilia, 2) }}</span>
            </div>
            <table class="member-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Miembro</th>
                        <th style="width: 50%;">Conceptos / Movimientos</th>
                        <th style="width: 20%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fam->miembros as $m)
                        @php $subtotalMiembro = $m->transacciones->sum('monto'); @endphp
                        @if($subtotalMiembro > 0)
                        <tr>
                            <td style="font-weight: bold;">{{ $m->nombres }} {{ $m->apellidos }}</td>
                            <td>
                                @foreach($m->transacciones as $t)
                                    <small>{{ $t->categoria->nombre }}: {{ $config->moneda ?? 'Q' }}{{ number_format($t->monto, 2) }}</small>@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td style="text-align: right; font-weight: bold; color: #6d0d0d;">{{ $config->moneda ?? 'Q' }}{{ number_format($subtotalMiembro, 2) }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    @endforeach

    <div class="footer">
        Este reporte muestra el aporte consolidado de los núcleos familiares registrados en {{ $config->nombre_iglesia ?? 'la iglesia' }}. Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
