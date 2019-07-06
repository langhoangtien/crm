<?php
$type_arr   = lang('contract_type');
$status_arr = array('-1' => 'Chọn trạng thái');
$tmp        = lang('contract_status');

foreach($tmp as $key => $val) {
    $status_arr[$key] = $val;
}

if($item['id'] > 0)
    $disabled = ' disabled';
else
    $disabled = '';
?>
<div id="section-1" class="col-md-6" style="padding-right: 5px;">
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Loại hợp đồng :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_dropdown('type', $type_arr, $item['type'], 'class="form-control form-inps" id ="type"' . $disabled);?>
            <span for="type" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Tên hợp đồng :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'name', 'id'=>'name','class'=>'form-control','value'=>$item['name']));?>
            <span for="name" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Mã hợp đồng :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'code', 'id'=>'code','class'=>'form-control','value'=>$item['code']));?>
            <span for="code" class="text-danger errors"></span>
        </div>
    </div>
</div>
<div id="section-2" class="col-md-6" style="padding-left: 5px;">
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Ngày ký :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'date_signing', 'id'=>'date_signing','class'=>'form-control datepicker','value'=>$item['date_signing']));?>
            <span for="date_signing" class="text-danger errors"></span>
        </div>
    </div>

    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Ngày hết hạn :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'date_expiration', 'id'=>'date_expiration','class'=>'form-control datepicker','value'=>$item['date_expiration']));?>
            <span for="date_expiration" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang" id="status_section">
        <label for="status" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Trạng thái :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_dropdown('status', $status_arr, $item['status'], 'class="form-control form-inps" id ="status"');?>
            <span for="status" class="text-danger errors"></span>
        </div>
    </div>
</div>
