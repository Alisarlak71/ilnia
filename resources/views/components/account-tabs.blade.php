{{-- Tabs between the customer's account pages. --}}
@php
    $tabs = [
        ['route' => 'account.orders', 'match' => 'account.orders*', 'label' => __('account.orders_title')],
        ['route' => 'account.addresses', 'match' => 'account.addresses', 'label' => __('account.addresses_title')],
    ];
@endphp

<nav aria-label="{{ __('account.account_nav') }}" {{ $attributes->merge(['class' => 'flex justify-center']) }}>
    <ul class="inline-flex gap-1 rounded-btn border border-line bg-paper p-1 text-sm">
        @foreach ($tabs as $tab)
            @php($active = request()->routeIs($tab['match']))
            <li>
                <a href="{{ route($tab['route']) }}"
                   @class(['block rounded-btn px-4 py-2 transition-colors', 'bg-ink font-bold text-cream' => $active, 'hover:bg-cream' => ! $active])
                   @if ($active) aria-current="page" @endif>{{ $tab['label'] }}</a>
            </li>
        @endforeach
    </ul>
</nav>
