<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleccion;
use App\Models\Candidato;
use App\Models\Miembro;
use Illuminate\Support\Facades\DB;

class PortalVotanteController extends Controller
{
    /**
     * Validaciones de elegibilidad de un miembro para votar.
     * Devuelve null si es elegible, o un string con el motivo si no.
     */
    private function validarElegibilidad(Miembro $miembro, Eleccion $eleccion): ?string
    {
        // 1. Miembro activo
        if (!$miembro->estado) {
            return "El miembro \"{$miembro->nombres} {$miembro->apellidos}\" no está activo en el sistema.";
        }

        // 2. Miembro bautizado
        if (strtolower(trim($miembro->etapa_consolidacion)) !== 'bautizado') {
            return "El miembro \"{$miembro->nombres} {$miembro->apellidos}\" no cumple el requisito de estar Bautizado para votar. Su etapa actual es: {$miembro->etapa_consolidacion}.";
        }

        // 3. EL MURO DE SEGURIDAD (Strict Padron Check)
        // Aquí verificamos si el miembro pertenece al padrón de la organización de ESTA elección.
        // Bajo NINGUNA circunstancia se debe hacer attach() o sync() aquí.
        $enPadron = $eleccion->organizacion->miembros()
            ->where('miembros.id', $miembro->id)
            ->exists();

        if (!$enPadron) {
            // Rechazo absoluto: El admin no lo incluyó en la lista VIP.
            return 'Acceso denegado: No te encuentras en el padrón electoral de esta organización. Consulta con el administrador.';
        }

        return null;
    }

    /**
     * Muestra la pantalla de ingreso de PIN.
     * Si ya hay un votante identificado en sesión, muestra su tarjeta con la opción de cambiar.
     */
    public function index()
    {
        $votanteId = session('votante_miembro_id');
        $votante = $votanteId ? Miembro::with('ministerios')->find($votanteId) : null;

        // Si el miembro ya no existe en la BD (caso extremo), limpiar sesión
        if ($votanteId && !$votante) {
            session()->forget('votante_miembro_id');
            $votante = null;
        }

        $eleccionId = session('eleccion_id_activa');
        $eleccion = $eleccionId ? Eleccion::with('organizacion')->find($eleccionId) : null;

        // Si la elección activa ya no existe o no está activa, limpiar sesión de elección
        if ($eleccionId && (!$eleccion || $eleccion->estado !== 'activa')) {
            session()->forget(['eleccion_id_activa', 'votante_miembro_id']);
            $eleccion = null;
            $votante = null;
        }

        return view('votar.index', compact('votante', 'eleccion'));
    }

    /**
     * Valida el PIN e inicia la sesión de la ronda.
     * Si el votante ya está identificado, lo manda directo a la papeleta.
     * Si NO está identificado, lo manda a la pantalla de identificación.
     */
    public function validarPin(Request $request)
    {
        $request->validate(['pin' => 'required|string|size:5']);

        $eleccion = Eleccion::where('pin_ronda', strtoupper($request->pin))
                            ->where('estado', 'activa')
                            ->first();

        if (!$eleccion) {
            return back()->withErrors(['pin' => 'PIN inválido o la ronda ha cerrado.']);
        }

        session(['eleccion_id_activa' => $eleccion->id]);

        // Si ya hay un votante identificado en sesión, saltar identificación
        if (session('votante_miembro_id')) {
            return redirect()->route('votar.papeleta');
        }

        return redirect()->route('votar.identificar');
    }

    /**
     * Limpia la identidad del votante de la sesión (útil si el miembro se retira).
     */
    public function cambiarVotante()
    {
        $eleccionId = session('eleccion_id_activa');
        if ($eleccionId) {
            $eleccion = Eleccion::find($eleccionId);
            if ($eleccion) {
                // Remover cualquier miembro con puesto 'Votante Temporal' en esta organización
                $eleccion->organizacion->miembros()->wherePivot('puesto', 'Votante Temporal')->detach();
            }
        }

        session()->forget('votante_miembro_id');
        return redirect()->route('votar.index')->with('success', 'Sesión de votante cerrada. El siguiente votante puede identificarse.');
    }

