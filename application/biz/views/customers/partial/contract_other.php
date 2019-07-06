<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_other"<?php echo $display_css; ?>>
    <div class="clearfix hang">
        <label class="col-md-3 col-lg-2 control-label">File Upload</label>
        <div class="col-md-9 col-lg-10">
            <input type="file" name="file_upload" class="file_upload" id="file_upload" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
            <div class="bootstrap-filestyle input-group"><input type="text" name="file_display" id="file_display" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image_id" class="btn btn-file-upload "><span class="glyphicon glyphicon-folder-open"></span> <span class="buttonText" id="choose_file">Choose file</span></label></span></div>
            <a href="" id="file_contract_download" target="_blank"></a>
            <span for="file_upload" class="text-danger errors"></span>
        </div>
    </div>
    <div class="clearfix hang">
        <label class="col-md-3 col-lg-2 control-label">Tên file</label>
        <div class="col-md-9 col-lg-10">
            <input type="text" name="file_name" id="file_name" value="" class="form-control">
            <span for="file_name" class="text-danger errors"></span>
        </div>
    </div>
    <div class="clearfix hang">
        <label for="family_info" class="col-sm-3 col-md-3 col-lg-2 control-label ">Yêu cầu khác :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <textarea name="note" cols="17" rows="3" id="note" class="form-control text-area"></textarea>
        </div>
    </div>
</div>
<?php
    if($action == 'edit') {
?>
<script type="text/javascript">
    $( document ).ready(function() {
        load_contract_other(<?php echo $contract_id; ?>);
    });
</script>
<?php
    }
?>