<style>
a.remove{
	border-right: none;
	height: auto;
}
.itemsContainLineNameEdit{
	transition: color .2s, font-size 0.7s;
	-webkit-transition: color .2s linear, font-size 0.7s;
	-moz-transition: color .2s linear, font-size 0.7s;
	-o-transition: color .2s linear,font-size 0.7s;
	text-align: right; 
	color: #3c8dbc; 
	font-size: 18px; 
	font-weight: bold; 
	cursor:pointer;
}
.itemsContainLineNameEdit:hover{
	color:red;
	font-size: 20px
}
.itemsNameEdit{
	transition: color .2s, font-size 0.7s;
	-webkit-transition: color .2s linear, font-size 0.7s;
	-moz-transition: color .2s linear, font-size 0.7s;
	-o-transition: color .2s linear,font-size 0.7s;
	text-align: right; 
	color: #438AF7; 
	font-size: 14px; 
	font-weight: bold; 
	cursor:pointer;
}
.itemsNameEdit:hover{
	color:red;
	font-size: 16px
}    
.register-item-name{
	transition: color .2s, font-size 0.7s;
	-webkit-transition: color .2s linear, font-size 0.7s;
	-moz-transition: color .2s linear, font-size 0.7s;
	-o-transition: color .2s linear,font-size 0.7s;
	color: #337ab7 !important; 
	cursor:pointer;
}
.register-item-name:hover{
	color:#438AF7 !important;
}
</style>

<script>
	(function ($) {
		"use strict";
		
		var Size = function (options) {
			this.init('size', options, Size.defaults);
		};

    //inherit from Abstract input
    $.fn.editableutils.inherit(Size, $.fn.editabletypes.abstractinput);

    $.extend(Size.prototype, {
        /**
        Renders input from tpl

        @method render() 
        **/        
        render: function() {
        	this.$input = this.$tpl.find('input');
        },
        
        /**
        Default method to show value in element. Can be overwritten by display option.
        
        @method value2html(value, element) 
        **/
        value2html: function(value, element) {
        	if(!value) {
        		$(element).empty();
        		return; 
        	}
        	var html = $('<div>').text(value.height).html() + ', ' + $('<div>').text(value.height).html() + ' st., bld. ';
        	$(element).html(html); 
        },
        
        /**
        Gets value from element's html
        
        @method html2value(html) 
        **/        
        html2value: function(html) {   
        	return null;  
        },
        
       /**
        Converts value to string. 
        It is used in internal comparing (not for sending to server).
        
        @method value2str(value)  
        **/
        value2str: function(value) {
        	var str = '';
        	if(value) {
        		for(var k in value) {
        			str = str + k + ':' + value[k] + ';';  
        		}
        	}
        	return str;
        }, 
        
       /*
        Converts string to value. Used for reading value from 'data-value' attribute.
        
        @method str2value(str)  
        */
        str2value: function(str) {
           /*
           this is mainly for parsing value defined in data-value attribute. 
           If you will always set value by javascript, no need to overwrite it
           */
           return str;
       },                
       
       /**
        Sets value of input.
        
        @method value2input(value) 
        @param {mixed} value
        **/         
        value2input: function(value) {
        	if(!value) {
        		return;
        	}
        	this.$input.filter('[name="width"]').val(value.width);
        	this.$input.filter('[name="height"]').val(value.height);
        	this.$input.filter('[name="mSquare"]').val(value.mSquare);
        },       
        
       /**
        Returns value of input.
        
        @method input2value() 
        **/          
        input2value: function() { 
        	return {
        		width: this.$input.filter('[name="width"]').val(), 
        		height: this.$input.filter('[name="height"]').val(),
        		mSquare: this.$input.filter('[name="mSquare"]').val()
        	};
        },        
        
        /**
        Activates input: sets focus on the first field.
        
        @method activate() 
        **/        
        activate: function() {
        	this.$input.filter('[name="height"]').focus();
        },  
        
       /**
        Attaches handler to submit form in case of 'showbuttons=false' mode
        
        @method autosubmit() 
        **/       
        autosubmit: function() {
        	this.$input.keydown(function (e) {
        		if (e.which === 13) {
        			$(this).closest('form').submit();
        		}
        	});
        }       
    });
    Size.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
    	tpl: '<div class="editable-address"><label><span style ="width:100px;    display: inline-block;">Chiều rộng: </span><input type="text" name="width" class="input-small"></label></div>'+
    	'<div class="editable-address"><label><span style ="width:100px; display: inline-block;">Chiều cao: </span><input type="text" name="height" class="input-mini"></label></div>'+
    	'<div class="editable-address"><label><span style ="width:100px; display: inline-block;">Số m<sup style="top:-.5em;">2</sup>: </span><input type="text" name="mSquare" class="input-mini"></label></div>',
    });
    $.fn.editabletypes.size = Size;

}(window.jQuery));

</script>
<?php
$this->load->helper('demo');
$thousands_separator = $this->config->item('thousands_separator');
$decimal_point       = $this->config->item('decimal_point');
$number_of_decimals  = $this->config->item('number_of_decimals');
const COMPLETE_PRODUCT_CATEGORY = 179;

$configRemHaMy =  ($this->config->item('company_user') == 'remHaMy');
if(isset($location_groups[1]) && isset($location_groups[2])){
	$sale_group     = $location_groups[1];
	$delivery_group = $location_groups[2];
	$manage_group   = $location_groups[3];
}
if(!empty($delivery_group))
	$location_groups[2] = $delivery_group;

$sale_mode =  $this->sale_lib->get_mode();

$sale_total    = $this->sale_lib->get_total();
$payment_total = $this->sale_lib->get_payments_totals();


$sale_time = $this->sale_lib->get_sale_time_date();
?>
<a tabindex="-1" href="#" class="dismissfullscreen <?php echo !$fullscreen ? 'hidden' : ''; ?>"><i class="ion-close-circled"></i></a>
<?php if($this->sale_lib->get_change_sale_id()) { ?>
	<?php 
	$sale_code = $this->sale_lib->get_sale_code();
	if(!empty($sale_code)) {
		?>
		<div class="alert alert-danger">
			<?php echo lang('sales_editing_sale'); ?> <strong><?php echo $sale_code; ?></strong>
		</div>
		<?php
	}else {
		?>
		<div class="alert alert-danger">
			<?php echo lang('sales_editing_sale'); ?> <strong><?php echo $this->config->item('sale_prefix').' '.$this->sale_lib->get_change_sale_id(); ?></strong>
		</div>
		<?php
	}
	?>

<?php } ?>

