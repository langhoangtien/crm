<?php
    if($type == 'list')
        $btn_submit = '<a href="javascript:;" onclick="send_mail_list();" class="btn btn-primary btn-send">Thực hiện</a>';
    else
        $btn_submit = '<a href="javascript:;" onclick="send_mail();" class="btn btn-primary btn-send">Thực hiện</a>';
?>
<div class="modal-dialog" style="padding-top: 10%;">
    <div class="modal-content">
        <div class="modal-header" >
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Gửi Email</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="frm_send_mail" id="frm_send_mail" class="form-horizontal" enctype="multipart/form-data">
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Mẫu Email</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="template_email_id" id="template_email_id" class="form-control">
                                <?php
                                foreach($list_mail as $key => $val) {
                                ?>
                                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php
                                }
                                ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <?php echo $btn_submit; ?>
        </div>
    </div>
</div>
