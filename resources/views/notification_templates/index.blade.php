@extends('layouts.admin')
@section('page-title')
    {{ __('Notification Template') }}
@endsection
@section('title')
    <div class="d-inline-block">
        @if (\Auth::user()->parent == 0)
            <h5 class="h4 d-inline-block font-weight-400 mb-0">{{ __('Notification Templates') }}</h5>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Notification Template') }}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="pt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name"> {{ __('Name') }}</th>
                                    @if (\Auth::user()->parent == 0)
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notification_templates as $notification_template)
                                    <tr>
                                        <td>{{ $notification_template->name }}</td>
                                        <td>
                                            @if (\Auth::user()->parent == 0)
                                                <div class="text-end">
                                                    <div class="dt-buttons">
                                                        <span>
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('manage.notification.language', [$notification_template->id, \Auth::user()->lang]) }}"
                                                                    class="mx-3 bg-warning btn btn-sm d-inline-flex align-items-center"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('View') }}"
                                                                    title="">
                                                                    <span class="text-white"><i
                                                                            class="ti ti-eye"></i></span>
                                                                </a>
                                                            </div>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
