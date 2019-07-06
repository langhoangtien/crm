<?php
if($type == 'without_db')
    $btn_submit = '<a href="javascript:;" onclick="save_location_group_without_db();" class="btn btn-primary btn-send">Lưu</a>';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Cập nhật nhóm điểm bán hàng</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="location_group_form" id="location_group_form" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="location_id" value="<?php echo $id; ?>" />
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Nhóm</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="location_group_id" class="form-control">
                            <?php
                            foreach($slb_group as $key => $val) {
                            ?>
                                        <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                            <?php
                            }
                            ?>
                                    </select>
                                    <span for="location_group_id" class="text-danger errors"></span>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label">Trạng thái</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="status" class="form-control">
                                        <option value="active">Hiển thị</option>
                                        <option value="unactive">Ẩn</option>
                                    </select>
                                    <span for="status" class="text-danger errors"></span>
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