function loading(data_table) {
	$('#'+data_table+'_loading').css('display', 'inline-block');
}

function close_loading(data_table) {
	$('#'+data_table+'_loading').hide();
}

function load_template_mail_template(items) {
	if(items.length) {
		 var string = new Array();
		 $.each(items, function( index, value ) {
			  var id      				= value.mail_id;
			  var title      			= value.mail_title;

			  string[string.length] = '<tr style="cursor: pointer;">'
										+'<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'
										+'<td class="cb center">'+id+'</td>'
										+'<td class="cb">'+title+'</td>'
										+'<td class="center" style="padding: 4px;"><a href="javascript:;" onclick="edit_mail_template('+id+');">Sửa</a></td>'
									 +'</tr>';
		 });
		 
		 string = string.join("");	
	}else
		var string = '<tr style="cursor: pointer;"><td colspan="4"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

	 return string;
}

function load_template_constract_type(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			= value.id;
            var title      			= value.title;
            var code      			= value.code;
            var status      		= value.status;
            if(value['no-delete'] == 0){
                var checkbox_input = '<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>';
                var edit_input     = '<td class="center" style="padding: 4px;"><a href="javascript:;" onclick="edit_constract_type('+id+');">Sửa</a></td>';
            }else{
                var checkbox_input = '<td class="cb"></td>';
                var edit_input     = '<td class="center" style="padding: 4px;"></td>';
            }

            string[string.length] = '<tr style="cursor: pointer;">'
                                        + checkbox_input
                                        +'<td class="cb center">'+code+'</td>'
                                        +'<td class="cb">'+title+'</td>'
                                        +'<td class="cb center">'+status+'</td>'
                                        +edit_input
                                    +'</tr>';

        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="5"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_constract(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			        = value.id;
            var title      			        = value.title;
            if(!value.constract_type_name)
                var constract_type_name         = '';
            else
                var constract_type_name      	= value.constract_type_name;

            if(value.created == '00-00-0000 00:00:00')
                var created = '';
            else
                var created = value.created;

            string[string.length] = '<tr style="cursor: pointer;">'
                                        +'<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'
                                        +'<td class="cb">'+title+'</td>'
                                        +'<td class="cb">'+constract_type_name+'</td>'
                                        +'<td class="cb center">'+created+'</td>'
                                        +'<td class="center" style="padding: 4px;"><a href="javascript:;" onclick="edit_constract('+id+');">Sửa</a></td>'
                                    +'</tr>';

        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="4"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_pos_contract(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			        = value.id;
            if(option == 'customer') {
                if(value.sale_id == null) {
                    var number_id       = '';
                    var fullname        = '';
                }else {
                    var number_id       = value.sale_id;
                    var fullname        = value.customer_name;
                }

            }else if(option == 'supplier') {
                if(value.receiving_id == null) {
                    // var number_id       = '';
                    var fullname        = '';
                }else {
                    // var number_id       = value.supplier_id;
                    var fullname        = value.supplier_name;
                }
            }

            if(number_id != ''){
                var email = '<a href="javascript:;" onclick="send_mail_contract('+id+');">Gửi</a>';
            }else
                var email = '';

            var code      			        = value.code;
            var implement                   = value.implement == null ? "":value.implement;
            var join                        = value.join==null ? "":value.join;
            var name      			        = value.name;
            var customer_name      			= value.customer_name;
            var date_signing      			= value.date_signing;
            var type      			        = value.type;
            var status      			    = value.status;
            var item_name                   =(value.item_name) ? (value.item_name) : "";
            var price                       =value.price;
            var payment_price               =value.payment_price;

            if( value.receiving_id != null)
            {
              var  number_id                   = value.receiving_id;
            } 
            else 
            {
              var  number_id    = '';
            }
            // var number_id                   = value.receiving_id;

            string[string.length] = '<tr style="cursor: pointer;">'
                                        +'<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'
                                        +'<td class="cb">'+code+'</td>' // ma hop dong
                                        +'<td class="cb">'+name+'</td>' // ten hop dong
                                        +'<td class="cb center">'+item_name+'</td>'//Loai dich vu
                                        +'<td class="cb">'+customer_name+'</td>' // ten khach hang
                                        +'<td class="cb">'+price+'</td>' // Giá trị hợp đồng    
                                        +'<td class="cb">'+payment_price+'</td>' //Giá trị đã nghiệm thu/Thanh lý
                                        +'<td class="cb center">'+status+'</td>' // trang thái
                                        +'<td class="cb center">'+date_signing+'</td>' // ngay ky
                                        +'<td class="cb center">'+implement+'</td>' // nguoi phu trach
                                        +'<td class="center">'+join+'</td>' 
                                        +'<td class="center"><a href="javascript:;" onclick="download_contract_file('+id+');"><i class="ti-download"></i></a></td>'
                                        +'<td class="center"></td>' // ghi chu
                                        +'<td class="center" style="padding: 4px;"><a href="javascript:;" onclick="edit_contract('+id+');">Sửa</a></td>'
                                    +'</tr>';

        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="10"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_pos_circle_contract(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			        = value.id;
            if(option == 'customer') {
                if(value.sale_id == null) {
                    var number_id       = '';
                    var fullname        = '';
                }else {
                    var number_id       = value.sale_id;
                    var fullname        = value.customer_name;
                }

            }else if(option == 'supplier') {
                if(value.receiving_id == null) {
                    var number_id       = '';
                    var fullname        = '';
                }else {
                    var number_id       = value.supplier_id;
                    var fullname        = value.supplier_name;
                }
            }

            var code      			        = value.code;
            var name      			        = value.name;
            var customer_name      			= value.customer_name;
            var date_signing      			= value.date_signing;
            var in_or_out      			    = value.in_or_out;
            var status      			    = value.status;

            if(in_or_out == 'in')
                var class_tr = '';
            else
                var class_tr = ' class="warning"';

            if(number_id != ''){
                var email = '<a href="javascript:;" onclick="send_mail_contract('+id+');">Gửi</a>';
            }else
                var email = '';

            string[string.length] = '<tr style="cursor: pointer;"'+class_tr+'>'
                                        +'<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'
                                        +'<td class="cb">'+code+'</td>'
                                        +'<td class="cb">'+name+'</td>'
                                        +'<td class="cb">'+fullname+'</td>'
                                        +'<td class="cb">'+number_id+'</td>'
                                        +'<td class="cb center">'+date_signing+'</td>'
                                        +'<td class="cb center">'+status+'</td>'
                                        +'<td class="center"><a href="javascript:;" onclick="download_contract_file('+id+');">Tải File</a></td>'
                                        +'<td class="center">'+email+'</td>'
                                        +'<td class="center"><a href="javascript:;" onclick="handling_contract('+id+');">Xử lý</a></td>'
                                        +'<td class="center" style="padding: 4px;"><a href="javascript:;" onclick="edit_contract('+id+');">Sửa</a></td>'
                                    +'</tr>';

        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="10"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_pos_expired_contract(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			        = value.id;
            if(option == 'customer') {
                if(value.sale_id == null) {
                    var number_id       = '';
                    var fullname        = '';
                }else {
                    var number_id       = value.sale_id;
                    var fullname        = value.customer_name;
                }

            }else if(option == 'supplier') {
                if(value.receiving_id == null) {
                    var number_id       = '';
                    var fullname        = '';
                }else {
                    var number_id       = value.supplier_id;
                    var fullname        = value.supplier_name;
                }
            }

            var code      			        = value.code;
            var name      			        = value.name;
            var customer_name      			= value.customer_name;
            var date_signing      			= value.date_signing;
            var in_or_out      			    = value.in_or_out;
            var status      			    = value.status;

            if(in_or_out == 'in')
                var class_tr = '';
            else
                var class_tr = ' class="warning"';

            if(number_id != ''){
                var email = '<a href="javascript:;" onclick="send_mail_contract('+id+');">Gửi</a>';
            }else
                var email = '';

            string[string.length] = '<tr style="cursor: pointer;"'+class_tr+'>'
                                        +'<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'
                                        +'<td class="cb">'+code+'</td>'
                                        +'<td class="cb">'+name+'</td>'
                                        +'<td class="cb">'+fullname+'</td>'
                                        +'<td class="cb">'+number_id+'</td>'
                                        +'<td class="cb center">'+date_signing+'</td>'
                                        +'<td class="cb center">'+status+'</td>'
                                        +'<td class="center"><a href="javascript:;" onclick="download_contract_file('+id+');">Tải File</a></td>'
                                        +'<td class="center">'+email+'</td>'
                                        +'<td class="center"><a href="javascript:;" onclick="handling_expired_contract('+id+');">Xử lý</a></td>'
                                        +'<td class="center" style="padding: 4px;"><a href="javascript:;" onclick="edit_contract('+id+');">Sửa</a></td>'
                                    +'</tr>';

        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="10"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_expenses(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			        = value.id;
            var expenses_date      			= value.expenses_date;
            var expense_amount      	    = value.expense_amount;
            var expense_description      	= value.expense_description;
            if(!value.fullname)
                var fullname         = '';
            else
                var fullname      	 = value.fullname;

            string[string.length] = '<tr style="cursor: pointer;">'
                                        +'<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'
                                        +'<td class="cb center">'+expenses_date+'</td>'
                                        +'<td class="cb center">'+expense_amount+'</td>'
                                        +'<td class="cb">'+expense_description+'</td>'
                                        +'<td class="cb center">'+fullname+'</td>'
                                        +'<td class="center" style="padding: 4px;"><a href="'+BASE_URL+'expenses/export_excel/'+id+'">Xuất tệp</a></td>'
                                    +'</tr>';

        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_shift_expenses_expensive(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			        = value.id;
            var expense_date      			= value.expense_date;
            var expense_amount      	    = value.expense_amount;
            var expense_description      	= value.expense_description;
            if(!value.fullname)
                var fullname         = '';
            else
                var fullname      	 = value.fullname;

            string[string.length] = '<tr class=""><th class="st-head-row" colspan="2"><div style="font-weight: bold;">'+expense_description+'</div></th></tr>'
                                    +'<tr class=""><td class="st-key">Số tiền: </td><td class="st-val ">'+expense_amount+'</td></tr>'
                                    +'<tr class=""><td class="st-key">Thời gian: </td><td class="st-val ">'+expense_date+'</td></tr>'
                                    +'<tr class=""><td class="st-key">Người nhận: </td><td class="st-val ">'+fullname+'</td></tr>';

        });

        string = string.join("");
    }else
        var string = '<tr class=""><th class="st-head-row" colspan="2" text-algin="center">Không có dữ liệu hiển thị</th></tr>';

    string = '<tr class="first"><th class="st-head-row st-head-row-main" colspan="2">Mô tả</th></tr>' + string;

    return string;
}

function load_template_shift_expenses(items) {
    if(items.length) {
        var string = new Array();
        $.each(items, function( index, value ) {
            var id      			        = value.id;
            var expense_date      			= value.expense_date;
            var expense_amount      	    = value.expense_amount;
            var expense_description      	= value.expense_description;
            if(!value.fullname)
                var fullname         = '';
            else
                var fullname      	 = value.fullname;

            string[string.length] = '<tr>'
                                        +'<td class="center">'+expense_date+'</td>'
                                        +'<td>'+expense_description+'</td>'
                                        +'<td class="right">'+expense_amount+'</td>'
                                        +'<td class="center bold">'+fullname+'</td>'
                                    +'</tr>';

        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="4"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_sale_modal(items) {
    if(items.length) {
        var string = new Array();
        var sale_id_selected = $('#sale_id').val();

        $.each(items, function( index, value ) {
            if(value.code == null || value.code == '')
                var sale_id         = value.code_don_hang;
            else
                var sale_id         = value.code;
            var linkDetail      = BASE_URL + 'sales/receipt/'+value.sale_id;
            var sale_time      	= value.sale_time_format;
            var total      	    = value.total_all;
            var sale_tax      	= value.tax_info;
            var discount      	= value.discount_total;
            var customer_name   = value.customer_name;
            var customer_id     = value.customer_id;
            if(sale_id_selected != value.sale_id) {
                if(value.select == 'true')
                    var selected_option = '<a href="javascript:;" onclick="select_sale_order('+value.sale_id+', \''+sale_id+'\', '+customer_id+');">Chọn</a>';
                else
                    var selected_option = '&nbsp';
            }
            else {
                var selected_option = '&nbsp';
            }

            string[string.length] = '<tr>'+
                                        '<td><a href="'+linkDetail+'" target="_blank">'+sale_id+'</a></td>'+
                                        '<td class="center">'+sale_time+'</td>'+
                                        '<td>'+customer_name+'</td>'+
                                        '<td class="right">'+total+'</td>'+
                                        '<td>'+sale_tax+'</td>'+
                                        '<td class="right">'+discount+'</td>'+
                                        '<td class="center" style="padding: 4px;">'+selected_option+'</td>'+
                                    '</tr>';
        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_receiving_modal(items) {
    if(items.length) {
        var string = new Array();
        var receiving_id_selected = $('#receiving_id').val();
        $.each(items, function( index, value ) {
            var id = value.receiving_id;
            var receiving_id    = value.ma_don_nhap_hang;
            var linkDetail      = BASE_URL + 'receivings/receipt/'+value.receiving_id;
            var receiving_time  = value.receiving_time_format;
            var total      	    = value.total_all;
            var sale_tax      	= value.tax_info;
            var discount      	= value.discount_total;
            var supplier_id     = value.supplier_id;
            var supplier_name   = value.supplier_name;
            if(supplier_name == null)
                supplier_name = '';
            
            if(receiving_id_selected != value.receiving_id) {
                if(value.select == 'true')
                    var selected_option = '<a href="javascript:;" onclick="select_receiving_order(\''+ id +'\', '+supplier_id+');">Chọn</a>';
                else
                    var selected_option = '&nbsp';
            }
            else {
                var selected_option = '&nbsp';
            }

            string[string.length] = '<tr>'+
                                        '<td><a href="'+linkDetail+'" target="_blank">'+receiving_id+'</a></td>'+
                                        '<td class="center">'+receiving_time+'</td>'+
                                        '<td>'+supplier_name+'</td>'+
                                        '<td class="right">'+total+'</td>'+
                                        '<td>'+sale_tax+'</td>'+
                                        '<td class="right">'+discount+'</td>'+
                                        '<td class="center" style="padding: 4px;">'+selected_option+'</td>'+
                                    '</tr>';
        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_contract_payment(items) {
    var type = $('#type').val();
    if(type == 'rule')
        var colspan = 5;
    else
        var colspan = 6;

    if(items.length) {
        var string = new Array();
        var link_vat_source = BASE_URL + 'ajax/select_vat';
        var url = BASE_URL + 'contracts/contract_payment_vat';
        var stt = 0;

        $.each(items, function( index, value ) {
            var id      	    = value.id;
            var name      	    = value.name;
            var date_payment   	= (value.date_payment_format !=null) ? value.date_payment_format : "" ;
            var price      	    = value.price;
            var vat             = value.vat;
            var payment_price   = value.payment_price;
            var task_name		= (value.task_name==null) ? "": value.task_name;
            var c_status        = value.c_status;
            stt++;
            var payment_td = '';
            var rule_td = '';
            
            if(type != 'rule') {
                if(vat == 'unpublished')
                    var vat_name = '<a href="#" class="x_vat" data-type="select" data-value="unpublished" data-pk="'+id+'" data-source="'+link_vat_source+'" data-url="'+url+'" data-title="Tình trạng">Không</a>';
                else if(vat == 'published')
                    var vat_name = '<a href="#" class="x_vat" data-type="select" data-value="published" data-pk="'+id+'" data-source="'+link_vat_source+'" data-url="'+url+'" data-title="Tình trạng">Có</a>';

                rule_td = '<td class="cb center">'+vat_name+'</td>';
                payment_td = '<td class="right"><a href="javascript:;" onclick="show_payment_list('+id+')">'+payment_price+'</a></td>';
            }else {
                payment_td = '';
                rule_td = '';
            }

            if(c_status=="done")
            {
                c_status = '<td class="cb center"> Đã nghiệm thu</td>';
            }
            else if(c_status=="liquidated")
            {
                c_status = '<td class="cb center"> Đã thanh lý</td>';
            }
            else
            {
                c_status = '<td class="cb center">Chưa nghiệm thu</td>';
            }
            string[string.length] = '<tr style="cursor: pointer;">'+
                                        '<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'+
                                        '<td class="cb center">'+stt+'</td>'+
                                        '<td class="cb">'+name+'</td>'+
                                        '<td class="cb">'+task_name+'</td>'+
                                        '<td class="cb center">'+date_payment+'</td>'+
                                        '<td class="cb center">'+price+'</td>'+
                                        c_status+
                                        rule_td+
                                        '<td class="center"><a href="javascript:;" onclick="contract_payment_frm('+id+');">Sửa</a></td>'+
                                    '</tr>';
        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="'+colspan+'"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_contract_payment_detail(items) {
    if(items.length) {
        var string = new Array();

        $.each(items, function( index, value ) {
            var id      	    = value.id;
            var name      	    = value.name;
            var price      	    = value.price;
            var note            = value.note;

            string[string.length] = '<tr style="cursor: pointer;">'+
                                        '<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'+
                                        '<td class="cb center">'+id+'</td>'+
                                        '<td class="cb">'+name+'</td>'+
                                        '<td class="right">'+price+'</td>'+
                                        '<td class="cb">'+note+'</td>'+
                                        '<td class="center"><a href="javascript:;" onclick="contract_payment_detail_frm('+id+');">Sửa</a></td>'+
                                    '</tr>';
        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}

function load_template_contract_payment_delivery(items) {
    if(items.length) {
        var string = new Array();
        var stt = 0;

        $.each(items, function( index, value ) {
            var id      	    = value.id;
            var payment_name    = value.payment_name;
            var date      	    = value.date_format;
            var company_name    = value.company_name;
            var address         = value.address;
            stt++;

            string[string.length] = '<tr style="cursor: pointer;">'+
                                        '<td class="cb"><input type="checkbox" value="'+id+'" class="file_checkbox"><label><span></span></label></td>'+
                                        '<td class="cb center">'+stt+'</td>'+
                                        '<td class="cb">'+payment_name+'</td>'+
                                        '<td class="center">'+date+'</td>'+
                                        '<td class="cb">'+company_name+'</td>'+
                                        '<td class="cb">'+address+'</td>'+
                                        '<td class="center"><a onclick="delivery_item_detail('+id+');" href="javascript:;">Chi tiết</a></td>'+
                                        '<td class="center"><a href="javascript:;" onclick="contract_delivery_frm('+id+');">Sửa</a></td>'+
                                    '</tr>';
        });

        string = string.join("");
    }else
        var string = '<tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>';

    return string;
}
function load_responsive_template(items, data_table) {
    switch (data_table){
        case 'shift_expenses' : {
            var html_string = load_template_shift_expenses_expensive(items);
            break;
        }
    }

    return html_string;
}

function load_template(items, data_table) {

	switch (data_table){
	    case 'mail_template' : {
	    	var html_string = load_template_mail_template(items);
	    	break;
	    }
        case 'constract_type' : {
            var html_string = load_template_constract_type(items);
            break;
        }

        case 'constract' : {
            var html_string = load_template_constract(items);
            break;
        }

        case 'expenses' : {
            var html_string = load_template_expenses(items);
            break;
        }

        case 'shift_expenses' : {
            var html_string = load_template_shift_expenses(items);
            break;
        }

        case 'pos_contract' : {
            var html_string = load_template_pos_contract(items);
            break;
        }

        case 'pos_circle_contract' : {
            var html_string = load_template_pos_circle_contract(items);
            break;
        }

        case 'pos_expired_contract' : {
            var html_string = load_template_pos_expired_contract(items);
            break;
        }

        case 'sale_modal' : {
            var html_string = load_template_sale_modal(items);
            break;
        }

        case 'receiving_modal' : {
            var html_string = load_template_receiving_modal(items);
            break;
        }

        case 'contract_payment' : {
            var html_string = load_template_contract_payment(items);
            break;
        }

        case 'contract_payment_detail' : {
            var html_string = load_template_contract_payment_detail(items);
            break;
        }

        case 'contract_delivery' : {
            var html_string = load_template_contract_payment_delivery(items);
            break;
        }
	}

	return html_string;
}

function load_pagination(pagination, template) {
	if(jQuery.type(pagination) == 'object') {
		var string = new Array();
		$.each( pagination, function( key, page ) {
			if(key == 'prev')
				string[string.length] = '<a href="javascript:;" data-page="'+page+'">&lt;</a>';
			else if(key == 'next')
				string[string.length] = '<a href="javascript:;" data-page="'+page+'">&gt;</a>';
			else if(key == 'current')
				string[string.length] = '<strong>'+page+'</strong>';
			else 
				string[string.length] = '<a href="javascript:;" data-page="'+page+'">'+page+'</a>';
		});

		string = string.join("");
		string = '<div class="pagination hidden-print alternate text-center">' + string + '</div>';

		return string;
	}else
		return '';
}

function load_list(data_table, page, no_load) {
	var data = new Object();
	var search = $('.data-n9-s');
	if(search.length) {
		search.each(function (index, value) { 
			 data_t = $(value).attr('data-table');
			 name = $(value).attr('name');
			 if(data_t == data_table) {
				 switch (name)
				 {
				     case 's_keywords' : {
				         data.keywords = $(value).val();
				         break;
				     }

                     case 's_category_id' : {
                         data.category_id = $(value).val();
                         break;
                     }

                     case 's_start_date' : {
                         data.start_date = $(value).val();
                         break;
                     }

                     case 's_end_date' : {
                         data.end_date = $(value).val();
                         break;
                     }

                     case 's_location_ids' : {
                         data.location_ids = $(value).val();
                         break;
                     }

                     case 's_type' : {
                         data.type = $(value).val();
                         break;
                     }

                     case 's_contract_id' : { 
                         data.contract_id = $(value).val();
                         break;
                     }

                     case 's_payment_id' : {
                         data.payment_id = $(value).val();
                         break;
                     }

                     case 's_option' : {
                         data.option = $(value).val();
                         break;
                     }

                     case 's_options' : {
                         data.options = $(value).val();
                         break;
                     }

                     case 's_customer_id' : {
                         data.customer_id = $(value).val();
                         break;
                     }

                     case 's_supplier_id' : {
                         data.supplier_id = $(value).val();
                         break;
                     }

                     case 's_tier_id' : {
                         data.tier_id = $(value).val();
                         break;
                     }

                     case 's_employee_id' : {
                         data.employee_id = $(value).val();
                         break;
                     }

                     case 's_customer_type' : {
                         data.customer_type = $(value).val();
                         break;
                     }

                     case 's_group_ids' : {
                         data.group_ids = $(value).val();
                         break;
                     }

                     case 's_employees' : {
                         data.employees = $(value).val();
                         break;
                     }
                     // thêm phần gửi lấy danh mục con
                     case 's_category_id' : {
                         data.category_id = $(value).val();
                         break;
                     }

                     case 's_category_child' : {
                         data.category_child = $(value).val();
                         break;
                     }
                     case 's_tai_khoan' : {
                         data.tai_khoan = $(value).val();
                         break;
                     }
                     case 's_customer_balance_options' : {
                         data.customer_balance_options = $(value).val();
                         break;
                     }

				 }
			 }
		});
	}

	if(page == null) {
	   var tableElement = $('table[data-table="'+data_table+'"]'); 
	   var current_page = tableElement.attr('data-currentPage');

	   page = current_page;
	}

	var tableElement             = $('table[data-table="'+data_table+'"]');
    var enable_scroll            = tableElement.attr('data-scroll');
    var callback                 = tableElement.attr('data-callback');
    var responsive_table_element = $('table[data-responsive-table="'+data_table+'"]');
	var url = tableElement.attr('data-url');

	// get field sort
	var elementSort = tableElement.find('th.header');
	if(elementSort.length){
		if(elementSort.hasClass('headerSortUp')){
			data.col   = elementSort.attr('data-field');
			data.order = 'ASC';
		}else {
			data.col   = elementSort.attr('data-field');
			data.order = 'DESC';
		}
	}


    var newSort_ui = tableElement.find('tr.ui-droppable');
    var the_sap_xep = newSort_ui.find('i.sap-xep.fa-sort-asc');
    var the_th = the_sap_xep.parent().parent();

    if(newSort_ui.children().length){
        if(the_sap_xep.hasClass('fa-sort-asc')){
            data.col   = the_th.attr('data-field');
            data.order = 'ASC';

        } else {
            data.col   = the_th.attr('data-field');
            data.order = 'DESC';
        }
    }

	data.page = page;

    $.ajax({
		type: "POST",
		url: url + page,
		data: data,
		beforeSend: function() {
            if(no_load != 'disabled')
                loading(data_table);
       },
		success: function(string){
            var result = $.parseJSON(string);
            if(no_load != 'disabled')
			    close_loading(data_table);

            var result = $.parseJSON(string);
            var pagination = result.pagination;
            var items = result.items;
            if(result.hasOwnProperty("html_string")){
                var html_string = result.html_string;
            }else 
				var html_string            = load_template(items, data_table);
           

            var pagination             = load_pagination(pagination, data_table);
          
            // large table
						if(data_table == 'summary_commission')
						{
							tableElement.html(html_string);
						}
						else
						{
							tableElement.find('tbody').html(html_string);
						}
           

            // responsive table
            if(responsive_table_element.length){
                var html_responsive_string = load_responsive_template(items, data_table);
                responsive_table_element.find('tbody').html(html_responsive_string);
            }

            if(result.hasOwnProperty("count_total")){
                $.each(result.count_total, function( index, value ) {
                    $('#'+index).text(value);
                });

            }else
						{
							$('#count_'+data_table).text(result.count);
							
							$('body .new_contracts_count').text(result.new_contracts_count);
						}
                

            tableElement.attr('data-currentpage', page);
            $('div.data-n9-pagination[data-table="'+data_table+'"]').html(pagination);

            if(current_page > 1 && enable_scroll != 'false') {
                $('body').animate({scrollTop: ($('table[data-table="'+data_table+'"]').offset().top) + 'px'}, 500);
            }

            if(callback == 'true') {
                n9_grid_callback(data_table,result);
            }

            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
            $('table[data-table="'+data_table+'"] thead input[type="checkbox"]').prop('checked', false);
            // danh mục con
            if(result.category_child){
                $("#s_category_child").animate({"opacity": "0"}, 500).stop();
                $("#s_category_child")
                .stop() // Stop on-going animation
                .animate({"opacity": "0"}, 500, function() {
                    // After first animation finished
                    $(this).html(result.category_child).animate({ opacity: 1 });
                });
                
            }
            if(result.select_columns){
                $('.custom-menu').html(result.select_columns);
            }
            // x-editable
            do_editable(data_table);
	    }
	});
}
	/* 
	*luongpham
	*load ajax kho
	*02/06/2017 
	*/
    function load_list_item(data_table, page, no_load , child) {
	var data = new Object();
	var search = $('.data-n9-s');
	if(search.length) {
		search.each(function (index, value) { 
			data_t = $(value).attr('data-table');
			name = $(value).attr('name');
            data.category_child = -1;
			if(data_t == data_table) {
                
				switch (name)
				{
					case 's_keywords' : {
						data.keywords = $(value).val();
						break;
					}
					
					case 's_category_id' : {
						data.category_id = $(value).val();
						break;
					}
					
					case 's_location_ids' : {
						data.location_ids = $(value).val();
						break;
					}          
					
					// thêm phần gửi lấy danh mục con
					case 's_category_id' : {
						data.category_id = $(value).val();
						break;
					}
					
					case 's_category_child' : {
                        if (child == -1) {
                        data.category_child = $(value).val();   
                        break; 
                        } 
                        data.category_child = -1; 
					}
					
				}
			}
		});
	}

	if(page == null) {
		var tableElement = $('table[data-table="'+data_table+'"]'); 
		var current_page = tableElement.attr('data-currentPage');
		
		page = current_page;
	}
	
	var tableElement             = $('table[data-table="'+data_table+'"]');
	var enable_scroll            = tableElement.attr('data-scroll');
	var callback                 = tableElement.attr('data-callback');
	var responsive_table_element = $('table[data-responsive-table="'+data_table+'"]');
	var url = tableElement.attr('data-url');
	
	// get field sort
	var elementSort = tableElement.find('th.header');
	if(elementSort.length){
		if(elementSort.hasClass('headerSortUp')){
			data.col   = elementSort.attr('data-field');
			data.order = 'ASC';
			}else {
			data.col   = elementSort.attr('data-field');
			data.order = 'DESC';
		}

    }


	data.page = page;

    console.log(data);
	$.ajax({
		type: "POST",
		url: url + page,
		data: data,
		beforeSend: function() {
			if(no_load != 'disabled')
			loading(data_table);
		},
		success: function(string){

			a
			var result = $.parseJSON(string);
			if(no_load != 'disabled')
			close_loading(data_table);
			
			var result = $.parseJSON(string);
			var pagination = result.pagination;
			var items = result.items;
			
			if(result.hasOwnProperty("html_string")){
				var html_string = result.html_string;
			}else 
			var html_string            = load_template(items, data_table);
			
			var pagination             = load_pagination(pagination, data_table);
			
			// large table
			tableElement.find('tbody').html(html_string);
			
			// responsive table
			if(responsive_table_element.length){
				var html_responsive_string = load_responsive_template(items, data_table);
				responsive_table_element.find('tbody').html(html_responsive_string);
			}
			
			if(result.hasOwnProperty("count_total")){
				$.each(result.count_total, function( index, value ) {
					$('#'+index).text(value);
				});
				
			}else
			$('#count_'+data_table).text(result.count);
			
			tableElement.attr('data-currentpage', page);
			$('div.data-n9-pagination[data-table="'+data_table+'"]').html(pagination);
			
			if(current_page > 1 && enable_scroll != 'false') {
				$('body').animate({scrollTop: ($('table[data-table="'+data_table+'"]').offset().top) + 'px'}, 500);
			}
			
			if(callback == 'true') {
				n9_grid_callback(data_table,result);
			}
			
			$('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
			$('table[data-table="'+data_table+'"] thead input[type="checkbox"]').prop('checked', false);
			
			// danh mục con
			if(result.category_child ){
				$("#s_category_child").animate({"opacity": "0"}, 500).stop();
				$("#s_category_child")
				.stop() // Stop on-going animation
				.animate({"opacity": "0"}, 500, function() {
					// After first animation finished
					$(this).html(result.category_child).animate({ opacity: 1 });
				});
				
			}
           
            if(result.select_columns){
                $('.custom-menu').html(result.select_columns);
            }
			// x-editable
			do_editable(data_table);
		}
	});
}		

/* 
	*luongpham
	*load ajax chi phí
	*02/06/2017 
	*/
function load_list_expenses(data_table, page, no_load) {
	var data = new Object();
    var search = $('.data-n9-s');
    if(search.length) {
        search.each(function (index, value) { 
            data_t = $(value).attr('data-table');
            name = $(value).attr('name');

            if(data_t == data_table) {
                switch (name)
                {
                    case 's_keywords' : {
                        data.keywords = $(value).val();
                        break;
                    }
             
                    case 's_location_ids' : {
                        data.location_ids = $(value).val();
                        break;
                    }          
                }
            }
        });
    }
    
    if(page == null) {
        var tableElement = $('table[data-table="'+data_table+'"]'); 
        var current_page = tableElement.attr('data-currentPage');
        
        page = current_page;
    }
    
    var tableElement             = $('table[data-table="'+data_table+'"]');
    var enable_scroll            = tableElement.attr('data-scroll');
    var callback                 = tableElement.attr('data-callback');
    var responsive_table_element = $('table[data-responsive-table="'+data_table+'"]');
    var url = tableElement.attr('data-url');
    
    // get field sort
    var elementSort = tableElement.find('th.header');
    if(elementSort.length){
        if(elementSort.hasClass('headerSortUp')){
            data.col   = elementSort.attr('data-field');
            data.order = 'ASC';
            }else {
            data.col   = elementSort.attr('data-field');
            data.order = 'DESC';
        }
    }
    
    data.page = page;

    $.ajax({
        type: "POST",
        url: url + page,
        data: data,
        beforeSend: function() {
            if(no_load != 'disabled')
            loading(data_table);
        },
        success: function(string){

            
            var result = $.parseJSON(string);
            if(no_load != 'disabled')
            close_loading(data_table);
            
            var result = $.parseJSON(string);
            var pagination = result.pagination;
            var items = result.items;
            
            if(result.hasOwnProperty("html_string")){
                var html_string = result.html_string;
            }else
            var html_string            = load_template(items, data_table);
            
            var pagination             = load_pagination(pagination, data_table);
            
            // large table
            tableElement.find('tbody').html(html_string);
            
            // responsive table
            if(responsive_table_element.length){
                var html_responsive_string = load_responsive_template(items, data_table);
                responsive_table_element.find('tbody').html(html_responsive_string);
            }
            
            if(result.hasOwnProperty("count_total")){
                $.each(result.count_total, function( index, value ) {
                    $('#'+index).text(value);
                });
                
            }else
            $('#count_'+data_table).text(result.count);
            
            tableElement.attr('data-currentpage', page);
            $('div.data-n9-pagination[data-table="'+data_table+'"]').html(pagination);
            
            if(current_page > 1 && enable_scroll != 'false') {
                $('body').animate({scrollTop: ($('table[data-table="'+data_table+'"]').offset().top) + 'px'}, 500);
            }
            
            if(callback == 'true') {
                n9_grid_callback(data_table,result);
            }
            
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
            $('table[data-table="'+data_table+'"] thead input[type="checkbox"]').prop('checked', false);
            
            // danh mục con
            if(result.category_child){
                $("#s_category_child").animate({"opacity": "0"}, 500).stop();
                $("#s_category_child")
                .stop() // Stop on-going animation
                .animate({"opacity": "0"}, 500, function() {
                    // After first animation finished
                    $(this).html(result.category_child).animate({ opacity: 1 });
                });
                
            }
            // x-editable
            do_editable(data_table);
        }
    });
}                

function do_editable(data_table) {
    if(data_table == 'contract_payment') {
        $('.x_vat').editable({
            success: function(response, newValue) {
                toastr.success('Cập nhật thành công', 'Thông báo');
                load_list('contract_payment');
            }
        });
    }else if(data_table == 'sale_store_payment_modal' || data_table == 'receiving_store_payment_modal') {
        $('.x_update_store_payment').editable({
            success: function(response, newValue) {
                var res = $.parseJSON(response);
                if(res.flag == 'false') {
                    toastr.error(res.msg, 'Lỗi');
                }else {
                    toastr.success('Thành công.', 'Thông báo');
                }

                load_list(data_table);
            }
        });
    }
}

$( document ).ready(function() {
	//pagination
	$('body').on('click','div.data-n9-pagination .pagination a',function(){
		var data_table = $(this).closest('div.data-n9-pagination').attr('data-table');	
		var page = $(this).attr('data-page');

		load_list(data_table, page);
	});
	
	// sort
	// $('body').on('click','table.data-n9-table th',function(){
	// 	var table = $(this).closest('.data-n9-table');
	// 	var data_table = table.attr('data-table');
	// 	var thElement = table.find('th');
	// 	var attr = $(this).attr('data-field');
	// 	if (typeof attr !== typeof undefined && attr !== false) {
	// 	   if($(this).hasClass('header')) {
	// 		   if($(this).hasClass('headerSortUp')){
	// 			   $(this).removeClass('headerSortUp');
	// 			   $(this).addClass('headerSortDown');
	// 		   }else {
	// 			   $(this).removeClass('headerSortDown');
	// 			   $(this).addClass('headerSortUp');
	// 		   }
	// 	   }else {
	// 		   thElement.removeClass('header');
	// 		   thElement.removeClass('headerSortUp');
	// 		   thElement.removeClass('headerSortDown');
	// 		   $(this).addClass('header headerSortUp');
	// 	   }
    //
	// 	   load_list(data_table, 1);
	// 	}
	// });
	
	// checkbox	
	$('body').on('click','table.data-n9-table tbody tr td.cb',function(){

		 var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
		 var table = checkbox.closest('table.data-n9-table');
		 var data_table = table.attr('data-table');
		 if (checkbox.prop('checked') == true){ 
			  checkbox.prop('checked', false);
		 }else{
			 $('.manage-row-options').show();
			 checkbox.prop('checked', true);
		 }

		var checked_box = table.find('.file_checkbox:checked');
		if(checked_box.length == 0) 
			$('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
		else
			$('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
    });
	
	// check all
	$('body').on('click','table.data-n9-table label.check_tatca',function(){
        console.log('ducang')
		  var checkbox = $(this).closest('th').find('input[type="checkbox"]'); 
		  var table = checkbox.closest('table.data-n9-table');
		  var data_table = table.attr('data-table');

		  if (checkbox.prop('checked') == true){ 
			 $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
			 checkbox.prop('checked', false);
			 table.find('td input[type="checkbox"]').prop('checked', false);
		  }else{
			  $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
			  checkbox.prop('checked', true);
			  table.find('td input[type="checkbox"]').prop('checked', true);
		  }
    });

   

    // delete
    $('body').on('click','.btn-red[data-table]',function(){
        var data_table = $(this).attr('data-table');
        var url        = $(this).attr('data-url');
        var table      = $('table[data-table="'+data_table+'"]');
        var checkbox   = table.find('.file_checkbox:checked');
        var manage_row = $(this).parents('.manage-row-options');

        var cid = new Array();
        $(checkbox).each(function( index ) {
            cid[cid.length] = $(this).val();
        });

        var data = new Object();
        data.cid = cid;

        var param = $(this).attr('data-param');
        var reload = $(this).attr('data-table-reload');

        if (typeof param !== typeof undefined && param !== false) {
            data.param = param;
        }

        if(cid.length == 0) {
            toastr.warning('Phải chọn ít nhất 1 bản ghi', 'Cảnh báo');
        }else {
            bootbox.confirm('Bạn có chắc muốn xóa không?', function(result){
                if (result){
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: function(string){
                            var result = $.parseJSON(string);
                            if(result.flag == 'warning')
                                toastr.warning(result.msg, 'Cảnh báo');
                            else if(result.flag == 'true') {
                                toastr.success(result.msg, 'Thông báo');
                                load_list(data_table);
                                if (typeof reload !== typeof undefined && reload !== false) {
                                    load_list(reload);
                                }
                            }

                            manage_row.addClass('hidden');
                        }
                    });
                }
            });
        }
    });
});