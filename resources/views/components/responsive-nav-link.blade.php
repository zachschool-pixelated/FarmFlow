@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-white bg-white/15 backdrop-blur-sm transition-all duration-200'
            : 'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <span class="flex-shrink-0 w-5 h-5 {{ $active ? 'text-farm-300' : 'text-white/50 group-hover:text-white/70' }} transition-colors duration-200">
            {{ $icon }}
        </span>
    @endif
    <span>{{ $slot }}</span>
</a>
