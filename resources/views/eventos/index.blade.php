@extends('layouts.app')

@php
    try {
        $config = \App\Models\Configuracion::first() ?? new \App\Models\Configuracion([
            'nombre_iglesia' => 'AD REY DE REYES',
            'pastor_general' => 'Pastor Principal',
            'direccion' => 'Ciudad Guatemala',
            'telefono' => '+502 0000 0000',
            'email' => 'contacto@iglesia.com'
        ]);
    } catch (\Exception $e) {
        $config = (object)[
            'nombre_iglesia' => 'AD REY DE REYES',
            'pastor_general' => 'Pastor Principal',
            'direccion' => 'Ciudad Guatemala',
            'telefono' => '+502 0000 0000',
            'email' => 'contacto@iglesia.com',
            'logo' => null
        ];
    }
@endphp

@section('title', 'Gestión de Eventos y Calendario - AD Rey de Reyes')

@push('styles')
<style>
    /* Forzar arquitectura Bento estricta */
    body { overflow: hidden !important; }
    .main-content { height: 100vh !important; max-height: 100vh !important; overflow: hidden !important; }
    main { overflow: hidden !important; display: flex !important; flex-direction: column !important; padding-bottom: 0 !important; }
    .bento-container { display: flex; flex-direction: column; flex-grow: 1; min-height: 0; }
    
    [x-cloak] { display: none !important; }
    .hidden-important { display: none !important; }

    /* FullCalendar Premium Customization */
    #calendar {
        font-family: 'Outfit', 'Inter', sans-serif;
        color: var(--text-color, #1e293b);
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: var(--text-color, #1e293b);
        text-transform: capitalize;
    }

    .fc .fc-button-primary {
        background: linear-gradient(135deg, #3b82f6, #6366f1) !important;
        border: none !important;
        border-radius: 0.5rem !important;
        font-weight: 600 !important;
        padding: 0.5rem 1rem !important;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2) !important;
        text-transform: capitalize !important;
        transition: all 0.2s ease;
    }

    .fc .fc-button-primary:hover {
        background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
    }

    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: linear-gradient(135deg, #1d4ed8, #4338ca) !important;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2) !important;
    }

    /* Ajustar grupos de botones en FullCalendar */
    .fc .fc-button-group .fc-button {
        margin: 0 !important;
    }

    /* ==========================================================================
       DISEÑO MOBILE FIRST PREMIUM PARA FULLCALENDAR (MÓVILES)
       ========================================================================== */
    @media (max-width: 767.98px) {
        .fc .fc-toolbar.fc-header-toolbar {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.25rem !important;
            align-items: stretch !important;
            margin-bottom: 1.5rem !important;
        }
        .fc .fc-toolbar-chunk {
            display: flex !important;
            justify-content: center !important;
            width: 100% !important;
        }
        /* Fila 1: Botones Prev, Next y Hoy */
        .fc .fc-toolbar-chunk:first-child {
            flex-direction: row !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .fc .fc-toolbar-chunk:first-child .fc-button-group {
            display: inline-flex !important;
        }
        .fc .fc-toolbar-chunk:first-child .fc-today-button,
        .fc .fc-toolbar-chunk:first-child .fc-miHoy-button {
            margin-left: 0.5rem !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1.25rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        /* Fila 2: Título del Mes */
        .fc .fc-toolbar-title {
            font-size: 1.4rem !important;
            text-align: center !important;
            line-height: 1.2 !important;
        }
        /* Fila 3: Vistas (Mes, Semana, Agenda) */
        .fc .fc-toolbar-chunk:last-child {
            display: flex !important;
            width: 100% !important;
        }
        .fc .fc-toolbar-chunk:last-child .fc-button-group {
            display: flex !important;
            width: 100% !important;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1) !important;
        }
        .fc .fc-toolbar-chunk:last-child .fc-button {
            flex: 1 1 0% !important;
            padding: 0.6rem 0.25rem !important;
            font-size: 0.8rem !important;
            white-space: nowrap !important;
            text-align: center !important;
        }
        /* Garantizar celdas proporcionales y no colapsadas en móviles */
        .fc .fc-daygrid-day-frame {
            min-height: 55px !important;
        }
        .fc .fc-daygrid-day-number {
            font-size: 0.75rem !important;
            padding: 4px 6px !important;
        }
        .fc .fc-col-header-cell-cushion {
            font-size: 0.7rem !important;
        }
        .fc-event {
            font-size: 0.7rem !important;
            padding: 1px 2px !important;
            margin-bottom: 1.5px !important;
        }
    }

    .fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid {
        border-color: var(--border-color, #e2e8f0) !important;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background-color: rgba(59, 130, 246, 0.05) !important;
    }

    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 4px;
        font-weight: bold;
    }

    .fc-event {
        border: none !important;
        border-radius: 6px !important;
        padding: 3px 6px !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        margin-bottom: 3px !important;
        transition: transform 0.15s ease;
    }

    .fc-event:hover {
        transform: scale(1.02);
        filter: brightness(1.1);
    }

    .fc-daygrid-event-dot {
        display: none !important;
    }

    /* ==========================================================================
       DISEÑO PREMIUM PARA MODO OSCURO (DARK MODE)
       ========================================================================== */
    .dark .fc {
        --fc-border-color: #334155 !important;
        --fc-page-bg-color: #1e293b !important;
        --fc-neutral-bg-color: #0f172a !important;
        --fc-neutral-text-color: #f8fafc !important;
        --fc-today-bg-color: rgba(59, 130, 246, 0.15) !important;
    }

    .dark .fc .fc-toolbar-title {
        color: #f8fafc !important;
    }

    /* Cabecera de días (Lunes, Martes...) en Modo Oscuro */
    .dark .fc .fc-col-header-cell {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        padding: 0.75rem 0 !important;
    }
    .dark .fc .fc-col-header-cell-cushion {
        color: #94a3b8 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.8rem !important;
        letter-spacing: 0.05em !important;
    }

    /* Celdas del calendario en Modo Oscuro */
    .dark .fc .fc-daygrid-day {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }
    .dark .fc .fc-day-other {
        background-color: #0f172a !important;
        opacity: 0.6 !important;
    }
    .dark .fc .fc-daygrid-day-number {
        color: #e2e8f0 !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        padding: 0.5rem 0.75rem !important;
    }
    .dark .fc .fc-day-other .fc-daygrid-day-number {
        color: #64748b !important;
    }

    /* Día actual en Modo Oscuro */
    .dark .fc .fc-daygrid-day.fc-day-today {
        background-color: rgba(59, 130, 246, 0.12) !important;
        border-top: 2px solid #3b82f6 !important;
        box-shadow: inset 0 0 20px rgba(59, 130, 246, 0.05) !important;
    }
    .dark .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, #3b82f6, #6366f1) !important;
        color: #ffffff !important;
        border-radius: 0.5rem !important;
        padding: 0.25rem 0.6rem !important;
        margin: 0.3rem !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4) !important;
    }

    /* Estilos de Eventos en Modo Oscuro (Solo para vista de calendario/grilla, excluyendo vista lista) */
    .dark .fc-event:not(.fc-list-event) {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3) !important;
        border-radius: 0.5rem !important;
        padding: 0.35rem 0.6rem !important;
        font-weight: 600 !important;
        background-color: var(--fc-event-bg-color-alpha, rgba(255, 255, 255, 0.06)) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-left: 4px solid var(--fc-event-border-color, #3b82f6) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        backdrop-filter: blur(8px) !important;
    }
    .dark .fc-event:not(.fc-list-event):hover {
        transform: translateY(-2px) scale(1.01) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -4px rgba(0, 0, 0, 0.4) !important;
        filter: brightness(1.15) !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
    .dark .fc-event:not(.fc-list-event) .fc-event-main,
    .dark .fc-event:not(.fc-list-event) .fc-event-title,
    .dark .fc-event:not(.fc-list-event) .fc-event-time {
        color: #f8fafc !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5) !important;
        letter-spacing: 0.01em !important;
    }
    /* Eventos de bloque (multi-día o todo el día) en Modo Oscuro */
    .dark .fc-daygrid-block-event:not(.fc-list-event),
    .dark .fc-h-event:not(.fc-list-event) {
        background-color: var(--fc-event-bg-color, #3b82f6) !important;
        border-left: none !important;
        background-image: linear-gradient(rgba(255, 255, 255, 0.15), rgba(0, 0, 0, 0.1)) !important;
    }
    .dark .fc-daygrid-block-event:not(.fc-list-event) .fc-event-main,
    .dark .fc-daygrid-block-event:not(.fc-list-event) .fc-event-title,
    .dark .fc-h-event:not(.fc-list-event) .fc-event-main,
    .dark .fc-h-event:not(.fc-list-event) .fc-event-title {
        color: #ffffff !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4) !important;
        font-weight: 700 !important;
    }

    /* ==========================================================================
       DISEÑO PREMIUM PARA VISTA AGENDA / LISTA (fc-list) EN MODO OSCURO
       ========================================================================== */
    .dark .fc .fc-list {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        border-radius: 1rem !important;
        overflow: hidden !important;
    }
    /* Cabecera de día en vista lista (ej. 1 de mayo de 2026) */
    .dark .fc .fc-list-day-cushion {
        background-color: #0f172a !important;
        color: #94a3b8 !important;
        font-weight: 700 !important;
        padding: 0.75rem 1.25rem !important;
        border-bottom: 1px solid #334155 !important;
        border-top: 1px solid #334155 !important;
    }
    /* Filas de eventos en vista lista */
    .dark .fc .fc-list-event td {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        padding: 0.8rem 1.25rem !important;
        color: #e2e8f0 !important;
        transition: background-color 0.15s ease !important;
    }
    /* Punto de color en vista lista */
    .dark .fc .fc-list-event-dot {
        border-color: var(--fc-event-border-color, #3b82f6) !important;
        box-shadow: 0 0 10px var(--fc-event-border-color, #3b82f6) !important;
    }
    /* Efecto Hover corregido y premium en vista lista (¡Sin destello blanco!) */
    .dark .fc .fc-list-event:hover td {
        background-color: rgba(59, 130, 246, 0.12) !important;
        color: #ffffff !important;
    }

    /* Botones de la barra de herramientas en Modo Oscuro - Estilo Premium Apple/Stripe */
    .dark .fc .fc-button-primary {
        background: rgba(15, 23, 42, 0.75) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #cbd5e1 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2) !important;
        backdrop-filter: blur(12px) !important;
        border-radius: 0.6rem !important;
        transition: all 0.2s ease !important;
    }
    .dark .fc .fc-button-primary:hover {
        background: rgba(30, 41, 59, 0.9) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
        transform: translateY(-1px) !important;
    }
    .dark .fc .fc-button-primary:not(:disabled):active,
    .dark .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: linear-gradient(135deg, #3b82f6, #6366f1) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    .dark .fc .fc-button-primary:disabled {
        background: rgba(15, 23, 42, 0.4) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
        color: #475569 !important;
    }

    /* Contenedor Principal del Calendario en Modo Oscuro */
    .dark .card-module {
        background: linear-gradient(145deg, #1e293b, #0f172a) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
        border-radius: 1.25rem !important;
    }

    /* Botón de Impresión con Micro-Animaciones Premium */
    .btn-print-premium {
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
    }
    .btn-print-premium:hover {
        transform: translateY(-2px) scale(1.04) !important;
        border-color: #3b82f6 !important;
        color: #3b82f6 !important;
        box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.15) !important;
    }
    .dark .btn-print-premium:hover {
        border-color: #60a5fa !important;
        color: #60a5fa !important;
        box-shadow: 0 10px 20px -5px rgba(96, 165, 250, 0.25) !important;
    }
    .btn-print-premium:hover i {
        animation: printIconPulse 1.2s infinite alternate ease-in-out;
    }
    .btn-print-premium:active {
        transform: translateY(1px) scale(0.96) !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
    }

    @keyframes printIconPulse {
        0% { transform: translateY(0) rotate(-10deg); }
        100% { transform: translateY(-3px) rotate(10deg); }
    }

    /* Ocultar cabecera corporativa de impresión en pantalla normal */
    .print-header {
        display: none !important;
    }




    /* --- 1. ESTILOS PREMIUM PARA FULLCALENDAR (MODO CLARO) --- */
    /* Estilo base para todos los eventos */
    .fc-event:not(.fc-list-event) {
        border: 1px solid rgba(0, 0, 0, 0.02) !important;
        border-radius: 6px !important;
        padding: 3px 6px !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        margin-bottom: 3px !important;
        transition: transform 0.15s ease;
    }

    /* 1.1 EVENTOS TIPO "DOT" o TIMED (Eventos de un solo día con hora específica) */
    .fc-daygrid-dot-event:not(.fc-list-event),
    .fc-event:not(.fc-daygrid-block-event):not(.fc-h-event):not(.fc-list-event) {
        background-color: var(--fc-event-bg-color-alpha, rgba(79, 70, 229, 0.1)) !important;
        border-left: 4px solid var(--fc-event-border-color, #4f46e5) !important;
    }
    
    .fc-daygrid-dot-event:not(.fc-list-event) .fc-event-main,
    .fc-daygrid-dot-event:not(.fc-list-event) .fc-event-title,
    .fc-daygrid-dot-event:not(.fc-list-event) .fc-event-time,
    .fc-event:not(.fc-daygrid-block-event):not(.fc-h-event):not(.fc-list-event) .fc-event-main,
    .fc-event:not(.fc-daygrid-block-event):not(.fc-h-event):not(.fc-list-event) .fc-event-title,
    .fc-event:not(.fc-daygrid-block-event):not(.fc-h-event):not(.fc-list-event) .fc-event-time {
        color: var(--fc-event-text-color, #4338ca) !important;
        font-weight: 700 !important;
    }

    /* 1.2 EVENTOS DE BLOQUE (Eventos todo el día o multi-día) */
    .fc-daygrid-block-event:not(.fc-list-event),
    .fc-h-event:not(.fc-list-event) {
        background-color: var(--fc-event-bg-color, #4f46e5) !important;
        border-left: none !important;
        background-image: linear-gradient(rgba(255, 255, 255, 0.15), rgba(0, 0, 0, 0.05)) !important;
    }

    .fc-daygrid-block-event:not(.fc-list-event) .fc-event-main,
    .fc-daygrid-block-event:not(.fc-list-event) .fc-event-title,
    .fc-daygrid-block-event:not(.fc-list-event) .fc-event-time,
    .fc-h-event:not(.fc-list-event) .fc-event-main,
    .fc-h-event:not(.fc-list-event) .fc-event-title,
    .fc-h-event:not(.fc-list-event) .fc-event-time {
        color: #ffffff !important;
        font-weight: 700 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2) !important;
    }
    /* Quitar bordes pesados del calendario */
    .fc-theme-standard td, .fc-theme-standard th { border-color: rgba(148, 163, 184, 0.2) !important; }
    .dark .fc-theme-standard td, .dark .fc-theme-standard th { border-color: rgba(51, 65, 85, 0.5) !important; }

    /* ==========================================================================
       ESTILOS DE IMPRESIÓN DE ALTA FIDELIDAD (COMPLETO Y EN UNA SOLA PÁGINA)
       ========================================================================== */
    @media print {
        @page { 
            size: landscape; 
            margin: 0.4cm; 
        }
        
        /* 1. LIBERACIÓN ABSOLUTA DE CONTENEDORES Y ALTURAS FIJAS */
        html, body, #app, .main-content, main, .bento-container, .card-module, #calendar, .fc {
            background: white !important;
            color: black !important;
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
            display: block !important;
        }

        /* Optimización de escala para asegurar que todo quepa en una sola página landscape */
        .bento-container {
            zoom: 0.78 !important;
        }
        /* Fallback para Firefox usando scale */
        @-moz-document url-prefix() {
            .bento-container {
                transform: scale(0.78);
                transform-origin: top left;
                width: 128% !important;
            }
        }

        /* Evitar saltos de página dentro del calendario */
        #calendar, .fc, .fc-scrollgrid {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* 2. OCULTAR ELEMENTOS INNECESARIOS (BARRA LATERAL, NAVEGACIÓN, BOTONES) */
        aside, #sidebar, .sidebar, header, .header, footer, button, .btn, .no-print, .print-hidden,
        .fc-header-toolbar, .fc-toolbar, .fc-toolbar-chunk, .fc-button-group {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .main-content > main {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* 3. MOSTRAR CABECERA CORPORATIVA CON ANCHO COMPLETO Y MÁS COMPACTA */
        .print-header {
            display: block !important;
            width: 100% !important;
            border-bottom: 2px solid #cbd5e1 !important;
            padding-bottom: 6px !important;
            margin-bottom: 10px !important;
        }

        .print-header-flex {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .print-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .print-logo-container {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            height: 42px !important;
            width: 42px !important;
        }

        .print-logo-img {
            max-height: 36px !important;
            max-width: 36px !important;
            object-fit: contain !important;
        }

        .print-church-title {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
            line-height: 1.2 !important;
        }

        .print-church-subtitle {
            font-size: 8px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            margin: 1px 0 0 0 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        .print-info-right {
            text-align: right !important;
            font-size: 8px !important;
            color: #334155 !important;
            line-height: 1.3 !important;
        }

        /* 4. PREVENIR SCROLLS Y FORZAR AJUSTE DE ALTURA EN FULLCALENDAR */
        .fc-scroller, .fc-scroller-harness {
            overflow: visible !important;
            height: auto !important;
        }

        .fc-view-harness, .fc-view-harness-active {
            height: auto !important;
            min-height: 0 !important;
        }

        .fc-scrollgrid {
            height: auto !important;
        }

        .fc-daygrid-body, .fc-scrollgrid-sync-table {
            height: auto !important;
        }

        /* Anular las alturas de las filas fijas inyectadas por JS sin romper el posicionamiento interno de celdas */
        .fc-daygrid-row,
        .fc-scrollgrid-sync-table tr {
            height: auto !important;
        }

        .fc-daygrid-day-frame {
            height: 100% !important; /* Permitir que ocupe todo el espacio de la fila */
            min-height: 48px !important; /* Altura reducida a 48px para forzar una sola página de impresión */
        }

        /* Ajustar ancho de tablas del calendario al 100% */
        .fc-view-harness, 
        .fc-view, 
        .fc-daygrid, 
        .fc-scrollgrid, 
        .fc-scrollgrid-sync-table, 
        .fc-col-header, 
        .fc-daygrid-body table, 
        .fc-scrollgrid-sync-table table,
        .fc-scrollgrid-section-body table,
        .fc-scrollgrid-section-header table {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
            table-layout: fixed !important;
        }

        .fc-scrollgrid {
            border-collapse: collapse !important;
        }

        /* 5. DISEÑO DE CELDAS Y EVENTOS PARA IMPRESIÓN */
        .fc {
            color: #000000 !important;
        }

        .fc-col-header-cell {
            padding: 3px 0 !important;
            background-color: #f8fafc !important;
        }

        .fc-col-header-cell-cushion, 
        .fc-daygrid-day-number {
            color: #000000 !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            font-size: 10px !important;
        }

        .fc-daygrid-day-number {
            padding: 2px 4px !important;
        }

        /* Optimizar eventos en la impresión (usar bordes negros/grises y texto negro sobre fondo blanco/gris claro) */
        .fc-event {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-left: 3px solid var(--fc-event-border-color, #4f46e5) !important; /* Mantener color de categoría como indicador visual */
            color: #0f172a !important;
            padding: 1px 3px !important;
            margin-bottom: 1px !important;
            page-break-inside: avoid !important;
            border-radius: 3px !important;
        }

        .fc-event .fc-event-main,
        .fc-event .fc-event-title,
        .fc-event .fc-event-time {
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 9px !important;
            line-height: 1.1 !important;
            white-space: normal !important; /* Permitir saltos de línea para que los títulos se lean completos */
        }

        /* Para eventos de bloque (multi-día) en la impresión, usar fondo un poco más oscuro o gris suave */
        .fc-daygrid-block-event,
        .fc-h-event {
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            border-left: 3px solid var(--fc-event-border-color, #4f46e5) !important;
        }
    }
</style>
@endpush

@section('header_title', 'Calendario de Eventos')
@section('header_subtitle', 'Planifica y sincroniza las actividades eclesiásticas y reuniones de célula')
@section('header_icon')
<i class="fas fa-calendar-alt fs-5"></i>
@endsection

@section('content')
<div class="bento-container" x-data="{ viewMode: 'calendar' }">
    
    <!-- ===== CABECERA CORPORATIVA DE IMPRESIÓN (Solo visible al imprimir) ===== -->
    <div class="print-header">
        <div class="print-header-flex">
            <div class="print-header-left">
                @if($config->logo)
                    <div class="print-logo-container">
                        <img src="{{ asset('storage/config/' . $config->logo) }}" alt="Logo" class="print-logo-img">
                    </div>
                @else
                    <div class="print-logo-container">
                        <i class="fas fa-church text-slate-700 text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h2 class="print-church-title">{{ $config->nombre_iglesia }}</h2>
                    <p class="print-church-subtitle">Calendario Oficial de Actividades Eclesiásticas</p>
                </div>
            </div>
            <div class="print-info-right">
                <div><strong>Pastor General:</strong> {{ $config->pastor_general }}</div>
                <div><strong>Dirección:</strong> {{ $config->direccion }}</div>
                <div><strong>Teléfono:</strong> {{ $config->telefono }}</div>
                <div><strong>Correo Electrónico:</strong> {{ $config->email }}</div>
            </div>
        </div>
    </div>
    <!-- ===== ENCABEZADO Y BOTONES DE VISTA ===== -->
    <div class="flex justify-between items-center mb-4 flex-wrap gap-3 flex-shrink-0 no-print print-hidden mt-3 sm:mt-0">
        <!-- ===== CONTROL RÁPIDO DE GOOGLE CALENDAR ===== -->
        <div class="flex items-center">
            @if($isConnected)
                {{-- Botón de Sincronización Manual --}}
                <form action="{{ route('google.calendar.sync') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm" title="Sincronizar eventos con Google Calendar">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Sincronizar Calendar
                    </button>
                </form>
            @else
                {{-- Botón de Conexión Rápida --}}
                <a href="{{ route('google.calendar.connect') }}" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-red-200 dark:border-red-900/50 rounded-xl text-sm font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors shadow-sm" title="Vincular cuenta de Google Calendar">
                    <i class="fab fa-google text-red-500"></i>
                    Vincular Google Calendar
                </a>
            @endif
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'" class="px-4 py-2 rounded-full font-bold flex items-center gap-2 transition-all border border-slate-200 dark:border-slate-700">
                <i class="fas fa-list-ul"></i> <span>Vista Lista</span>
            </button>
            <button @click="viewMode = 'calendar'; setTimeout(() => window.calendarInstance && window.calendarInstance.updateSize(), 50)" :class="viewMode === 'calendar' ? 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'" class="px-4 py-2 rounded-full font-bold flex items-center gap-2 transition-all border border-slate-200 dark:border-slate-700">
                <i class="fas fa-calendar-grid-58"></i> <span>Vista Calendario</span>
            </button>
            <a href="{{ route('eventos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full font-bold shadow-sm flex items-center gap-2 transition-colors">
                <i class="fas fa-plus-circle"></i> <span>Nuevo Evento</span>
            </a>
        </div>
    </div>

    <!-- BUSCADOR Y FILTROS (Solo visible en modo lista) -->
    <div :class="viewMode === 'list' ? 'card-module p-4 mb-4 shadow-sm flex-shrink-0 print-hidden' : 'hidden-important'">
        <form action="{{ route('eventos.index') }}" method="GET" id="searchForm">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-8 relative">
                    <label class="block text-sm mb-1.5 font-bold text-slate-700 dark:text-slate-300">Búsqueda de Eventos</label>
                    <div class="relative flex items-center w-full">
                        <span class="absolute left-3 text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" id="searchInput" class="w-full pl-10 pr-10 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" value="{{ request('search') }}" placeholder="Buscar por título, descripción o ubicación...">
                        @if(request('search') || request('tipo'))
                            <a href="{{ route('eventos.index') }}" class="absolute right-3 text-slate-400 hover:text-slate-600 clear-search" title="Limpiar filtros">
                                <i class="fas fa-times-circle text-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="md:col-span-4 relative">
                    <label class="block text-sm mb-1.5 font-bold text-slate-700 dark:text-slate-300">Filtrar por Tipo</label>
                    <div class="flex gap-2">
                        <select name="tipo" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" onchange="this.form.submit()">
                            <option value="">Todos los tipos</option>
                            <option value="Servicio" {{ request('tipo') === 'Servicio' ? 'selected' : '' }}>Servicio / Culto</option>
                            <option value="Célula" {{ request('tipo') === 'Célula' ? 'selected' : '' }}>Reunión de Célula</option>
                            <option value="Reunión" {{ request('tipo') === 'Reunión' ? 'selected' : '' }}>Reunión Ministerial</option>
                            <option value="Especial" {{ request('tipo') === 'Especial' ? 'selected' : '' }}>Evento Especial</option>
                        </select>
                        <noscript><button type="submit" class="bg-blue-600 text-white rounded-lg px-4"><i class="fas fa-search"></i></button></noscript>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ===== CONTENIDO VISTA LISTA ===== -->
    <div :class="viewMode === 'list' ? 'card-module p-0 overflow-hidden shadow-sm flex flex-col flex-grow mb-4 min-h-0' : 'hidden-important'">
        <div class="overflow-x-auto overflow-y-auto flex-grow min-h-0">
            <table class="w-full text-left border-separate border-spacing-0" style="min-width:800px;">
                <thead>
                    <tr>
                        <th class="pl-4 py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="width: 35%; position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Detalles del Evento</th>
                        <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="width: 20%; position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Horario</th>
                        <th class="py-3 text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="width: 20%; position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Ubicación / Meet</th>
                        <th class="py-3 text-center text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="width: 15%; position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Tipo</th>
                        <th class="pr-4 py-3 text-right text-slate-700 dark:text-slate-200 font-bold bg-white dark:bg-slate-800" style="width: 10%; position: sticky; top: 0; z-index: 1020; box-shadow: inset 0 -1px 0 var(--border-color), 0 2px 4px rgba(0,0,0,0.05); background-clip: padding-box;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse($eventos as $evento)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-200 dark:border-slate-800/50 last:border-0">
                            <td class="pl-4 py-3">
                                <div class="flex items-center gap-3">
                                    @php
                                        $bgColors = [
                                            'Servicio' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400',
                                            'Célula' => 'bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400',
                                            'Reunión' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400',
                                            'Especial' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400'
                                        ];
                                        $icons = [
                                            'Servicio' => 'fas fa-church',
                                            'Célula' => 'fas fa-network-wired',
                                            'Reunión' => 'fas fa-users',
                                            'Especial' => 'fas fa-star'
                                        ];
                                        $colorClass = $bgColors[$evento->tipo] ?? 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400';
                                        $iconClass = $icons[$evento->tipo] ?? 'fas fa-calendar-alt';
                                    @endphp

                                    <div class="h-12 w-12 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center shrink-0 shadow-inner group-hover:border-indigo-300 dark:group-hover:border-indigo-600 transition-colors">
                                        <span class="text-[8px] font-black uppercase tracking-widest text-rose-500 dark:text-rose-400">
                                            {{ \Carbon\Carbon::parse($evento->fecha_inicio ?? $evento->fecha)->translatedFormat('M') }}
                                        </span>
                                        <span class="text-lg font-black text-slate-800 dark:text-white leading-none mt-0.5">
                                            {{ \Carbon\Carbon::parse($evento->fecha_inicio ?? $evento->fecha)->format('d') }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white mb-0.5">{{ $evento->titulo }}</div>
                                        <div class="text-slate-500 dark:text-slate-400 text-xs line-clamp-1">{{ $evento->descripcion ?: 'Sin descripción adicional' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="font-semibold text-slate-800 dark:text-white text-sm flex items-center gap-1.5 mb-1">
                                    <i class="far fa-calendar-alt text-blue-500 opacity-80"></i> {{ $evento->fecha_inicio->format('d/m/Y') }}
                                </div>
                                <div class="text-slate-500 dark:text-slate-400 text-xs flex items-center gap-1.5">
                                    <i class="far fa-clock opacity-70"></i> {{ $evento->fecha_inicio->format('h:i A') }} 
                                    @if($evento->fecha_fin)
                                        - {{ $evento->fecha_fin->format('h:i A') }}
                                    @endif
                                </div>
                            </td>
                            <td class="py-3">
                                @if($evento->ubicacion)
                                    <div class="text-slate-800 dark:text-white text-sm font-medium mb-1.5 flex items-center gap-1.5">
                                        <i class="fas fa-map-marker-alt text-red-500 opacity-80"></i> {{ $evento->ubicacion }}
                                    </div>
                                @else
                                    <div class="text-slate-500 dark:text-slate-400 text-xs mb-1.5 flex items-center gap-1.5">
                                        <i class="fas fa-map-marker-alt opacity-50"></i> Iglesia Principal
                                    </div>
                                @endif

                                @if($evento->meet_link)
                                    <a href="{{ $evento->meet_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold transition-colors hover:bg-emerald-200 dark:hover:bg-emerald-900/50">
                                        <i class="fas fa-video"></i> <span>Google Meet</span>
                                    </a>
                                @elseif($evento->google_calendar_event_id)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold">
                                        <i class="fab fa-google"></i> <span>En Google Calendar</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                                    {{ $evento->tipo }}
                                </span>
                            </td>
                            <td class="pr-4 py-3 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('eventos.edit', $evento->id) }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-500 dark:hover:border-blue-500 transition-all shadow-sm" title="Editar Evento">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('eventos.destroy', $evento->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:border-red-500 dark:hover:border-red-500 transition-all shadow-sm" title="Eliminar Evento">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10">
                                <div class="text-slate-500 dark:text-slate-400 flex flex-col items-center justify-center">
                                    <i class="fas fa-calendar-times text-4xl mb-3 opacity-30"></i>
                                    <h6 class="font-bold text-slate-700 dark:text-slate-300 mb-1 text-lg">No se encontraron eventos programados</h6>
                                    <p class="text-sm mb-4">Utiliza el botón "Nuevo Evento" para programar la primera actividad.</p>
                                    <a href="{{ route('eventos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full font-bold shadow-sm transition-colors inline-flex items-center gap-2">
                                        <i class="fas fa-plus-circle"></i> Crear Evento
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($eventos->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900/50">
                <div class="w-full">
                    {{ $eventos->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- ===== CONTENIDO VISTA CALENDARIO ===== -->
    <div :class="viewMode === 'calendar' ? 'card-module p-4 shadow-sm flex flex-col flex-grow mb-4 min-h-0' : 'hidden-important'">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-3 border-b border-slate-200 dark:border-slate-800 pb-3 flex-shrink-0 no-print print-hidden">
            <div>
                <h5 class="font-bold text-slate-800 dark:text-white mb-1"><i class="fas fa-calendar-alt text-blue-500 mr-2"></i> Vista Mensual Interactiva</h5>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-0">Haz clic en cualquier día para agendar un evento en esa fecha</p>
            </div>
            <button onclick="window.print()" class="btn-print-premium bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-full font-bold shadow-sm flex items-center gap-2 print-hidden">
                <i class="fas fa-print"></i> <span>Imprimir Calendario</span>
            </button>
        </div>

        <div id="calendar" class="flex-grow overflow-auto"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/es.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var isMobile = window.innerWidth < 768;
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            firstDay: 0, // 0 = Domingo
            initialView: 'dayGridMonth',
            height: isMobile ? 'auto' : 680,
            aspectRatio: isMobile ? 0.85 : 1.8,
            customButtons: {
                miHoy: {
                    text: 'Hoy',
                    click: function() {
                        calendar.today();
                        var todayEl = document.querySelector('.fc-day-today');
                        if (todayEl) {
                            todayEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            todayEl.style.transition = 'all 0.5s ease';
                            todayEl.style.backgroundColor = 'rgba(59, 130, 246, 0.3)';
                            setTimeout(function() {
                                todayEl.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
                            }, 1000);
                        }
                    }
                }
            },
            headerToolbar: {
                left: 'prev,next miHoy',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                month: 'Mes',
                week: 'Semana',
                list: 'Agenda'
            },
            events: '{{ route('eventos.calendar') }}',
            dateClick: function (info) {
                window.location.href = '{{ route('eventos.create') }}?fecha=' + info.dateStr + 'T09:00';
            },
            eventClick: function (info) {
                window.location.href = '/eventos/' + info.event.id + '/edit';
            },
            eventDidMount: function (info) {
                var tooltipText = info.event.title;
                if (info.event.extendedProps.ubicacion) {
                    tooltipText += ' - Lugar: ' + info.event.extendedProps.ubicacion;
                }
                info.el.title = tooltipText;

                // Inyectar colores dinámicos premium para Modo Oscuro y Claro
                if (info.event.backgroundColor) {
                    var baseColor = info.event.backgroundColor.toLowerCase();
                    info.el.style.setProperty('--fc-event-border-color', baseColor, 'important');
                    info.el.style.setProperty('--fc-event-bg-color', baseColor, 'important');
                    info.el.style.setProperty('--fc-event-bg-color-alpha', baseColor + '1e', 'important'); // ~12% opacity (1e in hex is 30/255)
                    
                    var textColors = {
                        '#3b82f6': '#1d4ed8', // Servicio (Azul oscuro)
                        '#10b981': '#065f46', // Célula (Verde oscuro)
                        '#f59e0b': '#92400e', // Reunión (Miel/marrón)
                        '#8b5cf6': '#5b21b6'  // Especial (Púrpura oscuro)
                    };
                    var textColor = textColors[baseColor] || '#1e293b';
                    info.el.style.setProperty('--fc-event-text-color', textColor, 'important');
                }
            }
        });

        calendar.render();
        window.calendarInstance = calendar;
    });
</script>
@endpush
