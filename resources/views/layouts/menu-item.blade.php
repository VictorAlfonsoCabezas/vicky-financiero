@php
    $children = $item['submenu'] ?? [];
    $active = request()->is(ltrim($item['url'], '/'));
    foreach ($children as $child) {
        $active = $active || request()->is(ltrim($child['url'], '/') . '*');
    }
@endphp
<div class="menu-item {{ $children ? 'has-sub' : '' }} {{ $active ? 'active expand' : '' }}">
    <a href="{{ $children ? '#' : url($item['url']) }}" class="menu-link">
        <div class="menu-icon"><i class="{{ $item['icono'] ?: 'fa fa-circle' }}"></i></div>
        <div class="menu-text">{{ $item['nombre'] }}</div>
        @if($children)<div class="menu-caret"></div>@endif
    </a>
    @if($children)
        <div class="menu-submenu">
            @foreach($children as $child)
                @include('layouts.menu-item', ['item' => $child])
            @endforeach
        </div>
    @endif
</div>
