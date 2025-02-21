<nav class="navbar navbar-expand-md navbar-dark default">
    <div class="container-fluid pe-2">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ $logos . 'logo-dark.png' . '?' . time() }}" alt="logo" style="width:150px;" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01"
            aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">{{ __('Create Ticket') }}</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('search') }}">{{ __('Search Ticket') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                <li class="nav-item">
                    @if ($setting['FAQ'] == 'on')
                        <a class="nav-link" href="{{ route('faq') }}">{{ __('FAQ') }}</a>
                    @endif
                </li>
                <li class="nav-item">
                    @if ($setting['Knowlwdge_Base'] == 'on')
                        <a class="nav-link" href="{{ route('knowledge') }}">{{ __('Knowledge') }}</a>
                    @endif
                </li>


                @if (request()->routeIs('login') || request()->routeIs('password.request') || request()->routeIs('search'))
                    <div class="lang-dropdown-only-desk">
                        <li class="dropdown dash-h-item drp-language">
                            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <span class="drp-text"> {{ ucfirst($LangName->fullName) }}
                                </span>
                            </a>
                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                                @foreach (App\Models\Utility::languages() as $code => $language)
                                    <a href="{{ route('login', $code) }}" tabindex="0"
                                        class="dropdown-item dropdown-item {{ $LangName->code == $code ? 'active' : '' }}">
                                        <span>{{ ucFirst($language) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    </div>
                @endif
            </ul>
        </div>
    </div>
</nav>
