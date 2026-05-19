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

        return view('reportes.index', compact('totalMiembros', 'totalCelulas', 'mesActual', 'config', 'accounts'));
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
        $miembros = Miembro::orderBy('apellidos')->get();
        
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
        $miembros = Miembro::where('etapa_consolidacion', 'Bautizado')
            ->orderBy('apellidos')
            ->get();
        
        $config = $this->getConfig();
        $logoBase64 = $this->getLogoBase64($config);
        $pdf = Pdf::loadView('reportes.bautizados', compact('miembros', 'config', 'logoBase64'));
        return $pdf->setPaper('letter', 'portrait')->stream('Lista-Bautizados.pdf');
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
}
