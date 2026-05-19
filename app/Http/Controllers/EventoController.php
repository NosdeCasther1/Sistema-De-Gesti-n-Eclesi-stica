<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function index(Request $request)
    {
        $query = Evento::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $isConnected = $this->googleService->isConnected();

        if ($isConnected) {
            $this->googleService->syncEventsToDatabase();
        }

        $eventos = $query->orderBy('fecha_inicio', 'desc')->paginate(10)->withQueryString();

        return view('eventos.index', compact('eventos', 'isConnected'));
    }

    public function create(Request $request)
    {
        $fechaDefecto = $request->get('fecha', now()->format('Y-m-d\TH:i'));
        $isConnected = $this->googleService->isConnected();
        return view('eventos.create', compact('fechaDefecto', 'isConnected'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:Servicio,Célula,Reunión,Especial',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
            'sincronizar_google' => 'nullable|boolean',
        ]);

        $evento = new Evento($validated);

        if ($request->filled('sincronizar_google') && $this->googleService->isConnected()) {
            $googleEvent = $this->googleService->createEvent($evento);
            if ($googleEvent) {
                $evento->google_calendar_event_id = $googleEvent->getId();
                // Obtener enlace de Google Meet si se generó
                $evento->meet_link = $googleEvent->getHangoutLink();
            }
        }

        $evento->save();

        return redirect()->route('eventos.index')->with('success', 'Evento creado exitosamente.');
    }

    public function edit(Evento $evento)
    {
        $isConnected = $this->googleService->isConnected();
        return view('eventos.edit', compact('evento', 'isConnected'));
    }

    public function update(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:Servicio,Célula,Reunión,Especial',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
            'sincronizar_google' => 'nullable|boolean',
        ]);

        $evento->fill($validated);

        if ($evento->google_calendar_event_id && $this->googleService->isConnected()) {
            $googleEvent = $this->googleService->updateEvent($evento->google_calendar_event_id, $evento);
            if ($googleEvent && !$evento->meet_link) {
                $evento->meet_link = $googleEvent->getHangoutLink();
            }
        } elseif ($request->filled('sincronizar_google') && $this->googleService->isConnected()) {
            $googleEvent = $this->googleService->createEvent($evento);
            if ($googleEvent) {
                $evento->google_calendar_event_id = $googleEvent->getId();
                $evento->meet_link = $googleEvent->getHangoutLink();
            }
        }

        $evento->save();

        return redirect()->route('eventos.index')->with('success', 'Evento actualizado exitosamente.');
    }

    public function destroy(Evento $evento)
    {
        if ($evento->google_calendar_event_id && $this->googleService->isConnected()) {
            $this->googleService->deleteEvent($evento->google_calendar_event_id);
        }

        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento eliminado exitosamente.');
    }

    public function calendarEvents(Request $request)
    {
        if ($this->googleService->isConnected()) {
            $this->googleService->syncEventsToDatabase();
        }

        $eventos = Evento::all()->map(function ($e) {
            $colors = [
                'Servicio' => '#3b82f6', // blue
                'Célula' => '#10b981',   // green
                'Reunión' => '#f59e0b',  // amber
                'Especial' => '#8b5cf6'  // purple
            ];

            return [
                'id' => $e->id,
                'title' => $e->titulo,
                'start' => $e->fecha_inicio->toIso8601String(),
                'end' => $e->fecha_fin ? $e->fecha_fin->toIso8601String() : null,
                'backgroundColor' => $colors[$e->tipo] ?? '#3b82f6',
                'borderColor' => 'transparent',
                'extendedProps' => [
                    'ubicacion' => $e->ubicacion,
                    'tipo' => $e->tipo,
                    'descripcion' => $e->descripcion,
                    'meet_link' => $e->meet_link,
                ]
            ];
        });

        return response()->json($eventos);
    }
}