<?php if($this->config->item('test_mode')) { ?>
	<div class="alert alert-danger">
		<strong><?php echo lang('common_in_test_mode'); ?>. <a href="sales/disable_test_mode"></strong>
			<a href="<?php echo site_url('sales/disable_test_mode'); ?>" id="disable_test_mode"><?php echo lang('common_disable_test_mode');?></a>
		</div>
	<?php } ?>

	<?php if(isset($contract_name)) {
		?>
		<div class="alert alert-danger">
			Đơn hàng <strong><?php echo $this->config->item('sale_prefix').' '.$this->sale_lib->get_change_sale_id(); ?></strong> đã được dùng trong hợp đồng "<?php echo $contract_name; ?>"
		</div>
		<?php
	} ?>

	
	<div class="row register">
		<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 no-padding-right no-padding-left">
			<?php if (!$isStockOut) {?>
				<div class="register-box register-items-form">
					<div class="item-form">
						<!-- Item adding form -->
						
						<?php
						$cart_count = 0;
						?>
						<?php echo form_open( ($mode == 'return_by_sales') ? "sales/return_by_sales" : "sales/add_n9",array('id'=>'add_item_form','class'=>'form-inline', 'autocomplete'=> 'off')); ?>
						<div class="input-group input-group-mobile contacts">
							<span class="input-group-addon">
								<?php echo anchor("items/view/-1/1/sale","<i class='icon ti-pencil-alt'></i> <span class='register-btn-text'>".lang('common_new_item')."</span>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item-mobile','tabindex'=> '-1')); ?>
							</span>
						
							<span class="input-group-addon grid-buttons <?php echo $mode == ('store_account_payment' || 'assigment' || 'return_by_sales') ? 'hidden': '' ;?>">
								<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'>".lang('common_show_grid')."</span>", array('class'=>'none show-grid','tabindex'=>'-1','title'=>lang('common_show_grid'))); ?>
								<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'>".lang('common_hide_grid')."</span>", array('class'=>'none hide-grid hidden','tabindex'=>'-1','title'=>lang('common_hide_grid'))); ?>
							</span>
						</div>
						
						<div class="input-group contacts register-input-group">

							<!-- Css Loader  -->
							<div class="spinner" id="ajax-loader" style="display:none">
								<div class="rect1"></div>
								<div class="rect2"></div>
								<div class="rect3"></div>
							</div>
							
							<span class="input-group-addon <?php echo ($mode == "return_by_sales")? 'hidden' : '' ?>">
								<?php echo anchor("items/view/-1/1/sale","<i class='icon ti-pencil-alt'></i>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item', 'tabindex'=> '-1')); ?>
							</span>
							
							<input type="text" id="<?php echo ($mode == "return_by_sales")? 'sale-item' : 'item' ?>" name="item" <?php echo  ($mode=="store_account_payment") ? 'disabled="disabled"' : '' ?> class="add-item-input pull-left" placeholder="<?php echo ($mode == 'return_by_sales')? lang('common_start_typing_sale_id') : lang('common_start_typing_item_name'); ?>">
							
							<span class="input-group-addon grid-buttons <?php echo ($mode == 'store_account_payment' || $mode == 'assigment' || $mode == 'return_by_sales') ? 'hidden': '' ;?>">
								<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'>".lang('common_show_grid')."</span>", array('class'=>'none show-grid','tabindex'=>'-1','title'=>lang('common_show_grid'))); ?>
								<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'>".lang('common_hide_grid')."</span>", array('class'=>'none hide-grid hidden','tabindex'=>'-1','title'=>lang('common_hide_grid'))); ?>
							</span>
						</div>
					</form>
				</div>
			</div>
		<?php } ?>
		<!-- Register Items. @contains : Items table -->
		<div class="register-box register-items paper-cut">
			<div class="register-items-holder">
				<?php if ($mode != 'store_account_payment' && $mode != 'assigment') { ?>
					<table id="register" class="table table-hover">

						<thead>
							<tr class="register-items-header">
								<th></th>
								<th class="item_name_heading" ><?php echo lang('sales_item_name'); ?></th>
								<th class="sales_item_type"><?php echo lang('sales_item_type');?></th>
								<th class="sales_price"><?php echo lang('sales_item_price'); ?></th>
								<th class="sales_price"><?php echo lang('sale_cost_3rd'); ?></th>
								<th>Tổng phí dịch vụ dự kiến (VNĐ)</th>
							</tr>
						</thead>
						
						<tbody class="register-item-content">
							<?php
							if(count($cart)==0)	{ ?>
								<tr class="cart_content_area">
									<td colspan='8'>
										<div class='text-center text-warning' > <h3><?php echo lang('sale_empty_service'); ?><span class="flatGreenc"> [<?php echo lang('module_sales') ?>]</span></h3></div>
									</td>
								</tr>
								<?php 
							}
							else
							{
								if(empty($itemsContainsLine)) {
									$cart_count = 0;
									foreach($cart as $line=>$item) {
										if($item['name'] != lang('common_discount')){
											if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
											{
												$cart_count = $cart_count + $item['quantity'];
											}
											$listItemAtrribute = '';
											
											if (!empty($cartItemsAttribute[$line])) {
												foreach ($cartItemsAttribute[$line] as $itemAttibute) {
													$listItemAtrribute .='<option value ="'.$itemAttibute->attribute_id.'">'. $itemAttibute->name.'</option>';
												}
											}

											?>
											<tr class="register-item-details">
												<td class="text-center">
													<?php if (!$isStockOut) {?>
														<?php echo anchor("sales/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item', 'tabindex' => '-1'));?> </td>
													<?php }?>
													<td> 
														<a tabindex = "-1" class="register-item-name"  data-pk="1" data-line = "<?php echo $line?>" ><?php echo H($item['name']); ?></a>&nbsp;<?php if($configRemHaMy):?><i id = "itemsNameEdit_<?php echo $line ?>" data-type="textarea"  class="itemsNameEdit editable editable-pre-wrapped editable-click  icon ti-pencil"></i><?php endif;?>
													</td>
													<td>
														<?php echo $item['category_name'];?>
													</td>
													<td class="text-center">
														<?php
														echo ($item['cost_price_interval']);
														?>
													</td>
													<td>
														<?php 
														echo ($item['unit_price_interval']); 
														?>
													</td>
													
													<td class="text-center">
														<!-- <input id="<?php echo $line ?>" value="<?php echo number_format($item['calculatedPrice']) ?>" onchange="calculatedPrice(<?php echo $line ?>)">  -->
														<a  href="#" class="xeditable editable editable-click" data-validate-number="true" data-type="text" data-value="<?php echo $item['calculatedPrice'] ?>" data-pk="<?php echo $line ?>" data-name="calculatedPrice" data-url="<?php echo base_url('sales/edit_item_d13/'.$line) ?>" data-title="Giá"><?php echo to_currency($item['calculatedPrice']) ?></a>
													</td>
												</tr>
												<tr class="register-item-bottom">
													<td class="text-center cbRegister">
														<?php if($this->config->item('company_user') == 'remHaMy'):?>
															<input type="checkbox" class = "inputCbResgister" data-line = "<?php echo $line; ?>" id="checkbox_<?php echo $line; ?>">
															<label for="checkbox_<?php echo $line; ?>"><span></span></label>
														<?php endif;?>
													</td>
													<td colspan="7">
														<dl class="register-item-extra-details dl-horizontal">
															<?php if (!empty($qtyStockOut)){ ?>
																<dt><?php echo 'Đã xuất:'; ?></dt>										
																<dd><?php echo $qtyStockOut; ?></dd>
															<?php } ?>
															
															<?php
															
															if (!$this->config->item('always_use_average_cost_method') && $item['change_cost_price'] && $this->Employee->has_module_action_permission('sales', 'edit_sale_cost_price', $this->Employee->get_logged_in_employee_info()->person_id))
															{
																?>
																<dt><?php echo lang('common_cost_price');?></dt>
																<dd><a href="#" id="cost_price_<?php echo $line;?>" class="xeditable xeditable-cost-price" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($item['cost_price'])); ?>" data-pk="1" data-name="cost_price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_cost_price')); ?>"><?php echo to_currency($item['cost_price']); ?></a></dd>										
																<?php
															}
															?>
															
															
															
															<dt><?php echo lang('common_description') ?></dt>
															<dd>
																<?php if(isset($item['allow_alt_description']) && $item['allow_alt_description']==1) { ?>
																	<a href="#" id="description_<?php echo $line;?>" class="xeditable editable editable-click " data-type="text" data-pk="1" data-name="description" data-value="<?php echo H($item['description']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('sales_description_abbrv')); ?>"><?php echo character_limiter(H($item['description']), 50); ?></a>
																<?php	}
																else
																{
																	if ($item['description']!='')
																	{
																		echo $item['description'];
																	}
																	else
																	{
																		echo lang('common_none');
																	}
																}
																?>

															</dl>
														</td>
													</tr>
												<?php }} }
												else{
													$cart_count = 0;
													foreach ($itemsContainsLine as $itemsContainsLineIndex => $itemContainsLine) {
														?>
														<tr>
															<td colspan="8" style="text-align: left; font-size: 14px; font-weight: bold;">
																<p id = "itemsContainLineName_<?php echo $itemsContainsLineIndex ?>">Sản phẩm: <?php echo $itemContainsLine['itemName']?> <i id = "itemsContainLineNameEdit_<?php echo $itemsContainsLineIndex ?>"  class=" itemsContainLineNameEdit icon ti-pencil"></i></p>
																<div id ="form_<?php echo $itemsContainsLineIndex ?>" class = "hidden">
																	<div class="form-group col-lg-10">
																		<input id ="text_<?php echo $itemsContainsLineIndex ?>" type="text" value ="<?php echo $itemContainsLine['itemName']?>" class ="form-control">
																	</div>
																	<div class="form-group">
																		<button id="itemsContainLineBtnCheck_<?php echo $itemsContainsLineIndex ?>" class ="itemsContainLineBtnCheck btn btn-success"><i class="icon ti-check"></i></button>
																		<button id="itemsContainLineBtnClose_<?php echo $itemsContainsLineIndex ?>" class ="itemsContainLineBtnClose btn btn-danger"><i class="icon ti-close"></i></button>
																	</div>
																</div>
																
															</td>
														</tr>
														<?php
														foreach($itemContainsLine['line'] as $line1) {
															foreach($cart as $line=>$item) { 
																if($line1 == $line) {
																	if($item['name'] != lang('common_discount')) {
																		if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
																		{
																			$cart_count = $cart_count + $item['quantity'];
																		}
																		$listItemAtrribute = '';
																		
																		if (!empty($cartItemsAttribute[$line])) {
																			foreach ($cartItemsAttribute[$line] as $itemAttibute) {
																				$listItemAtrribute .='<option value ="'.$itemAttibute->attribute_id.'">'. $itemAttibute->name.'</option>';
																			}
																		}

																		?>
																		<tr class="register-item-details">
																			<td class="text-center">
																				<?php if (!$isStockOut) {?>
																					<?php echo anchor("sales/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item', 'tabindex' => '-1'));?> </td>
																				<?php }?>
																				<td> 
																					<a tabindex = "-1" class="register-item-name" data-pk="1" data-url="<?php echo site_url('sales/editItemName/'.$line); ?>" data-item-url="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-line = "<?php echo $line?>" ><?php echo H($item['name']); ?></a>&nbsp;<i id = "itemsNameEdit_<?php echo $line ?>" data-type="textarea" class="editable editable-pre-wrapped editable-click  itemsNameEdit icon ti-pencil"></i>
																				</td>
																				<td class="text-center">
																					<?php if ($this->Employee->has_module_action_permission('sales', 'edit_sale_price', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
																						<a href="#" id="price_<?php echo $line;?>" class="xeditable editable editable-click  xeditable-price" data-number-type="unsigned" data-validate-number="true" data-name = "price" data-type ="text" data-value="<?php echo to_currency_without_unit($item['price']); ?>" data-pk="1" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency($item['price'],10); ?></a>
																					<?php } else {
																						echo to_currency($item['price'],10); 
																					}	?>
																					
																				</td>
																				<td class="text-center"><?php echo to_currency($item['calculatedPrice'],10); ?></td>
																				<td class="text-center">
																					<?php if (!$isStockOut) {?>
																						<a href="#" id="quantity_<?php echo $line;?>" class="xeditable editable editable-click " dataEditable = "<?php echo ($item['item_category_id'] != 178)?'false':'true';?>" data-type ="text" data-number-type="unsigned" data-validate-number="true"  data-pk="1" data-name="quantity" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item['quantity']); ?></a>
																					<?php } elseif ($isStockOut == 2) {
																						$qtyStockOut = !empty($stockOutItems[$item['item_id']]['qty']) ? $stockOutItems[$item['item_id']]['qty'] : 0;
																						echo $qtyStockOut . '/' . to_quantity(abs($item['quantity']));
																					} else {
																						echo to_quantity(abs($item['quantity']));
																					}?>
																				</td>
																				
																				<td class="text-center">
																					<?php if (!$isStockOut) {?>
																						<a id="measure_<?php echo $line; ?>" class="measure_item <?php echo empty($item['measure_id']) ? 'editable-disabled' : 'xeditable'; ?>" data-type="select"  data-validate-number="true"  data-value="<?php echo $item['measure_id']; ?>" data-pk="2" data-source="<?php echo site_url("items/measures/" . $item['item_id']);?>" data-name="measure" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_measure') ?>"><?php echo $item['measure']; ?></a>
																					<?php } else { echo $item['measure']; }?>
																				</td>
																				
																				<td class="text-center">
																					<?php if (!$isStockOut && $line != $line_for_flat_discount_item && $this->Employee->has_module_action_permission('sales', 'give_discount', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
																						<a href="#" id="discount_<?php echo $line;?>" class="xeditable" data-type="text"  data-validate-number="true"  data-pk="1" data-name="discount" data-value="<?php echo to_quantity($item['discount']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_discount_percent') ?>"><?php echo to_quantity($item['discount']); ?>%</a>						
																					<?php }else{ ?>
																						
																						<?php echo to_quantity($item['discount']); ?>%
																						
																					<?php }	?>
																				</td>
																				<td class="text-center"><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
																			</tr>
																			<tr class="register-item-bottom">
																				<td class="text-center cbRegister">
																					<?php if($this->config->item('company_user') == 'remHaMy'):?>
																						<input type="checkbox" class = "inputCbResgister" data-line = "<?php echo $line; ?>" id="checkbox_<?php echo $line; ?>">
																						<label for="checkbox_<?php echo $line; ?>"><span></span></label>
																					<?php endif;?>
																				</td>
																				<td colspan="7">
																					<dl class="register-item-extra-details dl-horizontal">
																						<?php if (!empty($qtyStockOut)){ ?>
																							<dt><?php echo 'Đã xuất:'; ?></dt>										
																							<dd><?php echo $qtyStockOut; ?></dd>
																						<?php } ?>
																						
																						<?php
																						
																						if (!$this->config->item('always_use_average_cost_method') && $item['change_cost_price'] && $this->Employee->has_module_action_permission('sales', 'edit_sale_cost_price', $this->Employee->get_logged_in_employee_info()->person_id))
																						{
																							?>
																							<dt><?php echo lang('common_cost_price');?></dt>
																							<dd><a href="#" id="cost_price_<?php echo $line;?>" class="xeditable xeditable-cost-price" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($item['cost_price'])); ?>" data-pk="1" data-name="cost_price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_cost_price')); ?>"><?php echo to_currency($item['cost_price']); ?></a></dd>										
																							<?php
																						}
																						?>
																						
																						
																						
																						<dt><?php echo lang('common_description') ?></dt>
																						<dd>
																							<?php if(isset($item['allow_alt_description']) && $item['allow_alt_description']==1) { ?>
																								<a href="#" id="description_<?php echo $line;?>" class="xeditable" data-type="text" data-pk="1" data-name="description" data-value="<?php echo H($item['description']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('sales_description_abbrv')); ?>"><?php echo character_limiter(H($item['description']), 50); ?></a>
																							<?php	}
																							else
																							{
																								if ($item['description']!='')
																								{
																									echo $item['description'];
																								}
																								else
																								{
																									echo lang('common_none');
																								}
																							}
																							?>

																							<!-- Serial Number if exists -->
																						</dl>
																						<?php if ($this->config->item('company_user') =='remHaMy'):?>
																							<dd style="<?php echo $item['xoay_kho']? 'color:green':'' ?>"><?php echo $item['xoay_kho']? 'Được phép xoay khổ': ''?></dd>
																							<dd style="<?php echo $item['item_category_id'] == COMPLETE_PRODUCT_CATEGORY? 'color:red':'' ?>"><?php echo $item['item_category_id'] == COMPLETE_PRODUCT_CATEGORY? 'Sản phẩm nguyên chiếc': ''?></dd>
																							<dd style="<?php echo $item['stop_producing']? 'color:red':'' ?>"><?php echo $item['stop_producing']? 'Đã dừng sản xuất': ''?></dd>
																						<?php endif;?>
																					</td>
																				</tr>
																			<?php } break;}}}
																		} ?>
																		<tr><td colspan="8" style= "text-align: center;">--------------------------------------------------------------------------------</td></tr>
																		<?php							
																		foreach($cart as $line=>$item) { 
																			$flag = true;
																			foreach ($itemsContainsLine as $itemContainsLine) {
																				foreach($itemContainsLine['line'] as $line1) {
																					if($line1 == $line) {
																						$flag = false;
																					}
																				}
																			}
																			
																			if($item['name'] != lang('common_discount') && $flag) {
																				if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
																				{
																					$cart_count = $cart_count + $item['quantity'];
																				}
																				$listItemAtrribute = '';
																				
																				if (!empty($cartItemsAttribute[$line])) {
																					foreach ($cartItemsAttribute[$line] as $itemAttibute) {
																						$listItemAtrribute .='<option value ="'.$itemAttibute->attribute_id.'">'. $itemAttibute->name.'</option>';
																					}
																				}

																				?>
																				<tr class="register-item-details">
																					<td class="text-center">
																						<?php if (!$isStockOut) {?>
																							<?php echo anchor("sales/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item', 'tabindex' => '-1'));?> </td>
																						<?php }?>
																						<td> 
																							<a tabindex = "-1" class="register-item-name" data-pk="1" data-url="<?php echo site_url('sales/editItemName/'.$line); ?>" data-item-line-not-set = "true" data-item-url="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-line = "<?php echo $line?>" ><?php echo H($item['name']); ?></a>&nbsp;<i id = "itemsNameEdit_<?php echo $line ?>"   data-type="textarea" class="editable editable-pre-wrapped editable-click  itemsNameEdit icon ti-pencil"></i>
																						</td>
																						<td class="text-center">
																							<?php if ($this->Employee->has_module_action_permission('sales', 'edit_sale_price', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
																								<a href="#" id="price_<?php echo $line;?>" class="xeditable xeditable-price" data-number-type="unsigned" data-validate-number="true" data-name = "price" data-type ="text" data-value="<?php echo to_currency_without_unit($item['price']); ?>" data-pk="1" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency($item['price'],10); ?></a>
																							<?php } else {
																								echo to_currency($item['price'],10); 
																							}	?>
																							<td class="text-center"><?php echo to_currency($item['calculatedPrice'],10); ?></td>
																						</td>
																						<td class="text-center">
																							<?php if (!$isStockOut) {?>
																								<a href="#" id="quantity_<?php echo $line;?>" class="<?php echo ($item['item_category_id'] == COMPLETE_PRODUCT_CATEGORY && $configRemHaMy)? 'xeditable-complete-product':'xeditable'?>" data-type ="<?php echo ($item['item_category_id'] == COMPLETE_PRODUCT_CATEGORY && $configRemHaMy)?'size':'text'?>" data-number-type="unsigned" data-validate-number="true"  data-pk="1" data-name="quantity" data-url="<?php echo ($item['item_category_id'] == COMPLETE_PRODUCT_CATEGORY && $configRemHaMy)?site_url('sales/editCompleteProductItem/'.$line):site_url('sales/edit_item/'.$line)?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item['quantity']); ?></a>
																							<?php } elseif ($isStockOut == 2) {
																								$qtyStockOut = !empty($stockOutItems[$item['item_id']]['qty']) ? $stockOutItems[$item['item_id']]['qty'] : 0;
																								echo $qtyStockOut . '/' . to_quantity(abs($item['quantity']));
																							} else {
																								echo to_quantity(abs($item['quantity']));
																							}?>
																							
																						</td>
																						
																						<td class="text-center">
																							<?php if (!$isStockOut) {?>
																								<a id="measure_<?php echo $line; ?>" class="measure_item <?php echo empty($item['measure_id']) ? 'editable-disabled' : 'xeditable'; ?>" data-type="select"  data-validate-number="true"  data-value="<?php echo $item['measure_id']; ?>" data-pk="2" data-source="<?php echo site_url("items/measures/" . $item['item_id']);?>" data-name="measure" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_measure') ?>"><?php echo $item['measure']; ?></a>
																							<?php } else { echo $item['measure']; }?>
																						</td>
																						
																						<td class="text-center">
																							<?php if (!$isStockOut && $line != $line_for_flat_discount_item && $this->Employee->has_module_action_permission('sales', 'give_discount', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
																								<a href="#" id="discount_<?php echo $line;?>" class="xeditable" data-type="text"  data-validate-number="true"  data-pk="1" data-name="discount" data-value="<?php echo to_quantity($item['discount']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_discount_percent') ?>"><?php echo to_quantity($item['discount']); ?>%</a>						
																							<?php }else{ ?>
																								
																								<?php echo to_quantity($item['discount']); ?>%
																								
																							<?php }	?>
																						</td>
																						<td class="text-center"><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
																					</tr>
																					<tr class="register-item-bottom">
																						<td class="text-center cbRegister">
																							<?php if($this->config->item('company_user') == 'remHaMy'):?>
																								<input type="checkbox" class = "inputCbResgister" data-line = "<?php echo $line; ?>" id="checkbox_<?php echo $line; ?>">
																								<label for="checkbox_<?php echo $line; ?>"><span></span></label>
																							<?php endif;?>
																						</td>
																						<td colspan="7">
																							<dl class="register-item-extra-details dl-horizontal">
																								
																								
																								<dt><?php echo lang('common_description') ?></dt>
																								<?php if ($this->config->item('company_user') =='remHaMy'):?>
																									<dd style="<?php echo $item['xoay_kho']? 'color:green':'' ?>"><?php echo $item['xoay_kho']? 'Được phép xoay khổ': ''?></dd>
																									<dd style="<?php echo $item['item_category_id'] == COMPLETE_PRODUCT_CATEGORY? 'color:red':'' ?>"><?php echo $item['item_category_id'] == COMPLETE_PRODUCT_CATEGORY? 'Sản phẩm nguyên chiếc': ''?></dd>
																									<dd style="<?php echo $item['stop_producing']? 'color:red':'' ?>"><?php echo $item['stop_producing']? 'Đã dừng sản xuất': ''?></dd>
																								<?php endif;?>
																								
																							</dl>
																						</td>
																					</tr>
																				<?php }}?>
																			<?php }} ?>
																		</tbody>
																	</table>
																	
																</div>

																<!-- End of Sales or Return Mode -->
															<?php } else {  ?>

																<table id="register"  class="table table-hover ">

																	<thead>
																		<tr class="register-items-header">
																			<?php if($mode == 'assigment') : ?>
																				<th></th>
																			<?php endif; ?>

																			<th ><?php echo lang('sales_item_name'); ?></th>
																			<th ><?php echo lang('common_payment_amount'); ?></th>
																		</tr>
																	</thead>
																	<tbody id="cart_contents">
																		<?php
																		foreach(array_reverse($cart, true) as $line=>$item)	
																		{
																			?>
																			
																			<tr id="reg_item_top" >
																				<?php if($mode == 'assigment') : ?>
																					<td class="text-center">
																						<?php if (!$isStockOut) {?>
																							<?php echo anchor("sales/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item', 'tabindex' => '-1'));?> </td>
																						<?php }?>
																					<?php endif; ?>					
																					<td class="text text-center text-success"><a tabindex = "-1" href="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-toggle="modal" data-target="#myModal" ><?php echo H($item['name']); ?></a></td>
																					<td class="text-center">
																						<?php
																						echo form_open("sales/edit_item/$line", array('class' => 'line_item_form', 'autocomplete'=> 'off')); 	

																						?>
																						<a href="#" id="price_<?php echo $line; ?>" class="xeditable editable editable-click " data-validate-number="true" data-type="text" data-value="<?php echo to_currency_no_money($item['price'],10); ?>" data-pk="1" data-name="price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency_no_money($item['price'],10); ?></a>
																						<?php
																						echo form_hidden('quantity',to_quantity($item['quantity']));
																						echo form_hidden('description','');
																						echo form_hidden('serialnumber', '');
																						?>
																						
																					</form>		
																				</td>
																			</tr>
																			
																			
																			
																		<?php } /*Foreach*/?>
																	</tbody>
																</table>

															</div>

														<?php }  ?>
														<!-- End of Store Account Payment Mode -->

													</div>
													<!-- /.Register Items -->
												</div>
												<!-- /.Col-lg-8 @end of left Column -->

												<!-- col-lg-4 @start of right Column -->
												<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 no-padding-right">
													<div class="register-box register-right">

														<!-- If customer is added to the sale -->
														<?php if(isset($customer)) { ?>	
															<!-- Customer Badge when customer is added -->
															<div class="customer-badge" style="min-height: 30px">

																<div class="details">
																	<?php if(!$this->config->item('hide_customer_recent_sales') && isset($customer)) { ?>
																		<a style="color: red" class="name"><?php echo $customer; ?></a>
																	<?php } else if(isset($customer)) { ?>
																		<a href="<?php echo site_url('customers/view/'.$customer_id.'/1'); ?>" class="name"><?php echo $customer; ?></a>
																	<?php }else { ?>
																		<?php echo $customer; ?>
																	<?php } ?>
																	<?php if ($this->config->item('customers_store_accounts') && isset($customer_balance)) {?>
																		<div class="<?php echo $is_over_credit_limit ? 'text-danger' : 'text-success'; ?> balance"><?php echo $this->config->item('customer_balance').': '.to_currency($customer_balance); ?></div>
																	<?php } ?>

																	<?php if ($this->config->item('customers_store_accounts') && isset($customer_balance_2)) {?>
																		<div class="<?php echo $is_over_credit_limit ? 'text-danger' : 'text-success'; ?> balance"><?php echo $this->config->item('customer_balance_2').': '.to_currency($customer_balance_2); ?></div>
																	<?php } ?>

																	<?php if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple' && isset($sales_until_discount)) {?>
																		<div class="<?php echo $sales_until_discount > 0 ? 'text-danger' : 'text-success'; ?> sales_until_discount" ><?php echo lang('common_sales_until_discount').': '.to_quantity($sales_until_discount).($sales_until_discount <= 0 && !$redeem ?' ['.anchor('sales/redeem_discount', lang('sales_redeem'), array('id' => 'redeem_discount')).']' : ($redeem ? ' ['.anchor('sales/unredeem_discount', lang('sales_unredeem'), array('id' => 'unredeem_discount')).']' : '')) ?></div>
																	<?php } ?>
																	
																	<?php if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && isset($points)) {?>
																		<div class="<?php echo $points < 1 ? 'text-danger' : 'text-success'; ?> points"><?php echo lang('common_points').': '.to_quantity($points); ?></div>
																	<?php } ?>
																	

																	<!-- Customer Email  -->
																	<?php if(!empty($customer_email)) { ?>
																		<span class="email">
																			<?php echo character_limiter(H($customer_email), 25); ?>
																		</span>
																	<?php } ?>

																</div>

															</div>
															<div class="customer-action-buttons">
																<?php 
																if($sale_mode != 'store_account_payment' && $sale_mode != 'vat_order') {
																	?>
																	<?php if(!empty($customer_email)) { ?>
               <!--  <a href="#" class="btn <?php echo (boolean)$email_receipt ? 'email-checked' : '';?>" id="toggle_email_receipt">
                    <i class="ion-android-mail"></i>
                    <?php #echo lang('common_email_receipt'); ?>?
                </a> -->
            <?php } else { ?>
             <!--    <a href="<?php echo site_url('customers/view/'.$customer_id.'/1');  ?>" class="btn" >
                    <i class="ion-ios-compose-outline"></i>
		               <?php echo lang('common_update'); ?>
		           </a> -->
		           
		       <?php } ?>
		       <?php
		   }else {
		   	?>
		   	<a href="javascript:;" class="btn" id="cong_no_btn" onclick="load_sale_order_modal('<?php echo $sale_mode; ?>');">
		   		<i class="ion-android-mail"></i>
		   		Hóa đơn
		   	</a>
		   	<?php
		   }
		   ?>
		   
		   <a href="<?php echo base_url('sales/delete_customer') ?>" id="delete_customer" class="btn btn-primary hide">Xóa</a>
		   <?php if($this->config->item('sales_add_more_cus_for_visa')):?>
		   	<div class="form-group clearfix">
		   		
		   		<div id = "add_more_customer" class="col-sm-12 col-md-12 col-lg-12">
		   			<p><?php  echo lang('sales_add_more_cus_for_visa').': ';?></p>
		   			<p for="sales_add_more_cus_for_visa" class="text-danger errors"></p>
		   			<select class="form-control" name="sales_add_more_cus_for_visa[]" id="sales_add_more_cus_for_visa" multiple>
		   				<?php  foreach ($list_customer as $key => $customer_):?>
		   					<option value="<?php echo $customer_['person_id']?>" <?php foreach ($more_customers_in_service as $_key => $customers_in_service){ if(in_array($customer_['person_id'],$customers_in_service)){echo 'selected';}}?>><?php echo $customer_['first_name'].' '.$customer_['last_name']; ?></option>
		   				<?php endforeach;?>
		   			</select>		
		   		</div>
		   	</div>
		   <?php endif;?>
		</div>

	<?php }  else {  ?>

		<div class="customer-form">

			<!-- if the customer is not set , show customer adding form -->
			<?php echo form_open("sales/select_customer",array('id'=>'select_customer_form', 'autocomplete'=> 'off','class'=>'form-inline')); ?>
			<div class="input-group contacts">
				<span class="input-group-addon">
					<?php echo anchor("customers/view/-1/1","<i class='ion-person-add'></i>", array('class'=>'none','title'=>lang('common_new_customer'), 'id' => 'new-customer','tabindex' => '-1')); ?>
				</span>
				<input type="text" id="customer" name="customer" class="add-customer-input" placeholder="<?php echo lang('sales_start_typing_customer_name').($this->config->item('require_customer_for_sale') ? ' ('.lang('common_required').')' : '');?>">
			</div>
		</form>

	</div> 
<?php } ?>
</div>

<div class="register-box register-summary paper-cut">
	<div style="margin: 15px 15px;">
		<div class="form-group">
			<label for="sale_date"><?php echo lang('sale_time_create'); ?>: </label>
			<div class="input-group date">
				<span class="input-group-addon bg">
					<i class="ion ion-ios-calendar-outline"></i>
				</span>
				<input type="text" name="sale_time" value="<?php echo strlen($sale_time) ? date('d/m/Y', strtotime($sale_time)) : date('d/m/Y', strtotime('now')); ?>" id="sale_date" class="form-control" style="z-index: 0;" />
			</div>
		</div>
			<!-- <div class="form-group">
				<label><?php echo lang('sale_creator'); ?>: </label>
				<p class="form-control-static"><?php echo $sale_creator['first_name'] . ' ' . $sale_creator['last_name'];?></p>
			</div> -->
			
			<script type="text/javascript">
				$( document ).ready(function() {
					$('#sale_date').datetimepicker({format: JS_DATE_FORMAT, locale: LOCALE, defaultDate: null, ignoreReadonly: IS_MOBILE ? true : false});
					$('#sale_date').on('dp.change', function(e){
						$.ajax({
							type: "POST",
							url: BASE_URL + 'sales/change_sale_time',
							data: {
								sale_time_date : $('#sale_date').val()
							},
							success: function(string){
								console.log(string);
							}
						});
					});
				});
			</script>
			
			<div class="form-group">
				<label><?php echo lang('sale_supporter');?>: </label>
				<div class="input-group supporter">
					<span class="input-group-addon bg">
						<a href="<?php echo base_url() . 'employees/view/-1'; ?>" class="none" title="Nhân viên mới" tabindex="-1"><i class="ion-person-add"></i></a>
					</span>
					<span class="ui-helper-hidden-accessible"></span>
					<input type="text" id="supporter" name="supporter" class="add-customer-input ui-autocomplete-input form-control" autocomplete="off" style="z-index: 0;">
				</div>
				<div class="n9-autocomplete-result-list" id="supporter_select_list" style="text-align: left;">
					
					<?php 
					$supporter_list_str = '';
					if(!empty($supporter_list)) {
						foreach($supporter_list as $item) {
							$supporter_list_str[] = '<span class="key btn btn-primary">
							<a id="delete-supporter" class="delete-supporter" style="font-size: 1.3em; color: #fb5d5d;" dataid="'. $item['id'] .'"><i class="icon ion-android-cancel" ></i></a>
							<a href="'.base_url().'employees/view/'.$item['id'].'" target="_blank" style="color: #ffffff;">'.$item['name'].'  </a>
							</span>';
						}
						$supporter_list_str = implode('', $supporter_list_str);
					}
					?>
					<?php echo $supporter_list_str; ?>
				</div>
			</div>
			
			<div style="display: none" class="form-group">
				<label for="select-service"><?php echo lang('sale_catalog_service');?>:</label>
				<?php
				$service_value = isset($temp_service_id) ? $temp_service_id : '';
				?>
				<select id="select-service" name="select-service" class="form-control">
					<option><?php echo lang('sale_choose_service_type');?></option>
					<?php if(isset($service_list)) : ?>
						<?php
						if(!empty($service_list)) {
							foreach($service_list as $val) {
								?>
								<option value="<?php echo $val['id']; ?>" <?php echo (($service_value == $val['id'])? 'selected="selected"' : '');?>><?php echo $val['name']; ?></option>
								<?php
							}
						}
						?>
					<?php endif; ?>
				</select>
			</div> 			<div class="form-group">
				<label for="sale_status">Trạng thái: </label>
				<?php 
				$status_value = isset($status_id)? $status_id : '';
				?>
				<select id="sale_status" name="sale_status" class="form-control">
					<option><?php echo lang('common_choose');?></option>
					<?php if(!empty($status_list)) {?>
						<?php foreach($status_list as $status) { ?>
							<option tp="<?php echo $status->status_type; ?>" value="<?php echo $status->status_id;?>" <?php echo ($status_value == $status->status_id)? 'selected="selected"' : ''?>><?php echo $status->status_name;?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label id="comment_label" for="comment"><label id="comment_term_label" for="comment_term"><?php echo lang('sale_size');?>: </label></label>
				<?php echo form_textarea(array('name'=>'comment_term', 'id' => 'comment_term', 'value'=>isset($saleComment['comment_term'])?$saleComment['comment_term']:'','rows'=>'3', 'class'=>'form-control sale-comment')); ?>
			</div>
			<div class="form-group">
				<label id="comment_label" for="comment"><?php echo lang('common_comments'); ?> : </label>
				<?php echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'5', 'class'=>'form-control sale-comment')); ?>
			<!-- 	<?php 		echo form_checkbox(array(
        			'name'=>'show_comment_on_receipt',
        			'id'=>'show_comment_on_receipt',
        			'value'=>'1',
        			'checked'=>(boolean)$show_comment_on_receipt)
        		);
        		echo '<label for="show_comment_on_receipt"><span></span>'.lang('sales_comments_receipt').'</label>'; ?> -->
        	</div>
        	<?php 
        	$amount_tendered_value = 0;
        	if($payment_total > $sale_total) {
        		$amount_tendered_value = $payment_total - $sale_total;
        	}else {
        		$amount_tendered_value = $sale_total - $payment_total;
        	}
        	
        	?>
        	
        </div>
        <?php if(count($cart) > 0 && isset($customer)): ?>
        <div class="register-right">
        	<div id="finish_sale" class="finish-sale">
        		<?php if ($customer_required_check && !isset($contract_name)): ?>
        			<?php echo form_open("sales/cancel_sale",array('id'=>'cancel_sale_form', 'autocomplete'=> 'off', 'class' => 'col-md-5')); ?>
        			<input type="hidden" name="total_payment" value="<?php echo $total; ?>">
        			<input type="hidden" name="amount_tendered" value="<?php echo $amount_tendered_value;?>">
        			<input type="hidden" name="is_add_payment_click" value="1">
        			<input type="hidden" name="payment_type" value="Tiền mặt">
        			<!-- Lưu nợ đầu -->
        			<input type="hidden" name="customer_balance" value="<?php if(isset($customer_balance))	echo $customer_balance; ?>">
        			<?php if($this->sale_lib->get_suspended_sale_id()) { ?>
        				<button id="layaway_sale_button" class="btn btn-blue btn-block" type="button">Lưu</button>
        			<?php } else { ?>
        				<button id="layaway_sale_button" class="btn btn-blue" type="button">Lưu</button>
        			<?php } ?>
        		</form>
        		<?php echo form_open("sales/complete",array('id'=>'finish_sale_form', 'autocomplete'=> 'off', 'class' => 'col-md-7 text-right')); ?>
        		<input type="button" class="btn btn-blue" id="finish_sale_button" value="Đóng nhu cầu" />
        		<?php echo form_close();?>
        	<?php endif; ?>
        </div>
    </div>
<?php endif;?>
</div>
</div>








<div id="keyboardhelp" style="display: none;background-color:white;padding:12px;" title="<?php echo lang('sales_keyboard_help_title');?>">
	
	<div>
		<span>[F4]  => <?php echo lang('sales_completes_currrent_sale');?></span><br />
		<span>[F2]  => <?php echo lang('sales_set_focus_item');?></span><br />
		<span>[F7]  => <?php echo lang('sales_set_focus_payment');?></span><br />
		<span>[ESC] => <?php echo lang('sales_esc_cancel_sale');?></span><br>
	</div>
	
</div>
</div>

<div class="modal fade look-up-receipt" id="look-up-receipt" role="dialog" aria-labelledby="lookUpReceipt" aria-hidden="true">
	<div class="modal-dialog customer-recent-sales">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="lookUpReceipt"><?php echo lang('lookup_receipt') ?></h4>
			</div>
			<div class="modal-body">
				<?php echo form_open("sales/receipt_validate", array('class'=>'look-up-receipt-form','autocomplete'=> 'off')); ?>				
				<span class="text-danger text-center has-error look-up-receipt-error"></span>
				<input type="text" class="form-control text-center" name="sale_id" id="sale_id" placeholder="<?php echo lang('sales_id') ?>">
				<?php echo form_submit('submit_look_up_receipt_form',lang("lookup_receipt"),'class="btn btn-block btn-primary"'); ?>
				<?php echo form_close(); ?>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
	function calculatedPrice(line){
		var va = document.getElementById(line).value;
		$.ajax({
			url:'sales/calculatedPrice',
			type:'POST',
			data:{post_customers:va,line:line},
			success:function(data)
			{
				
			}
		});
	}
</script>
<?php if (!$this->config->item('disable_sale_notifications')) { ?>
	<script type="text/javascript">
		<?php
		if(isset($error))
		{
			echo "show_feedback('error', ".json_encode($error).", ".json_encode(lang('common_error')).");";

		}

		if (isset($warning))
		{
			echo "show_feedback('warning', ".json_encode($warning).", ".json_encode(lang('common_warning')).");";

		}

		if (isset($success))
		{
			if (isset($success_no_message))
			{
				?>
				if (ENABLE_SOUNDS)
				{
					$.playSound(BASE_URL + 'assets/sounds/success');
				}
				<?php
			}
			else
			{
				echo "show_feedback('success', ".json_encode($success).", ".json_encode(lang('common_success')).");";
			}
		}
		
		if ($this->session->flashdata('cash_drawer_add_subtract_message')) 
		{
			echo "show_feedback('success', ".json_encode($this->session->flashdata('cash_drawer_add_subtract_message')).", ".json_encode(lang('common_success')).");";
		}
		
		?>
	</script>
<?php } ?>

<script type="text/javascript" language="javascript">
	<?php if ($configRemHaMy): ?>
    //remove xeditable class
    $('.xeditable').each(function(event){
    	if (($(this).attr('id').indexOf("quantity") == 0 && $(this).attr('dataEditable') == "false")) {
    		$(this).removeClass('xeditable editable editable-click editable-open');
    		$(this).removeAttr('href');
    		
    	}
    });
    $('body').on('click',function(event){
    	if ($('input[name="mSquare"]').is(':focus')) {
    		$('input[name="height"]').val('');
    		$('input[name="width"]').val('');           
    		$('input[name="height"]').prop('readonly', true);
    		$('input[name="width"]').prop('readonly', true);
    		$('input[name="mSquare"]').prop('readonly', false);
    	}       
    	if ($('input[name="height"]').is(':focus') || $('input[name="width"]').is(':focus') ) {
    		$('input[name="mSquare"]').val('');
    		$('input[name="height"]').prop('readonly', false);
    		$('input[name="width"]').prop('readonly', false);
    		$('input[name="mSquare"]').prop('readonly', true);
    	}
    });
    
    $('.xeditable-complete-product').editable({
    	validate: function(value) {
    		if (value['mSquare'] == '') {
    			value['width'] = value['width'].replace(/\<?php echo $thousands_separator; ?>/g, "");
    			value['width'] = value['width'].replace(/\<?php echo $decimal_point; ?>/g, ".");            
    			value['height'] = value['height'].replace(/\<?php echo $thousands_separator; ?>/g, "");
    			value['height'] = value['height'].replace(/\<?php echo $decimal_point; ?>/g, ".");
    			if ($.isNumeric(value['width']) == '' && $(this).data('validate-number')) {
    				return <?php echo json_encode(lang('common_only_numbers_allowed').':' .'Chiều rộng'); ?>;
    			} else if($.isNumeric(value['height']) == '' && $(this).data('validate-number') ) {
    				return <?php echo json_encode(lang('common_only_numbers_allowed').':' .'Chiều cao'); ?>;
    			} else if ($(this).data('number-type') == 'unsigned' && value['width'] < 0) {
    				return <?php echo json_encode('Không được phép nhập số âm : Chiều rộng'); ?>;
    			} else if ($(this).data('number-type') == 'unsigned' && value['height']<0) {
    				return <?php echo json_encode('Không được phép nhập số âm: Chiều cao'); ?>;
    			} else if (value['width'] ==0) {
    				return <?php echo json_encode('Giá trị chiều rộng phải lớn hơn 0'); ?>;
    			} else if (value['height']==0) {
    				return <?php echo json_encode('Giá trị chiều cao phải lớn hơn 0'); ?>;
    			}
    		} else {
    			value['mSquare'] = value['mSquare'].replace(/\<?php echo $thousands_separator; ?>/g, "");
    			value['mSquare'] = value['mSquare'].replace(/\<?php echo $decimal_point; ?>/g, ".");            
    			if ($(this).data('number-type') == 'unsigned' && value['mSquare'] < 0) {
    				return <?php echo json_encode('Không được phép nhập số âm : diện tích'); ?>;
    			} else if (value['mSquare']==0) {
    				return <?php echo json_encode('Giá trị diện tích phải lớn hơn 0'); ?>;
    			}
    		}
    	},

    	success: function(response, newValue) {
    		last_focused_id = $(this).attr('id');
    		$("#register_container").html(response);
    	}
    }); 
    $('.itemsNameEdit').click(function(e){    
    	e.stopPropagation();
    	$(this).siblings().editable({
    		mode:'inline',
    		type:'textarea',
    		onblur:'ignore',
    		send: 'always',
    		rows: 2,
    		success: function(response, newValue) {
    			last_focused_id = $(this).attr('id');
    			$("#register_container").html(response);
    		}
    	});
    	$(this).siblings().editable('toggle');
    	$(this).hide();
    });
    $(document).on('click', '.editable-cancel, .editable-submit', function(){
    	$('.itemsNameEdit').show();
    } );

<?php endif;?>  

var submitting = false;
$('.register-item-name').click( function(e){
	e.preventDefault();
	$('#itemsModal').remove();
	
	$.ajax({
		url: $(this).attr('data-item-url') ,
		type:"post",
		dataType:'json',
		data:{line: $(this).attr('data-line'), isInItemContainsLine: $(this).attr('data-item-line-not-set')},
		success: function(response)
		{
			$('#itemsModal').remove();
			$('body').append(response);
			$('#itemsModal').modal('show');
		}
	}); 
});
$(document).ready(function(){

	var panata = $('#sale_status').children("option:selected").attr('tp');
	if(panata==1)
	{
		
		$('#finish_sale_button').hide();
	}
	if(panata==2)
	{
		$('#layaway_sale_button').hide();
	}


	

	date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
	$('#sales_add_more_cus_for_visa').selectize({
		plugins: ['remove_button'],
		delimiter: ',',
		persist: false,
		// create: function(input) {
		// 	return {
		// 		value:input,
		// 		text: input
		// 	}
		// }
	});
	$('#sales_add_more_cus_for_visa').change(function(){
		$.ajax({
			url:'sales/add_more_customer_to_service',
			type:'POST',
			data:{post_customers:$(this).val()},
			success:function(data)
			{
				
			}
		});
	});
	$('#saveItemName').click(function(){
		var itemName = $('#inputItemName').val();
		if (itemName != '') {
			var row_ids = new Array();
			$("#register tbody :checkbox:checked").each(function()
			{
				row_ids.push($(this).attr('data-line'));
			});
			$('.manage-row-options').addClass('hidden');
			$.ajax({
				url: 'sales/groupCartItem',
				type: 'post',
				
				data: {line: row_ids, itemName: itemName},
				beforeSend:salesBeforeSubmit,
				success: function(data){
					$('#register_container').html(data);
				}
			});
		} else {
			toastr.error('Bạn chưa nhập tên sản phẩm', 'Thông báo')
		}

	});
	
	$('.itemsContainLineNameEdit').click(function(){
		var itemContainsLinePosition = $(this).attr("id").replace("itemsContainLineNameEdit_",'');
		$('#itemsContainLineName_'+itemContainsLinePosition).addClass('hidden');
		$('#form_'+itemContainsLinePosition).removeClass('hidden');
	});
	$('.itemsContainLineBtnClose').click(function(){
		var itemContainsLinePosition = $(this).attr("id").replace("itemsContainLineBtnClose_",'');
		$('#itemsContainLineName_'+itemContainsLinePosition).removeClass('hidden');
		$('#form_'+itemContainsLinePosition).addClass('hidden');
	}); 
	
	$('.itemsContainLineBtnCheck').click(function(){
		var arrItemContainPosition = [];
		var arrNewItemContainName = {};
		$('.itemsContainLineBtnCheck').each(function(){
			var itemContainsLinePosition = $(this).attr("id").replace("itemsContainLineBtnCheck_",'');
			arrItemContainPosition.push(itemContainsLinePosition);
			arrNewItemContainName[itemContainsLinePosition] = $('#text_'+itemContainsLinePosition).val();
		});
		$.ajax({
			url: 'sales/changeItemContainsLineName',
			type:"post",
			data:{  position: arrItemContainPosition,
				newName: arrNewItemContainName
			},
			beforeSend:salesBeforeSubmit,
			success: function(data) {
				$('#register_container').html(data);
			}
		});
	}); 
	
	$(document).on('change','.selectAttribute', function(){
		var value = $(this).attr('id');
		$.ajax({
			url: 'sales/selectedAttribute',
			type: 'post',
			data: {line: $(this).attr('id'), attributeId: $(this).val()},
			success: function(data){
				$('body #text_'+value).val(data);
			}
		});
	});
	
	$( "#keyboard_toggle" ).click(function(e) {
		e.preventDefault();
		$( "#keyboardhelp" ).toggle();
	});
	
	$.fn.editable.defaults.mode = 'popup';     

	$('.fullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvFullScreen();
		$.get('<?php echo site_url("home/set_fullscreen/1");?>');
	});

	$('.dismissfullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvDismissFullscren();
		$.get('<?php echo site_url("home/set_fullscreen/0");?>');
	});
	


	$('.measure_item .xeditable').editable({
		success: function(response, newValue) {
			last_focused_id = $(this).attr('id');
			$("#register_container").html(response);
		}
	});
	$('.xeditable').editable({
		validate: function(value) {
			value = value.replace(/\<?php echo $thousands_separator; ?>/g, "");
			value = value.replace(/\<?php echo $decimal_point; ?>/g, ".");
			if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
				return <?php echo json_encode(lang('common_only_numbers_allowed')); ?>;
			} else if ($(this).data('number-type') == 'unsigned' && value < 0) {
				return <?php echo json_encode('Không được phép nhập số âm'); ?>;
			}
		},
		success: function(response, newValue) {
			last_focused_id = $(this).attr('id');
			$("#register_container").html(response);
		}
	});

	$('.xeditable').on('shown', function(e, editable) {
		editable.input.postrender = function() {
			//Set timeout needed when calling price_to_change.editable('show') (Not sure why)
			setTimeout(function() {
				editable.input.$input.select();
			}, 50);
		};
	});

	$('#amount_tendered').autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>', vMax : 999999999999999999.9999});
	$('body').on('click','#amount_tendered',function(){
		$(this).autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>', vMax : 999999999999999999.9999});
	});


	$('body').on('click','.editable-input input[type="text"]',function(){
		var popover  = $(this).closest('.popover ');
		var prev     = popover.prev();

		var data_name = prev.attr('data-name');
		if(data_name == 'calculatedPrice' || data_name == 'discount_all_flat') {
			$(this).autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>', vMin : -999999999999999999.9999});
		}
	});

	$('body').on('focus','.editable-input input[type="text"]',function(){
		var popover  = $(this).closest('.popover ');
		var prev     = popover.prev();

		var data_name = prev.attr('data-name');
		if(data_name == 'calculatedPrice' || data_name == 'discount_all_flat') {
			$(this).autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>', vMin : -999999999999999999.9999});
		}
	});


	$('.xeditable').on('hidden', function(e, editable) {
		last_focused_id = $(this).attr('id');
		$('#'+last_focused_id).focus();
		$('#'+last_focused_id).select();
	});
	
	<?php
	if (isset($price_zero) && $price_zero)
	{
		?>
		var price_to_change = $('#register a[data-name="price"]').first();
		price_to_change.editable('show');
		<?php
	}
	?>
	
		// Look up receipt form handling
		
		$('#look-up-receipt').on('shown.bs.modal', function() {
			$('#sale_id').focus();
		});
		
		$('.look-up-receipt-form').on('submit',function(e){
			e.preventDefault();

			$('.look-up-receipt-form').ajaxSubmit({
				success:function(response)
				{
					if(response.success)
					{
						window.location.href = '<?php echo site_url("sales/receipt"); ?>/'+$("#sale_id").val();
					}
					else
					{
						$('.look-up-receipt-error').html(response.message);
					}
				},
				dataType:'json'
			});
		});

		//Set Item tier after selection
		$('.item-tiers a').on('click',function(e){
			e.preventDefault();

			$('.selected-tier').html($(this).text());
			$.post('<?php echo site_url("sales/set_tier_id");?>', {tier_id: $(this).data('value')}, function(response)
			{
				$('.item-tiers').slideToggle("fast", function()
				{
					$("#register_container").html(response);					
				});
			});
		});

		//Slide Toggle item tier options
		$('.item-tier').on('click',function(e){
			e.preventDefault();
			$('.item-tiers').slideToggle("fast");
		});

		checkPaymentTypeGiftcard();
		checkPaymentTypePoints();

		// begin
		$('.select-store_acount_payments a').on('click',function(e){
			e.preventDefault();

			$('span.select-store_acount_payment').html($(this).text());
			$.ajax({
				type: "POST",
				url: BASE_URL + 'sales/set_store_account_payment_value',
				data: {
					value : $(this).data('value')
				},
				success: function(string){
					$('.select-store_acount_payments').slideToggle("fast");
					$("#register_container").load('<?php echo site_url("sales/reload"); ?>');
				}
			});

		});

		$('span.select-store_acount_payment').on('click',function(e){
			e.preventDefault();
			$('.select-store_acount_payments').slideToggle("fast");
		});

		// end


		$('#toggle_email_receipt').on('click',function(e) {
			e.preventDefault();
			var checkBoxes = $("#email_receipt");
			checkBoxes.prop("checked", !checkBoxes.prop("checked")).trigger("change");
			$(this).toggleClass('email-checked');

		})

		$('#email_receipt').change(function(e) 
		{	
			e.preventDefault();
			$.post('<?php echo site_url("sales/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
		});

		// Customer form script
		$('#item,#customer, #deliverer, #support').click(function()
		{
			$(this).attr('value','');
		});


		// if #mode is changed
		$('.change-mode').click(function(e){
			e.preventDefault();
			if ($(this).data('mode') == "store_account_payment") { // Hiding the category grid
				$('#show_hide_grid_wrapper, #category_item_selection_wrapper').fadeOut();
			}else { // otherwise, show the categories grid
				$('#show_hide_grid_wrapper, #show_grid').fadeIn();
				$('#hide_grid').fadeOut();
			}
			$.post('<?php echo site_url("sales/change_mode");?>', {mode: $(this).data('mode')}, function(response)
			{
				$("#register_container").html(response);
			});
		});
		

		<?php if (!$this->agent->is_mobile()) { ?>
			<?php if (!$this->config->item('auto_focus_on_item_after_sale_and_receiving'))
			{
				?>
				if (last_focused_id && last_focused_id != 'item')
				{
					$('#'+last_focused_id).focus();
					$('#'+last_focused_id).select();
				}
				<?php 
			}
			else
			{
				?>
				setTimeout(function(){$("#item").focus();}, 10);	
				<?php
			}
			?>
			$(document).focusin(function(event) 
			{
				last_focused_id = $(event.target).attr('id');
			});
			<?php 
		}
		else
		{
			if ($this->config->item('wireless_scanner_support_focus_on_item_field'))
			{
				?>
				setTimeout(function(){$("#item").focus();}, 10);				
			<?php } ?>
			
			
		<?php } ?>	
		
		$('#select_customer_form').ajaxForm({target: "#register_container", beforeSubmit: salesBeforeSubmit});
		$('#add_item_form').ajaxForm({target: "#register_container", beforeSubmit: salesBeforeSubmit, success: itemScannedSuccess});

		if ($("#item").length) {
			$( "#item" ).autocomplete({
				source: '<?php echo site_url("sales/item_search_n9");?>',
				delay: 150,
				autoFocus: false,
				minLength: 0,
				select: function( event, ui ) 
				{
					$( "#item" ).val(ui.item.item_id);
					$('#add_item_form').ajaxSubmit({
						target: "#register_container", 
						beforeSubmit: salesBeforeSubmit, 
						success: itemScannedSuccess
					});
					
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
					'</div>')
				.appendTo(ul);
			};
		}
		
		if ($("#sale-item").length) {
			$( "#sale-item" ).autocomplete({
				source: '<?php echo  site_url("sales/sales_search");?>',
				delay: 150,
				autoFocus: false,
				minLength: 0,
				select: function( event, ui )  {
					$( "#sale-item" ).val(ui.item.sale_id);
					$('#add_item_form').ajaxSubmit({
						target: "#register_container", 
						success: itemScannedSuccess
					});                        
				},
			}).data("ui-autocomplete")._renderItem = function (ul, sale_item) {
				return $("<li class='item-suggestions'></li>")
				.data("sale_item.autocomplete", sale_item)
				.append('<a class="suggest-item">' +
					'<div class="details">' +
					'<div class="name">' + 
					sale_item.label +
					'</div>' +
					'</div>')
				.appendTo(ul);
			};
		}
		<?php if(!isset($customer)) { ?>	

			$( "#customer" ).autocomplete({
				source: '<?php echo site_url("sales/customer_search");?>',
				delay: 150,
				autoFocus: false,
				minLength: 0,
				select: function( event, ui ) 
				{
					$.post('<?php echo site_url("sales/select_customer");?>', {customer: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});
				},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
				return $("<li class='customer-badge suggestions'></li>")
				.data("item.autocomplete", item)
				.append('<a class="suggest-item"><div class="avatar">' +
					'<img src="' + item.avatar + '" alt="">' +
					'</div>' +
					'<div class="details">' +
					'<div class="name">' + 
					item.label +
					'</div>' + 
					'<span class="email">' +
					item.subtitle + 
					'</span>' +
					'</div></a>')
				.appendTo(ul);
			};
			
		<?php } ?>


		$('#customer').blur(function()
		{
			$(this).attr('value',<?php echo json_encode(lang('sales_start_typing_customer_name').($this->config->item('require_customer_for_sale') ? ' ('.lang('common_required').')' : '')); ?>);
		});
		
		$('#change_sale_date_enable').is(':checked') ? $("#change_sale_date_picker").show() : $("#change_sale_date_picker").hide(); 

		$('#change_sale_date_enable').click(function() {
			if( $(this).is(':checked')) {
				$("#change_sale_date_picker").show();
			} else {
				$("#change_sale_date_picker").hide();
			}
		});
		if($( "#deliverer" ).length) {
			$('#deliverer').blur(function()
			{
				$(this).attr('value',<?php echo json_encode(lang('sales_start_typing_deliverer_name')); ?>);
			});
			
			$( "#deliverer" ).autocomplete({
				source: '<?php echo site_url("sales/deliverer_search");?>',
				delay: 150,
				autoFocus: false,
				minLength: 0,
				select: function( event, ui ) 
				{
					$.post('<?php echo site_url("sales/select_deliverer");?>', {deliverer: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});
				},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
				return $("<li class='customer-badge suggestions'></li>")
				.data("item.autocomplete", item)
				.append('<a class="suggest-item"><div class="avatar">' +
					'<img src="' + item.avatar + '" alt="">' +
					'</div>' +
					'<div class="details">' +
					'<div class="name">' + 
					item.label +
					'</div>' + 
					'<span class="email">' +
					item.subtitle + 
					'</span>' +
					'</div></a>')
				.appendTo(ul);
			};
		}

		if($( "#supporter" ).length) {
			
			$( "#supporter" ).autocomplete({
				source: '<?php echo site_url("sales/supporter_search");?>',
				delay: 150,
				autoFocus: false,
				minLength: 0,
				select: function( event, ui ) 
				{
					$.post('<?php echo site_url("sales/select_supporter");?>', {supporter: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});
				},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
				return $("<li class='customer-badge suggestions'></li>")
				.data("item.autocomplete", item)
				.append('<a class="suggest-item"><div class="avatar">' +
					'<img src="' + item.avatar + '" alt="">' +
					'</div>' +
					'<div class="details">' +
					'<div class="name">' + 
					item.label +
					'</div>' + 
					'<span class="email">' +
					item.subtitle + 
					'</span>' +
					'</div></a>')
				.appendTo(ul);
			};

		}

		if($( "#delete-supporter" ).length) {
			$('#delete-supporter').on('click', function(e) {
				var supporterId = $(this).attr('dataid');
				e.preventDefault();
				$.post('<?php echo site_url('sales/delete_supporter')?>', {supporter_id: supporterId}, function(response) {
					$("#register_container").html(response);
				});
			});
		}
		
		$('#comment').change(function() 
		{
			$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()});
		});
		
		$('#show_comment_on_receipt').change(function() 
		{
			$.post('<?php echo site_url("sales/set_comment_on_receipt");?>', {show_comment_on_receipt:$('#show_comment_on_receipt').is(':checked') ? '1' : '0'});
		});
		
		
		date_time_picker_field($("#change_sale_date"), JS_DATE_FORMAT + " "+JS_TIME_FORMAT);
		
		$("#change_sale_date").on("dp.change", function(e) {
			$.post('<?php echo site_url("sales/set_change_sale_date");?>', {change_sale_date: $('#change_sale_date').val()});			
		});
		
		//Input change
		$("#change_sale_date").change(function(){
			$.post('<?php echo site_url("sales/set_change_sale_date");?>', {change_sale_date: $('#change_sale_date').val()});			
		});

		$('#change_sale_date_enable').change(function() 
		{
			$.post('<?php echo site_url("sales/set_change_sale_date_enable");?>', {change_sale_date_enable: $('#change_sale_date_enable').is(':checked') ? '1' : '0'});
		});
		

		$('.delete-item, #delete_customer, .delete-tax').click(function(event)
		{
			event.preventDefault();
			$('.manage-row-options').addClass('hidden');
			$("#register_container").load($(this).attr('href'));	
		});
		
		
		$('#redeem_discount,#unredeem_discount').click(function(event)
		{
			event.preventDefault();
			$("#register_container").load($(this).attr('href'));	
		});

		//Layaway Sale
		$("#layaway_sale_button").click(function(e)
		{
			// <?php 
			// $layaway_msg_confirm = '';
			// if($this->sale_lib->get_suspended_sale_id()) {
			//      $layaway_msg_confirm = lang("sales_confirm_update_sale");
			// } else {
			//      $layaway_msg_confirm = lang("sales_confirm_add_new_sale");
			// } ?>
			// e.preventDefault();
			// bootbox.confirm(<?php echo json_encode($layaway_msg_confirm); ?>, function(result)
			// {
			// 	if(result)
			// 	{


				$.ajax({
					type: "POST",
					url: BASE_URL + 'sales/check_before_complete',
					data: {
					},
					success: function(string){
						var res = $.parseJSON(string);
						if(res.flag == 'false') {
							$("#finish_sale_button").show();
							toastr.error(res.msg, 'Lỗi');
						}else{
							$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function() {
								<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
									window.location = '<?php echo site_url("sales/suspend"); ?>';
								<?php }else { ?>
									$("#register_container").load('<?php echo site_url("sales/suspend"); ?>');
								<?php } ?>
							});
						}
						
					}
				});	

			// 	}
			// });
		});

		//Estimate Sale
		$("#estimate_sale_button").click(function(e)
		{
			e.preventDefault();
			bootbox.confirm(<?php echo json_encode(lang("sales_confirm_suspend_sale")); ?>, function(result)
			{
				if(result)
				{
					$.ajax({
						type: "POST",
						url: BASE_URL + 'sales/check_before_complete',
						data: {
						},
						success: function(string){
							var res = $.parseJSON(string);
							if(res.flag == 'false') {
								$("#finish_sale_button").show();
								toastr.error(res.msg, 'Lỗi');
							}else{
								$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function() {
									<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
										window.location = '<?php echo site_url("sales/suspend/2"); ?>';
									<?php }else { ?>
										$("#register_container").load('<?php echo site_url("sales/suspend/2"); ?>');
									<?php } ?>
								});
							}
							
						}
					});


				}
			});
		});
        //Estimate Sale Genneral
        $("#estimate_sale_button_general").click(function(e)
        {
        	e.preventDefault();
        	bootbox.confirm(<?php echo json_encode(lang("sales_confirm_suspend_sale")); ?>, function(result)
        	{
        		if(result)
        		{
        			$.ajax({
        				type: "POST",
        				url: BASE_URL + 'sales/check_before_complete',
        				data: {
        				},
        				success: function(string){
        					var res = $.parseJSON(string);
        					if(res.flag == 'false') {
        						$("#finish_sale_button").show();
        						toastr.error(res.msg, 'Lỗi');
        					}else{
        						$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function() {
        							<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
        								window.location = '<?php echo site_url("sales/suspend/2/general_receipt"); ?>';
        							<?php }else { ?>
        								$("#register_container").load('<?php echo site_url("sales/suspend/2"); ?>');
        							<?php } ?>
        						});
        					}
        					
        				}
        			});


        		}
        	});
        });
        //Estimate Sale detail
        $("#estimate_sale_button_detail").click(function(e)
        {
        	e.preventDefault();
        	bootbox.confirm(<?php echo json_encode(lang("sales_confirm_suspend_sale")); ?>, function(result)
        	{
        		if(result)
        		{
        			$.ajax({
        				type: "POST",
        				url: BASE_URL + 'sales/check_before_complete',
        				data: {
        				},
        				success: function(string){
        					var res = $.parseJSON(string);
        					if(res.flag == 'false') {
        						$("#finish_sale_button").show();
        						toastr.error(res.msg, 'Lỗi');
        					}else{
        						$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function() {
        							<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
        								window.location = '<?php echo site_url("sales/suspend/2/detail_receipt"); ?>';
        							<?php }else { ?>
        								$("#register_container").load('<?php echo site_url("sales/suspend/2"); ?>');
        							<?php } ?>
        						});
        					}
        					
        				}
        			});


        		}
        	});
        });

		//Cancel Sale
		$("#cancel_sale_button").click(function(e)
		{
			e.preventDefault();
			bootbox.confirm(<?php echo json_encode(lang("sales_confirm_cancel_sale")); ?>, function(result)
			{
				if (result)
				{
					$('#cancel_sale_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit});
				}
			});
		});

		
		// Sử dụng khi bấm nút add_payment_button 
		$("#add_payment_button").click(function(e) {
			e.preventDefault();
			$('#add_payment_form').ajaxSubmit({
				target: "#register_container", 
				beforeSubmit: salesBeforeSubmit
			});
			
		});

		function salesBeforeSubmit(formData, jqForm, options) {
			
			if (submitting)
			{ 	
				return false; 
			}
			$('.manage-row-options').addClass('hidden');
			submitting = true;
			$("#ajax-loader").show();
			$("#add_payment_button").hide();
			// $("#finish_sale_button").hide();
		}
		

		//Select all text in the input when input is clicked
		$("input[type=text]").not(".description").click(function() {
			$(this).select();
		});
		

		// Finish Sale button
		$("#finish_sale_button").click(function(e)
		{
			if (confirm('Bạn có chắc chắn muốn đóng nhu cầu?')) {
				e.preventDefault();
				
				var confirm_messages = [];
			//Prevent double submission of form
			$("#finish_sale_button").hide();
			$('#grid-loader').show();
			
			
			<?php if ($this->sale_lib->get_payment_amount(lang('common_store_account')) >0) { ?> 
				<?php if ($is_over_credit_limit && $mode!='store_account_payment' && !$this->config->item('disable_store_account_when_over_credit_limit')) { ?>
					confirm_messages.push(<?php echo json_encode(lang('sales_over_credit_limit_warning')); ?>);					
				<?php }elseif($is_over_credit_limit && $mode!='store_account_payment' && $this->config->item('disable_store_account_when_over_credit_limit')) {
					echo "show_feedback('error', ".json_encode(lang('sales_over_credit_limit_error')).", ".json_encode(lang('common_error')).");";
					echo '$("#finish_sale_button").show();';
					echo "$('#grid-loader').hide();";
					echo 'return;';
				} ?>
			<?php } ?>
			
			<?php if (!$this->config->item('disable_confirmation_sale')) { ?>
				confirm_messages.push(<?php echo json_encode(lang("sales_confirm_finish_sale")); ?>);
			<?php } ?>
			
			if (confirm_messages.length)						
			{
					// bootbox.confirm(confirm_messages.join("<br />"), function(result)
					// {
						// if (result)
						// {

							$.ajax({
								type: "POST",
								url: BASE_URL + 'sales/check_before_complete',
								data: {
								},
								success: function(string){
									var res = $.parseJSON(string);
									if(res.flag == 'false') {
										$("#finish_sale_button").show();
										toastr.error(res.msg, 'Lỗi');
									}else{

										finishSale();
									}
									
								}
							});	

						// }
						// else
						// {
						// 	//Bring back submit and unmask if fail to confirm
						// 	$("#finish_sale_button").show();
						// 	$('#grid-loader').hide();
						// }
					// });
				}
				else
				{
					finishSale();
				}
			}
			
		});
		
		$('#finish_sale_alternate_button').on('click',function(e){
			e.preventDefault();
			$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit, complete: function()
			{
				$('#finish_sale_button').trigger('click');
			}});
		});
		
		
		
		// Show or hide item grid
		$("#show_grid, .show-grid").on('click',function(e)
		{
			e.preventDefault();
			$("#category_item_selection_wrapper").slideDown();

			$('.show-grid').addClass('hidden');
			$('.hide-grid').removeClass('hidden');
		});

		$("#hide_grid,#hide_grid_top, .hide-grid").on('click',function(e)
		{
			e.preventDefault();
			$("#category_item_selection_wrapper").slideUp();

			$('.hide-grid').addClass('hidden');
			$('.show-grid').removeClass('hidden');
		});
		
		// Save credit card info
		$('#save_credit_card_info').change(function() 
		{
			$.post('<?php echo site_url("sales/set_save_credit_card_info");?>', {save_credit_card_info:$('#save_credit_card_info').is(':checked') ? '1' : '0'});
		});

		// Use saved cc info
		$('#use_saved_cc_info').change(function() 
		{
			$.post('<?php echo site_url("sales/set_use_saved_cc_info");?>', {use_saved_cc_info:$('#use_saved_cc_info').is(':checked') ? '1' : '0'});
		});

		// Prompt for cc info (EMV integration only)
		$('#prompt_for_card').change(function() 
		{
			$.post('<?php echo site_url("sales/set_prompt_for_card");?>', {prompt_for_card:$('#prompt_for_card').is(':checked') ? '1' : '0'});
		});

		<?php if (isset($cart_count)) { ?>
			$('.cart-number').html(<?php echo $cart_count; ?>);
		<?php } ?>
		
		
	});
	// end of document ready

	$('body').on('click','table#register tbody tr td.cbRegister',function(){

		var checkbox  = $(this).closest('tr').find('input[type="checkbox"]');
		var table = $('table#register');
		if (checkbox.prop('checked') == true){ 
			checkbox.prop('checked', false);
		}else{
			$('.manage-row-options').show();
			checkbox.prop('checked', true);
		}

		var checked_box = table.find('.inputCbResgister:checked');
		if(checked_box.length == 0) 
			$('.manage-row-options').addClass('hidden');
		else
			$('.manage-row-options').removeClass('hidden');
	});
	
	var timerSaleComment;
	$('body').on('keyup', '.sale-comment', function(){
		var id = $(this).attr('id');
		var val = $(this).val();
		if(this.value.length >=0)
		{
			if(timerSaleComment){
				window.clearTimeout(timerSaleComment);
			}
			timerSaleComment = window.setTimeout(function(){
				$.ajax({
					url: 'sales/setCommentTerm',
					type: 'POST',
					data:{	saleCommentId: id ,
						saleCommentVal:val
					},
					success:function (response){
						
					},
				});
			},700);
		}});	
