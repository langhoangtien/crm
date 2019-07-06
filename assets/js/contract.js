$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 9999040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});
$( document ).ready(function() {
    var DATE_FORMAT = 'DD-MM-YYYY';
    date_time_picker_field_report($('#status_date'), DATE_FORMAT);
    date_time_picker_field($('.datepicker'), DATE_FORMAT);
    if(action == 'add') {
        load_type_input();
    }
    load_request_section();

    $('body').on('change','#type',function(){
        load_type_input();
        load_request_section();
        //reset_form_contract();
    });

    $('body').on('click','.input-group-btn',function(){
        $('#file_upload').trigger('click');
    });

    $('body').on('click','#sale_code_button',function(){
        if(action == 'add') {
            $.ajax({
                type: "GET",
                url: BASE_URL + 'sales/modal_list',
                data: {
                },
                success: function(html){
                    $('#my_table').addClass('size-1200');
                    $('#my_table').html(html);
                    $('#my_table').modal('toggle');
                }
            });
        }
    });

    $('body').on('click','#receiving_code',function(){
        if(action == 'add') {
            $.ajax({
                type: "GET",
                url: BASE_URL + 'receivings/modal_list',
                data: {
                },
                success: function(html){
                    $('#my_table').addClass('size-1200');
                    $('#my_table').html(html);
                    $('#my_table').modal('toggle');
                }
            });
        }

    });

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

    $('body').on('change','.file_upload',function(){
        var yourstring           = $(this).val();
        var filename             = yourstring.replace(/^.*[\\\/]/, '')
        var filename_without_ext = filename.substr(0, filename.lastIndexOf('.')) || filename;
        $('#file_display').val(filename);
        $('#file_name').val(filename_without_ext);
    });

    $('body').on('click','[data-tab]',function(){
        $( "[data-tab]" ).removeClass('active');
        var data_id = $(this).attr('data-tab');
        $('.manage-table.type-2 .tabs').hide();
        $(this).addClass('active');
        $('#'+data_id).show();

        $('#request_section .data-n9-pagination').hide();
        if(data_id == 'contract_payment')
            $('.data-n9-pagination[data-table="contract_payment"]').show();

        if(data_id == 'contract_delivery')
            $('.data-n9-pagination[data-table="contract_delivery"]').show();
    });

    $('body').on('change','#parent_id',function(){
        var contract_id = $(this).val();
        if(contract_id != -1) {
            if(action == 'add') {
                load_rule_contract_list(contract_id);
                var code    = $('#parent_id option:selected').attr('data-code');
                if(option == 'customer')
                    var number_id = $('#sale_id').val();
                else if(option == 'supplier')
                    var number_id = $('#receiving_id').val();

                create_contract_code(number_id, code);
            }
        }
    });

//        $(window).keydown(function(event){
//            if(event.keyCode == 13) {
//                $( "#submitf" ).trigger( "click" );
//                event.preventDefault();
//
//                return false;
//            }
//        });
});

function load_type_input() {
    var DATE_FORMAT = 'DD-MM-YYYY';
    if($('#type').length)
       var select_value   = $('#type').val();
    else
       var select_value   = 'rule';
    
    var sale_id = $('#vivu').val();
    // alert(sale_id);
    $.ajax({
        type: "GET",
        data : {
            type : select_value,
            option: option,
            sale_id: sale_id
        },
        url: BASE_URL + 'contracts/load_contract_section',
        success: function(html){
            $('#contract_section').html(html);
            date_time_picker_field($('.datepicker'), DATE_FORMAT);
        }
    });
}

function load_request_section() {
    $('#customer_section').hide();
    $('#supplier_section').hide();
    var select_value = $('#type').val();
    if (typeof select_value === "undefined") {
        select_value = 'rule';
    }

    var contract_id = $('#contract_id').val();
    
    $.ajax({
        type: "GET",
        data : {
            type        : select_value,
            action      : action,
            contract_id : contract_id
        },
        url: BASE_URL + 'ajax/html_load_request_section',
        success: function(html){
            $('#request_section .panel-piluku').html(html);

            if(action == 'edit') {
                if(select_value == 'rule')
                    $( "[data-tab='contract_payment']" ).trigger( "click" );
            }
        }
    });
}

