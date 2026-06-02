<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CertificadoMatrimonio;
use App\Models\Miembro;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Configuracion;

class CertificadoMatrimonioController extends Controller
{
    public function index()
    {
        $certificados = CertificadoMatrimonio::with(['esposo', 'esposa'])->paginate(15);
        return view('certificados.matrimonio.index', compact('certificados'));
    }

    public function create()
    {
        $miembros = Miembro::orderBy('nombres')->get();
        return view('certificados.matrimonio.create', compact('miembros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'esposo_id' => 'required|exists:miembros,id',
            'esposa_id' => 'required|exists:miembros,id',
            'fecha_matrimonio' => 'required|date',
            'pastor_oficiante' => 'nullable|string|max:255',
            'testigo_1' => 'nullable|string|max:255',
            'testigo_2' => 'nullable|string|max:255',
        ]);

        CertificadoMatrimonio::create($request->all());

        return redirect()->route('matrimonio.index')->with('success', 'Certificado guardado exitosamente.');
    }

    public function destroy(CertificadoMatrimonio $matrimonio)
    {
        $matrimonio->delete();
        return redirect()->route('matrimonio.index')->with('success', 'Certificado eliminado.');
    }

    public function pdf(CertificadoMatrimonio $matrimonio)
    {
        $config = Configuracion::first();
        $pastor = $matrimonio->pastor_oficiante ?: ($config ? ($config->pastor_general ?? 'Pastor General') : 'Pastor General');
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

        $pdf = Pdf::loadView('certificados.matrimonio.pdf', compact('matrimonio', 'pastor', 'iglesia', 'logoBase64', 'firmaBase64', 'selloBase64'))
                  ->setPaper('letter', 'landscape');

        return $pdf->stream('Certificado_Matrimonio_' . \Illuminate\Support\Str::slug($matrimonio->esposo->nombres . '_y_' . $matrimonio->esposa->nombres) . '.pdf');
    }
}
