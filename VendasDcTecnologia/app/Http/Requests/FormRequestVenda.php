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
       
    // Definir regras de validação dependendo do método HTTP (POST ou PUT)
    $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'valorTotal' => 'required|numeric|min:0',
    ];

    // Adiciona regras específicas para métodos POST e PUT
    if ($this->isMethod('post') || $this->isMethod('put')) {
        $rules['numeroVenda'] = 'required|integer';
        $rules['produtos'] = 'required|array';
        $rules['produtos.*.produto_id'] = 'required|exists:produtos,id';
        $rules['produtos.*.valorParcela'] = 'required|numeric|min:0';
        $rules['produtos.*.dataVencimento'] = 'required|date';
    }

    return $rules;

    }

    public function messages()
    {
        return [
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'valorTotal.required' => 'O valor total é obrigatório.',
            'valorTotal.numeric' => 'O valor total deve ser um número.',
            'produtos.required' => 'Você deve adicionar pelo menos um produto.',
            'produtos.array' => 'Os produtos devem ser um array.',
            'produtos.*.produto_id.required' => 'O campo produto é obrigatório.',
            'produtos.*.produto_id.exists' => 'O produto selecionado não existe.',
            'produtos.*.valorParcela.required' => 'O valor da parcela é obrigatório.',
            'produtos.*.valorParcela.numeric' => 'O valor da parcela deve ser um número.',
            'produtos.*.dataVencimento.required' => 'A data de vencimento é obrigatória.',
            'produtos.*.dataVencimento.date' => 'A data de vencimento deve ser uma data válida.',
        ];
    }
}
