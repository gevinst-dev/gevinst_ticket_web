@php
    $setting = App\Models\Utility::settings();
@endphp
<div class="row">
    <div class="col-12">
        <div class="row">
            @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                <div class="float-end" style="margin-top: 18px;">
                    <a class="btn btn-primary btn-sm float-end ms-2" href="#" data-size="lg"
                        data-ajax-popup-over="true" data-url="{{ route('generate', ['knowledge']) }}"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}"
                        data-title="{{ __('Generate Content with AI') }}"><i class="fas fa-robot">
                            {{ __('Generate with AI') }}</i></a>
                </div>
            @endif
        </div>
        <form method="post" class="needs-validation" class="needs-validation" novalidate
            action="{{ route('admin.knowledge.store') }}">
            @csrf
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Title') }}</label><x-required></x-required>
                    <div class="col-sm-12 col-md-12">
                        <input type="text" placeholder="{{ __('Title of the Knowledge') }}" name="title"
                            class="form-control" required autofocus>

                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Category') }}</label>
                    <div class="col-sm-12 col-md-12">
                        <select class="form-select" name="category">
                            @foreach ($category as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="require form-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control summernote-simple"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="form-label"></label>
                    <div class="col-sm-12 col-md-12 text-end">
                        <button class="btn btn-primary btn-block btn-submit"><span>{{ __('Add') }}</span></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
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
