<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('reservations.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-dark bg-white px-3 py-1 rounded-full border border-slate-200 shadow-2xs transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        <span>Voltar às Reservas</span>
                    </a>
                    <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-brand/10 text-brand-dark border border-brand/20">
                        Reserva #{{ $reservation->id }}
                    </span>
                </div>
                <h2 class="font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    {{ $reservation->identifier ?? 'Reserva #' . $reservation->id }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Criada em {{ $reservation->created_at ? $reservation->created_at->format('d/m/Y \à\s H:i') : 'Data não registrada' }}
                </p>
            </div>

            <!-- Botões de Ação Principais -->
            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                <!-- Botão Lançar / Alterar Pagamento -->
                <button type="button" 
                        @click="modalOpen = true" 
                        class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 sm:px-5 rounded-2xl shadow-sm hover:shadow-md transition-all text-sm shrink-0">
                    <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Lançar Pagamento</span>
                </button>

                <!-- Botão Editar Reserva (SEMPRE visível!) -->
                <a href="{{ route('reservations.edit', $reservation) }}" 
                   class="inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-4 sm:px-5 rounded-2xl shadow-sm hover:shadow-md transition-all text-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Editar Reserva</span>
                </a>

                <!-- Imprimir PDF -->
                <a href="{{ route('reservations.print', $reservation) }}" target="_blank" 
                   class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-bold py-2.5 px-4 rounded-2xl border border-slate-200 shadow-2xs transition-all text-sm shrink-0">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.033-.42-2.12-.42-3.329 0-4.418 3.582-8 8-8s8 3.582 8 8c0 1.21-.18 2.296-.42 3.329M6 18h12M8 14h8m-10 7h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2z"/></svg>
                    <span>Imprimir A4</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $startDate = \Carbon\Carbon::parse($reservation->start_date);
        $endDate = \Carbon\Carbon::parse($reservation->end_date);
        $durationDays = max(1, $startDate->diffInDays($endDate));
        $payStatus = $reservation->payment_status ?? 'pending';
        $totalPrice = (float)($reservation->total_price ?? 0);
        $paidAmount = (float)($reservation->paid_amount ?? 0);
        $remaining = max(0, $totalPrice - $paidAmount);
        $percentPaid = $totalPrice > 0 ? min(100, round(($paidAmount / $totalPrice) * 100)) : 0;
    @endphp

    <div class="py-8" x-data="{
        modalOpen: false,
        payment_status: '{{ $reservation->payment_status ?? 'pending' }}',
        paid_amount: {{ (float)($reservation->paid_amount ?? 0) }},
        payment_date: '{{ $reservation->payment_date ? $reservation->payment_date->toDateString() : '' }}',
        total_price: {{ (float)($reservation->total_price ?? 0) }},

        setPaymentDateToday() {
            this.payment_date = '{{ now()->toDateString() }}';
        },

        clearPaymentDate() {
            this.payment_date = '';
        },

        onModalPaymentChange() {
            if (this.payment_status === 'signal') {
                this.paid_amount = Math.round(this.total_price * 50) / 100;
                if (!this.payment_date) this.payment_date = '{{ now()->toDateString() }}';
            } else if (this.payment_status === 'paid') {
                this.paid_amount = this.total_price;
                if (!this.payment_date) this.payment_date = '{{ now()->toDateString() }}';
            } else if (this.payment_status === 'pending') {
                this.paid_amount = 0;
                this.payment_date = '';
            } else if (this.payment_status === 'other') {
                if (!this.payment_date) this.payment_date = '{{ now()->toDateString() }}';
                if (!this.paid_amount || this.paid_amount === 0) {
                    this.paid_amount = Math.round(this.total_price * 50) / 100;
                }
            }
        },

        calculatedPaidAmount() {
            if (this.payment_status === 'pending') return 0;
            if (this.payment_status === 'signal') return Math.round(this.total_price * 50) / 100;
            if (this.payment_status === 'paid') return this.total_price;
            if (this.payment_status === 'other') return parseFloat(this.paid_amount) || 0;
            return 0;
        },

        remainingAmount() {
            return Math.max(0, this.total_price - this.calculatedPaidAmount());
        },

        formatCurrency(val) {
            return Number(val || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Mensagem de Sucesso -->
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-2xl shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="shrink-0 text-emerald-500">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- DASHBOARD BENTO GRID: INFORMAÇÕES DA RESERVA -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                
                <!-- CARD 1: Período da Reserva & Logística -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand-dark flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Período da Reserva</h3>
                            </div>
                            <!-- Status da Reserva -->
                            @if($reservation->status == 'active')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold text-xs text-brand-dark bg-brand/15 rounded-full border border-brand/20">
                                    <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                                    Ativa
                                </span>
                            @elseif($reservation->status == 'completed')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold text-xs text-emerald-800 bg-emerald-100 rounded-full border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Encerrada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold text-xs text-slate-600 bg-slate-100 rounded-full border border-slate-200">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    Cancelada
                                </span>
                            @endif
                        </div>

                        <!-- Linha do Tempo de Datas -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider mb-0.5">Retirada (Início)</span>
                                <span class="text-base font-black text-slate-800 block">{{ $startDate->format('d/m/Y') }}</span>
                                <span class="text-[11px] text-slate-500 capitalize">{{ $startDate->translatedFormat('l') }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider mb-0.5">Devolução (Fim)</span>
                                <span class="text-base font-black text-slate-800 block">{{ $endDate->format('d/m/Y') }}</span>
                                <span class="text-[11px] text-slate-500 capitalize">{{ $endDate->translatedFormat('l') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo da Duração & Enxovais -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span class="font-medium">Duração: <strong class="text-slate-800 font-bold">{{ $durationDays }} {{ $durationDays == 1 ? 'noite' : 'noites' }}</strong></span>
                        <span class="font-medium">Total: <strong class="text-slate-800 font-bold">{{ $reservation->items->sum('quantity') }} peças</strong></span>
                    </div>
                </div>

                <!-- CARD 2: Situação Financeira do Pedido -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Situação Financeira</h3>
                            </div>
                            <!-- Badge de Status de Pagamento -->
                            @if($payStatus == 'paid')
                                <span class="px-3 py-1 font-bold text-xs text-emerald-800 bg-emerald-100 rounded-full border border-emerald-200">
                                    Totalmente Pago
                                </span>
                            @elseif($payStatus == 'signal')
                                <span class="px-3 py-1 font-bold text-xs text-sky-800 bg-sky-100 rounded-full border border-sky-200">
                                    Sinal (50%)
                                </span>
                            @elseif($payStatus == 'other')
                                <span class="px-3 py-1 font-bold text-xs text-indigo-800 bg-indigo-100 rounded-full border border-indigo-200">
                                    Outro Valor
                                </span>
                            @else
                                <span class="px-3 py-1 font-bold text-xs text-amber-800 bg-amber-100 rounded-full border border-amber-200">
                                    Pendente
                                </span>
                            @endif
                        </div>

                        <!-- Valor Total e Barra de Progresso -->
                        <div class="mb-4">
                            <div class="flex items-baseline justify-between mb-1.5">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total do Pedido</span>
                                <span class="text-xl font-black text-slate-900">R$ {{ number_format($totalPrice, 2, ',', '.') }}</span>
                            </div>

                            <!-- Barra de Progresso de Quitação -->
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden mb-1">
                                <div class="h-full rounded-full transition-all duration-500 {{ $percentPaid >= 100 ? 'bg-emerald-500' : ($percentPaid > 0 ? 'bg-sky-500' : 'bg-slate-300') }}"
                                     style="width: {{ $percentPaid }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 font-semibold">
                                <span>{{ $percentPaid }}% quitado</span>
                                <span>Restam R$ {{ number_format($remaining, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Mini cards Pago x Restante -->
                        <div class="grid grid-cols-2 gap-2 text-center">
                            <div class="p-2.5 bg-emerald-50/70 rounded-xl border border-emerald-100">
                                <span class="text-[10px] uppercase font-bold text-emerald-700 block tracking-wider">Recebido</span>
                                <span class="text-sm font-black text-emerald-800">R$ {{ number_format($paidAmount, 2, ',', '.') }}</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">A Receber</span>
                                <span class="text-sm font-black {{ $remaining > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    R$ {{ number_format($remaining, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Ação Rápida de Pagamento -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">Lançamento</span>
                        <button type="button" @click="modalOpen = true" class="text-emerald-700 hover:text-emerald-800 font-bold hover:underline inline-flex items-center gap-1">
                            <span>Alterar status</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </div>
                </div>

                <!-- CARD 3: Data & Registro do Pagamento -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Data do Pagamento</h3>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Auditoria</span>
                        </div>

                        @if($reservation->payment_date && $payStatus !== 'pending')
                            <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100 mb-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Pagamento Confirmado</span>
                                </div>
                                <p class="text-xl font-black text-slate-800">
                                    {{ \Carbon\Carbon::parse($reservation->payment_date)->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Lançado {{ \Carbon\Carbon::parse($reservation->payment_date)->diffForHumans() }}
                                </p>
                            </div>
                        @else
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 mb-3 text-center">
                                <svg class="w-6 h-6 text-slate-300 mx-auto mb-1.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                <p class="text-xs font-bold text-slate-700">Sem data registrada</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Nenhuma data de recebimento foi vinculada a esta reserva.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Botão de Ação -->
                    <div class="pt-3 border-t border-slate-100">
                        <button type="button" @click="modalOpen = true" class="w-full py-2 px-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            <span>{{ $reservation->payment_date ? 'Editar Data ou Pagamento' : 'Lançar Data do Pagamento' }}</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- CARD DE OBSERVAÇÕES (se houver) -->
            @if($reservation->notes)
                <div class="bg-amber-50/70 border border-amber-200/80 rounded-3xl p-5 shadow-xs flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">Observações da Reserva</h4>
                        <p class="text-sm text-amber-950 font-medium leading-relaxed">{{ $reservation->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- TABELA DE ENXOVAIS RESERVADOS -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/40">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand-dark flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Itens e Enxovais Reservados</h3>
                            <p class="text-xs text-slate-500">Relação completa de produtos, quantidades e valores aplicados neste pedido.</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs self-start sm:self-auto">
                        {{ $reservation->items->count() }} {{ $reservation->items->count() == 1 ? 'item diferente' : 'itens diferentes' }} • {{ $reservation->items->sum('quantity') }} peças no total
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/70">
                                <th class="px-6 py-3.5">Produto / Enxoval</th>
                                <th class="px-6 py-3.5">Categoria / Tamanho</th>
                                <th class="px-6 py-3.5 text-center">Quantidade</th>
                                <th class="px-6 py-3.5 text-right">Valor Unitário</th>
                                <th class="px-6 py-3.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($reservation->items as $item)
                            @php
                                $unitPrice = $item->unit_price > 0 ? $item->unit_price : ($item->product->price ?? 0);
                                $subtotal = $unitPrice * $item->quantity;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $item->product->name ?? 'Enxoval' }}</div>
                                    @if(isset($item->product->description) && $item->product->description)
                                        <div class="text-xs text-slate-400 line-clamp-1">{{ $item->product->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $item->product->category ?? '-' }} • {{ $item->product->size ?? 'Único' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2.5 py-1 text-xs font-black bg-brand/10 text-brand-dark rounded-xl">
                                        {{ $item->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-600">
                                    R$ {{ number_format($unitPrice, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900">
                                    R$ {{ number_format($subtotal, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/80 border-t border-slate-200">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Total de Peças:
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-black text-slate-800 text-sm">{{ $reservation->items->sum('quantity') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-brand-dark uppercase tracking-wider">
                                    Total do Pedido:
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-lg font-black text-brand-dark">
                                        R$ {{ number_format($reservation->total_price ?? 0, 2, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- MODAL DE LANÇAMENTO / ALTERAÇÃO DE PAGAMENTO -->
            <div x-show="modalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div x-show="modalOpen" 
                     x-transition:enter="ease-out duration-200" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-150" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                     @click="modalOpen = false"></div>

                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
                    <div x-show="modalOpen" 
                         x-transition:enter="ease-out duration-200" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-150" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-100">
                        
                        <form action="{{ route('reservations.payment', $reservation) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <!-- Cabeçalho do Modal fiel à imagem de inspiração -->
                                    <div class="px-6 sm:px-8 pt-7 pb-4 flex justify-between items-start bg-white" style="background-color: #ffffff;">
                                        <div class="flex items-center gap-3.5">
                                            <!-- Ícone circular teal/ciano -->
                                            <div class="w-14 h-14 rounded-full flex items-center justify-center shrink-0" style="background-color: #d6edf3; color: #0b7c93;">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0b7c93;">
                                                    <rect x="2" y="5" width="20" height="14" rx="2.5" />
                                                    <line x1="2" y1="10" x2="22" y2="10" />
                                                    <line x1="6" y1="15" x2="10" y2="15" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-2xl font-black tracking-tight leading-tight" style="color: #0c2130;" id="modal-title">Lançar Pagamento</h3>
                                                <p class="text-sm font-semibold mt-0.5" style="color: #718292;">Reserva #{{ $reservation->id }} • {{ $reservation->identifier ?? 'Cliente' }}</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="modalOpen = false" class="p-1.5 rounded-xl hover:bg-slate-100 transition-colors" style="color: #8c9ba5;">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div class="px-6 sm:px-8 pb-7 space-y-5 bg-white" style="background-color: #ffffff;">
                                        <!-- Resumo dos Valores (Fundo #e5f3f7 com divisórias) -->
                                        <div class="rounded-2xl py-4 sm:py-5 px-4 sm:px-6 grid grid-cols-3 text-center" style="background-color: #e5f3f7;">
                                            <div class="px-2" style="border-right: 1px solid #cbe4ec;">
                                                <span class="text-xs sm:text-sm font-semibold block mb-1" style="color: #185c6d;">Total da Reserva</span>
                                                <span class="text-xl sm:text-2xl font-black" style="color: #073643;">R$ <span x-text="formatCurrency(total_price)"></span></span>
                                            </div>
                                            <div class="px-2" style="border-right: 1px solid #cbe4ec;">
                                                <span class="text-xs sm:text-sm font-semibold block mb-1" style="color: #185c6d;">Recebido</span>
                                                <span class="text-xl sm:text-2xl font-black" style="color: #073643;">R$ <span x-text="formatCurrency(calculatedPaidAmount())"></span></span>
                                            </div>
                                            <div class="px-2">
                                                <span class="text-xs sm:text-sm font-semibold block mb-1" style="color: #185c6d;">Restante</span>
                                                <span class="text-xl sm:text-2xl font-black" style="color: #073643;">R$ <span x-text="formatCurrency(remainingAmount())"></span></span>
                                            </div>
                                        </div>

                                        <!-- Seletor do Status de Pagamento (Dropdown List conforme solicitado) -->
                                        <div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0b7c93;">
                                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                                    <line x1="2" y1="10" x2="22" y2="10" />
                                                    <line x1="6" y1="15" x2="10" y2="15" />
                                                </svg>
                                                <label class="text-sm font-bold" style="color: #0c2130;" for="modal_payment_status">
                                                    Situação do Pagamento
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <select name="payment_status" id="modal_payment_status" 
                                                        x-model="payment_status" 
                                                        @change="onModalPaymentChange()" 
                                                        class="w-full rounded-2xl py-3.5 pl-4 pr-11 text-sm font-bold shadow-xs cursor-pointer appearance-none transition-all"
                                                        style="background-color: #ffffff; border: 1.5px solid #d2e0ea; color: #0c2130;">
                                                    <option value="pending">Pendente (Nenhum valor pago)</option>
                                                    <option value="signal">Sinal (50%)</option>
                                                    <option value="paid">Totalmente Pago</option>
                                                    <option value="other">Outro Valor (Digitar valor específico)</option>
                                                </select>
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none" style="color: #718292;">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Campo: Valor Efetivamente Pago (R$) * -->
                                        <div x-show="payment_status === 'other'" x-transition>
                                            <label class="block text-sm font-bold mb-1.5" style="color: #0c2130;" for="modal_paid_amount">
                                                Valor Efetivamente Pago (R$) *
                                            </label>
                                            <div class="relative flex items-center rounded-2xl shadow-xs transition-all overflow-hidden px-3.5 py-2"
                                                 style="background-color: #ffffff; border: 1.5px solid #d2e0ea;">
                                                <div class="mr-3 flex items-center shrink-0">
                                                    <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-xl font-bold text-xs sm:text-sm select-none"
                                                          style="background-color: #d6edf3; color: #0b7c93;">
                                                        R$
                                                    </span>
                                                </div>
                                                <input type="number" step="0.01" min="0" name="paid_amount" id="modal_paid_amount"
                                                       x-model.number="paid_amount"
                                                       placeholder="0.00"
                                                       class="w-full border-0 focus:ring-0 p-0 text-base sm:text-lg font-bold bg-transparent"
                                                       style="color: #0c2130; outline: none; box-shadow: none;">
                                            </div>
                                        </div>

                                        <!-- Campo: Data do Pagamento (#f3f8fb) -->
                                        <div x-show="payment_status !== 'pending'" x-transition class="rounded-2xl p-5 space-y-3.5"
                                             style="background-color: #f3f8fb; border: 1px solid #e1ebf2;">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #0b7c93;">
                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                                    </svg>
                                                    <label class="text-sm font-bold" style="color: #0c2130;" for="modal_payment_date">
                                                        Data do Pagamento
                                                    </label>
                                                </div>
                                                <!-- Botões de Ação Rápida -->
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="setPaymentDateToday()" 
                                                            class="text-xs sm:text-sm font-bold px-4 py-1.5 rounded-full shadow-xs transition-colors"
                                                            style="background-color: #0b7c93; color: #ffffff; border: none; cursor: pointer;">
                                                        Hoje
                                                    </button>
                                                    <button type="button" @click="clearPaymentDate()" 
                                                            class="text-xs sm:text-sm font-semibold px-4 py-1.5 rounded-full transition-colors"
                                                            style="background-color: #e3ebf2; color: #475f73; border: none; cursor: pointer;">
                                                        Deixar sem data
                                                    </button>
                                                </div>
                                            </div>

                                            <input type="date" name="payment_date" id="modal_payment_date"
                                                   x-model="payment_date"
                                                   class="w-full rounded-2xl py-3 px-4 text-sm font-medium shadow-xs transition-all"
                                                   style="background-color: #ffffff; border: 1.5px solid #d2e0ea; color: #0c2130; outline: none;">
                                            
                                            <div class="flex items-center gap-2 text-xs pt-0.5" style="color: #718292;">
                                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20" style="color: #8a9ba8;">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Caso não queira informar uma data específica, clique em <strong style="color: #334155;">Deixar sem data</strong> ou limpe o campo acima.</span>
                                            </div>
                                        </div>

                                        <!-- Rodapé do Modal (Cancelar + Salvar Pagamento) -->
                                        <div class="pt-2 flex items-center justify-end gap-4">
                                            <button type="button" @click="modalOpen = false" class="px-5 py-2.5 text-sm font-bold transition-colors"
                                                    style="color: #475f73; background: transparent; border: none; cursor: pointer;">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="font-bold text-sm px-6 py-3.5 rounded-2xl shadow-sm transition-all inline-flex items-center gap-2.5"
                                                    style="background-color: #0b7c93; color: #ffffff; border: none; cursor: pointer;">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #ffffff;">
                                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                                    <line x1="2" y1="10" x2="22" y2="10" />
                                                    <line x1="6" y1="15" x2="10" y2="15" />
                                                </svg>
                                                <span>Salvar Pagamento</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
