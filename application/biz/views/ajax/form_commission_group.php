<?php
$btn_submit = '<a href="javascript:;" onclick="save_commission_group();" class="btn btn-primary btn-send">Lưu</a>';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Cập nhập nhóm hoa hồng</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="group_commission_form" id="group_commission_form" class="form-horizontal" enctype="multipart/form-data">
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Nhóm</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="group_id" class="form-control">
                                        <?php
                                        foreach($slb_group as $key => $val) {
                                            ?>
                                            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <span for="group_id" class="text-danger errors"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tỷ lệ</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="commission_percent" value="0" class="form-control">
                                    <span for="commission_percent" class="text-danger errors"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tính theo</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="commission_percent_type" class="form-control">
                                        <option value="selling_price" selected="">Giá bán</option>
                                        <option value="profit">Lợi nhuận</option>
                                    </select>
                                    <span for="commission_percent" class="text-danger errors"></span>
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