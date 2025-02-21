@extends('layouts.admin')

@section('page-title')
    {{ __('Edit Profile') }} ({{ $user->name }})
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">{{ __('Users') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@php
    $logos = \App\Models\Utility::get_file('public/');
@endphp


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ route('update.profile', $user->id) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Name') }}</label>
                                <div class="col-sm-12 col-md-12">
                                    <input type="text" placeholder="{{ __('Full name of the user') }}" name="name"
                                        class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        value="{{ $user->name }}" autofocus>
                                    <div class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <div class="col-sm-12 col-md-12">
                                    <input type="email" placeholder="{{ __('Email address (should be unique)') }}"
                                        name="email"
                                        class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ $user->email }}">
                                    <div class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Password') }}</label>
                                <div class="col-sm-12 col-md-12">
                                    <input type="password" name="password" autocomplete="new-password"
                                        placeholder="{{ __('Set an account password') }}"
                                        class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}">
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password') }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label">{{ __('Confirm Password') }}</label>
                                <div class="col-sm-12 col-md-12">
                                    <input type="password" name="password_confirmation"
                                        placeholder="{{ __('Confirm account password') }}" autocomplete="new-password"
                                        class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}">
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password_confirmation') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label">{{ __('Picture') }}</label>
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-group col-lg-12 col-md-12">
                                        <div class="choose-file form-group">
                                            <label for="file" class="form-label">
                                                <div>{{ __('Choose File Here') }}</div>

                                                <input type="file" name="avatar" id="file"
                                                    class="form-control {{ $errors->has('avatar') ? ' is-invalid' : '' }}"
                                                    onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('avatar') }}
                                                </div>
                                            </label>
                                            <p class="avatar_selection"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label"></label>
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-group col-lg-12 col-md-12">
                                        <div class="user-main-image">
                                            @php
                                                $logos = \App\Models\Utility::get_file('public/');
                                            @endphp

                                            <a href="{{ !empty($user->avatar) ? $logos . $user->avatar : $logos . '/avatar.png' }}"
                                                target="_blank">
                                                <img src="{{ !empty($user->avatar) ? $logos . $user->avatar : $logos . '/avatar.png' }}"
                                                    class="img-fluid rounded-circle card-avatar" width="35"
                                                    id="blah3">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label"></label>
                                <div class="col-sm-12 col-md-12 text-end">
                                    <button
                                        class="btn btn-primary btn-block btn-submit"><span>{{ __('Update') }}</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card" id="authentication-sidenav">
                <div class="card-header">
                    <h4 class="mb-0">{{ __('Two Factor Authentication') }}</h4>
                </div>
                <div class="card-body">
                    <p>{{ __('Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.') }}
                    </p>
                    @if ($data['user']->google2fa_secret == null)
                        <form class="form-horizontal" method="POST" action="{{ route('generate2faSecret') }}">
                            {{ csrf_field() }}
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __(' Generate Secret Key to Enable 2FA') }}
                                </button>
                            </div>
                        </form>
                    @elseif($data['user']->google2fa_enable == 0 && $data['user']->google2fa_secret != null)
                        1. {{ __('Install “Google Authentication App” on your') }} <a
                            href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_black">
                            {{ __('IOS') }}</a> {{ __('or') }} <a
                            href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                            target="_black">{{ __('Android phone.') }}</a><br />
                        2. {{ __('Open the Google Authentication App and scan the below QR code.') }}<br />
                        @php
                            $f = finfo_open();
                            $mime_type = finfo_buffer($f, $data['google2fa_url'], FILEINFO_MIME_TYPE);
                        @endphp
                        @if ($mime_type == 'text/plain')
                            <img src="{{ $data['google2fa_url'] }}" alt="">
                        @else
                            {!! $data['google2fa_url'] !!}
                        @endif
                        <br /><br />
                        {{ __('Alternatively, you can use the code:') }} <code>{{ $data['secret'] }}</code>.<br />
                        3. {{ __('Enter the 6-digit Google Authentication code from the app') }}<br /><br />
                        <form class="form-horizontal needs-validation" novalidate method="POST" action="{{ route('enable2fa') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}">
                                <label for="secret" class="col-form-label">{{ __('Authenticator Code') }}</label>
                                <input id="secret" type="password" class="form-control" name="secret"
                                    required="required">
                            </div>
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Enable 2FA') }}
                                </button>
                            </div>
                        </form> 
                    @elseif($data['user']->google2fa_enable == 1 && $data['user']->google2fa_secret != null)
                        <div class="alert alert-success">
                            {{ __('2FA is currently') }} <strong>{{ __('Enabled') }}</strong>
                            {{ __('on your account.') }}
                        </div>
                        <p>{{ __('If you are looking to disable Two Factor Authentication. Please confirm your password and Click Disable 2FA Button.') }}
                        </p>

                        <form class="form-horizontal needs-validation" novalidate method="POST"
                            action="{{ route('disable2fa') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                                <label for="change-password" class="col-form-label">{{ __('Current Password') }}</label>
                                <input id="current-password" type="password" class="form-control"
                                    name="current-password" required="required">
                                @if ($errors->has('current-password'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('current-password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Disable 2FA') }}
                                </button>
                            </div>
                        </form>

                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


