// start filter inside start end date used
if ($("#filter_form #s_date").length != 0 && $("#filter_form #e_date").length != 0) {
    var startPicker = flatpickr("#filter_form #s_date", {
        altInput: true,
        altFormat: 'd-m-Y',
        dateFormat: 'Y-m-d',
        maxDate: new Date(),
        onChange: function (selectedDates) {
            endPicker.set('minDate', selectedDates[0]);
        }
    });

    var endPicker = flatpickr("#filter_form #e_date", {
        altInput: true,
        altFormat: 'd-m-Y',
        dateFormat: 'Y-m-d',
        maxDate: new Date(),
        onChange: function (selectedDates) {
            startPicker.set('maxDate', selectedDates[0]);
        }
    });
}
// end filter inside start end date used

// start filter
if ($("#filter_form .search").length != 0) {
    $(document).on('click', '.search', function () {
        table.draw();
    });
}
// end filter

// start clear filter
if ($("#filter_form .reset").length != 0) {
    $(document).on('click', '.reset', function () {
        $('#filter_form').trigger('reset');
        $('#filter_form .select2').val('All').trigger('change');
        if ($("#filter_form #s_date").length != 0 && $("#filter_form #e_date").length != 0) {
            startPicker.clear();
            startPicker.set('maxDate', null);

            endPicker.clear();
            endPicker.set('minDate', null);
        }
        if ($("#filter_form .export").length != 0) {
            $("#filter_form .export").html('<i class="fa fa-download"></i>');
            $("#filter_form .export").attr('disabled', false);
        }
        table.draw();
    });
}
// end clear filter

// start filter after excel export
if ($("#filter_form .export").length != 0) {
    $(document).on('click', '.export', function () {
        var route = $(this).attr('data-route');
        if (route != undefined) {
            var me = $(this);
            me.html('<i class="fa fa-spinner fa-spin"></i>');
            me.attr('disabled', true)
            var filters = $('#filter_form').serialize()
            $.ajax({
                url: route,
                type: 'GET',
                data: filters,
                success: function (response) {
                    me.html('<i class="fa fa-download"></i>');
                    me.attr('disabled', false);
                    if (response.status_code == 500) {
                        toastr.error(response.message, "Opps");
                    } else if (response.status_code == 404) {
                        toastr.warning(response.message, "Warning");
                    } else {
                        //open in new tab
                        window.open(response.download_url, '_blank');
                        // window.location.href = response.download_url;
                    }
                },
                error: function () {
                    toastr.error('Something went wrong while exporting the data.', "Opps");
                    // alert('Something went wrong while exporting the data.');
                }
            });
        }
    });
}
// end filter after excel export
