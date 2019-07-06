<a tabindex="-1" href="#" class="dismissfullscreen <?php echo !$fullscreen ? 'hidden' : ''; ?>"><i class="ion-close-circled"></i></a>
	<?php if($this->receiving_lib->get_change_recv_id()) { ?>
		<div class="alert alert-danger">
			<?php echo lang('receivings_editing_recv'); ?> <strong><?php echo 'RECV '.$this->receiving_lib->get_change_recv_id(); ?></strong>
		</div>
	<?php } ?>
<?php
    $thousands_separator = $this->config->item('thousands_separator');
    $decimal_point       = $this->config->item('decimal_point');
    $number_of_decimals  = $this->config->item('number_of_decimals');
    $mode                = $this->receiving_lib->get_mode();
?>
<div class="row register">
	<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 no-padding-right no-padding-left">
		<div class="register-box register-items-form">
			<div class="item-form">
				<!-- Item adding form -->
				<?php echo form_open("receivings/add_n9",array('id'=>'add_item_form','class'=>'form-inline', 'autocomplete'=> 'off')); ?>
					<div class="input-group input-group-mobile contacts">
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/receiving","<i class='icon ti-pencil-alt'></i> <span class='register-btn-text'>".lang('common_new_item')."</span>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item-mobile')); ?>
						</span>
						<div class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
							<?php echo anchor("#","<i class='icon ti-shopping-cart'></i><span class='register-btn-text'>".$modes[$mode]."</span>", array('class'=>'none active','title'=>$modes[$mode], 'id' => 'register-mode-mobile', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
					        <ul class="dropdown-menu sales-dropdown">
					        <?php foreach ($modes as $key => $value) {
					        	if($key!=$mode){
					        ?>
					        <!-- ẩn đi phần vat -->
					        	<?php if($key === 'vat_order') continue; ?>
					        	<li><a tabindex="-1" href="#" data-mode="<?php echo $key; ?>" class="change-mode"><?php echo $value;?></a></li>

					        <?php } 
							  } ?>
        					</ul>
						</div>

					<span class="input-group-addon grid-buttons 2">
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'> ".lang('common_show_grid')."</span>", array('class'=>'none show-grid','title'=>lang('common_show_grid'))); ?>
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'> ".lang('common_hide_grid')."</span>", array('class'=>'none hide-grid hidden','title'=>lang('common_hide_grid'))); ?>
						</span>	

					</div>

					<div class="input-group contacts  register-input-group">
					<!-- Css Loader  -->
						<div class="spinner" id="ajax-loader" style="display:none">
						  <div class="rect1"></div>
						  <div class="rect2"></div>
						  <div class="rect3"></div>
						</div>
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/receiving","<i class='icon ti-pencil-alt'></i>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item')); ?>
						</span>
						
						<input type="text" id="item" name="item" class="add-item-input pull-left" placeholder="<?php echo lang('common_start_typing_item_name'); ?>">										             				
					</div>
				</form>
			</div>

		</div>

			<!-- Register Items. @contains : Items table -->
		<div class="register-box register-items paper-cut">
			<div class="register-items-holder">
					<!-- hiển thị sản phẩm trong giỏ hàng -->
					<table id="register" class="table table-hover">
						<thead>
					
						<!-- # MẶC ĐỊNH LÀ RECEIVED -->
								<tr class="register-items-header">
									<th></th>
									<th class="item_name_heading">Tên dịch vụ</th>
									<th class="sales_price">Chi phí dự kiến</th>
									<th><?php echo lang('receivings_total'); ?></th>								
								</tr>
						
						</thead>
						<tbody class="register-item-content">
							<?php
							$cart_count = 0;
							if(count($cart)==0)	{ ?>
							<tr class="cart_content_area">
								<td colspan='7'>
									<div class='text-center text-warning' > <h3> <span class="flatBluec"> [<?php echo lang('module_receivings') ?>]</span></h3></div>
								</td>
							</tr>
							<?php }	else { 
							foreach($cart as $line=>$item) { $cart_count = $cart_count + $item['quantity']; ?>		

							<?php  if($item['name'] != lang('common_discount')){		?>
							<tr class="register-item-details">
								<td class="text-center">
								 	<?php if(empty($isStockIn)) {
									echo anchor("receivings/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item'));
						 			}?> 
								</td>
								<td> 
									<a tabindex = "-1" style="text-align: center" href="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-toggle="modal" data-target="#myModal" class="register-item-name" ><?php echo H($item['name']); ?><?php echo $item['size'] ? ' ('.H($item['size']).')': ''; ?></a>
								</td>
								
								<td class="text-center">
									<?php if ($items_module_allowed) {  $cost = isset($item['cost_price_interval'])? $item['cost_price_interval']:0;  ?>

											
									<?php echo $cost; }?>
								</td>
																																				
								<!-- <td class="text-center"><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100, 10); ?></td> -->
								<td class="text-center">
									<a href="#" class="xeditable" data-validate-number="true" data-type="text" data-value="<?php echo to_currency_no_money($item['price']); ?>" data-pk="1" data-name="price" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php  echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100, 10); ?></a>
								</td>
							</tr>
							<tr class="register-item-bottom">
								<td>&nbsp;</td>
								
							</tr>
							<?php
							 } ?> <!-- end else -->
						<?php } }  ?>
						</tbody>
					</table>
			</div>
		</div>
		<!-- /.Register Items -->
	</div>

	<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
		<div class="register-box register-right">
	<!-- Receive  Top Buttons  -->
			<div class="sale-buttons">
				<!-- Extra links -->
			
				<?php if(count($cart) > 0){ ?>
				<?php echo form_open("receivings/cancel_receiving",array('id'=>'cancel_sale_form', 'autocomplete'=> 'off')); ?>
				
				
				<a href="" class="btn btn-cancel"  id="cancel_recv_button" >
					<i class="ion-close-circled"></i>
					<?php echo "Hủy";?>
				</a>
			</form>
			<?php } ?>

		</div>

		<!-- Been thu 3 -->
		<?php if(isset($supplier)) {  ?>
			<!-- Customer Badge when customer is added -->

			<div class="form-group panel-heading">
				<!-- <h3>Tên bên thứ 3</h3>				 -->
				<label class="" for=""><?php echo $supplier ?></label>
				<a id="delete_supplier" class="form-control btn btn-primary" href="<?php echo base_url('receivings/delete_supplier') ?>">Xóa</a>
			</div>

		<?php }
		else {  ?>
			<div class="customer-form">
				<?php echo form_open("receivings/select_supplier",array('id'=>'select_supplier_form', 'autocomplete'=> 'off')); ?>
				<div class="input-group contacts">
					<span class="input-group-addon">
						<?php echo anchor("suppliers/view/-1/1","<i class='ion-plus'></i>", array('class'=>'none','title'=>lang('receivings_new_supplier'), 'id' => 'new-customer')); ?>
					</span>
					<input type="text" id="supplier" name="supplier" class="add-customer-input" autocomplete="off" placeholder="Nhập tên bên thứ ba" /> 

				</div>
			</form>

		</div> 				

<?php }  ?>
			<!-- ADD TASK -->
			<?php if(isset($task)){ ?>

				<div class="form-group panel-heading">
					<!-- <h3> Tên dự án </h3>		 -->
					<label id="task"  name="task" class="add-task-input ui-autocomplete-input"> <?php echo $task ?></label>
					<a class="form-control btn btn-primary" id="delete_task" href="<?php echo base_url('receivings/delete_task') ?>">Xóa</a>

				</div>

			<?php } else {?>

					<div class="customer-form">
				<!-- if the supplier is not set , show supplier adding form -->
				<?php echo form_open("task/search_projects",array('id'=>'select_task_form', 'autocomplete'=> 'off')); ?>
				<div class="input-group contacts">
					<span class="input-group-addon">
						<?php echo anchor("tasks","<i class='ion-plus'></i>", array('class'=>'none','title'=>'Xem dự án', 'id' => 'new-customer')); ?>
					</span>
					<input type="text" id="customer" name="task" class="add-task-input" placeholder="Chọn dự án" /> 

				</div>
			</form>

		</div> 
			<?php } ?>

			<!-- END ADD TASK -->

		</div>

		<?php if($mode != 'transfer') { ?>
		
		<?php } ?>
		<?php $store_account_payment_value = $this->receiving_lib->get_store_account_payment_value(); ?>
				<!-- Summary -->
				<div class="register-box register-summary paper-cut">
	<!-- 	<li class="sub-total list-group-item">
	    <span class="key"><?php echo lang('common_sub_total'); ?>:</span>
	    <span class="value"><?php echo to_currency($subtotal); ?></span>
        </li>	 -->	      
			<?php foreach($taxes as $name=>$value) { ?>
				<li class="list-group-item">
					<span class="key">
						<?php if ($this->Employee->has_module_action_permission('receivings', 'delete_taxes', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
							<?php echo anchor("receivings/delete_tax/".rawurlencode($name),'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-tax remove'));?>

						<?php } ?>
						<?php echo $name; ?>:</td>
					</span>
					<span class="value pull-right">
						<?php echo to_currency($value); ?>
					</span>
				</li>
			<?php }; ?>
		</ul>
		<!-- nếu không phải là modul chuyển kho -->
		<?php if($mode != 'transfer') { ?>
		<div class="amount-block">
			<!-- tổng mặt hàng và tổng giá trị -->
			<div class="total amount-due hide">
				<div class="side-heading">
					<?php echo "Số lượng" ?>
				</div>
				<div class="amount" >
					<?php echo $items_in_cart; ?>
				</div>
			</div>
			<div class="total amount">
				<div class="side-heading">
					<?php echo "Tổng tiền"; ?>
				</div>
				<div class="amount total-amount" >
					<?php echo to_currency($total); ?>
				</div>
			</div>
		</div>
		<!-- ./amount block -->
		<div id="list_payments">
			<?php if(count($cart) > 0) { 
			 $this->load->view('receivings/partials/payments', $payments);} ?>		
		</div>
		
		<?php } ?>
			 <!-- Only show this part if there are Items already in the Table. -->	
			<?php if(count($cart) > 0) { ?>
		
			<div class="change-date">
					
						<div id="change_receiving_date_picker" class="input-group date datepicker" >
							<span class="input-group-addon"><i class="ion-calendar"></i></span>
							<?php echo form_input(array(
								'name'=>'change_receiving_date',
								'id' => 'change_receiving_date',
								'size'=>'8',
								'class' => 'form-control',
								'value'=> date(get_date_format().' '.get_time_format(), $change_receiving_date ? strtotime($change_receiving_date) : time()),
								)
							);?>       
						</div>
			
				<div class="receivings-finish-sale">
					<div class="input-group add-payment-form">
						<!-- nếu không phải chuyển kho -->
						<?php if($mode != "transfer") { ?>
							<?php echo form_dropdown('payment_type',$payment_options, $selected_payment,'class="input-medium hidden" id="payment_type"');?>
							<?php echo form_input(array('name'=>'amount_tendered','id'=>'amount_tendered','value'=> $amount_tendered,'class'=>'add-input auto', 'accesskey' => 'p','type'=>'hidden'));	?>
							
						<?php } ?>
					
					</div>
					<div>Người khởi tạo : <strong><?php echo $this->Employee->get_logged_in_employee_info()->first_name; ?></strong></div>

					<?php echo form_open("receivings/".(!$is_po ? 'complete' : 'suspend'),array('id'=>'finish_sale_form', 'autocomplete'=> 'off')); ?>
					<div class="comment-block">
						<div class="side-heading">
						<label id="comment_label" for="comment"><?php echo lang('common_comments'); ?> : </label>
						</div>
						<?php echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'4', 'class'=>'form-control')); ?>
					</div>

					<div id="finish-recv" class="finish-recv" >

						<input type="submit" class="btn btn-primary btn-block" id="finish_sale_button" value="<?php echo !$is_po ? "Lưu đóng" : "lang('receivings_suspend_and_complete_po')"; ?>">
					</div>
					


				</div>
			</div> <!-- end change-date -->
		</div>
		</form>
		<?php } ?>
		</div>
		<!-- /.Summary -->
		</div>
	</div>
