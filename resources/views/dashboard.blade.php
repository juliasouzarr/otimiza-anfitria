<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-dark leading-tight">
            {{ __('Painel de Controle') }}
        </h2>
    </x-slot>

    <!-- Adicionando CDN do FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.9/locales/pt-br.global.min.js'></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Hero -->
            <div class="p-8 bg-gradient-to-r from-brand to-brand-secondary rounded-3xl shadow-lg text-white flex flex-col md:flex-row justify-between items-center relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-40 -bottom-20 w-48 h-48 bg-brand-accent opacity-20 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 text-center md:text-left mb-6 md:mb-0">
                    <h1 class="text-3xl font-extrabold mb-2 tracking-tight">Olá, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h1>
                    <p class="text-brand-accent text-lg font-medium opacity-90">Bem-vinda à gestão inteligente dos seus enxovais.</p>
                </div>

                <div class="flex gap-4 relative z-10">
                    <a href="{{ route('reservations.create') }}" class="bg-white text-brand-dark font-bold py-3 px-6 rounded-full shadow-md hover:scale-105 transition-transform duration-200">
                        + Nova Reserva
                    </a>
                </div>
            </div>

            <!-- Cards de Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 flex items-center border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="p-4 rounded-2xl bg-brand bg-opacity-10 text-brand">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="ml-5">
                        <p class="mb-1 text-sm font-semibold text-gray-500 uppercase tracking-wider">Reservas Ativas</p>
                        <p class="text-3xl font-black text-brand-dark">{{ $stats['active_reservations'] }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 flex items-center border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="p-4 rounded-2xl bg-brand-secondary bg-opacity-10 text-brand-secondary">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div class="ml-5">
                        <p class="mb-1 text-sm font-semibold text-gray-500 uppercase tracking-wider">Enxovais em uso (hoje)</p>
                        <p class="text-3xl font-black text-brand-dark">{{ $stats['items_in_use'] }} <span class="text-lg font-medium text-gray-400">peças</span></p>
                    </div>
                </div>
            </div>

            <!-- Calendário -->
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-xl font-bold text-brand-dark mb-6">Visão Geral de Reservas</h3>
                    <div id='calendar' class="rounded-xl overflow-hidden"></div>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia'
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: @json($events),
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault(); 
                    }
                },
                displayEventTime: false,
                height: 700,
                firstDay: 1 
            });
            calendar.render();
        });
    </script>
    
    <style>
        .fc-event {
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
            padding: 3px 6px;
            border-radius: 6px;
            font-weight: 600;
            border: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .fc-event:hover {
            transform: scale(1.02);
            opacity: 0.9;
        }
        .fc-toolbar-title {
            color: #135161;
            font-weight: 800;
            font-size: 1.5rem !important;
        }
        .fc .fc-button-primary {
            background-color: #2298b6;
            border-color: #2298b6;
            border-radius: 8px;
            font-weight: bold;
            text-transform: capitalize;
        }
        .fc .fc-button-primary:hover {
            background-color: #2890a8;
            border-color: #2890a8;
        }
        .fc .fc-button-primary:not(:disabled).fc-button-active, 
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #135161;
            border-color: #135161;
        }
        .fc .fc-daygrid-day.fc-day-today {
            background-color: #97cfc515; 
        }
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #f3f4f6;
        }
    </style>
</x-app-layout>
