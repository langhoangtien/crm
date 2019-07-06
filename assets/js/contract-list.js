$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 9999040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$( document ).ready(function() {
    $('#my_table').on('hidden.bs.modal', function () {
        if($('#my_table').hasClass('size-1200'))
            $('#my_table').removeClass('size-1200');

        if($('#my_table').hasClass('size-1000'))
            $('#my_table').removeClass('size-1000');
    })

    $('#quick_modal').on('hidden.bs.modal', function () {
        if($('#quick_modal').hasClass('size-700'))
            $('#quick_modal').removeClass('size-700');

        if($('#quick_modal').hasClass('size-1000'))
            $('#quick_modal').removeClass('size-1000');
    })
});

function download_contract_file(id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_document_form',
        data: {
            contract_id   :  id
        },
        success: function(html){
            $('#quick_modal').addClass('size-1000');
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
        }
    });
}

function download_file() {
    var quote_contact_id = $('#slb_quote_contract').val();
    var contract_id = $('#contract_file_download_form input[name="contract_id"]').val();
    if(quote_contact_id == -1)
        toastr.warning('Bạn phải chọn mẫu hợp đồng', 'Cảnh bảo');
    else {
        window.location.href = BASE_URL + 'contracts/do_make_file_download/?contract_id='+contract_id+'&quote_contract_id='+quote_contact_id;
        toastr.success('Tài file thành công', 'Thông báo');

        $('#quick_modal').modal('toggle');
    }
}

function handling_contract(id) {
    bootbox.confirm('Bạn có chắc không?', function(result){
        if (result){
            $.ajax({
                type: "POST",
                url: BASE_URL + 'contracts/contract_handling',
                data: {
                    contract_id   :  id
                },
                success: function(html){
                    toastr.success('Thao tác thành công.', 'Thông báo');
                    load_list('pos_circle_contract');
                }
            });
        }
    });
}

function handling_expired_contract(id) {
    bootbox.confirm('Bạn có chắc không?', function(result){
        if (result){
            $.ajax({
                type: "POST",
                url: BASE_URL + 'contracts/contract_expired_handling',
                data: {
                    contract_id   :  id
                },
                success: function(html){
                    toastr.success('Thao tác thành công.', 'Thông báo');
                    load_list('pos_expired_contract');
                }
            });
        }
    });
}