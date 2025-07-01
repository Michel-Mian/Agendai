@extends('index')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-white">
    @include('components/layout/sidebar')
    <div class="flex-1 flex flex-col">
        @include('components/layout/header')

        <div class="w-full max-w-3xl mx-auto mt-12">
            <!-- Barra de progresso -->
            <div class="flex justify-between items-center mb-12 px-4">
                @foreach(['Informações iniciais', 'Detalhes da viagem', 'Preferências', 'Informações orçamentárias', 'Voos'] as $i => $etapa)
                    <div class="flex-1 flex items-center relative">
                        <div class="step-indicator @if($i==0) active @endif" id="step-indicator-{{ $i+1 }}">{{ $i+1 }}</div>
                        <span class="step-label">{{ $etapa }}</span>
                        @if($i < 4)
                            <div class="step-line"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-10 mb-10 animate-fade-in">
                <form id="multiStepForm">
                    <!-- Passo 1 -->
                    <div class="form-step active">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Informações iniciais</h2>
                        <div class="mb-6">
                            <label class="block text-gray-600 font-semibold mb-2">Qual seu destino?</label>
                            <input type="text" id="locationInput" class="input" placeholder="Digite o destino dos sonhos...">
                        </div>
                        <div class="flex gap-6 mb-6">
                            <div class="flex-1">
                                <label class="block text-gray-600 font-semibold mb-2">Nº de adultos:</label>
                                <select class="input">
                                    <option>1</option><option>2</option><option>3</option><option>4+</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-gray-600 font-semibold mb-2">Nº de crianças:</label>
                                <select class="input">
                                    <option>0</option><option>1</option><option>2</option><option>3+</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-6 mb-8">
                            <div class="flex-1">
                                <label class="block text-gray-600 font-semibold mb-2">Data de ida:</label>
                                <input type="date" class="input">
                            </div>
                            <div class="flex-1">
                                <label class="block text-gray-600 font-semibold mb-2">Data de volta:</label>
                                <input type="date" class="input">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 2 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Detalhes da viagem</h2>
                        <div class="mb-6">
                            <label class="block text-gray-600 font-semibold mb-2">Qual será o meio de locomoção?</label>
                            <select class="input">
                                <option>Carro</option>
                                <option>Ônibus</option>
                                <option>Avião</option>
                            </select>
                        </div>
                        <div class="mb-8">
                            <label class="block text-gray-600 font-semibold mb-2">Qual será a forma de estadia?</label>
                            <select class="input">
                                <option>Hotel</option>
                                <option>Pousada</option>
                                <option>Casa alugada</option>
                            </select>
                        </div>
                        <div class="flex justify-between">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 3 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Preferências</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                            @foreach(['Cultura e história' => '🏛️', 'Gastronomia' => '🍽️', 'Natureza' => '🌳', 'Aventura' => '⛰️', 'Praia' => '🏖️', 'Vida noturna' => '🌃', 'Compras' => '🛍️', 'Arte e museus' => '🖼️'] as $pref => $icon)
                                <button type="button" class="pref-btn flex flex-col items-center gap-2">
                                    <span class="text-2xl">{{ $icon }}</span>
                                    <span class="text-gray-700 font-medium">{{ $pref }}</span>
                                </button>
                            @endforeach
                        </div>
                        <div class="flex justify-between">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 4 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Informações orçamentárias</h2>
                        <div class="mb-6">
                            <label class="block text-gray-600 font-semibold mb-2">Qual seu orçamento total?</label>
                            <input type="number" class="input" placeholder="R$">
                        </div>
                        <div class="mb-8">
                            <label class="block text-gray-600 font-semibold mb-2">Escolha seu seguro viagem</label>
                            <div class="grid grid-cols-2 gap-6">
                                <button type="button" class="insurance-btn flex flex-col items-center gap-1">
                                    <span class="text-xl">🟢</span>
                                    <span class="font-semibold">Básico</span>
                                    <span class="text-green-600 font-bold">R$35/dia</span>
                                    <span class="text-xs text-gray-400">Cobertura essencial</span>
                                </button>
                                <button type="button" class="insurance-btn flex flex-col items-center gap-1">
                                    <span class="text-xl">🔵</span>
                                    <span class="font-semibold">Premium</span>
                                    <span class="text-green-600 font-bold">R$60/dia</span>
                                    <span class="text-xs text-gray-400">Cobertura completa</span>
                                </button>
                                <button type="button" class="insurance-btn flex flex-col items-center gap-1">
                                    <span class="text-xl">🟣</span>
                                    <span class="font-semibold">Família</span>
                                    <span class="text-green-600 font-bold">R$90/dia</span>
                                    <span class="text-xs text-gray-400">Até 5 pessoas</span>
                                </button>
                                <button type="button" class="insurance-btn flex flex-col items-center gap-1">
                                    <span class="text-xl">⚪</span>
                                    <span class="font-semibold">Sem Seguro</span>
                                    <span class="text-green-600 font-bold">R$0</span>
                                    <span class="text-xs text-gray-400">Viajar sem proteção</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="button" class="next-btn btn-primary">Próximo →</button>
                        </div>
                    </div>

                    <!-- Passo 5 -->
                    <div class="form-step">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Voos</h2>
                        <p class="mb-4 text-gray-600">Escolha sua passagem aérea</p>
                        @php
                            $flights = [
                                [
                                    'airline_logo' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Logo_LATAM.svg',
                                    'airline' => 'LATAM',
                                    'travel_class' => 'Econômica',
                                    'departure_time' => '08:30',
                                    'departure_code' => 'GRU',
                                    'arrival_time' => '10:45',
                                    'arrival_code' => 'SDU',
                                    'duration' => '2h 15m',
                                    'type' => 'Direto',
                                    'price' => '450,00',
                                    'selected' => false,
                                    'extras' => ['wifi', 'entretenimento', 'lanche'],
                                ],
                                [
                                    'airline_logo' => 'https://logodownload.org/wp-content/uploads/2019/06/gol-logo-0.png',
                                    'airline' => 'GOL',
                                    'travel_class' => 'Econômica',
                                    'departure_time' => '14:20',
                                    'departure_code' => 'GRU',
                                    'arrival_time' => '16:30',
                                    'arrival_code' => 'SDU',
                                    'duration' => '2h 10m',
                                    'type' => 'Direto',
                                    'price' => '380,00',
                                    'selected' => true,
                                    'extras' => ['wifi', 'lanche'],
                                ],
                                [
                                    'airline_logo' => 'https://logodownload.org/wp-content/uploads/2019/08/azul-linhas-aereas-logo-0.png',
                                    'airline' => 'Azul',
                                    'travel_class' => 'Econômica',
                                    'departure_time' => '19:15',
                                    'departure_code' => 'GRU',
                                    'arrival_time' => '21:25',
                                    'arrival_code' => 'SDU',
                                    'duration' => '2h 10m',
                                    'type' => 'Direto',
                                    'price' => '420,00',
                                    'selected' => false,
                                    'extras' => ['wifi', 'entretenimento'],
                                ],
                            ];
                        @endphp
                        <div class="space-y-4">
                            @foreach($flights as $index => $flight)
                                <div class="flex items-center bg-white rounded-xl px-6 py-5 border transition-all
                                    @if($flight['selected']) border-2 border-blue-500 ring-2 ring-blue-100 @else border border-gray-200 hover:border-blue-300 @endif
                                ">
                                    <!-- Companhia -->
                                    <div class="flex flex-col items-center w-24">
                                        <img src="{{ $flight['airline_logo'] }}" alt="Logo" class="h-6 mb-1">
                                        <span class="text-xs font-semibold text-gray-700">{{ $flight['airline'] }}</span>
                                        <span class="text-xs text-gray-400">{{ $flight['travel_class'] }}</span>
                                    </div>
                                    <!-- Horário de saída -->
                                    <div class="flex flex-col items-center w-20">
                                        <span class="text-base font-bold text-gray-900">{{ $flight['departure_time'] }}</span>
                                        <span class="text-xs text-gray-500">{{ $flight['departure_code'] }}</span>
                                    </div>
                                    <!-- Duração -->
                                    <div class="flex flex-col items-center w-20">
                                        <span class="text-xs text-gray-500">{{ $flight['duration'] }}</span>
                                        <span class="text-xs text-gray-400">{{ $flight['type'] }}</span>
                                    </div>
                                    <!-- Horário de chegada -->
                                    <div class="flex flex-col items-center w-20">
                                        <span class="text-base font-bold text-gray-900">{{ $flight['arrival_time'] }}</span>
                                        <span class="text-xs text-gray-500">{{ $flight['arrival_code'] }}</span>
                                    </div>
                                    <!-- Extras -->
                                    <div class="flex flex-col gap-1 w-32 pl-4">
                                        <div class="flex gap-2 text-xs text-gray-500">
                                            @if(in_array('wifi', $flight['extras']))
                                                <i class="fa-solid fa-wifi"></i>
                                            @endif
                                            @if(in_array('entretenimento', $flight['extras']))
                                                <i class="fa-solid fa-tv"></i>
                                            @endif
                                            @if(in_array('lanche', $flight['extras']))
                                                <i class="fa-solid fa-utensils"></i>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            {{ in_array('lanche', $flight['extras']) ? 'Lanche' : ' ' }}
                                        </span>
                                    </div>
                                    <!-- Preço -->
                                    <div class="flex flex-col items-end flex-1">
                                        <span class="text-blue-600 text-xl font-bold">R$ {{ $flight['price'] }}</span>
                                        <span class="text-xs text-gray-500">por pessoa</span>
                                        <span class="text-xs text-gray-400 mt-1">23kg incluído</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between mt-8">
                            <button type="button" class="prev-btn btn-secondary">← Voltar</button>
                            <button type="button" class="btn-primary">Consultar →</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
