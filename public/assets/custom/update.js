$(document).on('click', '.update', function (e) {
    e.preventDefault();
    let formClass = '#form';
    if ($(this).attr('data-form-class') !== undefined) {
        formClass = '.' + $(this).attr('data-form-class');
    }
    const $form = $(formClass);
    if ($form.valid()) {
        const formData = new FormData($form[0]);
        var route = $(this).attr('data-route');
        $.ajax({
            type: 'POST',
            url: route,
            data: formData,
            dataType: 'json',
            cache: true,
            contentType: false,
            processData: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT',
            },
            beforeSend: function () {
                $(".invalid-feedback, .custom-error").html('');
                $(".update").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait`);
                $(".update").attr('disabled', true);
            },
            success: function (response) {
                $(".update").html("Submit");
                $(".update").attr('disabled', false);
                if (response.status_code == 500) {
                    toastr.error(response.message, "Error");
                } else if (response.status_code == 403 || response.status_code == 404) {
                    toastr.warning(response.message, "Warning");
                } else if (response.status_code == 201) {
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
