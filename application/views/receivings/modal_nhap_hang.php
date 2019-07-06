<?php $ma_don_nhap_hang = $this->config->item('receive_prefix') ?>
<div class="row" id="form">
	<div class="col-md-12">
		<div class="portlet box blue ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> <?php echo lang('receivings_register')." - ".lang('receivings_edit_receiving'); ?> <?php echo $ma_don_nhap_hang.' '.$receiving_info['receiving_id']; ?> </div>
        </div>

        <div class="portlet-body form">
			<?php echo form_open("receivings/save/".$receiving_info['receiving_id'],array('id'=>'receivings_edit_form','class'=>'form-horizontal')); ?>
                <div class="form-body">
                        	<label class="form_control_1"><?php echo lang('receivings_receipt'); ?></label>
                        	<a href="<?php echo 'receivings/receipt/'.$receiving_info['receiving_id']; ?>"><?php echo $ma_don_nhap_hang.' '.$receiving_info['receiving_id']; ?></a>
                        	<br>

                        	<label class="form_control_1"><?php echo lang('common_date'); ?></label>
                        	<input type="text" name="date" value="<?php echo date(get_date_format()." ".get_time_format(), strtotime($receiving_info['receiving_time'])); ?>" class="form-control" id="date"> 

                        	<label class="form_control_1"><?php echo lang('receivings_supplier'); ?></label>
                        	<?php echo form_dropdown('supplier_id', $suppliers, $receiving_info['supplier_id'], 'id="supplier_id"');?>
                        	<br>
							
							<label class="form_control_1"><?php echo lang('common_employee'); ?></label>
                        	<?php echo form_dropdown('employee_id', $employees, $receiving_info['employee_id'], 'id="employee_id"');?>
                        	<br>
							
							<label class="form_control_1"><?php echo lang('common_comment'); ?></label>
                        	<?php echo form_textarea(array('name'=>'comment','value'=>$receiving_info['comment'],'rows'=>'5','cols'=>'10', 'id'=>'comment','class'=>'form-control textarea'));?>

						
	
                        <div class="form-actions">
                        	<input type="submit" name="submit" value="<?php echo lang('common_submit'); ?>" class="btn btn-info submitzz pull-right">

                        	<?php if ($receiving_info['deleted']) { ?>
								<?php echo form_open("receivings/undelete/".$receiving_info['receiving_id'],array('id'=>'receivings_undelete_form')); ?>
		                    		<input type="submit" name="undelete_submit_form" id="undelete_submit_form" value="<?php echo lang('receivings_undelete_entire_sale'); ?>" class="btn btn-default submitzz pull-right">
							</form>
		    				
						<?php } else { ?>
							
							<?php 
							 if ($this->Employee->has_module_action_permission('receivings', 'edit_receiving', $this->Employee->get_logged_in_employee_info()->person_id)){
						   		$edit_recv_url = $receiving_info['suspended'] ? 'unsuspend' : 'change_recv';
								echo form_open("receivings/$edit_recv_url/".$receiving_info['receiving_id'],array('id'=>'receivings_change_form')); ?>
								<input type="submit" name="edit_submit_form" id="edit_submit_form" value="<?php echo lang('receivings_edit'); ?>" class="btn btn-primary submitzz pull-right">
							</form>		
							<?php }	?>
							
							<?php 
							if ($this->Employee->has_module_action_permission('receivings', 'delete_receiving', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
							
								<?php echo form_open("receivings/delete/".$receiving_info['receiving_id'],array('id'=>'receivings_delete_form')); ?>
									<input type="submit" name="delete_submit_form" id="delete_submit_form" value="<?php echo lang('receivings_delete_entire_receiving'); ?>" class="btn btn-danger delete_button delete_btnz pull-right">
								</form>
								<?php } ?>
							<?php } ?>

	                    	
	                	</div>
                   
                </div>
			<?php form_close(); ?>
        </div>
    </div>
                                



<script type="text/javascript" language="javascript">
$(document).ready(function()
{	
	$("#employee_id").select2();
	$("#supplier_id").select2();
	
	date_time_picker_field($('#date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
	
	$("#receivings_undelete_form").submit(function()
	{
		var unDeleteForm = this;
		
		bootbox.confirm(<?php echo json_encode(lang("receivings_undelete_confirmation")); ?>, function(result)
		{
			if (result)
			{
				unDeleteForm.submit();
			}
		});
		
		return false;
		
	});
	
	$("#receivings_delete_form").submit(function()
	{
		var deleteForm = this;
		bootbox.confirm(<?php echo json_encode(lang("receivings_delete_confirmation")); ?>, function(result)
		{
			if (result)
			{
				deleteForm.submit();
			}
		});
		
		return false;
	});
	
	var submitting = false;
	$('#receivings_edit_form').validate({
		submitHandler:function(form)
		{
			if (submitting) return;
			submitting = true;
			
			$(form).ajaxSubmit({
			success:function(response)
			{
				submitting = false;
				if(response.success)
				{
					show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);
					$('#sua_xoa_don_nhap_hang').modal('hide');
				}
				else
				{
					show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);					
				}
				
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
   		},
		messages: 
		{
		}
	});
});
</script>
    </div>
</div>