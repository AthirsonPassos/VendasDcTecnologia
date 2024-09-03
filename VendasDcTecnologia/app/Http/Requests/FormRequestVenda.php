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

    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:clientes,id',
            'valor_total' => 'nullable|numeric',
            'forma_pagamento' => 'nullable|in:avista,parcelado',
            'quantidade_parcelas' => 'nullable|integer|min:1',
            'parcelas.*.valor' => 'nullable|numeric',
            'parcelas.*.data_vencimento' => 'nullable|date',
            'produtos.*' => 'nullable|numeric|exists:produtos,id'
        ];
    }

    public function messages()
    {
        return [
            'cliente_id.required' => 'O cliente é obrigatório.',
            'valorTotal.required' => 'O valor total é obrigatório.',
            'valorTotal.numeric' => 'O valor total deve ser um número.',
            'produtos.required' => 'Pelo menos um produto deve ser selecionado.',
            'produtos.*.exists' => 'Um ou mais produtos selecionados são inválidos.',
            'forma_pagamento.required' => 'A forma de pagamento é obrigatória.',
            'quantidade_parcelas.required_if' => 'A quantidade de parcelas é obrigatória para pagamento parcelado.',
            'quantidade_parcelas.integer' => 'A quantidade de parcelas deve ser um número inteiro.',
            'parcelas.required_if' => 'As parcelas são obrigatórias para pagamento parcelado.',
            'parcelas.*.valor.required_with' => 'O valor da parcela é obrigatório.',
            'parcelas.*.valor.numeric' => 'O valor da parcela deve ser um número.',
            'parcelas.*.data_vencimento.required_with' => 'A data de vencimento é obrigatória.',
            'parcelas.*.data_vencimento.date' => 'A data de vencimento deve ser uma data válida.',
            'parcelas.*.data_vencimento.after_or_equal' => 'A data de vencimento deve ser igual ou posterior à data atual.',
        ];
    }
}