function reset_form_error() {
    $('#contract_form .has-error').removeClass('has-error');
    $('#contract_form span.errors').text('');
}

function reset_error() {
    $('#quick_modal span.errors').text('');
    $('#quick_modal .has-error').removeClass('has-error');
}

function contract_payment_detail_frm(payment_detail_id) {
    var payment_id = $('[name="s_payment_id"]').val();
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_payment_detail_form',
        data: {
            id                   : payment_detail_id,
            contract_payment_id  : payment_id
        },
        success: function(html){
            $('#quick_modal').addClass('size-700');
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');

            setTimeout(function() { $('#contract_payment_detail_form [name="name"]').focus() }, 500);
        }
    });
}

function contract_payment_form_without_db(row) {



    // var vivi =0;
    // var st;
    // $("input[name*='payment[c_status][]']").each(function(){
    //     console.log(this);
    //     if($(this).val() == 'liquidated')
    //     {
    //         vivi =1;
    //     }
        
    // });

    // // console.log(st);
    // if(vivi ==1)
    // {
    //     // $('#quick_modal').modal('toggle');
    //     toastr.warning("Hợp đồng đã thanh lý, bạn không thể thêm giai đoạn","Cảnh báo");
    //     return;
    // }

	var project_id = $('#project_id').val();
	if (project_id == '') {
		bootbox.alert("Bạn cần chọn dự án trước.");
		return false;
	}
    var data    = new Object();
    data.id     = row;
    data.action = action;
    data.type   = $('#type').val();
    data.project_id = project_id;

    if(row > 0){
        var tr_element = $('[data-table="contract_payment"] tbody tr[data-row='+row+']');
        var item = {
            name         : tr_element.find('[name="payment[name][]"]').val(),
            task_name : 	tr_element.find('[name="task_name[task_name][]"]').val(),
            task_id : 	tr_element.find('[name="task_id[task_id][]"]').val(),
            date_payment : tr_element.find('[name="payment[date_payment][]"]').val(),
            price        : tr_element.find('[name="payment[price][]"]').val(),
            vat          : tr_element.find('[name="payment[vat][]"]').val(),
            c_status     : tr_element.find('[name="payment[c_status][]"]').val(),
            unit         : tr_element.find('[name="payment[unit][]"]').val()
        };
        data.item = item;
    }
    console.log(data);
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_payment_form',
        data: data,
        success: function(html){
            $('#quick_modal').addClass('size-700');
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');

            setTimeout(function() { $('#c_payment_name').focus() }, 500);
        }
    });
}

function contract_payment_frm(payment_id) {
    var type_value = $('#type').val();
    var contract_id = $('#contract_id').val();
    var project_id = $('#project_id').val();
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_payment_form',
        data: {
            id          : payment_id,
            contract_id : contract_id,
            type        : type_value,
            html        : 1,
            project_id: project_id
        },
        success: function(html){
            $('#quick_modal').addClass('size-700');
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');

            setTimeout(function() { $('#c_payment_name').focus() }, 500);
        }
    });
}

function contract_delivery_frm(delivery_id) {
    var contract_id = $('#contract_id').val();
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_delivery_form',
        data: {
            delivery_id : delivery_id,
            contract_id : contract_id
        },
        success: function(html){
            $('#quick_modal').addClass('size-1000');
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
        }
    });
}