// Re-usable Functions 

function checkPaymentTypeGiftcard()
{
	if ($("#payment_types").val() == <?php echo json_encode(lang('common_giftcard')); ?>)
	{
		$("#amount_tendered").val('');
		$("#amount_tendered").attr('placeholder',<?php echo json_encode(lang('sales_swipe_type_giftcard')); ?>);
		
		<?php if (!$this->agent->is_mobile()) { ?>
			$("#amount_tendered").focus();
		<?php } ?> 
		<?php if (!$this->config->item('disable_giftcard_detection')) { ?>
			giftcard_swipe_field($("#amount_tendered"));
			<?php
		}
		?>
	}
}

function checkPaymentTypePoints()
{
	if ($("#payment_types").val() == <?php echo json_encode(lang('common_points')); ?>)
	{
		$("#amount_tendered").val('');
		$("#amount_tendered").attr('placeholder',<?php echo json_encode(lang('sales_enter_amount_of_points')); ?>);
		
		<?php if (!$this->agent->is_mobile()) { ?>
			$("#amount_tendered").focus();
		<?php } ?> 
	}
}

function itemScannedSuccess(responseText, statusText, xhr, $form)
{
	setTimeout(function(){$('#item').focus();}, 10);
}

function finishSale()
{
	if ($("#comment").val())
	{
		$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function()
		{
			$('#finish_sale_form').submit();						
		});						
	}
	else
	{
		$('#finish_sale_form').submit();						
	}
}


