<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento Online | JC Barber</title>
    <style>
        :root {
            --bg: #09090b;
            --card: #111118;
            --muted: #a1a1aa;
            --text: #f4f4f5;
            --accent: #f59e0b;
            --accent-strong: #d97706;
            --border: #27272a;
            --danger: #f87171;
            --ok-bg: #052e16;
            --ok-border: #166534;
            --ok-text: #86efac;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, Segoe UI, Roboto, Arial, sans-serif;
            background: radial-gradient(circle at top, #18181b 0%, var(--bg) 45%);
            color: var(--text);
            min-height: 100vh;
        }

        .wrap {
            width: min(980px, 92%);
            margin: 2rem auto 3rem;
        }

        .hero {
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: #0f0f16;
            padding: .45rem;
            object-fit: contain;
        }

        .brand {
            color: var(--accent);
            font-weight: 700;
            letter-spacing: .06em;
            font-size: .85rem;
            text-transform: uppercase;
        }

        h1 {
            margin: .35rem 0 .5rem;
            font-size: clamp(1.45rem, 2.7vw, 2.1rem);
        }

        .subtitle {
            color: var(--muted);
            margin: 0;
            max-width: 700px;
            line-height: 1.5;
        }

        .card {
            margin-top: 1.5rem;
            background: color-mix(in srgb, var(--card) 92%, #000);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.2rem;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.35);
        }

        .grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        @media (min-width: 760px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .full { grid-column: 1 / -1; }
        }

        .field label {
            display: block;
            margin-bottom: .45rem;
            font-size: .92rem;
            font-weight: 600;
        }

        .hint {
            color: var(--muted);
            font-size: .78rem;
            margin-top: .35rem;
        }

        input, select, textarea, button {
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #0f0f16;
            color: var(--text);
            padding: .82rem .88rem;
            font-size: .96rem;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
        }

        textarea { min-height: 110px; resize: vertical; }

        .actions {
            display: flex;
            gap: .75rem;
            align-items: center;
            justify-content: flex-end;
            margin-top: .5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-strong));
            color: #111;
            font-weight: 700;
            border: 0;
            cursor: pointer;
            transition: transform .08s ease, filter .12s ease;
        }

        .btn-primary:hover { filter: brightness(1.06); }
        .btn-primary:active { transform: translateY(1px); }

        .alert {
            margin-bottom: 1rem;
            border-radius: 12px;
            padding: .8rem .9rem;
            border: 1px solid transparent;
            font-size: .92rem;
        }

        .alert-ok {
            background: var(--ok-bg);
            border-color: var(--ok-border);
            color: var(--ok-text);
        }

        .alert-error {
            background: rgba(127, 29, 29, 0.35);
            border-color: rgba(248, 113, 113, 0.35);
            color: #fecaca;
        }

        .error {
            margin-top: .4rem;
            color: var(--danger);
            font-size: .78rem;
        }

        .picker-trigger {
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            cursor: pointer;
        }

        .picker-trigger .muted {
            color: var(--muted);
            font-weight: 500;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            opacity: 0;
            visibility: hidden;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
            transition: opacity .22s ease, visibility .22s ease;
        }

        .modal-backdrop.open {
            display: flex;
            opacity: 1;
            visibility: visible;
        }

        .modal {
            width: min(480px, 100%);
            background: #101018;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
            transform: translateY(10px) scale(0.985);
            transition: transform .22s ease;
        }

        .modal-backdrop.open .modal {
            transform: translateY(0) scale(1);
        }

        .modal h2 {
            margin: 0 0 .35rem;
            font-size: 1.12rem;
        }

        .modal p {
            margin: 0 0 .9rem;
            color: var(--muted);
            font-size: .9rem;
        }

        .modal-grid {
            display: grid;
            gap: .85rem;
        }

        .modal-actions {
            margin-top: .95rem;
            display: flex;
            gap: .65rem;
            justify-content: flex-end;
        }

        .btn-secondary {
            background: #18181f;
            border: 1px solid var(--border);
            color: var(--text);
            cursor: pointer;
        }

        .slots {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .55rem;
            max-height: 220px;
            overflow-y: auto;
            padding-right: .1rem;
        }

        @media (max-width: 560px) {
            .slots {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .slot-btn {
            padding: .52rem .4rem;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #13131b;
            color: var(--text);
            font-size: .85rem;
            cursor: pointer;
        }

        .slot-btn:hover {
            border-color: var(--accent);
        }

        .slot-btn.selected {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.28), rgba(217, 119, 6, 0.18));
            border-color: var(--accent);
            color: #fde68a;
            font-weight: 700;
        }

        .slot-btn.occupied,
        .slot-btn.past {
            background: #17171f;
            color: #71717a;
            border-color: #30303a;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .slots-legend {
            display: flex;
            gap: .85rem;
            flex-wrap: wrap;
            margin-top: .55rem;
            color: var(--muted);
            font-size: .77rem;
        }

        .modal-feedback {
            margin-top: .5rem;
            color: var(--muted);
            font-size: .82rem;
        }
    </style>
