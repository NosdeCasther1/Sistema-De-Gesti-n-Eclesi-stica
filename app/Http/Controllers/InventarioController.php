<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inventario;
use App\Models\Miembro;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $inventarios = Inventario::with('responsable')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('ubicacion', 'LIKE', "%{$search}%")
                      ->orWhereHas('responsable', function($q) use ($search) {
                          $q->where('nombres', 'LIKE', "%{$search}%")
                            ->orWhere('apellidos', 'LIKE', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('inventario.index', compact('inventarios', 'search'));
    }

    public function create()
    {
        $miembros = Miembro::orderBy('nombres')->get();
        return view('inventario.create', compact('miembros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cantidad' => 'required|integer|min:1',
            'estado' => 'required|in:Nuevo,Bueno,Regular,Malo',
            'ubicacion' => 'nullable|string|max:255',
            'responsable_id' => 'nullable|exists:miembros,id',
            'fecha_adquisicion' => 'nullable|date',
        ]);

        Inventario::create($request->all());

        return redirect()->route('inventario.index')->with('success', 'Artículo agregado al inventario exitosamente.');
    }

    public function show(Inventario $inventario)
    {
        return view('inventario.show', compact('inventario'));
    }

    public function edit(Inventario $inventario)
    {
        $miembros = Miembro::orderBy('nombres')->get();
        return view('inventario.edit', compact('inventario', 'miembros'));
    }

    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cantidad' => 'required|integer|min:1',
            'estado' => 'required|in:Nuevo,Bueno,Regular,Malo',
            'ubicacion' => 'nullable|string|max:255',
            'responsable_id' => 'nullable|exists:miembros,id',
            'fecha_adquisicion' => 'nullable|date',
        ]);

        $inventario->update($request->all());

        return redirect()->route('inventario.index')->with('success', 'Artículo actualizado exitosamente.');
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return redirect()->route('inventario.index')->with('success', 'Artículo eliminado exitosamente.');
    }
}