if ($('#amount_tendered').val()>=<?php echo $amount_due; ?>)
{
	$('#finish_sale_alternate_button').removeClass('hidden');
	$('#add_payment_button').addClass('hidden');
}
else
{
	$('#finish_sale_alternate_button').addClass('hidden');
	$('#add_payment_button').removeClass('hidden');
}

if(<?php echo ($payment_total-$sale_total);?> >= 0 )
{
	$('#finish_sale_alternate_button').removeClass('hidden');
	$('#add_payment_button').addClass('hidden');
}

<?php if (!$this->config->item('disable_quick_complete_sale') && ($is_add_payment_click ==1)) { ?>
	if($('#amount_tendered').val() == 0  )
	{
		$('#finish_sale_button').trigger('click');
	}
	else if($('#amount_tendered').val().replace(",", "") > 0 ){
		var _add_payment = true;
		$('.select-payment').each(function(){
			if($(this).data('payment') == <?php echo json_encode(lang('common_debt_customer'))?>)
			{
				_add_payment = false;
				return false;
			}
		});
		if (!($('.select-payment').length>0)) {
			_add_payment = true;
		}

		if(_add_payment)
		{
			$('#finish_sale_button').trigger('click');
		}
		
	}
	
<?php } ?>
$('#select-service').on('change', function(e) {
	e.preventDefault();
	$.post('<?php echo site_url("sales/select_service_temp");?>', {temp_id: $(this).val() }, function(response)
	{
		$("#register_container").html(response);
	});
});

$('#sale_status').on('change', function(e) {
	e.preventDefault();
	$.post('<?php echo site_url("sales/select_sale_status");?>', {status_id: $(this).val() }, function(response)
	{
		$("#register_container").html(response);
	});
});





</script>
<style>
#add_more_customer a{
	border-right: none;
	height: auto;
}
.editable-price {
	display: block;
	margin-bottom: 5px;  
}

.editable-price span {
	width: 100px;  
	display: inline-block;
}

#delete_customer{
	color: white;
	display: flex;
	justify-content: center;
}
.customer-badge{
	font-size: 1.5rem  !important;
}
.customer-action-buttons{
	display: flex;
	align-items: center;
	justify-content: center;
}
</style>



