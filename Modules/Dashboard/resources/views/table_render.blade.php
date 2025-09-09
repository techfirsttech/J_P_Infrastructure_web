<div class="row g-2">
    <div class="col-12 col-md-12 col-lg-6">
        <div class="card p-2 h-100">
            <h5 class="card-title mb-0">{{ __('Dashboard::message.site') }}</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>{{ __('Dashboard::message.site') }}</th>
                            <th>{{ __('Dashboard::message.total_credit') }}</th>
                            <th>{{ __('Dashboard::message.total_debit') }}</th>
                            <th>{{ __('Dashboard::message.closing_balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siteData as $site)
                        <tr>
                            <td>{{ $site->site_name }}</td>
                            <td class="text-end pe-3">{{ number_format($site->total_credit, 2) }}</td>
                            <td class="text-end pe-3">{{ number_format($site->total_debit, 2) }}</td>
                            <td class="text-end pe-3">{{ number_format($site->closing_balance, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">
        <div class="card p-2 h-100">
            <h5 class="card-title mb-0">{{ __('Dashboard::message.supervisors') }}</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>{{ __('Dashboard::message.supervisors') }}</th>
                            <th>{{ __('Dashboard::message.total_credit') }}</th>
                            <th>{{ __('Dashboard::message.total_debit') }}</th>
                            <th>{{ __('Dashboard::message.closing_balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supervisorData as $item)
                        <tr>
                            <td>{{ $item->supervisor_name }}</td>
                            <td class="text-end pe-3">{{ $item->total_credit }}</td>
                            <td class="text-end pe-3">{{ $item->total_debit }}</td>
                            <td class="text-end pe-3">{{ $item->closing_balance }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>