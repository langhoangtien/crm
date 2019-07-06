<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Thêm mới công việc</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="template_task_form" id="template_task_form" class="form-horizontal">
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group" style="margin-bottom: 0">
                                <label class="col-md-4 control-label">Tên</label>
                                <div class="col-md-8">
                                    <input type="text" name="template_task" id="template_task" value="" class="form-control">
                                    <span for="name" class="text-danger" class="errors"></span>
                                </div>
                            </div>
                            
                            <div class="form-group" style="padding-top: 20px;">
                                <label class="col-md-4 control-label">Thời gian dự kiến (ngày)</label>
                                <div class="col-md-8">
                                    <input type="text" name="template_task_duration" id="template_task_duration" value="" class="form-control">
                                    <span for="name" class="text-danger" class="errors"></span>
                                </div>
                            </div>
                            
                  
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <a href="javascript:;" id="btn_save_templae_task" class="btn btn-primary">Lưu</a>
        </div>
    </div>
</div>
