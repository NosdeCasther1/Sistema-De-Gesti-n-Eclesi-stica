<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Documento Oficial</title>
    <style>
        @page { margin: 100px 80px; } /* Márgenes profesionales */
        body { 
            font-family: 'Georgia', serif; /* Tipografía formal */
            color: #1e293b;
            line-height: 1.8;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
        }
        .logo { max-height: 110px; width: auto; margin-bottom: 10px; }
        .church-name { font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin: 0; color: #0f172a; }
        .date-container { text-align: right; margin-bottom: 40px; font-style: italic; }
        .greeting { font-weight: bold; font-size: 14pt; margin-bottom: 30px; }
        .body-text { text-align: justify; margin-bottom: 60px; }
        .signature-container {
            text-align: center;
            margin-top: 100px;
            page-break-inside: avoid;
        }
        .signature-line {
            width: 300px;
            border-top: 1px solid #1e293b;
            margin: 0 auto 10px auto;
        }
        .pastor-name { font-weight: bold; font-size: 13pt; margin: 0; }
        .pastor-title { font-size: 11pt; color: #475569; margin: 0; }
    </style>
</head>
<body>

    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo Iglesia">
        @endif
        <h1 class="church-name">{{ $iglesia }}</h1>
    </div>

    <div class="date-container">
        Huehuetenango, {{ now()->translatedFormat('d \d\e F \d\e Y') }}
    </div>

    <div class="greeting">
        A QUIEN CORRESPONDA:
    </div>

    <div class="body-text">
        Por este medio hacemos constar que el(la) hermano(a) <strong>{{ $miembro->nombres }} {{ $miembro->apellidos }}</strong>, 
        identificado(a) con DPI <strong>{{ $miembro->dpi }}</strong>, es miembro activo y en plena comunión de nuestra congregación.
        <br><br>
        Extendemos la presente carta de recomendación para los usos que el(la) interesado(a) estime convenientes.
    </div>

    <div class="signature-container">
        <div class="signature-line"></div>
        <p class="pastor-name">{{ $pastor }}</p>
        <p class="pastor-title">Pastor General</p>
        <p class="pastor-title">{{ $iglesia }}</p>
    </div>

</body>
</html>
