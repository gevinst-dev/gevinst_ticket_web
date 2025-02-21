@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Users') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Users') }}</li>
@endsection

@php
    $logos = \App\Models\Utility::get_file('public/');
@endphp

@section('multiple-action-button')
    @can('create-users')
        <a href="{{ route('admin.users.create') }}" class="me-2">
            <div class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Create User') }}">
                <i class="ti ti-plus text-white"></i>
            </div>
        </a>
    @endcan
    @if (\Auth::user()->parent == 0)
        <a href="{{ route('userlog') }}" class="btn btn-sm btn-primary btn-icon" title="{{ __('User Login History') }}"
            data-bs-toggle="tooltip" data-bs-placement="top">
            <i class="ti ti-user-check"></i>
        </a>
    @endif
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                                <tr>
                                    {{-- <span class="hide-mob ms-2"> --}}
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('Picture') }}</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Email') }}</th>
                                    <th scope="col">{{ __('Category') }}</th>
                                    <th scope="col">{{ __('Sub Category') }}</th>
                                    <th scope="col">{{ __('Role') }}</th>
                                    <th scope="col" class="text-end me-3">{{ __('Action') }}</th>
                                    {{-- </span> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <th scope="row">{{ ++$index }}</th>
                                        <td>
                                            <a href="{{ !empty($user->avatar) ? $logos . $user->avatar : $logos . 'avatar.png' }}"
                                                target="_blank">
                                                <img src="{{ !empty($user->avatar) ? $logos . $user->avatar : $logos . 'avatar.png' }}"
                                                    class="rounded border-2 border border-primary" width="35"
                                                    id="blah3" style="border-color: #0CAF60 !important; ">
                                            </a>
                                        </td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>

                                        <td>
                                            @foreach ($user->Category() as $category)
                                                <span class="badge badge-white p-2 px-3 fix_badge"
                                                    style="background: {{ $category->color }}">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </td>

                                        <td>
                                            @foreach ($user->Subcategories() as $subcategory)
                                                <span class="badge badge-white p-2 px-3 fix_badge"
                                                style="background: {{ $subcategory->color }}">
                                                    {{ $subcategory->subcategory }}
                                                </span>
                                            @endforeach
                                        </td>


                                        <td>
                                            <span class="badge bg-primary p-2 px-3 ">
                                                {{ !empty($user->parent) && $user->parent == 1 ? 'Agent' : 'Admin' }}
                                            </span>
                                        </td>
                                        <td class="text-end me-3">
                                            @if ($user->is_enable_login == 1)
                                                <div class="action-btn me-2">
                                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center bg-danger"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Login Disable') }}"> <span
                                                            class="text-white"><i class="ti ti-road-sign"></i></a>
                                                </div>
                                            @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                <div class="action-btn me-2">
                                                    <a href="#"
                                                        data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"
                                                        data-ajax-popup="true" data-size="md"
                                                        class="mx-3 bg-secondary btn btn-sm d-inline-flex align-items-center login_enable"
                                                        data-title="{{ __('New Password') }}" data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('New Password') }}"> <span
                                                            class="text-white"><i class="ti ti-road-sign"></i></a>
                                                </div>
                                            @else
                                                <div class="action-btn me-2">
                                                    <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                        class="mx-3 bg-success btn btn-sm d-inline-flex align-items-center login_enable"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Login Enable') }}"> <span
                                                            class="text-white"> <i class="ti ti-road-sign"></i>
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 bg-warning btn btn-sm d-inline-flex align-items-center"
                                                    data-size="md"
                                                    data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"
                                                    data-ajax-popup="true" data-title="{{ __('Reset Password') }}"
                                                    data-toggle="tooltip" title="{{ __('Reset Password') }}">
                                                    <span class="text-white"> <i class="ti ti-key"></i> </span>
                                                </a>
                                            </div>
                                            @can('edit-users')
                                                <div class="action-btn me-2">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                                        class="mx-3 bg-info btn btn-sm d-inline-flex align-items-center"
                                                        data-toggle="tooltip" title="{{ __('Edit') }}"> <span
                                                            class="text-white"> <i class="ti ti-pencil"></i></span></a>
                                                </div>
                                            @endcan
                                            @can('delete-users')
                                                <div class="action-btn">
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                                        id="delete-form-{{ $user->id }}">
                                                        @csrf
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="submit"
                                                            class="mx-3 btn btn-sm bg-danger d-inline-flex align-items-center show_confirm"
                                                            data-toggle="tooltip" title="{{ __('Delete') }}">
                                                            <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endcan

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
