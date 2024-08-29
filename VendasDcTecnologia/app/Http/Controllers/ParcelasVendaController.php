<?php

namespace App\Http\Controllers;

use App\Models\ParcelasVenda;
use App\Models\Venda;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ParcelasVendaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'venda_id' => 'required|exists:vendas,id',
            'parcelas' => 'required|array|min:1',
            'parcelas.*.valor' => 'required|numeric|min:0',
            'parcelas.*.data_vencimento' => 'required|date'
        ]);

        $venda = Venda::find($request->venda_id);

        foreach ($request->parcelas as $parcela) {
            ParcelasVenda::create([
                'venda_id' => $venda->id,
                'valor' => $parcela['valor'],
                'data_vencimento' => $parcela['data_vencimento']
            ]);
        }

        Toastr::success('Parcelas adicionadas com sucesso.');
        return redirect()->route('vendas.index');
    }
}