body { font-family: 'Inter', sans-serif; }
.animate-fade-in { animation: fadeIn 0.7s; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(30px);} to { opacity: 1, transform: none; } }

.form-step { display: none; }
.form-step.active { display: block; animation: fadeIn 0.5s; }
.step-indicator {
    width: 38px; height: 38px; border-radius: 50%; background: #fff;
    border: 3px solid #22c55e; color: #22c55e;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1.2rem; box-shadow: 0 2px 8px #22c55e22;
    transition: all 0.3s;
    z-index: 1;
}
.step-indicator:not(.active) {
    border-color: #d1d5db; color: #d1d5db; background: #f3f4f6;
    box-shadow: none;
}
.step-indicator.active {
    background: linear-gradient(135deg, #22c55e 60%, #0ea5e9 100%);
    color: #fff;
    border-color: #0ea5e9;
    box-shadow: 0 4px 16px #0ea5e955;
}
.step-label {
    margin-left: 10px; margin-right: 10px;
    font-weight: 600; color: #22c55e;
    font-size: 1rem;
}
.step-line {
    flex: 1; height: 3px; background: linear-gradient(90deg, #22c55e, #0ea5e9);
    margin-left: 10px; margin-right: 10px; border-radius: 2px;
}
.input {
    border: 1.5px solid #d1d5db; border-radius: 8px; padding: 10px 14px; width: 100%;
    font-size: 1rem; background: #f9fafb; transition: border 0.2s, box-shadow 0.2s;
    outline: none;
}
.input:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 2px #0ea5e955;
    background: #fff;
}
.btn-primary {
    background: linear-gradient(90deg, #22c55e, #0ea5e9);
    color: #fff; padding: 10px 32px; border-radius: 8px;
    font-weight: 700; font-size: 1rem; border: none;
    box-shadow: 0 2px 8px #0ea5e933;
    transition: background 0.2s, transform 0.2s;
    cursor: pointer;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #0ea5e9, #22c55e);
    transform: translateY(-2px) scale(1.03);
}
.btn-secondary {
    background: #fff; border: 1.5px solid #d1d5db; color: #222;
    padding: 10px 32px; border-radius: 8px; font-weight: 600;
    transition: border 0.2s, background 0.2s, color 0.2s;
    cursor: pointer;
}
.btn-secondary:hover {
    border-color: #0ea5e9; color: #0ea5e9; background: #f0f9ff;
}
.pref-btn, .insurance-btn {
    border: 2px solid #e5e7eb; border-radius: 16px; padding: 22px 0;
    background: #f9fafb; cursor: pointer; font-size: 1rem;
    font-weight: 500; box-shadow: 0 2px 8px #0001;
    transition: border 0.2s, background 0.2s, box-shadow 0.2s, transform 0.2s;
    outline: none;
    min-width: 120px;
}
.pref-btn.selected, .insurance-btn.selected {
    border-color: #22c55e; background: #e6f9f0;
    box-shadow: 0 4px 16px #22c55e33;
    transform: scale(1.04);
}
.pref-btn:hover, .insurance-btn:hover {
    border-color: #0ea5e9; background: #e0f2fe;
    box-shadow: 0 4px 16px #0ea5e933;
    transform: scale(1.03);
}
</style>