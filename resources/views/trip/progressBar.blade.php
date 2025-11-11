<div class="progress-bar-container">
    <style>
        /* Container */
        .progress-bar-container {
            padding: 0 8px 1.25rem;
        }

        .progress-steps {
            display: flex;
            gap: 0;
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

        /* Linha de conexão padrão */
        .progress-step::after {
            content: '';
            position: absolute;
            height: 6px;
            background: linear-gradient(90deg, #e0e7ef 0%, #f3f4f6 100%);
            top: 50%;
            right: calc(-50% + 22px);
            left: calc(50% + 22px);
            transform: translateY(-50%);
            border-radius: 4px;
            z-index: 0;
        }
        .progress-step:last-child::after { display: none; }

        /* Linha de conexão verde para passos concluídos */
        .progress-step.completed::after {
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
        }

        /* Step concluído: verde permanente */
        .progress-step.completed .step-indicator {
            background: linear-gradient(135deg,#22c55e 0%,#16a34a 100%);
            color: #fff;
            border-color: #22c55e;
            box-shadow: 0 6px 20px rgba(34,197,94,0.18);
            transform: translateY(-4px) scale(1.05);
        }

        /* Step indicator (circle) */
        .step-indicator {
            width: 44px;
            height: 44px;
            border-radius: 9999px;
            background: #f9fafb;
            border: 2px solid #e0e7ef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #2563eb;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(15,23,42,0.04);
            transition: all 220ms cubic-bezier(.2,.9,.2,1);
            transform: translateY(0);
        }

        /* Active step style */
        .step-indicator.active {
            background: linear-gradient(135deg,#2563eb 0%,#3b82f6 100%);
            color: #fff;
            border-color: #2563eb;
            box-shadow: 0 6px 20px rgba(37,99,235,0.18);
            transform: translateY(-6px) scale(1.07);
        }

        /* Step label */
        .step-label {
            display: block;
            margin-top: 8px;
            font-size: 0.875rem;
            color: #475569;
            font-weight: 600;
            line-height: 1.1;
            max-width: 120px;
        }

        /* Small subtitle (optional) */
        .step-sub {
            display: block;
            margin-top: 4px;
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 720px) {
            .step-label { font-size: 0.78rem; max-width: 80px; }
            .step-indicator { width: 40px; height: 40px; }
            .progress-step::after { height: 5px; right: calc(-50% + 20px); left: calc(50% + 20px); }
        }

        .step-indicator:focus {
            outline: 3px solid rgba(59,130,246,0.18);
            outline-offset: 2px;
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
            <div class="progress-step{{ $isCompleted ? ' completed' : '' }}" aria-current="{{ $isActive ? 'step' : 'false' }}">
                <div class="step-indicator @if($isActive) active @endif" id="step-indicator-{{ $stepNumber }}" aria-hidden="true">
                    {{ $stepNumber }}
                </div>
                <span class="step-label">{{ $etapa }}</span>
                @if($i < 5)
                    <span class="step-sub" aria-hidden="true"></span>
                @endif
            </div>
        @endforeach
    </nav>
</div>