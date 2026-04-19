@props(['href', 'active' => false, 'icon'])

@php
    $base = 'flex items-center gap-3 py-3 font-manrope text-sm tracking-wide rounded-r-lg transition-all';

    $activeClass = 'text-[#00113a] font-extrabold border-l-4 border-[#00113a] pl-4 translate-x-1 bg-slate-100/50';
    $inactiveClass = 'text-slate-400 pl-5 hover:bg-slate-100 hover:text-[#00113a]';

    $classes = $active ? "$base $activeClass" : "$base $inactiveClass";
@endphp

<a href="{{ $href }}" wire:navigate class="{{ $classes }}">
    <span class="material-symbols-outlined"
        @if ($active) style="font-variation-settings: 'FILL' 1;" @endif>
        {{ $icon }}
    </span>

    <span>{{ $slot }}</span>
</a>
