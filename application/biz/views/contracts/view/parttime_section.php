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

        if(!empty($sale_info['code']))
            $sale_code = $sale_info['code'];
        else
            $sale_code = $sale_prefix . ' ' . $sale_info['sale_id'];

        $link_sale_edit = base_url() . 'sales/receipt/' . $sale_info['sale_id'];
    }

    $circle = (!empty($item['circle'])) ? $item['circle'] : 0;
    $bidding = (!empty($item['bidding'])) ? $item['bidding'] : 0;
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
    <?php if($option == 'customer'):?>
        <div class="form-group hang">
            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Mã đơn hàng:</label>
            <div class="col-sm-9 col-md-9 col-lg-9">
                <div class="input-group date">
<?php if(empty($action)): ?>
                    <span class="input-group-addon" id="sale_code_button"><i class="ion-clipboard"></i></span>
<?php endif; ?>
<?php if($action == 'edit'): ?>
                    <a target="_blank" class="input-group-addon" id="sale_code_button" href="<?php echo $link_sale_edit; ?>"><i class="ion-clipboard"></i></a>
<?php endif; ?>
                    <input type="text" value="<?php echo $sale_code; ?>" class="form-control" readonly="true" id="sale_code">
                </div>
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
    <?php endif; ?>
    <?php if($option == 'supplier'):?>
        <div class="form-group hang">
            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Mã đơn hàng:</label>
            <div class="col-sm-9 col-md-9 col-lg-9">
                <?php echo form_input(array( 'name'=>'receiving_code', 'id'=>'receiving_code','class'=>'form-control','value'=>$sale_prefix . $item['receiving_id'], 'readonly'=>true));?>
                <span for="receiving_code" class="text-danger errors"></span>
            </div>
        </div>
        <div class="form-group hang">
            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Số đơn hàng:</label>
            <div class="col-sm-9 col-md-9 col-lg-9">
                <?php echo form_input(array( 'name'=>'receiving_id', 'id'=>'receiving_id','class'=>'form-control','value'=>$item['receiving_id'], 'readonly'=>true));?>
                <span for="receiving_id" class="text-danger errors"></span>
            </div>
        </div>
    <?php endif; ?>
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
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Ngày bắt đầu :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'date_start', 'id'=>'date_start','class'=>'form-control datepicker','value'=>$item['date_start']));?>
            <span for="date_start" class="text-danger errors"></span>
        </div>
    </div>

    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Ngày hết hạn :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array( 'name'=>'date_expiration', 'id'=>'date_expiration','class'=>'form-control datepicker','value'=>$item['date_expiration']));?>
            <span for="date_expiration" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Chu kỳ (ngày):</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <input type="number" name="circle" value="<?php echo $circle; ?>" id="circle" class="form-control" min="0">
            <span for="circle" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Báo trước (ngày):</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <input type="number" name="bidding" value="<?php echo $bidding; ?>" id="bidding" class="form-control" min="0">
            <span for="bidding" class="text-danger errors"></span>
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