<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Matrimonio</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
            text-align: center;
        }
        .border {
            border: 15px solid #2d3748;
            padding: 40px;
            margin: 30px;
            position: relative;
            height: 90%;
        }
        .inner-border {
            border: 2px solid #a0aec0;
            padding: 30px;
            height: 95%;
        }
        .header {
            margin-bottom: 20px;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .title {
            font-size: 38px;
            font-weight: bold;
            color: #2b6cb0;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 20px;
            color: #4a5568;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .content {
            font-size: 22px;
            line-height: 1.8;
            margin-bottom: 40px;
            padding: 0 50px;
        }
        .name {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            border-bottom: 1px solid #718096;
            display: inline-block;
            padding: 0 20px;
            margin: 10px 0;
        }
        .footer {
            position: absolute;
            bottom: 50px;
            left: 0;
            width: 100%;
        }
        .signatures {
            width: 90%;
            margin: 0 auto;
        }
        .signature-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            border-bottom: 1px solid #2d3748;
            width: 80%;
            margin: 0 auto 10px auto;
            height: 60px;
            position: relative;
        }
        .signature-img {
            max-height: 60px;
            max-width: 150px;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
        }
        .seal {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            width: 300px;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="border">
        <div class="inner-border">
            
            @if($logoBase64)
            <div class="header">
                <img src="{{ $logoBase64 }}" class="logo" alt="Logo Iglesia">
            </div>
            @endif

            @if($selloBase64)
                <img src="{{ $selloBase64 }}" class="seal" alt="Sello de Fondo">
            @endif

            <h1 class="title">Certificado de Matrimonio</h1>
            <div class="subtitle">{{ $iglesia }}</div>

            <div class="content">
                <em>"Por tanto, dejará el hombre a su padre y a su madre, y se unirá a su mujer, y serán una sola carne."</em> (Génesis 2:24).<br><br>
                
                Hacemos constar ante Dios y la congregación, que:<br>
                <div class="name">{{ $matrimonio->esposo->nombres }} {{ $matrimonio->esposo->apellidos }}</div><br>
                y<br>
                <div class="name">{{ $matrimonio->esposa->nombres }} {{ $matrimonio->esposa->apellidos }}</div><br><br>

                Fueron unidos en el Sagrado Vínculo del Matrimonio el día <strong>{{ $matrimonio->fecha_matrimonio->format('d') }}</strong> del mes de <strong>{{ $matrimonio->fecha_matrimonio->translatedFormat('F') }}</strong> del año <strong>{{ $matrimonio->fecha_matrimonio->format('Y') }}</strong>, habiendo cumplido con las leyes civiles y los requisitos de nuestra congregación.
            </div>

            <div class="footer">
                <table class="signatures">
                    <tr>
                        <td class="signature-box">
                            <div class="signature-line">
                                @if($firmaBase64)
                                    <img src="{{ $firmaBase64 }}" class="signature-img" alt="Firma Pastor">
                                @endif
                            </div>
                            <strong>{{ $pastor }}</strong><br>
                            Pastor Oficiante
                        </td>
                        <td class="signature-box">
                            <div class="signature-line"></div>
                            <strong>{{ $matrimonio->testigo_1 ?: 'Testigo 1' }}</strong><br>
                            Firma
                        </td>
                        <td class="signature-box">
                            <div class="signature-line"></div>
                            <strong>{{ $matrimonio->testigo_2 ?: 'Testigo 2' }}</strong><br>
                            Firma
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</body>
</html>
