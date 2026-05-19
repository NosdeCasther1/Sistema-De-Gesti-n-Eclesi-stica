<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function fetchAccessTokenWithAuthCode($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
    }

    public function isConnected()
    {
        return session()->has('google_calendar_token') && !isset(session('google_calendar_token')['error']);
    }

    protected function setupClient()
    {
        if (session()->has('google_calendar_token') && !isset(session('google_calendar_token')['error'])) {
            $token = session('google_calendar_token');
            $this->client->setAccessToken($token);
            
            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = $this->client->getRefreshToken();
                if ($refreshToken) {
                    try {
                        $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                        session(['google_calendar_token' => $this->client->getAccessToken()]);
                    } catch (\Exception $e) {
                        Log::error("Error refrescando token de Google Calendar: " . $e->getMessage());
                        session()->forget('google_calendar_token');
                        return false;
                    }
                } else {
                    session()->forget('google_calendar_token');
                    return false;
                }
            }
            return true;
        }
        return false;
    }


    public function listCalendars()
    {
        if (!$this->setupClient()) {
            return [];
        }

        $service = new Calendar($this->client);

        try {
            $calendarList = $service->calendarList->listCalendarList();
            session()->forget('google_calendar_error');
            return $calendarList->getItems();
        } catch (\Exception $e) {
            Log::error("Error listando calendarios de Google: " . $e->getMessage());
            session(['google_calendar_error' => $e->getMessage()]);
            return [];
        }
    }

    public function getSelectedCalendarId()
    {
        return session('google_calendar_id', 'primary');
    }

    public function createEvent($eventoData)
    {
        if (!$this->setupClient()) {
            return null;
        }

        $service = new Calendar($this->client);

        $event = new Event([
            'summary' => $eventoData->titulo,
            'location' => $eventoData->ubicacion,
            'description' => $eventoData->descripcion,
            'start' => [
                'dateTime' => $eventoData->fecha_inicio->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => ($eventoData->fecha_fin ?? $eventoData->fecha_inicio->addHour())->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                ]
            ]
        ]);

        $calendarId = $this->getSelectedCalendarId();
        $optParams = ['conferenceDataVersion' => 1];

        try {
            $createdEvent = $service->events->insert($calendarId, $event, $optParams);
            return $createdEvent;
        } catch (\Exception $e) {
            Log::error("Error de Google Calendar al crear evento: " . $e->getMessage());
            return null;
        }
    }

    public function updateEvent($calendarEventId, $eventoData)
    {
        if (!$this->setupClient() || !$calendarEventId) {
            return null;
        }

        $service = new Calendar($this->client);
        $calendarId = $this->getSelectedCalendarId();

        try {
            $event = $service->events->get($calendarId, $calendarEventId);
            $event->setSummary($eventoData->titulo);
            $event->setLocation($eventoData->ubicacion);
            $event->setDescription($eventoData->descripcion);

            $start = new \Google\Service\Calendar\EventDateTime();
            $start->setDateTime($eventoData->fecha_inicio->toRfc3339String());
            $start->setTimeZone(config('app.timezone'));
            $event->setStart($start);

            $end = new \Google\Service\Calendar\EventDateTime();
            $end->setDateTime(($eventoData->fecha_fin ?? $eventoData->fecha_inicio->addHour())->toRfc3339String());
            $end->setTimeZone(config('app.timezone'));
            $event->setEnd($end);

            $updatedEvent = $service->events->update($calendarId, $calendarEventId, $event);
            return $updatedEvent;
        } catch (\Exception $e) {
            Log::error("Error actualizando evento en Google Calendar: " . $e->getMessage());
            return null;
        }
    }

    public function deleteEvent($calendarEventId)
    {
        if (!$this->setupClient() || !$calendarEventId) {
            return false;
        }

        $service = new Calendar($this->client);
        $calendarId = $this->getSelectedCalendarId();

        try {
            $service->events->delete($calendarId, $calendarEventId);
            return true;
        } catch (\Exception $e) {
            Log::error("Error eliminando evento en Google Calendar: " . $e->getMessage());
            return false;
        }
    }

    public function listEvents($calendarId = null, $maxResults = 250)
    {
        if (!$this->setupClient()) {
            return [];
        }

        $calendarId = $calendarId ?: $this->getSelectedCalendarId();
        $service = new Calendar($this->client);

        $optParams = [
            'maxResults' => $maxResults,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => now()->subMonths(6)->toRfc3339String(), // Desde hace 6 meses en adelante
        ];

        try {
            $results = $service->events->listEvents($calendarId, $optParams);
            return $results->getItems();
        } catch (\Exception $e) {
            Log::error("Error listando eventos de Google Calendar: " . $e->getMessage());
            return [];
        }
    }

    public function syncEventsToDatabase()
    {
        // Evitar sincronizar el calendario personal ('primary') automáticamente si el usuario no lo ha elegido explícitamente
        if ($this->getSelectedCalendarId() === 'primary' && !session()->has('google_calendar_id')) {
            return 0;
        }

        $googleEvents = $this->listEvents();

        foreach ($googleEvents as $gEvent) {
            if ($gEvent->getStatus() === 'cancelled') {
                continue;
            }

            $googleId = $gEvent->getId();
            $evento = \App\Models\Evento::where('google_calendar_event_id', $googleId)->first();

            $start = $gEvent->getStart();
            $end = $gEvent->getEnd();

            $fechaInicio = $start->getDateTime() ? \Carbon\Carbon::parse($start->getDateTime()) : \Carbon\Carbon::parse($start->getDate());
            $fechaFin = $end->getDateTime() ? \Carbon\Carbon::parse($end->getDateTime()) : \Carbon\Carbon::parse($end->getDate());

            $titulo = $gEvent->getSummary() ?? 'Evento sin título';
            $tituloLower = mb_strtolower($titulo);
            $tipo = 'Especial';

            if (str_contains($tituloLower, 'servicio') || str_contains($tituloLower, 'culto')) {
                $tipo = 'Servicio';
            } elseif (str_contains($tituloLower, 'célula') || str_contains($tituloLower, 'celula')) {
                $tipo = 'Célula';
            } elseif (str_contains($tituloLower, 'reunión') || str_contains($tituloLower, 'reunion')) {
                $tipo = 'Reunión';
            }

            if (!$evento) {
                \App\Models\Evento::create([
                    'titulo' => $titulo,
                    'descripcion' => $gEvent->getDescription(),
                    'ubicacion' => $gEvent->getLocation() ?? 'Templo Principal',
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'tipo' => $tipo,
                    'google_calendar_event_id' => $googleId,
                    'meet_link' => $gEvent->getHangoutLink(),
                ]);
            } else {
                $evento->update([
                    'titulo' => $titulo,
                    'descripcion' => $gEvent->getDescription() ?? $evento->descripcion,
                    'ubicacion' => $gEvent->getLocation() ?? $evento->ubicacion,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'meet_link' => $gEvent->getHangoutLink() ?? $evento->meet_link,
                ]);
            }
        }

        return count($googleEvents);
    }
}
