<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Bautismo - {{ $miembro->nombres }}</title>
    <style>
        @page {
            margin: 0px;
            size: letter landscape;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #fcfcfc;
            font-family: 'Georgia', serif;
            color: #1f2937; /* slate-800 */
        }
        
        /* Step 1: Blindar la Simetría del Marco */
        .frame-outer {
            position: absolute;
            top: 30px;
            bottom: 30px;
            left: 30px;
            right: 30px;
            border: 2px solid #A16207; /* 2px Oro */
        }

        .frame-inner {
            position: absolute;
            top: 38px;
            bottom: 38px;
            left: 38px;
            right: 38px;
            border: 5px solid #7F1D1D; /* 5px Vino */
        }

        .content-wrapper {
            position: absolute;
            top: 50px;
            bottom: 50px;
            left: 50px;
            right: 50px;
            text-align: center;
            padding-top: 30px; /* Space before logo */
        }

        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        /* Typography & Hierarchy */
        .title {
            color: #7F1D1D; /* Vino */
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            margin: 5px 0 10px 0;
            text-transform: uppercase;
        }
        
        .opening-phrase {
            font-size: 15px;
            font-style: italic;
            color: #4b5563; /* slate-600 */
            margin-bottom: 8px;
        }

        .member-name-container {
            margin: 12px 0;
        }
        .member-name {
            font-size: 34pt;
            font-weight: bold;
            color: #7F1D1D; /* Vino */
            display: inline-block;
            border-bottom: 1px solid #A16207; /* Fine decorative line in Oro */
            padding-bottom: 4px;
            min-width: 65%;
        }

        .faith-text {
            font-size: 16px;
            line-height: 1.4;
            color: #1f2937;
            margin: 10px auto;
            max-width: 85%;
        }

        .verse-container {
            margin: 15px auto;
            max-width: 75%;
        }
        .verse-text {
            font-style: italic;
            font-size: 14px;
            color: #475569; /* Dark Gray */
            line-height: 1.45;
        }

        .date-text {
            font-size: 15px;
            margin: 12px 0;
            color: #1f2937;
        }

        /* Footer Table (2-column layout) */
        .footer-table {
            width: 100%;
            margin-top: 35px;
            border-collapse: collapse;
        }

        .footer-col-left {
            width: 65%;
            text-align: right;
            padding-right: 40px;
            vertical-align: bottom;
        }

        .footer-col-right {
            width: 35%;
            text-align: left;
            padding-left: 20px;
            vertical-align: middle;
        }

        .signature-container {
            display: inline-block;
            text-align: center;
            width: 250px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            padding-top: 5px;
            font-size: 15px;
            color: #1f2937;
        }

        .seal-img {
            width: 105px;
            height: 105px;
            opacity: 0.90; /* 90% opacity */
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @php
        $fechaBautismo = \Carbon\Carbon::parse($miembro->fecha_bautismo ?? now())->locale('es');
        $dia = $fechaBautismo->format('j');
        $mes = $fechaBautismo->isoFormat('MMMM');
        $año = $fechaBautismo->format('Y');
    @endphp

    <div class="frame-outer"></div>
    <div class="frame-inner"></div>

    <div class="content-wrapper">
        <!-- Header with center Logo -->
        <div class="header">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" class="logo" alt="Logo Iglesia">
            @endif
        </div>

        <!-- Title -->
        <div class="title">Certificado de Bautismo</div>

        <!-- Opening -->
        <div class="opening-phrase">
            La Iglesia AD REY DE REYES hace constar que
        </div>

        <!-- Central Member Name -->
        <div class="member-name-container">
            <div class="member-name">
                {{ $miembro->nombres }} {{ $miembro->apellidos }}
            </div>
        </div>

        <!-- Faith Text -->
        <div class="faith-text">
            ha cumplido con el mandato bíblico del bautismo en aguas, <br>
            dando testimonio público de su fe en Jesucristo.
        </div>

        <!-- Spiritual Element (Verse) -->
        <div class="verse-container">
            <div class="verse-text">
                “Porque somos sepultados juntamente con él para muerte por el bautismo, a fin de que como Cristo resucitó de los muertos por la gloria del Padre, así también nosotros andemos en vida nueva.”<br>
                <strong>(Romanos 6:4)</strong>
            </div>
        </div>

        <!-- Date -->
        <div class="date-text">
            Dado a los {{ $dia }} días del mes de {{ $mes }} de {{ $año }}.
        </div>

        <!-- Footer (2 Columns for Signature and Seal) -->
        <table class="footer-table">
            <tr>
                <td class="footer-col-left">
                    <div class="signature-container">
                        <div style="position: relative; height: 50px; width: 250px;">
                                @if($firmaBase64)
                                    <img src="{{ $firmaBase64 }}" style="position: absolute; left: 5px; bottom: -98px; width: 240px; height: auto; z-index: 10;" alt="Firma Pastor">
                                @endif
                        </div>
                        <div class="signature-line">
                            <strong>{{ $pastor }}</strong><br>
                            Pastor General
                        </div>
                    </div>
                </td>
                <td class="footer-col-right">
                    <div style="position: relative; height: 50px; width: 150px; display: inline-block;">
                        @if($selloBase64)
                            <img src="{{ $selloBase64 }}" style="position: absolute; left: 10px; bottom: -35px; width: 135px; height: 135px; opacity: 0.90; z-index: 10;" alt="Sello Iglesia">
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
