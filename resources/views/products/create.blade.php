<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-secondary leading-tight">
            {{ __('Cadastrar Enxoval') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nome do Enxoval *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-brand-secondary" required>
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Categoria</label>
                                <input type="text" name="category" id="category" value="{{ old('category') }}" placeholder="Ex: Cama, Banho" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-brand-secondary">
                                @error('category') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="size">Tamanho</label>
                                <input type="text" name="size" id="size" value="{{ old('size') }}" placeholder="Ex: Casal, Solteiro" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-brand-secondary">
                                @error('size') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="price">Valor Unitário (R$) *</label>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', '0.00') }}" min="0" placeholder="Ex: 18.00" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-brand-secondary" required>
                                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="total_quantity">Quantidade Total *</label>
                                <input type="number" name="total_quantity" id="total_quantity" value="{{ old('total_quantity', 0) }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-brand-secondary" required>
                                @error('total_quantity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="min_stock">Estoque Mínimo</label>
                                <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 0) }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-brand-secondary">
                                @error('min_stock') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-brand shadow-sm focus:ring-brand-secondary" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">Produto Ativo (disponível para novas reservas)</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 mr-4 font-medium">Cancelar</a>
                            <button type="submit" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2 px-6 rounded shadow transition-colors">
                                Salvar Enxoval
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
