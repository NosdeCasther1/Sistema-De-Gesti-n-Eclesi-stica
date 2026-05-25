<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carnet - {{ $miembro->nombres }}</title>
    <style>
        @page { 
            margin: 0px; 
            size: 330px 210px; /* Le dice al motor del PDF el tamaño exacto de la hoja */
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; 
            padding: 0; 
            width: 330px; 
            height: 210px; 
        }
        /* Aseguramos que la tarjeta mida exactamente lo mismo que la hoja */
        .card { 
            width: 330px; 
            height: 210px; 
            background-color: #fff;
            position: relative;
            overflow: hidden; /* Corta cualquier elemento que intente salirse */
        }

        /* ==== LADO FRONTAL ==== */
        .front-header { 
            background-color: #D4AF37; 
            color: white; 
            text-align: center; 
            padding: 5px 0; 
            font-size: 11px; 
            font-weight: bold; 
            letter-spacing: 1px;
            height: 14px;
        }
        
        .front-body { width: 100%; height: 165px; position: relative; z-index: 2; }
        .col-left { float: left; width: 100px; text-align: center; padding-top: 12px; position: relative; z-index: 2; }
        .col-right { float: left; width: 210px; padding-top: 20px; padding-left: 10px; text-align: left; position: relative; z-index: 2; }

        .logo-img { width: 45px; height: auto; }
        .membresia-text { font-size: 8px; font-weight: bold; color: #8B0000; margin: 3px 0; }
        .photo { width: 65px; height: 85px; object-fit: cover; border: 1px solid #cbd5e1; border-radius: 4px; }
        
        .name { 
            font-size: 13px; 
            font-weight: bold; 
            color: #8B0000; 
            text-transform: uppercase; 
            line-height: 1.2; 
            margin-top: 0; 
            margin-bottom: 6px; 
            border-bottom: 1px solid #e2e8f0; 
            padding-bottom: 4px; 
            width: 95%;
        }
        
        .info-row { margin-bottom: 5px; }
        .field-label { font-size: 7.5px; color: #D4AF37; font-weight: bold; margin: 0; letter-spacing: 0.5px; }
        .field-value { font-size: 9.5px; color: #333; font-weight: bold; margin: 1px 0 0 0; }
        
        .qr-container { position: absolute; bottom: 35px; right: 10px; z-index: 3; }
        .qr-img { width: 45px; height: 45px; }
        
        .watermark {
            position: absolute;
            bottom: 35px;
            right: 62px;
            font-size: 9px;
            color: rgba(139, 0, 0, 0.4);
            font-weight: bold;
            letter-spacing: 1.5px;
            z-index: 2;
            height: 45px;
            line-height: 45px;
            white-space: nowrap;
        }
        
        .front-footer { 
            background-color: #D4AF37; 
            color: white; 
            text-align: center; 
            font-size: 8.5px; 
            line-height: 18px; 
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 18px;
            box-sizing: border-box;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 3;
        }

        /* ==== LADO TRASERO ==== */
        .back-card {
            background-color: #8B0000; 
            border: 4px solid #D4AF37;
            text-align: center;
            box-sizing: border-box;
            width: 322px; 
            height: 202px; 
            page-break-before: always;
        }
        
        .back-logo-wrapper {
            width: 90px;
            height: 46px;
            background-color: #ffffff;
            border-radius: 6px;
            margin: 10px auto 5px auto;
            text-align: center;
            line-height: 46px;
            box-shadow: 0px 2px 4px rgba(0,0,0,0.3);
        }
        .back-logo { max-width: 88px; max-height: 44px; vertical-align: middle; }
        
        .back-title { color: #d4af37; font-size: 11.5px; font-weight: bold; margin-bottom: 6px; letter-spacing: 1px;}
        .back-text { color: #fdfbf7; font-size: 8.5px; text-align: justify; padding: 0 20px; line-height: 1.3; margin-bottom: 5px; }
        
        .signature-container { margin-top: 0px; }
        .signature-line { border-top: 1px solid white; width: 150px; margin: 22px auto 3px auto; }
        .signature-text { color: white; font-size: 8px; margin: 1px 0; }

    </style>
</head>
<body>

    <div class="card">
        <div class="front-header">
            {{ mb_strtoupper($iglesia ?? 'AD REY DE REYES') }}
        </div>
        
        <div class="watermark">
            ID: {{ str_pad($miembro->id, 5, '0', STR_PAD_LEFT) }}
        </div>
        
        <div class="front-body">
            <div class="col-left">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img">
                @endif
                <div class="membresia-text">MEMBRESÍA</div>
                @if(isset($fotoBase64) && $fotoBase64)
                    <img src="{{ $fotoBase64 }}" class="photo">
                @endif
            </div>
            
            <div class="col-right">
                <div class="name">{{ $miembro->nombres }}<br>{{ $miembro->apellidos }}</div>
                
                <div class="info-row">
                    <p class="field-label">DPI / IDENTIFICACIÓN</p>
                    <p class="field-value">{{ $miembro->dpi }}</p>
                </div>
                
                <div class="info-row">
                    <p class="field-label">MIEMBRO DESDE</p>
                    <p class="field-value">{{ $miembro->fecha_integracion ? \Carbon\Carbon::parse($miembro->fecha_integracion)->format('d/m/Y') : '---' }}</p>
                </div>
                
                <div class="info-row">
                    <p class="field-label">MINISTERIOS / FUNCIÓN</p>
                    <p class="field-value">
                        {{ $miembro->ministerios->pluck('nombre')->join(', ') ?: 'Miembro General' }}
                        @if($miembro->es_lider) (Líder) @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="qr-container">
            @if(isset($qrBase64) && $qrBase64)
                <img src="{{ $qrBase64 }}" class="qr-img">
            @endif
        </div>

        <div class="front-footer">
            {{ $config->direccion ?? 'Zaculeu Central, Zona 9, Huehuetenango' }}
        </div>
    </div>

    <div class="card back-card">
        @if(isset($logoBase64) && $logoBase64)
            <div class="back-logo-wrapper">
                <img src="{{ $logoBase64 }}" class="back-logo">
            </div>
        @endif
        <div class="back-title">USO OFICIAL DE MEMBRESÍA</div>
        <div class="back-text">
            Este documento es personal e intransferible. Acredita al portador como miembro activo de nuestra congregación bajo los estatutos establecidos. El titular se compromete a portarlo en actividades oficiales y asambleas.
            <br><br>
            <center><i>"Restaurando a la familia"</i></center>
        </div>
        
        <div class="signature-container">
            <!-- Contenedor de altura cero para posicionar firma y sello sin ocupar espacio vertical -->
            <div style="position: relative; width: 150px; height: 0px; margin: 0 auto; z-index: 10;">
                @if(isset($selloBase64) && $selloBase64)
                    <img src="{{ $selloBase64 }}" style="position: absolute; left: -48px; top: -24px; width: 55px; height: auto; opacity: 0.85; z-index: 5;">
                @endif
                
                @if(isset($firmaBase64) && $firmaBase64)
                    <img src="{{ $firmaBase64 }}" style="position: absolute; left: 17px; top: -48px; width: 115px; height: auto; z-index: 10;">
                @endif
            </div>
            
            <div class="signature-line"></div>
            <div class="signature-text">{{ mb_strtoupper($config->pastor_general ?? 'WILMAN RODAS') }}</div>
            <div class="signature-text">Pastor General</div>
            <div class="signature-text" style="font-size: 6.5px; opacity: 0.8; margin-top: 1px; margin-bottom: 2px;">En caso de extravío, por favor reportar a la administración de la iglesia.</div>
        </div>
    </div>

</body>
</html>
