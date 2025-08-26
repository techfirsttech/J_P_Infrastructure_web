<!--Start Model Card Open-->
<div class="modal-header">
    <h5 class="mb-0" id="detailModalTitle">{{ __('supplier::message.supplier_details') }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.code') }}</span>
                </div>
                @if($query->supplier_code != "")
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->supplier_code }}</h6>
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.supplier_name') }}</span>
                </div>
                @if($query->supplier_name != "")
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->supplier_name }}</h6>
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.mobile') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->mobile }}</h6>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.contact_number') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->contact_number }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.contact_person_name') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->contact_person_name }}</h6>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.contact_person_number') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->contact_person_number }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.email') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->email }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.gst_number') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->gst_number }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.country_city_state') }}</span>
                </div>
                @if($query->country_id != "" || $query->state_id != "" || $query->city_id != "")
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ !is_null($query->country) ? $query->country->name : '' }} / {{ !is_null($query->state) ? $query->state->name : '' }} / {{ !is_null($query->city) ? $query->city->name : '' }}</h6>
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('supplier::message.gst') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->gst_apply == 1 ? __('supplier::message.included')  : __('supplier::message.excluded') }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <span class="fw-bold">{{ __('supplier::message.address') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->address_line_1 }},{{ $query->address_line_2 }},{{ $query->address_line_3 }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <span class="fw-bold">{{ __('supplier::message.term_condition') }} :</span>
                </div>
                <div class="col-12 col-lg-12">
                    <h6 class="m-25">{!! $query->term_condition !!}</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Model Card Open-->
