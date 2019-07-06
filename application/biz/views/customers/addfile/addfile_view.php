<?php  

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" id="close_popup" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Thêm mới File</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="file_form_customer" id="file_form_customer" class="form-horizontal" enctype="multipart/form-data">
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tên tài liệu</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="name" value="" class="form-control" id="name_file">
                                    <span for="name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">File Upload</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="file" name="file_upload" id="file_upload" class="filestyle file_upload"
                                    tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
                                    <input type="hidden" name="person_id" id="person_id" value="<?php echo $person_id ?>"/>
                                    <div class="bootstrap-filestyle input-group">
                                        <input type="text" name="file_display" id="file_display"
                                        class="form-control " disabled=""> 
                                        <span class="group-span-filestyle input-group-btn" tabindex="0">
                                            <label class="btn btn-file-upload ">
                                                <span class="buttonText" id="choose_file" >Choose file</span>
                                            </label>
                                        </span>
                                    </div>
                                    <span for="file_upload" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tên file</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="file_name" id="file_name" value="" class="form-control">
                                    <span for="file_name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label">Mô tả</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="excerpt" id="excerpt" class="form-control" style="margin-bottom: 0"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="padding-top: 0;">
                    <a onclick="save_file_customer()" class="btn btn-primary">Lưu file</a>
                </div>
            </form>

        </div>

    </div>
</div>

<script type="text/javascript">

function reset_error() {
    $('#my_modal .form-control').removeClass('has-error');
    $('#my_modal span.errors').text('');
    $('#quick-form .form-control').removeClass('has-error');
    $('#quick-form span.errors').text('');
}
function save_file_customer(type) {
    reset_error();
    if(type == 'edit') 
        var url = BASE_URL + 'customers/editfile';
    else 
        var url = BASE_URL + 'customers/addfile';

    var checkOptions = {
            url : url,
            dataType: "json",  
            success: fileData
        };
    $("#file_form_customer").ajaxSubmit(checkOptions); 
    return false; 
}

function fileData(data) {
    // alert('ff');
    console.log(data);
    if(data.flag == 'false') {
        toastr.error(data.errors.name, 'Lỗi');

    }else if(data.flag == 'true') {
        toastr.success('Cập nhật thành công!', 'Thông báo');
        $('#my_modal').modal('toggle');

        // load_list('file', 1);
    }
    else{
          toastr.error('Không xác định được lỗi :((', 'Thông báo');
    }
}

</script>
<script type="text/javascript">
    $( document ).ready(function() {
        $( "#choose_file" ).click(function() {
            $('#file_upload').trigger('click');
        });

        $( ".file_upload" ).change(function() {
            // alert('thay doi');
            var yourstring = $(this).val();
            var filename = yourstring.replace(/^.*[\\\/]/, '')

            var output  = filename.split('/').pop().split('.').shift();
            $('#file_display').val(filename);
            $('#file_name').val(output);
        });
    });
</script>