function load_contract_payment_list(items) {
    if(items.length) {
        var string = new Array();
        var source_url = BASE_URL + 'ajax/select_vat';
        var url        = BASE_URL + 'customers/vat_without_db';
        $.each(items, function( index, value ) {
            var row_id = index + 1;
            var type = 'parttime';
            var stt = row_id;
            var name = value.name;
            var date_payment = value.date_payment;
            var price   = parseFloat(value.price);
            var percent = parseFloat(value.percent);
            var vat = value.vat;
            var task_id = '';
            var task_name = '';
            if(price != -1) {
                price = value.price_format;
                var unit = 'money';
            }

            if(percent != -1) {
                price = value.percent_format;
                var unit = 'percent';
            }

            if(vat == 'unpublished' || vat == '')
                var vat_name = '<a href="javascript:;" class="x_vat" data-type="select" data-value="unpublished" data-pk="1" data-source="'+source_url+'" data-url="'+url+'" data-title="Tình trạng">Không</a>';
            else
                var vat_name = '<a href="javascript:;" class="x_vat" data-type="select" data-value="published" data-pk="1" data-source="'+source_url+'" data-url="'+url+'" data-title="Tình trạng">Có</a>';

            string[string.length] = load_row(row_id, type, stt, name, date_payment, price, unit, vat_name, vat, task_id, task_name);
        });
        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function do_x_select() {
    $('.x_vat').editable({
        success: function(response, newValue) {
            var res = $.parseJSON(response);
            var row_id = res.pk;
            var vat_input = $('[data-table="contract_payment"] tr[data-row="'+row_id+'"] [name="payment[vat][]"]');
            vat_input.val(res.value);
        }
    });
}

function load_row(row_id, row_type, stt, name, date_payment, price, unit, vat_name, vat, task_id, task_name,c_status) {
    if(vat == '')
        vat = 'unpublished';

    if(row_type != 'rule')
        var td_element = '<td class="center">'+vat_name+'<input type="hidden" name="payment[vat][]" value="'+vat+'" /></td>';
    else
        var td_element = '';

    if(unit == 'money')
        var unit_name = currency_symbol;
    else
        var unit_name = ' %';

    var html = '<tr style="cursor: pointer;" data-row="'+row_id+'">'+
                    '<td class="cb"><input type="checkbox" value="'+row_id+'" class="file_checkbox"><label><span></span></label></td>'+
                    '<td class="center stt">'+stt+'<input type="hidden" name="payment[order][]" value="'+stt+'" /></td>'+
                    '<td class="center">'+name+'<input type="hidden" name="payment[name][]" value="'+name+'" /></td>'+
                    '<td class="center">'+task_name+'<input type="hidden" name="task_id[task_id][]" value="'+task_id+'" /><input type="hidden" name="task_name[task_name][]" value="'+task_name+'" /></td>'+
                    '<td class=" center">'+date_payment+'<input type="hidden" name="payment[date_payment][]" value="'+date_payment+'" /></td>'+
                    '<td class="center">'+price+' '+unit_name+'<input type="hidden" name="payment[price][]" value="'+price+'" /><input type="hidden" name="payment[unit][]" value="'+unit+'" /></td>'+
                    c_status+
                    td_element+
                    '<td class="center"><a href="javascript:;" class="center" onclick="contract_payment_form_without_db('+row_id+');">Sửa</a></td>'+
                '</tr>';

    return html;
}

function save_contract(type) {
    reset_form_error();

    if(action == 'add')
        var url = BASE_URL + 'contracts/contract_add?back_type='+type+'&list_type='+list_type;
    else if(action == 'edit')
        var url = BASE_URL + 'contracts/contract_edit?back_type='+type+'&list_type='+list_type;

    var checkOptions = {
        url : url,
        dataType: "json",
        success: contract_data
    };

    $("#contract_form").ajaxSubmit(checkOptions);
    return false;
}

function contract_data(data) {
     if(data.c_status) {
        toastr.error("Có nhiều hơn 1 giai đoạn đã thanh lý","Lỗi");
    }

      if(data.status) {
        toastr.error("Trạng thái của hợp đồng phụ thuộc vào trạng thái giai đoạn","Lỗi");
    }
    if(data.flag == 'false') {
        if(data.errors.sale_id) {
        show_feedback('error', 'Nhu cầu khách hàng đã tồn tại');
    }

   
        var first_key = Object.keys(data.errors)[0];
        var flag_tab = false;
        $.each(data.errors, function( index, value ) {
            element = $( '#contract_form [name="'+index+'"]' );
            group = element.closest('.hang');
            group.addClass('has-error');
            group.find('span[for="'+index+'"]').text(value);
            if(index == 'file_upload' || 'file_name')
                flag_tab = true;
        });

        $( '[name="'+first_key+'"]' ).focus();

        if(flag_tab == true) {
            $( "[data-tab='contract_other']" ).trigger( "click" );
        }
    }else {
        if(data.back_type == 'save') {
            var url_redirect = BASE_URL + 'contracts/view/'+option+'/'+data.last_id;
            if(data.list_type != 'list')
                url_redirect = url_redirect + '/' + data.list_type;

            if(page > 1)
                url_redirect = url_redirect+'/'+page;

            window.location.href = url_redirect;
        }else if(data.back_type == 'save-close') {
            if(data.list_type != 'list')
                url_redirect = BASE_URL + 'contracts/'+data.list_type+'/'+option;
            else
                var url_redirect = BASE_URL + 'contracts/index/'+option;

            if(page > 1 && data.list_type != 'list')
                url_redirect = url_redirect+'/'+page;

            if(data.list_type != 'list')
                url_redirect = url_redirect+'/'+data.list_type;

            window.location.href = url_redirect;
        }
    }
}

function save_contract_payment_detail() {
    reset_error();
    var checkOptions = {
        url : BASE_URL + 'contracts/contract_payment_detail_save',
        dataType: "json",
        success: save_contract_payment_detail_data
    };
    $("#contract_payment_detail_form").ajaxSubmit(checkOptions);
    return false;
}

function save_contract_payment_detail_data(data) {
    if(data.flag == 'false') {
        $.each(data.errors, function( index, value ) {
            element = $( '#contract_payment_detail_form span[for="'+index+'"]' );
            element.prev().addClass('has-error');
            element.text(value);
        });
    }else {
        toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#quick_modal').modal('toggle');

        load_list('contract_payment_detail');
        load_list('contract_payment');
    }
}

function save_contract_payment() {
    reset_error();
    var checkOptions = {
        url : BASE_URL + 'contracts/contract_payment_save',
        dataType: "json",
        success: save_contract_payment_data
    };
    $("#contract_payment_form").ajaxSubmit(checkOptions);
    return false;
}

function save_contract_payment_data(data) {
    if(data.flag == 'false') {
        $.each(data.errors, function( index, value ) {
            element = $( '#contract_payment_form span[for="'+index+'"]' );
            element.prev().addClass('has-error');
            element.text(value);
        });
    }
    else if(data.flag == 'warning'){
        toastr.warning(data.canhbao,'Cảnh báo');
        $('#quick_modal').modal('toggle');
    }

    else {
        toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#quick_modal').modal('toggle');

        load_list('contract_payment');
        location.reload();
    }
}

function save_contract_delivery() {
    reset_error();
    var checkOptions = {
        url : BASE_URL + 'contracts/contract_delivery_save',
        dataType: "json",
        success: save_contract_delivery_data
    };
    $("#contract_delivery_form").ajaxSubmit(checkOptions);
    return false;
}

function save_contract_delivery_data(data) {
    if(data.flag == 'false') {
        $.each(data.errors, function( index, value ) {
            element = $( '#contract_delivery_form span[for="'+index+'"]' );
            element.prev().addClass('has-error');
            element.text(value);
        });
    }else if(data.flag == 'no-store') {
        $.each(data.update_limit, function( index, value ) {
            $('#delivered_'+index).html(value);

        });

        $.each(data.update_limit_msg, function( index, value ) {
            toastr.warning(value, 'Cảnh báo');
        });
    }else if(data.flag == 'no-limit') {
        toastr.error('Không có hàng để giao', 'Lỗi');
    }else {
        toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#quick_modal').modal('toggle');

        load_list('contract_delivery');
    }
}

function save_contract_payment_without_db() {
    reset_error();
    var errors = new Object();
    var payment_id   = $('#payment_id').val();
    var unit         = $('#unit').val();
    var name         = $.trim($('#c_payment_name').val());
    var date_payment = $.trim($('#c_date_payment').val());
    var price        = $.trim($('#c_payment_price').val());
    var vat          = $.trim($('#c_payment_vat').val());
    var task_id		= $('#c_task_id').val();
    var c_status    = $('#c_status').val();
    var task_name   = $('#c_task_id').find('option[value="'+ task_id +'"]').text();
    var link_vat_source = BASE_URL + 'ajax/select_vat';
    var url             = BASE_URL + '/customers/vat_without_db';

    var flag = 'true';

    // var vivi =0;
    // var st;
    // $("input[name*='payment[c_status][]']").each(function(){
    //     console.log(this);
    //     if($(this).val() == 'liquidated')
    //     {
    //         vivi =1;
    //     }
        
    // });

    // // console.log(st);
    // if(vivi ==1)
    // {
    //     // $('#quick_modal').modal('toggle');
    //     toastr.warning("Hợp đồng đã thanh lý, bạn không thể thêm giai đoạn","Cảnh báo");
    //     return;
    // }


 
    if(!name) {
        flag = 'false';
        errors.c_payment_name = 'Tên giai đoạn không được rỗng.';
    }
    if (task_id == "-1") {
    	flag = 'false';
    	errors.c_task_id = 'Công việc chưa được chọn.';
    }

    if(c_status == 'done' || c_status=='liquidated')
    {
        if(!date_payment) {
            flag = 'false';
            errors.c_date_payment = 'Thời hạn không được rỗng.';
        }
    }
    if(!price) {
        flag = 'false';
        errors.c_payment_price = 'Số tiền không được rỗng.';
    }

    if(flag == 'false') {
        $.each(errors, function( index, value ) {
            element = $( '#quick_modal span[for="'+index+'"]' );
            element.text(value);
        });
    }else {


        if(c_status=='done')
        {
            c_status ='<td class=" center">Đã nghiệm thu<input type="hidden" name="payment[c_status][]" value="'+c_status+'" /></td>';
        }
        else if(c_status=='liquidated')
        {
            c_status='<td class=" center">Đã thanh lý<input type="hidden" name="payment[c_status][]" value="'+c_status+'" /></td>';
        }
        else
        {
            c_status = '<td class=" center">Chưa nghiệm thu<input type="hidden" name="payment[c_status][]" value="" /></td>';
        }
        var table = '[data-table="contract_payment"]';

        var type_value = $('#type').val();

        if(payment_id > 0) {
            var row_id     = payment_id;
            var tr_element = $(table + ' tbody tr[data-row="'+row_id+'"]');
            var stt        = tr_element.find('td.stt').text();

            if(vat == 'unpublished')
                var vat_name = '<a href="#" class="x_vat" data-type="select" data-value="unpublished" data-pk="'+row_id+'" data-source="'+link_vat_source+'" data-url="'+url+'" data-title="Tình trạng">Không</a>';
            else
                var vat_name = '<a href="#" class="x_vat" data-type="select" data-value="published" data-pk="'+row_id+'" data-source="'+link_vat_source+'" data-url="'+url+'" data-title="Tình trạng">Có</a>';

            var html = load_row(row_id, type_value, stt, name, date_payment, price, unit, vat_name, vat, task_id, task_name,c_status);

            tr_element.replaceWith( html );
        }else {
            var count_row = $(table+' tbody tr[data-row]').length;
            if(count_row == 0) {
                $( table+' tbody').html('');
            }

            var stt = count_row + 1;
            var row_id = stt;

            if(vat == 'unpublished')
                var vat_name = '<a href="#" class="x_vat" data-type="select" data-value="unpublished" data-pk="'+row_id+'" data-source="'+link_vat_source+'" data-url="'+url+'" data-title="Tình trạng">Không</a>';
            else
                var vat_name = '<a href="#" class="x_vat" data-type="select" data-value="published" data-pk="'+row_id+'" data-source="'+link_vat_source+'" data-url="'+url+'" data-title="Tình trạng">Có</a>';

            var html = load_row(row_id, type_value, stt, name, date_payment, price, unit, vat_name, vat, task_id, task_name,c_status);
            $( table+' tbody' ).append( html);
        }

        do_x_select();
        $('#quick_modal').modal('toggle');
    }
}

function delete_contract_payment_without_db() {
    var table       = $('[data-table="contract_payment"]');
    var checkbox    = table.find('.file_checkbox:checked');
    var row_element = $('[data-table="contract_payment"] tbody tr[data-row]');

    var cid = new Array();
    $(checkbox).each(function( index ) {
        cid[cid.length] = $(this).val();
    });

    if(cid.length == 0) {
        toastr.warning('Phải chọn ít nhất 1 bản ghi', 'Cảnh báo');
    }else {
        bootbox.confirm('Bạn có chắc muốn xóa không?', function(result){
            if (result){
                $.each(cid, function( index, row_id ) {
                    $('[data-table="contract_payment"] tbody tr[data-row='+row_id+']').remove();
                });

                row_element = $('[data-table="contract_payment"] tbody tr[data-row]');
                if(row_element.length == 0)
                    table.find('tbody').html('<tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>');
                else {
                    row_element.each(function( index ) {
                        $(this).find('.file_checkbox').val(index + 1)
                        $(this).find('.stt').text(index + 1)
                    });
                }
            }
        });
    }
}

function delete_contract_payment_detail() {
    var cid = new Array();
    $(checkbox).each(function( index ) {
        cid[cid.length] = $(this).val();
    });
    if(cid.length == 0) {
        toastr.warning('Phải chọn ít nhất 1 bản ghi', 'Cảnh báo');
    }else {
        bootbox.confirm('Bạn có chắc muốn xóa không?', function(result){
            if (result){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        cid   : cid
                    },
                    success: function(string){
                        var result = $.parseJSON(string);
                        if(result.flag == 'warning')
                            toastr.warning(result.msg, 'Cảnh báo');
                        else if(result.flag == 'true') {
                            toastr.success(result.msg, 'Thông báo');
                            load_list(data_table);
                        }

                        manage_row.addClass('hidden');
                    }
                });
            }
        });
    }
}

