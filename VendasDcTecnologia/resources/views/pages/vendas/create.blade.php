@extends('index')

@section('content')
    <form class="form" method="POST" action="{{ route('cadastrar.venda') }}">
        @csrf
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Cadastrar nova Venda</h1>
        </div>

        <div class="mb-3">
            <label class="form-label">Numeração da Venda</label>
            <input type="text" disabled value="{{ $findNumeracao }}" class="form-control" name="numeroVenda">
        </div>

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select class="form-select" name="cliente_id" required>
                <option value="">Selecione um cliente</option>
                @foreach ($findCliente as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Produto</label>
            <select class="form-select" id="produto_select">
                <option selected>Clique para selecionar</option>
                @foreach ($findProduto as $produto)
                    <option value="{{ $produto->id }}" data-valor="{{ $produto->valor }}">
                        {{ $produto->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="button" id="adicionar_produto" class="btn btn-primary mb-3">Adicionar Produto</button>

        <div class="mb-3">
            <label class="form-label">Produtos Selecionados</label>
            <ul id="lista_produtos"></ul>
        </div>

        <div class="mb-3">
            <label class="form-label">Total</label>
            <input type="text" class="form-control" id="valor_total" name="valorTotal" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Forma de Pagamento</label>
            <select class="form-select" id="forma_pagamento" name="forma_pagamento">
                <option value="avista" selected>A vista</option>
                <option value="parcelado">Parcelado</option>
            </select>
        </div>

        <div class="mb-3" id="parcelamento_section" style="display: none;">
            <label class="form-label">Quantidade de Parcelas</label>
            <input type="number" class="form-control" id="quantidade_parcelas" name="quantidade_parcelas" min="1"
                value="1">
            <button type="button" id="gerar_parcelas" class="btn btn-primary mt-3 float-end">Gerar Parcelas</button>
        </div>

        <div class="mb-3" id="parcelas_section" style="display: none;">
            <label class="form-label">Parcelas</label>
            <ul id="lista_parcelas"></ul>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                let total = 0;

                // Adicionar Produto à Lista
                $('#adicionar_produto').click(function() {
                    let produtoSelecionado = $('#produto_select').find(':selected');
                    let valor = parseFloat(produtoSelecionado.data('valor'));
                    let nomeProduto = produtoSelecionado.text();

                    if (produtoSelecionado.val() !== "Clique para selecionar") {
                        let itemLista = $('<li>' + nomeProduto + ' - R$' + valor.toFixed(2) +
                            ' <button type="button" class="btn btn-danger btn-sm remover_produto float-end">Remover</button></li>'
                        );

                        $('#lista_produtos').append(itemLista);

                        total += valor;
                        $('#valor_total').val(total.toFixed(2));

                        itemLista.find('.remover_produto').click(function() {
                            total -= valor;
                            $('#valor_total').val(total.toFixed(2));
                            itemLista.remove();
                        });
                    }

                    $('#produto_select').val("Clique para selecionar");
                });

                // Mostrar/Ocultar Seção de Parcelamento
                $('#forma_pagamento').change(function() {
                    if ($(this).val() === 'parcelado') {
                        $('#parcelamento_section').show();
                    } else {
                        $('#parcelamento_section').hide();
                        $('#parcelas_section').hide();
                    }
                });

                // Gerar Parcelas
                $('#gerar_parcelas').click(function() {
                    let quantidadeParcelas = parseInt($('#quantidade_parcelas').val());
                    let valorParcela = (total / quantidadeParcelas).toFixed(2);

                    $('#lista_parcelas').empty();
                    for (let i = 1; i <= quantidadeParcelas; i++) {
                        let parcelaHTML = `
                        <li>
                            Parcela ${i}: 
                            <input type="number" class="form-control parcela_valor" name="parcelas[${i}][valor]" value="${valorParcela}" step="0.01" required>
                            <input type="date" class="form-control parcela_data" name="parcelas[${i}][data_vencimento]" required>
                        </li>`;
                        $('#lista_parcelas').append(parcelaHTML);
                    }
                    $('#parcelas_section').show();

                    $('.parcela_valor').on('input', function() {
                        let valorTotalAtualizado = total;
                        let valorAtual = parseFloat($(this).val());

                        let restante = $('.parcela_valor').not(this).length;
                        let novaParcela = ((valorTotalAtualizado - valorAtual) / restante).toFixed(2);

                        $('.parcela_valor').not(this).each(function() {
                            $(this).val(novaParcela);
                        });
                    });
                });
            });
        </script>

        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
@endsection
