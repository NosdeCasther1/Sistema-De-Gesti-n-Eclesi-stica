<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTesoreriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in(['income', 'expense']),
            ],
            'account_id' => [
                'required',
                Rule::exists('financial_accounts', 'id')->where(function ($query) {
                    return $query->where('is_active', 1)->whereNull('deleted_at');
                }),
            ],
            'categoria_id' => [
                'required',
                Rule::exists('financial_categories', 'id')->where(function ($query) {
                    return $query->where('type', $this->input('type'))->where('is_active', 1)->whereNull('deleted_at');
                }),
            ],
            'monto' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'fecha' => [
                'required',
                'date',
            ],
            'descripcion' => [
                'required',
                'string',
                'max:255',
            ],
            'reference_number' => 'nullable|string|max:50',
            'proof_path' => [
                'nullable',
                'file',
                'mimes:jpg,png,pdf',
                'max:2048',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de transacción es obligatorio.',
            'type.in' => 'El tipo de transacción seleccionado no es válido (debe ser ingreso o gasto).',
            
            'account_id.required' => 'Debes seleccionar una cuenta o fondo ministerial.',
            'account_id.exists' => 'La cuenta seleccionada no existe o se encuentra inactiva en el sistema.',
            
            'categoria_id.required' => 'Debes seleccionar una categoría financiera.',
            'categoria_id.exists' => 'La categoría seleccionada no existe, está inactiva o no coincide con el tipo de movimiento.',
            
            'monto.required' => 'El monto de la transacción es obligatorio.',
            'monto.numeric' => 'El monto debe ser un valor numérico.',
            'monto.min' => 'El monto debe ser mayor a Q0.00 (mínimo Q0.01). No se permiten valores en cero o negativos.',
            
            'fecha.required' => 'La fecha de la transacción es obligatoria.',
            'fecha.date' => 'La fecha ingresada no tiene un formato válido.',
            
            'descripcion.required' => 'La descripción o concepto es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',
            
            'proof_path.file' => 'El comprobante debe ser un archivo válido.',
            'proof_path.mimes' => 'El comprobante debe ser un archivo de tipo: jpg, png o pdf.',
            'proof_path.max' => 'El tamaño del comprobante no debe superar los 2 MB (2048 KB).',
        ];
    }
}
