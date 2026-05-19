<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Configuracion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteTesoreriaController extends Controller
{
    public function generateCorteCaja(Request $request) 
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'account_id' => 'nullable|exists:financial_accounts,id',
            'type' => 'nullable|in:income,expense',
        ]);

        // Optimización: Traemos la cuenta y categoría
        $query = FinancialTransaction::with(['account', 'category'])
            ->whereBetween('transaction_date', [$request->fecha_inicio, $request->fecha_fin])
            ->where('status', 'completed');

        if ($request->account_id) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();
        
        // Totales calculados para el reporte en el rango seleccionado
        $totalIngresos = $transactions->where('type', 'income')->sum('amount');
        $totalGastos = $transactions->where('type', 'expense')->sum('amount');

        // Cálculo de Saldo Anterior (Audit Grade)
        $saldoAnterior = 0;
        $account = null;

        if ($request->account_id) {
            $account = FinancialAccount::find($request->account_id);
            $baseInitial = $account ? $account->initial_balance : 0;

            $ingresosAnt = FinancialTransaction::where('account_id', $request->account_id)
                ->where('transaction_date', '<', $request->fecha_inicio)
                ->where('status', 'completed')
                ->where('type', 'income')
                ->sum('amount');

            $gastosAnt = FinancialTransaction::where('account_id', $request->account_id)
                ->where('transaction_date', '<', $request->fecha_inicio)
                ->where('status', 'completed')
                ->where('type', 'expense')
                ->sum('amount');

            $saldoAnterior = $baseInitial + $ingresosAnt - $gastosAnt;
        } else {
            $baseInitial = FinancialAccount::where('is_active', true)->sum('initial_balance');

            $ingresosAnt = FinancialTransaction::where('transaction_date', '<', $request->fecha_inicio)
                ->where('status', 'completed')
                ->where('type', 'income')
                ->sum('amount');

            $gastosAnt = FinancialTransaction::where('transaction_date', '<', $request->fecha_inicio)
                ->where('status', 'completed')
                ->where('type', 'expense')
                ->sum('amount');

            $saldoAnterior = $baseInitial + $ingresosAnt - $gastosAnt;
        }

        $saldoActual = $saldoAnterior + $totalIngresos - $totalGastos;

        $iglesia = Configuracion::first() ?? Configuracion::create([
            'nombre_iglesia' => 'AD REY DE REYES', 
            'moneda' => 'Q',
            'pastor_general' => 'Pastor General'
        ]);

        $logoBase64 = null;
        if ($iglesia->logo) {
            $path = storage_path('app/public/config/' . $iglesia->logo);
            if (file_exists($path)) {
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('reportes.pdf.corte-caja', [
            'transactions' => $transactions,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'balance' => $totalIngresos - $totalGastos,
            'saldoAnterior' => $saldoAnterior,
            'saldoActual' => $saldoActual,
            'account' => $account,
            'selectedType' => $request->type,
            'iglesia' => $iglesia,
            'logoBase64' => $logoBase64,
        ]);

        return $pdf->setPaper('letter', 'portrait')->stream('Corte_de_Caja_' . Carbon::parse($request->fecha_inicio)->format('d_m_Y') . '.pdf');
    }
}
