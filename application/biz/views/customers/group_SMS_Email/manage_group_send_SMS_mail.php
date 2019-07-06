<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function (){
    var table_columns = ["","smsmail_group_id", "name","","",""];
    enable_sorting("<?php echo site_url("$controller_name/manage_group_send_SMS_email/1/t"); ?>", table_columns, 0,'smsmail_group_id','asc', '#sortable_table','#_pagination');
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
		enable_search_2('<?php echo ("$controller_name/manage_group_send_SMS_email/1");?>',400,'#sortable_table tbody','#_pagination');
    enable_delete(<?php echo json_encode(lang('customers_sms_delete_msg_confirm')); ?>,<?php echo json_encode(lang($controller_name . "_none_selected")); ?>);
	
	/* 
	* EVENT 
	*/
	var baseUrl ='<?php echo $baseUrl;?>';
	
	
	// callback function when pagination changed
	function change_the_pagination(href)
	{
		$.ajax({
			url : href+'/t',
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
	
		change_the_pagination('customers/manage_group_send_SMS_email/1');
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

                    bootbox.confirm('Xóa nhóm sẽ xóa các chiến dịch email, SMS, gửi tới nhóm đã xóa', function(result)
                    {
                        if (result)
                        {
                            $.ajax({
                                type: "POST",
                                url: '<?php echo site_url("$controller_name/delete_group_send_SMS_email");?>',
                                data: {
                                    items : selected
                                },
                                success: function(string){
                                    show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
                                    window.location.href = '<?php echo site_url("$controller_name/manage_group_send_SMS_email/1");?>';
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
			
			<a href="javascript:;" id="delete_multi" class="btn btn-danger btn-lg disabled_ delete_inactive " title="<?php echo lang('common_delete'); ?>"><span class=""><?php echo lang('common_delete'); ?></span></a>
			<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo isset($search) ? H($search) : ''; ?>" placeholder="<?php echo lang('common_search'); ?>"/>
				</div>
				<div class="clear-block <?php echo (!isset($search)||$search=='') ? 'hidden' : ''  ?>">
					<a class="clear" href="<?php echo site_url($controller_name.'/clear_state_sms'); ?>">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			</form>	
			
		</div>
		<div class="col-md-7">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					
					<?php echo anchor("$controller_name/update_group_SMS_email/0",'<span class="">'. lang('customers_group_new_group') .'</span>',array('id' => 'new-group-btn', 'class'=>'btn btn-primary btn-lg', 'title'=> lang('common_add').' '.lang('customers_group_send_SMS_email')));
					 echo anchor("$controller_name/manage_mail/0",'<span class="">'.lang('customers_group_mail_management').'</span>',array('id' => 'new-email-btn', 'class'=>'btn btn-primary btn-lg', 'title'=> lang('customers_group_mail_management')));
					  echo anchor("$controller_name/manage_sms/0",'<span class="">'.lang('customers_group_sms_management').'</span>',array('id' => 'new-email-btn', 'class'=>'btn btn-primary btn-lg', 'title'=>lang('customers_group_sms_management') ));
					}	
					?>
				</div>
			</div>				
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo lang('common_list_of').' '.lang('module_'.$controller_name.'_group_send_SMS_mail'); ?>
					<span id="totalRows" title="" class="badge bg-primary tip-left"></span>
					<div class="panel-options custom">
						<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
							
						</div>
					
					</div>
				</h3>
			</div>
			<div id="menutest5" class="panel-body nopadding table_holder table-responsive">
			   <table class="tablesorter table  table-hover" ` id="sortable_table">
					<thead>
						<tr>
							<th><input type="checkbox" id="select_all" /><label for="select_all"><span></span></label></th>
							<th><?php echo lang('customers_group_SMS_id') ?></th>
							<!--<th>id</th>-->
							<th><?php echo lang('customers_group_name_of_group') ?></th>
							<th><?php echo lang('customers_group_description') ?></th>
							<th><?php echo lang('customers_group_customers_number') ?></th>
							<th><?php echo lang('customers_group_action') ?></th>
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
<div class="modal fade box-modal" id="quick_modal">
</div>
<?php $this->load->view("partial/footer"); ?>