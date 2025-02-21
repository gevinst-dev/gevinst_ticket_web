<form action="{{ route('admin.subcategory.update', $subcategory->id) }}" class="needs-validation" novalidate method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-md-6">
            <label for="category_id">{{ __('Category') }}</label><x-required></x-required>
            <div class="col-sm-12 col-md-12">
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="" disabled>{{ __('Select Category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $subcategory->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="subcategory">{{ __('Sub Category') }}</label><x-required></x-required>
            <div class="col-sm-12 col-md-12">
                <input type="text" name="subcategory" id="subcategory" class="form-control"
                    value="{{ $subcategory->subcategory }}" required>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="exampleColorInput" class="form-label">{{ __('Color') }}</label>
            <div class="col-sm-12 col-md-12">
                <input name="color" type="color"
                    class="form-control form-control-color {{ $errors->has('color') ? ' is-invalid' : '' }}"
                    value="{{ $subcategory->color }}">
                <div class="invalid-feedback">
                    {{ $errors->first('color') }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <label class="form-label"></label>
            <div class="col-sm-12 col-md-12 text-end">
                <button class="btn btn-primary btn-block btn-submit"><span>{{ __('Update') }}</span></button>
            </div>
        </div>
    </div>
</form>
