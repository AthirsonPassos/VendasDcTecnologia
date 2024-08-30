@extends('index')

@section('content')
    <form class="form" method="POST" action="{{ route('cadastrar.venda') }}">
        @csrf
        <div class="form-group">
            <label>Cliente</label>
            <select class="form-control" name="cliente_id" required>
                <option value="">Selecione um cliente</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Produto</label>
            <select class="form-control" id="produto_select">
                <option value="">Selecione um produto</option>
                @foreach ($produtos as $produto)
                    <option value="{{ $produto->id }}" data-valor="{{ $produto->valor }}">{{ $produto->nome }}</option>
                @endforeach
            </select>
        </div>

        <button type="button" id="adicionar_produto" class="btn btn-primary">Adicionar Produto</button>

        <div class="form-group mt-3">
            <label>Produtos Selecionados</label>
            <ul id="lista_produtos"></ul>
        </div>

        <div class="form-group">
            <label>Valor Total</label>
            <input type="text" id="valor_total" name="valorTotal" class="form-control" readonly value="0.00">
        </div>

        <div class="form-group">
            <label>Forma de Pagamento</label>
            <select class="form-control" id="forma_pagamento" name="forma_pagamento">
                <option value="avista">À vista</option>
                <option value="parcelado">Parcelado</option>
            </select>
        </div>

        <div class="form-group" id="parcelamento_section" style="display: none;">
            <label>Quantidade de Parcelas</label>
            <input type="number" id="quantidade_parcelas" class="form-control" name="quantidade_parcelas" min="1">
            <button type="button" id="gerar_parcelas" class="btn btn-primary mt-2">Gerar Parcelas</button>

            <div id="parcelas_section" style="display: none;">
                <label>Parcelas</label>
                <ul id="lista_parcelas"></ul>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let total = 0;

            // Função para atualizar o valor total
            function atualizarValorTotal(valor) {
                total += valor;
                $('#valor_total').val(total.toFixed(2));
            }

            // Adicionar Produto à Lista
            $('#adicionar_produto').click(function() {
                let produtoSelecionado = $('#produto_select').find(':selected');
                let valor = parseFloat(produtoSelecionado.data('valor'));
                let nomeProduto = produtoSelecionado.text();
                let produtoId = produtoSelecionado.val();

                if (produtoId !== "") {
                    let itemLista = $('<li>' + nomeProduto + ' - R$' + valor.toFixed(2) +
                        ' <button type="button" class="btn btn-danger btn-sm remover_produto float-end">Remover</button>' +
                        '<input type="hidden" name="produtos[]" value="' + produtoId + '">' +
                        '</li>');

                    $('#lista_produtos').append(itemLista);

                    atualizarValorTotal(valor);

                    // Remover Produto da Lista
                    itemLista.find('.remover_produto').click(function() {
                        total -= valor;
                        $('#valor_total').val(total.toFixed(2));
                        itemLista.remove();
                    });
                }

                $('#produto_select').val("");
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
                    let novaParcela = restante > 0 ? ((valorTotalAtualizado - valorAtual) /
                        restante).toFixed(2) : valorAtual;

                    $('.parcela_valor').not(this).each(function() {
                        $(this).val(novaParcela);
                    });
                });
            });
        });
    </script>
@endsection
