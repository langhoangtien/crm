<?php
   $btn_submit = '<a href="javascript:;" onclick="download_file();" class="btn btn-primary btn-send">Lưu</a>';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Tải file hợp đồng</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="contract_file_download_form" id="contract_file_download_form" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>"/>
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group end">
                                <label class="col-md-3 col-lg-2 control-label">Tệp đính kèm</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="quote_contract_id" id="slb_quote_contract" class="form-control">
                            <?php
                            foreach($slb_quote_contract as $key => $value) {
                            ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php
                            }
                            ?>
                                    </select>
                                    <span for="quote_contract_id" class="text-danger errors"></span>
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