@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Sub Category') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Sub Category') }}</li>
@endsection
@php
    $logos = \App\Models\Utility::get_file('public/');
@endphp
@section('multiple-action-button')
    @can('create-category')
        <div class="float-end">
            <div class="col-auto">
                <a href="#"  class="btn btn-sm btn-primary btn-icon" title="{{__('Create')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-ajax-popup="true" data-title="{{__('Create Sub Category')}}"
                data-url="{{ route('admin.subcategory.create') }}"
                data-size="md"><i class="ti ti-plus"></i></a>
            </div>
        </div>

    @endcan

@endsection

@section('content')
<div class="row">
    <div class="col-3">
        @include('layouts.setup')
    </div>
    <div class="col-9">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pc-dt-simple" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('Category') }}</th>
                            <th scope="col">{{ __('Sub Category') }}</th>
                            <th scope="col">{{ __('Color') }}</th>
                            <th scope="col" class="text-end me-3">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($subcategories as $index => $subcategory)
                            <tr>
                                <th scope="row">{{++$index}}</th>
                                <td>{{ $subcategory->category->name }}</td>
                                <td>{{$subcategory->subcategory}}</td>
                                <td><span class="badge" style="background: {{$subcategory->color}}">&nbsp;&nbsp;&nbsp;</span></td>

                                    </td>

                                    <td class="text-end">


                                        <div class="action-btn me-2">

                                            <a href="#"  class="mx-3 bg-info btn btn-sm d-inline-flex align-items-center" title="{{__('Create')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-ajax-popup="true" data-title="{{__('Edit Sub Category')}}"
                                            data-url="{{ route('admin.subcategory.edit', $subcategory->id) }}"
                                            data-size="md"><span class="text-white"><i class="ti ti-pencil"></i>
                                                </span>
                                            </a>
                                        </div>
                                            <div class="action-btn">
                                                <form method="POST" action="{{route('admin.subcategory.destroy',$subcategory->id) }}" id="user-form-{{$subcategory->id}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input name="_method" type="hidden" value="DELETE">
                                                    <button type="submit" class="mx-3 btn btn-sm d-inline-flex align-items-center bg-danger show_confirm" data-toggle="tooltip"
                                                    title="{{ __('Delete') }}">
                                                        <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                    </button>
                                                </form>
                                            </div>
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
