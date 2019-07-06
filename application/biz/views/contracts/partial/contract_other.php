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
    <div class="col-md-12">
        <div class="col-md-2">
            Danh sách file liên quan:
        </div>   
        <div class="col-md-10">
            <table class="table table-bordered table-striped data-n9-table ">
                <thead>
                    <tr>
                        <td class="text-left">Tên file</td>
                        <td>Yêu cầu</td>
                        <td>Ngày tải lên</td>
                        <td class="text-center">Tải xuống</td>
                        <td class="text-center">Xóa</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $get = $this->input->get();
                    $arrParam['contract_id'] = $get['contract_id'];
                    $list_file = $this->Contract->list_file_contract($arrParam);
                    if (!empty($list_file)) {
                        foreach ($list_file as $key => $value) {
                            ?>
                            <tr class="row_file_contract_<?php echo $value['id'];?>">
                                <td><?php echo $value['name_file']; ?></td>
                                <td><?php echo $value['note']; ?></td>
                                <td class="text-right"><?php echo Date('m-d-Y', strtotime($value['date_up'])); ?></td>
                                <td class="text-center"><a href="<?php echo base_url().'contracts/download_file_contract/'.$value['id']; ?>"><i class="fa fa-download text-primary"></i></a></td>
                                <td class="text-center"><a href="javascript:;" class="text-danger" onclick="del_file_contract(<?php echo $value['id']; ?>);"><i class="fa fa-trash-o"></i></a></td>
                            </tr>
                            <?php
                        }
                    }else{
                        ?>
                        <?php  
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function del_file_contract(id){
        bootbox.confirm({
            message: "Bạn có muốn xóa file không?",
            buttons: {
                confirm: {
                    label: 'Xóa',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'Không',
                    className: 'btn-primary'
                }
            },
            callback: function (result) {
                $.ajax({
                    url: '<?php echo base_url('contracts/delete_file_contract')?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {file_id: id},
                })
                .done(function(data) {
                    if (data.success=='success') {
                        toastr.success('Xóa file thành công!');
                        $('.row_file_contract_'+id).remove();
                    }else{
                        toastr.error('Xóa file không thành công!');
                    }
                })
                .fail(function() {
                })
                .always(function() {
                });
                
            }
        });
    }
</script>
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