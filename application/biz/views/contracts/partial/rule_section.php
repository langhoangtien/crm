<?php
$type_arr   = lang('contract_type');
$status_arr = array('-1' => 'Chọn trạng thái');
$tmp        = lang('contract_status');
// var_dump(lang('contract_status'));die();
// var_dump($tmp);die();
// var_dump($item);die();
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
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Tên dịch vụ :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
        
            <?php echo form_input(array( 'name'=>'service_name', 'id'=>'service_name','class'=>'form-control','value'=>$item['service_name']));?>
            <input type="hidden" name="service_id" id="service_id" value="<?php echo $item['service_id'];?>" />
            <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $item['customer_id'];?>" />
            <span for="name" class="text-danger errors"></span>
        </div>
    </div>
	<div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Nhóm dịch vụ :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <input type="text" readonly="readonly" id="service_type_name" class="form-control form-inps" value="<?php echo $item['service_type_name'];?>" />
            <span for="type" class="text-danger errors"></span>
            <input type="hidden" name="type" value="rule" />
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
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Số hợp đồng :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <input type="text" name="code" value="<?php echo $item['code'];?>" class="form-control" /> 
            <span for="code" class="text-danger errors"></span>
        </div>
    </div>
    <div class="form-group hang">
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Chọn dự án liên quan :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <input type="text" readonly="readonly" name="project_name" id="project_name" class="form-control" value="<?php echo $item['project_name'];?>" />
            <input type="hidden" id="project_id" name="project_id" value="<?php echo $item['project_id'];?>" />
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
        <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label ">Ngày hiệu lực :</label>
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

	<div class="form-group hang" id="status_section">
        <label for="status" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Trạng thái :</label>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_dropdown('status', $status_arr, $item['status'], 'class="form-control form-inps" id ="status"');?>
            <span for="status" class="text-danger errors"></span>
        </div>
    </div>


</div>

<script type="text/javascript">
	$(document).ready(function() {

	    $( "#service_name" ).autocomplete({
		 		source: '<?php echo site_url("sales/item_search_for_contract");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$( "#service_id" ).val(ui.item.item_id);
					$( "#service_name" ).val(ui.item.label);
					$( "#service_type_name" ).val(ui.item.category);
					$("#project_name").prop("readonly", false);
	
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='item-suggestions'></li>")
		             .data("item.autocomplete", item)
			           .append('<a class="suggest-item"><div class="item-image">' +
									'<img src="' + item.image + '" alt="">' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' +
									'<span class="attributes">' +
										'<?php echo lang("common_category"); ?>' + ' : <span class="value">' + (item.category ? item.category : <?php echo json_encode(lang('common_none')); ?>) + '</span>' +
									'</span>' +
								'</div></a>')
		             .appendTo(ul);
		     };
		
	    $( "#project_name" ).autocomplete({
		 		source: '<?php echo site_url("tasks/search_projects");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$( "#project_id" ).val(ui.item.id);
					$( "#project_name" ).val(ui.item.label);
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='item-suggestions'></li>")
		             .data("item.autocomplete", item)
			           .append('<a class="suggest-item"><div class="item-image">' +
									'<img src="' + item.image + '" alt="">' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' +
									'<span class="attributes"></span>' +
								'</div></a>')
		             .appendTo(ul);
		     };
	});
</script>
