<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-brand-dark leading-tight tracking-tight">
                {{ __('Reservas') }}
            </h2>
            <a href="{{ route('reservations.create') }}" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-6 rounded-full shadow-md transition-transform hover:scale-105">
                + Nova Reserva
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl" 
                 x-data="{ 
                     search: '', 
                     filterStatus: 'all', 
                     filterPayment: 'all',
                     modalOpen: false,
                     selectedReservation: null,
                     payment_status: 'pending',
                     paid_amount: 0,
                     payment_date: '',
                     total_price: 0,

                     openPaymentModal(res) {
                         this.selectedReservation = res;
                         this.payment_status = res.payment_status || 'pending';
                         this.paid_amount = parseFloat(res.paid_amount) || 0;
                         this.payment_date = res.payment_date ? res.payment_date.substring(0, 10) : '';
                         this.total_price = parseFloat(res.total_price) || 0;
                         if (this.payment_status === 'signal' && this.paid_amount === 0) {
                             this.paid_amount = Math.round(this.total_price * 50) / 100;
                         } else if (this.payment_status === 'paid' && this.paid_amount === 0) {
                             this.paid_amount = this.total_price;
                         }
                         this.modalOpen = true;
                     },

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
                <div class="p-6 md:p-8 text-gray-900 overflow-x-auto">
                    
                    <!-- Modern Filter Bar -->
                    <div class="mb-6 bg-slate-50 p-4 rounded-2xl border border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div class="w-full md:w-5/12 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                            <input type="text" x-model="search" placeholder="Pesquisar por cliente, ref ou ID..." 
                                class="pl-11 w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2.5 transition-shadow hover:shadow-md text-sm">
                        </div>
                        
                        <div class="w-full md:w-7/12 flex flex-col sm:flex-row items-center gap-3">
                            <div class="w-full sm:w-1/2 flex items-center gap-2">
                                <label class="text-xs font-bold text-gray-600 whitespace-nowrap">Reserva:</label>
                                <select x-model="filterStatus" class="w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2 text-sm">
                                    <option value="all">Todas as Situações</option>
                                    <option value="active">Ativas</option>
                                    <option value="completed">Encerradas</option>
                                    <option value="cancelled">Canceladas</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-1/2 flex items-center gap-2">
                                <label class="text-xs font-bold text-gray-600 whitespace-nowrap">Pagamento:</label>
                                <select x-model="filterPayment" class="w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2 text-sm">
                                    <option value="all">Todos os Pagamentos</option>
                                    <option value="pending">Pendente</option>
                                    <option value="signal">Sinal Pago (50%)</option>
                                    <option value="paid">Totalmente Pago</option>
                                    <option value="other">Outro Valor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <table class="w-full whitespace-no-wrap shadow-sm rounded-2xl overflow-hidden">
                        <thead class="bg-brand-dark text-white">
                            <tr class="text-xs font-semibold tracking-wide text-left uppercase border-b border-brand-dark">
                                <th class="px-5 py-4">ID / Ref (Cliente)</th>
                                <th class="px-5 py-4">Período</th>
                                <th class="px-5 py-4">Volume</th>
                                <th class="px-5 py-4">Valor Total</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4">Pagamento</th>
                                <th class="px-5 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($reservations as $reservation)
                            <tr class="text-gray-700 hover:bg-slate-50 transition-colors {{ $reservation->status == 'cancelled' ? 'opacity-50' : '' }}" 
                                x-show="(search === '' || '{{ strtolower(\Illuminate\Support\Str::ascii($reservation->identifier ?? '')) }}'.includes(search.toLowerCase()) || '{{ $reservation->id }}'.includes(search)) && (filterStatus === 'all' || '{{ $reservation->status }}' === filterStatus) && (filterPayment === 'all' || '{{ $reservation->payment_status ?? 'pending' }}' === filterPayment)">
                                
                                <td class="px-5 py-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-brand bg-opacity-10 text-brand flex items-center justify-center font-bold mr-3">
                                            #{{ $reservation->id }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $reservation->identifier ?? 'Sem Nome' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm font-medium">
                                    <span class="text-brand-dark">{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</span> 
                                    <span class="text-gray-400 mx-1">até</span> 
                                    <span class="text-brand-dark">{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="font-bold text-gray-700">{{ $reservation->items->sum('quantity') }}</span> peças
                                </td>
                                <td class="px-5 py-4 text-sm font-black text-brand-dark">
                                    R$ {{ number_format($reservation->total_price ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    @if($reservation->status == 'active')
                                        <span class="px-3 py-1 font-bold text-xs text-brand-dark bg-brand bg-opacity-20 rounded-full border border-brand border-opacity-30">Ativa</span>
                                    @elseif($reservation->status == 'completed')
                                        <span class="px-3 py-1 font-bold text-xs text-green-800 bg-green-100 rounded-full border border-green-200">Encerrada</span>
                                    @else
                                        <span class="px-3 py-1 font-bold text-xs text-gray-600 bg-gray-100 rounded-full border border-gray-200">Cancelada</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm whitespace-nowrap">
                                    @php 
                                        $payStatus = $reservation->payment_status ?? 'pending'; 
                                        $resData = [
                                            'id' => $reservation->id,
                                            'identifier' => $reservation->identifier ?? 'Sem Nome',
                                            'total_price' => (float)($reservation->total_price ?? 0),
                                            'payment_status' => $reservation->payment_status ?? 'pending',
                                            'paid_amount' => (float)($reservation->paid_amount ?? 0),
                                            'payment_date' => $reservation->payment_date ? $reservation->payment_date->toDateString() : null,
                                        ];
                                    @endphp
                                    <div class="flex flex-col items-start gap-1 whitespace-nowrap">
                                        @if($payStatus == 'paid')
                                            <span class="inline-flex items-center px-2.5 py-1 font-bold text-xs text-emerald-800 bg-emerald-100 rounded-full border border-emerald-200 whitespace-nowrap">
                                                Totalmente Pago
                                            </span>
                                        @elseif($payStatus == 'signal')
                                            <span class="inline-flex items-center px-2.5 py-1 font-bold text-xs text-sky-800 bg-sky-100 rounded-full border border-sky-200 whitespace-nowrap">
                                                Sinal pago
                                            </span>
                                        @elseif($payStatus == 'other')
                                            <span class="inline-flex items-center px-2.5 py-1 font-bold text-xs text-indigo-800 bg-indigo-100 rounded-full border border-indigo-200 whitespace-nowrap">
                                                Pago: R$ {{ number_format($reservation->paid_amount ?? 0, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 font-bold text-xs text-amber-800 bg-amber-100 rounded-full border border-amber-200 whitespace-nowrap">
                                                Pendente
                                            </span>
                                        @endif

                                        @if($reservation->payment_date && $payStatus !== 'pending')
                                            <span class="text-[11px] text-slate-500 font-medium whitespace-nowrap">
                                                Em {{ \Carbon\Carbon::parse($reservation->payment_date)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <!-- Lançar Pagamento Rápido (Somente Ícone) -->
                                        <button type="button" 
                                                @click="openPaymentModal({{ json_encode($resData) }})"
                                                class="p-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl shadow-xs transition-colors inline-flex items-center justify-center hover:scale-105"
                                                title="Lançar ou alterar pagamento">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>

                                        <!-- Editar Reserva (Somente Ícone) -->
                                        <a href="{{ route('reservations.edit', $reservation) }}" 
                                           class="p-2 bg-brand hover:bg-brand-secondary text-white rounded-xl shadow-xs transition-colors inline-flex items-center justify-center hover:scale-105"
                                           title="Editar informações da reserva">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <!-- Detalhes -->
                                        <a href="{{ route('reservations.show', $reservation) }}" 
                                           class="px-2.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-colors"
                                           title="Ver detalhes da reserva">
                                            Detalhes
                                        </a>

                                        <!-- Imprimir PDF -->
                                        <a href="{{ route('reservations.print', $reservation) }}" target="_blank" 
                                           class="p-2 text-slate-400 hover:text-brand hover:bg-slate-100 rounded-xl transition-colors inline-flex items-center justify-center"
                                           title="Imprimir Pedido (PDF)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.033-.42-2.12-.42-3.329 0-4.418 3.582-8 8-8s8 3.582 8 8c0 1.21-.18 2.296-.42 3.329M6 18h12M8 14h8m-10 7h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            
                            @if($reservations->isEmpty())
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 font-medium bg-gray-50">
                                    Nenhuma reserva encontrada.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- MODAL DE LANÇAMENTO RÁPIDO DE PAGAMENTO -->
                    <div x-show="modalOpen" 
                         x-cloak 
                         class="fixed inset-0 z-50 overflow-y-auto" 
                         aria-labelledby="modal-title" 
                         role="dialog" 
                         aria-modal="true">
                        <!-- Backdrop escurecido -->
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
                                
                                <form :action="selectedReservation ? `/reservations/${selectedReservation.id}/payment` : '#'" method="POST">
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
                                                <p class="text-sm font-semibold mt-0.5" style="color: #718292;">Reserva #<span x-text="selectedReservation?.id"></span> • <span x-text="selectedReservation?.identifier"></span></p>
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
        </div>
    </div>
</x-app-layout>
