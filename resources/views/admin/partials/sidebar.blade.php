   @php
       use App\Models\Utility;
       $logo = Utility::get_superadmin_logo();
       $emailTemplate = App\Models\EmailTemplate::first();
       $logos = \App\Models\Utility::get_file('uploads/logo/');
   @endphp

   @if ((isset($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on') || $settings['SITE_RTL'] == 'on')
       <nav class="dash-sidebar light-sidebar transprent-bg">
       @else
           <nav class="dash-sidebar light-sidebar">
   @endif
   {{-- <nav class="dash-sidebar light-sidebar {{ (!empty($setting['cust_theme_bg']) && $setting['cust_theme_bg']) == 'off' ? '' : 'transprent-bg' }}"> --}}
   {{-- <nav class="dash-sidebar light-sidebar transprent-bg"> --}}

   <div class="navbar-wrapper">
       <div class="m-header main-logo">
           <a href="{{ route('admin.dashboard') }}" class="b-brand">
               <img src="{{ $logos . (isset($logo) && !empty($logo) ? $logo . '?' . time() : 'logo-dark.png' . '?' . time()) }}"
                   alt="{{ config('app.name', 'TicketGo SaaS') }}" class="logo logo-lg">
           </a>
       </div>
       <div class="navbar-content">
           <ul class="dash-navbar">
               <li class="dash-item {{ request()->is('*dashboard*') ? ' active' : '' }}">
                   <a href="{{ route('admin.dashboard') }}" class="dash-link "><span class="dash-micon"><i
                               class="ti ti-home"></i></span><span class="dash-mtext">{{ __('Dashboard') }}</span></a>
               </li>
               @can('manage-users')
                   <li class="dash-item {{ request()->is('*users*') ? ' active' : '' }}">
                       <a href="{{ route('admin.users') }}" class="dash-link"><span class="dash-micon"><i
                                   class="ti ti-users"></i></span><span class="dash-mtext">{{ __('Users') }}</span></a>
                   </li>
               @endcan
               @can('manage-tickets')
                   <li class="dash-item {{ request()->is('*ticket*') ? ' active' : '' }}">
                       <a href="{{ route('admin.tickets.index') }}" class="dash-link"><span class="dash-micon"><i
                                   class="ti ti-ticket"></i></span><span class="dash-mtext">{{ __('Tickets') }}</span></a>
                   </li>
               @endcan
               {{-- @can('manage-category')
                    <li class="dash-item {{ (\Request::route()->getName()=='admin.category' || \Request::route()->getName()=='admin.category.create' || \Request::route()->getName()=='admin.category.edit') ? ' active' : '' }}">
                        <a href="{{ route('admin.category') }}" class="dash-link"><span class="dash-micon"><i class="ti ti-clipboard-list"></i></span><span class="dash-mtext">{{ __('Category') }}</span></a>
                    </li>
                @endcan  --}}
               @can('manage-faq')
                   @if (Utility::getSettingValByName('FAQ') == 'on')
                       <li class="dash-item {{ request()->is('*faq*') ? ' active' : '' }}">
                           <a href="{{ route('admin.faq') }}" class="dash-link"><span class="dash-micon"><i
                                       class="ti ti-question-mark"></i></span><span
                                   class="dash-mtext">{{ __('FAQ') }}</span></a>
                       </li>
                   @endif
               @endcan
               @if (Utility::getSettingValByName('CHAT_MODULE') == 'yes')
                   <li class="dash-item {{ request()->is('*Messenger*') ? ' active' : '' }}">
                       <a href="{{ route('admin.chats') }}" class="dash-link"><span class="dash-micon"><i
                                   class="ti ti-brand-hipchat"></i></span><span
                               class="dash-mtext">{{ __('Messenger') }}</span></a>
                   </li>
               @endif
               @can('manage-knowledge')
                   @if (Utility::getSettingValByName('Knowlwdge_Base') == 'on')
                       <li class="dash-item {{ request()->is('*knowledge*') ? ' active' : '' }}">
                           <a href="{{ route('admin.knowledge') }}" class="dash-link"><span class="dash-micon"><i
                                       class="ti ti-school"></i></span><span
                                   class="dash-mtext">{{ __('Knowledge Base') }}</span></a>
                       </li>
                   @endif
               @endcan

               {{-- ------------------------------------------------------------------------------------------------- --}}
                @if (\Auth::user()->parent == 0)
                    <li class="dash-item dash-hasmenu">
                        <a href="#" class="dash-link"><span class="dash-micon"><i
                                    class="ti ti-template"></i></span><span
                                class="dash-mtext">{{ __('Templates') }}</span><span class="dash-arrow"><i
                                    data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @if (\Auth::user()->parent == 0)
                                <li
                                    class="dash-item {{ \Request::route()->getName() == 'notifications-templates.index' ? ' active' : '' }}">
                                    <a href="{{ route('notifications-templates.index') }}" class="dash-link"><span
                                            class="dash-mtext">{{ __('Notification') }}</span></a>
                                </li>
                            @endif

                            @if (\Auth::user()->parent == 0)
                                <li class="dash-item {{ request()->is('*email*') ? ' active' : '' }}">
                                    <a href="{{ route('email_template.index', [$emailTemplate->id, \Auth::user()->lang]) }}"
                                        class="dash-link"><span
                                            class="dash-mtext">{{ __('Email Template') }}</span></a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

               {{-- ------------------------------------------------------------------------------------------------- --}}

               @if (\Auth::user()->parent == 0)
                   <li class="dash-item">
                       <a href="{{ route('admin.category') }}" class="dash-link"><span class="dash-micon"><i
                                   class="ti ti-layout-2"></i></span><span
                               class="dash-mtext">{{ __('Setup') }}</span></a>
                   </li>
               @endif

               @if (\Auth::user()->parent == 0)
                   @can('manage-setting')
                       <li class="dash-item {{ request()->is('*setting*') ? ' active' : '' }}">
                           <a href="{{ route('admin.settings.index') }}" class="dash-link"><span class="dash-micon"><i
                                       class="ti ti-settings"></i></span><span
                                   class="dash-mtext">{{ __('Settings') }}</span></a>
                       </li>
                   @endcan
               @endif
           </ul>
       </div>
   </div>
   </nav>
