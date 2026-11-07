<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-brand-dark leading-tight tracking-tight">
                    {{ __('Kits de Enxoval') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Conjuntos pré-montados para agilizar a criação de pedidos e reservas.
                </p>
            </div>
            <a href="{{ route('kits.create') }}" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-6 rounded-full shadow-md transition-transform hover:scale-105">
                + Novo Kit
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ search: '', filterStatus: 'all' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm">
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

            <!-- Barra de Busca e Filtros -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="w-full md:w-2/3 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Pesquisar por nome ou tamanho do kit..." 
                        class="pl-11 w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2.5 transition-shadow hover:shadow-md">
                </div>
                
                <div class="w-full md:w-1/3 flex items-center gap-3">
                    <label class="text-sm font-bold text-gray-600 whitespace-nowrap">Status:</label>
                    <select x-model="filterStatus" class="w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2.5">
                        <option value="all">Todos os status</option>
                        <option value="active">Apenas Ativos</option>
                        <option value="inactive">Apenas Inativos</option>
                    </select>
                    <button type="button" x-show="search !== '' || filterStatus !== 'all'" @click="search = ''; filterStatus = 'all'" class="text-xs text-red-500 hover:text-red-700 font-bold underline px-2 py-1 shrink-0" style="display: none;">
                        Limpar
                    </button>
                </div>
            </div>

            <!-- Grid de Kits -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($kits as $kit)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col justify-between {{ !$kit->is_active ? 'opacity-60' : '' }}"
                     x-show="(search === '' || '{{ strtolower(\Illuminate\Support\Str::ascii($kit->name . ' ' . ($kit->size ?? ''))) }}'.includes(search.toLowerCase()) || '{{ strtolower($kit->name . ' ' . ($kit->size ?? '')) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || (filterStatus === 'active' && {{ $kit->is_active ? 'true' : 'false' }}) || (filterStatus === 'inactive' && !{{ $kit->is_active ? 'true' : 'false' }}))">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-brand/10 text-brand-dark border border-brand/20">
                                    {{ $kit->size ?? 'Padrão' }} &bull; {{ $kit->period_days }} dias
                                </span>
                                <h3 class="text-xl font-bold text-slate-800 mt-2">{{ $kit->name }}</h3>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 font-semibold block uppercase">Valor do Kit</span>
                                <span class="text-2xl font-black text-brand-dark">
                                    R$ {{ number_format($kit->price, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        @if($kit->description)
                        <p class="text-sm text-slate-500 mt-3">{{ $kit->description }}</p>
                        @endif

                        <!-- Itens do Kit -->
                        <div class="mt-5 pt-4 border-t border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                                Composição ({{ $kit->items->sum('quantity') }} peças):
                            </p>
                            <ul class="space-y-2">
                                @foreach ($kit->items as $item)
                                <li class="flex items-center justify-between text-sm py-1 border-b border-slate-50">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                                            {{ $item->quantity }}x
                                        </span>
                                        <span class="font-medium text-slate-700">
                                            {{ $item->product->name ?? 'Produto removido' }}
                                        </span>
                                    </div>
                                    <span class="text-slate-500 text-xs font-semibold">
                                        R$ {{ number_format(($item->unit_price ?? $item->product->price ?? 0) * $item->quantity, 2, ',', '.') }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Footer do Card de Kit -->
                    <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            @if($kit->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inativo
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('kits.edit', $kit) }}" class="text-sm font-bold text-brand hover:text-brand-secondary transition-colors">
                                Editar
                            </a>
                            <form action="{{ route('kits.destroy', $kit) }}" method="POST" class="inline" onsubmit="return confirm('Deseja realmente alterar o status deste kit?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold {{ $kit->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }}">
                                    {{ $kit->is_active ? 'Inativar' : 'Ativar' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($kits->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center text-slate-500 border border-slate-200">
                <p class="font-medium text-lg">Nenhum kit cadastrado ainda.</p>
                <a href="{{ route('kits.create') }}" class="mt-4 inline-block bg-brand hover:bg-brand-secondary text-white font-bold py-2 px-6 rounded-full">
                    Cadastrar Primeiro Kit
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>

