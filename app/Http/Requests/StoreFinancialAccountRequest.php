<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancialAccountRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:financial_accounts,name',
            'initial_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe una caja con este nombre.',
            'initial_balance.min' => 'El saldo inicial no puede ser negativo.',
        ];
    }
}
