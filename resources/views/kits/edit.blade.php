<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-brand-dark leading-tight tracking-tight">
                    {{ __('Editar Kit') }}: {{ $kit->name }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Altere os itens, quantidades ou valores deste kit.</p>
            </div>
            <a href="{{ route('kits.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand">
                &larr; Voltar para Kits
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 md:p-8">
                    
                    <form action="{{ route('kits.update', $kit) }}" method="POST" x-data="kitForm()">
                        @csrf
                        @method('PUT')

                        <!-- Nome e Tamanho -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="name">Nome do Kit *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $kit->name) }}" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3" required>
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="size">Tamanho / Categoria</label>
                                <input type="text" name="size" id="size" value="{{ old('size', $kit->size) }}" placeholder="Ex: Solteiro, Casal, Queen, King" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3">
                                @error('size') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Período e Valor -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="period_days">Período Padrão (dias) *</label>
                                <input type="number" name="period_days" id="period_days" value="{{ old('period_days', $kit->period_days) }}" min="1" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3" required>
                                @error('period_days') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-2" for="price">Valor Final do Kit (R$) *</label>
                                <input type="number" step="0.01" name="price" id="price" x-model="kitPrice" placeholder="0.00" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 font-bold text-brand-dark" required>
                                <div class="flex justify-between items-center mt-1 text-xs text-slate-400">
                                    <span>Soma dos itens: R$ <span x-text="itemsSum().toFixed(2)"></span></span>
                                    <button type="button" @click="kitPrice = itemsSum().toFixed(2)" class="text-brand hover:underline font-semibold">Usar soma exata</button>
                                </div>
                                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Itens do Kit -->
                        <div class="mb-6 p-5 bg-slate-50 rounded-2xl border border-slate-200">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Itens que Compõem o Kit</h3>
                                    <p class="text-xs text-slate-500">Total de peças: <span class="font-bold text-brand-dark" x-text="totalPieces()"></span></p>
                                </div>
                                <button type="button" @click="addItem()" class="bg-brand/10 hover:bg-brand text-brand-dark hover:text-white font-bold text-xs py-1.5 px-3 rounded-lg transition-colors">
                                    + Adicionar Item
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex flex-col sm:flex-row items-center gap-3 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                                        <!-- Produto -->
                                        <div class="flex-1 w-full">
                                            <select :name="`items[${index}][product_id]`" 
                                                    x-model="item.product_id" 
                                                    @change="onProductChange(item)"
                                                    class="border-slate-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-lg text-sm w-full py-2 px-3" required>
                                                <option value="">Selecione o produto...</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}">
                                                        {{ $p->name }} ({{ $p->size ?? 'Único' }}) - R$ {{ number_format($p->price, 2, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Quantidade -->
                                        <div class="w-full sm:w-28">
                                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" placeholder="Qtd" class="border-slate-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-lg text-sm w-full py-2 px-3" required>
                                        </div>

                                        <!-- Preço Unitário no Kit -->
                                        <div class="w-full sm:w-32">
                                            <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" placeholder="R$ Unit." class="border-slate-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-lg text-sm w-full py-2 px-3">
                                        </div>

                                        <!-- Subtotal e Remover -->
                                        <div class="flex items-center gap-2 justify-between w-full sm:w-auto">
                                            <span class="text-xs font-bold text-slate-700 whitespace-nowrap min-w-[70px] text-right">
                                                R$ <span x-text="((item.quantity || 0) * (item.unit_price || 0)).toFixed(2)"></span>
                                            </span>
                                            <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50" title="Remover item">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-5">
                            <label class="block text-slate-700 text-sm font-bold mb-2" for="description">Descrição / Observações</label>
                            <textarea name="description" id="description" rows="3" class="border-slate-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 px-3 text-sm">{{ old('description', $kit->description) }}</textarea>
                        </div>

                        <!-- Ativo -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-brand shadow-sm focus:ring-brand-secondary" {{ old('is_active', $kit->is_active) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm font-medium text-slate-700">Kit ativo (disponível para seleção nas reservas)</span>
                            </label>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('kits.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-colors">
                                Atualizar Kit
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @php
        $initialKitItems = old('items');
        if (!$initialKitItems) {
            $initialKitItems = [];
            foreach ($kit->items as $item) {
                $initialKitItems[] = [
                    'product_id' => $item->product_id,
                    'quantity' => (int)$item->quantity,
                    'unit_price' => (float)$item->unit_price,
                ];
            }
        }
        if (empty($initialKitItems)) {
            $initialKitItems = [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]];
        }
    @endphp

    <script>
        function kitForm() {
            let productsMap = @json($products->keyBy('id'));
            let initialItems = @json($initialKitItems);

            return {
                kitPrice: '{{ old('price', $kit->price) }}',
                items: initialItems,
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    } else {
                        alert('O kit precisa conter pelo menos 1 item.');
                    }
                },
                onProductChange(item) {
                    if (productsMap[item.product_id]) {
                        item.unit_price = parseFloat(productsMap[item.product_id].price);
                    }
                },
                totalPieces() {
                    return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                },
                itemsSum() {
                    return this.items.reduce((sum, item) => sum + ((parseFloat(item.unit_price) || 0) * (parseInt(item.quantity) || 0)), 0);
                }
            }
        }
    </script>
</x-app-layout>

