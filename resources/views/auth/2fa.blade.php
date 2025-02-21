@extends('layouts.auth')

@section('page-title')
    {{ __('Login') }}
@endsection
@php

    use App\Models\Utility;
    $logo = Utility::get_superadmin_logo();
    $logos = \App\Models\Utility::get_file('uploads/logo/');

    $lang = app()->getLocale();
    if ($lang == 'ar' || $lang == 'he') {
        $settings['SITE_RTL'] = 'on';
    }
    $LangName = \App\Models\Languages::where('code', $lang)->first();

    if (empty($LangName)) {
        $LangName = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
    $setting = App\Models\Settings::colorset();

    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
    $settings = \App\Models\Utility::settings();
    config([
        'captcha.secret' => $settings['NOCAPTCHA_SECRET'],
        'captcha.sitekey' => $settings['NOCAPTCHA_SITEKEY'],
        'options' => [
            'timeout' => 30,
        ],
    ]);
@endphp
@section('content')
    <div class="custom-login">
        <div class="login-bg-img">

            <img src="{{ isset($setting['color_flag']) && $setting['color_flag'] == 'false' ? asset('assets/images/auth/' . $themeColor . '.svg') : asset('assets/images/auth/theme-3.svg') }}"
                class="login-bg-1">

            <img src="{{ asset('assets/images/user2.svg') }}" class="login-bg-2">
        </div>
        <div class="bg-login bg-primary"></div>
        <div class="custom-login-inner">
            <nav class="navbar navbar-expand-md default">
                <div class="container pe-2">
                    <div class="navbar-brand">
                        <a href="#">
                            <img src="{{ $logos . $logo . '?timestamp=' . time() }}"
                                alt="{{ config('app.name', 'TicketGo Saas') }}" alt="logo" loading="lazy"
                                class="logo" />
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarlogin">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarlogin">
                        <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">{{ __('Create Ticket') }}</a>
                            </li>

                            <li class="nav-item">
                                @if ($settings['FAQ'] == 'on')
                                    <a class="nav-link" href="{{ route('faq') }}">{{ __('FAQ') }}</a>
                                @endif
                            </li>
                            <li class="nav-item">
                                @if ($settings['Knowlwdge_Base'] == 'on')
                                    <a href="{{ route('knowledge') }}" class="nav-link">{{ __('Knowledge') }}</a>
                                @endif
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('search') }}">{{ __('Search Ticket') }}</a>
                            </li>
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
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="custom-wrapper">
                <div class="custom-row">

                    <div class="card">
                        <div class="row align-items-center text-start">
                            <div class="card-body">
                                <div class="">
                                    <h2 class="mb-3 f-w-600">{{ __('Login') }}</h2>
                                </div>
                                <form class="form-horizontal" action="{{ route('2faVerify') }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="2fa_referrer"
                                        value="{{ request()->get('2fa_referrer') ?? URL()->current() }}">

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <p>{{ __('Please enter the') }} <strong>{{ __(' OTP') }}</strong>
                                                {{ __(' generated on your Authenticator App') }}. <br>
                                                {{ __('Ensure you submit the current one because it refreshes every 30 seconds') }}.
                                            </p>
                                            <label for="one_time_password"
                                                class="col-md-12 form-label">{{ __('One Time Password') }}</label>
                                            <input id="one_time_password" type="password"
                                                class="form-control @if ($errors->any()) is-invalid @endif"
                                                name="one_time_password" required="required" autofocus>
                                            @if ($errors->any())
                                                <span class="error invalid-email text-danger" role="alert">
                                                    @foreach ($errors->all() as $error)
                                                        <small>{{ $error }}</small>
                                                    @endforeach
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4 mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Login') }}
                                            </button>
                                            <a href="{{ route('login') }}"
                                                class="btn btn-danger text-white">{{ __('Logout') }}
                                            </a>
                                            {{-- <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger text-white">
                                                    {{ __('Logout') }}
                                                </button>
                                            </form> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <span>&copy; {{ date('Y') }}
                                    {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'TicketGo') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
