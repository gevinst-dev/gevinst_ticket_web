@php
    use App\Models\Utility;

    $settings = App\Models\Utility::settings();
    $logo = \App\Models\Utility::get_file('public/');
    $Lang = $curr_noti_tempLang->language ?? App\Models\Languages::where('code', $currantLang)->first();

@endphp

@if ($settings['cust_theme_bg'] == 'on')
    <header class="dash-header transprent-bg">
    @else
        <header class="dash-header">
@endif
<div class="header-wrapper">
    <div class="me-auto dash-mob-drp">
        <ul class="list-unstyled">
            <li class="dash-h-item mob-hamburger">
                <a href="#!" class="dash-head-link" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
            </li>

            <li class="dropdown dash-h-item drp-company">
                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">

                    <span class="theme-avtar">
                        <img src="{{ !empty(\Auth::user()->avatar) ? $logo . \Auth::user()->avatar : $logo . '/avatar.png' }}"
                            class="img-fluid rounded-circle" class="header-avtar" width="50">
                    </span>
                    <span class="hide-mob ms-2">{{ __('Hi') }}, {{ Auth::user()->name }}</span>
                    <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown">

                    {{-- <a href="{{ Auth::user()->profilelink }}" class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{ __('Profile') }}</span>
                        </a> --}}
                    <a href="{{ route('profile') }}" class="dropdown-item">
                        <i class="ti ti-user text-dark"></i><span>{{ __('Profile') }}</span>
                    </a>
                    <a href="#!" class="dropdown-item"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="ti ti-power"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
    @php
        $unseenCounter = App\Models\FloatingChatMessage::where('id', Auth::user()->id)
            ->where('is_read', 0)
            ->count();
    @endphp
    <div class="ms-auto">
        <ul class="list-unstyled">
            @if (\Auth::user()->type != 'Super Admin' && \Auth::user()->type != 'Agent')
                @if (Utility::settings()['CHAT_MODULE'] == 'yes')
                    <li class="dash-h-item">
                        <a class="dash-head-link me-0" href="{{ route('admin.chats') }}">
                            <i class="ti ti-message-circle"></i>
                            <span
                                class="bg-danger px-1 mb-1 dash-h-badge message-counter custom_messanger_counter">{{ $unseenCounter }}<span
                                    class="sr-only"></span>
                        </a>
                    </li>
                @endif
            @endif
            <li class="dropdown dash-h-item drp-language">
                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ti ti-world nocolor"></i>
                    <span class="drp-text hide-mob">{{ ucFirst($Lang->fullName) }}</span>
                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                    <div class="dropdown-menu-inner">
                        @foreach (App\Models\Utility::languages() as $code => $lang)
                            <a href="{{ route('admin.lang.update', $code) }}"
                                class="dropdown-item {{ $currantLang == $code ? 'text-primary' : '' }}">
                                <span>{{ ucFirst($lang) }}</span>
                            </a>
                        @endforeach

                        @if (\Auth::user()->parent == 0)
                            @can('lang-create')
                                <a href="#" data-url="{{ route('admin.lang.create') }}" data-size="md"
                                    data-ajax-popup="true" data-title="{{ __('Create New Language') }}"
                                    class="dropdown-item border-top py-1 text-primary">{{ __('Create Language') }}</a>
                                </a>
                            @endcan
                            @can('lang-manage')
                                <a href="{{ route('admin.lang.index', [$currantLang]) }}"
                                    class="dropdown-item border-top py-1 text-primary">{{ __('Manage Languages') }}
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
</header>