function load_rule_contract_list(contract_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/rule_contract_info',
        data: {
            id : contract_id
        },
        success: function(string){
            var result = $.parseJSON(string);
            var items = result.payment;
            var date_start      = result.date_start;
            var date_signing    = result.date_signing;
            var date_expiration = result.date_expiration;
            var code            = result.code;

            $('#date_start').val(date_start);
            $('#date_signing').val(date_signing);
            $('#date_expiration').val(date_expiration);

            if(items) {
                var html_string = load_contract_payment_list(items);
                $('[data-table="contract_payment"] tbody').html(html_string);
                $('[data-tab="contract_payment"]').trigger('click');

                load_contract_other(result.id);

                do_x_select();
            }
        }
    });
}

function select_sale_order(sale_id, sale_code, customer_id) {
    bootbox.confirm('Bạn có chắc muốn chọn không?', function(result){
        if(result == true) {
            $('#sale_code').val(sale_code);
            $('#sale_id').val(sale_id);
            $('#my_table').modal('toggle');

            load_sale_order_html(sale_id);
            load_order_customer_info(sale_id);
        }
    });
}

function select_receiving_order(receiving_id, supplier_id) {
    bootbox.confirm('Bạn có chắc muốn chọn không?', function(result){
        if(result == true) {
            $('#receiving_code').val(receive_prefix + ' ' + receiving_id);
            $('#receiving_id').val(receiving_id);
            $('#my_table').modal('toggle');

            load_receiving_order_html(receiving_id);
            load_order_supplier_info(receiving_id);
        }
    });
}