    /**
     * Limpia completamente toda la sesión del portal (elección activa y miembro).
     * Se usa cuando el votante o administrador quiere salir del portal por completo.
     */
    public function salirPortal()
    {
        $eleccionId = session('eleccion_id_activa');
        if ($eleccionId) {
            $eleccion = Eleccion::find($eleccionId);
            if ($eleccion) {
                // Remover cualquier miembro con puesto 'Votante Temporal' en esta organización
                $eleccion->organizacion->miembros()->wherePivot('puesto', 'Votante Temporal')->detach();
            }
        }

        session()->forget(['eleccion_id_activa', 'votante_miembro_id']);
        return redirect()->route('votar.index')->with('success', 'Sesión del portal cerrada correctamente.');
    }

    /**
     * Busca un miembro por ID o DPI y devuelve sus datos básicos (AJAX).
     * Incluye validaciones de elegibilidad (activo + bautizado + padrón).
     */
    public function buscarMiembro(Request $request)
    {
        $eleccionId = session('eleccion_id_activa');
        if (!$eleccionId) {
            return response()->json(['error' => 'Sesión expirada. Vuelve a ingresar el PIN.'], 401);
        }

        $valor = trim($request->query('q', ''));
        if (!$valor) {
            return response()->json(['error' => 'Ingresa un ID o DPI para buscar.'], 422);
        }

        $miembro = null;
        if (is_numeric($valor)) {
            $miembro = Miembro::with('ministerios')->find((int) $valor);
        }
        if (!$miembro) {
            $miembro = Miembro::with('ministerios')->where('dpi', $valor)->first();
        }

        if (!$miembro) {
            return response()->json(['error' => 'No se encontró ningún miembro con ese ID o DPI.'], 404);
        }

        $eleccion = Eleccion::with('organizacion.miembros')->findOrFail($eleccionId);

        // Validar elegibilidad completa
        $errorElegibilidad = $this->validarElegibilidad($miembro, $eleccion);
        if ($errorElegibilidad) {
            return response()->json(['error' => $errorElegibilidad], 422);
        }

        // Verificar si ya votó en el puesto en curso de esta ronda
        $yaVoto = DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->where('miembro_id', $miembro->id)
            ->where('puesto_votado', $eleccion->puesto_en_curso)
            ->exists();

        if ($yaVoto) {
            return response()->json(['error' => "Este miembro ya emitió su voto para el puesto de {$eleccion->puesto_en_curso} en esta ronda."], 422);
        }

        $fotoUrl = null;
        if ($miembro->foto && $miembro->foto !== 'default_avatar.png') {
            $fotoUrl = asset('storage/miembros/' . $miembro->foto);
        }

        return response()->json([
            'id'                   => $miembro->id,
            'nombres'              => $miembro->nombres,
            'apellidos'            => $miembro->apellidos,
            'dpi'                  => $miembro->dpi ?? '—',
            'ministerio'           => $miembro->ministerios->pluck('nombre')->implode(', ') ?: ($miembro->es_lider ? 'Líder' : 'General'),
            'etapa_consolidacion'  => $miembro->etapa_consolidacion,
            'foto_url'             => $fotoUrl,
            'iniciales'            => strtoupper(substr($miembro->nombres, 0, 1) . substr($miembro->apellidos, 0, 1)),
        ]);
    }

    /**
     * Muestra la pantalla de identificación del votante (QR / ID / DPI).
     * Solo se muestra si el votante NO está ya identificado en sesión.
     */
    public function identificar()
    {
        // Si ya está identificado, no necesita identificarse de nuevo
        if (session('votante_miembro_id')) {
            return redirect()->route('votar.papeleta');
        }

        $eleccionId = session('eleccion_id_activa');
        if (!$eleccionId) return redirect()->route('votar.index');

        $eleccion = Eleccion::with('organizacion')->findOrFail($eleccionId);

        if (!$eleccion->pin_ronda || !$eleccion->puesto_en_curso) {
            session()->forget(['eleccion_id_activa', 'votante_miembro_id']);
            return redirect()->route('votar.index')->withErrors(['pin' => 'La ronda de votación ha concluido.']);
        }

        return view('votar.identificar', compact('eleccion'));
    }

