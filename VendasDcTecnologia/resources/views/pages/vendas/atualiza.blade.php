@extends('index')

@section('content')
    <form class="form" method="POST" action="{{ route('atualiza.venda', $venda->id) }}">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Editar Venda</h1>
        </div>

        <div class="mb-3">
            <label class="form-label" for="cliente_id">Cliente</label>
            <select class="form-control @error('cliente_id') is-invalid @enderror" name="cliente_id" id="cliente_id">
                <option value="">Selecione um cliente</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $cliente->id == $venda->cliente_id ? 'selected' : '' }}>
                        {{ $cliente->nome }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('cliente_id'))
                <div class="invalid-feedback">{{ $errors->first('cliente_id') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label" for="valor_total">Valor Total</label>
            <input type="text" id="valor_total" name="valor_total" class="form-control" readonly
                value="{{ number_format($venda->valor_total, 2, ',', '.') }}">
        </div>

        <div class="mb-3">
            <label class="form-label" for="forma_pagamento">Forma de Pagamento</label>
            <select class="form-control" id="forma_pagamento" name="forma_pagamento">
                <option value="avista" {{ $venda->is_avista ? 'selected' : '' }}>À vista</option>
                <option value="parcelado" {{ !$venda->is_avista ? 'selected' : '' }}>Parcelado</option>
            </select>
        </div>

        <div class="mb-3" id="parcelamento_section" style="{{ !$venda->is_avista ? '' : 'display: none;' }}">
            <label class="form-label" for="quantidade_parcelas">Quantidade de Parcelas</label>
            <input type="number" id="quantidade_parcelas" class="form-control" name="quantidade_parcelas"
                value="{{ count($venda->parcelas) }}">
            <button type="button" id="gerar_parcelas" class="btn btn-primary mt-2">Gerar Parcelas</button>

            <div id="parcelas_section" style="{{ !$venda->is_avista ? '' : 'display: none;' }}">
                <label class="form-label">Parcelas</label>
                <ul id="lista_parcelas">
                    @foreach ($venda->parcelas as $index => $parcela)
                        <li>
                            Valor: <input type="text" name="parcelas[{{ $index }}][valor]"
                                id="parcela_{{ $index }}_valor" class="form-control parcela-valor"
                                value="{{ number_format($parcela->valor, 2, ',', '.') }}">
                            - Data de Vencimento: <input type="date"
                                name="parcelas[{{ $index }}][data_vencimento]"
                                id="parcela_{{ $index }}_data_vencimento" class="form-control parcela-data"
                                value="{{ $parcela->data_vencimento }}">
                            <button type="button" class="btn btn-danger btn-sm btn-remover-parcela"
                                data-parcela-id="{{ $index }}">Remover</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="produto_select">Produto</label>
            <select class="form-control" id="produto_select" name="produto_select">
                <option value="">Selecione um produto</option>
                @foreach ($produtos as $produto)
                    <option value="{{ $produto->id }}" data-valor="{{ $produto->valor }}">{{ $produto->nome }}</option>
                @endforeach
            </select>
            <button type="button" id="adicionar_produto" class="btn btn-primary mt-2">Adicionar Produto</button>
        </div>

        <div class="mb-3">
            <label class="form-label">Produtos Selecionados</label>
            <ul id="lista_produtos">
                @foreach ($venda->produtos as $produto)
                    <li>
                        {{ $produto->nome }} - R$ {{ number_format($produto->pivot->valor, 2, ',', '.') }}
                        <button type="button" class="btn btn-danger btn-sm btn-remover-produto"
                            data-produto-id="{{ $produto->id }}" data-produto-valor="{{ $produto->pivot->valor }}">
                            Remover
                        </button>
                        <input type="hidden" name="produtos[]" value="{{ $produto->id }}">
                    </li>
                @endforeach
            </ul>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
    </form>

    <!-- Inclusão do jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Código JavaScript -->
    <script>
        $(document).ready(function() {

            function atualizarValorTotal() {
                var total = 0;
                $('#lista_produtos li').each(function() {
                    var valor = $(this).find('button').data('produto-valor');
                    if (typeof valor === 'string') {
                        valor = parseFloat(valor.replace(',', '.'));
                        if (!isNaN(valor)) {
                            total += valor;
                        }
                    }
                });
                $('#valor_total').val(total.toFixed(2).replace('.', ','));
            }

            $('#adicionar_produto').click(function() {
                let produtoSelecionado = $('#produto_select').find(':selected');
                let valor = parseFloat(produtoSelecionado.data('valor'));
                let nomeProduto = produtoSelecionado.text();
                let produtoId = produtoSelecionado.val();

                if (produtoId !== "") {
                    let itemLista = $('<li>' + nomeProduto + ' - R$ ' + valor.toFixed(2).replace('.', ',') +
                        ' <button type="button" class="btn btn-danger btn-sm btn-remover-produto" ' +
                        'data-produto-id="' + produtoId + '" data-produto-valor="' + valor.toFixed(2) +
                        '">Remover</button>' +
                        '<input type="hidden" name="produtos[]" value="' + produtoId + '">' +
                        '</li>');

                    $('#lista_produtos').append(itemLista);
                    atualizarValorTotal();
                }

                $('#produto_select').val("");
            });

            $(document).on('click', '.btn-remover-produto', function() {
                $(this).parent().remove();
                atualizarValorTotal();
            });

            $('#forma_pagamento').change(function() {
                if ($(this).val() === 'parcelado') {
                    $('#parcelamento_section').show();
                } else {
                    $('#parcelamento_section').hide();
                    $('#parcelas_section').hide();
                }
            });

            $('#gerar_parcelas').click(function() {
                var quantidadeParcelas = parseInt($('#quantidade_parcelas').val());
                var valorTotal = parseFloat($('#valor_total').val().replace(',', '.'));

                if (quantidadeParcelas >= 1 && valorTotal > 0) {
                    var valorParcela = valorTotal / quantidadeParcelas;
                    var listaParcelas = $('#lista_parcelas');
                    listaParcelas.empty();

                    for (var i = 0; i < quantidadeParcelas; i++) {
                        var item = `<li>
                            Valor: <input type="text" name="parcelas[${i}][valor]" id="parcela_${i}_valor"
                            class="form-control parcela-valor" value="${valorParcela.toFixed(2).replace('.', ',')}" step="0.01" required>
                            - Data de Vencimento: <input type="date" name="parcelas[${i}][data_vencimento]" id="parcela_${i}_data_vencimento"
                            class="form-control parcela-data" required>
                            <button type="button" class="btn btn-danger btn-sm btn-remover-parcela" data-parcela-id="${i}">Remover</button>
                        </li>`;
                        listaParcelas.append(item);
                    }

                    $('#parcelas_section').show();
                    atualizarValorTotal(); // Atualiza o valor total após gerar parcelas
                } else {
                    alert('Por favor, insira uma quantidade de parcelas válida e verifique o valor total.');
                }
            });

            $(document).on('click', '.btn-remover-parcela', function() {
                $(this).parent().remove();
                atualizarValorTotal();
            });

            $('#quantidade_parcelas').on('input', function() {
                if ($('#forma_pagamento').val() === 'parcelado') {
                    $('#gerar_parcelas').click();
                }
            });
        });
    </script>
@endsection
