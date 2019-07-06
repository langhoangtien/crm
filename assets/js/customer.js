$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 9999040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

function send_mail() {
    var template_email_id = $('#template_email_id').val();
    if (template_email_id == -1)
        toastr.warning('Bạn phải chọn một mẫu email!', 'Cảnh báo');
    else {
        var table = $('#tbl_customer');
        var checkbox = table.find('.file_checkbox:checked');

        var cid = new Array();
        $(checkbox).each(function (index) {
            cid[cid.length] = $(this).val();
        });

        $.ajax({
            type: "POST",
            url: BASE_URL + 'customers/do_send_mail',
            data: {
                cid: cid,
                template_email_id: template_email_id
            },
            beforeSend: function () {
                $('.mask').show();
            },
            success: function (string) {
                $('.mask').hide();
                var result = $.parseJSON(string);

                if (result == false)
                    toastr.error('Gửi mail không thành công.', 'Lỗi');
                else {
                    $('.manage-row-options').addClass('hidden');
                    table.find('input[type="checkbox"]').prop('checked', false);
                    toastr.success('Gửi mail thành công.', 'Thông báo');
                }

                $('#quick_modal').modal('toggle');
            }
        });
    }
}

function add_customer_tmp_list() {
    var table = $('#tbl_customer');
    var checkbox = table.find('.file_checkbox:checked');

    var cid = new Array();
    $(checkbox).each(function (index) {
        cid[cid.length] = $(this).val();
    });

    $.ajax({
        type: "POST",
        url: BASE_URL + 'customers/add_customer_tmp_list',
        data: {
            cid: cid
        },
        beforeSend: function () {
            $('.mask').show();
        },
        success: function (string) {
            var result = $.parseJSON(string);

            $('.mask').hide();
            toastr.success('Thực hiện thành công.', 'Thông báo');
            table.find('input[type="checkbox"]').prop('checked', false);
            $('.manage-row-options').addClass('hidden');

            $('#count_tmp_list').text(result.count_tmp);
            $('#count_tmp_list_title').css('display', 'inline-block');
            $('#count_tmp_list').css('display', 'inline-block');
        }
    });
}

function remove_from_tmp_list() {
    var table = $('#tbl_customer');
    var checkbox = table.find('.file_checkbox:checked');

    var cid = new Array();
    $(checkbox).each(function (index) {
        cid[cid.length] = $(this).val();
    });

    $.ajax({
        type: "POST",
        url: BASE_URL + 'customers/remove_customer_tmp_list',
        data: {
            cid: cid
        },
        beforeSend: function () {
            $('.mask').show();
        },
        success: function (string) {
            $('.mask').hide();
            toastr.success('Xóa thành công.', 'Thông báo');

            table.find('input[type="checkbox"]').prop('checked', false);
            $('.manage-row-options').addClass('hidden');
            load_list('customer_tmp_list');
        }
    });
}

$(document).ready(function () {
    $('#sendMail').click(function () {
        $.ajax({
            type: "GET",
            url: BASE_URL + 'customers/send_mail',
            success: function (html) {
                $('#quick_modal').addClass('size-700');
                $('#quick_modal').html(html);
                $('#quick_modal').modal('toggle');
            }
        });
    });
    $('#sendSMS').click(function () {
        $.ajax({
            type: "GET",
            url: BASE_URL + 'customers/send_sms',
            success: function (html) {
                $('#quick_modal').addClass('size-700');
                $('#quick_modal').html(html);
                $('#quick_modal').modal('toggle');
            }
        });
    });

    $('#btn_delete_customer').click(function () {
        bootbox.confirm("Bạn có thực sự muốn xóa không?", function (result) {
            if (result) {
                var table = $('#tbl_customer');
                var checkbox = table.find('.file_checkbox:checked');
                var cid = new Array();
                $(checkbox).each(function (index) {
                    cid[cid.length] = $(this).val();
                });
                var _data = {};
                _data['cid'] = cid;
                coreAjax.call(
                    BASE_URL + 'customers/deletes',
                    _data,
                    function (response) {
                        toastr.success('Xóa thành công.', 'Thông báo');
                        location.reload();
                    }
                );
            }
            else {
                return;
            }
        });

    });

    $('#btn_add_tmp_list').click(function () {
        add_customer_tmp_list();
    });

    $('#btn_remove_from_tmp_list').click(function () {
        remove_from_tmp_list();
    });
    
});


