@extends('index')

@section('content')
    <form class="form" method="POST" action="{{ route('atualizar.venda', $venda->id) }}">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Editar Venda</h1>
        </div>

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select class="form-control @error('cliente_id') is-invalid @enderror" name="cliente_id">
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
            <label class="form-label">Valor Total</label>
            <input type="text" id="valor_total" name="valorTotal" class="form-control" readonly
                value="{{ number_format($venda->valor_total, 2, ',', '.') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Forma de Pagamento</label>
            <select class="form-control" id="forma_pagamento" name="forma_pagamento">
                <option value="avista" {{ $venda->a_vista ? 'selected' : '' }}>À vista</option>
                <option value="parcelado" {{ !$venda->a_vista ? 'selected' : '' }}>Parcelado</option>
            </select>
        </div>

        <div class="mb-3" id="parcelamento_section" style="{{ !$venda->a_vista ? '' : 'display: none;' }}">
            <label class="form-label">Quantidade de Parcelas</label>
            <input type="number" id="quantidade_parcelas" class="form-control" name="quantidade_parcelas" min="1"
                value="{{ count($venda->parcelas) }}">
            <button type="button" id="gerar_parcelas" class="btn btn-primary mt-2">Gerar Parcelas</button>

            <div id="parcelas_section" style="{{ !$venda->a_vista ? '' : 'display: none;' }}">
                <label class="form-label">Parcelas</label>
                <ul id="lista_parcelas">
                    @foreach ($venda->parcelas as $index => $parcela)
                        <li>
                            Valor: <input type="text" name="parcelas[{{ $index }}][valor]"
                                class="form-control parcela-valor"
                                value="{{ number_format($parcela->valor, 2, ',', '.') }}">
                            - Data de Vencimento: <input type="date"
                                name="parcelas[{{ $index }}][data_vencimento]" class="form-control parcela-data"
                                value="{{ $parcela->data_vencimento }}">
                            <button type="button" class="btn btn-danger btn-sm btn-remover-parcela"
                                data-parcela-id="{{ $index }}">Remover</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
    </form>

    <script>
        $(document).ready(function() {
            // Mostrar ou esconder a seção de parcelamento com base na forma de pagamento selecionada
            $('#forma_pagamento').change(function() {
                if ($(this).val() === 'parcelado') {
                    $('#parcelamento_section').show();
                } else {
                    $('#parcelamento_section').hide();
                }
            });

            // Gerar parcelas quando o botão for clicado
            $('#gerar_parcelas').click(function() {
                var quantidadeParcelas = $('#quantidade_parcelas').val();
                var valorTotal = parseFloat($('#valor_total').val().replace(',', '.'));

                if (quantidadeParcelas > 0 && valorTotal > 0) {
                    var valorParcela = valorTotal / quantidadeParcelas;
                    var listaParcelas = $('#lista_parcelas');
                    listaParcelas.empty();

                    for (var i = 0; i < quantidadeParcelas; i++) {
                        var item = `<li>
                                        Valor: <input type="text" name="parcelas[${i}][valor]" class="form-control parcela-valor" value="${valorParcela.toFixed(2).replace('.', ',')}">
                                        - Data de Vencimento: <input type="date" name="parcelas[${i}][data_vencimento]" class="form-control parcela-data">
                                        <button type="button" class="btn btn-danger btn-sm btn-remover-parcela" data-parcela-id="${i}">Remover</button>
                                    </li>`;
                        listaParcelas.append(item);
                    }

                    $('#parcelas_section').show();
                }
            });

            // Remover parcela e atualizar valor total
            $(document).on('click', '.btn-remover-parcela', function() {
                $(this).parent().remove();
                atualizarValorTotal();
            });

            // Atualizar o valor total sempre que um valor de parcela for alterado
            $(document).on('change', '.parcela-valor', function() {
                atualizarValorTotal();
            });

            function atualizarValorTotal() {
                var total = 0;
                $('#lista_parcelas .parcela-valor').each(function() {
                    var valor = parseFloat($(this).val().replace(',', '.'));
                    total += valor;
                });
                $('#valor_total').val(total.toFixed(2).replace('.', ','));
            }
        });
    </script>
@endsection
