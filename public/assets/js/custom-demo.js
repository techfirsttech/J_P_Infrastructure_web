$(document).ready(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
    // $('.select-2').select2();
});

if ($(".select2").length != 0) {    
    $(document).ready(function () {
        select2Init();
        $('.select2').on('change', function () {
            let element = $(this).attr('name');
            if (element && element.includes('[]')) {
                let cleanElement = $(this).attr('id');
                $(`#${cleanElement}-error`).text('');
                $(`#${cleanElement}-error`).hide();
            } else {
                $(`#${element}-error`).text('');
                $(`#${element}-error`).hide();
            }
        });
    });


    function select2Init() {
        let select2 = $('.select2');
        if (select2.length) {
            select2.each(function () {
                var $this = $(this);
                let placeholder = $this.attr('data-placeholder') || 'Select value';
                $this.select2({
                    allowClear: false,
                    dropdownParent: $this.parent(),
                    selectOnClose: true,
                    width: '100%',
                });

                $this.on('select2:close', function () {
                    var $formElements = $('input, select, textarea').not(':disabled, [type=hidden]');
                    var currentIndex = $formElements.index(this);
                    if (currentIndex !== -1 && currentIndex + 1 < $formElements.length) {
                        $formElements.eq(currentIndex + 1).focus();
                    }
                });
            });
        }
    }
}

var datepickerList = document.querySelectorAll('.date-picker');
const today = new Date();
const minDate = new Date();
minDate.setDate(today.getDate() - 7);

// Flat Picker Birth Date
if (datepickerList) {
    datepickerList.forEach(function (datepicker) {
        datepicker.flatpickr({
            monthSelectorType: 'static',
            dateFormat: "d-m-Y",
            maxDate: "today",
            defaultDate: "today",
            // minDate:minDate
        });
    });
}

$('.toggle-password').click(function () {
    $(this).children().toggleClass('fa fa-eye fa fa-eye-slash');
    let input = $(this).prev();
    input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
});

// Keydown event to trigger add button
$(document).on('keydown', function (event) {
    if (event.key === '+') {
        $('.new-create').last().trigger('click'); // Trigger the click on the last add button
    }
});

document.querySelectorAll('.menu-item.has-submenu > a').forEach(function (menuItem) {
    menuItem.addEventListener('click', function () {
        let parentItem = menuItem.parentElement;
        parentItem.classList.toggle('open');
    });
});
