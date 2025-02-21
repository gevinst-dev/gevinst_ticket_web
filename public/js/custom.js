"use strict";

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    cache: false,
    complete: function () {
        // LetterAvatar.transform();
        $('[data-toggle="tooltip"]').tooltip();
        function select2() {
            if ($(".select2").length > 0) {
                $($(".select2")).each(function(index, element) {
                    var id = $(element).attr('id');
                    var multipleCancelButton = new Choices(
                        '#' + id, {
                            removeItemButton: true,
                        }
                    );
                });
            }
        }
    },
});


function show_toastr(title, message, type) {
    var o, i;
    var icon = '';
    var cls = '';

    if (type == 'success') {
        icon = 'fas fa-check-circle';
        cls = 'success';
    } else {
        icon = 'fas fa-times-circle';
        cls = 'danger';
    }

    $.notify({icon: icon, title: title, message: message, url: ""}, {
        element: "body",
        type: cls,
        allow_dismiss: !0,
        placement: {from: 'top', align: 'right'},
        offset: {x: 15, y: 15},
        spacing: 10,
        z_index: 9999,
        delay: 2500,
        timer: 2000,
        url_target: "_blank",
        mouse_over: !1,
        animate: {enter: o, exit: i},
        template: '<div class="alert alert-{0} alert-icon alert-group alert-notify" data-notify="container" role="alert"><div class="alert-group-prepend alert-content"><span class="alert-group-icon"><i data-notify="icon"></i></span></div><div class="alert-content"><strong data-notify="title">{1}</strong><div data-notify="message">{2}</div></div><button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
    });
}





function validation() {
    var forms = document.querySelectorAll('.needs-validation');


    Array.prototype.forEach.call(forms, function (form) {

        form.addEventListener('submit', function (event) {
            var submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
            }
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }

            form.classList.add('was-validated');
        }, false);
    });
}

$(document).ready(function () {
    if ($(".needs-validation").length > 0) {
        validation();
    }
    if ($(".pc-dt-simple").length > 0) {
        $($(".pc-dt-simple")).each(function (index, element) {
            var id = $(element).attr('id');
            const dataTable = new simpleDatatables.DataTable("#" + id);
        });
    }
    // common_bind();
    // summernote();


    // for Choose file
    $(document).on('change', 'input[type=file]', function () {
        var fileclass = $(this).attr('data-filename');
        var finalname = $(this).val().split('\\').pop();
        $('.' + fileclass).html(finalname);
    });
});









$(document).ready(function () {
    $(window).resize();

    loadConfirm();

    if ($("#selection-datatable").length) {
        $("#selection-datatable").DataTable({
            order: [],
            select: {style: "multi"},
            "language": dataTableLang,
            drawCallback: function () {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
            }
        });
    }

    // LetterAvatar.transform();
    $('[data-toggle="tooltip"]').tooltip();

    $('#commonModal-right').on('shown.bs.modal', function () {
        $(document).off('focusin.modal');
    });

    if ($(".select2").length) {
        $('.select2').select2({
            "language": {
                "noResults": function () {
                    return "No result found";
                }
            },
        });
    }

    // for Choose file
    $(document).on('change', 'input[type=file]', function () {
            var names = '';

            var fileclass = $(this).attr('data-filename');
            var attr = $(this).attr('multiple');

            if (typeof attr !== typeof undefined && attr !== false) {
                var files = $(this)[0].files;
                for (var i = 0; i < files.length; i++) {
                    names += files[i].name + '<br>';
                }
            } else {
                names = $(this).val().split('\\').pop();
            }

            $('.' + fileclass).html(names);
        }
    );
});

// Common Modal
$(document).on('click', 'a[data-ajax-popup="true"], button[data-ajax-popup="true"]', function (e) {
    var title = $(this).data('title');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var url = $(this).data('url');

    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);

    $.ajax({
        url: url,
        cache: false,
        success: function (data) {
            // $('#commonModal .modal-body .card-box').html(data);
            $('#commonModal .modal-body ').html(data);
            $("#commonModal").modal('show');
            commonLoader();
            validation();
        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });
    e.stopImmediatePropagation();
    return false;
});

// Common Modal from right side
$(document).on('click', 'a[data-ajax-popup-right="true"], button[data-ajax-popup-right="true"], div[data-ajax-popup-right="true"], span[data-ajax-popup-right="true"]', function (e) {
    var url = $(this).data('url');

    $.ajax({
        url: url,
        cache: false,
        success: function (data) {
            $('#commonModal-right').html(data);
            $("#commonModal-right").modal('show');
            commonLoader();
            validation();
        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });
});


$(document).on('click', 'a[data-ajax-popup-over="true"], button[data-ajax-popup-over="true"], div[data-ajax-popup-over="true"]', function () {

    var validate = $(this).attr('data-validate');
    var id = '';
    if (validate) {
        id = $(validate).val();
    }

    var title = $(this).data('title');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
    var url = $(this).data('url');

    $("#commonModalOver .modal-title").html(title);
    $("#commonModalOver .modal-dialog").addClass('modal-' + size);

    $.ajax({
        url: url + '?id=' + id,
        success: function (data) {
            $('#commonModalOver .modal-body').html(data);
            $("#commonModalOver").modal('show');
            taskCheckbox();
        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });

});


function commonLoader() {

    // LetterAvatar.transform();


    $('[data-toggle="tooltip"]').tooltip();

    if ($(".select2").length) {
        $('.select2').select2({
            "language": {
                "noResults": function () {
                    return "No result found";
                }
            },
        });
    }



    if ($(".datepicker").length) {
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            format: 'yyyy-mm-dd',
            locale: date_picker_locale,
        });
    }

}

function loadConfirm() {
    $('[data-confirm]').each(function () {
        var me = $(this),
            me_data = me.data('confirm');

        me_data = me_data.split("|");
        me.fireModal({
            title: me_data[0],
            body: me_data[1],
            buttons: [
                {
                    text: me.data('confirm-text-yes') || 'Yes',
                    class: 'btn btn-sm btn-danger rounded-pill',
                    handler: function () {
                        eval(me.data('confirm-yes'));
                    }
                },
                {
                    text: me.data('confirm-text-cancel') || 'Cancel',
                    class: 'btn btn-sm btn-secondary rounded-pill',
                    handler: function (modal) {
                        $.destroyModal(modal);
                        eval(me.data('confirm-no'));
                    }
                }
            ]
        })
    });
}


if ($(".multi-select").length > 0) {
    $( $(".multi-select") ).each(function( index,element ) {
        var id = $(element).attr('id');
           var multipleCancelButton = new Choices(
                '#'+id, {
                    removeItemButton: true,
                }
            );
    });

}
