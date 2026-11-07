<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-2xl text-brand-dark leading-tight tracking-tight">
                {{ __('Pedido / Reserva #') }}{{ $reservation->id }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('reservations.print', $reservation) }}" target="_blank" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-secondary text-white font-bold py-2.5 px-5 rounded-full shadow-md hover:shadow-lg transition-transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.033-.42-2.12-.42-3.329 0-4.418 3.582-8 8-8s8 3.582 8 8c0 1.21-.18 2.296-.42 3.329M6 18h12M8 14h8m-10 7h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2z"/></svg>
                    Imprimir Pedido (PDF)
                </a>
                <a href="{{ route('reservations.index') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-dark py-2 px-3 rounded-xl hover:bg-slate-100 transition-colors">Voltar</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-2">Informações</h3>
                        <p><strong>Identificador:</strong> {{ $reservation->identifier ?? 'Não informado' }}</p>
                        <p><strong>Início (Retirada):</strong> {{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</p>
                        <p><strong>Fim (Devolução):</strong> {{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</p>
                        <p class="mt-1"><strong>Status:</strong> 
                            @if($reservation->status == 'active')
                                <span class="px-2.5 py-1 text-xs font-bold text-brand-dark bg-brand bg-opacity-20 rounded-full border border-brand border-opacity-30">Ativa</span>
                            @elseif($reservation->status == 'completed')
                                <span class="px-2.5 py-1 text-xs font-bold text-green-800 bg-green-100 rounded-full border border-green-200">Encerrada</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold text-gray-600 bg-gray-100 rounded-full border border-gray-200">Cancelada</span>
                            @endif
                        </p>
                        <p class="mt-2"><strong>Valor Total:</strong> <span class="text-xl font-black text-brand-dark">R$ {{ number_format($reservation->total_price ?? 0, 2, ',', '.') }}</span></p>
                        @if($reservation->notes)
                            <p class="mt-2"><strong>Observações:</strong><br/> {{ $reservation->notes }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <a href="{{ route('reservations.print', $reservation) }}" target="_blank" class="inline-flex items-center gap-2 border border-brand text-brand hover:bg-brand hover:text-white font-bold py-2 px-4 rounded-xl shadow-sm transition-all text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.033-.42-2.12-.42-3.329 0-4.418 3.582-8 8-8s8 3.582 8 8c0 1.21-.18 2.296-.42 3.329M6 18h12M8 14h8m-10 7h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2z"/></svg>
                            Imprimir A4 (PDF)
                        </a>
                        @if($reservation->status == 'active')
                        <a href="{{ route('reservations.edit', $reservation) }}" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2 px-4 rounded-xl shadow-sm transition-all text-sm">
                            Editar Reserva
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Enxovais Reservados</h3>
                    <table class="w-full whitespace-no-wrap border rounded-2xl overflow-hidden shadow-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-semibold tracking-wide text-left text-slate-500 uppercase border-b">
                                <th class="px-4 py-3">Produto</th>
                                <th class="px-4 py-3">Categoria/Tamanho</th>
                                <th class="px-4 py-3 text-center">Quantidade</th>
                                <th class="px-4 py-3 text-right">Valor Unitário</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @foreach ($reservation->items as $item)
                            @php
                                $unitPrice = $item->unit_price > 0 ? $item->unit_price : ($item->product->price ?? 0);
                                $subtotal = $unitPrice * $item->quantity;
                            @endphp
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm font-semibold text-slate-800">
                                    {{ $item->product->name ?? 'Enxoval' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-500">
                                    {{ $item->product->category ?? '-' }} / {{ $item->product->size ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-center">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-slate-600">
                                    R$ {{ number_format($unitPrice, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-slate-900">
                                    R$ {{ number_format($subtotal, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right text-slate-600">Total de peças:</td>
                                <td class="px-4 py-3 text-center text-slate-800">{{ $reservation->items->sum('quantity') }}</td>
                                <td class="px-4 py-3 text-right text-brand-dark uppercase text-xs">Total do Pedido:</td>
                                <td class="px-4 py-3 text-right text-base text-brand-dark font-black">
                                    R$ {{ number_format($reservation->total_price ?? 0, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
