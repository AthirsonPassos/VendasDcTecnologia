<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoVenda extends Model
{
    use HasFactory;

    protected $table = 'produto_venda';

    protected $fillable = [
        'venda_id',
        'produto_id',
        'quantidade',
    ];

    // Relacionamento com a model Venda
    public function venda()
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }

    // Relacionamento com a model Produto
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
