<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Session;

class GoogleCalendarController extends Controller
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function connect()
    {
        if (empty(env('GOOGLE_CLIENT_ID')) || empty(env('GOOGLE_CLIENT_SECRET'))) {
            return redirect()->route('configuracion.index')->with('error', '⚠️ Faltan las credenciales de Google Calendar (GOOGLE_CLIENT_ID y GOOGLE_CLIENT_SECRET) en el archivo .env. Por favor, configúralas en tu proyecto antes de vincular la cuenta.');
        }

        return redirect()->away($this->googleService->getAuthUrl());
    }

    public function callback(Request $request)
    {
        session(['active_tab' => 'integraciones']);

        if ($request->has('code')) {
            $token = $this->googleService->fetchAccessTokenWithAuthCode($request->get('code'));
            
            if (isset($token['error'])) {
                return redirect()->route('configuracion.index')->with('error', 'Error al autenticar con Google: ' . ($token['error_description'] ?? $token['error']));
            }

            Session::put('google_calendar_token', $token);

            return redirect()->route('configuracion.index')->with('success', 'Google Calendar conectado exitosamente.');
        }

        return redirect()->route('configuracion.index')->with('error', 'Error al conectar con Google Calendar.');
    }

    public function disconnect()
    {
        session(['active_tab' => 'integraciones']);
        Session::forget('google_calendar_token');
        Session::forget('google_calendar_id');
        return redirect()->route('configuracion.index')->with('success', 'Google Calendar desconectado exitosamente.');
    }

    public function selectCalendar(Request $request)
    {
        session(['active_tab' => 'integraciones']);
        $request->validate([
            'calendar_id' => 'required|string'
        ]);

        Session::put('google_calendar_id', $request->calendar_id);

        // Limpiar eventos de Google Calendar anteriores para evitar mezcla de calendarios (ej. cumpleaños del calendario personal)
        \App\Models\Evento::whereNotNull('google_calendar_event_id')->delete();

        // Sincronizar eventos del nuevo calendario seleccionado
        $this->googleService->syncEventsToDatabase();

        return redirect()->route('configuracion.index')->with('success', 'Calendario seleccionado exitosamente. Eventos sincronizados limpios de otras agendas.');
    }

    public function sync()
    {
        if ($this->googleService->isConnected()) {
            $count = $this->googleService->syncEventsToDatabase();
            return redirect()->back()->with('success', "Eventos de Google Calendar sincronizados exitosamente ($count eventos procesados).");
        }
        return redirect()->back()->with('error', 'Google Calendar no está conectado.');
    }
}

