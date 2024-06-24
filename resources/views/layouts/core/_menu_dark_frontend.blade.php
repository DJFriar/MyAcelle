<nav class="navbar navbar-expand-xl navbar-dark bg-dark fixed-top navbar-main py-0">
    <div class="container-fluid ms-0">
        <a class="navbar-brand d-flex align-items-center me-2" href="{{ action('HomeController@index') }}">
            <img class="logo" src="{{ getSiteLogoUrl('light') }}" alt="">
        </a>
        <button class="navbar-toggler" role="button" data-bs-toggle="collapse" data-bs-target="#mainAppNav" aria-controls="mainAppNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainAppNav">
            <ul class="navbar-nav me-auto mb-md-0">
                <li class="nav-item">
                    @yield('menu_title')
                </li>
            </ul>
            <div class="navbar-right">
                <ul class="navbar-nav me-auto mb-md-0">
                    @yield('menu_right')
                </ul>
            </div>
        </div>
    </div>
</nav>
