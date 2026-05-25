<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta Oficial de Escrutinio</title>
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
        
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 15px; }
        .data-table th { background-color: #6d0d0d; color: #ffffff; text-align: left; padding: 8px 10px; font-size: 9px; text-transform: uppercase; font-weight: bold; border: 1px solid #6d0d0d; }
        .data-table td { padding: 8px 10px; border: 1px solid #e2e8f0; font-size: 10px; }
        .data-table tr:nth-child(even) td { background-color: #fcfaf5; }
        
        .row-winner { background-color: #fdfbeb !important; font-weight: bold; }
        .badge-winner { color: #6d0d0d; background-color: #fdfbeb; padding: 2px 6px; border: 1px solid #c9a227; border-radius: 4px; font-size: 8px; font-weight: bold; display: inline-block; }
        
        .signatures-container { margin-top: 50px; width: 100%; page-break-inside: avoid; }
        .signature-line { width: 30%; text-align: center; vertical-align: top; font-size: 10px; }
        .signature-line div { border-top: 1px solid #c9a227; margin-top: 45px; padding-top: 5px; font-weight: bold; color: #6d0d0d; text-transform: uppercase; font-size: 9px; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo-img">
        @endif
        <h1 class="church-name">{{ $config->nombre_iglesia ?? 'AD REY DE REYES' }}</h1>
        <div class="report-title">Acta de Escrutinio Electoral</div>
        <div style="font-size: 10px; color: #64748b;">ID Elección: #{{ $eleccion->id }} | Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Organización / Sociedad:</strong><br>{{ $organizacion->nombre }}</td>
            <td><strong>Proceso Electoral:</strong><br>{{ $eleccion->titulo }}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Finalización:</strong><br>{{ $eleccion->fecha_fin ? $eleccion->fecha_fin->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
            <td><strong>Quórum de Asamblea:</strong><br>{{ $totalVotantesUnicos }} de {{ $totalPadron }} Miembros ({{ $totalPadron > 0 ? round(($totalVotantesUnicos / $totalPadron) * 100, 2) : 0 }}%)</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Regla de Escrutinio:</strong><br>{{ $eleccion->tipo_mayoria === 'absoluta' ? 'Mayoría Absoluta (Mitad más 1)' : 'Mayoría Simple' }}</td>
        </tr>
    </table>

    @foreach($resultados as $puesto => $listaCandidatos)
        <div class="section-title">CARGO: {{ strtoupper($puesto) }}</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Candidato Postulado</th>
                    <th style="text-align: center; width: 15%;">Votos Dig.</th>
                    <th style="text-align: center; width: 15%;">Votos Man.</th>
                    <th style="text-align: center; width: 15%;">Total Votos</th>
                    <th style="text-align: right; width: 10%;">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listaCandidatos as $index => $candidato)
                    <tr class="{{ $candidato->es_ganador ? 'row-winner' : '' }}">
                        <td>
                            <span style="{{ $candidato->es_ganador ? 'color: #6d0d0d;' : '' }}">{{ $candidato->miembro->nombres }} {{ $candidato->miembro->apellidos }}</span>
                            @if($candidato->es_ganador)
                                <span class="badge-winner" style="margin-left: 8px;">ELECTO</span>
                            @elseif($candidato->requiere_segunda_vuelta)
                                <span style="color: #c9a227; background-color: #fdfbeb; border: 1px solid #c9a227; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; margin-left: 8px; display: inline-block;">A 2DA VUELTA</span>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $candidato->votos_digitales }}</td>
                        <td style="text-align: center;">{{ $candidato->votos_manuales }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $candidato->votos_totales }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ $candidato->porcentaje }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <table class="signatures-container">
        <tr>
            <td class="signature-line">
                <div>Presidente de Debates</div>
            </td>
            <td style="width: 5%;"></td>
            <td class="signature-line">
                <div>Secretario de Actas</div>
            </td>
            <td style="width: 5%;"></td>
            <td class="signature-line">
                <div>Escrutador Oficial</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ $config->nombre_iglesia ?? 'AD REY DE REYES' }} - Acta Oficial de Escrutinio Electoral de {{ $organizacion->nombre }}
    </div>
</body>
</html>
