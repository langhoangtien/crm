<?php
$name         = $item['name'];
$date_payment = $item['date_payment'];
$price        = $item['price'];
$vat          = $item['vat'];
$unit         = $item['unit'];
$status       = array(''=>'Chưa nghiệm thu');
$status       = array_merge($status,lang('common_contract_form_payment'));
// var_dump($status);die();
$c_status     = $item['c_status'];
//var_dump($item['status']);

// echo "<pre>";var_dump($item);die();
// var_dump($status);die();
if($html == true)
    $btn_submit = '<a href="javascript:;" onclick="save_contract_payment_without_db();" class="btn btn-primary btn-send">Lưu</a>';
else
    $btn_submit = '<a href="javascript:;" onclick="save_contract_payment();" class="btn btn-primary btn-send">Lưu</a>';

$currency_symbol = $this->config->item('currency_symbol');
$thousands_separator = $this->config->item('thousands_separator');
$decimal_point       = $this->config->item('decimal_point');
$number_of_decimals  = $this->config->item('number_of_decimals');
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Yêu cầu thanh lý / nghiệm thu</h4>
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
                                <label class="col-md-6 col-lg-4 control-label">Tên giai đoạn nghiệm thu thanh lý</label>
                                <div class="col-md-6 col-lg-8">
                                    <input type="text" name="c_payment_name" id="c_payment_name" value="<?php echo $name; ?>" class="form-control">
                                    <span for="c_payment_name" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-6 col-lg-4 control-label">Thực hiện khi hoàn thành xong công việc</label>
                                <?php  

                                ?>
                                <div class="col-md-6 col-lg-8">
                                    <select class="form-control" name="c_task_id" id="c_task_id">
                                        <option value="-1">Chọn</option>
                                            <!-- <?php if (!empty($tasks)):?>
                                                <?php 
                                                foreach($tasks as $task) {
                                                    $selected = '';
                                                    $level ='';
                                                    if ($task['level']==1) {
                                                        $level ='';
                                                    }else{
                                                        $level ='--';
                                                    }
                                                    if ($task['id'] == $item['task_id']) $selected = ' selected="selected" ';
                                                    ?>
                                                    <option <?php echo $selected;?> value="<?php echo $task['id'];?>">
                                                        <?php echo $level.' '.$task['name'];?>
                                                    </option>
                                                    <?php 
                                                }
                                                ?>
                                                <?php endif;?> -->
                                                <?php 
                                                if (!empty($item)) {
                                                    $this->Contract->showCategories($tasks,$project_id,'',(int)$item['task_id']);
                                                }else{
                                                    $this->Contract->showCategories($tasks,$project_id);
                                                }
                                                ?>
                                            </select>
                                            <span for="c_task_id" class="text-danger errors"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-6 col-lg-4 control-label">Trạng thái</label>
                                        <div class="col-md-6 col-lg-8">
                                            <select name="c_status" id="c_status" class="form-control">
                                                <?php foreach ($status as $key => $value) {$c_select=($key==$c_status)? "selected" :"";
                                                echo '<option '.$c_select.' value="'.$key.'">'.$value.'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-md-6 col-lg-4 control-label">Ngày ký biên bản</label>
                                    <div class="col-md-6 col-lg-8">
                                        <input type="text" class="form-control datepicker" name="c_date_payment" id="c_date_payment" value="<?php echo $date_payment;?>" />
                                        <span for="c_date_payment" class="text-danger errors"></span>
                                    </div>
                                </div>
                                <div class="form-group"<?php if($type == 'rule') echo ' style="margin-bottom: 0;"'; ?>>
                                    <label class="col-md-6 col-lg-4 control-label">Số tiền</label>
                                    <div class="col-md-6 col-lg-8">
                                        <input type="text" name="c_payment_price" id="c_payment_price" value="<?php echo $price; ?>" class="form-control price">
                                        <select name="unit" id="unit" class="form-control">
                                            <option value="money"<?php if($unit == 'money') echo ' selected'; ?>><?php echo $currency_symbol; ?></option>
                                        </select>
                                        <span for="c_payment_price" class="text-danger errors"></span>
                                    </div>
                                </div>
                                <?php
                                if($type != 'rule') {
                                    ?>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="col-md-6 col-lg-4 control-label">VAT</label>
                                        <div class="col-md-6 col-lg-8">
                                            <select name="c_payment_vat" id="c_payment_vat" class="form-control">
                                                <option value="unpublished"<?php if($vat == 'unpublished') echo ' selected'; ?>>Không</option>
                                                <option value="published"<?php if($vat == 'published') echo ' selected'; ?>>Có</option>
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
            date_time_picker_field($('#c_date_payment'), "DD-MM-YYYY");
            $('.price').autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>'});
        });
    </script>