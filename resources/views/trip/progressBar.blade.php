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

        .progress-step:last-child::after {
            display: none;
        }

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
            text-align: center;
        }

        /* Disabled step style */
        .step-indicator.disabled {
            background: #f1f5f9;
            color: #cbd5e1;
            border-color: #e2e8f0;
            opacity: 0.5;
            cursor: not-allowed;
        }

        .progress-step.disabled .step-label {
            color: #cbd5e1;
        }

        /* Responsive adjustments */
        @media (max-width: 720px) {
            .step-label { font-size: 0.78rem; max-width: 80px; }
            .step-indicator { width: 40px; height: 40px; }
            .progress-step::after { height: 5px; right: calc(-50% + 20px); left: calc(50% + 20px); }
        }
    </style>

    <nav aria-label="Progresso da criação do roteiro" class="progress-steps mb-8" role="navigation">
        @foreach(['Informações iniciais', 'Detalhes da viagem', 'Preferências', 'Seguros', 'Voos', 'Aluguel de carros', 'Revisão final'] as $i => $etapa)
            <div class="progress-step" data-step-id="{{ $i }}">
                <div class="step-indicator @if($i==0) active @endif" id="step-indicator-{{ $i }}">{{ $i+1 }}</div>
                <span class="step-label">{{ $etapa }}</span>
            </div>
        @endforeach
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para atualizar o estado dos indicadores de progresso
    window.updateProgressIndicators = function() {
        const meioLocomocao = document.getElementById('meio_locomocao')?.value;
        const seguroViagem = document.getElementById('seguroViagem')?.value;
        
        // Step 4 (índice 4) = Voos
        const stepVoos = document.querySelector('.progress-step[data-step-id="4"]');
        const indicatorVoos = stepVoos?.querySelector('.step-indicator');
        
        // Step 5 (índice 5) = Aluguel de carros
        const stepCarros = document.querySelector('.progress-step[data-step-id="5"]');
        const indicatorCarros = stepCarros?.querySelector('.step-indicator');
        
        // Habilitar/desabilitar step de Voos
        if (meioLocomocao === 'Avião') {
            stepVoos?.classList.remove('disabled');
            indicatorVoos?.classList.remove('disabled');
        } else {
            stepVoos?.classList.add('disabled');
            indicatorVoos?.classList.add('disabled');
        }
        
        // Habilitar/desabilitar step de Aluguel de carros
        if (meioLocomocao === 'Carro (alugado)') {
            stepCarros?.classList.remove('disabled');
            indicatorCarros?.classList.remove('disabled');
        } else {
            stepCarros?.classList.add('disabled');
            indicatorCarros?.classList.add('disabled');
        }
    };
    
    // Observar mudanças no meio de locomoção
    const meioLocomocaoSelect = document.getElementById('meio_locomocao');
    if (meioLocomocaoSelect) {
        meioLocomocaoSelect.addEventListener('change', window.updateProgressIndicators);
        // Executar inicialização
        setTimeout(() => window.updateProgressIndicators(), 100);
    }
});
</script>