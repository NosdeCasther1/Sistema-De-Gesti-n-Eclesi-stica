<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FamiliaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = \App\Models\Familia::withCount('miembros');

        if ($search) {
            $query->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('direccion', 'LIKE', "%{$search}%");
        }

        $familias = $query->paginate(9);

        if ($request->ajax()) {
            return view('familias._table', compact('familias'))->render();
        }

        return view('familias.index', compact('familias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $celulas = \App\Models\Celula::orderBy('nombre')->get();
        return view('familias.create', compact('celulas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:familias,nombre',
            'direccion' => 'nullable|string|max:255',
            'zona' => 'nullable|string|max:50',
            'municipio' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'telefono_principal' => 'nullable|numeric|digits:8',
            'notas' => 'nullable|string',
            'celula_id' => 'nullable|exists:celulas,id'
        ]);

        $data = $request->all();

        // Buscar el último código de familia registrado
        $ultimaFamilia = \App\Models\Familia::orderBy('codigo_familia', 'desc')->first();
        $siguienteNumero = $ultimaFamilia ? intval($ultimaFamilia->codigo_familia) + 1 : 1;

        // Formatear a 3 dígitos (Ej: '001', '015', '120')
        $codigo_familia = str_pad($siguienteNumero, 3, '0', STR_PAD_LEFT);
        $data['codigo_familia'] = $codigo_familia;

        \App\Models\Familia::create($data);

        return redirect()->route('familias.index')->with('success', 'Familia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $familia = \App\Models\Familia::with('miembros.ministerios', 'celula')->findOrFail($id);
        return view('familias.show', compact('familia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $familia = \App\Models\Familia::findOrFail($id);
        $celulas = \App\Models\Celula::orderBy('nombre')->get();
        return view('familias.edit', compact('familia', 'celulas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:familias,nombre,' . $id,
            'direccion' => 'nullable|string|max:255',
            'zona' => 'nullable|string|max:50',
            'municipio' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'telefono_principal' => 'nullable|numeric|digits:8',
            'notas' => 'nullable|string',
            'celula_id' => 'nullable|exists:celulas,id'
        ]);

        $familia = \App\Models\Familia::findOrFail($id);
        $familia->update($request->all());

        return redirect()->route('familias.index')->with('success', 'Familia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $familia = \App\Models\Familia::findOrFail($id);
        $familia->delete();

        return redirect()->route('familias.index')->with('success', 'Familia eliminada exitosamente.');
    }
}
