<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CertificadoPresentacion;
use App\Models\Miembro;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Configuracion;

class CertificadoPresentacionController extends Controller
{
    public function index()
    {
        $certificados = CertificadoPresentacion::with(['padre', 'madre'])->paginate(15);
        return view('certificados.presentacion.index', compact('certificados'));
    }

    public function create()
    {
        $miembros = Miembro::orderBy('nombres')->get();
        return view('certificados.presentacion.create', compact('miembros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nino_nombre' => 'required|string|max:255',
            'nino_fecha_nacimiento' => 'nullable|date',
            'lugar_nacimiento' => 'nullable|string|max:255',
            'padre_id' => 'nullable|exists:miembros,id',
            'madre_id' => 'nullable|exists:miembros,id',
            'fecha_presentacion' => 'required|date',
            'pastor_oficiante' => 'nullable|string|max:255',
        ]);

        CertificadoPresentacion::create($request->all());

        return redirect()->route('presentacion.index')->with('success', 'Certificado guardado exitosamente.');
    }

    public function destroy(CertificadoPresentacion $presentacion)
    {
        $presentacion->delete();
        return redirect()->route('presentacion.index')->with('success', 'Certificado eliminado.');
    }

    public function pdf(CertificadoPresentacion $presentacion)
    {
        $config = Configuracion::first();
        $pastor = $presentacion->pastor_oficiante ?: ($config ? ($config->pastor_general ?? 'Pastor General') : 'Pastor General');
        $iglesia = $config ? ($config->nombre_iglesia ?? 'AD Rey de Reyes') : 'AD Rey de Reyes';

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

        $pdf = Pdf::loadView('certificados.presentacion.pdf', compact('presentacion', 'pastor', 'iglesia', 'logoBase64', 'firmaBase64', 'selloBase64'))
                  ->setPaper('letter', 'landscape');

        return $pdf->stream('Certificado_Presentacion_' . \Illuminate\Support\Str::slug($presentacion->nino_nombre) . '.pdf');
    }
}
