<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }} - Otimiza Anfitriã</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-32x32.png') }}">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: #2298b6;
            --brand-dark: #135161;
            --brand-accent: #99d4de;
            --slate-800: #1e293b;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 13px;
            line-height: 1.5;
            padding: 24px 0 48px;
        }

        /* Barra de Ações Superior (Apenas Tela) */
        .action-bar {
            max-width: 210mm;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 12px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--slate-200);
        }

        .action-bar-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(34, 152, 182, 0.35);
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--slate-100);
            color: var(--slate-600);
            border: 1px solid var(--slate-200);
        }

        .btn-secondary:hover {
            background: var(--slate-200);
            color: var(--slate-800);
        }

        .tip-text {
            font-size: 12px;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Folha A4 */
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 18mm 20mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Cabeçalho */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--brand);
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-logo {
            height: 64px;
            width: auto;
            object-fit: contain;
        }

        .header-brand-info h1 {
            font-size: 20px;
            font-weight: 900;
            color: var(--brand-dark);
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }

        .header-brand-info p {
            font-size: 11px;
            color: var(--slate-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-doc {
            text-align: right;
        }

        .doc-badge {
            display: inline-block;
            background: var(--brand-dark);
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .doc-title {
            font-size: 18px;
            font-weight: 900;
            color: var(--slate-800);
            line-height: 1.2;
        }

        .doc-meta {
            font-size: 11px;
            color: var(--slate-500);
            margin-top: 4px;
        }

        /* Grid de Informações */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-card {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .info-card-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--brand);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 12px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--slate-500);
            font-weight: 500;
        }

        .info-value {
            font-weight: 700;
            color: var(--slate-800);
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status-active {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .status-completed {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Tabela de Enxovais */
        .section-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--slate-800);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .items-table th {
            background: var(--brand-dark);
            color: white;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }

        .items-table th.text-center { text-align: center; }
        .items-table th.text-right { text-align: right; }

        .items-table td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--slate-200);
            font-size: 12px;
            color: var(--slate-800);
        }

        .items-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .items-table .text-center { text-align: center; }
        .items-table .text-right { text-align: right; }

        .item-name {
            font-weight: 700;
            color: var(--slate-800);
        }

        .item-sub {
            font-size: 10px;
            color: var(--slate-500);
        }

        .item-qty {
            font-weight: 800;
            color: var(--brand-dark);
        }

        /* Totais e Condições de Pagamento */
        .totals-section {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 20px;
            margin-bottom: 22px;
        }

        .payment-policy-box {
            flex: 1;
            background: #f0fdf4;
            border: 1.5px solid #22c55e;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .policy-header {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #15803d;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .policy-text {
            font-size: 12.5px;
            font-weight: 800;
            color: #14532d;
            line-height: 1.4;
        }

        .policy-sub {
            font-size: 11.5px;
            color: #166534;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed rgba(34, 197, 94, 0.4);
            font-weight: 600;
        }

        .totals-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 0;
        }

        .totals-box {
            width: 280px;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            padding: 14px 18px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 12px;
        }

        .totals-row.grand-total {
            border-top: 2px solid var(--brand);
            margin-top: 8px;
            padding-top: 10px;
        }

        .grand-total-label {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--brand-dark);
        }

        .grand-total-value {
            font-size: 18px;
            font-weight: 900;
            color: var(--brand-dark);
        }

        /* Observações */
        .notes-card {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 24px;
            font-size: 11.5px;
            color: #92400e;
        }

        .notes-card strong {
            display: block;
            margin-bottom: 4px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.5px;
        }

        /* Termo e Assinaturas */
        .terms-block {
            margin-top: auto;
            padding-top: 18px;
            border-top: 1px dashed var(--slate-200);
            page-break-inside: avoid;
        }

        .terms-text {
            font-size: 10.5px;
            color: var(--slate-500);
            text-align: justify;
            margin-bottom: 34px;
            line-height: 1.4;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 24px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1.5px solid var(--slate-800);
            margin-bottom: 6px;
            height: 35px;
        }

        .signature-name {
            font-size: 11px;
            font-weight: 800;
            color: var(--slate-800);
        }

        .signature-role {
            font-size: 10px;
            color: var(--slate-500);
        }

        /* Rodapé */
        .footer {
            border-top: 1px solid var(--slate-200);
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: var(--slate-400);
        }

        /* Regras de Impressão */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .a4-sheet {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- BARRA DE AÇÕES (Exibida somente na tela do navegador) -->
    <div class="action-bar no-print">
        <div class="action-bar-buttons">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.033-.42-2.12-.42-3.329 0-4.418 3.582-8 8-8s8 3.582 8 8c0 1.21-.18 2.296-.42 3.329M6 18h12M8 14h8m-10 7h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2z"/>
                </svg>
                Imprimir Pedido / Salvar em PDF
            </button>
            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar aos Detalhes
            </a>
        </div>
    </div>

    <!-- FOLHA A4 PRINCIPAL -->
    <div class="a4-sheet">

        <div>
            <!-- CABEÇALHO -->
            <div class="header">
                <div class="header-brand">
                    <img src="{{ asset('images/logocolorida.png') }}" alt="Otimiza Anfitriã" class="header-logo">
                    <div class="header-brand-info">
                        <h1>Otimiza Anfitriã</h1>
                        <p>Locação de Enxovais para Hospedagens</p>
                    </div>
                </div>
                <div class="header-doc">                   
                    <div class="doc-title">PEDIDO #{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div class="doc-meta">Emissão: {{ \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}</div>
                </div>
            </div>

            <!-- INFORMAÇÕES DO PEDIDO E PERÍODO -->
            <div class="info-grid">
                <!-- Card Cliente -->
                <div class="info-card">
                    <div class="info-card-title">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Dados do Cliente / Identificador
                    </div>
                    <div class="info-row">
                        <span class="info-label">Identificação:</span>
                        <span class="info-value">{{ $reservation->identifier ?? 'Cliente não identificado' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status do Pedido:</span>
                        <span class="info-value">
                            @if($reservation->status == 'active')
                                <span>Ativo</span>
                            @elseif($reservation->status == 'completed')
                                <span>Encerrado</span>
                            @else
                                <span>Cancelado</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cód. Interno:</span>
                        <span class="info-value">#{{ $reservation->id }}</span>
                    </div>
                </div>

                <!-- Card Período -->
                <div class="info-card">
                    <div class="info-card-title">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Período da Reserva
                    </div>
                    <div class="info-row">
                        <span class="info-label">Data de Retirada (Início):</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Data de Devolução (Fim):</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</span>
                    </div>
                    @php
                        $start = \Carbon\Carbon::parse($reservation->start_date);
                        $end = \Carbon\Carbon::parse($reservation->end_date);
                        $days = $start->diffInDays($end) ?: 1;
                    @endphp
                    <div class="info-row">
                        <span class="info-label">Duração Prevista:</span>
                        <span class="info-value">{{ $days }} {{ $days == 1 ? 'dia' : 'dias' }}</span>
                    </div>
                </div>
            </div>

            <!-- TABELA DE ITENS -->
            <div class="section-title">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Itens e Enxovais Reservados
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Enxoval / Descrição do Item</th>
                        <th style="width: 140px;">Categoria / Tamanho</th>
                        <th style="width: 70px;" class="text-center">Qtd</th>
                        <th style="width: 100px;" class="text-right">Valor Unit.</th>
                        <th style="width: 110px;" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservation->items as $index => $item)
                    @php
                        $unitPrice = $item->unit_price > 0 ? $item->unit_price : ($item->product->price ?? 0);
                        $subtotal = $unitPrice * $item->quantity;
                    @endphp
                    <tr>
                        <td class="text-center" style="color: var(--slate-500); font-size: 11px;">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <div class="item-name">{{ $item->product->name ?? 'Enxoval' }}</div>
                        </td>
                        <td>
                            <div class="item-sub">{{ $item->product->category ?? '-' }} &bull; {{ $item->product->size ?? 'Padrão' }}</div>
                        </td>
                        <td class="text-center item-qty">
                            {{ $item->quantity }}
                        </td>
                        <td class="text-right" style="color: var(--slate-600);">
                            R$ {{ number_format($unitPrice, 2, ',', '.') }}
                        </td>
                        <td class="text-right" style="font-weight: 800; color: var(--slate-800);">
                            R$ {{ number_format($subtotal, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- QUADRO DE TOTAIS -->
            <div class="totals-container">                
                <div class="totals-box">
                    <div class="totals-row">
                        <span class="info-label">Total de Itens / Peças:</span>
                        <span class="info-value">{{ $reservation->items->sum('quantity') }} peças</span>
                    </div>
                    <div class="totals-row">
                        <span class="info-label">Subtotal Geral:</span>
                        <span class="info-value">R$ {{ number_format($reservation->total_price ?? 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="totals-row grand-total">
                        <span class="grand-total-label">Valor Final:</span>
                        <span class="grand-total-value">R$ {{ number_format($reservation->total_price ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>                   
        </div>

        <!-- TERMO DE RECEBIMENTO E ASSINATURAS -->
        <div class="terms-block">
            <p class="terms-text">
                <strong>Condição de Reserva e Recebimento:</strong> Para a reserva, solicitamos o pagamento de sinal de 50% do valor do pedido. <br><br> Declaro ter conferido e recebido todos os enxovais e peças acima relacionados em perfeito estado de conservação, higienização e embalagem. Comprometo-me a zelar pela integridade dos itens e devolvê-los no prazo acordado ({{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}).
            </p>

            <div class="signatures-grid">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $reservation->identifier ?? 'Cliente / Responsável' }}</div>
                    <div class="signature-role">Assinatura do Hóspede ou Locatário</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name">Otimiza Anfitriã</div>
                    <div class="signature-role">Responsável pela Conferência</div>
                </div>
            </div>

            <!-- RODAPÉ -->
            <div class="footer">
                <span>Otimiza Anfitriã • Locação de Enxovais</span>
                <span>Documento emitido eletronicamente em {{ \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y \à\s H:i:s') }}</span>
                <span>Página 1 de 1</span>
            </div>
        </div>

    </div>

</body>
</html>

