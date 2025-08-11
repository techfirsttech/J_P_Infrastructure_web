// start filter inside start end date used
if ($("#s_date").length != 0 && $("#e_date").length != 0) {
    var startPicker = flatpickr("#s_date", {
        altInput: true,
        altFormat: 'd-m-Y',
        dateFormat: 'Y-m-d',
        maxDate: new Date(),
        onChange: function (selectedDates) {
            endPicker.set('minDate', selectedDates[0]);
        }
    });

    var endPicker = flatpickr("#e_date", {
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
if ($(".search").length != 0) {
    $(document).on('click', '.search', function () {
        table.draw();
    });
}
// end filter

// start clear filter
if ($(".reset").length != 0) {
    $(document).on('click', '.reset', function () {
        $('#filter_form').trigger('reset');
        $('.select2').val('All').trigger('change');
        if ($("#s_date").length != 0 && $("#e_date").length != 0) {
            startPicker.clear();
            startPicker.set('maxDate', null);

            endPicker.clear();
            endPicker.set('minDate', null);
        }
        table.draw();
    });
}
// end clear filter

// start filter after excel export
if ($(".export").length != 0) {
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
                        window.location.href = response.download_url;
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
