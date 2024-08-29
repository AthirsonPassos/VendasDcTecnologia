<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nome',
        'email',
    ];

    public function getClientesPesquisarIndex(string $search = '') {
        $clientes = $this->where(function ($query) use ($search){
            if ($search) {
                $query->where('nome', $search);
                $query->orWhere('nome', 'LIKE',"%{$search}%");
                $query->where('email', $search);
                $query->orWhere('email', 'LIKE',"%{$search}%");
            }
        })->GET();

        return $clientes;
    }
}

