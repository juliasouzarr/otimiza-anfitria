@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-brand-secondary focus:ring-brand-secondary rounded-md shadow-sm text-gray-800 font-medium placeholder-gray-400']) }}>
