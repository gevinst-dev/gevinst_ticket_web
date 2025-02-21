@php
    $chatgpt = App\Models\Utility::getValByName('enable_chatgpt');
    $languages = \App\Models\Utility::languages();
    $lang = isset($curr_noti_tempLang->lang) ? $curr_noti_tempLang->lang : 'en';
    if ($lang == null) {
        $lang = 'en';
    }
@endphp

@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Notification Templates') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Notification Templates') }}</li>
@endsection
@push('pre-purpose-css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
@endpush
@section('content')
    @if ($chatgpt == 'on')
        <div class="text-end mb-2">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="{{ route('generate', ['notification-templates']) }}" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
            </a>
        </div>
    @endif
    <div class="pt-md-4">
        <div class="row">
            <div class="col-xxl-3 col-xl-4 col-lg-5 col-md-5 col-12">
                <div class="card manage-notification-left-col sticky-top mb-0">
                    <div class="card-header card-body">
                        <h5 class="font-weight-bold">{{ __('Variables') }}</h5>
                    </div>
                    <div class="text-xs p-md-4 p-3">
                        @php
                            $variables = json_decode($curr_noti_tempLang->variables);
                        @endphp
                        @if (!empty($variables) > 0)
                            @foreach ($variables as $key => $var)
                                    <p class="mb-2">{{ __($key) }} : <span
                                            class="pull-right text-primary">{{ '{' . $var . '}' }}</span></p>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xxl-9 col-xl-8 col-lg-7 col-md-7 col-12">
                <div class="manage-notification-right-col">
                    <div class="card language-sidebar p-2">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $key => $lang)
                                <a class="list-group-item list-group-item-action border-0 rounded {{ $curr_noti_tempLang->lang == $key ? 'active' : '' }}"
                                    href="{{ route('manage.notification.language', [$notification_template->id, $key]) }}">
                                    {{ Str::ucfirst($lang) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="card p-3 border">
                        {{ Form::model($curr_noti_tempLang, ['route' => ['notification-templates.update', $curr_noti_tempLang->parent_id], 'method' => 'PUT']) }}
                        <div class="row">
                            <div class="form-group col-12">
                                {{ Form::label('name', __('Name'), ['class' => 'col-form-label text-dark']) }}
                                {{ Form::text('name', $notification_template->name, ['class' => 'form-control font-style', 'disabled' => 'disabled']) }}
                            </div>
                            <div class="form-group col-12">
                                {{ Form::label('content', __('Notification Message'), ['class' => 'col-form-label text-dark']) }}
                                {{ Form::textarea('content', $curr_noti_tempLang->content, ['class' => 'form-control font-style', 'required' => 'required']) }}
                            </div>
                            <div class="col-md-12 mb-2">
                                {{ Form::hidden('lang', null) }}
                                <input type="submit" value="{{ __('Save') }}"
                                    class="btn btn-print-invoice  btn-primary">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        
    </div>

@endsection
