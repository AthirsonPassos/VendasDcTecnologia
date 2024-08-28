<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormResquestProduto;
use App\Models\Componentes;
use App\Models\Produto;
use Brian2694\Toastr\Facades\Toastr;
use Brian2694\Toastr\Toastr as ToastrToastr;
use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    protected $produto;
    public function __construct(Produto $produto)  
    {
        $this->produto = $produto;
    }
    public function index(Request $request) 
    {
        $pesquisar = $request->pesquisar;
        $findProduto = $this->produto->getProdutosPesquisarIndex(search: $pesquisar ?? '');

        return view('pages.produtos.paginacao', compact('findProduto'));
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $buscaRegistro = Produto::find($id);
        $buscaRegistro->delete();

        return response()->json(['success' => true]);
    }

    public function cadastrarProduto(FormResquestProduto $request)
    {
        if ($request->method() == "POST") {
            // cria os dados
            $data = $request->all();
            $componentes = new Componentes();
            $data['valor'] = $componentes->formatacaoMascaraDinheiroDecimal($data['valor']);
            Produto::create($data);

            Toastr::success('Dados gravados com sucesso.');
            return redirect()->route('produto.index');
        }
        // mostrar os dados
        return view('pages.produtos.create');
    }

    public function atualizarProduto(FormResquestProduto $request, $id)
    {
        if ($request->method() == "PUT") {
            // atualiza os dados
            $data = $request->all();
            $componentes = new Componentes();
            $data['valor'] = $componentes->formatacaoMascaraDinheiroDecimal($data['valor']);
            $buscaRegistro = Produto::find($id);
            $buscaRegistro->update($data);

            Toastr::success('Dados atualizados com sucesso.');
            return redirect()->route('produto.index');
        }
        $findProduto = Produto::where('id', '=', $id)->first();

        return view('pages.produtos.atualiza', compact('findProduto'));
    }
}
