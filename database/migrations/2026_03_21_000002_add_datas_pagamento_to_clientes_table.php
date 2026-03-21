<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->date('data_ultimo_pagamento')->nullable()->after('data_fim_plano');
            $table->date('data_pagamento_previsto')->nullable()->after('data_ultimo_pagamento');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['data_ultimo_pagamento', 'data_pagamento_previsto']);
        });
    }
};

