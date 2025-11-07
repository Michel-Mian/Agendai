<div class="progress-bar-container">
    <style>
        .progress-bar-container {
            padding: 0 8px 1.25rem;
        }
        .progress-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
        }
        .progress-step {
            position: relative;
            flex: 1 1 0%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 0 6px;
            min-width: 80px;
        }
        .progress-step::after {
            content: '';
            position: absolute;
            height: 6px;
            background: #e5e7eb;
            top: 50%;
            right: calc(-50% + 22px);
            left: calc(50% + 22px);
            transform: translateY(-50%);
            border-radius: 4px;
            z-index: 0;
            transition: background 0.3s ease;
        }
        .progress-step.completed::after {
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%) !important;
        }
        .progress-step:last-child::after {
            display: none;
        }
        .step-indicator {
            width: 44px;
            height: 44px;
            border-radius: 9999px;
            background: #ffffff;
            border: 2px solid #e6eefc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #374151;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            transition: background 0.2s, color 0.2s, border-color 0.2s;
        }
        /* Força o verde nos steps concluídos, sobrescrevendo qualquer regra global */
        .progress-step.completed .step-indicator {
            background: linear-gradient(135deg,#22c55e 0%,#16a34a 100%) !important;
            color: #fff !important;
            border-color: #16a34a !important;
        }
        .progress-step.active .step-indicator {
            background: linear-gradient(135deg,#22c55e 0%,#16a34a 100%) !important;
            color: #fff !important;
            border-color: #16a34a !important;
        }
        /* Sobrescreve regras globais que deixam steps cinza */
        .progress-step.completed .step-indicator,
        .progress-step.active .step-indicator {
            box-shadow: 0 2px 6px rgba(34,197,94,0.12) !important;
        }
        .step-label {
            margin-top: 8px;
            font-size: 0.875rem;
            color: #475569;
            font-weight: 600;
            line-height: 1.1;
            max-width: 120px;
        }
        .step-sub {
            margin-top: 4px;
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }
        @media (max-width: 720px) {
            .step-label { font-size: 0.78rem; max-width: 80px; }
            .step-indicator { width: 40px; height: 40px; }
            .progress-step::after { height: 5px; right: calc(-50% + 20px); left: calc(50% + 20px); }
        }
    </style>
    <nav aria-label="Progresso da criação do roteiro" class="progress-steps" role="navigation">
        @php
            $currentStep = request()->input('step', 1);
        @endphp
        @foreach(['Informações iniciais', 'Detalhes da viagem', 'Preferências', 'Seguros', 'Voos', 'Revisão final'] as $i => $etapa)
            @php
                $stepNumber = $i + 1;
                $isActive = $stepNumber == $currentStep;
                $isCompleted = $stepNumber < $currentStep;
            @endphp
            <div class="progress-step{{ $isCompleted ? ' completed' : '' }}{{ $isActive ? ' active' : '' }}" aria-current="{{ $isActive ? 'step' : 'false' }}">
                <div class="step-indicator" id="step-indicator-{{ $stepNumber }}" aria-hidden="true">
                    {{ $stepNumber }}
                </div>
                <span class="step-label">{{ $etapa }}</span>
                @if($i < 5)
                    <span class="step-sub" aria-hidden="true"></span>
                @endif
            </div>
        @endforeach
    </nav>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateProgressBar(step) {
            const steps = document.querySelectorAll('.progress-step');
            steps.forEach((el, idx) => {
                el.classList.remove('completed', 'active');
                if (idx + 1 < step) {
                    el.classList.add('completed');
                }
                if (idx + 1 === step) {
                    el.classList.add('active');
                }
            });
        }
        let step = 1;
        const stepInput = document.querySelector('input[name="currentStep"]');
        if (stepInput) step = parseInt(stepInput.value) || 1;
        else if (window.currentStep) step = parseInt(window.currentStep) || 1;
        else {
            const activeStep = document.querySelector('.form-step.active');
            if (activeStep) {
                const steps = Array.from(document.querySelectorAll('.form-step'));
                step = steps.indexOf(activeStep) + 1;
            }
        }
        updateProgressBar(step);
        window.updateProgressBar = updateProgressBar;
    });
    </script>
</div>