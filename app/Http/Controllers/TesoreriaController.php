<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\User;
use App\Http\Requests\StoreTesoreriaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TesoreriaController extends Controller
{
    /**
     * Muestra el Dashboard Principal de Tesorería
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'all');
        $search = $request->query('search');

        // 1. Obtener cajas activas
        $accounts = FinancialAccount::where('is_active', true)->whereNull('deleted_at')->get();

        // 2. Obtener categorías separadas por tipo para los modales
        $incomeCategories = FinancialCategory::where('type', 'income')->where('is_active', true)->whereNull('deleted_at')->get();
        $expenseCategories = FinancialCategory::where('type', 'expense')->where('is_active', true)->whereNull('deleted_at')->get();

        // Determinar qué cuenta filtrar basado en activeTab
        $selectedAccountId = null;
        if ($activeTab === 'general') {
            $acc = FinancialAccount::where('name', 'LIKE', '%General%')->first();
            $selectedAccountId = $acc ? $acc->id : null;
        } elseif ($activeTab === 'jovenes') {
            $acc = FinancialAccount::where('name', 'LIKE', '%Jóvenes%')->first();
            $selectedAccountId = $acc ? $acc->id : null;
        } elseif ($activeTab === 'misiones') {
            $acc = FinancialAccount::where('name', 'LIKE', '%Misiones%')->first();
            $selectedAccountId = $acc ? $acc->id : null;
        }

        // 3. Obtener las transacciones recientes para la tabla con filtro de caja y búsqueda
        $transactionsQuery = FinancialTransaction::with(['account', 'category', 'user'])
                                ->orderBy('transaction_date', 'desc')
                                ->orderBy('created_at', 'desc');

        if ($selectedAccountId) {
            $transactionsQuery->where('account_id', $selectedAccountId);
        }

        if ($search) {
            $transactionsQuery->where(function($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhere('reference_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($c) use ($search) {
                      $c->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Paginar o limitar a 15
        $recentTransactions = $transactionsQuery->paginate(15);

        // 4. Calcular métricas financieras (Ingresos, Gastos, Balance) filtradas por caja
        $ingresosQuery = FinancialTransaction::where('type', 'income')->where('status', 'completed');
        $gastosQuery = FinancialTransaction::where('type', 'expense')->where('status', 'completed');

        if ($selectedAccountId) {
            $ingresosQuery->where('account_id', $selectedAccountId);
            $gastosQuery->where('account_id', $selectedAccountId);
        }

        $totalIngresos = $ingresosQuery->sum('amount');
        $totalGastos = $gastosQuery->sum('amount');

        if ($selectedAccountId && isset($acc)) {
            // El balance de una caja individual incluye su balance inicial
            $balanceGeneral = $acc->initial_balance + $totalIngresos - $totalGastos;
        } else {
            // Balance consolidado total
            $initialBalances = FinancialAccount::where('is_active', true)->sum('initial_balance');
            $balanceGeneral = $initialBalances + $totalIngresos - $totalGastos;
        }

        // Si es una petición AJAX (búsqueda en vivo o cambio de tab), devolver solo la tabla parcial
        if ($request->ajax()) {
            return view('tesoreria._table', compact('recentTransactions'))->render();
        }

        // Enviar todo empaquetado a la vista
        return view('tesoreria.index', compact(
            'accounts',
            'incomeCategories',
            'expenseCategories',
            'recentTransactions',
            'totalIngresos',
            'totalGastos',
            'balanceGeneral',
            'activeTab',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tipo = $request->query('tipo', 'Ingreso');
        $type = $tipo === 'Gasto' ? 'expense' : 'income';
        $categorias = FinancialCategory::where('type', $type)->where('is_active', true)->whereNull('deleted_at')->get();
        $accounts = FinancialAccount::where('is_active', true)->whereNull('deleted_at')->get();

        return view('tesoreria.create', compact('categorias', 'accounts', 'type', 'tipo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTesoreriaRequest $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            $defaultUser = User::first();
            if (!$defaultUser) {
                $defaultUser = User::create([
                    'name' => 'Administrador del Sistema',
                    'email' => 'admin@iglesia.com',
                    'password' => bcrypt('password123'),
                ]);
            }
            $userId = $defaultUser->id;
        }

        // 1. IDEMPOTENCIA: Bloqueo atómico en Caché (Double-Submit Protection)
        $lockName = 'transaction_submit_' . $userId;
        $lock = Cache::lock($lockName, 5);

        if (!$lock->get()) {
            return back()->with('error', 'La transacción ya está siendo procesada. Evite hacer doble clic.');
        }

        try {
            // 2. TRANSACCIONES ACID: Todo o Nada
            DB::transaction(function () use ($request, $userId) {
                
                // 3. CONTROL DE CONCURRENCIA: Bloqueo Pesimista (Pessimistic Locking)
                $account = FinancialAccount::lockForUpdate()->findOrFail($request->account_id);

                // Evitar sobregiros en gastos
                if ($request->type === 'expense') {
                    if ($account->balance < $request->monto) {
                        throw new \Exception('Fondos insuficientes en la caja seleccionada.');
                    }
                }

                // Subida de Comprobante Físico
                $proofPath = null;
                if ($request->hasFile('proof_path')) {
                    $proofPath = $request->file('proof_path')->store('receipts', 'public');
                }

                // Inserción inmutable en el Libro Diario
                FinancialTransaction::create([
                    'account_id'       => $account->id,
                    'category_id'      => $request->categoria_id,
                    'user_id'          => $userId,
                    'type'             => $request->type,
                    'amount'           => $request->monto,
                    'transaction_date' => $request->fecha,
                    'description'      => $request->descripcion,
                    'reference_number' => $request->reference_number,
                    'proof_path'       => $proofPath,
                    'status'           => 'completed',
                ]);
            });

            return back()->with('success', 'Transacción registrada correctamente en el libro diario inmutable.');

        } catch (\Exception $e) {
            Log::error('Error Crítico en Tesorería: ' . $e->getMessage());
            $lock->release(); 

            $mensaje = $e->getMessage() === 'Fondos insuficientes en la caja seleccionada.' 
                        ? $e->getMessage() 
                        : 'Ocurrió un error al procesar la transacción. Intente de nuevo.';

            return back()->with('error', $mensaje)->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Realiza una transferencia atómica de doble entrada entre cajas financieras.
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:financial_accounts,id',
            'to_account_id' => 'required|exists:financial_accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ], [
            'from_account_id.required' => 'Debe seleccionar la caja de origen.',
            'from_account_id.exists' => 'La caja de origen seleccionada no es válida.',
            'to_account_id.required' => 'Debe seleccionar la caja de destino.',
            'to_account_id.exists' => 'La caja de destino seleccionada no es válida.',
            'to_account_id.different' => 'La caja de destino debe ser diferente a la caja de origen.',
            'amount.required' => 'El monto a transferir es obligatorio.',
            'amount.numeric' => 'El monto debe ser un valor numérico.',
            'amount.min' => 'El monto mínimo a transferir es de Q0.01.',
        ]);

        $fromAccountCheck = FinancialAccount::findOrFail($validated['from_account_id']);

        if ($fromAccountCheck->balance < $validated['amount']) {
            return back()->with('error', 'La caja de origen no tiene fondos suficientes para esta operación.');
        }

        $userId = auth()->id() ?? User::first()->id ?? 1;

        // 1. IDEMPOTENCIA: Bloqueo atómico en Caché (Double-Submit Protection)
        $lockName = 'transfer_submit_' . $userId;
        $lock = Cache::lock($lockName, 5);

        if (!$lock->get()) {
            return back()->with('error', 'La transferencia ya está siendo procesada. Evite hacer doble clic.');
        }

        try {
            DB::transaction(function () use ($validated, $userId) {
                // 2. CONTROL DE CONCURRENCIA: Bloqueo Pesimista (Pessimistic Locking) de ambas cajas
                $fromAccount = FinancialAccount::lockForUpdate()->findOrFail($validated['from_account_id']);
                $toAccount = FinancialAccount::lockForUpdate()->findOrFail($validated['to_account_id']);

                if ($fromAccount->balance < $validated['amount']) {
                    throw new \Exception('La caja de origen no tiene fondos suficientes para esta operación.');
                }

                // 3. Buscar o crear categorías especiales de Transferencia Interna para mantener la pureza contable
                $catSalida = FinancialCategory::firstOrCreate(
                    ['name' => 'Transferencia Interna (Salida)', 'type' => 'expense'],
                    ['description' => 'Movimiento automático de salida por transferencia', 'is_active' => true]
                );

                $catEntrada = FinancialCategory::firstOrCreate(
                    ['name' => 'Transferencia Interna (Entrada)', 'type' => 'income'],
                    ['description' => 'Movimiento automático de entrada por transferencia', 'is_active' => true]
                );

                $reference = 'TRF-' . strtoupper(Str::random(8));
                $desc = $validated['description'] ? ": " . $validated['description'] : ".";

                // 4. Registrar Salida (Gasto en origen)
                FinancialTransaction::create([
                    'account_id' => $fromAccount->id,
                    'category_id' => $catSalida->id,
                    'user_id' => $userId,
                    'type' => 'expense',
                    'amount' => $validated['amount'],
                    'description' => "Transferencia enviada a {$toAccount->name}{$desc}",
                    'reference_number' => $reference,
                    'transaction_date' => now(),
                    'status' => 'completed',
                ]);

                // 5. Registrar Entrada (Ingreso en destino)
                FinancialTransaction::create([
                    'account_id' => $toAccount->id,
                    'category_id' => $catEntrada->id,
                    'user_id' => $userId,
                    'type' => 'income',
                    'amount' => $validated['amount'],
                    'description' => "Transferencia recibida de {$fromAccount->name}{$desc}",
                    'reference_number' => $reference,
                    'transaction_date' => now(),
                    'status' => 'completed',
                ]);
            });

            return back()->with('success', 'Transferencia entre cajas completada con éxito.');

        } catch (\Exception $e) {
            Log::error('Error Crítico en Transferencia: ' . $e->getMessage());
            $lock->release();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
