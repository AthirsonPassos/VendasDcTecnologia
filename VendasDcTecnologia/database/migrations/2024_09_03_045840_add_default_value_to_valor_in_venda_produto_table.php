<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('venda_produto', function (Blueprint $table) {
            // Adiciona um valor default para a coluna 'valor'
            $table->decimal('valor', 10, 2)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venda_produto', function (Blueprint $table) {
            // Reverte para a definição anterior da coluna 'valor'
            // Remova o valor padrão se a coluna não permitia valor nulo antes
            $table->decimal('valor', 10, 2)->change();
        });
    }
};
