<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Eleccion;

class StoreCandidatoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Asumimos que el RBAC middleware protege la ruta, pero agregamos una capa extra
        return $this->user()->can('gestionar-elecciones');
    }

    public function rules(): array
    {
        return [
            'eleccion_id' => 'required|exists:elecciones,id',
            'puesto_postulado' => 'required|string|max:100',
            'miembro_id' => [
                'required',
                // 1. Validar que exista en la membresía general
                Rule::exists('miembros', 'id'),
                
                // 2. Validar que el miembro pertenezca activamente a la organización de esta elección
                function ($attribute, $value, $fail) {
                    $eleccion = Eleccion::find($this->eleccion_id);
                    if (!$eleccion) return;

                    $pertenece = DB::table('miembro_organizacion')
                        ->where('miembro_id', $value)
                        ->where('organizacion_id', $eleccion->organizacion_id)
                        ->where('estado', 'activo')
                        ->exists();
                        
                    if (!$pertenece) {
                        $fail('El miembro seleccionado no pertenece al padrón activo de esta organización.');
                    }
                },

                // 3. Validar que no esté postulado ya en esta misma elección
                Rule::unique('candidatos')->where(function ($query) {
                    return $query->where('eleccion_id', $this->eleccion_id);
                })
            ],
        ];
    }
}
