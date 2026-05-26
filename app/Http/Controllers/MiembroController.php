<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Miembro;
use App\Models\Ministerio;
use App\Models\Organizacion;
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
        $miembro->load('ministerios');
        try {
            $config = \App\Models\Configuracion::first() ?? \App\Models\Configuracion::create(['nombre_iglesia' => 'AD REY DE REYES']);
            $iglesia = $config->nombre_iglesia ?? 'AD REY DE REYES';
            
            // Generar QR PNG usando GD de forma nativa (evitando imagick)
            $qrBase64 = '';
            try {
                $matrix = \BaconQrCode\Encoder\Encoder::encode($miembro->dpi, \BaconQrCode\Common\ErrorCorrectionLevel::M())->getMatrix();
                $w = $matrix->getWidth();
                $scale = 4; // Cada celda será de 4x4 píxeles
                $img = imagecreatetruecolor($w * $scale, $w * $scale);
                $white = imagecolorallocate($img, 255, 255, 255);
                $black = imagecolorallocate($img, 0, 0, 0);
                
                // Rellenar el fondo de blanco (imagecreatetruecolor por defecto es negro)
                imagefill($img, 0, 0, $white);
                
                for ($y = 0; $y < $w; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        if ($matrix->get($x, $y) === 1) {
                            imagefilledrectangle(
                                $img,
                                $x * $scale,
                                $y * $scale,
                                (($x + 1) * $scale) - 1,
                                (($y + 1) * $scale) - 1,
                                $black
                            );
                        }
                    }
                }
                
                ob_start();
                imagepng($img);
                $pngData = ob_get_clean();
                imagedestroy($img);
                
                $qrBase64 = 'data:image/png;base64,' . base64_encode($pngData);
            } catch (\Exception $ex) {
                Log::error("Error generating QR PNG via GD: " . $ex->getMessage());
            }

            // Convertir foto del miembro a Base64 para evitar problemas de rutas/chroot en DomPDF en Windows
            $fotoBase64 = null;
            $photoPath = null;
            if ($miembro->foto && $miembro->foto !== 'default_avatar.png') {
                $photoPath = storage_path('app/public/miembros/' . $miembro->foto);
            }
            if (!$photoPath || !file_exists($photoPath)) {
                $photoPath = public_path('assets/img/miembros/default_avatar.png');
            }
            if (file_exists($photoPath)) {
                $extension = pathinfo($photoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($photoPath);
                $fotoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
            }

            // Convertir logo a Base64 para DomPDF (usando logo de configuración o por defecto)
            $logoBase64 = '';
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
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($logoPath));
            }

            // Firma del Pastor (PNG transparente recomendado)
            $pathFirma = ($config && $config->firma_pastor) ? storage_path('app/public/config/' . $config->firma_pastor) : public_path('imagen/firma_pastor.png');
            $firmaBase64 = file_exists($pathFirma) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathFirma)) : null;

            // Sello de la Iglesia (PNG transparente recomendado)
            $pathSello = ($config && $config->sello_iglesia) ? storage_path('app/public/config/' . $config->sello_iglesia) : public_path('imagen/sello_iglesia.png');
            $selloBase64 = file_exists($pathSello) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathSello)) : null;

            // Generación del PDF con tamaño personalizado de tarjeta PVC
            $pdf = Pdf::loadView('miembros.pdf.carnet', compact('miembro', 'config', 'iglesia', 'fotoBase64', 'logoBase64', 'qrBase64', 'firmaBase64', 'selloBase64'))
                      ->setPaper(array(0, 0, 330, 210));

            return $pdf->stream("Carnet-{$miembro->nombres}.pdf");
        } catch (\Exception $e) {
            Log::error("Error al generar carnet: " . $e->getMessage());
            return back()->with('error', 'Error al generar el carnet: ' . $e->getMessage());
        }
    }

    public function cartaRecomendacion(Miembro $miembro)
    {
        $config = \App\Models\Configuracion::first();
        $pastor = $config ? ($config->pastor_general ?? 'Pastor General') : 'Pastor General'; 
        $iglesia = $config ? ($config->nombre_iglesia ?? 'AD Rey de Reyes') : 'AD Rey de Reyes';

        // Convertir logo a Base64 para DomPDF (usando logo optimizado)
        $path = public_path('imagen/Logo_AD_Rey_de_Reyes_optimized.png');
        if (!file_exists($path)) {
            $path = public_path('imagen/Logo AD Rey de Reyes.png');
        }
        $logoBase64 = '';
        if (file_exists($path)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        }

        $pdf = Pdf::loadView('miembros.pdf.carta_recomendacion', compact('miembro', 'pastor', 'iglesia', 'logoBase64'))
                  ->setPaper('letter', 'portrait');

        return $pdf->stream('Carta_Recomendacion_' . \Illuminate\Support\Str::slug($miembro->nombres) . '.pdf');
    }

    public function cartaTraslado(Miembro $miembro)
    {
        $config = \App\Models\Configuracion::first();
        $pastor = $config ? ($config->pastor_general ?? 'Pastor General') : 'Pastor General';
        $iglesia = $config ? ($config->nombre_iglesia ?? 'AD Rey de Reyes') : 'AD Rey de Reyes';

        // Convertir logo a Base64 para DomPDF (usando logo optimizado)
        $path = public_path('imagen/Logo_AD_Rey_de_Reyes_optimized.png');
        if (!file_exists($path)) {
            $path = public_path('imagen/Logo AD Rey de Reyes.png');
        }
        $logoBase64 = '';
        if (file_exists($path)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        }

        $pdf = Pdf::loadView('miembros.pdf.carta_traslado', compact('miembro', 'pastor', 'iglesia', 'logoBase64'))
                  ->setPaper('letter', 'portrait');

        return $pdf->stream('Carta_Traslado_' . \Illuminate\Support\Str::slug($miembro->nombres) . '.pdf');
    }

    public function certificadoBautismo(Miembro $miembro)
    {
        $config = \App\Models\Configuracion::first();
        $pastor = $config ? ($config->pastor_general ?? 'Pastor General') : 'Pastor General';
        
        $pathLogo = public_path('imagen/Logo_AD_Rey_de_Reyes_optimized.png');
        if (!file_exists($pathLogo)) {
            $pathLogo = public_path('imagen/Logo AD Rey de Reyes.png');
        }
        $logoBase64 = '';
        if (file_exists($pathLogo)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogo));
        }

        $pathFirma = ($config && $config->firma_pastor) ? storage_path('app/public/config/' . $config->firma_pastor) : public_path('imagen/firma_pastor.png');
        $firmaBase64 = file_exists($pathFirma) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathFirma)) : null;

        $pathSello = ($config && $config->sello_iglesia) ? storage_path('app/public/config/' . $config->sello_iglesia) : public_path('imagen/sello_iglesia.png');
        $selloBase64 = file_exists($pathSello) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathSello)) : null;

        $pdf = Pdf::loadView('miembros.pdf.certificado', compact('miembro', 'pastor', 'logoBase64', 'firmaBase64', 'selloBase64'))
                  ->setPaper('letter', 'landscape');

        return $pdf->stream('Certificado_Bautismo_' . \Illuminate\Support\Str::slug($miembro->nombres) . '.pdf');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $ministerio = $request->input('ministerio');
        $etapa = $request->input('etapa');

        $miembros = Miembro::with(['familia', 'ministerios'])
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
                return $query->whereHas('ministerios', function ($q) use ($ministerio) {
                    $q->where('ministerios.id', $ministerio);
                });
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
        $ministerios = Ministerio::orderBy('nombre')->get();
        $etapas = ['Nuevo', 'En Discipulado', 'Asignado a Célula', 'Bautizado'];

        return view('miembros.index', compact('miembros', 'search', 'ministerio', 'etapa', 'ministerios', 'etapas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $ministerios = Ministerio::orderBy('nombre')->get();
        return view('miembros.create', compact('organizaciones', 'ministerios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'dpi' => 'required|numeric|digits:13|unique:miembros,dpi',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'estado_civil' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'nivel_academico' => 'nullable|string|max:100',
            'profesion' => 'nullable|string|max:100',
            'lugar_trabajo_estudio' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|numeric|digits:8',
            'es_lider' => 'nullable|boolean',
            'ministerios' => 'nullable|array',
            'ministerios.*' => 'exists:ministerios,id',
            'etapa_consolidacion' => 'required|string',
            'fecha_integracion' => 'nullable|date',
            'fecha_bautismo' => 'nullable|date',
            'familia_id' => 'nullable|exists:familias,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organizaciones' => 'nullable|array',
            'organizaciones.*' => 'exists:organizaciones,id',
        ]);

        $data = $request->except(['foto', 'organizaciones', 'ministerios']);
        $data['estado'] = true; // Miembro activo por defecto
        $data['es_lider'] = $request->has('es_lider');

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('miembros', $filename, 'public');
            $data['foto'] = $filename;
        } else {
            $data['foto'] = 'default_avatar.png';
        }

        if ($request->filled('familia_id')) {
            $familia = \App\Models\Familia::find($request->familia_id);
            if ($familia) {
                // Buscar el miembro con el código más alto DENTRO de esta familia
                $ultimoMiembro = Miembro::where('familia_id', $familia->id)
                    ->orderBy('codigo_miembro', 'desc')
                    ->first();

                if ($ultimoMiembro && $ultimoMiembro->codigo_miembro) {
                    // Extraer los últimos 2 dígitos del código del miembro
                    $ultimoCorrelativo = intval(substr($ultimoMiembro->codigo_miembro, -2));
                    $nuevoCorrelativo = $ultimoCorrelativo + 1;
                } else {
                    // Es el primer miembro de esta familia
                    $nuevoCorrelativo = 1;
                }

                // Concatenar: Código de Familia (3) + Correlativo de Miembro (2)
                $codigo_miembro = $familia->codigo_familia . str_pad($nuevoCorrelativo, 2, '0', STR_PAD_LEFT);

                // Asignar el código generado antes de guardar
                $data['codigo_miembro'] = $codigo_miembro;
            }
        }

        $miembro = Miembro::create($data);
        $miembro->ministerios()->sync($request->ministerios ?? []);

        $organizaciones = $request->input('organizaciones', []);
        $syncData = [];
        foreach ($organizaciones as $orgId) {
            $syncData[$orgId] = [
                'puesto' => 'Miembro',
                'fecha_asignacion' => now()->format('Y-m-d'),
                'estado' => true
            ];
        }
        $miembro->organizaciones()->sync($syncData);

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
        $organizaciones = Organizacion::orderBy('nombre')->get();
        $ministerios = Ministerio::orderBy('nombre')->get();
        return view('miembros.edit', compact('miembro', 'organizaciones', 'ministerios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Miembro $miembro)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'dpi' => 'required|numeric|digits:13|unique:miembros,dpi,' . $miembro->id,
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'estado_civil' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'nivel_academico' => 'nullable|string|max:100',
            'profesion' => 'nullable|string|max:100',
            'lugar_trabajo_estudio' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|numeric|digits:8',
            'es_lider' => 'nullable|boolean',
            'ministerios' => 'nullable|array',
            'ministerios.*' => 'exists:ministerios,id',
            'etapa_consolidacion' => 'required|string',
            'fecha_integracion' => 'nullable|date',
            'fecha_bautismo' => 'nullable|date',
            'familia_id' => 'nullable|exists:familias,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organizaciones' => 'nullable|array',
            'organizaciones.*' => 'exists:organizaciones,id',
        ]);

        $data = $request->except(['foto', 'organizaciones', 'ministerios']);
        $data['es_lider'] = $request->has('es_lider');

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
        $miembro->ministerios()->sync($request->ministerios ?? []);

        $organizaciones = $request->input('organizaciones', []);
        $syncData = [];
        $currentOrgs = $miembro->organizaciones->keyBy('id');
        
        foreach ($organizaciones as $orgId) {
            if ($currentOrgs->has($orgId)) {
                $pivot = $currentOrgs->get($orgId)->pivot;
                $syncData[$orgId] = [
                    'puesto' => $pivot->puesto ?? 'Miembro',
                    'fecha_asignacion' => $pivot->fecha_asignacion ? ($pivot->fecha_asignacion instanceof \Carbon\Carbon ? $pivot->fecha_asignacion->format('Y-m-d') : $pivot->fecha_asignacion) : now()->format('Y-m-d'),
                    'estado' => $pivot->estado ?? true
                ];
            } else {
                $syncData[$orgId] = [
                    'puesto' => 'Miembro',
                    'fecha_asignacion' => now()->format('Y-m-d'),
                    'estado' => true
                ];
            }
        }
        $miembro->organizaciones()->sync($syncData);

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
