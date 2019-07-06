<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function (){
    var table_columns = ["","people.person_id", "people.last_name",];
    enable_sorting("<?php echo site_url("$controller_name/manage_group_SMS_email_detail/$smsmail_group_id/1/t"); ?>", table_columns, 0,'people.person_id','asc', '#sortable_table','#_pagination');
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
	enable_search_2('<?php echo ("$controller_name/manage_group_SMS_email_detail/$smsmail_group_id/1");?>',400,'#sortable_table tbody','#_pagination');
    enable_delete(<?php echo json_encode(lang('customers_sms_delete_msg_confirm')); ?>,<?php echo json_encode(lang($controller_name . "_none_selected")); ?>);
	
	/* 
	* EVENT 
	*/
	var baseUrl ='<?php echo $baseUrl;?>';
	
	
	// callback function when pagination changed
	function change_the_pagination(href)
	{
		$.ajax({
			url: href+'/t',
			type: 'GET',
			beforeSend: function()
			{
				$('#sortable_table tbody').html('<img src="assets/img/ajax-loader.gif"  width="16" height="16" />');
			},
			success:function (data){
				$('#sortable_table tbody').html(JSON.parse(data)['manage_table']);
				$('#_pagination').html(JSON.parse(data)['pagination']);
				$('#totalRows').html(JSON.parse(data)['total_row']);
			},
				
				
		});
	}
  
	// Event on first load page
	if(window.location.href == baseUrl)
	{
	
		change_the_pagination('customers/manage_group_SMS_email_detail/<?php echo $smsmail_group_id;?>/1');
	}
	else{
			
		change_the_pagination(window.location.href);
	}
	
	// Event change page view when pagination click
	jQuery("body").on('click','.linkClicked',function(e){
		e.preventDefault();
		href = $(this).attr('href');
		change_the_pagination(href);
	});
	 $('#delete_multi').click(function(){
                    var selected = get_selected_values();

                    bootbox.confirm('<?php echo lang("customers_confirm_delete");?>', function(result)
                    {
                        if (result)
                        {
                            $.ajax({
                                type: "POST",
                                url: '<?php echo site_url("$controller_name/delete_group_SMS_email_detail");?>',
                                data: {
                                    items : selected,
																		smsmail_group_id:'<?php echo $smsmail_group_id;?>',
                                },
                                success: function(string){
                                    show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
                                    window.location.href = '<?php echo site_url("$controller_name/manage_group_SMS_email_detail/$smsmail_group_id/1");?>';
                                }
                            });

                        }
                    });
                })
	
	
	
});

</script>
<div class="manage_buttons">
	<div class="manage-row-options hidden">
		<div class="email_buttons text-center">
			
			<a href="javascript:;" id="delete_multi" class="btn btn-red btn-lg disabled_ delete_inactive " title="<?php echo lang('common_delete'); ?>"><span class=""><?php echo lang('common_delete'); ?></span></a>
			<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8 col-lg-8 col-sm-8" >
			
				<div class="search no-left-border">
					<label class="col-md-6 col-lg-7 control-label"><strong>Tên nhóm</strong></label>
					<input disabled type="text" class="form-control" name ='group_sms_email_name' id='group_sms_email_name' value="<?php echo empty($customer_detail)?'':$customer_detail->name;?>" placeholder="<?php echo lang('customers_create_group_name_label'); ?> "/>
				</div>
				<div class="search no-left-border">
					<label id="lb_group_description" class="col-md-6 col-lg-7 control-label"><strong><?php echo lang('customers_create_group_description');?></strong></label>	
					<textarea disabled type="text" class="form-control" name ='group_sms_email_description' id='group_sms_email_description'  placeholder="<?php echo lang('customers_create_group_description'); ?>"><?php echo empty($customer_detail)?'':$customer_detail->description;?></textarea>
				</div>
		</div>
		<div class="col-md-4 col-lg-4 col-sm-4">
				<div class="search no-left-border" style="margin-top: 3rem;">
				<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					<?php echo anchor("$controller_name/update_group_SMS_email/$smsmail_group_id",
						'<span class="">'.lang('customers_group_emailSMS_add').'</span>',
						array('id' => 'update-group-btn', 'class'=>'btn btn-primary btn-lg', 'title'=>lang('customers_group_emailSMS_add')));
					}	
					?>
				</div>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo lang('common_list'); ?>
					<span id="totalRows" title="" class="badge bg-primary tip-left"></span>
					<div class="panel-options custom">
						<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
							
						</div>
					
					</div>
				</h3>
			</div>
			<div id="menutest5" class="panel-body nopadding table_holder table-responsive">
			   <table class="tablesorter table  table-hover" id="sortable_table">
					<thead>
						<tr>
							<th><input type="checkbox" id="select_all" /><label for="select_all"><span></span></label></th>
							<th>STT</th>
							<th><?php echo lang('customers_customer_name');?></th>
							<th><?php echo lang('customers_customer_phoneNumber');?></th>
							<th><?php echo lang('customers_customer_email');?></th>
							<th><?php echo lang('customers_customer_address');?></th>
							<th></th>
							</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<div id="_pagination" class="panel-options custom">
				</div>
        </div>   	
			
		</div>
	</div>
</div>
<style>
.search{
	display:block !important;
}
</style>
<?php $this->load->view("partial/footer"); ?>
