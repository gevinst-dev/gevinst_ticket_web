@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Knowledge') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Knowledge') }}</li>
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush


@section('multiple-action-button')
    @can('create-knowledge')
        <a href="#" class="btn btn-sm btn-primary btn-icon" title="{{ __('Create Knowledge') }}" data-bs-toggle="tooltip"
            data-bs-placement="top" data-ajax-popup="true" data-title="{{ __('Create Knowledge') }}"
            data-url="{{ route('admin.knowledge.create') }}" data-size="lg"><i class="ti ti-plus"></i></a>
    @endcan

    @can('manage-knowledgecategory')
        <div class="btn btn-sm btn-primary btn-icon float-end ms-2" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Create Knowledge Category') }}">
            <a href="{{ route('admin.knowledgecategory') }}" class=""><i class="ti ti-vector-bezier text-white"></i></a>
        </div>
    @endcan
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive knowledge-table-wrapper">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th class="w-25">{{ __('Title') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th class="text-end me-3">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($knowledges as $index => $knowledge)
                                    <tr>
                                        <th scope="row">{{ ++$index }}</th>
                                        <td><span class="font-weight-bold white-space">{{ $knowledge->title }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-normal">
                                                {{ !empty($knowledge->getCategoryInfo) ? $knowledge->getCategoryInfo->title : '-' }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            @can('show-knowledgecategory')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="btn btn-sm bg-warning btn-icon text-white"
                                                        title="{{ __('Show Knowledge') }}" data-bs-toggle="tooltip"
                                                        data-url="{{ route('admin.show.knowledgebase', $knowledge->id) }}"
                                                        data-bs-placement="top" data-ajax-popup="true"
                                                        data-title="{{ __('Show Knowledge') }}" data-size="lg"> <i
                                                            class="ti ti-eye"></i></a>
                                                </div>
                                            @endcan
                                            @can('edit-knowledge')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="btn btn-sm bg-info btn-icon text-white"
                                                        title="{{ __('Edit Knowledge') }}" data-bs-toggle="tooltip"
                                                        data-url="{{ route('admin.knowledge.edit', $knowledge->id) }}"
                                                        data-bs-placement="top" data-ajax-popup="true"
                                                        data-title="{{ __('Edit Knowledge') }}" data-size="lg"> <i
                                                            class="ti ti-pencil"></i></a>
                                                </div>
                                            @endcan
                                            @can('delete-knowledge')
                                                <div class="action-btn">
                                                    <form method="POST"
                                                        action="{{ route('admin.knowledge.destroy', $knowledge->id) }}"
                                                        id="user-form-{{ $knowledge->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="submit"
                                                            class="mx-3 bg-danger btn btn-sm d-inline-flex align-items-center show_confirm"
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
