<div class="modal-dialog">
	<div class="modal-content customer-recent-sales">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"> <?php echo lang('common_edit_profile'); ?></h4>
		</div>
		<div class="modal-body ">
			<div class="row" id="form">
				
				<div class="spinner" id="grid-loader" style="display:none">
					<div class="rect1"></div>
					<div class="rect2"></div>
					<div class="rect3"></div>
				</div>
				<div class="col-md-12">
					<?php 
					echo form_open('login/do_edit_profile',array('id'=>'employee_form','class'=>'form-horizontal'));
					?>

					<div class="form-group">
						<?php 
						echo form_label('Họ và tên', 'first_name',array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control',
								'name'=>'first_name',
								'id'=>'first_name',
								'value'=>$person_info->first_name)
							);?>
						</div>
					</div>
					<?php $this->load->view("people/form_basic_info1"); ?>

					<legend class="page-header text-info"> &nbsp; &nbsp; <?php echo lang("common_login_info"); ?></legend>
					<div class="form-group <?php if(!$check) echo 'hide' ?>">	
						<?php echo form_label(lang('common_username'), 'username',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php 
							if ($this->Employee->get_logged_in_employee_info()->person_id==1) {
								echo form_input(array(
									'name'=>'username',
									'id'=>'username',
									'class'=>'form-control',
									'value'=>$person_info->username));
							}else{
								echo form_input(array(
									'name'=>'username',
									'id'=>'username',
									'disabled'=>'disabled',
									'class'=>'form-control',
									'value'=>$person_info->username));
							}

							?>
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

					<div class="form-group">	
						<?php echo form_label(lang('common_language'), 'language',array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('language', array(
								// 'english'  => 'English',
								'vietnam'	=> 'VietNam'
							),
							$person_info->language ? $person_info->language : $this->Appconfig->get_raw_language_value(), 'class="form-control"');
							?>
						</div>
					</div>


					<div class="modal-footer">
						<div class="form-acions">
							<?php
							echo form_submit(array(
								'name'=>'submitf',
								'id'=>'submitf',
								'value'=>lang('common_submit'),
								'class'=>'btn btn-primary btn-block float_right btn-lg')
						);

						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>


<?php 
echo form_close();
?>

<script type='text/javascript'>

$('#image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

//validation and submit handling
$(document).ready(function()
{
	setTimeout(function(){$(":input:visible:first","#employee_form").focus();},100);

	$('#employee_form').validate({
		submitHandler:function(form)
		{
			doEmployeeSubmit(form);
		},
		errorClass: "text-danger",
		errorElement: "span",
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
				required:true,
				minlength: 1
			},

			password:
			{
				minlength: 8
			},	
			repeat_password:
			{
				equalTo: "#password"
			},
			email: {
				"required": true
			}
		},
		messages: 
		{
			first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
			last_name: <?php echo json_encode(lang('common_last_name_required')); ?>,
			username:
			{
				required: <?php echo json_encode(lang('common_username_required')); ?>,
				minlength: <?php echo json_encode(lang('common_username_minlength')); ?>
			},
			password:
			{
				minlength: <?php echo json_encode(lang('common_password_minlength')); ?>
			},
			repeat_password:
			{
				equalTo: <?php echo json_encode(lang('common_password_must_match')); ?>
			},
			email: <?php echo json_encode(lang('common_email_invalid_format')); ?>
		}
	});
});

var submitting = false;

function doEmployeeSubmit(form)
{
	$('#grid-loader').show();
	if (submitting) return;
	submitting = true;

	$(form).ajaxSubmit({
		success:function(response)
		{
			$('#grid-loader').hide();
			submitting = false;
			$('#myModal').modal('hide');
			if (response.success)
			{
				show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>+' #' + response.person_id);
			}
			else
			{
				show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
			}
			
		},
		dataType:'json'
	});
}
</script>
