<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id', 'valor_total', 'forma_pagamento', 'is_avista'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'venda_produto')->withPivot('valor');
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class);
    }
}
