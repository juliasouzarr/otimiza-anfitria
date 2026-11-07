<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-brand-secondary focus:bg-brand-secondary active:bg-[#1c7c94] focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition ease-in-out duration-150 shadow-md']) }}>
    {{ $slot }}
</button>