</div>

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
		echo "show_feedback('success', ".json_encode($success).", ".json_encode(lang('common_success')).");";
	}
	?>
</script>


<script type="text/javascript" language="javascript">
var submitting = false;
// $(document).ready(function()
// {

	$('#toggle_email_receipt').on('click',function(e) {
		e.preventDefault();
        var checkBoxes = $("#email_receipt");
        checkBoxes.prop("checked", !checkBoxes.prop("checked")).trigger("change");
        $(this).toggleClass('email-checked');

	})

	$('#email_receipt').change(function(e) 
	{	
		e.preventDefault();
		$.post('<?php echo site_url("receivings/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
	});
	
	
	$('#change_receiving_date_enable').is(':checked') ? $("#change_receiving_date_picker").show() : $("#change_receiving_date_picker").hide(); 

	$('#change_receiving_date_enable').click(function() {
		if( $(this).is(':checked')) {
			$("#change_receiving_date_picker").show();
		} else {
			$("#change_receiving_date_picker").hide();
		}
	});
	
	date_time_picker_field($("#change_receiving_date"), JS_DATE_FORMAT + " "+ JS_TIME_FORMAT);
	
   $("#change_receiving_date").on("dp.change", function(e) {
		$.post('<?php echo site_url("receivings/set_change_receiving_date");?>', {change_receiving_date: $('#change_receiving_date').val()});			
   });
	
	//Input change
	$("#change_receiving_date").change(function(){
		$.post('<?php echo site_url("receivings/set_change_receiving_date");?>', {change_receiving_date: $('#change_receiving_date').val()});			
	});

	$('#change_receiving_date_enable').change(function() 
	{
		$.post('<?php echo site_url("receivings/set_change_receiving_date_enable");?>', {change_receiving_date_enable: $('#change_receiving_date_enable').is(':checked') ? '1' : '0'});
	});

	//Here just in case the loader doesn't go away for some reason
	$("#ajax-loader").hide();
	
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
			setTimeout(function(){$('#item').focus();}, 10);	
		<?php
		}
		?>
	
		$(document).focusin(function(event) 
		{
			last_focused_id = $(event.target).attr('id');
		});
	<?php }
	else
	{
		if ($this->config->item('wireless_scanner_support_focus_on_item_field'))
		{
		?>
			setTimeout(function(){$('#item').focus();}, 10);				
		<?php
		}
	} ?>
	
		$('#add_item_form').ajaxForm({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, success: itemScannedSuccess});
		$('#select_supplier_form,#select_location_form,#select_task_form',).ajaxForm({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});

			$( "#item" ).autocomplete({
		 		source: '<?php echo site_url("receivings/item_search_n9");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
					$( "#item" ).val(ui.item.item_id);
		 			
		 			$('#add_item_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, success: itemScannedSuccess});
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
		// if #mode is changed
			  //TODO: Remove this code as we don't have store account payments in recv
		$('.change-mode').click(function(e){
			e.preventDefault();
			if ($(this).data('mode') == "store_account_payment") { // Hiding the category grid
				$('#show_hide_grid_wrapper, #category_item_selection_wrapper').fadeOut();
			}else { // otherwise, show the categories grid
				$('#show_hide_grid_wrapper, #show_grid').fadeIn();
				$('#hide_grid').fadeOut();
			}
			$.post('<?php echo site_url("receivings/change_mode");?>', {mode: $(this).data('mode')}, function(response)
			{
				$("#register_container").html(response);
			});
		});


    //make username editable
    $('.xeditable').editable({
    	validate: function(value) {
            value = value.replace(/\<?php echo $thousands_separator; ?>/g, "");
            value = value.replace(/\<?php echo $decimal_point; ?>/g, ".");

            if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
                return <?php echo json_encode('Chỉ số được cho phép'); ?>;
            }else if ($(this).data('number-type') == 'unsigned' && value < 0) {
            	return <?php echo json_encode('Không được phép nhập số âm'); ?>;
            }
        },
    	success: function(response, newValue) {
    		// console.log(response);
			 last_focused_id = $(this).attr('id');
			 $("#register_container").html(response);
		}
    });

    $('.measure_item .xeditable').editable({
    	success: function(response, newValue) {
			 last_focused_id = $(this).attr('id');
			 $("#register_container").html(response);
		}
    });
	 
	 $(".expire_date").editable({
     	validate: function(value) {
          if (!value) 
			 {
             return <?php echo json_encode(lang('receivings_invalid_date')); ?>;
          }
       },
		 combodate: {
			 maxYear: <?php echo date("Y", strtotime('+3 years'));?>,
			 minYear: <?php echo date("Y");?>,
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
	
	 $('.xeditable').on('hidden', function(e, editable) {
		 last_focused_id = $(this).attr('id');
	 	$('#'+last_focused_id).focus();
	 	$('#'+last_focused_id).select();
 	});
	
	<?php if (isset($cart_count)) { ?>
      	$('.cart-number').html(<?php echo $cart_count; ?>);
	<?php } ?>

	// Location form 
		$('#locatio').selectize({
			valueField: 'value',
			labelField: 'label',
			searchField: 'label',
			options: [],
			create: false,
			render: {
				option: function(item, escape) {
					return '<div class="customer-badge suggestions">' +
								'<div class="avatar">' +
									'<span class="badge" style="background-color:' + escape(item.color) + '">&nbsp;</span>' +
								'</div>' +
								'<div class="details">' +
									'<a href="#" class="name">' +
										escape(item.label) +
									'</a>' +
								'</div>' +
							'</div>';
				}
			},

			load: function(query, callback) {
				if (!query.length) return callback();
				$.ajax({
					url:'<?php echo site_url("receivings/location_search");?>'+'?term='+encodeURIComponent(query),
					type: 'GET',
					error: function() {
						callback();
					},
					success: function(res) {
						res = $.parseJSON(res);
						callback(res);
					}
				});
			}
		});

		$('#location').change(function(){
			$('#select_location_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
		});

		// Select Location 
		<?php if($mode=="transfer" and !isset($location)) { ?>
		

			$( "#location" ).autocomplete({
		 		source: '<?php echo site_url("receivings/location_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("receivings/select_location");?>', {location: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});	
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='customer-badge suggestions'></li>")
		             .data("item.autocomplete", item)
			         .append('<a class="suggest-item location-suggest"><div class="avatar">' +
									'<span class="badge" style="background-color:' + item.color + '">&nbsp;</span>' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' + 
								'</div></a>')
		             .appendTo(ul);

		     };
	     <?php } ?>


		// Select Supplier 
		<?php if($mode!="transfer" and !isset($supplier)) { ?>

			$( "#supplier" ).autocomplete({
		 		source: '<?php echo site_url("receivings/supplier_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("receivings/select_supplier");?>', {supplier: ui.item.value }, function(response)
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

    // $('#amount_tendered').autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>'});
    $('body').on('click','#amount_tendered',function(){
        $(this).autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>'});
    });

    $('body').on('click','.editable-input input[type="text"]',function(){
        var popover  = $(this).closest('.popover ');
        var prev     = popover.prev();

        var data_name = prev.attr('data-name');
        if(data_name == 'price' || data_name == 'discount_all_flat') {
            $(this).autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>'});
        }
    });

    $('body').on('focus','.editable-input input[type="text"]',function(){
        var popover  = $(this).closest('.popover ');
        var prev     = popover.prev();

        var data_name = prev.attr('data-name');
        if(data_name == 'price' || data_name == 'discount_all_flat') {
            $(this).autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>'});
        }
    });


		//Add payment to the sale 
		$("#add_payment_button").click(function(e)
		{
			e.preventDefault();

			$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit});
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
	

	$("#cart_contents input").change(function()
	{
		$(this.form).ajaxSubmit({target: "#register_container",beforeSubmit: receivingsBeforeSubmit});
	});
	
	$('#item,#supplier,#location,#customer').click(function()
    {
    	$(this).attr('value','');
    });

	$('#mode').change(function()
	{
		$('#mode_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
	});
	
	$('#comment').change(function() 
	{
		$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()});
	});



	<?php if (!$is_po) { ?>
	    $("#finish_sale_form").submit(function()
		{
			<?php if($mode=="transfer" and !isset($location)) { ?>
				bootbox.alert(<?php echo json_encode(lang("receivings_location_required")); ?>);
				$('#location').focus();
				return;
				<?php } ?>
			
				var finishForm = this;
				
				bootbox.confirm(<?php echo json_encode(lang("receivings_confirm_finish_receiving")); ?>, function(result)
	    		{
					if (result)
					{
						//Prevent double submission of form
						$("#finish_sale_button").hide();
						finishForm.submit();
					}
				});
				return false;
		});
	    $("#finish_sale_button").click(function(e)
	    {
	    	e.preventDefault();
				var add_payment_recv_button = false;
				$('.select-payment').each(function(){
					if($(this).data('payment') ==  <?php echo json_encode(lang('common_refund_from_supplier'));?>)
					{
						if($(this).hasClass('active'))
						{
							add_payment_recv_button = true;
						}
					}
				});
				if(add_payment_recv_button)
				{
					 $("#add_payment_recv_button").trigger('click');
				}
       
			$.ajax({
				type: "POST",
				url: BASE_URL + 'receivings/check_before_complete',
				data: {
					'a' : 1
				},
				success: function(string){
					var res = $.parseJSON(string);
					if(res.flag == 'false')
						toastr.error(res.msg, 'Lỗi');
					else{
						$('#finish_sale_form').submit();
						
					}

				}
			});
		});
	<?php } ?>
	
	$('#add_payment_recv_button').click(function(e){
		e.preventDefault();
		var _data = {};
		_data['payment_type'] = $('.add-payment').find('a.active').data('payment');
		if($('.add-payment').find('a.active').data('payment') == <?php echo json_encode(lang('common_refund_from_supplier'))?>)
		{
			_data['amount'] = '-'+$('#amount_tendered').val();
		}
		else
		{
			_data['amount'] = $('#amount_tendered').val();
		}
		

		coreAjax.call(
			'<?php echo site_url("receivings/add_payment");?>',
			_data,
			function(response)
			{
				if(response.success == false) {
					toastr.error(response.msg, 'Lỗi');
				}else {
					var amount_tendered = parseFloat(response.amount_tendered);
					if(response.amount_tendered){
						$('#amount_tendered').val(response.amount_tendered);
					}
					else
						$('#amount_tendered').val('0');

					$('#list_payments').html(response.html_payments);
					$('#list-payments-option').html(response.html_payments_option);
					if(amount_tendered == 0 ) {
						$('#finish-recv').show();
					}
				}

			}
		);
	});
	
    $("#cancel_recv_button").click(function(e)
    {
     	e.preventDefault();		 
    	bootbox.confirm(<?php echo json_encode(lang("receivings_confirm_cancel_receiving")); ?>, function(result)
    	{
			if (result)
			{
				$('#cancel_sale_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
			}
		});
    });

	$('.delete-item, #delete_supplier, #delete_task, #delete_location,.delete-tax').click(function(event)
	{
		event.preventDefault();
		$("#register_container").load($(this).attr('href'));	
	});

	$("input[type=text]").click(function() {
		$(this).select();
	});
		
	$("#suspend_recv_button<?php echo $is_po ? ', #finish_sale_button': '';?>").click(function(e)
	{
		e.preventDefault();
		bootbox.confirm(<?php echo json_encode(lang("receivings_confim_suspend_recv")); ?>, function(result)
		{
			if (result)
			{
				if ($("#comment").val())
				{
					$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()}, function()
					{
						doSuspendRecv();
					});						
				}
				else
				{
					doSuspendRecv();	
				}
			}
		});
	});

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
// });

function doSuspendRecv()
{
	<?php if (!$is_po) { ?>
		<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
			window.location = '<?php echo site_url("receivings/suspend"); ?>';
		<?php }else { ?>
			$("#register_container").load('<?php echo site_url("receivings/suspend"); ?>');
		<?php } ?>
		<?php 
		}
		else
		{
		?>
		window.location = '<?php echo site_url("receivings/suspend"); ?>';			
		<?php	
		} 
		?>

}
function receivingsBeforeSubmit(formData, jqForm, options)
{
	if (submitting)
	{
		return false;
	}
	$('.cart-number').html(<?php echo $cart_count; ?>);
	submitting = true;
	
	$("#ajax-loader").show();
	$("#finish_sale_button").hide();
}

function itemScannedSuccess(responseText, statusText, xhr, $form)
{
	setTimeout(function(){$('#item').focus();}, 10);
}
</script>

<!-- SEARCH PROJECT -->

<script>

		$("#customer").autocomplete({
		 		source: '<?php echo site_url("tasks/search_projects");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("receivings/select_task");?>', {task: ui.item.task_id }, function(response)
					{
						
						$("#register_container").html(response);
					});	
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {

		         return $("<li class='customer-badge suggestions'></li>")
		             .data("item.autocomplete", item)
			           .append('<a class="suggest-item"><div class="avatar">' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' + 
									'<span class="email">' +
										item.task_id + 
									'</span>' +
								'</div></a>')
		             .appendTo(ul);


};

</script>



