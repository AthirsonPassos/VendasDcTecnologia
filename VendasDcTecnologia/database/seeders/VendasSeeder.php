<?php

namespace Database\Seeders;

use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar uma nova venda
        $venda = Venda::create([
            'numeroVenda' => 1,
            'cliente_id' => 2, // ID do cliente
            'valorTotal' => 150.00, // Valor total da venda
        ]);

        // Adicionar produtos à venda com suas respectivas parcelas
        $produtos = [
            ['produto_id' => 5, 'valorParcela' => 50.00, 'dataVencimento' => Carbon::now()->addDays(30)],
            ['produto_id' => 7, 'valorParcela' => 100.00, 'dataVencimento' => Carbon::now()->addDays(60)]
        ];

        foreach ($produtos as $produto) {
            $venda->parcelas()->create([
                'produto_id' => $produto['produto_id'],
                'valorParcela' => $produto['valorParcela'],
                'dataVencimento' => $produto['dataVencimento']
            ]);
        }
    }
}
