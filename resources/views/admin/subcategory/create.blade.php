@php
    $setting = App\Models\Utility::settings();
    // Assuming you have a list of categories to populate the dropdown
    $categories = \App\Models\Category::all();
@endphp

<form method="post" class="needs-validation" novalidate action="{{ route('admin.subcategory.store') }}">
    @csrf
    <div class="row">
        @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
            <div class="float-end" style="margin-bottom: 15px">
                <a class="btn btn-primary btn-sm" href="#" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate', ['category']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content with AI') }}">
                    <i class="fas fa-robot"> {{ __('Generate with AI') }}</i>
                </a>
            </div>
        @endif

        <!-- Category Dropdown -->
        <div class="form-group col-md-6">
            <label class="form-label">{{ __('Category') }}</label><x-required></x-required>
            <div class="col-sm-12 col-md-12">
                <select name="category_id" class="form-control {{ $errors->has('category_id') ? ' is-invalid' : '' }}" required>
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    {{ $errors->first('category_id') }}
                </div>
            </div>
        </div>

        <!-- Subcategory Name -->
        <div class="form-group col-md-6">
            <label class="form-label">{{ __('Subcategory') }}</label><x-required></x-required>
            <div class="col-sm-12 col-md-12">
                <input type="text" placeholder="{{ __('Name of the Subcategory') }}" name="subcategory"
                    class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required>
                <div class="invalid-feedback">
                    {{ $errors->first('name') }}
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">

            <label for="exampleColorInput" class="form-label">{{ __('Color') }}</label>
            <div class="col-sm-12 col-md-12">
                <input name="color" type="color"
                    class=" form-control  form-control-color {{ $errors->has('color') ? ' is-invalid' : '' }}"
                    value="255ff7" id="exampleColorInput">
                <div class="invalid-feedback">
                    {{ $errors->first('color') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="form-group col-md-12 text-end">
            <button class="btn btn-primary btn-block btn-submit"><span>{{ __('Add') }}</span></button>
        </div>
    </div>
</form>
