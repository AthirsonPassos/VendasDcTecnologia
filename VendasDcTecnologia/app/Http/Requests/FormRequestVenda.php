<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormRequestVenda extends FormRequest
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
        $rules = [
            'cliente_id' => 'required|exists:clientes,id',
            'valorTotal' => 'required|numeric|min:0',
            'forma_pagamento' => 'required|in:avista,parcelado',
        ];

        if ($this->forma_pagamento === 'parcelado') {
            $rules['parcelas'] = 'required|array';
            $rules['parcelas.*.valor'] = 'required|numeric|min:0';
            $rules['parcelas.*.data_vencimento'] = 'required|date';
        }

        return $rules;
    }
}
