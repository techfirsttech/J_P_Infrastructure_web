<!--Start Model Card Open-->
<div class="modal-header">
    <h5 class="mb-0" id="detailModalTitle">{{ __('sitemaster::message.siteName') }} : {{ $query->site_name }} </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <span class="fw-bold">{{ __('sitemaster::message.supervisors') }}  </span>
                </div>
                <div class="col-12 col-lg-10">

                        {{-- @foreach ($query->supervisors as $supervisor)
                            {{ $supervisor->name }}
                        @endforeach --}}

                         : {{ $query->supervisors->pluck('name')->implode(', ') }}
                    </ul>
                </div>
            </div>
        </div>
        <hr>
        <div class="col-lg-4">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <span class="fw-bold">{{ __('sitemaster::message.state') }}</span>
                </div>
                <div class="col-12 col-lg-9">
                    <h6 class="m-25">: {{ $query->state_name }}</h6>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <span class="fw-bold">{{ __('sitemaster::message.city') }}</span>
                </div>
                <div class="col-12 col-lg-9">
                    <h6 class="m-25">: {{ $query->city_name }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <span class="fw-bold">{{ __('sitemaster::message.pincode') }}</span>
                </div>
                <div class="col-12 col-lg-8">
                    <h6 class="m-25">: {{ $query->pincode }}</h6>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="row">
                <div class="col-12 col-lg-12">
                    <span class="fw-bold">{{ __('sitemaster::message.address') }} : </span>
                </div>
                <div class="col-12 col-lg-12">
                    <h6 class="m-25 ps-3"> {{ $query->address }}</h6>
                </div>
            </div>
        </div>


    </div>
</div>
<!--End Model Card Open-->
