<div class="flex justify-between items-center mb-12 px-4">
    @foreach(['Informações iniciais', 'Detalhes da viagem', 'Preferências', 'Seguros', 'Voos', 'Aluguel de carros', 'Revisão final'] as $i => $etapa)
        <div class="flex-1 flex items-center relative">
            <div class="step-indicator @if($i==0) active @endif" id="step-indicator-{{ $i+1 }}">{{ $i+1 }}</div>
            <span class="step-label">{{ $etapa }}</span>
            @if($i < 6)
                <div class="step-line"></div>
            @endif
        </div>
    @endforeach
</div>