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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl" x-data="{ search: '', filterStatus: 'all' }">
                <div class="p-6 md:p-8 text-gray-900 overflow-x-auto">
                    
                    <!-- Modern Filter Bar -->
                    <div class="mb-6 bg-slate-50 p-4 rounded-2xl border border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div class="w-full md:w-1/2 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                            <input type="text" x-model="search" placeholder="Pesquisar por cliente, ref ou ID..." 
                                class="pl-11 w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2.5 transition-shadow hover:shadow-md">
                        </div>
                        
                        <div class="w-full md:w-1/3 flex items-center gap-3">
                            <label class="text-sm font-bold text-gray-600 whitespace-nowrap">Status:</label>
                            <select x-model="filterStatus" class="w-full border-gray-200 focus:border-brand-secondary focus:ring-brand-secondary rounded-xl shadow-sm py-2.5">
                                <option value="all">Todas</option>
                                <option value="active">Ativas</option>
                                <option value="completed">Encerradas</option>
                                <option value="cancelled">Canceladas</option>
                            </select>
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
                                <th class="px-5 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($reservations as $reservation)
                            <tr class="text-gray-700 hover:bg-slate-50 transition-colors {{ $reservation->status == 'cancelled' ? 'opacity-50' : '' }}" 
                                x-show="(search === '' || '{{ strtolower(\Illuminate\Support\Str::ascii($reservation->identifier ?? '')) }}'.includes(search.toLowerCase()) || '{{ $reservation->id }}'.includes(search)) && (filterStatus === 'all' || '{{ $reservation->status }}' === filterStatus)">
                                
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
                                <td class="px-5 py-4 text-sm text-right space-x-3">
                                    <a href="{{ route('reservations.show', $reservation) }}" class="text-gray-500 hover:text-brand-dark font-semibold transition-colors">Detalhes</a>
                                    
                                    @if($reservation->status == 'active')
                                    <a href="{{ route('reservations.edit', $reservation) }}" class="text-brand hover:text-brand-secondary font-semibold transition-colors">Gerenciar</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            
                            @if($reservations->isEmpty())
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium bg-gray-50">
                                    Nenhuma reserva encontrada.
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
