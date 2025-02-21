
@php
    $chatgpt_key = App\Models\Utility::getValByName('chatgpt_key');
    $chatgpt_enable = !empty($chatgpt_key);
    $languages = \App\Models\Utility::languages();
    $lang = isset($currEmailTempLang->lang) ? $currEmailTempLang->lang : 'en';
    if ($lang == null) {
        $lang = 'en';
    }
    $LangName = $currEmailTempLang->language;
@endphp
@extends('layouts.admin')

@section('page-title')
{{__('Email Templates')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Email Templates') }}</li>
@endsection

@php
$setting = App\Models\Utility::settings();
$LangName = $currEmailTempLang->language;

@endphp

@push('css-page')
<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush

@push('scripts')

<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<script>
    if ($(".summernote-simple").length > 0) {
        $('.summernote-simple').summernote({
            dialogsInBody: !0,
            minHeight: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 250,
        });
    }
</script>
@endpush

@section('content')
    <div class="row invoice-row">
        <div class="col-md-4  col-12">
            <div class="card mb-0 h-100">
                <div class="card-header card-body">
                    <h5></h5>
                    {{ Form::model($emailTemplate, ['route' => ['email_template.update', $emailTemplate->id], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            {{ Form::label('name', __('Name'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('name', null, ['class' => 'form-control font-style', 'disabled' => 'disabled']) }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('from', __('From'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('from', null, ['class' => 'form-control font-style', 'required' => 'required', 'placeholder' => __('Enter From Name')]) }}
                        </div>
                        {{ Form::hidden('lang', $currEmailTempLang->lang, ['class' => '']) }}
                        <div class="col-12 text-end">
                            <input type="submit" value="{{ __('Save') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-8 col-12">
            <div class="card h-100">
                <div class=" card-header card-body">
                    <h5></h5>
                    <div class="row text-xs">

                        <h6 class="font-weight-bold mb-4">{{ __('Place Holders') }}</h6>
                        @if($emailTemplate->slug=='new_ticket')
                                                <div class="row">
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('Ticket Name')}} : <span class="pull-right text-primary">{ticket_name}</span></p>
                                                    <p class="col-6">{{__('Ticket Id')}} : <span class="pull-right text-primary">{ticket_id}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('Email')}} : <span class="pull-right text-primary">{email}</span></p>
                                                    <p class="col-6">{{__('Password')}} : <span class="pull-right text-primary">{password}</span></p>
                                                </div>
                                            @elseif($emailTemplate->slug=='new_ticket_reply')
                                                <div class="row">
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('Company Name')}} : <span class="pull-right text-primary">{company_name}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('Ticket Name')}} : <span class="pull-right text-primary">{ticket_name}</span></p>
                                                    <p class="col-6">{{__('Ticket Id')}} : <span class="pull-right text-primary">{ticket_id}</span></p>
                                                    <p class="col-6">{{__('Ticket Description')}} : <span class="pull-right text-primary">{ticket_description}</span></p>

                                                </div>
                                            @elseif($emailTemplate->slug=='new_user')
                                                <div class="row">
                                                    <p class="col-6">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                                    <p class="col-6">{{__('Company Name')}} : <span class="pull-right text-primary">{company_name}</span></p>
                                                    <p class="col-6">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                                    <p class="col-6">{{__('Email')}} : <span class="pull-right text-primary">{email}</span></p>
                                                    <p class="col-6">{{__('Password')}} : <span class="pull-right text-primary">{password}</span></p>
                                                </div>
                                            @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 col-12">
            <h5></h5>
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="card sticky-top language-sidebar mb-0">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $key => $lang)
                                <a class="list-group-item list-group-item-action border-0 {{ $currEmailTempLang->lang == $key ? 'active' : '' }}"
                                    href="{{ route('manage.email.language', [$emailTemplate->id, $key]) }}">
                                    {{ Str::ucfirst($lang) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-md-9 col-sm-9">
                    <div class="card h-100 p-3">
                        {{ Form::model($currEmailTempLang, ['route' => ['store.email.language', $currEmailTempLang->parent_id], 'method' => 'POST']) }}
                    <div class="form-group col-12">
                        {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label text-dark']) }}
                        {{ Form::text('subject', null, ['class' => 'form-control font-style', 'required' => 'required']) }}
                    </div>
                    <div class="form-group col-12">
                        {{ Form::label('content', __('Email Message'), ['class' => 'col-form-label text-dark']) }}
                        {{ Form::textarea('content', $currEmailTempLang->content, ['class' => 'summernote-simple', 'id' => 'content', 'required' => 'required']) }}
                    </div>

                    <div class="col-md-12 text-end mb-3">
                        {{ Form::hidden('lang', null) }}
                        <input type="submit" value="{{ __('Save') }}"
                            class="btn btn-print-invoice  btn-primary m-r-10">
                    </div>
                    {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
