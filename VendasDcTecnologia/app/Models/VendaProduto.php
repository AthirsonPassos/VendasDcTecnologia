<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaProduto extends Model
{
    use HasFactory;
    protected $table = 'venda_produto';

    // Define os campos que podem ser preenchidos em massa
    protected $fillable = [
        'venda_id',
        'produto_id',
        'valor',
    ];

    // Define os relacionamentos com outros modelos, se necessário
    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
