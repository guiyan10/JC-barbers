<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->unsignedInteger('duracao_dias')->default(30)->after('limite_mensal');
        });
    }

    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->dropColumn('duracao_dias');
        });
    }
};

