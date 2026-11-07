<!-- Mobile Header Bar -->
<div class="md:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
    <button @click="sidebarOpen = true" class="p-2 -ml-1 text-slate-600 hover:text-brand hover:bg-slate-100 rounded-xl transition-colors focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <a href="{{ route('dashboard') }}" class="flex items-center">
        <img src="{{ asset('images/logocolorida.png') }}" class="h-9 w-auto object-contain" alt="Otimiza Anfitriã" />
    </a>

    <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-full bg-gradient-to-tr from-brand to-brand-accent text-white flex items-center justify-center font-bold text-xs shadow-sm">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </a>
</div>

<!-- Mobile Backdrop Drawer -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 md:hidden"
     style="display: none;">
</div>

<!-- Sidebar Container -->
<aside :class="{
           'translate-x-0': sidebarOpen,
           '-translate-x-full md:translate-x-0': !sidebarOpen,
           'md:w-20': sidebarCollapsed,
           'md:w-72': !sidebarCollapsed
       }"
       class="fixed md:sticky top-0 inset-y-0 left-0 z-50 bg-white border-r border-slate-200/80 shadow-lg md:shadow-none flex flex-col justify-between transition-all duration-300 ease-in-out h-screen shrink-0">
    
    <!-- Top Area: Brand Logo & Navigation -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- Logo Header & Collapse Toggle -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between"
             :class="sidebarCollapsed ? 'flex-col gap-3 py-4' : 'px-5 py-5'">
            
            <!-- Logo (Expanded) -->
            <a href="{{ route('dashboard') }}" 
               x-show="!sidebarCollapsed"
               class="flex items-center group max-w-[170px]">
                <img src="{{ asset('images/logocolorida.png') }}" class="h-14 w-auto object-contain transition-transform duration-200 group-hover:scale-105" alt="Otimiza Anfitriã" />
            </a>

            <!-- Logo (Collapsed - Compact Bed Icon) -->
            <a href="{{ route('dashboard') }}" 
               x-show="sidebarCollapsed"
               title="Otimiza Anfitriã - Painel"
               class="flex items-center justify-center p-1 group">
                <img src="{{ asset('images/favicon.png') }}" class="w-10 h-10 object-contain transition-transform duration-200 group-hover:scale-110" alt="Otimiza Anfitriã" />
            </a>

            <!-- Desktop Collapse/Expand Toggle Button -->
            <button @click="sidebarCollapsed = !sidebarCollapsed" 
                    type="button"
                    :title="sidebarCollapsed ? 'Expandir menu lateral' : 'Recolher menu lateral'"
                    class="hidden md:flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-brand-dark hover:bg-slate-100 transition-colors">
                <!-- Icon: Collapse (chevron left) -->
                <svg x-show="!sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <!-- Icon: Expand (chevron right) -->
                <svg x-show="sidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-5 space-y-6">
            <!-- Section: Menu Principal -->
            <div>
                <!-- Heading (Expanded only) -->
                <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                    Menu Principal
                </p>
                <!-- Divider (Collapsed only) -->
                <div x-show="sidebarCollapsed" class="w-8 h-px bg-slate-200 mx-auto my-2"></div>

                <div class="space-y-1.5">
                    <!-- Painel Geral -->
                    <a href="{{ route('dashboard') }}" 
                       title="Painel Geral"
                       class="flex items-center rounded-xl font-semibold text-sm transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-brand to-brand-secondary text-white shadow-md shadow-brand/25' : 'text-slate-600 hover:text-brand-dark hover:bg-slate-100/80' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-3'">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Painel Geral</span>
                    </a>

                    <!-- Enxovais -->
                    <a href="{{ route('products.index') }}" 
                       title="Enxovais"
                       class="flex items-center rounded-xl font-semibold text-sm transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-gradient-to-r from-brand to-brand-secondary text-white shadow-md shadow-brand/25' : 'text-slate-600 hover:text-brand-dark hover:bg-slate-100/80' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-3'">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Enxovais</span>
                    </a>

                    <!-- Kits -->
                    <a href="{{ route('kits.index') }}" 
                       title="Kits de Enxoval"
                       class="flex items-center rounded-xl font-semibold text-sm transition-all duration-150 {{ request()->routeIs('kits.*') ? 'bg-gradient-to-r from-brand to-brand-secondary text-white shadow-md shadow-brand/25' : 'text-slate-600 hover:text-brand-dark hover:bg-slate-100/80' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-3'">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('kits.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Kits de Enxoval</span>
                    </a>

                    <!-- Reservas -->
                    <a href="{{ route('reservations.index') }}" 
                       title="Reservas"
                       class="flex items-center rounded-xl font-semibold text-sm transition-all duration-150 {{ request()->routeIs('reservations.*') ? 'bg-gradient-to-r from-brand to-brand-secondary text-white shadow-md shadow-brand/25' : 'text-slate-600 hover:text-brand-dark hover:bg-slate-100/80' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-3.5 py-3'">
                        <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('reservations.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Reservas</span>
                    </a>
                </div>
            </div>

            <!-- Section: Ações Rápidas -->
            <div class="pt-2">
                <p x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                    Acesso Rápido
                </p>
                <div x-show="sidebarCollapsed" class="w-8 h-px bg-slate-200 mx-auto my-2"></div>

                <a href="{{ route('reservations.create') }}" 
                   title="Nova Reserva"
                   class="flex items-center rounded-xl bg-brand/10 hover:bg-brand text-brand-dark hover:text-white font-bold text-sm transition-all duration-200 border border-brand/20 group"
                   :class="sidebarCollapsed ? 'justify-center p-3' : 'justify-center gap-2 w-full py-3 px-4'">
                    <svg class="w-4 h-4 text-brand group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="truncate">Nova Reserva</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Bottom Area: User Profile & Logout -->
    <div class="p-3 border-t border-slate-100 bg-slate-50/70">
        <!-- Expanded Profile Card -->
        <div x-show="!sidebarCollapsed" class="flex items-center justify-between p-2 rounded-2xl bg-white border border-slate-200/60 shadow-sm">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand to-brand-accent text-white flex items-center justify-center font-extrabold text-xs shadow-sm shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-800 truncate" title="{{ Auth::user()->name }}">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[11px] text-slate-400 truncate" title="{{ Auth::user()->email }}">
                        {{ Auth::user()->email }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-0.5 shrink-0 ml-1">
                <!-- Perfil -->
                <a href="{{ route('profile.edit') }}" 
                   title="Meu Perfil"
                   class="p-1.5 text-slate-400 hover:text-brand-dark hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                    </svg>
                </a>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            title="Sair do sistema"
                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Collapsed Profile Icons -->
        <div x-show="sidebarCollapsed" class="flex flex-col items-center gap-2 py-1">
            <a href="{{ route('profile.edit') }}" 
               title="{{ Auth::user()->name }} (Meu Perfil)"
               class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand to-brand-accent text-white flex items-center justify-center font-extrabold text-xs shadow-sm hover:scale-105 transition-transform">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        title="Sair do sistema"
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>
