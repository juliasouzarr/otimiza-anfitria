<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-brand-dark leading-tight tracking-tight">
                    {{ __('Nova Reserva / Pedido') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Selecione as datas, adicione kits ou enxovais avulsos com cálculo automático de valores.</p>
            </div>
            <a href="{{ route('reservations.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand">
                &larr; Voltar para Reservas
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 md:p-8">
                    
                    @if ($errors->has('availability'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm text-red-700" role="alert">
                            <span class="font-bold">Erro de Estoque:</span>
                            <span>{{ $errors->first('availability') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('reservations.store') }}" method="POST" x-data="reservationForm()">
                        @csrf

                        <!-- Identificação -->
                        <div class="mb-5">
                            <label class="block text-slate-700 text-sm font-bold mb-2" for="identifier">
                                Identificação da Reserva * 
                            </label>
                            <input type="text" name="identifier" id="identifier" value="{{ old('identifier') }}" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm" required>
                            @error('identifier') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Período: Início e Fim -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="start_date">Data de Início (Retirada) *</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm" required>
                                @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="end_date">Data de Fim (Devolução) *</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm" required>
                                @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- SEÇÃO: ADICIONAR POR KIT -->
                        @if($kits->isNotEmpty())
                        <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-brand/10 via-brand-accent/15 to-brand/10 border border-brand/20">
                            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 lg:gap-4">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-brand-dark text-sm flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                                        <span>Adicionar por Kit de Enxoval</span>
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Preenche os itens automaticamente no pedido.</p>
                                </div>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                                    <select x-model="selectedKitId" class="border-brand/30 focus:border-brand focus:ring-brand rounded-xl text-xs sm:text-sm py-2 px-3 bg-white w-full sm:w-56 md:w-64 shadow-xs">
                                        <option value="">Selecione um Kit...</option>
                                        @foreach($kits as $k)
                                            <option value="{{ $k->id }}">
                                                {{ $k->name }} ({{ $k->items->sum('quantity') }} peças) — R$ {{ number_format($k->price, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="flex items-center justify-center gap-1.5 shrink-0 bg-white px-2.5 py-1.5 rounded-xl border border-brand/30 shadow-xs">
                                        <span class="text-xs font-bold text-slate-500">Qtd:</span>
                                        <input type="number" x-model.number="kitQuantity" min="1" max="99" class="w-10 text-center border-0 focus:ring-0 p-0 text-sm font-bold text-brand-dark">
                                    </div>
                                    <button type="button" @click="addKit()" class="bg-brand hover:bg-brand-secondary text-white font-bold text-xs py-2 px-4 rounded-xl shadow-xs transition-all shrink-0 whitespace-nowrap text-center">
                                        + Inserir Kit
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- SEÇÃO: ITENS DO PEDIDO -->
                        <div class="mb-6 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Itens do Pedido</h3>
                                    <p class="text-xs text-slate-500">Adicione os enxovais avulsos ou complemente com kits.</p>
                                </div>
                                <button type="button" @click="addItem()" class="bg-brand/10 hover:bg-brand text-brand-dark hover:text-white font-bold text-xs py-1.5 px-3 rounded-lg transition-colors">
                                    + Adicionar Item Avulso
                                </button>
                            </div>

                            <!-- Cabeçalho das colunas -->
                            <div class="hidden sm:grid grid-cols-12 gap-3 px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <div class="col-span-6">Enxoval / Produto</div>
                                <div class="col-span-2 text-center">Qtd</div>
                                <div class="col-span-2 text-right">Valor Unit. (R$)</div>
                                <div class="col-span-2 text-right">Subtotal (R$)</div>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex flex-col sm:grid sm:grid-cols-12 gap-3 items-center bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                                        <!-- Produto -->
                                        <div class="w-full sm:col-span-6">
                                            <select :name="`items[${index}][product_id]`" 
                                                    x-model="item.product_id" 
                                                    @change="onProductChange(item)"
                                                    class="border-slate-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-lg text-sm w-full py-2 px-3" required>
                                                <option value="">Selecione o enxoval...</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}" data-price="{{ $p->price }}">
                                                        {{ $p->name }} ({{ $p->size ?? 'Único' }}) - R$ {{ number_format($p->price, 2, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Quantidade -->
                                        <div class="w-full sm:col-span-2">
                                            <input type="number" 
                                                   :name="`items[${index}][quantity]`" 
                                                   x-model.number="item.quantity" 
                                                   min="1" 
                                                   placeholder="Qtd" 
                                                   class="border-slate-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-lg text-sm w-full py-2 px-3 text-center font-bold" required>
                                        </div>

                                        <!-- Valor Unitário (editável) -->
                                        <div class="w-full sm:col-span-2">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-xs text-slate-400 font-bold pointer-events-none">R$</span>
                                                <input type="number" 
                                                       step="0.01" 
                                                       :name="`items[${index}][unit_price]`" 
                                                       x-model.number="item.unit_price" 
                                                       placeholder="0.00" 
                                                       class="border-slate-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-lg text-sm w-full py-2 pl-8 pr-2 text-right font-semibold">
                                            </div>
                                        </div>

                                        <!-- Subtotal e Botão Excluir -->
                                        <div class="w-full sm:col-span-2 flex items-center justify-between sm:justify-end gap-2">
                                            <div class="text-sm font-black text-slate-800 text-right">
                                                R$ <span x-text="formatCurrency((item.quantity || 0) * (item.unit_price || 0))"></span>
                                            </div>
                                            <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Remover item">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            @error('items') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror

                            <!-- RESUMO FINANCEIRO DO PEDIDO -->
                            <div class="mt-4 pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-4 rounded-xl">
                                <div class="text-xs text-slate-500 font-medium">
                                    Total de peças reservadas: <span class="font-bold text-slate-800" x-text="totalPieces()"></span> peças
                                </div>
                                <div class="text-right flex items-baseline gap-2">
                                    <span class="text-xs uppercase font-bold text-slate-400">Total do Pedido:</span>
                                    <span class="text-2xl font-black text-brand-dark">
                                        R$ <span x-text="formatCurrency(totalOrder())"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO: STATUS DE PAGAMENTO -->
                        <div class="mb-6 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                            <div class="mb-3">
                                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Status do Pagamento</h3>
                                <p class="text-xs text-slate-500">Informe a situação do pagamento desta reserva.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                                <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col justify-between transition-all"
                                       :class="payment_status === 'pending' ? 'border-amber-400 bg-amber-50/60 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                                    <div class="flex items-center gap-2 mb-1">
                                        <input type="radio" name="payment_status" value="pending" x-model="payment_status" @change="onPaymentStatusChange()" class="text-amber-600 focus:ring-amber-500">
                                        <span class="text-xs font-bold text-slate-700">Pendente</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Nenhum valor pago</p>
                                </label>

                                <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col justify-between transition-all"
                                       :class="payment_status === 'signal' ? 'border-sky-500 bg-sky-50/60 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                                    <div class="flex items-center gap-2 mb-1">
                                        <input type="radio" name="payment_status" value="signal" x-model="payment_status" @change="onPaymentStatusChange()" class="text-sky-600 focus:ring-sky-500">
                                        <span class="text-xs font-bold text-slate-700">Sinal Pago (50%)</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">50% do valor do pedido</p>
                                </label>

                                <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col justify-between transition-all"
                                       :class="payment_status === 'paid' ? 'border-emerald-500 bg-emerald-50/60 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                                    <div class="flex items-center gap-2 mb-1">
                                        <input type="radio" name="payment_status" value="paid" x-model="payment_status" @change="onPaymentStatusChange()" class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-xs font-bold text-slate-700">Totalmente Pago</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">100% quitado</p>
                                </label>

                                <label class="cursor-pointer border-2 rounded-xl p-3 flex flex-col justify-between transition-all"
                                       :class="payment_status === 'other' ? 'border-indigo-500 bg-indigo-50/60 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                                    <div class="flex items-center gap-2 mb-1">
                                        <input type="radio" name="payment_status" value="other" x-model="payment_status" @change="onPaymentStatusChange()" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-bold text-slate-700">Outro Valor</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Digitar valor específico</p>
                                </label>
                            </div>

                            <!-- Campo customizado para 'Outro Valor' -->
                            <div x-show="payment_status === 'other'" x-transition class="mb-4 p-4 bg-white rounded-xl border border-indigo-200 shadow-sm">
                                <label class="block text-slate-700 text-xs font-bold mb-1.5" for="paid_amount">
                                    Valor Pago pelo Cliente (R$) *
                                </label>
                                <div class="relative max-w-xs">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm text-slate-400 font-bold pointer-events-none">R$</span>
                                    <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount"
                                           x-model.number="paid_amount"
                                           placeholder="0.00"
                                           class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm w-full py-2 pl-9 pr-3 text-sm font-semibold">
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">Informe exatamente a quantia em R$ já recebida.</p>
                            </div>

                            <!-- Campo: Data do Pagamento (Opcional) -->
                            <div x-show="payment_status !== 'pending'" x-transition class="mb-4 p-4 sm:p-5 bg-white rounded-2xl border border-slate-200 shadow-xs">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <label class="block text-slate-800 text-xs font-bold uppercase tracking-wider" for="payment_date">
                                            Data do Pagamento
                                        </label>
                                        <span class="text-[10px] text-slate-400 font-medium px-2 py-0.5 bg-slate-100 rounded-full">Opcional</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="payment_date = '{{ now()->toDateString() }}'" 
                                                class="text-xs font-semibold text-brand hover:text-brand-secondary bg-slate-50 hover:bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 shadow-2xs transition-colors">
                                            Hoje
                                        </button>
                                        <button type="button" @click="payment_date = ''" 
                                                class="text-xs font-semibold text-rose-600 hover:text-rose-700 bg-slate-50 hover:bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200 shadow-2xs transition-colors">
                                            Deixar sem data
                                        </button>
                                    </div>
                                </div>
                                <div class="relative max-w-xs">
                                    <input type="date" name="payment_date" id="payment_date"
                                           x-model="payment_date"
                                           class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-xs w-full py-2.5 px-3.5 text-sm font-medium text-slate-800">
                                </div>
                                <p class="text-[11px] text-slate-500 mt-2 flex items-center gap-1.5">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Para deixar sem data, clique no botão <strong>Deixar sem data</strong> ou apague o campo.</span>
                                </p>
                            </div>

                            <!-- Resumo dos Valores de Pagamento -->
                            <div class="bg-white p-4 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3 text-center sm:text-left">
                                <div>
                                    <span class="text-[11px] uppercase font-bold text-slate-400 block">Total do Pedido</span>
                                    <span class="text-base font-black text-slate-800">R$ <span x-text="formatCurrency(totalOrder())"></span></span>
                                </div>
                                <div>
                                    <span class="text-[11px] uppercase font-bold text-emerald-600 block">Valor Pago</span>
                                    <span class="text-base font-black text-emerald-700">R$ <span x-text="formatCurrency(calculatedPaidAmount())"></span></span>
                                </div>
                                <div>
                                    <span class="text-[11px] uppercase font-bold text-slate-400 block">Restante a Pagar</span>
                                    <span class="text-base font-black" :class="remainingAmount() > 0 ? 'text-amber-600' : 'text-slate-500'">
                                        R$ <span x-text="formatCurrency(remainingAmount())"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="mb-6">
                            <label class="block text-slate-700 text-sm font-bold mb-2" for="notes">Observações</label>
                            <textarea name="notes" id="notes" rows="3" placeholder="Informações adicionais de entrega ou hóspede..." class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm">{{ old('notes') }}</textarea>
                            @error('notes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('reservations.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-colors">
                                Salvar Reserva
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @php
        $initialReservationItems = old('items', []);
        if (empty($initialReservationItems)) {
            $initialReservationItems = [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]];
        }
    @endphp

    <script>
        function reservationForm() {
            let productsMap = @json($products->keyBy('id'));
            let kitsMap = @json($kits->keyBy('id'));
            let initialItems = @json($initialReservationItems);

            // Se algum item não tiver unit_price definido, pega do produto
            initialItems.forEach(item => {
                if (!item.unit_price && productsMap[item.product_id]) {
                    item.unit_price = parseFloat(productsMap[item.product_id].price);
                }
            });

            return {
                productsMap: productsMap,
                kitsMap: kitsMap,
                selectedKitId: '',
                kitQuantity: 1,
                items: initialItems,
                payment_status: '{{ old('payment_status', 'pending') }}',
                paid_amount: {{ old('paid_amount', 0) }},
                payment_date: '{{ old('payment_date', '') }}',

                onPaymentStatusChange() {
                    if (this.payment_status === 'signal') {
                        this.paid_amount = Math.round(this.totalOrder() * 50) / 100;
                    } else if (this.payment_status === 'paid') {
                        this.paid_amount = this.totalOrder();
                    } else if (this.payment_status === 'pending') {
                        this.paid_amount = 0;
                        this.payment_date = '';
                    }
                },

                calculatedPaidAmount() {
                    if (this.payment_status === 'pending') return 0;
                    if (this.payment_status === 'signal') return Math.round(this.totalOrder() * 50) / 100;
                    if (this.payment_status === 'paid') return this.totalOrder();
                    if (this.payment_status === 'other') return parseFloat(this.paid_amount) || 0;
                    return 0;
                },

                remainingAmount() {
                    return Math.max(0, this.totalOrder() - this.calculatedPaidAmount());
                },

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    } else {
                        alert('A reserva precisa ter pelo menos um enxoval.');
                    }
                },

                onProductChange(item) {
                    if (this.productsMap[item.product_id]) {
                        item.unit_price = parseFloat(this.productsMap[item.product_id].price);
                    }
                },

                addKit() {
                    if (!this.selectedKitId) return;
                    const kit = this.kitsMap[this.selectedKitId];
                    if (!kit || !kit.items) return;

                    const multiplier = Math.max(1, parseInt(this.kitQuantity) || 1);

                    kit.items.forEach(kItem => {
                        const itemQty = parseInt(kItem.quantity || 1) * multiplier;
                        const existing = this.items.find(i => String(i.product_id) === String(kItem.product_id));
                        if (existing) {
                            existing.quantity = parseInt(existing.quantity || 0) + itemQty;
                        } else {
                            // Se a primeira linha estiver vazia, substitui
                            if (this.items.length === 1 && !this.items[0].product_id) {
                                this.items[0] = {
                                    product_id: String(kItem.product_id),
                                    quantity: itemQty,
                                    unit_price: parseFloat(kItem.unit_price || (this.productsMap[kItem.product_id]?.price) || 0)
                                };
                            } else {
                                this.items.push({
                                    product_id: String(kItem.product_id),
                                    quantity: itemQty,
                                    unit_price: parseFloat(kItem.unit_price || (this.productsMap[kItem.product_id]?.price) || 0)
                                });
                            }
                        }
                    });

                    this.selectedKitId = '';
                    this.kitQuantity = 1;
                },

                totalPieces() {
                    return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                },

                totalOrder() {
                    return this.items.reduce((sum, item) => sum + ((parseFloat(item.unit_price) || 0) * (parseInt(item.quantity) || 0)), 0);
                },

                formatCurrency(val) {
                    return Number(val || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</x-app-layout>
