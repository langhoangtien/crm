<?php
    $type_arr   = lang('contract_type');
    $status_arr = array('-1' => 'Chọn trạng thái');
    $tmp        = lang('contract_status');

    foreach($tmp as $key => $val) {
        $status_arr[$key] = $val;
    }

    if($action == 'edit') {
        $sale_prefix = $this->config->item('sale_prefix') . ' ';
        $disabled_attr = ' disabled';
    }

?>
<div id="section-1" class="col-md-6" style="padding-right: 5px;">
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Loại hợp đồng :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_dropdown('type', $type_arr, 'parttime', 'class="form-control form-inps" id ="type"'.$disabled_attr);?>
            <span for="type" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Thuộc :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <select name="parent_id" class="form-control form-inps" id="parent_id"<?php echo $disabled_attr; ?>>
                <option value="-1" data-code="">Chọn hợp đồng nguyên tắc</option>
                <?php
                if(!empty($contract_rule)) {
                    foreach($contract_rule as $val) {
                        ?>
                        <option value="<?php echo $val['id']; ?>" data-code="<?php echo $val['code']; ?>"<?php if($val['id'] == $item['parent_id']) echo ' selected'; ?>><?php echo $val['name']; ?></option>
                    <?php
                    }
                }
                ?>
            </select>
            <span for="parent_id" class="text-danger errors"></span>
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
    <div class="form-group hang" id="status_section">
        <label for="status" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Trạng thái :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_dropdown('status', $status_arr, $item['status'], 'class="form-control form-inps" id ="status"');?>
            <span for="status" class="text-danger errors"></span>
        </div>
    </div>
</div>
<div id="section-2" class="col-md-6" style="padding-left: 5px;">
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Mã đơn hàng:</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'sale_code', 'id'=>'sale_code','class'=>'form-control','value'=>$sale_prefix . $item['sale_id'], 'readonly'=>true));?>
            <span for="sale_code" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Số đơn hàng:</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'sale_id', 'id'=>'sale_id','class'=>'form-control','value'=>$item['sale_id'], 'readonly'=>true));?>
            <span for="sale_id" class="text-danger errors"></span>
        </div>
    </div>
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
</div>