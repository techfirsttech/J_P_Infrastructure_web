
<div class="row g-6">
    @foreach($siteMaster as $site)
    <div class="col-lg-4 col-sm-6">
        <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar me-4">
                        <span class="avatar-initial rounded bg-label-primary"><i class="fa fa-tag"></i></span>
                    </div>
                    <h4 class="mb-0">{{$site->site_name}}</h4>
                </div>

                <p class="text-heading fw-medium me-2 mb-1">Total Balance : {{$site->closing_balance}}</p>
                <!-- <p class="mb-0"> -->
                <div class="d-flex justify-content-between">
                    <p class="text-muted  mb-50 me-2">Income : <span class="text-success"> {{$site->total_credit}}</span></p>
                    <p class="text-muted  mb-50 me-2">Expense : <span class="text-danger"> {{$site->total_debit}}</span></p>
                </div>

                <!-- <span class="text-heading fw-medium me-2">+18.2%</span>
                    <small class="text-muted">than last week</small> -->
                <!-- </p> -->
            </div>
        </div>
    </div>
    @endforeach
</div>

