@extends('layouts.admin')

@section('page-title')
    {{ __('Create Ticket') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">{{ __('Tickets') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection

@push('css-page')
<link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{asset('css/summernote/summernote-bs4.js')}}"></script>

{{-- <script src="{{ asset('js/jquery.min.js') }}"></script> --}}
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
<script type="text/javascript">
    $(document).on('change', '#category', function(e) {
        $.ajax({
            url: '{{ route('get.ticket.subcategory') }}',
            dataType: 'json',
            data: {
                'category_id': $(this).val()
            },
            success: function(data) {
                $('#subcategory').find('option').not(':first').remove();
                $.each(data, function(key, value) {

                    $('#subcategory')
                        .append($("<option></option>")
                            .attr("value", value.id)
                            .text(value.subcategory));
                });
            },
            error: function(data) {
                data = data.responseJSON;
                show_toastr('{{ __('Error') }}', data.message, 'error');
            }
        });
    });
</script>
@endpush

@php

$setting = App\Models\Utility::settings();

@endphp
@section('content')
    <form action="{{route('admin.tickets.store')}}" class="needs-validation mt-3" novalidate  method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header flex-column flex-lg-row  d-flex align-items-lg-center gap-2 justify-content-between">
                        <h6>{{ __('Ticket Information') }}</h6>
                    @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                        <a class="btn btn-primary btn-sm float-end ms-2" href="#" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['support']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content with AI') }}"><i class="fas fa-robot"> {{ __('Generate with AI') }}</i></a>
                    @endif
                  </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="require form-label">{{ __('Name') }}</label><x-required></x-required>
                                <input class="form-control {{(!empty($errors->first('name')) ? 'is-invalid' : '')}}" type="text" name="name" required="" placeholder="{{ __('Name') }}">

                            </div>
                            <div class="form-group col-md-6">
                                <label class="require form-label">{{ __('Email') }}</label><x-required></x-required>
                                <input class="form-control {{(!empty($errors->first('email')) ? 'is-invalid' : '')}}" type="email" name="email" required="" placeholder="{{ __('Email') }}">

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="require form-label">{{ __('Category') }}</label><x-required></x-required>
                                <select id="category" class="form-control {{(!empty($errors->first('category')) ? 'is-invalid' : '')}}" name="category" required>
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    {{ $errors->first('category') }}
                                </div>
                            </div>

                            <div class="form-group col-md-6 subcategory_id_div">
                                {{ Form::label('subcategory', __('Sub Category'), ['class' => 'form-label']) }}<x-required></x-required><br>
                                <div class="input-group">
                                    {{ Form::select('subcategory', ['' => __('Select sub category')], null, ['class' => 'form-control','required'=>'required', 'data-toggle' => 'select']) }}
                                </div>
                            </div>


                            <div class="form-group col-md-6">
                                <label class="require form-label">{{ __('Status') }}</label><x-required></x-required>
                                <select class="form-control {{(!empty($errors->first('status')) ? 'is-invalid' : '')}}" name="status" required="">
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="New Ticket">{{ __('New Ticket') }}</option>
                                    <option value="In Progress">{{ __('In Progress') }}</option>
                                    <option value="On Hold">{{ __('On Hold') }}</option>
                                    <option value="Closed">{{ __('Closed') }}</option>
                                    <option value="Resolved">{{ __('Resolved') }}</option>
                                </select>
                                <div class="invalid-feedback">
                                    {{ $errors->first('status') }}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="require form-label">{{ __('Subject') }}</label><x-required></x-required>
                                <input class="form-control {{(!empty($errors->first('subject')) ? 'is-invalid' : '')}}" type="text" name="subject" required="" placeholder="{{ __('Subject') }}">
                                <div class="invalid-feedback">
                                    {{ $errors->first('subject') }}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="require form-label">{{ __('Attachments') }} <small>({{__('You can select multiple files')}})</small> </label>
                                <div class="choose-file form-group">
                                    <label for="file" class="form-label d-block">
                                        <input type="file" name="attachments[]" id="file" class="form-control mb-2 {{ $errors->has('attachments') ? ' is-invalid' : '' }}" multiple=""  data-filename="multiple_file_selection" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                        <img src="" id="blah" width="20%"/>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('attachments.*') }}
                                        </div>
                                    </label>
                                </div>
                                <p class="multiple_file_selection mx-4"></p>
                            </div>
                            
                            <div class="form-group col-md-6">

                                <label class="require form-label">{{ __('Priority') }}</label><x-required></x-required>
                                <select class="form-control {{(!empty($errors->first('priority')) ? 'is-invalid' : '')}}" name="priority" required="">
                                    <option value="">{{ __('Select Priority') }}</option>

                                    @foreach($priorities as $priority)

                                        <option value="{{$priority->id}}">{{$priority->name}}</option>
                                    @endforeach

                                </select>
                                <div class="invalid-feedback">
                                    {{ $errors->first('priority') }}
                                </div>

                            </div>

                            <div class="form-group col-md-12">
                                <label class="require form-label">{{ __('Description') }}</label>
                                <textarea name="description" id="description" class="form-control summernote-simple {{(!empty($errors->first('description')) ? 'is-invalid' : '')}}"></textarea>
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            </div>
                            @if(!$customFields->isEmpty())
                                @include('admin.customFields.formBuilder')
                            @endif
                        </div>
                        <div class="d-flex justify-content-end text-end">
                            <a class="btn btn-secondary btn-light custom-cancel-btn btn-submit" href="{{route('admin.tickets.index')}}">{{ __('Cancel') }}</a>
                            <button class="btn btn-primary btn-submit ms-2" type="submit">{{ __('Submit') }}</button>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </form>
@endsection

