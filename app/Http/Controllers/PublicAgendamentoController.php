<?php

namespace App\Http\Controllers;

use App\Mail\AgendamentoConfirmadoMail;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class PublicAgendamentoController extends Controller
{
    private const HORA_INICIO = 8;
    private const HORA_FIM = 20;
    private const INTERVALO_MINUTOS = 30;

    public function create(): View
    {
        $servicos = Servico::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'preco', 'duracao_estimada']);

        return view('public.agendamento', [
            'servicos' => $servicos,
            'minDateTime' => now()->format('Y-m-d\TH:i'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'servico_id' => ['required', 'exists:servicos,id'],
            'data_hora' => ['required', 'date_format:Y-m-d\TH:i'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ]);

        $dataHora = Carbon::createFromFormat('Y-m-d\TH:i', $validated['data_hora']);

        if ($dataHora->lessThan(now())) {
            return back()
                ->withInput()
                ->withErrors(['data_hora' => 'Escolha um horario futuro para o agendamento.']);
        }

        if ($dataHora->dayOfWeek === Carbon::SUNDAY) {
            return back()
                ->withInput()
                ->withErrors(['data_hora' => 'Nao atendemos aos domingos. Escolha outro dia.']);
        }

        if (! $this->isHorarioValido($dataHora)) {
            return back()
                ->withInput()
                ->withErrors(['data_hora' => 'Horario fora do expediente. Atendemos de 08:00 as 20:00, em intervalos de 30 minutos.']);
        }

        $servicoAtivo = Servico::query()
            ->where('id', $validated['servico_id'])
            ->where('ativo', true)
            ->exists();

        if (! $servicoAtivo) {
            return back()
                ->withInput()
                ->withErrors(['servico_id' => 'Servico indisponivel no momento.']);
        }

        $conflitoHorario = Agendamento::query()
            ->where('data_hora', $dataHora)
            ->exists();

        if ($conflitoHorario) {
            return back()
                ->withInput()
                ->withErrors(['data_hora' => 'Este horario ja foi reservado. Escolha outro horario.']);
        }

        $telefone = $this->normalizePhone($validated['telefone']);

        $cliente = Cliente::query()->firstOrCreate(
            ['telefone' => $telefone],
            [
                'nome' => $validated['nome'],
                'email' => $validated['email'] ?: null,
            ],
        );

        $cliente->nome = $validated['nome'];
        $cliente->email = $validated['email'] ?: null;
        $cliente->save();

        $agendamento = Agendamento::query()->create([
            'cliente_id' => $cliente->id,
            'servico_id' => (int) $validated['servico_id'],
            'data_hora' => $dataHora,
            'status' => Agendamento::STATUS_AGENDADO,
            'observacoes' => $validated['observacoes'] ?? null,
            'descontado_plano' => false,
        ]);

        if (filled($cliente->email)) {
            try {
                Mail::to($cliente->email)->send(new AgendamentoConfirmadoMail($agendamento->load(['cliente', 'servico'])));
            } catch (Throwable $exception) {
                Log::warning('Falha ao enviar e-mail de confirmacao de agendamento.', [
                    'agendamento_id' => $agendamento->id,
                    'cliente_email' => $cliente->email,
                    'erro' => $exception->getMessage(),
                ]);
            }
        }

        try {
            $servico = Servico::find($validated['servico_id']);
            $mensagem = "🔔 *Novo Agendamento - JC Barber!*\n\n"
                . "👤 *Cliente:* {$cliente->nome}\n"
                . "📱 *Telefone:* {$cliente->telefone}\n"
                . "✂️ *Serviço:* {$servico->nome}\n"
                . "📅 *Data/Hora:* " . $dataHora->format('d/m/Y \à\s H:i') . "\n"
                . (filled($validated['observacoes'] ?? null)
                    ? "💬 *Obs:* {$validated['observacoes']}\n"
                    : '');

            app(WhatsAppService::class)->notificarBarbearia($mensagem);
        } catch (Throwable $exception) {
            Log::warning('Falha ao enviar WhatsApp de agendamento.', [
                'agendamento_id' => $agendamento->id,
                'erro' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('agendamento.publico.create')
            ->with('success', 'Agendamento realizado com sucesso! Te esperamos na JC Barber.');
    }

    public function horariosPorData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => ['required', 'date_format:Y-m-d'],
        ]);

        $data = Carbon::createFromFormat('Y-m-d', $validated['data']);

        if ($data->dayOfWeek === Carbon::SUNDAY) {
            return response()->json([
                'slots' => [],
                'ocupados' => [],
                'fechado' => true,
                'mensagem' => 'Domingo fechado. Escolha outro dia.',
            ]);
        }

        $ocupados = Agendamento::query()
            ->whereDate('data_hora', $data)
            ->orderBy('data_hora')
            ->get(['data_hora'])
            ->map(fn (Agendamento $agendamento) => Carbon::parse($agendamento->data_hora)->format('H:i'))
            ->values();

        return response()->json([
            'slots' => $this->generateTimeSlots(),
            'ocupados' => $ocupados,
            'fechado' => false,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function generateTimeSlots(): array
    {
        $slots = [];
        $cursor = Carbon::createFromTime(self::HORA_INICIO, 0);
        $fim = Carbon::createFromTime(self::HORA_FIM, 0);

        while ($cursor->lt($fim)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes(self::INTERVALO_MINUTOS);
        }

        return $slots;
    }

    private function isHorarioValido(Carbon $dateTime): bool
    {
        if ($dateTime->minute % self::INTERVALO_MINUTOS !== 0) {
            return false;
        }

        $inicio = $dateTime->copy()->setTime(self::HORA_INICIO, 0, 0);
        $fim = $dateTime->copy()->setTime(self::HORA_FIM, 0, 0);

        return $dateTime->gte($inicio) && $dateTime->lt($fim);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) === 11) {
            return sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 5), substr($digits, 7, 4));
        }

        if (strlen($digits) === 10) {
            return sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6, 4));
        }

        return $phone;
    }
}

