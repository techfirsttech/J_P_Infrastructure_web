$(document).on('click', '.save', function (e) {

    var formClass = '#form';

    if ($(this).attr('data-form-class') !== undefined) {
        formClass = '.' + $(this).attr('data-form-class');
    }

    e.preventDefault();
    if ($(formClass).valid()) {
        var formData = new FormData($(formClass)[0]);
        var route = $(this).attr('data-route');
        var status = $(this).attr('data-status');
        if (status != undefined) {
            formData.append('status', status);
        }
        $.ajax({
            type: 'POST',
            url: route,
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".invalid-feedback,.custom-error").html('');
                $(".save").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait`);
                $(".save").attr('disabled', true);
            },
            success: function (response) {
                if (response.status_code == 500) {
                    $(".save").html("Submit");
                    $(".save").attr('disabled', false);
                    toastr.error(response.message, "Error");
                } else if (response.status_code == 403 || response.status_code == 404) {
                    $(".save").html("Submit");
                    $(".save").attr('disabled', false);
                    toastr.warning(response.message, "Warning");
                } else if (response.status_code == 201) {
                    $(".save").html("Submit");
                    $(".save").attr('disabled', false);
                    $.each(response.errors, function (key, value) {
                        if (key.indexOf('.') !== -1) {
                            $('#error_' + key.replace(/\./g, '_')).html('<p class="text-danger mb-0">' + value + '</p>');
                        } else {
                            $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                        }
                    });
                    // toastr.warning(response.message, "Warning");
                } else {
                    toastr.success(response.message, "Success");
                    if (response.data != undefined) {
                        setTimeout(function () {
                            location.href = response.data;
                        }, 500);
                    } else {
                        $(".save").html("Submit");
                        $(".save").attr('disabled', false);
                        $("#inlineModal").modal('hide');
                        table.ajax.reload(null, true);
                    }
                }
            }
        });
    } else {
        return false;
    }
});
