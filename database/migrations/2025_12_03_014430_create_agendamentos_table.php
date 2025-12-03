<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('servico_id')->constrained('servicos')->cascadeOnDelete();
            $table->dateTime('data_hora');
            $table->tinyInteger('status')->default(0)->comment('0=Agendado, 1=Iniciado, 2=Finalizado');
            $table->dateTime('hora_inicio')->nullable();
            $table->dateTime('hora_fim')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('descontado_plano')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
