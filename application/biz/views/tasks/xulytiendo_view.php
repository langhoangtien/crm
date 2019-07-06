<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Phê duyệt tiến độ</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="task_form" id="progress_form" class="form-horizontal">
                <input type="hidden" name="id" value="<?php echo $arrParam['id']; ?>" />
                <div class="clearfix hang" style="margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Phê duyệt</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="pheduyet" class="form-control">
                                        <option value="1">Có</option>
                                        <option value="0">Không</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label">Phản hồi</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="reply" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <a href="javascript:;" onclick="save_tiendo('xuly');" class="btn btn-primary">Lưu</a>
        </div>
    </div>
</div>