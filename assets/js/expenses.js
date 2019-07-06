function load_sale_modal() {
    $.ajax({
        type: "GET",
        url: BASE_URL + 'sales/modal_ds',
        data: {
        },
        success: function(html){
            $('#my_table').addClass('size-1200');
            $('#my_table').html(html);
            $('#my_table').modal('toggle');
        }
    });
}

function select_sale_order(sale_id, sale_prefix, customer_id) {
    bootbox.confirm('Bạn có chắc muốn chọn không?', function(result){
        if(result == true) {
            $('#sale_code').val(sale_prefix);
            $('#sale_id').val(sale_id);
            $('#my_table').modal('toggle');
        }
    });
}

function load_receiving_modal() {
    $.ajax({
        type: "GET",
        url: BASE_URL + 'receivings/modal_ds',
        data: {
        },
        success: function(html){
            $('#my_table').addClass('size-1200');
            $('#my_table').html(html);
            $('#my_table').modal('toggle');
        }
    });
}

function select_receiving_order(ma_don_nhap_hang, supplier_id) {
    bootbox.confirm('Bạn có chắc muốn chọn không?', function(result){
        if(result == true) {
            $('#receiving_code').val(ma_don_nhap_hang);
            $('#receiving_id').val(ma_don_nhap_hang.replace('HĐNH',''));
            $('#my_table').modal('toggle');
        }
    });
}