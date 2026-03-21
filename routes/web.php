<?php

use App\Http\Controllers\PublicAgendamentoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/agendar');
});

Route::get('/agendar', [PublicAgendamentoController::class, 'create'])->name('agendamento.publico.create');
Route::post('/agendar', [PublicAgendamentoController::class, 'store'])->name('agendamento.publico.store');
Route::get('/agendar/horarios', [PublicAgendamentoController::class, 'horariosPorData'])->name('agendamento.publico.horarios');

