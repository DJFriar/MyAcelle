@php $menu = $menu ?? false @endphp

<nav class="navbar navbar-expand-xl navbar-dark fixed-top navbar-main frontend py-0">
    <div class="container-fluid ms-0">
        <a class="navbar-brand d-flex align-items-center me-2" href="{{ action('HomeController@index') }}">
            @if (getLogoMode(Auth::user()->customer->theme_mode, Auth::user()->customer->getColorScheme(), request()->session()->get('customer-auto-theme-mode')) == 'dark')
                <img class="logo" src="{{ getSiteLogoUrl('dark') }}" data-dark="{{ getSiteLogoUrl('dark') }}" data-light="{{ getSiteLogoUrl('light') }}" />
            @else
                <img class="logo" src="{{ getSiteLogoUrl('light') }}" data-dark="{{ getSiteLogoUrl('dark') }}" data-light="{{ getSiteLogoUrl('light') }}" />
            @endif
        </a>
        <button class="navbar-toggler" role="button" data-bs-toggle="collapse" data-bs-target="#mainAppNav" aria-controls="mainAppNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <span middle-bar-control="element" class="leftbar-hide-menu middle-bar-element">
            <svg class="SideBurgerIcon-image" viewBox="0 0 50 32"><path d="M49,4H19c-0.6,0-1-0.4-1-1s0.4-1,1-1h30c0.6,0,1,0.4,1,1S49.6,4,49,4z"></path><path d="M49,16H19c-0.6,0-1-0.4-1-1s0.4-1,1-1h30c0.6,0,1,0.4,1,1S49.6,16,49,16z"></path><path d="M49,28H19c-0.6,0-1-0.4-1-1s0.4-1,1-1h30c0.6,0,1,0.4,1,1S49.6,28,49,28z"></path><path d="M8.1,22.8c-0.3,0-0.5-0.1-0.7-0.3L0.7,15l6.7-7.8c0.4-0.4,1-0.5,1.4-0.1c0.4,0.4,0.5,1,0.1,1.4L3.3,15l5.5,6.2   c0.4,0.4,0.3,1-0.1,1.4C8.6,22.7,8.4,22.8,8.1,22.8z"></path></svg>
        </span>

        <div class="collapse navbar-collapse" id="mainAppNav">
            <ul class="navbar-nav me-auto mb-md-0 main-menu">
                <li class="nav-item {{ $menu == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ action('HomeController@index') }}" title="{{ trans('messages.dashboard') }}" class="leftbar-tooltip nav-link d-flex align-items-center py-3 lvl-1">
                        <i class="navbar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92.1 86.1"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><g id="Layer_2-2" data-name="Layer 2"><g id="Layer_1-2-2" data-name="Layer 1-2"><path class="color-badge"  d="M51.8,86.1H41.9a8.5,8.5,0,0,1-8.5-8.5V60.2a8.5,8.5,0,0,1,8.5-8.5h9.9a8.5,8.5,0,0,1,8.5,8.5V77.6A8.5,8.5,0,0,1,51.8,86.1ZM41.9,58.7a1.5,1.5,0,0,0-1.5,1.5V77.6a1.5,1.5,0,0,0,1.5,1.5h9.9a1.5,1.5,0,0,0,1.5-1.5V60.2a1.5,1.5,0,0,0-1.5-1.5Z" style="fill:aqua"/><path d="M60.4,86.1H31.7A20.6,20.6,0,0,1,11.2,65.7V24.6h7V65.7A13.5,13.5,0,0,0,31.7,79.1H60.4A13.5,13.5,0,0,0,73.9,65.7V25.3h7V65.7A20.6,20.6,0,0,1,60.4,86.1Z" style="fill:#f2f2f2"/><path d="M88.6,36.5a3.6,3.6,0,0,1-2-.6L45.7,7.7,5.5,35.1a3.5,3.5,0,1,1-4-5.8L43.7.6a3.6,3.6,0,0,1,4,0L90.6,30.1a3.5,3.5,0,0,1-2,6.4Z" style="fill:#f2f2f2"/></g></g></g></g></svg>
                        </i>
                        <span>{{ trans('messages.dashboard') }}</span>
                    </a>
                </li>
                <li class="nav-item {{ $menu == 'profile' ? 'active' : '' }}">
                    <a title="{{ trans('messages.my_profile') }}" href="{{ action("AccountController@profile") }}" class="leftbar-tooltip nav-link d-flex align-items-center py-3 lvl-1">
                        <i class="navbar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M7,12.3a1.8,1.8,0,0,1-.9-.4,1.4,1.4,0,0,1-.3-.9,1.6,1.6,0,0,1,.3-.9L7,9.8a1.4,1.4,0,0,1,.9.3,1.8,1.8,0,0,1,.4.9A1.4,1.4,0,0,1,7,12.3Zm6,0a1.8,1.8,0,0,1-.9-.4,1.4,1.4,0,0,1-.3-.9,1.6,1.6,0,0,1,.3-.9l.9-.3a1.4,1.4,0,0,1,.9.3,1.8,1.8,0,0,1,.4.9A1.4,1.4,0,0,1,13,12.3ZM10,18a8.1,8.1,0,0,0,5.7-2.3A8.1,8.1,0,0,0,18,10a4.9,4.9,0,0,0-.1-1.2,3.6,3.6,0,0,0-.2-1H15.5a9.4,9.4,0,0,1-4.3-1A8.9,8.9,0,0,1,7.8,4.3,11.3,11.3,0,0,1,5.5,7.7,11.5,11.5,0,0,1,2,9.9H2a8.1,8.1,0,0,0,2.3,5.7A8.1,8.1,0,0,0,10,18Zm0,2a10.1,10.1,0,0,1-3.9-.8,9.9,9.9,0,0,1-3.2-2.1A9.9,9.9,0,0,1,.8,13.9a9.9,9.9,0,0,1,0-7.8A9.9,9.9,0,0,1,2.9,2.9,9.9,9.9,0,0,1,6.1.8a9.9,9.9,0,0,1,7.8,0,9.9,9.9,0,0,1,3.2,2.1,9.9,9.9,0,0,1,2.1,3.2,9.9,9.9,0,0,1,0,7.8,9.9,9.9,0,0,1-2.1,3.2,9.9,9.9,0,0,1-3.2,2.1A10.1,10.1,0,0,1,10,20ZM8.7,2.1a7.5,7.5,0,0,0,2.8,2.8,7.7,7.7,0,0,0,4,1.1h1.4A7.8,7.8,0,0,0,14,3.1,7.7,7.7,0,0,0,10,2H8.7ZM2.4,7.5A8.3,8.3,0,0,0,4.7,5.6,8.2,8.2,0,0,0,6.1,3,6.7,6.7,0,0,0,3.9,4.9,6.8,6.8,0,0,0,2.4,7.5Z" style="fill:#f2f2f2"/></g></g></svg>
                        </i>
                        <span>{{ trans('messages.my_profile') }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ $menu == 'contact' ? 'active' : '' }}">
                    <a title="{{ trans('messages.contact_information') }}" href="{{ action("AccountController@contact") }}" class="leftbar-tooltip nav-link d-flex align-items-center py-3 lvl-1">
                        <i class="navbar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 18"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M16,6h2V4H16Zm0,4h2V8H16Zm0,4h2V12H16Zm0,4V16h4V2H11V3.4L9,2A1.8,1.8,0,0,1,9.6.6,2,2,0,0,1,11,0h9a1.8,1.8,0,0,1,1.4.6A2,2,0,0,1,22,2V16a2.1,2.1,0,0,1-2,2ZM0,17V9a2.9,2.9,0,0,1,.2-.9,2.3,2.3,0,0,1,.7-.7l5-3.6A2,2,0,0,1,7,3.5a2.7,2.7,0,0,1,1.2.3l5,3.6.6.7A2.9,2.9,0,0,1,14,9v8a1,1,0,0,1-1,1H8V13H6v5H1a.9.9,0,0,1-.7-.3A.9.9,0,0,1,0,17Zm2-1H4V11h6v5h2V9L7,5.5,2,9Zm8,0V11H4v0h6Z" style="fill:#f2f2f2"/></g></g></svg>
                        </i>
                        <span>{{ trans('messages.contact_information') }}</span>
                    </a>
                </li>

                <li class="nav-item {{ $menu == 'billing' ? 'active' : '' }}">
                    <a title="{{ trans('messages.billing') }}" href="{{ action("AccountController@billing") }}" class="leftbar-tooltip nav-link d-flex align-items-center py-3 lvl-1">
                        <i class="navbar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 16"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M20,2V14a2.1,2.1,0,0,1-2,2H2a2,2,0,0,1-1.4-.6A1.8,1.8,0,0,1,0,14V2A2,2,0,0,1,.6.6,2,2,0,0,1,2,0H18a1.8,1.8,0,0,1,1.4.6A2,2,0,0,1,20,2ZM2,4H18V2H2ZM2,8v6H18V8Zm0,6v0Z" style="fill:#f2f2f2"/></g></g></svg>
                        </i>
                        <span>{{ trans('messages.billing') }}</span>
                    </a>
                </li>

                <li class="nav-item {{ $menu == 'subscription' ? 'active' : '' }}">
                    <a title="{{ trans('messages.billing') }}" href="{{ action("SubscriptionController@index") }}" class="leftbar-tooltip nav-link d-flex align-items-center py-3 lvl-1">
                        <i class="navbar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 20"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M2,20a2,2,0,0,1-1.4-.6A1.8,1.8,0,0,1,0,18V4A2,2,0,0,1,.6,2.6,2,2,0,0,1,2,2H6.2A2.6,2.6,0,0,1,7.3.6a2.7,2.7,0,0,1,3.4,0A2.6,2.6,0,0,1,11.8,2H16a1.8,1.8,0,0,1,1.4.6A2,2,0,0,1,18,4V18a2.1,2.1,0,0,1-2,2Zm0-2H16V4H2Zm3-2h5a1,1,0,0,0,.7-1.7A.9.9,0,0,0,10,14H5a.9.9,0,0,0-.7.3,1,1,0,0,0,0,1.4A.9.9,0,0,0,5,16Zm0-4h8a1,1,0,0,0,1-1,1,1,0,0,0-1-1H5a.9.9,0,0,0-.7.3,1,1,0,0,0,0,1.4A.9.9,0,0,0,5,12ZM5,8h8a1,1,0,0,0,1-1,1,1,0,0,0-1-1H5a.9.9,0,0,0-.7.3,1,1,0,0,0,0,1.4A.9.9,0,0,0,5,8ZM9,3.3A.5.5,0,0,0,9.5,3a.6.6,0,0,0,0-1c-.1-.2-.3-.2-.5-.2s-.4,0-.5.2-.2.3-.2.5,0,.4.2.5A.5.5,0,0,0,9,3.3ZM2,18v0Z" style="fill:#f2f2f2"/></g></g></svg>
                        </i>
                        <span>{{ trans('messages.subscription') }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ $menu == 'log' ? 'active' : '' }}">
                    <a title="{{ trans('messages.billing') }}" href="{{ action("AccountController@logs") }}" class="leftbar-tooltip nav-link d-flex align-items-center py-3 lvl-1">
                        <i class="navbar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 16"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M2,16a2,2,0,0,1-1.4-.6A1.8,1.8,0,0,1,0,14V10.6a.5.5,0,0,1,.2-.4c.1-.2.2-.3.4-.3a1.8,1.8,0,0,0,1-.7,2,2,0,0,0,0-2.4,1.8,1.8,0,0,0-1-.7L.2,5.9C.1,5.7,0,5.6,0,5.4V2A2,2,0,0,1,.6.6,2,2,0,0,1,2,0H18a1.8,1.8,0,0,1,1.4.6A2,2,0,0,1,20,2V5.4c0,.2-.1.3-.2.5a.5.5,0,0,1-.4.2,1.8,1.8,0,0,0-1,.7,2,2,0,0,0,0,2.4,1.8,1.8,0,0,0,1,.7c.2,0,.3.1.4.3a.5.5,0,0,1,.2.4V14a2.1,2.1,0,0,1-2,2Zm0-2H18V11.5A5.4,5.4,0,0,1,16.5,10,4.6,4.6,0,0,1,16,8a4.6,4.6,0,0,1,.5-2A4,4,0,0,1,18,4.6V2H2V4.6A4,4,0,0,1,3.5,6,4.6,4.6,0,0,1,4,8a4.6,4.6,0,0,1-.5,2A5.4,5.4,0,0,1,2,11.5Zm8-1a1,1,0,0,0,.7-1.7,1,1,0,0,0-1.4,0A1,1,0,0,0,10,13Zm0-4a1,1,0,0,0,1-1,1,1,0,0,0-1-1A1,1,0,0,0,9,8a1,1,0,0,0,1,1Zm0-4a.9.9,0,0,0,.7-.3A.9.9,0,0,0,11,4a.9.9,0,0,0-.3-.7,1,1,0,0,0-1.4,0A.9.9,0,0,0,9,4a.9.9,0,0,0,.3.7A.9.9,0,0,0,10,5Z" style="fill:#f2f2f2"/></g></g></svg>
                        </i>
                        <span>{{ trans('messages.logs') }}</span>
                    </a>
                </li>
                <li class="nav-item {{ $menu == 'api' ? 'active' : '' }}">
                    <a title="{{ trans('messages.billing') }}" href="{{ action("AccountController@api") }}" class="leftbar-tooltip nav-link d-flex align-items-center py-3 lvl-1">
                        <i class="navbar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 12"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path d="M6,12a5.7,5.7,0,0,1-4.2-1.7A5.8,5.8,0,0,1,0,6,5.4,5.4,0,0,1,1.8,1.8,5.4,5.4,0,0,1,6,0,6.1,6.1,0,0,1,9,.8,6.6,6.6,0,0,1,11.2,3H20a1.8,1.8,0,0,1,1.4.6A2,2,0,0,1,22,5V7a1.8,1.8,0,0,1-.6,1.4A1.8,1.8,0,0,1,20,9v1a1.8,1.8,0,0,1-.6,1.4A1.8,1.8,0,0,1,18,12H16a2,2,0,0,1-1.4-.6A1.8,1.8,0,0,1,14,10V9H11.2A6.6,6.6,0,0,1,9,11.2,6.1,6.1,0,0,1,6,12Zm0-2A3.6,3.6,0,0,0,8.7,9,5.3,5.3,0,0,0,9.9,7H16v3h2V7h2V5H9.9A6.2,6.2,0,0,0,8.7,3,3.6,3.6,0,0,0,6,2,3.8,3.8,0,0,0,3.2,3.2,3.8,3.8,0,0,0,2,6,3.8,3.8,0,0,0,3.2,8.8,3.8,3.8,0,0,0,6,10ZM6,8a1.8,1.8,0,0,0,1.4-.6A1.8,1.8,0,0,0,8,6a2,2,0,0,0-.6-1.4A1.8,1.8,0,0,0,6,4,2.1,2.1,0,0,0,4,6a1.8,1.8,0,0,0,.6,1.4A2,2,0,0,0,6,8Z" style="fill:#f2f2f2"/></g></g></svg>
                        </i>
                        <span>{{ trans('messages.api') }}</span>
                    </a>
                </li>
            </ul>
            <div class="navbar-right">
                <ul class="navbar-nav me-auto mb-md-0">
                    @include('layouts.core._top_activity_log')
                    @include('layouts.core._menu_frontend_user')
                </ul>
            </div>
        </div>
    </div>
</nav>