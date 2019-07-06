$(document).ready(function() {
	$(document).on('click', '.btn-create-project', function() {
		var saleId = $(this).attr('dataid');
		var DATE_FORMAT = "DD-MM-YYYY";
		//reset_error();
        $.ajax({
            url : BASE_URL+'tasks/add_task_by_sale',
            data: {
            	parent: 0,
            	project_id: 0,
            	current_type: 'new',
            	sale_id: saleId
            },
            success: function(data) {
            	if (data == 0) {
            		bootbox.alert("Cần cập nhật tên Khách hàng trước khi tạo dự án.");
            		return false;
            	} else if (data == 1) {
            		bootbox.alert("Nhu cầu này không thể tạo dự án");
            		return false;
            	}
            	$('#my_model').empty();
            	$('#my_modal').html(data);
    			$('#my_modal').modal('toggle');
//    			$('#date_start').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss', locale: 'vi_VN'});
//    			$('#date_end').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss', locale: 'vi_VN'});

    			date_time_picker_field_report($('#date_start'), DATE_FORMAT);
                date_time_picker_field_report($('#date_end'), DATE_FORMAT);
    			
    			var frame_array = ['xem_list','join_list', 'create_task_list', 'pheduyet_task_list', 'progress_list'];
                $.each(frame_array, function( index, value ) {
                    css_form(value);
                    press(value);
                });
            },
            error: function(err) {
            	console.log(err.message);
            }
        });
	});
});

function press(frame_id) {
   if($('#'+frame_id).length) {
	   var typingTimer;                
	   var doneTypingInterval = 200;  

	   $('#'+frame_id+' .quick_search').on('keyup', function () {
		   clearTimeout(typingTimer);
		   typingTimer = setTimeout(function(){
			   doneTyping(frame_id)
		    },doneTypingInterval);

		 });

	   //on keydown, clear the countdown 
	   $('#'+frame_id+' .quick_search').on('keydown', function () {
	   	  clearTimeout(typingTimer);
	   });
   }
}

function css_form(obj_id) {
    if($('#'+obj_id).length) {
	   var top = $("#"+obj_id+" .quick_search").offset().top - $("#"+obj_id).offset().top + 20;

	   var styles = {
	      top : top + 'px'
	   };
	   
	   $("#"+obj_id+" .result").css( styles );	
    }
}

function reset_error() {
	$('#my_modal .form-control').removeClass('has-error');
	$('#my_modal span.errors').text('');
	$('#quick-form .form-control').removeClass('has-error');
	$('#quick-form span.errors').text('');
}

function foucs(obj) {
	$(obj).find('.quick_search').focus();
}

function doneTyping(frame_id) {
    switch(frame_id) {
        case 'customer_list':
            var url = BASE_URL + 'tasks/customerList';
            break;
        case 'trangthai_list':
            var url = BASE_URL + 'tasks/trangthaiList';
            break;
        default:
            var url = BASE_URL + 'tasks/userList';
    }

	$('#'+frame_id+' .result').html('');
	$('#'+frame_id+' .result').hide();
	var keywords = $.trim($('#'+frame_id+' .quick_search').val());

	if (keywords) {
		$.ajax({
			type: "POST",
			url: url,
			data: {
				keywords : keywords
			},
			success: function(string){
				array = $.parseJSON(string);
				css_form(frame_id);
				if(array.length) {
					var html = new Array();
					$.each(array, function( index, value ) {
						if(frame_id == 'customer_list' || frame_id == 'trangthai_list')
							html[html.length] = '<li><a href="javascript:;" data-id="'+value.id+'" data-name="'+value.name+'" onclick="add_item(this, \''+frame_id+'\');">'+value.name+'</a></li>';
						else
							html[html.length] = '<li><a href="javascript:;" data-id="'+value.id+'" data-name="'+value.name+'" onclick="add_item(this, \''+frame_id+'\');">'+value.name+' - '+value.fullname+'</a></li>';
					});

					html = html.join('');
					html = '<ul class="list">'+html+'</ul>'; 

					$('#'+frame_id+' .result').html(html);
					$('#'+frame_id+' .result').show();
				}
		    }
		});
	}
}

function cancel(typeP, type) {
	if(typeP == 'quick') {
		$('#quick-form').html('');
		$('#quick-form').hide();

		close_layer('quick');
	}else {
        $('#my_modal').modal('toggle');
	}
}

function add_item(obj, frame_id) {
    var item_name = $(obj).attr('data-name');
    var item_id   = $(obj).attr('data-id');
    var array = new Array();
    array['customer_list'] 	    = 'customer';
    array['trangthai_list'] 	= 'trangthai';
    array['xem_list'] 		    = 'xem';
    array['implement_list']     = 'implement';
    array['join_list']          = 'join';
    array['create_task_list']   = 'create_task';
    array['pheduyet_task_list'] = 'pheduyet_task';
    array['progress_list'] 		= 'progress_task';

    var detect_element 	 = $(obj).parents('.result').prev();
    var result_frame   	 = $(obj).parents('.result');
    var class_name 	 	 = array[frame_id];
    var object_section   = $(obj).closest('.x-select-users');
    var span_element     = object_section.find('#'+class_name+'_'+item_id);

    if(!span_element.length) {
        var html = '<span class="item"><input type="hidden" name="'+class_name+'[]" class="'+class_name+'" id="'+class_name+'_'+item_id+'" value="'+item_id+'"><a>'+item_name+'</a>&nbsp;&nbsp;<span class="x" onclick="delete_item(this);"></span></span>';
        $( html ).insertBefore( detect_element );
        result_frame.hide();
        detect_element.val('');
        detect_element.focus();
    }

}

function delete_item(obj) {
    $(obj).parents('span.item').remove();
}

function add_congviec() {
	reset_error();

    var checkOptions = {
        url : BASE_URL+'tasks/addcongviec',
        dataType: "json",
        success: congviecData
    };
    $("#task_form").ajaxSubmit(checkOptions);

    return false;
}

function add_task()
{
    reset_error();
    var checkOptions = {
        url : BASE_URL+'tasks/addtask',
        dataType: "json",
        success: congviecData
    };
    $("#task_form").ajaxSubmit(checkOptions);
    return false;

}

function congviecData(data) {
	if(data.flag == 'false') {
		$.each(data.errors, function( index, value ) {
			element = $( '#my_modal span[for="'+index+'"]' );
			element.prev().addClass('has-error');
			element.text(value);
		});
        console.log(data);
        if(data.errors.hasOwnProperty("date_time")) {
        	toastr.error(data.errors.date_time, 'Lỗi');
        } else if(data.errors.hasOwnProperty("progress")) {
        	toastr.error(data.errors.progress, 'Lỗi');
        }
         else if(data.errors.hasOwnProperty("template")) {
            // alert('gdfg');
            toastr.error(data.errors.template, 'Lỗi');
        }
	}else {
		toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#my_modal').modal('toggle');
        window.location.href = BASE_URL + 'tasks/task_chart';
	}
}