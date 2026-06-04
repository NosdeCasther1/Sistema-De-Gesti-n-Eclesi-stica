<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use App\Models\Asistencia;
use App\Models\Celula;
use App\Models\Evento;
use App\Models\Transaccion;
use App\Models\FinancialAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Models\Eleccion;
use App\Models\Candidato;
use Carbon\Carbon;

class ReporteController extends Controller
{
    private function getConfig()
    {
        return Configuracion::first() ?? Configuracion::create(['nombre_iglesia' => 'AD REY DE REYES', 'moneda' => 'Q']);
    }

    private function getLogoBase64($config)
    {
        $logoPath = null;
        if ($config && $config->logo) {
            $logoPath = storage_path('app/public/config/' . $config->logo);
        }
        
        if (!$logoPath || !file_exists($logoPath)) {
            $logoPath = public_path('imagen/Logo_AD_Rey_de_Reyes_optimized.png');
        }
        
        if (!file_exists($logoPath)) {
            $logoPath = public_path('imagen/Logo AD Rey de Reyes.png');
        }

        if (file_exists($logoPath)) {
            $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            return 'data:image/' . $extension . ';base64,' . base64_encode($data);
        }
        return null;
    }

    public function index()
    {
        $config = $this->getConfig();
        $totalMiembros = Miembro::count();
        $totalCelulas = Celula::count();
        $mesActual = Carbon::now()->translatedFormat('F');
        $accounts = FinancialAccount::withSum('transactions as total_balance', 'amount')->get();
        $elecciones = Eleccion::with('organizacion')->orderBy('created_at', 'desc')->get();

        // Data for Inventario Filters
        $ubicacionesInventario = \App\Models\Inventario::whereNotNull('ubicacion')->distinct()->pluck('ubicacion');
        $responsablesInventario = Miembro::whereIn('id', \App\Models\Inventario::whereNotNull('responsable_id')->distinct()->pluck('responsable_id'))->get();
        $organizacionesReporte = \App\Models\Organizacion::orderBy('nombre')->get();

        return view('reportes.index', compact('totalMiembros', 'totalCelulas', 'mesActual', 'config', 'accounts', 'elecciones', 'ubicacionesInventario', 'responsablesInventario', 'organizacionesReporte'));
    }

