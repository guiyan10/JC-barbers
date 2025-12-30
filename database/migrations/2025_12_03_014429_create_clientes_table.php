<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone', 191)->unique();
            $table->string('email')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('plano_id')->nullable()->constrained('planos')->nullOnDelete();
            $table->date('data_inicio_plano')->nullable();
            $table->date('data_fim_plano')->nullable();
            $table->integer('cortes_utilizados_mes')->default(0);
            $table->date('ultimo_reset_contador')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
