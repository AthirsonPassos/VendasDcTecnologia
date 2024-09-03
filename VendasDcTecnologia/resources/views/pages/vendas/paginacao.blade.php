@extends('index')

@section('content')
    <a href="{{ route('cadastrar.venda') }}" class="btn btn-success">Cadastrar Venda</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Valor Total</th>
                <th>Forma de Pagamento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vendas as $venda)
                <tr>
                    <td>{{ $venda->id }}</td>
                    <td>{{ $venda->cliente->nome }}</td>
                    <td>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                    <td>{{ $venda->is_avista ? 'À vista' : 'Parcelado' }}</td>
                    <td>
                        <a href="{{ route('atualiza.venda', $venda->id) }}" class="btn btn-primary">Editar</a>
                        <form action="{{ route('venda.delete', $venda->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