    public function reportarAsistenciaCelula(Request $request, $celula_id)
    {
        ini_set('memory_limit', '256M');
        app()->setLocale('es');
        $mes = $request->query('mes', date('m'));
        $anio = $request->query('anio', date('Y'));
        $celula = Celula::with('miembros', 'lider')->findOrFail($celula_id);
        
        $fechaInicio = Carbon::createFromDate($anio, $mes, 1);
        $fechaFin = $fechaInicio->copy()->endOfMonth();
        $diasDelMes = $fechaInicio->daysInMonth;

        $asistencias = Asistencia::where('celula_id', $celula_id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy(['miembro_id', function ($item) {
                return Carbon::parse($item->fecha)->day;
            }]);

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.asistencia_celula', compact('celula', 'mes', 'anio', 'diasDelMes', 'asistencias', 'fechaInicio', 'config', 'logoBase64'));
        
        return $pdf->setPaper('legal', 'landscape')->stream("Asistencia-{$celula->nombre}-{$mes}-{$anio}.pdf");
    }

    public function reportarAsistenciaEvento(Request $request, $evento_id)
    {
        ini_set('memory_limit', '256M');
        app()->setLocale('es');
        $evento = Evento::findOrFail($evento_id);
        
        $asistencias = Asistencia::with('miembro')
            ->where('evento_id', $evento_id)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.asistencia_evento', compact('evento', 'asistencias', 'config', 'logoBase64'));
        
        return $pdf->setPaper('letter', 'portrait')->stream("Asistencia-Evento-{$evento->id}.pdf");
    }

    public function reportarMembresiaDinamico(Request $request)
    {
        ini_set('memory_limit', '256M');
        $tipo = $request->query('tipo', 'general');

        $search = $request->query('search');
        $ministerio = $request->query('ministerio');
        $etapa = $request->query('etapa');
        $cargo = $request->query('cargo');

        $query = Miembro::with('ministerios')->orderBy('apellidos');

        if ($tipo === 'bautizados') {
            $query->where('etapa_consolidacion', 'Bautizado')->orWhere('bautizado_agua', true);
            $vista = 'reportes.bautizados';
            $titulo = 'Lista-Bautizados.pdf';
        } elseif ($tipo === 'no_bautizados') {
            $query->where('etapa_consolidacion', '!=', 'Bautizado')->where('bautizado_agua', false);
            $vista = 'reportes.miembros';
            $titulo = 'Censo-No-Bautizados.pdf';
        } else {
            $vista = 'reportes.miembros';
            $titulo = 'Censo-Membresia.pdf';
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'LIKE', "%{$search}%")
                  ->orWhere('apellidos', 'LIKE', "%{$search}%")
                  ->orWhere('dpi', 'LIKE', "%{$search}%")
                  ->orWhere('telefono', 'LIKE', "%{$search}%");
            });
        }
        if ($ministerio) {
            $query->whereHas('ministerios', function ($q) use ($ministerio) {
                $q->where('ministerios.id', $ministerio);
            });
        }
        if ($etapa) {
            $query->where('etapa_consolidacion', $etapa);
        }
        if ($cargo) {
            $query->where(function ($q) use ($cargo) {
                $q->where('cargo_liderazgo', 'LIKE', "%{$cargo}%")
                  ->orWhereHas('organizaciones', function ($qOrg) use ($cargo) {
                      $qOrg->where('miembro_organizacion.puesto', 'LIKE', "%{$cargo}%");
                  });
            });
        }

        $miembros = $query->get();
        
        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView($vista, compact('miembros', 'config', 'logoBase64'));
        
        return $pdf->setPaper('letter', 'portrait')->stream($titulo);
    }
    public function reportarTesoreria(Request $request)
    {
        ini_set('memory_limit', '256M');
        $desde = $request->query('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->query('hasta', now()->toDateString());

        $transacciones = Transaccion::with('categoria', 'miembro')
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha', 'asc')
            ->get();

        $ingresos = $transacciones->where('categoria.tipo', 'Ingreso')->sum('monto');
        $gastos = $transacciones->where('categoria.tipo', 'Gasto')->sum('monto');
        $balance = $ingresos - $gastos;

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.tesoreria', compact('transacciones', 'ingresos', 'gastos', 'balance', 'desde', 'hasta', 'config', 'logoBase64'));
        
        return $pdf->setPaper('letter', 'portrait')->stream('Reporte-Tesoreria.pdf');
    }

    public function reportarOrganizaciones(Request $request)
    {
        ini_set('memory_limit', '256M');
        $organizacionId = $request->query('organizacion_id');
        $modoReporte = $request->query('modo_reporte', 'organizacion');
        
        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);

        if ($modoReporte === 'puesto') {
            $query = \Illuminate\Support\Facades\DB::table('miembro_organizacion')
                ->join('miembros', 'miembro_organizacion.miembro_id', '=', 'miembros.id')
                ->join('organizaciones', 'miembro_organizacion.organizacion_id', '=', 'organizaciones.id')
                ->select(
                    'miembro_organizacion.puesto', 
                    'miembro_organizacion.fecha_asignacion', 
                    'miembro_organizacion.estado', 
                    'miembros.nombres', 
                    'miembros.apellidos', 
                    'miembros.dpi',
                    'miembros.telefono',
                    'miembros.id as miembro_id',
                    'organizaciones.nombre as organizacion_nombre'
                );

            if ($organizacionId) {
                $query->where('organizaciones.id', $organizacionId);
            }

            $asignacionesAgrupadas = $query
                ->orderBy('miembro_organizacion.puesto')
                ->orderBy('organizaciones.nombre')
                ->orderBy('miembros.nombres')
                ->get()
                ->groupBy(function($item) {
                    return $item->puesto ?: 'Miembro Regular';
                });

            $pdf = Pdf::loadView('reportes.organizaciones_puestos_pdf', compact('asignacionesAgrupadas', 'config', 'logoBase64'));
            return $pdf->setPaper('letter', 'portrait')->stream('Reporte-Cargos-Organizaciones.pdf');
        }

        $query = \App\Models\Organizacion::with(['miembros' => function($q) {
            $q->orderBy('nombres');
        }])->orderBy('nombre');

        if ($organizacionId) {
            $query->where('id', $organizacionId);
        }

        $organizaciones = $query->get();
        
        $pdf = Pdf::loadView('reportes.organizaciones_pdf', compact('organizaciones', 'config', 'logoBase64'));
        
        return $pdf->setPaper('letter', 'portrait')->stream('Reporte-Organizaciones.pdf');
    }

    public function reportarInventario(Request $request)
    {
        ini_set('memory_limit', '256M');
        $query = \App\Models\Inventario::with('responsable')->orderBy('nombre');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', $request->ubicacion);
        }

        if ($request->filled('responsable_id')) {
            $query->where('responsable_id', $request->responsable_id);
        }

        $inventarios = $query->get();

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.inventario_pdf', compact('inventarios', 'config', 'logoBase64'));
        return $pdf->setPaper('letter', 'portrait')->stream('Reporte-Inventario.pdf');
    }

    public function reportarIngresosFamilia(Request $request)
    {
        ini_set('memory_limit', '256M');
        $desde = $request->query('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->query('hasta', now()->toDateString());

        $familias = \App\Models\Familia::with(['miembros.transacciones' => function($q) use ($desde, $hasta) {
            $q->whereBetween('fecha', [$desde, $hasta]);
        }])->get();

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.ingresos_familia', compact('familias', 'desde', 'hasta', 'config', 'logoBase64'));
        return $pdf->setPaper('letter', 'portrait')->stream('Ingresos-por-Familia.pdf');
    }

    public function reportarVotacionesEscrutinio(Eleccion $eleccion)
    {
        ini_set('memory_limit', '256M');
        $eleccion->load('organizacion');
        $organizacion = $eleccion->organizacion;
        $candidatos = Candidato::with('miembro')
            ->where('eleccion_id', $eleccion->id)
            ->get();

        $esAbsoluta = $eleccion->tipo_mayoria === 'absoluta';

        $resultados = $candidatos->groupBy('puesto_postulado')->map(function ($grupoCandidatos) use ($esAbsoluta) {
            $totalVotosPuesto = $grupoCandidatos->sum(function ($candidato) {
                return $candidato->votos_digitales + $candidato->votos_manuales;
            });

            $grupoCandidatos = $grupoCandidatos->map(function ($candidato) {
                $candidato->votos_totales = $candidato->votos_digitales + $candidato->votos_manuales;
                return $candidato;
            });

            $maxVotos = $grupoCandidatos->max('votos_totales');

            return $grupoCandidatos->map(function ($candidato) use ($totalVotosPuesto, $maxVotos, $esAbsoluta) {
                $candidato->porcentaje = $totalVotosPuesto > 0 ? round(($candidato->votos_totales / $totalVotosPuesto) * 100, 2) : 0;
                $candidato->es_ganador = false;
                $candidato->requiere_segunda_vuelta = false;

                if ($candidato->votos_totales === $maxVotos && $maxVotos > 0) {
                    if ($esAbsoluta) {
                        if ($candidato->votos_totales > ($totalVotosPuesto / 2)) {
                            $candidato->es_ganador = true;
                        } else {
                            $candidato->requiere_segunda_vuelta = true;
                        }
                    } else {
                        $candidato->es_ganador = true;
                    }
                }

                return $candidato;
            })->sortByDesc('votos_totales')->values();
        });

        $totalPadron = $organizacion->miembros()->wherePivot('estado', true)->count();
        $totalVotantesUnicos = \Illuminate\Support\Facades\DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->distinct()
            ->count('miembro_id');

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);

        $pdf = Pdf::loadView('elecciones.reporte_escrutinio', compact(
            'eleccion',
            'organizacion',
            'resultados',
            'totalPadron',
            'totalVotantesUnicos',
            'config',
            'logoBase64'
        ));

        return $pdf->stream("Acta_Escrutinio_{$eleccion->id}.pdf");
    }

    public function reportarVotacionesParticipantes(Eleccion $eleccion)
    {
        ini_set('memory_limit', '256M');
        $eleccion->load('organizacion');
        $organizacion = $eleccion->organizacion;

        $participantes = \Illuminate\Support\Facades\DB::table('registro_votantes')
            ->join('miembros', 'registro_votantes.miembro_id', '=', 'miembros.id')
            ->where('registro_votantes.eleccion_id', $eleccion->id)
            ->select('miembros.nombres', 'miembros.apellidos', 'registro_votantes.puesto_votado', 'registro_votantes.modalidad', 'registro_votantes.created_at')
            ->orderBy('registro_votantes.created_at', 'asc')
            ->get();

        $totalPadron = $organizacion->miembros()->wherePivot('estado', true)->count();
        $totalVotantesUnicos = \Illuminate\Support\Facades\DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->distinct()
            ->count('miembro_id');

        $votosDigitales = \Illuminate\Support\Facades\DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->where('modalidad', 'digital')
            ->count();

        $votosManuales = \Illuminate\Support\Facades\DB::table('registro_votantes')
            ->where('eleccion_id', $eleccion->id)
            ->where('modalidad', 'manual')
            ->count();

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);

        $pdf = Pdf::loadView('reportes.votaciones_participantes', compact(
            'eleccion',
            'organizacion',
            'participantes',
            'totalPadron',
            'totalVotantesUnicos',
            'votosDigitales',
            'votosManuales',
            'config',
            'logoBase64'
        ));

        return $pdf->stream("Participantes_Votantes_{$eleccion->id}.pdf");
    }

    public function reportarVotacionesConformacion(Eleccion $eleccion)
    {
        ini_set('memory_limit', '256M');
        $eleccion->load('organizacion');
        $organizacion = $eleccion->organizacion;
        $candidatos = Candidato::with('miembro')
            ->where('eleccion_id', $eleccion->id)
            ->get();

        $esAbsoluta = $eleccion->tipo_mayoria === 'absoluta';

        $resultados = $candidatos->groupBy('puesto_postulado')->map(function ($grupoCandidatos) use ($esAbsoluta) {
            $totalVotosPuesto = $grupoCandidatos->sum(function ($candidato) {
                return $candidato->votos_digitales + $candidato->votos_manuales;
            });

            $grupoCandidatos = $grupoCandidatos->map(function ($candidato) {
                $candidato->votos_totales = $candidato->votos_digitales + $candidato->votos_manuales;
                return $candidato;
            });

            $maxVotos = $grupoCandidatos->max('votos_totales');

            return $grupoCandidatos->map(function ($candidato) use ($totalVotosPuesto, $maxVotos, $esAbsoluta) {
                $candidato->porcentaje = $totalVotosPuesto > 0 ? round(($candidato->votos_totales / $totalVotosPuesto) * 100, 2) : 0;
                $candidato->es_ganador = false;

                if ($candidato->votos_totales === $maxVotos && $maxVotos > 0) {
                    if ($esAbsoluta) {
                        if ($candidato->votos_totales > ($totalVotosPuesto / 2)) {
                            $candidato->es_ganador = true;
                        }
                    } else {
                        $candidato->es_ganador = true;
                    }
                }

                return $candidato;
            })->sortByDesc('votos_totales')->values();
        });

        $directivaConformada = [];
        foreach ($resultados as $puesto => $cands) {
            $ganador = $cands->firstWhere('es_ganador', true);
            if ($ganador) {
                $directivaConformada[] = [
                    'puesto' => $puesto,
                    'nombre' => $ganador->miembro->nombre_completo,
                    'votos' => $ganador->votos_totales,
                    'porcentaje' => $ganador->porcentaje,
                    'estado' => 'Electo'
                ];
            } else {
                $directivaConformada[] = [
                    'puesto' => $puesto,
                    'nombre' => 'VACANTE (Requiere Segunda Vuelta o Sin Votos)',
                    'votos' => '-',
                    'porcentaje' => '-',
                    'estado' => 'Vacante'
                ];
            }
        }

        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);

        $pdf = Pdf::loadView('reportes.votaciones_conformacion', compact(
            'eleccion',
            'organizacion',
            'directivaConformada',
            'config',
            'logoBase64'
        ));

        return $pdf->stream("Conformacion_Directiva_{$eleccion->id}.pdf");
    }
}
