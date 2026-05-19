<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CelulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = \App\Models\Celula::with('lider')->withCount('miembros');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('sector', 'LIKE', "%{$search}%")
                  ->orWhereHas('lider', function($qL) use ($search) {
                      $qL->where('nombres', 'LIKE', "%{$search}%")
                        ->orWhere('apellidos', 'LIKE', "%{$search}%");
                  });
            });
        }

        $celulas = $query->orderBy('nombre')->paginate(12);

        if ($request->ajax()) {
            return view('celulas._grid', compact('celulas'))->render();
        }

        return view('celulas.index', compact('celulas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $miembros = \App\Models\Miembro::orderBy('nombres')->get();
        return view('celulas.create', compact('miembros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'sector' => 'nullable|string|max:255',
            'lider_id' => 'required|exists:miembros,id',
            'dia_reunion' => 'required|string',
            'hora_reunion' => 'required',
            'direccion' => 'nullable|string'
        ]);

        \App\Models\Celula::create($request->all());

        return redirect()->route('celulas.index')->with('success', 'Célula creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $celula = \App\Models\Celula::with('lider', 'miembros')->withCount('miembros')->findOrFail($id);
        
        return view('celulas.show', compact('celula'));
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
}