function load_order_customer_info(sale_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'sales/ajax_order?type=customer',
        data: {
            sale_id   : sale_id
        },
        success: function(string){
            var res = $.parseJSON(string);
            if(res != null) {
                $('#customer_fullname').val(res.fullname);
                $('#customer_company_name').val(res.company_name);
                $('#customer_address').val(res.address);
                $('#customer_phone').val(res.phone_number);

                $('#customer_section').show();
            }else {
                $('#customer_fullname').val('');
                $('#customer_company_name').val('');
                $('#customer_address').val('');
                $('#customer_phone').val('');

                $('#customer_section').hide();
            }
        }
    });
}

function load_order_supplier_info(receiving_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'receivings/ajax_order?type=supplier',
        data: {
            receiving_id   : receiving_id
        },
        success: function(string){
            var res = $.parseJSON(string);
            if(res != null) {
                $('#supplier_fullname').val(res.fullname);
                $('#supplier_company_name').val(res.company_name);
                $('#supplier_address').val(res.address);
                $('#supplier_phone').val(res.phone_number);

                $('#supplier_section').show();
            }else {
                $('#supplier_fullname').val('');
                $('#supplier_company_name').val('');
                $('#supplier_address').val('');
                $('#supplier_phone').val('');

                $('#supplier_section').hide();
            }
        }
    });
}

