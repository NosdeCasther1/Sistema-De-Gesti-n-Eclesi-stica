<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Miembro;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MiembroController extends Controller
{
    /**
     * Generar carnet PDF para un miembro específico.
     */
    public function generarCarnet(Miembro $miembro)
    {
        try {
            // Generar QR en formato SVG nativo (sin requerir extensión Imagick en Windows)
            $qrCode = base64_encode(QrCode::format('svg')
                ->size(200)
                ->errorCorrection('M')
                ->generate(route('miembros.show', $miembro->id)));

            $config = \App\Models\Configuracion::first() ?? \App\Models\Configuracion::create(['nombre_iglesia' => 'AD REY DE REYES']);
            
            // Convertir foto del miembro a Base64 para evitar problemas de rutas/chroot en DomPDF en Windows
            $fotoBase64 = null;
            if ($miembro->foto && $miembro->foto !== 'default_avatar.png') {
                $path = storage_path('app/public/miembros/' . $miembro->foto);
                if (file_exists($path)) {
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $fotoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
                }
            }

            $pdf = Pdf::loadView('miembros.carnet', compact('miembro', 'qrCode', 'config', 'fotoBase64'))
                      ->setPaper([0, 0, 242.65, 153.07], 'portrait');

            return $pdf->stream("Carnet-{$miembro->nombres}.pdf");
        } catch (\Exception $e) {
            Log::error("Error al generar carnet: " . $e->getMessage());
            return back()->with('error', 'Error al generar el carnet: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $ministerio = $request->input('ministerio');
        $etapa = $request->input('etapa');

        $miembros = Miembro::with('familia')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nombres', 'LIKE', "%{$search}%")
                      ->orWhere('apellidos', 'LIKE', "%{$search}%")
                      ->orWhere('dpi', 'LIKE', "%{$search}%")
                      ->orWhere('telefono', 'LIKE', "%{$search}%")
                      ->orWhere('id', $search)
                      ->orWhereHas('familia', function ($qFam) use ($search) {
                          $qFam->where('nombre', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->when($ministerio, function ($query, $ministerio) {
                return $query->where('ministerio', $ministerio);
            })
            ->when($etapa, function ($query, $etapa) {
                return $query->where('etapa_consolidacion', $etapa);
            })
            ->paginate(15)
            ->appends($request->all());
        
        if ($request->ajax()) {
            return view('miembros._table', compact('miembros'))->render();
        }

        // Obtener listas para los filtros
        $ministerios = Miembro::whereNotNull('ministerio')
            ->where('ministerio', '!=', '')
            ->where('ministerio', '!=', ' ')
            ->distinct()
            ->pluck('ministerio');
        $etapas = ['Nuevo', 'En Discipulado', 'Asignado a Célula', 'Bautizado'];

        return view('miembros.index', compact('miembros', 'search', 'ministerio', 'etapa', 'ministerios', 'etapas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('miembros.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'dpi' => 'required|string|max:20|unique:miembros,dpi',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'estado_civil' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'nivel_academico' => 'nullable|string|max:100',
            'profesion' => 'nullable|string|max:100',
            'lugar_trabajo_estudio' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:20',
            'ministerio' => 'nullable|string|max:100',
            'etapa_consolidacion' => 'required|string',
            'fecha_integracion' => 'nullable|date',
            'familia_id' => 'nullable|exists:familias,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('foto');
        $data['estado'] = true; // Miembro activo por defecto

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('miembros', $filename, 'public');
            $data['foto'] = $filename;
        } else {
            $data['foto'] = 'default_avatar.png';
        }

        $miembro = Miembro::create($data);

        return redirect()->route('miembros.show', $miembro->id)->with('success', 'Miembro registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Miembro $miembro)
    {
        return view('miembros.show', compact('miembro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Miembro $miembro)
    {
        return view('miembros.edit', compact('miembro'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Miembro $miembro)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'dpi' => 'required|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'estado_civil' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'nivel_academico' => 'nullable|string|max:100',
            'profesion' => 'nullable|string|max:100',
            'lugar_trabajo_estudio' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:20',
            'ministerio' => 'nullable|string|max:100',
            'etapa_consolidacion' => 'required|string',
            'fecha_integracion' => 'nullable|date',
            'familia_id' => 'nullable|exists:familias,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe y no es la default
            if ($miembro->foto && $miembro->foto !== 'default_avatar.png') {
                Storage::disk('public')->delete('miembros/' . $miembro->foto);
            }

            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('miembros', $filename, 'public');
            $data['foto'] = $filename;
        }

        $miembro->update($data);

        return redirect()->route('miembros.show', $miembro->id)->with('success', 'Miembro actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Miembro $miembro)
    {
        $miembro->delete();
        return redirect()->route('miembros.index');
    }
}
