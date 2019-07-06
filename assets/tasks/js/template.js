$( document ).ready(function() {
// template task list table
    $('body').on('click','#btnListTasks',function(){
        var tree_array    = $('#sTree2').sortableListsToArray();
        var tasks = new Array();
        if(tree_array.length > 0) {
            $.each( tree_array, function( key, value ) {
                tmp = new Object();
                tmp.id   = value.id;
                tmp.name = $('#'+value.id).attr('data-name');
                if (typeof value.parentId === "undefined") {
                    tmp.parent = 'root';
                }else
                    tmp.parent = value.parentId;

                tasks[tasks.length] = tmp;
            });
        }

        var url = BASE_URL + 'tasks/listTemplateTask';
        $.ajax({
            type: "GET",
            url: url,
            data: {
                tasks : tasks
            },
            success: function(html){
                $('#quick_modal').html(html);
                $('#quick_modal').modal('toggle');
            }
        });
    });

    //checkbox
    $('body').on('click','table[data-table] td.cb',function(){
        var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
        var table = checkbox.closest('[data-table]');
        var data_table = table.attr('data-table');

        if (checkbox.prop('checked') == true){
            checkbox.prop('checked', false);
        }else{
            checkbox.prop('checked', true);
        }

        var checked_box = table.find('.file_checkbox:checked');
        if(checked_box.length == 0){
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
        }else {
            $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
        }
    });

    // check all
    $('body').on('click','table[data-table] .check_tatca',function(){
        var checkbox = $(this).closest('th').find('input[type="checkbox"]');
        var table = checkbox.closest('[data-table]');
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

        var checked_box = table.find('.file_checkbox:checked');
        if(checked_box.length == 0){
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
        }else {
            $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
        }
    });

    //Turn text element into input field - update template task
    $('body').on('dblclick', '[data-editable]', function(){
        var $el = $(this);
        trElement    = $el.closest('tr');
        var id = trElement.find('a').attr('data-id');

        var $input = $('<input/>').val( $el.text() );
        $el.replaceWith( $input );

        var save = function(){
            var $span = $('<span data-editable />').text( $input.val() );
            $input.replaceWith( $span );

            data = new Object();

            data.text    = $input.val();
            data.id      = id;

            do_something(data);
        };

        $input.one('blur', save).focus();
    });
});

function add_template_task_tree() {
    $('#template_task_form span[for="name"]').text('');
    $('#template_task').removeClass('has-error');
    var task_name = $.trim($('#template_task').val());
    var danhmuc = $('select[name="danh_muc_cong_viec"]').val();
    var duration = $('#template_task_duration').val();
    var tile = $('#template_tile').val();
    
    var xemList = [];
    $('input[name="xem[]"]').each(function(){
    	xemList.push($(this).val());
    });
    
    var implementList = [];
    $('input[name="implement[]"]').each(function(){
    	implementList.push($(this).val());
    });
    
    var approveList = [];
    $('input[name="progress_task[]"]').each(function(){
    	approveList.push($(this).val());
    });
    
    
    var xem_list = xemList.join();
    var implement_list = implementList.join();
    var progress_list = approveList.join();
    
    if (!task_name) {
        $('#template_task').addClass('has-error');
        $('#template_task_form span[for="name"]').text('Tên công việc không được rỗng.')
    }else {
        var count_task = $('#count_task').val();
        count_task     = parseInt(count_task) + 1;
        $('#count_task').val(count_task);

        $('#sTree2').append('<li data-module="'+count_task+'" id="t_'+count_task+'" data-tile="'+ tile +'" data-xemlist="'+ xem_list +'"  data-approvelist="'+ progress_list +'" data-implementlist="'+ implement_list +'" data-duration="'+ duration +'" data-danhmuc="'+ danhmuc +'" data-name="'+task_name+'"><div>'+task_name+'</div></li>');
        $('#quick_modal').modal('toggle');
    }
}

function add_template_task() {
    var url = BASE_URL + 'tasks/addcvtemplate'
    $.ajax({
        type: "GET",
        url: url,
        data: {
        },
        success: function(html){
            $('#quick_modal').html(html);
            $('#quick_modal').modal('toggle');
            var frame_array = ['customer_list', 'xem_list', 'implement_list', 'create_task_list', 'pheduyet_task_list', 'progress_list'];
            $.each(frame_array, function( index, value ) {
                css_form(value);
                press(value);
            });
        }
    });
}

function delete_template() {
    var checkbox = $(".file_checkbox:checked");
    var template_ids = new Array();
    $(checkbox).each(function( index ) {
        template_ids[template_ids.length] = $(this).val();
    });

    bootbox.confirm('Bạn có chắc muốn xóa không?', function(result){
        if (result){
            $.ajax({
                type: "POST",
                url: BASE_URL + 'tasks/deleteTemplate',
                data: {
                    template_ids   : template_ids,
                },
                success: function(string){
                    toastr.success('Cập nhật thành công!', 'Thông báo');
                    load_list('template', 1);
                }
            });
        }
    });
}

function del_template_task(obj) {
    var id = $(obj).attr('data-id');
    parent_item = $('#'+id).closest('ul');

    if(parent_item.hasClass('listsClass')){
        $('#'+id).remove();
    }else{
        parent_item.remove();
    }

    // remove on table
    $(obj).closest('tr').remove();
    var child_ids = $(obj).attr('data-child');
    if (child_ids) {
        var child_ids = child_ids.split(",");
        $.each(child_ids, function( index, value ) {
            $('#template_task_list tbody tr a[data-id="'+value+'"]').closest('tr').remove();
        });
    }

    var count = $('#template_task_list tbody tr').length;
    $('#count_template_task').text(count);

    if(count == 0) {
        $('#template_task_list tbody').html('<tr><td colspan="2"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>');
    }
}

function do_something(data) {
    var id   = data.id;
    var text = data.text;
    $('#'+id+ ' > div').html(text);
}

function update_template(id) {
    var template_name = $.trim($('#template_name').val());
    var tree_array    = $('#sTree2').sortableListsToArray();
    var cate = $('#s_category_id').find(":selected").val();
    var tasks = new Array();
    if(tree_array.length > 0) {
        $.each( tree_array, function( key, value ) {
            tmp = new Object();
            tmp.id   = value.id;
            tmp.name = $('#'+value.id).attr('data-name');
            tmp.danhmuc = $('#'+value.id).attr('data-danhmuc');
            tmp.duration = $('#'+value.id).attr('data-duration');
            tmp.tile = $('#'+value.id).data('tile');
            tmp.xemlist = $('#'+value.id).data('xemlist');
            tmp.approvelist = $('#'+value.id).data('approvelist');
            tmp.implementlist = $('#'+value.id).data('implementlist');
            
            if (typeof value.parentId === "undefined") {
                tmp.parent = 'root';
            }else
                tmp.parent = value.parentId;

            tasks[tasks.length] = tmp;
        });
    }
    var data           = new Object();
    data.template_name = template_name;
    data.cate          = cate;
    data.tasks         = tasks;
    if(id > 0) {
        data.id = id;
        var url = BASE_URL + 'tasks/editTemplate';
    }else {
        var url = BASE_URL + 'tasks/templateAdd';
    }
    // console.log(data);return;
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        // dataType: 'json',
        success: function(string){
            var res = $.parseJSON(string);

            if(res.flag == 'false'){
                toastr.error(res.msg, 'Lỗi!');
            }else {
                 window.location = BASE_URL + 'tasks/template';
            }
        }
    });
}