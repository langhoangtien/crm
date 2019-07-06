<?php
    $name         = $item['name'];
    $date_payment = $item['date_payment'];
    $price        = $item['price'];
    $vat          = $item['vat'];
    $unit         = $item['unit'];
    if($html == true)
        $btn_submit = '<a href="javascript:;" onclick="save_contract_payment_without_db();" class="btn btn-primary">Lưu</a>';
    else
        $btn_submit = '<a href="javascript:;" onclick="save_contract_payment();" class="btn btn-primary">Lưu</a>';

    $currency_symbol = $this->config->item('currency_symbol');
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Cập nhật yêu cầu thanh toán</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="contract_payment_form" id="contract_payment_form" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="payment_id" id="payment_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>"/>
                <input type="hidden" name="type" value="<?php echo $type; ?>"/>
                <div class="clearfix hang">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Tên giai đoạn</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="c_payment_name" id="c_payment_name" value="<?php echo $name; ?>" class="form-control">
                                    <span for="c_payment_name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Thời hạn thanh toán</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="c_date_payment" id="c_date_payment" value="<?php echo $date_payment; ?>" class="form-control">
                                    <span for="c_date_payment" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group"<?php if($type == 'rule') echo ' style="margin-bottom: 0;"'; ?>>
                                <label class="col-md-3 col-lg-2 control-label">Số tiền</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="c_payment_price" id="c_payment_price" value="<?php echo $price; ?>" class="form-control price">
                                    <select name="unit" id="unit" class="form-control">
                                        <option value="money"<?php if($unit == 'money') echo ' selected'; ?>><?php echo $currency_symbol; ?></option>
                                        <option value="percent"<?php if($unit == 'percent') echo ' selected'; ?>>%</option>
                                    </select>
                                    <span for="c_payment_price" class="text-danger errors"></span>
                                </div>
                            </div>
                    <?php
                    if($type != 'rule') {
                    ?>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label">VAT</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="c_payment_vat" id="c_payment_vat" class="form-control">
                                        <option value="unpublished"<?php if($vat == 'unpublished') echo ' selected'; ?>>Chưa xuất</option>
                                        <option value="published"<?php if($vat == 'published') echo ' selected'; ?>>Đã xuất</option>
                                    </select>
                                </div>
                            </div>
                    <?php
                    }
                    ?>

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
        date_time_picker_field($('#c_date_payment'), JS_DATE_FORMAT);
        $('.price').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ','});
    });
</script>