<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequestVenda;
use App\Models\Cliente;
use App\Models\Componentes;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaProduto;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendaController extends Controller
{
    public function index()
    {
        $vendas = Venda::with('cliente')->get();
        return view('pages.vendas.paginacao', compact('vendas'));
        
    }

    public function cadastrarVendas()
    {
        $clientes = Cliente::all();
        $produtos = Produto::all();
        return view('pages.vendas.create', compact('clientes', 'produtos'));
    }

    public function store(Request $request)
    {
        DB::transaction(function() use ($request) {
            // Cria a venda com dados formatados
            $venda = Venda::create([
                'cliente_id' => $request->cliente_id,
                'valor_total' => $request->valorTotal, // Remover number_format se estiver armazenando como decimal
                'forma_pagamento' => $request->forma_pagamento,
                'is_avista' => $request->forma_pagamento === 'avista'
            ]);
    
            // Associa os produtos à venda utilizando o modelo VendaProduto
            foreach ($request->produtos as $produtoId) {
                // Obtém o valor do produto
                $produto = Produto::find($produtoId);
    
                if ($produto) {
                    VendaProduto::create([
                        'venda_id' => $venda->id,
                        'produto_id' => $produtoId,
                        'valor' => $produto->valor // Sem formatação adicional
                    ]);
                }
            }
    
            // Adiciona as parcelas se a forma de pagamento for parcelado
            if ($request->forma_pagamento === 'parcelado' && isset($request->parcelas)) {
                foreach ($request->parcelas as $parcela) {
                    Parcela::create([
                        'venda_id' => $venda->id,
                        'valor' => $parcela['valor'], // Sem formatação adicional
                        'data_vencimento' => $parcela['data_vencimento']
                    ]);
                }
            }
        });
    
        return redirect()->route('venda.index')->with('success', 'Venda criada com sucesso!');
    }
    
    public function atualizarVenda($id)
    {
        $venda = Venda::with('produtos', 'parcelas')->findOrFail($id);
        $clientes = Cliente::all();
        $produtos = Produto::all();
        return view('pages.vendas.atualiza', compact('venda', 'clientes', 'produtos'));
    }
    
    public function update(Request $request, $id)
{

    // Validação dos dados
    $validatedData = $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'valor_total' => 'required|string',
        'forma_pagamento' => 'required|in:avista,parcelado',
        'quantidade_parcelas' => 'nullable|integer|min:0',
        'produtos' => 'required|array',
        'produtos.*' => 'exists:produtos,id',
        'parcelas.*.valor' => 'nullable|string',
        'parcelas.*.data_vencimento' => 'nullable|date',
    ]);
    
    $valorTotal = $request->input('valor_total');
    // Remove pontos usados como separadores de milhar
    $valorTotal = str_replace('.', '', $valorTotal);
    // Substitui a vírgula usada como separador decimal por ponto
    $valorTotal = str_replace(',', '.', $valorTotal);
    // Converte para float
    $valorTotal = floatval($valorTotal);

    // Atualiza a venda
    $venda = Venda::findOrFail($id);
    $venda->cliente_id = $request->input('cliente_id');
    $venda->valor_total = $valorTotal;
    $venda->is_avista = $request->input('forma_pagamento') === 'avista';
    $venda->save();

    // Atualiza os produtos da venda
    $venda->produtos()->sync($request->input('produtos'));

    // Atualiza as parcelas da venda
    if ($request->input('forma_pagamento') === 'parcelado') {
        $parcelas = $request->input('parcelas', []);
        foreach ($parcelas as $parcela) {
            if (is_array($parcela)) {
                $valorParcela = $parcela['valor'] ?? '0';
                 // Remove pontos usados como separadores de milhar
                $valorParcela = str_replace('.', '', $valorParcela);
                // Substitui a vírgula usada como separador decimal por ponto
                $valorParcela = str_replace(',', '.', $valorParcela);
                // Converte para float
                $valorParcela = floatval($valorParcela);
                $venda->parcelas()->updateOrCreate(
                    ['id' => $parcela['id'] ?? null],
                    [
                        'valor' => $valorParcela,
                        'data_vencimento' => $parcela['data_vencimento'] ?? null,
                    ]
                );
            }
        }
    } else {
        $venda->parcelas()->delete();
    }

    return redirect()->route('venda.index')->with('success', 'Venda atualizada com sucesso!');
}
    public function delete(Request $request)
    {
        $venda = Venda::findOrFail($request->id);
        $venda->delete();

        return redirect()->route('venda.index')->with('success', 'Venda excluída com sucesso!');
    }
}
