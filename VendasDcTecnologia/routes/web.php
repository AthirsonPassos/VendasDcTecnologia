<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::prefix('produtos')->group(function () {
        Route::get('/',[ProdutosController::class,'index'])->name('produto.index');
        //Cadastro de Produtos
        Route::get('/cadastrarProduto', [ProdutosController::class, 'cadastrarProduto'])->name('cadastrar.produto');
        Route::post('/cadastrarProduto', [ProdutosController::class, 'cadastrarProduto'])->name('cadastrar.produto');
        //Atualizar Produtos
        Route::get('/atualizarProduto/{id}', [ProdutosController::class, 'atualizarProduto'])->name('atualizar.produto');
        Route::put('/atualizarProduto/{id}', [ProdutosController::class, 'atualizarProduto'])->name('atualizar.produto');
        //Excluir Produtos
        Route::delete('/delete',[ProdutosController::class,'delete'])->name('produto.delete');
});

Route::prefix('clientes')->group(function () {
    Route::get('/', [ClientesController::class, 'index'])->name('clientes.index');
    //Cadastro de Clientes
    Route::get('/cadastrarCliente', [ClientesController::class, 'cadastrarCliente'])->name('cadastrar.cliente');
    Route::post('/cadastrarCliente', [ClientesController::class, 'cadastrarCliente'])->name('cadastrar.cliente');
    //Atualizar Clientes
    Route::get('/atualizarCliente/{id}', [ClientesController::class, 'atualizarCliente'])->name('atualizar.cliente');
    Route::put('/atualizarCliente/{id}', [ClientesController::class, 'atualizarCliente'])->name('atualizar.cliente');
    //Excluir Clientes
    Route::delete('/delete', [ClientesController::class, 'delete'])->name('cliente.delete');
});

Route::prefix('vendas')->group(function () {
    // Listar todas as vendas
    Route::get('/', [VendaController::class, 'index'])->name('venda.index');    
    //Cadastro de Vendas
    Route::get('/cadastrar', [VendaController::class, 'cadastrarVendas'])->name('cadastrar.venda');
    Route::post('/cadastrar', [VendaController::class, 'store'])->name('cadastrar.venda');    
     //Atualizar Vendas
    Route::get('/editar/{id}', [VendaController::class, 'atualizarVenda'])->name('atualiza.venda');    
    Route::put('/editar/{id}', [VendaController::class, 'update'])->name('atualiza.venda');    
    //Excluir Vendas
    Route::delete('/deletar/{id}', [VendaController::class, 'delete'])->name('venda.delete');    
    
    //Route::get('/enviaComprovantePorEmail/{id}', [VendaController::class, 'enviaComprovantePorEmail'])->name('enviaComprovantePorEmail.venda');

});