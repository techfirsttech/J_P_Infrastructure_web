@if(!empty($search))
<button class="btn btn-outline-primary py-2 px-4 search" type="button" title="Search"><i class="fa fa-search"></i></button>
@endif

@if(!empty($export))
<button class="btn btn-outline-success py-2 px-4 export" type="submit" title="Download" data-route="{{ $export }}"><i class="fa fa-download"></i></button>
@endif

@if(!empty($reset))
<button class="btn btn-outline-danger py-2 px-4 reset" type="reset" title="Clear"><i class='fa fa-x'></i></button>
@endif
