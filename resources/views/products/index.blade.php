<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-brand-dark leading-tight tracking-tight">
                {{ __('Enxovais') }}
            </h2>
            <a href="{{ route('products.create') }}" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-6 rounded-full shadow-md transition-transform hover:scale-105">
                + Novo Enxoval
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl" x-data="{ search: '', filterStatus: 'all' }">
                <div class="p-6 md:p-8 text-gray-900 overflow-x-auto">
                    
                    <div class="mb-6 bg-slate-50 p-4 rounded-2xl border border-gray-100">
                        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                            <div class="w-full lg:w-1/3 relative">
                                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Pesquisar texto</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <input type="text" x-model="search" placeholder="Busque por nome, categoria..." 
                                        class="pl-11 w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2.5 transition-shadow hover:shadow-md">
                                </div>
                            </div>

                            <div class="w-full lg:w-1/4">
                                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Status</label>
                                <select x-model="filterStatus" class="border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5">
                                    <option value="all">Todos os status</option>
                                    <option value="active">Apenas Ativos</option>
                                    <option value="inactive">Apenas Inativos</option>
                                </select>
                            </div>
                            
                            <div class="w-full lg:w-1/5">
                                <label class="block text-xs font-bold text-brand-dark mb-2 uppercase tracking-wider">Disponibilidade (Início)</label>
                                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" @change="$event.target.form.submit()" 
                                    class="border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 hover:shadow-md transition-shadow">
                            </div>

                            <div class="w-full lg:w-1/5">
                                <label class="block text-xs font-bold text-brand-dark mb-2 uppercase tracking-wider">Disponibilidade (Fim)</label>
                                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" @change="$event.target.form.submit()" 
                                    class="border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm w-full py-2.5 hover:shadow-md transition-shadow">
                            </div>

                            @if($startDate && $endDate)
                            <div class="w-full lg:w-auto pb-1">
                                <a href="{{ route('products.index') }}" class="text-sm text-red-500 hover:text-red-700 font-bold underline bg-red-50 py-2.5 px-3 rounded-xl inline-block">Limpar</a>
                            </div>
                            @endif
                        </form>
                    </div>

                    <table class="w-full whitespace-no-wrap shadow-sm rounded-2xl overflow-hidden">
                        <thead class="bg-brand-dark text-white">
                            <tr class="text-xs font-semibold tracking-wide text-left uppercase border-b border-brand-dark">
                                <th class="px-5 py-4">Nome</th>
                                <th class="px-5 py-4">Categoria</th>
                                <th class="px-5 py-4">Tamanho</th>
                                <th class="px-5 py-4">Valor Unit.</th>
                                <th class="px-5 py-4">Estoque Físico</th>
                                @if($startDate && $endDate)
                                <th class="px-5 py-4 text-brand font-bold bg-brand bg-opacity-10">Livre no Período</th>
                                @endif
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($products as $product)
                            <tr class="text-gray-700 hover:bg-slate-50 transition-colors {{ !$product->is_active ? 'opacity-50' : '' }}" 
                                x-show="(search === '' || '{{ strtolower(\Illuminate\Support\Str::ascii($product->name . ' ' . $product->category . ' ' . $product->size)) }}'.includes(search.toLowerCase()) || '{{ strtolower($product->name . ' ' . $product->category . ' ' . $product->size) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || (filterStatus === 'active' && {{ $product->is_active ? 'true' : 'false' }}) || (filterStatus === 'inactive' && !{{ $product->is_active ? 'true' : 'false' }}))">
                                <td class="px-4 py-3 text-sm font-medium">
                                    {{ $product->name }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $product->category ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $product->size ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-slate-800">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold">
                                    {{ $product->total_quantity }}
                                </td>
                                @if($startDate && $endDate)
                                <td class="px-4 py-3 text-sm font-bold {{ $product->available_quantity > 0 ? 'text-brand' : 'text-red-500' }} bg-brand bg-opacity-5">
                                    {{ $product->available_quantity }}
                                </td>
                                @endif
                                <td class="px-4 py-3 text-sm">
                                    @if($product->is_active)
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Ativo</span>
                                    @else
                                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right space-x-2">
                                    <a href="{{ route('products.edit', $product) }}" class="text-brand hover:text-brand-secondary font-medium">Editar</a>
                                    
                                    @if($product->is_active)
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Deseja realmente inativar este enxoval? Ele não aparecerá mais para novas reservas.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Inativar</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            
                            @if($products->isEmpty())
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Nenhum enxoval cadastrado ainda.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
