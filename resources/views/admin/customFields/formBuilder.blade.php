<link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@if ($customFields)
    @foreach ($customFields as $customField)
        @if ($customField->id == '1')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    <label for="name" class="form-label">{{ __($customField->name) }}</label>
                    <div class="form-icon-user">
                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                            id="name" name="name" placeholder="{{ __($customField->placeholder) }}"
                            required="" value="{{ old('name') }}">
                        <div class="invalid-feedback d-block">
                            {{ $errors->first('name') }}
                        </div>
                    </div>
                </div>
            </div>
        @elseif($customField->id == '2')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    <label for="email" class="form-label">{{ __($customField->name) }}</label>
                    <div class="form-icon-user">
                        <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                            id="email" name="email" placeholder="{{ __($customField->placeholder) }}"
                            required="" value="{{ old('email') }}">
                        <div class="invalid-feedback d-block">
                            {{ $errors->first('email') }}
                        </div>
                    </div>
                </div>
            </div>
        @elseif($customField->id == '3')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    <label for="category" class="form-label">{{ __($customField->name) }}</label>
                    <select class="form-select" id="category" name="category" required
                        data-placeholder="{{ __($customField->placeholder) }}">
                        <option value="">{{ __($customField->placeholder) }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if (old('category') == $category->id) selected @endif>
                                {{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('category') }}
                    </div>
                </div>

            </div>
            {{-- -------------- --}}
        @elseif($customField->id == '4')
            <div class="form-group col-md-{{ $customField->width }} subcategory_id_div">

                {{ Form::label('subcategory', __('sub category'), ['class' => 'form-label']) }}
                <div class="input-group">
                    {{ Form::select('subcategory', ['' => __('Select sub category')], null, ['class' => 'form-control', 'required' => 'required', 'data-toggle' => 'select']) }}
                </div>
            </div>
            {{-- -------------- --}}
        @elseif($customField->id == '5')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    <label for="subject" class="form-label">{{ __($customField->name) }}</label>
                    <div class="form-icon-user">
                        <input type="text" class="form-control {{ $errors->has('subject') ? ' is-invalid' : '' }}"
                            id="subject" name="subject" placeholder="{{ __($customField->placeholder) }}"
                            required="" value="{{ old('subject') }}">
                        <div class="invalid-feedback d-block">
                            {{ $errors->first('subject') }}
                        </div>
                    </div>
                </div>
            </div>
        @elseif($customField->id == '6')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    <label for="description" class="form-label">{{ __('Description') }}</label>
                    <textarea name="description"
                        class="form-control summernote-simple {{ $errors->has('description') ? 'is-invalid' : '' }}"
                        placeholder="{{ __($customField->placeholder) }}" required="">{{ old('description') }}</textarea>
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                </div>
            </div>
        @elseif($customField->id == '7')
            <div class="">
                <label class="form-label form-bottom-content mb-3">{{ $customField->name }}
                    <b>({{ $customField->placeholder }})</b></label>
            </div>
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    <div class="choose-file form-group">
                        <label for="file" class="form-label">
                            <div class="mb-2">{{ __('Choose File Here') }}</div>
                            {{-- <input type="file" class="form-control {{ $errors->has('attachments.') ? 'is-invalid' : '' }}" multiple="" name="attachments[]" id="file" data-filename="multiple_file_selection"> --}}
                            <div class="file-upload">
                                <div class="file-select">
                                    <div class="file-select-button btn btn-primary btn-block" id="fileName">Choose File
                                    </div>
                                    <div class="file-select-name" id="noFile">No file chosen...</div>
                                    {{-- <input type="file" name="chooseFile" id="chooseFile"> --}}
                                    <input type="file"
                                        class="form-control {{ $errors->has('attachments.') ? 'is-invalid' : '' }}"
                                        multiple="" name="attachments[]" id="chooseFile"
                                        data-filename="multiple_file_selection">
                                </div>
                            </div>
                        </label>
                        <p class="multiple_file_selection"></p>
                    </div>
                </div>
                <div class="invalid-feedback d-block">
                    {{ $errors->first('attachments.*') }}
                </div>
            </div>
        @elseif($customField->custom_id == '8')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    <label for="priority" class="form-label">{{ __($customField->name) }}</label>
                    <select class="form-select" id="priority" name="priority" required
                        data-placeholder="{{ __($customField->placeholder) }}">
                        <option value="">{{ __($customField->placeholder) }}</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->id }}" @if (old('priority') == $priority->id) selected @endif>
                                {{ $priority->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('priority') }}
                    </div>
                </div>
            </div>
        @elseif($customField->type == 'text')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3{{ $customField->width }}">
                    {{ Form::label('customField-' . $customField->id, __($customField->name), ['class' => 'form-label']) }}
                    @if ($customField->is_required == 1)
                        {{ Form::text('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder), 'required']) }}
                    @else
                        {{ Form::text('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder)]) }}
                    @endif
                </div>
            </div>
        @elseif($customField->type == 'email')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    {{ Form::label('customField-' . $customField->id, __($customField->name), ['class' => 'form-label']) }}
                    @if ($customField->is_required == 1)
                        {{ Form::email('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder), 'required']) }}
                    @else
                        {{ Form::email('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder)]) }}
                    @endif
                </div>
            </div>
        @elseif($customField->type == 'number')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    {{ Form::label('customField-' . $customField->id, __($customField->name), ['class' => 'form-label']) }}
                    @if ($customField->is_required == 1)
                        {{ Form::number('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder), 'required']) }}
                    @else
                        {{ Form::number('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder)]) }}
                    @endif
                </div>
            </div>
        @elseif($customField->type == 'date')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    {{ Form::label('customField-' . $customField->id, __($customField->name), ['class' => 'form-label']) }}
                    @if ($customField->is_required == 1)
                        {{ Form::date('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder), 'required']) }}
                    @else
                        {{ Form::date('customField[' . $customField->id . ']', null, ['class' => 'form-control', 'placeholder' => __($customField->placeholder)]) }}
                    @endif
                </div>
            </div>
        @elseif($customField->type == 'textarea')
            <div class="col-lg-{{ $customField->width }}">
                <div class="form-group mb-3 {{ $customField->width }}">
                    {{ Form::label('customField-' . $customField->id, __($customField->name), ['class' => 'form-label']) }}
                    @if ($customField->is_required == 1)
                        {{ Form::textarea('customField[' . $customField->id . ']', null, ['class' => 'form-control summernote-simple', 'placeholder' => __($customField->placeholder), 'required']) }}
                    @else
                        {{ Form::textarea('customField[' . $customField->id . ']', null, ['class' => 'form-control summernote-simple', 'placeholder' => __($customField->placeholder)]) }}
                    @endif
                </div>
            </div>
        @endif
    @endforeach
@endif
            
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>

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

            url: '{{ route('get.ticket.sub') }}',
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

    $('#chooseFile').bind('change', function() {
        var filename = $("#chooseFile").val();
        if (/^\s*$/.test(filename)) {
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen...");
        } else {
            $(".file-upload").addClass('active');
            $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
        }
    });
</script>
