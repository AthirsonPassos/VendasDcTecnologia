<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Parcela;
use App\Models\Cliente;
use App\Models\Produto;
use Illuminate\Http\Request;
use App\Http\Requests\FormRequestVenda;
use Brian2694\Toastr\Facades\Toastr;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;
use PhpParser\Node\Stmt\Else_;

class VendaController extends Controller
{
    protected $venda;
    public function __construct(Venda $venda)  
    {
        $this->venda = $venda;
    }
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

    public function store(FormRequestVenda $request)
    {
        DB::transaction(function() use ($request) {
            $venda = Venda::create([
                'cliente_id' => $request->cliente_id,
                'valor_total' => $request->valorTotal,
                'a_vista' => $request->forma_pagamento === 'avista'
            ]);

            if ($request->forma_pagamento === 'parcelado') {
                foreach ($request->parcelas as $parcela) {
                    Parcela::create([
                        'venda_id' => $venda->id,
                        'valor' => $parcela['valor'],
                        'data_vencimento' => $parcela['data_vencimento'],
                    ]);
                }
            }
        });
        Toastr::success('Dados gravados com sucesso.');
        return redirect()->route('venda.index');
    }

    public function atualizarVenda($id)
    {
        $venda = Venda::with('parcelas')->findOrFail($id);
        $clientes = Cliente::all();
        return view('pages.vendas.atualiza', compact('venda', 'clientes'));
    }

    public function update(FormRequestVenda $request, $id)
    {
        DB::transaction(function() use ($request, $id) {
            $venda = Venda::findOrFail($id);
    
            // Atualiza os dados da venda
            $venda->update([
                'cliente_id' => $request->cliente_id,
                'valor_total' => $request->valorTotal,
                'a_vista' => $request->forma_pagamento === 'avista'
            ]);
    
            // Remove parcelas existentes se a forma de pagamento for alterada
            if ($request->forma_pagamento === 'parcelado') {
                $venda->parcelas()->delete();
    
                // Verifica se $request->parcelas está definido e não é nulo
                if ($request->has('parcelas') && is_array($request->parcelas)) {
                    foreach ($request->parcelas as $parcela) {
                        // Verifica se cada parcela tem os dados necessários
                        if (isset($parcela['valor']) && isset($parcela['data_vencimento'])) {
                            Parcela::create([
                                'venda_id' => $venda->id,
                                'valor' => $parcela['valor'],
                                'data_vencimento' => $parcela['data_vencimento'],
                            ]);
                        }
                    }
                }
            } else {
                // Se a forma de pagamento for alterada para 'à vista', remove parcelas
                $venda->parcelas()->delete();
            }
        });
    
        Toastr::success('Dados atualizados com sucesso.');
        return redirect()->route('venda.index');
    }
}
