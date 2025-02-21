@php
$setting = App\Models\Utility::settings();
@endphp
<form method="post" class="needs-validation" novalidate action="{{ route('admin.category.update', $category->id) }}">
    @csrf
    @method('PUT')
    <div class="row">
        @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
        <div class="float-end" style="margin-bottom: 15px">
            <a class="btn btn-primary btn-sm" href="#" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['category']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content with AI') }}"><i class="fas fa-robot"> {{ __('Generate with AI') }}</i></a>
        </div>
        @endif
        <div class="form-group col-md-6">
            <label class="form-label">{{ __('Name') }}</label><x-required></x-required>
            <div class="col-sm-12 col-md-12">
                <input type="text" placeholder="{{ __('Name of the Category') }}" name="name"
                    class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" required value="{{ $category->name }}"
                    autofocus>

            </div>
        </div>

        <div class="form-group col-md-6">
            <label for="exampleColorInput" class="form-label">{{ __('Color') }}</label>
            <div class="col-sm-12 col-md-12">
                <input name="color" type="color"
                    class="form-control form-control-color {{ $errors->has('color') ? ' is-invalid' : '' }}"
                    value="{{ $category->color }}">
                <div class="invalid-feedback">
                    {{ $errors->first('color') }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 form-group">
        {{ Form::label('users', __('User'), ['class' => 'col-form-label']) }}
        {{ Form::select('users[]', $users, $catgoryuser, ['class' => 'form-control multi-select ', 'id' => 'choices-multiple', 'multiple' => true]) }}
    </div>
{{-- 
    <div class="col-12 form-group">
        {{ Form::label('categories', __('Category'), ['class' => 'col-form-label']) }}<x-required></x-required>
        {{ Form::select('categories[]', $categories, $category_list, ['class' => 'form-control multi-select', 'id' => 'choices-multiple', 'multiple' => true]) }}
    </div> --}}


    <div class="row">
        <div class="form-group col-md-12">
            <label class="form-label"></label>
            <div class="col-sm-12 col-md-12 text-end">
                <button class="btn btn-primary btn-block btn-submit"><span>{{ __('Update') }}</span></button>
            </div>
        </div>
    </div>
</form>

<script src="{{ asset('public/libs/select2/dist/js/select2.min.js')}}"></script>

<script>
    if ($(".multi-select").length > 0) {
    $( $(".multi-select") ).each(function( index,element ) {
        var id = $(element).attr('id');
           var multipleCancelButton = new Choices(
                '#'+id, {
                    removeItemButton: true,
                }
            );
    });

}


if ($(".select2").length) {
        $('.select2').select2({
            "language": {
                "noResults": function () {
                    return "No result found";
                }
            },
        });
    }

</script>
