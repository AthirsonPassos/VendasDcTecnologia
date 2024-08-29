<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::create([
            'nome' => 'Teste',
            'email' => 'teste@hotmail.com'
        ]
            
        );
        Cliente::create([
            'nome' => 'Teste3',
            'email' => 'test4e@hotmail.com'
        ]
            
        );
        Cliente::create([
            'nome' => 'Teste2',
            'email' => 'teste@hotmail.com'
        ]
            
        );
    }
}
