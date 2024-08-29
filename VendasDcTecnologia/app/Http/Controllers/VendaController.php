<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequestVenda;
use App\Models\Cliente;
use App\Models\Componentes;
use App\Models\ParcelasVenda;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\ProdutoVenda;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('pesquisar', '');

        $findVenda = Venda::with(['produtos', 'cliente'])
            ->where(function ($query) use ($search) {
                if ($search) {
                    $query->where('numeroVenda', 'LIKE', "%{$search}%")
                          ->orWhereHas('produtos', function ($query) use ($search) {
                              $query->where('nome', 'LIKE', "%{$search}%");
                          })
                          ->orWhereHas('cliente', function ($query) use ($search) {
                              $query->where('nome', 'LIKE', "%{$search}%");
                          });
                }
            })
            ->paginate(10);

        return view('pages.vendas.paginacao', compact('findVenda'));
    }

    public function deletarVenda($id)
    {
        $venda = Venda::find($id);
        if ($venda) {
            $venda->delete();
            Toastr::success('Venda excluída com sucesso.');
        } else {
            Toastr::error('Venda não encontrada.');
        }
        return redirect()->route('vendas.index');
    }

    public function cadastrarVendas(Request $request)
    {
        $findNumeracao = Venda::max('numeroVenda') + 1;
        $findProduto = Produto::all();
        $findCliente = Cliente::all();

        if ($request->method() == "POST") {
            $data = $request->all();

            $validatedData = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'valorTotal' => 'required|numeric|min:0',
                'produtos' => 'required|array|min:1',
                'produtos.*' => 'exists:produtos,id', // Validação para cada produto selecionado
                'forma_pagamento' => 'required|in:avista,parcelado',
                'parcelas' => 'nullable|array',
                'parcelas.*.valorParcela' => 'nullable|numeric|min:0',
                'parcelas.*.dataVencimento' => 'nullable|date'
            ]);

            $componentes = new Componentes();
            $data['valorTotal'] = $componentes->formatacaoMascaraDinheiroDecimal($data['valorTotal']);

            // Criar a venda
            $venda = Venda::create([
                'numeroVenda' => $findNumeracao,
                'cliente_id' => $data['cliente_id'],
                'valorTotal' => $data['valorTotal'],
            ]);

            // Associar produtos à venda
            foreach ($data['produtos'] as $produto_id) {
                ProdutoVenda::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $produto_id
                ]);
            }

            // Salvar as parcelas, se houver
            if ($data['forma_pagamento'] === 'parcelado' && isset($data['parcelas'])) {
                foreach ($data['parcelas'] as $parcela) {
                    $venda->parcelas()->create([
                        'valor' => $parcela['valorParcela'],
                        'data_vencimento' => $parcela['dataVencimento'],
                    ]);
                }
            }

            Toastr::success('Dados gravados com sucesso.');
            return redirect()->route('vendas.index');
        }

        return view('pages.vendas.create', compact('findNumeracao', 'findProduto', 'findCliente'));
    }

    public function atualizarVenda(FormRequestVenda $request, $id)
    {
        if ($request->method() == "PUT") {
            $data = $request->all();

            $componentes = new Componentes();
            $data['valorTotal'] = $componentes->formatacaoMascaraDinheiroDecimal($data['valorTotal']);

            $venda = Venda::find($id);

            $venda->update([
                'cliente_id' => $data['cliente_id'],
                'valorTotal' => $data['valorTotal'],
            ]);

            // Atualizar produtos associados à venda
            ProdutoVenda::where('venda_id', $venda->id)->delete();
            foreach ($data['produtos'] as $produto_id) {
                ProdutoVenda::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $produto_id
                ]);
            }

            // Atualizar as parcelas, se houver
            if (isset($data['parcelas'])) {
                // Remover as parcelas antigas
                $venda->parcelas()->delete();

                // Adicionar as novas parcelas
                foreach ($data['parcelas'] as $parcela) {
                    ParcelasVenda::create([
                        'venda_id' => $venda->id,
                        'valor' => $parcela['valorParcela'],
                        'data_vencimento' => $parcela['dataVencimento'],
                    ]);
                }
            }

            Toastr::success('Dados atualizados com sucesso.');
            return redirect()->route('vendas.index');
        }

        $findVenda = Venda::where('id', $id)->first();
        return view('pages.vendas.atualiza', compact('findVenda'));
    }
}
