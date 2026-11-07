<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-brand-dark leading-tight tracking-tight">
                    {{ __('Editar Reserva #') }}{{ $reservation->id }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Atualize datas, itens do pedido, valores ou status da reserva.</p>
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

                    <form action="{{ route('reservations.update', $reservation) }}" method="POST" x-data="reservationForm()">
                        @csrf
                        @method('PUT')

                        <!-- Identificação e Status -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                            <div class="md:col-span-2">
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="identifier">
                                    Identificação da Reserva * (Ex: Nome do hóspede, número do apto)
                                </label>
                                <input type="text" name="identifier" id="identifier" value="{{ old('identifier', $reservation->identifier) }}" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm" required>
                                @error('identifier') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="status">Status da Reserva *</label>
                                <select name="status" id="status" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm font-semibold">
                                    <option value="active" {{ old('status', $reservation->status) == 'active' ? 'selected' : '' }}>Ativa</option>
                                    <option value="completed" {{ old('status', $reservation->status) == 'completed' ? 'selected' : '' }}>Encerrada</option>
                                    <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Período: Início e Fim -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="start_date">Data de Início (Retirada) *</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $reservation->start_date ? $reservation->start_date->toDateString() : '') }}" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm" required>
                                @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="end_date">Data de Fim (Devolução) *</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $reservation->end_date ? $reservation->end_date->toDateString() : '') }}" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm" required>
                                @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- SEÇÃO: ADICIONAR POR KIT -->
                        @if(isset($kits) && $kits->isNotEmpty())
                        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-brand/10 to-brand-accent/20 border border-brand/20">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-brand-dark text-sm flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                                        Adicionar por Kit de Enxoval
                                    </h4>
                                    <p class="text-xs text-slate-600">Insere automaticamente todos os itens e quantidades do kit neste pedido.</p>
                                </div>
                                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
                                    <select x-model="selectedKitId" class="border-brand/30 focus:border-brand focus:ring-brand rounded-xl text-sm py-2 px-3 bg-white flex-1 sm:w-auto">
                                        <option value="">Selecione um Kit...</option>
                                        @foreach($kits as $k)
                                            <option value="{{ $k->id }}">
                                                {{ $k->name }} ({{ $k->items->sum('quantity') }} peças) — R$ {{ number_format($k->price, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="flex items-center gap-1.5 shrink-0 bg-white px-2.5 py-1 rounded-xl border border-brand/30 shadow-sm" title="Quantidade de kits a adicionar">
                                        <span class="text-xs font-bold text-slate-500">Qtd:</span>
                                        <input type="number" x-model.number="kitQuantity" min="1" max="99" class="w-14 text-center border-0 focus:ring-0 p-0 text-sm font-bold text-brand-dark">
                                    </div>
                                    <button type="button" @click="addKit()" class="bg-brand hover:bg-brand-secondary text-white font-bold text-xs py-2 px-4 rounded-xl shadow-sm transition-all shrink-0">
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
                                    <p class="text-xs text-slate-500">Altere quantidades, adicione novos itens ou kits.</p>
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

                        <!-- Observações -->
                        <div class="mb-6">
                            <label class="block text-slate-700 text-sm font-bold mb-2" for="notes">Observações</label>
                            <textarea name="notes" id="notes" rows="3" placeholder="Informações adicionais..." class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm">{{ old('notes', $reservation->notes) }}</textarea>
                            @error('notes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('reservations.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-colors">
                                Atualizar Reserva
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @php
        $initialReservationItems = old('items');
        if (!$initialReservationItems) {
            $initialReservationItems = [];
            foreach ($reservation->items as $item) {
                $initialReservationItems[] = [
                    'product_id' => (string)$item->product_id,
                    'quantity' => (int)$item->quantity,
                    'unit_price' => (float)($item->unit_price > 0 ? $item->unit_price : ($item->product->price ?? 0)),
                ];
            }
        }
        if (empty($initialReservationItems)) {
            $initialReservationItems = [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]];
        }
    @endphp

    <script>
        function reservationForm() {
            let productsMap = @json($products->keyBy('id'));
            let kitsMap = @json($kits->keyBy('id'));
            let initialItems = @json($initialReservationItems);

            return {
                productsMap: productsMap,
                kitsMap: kitsMap,
                selectedKitId: '',
                kitQuantity: 1,
                items: initialItems,

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
