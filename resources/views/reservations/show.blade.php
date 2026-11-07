<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-brand-secondary leading-tight">
                {{ __('Detalhes da Reserva #') }}{{ $reservation->id }}
            </h2>
            <a href="{{ route('reservations.index') }}" class="text-gray-500 hover:text-gray-700">Voltar</a>
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
                    @if($reservation->status == 'active')
                    <div>
                        <a href="{{ route('reservations.edit', $reservation) }}" class="bg-brand hover:bg-brand-secondary text-white font-bold py-2 px-4 rounded shadow">
                            Editar Reserva
                        </a>
                    </div>
                    @endif
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
