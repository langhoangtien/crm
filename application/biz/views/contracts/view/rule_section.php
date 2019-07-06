<?php
$type_arr   = lang('contract_type');
$status_arr = array('-1' => 'Chọn trạng thái');
$tmp        = lang('contract_status');
foreach($tmp as $key => $val) {
    $status_arr[$key] = $val;
}
$item['status_date'] = date("d-m-Y",strtotime($item['status_date'])); 
if($item['id'] > 0)
    $disabled = ' disabled';
else
    $disabled = '';
?>
<div id="section-1" class="col-md-6" style="padding-right: 5px;">
	<div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Tên dịch vụ: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['service_name']; ?></p>
        </div>
    </div>
	<div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Nhóm dịch vụ: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['service_type_name'];?></p>
        </div>
    </div>
    
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Tên hợp đồng: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['name']; ?></p>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Số hợp đồng: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['code']; ?></p>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Dự án liên quan: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['project_name']; ?></p>
        </div>
    </div>
</div>
<div id="section-2" class="col-md-6" style="padding-left: 5px;">
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Ngày ký: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['date_signing']; ?></p>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Ngày hiệu lực: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['date_start']; ?></p>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Ngày hết hạn: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <p><?php echo $item['date_expiration']; ?></p>
        </div>
    </div>
	<div class="form-group hang" id="status_section">
        <label for="status" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Trạng thái: </label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_dropdown('status', $status_arr, $item['status'], 'class="form-control form-inps" disabled id ="status"');?>
            <span for="status" class="text-danger errors"></span>
        </div>
    </div>
</div>
