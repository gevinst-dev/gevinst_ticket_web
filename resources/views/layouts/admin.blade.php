@php
    use App\Models\Utility;
    $setting = App\Models\Settings::colorset();
    $getSetting = \App\Models\Utility::getSeoSetting();

    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }

    $settings = Utility::settings();
    // $cust_darklayout = $setting['cust_darklayout'];
    $logos = \App\Models\Utility::get_file('uploads/logo/');
    $currantLang = \Auth::user()->lang;
    $SITE_RTL = !empty($setting['SITE_RTL']) ? $setting['SITE_RTL'] : 'off';
@endphp


<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $settings['SITE_RTL'] == 'on' ? 'rtl' : '' }}"> --}}
<html lang="{{ Auth::user()->lang }}" dir="{{ $settings['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="WorkDo" />

    <meta name="title" content="{{ $getSetting['meta_keywords'] }}">
    <meta name="description" content="{{ $getSetting['meta_description'] }}">
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $getSetting['meta_keywords'] }}">
    <meta property="og:description" content="{{ $getSetting['meta_description'] }}">
    <meta property="og:image" content="{{ asset('uploads/metaevent/' . $getSetting['meta_image']) }}">
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $getSetting['meta_keywords'] }}">
    <meta property="twitter:description" content="{{ $getSetting['meta_description'] }}">
    <meta property="twitter:image" content="{{ asset('uploads/metaevent/' . $getSetting['meta_image']) }}">




    <!-- CSRF Token -->
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">


    <meta name="csrf-token" content="{{ csrf_token() }}" />



    <title>
        @yield('page-title') - {{ config('app.name', 'Support Ticket') }}
    </title>

    {{-- <link rel="shortcut icon" href="{{ $logos.'favicon.png'}}"> --}}
    <link rel="shortcut icon" href="{{ $logos . 'favicon.png' }}?timestamp={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }
         :root {
            --support-svg-clr: <?= $color ?>;
        }
    </style>
    <!-- vendor css -->

    @if ($settings['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    @endif

    @if (isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
        <style>
            :root {
                --color-customColor: <?=$color ?>;
            }
        </style>
        <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    @endif


    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">

    <!-- switch button -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/libs/select2/dist/css/select2.min.css') }}">
    @stack('css-page')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

</head>

<body class="{{ $themeColor }}">

    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('admin.partials.sidebar')


    @include('admin.partials.topnav')

    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commonModal"></h5>
                    <a type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commonModal"></h5>
                    <a type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>



    <div class="dash-container">
        <div class="dash-content">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="row page-header-title">
                            <div class="col-md-6">
                                @if (trim($__env->yieldContent('page-title')))
                                    <h4 class="m-0">@yield('page-title')</h4>
                                @endif
                                <ul class="breadcrumb">
                                    @yield('breadcrumb')
                                </ul>
                            </div>
                            <div class="col-md-6 text-right">
                                @if (trim($__env->yieldContent('action-button')))
                                    <div class=" "
                                        @if ($SITE_RTL == 'on') style=" float: left !important;" @endif>
                                        <div class="all-button-box float-end mb-3" style="margin-right: -20px;">
                                            @yield('action-button')
                                        </div>
                                    </div>
                                @elseif(trim($__env->yieldContent('multiple-action-button')))
                                    <div class=" "
                                        @if ($SITE_RTL == 'on') style=" float: left !important;" @endif>
                                        <div style="margin-right: -20px;" class="d-flex justify-content-end">
                                            @yield('multiple-action-button')
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @yield('content')

        </div>
    </div>

    @include('admin.partials.footer')

    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/dash.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>



    <script src="{{ asset('js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/fire.modal.js') }}"></script>

    <script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>

    <script>
        if ($('#pc-dt-simple').length) {
            const dataTable = new simpleDatatables.DataTable("#pc-dt-simple");
        }
    </script>

    <script src="{{ asset('public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <!-- switch button -->
    <script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>

    <script type="text/javascript">
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Are you sure?`,
                    text: "This action can not be undone. Do you want to continue?",
                    icon: "warning",
                    buttons: ["No", "Yes"],
                    //   buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>

    <script>
        var date_picker_locale = {
            format: 'YYYY-MM-DD',
            daysOfWeek: [
                "{{ __('Sun') }}",
                "{{ __('Mon') }}",
                "{{ __('Tue') }}",
                "{{ __('Wed') }}",
                "{{ __('Thu') }}",
                "{{ __('Fri') }}",
                "{{ __('Sat') }}"
            ],
            monthNames: [
                "{{ __('January') }}",
                "{{ __('February') }}",
                "{{ __('March') }}",
                "{{ __('April') }}",
                "{{ __('May') }}",
                "{{ __('June') }}",
                "{{ __('July') }}",
                "{{ __('August') }}",
                "{{ __('September') }}",
                "{{ __('October') }}",
                "{{ __('November') }}",
                "{{ __('December') }}"
            ],
        };
        var calender_header = {
            today: "{{ __('today') }}",
            month: '{{ __('month') }}',
            week: '{{ __('week') }}',
            day: '{{ __('day') }}',
            list: '{{ __('list') }}'
        };
    </script>

    <script>
        var dataTableLang = {
            paginate: {
                previous: "<i class='fas fa-angle-left'>",
                next: "<i class='fas fa-angle-right'>"
            },
            lengthMenu: "{{ __('Show') }} _MENU_ {{ __('entries') }}",
            zeroRecords: "{{ __('No data available in table.') }}",
            info: "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}",
            infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
            infoFiltered: "{{ __('(filtered from _MAX_ total entries)') }}",
            search: "{{ __('Search:') }}",
            thousands: ",",
            loadingRecords: "{{ __('Loading...') }}",
            processing: "{{ __('Processing...') }}"
        }
    </script>



    @if (APP\Models\Utility::getSettingValByName('gdpr_cookie') == 'on')
        <script type="text/javascript">
            var defaults = {
                'messageLocales': {
                    /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
                    'en': "{{ APP\Models\Utility::getSettingValByName('cookie_text') }}"
                },
                'buttonLocales': {
                    'en': 'Ok'
                },
                'cookieNoticePosition': 'bottom',
                'learnMoreLinkEnabled': false,
                'learnMoreLinkHref': '/cookie-banner-information.html',
                'learnMoreLinkText': {
                    'it': 'Saperne di più',
                    'en': 'Learn more',
                    'de': 'Mehr erfahren',
                    'fr': 'En savoir plus'
                },
                'buttonLocales': {
                    'en': 'Ok'
                },
                'expiresIn': 30,
                'buttonBgColor': '#d35400',
                'buttonTextColor': '#fff',
                'noticeBgColor': '#000000',
                'noticeTextColor': '#fff',
                'linkColor': '#009fdd'
            };
        </script>
        <script src="{{ asset('js/cookie.notice.js') }}"></script>
    @endif

    <script>
        function show_toastr(title, message, type) {
            var o, i;
            var icon = '';
            var cls = '';
            if (type == 'success') {
                icon = 'fas fa-check-circle';
                // cls = 'success';
                cls = 'primary';
            } else {
                icon = 'fas fa-times-circle';
                cls = 'danger';
            }


            $.notify({
                icon: icon,
                title: " " + title,
                message: message,
                url: ""
            }, {
                element: "body",
                type: cls,
                allow_dismiss: !0,
                placement: {
                    from: 'top',
                    align: 'right'
                },
                offset: {
                    x: 15,
                    y: 15
                },
                spacing: 10,
                z_index: 1080,
                delay: 2500,
                timer: 2000,
                url_target: "_blank",
                mouse_over: !1,
                animate: {
                    enter: o,
                    exit: i
                },
                // danger
                template: '<div class="toast text-white bg-' + cls +
                    ' fade show" role="alert" aria-live="assertive" aria-atomic="true">' +
                    '<div class="d-flex">' +
                    '<div class="toast-body"> ' + message + ' </div>' +
                    '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div>' +
                    '</div>'
                // template: '<div class="alert alert-{0} alert-icon alert-group alert-notify" data-notify="container" role="alert"><div class="alert-group-prepend alert-content"><span class="alert-group-icon"><i data-notify="icon"></i></span></div><div class="alert-content"><strong data-notify="title">{1}</strong><div data-notify="message">{2}</div></div><button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
            });
        }
    </script>

    <script>
        feather.replace();
        var pctoggle = document.querySelector("#pct-toggler");
        if (pctoggle) {
            pctoggle.addEventListener("click", function() {
                if (
                    !document.querySelector(".pct-customizer").classList.contains("active")
                ) {
                    document.querySelector(".pct-customizer").classList.add("active");
                } else {
                    document.querySelector(".pct-customizer").classList.remove("active");
                }
            });
        }
        var themescolors = document.querySelectorAll(".themes-color > a");
        for (var h = 0; h < themescolors.length; h++) {
            var c = themescolors[h];

            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }

        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }
    </script>


    @stack('scripts')
    @if (Session::has('success'))
        <script>
            show_toastr('{{ __('Success') }}', '{!! session('success') !!}', 'success');
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            show_toastr('{{ __('Error') }}', '{!! session('error') !!}', 'error');
        </script>
    @endif
</body>
@include('layouts.cookie_consent')

</html>
