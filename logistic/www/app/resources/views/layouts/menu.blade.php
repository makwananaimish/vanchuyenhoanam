@if (auth()->check())
    @if (auth()->user()->role === \App\User::ROLE_ADMIN || auth()->user()->role === \App\User::ROLE_SELLER)
        @include('layouts.menus.default')
    @endif

    @if (auth()->user()->role === \App\User::ROLE_ACCOUNTANT)
        @include('layouts.menus.accountant')
    @endif

    @if (auth()->user()->role === \App\User::ROLE_VN_INVENTORY)
        @include('layouts.menus.vn-inventory')
    @endif

    @if (auth()->user()->role === \App\User::ROLE_CN_INVENTORY)
        @include('layouts.menus.cn-inventory')
    @endif
@endif

@if (auth()->guard('customer')->check())
    @include('layouts.menus.customer')
@endif