function load_sale_order_html(sale_id) {{
    $.ajax({
        type: "POST",
        url: BASE_URL + 'sales/ajax_order?type=sale',
        data: {
            sale_id   : sale_id
        },
        success: function(string){
            $('table[data-table="contract_sales"]').replaceWith( string );
            $('[data-tab="contract_sales"]').trigger('click');

            var code = $( "#parent_id option:selected" ).attr('data-code');
            if(code)
                create_contract_code(sale_id, code);
        }
    });
}}

function load_receiving_order_html(receiving_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'receivings/ajax_order?type=receivings',
        data: {
            receiving_id   : receiving_id
        },
        success: function(string){
            $('table[data-table="contract_sales"]').replaceWith( string );
            $('[data-tab="contract_sales"]').trigger('click');

            var code = $( "#parent_id option:selected" ).attr('data-code');
            if(code)
                create_contract_code(receiving_id, code);
        }
    });
}

function load_contract_order_info(contract_id) {
   load_sale_info_form_contract(contract_id);

}

function load_sale_info_form_contract(contract_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_sale_info',
        data: {
            contract_id   : contract_id
        },
        success: function(html){
            $('table[data-table="contract_sales"] tbody').html(html);
        }
    });
}


function load_customer_from_contract(contract_id) {
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_sale_info?info=customer',
        data: {
            contract_id   : contract_id
        },
        success: function(html){
            $('#order_customer_info').html(html);
        }
    });
}

