<?php $this->load->view("partial/header"); ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/location.js" ></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-n9-autocomplete.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-n9-autocomplete.js" ></script>
<?php if ( 0 && isset($needs_auth) && $needs_auth) {?>
	<?php echo form_open('locations/check_auth',array('id'=>'location_form_auth','class'=>'form-horizontal')); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					<h3 style="margin-left: 80px;"><a href="http://4biz.vn/buy_additional.php" target="_blank"><?php echo lang('locations_purchase_additional_licenses'); ?> &raquo;</a></h3>
					<?php if (validation_errors()) {?>
				        <div class="alert alert-danger">
				            <strong><?php echo lang('common_error'); ?></strong>
				            <?php echo validation_errors(); ?>
				        </div>
			        <?php } ?>
					<div class="form-group">
						<?php echo form_label(lang('locations_purchase_email').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'purchase_email',
								'id'=>'purchase_email')
							);?>
						</div>	
					</div>
					<div class="form-actions pull-right">
						<?php
						echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>lang('common_submit'),
							'class'=>'submit_button btn btn-primary')
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php form_close(); ?>
<?php } else {?>

	<?php echo form_open_multipart('locations/save/'.$location_info->location_id,array('id'=>'location_form_n9','class'=>'form-horizontal','autocomplete'=> 'off')); ?>
		<input type="hidden" name="location_id" value="<?php echo $location_id ; ?>" />
        <div class="row" id="form">
			<div class="spinner" id="grid-loader" style="display:none">
			  <div class="rect1"></div>
			  <div class="rect2"></div>
			  <div class="rect3"></div>
			</div>
			<div class="col-md-12">				
				<div class="panel panel-piluku">
					<div class="panel-heading">
		                <h3 class="panel-title">
		                    <i class="ion-edit"></i> 
		                    <?php echo lang("locations_basic_information"); ?>
	    					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
		                </h3>
			        </div>

					<div class="panel-body">

						<div class="form-group">
							<?php echo form_label(lang('locations_name').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="name" class="text-danger errors"></p>
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'name',
									'id'=>'name',
									'value'=>$location_info->name)
								);?>
							</div>
						</div>
						<div class="form-group">
							<?php echo form_label('Code :', 'code',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="code" class="text-danger errors"></p>
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'code',
									'id'=>'code',
									'value'=>$location_info->code)
								);?>
							</div>
						</div>
						<div class="form-group locations_type">
							<?php echo form_label(lang('location_parent').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<select class="form-control" name="location_parent">
									<option value="0">----- Chọn địa điểm cha</option>
									<?php 
										foreach($locations as $location)
										{
										    if ($location['location_id'] == $location_info->location_id) continue;
											$selected = ($location_info->parent_id == $location['location_id']) ? 'selected' : '';
											echo '<option value="'.$location['location_id'].'" '.$selected.'> '.$location['name'].'</option>';
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="form-group hidden locations_type">
							<?php echo form_label(lang('locations_type').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php foreach ($types as $type) {
								$checked = ($type['code'] == $location_info->type) ? 'checked="checked"' : ''; 
								?>
                                <div>
                                	<input type="radio" name="location_type" <?php echo $checked;?> value="<?php echo $type['code']; ?>" id="locations_type_<?php echo $type['code']; ?>">
									<label for="locations_type_<?php echo $type['code']; ?>"><span></span></label>
									<label for="locations_type_<?php echo $type['code']; ?>" style="font-weight: inherit"> <?php echo $type['label']; ?></label>
                                </div>
                                <?php } ?>
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_color').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'color',
									'id'=>'color',
									'value'=>$location_info->color)
								);?>
							</div>
						</div>


						<div class="form-group">
							<?php echo form_label(lang('locations_address').' :', 'address',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="address" class="text-danger errors"></p>
								<?php echo form_textarea(array(
									'name'=>'address',
									'id'=>'address',
									'class'=>'form-control text-area',
									'rows'=>'4',
									'cols'=>'30',
									'value'=>$location_info->address));?>								
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_phone').' :', 'phone',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="phone" class="text-danger errors"></p>
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'phone',
									'id'=>'phone',
									'value'=>$location_info->phone)
								);?>
							</div>
						</div>
					
						<div class="form-group">
							<?php echo form_label(lang('locations_fax').' :', 'fax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'fax',
									'id'=>'fax',
									'value'=>$location_info->fax)
								);?>
							</div>
						</div>
						
						<div class="form-group">
							<?php echo form_label(lang('locations_account_bank').' :', 'account_bank',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'account_bank',
									'id'=>'account_bank',
									'value'=>$location_info->account_bank)
								);?>
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_email').' :', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'type'=>'text',
									'class'=>'form-control form-inps',
									'name'=>'email',
									'id'=>'email',
									'value'=>$location_info->email)
								);?>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('common_return_policy').' :', 'return_policy',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'return_policy',
								'id'=>'return_policy',
								'class'=>'form-control text-area',
								'rows'=>'4',
								'cols'=>'30',
								'value'=>$location_info->return_policy));?>
							</div>
						</div>
						
						
						<div class="form-group">
							<?php echo form_label(lang('reports_employees').' :', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="employees" class="text-danger errors"></p>
								<!-- <input type="text" id="emp" class="emp form-control" name="emp[]" /> -->
								<select class="form-control" name="employees[]" id="employees" multiple>
									<?php  
										foreach($employees as $person_id => $employee)
										{
											$selected = ($employee['has_access'] == true) ? 'selected' : '';
											echo '<option value="'.$person_id.'" '.$selected.'> '.$employee['name'].'</option>';
										}
									?>
								</select>		
							</div>
						</div>


                   	

						<div class="form-group">	
							<?php echo form_label(lang('locations_timezone').' :', 'timezone',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('timezone', $all_timezones, $location_info->timezone, 'class="form-control" id="timezone"');
								?>
							</div>
						</div>

					
						<?php echo form_hidden('redirect', $redirect); ?>

						<div class="form-actions pull-right">
							<?php
							if ($purchase_email)
							{
								echo form_hidden('purchase_email', $purchase_email);
							}
							
//							echo form_submit(array(
//								'name'=>'submitf',
//								'id'=>'submitf',
//								'value'=>lang('common_submit'),
//								'class'=>'submit_button btn btn-primary')
//							);
							?>
                            <input type="button" name="submitf" value="Thực hiện" id="submitf" class="submit_button btn btn-primary" onclick="save_location();"/>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php echo form_close(); ?>
<?php }?>

<div class="modal fade box-modal" id="quick_modal">
</div>

<script type='text/javascript'>
	var submitting = false;
		//validation and submit handling
		$(document).ready(function()
		{	

// 			$('.locations_type input[type="checkbox"]').change(function(){
// 				$('.locations_type input[type="checkbox"]').not( "#" + $(this).attr('id') ).attr('checked', false);
// 				$('.locations_type input[name="location_type"]').val($(this).val());
// 			});
			
			$("#locations_init_mercury_emv").click(function()
			{
				$("#ajax-loader").show();
				$("#locations_init_mercury_emv").hide();							
				
				var emv_merchant_id = $("#emv_merchant_id").val();
				var com_port = $("#com_port").val();
				var listener_port = $("#listener_port").val();
				
				$.post('<?php echo site_url("locations/save_emv_data/".$location_info->location_id);?>', 
				{emv_merchant_id: emv_merchant_id, com_port: com_port, listener_port:listener_port }, function(response) {
					
					if(response.success)
					{
				   	 var data = {};
				   	 <?php
				   	 foreach($mercury_emv_param_download_init_params['post_data'] as $name=>$value)
				   	 {
				   		 if ($name && $value)
				   		 {
				   		 ?>
				  	 		 data['<?php echo $name; ?>'] = '<?php echo $value; ?>';
				   	 	 <?php 
				   		 }
				   	 }
				   	 ?>

 				   	data['ComPort'] = com_port;
				   	data['MerchantID'] = emv_merchant_id;
						
						mercury_emv_param_download(<?php echo json_encode($mercury_emv_param_download_init_params['post_host']); ?>, listener_port, data, <?php echo json_encode(lang('locations_init_device_success')); ?>, <?php echo json_encode(lang('locations_unable_to_init_device'));?>, function()
						{
							$("#ajax-loader").hide();
							$("#locations_init_mercury_emv").show();							
						});
					}
					else
					{
						$("#ajax-loader").hide();
						$("#locations_init_mercury_emv").show();
					}
				}, 'json');
			});
			$('#employees').selectize();
			
         $('#color').colorpicker();
			
			$(".delete_register").click(function()
			{
				$("#location_form_n9").append('<input type="hidden" name="registers_to_delete[]" value="'+$(this).data('register-id')+'" />');
				$(this).parent().parent().remove();
			});
	
			$("#add_register").click(function()
			{
				$("#price_registers tbody").append('<tr><td><input type="text" class="registers_to_add form-control" name="registers_to_add[]" value="" /></td><td>&nbsp;</td></tr>');
			});
						
			if ($("#location_form_auth").length == 1)
			{
			    setTimeout(function(){$(":input:visible:first","#location_form_auth").focus();},100);
			}
			else
			{
			    setTimeout(function(){$(":input:visible:first","#location_form").focus();},100);				
			}
			var submitting = false;
			$('#location_form').validate({
				submitHandler:function(form)
				{
					if (submitting) return;
					submitting = true;
$('#grid-loader').show();
					$(form).ajaxSubmit({
					success:function(response)
					{
						//Don't let the registers be double submitted, so we change the name
						$(".registers_to_add").attr('name', 'registers_added[]');
						
$('#grid-loader').hide();
						submitting = false;						
						show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> +' #' + response.location_id : <?php echo json_encode(lang('common_error')); ?>);
						
						
						if(response.redirect==2 && response.success)
						{
							window.location.href = '<?php echo site_url('locations'); ?>';
						}
						else
						{
							$("html, body").animate({ scrollTop: 0 }, "slow");
						}
										
					},
					<?php if(!$location_info->location_id) { ?>
					resetForm: true,
					<?php } ?>
					dataType:'json'
				});

				},
				ignore: '',
				errorClass: "text-danger",
				errorElement: "p",
				errorPlacement: function(error, element) {
				    error.insertBefore(element);
				},
					highlight:function(element, errorClass, validClass) {
						$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
					},
					unhighlight: function(element, errorClass, validClass) {
						$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
					},
				rules:
				{
					name:
					{
						required:true,
					},
					phone:
					{
						required:true
					},
					address:
					{
						required:true
					},
					timezone:
					{
						required: true
					},
					"employees[]": "required"
					
		   		},
				messages:
				{
					name:
					{
						required:<?php echo json_encode(lang('locatoins_name_required')); ?>,

					},
					phone:
					{
						required:<?php echo json_encode(lang('locations_phone_required')); ?>,
						number:<?php echo json_encode(lang('locations_phone_valid')); ?>
					},
					address:
					{
						required:<?php echo json_encode(lang('locations_address_required')); ?>
					},
					timezone:
					{
						required:<?php echo json_encode(lang('locations_timezone_required_field')); ?>
					},
					"employees[]": <?php echo json_encode(lang('locations_one_employee_required')); ?>
					
				}
			});
			
			$("#enable_credit_card_processing").change(check_enable_credit_card_processing).ready(check_enable_credit_card_processing);

			$("#credit_card_processor").change(check_credit_card_processor).ready(check_credit_card_processor);
			
			function check_enable_credit_card_processing()
			{
				if($("#enable_credit_card_processing").prop('checked'))
				{
					$("#merchant_information").show();
				}
				else
				{
					$("#merchant_information").hide();
				}

			}
			
			function check_credit_card_processor()
			{
				var cc_processor = $("#credit_card_processor").val();
				if (cc_processor == 'mercury')
				{
					$("#emv_info").show();
					$("#mercury_hosted_checkout_info").show();
					$("#stripe_info").hide();
					$("#braintree_info").hide();
					
				}
				else if (cc_processor == 'stripe')
				{
					$("#emv_info").hide();
					$("#mercury_hosted_checkout_info").hide();
					$("#stripe_info").show();
					$("#braintree_info").hide();
				}
				else if (cc_processor == 'braintree')
				{
					$("#emv_info").hide();
					$("#mercury_hosted_checkout_info").hide();
					$("#stripe_info").hide();
					$("#braintree_info").show();
				}
			}
			
			$("#receive_stock_alert").change(check_enable_stock_alert).ready(check_enable_stock_alert);
			
			function check_enable_stock_alert()
			{
				if($("#receive_stock_alert").prop('checked'))
				{
					$("#stock_alert_email_container").show();
				}
				else
				{
					$("#stock_alert_email_container").hide();
				}

			}
			
		});

</script>

<?php $this->load->view('partial/footer'); ?>