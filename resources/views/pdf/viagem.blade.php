@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Roteiro de Viagem</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f0f4f7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #34495e;
            color: #ecf0f1;
            padding: 3rem 0;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5rem;
            margin: 0;
            font-weight: 300;
        }
        .header h1 span {
            font-weight: 700;
        }
        .header .sub {
            font-size: 1rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        .container {
            max-width: 800px;
            margin: -2rem auto 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 2.5rem;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table td.label {
            font-weight: bold;
            width: 130px;
            color: #555;
        }
        .divider {
            border: none;
            border-top: 1px solid #ddd;
            margin: 2.5rem 0;
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #555;
            margin-bottom: 1rem;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }
        .day-section {
            page-break-inside: avoid;
            margin-bottom: 2rem;
        }
        .day-card {
            background: #f9fbfd;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            display: table;
            width: 100%;
        }
        .day-card .icon-cell,
        .day-card .content-cell {
            display: table-cell;
            vertical-align: top;
        }
        .day-card .icon-cell {
            width: 70px;
            text-align: center;
        }
        .day-card .icon-box {
            background-color: #8bb7af;
            width: 50px;
            height: 50px;
            border-radius: 8px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .day-card .icon-box svg {
            width: 30px;
            height: 30px;
            fill: #fff;
        }
        .day-content {
            padding-left: 1.5rem;
        }
        .day-header {
            font-size: 1.15rem;
            font-weight: bold;
            color: #444;
            margin-bottom: 0.5rem;
        }
        .day-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .day-list li {
            font-size: 0.95rem;
            margin-bottom: 0.7rem;
            line-height: 1.4;
        }
        .day-list li .time {
            font-weight: bold;
            color: #34495e;
        }
        .day-list li .location {
            color: #888;
            font-style: italic;
        }
        
        /* Estilos para a seção de voos */
        .voo-card {
            background: #e6f0f5;
            border: 1px solid #cce0eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .voo-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .voo-card-header-left {
            display: flex;
            align-items: center;
        }
        .voo-icon {
            background-color: #48a2d1;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            margin-right: 15px;
        }
        .voo-details-text {
            line-height: 1.2;
        }
        .voo-details-text .line1 {
            font-size: 1rem;
            font-weight: bold;
        }
        .voo-details-text .line2 {
            font-size: 0.8rem;
            color: #555;
        }
        .voo-card-header-right {
            text-align: right;
        }
        .voo-card-header-right .date {
            font-size: 0.9rem;
            font-weight: bold;
        }
        .voo-card-header-right .time {
            font-size: 0.8rem;
            color: #555;
        }
        .voo-route-info {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .voo-route-info .cell {
            display: table-cell;
            vertical-align: top;
            font-size: 0.9rem;
        }
        .voo-route-info .cell.origin {
            width: 40%;
        }
        .voo-route-info .cell.route {
            width: 20%;
            text-align: center;
        }
        .voo-route-info .cell.destination {
            width: 40%;
        }
        .voo-route-info .cell .label {
            font-size: 0.75rem;
            color: #888;
        }
        .voo-route-info .cell .code {
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 5px;
        }
        .voo-route-info .cell .airport-name {
            font-size: 0.75rem;
            color: #555;
        }
        .voo-additional-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-top: 1rem;
            border-top: 1px solid #c9d8e0;
            padding-top: 1rem;
        }
        .voo-additional-info .info-item {
            width: 30%;
        }
        .voo-additional-info .info-label {
            font-weight: bold;
            color: #555;
        }

        /* Estilos para a seção de seguros */
        .seguro-card {
            background: #e6f5e6;
            border-radius: 8px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .seguro-info {
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .seguro-info a {
            color: #2e8b57;
            text-decoration: none;
            word-wrap: break-word;
        }

        /* Estilos para a seção de hotéis */
        .hotel-card {
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 2rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: table;
            width: 100%;
        }
        .hotel-card .image-cell {
            display: table-cell;
            width: 200px; /* Largura fixa da imagem */
            background-color: #dbe4eb;
        }
        .hotel-card .image-cell img {
            width: 100%;
            height: auto;
            max-height: 220px;
            object-fit: cover;
            display: block;
        }
        .hotel-card .details-cell {
            display: table-cell;
            padding: 1.5rem;
            position: relative;
        }
        .hotel-name {
            font-size: 1.0rem;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 0.3rem;
        }
        .hotel-rating {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 1rem;
        }
        .hotel-details {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .hotel-details .cell {
            display: table-cell;
            vertical-align: top;
            padding-right: 1.5rem;
        }
        .hotel-details .cell .label {
            font-size: 0.8rem;
            color: #888;
            font-weight: bold;
        }
        .hotel-details .cell .value {
            font-size: 0.95rem;
            margin-top: 5px;
        }
        .hotel-price {
            text-align: right;
            font-weight: bold;
            font-size: 1.2rem;
            color: #c0392b;
            margin-top: 1rem;
        }
        .hotel-price .label {
            font-size: 0.9rem;
            font-weight: normal;
            color: #555;
        }
        .hotel-total {
            text-align: right;
            font-weight: bold;
            font-size: 1.2rem;
            color: #c0392b;
            margin-top: 1rem;
        }
        .hotel-total .label {
            font-size: 0.9rem;
            font-weight: normal;
            color: #555;
        }
        .hotel-link {
            font-size: 0.85rem;
            color: #3498db;
            text-decoration: none;
            margin-top: 1rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Roteiro de Viagem</h1>
        <div class="sub">Detalhes do seu roteiro em {{ $viagem->destino_viagem ?? 'destino desconhecido' }}</div>
    </div>
    <div class="container">
        <table class="info-table">
            <tr><td class="label">Destino:</td><td>{{ $viagem->destino_viagem ?? '-' }}</td></tr>
            <tr><td class="label">Data de Início:</td><td>{{ $viagem->data_inicio_viagem ? Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') : '-' }}</td></tr>
            <tr><td class="label">Data de Fim:</td><td>{{ $viagem->data_final_viagem ? Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') : '-' }}</td></tr>
            <tr>
                <td class="label">Viajantes:</td>
                <td>
                    @forelse($viagem->viajantes as $v)
                        {{ $v->nome ?? 'Sem nome' }}
                        @if (isset($v->idade)) ({{ $v->idade }} anos) @endif
                        @if(!$loop->last), @endif
                    @empty
                        <span class="empty">Nenhum viajante cadastrado.</span>
                    @endforelse
                </td>
            </tr>
        </table>
        
        <div class="section-title">Objetivos</div>
        <ul style="padding-left: 20px;">
            @forelse($viagem->objetivos as $objetivo)
                <li>{{ $objetivo->nome ?? '-' }}</li>
            @empty
                <li class="empty">Nenhum objetivo cadastrado.</li>
            @endforelse
        </ul>

        <hr class="divider" />
        
        <div class="section-title">Hotéis</div>
        @forelse($viagem->hotel as $hotel)
            <div class="hotel-card">
                <div class="image-cell">
                    <img src="{{ $hotel->image_url ?? 'nada tem' }}" alt="Imagem do Hotel">
                </div>
                <div class="details-cell">
                    <div class="hotel-name">{{ $hotel->nome_hotel ?? '-' }}</div>
                    <div class="hotel-rating">Avaliação: {{ $hotel->avaliacao ?? '-' }}</div>
                    
                    <div class="hotel-details">
                        <div class="cell">
                            <div class="label">Check-in</div>
                            <div class="value">{{ $hotel->data_check_in ? Carbon::parse($hotel->data_check_in)->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="cell">
                            <div class="label">Check-out</div>
                            <div class="value">{{ $hotel->data_check_out ? Carbon::parse($hotel->data_check_out)->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="cell">
                            <div class="label">Duração</div>
                            <div class="value">
                                @php
                                    $dias_hospedagem = 0;
                                    if ($hotel->data_check_in && $hotel->data_check_out) {
                                        $check_in = Carbon::parse($hotel->data_check_in);
                                        $check_out = Carbon::parse($hotel->data_check_out);
                                        $dias_hospedagem = $check_in->diffInDays($check_out);
                                    }
                                    $preco_total = ($dias_hospedagem > 0 && $hotel->preco) ? $dias_hospedagem * $hotel->preco : 0;
                                @endphp
                                {{ $dias_hospedagem }} noites
                            </div>
                        </div>
                    </div>
                    
                    <div class="hotel-price">
                        <span class="label">Valor por noite: </span>R$ {{ number_format($hotel->preco, 2, ',', '.') ?? '0,00' }}
                    </div>
                    <div class="hotel-total">
                        <span class="label">Total estimado da hospedagem:</span> R$ {{ number_format($preco_total, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        @empty
            <p class="empty">Nenhum hotel cadastrado para esta viagem.</p>
        @endforelse

        <hr class="divider" />

        <div class="section-title">Seguros</div>
        @forelse($viagem->seguros as $seguro)
            <div class="seguro-card">
                <div class="seguro-info">
                    <p><b>{{ $seguro->site ?? '-' }}</b> - Preço: R$ {{ number_format($seguro->preco ?? 0, 2, ',', '.') }}</p>
                    <p><b>Informações:</b> {{ $seguro->dados ?? 'Nenhuma informação adicional.' }}</p>
                    <p><b>Link:</b> <a href="{{ $seguro->link ?? '#' }}" target="_blank" style="word-break: break-all;">{{ $seguro->link ?? 'N/A' }}</a></p>
                </div>
            </div>
        @empty
            <p class="empty">Nenhum seguro cadastrado para esta viagem.</p>
        @endforelse

        <hr class="divider" />
        
        <div class="section-title">Voos</div>
        @forelse($viagem->voos as $voo)
            <div class="voo-card">
                <div class="voo-card-header">
                    <div class="voo-card-header-left">
                        <div class="voo-icon">{{ $loop->iteration }}</div>
                        <div class="voo-details-text">
                            <div class="line1">{{ $voo->desc_aeronave_voo ?? '-' }}</div>
                            <div class="line2">{{ $voo->companhia_voo ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="voo-card-header-right">
                        <div class="date">{{ $voo->data_hora_partida ? Carbon::parse($voo->data_hora_partida)->format('d/m/Y') : '-' }}</div>
                        <div class="time">{{ $voo->data_hora_partida ? Carbon::parse($voo->data_hora_partida)->format('H:i') : '-' }}</div>
                    </div>
                </div>
                
                <div class="voo-route-info">
                    <div class="cell origin">
                        <div class="label">Origem</div>
                        <div class="code">{{ $voo->origem_voo ?? '-' }}</div>
                        <div class="airport-name">{{ $voo->origem_nome_voo ?? '' }}</div>
                    </div>
                    <div class="cell route">
                        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNTAgMTAwIj48cGF0aCBkPSJNMCA1MEgyNDcuNSIgc3Ryb2tlPSIjMjk4M2I4IiBzdHJva2Utd2lkdGg9IjEuNSIgZmlsbD0ibm9uZSIvPjxjaXJjbGUgY3g9IjIuNSIgY3k9IjUwIiByPSIyLjUiIGZpbGw9IiMyOTgzYjgiLz48Y2lyY2xlIGN4PSI2Ny41IiBjeT0iNTAiIHI9IjIuNSIgZmlsbD0iI2Q3ZWVmOSIvPjxjaXJjbGUgY3g9IjEyMi41IiBjeT0iNTAiIHI9IjIuNSIgZmlsbD0iI2Q3ZWVmOSIvPjxjaXJjbGUgY3g9IjE4Mi41IiBjeT0iNTAiIHI9IjIuNSIgZmlsbD0iI2Q3ZWVmOSIvPjxjaXJjbGUgY3g9IjI0Ny41IiBjeT0iNTAiIHI9IjIuNSIgZmlsbD0iIzI5ODNiOCIvPjwvc3ZnPg==" style="width: 100%; height: auto; display: block; margin: 0 auto;"/>
                    </div>
                    <div class="cell destination">
                        <div class="label">Destino</div>
                        <div class="code">{{ $voo->destino_voo ?? '-' }}</div>
                        <div class="airport-name">{{ $voo->destino_nome_voo ?? '' }}</div>
                    </div>
                </div>
                <div class="voo-additional-info">
                    <div class="info-item">
                        <div class="info-label">Classe:</div>
                        <span>{{ $voo->classe_voo ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Preço:</div>
                        <span>R$ {{ number_format($voo->preco_voo, 2, ',', '.') ?? '0,00' }}</span>
                    </div>
                    <div class="info-item">
                        <div class="info-label">N° Voo:</div>
                        <span>{{ $voo->numero_voo ?? '-' }}</span>
                    </div>
                </div>
                @if ($voo->conexao_voo)
                <div class="voo-additional-info" style="margin-top:0.5rem; padding-top:0.5rem; border-top:1px dashed #c9d8e0;">
                    <div class="info-item" style="width:100%;">
                         <div class="info-label">Conexão:</div>
                         <span>{{ $voo->conexao_voo ?? '-' }} - {{ $voo->conexao_nome_voo ?? '-' }}</span>
                    </div>
                </div>
                @endif
            </div>
        @empty
            <p class="empty">Nenhum voo cadastrado para esta viagem.</p>
        @endforelse

        <hr class="divider" />
        
        <div class="section-title">Roteiro Detalhado</div>
        
        @php
            $dias = [];
            foreach($viagem->pontosInteresse as $ponto) {
                $data = !empty($ponto->data_ponto_interesse) ? Carbon::parse($ponto->data_ponto_interesse)->format('d/m/Y') : 'Data não definida';
                $dias[$data][] = $ponto;
            }
            $icones = [
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M542.2 32.7c-5.6-2.5-12.1-1.2-16.1 3.2L304 221.7l-94.2-22.6-47.5-120.7c-2.7-6.8-9.4-11.2-16.8-11.2s-14.1 4.4-16.8 11.2l-47.5 120.7-94.2 22.6L50.2 36c-4.1-4.4-10.6-5.7-16.1-3.2S19.5 41.8 19 48.3L1.5 258.9c-.3 4.1-.2 8.3.3 12.3l11.4 86.4c.5 4.1 2.3 8.1 5.3 11.2L282.6 498.4c5.7 5.7 13.3 8.6 20.9 8.6s15.2-2.9 20.9-8.6l253.9-204.6c3-3.1 4.8-7.1 5.3-11.2l11.4-86.4c.5-4 .6-8.2.3-12.3L557 48.3c-.5-6.5-6.1-11.1-14.8-15.6zM288 421.2L85.6 257.7l86.6-20.8L288 344.2l115.8-107.3 86.6 20.8L288 421.2z"/></svg>',
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M592 128h-32v32c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32H256v32c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32H48c-26.5 0-48 21.5-48 48v224c0 26.5 21.5 48 48 48h544c26.5 0 48-21.5 48-48V176c0-26.5-21.5-48-48-48zM32 368V176c0-8.8 7.2-16 16-16h80v32c0 17.7 14.3 32 32 32s32-14.3 32-32v-32h160v32c0 17.7 14.3 32 32 32s32-14.3 32-32v-32h80c8.8 0 16 7.2 16 16v192c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16zM464 96H288v32h176V96zm-96-64h96c17.7 0 32 14.3 32 32v32h32c17.7 0 32 14.3 32 32v128c0 17.7-14.3 32-32 32h-32v32c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32H256v32c0 17.7-14.3 32-32 32s-32-14.3-32-32v-32H160c-17.7 0-32-14.3-32-32V96c0-17.7 14.3-32 32-32h32V32c0-17.7 14.3-32 32-32h96c17.7 0 32 14.3 32 32V64zM256 64h128V32h-128v32z"/></svg>',
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M495.9 203.2c-5.7-11.7-16.4-18.2-28.5-18.2H388.5c-4.4 0-8.6 2.1-11.4 5.7L256 312.4 134.9 190.7c-2.8-3.6-7-5.7-11.4-5.7H44.6c-12.1 0-22.8 6.5-28.5 18.2-5.7 11.7-6.2 25.3-1.4 37.5L245.2 466.7c7.7 19.3 28.7 28.7 48.7 21.8L497.3 240.7c4.8-12.2 4.3-25.8-1.4-37.5z"/></svg>',
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M495.9 203.2c-5.7-11.7-16.4-18.2-28.5-18.2H388.5c-4.4 0-8.6 2.1-11.4 5.7L256 312.4 134.9 190.7c-2.8-3.6-7-5.7-11.4-5.7H44.6c-12.1 0-22.8 6.5-28.5 18.2-5.7 11.7-6.2 25.3-1.4 37.5L245.2 466.7c7.7 19.3 28.7 28.7 48.7 21.8L497.3 240.7c4.8-12.2 4.3-25.8-1.4-37.5z"/></svg>'
            ];
            $i = 0;
        @endphp

        @forelse($dias as $data => $pontos)
            <div class="day-section">
                <div class="day-card">
                    <div class="icon-cell">
                        <div class="icon-box">
                             {!! $icones[$i % count($icones)] !!}
                        </div>
                    </div>
                    <div class="content-cell">
                        <div class="day-header">Dia {{ $loop->iteration }} <span style="font-weight:normal; color:#888; font-size:0.9em;">({{ $data }})</span></div>
                        <ul class="day-list">
                            @foreach($pontos as $ponto)
                                <li>
                                    @if (!empty($ponto->hora_ponto_interesse))
                                        <span class="time">{{ Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') }}</span> -
                                    @endif
                                    {{ $ponto->nome_ponto_interesse ?? '-' }}
                                    @if (!empty($ponto->desc_ponto_interesse))
                                        <span class="location">{{ $ponto->desc_ponto_interesse }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @php $i++; @endphp
        @empty
            <div class="day-section">
                <p class="empty">Nenhum ponto de interesse cadastrado para esta viagem.</p>
            </div>
        @endforelse
    </div>
</body>
</html>