</head>
<body>
<main class="wrap">
    <section class="hero">
        <img src="<?php echo e(asset('images/logo.svg')); ?>" alt="Logo JC Barber" class="brand-logo">
        <div>
            <p class="brand">JC Barber</p>
            <h1>Agendamento Online</h1>
            <p class="subtitle">Escolha seu servico, informe o melhor horario e finalize seu agendamento em menos de 1 minuto.</p>
        </div>
    </section>

    <section class="card">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-ok"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-error">
                Verifique os dados informados e tente novamente.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form method="POST" action="<?php echo e(route('agendamento.publico.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="grid">
                <div class="field">
                    <label for="nome">Nome completo</label>
                    <input id="nome" name="nome" type="text" required value="<?php echo e(old('nome')); ?>" placeholder="Ex.: Angelo Rocha">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="field">
                    <label for="telefone">Telefone</label>
                    <input id="telefone" name="telefone" type="text" required value="<?php echo e(old('telefone')); ?>" placeholder="(99) 99999-9999">
                    <div class="hint">Use o WhatsApp para contato rapido.</div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['telefone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="field">
                    <label for="email">E-mail (opcional)</label>
                    <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" placeholder="voce@email.com">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="field">
                    <label for="servico_id">Servico</label>
                    <select id="servico_id" name="servico_id" required>
                        <option value="">Selecione</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $servicos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($servico->id); ?>" <?php if((string) old('servico_id') === (string) $servico->id): echo 'selected'; endif; ?>>
                                <?php echo e($servico->nome); ?> - R$ <?php echo e(number_format((float) $servico->preco, 2, ',', '.')); ?>

                                (<?php echo e($servico->duracao_estimada); ?> min)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['servico_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="field full">
                    <label for="data_hora">Data e horario</label>
                    <input id="data_hora" name="data_hora" type="hidden" required value="<?php echo e(old('data_hora')); ?>">
                    <button id="openDateTimeModal" type="button" class="picker-trigger">
                        <span id="selectedDateTimeText" class="muted">Selecionar data e horario</span>
                        <span>📅</span>
                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['data_hora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="field full">
                    <label for="observacoes">Observacoes (opcional)</label>
                    <textarea id="observacoes" name="observacoes" placeholder="Ex.: preferencia de barbeiro, detalhes do corte..."><?php echo e(old('observacoes')); ?></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="actions">
                <button class="btn-primary" type="submit">Confirmar agendamento</button>
            </div>
        </form>
    </section>
</main>

<div id="dateTimeModal" class="modal-backdrop" aria-hidden="true">
    <div class="modal">
        <h2>Escolha data e horario</h2>
        <p>Selecione um horario disponivel para seu atendimento.</p>

        <div class="modal-grid">
            <div class="field">
                <label for="modalData">Data</label>
                <input id="modalData" type="date">
                <div class="hint">Atendimento de segunda a sabado, das 08:00 as 20:00.</div>
            </div>
            <div class="field">
                <label>Horarios</label>
                <div id="slots" class="slots"></div>
                <div class="slots-legend">
                    <span>Disponivel</span>
                    <span>Selecionado</span>
                    <span>Indisponivel</span>
                </div>
                <div id="modalFeedback" class="modal-feedback"></div>
            </div>
        </div>

        <div class="modal-actions">
            <button id="cancelDateTimeModal" type="button" class="btn-secondary">Cancelar</button>
            <button id="confirmDateTimeModal" type="button" class="btn-primary">Confirmar</button>
        </div>
    </div>
</div>

<script>
    (function () {
        const hiddenInput = document.getElementById('data_hora');
        const openBtn = document.getElementById('openDateTimeModal');
        const modal = document.getElementById('dateTimeModal');
        const cancelBtn = document.getElementById('cancelDateTimeModal');
        const confirmBtn = document.getElementById('confirmDateTimeModal');
        const modalData = document.getElementById('modalData');
        const slotsContainer = document.getElementById('slots');
        const feedback = document.getElementById('modalFeedback');
        const selectedText = document.getElementById('selectedDateTimeText');
        const horariosEndpoint = "<?php echo e(route('agendamento.publico.horarios')); ?>";

        const minDateTime = "<?php echo e($minDateTime); ?>";
        const [minDate, minTime] = minDateTime.split('T');
        const minDateObj = new Date(minDateTime);
        let selectedTime = null;

        modalData.min = minDate;

        const updateLabel = (value) => {
            if (!value || !value.includes('T')) {
                selectedText.textContent = 'Selecionar data e horario';
                selectedText.classList.add('muted');
                return;
            }

            const [datePart, timePart] = value.split('T');
            const [year, month, day] = datePart.split('-');
            selectedText.textContent = `${day}/${month}/${year} as ${timePart}`;
            selectedText.classList.remove('muted');
        };

        const isPastSlot = (date, time) => {
            const candidate = new Date(`${date}T${time}`);
            return candidate < minDateObj;
        };

        const renderSlots = async (date) => {
            slotsContainer.innerHTML = '<span class="hint">Carregando horarios...</span>';
            feedback.textContent = '';

            try {
                const response = await fetch(`${horariosEndpoint}?data=${encodeURIComponent(date)}`);
                const payload = await response.json();
                const slots = payload.slots || [];
                const ocupados = new Set(payload.ocupados || []);

                if (payload.fechado) {
                    slotsContainer.innerHTML = '<span class="hint">Sem horarios para este dia.</span>';
                    feedback.textContent = payload.mensagem || 'Dia indisponivel para agendamento.';
                    selectedTime = null;
                    return;
                }

                slotsContainer.innerHTML = '';

                slots.forEach((slot) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'slot-btn';
                    button.textContent = slot;
                    button.dataset.time = slot;

                    const past = isPastSlot(date, slot);
                    const occupied = ocupados.has(slot);

                    if (past) {
                        button.classList.add('past');
                        button.disabled = true;
                    } else if (occupied) {
                        button.classList.add('occupied');
                        button.disabled = true;
                    }

                    if (selectedTime === slot && !button.disabled) {
                        button.classList.add('selected');
                    }

                    button.addEventListener('click', () => {
                        if (button.disabled) return;
                        selectedTime = slot;

                        slotsContainer.querySelectorAll('.slot-btn').forEach((el) => {
                            el.classList.remove('selected');
                        });
                        button.classList.add('selected');
                    });

                    slotsContainer.appendChild(button);
                });
            } catch (e) {
                slotsContainer.innerHTML = '<span class="hint">Nao foi possivel carregar horarios agora.</span>';
                feedback.textContent = 'Tente novamente em instantes.';
            }
        };

        const openModal = () => {
            if (hiddenInput.value && hiddenInput.value.includes('T')) {
                const [datePart, timePart] = hiddenInput.value.split('T');
                modalData.value = datePart;
                selectedTime = timePart;
            } else {
                modalData.value = minDate;
                selectedTime = minTime;
            }

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            renderSlots(modalData.value);
        };

        const closeModal = () => {
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
        };

        openBtn.addEventListener('click', openModal);
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        modalData.addEventListener('change', () => {
            selectedTime = null;
            renderSlots(modalData.value);
        });

        confirmBtn.addEventListener('click', () => {
            if (!modalData.value || !selectedTime) {
                return;
            }

            const selected = `${modalData.value}T${selectedTime}`;

            if (selected < minDateTime) {
                modalData.value = minDate;
                selectedTime = minTime;
                hiddenInput.value = minDateTime;
            } else {
                hiddenInput.value = selected;
            }

            updateLabel(hiddenInput.value);
            closeModal();
        });

        updateLabel(hiddenInput.value);
    })();
</script>
</body>
</html>

<?php /**PATH C:\Users\guiya\OneDrive\Área de Trabalho\Projetos\JC barbers\resources\views/public/agendamento.blade.php ENDPATH**/ ?>