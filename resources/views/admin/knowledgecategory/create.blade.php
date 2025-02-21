@php
    $setting = App\Models\Utility::settings();
@endphp


<div class="row">
    <div class="col-12">
        <div class="row">
            @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                <div class="float-end" style="margin-top: 18px;">
                    <a class="btn btn-primary btn-sm float-end ms-2" href="#" data-size="lg"
                        data-ajax-popup-over="true" data-url="{{ route('generate', ['knowledge_category']) }}"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}"
                        data-title="{{ __('Generate Content with AI') }}"><i class="fas fa-robot">
                            {{ __('Generate with AI') }}</i></a>
                </div>
            @endif
        </div>
        <form method="POST" class="needs-validation" novalidate
            action="{{ route('admin.knowledgecategory.store') }}">
            @csrf
            <div class="row">
                <div class="form-group col-12">
                    <label class="form-label">{{ __('Title') }}</label><x-required></x-required>
                    <div class="col-sm-12 col-md-12">
                        <input type="text" placeholder="{{ __('Title of the Knowledge') }}" name="title" class="form-control" required autofocus>
                    </div>
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
