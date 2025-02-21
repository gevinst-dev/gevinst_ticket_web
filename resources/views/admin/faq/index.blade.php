@extends('layouts.admin')

@section('page-title')
    {{ __('Manage FAQ') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('FAQ') }}</li>
@endsection


@section('multiple-action-button')
    @can('create-faq')
        <a href="#" class="btn btn-sm btn-primary btn-icon" title="{{ __('Create FAQ') }}" data-bs-toggle="tooltip"
            data-bs-placement="top" data-ajax-popup="true" data-title="{{ __('Create FAQ') }}"
            data-url="{{ route('admin.faq.create') }}" data-size="lg"><i class="ti ti-plus"></i></a>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card faq-page-tabel">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th class="w-25">{{ __('Title') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-end me-3">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($faqs as $index => $faq)
                                    <tr>
                                        <th scope="row">{{ ++$index }}</th>
                                        <td><span class="font-weight-bold white-space">{{ $faq->title }}</span></td>
                                        <td class="faq_desc"><p>{!! $faq->description !!}</p></td>
                                        <td class="text-end">
                                            @can('show-faq')
                                                    <div class="action-btn me-2">
                                                        <a href="#" class="btn btn-sm btn-icon bg-warning text-white"
                                                            title="{{ __('Show FAQ') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" data-ajax-popup="true"
                                                            data-title="{{ __('Show FAQ') }}"
                                                            data-url="{{ route('admin.show.faq', $faq->id) }}" data-size="lg"><i class="ti ti-eye"></i></a>
                                                    </div>
                                            @endcan
                                            @can('edit-faq')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="btn btn-sm btn-icon bg-info text-white"
                                                        title="{{ __('Edit FAQ') }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" data-ajax-popup="true"
                                                        data-title="{{ __('Edit FAQ') }}"
                                                        data-url="{{ route('admin.faq.edit', $faq->id) }}" data-size="lg"><i class="ti ti-pencil"></i></a>
                                                </div>
                                            @endcan
                                            @can('delete-faq')
                                                <div class="action-btn ">
                                                    <form method="POST" action="{{ route('admin.faq.destroy', $faq->id) }}"
                                                        id="user-form-{{ $faq->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="submit"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center bg-danger show_confirm"
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
