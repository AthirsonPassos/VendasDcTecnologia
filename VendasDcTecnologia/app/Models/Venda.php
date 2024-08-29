<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $table = 'vendas';

    protected $fillable = [
        'numeroVenda',
        'cliente_id',
        'valorTotal',
    ];

    // Relacionamento com Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Relacionamento com Produtos
    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_venda', 'venda_id', 'produto_id')
                    ->withPivot('quantidade') // Se você estiver armazenando a quantidade de cada produto
                    ->withTimestamps();
    }

    // Relacionamento com Parcelas
    public function parcelas()
    {
        return $this->hasMany(ParcelasVenda::class, 'venda_id');
    }
}
