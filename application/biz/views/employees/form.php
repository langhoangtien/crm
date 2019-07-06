<?php $this->load->view("partial/header");?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/employee.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/d13_validate.js" ></script>

	<div class="row" id="form">
		<div class="spinner" id="grid-loader" style="display:none">
		  <div class="rect1"></div>
		  <div class="rect2"></div>
		  <div class="rect3"></div>
		</div>
		<div class="col-md-12">
			<?php 	$current_employee_editing_self = $this->Employee->get_logged_in_employee_info()->person_id == $person_info->person_id;
					echo form_open('employees/save/'.(!isset($is_clone) ? $person_info->person_id: ''),array('id'=>'employee_form','class'=>'form-horizontal'));
			?>
<!-- bat dau thong tin -->
<div class="col-md-6">
			<div class="panel panel-piluku">
				<div class="panel-heading">
	                <h3 class="panel-title">
	                    <i class="ion-edit"></i> 
	                    <?php echo lang("employees_basic_information"); ?>
    					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
	                </h3>
		        </div>

				<div class="panel-body">
					<?php $this->load->view("people/form-employee"); ?>
										
					<div class="form-group" style="display: none;">
						<?php echo form_label(lang('common_commission_default_rate').' ('.lang('common_commission_help').'):', 'commission_percent',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'name'=>'commission_percent',
							'id'=>'commission_percent',
							'class'=>'form-control',
							'value'=>to_quantity($person_info->commission_percent,FALSE)));?>%
						</div>
					</div>
					
					<div class="form-group" style="display: none;">
						<?php echo form_label(lang('common_commission_percent_calculation').': ', 'commission_percent_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('commission_percent_type', array(
							'selling_price'  => lang('common_unit_price'),
							'profit'    => lang('common_profit'),
							),
							$person_info->commission_percent_type,
							array('class' => 'form-control',
									'id' => 'commission_percent_type'))
							?>
						</div>
					</div>
					
				
					<?php if ($this->config->item('timeclock')) {?>
						<div class="form-group">	
							<?php echo form_label(lang('common_hourly_pay_rate'), 'hourly_pay_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<div class="input-group">
							      <div class="input-group-addon"><?php echo $this->config->item('currency_symbol'); ?></div>
							      <?php echo form_input(array(
									'name'=>'hourly_pay_rate',
									'id'=>'hourly_pay_rate',
									'class'=>'form-control',
									'value'=>$person_info->hourly_pay_rate? to_currency_no_money($person_info->hourly_pay_rate, 2) : ''));?>
							    </div>

								
							</div>
						</div>
					<?php 
					}
					else
					{
						echo form_hidden('hourly_pay_rate', 0);
					}
					?>
														
					
                    <div class="form-heading">
						<?php echo lang("common_login_info"); ?>
					</div>
					<div class="form-group">	
					<?php echo form_label(lang('common_username'), 'username',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'username',
							'id'=>'username',
							'class'=>'form-control',
							'value'=>$person_info->username));?>
						</div>
					</div>

					<div class="form-group">	
					<?php echo form_label(lang('common_password'), 'password',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_password(array(
							'name'=>'password',
							'id'=>'password',
							'class'=>'form-control',
							'autocomplete'=>'off',
						));?>
						</div>
					</div>

					<div class="form-group">	
					<?php echo form_label(lang('common_repeat_password'), 'repeat_password',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_password(array(
							'name'=>'repeat_password',
							'id'=>'repeat_password',
							'class'=>'form-control',
							'autocomplete'=>'off',
						));?>
						</div>
					</div>
					
					
					<?php if(empty($person_info->force_password_change)){ ?>
					<div class="form-group">	
					<?php echo form_label(lang('employees_force_password_change_upon_login'), 'force_password_change',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
							echo	form_checkbox(array(
								'name' => 'force_password_change',
								'id' => 'force_password_change',
								'value' => 1,
								'checked' => 1,
								'disabled'=>1
								));
								echo '<label for="force_password_change"><span></span></label>';;
							?>
						</div>
					</div>
					<?php } ?>
					
					<div class="form-group">	
					<?php echo form_label(lang('employees_inactive'), 'inactive',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
							echo	form_checkbox(array(
								'name' => 'inactive',
								'id' => 'inactive',
								'value' => 1,
								'checked' => $person_info->inactive,
								));
								echo '<label for="inactive"><span></span></label>';;
							?>
						</div>
					</div>
					
					<div id="inactive_info">
						<div class="form-group">	
						<?php echo form_label(lang('employees_reason_inactive'), 'reason_inactive',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'reason_inactive',
								'id'=>'reason_inactive',
								'class'=>'form-control text-area',
								'value'=>$person_info->reason_inactive,
								'rows'=>'5',
								'cols'=>'17')		
							);?>
							</div>
						</div>
						
						<div class="form-group offset1">
							<?php echo form_label(lang('employees_termination_date'), 'termination_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							    <div class="input-group date">
									<span class="input-group-addon bg">
			                           <i class="ion ion-ios-calendar-outline"></i>
			                       	</span>
									<?php echo form_input(array(
								        'name'=>'termination_date',
								        'id'=>'termination_date',
										'class'=>'form-control datepicker',
								        'value'=>$person_info->termination_date ? date(get_date_format(), strtotime($person_info->termination_date)) : '')
								    );?> 
							    </div>
						    </div>
						</div>
					</div>
					
					<div class="form-group">	
					<?php echo form_label(lang('common_language'), 'language',array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('language', array(
							'vietnam'  => 'Việt nam',
							// 'english'  => 'English',
//							'indonesia'    => 'Indonesia',
//							'spanish'   => 'Español', 
//							'french'    => 'Fançais',
//							'italian'    => 'Italiano',
//							'german'    => 'Deutsch',
//							'dutch'    => 'Nederlands',
//							'portugues'    => 'Portugues',
//							'arabic' => 'العَرَبِيةُ‎‎',
//							'khmer' => 'Khmer',
							),
							$person_info->language ? $person_info->language : $this->Appconfig->get_raw_language_value(), 'class="form-control" id="language"');
							?>
						</div>
					</div>
					
					<?php if (count($locations) == 1) { ?>
						<?php
							echo form_hidden('locations[]', current(array_keys($locations)));
						?>
					<?php }else { ?>
						<div class="form-group">	
						<?php echo form_label(lang('common_geographical_area'), null,array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<ul id="locations_list" class="list-inline">
							<?php
								foreach($locations as $location_id => $location) 
								{
									$checkbox_options = array(
									'name' => 'locations[]',
									'id' => 'locations'.$location_id,
									'value' => $location_id,
									'checked' => $location['has_access'],
									);
									
									if (!$location['can_assign_access'])
									{
										$checkbox_options['disabled'] = 'disabled';
										
										//Only send permission if checked
										if ($checkbox_options['checked'])
										{
											echo form_hidden('locations[]', $location_id);
										}
									}
																
									echo '<li>'.form_checkbox($checkbox_options). '<label for="locations'.$location_id.'"><span></span></label> '.$location['name'].'</li>';
								}
							?>
							</ul>
							</div>
						</div>
					<?php } ?>

					<div class="form-group">
                            <?php echo form_label(lang('employees_department'), 'department_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_dropdown('department_id', $departments, $person_info->department_id, 'class="form-control" id="department_id"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo form_label(lang('employees_group'), 'group_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_dropdown('group_id', $groups, $person_info->group_id, 'class="form-control" id="group_id"'); ?>
                            </div>
                        </div>
				

				

				
					
			</div>
		</div>
	</div>

<!-- Ket thuc thong tin -->


<!-- logo -->

<div class="col-md-6">
	<div class="tab-content">
		<div class="panel-heading header-tab">
			<h3 class="panel-title"><i class="ion-ios-person"></i>Ảnh đại diện </h3>
		</div>
		 <div class="form-group">	
			<?php echo form_label('', 'image_id',array('class'=>'')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
		      		<div class="list-unstyled avatar-list" style="padding-top: 15px;">
						<div style="float: left;">
							<?php echo $person_info->image_id ? '<div id="avatar">'.img(array('src' => site_url('app_files/view/'.$person_info->image_id),'class'=>'avatar', 'style' => "height: 143px;",)).'</div>' : '<div id="avatar">'.img(array('src' => base_url().'assets/img/avatar.png','class'=>'img-polaroid' ,'style' => "height: 143px;",'id'=>'image_empty')).'</div>'; ?>
						</div>
						<div style="float: left; padding: 50px 15px;">
							<input type="file" name="image_id" id="image_id" class="filestyle" data-input="false">
						</div>
					</div>
					<div style="clear: both;"></div>
				</div>
			</div>
		
		<?php if($person_info->image_id) {  ?>

		<div class="form-group">
		<?php echo form_label(lang('common_del_image').' :', 'del_image',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label', 'style' => 'text-align: left')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
			<?php echo form_checkbox(array(
				'name'=>'del_image',
				'id'=>'del_image',
				'class'=>'delete-checkbox', 
				'value'=>1
			));
			echo '<label for="del_image"><span></span></label> ';
			
			?>
			</div>
		</div>

		<?php }  ?>



		</div>
</div>

<!-- ket thuc logo -->


<!-- BAT DAU PHAN QUYEN -->


<div class="col-md-12">

	 <div class="form-heading" style="margin-left: 0px; margin-top: 30px;">
						<p><?php echo lang("employees_permission_info") . ' (' . lang("employees_permission_desc") . ')'; ?></p>
					</div>

					<div class="panel-body pq-2 form-group">

                        

						<ul id="permission_list" class="list-unstyled">
						<?php
						foreach($all_modules->result() as $module)
						{
							if($module->sort != 1)
							{
								$checkbox_options = array(
								'name' => 'permissions[]',
								'id' => 'permissions'.$module->module_id,
								'value' => $module->module_id,
								'checked' => $this->Employee->has_module_permission($module->module_id,$person_info->person_id, true),
								'class' => 'module_checkboxes '
								);
								
								if ($logged_in_employee_id != 1)
								{
									if(($current_employee_editing_self && $checkbox_options['checked']) || !$this->Employee->has_module_permission($module->module_id,$logged_in_employee_id, false))
									{
										$checkbox_options['disabled'] = 'disabled';
										
										//Only send permission if checked
									if ($checkbox_options['checked'] && $checkbox_options['disabled']!= 'disabled')
										{
											echo form_hidden('permissions[]', $module->module_id);
										}
									}
								}
						?>
						<li>	
						<?php echo form_checkbox($checkbox_options).'<label for="permissions'.$module->module_id.'"><span></span></label>'; ?>
						<span class="text-success"><?php echo lang('module_'.$module->module_id);?>:</span>
						<span class="text-warning"><?php echo lang('module_'.$module->module_id.'_desc');?></span>
							<ul class="list-unstyled list-permission-actions">
							<?php
							$module_actions = $this->Module_action->get_module_actions($module->module_id)->result();
							foreach($module_actions as $module_action)												 
							{
								$checkbox_options = array(
								'name' => 'permissions_actions[]',
								'data-module-checkbox-id' => 'permissions'.$module->module_id,
								'module' => $module->module_id,
								'action' => $module_action->action_id,
								'class' => 'module_action_checkboxes',
								'id' => 'permissions_actions'.$module_action->module_id."|".$module_action->action_id,
								'value' => $module_action->module_id."|".$module_action->action_id,
								'checked' => $this->Employee->has_module_action_permission($module->module_id, $module_action->action_id, $person_info->person_id, true)
								);
								if($this->Employee->has_module_group_action_permission($module->module_id, $module_action->action_id, $person_info->group_id)){
									$checkbox_options['disabled'] = 'disabled';
								}
								if ($logged_in_employee_id != 1)
								{
									if(($current_employee_editing_self && $checkbox_options['checked']) || (!$this->Employee->has_module_action_permission($module->module_id,$module_action->action_id,$logged_in_employee_id, true)))
									{
										$checkbox_options['disabled'] = 'disabled';
										
										//Only send permission if checked
										if ($checkbox_options['checked'] && $checkbox_options['disabled'] != 'disabled')
										{
											echo form_hidden('permissions_actions[]', $module_action->module_id."|".$module_action->action_id);
										}
									}							
								}
								?>
								<li>
								<?php echo form_checkbox($checkbox_options).'<label for="permissions_actions'.$module_action->module_id."|".$module_action->action_id.'"><span></span></label>'; ?>
								<span class="text-info"><?php echo lang($module_action->action_name_key);?></span>
								</li>
							<?php
							}
							?>
							</ul>
						</li>
						<?php
							}
						}
						?>
						</ul>
					
					</div>



	<?php echo form_hidden('redirect_code', $redirect_code); ?>

					<div class="form-actions pull-right">
					<?php
							echo form_submit(array(
								'name'=>'submitf',
								'id'=>'submitf',
								'value'=>'Lưu',
								'class'=>'btn btn-primary float_right')
							);

					?>
					</div>


		</div>



<!-- KET THUC PHAN QUYEN -->




        <?php $this->load->view('attribute_sets/widgets/attribute_set', array('entity_info' => $person_info)); ?>
        <?php $this->load->view('attribute_sets/widgets/attributes'); ?>
		<?php echo form_close(); ?>
	</div>
</div>
</div>					

<script type='text/javascript'>
$('#image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

//validation and submit handling
$(document).ready(function()
{


$('#birthday').datepicker({ format: 'dd-mm-yyyy' });
$('#hire_date').datepicker({ format: 'dd-mm-yyyy' });

// Ẩn hiện phân quyền



	$('#group_id').on('change',function(){
		$.ajax({
			url:window.location.href ,
			type:'POST',
			data: {group_id: $(this).val()},
			success: function(data){
				$('#permission_list').html(JSON.parse(data));
			}
			
		})
		
	});



	
	// TODO
	$('input[module="customers"][action="view_scope_owner"]').change(function(){
		$(this).prop('checked', true);
		$('input[module="customers"][action="view_scope_location"], input[module="customers"][action="view_scope_all"]').prop('checked', false);
	});

	$('input[module="customers"][action="view_scope_all"]').change(function(){
		if($(this).is(':checked')){
			$('input[module="customers"][action="view_scope_location"], input[module="customers"][action="view_scope_owner"]').prop('checked', false);
		} else {
			$('input[module="customers"][action="view_scope_owner"]').prop('checked', true);
		}
	});

	$('input[module="customers"][action="view_scope_location"]').change(function(){
		if($(this).is(':checked')){
			$('input[module="customers"][action="view_scope_owner"], input[module="customers"][action="view_scope_all"]').prop('checked', false);
		} else {
			$('input[module="customers"][action="view_scope_owner"]').prop('checked', true);
		}
	});

	date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);	
	$("#inactive").change(check_inactive);
	
	check_inactive();
	
	function check_inactive()
	{
		if ($("#inactive").prop('checked'))
		{
			$("#inactive_info").show();
		}
		else
		{
			$("#inactive_info").hide();
		}
	}
	
	
	
    setTimeout(function(){$(":input:visible:first","#employee_form").focus();},100);
	$('body').on('change','.module_checkboxes',function()
	{
		if ($(this).prop('checked'))
		{
			$(this).parent().find('input[type=checkbox]').not(':disabled').prop('checked', true);
		}
		else
		{
			$(this).parent().find('input[type=checkbox]').not(':disabled').prop('checked', false);			
		}
	});
	
	$('body').on('change','.module_action_checkboxes',function()
	{
		if ($(this).prop('checked'))
		{
			$('#'+$(this).data('module-checkbox-id')).prop('checked', true);
		}
	});	

	$('#employee_form').validate({
		submitHandler:function(form)
		{
			$.post('<?php echo site_url("employees/check_duplicate");?>', {term: $('#first_name').val()+' '+$('#last_name').val()},function(data) {
			<?php if(!$person_info->person_id) { ?>
			if(data.duplicate)
			{					
				bootbox.confirm(<?php echo json_encode(lang('employees_duplicate_exists'));?>, function(result)
				{
					if (result)
					{
						doEmployeeSubmit(form);
					}
				});					
			}
			else
			{
				doEmployeeSubmit(form);
			}
			<?php } else { ?>
				doEmployeeSubmit(form);
			<?php } ?>
			} , "json")
			.error(function() { 
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
			first_name: "required",
			
			username:
			{
				<?php if(!$person_info->person_id) { ?>
				remote: 
			    { 
					url: "<?php echo site_url('employees/exmployee_exists');?>", 
					type: "post"
			    }, 
				<?php } ?>
				required:true,
			},

			password:
			{
				<?php
				if($person_info->person_id == "")
				{
				?>
				required:true,
				<?php
				}
				?>
				minlength: 8
			},	
			repeat_password:
			{
 				equalTo: "#password"
			},
    		
			employee_number:
			{
				"required":true,
				maxlength:200
			},
			"locations[]": "required",
			sex:
			{
				"number":true,
				maxlength:1,
			},
			indentity_card:
			{
				maxlength:60
			},
			phone_number:
			{
				maxlength:13
			},
			birthday:
			{
				maxDate:'',
			},
			hire_date:
			{
				maxDate:'',
			},
			email:
			{
				email:true,
			}




   		},
		messages: 
		{
     		first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
     		last_name: <?php echo json_encode(lang('common_last_name_required')); ?>,
     		username:
     		{
				<?php if(!$person_info->person_id) { ?>
	     			remote: <?php echo json_encode(lang('employees_username_exists')); ?>,
				<?php } ?>
     			required: <?php echo json_encode(lang('common_username_required')); ?>,
     			minlength: <?php echo json_encode(lang('common_username_minlength')); ?>
     		},
			password:
			{
				<?php
				if($person_info->person_id == "")
				{
				?>
				required:<?php echo json_encode(lang('employees_password_required')); ?>,
				<?php
				}
				?>
				minlength: <?php echo json_encode(lang('common_password_minlength')); ?>
			},
			repeat_password:
			{
				equalTo: <?php echo json_encode(lang('common_password_must_match')); ?>
     		},
			"locations[]": <?php echo json_encode(lang('employees_one_location_required')); ?>,
			
			employee_number:
			{
				required:'Mã số nhân viên cần phải nhập',
				maxlength:'Tối đa 200 ký tự'
			},
			sex:'Chọn giới tính nam hoặc nữ',
			indentity_card:"Số CMND không được quá 60 ký tự",
			phone_number: "Số điện thoại không đúng",
			email:"Email không đúng định dạng",
			
		}
	});
});

var submitting = false;

function doEmployeeSubmit(form)
{
	$("#grid-loader").show();
	if (submitting) return;
	submitting = true;

	$(form).ajaxSubmit({
	success:function(response)
		{
			$("#grid-loader").hide();
			submitting = false;
			if(response.redirect_code==1 && response.success)
			{
				if (response.success)
				{
					show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
				}
				else
				{
					show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
				}
			}
			else if(response.redirect_code==2 && response.success)
			{
				window.location.href = '<?php echo site_url('employees'); ?>';
			}
			else if(response.success)
			{
				show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
				$("html, body").animate({ scrollTop: 0 }, "slow");
			}
			else
			{
				show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
				$("html, body").animate({ scrollTop: 0 }, "slow");
			}
		},
	<?php if(!$person_info->person_id) { ?>
	resetForm: true,
	<?php } ?>
	dataType:'json'
	});
}
</script>
<div class="modal fade box-modal" id="quick_modal">
</div>
<?php $this->load->view("partial/footer"); ?>
