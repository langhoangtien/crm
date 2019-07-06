<?php
$thousands_separator = $this->config->item('thousands_separator');
$decimal_point       = $this->config->item('decimal_point');
$number_of_decimals  = $this->config->item('number_of_decimals');

if(isset($item)) {
    $id                  = $item['id'];
    $name                = $item['name'];
    $contract_payment_id = $item['contract_payment_id'];
    $price               = $item['price'];
    $note                = $item['note'];
}

$btn_submit = '<a href="javascript:;" onclick="save_contract_payment_detail();" class="btn btn-primary">Lưu</a>';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Cập nhật chi tiết thanh toán</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="contract_payment_detail_form" id="contract_payment_detail_form" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <input type="hidden" name="contract_payment_id" value="<?php echo $contract_payment_id; ?>" />
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tiêu đề</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="name" value="<?php echo $name; ?>" class="form-control">
                                    <span for="name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Số tiền</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="price" value="<?php echo $price; ?>" class="form-control price">
                                    <span for="price" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label">Ghi chú</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="note" class="form-control" style="margin-bottom: 0"><?php echo $note; ?></textarea>
                                    <span for="note" class="text-danger errors"></span>
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
<script type="text/javascript">
    $( document ).ready(function() {
        $('.price').autoNumeric('init', { mDec: 2, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>'});
    });
</script>