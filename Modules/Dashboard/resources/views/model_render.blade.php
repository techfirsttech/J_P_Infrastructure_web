<!--Start Model Card Open-->
<div class="modal-header">
    <h5 class="mb-0" id="detailModalTitle"></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('Dashboard::message.supervisors') }}</th>
                        <th>{{ __('Dashboard::message.total_credit') }}</th>
                        <th>{{ __('Dashboard::message.total_debit') }}</th>
                        <th>{{ __('Dashboard::message.closing_balance') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($supervisor as $item)
                    <tr>
                        <td>{{ $item->supervisor_name }}</td>
                        <td>{{ $item->total_credit }}</td>
                        <td>{{ $item->total_debit }}</td>
                        <td>{{ $item->closing_balance }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--End Model Card Open-->