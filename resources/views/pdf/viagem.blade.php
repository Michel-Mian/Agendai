@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Roteiro de Viagem - {{ $viagem->destino_viagem ?? 'Detalhes' }}</title>
    <style>
        /* BASE E TIPOGRAFIA */
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f4f7fa;
            color: #343a40;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 850px;
            margin: -3rem auto 3rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            border: 1px solid #e9ecef;
        }

        /* HEADER (AZUL PROFUNDO) */
        .header {
            background-color: #0056b3; 
            color: #ffffff;
            padding: 4rem 0;
            text-align: center;
        }
        .header h1 {
            font-size: 3rem;
            margin: 0;
            font-weight: 300;
        }
        .header .sub {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* SEPARADORES E TÍTULOS */
        .divider {
            border: none;
            border-top: 2px solid #e9ecef;
            margin: 3rem 0;
        }
        .section-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 8px;
            border-bottom: 3px solid; /* Linha de destaque, cor definida abaixo */
            display: inline-block;
        }
        
        /* CORES ESPECÍFICAS POR SEÇÃO */
        /* Visão Geral (Base) */
        .section-title.geral {
            color: #0056b3;
            border-bottom-color: #0056b3;
        }
        /* Objetivos e Foco da Viagem */
        .section-title.objetivos {
            color: #17a2b8; /* Ciano/Claro */
            border-bottom-color: #17a2b8;
        }
        ul.objectives-list li:before {
            color: #17a2b8; /* Cor do checkmark */
        }
        /* Hospedagem */
        .section-title.hospedagem {
            color: #e98074;
            border-bottom-color: #e98074;
        }
        .hotel-card {
            border-left: 5px solid #e98074;
            border-radius: 10px;
            border: 1px solid #dcdcdc;
            margin-bottom: 2.5rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            page-break-inside: avoid;
        }
        .hotel-price, .hotel-total {
            color: #cc0000; /* Preço mantém vermelho padrão de aviso */
        }
        
        /* Seguros */
        .section-title.seguros {
            color: #2a9d8f;
            border-bottom-color: #2a9d8f;
        }
        .seguro-card {
            background: #e6f6f5; /* Fundo claro com a cor do seguro */
            border-left-color: #2a9d8f;
        }
        .seguro-info b {
            color: #2a9d8f;
        }
        .seguro-info a {
            color: #2a9d8f;
        }

        /* Voos */
        .section-title.voos {
            color: #f4a261;
            border-bottom-color: #f4a261;
        }
        .voo-icon {
            background-color: #f4a261; /* Cor de destaque para voos */
        }
        .voo-route-info .cell.route img {
            background: #f4a261;
        }

        /* Roteiro */
        .section-title.roteiro {
            color: #5e60ce;
            border-bottom-color: #5e60ce;
        }
        .day-card {
            border-left-color: #5e60ce;
        }
        .day-card .icon-box {
            background-color: #5e60ce;
        }
        .day-header {
            color: #5e60ce;
        }
        
        /* ESTILOS COMUNS QUE DEPENDEM APENAS DA ESTRUTURA */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
        }
        .info-table td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 1px dashed #e9ecef;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }
        .info-table td.label {
            font-weight: 700;
            width: 150px;
            color: #495057;
        }
        .empty {
            color: #999;
            font-style: italic;
        }
        ul.objectives-list {
            list-style: none;
            padding-left: 0;
        }
        ul.objectives-list li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }
        ul.objectives-list li:before {
            content: "✓"; 
            font-weight: bold;
            display: inline-block;
            width: 1em;
            margin-left: -1.5em;
        }
        .hotel-card .image-cell {
            display: table-cell;
            width: 200px;
            background-color: #e9ecef;
        }
        .hotel-card .image-cell img {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
            display: block;
        }
        .hotel-card .details-cell {
            padding: 1.5rem;
            display: table-cell;
        }
        .hotel-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 0.2rem;
        }
        .hotel-rating {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .hotel-details {
            display: flex;
            gap: 20px;
            margin-bottom: 1rem;
            border-top: 1px dashed #e9ecef;
            padding-top: 1rem;
        }
        .hotel-details .cell .label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 600;
        }
        .hotel-details .cell .value {
            font-size: 1rem;
            font-weight: bold;
            margin-top: 5px;
        }
        .seguro-card {
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
            border-left: 5px solid; 
            page-break-inside: avoid;
        }
        .seguro-info p {
            margin: 0.75rem 0;
            font-size: 0.95rem;
        }
        .voo-card {
            background: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            page-break-inside: avoid;
        }
        .voo-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1rem;
        }
        .voo-card-header-left {
            display: flex;
            align-items: center;
        }
        .voo-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            margin-right: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .voo-details-text .line1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #343a40;
        }
        .voo-details-text .line2 {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .voo-route-info {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .voo-route-info .cell {
            display: table-cell;
            vertical-align: top;
            font-size: 0.9rem;
            text-align: center;
        }
        .voo-route-info .cell.origin {
            width: 40%;
            text-align: left;
        }
        .voo-route-info .cell.destination {
            width: 40%;
            text-align: right;
        }
        .voo-route-info .cell .label {
            font-size: 0.75rem;
            color: #999;
        }
        .voo-route-info .cell .code {
            font-size: 1.6rem;
            font-weight: 700;
            margin-top: 5px;
            color: #343a40;
        }
        .voo-route-info .cell.route img {
            max-width: 100%;
            height: 2px;
            border-radius: 1px;
            margin-top: 25px;
        }
        .day-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            border-left: 5px solid; 
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            width: 100%;
        }
        .day-card .icon-cell {
            width: 60px;
            flex-shrink: 0;
            text-align: center;
        }
        .day-card .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .day-card .icon-box svg {
            width: 20px;
            height: 20px;
            fill: #fff;
        }
        .day-card .content-cell {
            padding-left: 1.5rem;
            flex-grow: 1;
        }
        .day-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .day-list li {
            font-size: 0.95rem;
            margin-bottom: 1rem;
            padding-left: 10px;
            border-left: 2px solid #e9ecef;
        }
        .day-list li:last-child {
            border-left: none;
            padding-left: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ROTEIRO DE VIAGEM</h1>
        <div class="sub">Seu Plano Detalhado para **{{ $viagem->destino_viagem ?? 'uma Jornada Incrível' }}**</div>
    </div>

    <div class="container">
        
        <div class="section-title geral">Visão Geral da Viagem</div>
        <table class="info-table">
            <tr><td class="label">Destino:</td><td>**{{ $viagem->destino_viagem ?? 'Não Informado' }}**</td></tr>
            <tr><td class="label">Início:</td><td>{{ $viagem->data_inicio_viagem ? Carbon::parse($viagem->data_inicio_viagem)->format('d/m/Y') : '-' }}</td></tr>
            <tr><td class="label">Fim:</td><td>{{ $viagem->data_final_viagem ? Carbon::parse($viagem->data_final_viagem)->format('d/m/Y') : '-' }}</td></tr>
            <tr>
                <td class="label">Viajantes:</td>
                <td>
                    @forelse($viagem->viajantes ?? [] as $v)
                        {{ $v->nome ?? 'Sem nome' }} 
                        @if (isset($v->idade)) ({{ $v->idade }} anos) @endif 
                        @if(!$loop->last), @endif
                    @empty
                        <span class="empty">Nenhum viajante cadastrado.</span>
                    @endforelse
                </td>
            </tr>
        </table>
        
        <div class="section-title objetivos">Objetivos e Foco da Viagem</div>
        <ul class="objectives-list">
            @forelse($viagem->objetivos ?? [] as $objetivo)
                <li>{{ $objetivo->nome ?? 'Objetivo não detalhado.' }}</li>
            @empty
                <li class="empty">Nenhum objetivo cadastrado.</li>
            @endforelse
        </ul>

        <hr class="divider" />
        
        <div class="section-title hospedagem">Hospedagem</div>
        @forelse($viagem->hotel ?? [] as $hotel)
            @php
                $check_in = $hotel->data_check_in ? Carbon::parse($hotel->data_check_in) : null;
                $check_out = $hotel->data_check_out ? Carbon::parse($hotel->data_check_out) : null;
                $dias_hospedagem = ($check_in && $check_out) ? $check_in->diffInDays($check_out) : 0;
                $preco_total = ($dias_hospedagem > 0 && isset($hotel->preco)) ? $dias_hospedagem * $hotel->preco : 0;
            @endphp
            <div class="hotel-card">
                <div class="image-cell">
                    <img src="{{ $hotel->image_url ?? 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjIwIiB2aWV3Qm94PSIwIDAgMjAwIDIyMCI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMjAiIGZpbGw9IiNlOWVjZWYiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjExIiBmaWxsPSIjNjc3ZDc1IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjM1ZW0iPlNFTSBJTUFHRU0gREUgSE9URUw8L3RleHQ+PC9zdmc+' }}" alt="Imagem do Hotel">
                </div>
                <div class="details-cell">
                    <div class="hotel-name">{{ $hotel->nome_hotel ?? 'Hotel Não Informado' }}</div>
                    <div class="hotel-rating">Avaliação: **{{ $hotel->avaliacao ?? '-' }}** estrelas</div>
                    
                    <div class="hotel-details">
                        <div class="cell">
                            <div class="label">CHECK-IN</div>
                            <div class="value">{{ $check_in ? $check_in->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="cell">
                            <div class="label">CHECK-OUT</div>
                            <div class="value">{{ $check_out ? $check_out->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="cell">
                            <div class="label">DURAÇÃO</div>
                            <div class="value">**{{ $dias_hospedagem }}** noites</div>
                        </div>
                    </div>
                    
                    <div class="hotel-price">
                        <span class="label">Valor por noite: </span>R$ **{{ number_format($hotel->preco ?? 0, 2, ',', '.') }}**
                    </div>
                    @if ($dias_hospedagem > 0 && isset($hotel->preco))
                        <div class="hotel-total">
                            <span class="label">Total estimado da hospedagem:</span> R$ **{{ number_format($preco_total, 2, ',', '.') }}**
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="empty">Nenhum hotel cadastrado para esta viagem.</p>
        @endforelse

        <hr class="divider" />

        <div class="section-title seguros">Seguros de Viagem</div>
        @forelse($viagem->seguros ?? [] as $seguro)
            <div class="seguro-card">
                <div class="seguro-info">
                    <p>
                        <b>{{ $seguro->site ?? 'Site Não Informado' }}</b> 
                        - Preço: R$ **{{ number_format($seguro->preco ?? 0, 2, ',', '.') }}**
                    </p>
                    <p><b>Informações:</b> {{ $seguro->dados ?? 'Nenhuma informação adicional.' }}</p>
                    <p><b>Link:</b> <a href="{{ $seguro->link ?? '#' }}" target="_blank" style="word-break: break-all;">{{ $seguro->link ?? 'N/A' }}</a></p>
                </div>
            </div>
        @empty
            <p class="empty">Nenhum seguro cadastrado para esta viagem.</p>
        @endforelse

        <hr class="divider" />
        
        <div class="section-title voos">Detalhes dos Voos</div>
        @forelse($viagem->voos ?? [] as $voo)
            @php
                $data_hora_partida = $voo->data_hora_partida ? Carbon::parse($voo->data_hora_partida) : null;
            @endphp
            <div class="voo-card">
                <div class="voo-card-header"> 
                    <div class="voo-card-header-left"> 
                        <div class="voo-icon">{{ $loop->iteration }}</div> 
                        <div class="voo-details-text"> 
                            <div class="line1">{{ $voo->desc_aeronave_voo ?? 'Voo Não Informado' }}</div> 
                            <div class="line2">{{ $voo->companhia_voo ?? 'Companhia Aérea' }}</div> 
                        </div> 
                    </div> 
                    <div class="voo-card-header-right"> 
                        <div class="date">{{ $data_hora_partida ? $data_hora_partida->format('d/m/Y') : '-' }}</div> 
                        <div class="time">Partida: **{{ $data_hora_partida ? $data_hora_partida->format('H:i') : '-' }}**</div> 
                    </div> 
                </div> 
                
                <div class="voo-route-info">
                    <div class="cell origin">
                        <div class="label">ORIGEM</div>
                        <div class="code">{{ $voo->origem_voo ?? '???' }}</div>
                        <div class="airport-name">{{ $voo->origem_nome_voo ?? 'Aeroporto' }}</div>
                    </div>
                    <div class="cell route">
                        <div style="display: block; width: 100%;">
                            <img class="route-icon" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSI0cHgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMiIgeT0iMiIgZmlsbD0iI2Y0YTI2MSIvPjwvcmVjdD48L3N2Zz4=" alt="Linha de Rota" style="max-width: 100%; height: 2px; background: #f4a261; border-radius: 1px; margin-top: 25px;"/>
                        </div>
                    </div>
                    <div class="cell destination">
                        <div class="label">DESTINO</div>
                        <div class="code">{{ $voo->destino_voo ?? '???' }}</div>
                        <div class="airport-name">{{ $voo->destino_nome_voo ?? 'Aeroporto' }}</div>
                    </div>
                </div>

                <div class="voo-additional-info">
                    <div class="info-item">
                        <div class="info-label">Classe:</div>
                        <span>{{ $voo->classe_voo ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Preço:</div>
                        <span>R$ **{{ number_format($voo->preco_voo ?? 0, 2, ',', '.') }}**</span>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nº Voo / Reserva:</div>
                        <span>{{ $voo->numero_voo ?? '-' }} / {{ $voo->codigo_reserva_voo ?? '-' }}</span>
                    </div>
                </div>
                
                @if ($voo->conexao_voo)
                <div class="voo-additional-info" style="margin-top:0.5rem; padding-top:0.5rem; border-top:1px dashed #e9ecef;">
                    <div class="info-item" style="width:100%;">
                        <div class="info-label">Conexão (Escala):</div>
                        <span>**{{ $voo->conexao_voo ?? '-' }}** - {{ $voo->conexao_nome_voo ?? 'Nome do Aeroporto de Conexão' }}</span>
                    </div>
                </div>
                @endif
            </div>
        @empty
            <p class="empty">Nenhum voo cadastrado para esta viagem.</p>
        @endforelse

        <hr class="divider" />
        
        <div class="section-title roteiro">Roteiro Diário Detalhado</div>
        
        @php 
            $dias = collect($viagem->pontosInteresse ?? [])
                ->map(function ($ponto) {
                    $ponto->data_formatada = !empty($ponto->data_ponto_interesse) 
                        ? Carbon::parse($ponto->data_ponto_interesse)->format('d/m/Y') 
                        : 'Data não definida';
                    return $ponto;
                })
                ->groupBy('data_formatada')
                ->sortKeys();
            
            // Ícones mais temáticos e limpos
            $icones = [ 
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M48 200h416c17.7 0 32-14.3 32-32s-14.3-32-32-32H48c-17.7 0-32 14.3-32 32s14.3 32 32 32zm0 160h416c17.7 0 32-14.3 32-32s-14.3-32-32-32H48c-17.7 0-32 14.3-32 32s14.3 32 32 32z"/></svg>', // Avião
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 32c12.5 0 24.5 3.1 35.3 8.8l144 72c6.9 3.4 13 8.3 17.8 14.2L498.4 227c2.3 2.9 3.6 6.3 3.6 9.8c0 8-6.5 14.5-14.5 14.5H448V496c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V251.3H14.5C6.5 251.3 0 244.8 0 236.8c0-3.5 1.3-6.9 3.6-9.8L59.1 115c4.8-5.9 10.9-10.8 17.8-14.2l144-72C231.5 35.1 243.5 32 256 32zm-8 160c-2.7 0-5-2.2-5-5s2.2-5 5-5h16c2.7 0 5 2.2 5 5s-2.2 5-5 5h-16zM155.1 192h16c2.7 0 5-2.2 5-5s-2.2-5-5-5h-16c-2.7 0-5 2.2-5 5s2.2 5 5 5zM357.5 192h16c2.7 0 5-2.2 5-5s-2.2-5-5-5h-16c-2.7 0-5 2.2-5 5s2.2 5 5 5zM435.9 192h16c2.7 0 5-2.2 5-5s-2.2-5-5-5h-16c-2.7 0-5 2.2-5 5s2.2 5 5 5z"/></svg>', // Edifício (Hotel)
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 0 0 128 64 64 0 1 0 0-128z"/></svg>', // Local (Pin)
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 32a224 224 0 1 0 0 448 224 224 0 1 0 0-448zm-16 64h32c8.8 0 16 7.2 16 16v160c0 8.8-7.2 16-16 16h-32c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"/></svg>' // Relógio (Atividade)
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
                        <div class="day-header">
                            Dia {{ $loop->iteration }} 
                            <span style="font-weight:400; color:#6c757d; font-size:0.8em;">
                                &bull; {{ $data }}
                            </span>
                        </div>
                        <ul class="day-list">
                            @foreach($pontos as $ponto)
                                <li>
                                    @if (!empty($ponto->hora_ponto_interesse))
                                        <span class="time">{{ Carbon::parse($ponto->hora_ponto_interesse)->format('H:i') }}</span>
                                    @endif
                                    **{{ $ponto->nome_ponto_interesse ?? 'Atividade Sem Nome' }}**
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