function load_contract_other(contract_id){
    $.ajax({
        type: "POST",
        url: BASE_URL + 'contracts/contract_other_info',
        data: {
            contract_id   : contract_id
        },
        success: function(string){
            var res = $.parseJSON(string);
            $('#note').val(res.note);

        }
    });
}

function show_payment_list(payment_id) {
    $.ajax({
        type: "GET",
        url: BASE_URL + 'contracts/contract_payment_detail_list',
        data: {
            payment_id : payment_id
        },
        success: function(html){
            $('#my_table').addClass('size-1200');
            $('#my_table').html(html);
            $('#my_table').modal('toggle');
        }
    });
}

function delivery_item_detail(delivery_id) {
    $.ajax({
        type: "GET",
        url: BASE_URL + 'contracts/contract_delivery_item_detail_list',
        data: {
            delivery_id : delivery_id
        },
        success: function(html){
            $('#my_table').addClass('size-1000');
            $('#my_table').html(html);
            $('#my_table').modal('toggle');
        }
    });
}

function reset_form_contract() {
    $('#name').val('');
    $('#code').val('');
    $('#date_signing').val('');
    $('#date_expiration').val('');
    $('#parent_id').val(-1);
    $('#status').val(-1);
    $('#sale_code').val('');
    $('#sale_id').val('');
}

function back_list() {
    if(list_type == 'list')
        var url_redirect = BASE_URL + "contracts/index/"+option;
    else
        var url_redirect = BASE_URL + "contracts/"+list_type+"/"+option;
    if(page > 1)
        url_redirect = url_redirect+'/'+page;

    window.location.href = url_redirect;
}

function create_contract_code(number_id, code) {
    var year = new Date().getFullYear().toString().substr(2,2);
    var month = new Date().getMonth()+1;
    var date = new Date().getDate().toString();

    if(code && number_id) {
        if(option == 'supplier'){
            var new_code = code + '-' + receive_prefix + number_id + '-' + year + month + date + last_id;
            $('#code').val(new_code);

        }else if(option == 'customer') {
            $.ajax({
                type: "POST",
                url: BASE_URL + 'contracts/get_contract_parttime_code',
                data: {
                    code : code
                },
                success: function(string){
                    var sale_code = $('#sale_code').val();
                    var new_code = string + '/' + sale_code;

                    $('#code').val(new_code);
                }
            });
        }
    }
}
