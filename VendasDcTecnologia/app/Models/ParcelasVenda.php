<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelasVenda extends Model
{
    use HasFactory;

    protected $table = 'parcelas_venda';

    protected $fillable = [
        'venda_id',
        'valor',
        'data_vencimento',
    ];

    // Relacionamento com Venda
    public function venda()
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }
}
