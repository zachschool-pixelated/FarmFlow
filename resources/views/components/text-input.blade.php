@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-farm-500 focus:ring-farm-500 rounded-xl shadow-sm text-gray-900 placeholder-gray-400']) }}>