    /**
     * Procesa la identificación del votante (QR, ID o DPI).
     * Incluye validaciones de elegibilidad y guarda el ID en sesión.
     */
    public function procesarIdentificacion(Request $request)
    {
        $eleccionId = session('eleccion_id_activa');
        if (!$eleccionId) return redirect()->route('votar.index');

        $eleccion = Eleccion::with('organizacion.miembros')->findOrFail($eleccionId);

        $request->validate([
            'identificacion' => 'required|string|max:100',
        ], [
            'identificacion.required' => 'Debes ingresar tu ID de Miembro o DPI.',
        ]);

        $valor = trim($request->identificacion);

        $miembro = null;
        if (is_numeric($valor)) {
            $miembro = Miembro::find((int) $valor);
        }
        if (!$miembro) {
            $miembro = Miembro::where('dpi', $valor)->first();
        }

        if (!$miembro) {
            return back()->withErrors(['identificacion' => 'No se encontró ningún miembro con ese ID o DPI.'])->withInput();
        }

        // Validar elegibilidad completa
        $errorElegibilidad = $this->validarElegibilidad($miembro, $eleccion);
        if ($errorElegibilidad) {
            return back()->withErrors(['identificacion' => $errorElegibilidad])->withInput();
        }

        // Anti-doble voto en el puesto en curso
        $yaVoto = DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->where('miembro_id', $miembro->id)
            ->where('puesto_votado', $eleccion->puesto_en_curso)
            ->exists();

        if ($yaVoto) {
            return back()->withErrors(['identificacion' => "El miembro \"{$miembro->nombres} {$miembro->apellidos}\" ya emitió su voto para el puesto de {$eleccion->puesto_en_curso} en esta ronda."])->withInput();
        }

        // ✅ Guardar el ID en sesión. Esta identificación es permanente para toda la sesión de votación.
        session(['votante_miembro_id' => $miembro->id]);

        return redirect()->route('votar.papeleta');
    }

    /**
     * Muestra la papeleta oficial filtrada por el puesto en curso.
     */
    public function papeleta()
    {
        $eleccionId = session('eleccion_id_activa');
        if (!$eleccionId) return redirect()->route('votar.index');

        $miembroId = session('votante_miembro_id');
        if (!$miembroId) return redirect()->route('votar.identificar');

        $eleccion = Eleccion::with('organizacion')->findOrFail($eleccionId);

        if (!$eleccion->pin_ronda || !$eleccion->puesto_en_curso) {
            session()->forget(['eleccion_id_activa', 'votante_miembro_id']);
            return redirect()->route('votar.index')->withErrors(['pin' => 'La ronda de votación ha concluido.']);
        }

        $yaVoto = DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->where('miembro_id', $miembroId)
            ->where('puesto_votado', $eleccion->puesto_en_curso)
            ->exists();

        if ($yaVoto) {
            // Ya votó en este puesto — redirigir al PIN para esperar siguiente ronda
            // Pero SIN borrar la identidad — el miembro sigue siendo él
            return redirect()->route('votar.index')->with('success', 'Tu voto para el puesto de "' . $eleccion->puesto_en_curso . '" ya fue registrado. Espera al siguiente puesto.');
        }

        $miembro = Miembro::find($miembroId);

        $candidatos = Candidato::with('miembro')
            ->where('eleccion_id', $eleccion->id)
            ->where('puesto_postulado', $eleccion->puesto_en_curso)
            ->get();

        return view('votar.papeleta', compact('eleccion', 'candidatos', 'miembroId', 'miembro'));
    }
}
