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
        if ($config && $config->logo) {
            $path = storage_path('app/public/config/' . $config->logo);
            if (file_exists($path)) {
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                return 'data:image/' . $extension . ';base64,' . base64_encode($data);
            }
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

        return view('reportes.index', compact('totalMiembros', 'totalCelulas', 'mesActual', 'config', 'accounts', 'elecciones'));
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

    public function reportarMiembros()
    {
        ini_set('memory_limit', '256M');
        $miembros = Miembro::with('ministerios')->orderBy('apellidos')->get();
        
        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.miembros', compact('miembros', 'config', 'logoBase64'));
        
        return $pdf->setPaper('letter', 'landscape')->stream('Censo-Membresia.pdf');
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

    public function reportarBautizados()
    {
        ini_set('memory_limit', '256M');
        $miembros = Miembro::with('ministerios')->where('etapa_consolidacion', 'Bautizado')
            ->orderBy('apellidos')
            ->get();
        
        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.bautizados', compact('miembros', 'config', 'logoBase64'));
        return $pdf->setPaper('letter', 'portrait')->stream('Lista-Bautizados.pdf');
    }

    public function reportarInventario(Request $request)
    {
        ini_set('memory_limit', '256M');
        $inventarios = \App\Models\Inventario::with('responsable')
            ->orderBy('nombre')
            ->get();

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
