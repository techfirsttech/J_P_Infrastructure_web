<div class="modal-header order-0">
    <h5 class="text-center mb-0" id="detailModalTitle">{{ __('unit::message.unit') }} : {{ isset($unit->name) ? $unit->name : '' }} </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" id="body">
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="append_table" class="datatables-basic table table-hover">
                    <thead class="">
                        <tr>
                            <th>#</th>
                            <th>{{ __('unit::message.sub_unit') }}</th>
                            <th>{{ __('unit::message.sub_unit_value') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(count($unit->unitGravity) > 0)
                        @foreach ($unit->unitGravity as $key => $value)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ !is_null($value->unit) ? $value->unit->name : '' }}</td>
                            <td>{{ !is_null($value->unit) ? $value->unit_value : '' }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>