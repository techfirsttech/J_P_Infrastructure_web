<div class="modal-header bg-transparent">
    <h5 class="text-center mb-0" id="exampleModalTitle" data-title="{{ __('category::message.add') }}">{{ __('category::message.add') }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" id="category_body">
    <form class="category" action="javascript:void(0);" method="POST">
        @csrf
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_parent" id="is_parent">
                    <label class="form-check-label" for="is_parent">{{ __('category::message.is_parent') }}</label>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group is-parent">
                <label for="parent_id" class="form-label">{{ __('category::message.select_parent') }} <span class="text-danger">*</span></label>
                <select id="parent_id" name="parent_id" class="select2 form-select" data-error="{{ __('category::message.select_parent_category') }}">
                    <option value="" selected>{{ __('message.common.select') }}</option>
                    @if($category->count() > 0)
                    @foreach ($category as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group custom-input-group">
                <label class="form-label" for="category_name">{{ __('category::message.name') }} <span class="text-danger">*</span></label>
                <input type="hidden" name="id" id="id" value="">
                <input type="text" class="form-control" name="category_name" id="category_name" placeholder="{{ __('category::message.name') }}" data-error="{{ __('category::message.enter_name') }}">
                <span class="invalid-feedback d-block" id="error_category_name" role="alert"></span>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-1">
                <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-label-secondary float-start">{{ __('message.common.cancel') }}</button>
                <button type="submit" class="btn btn-sm btn-primary float-end save" data-route="{{route($type.'category.store')}}" data-text="{{ __('message.common.submit') }}">{{ __('message.common.submit') }}</button>
            </div>
        </div>
    </form>
</div>