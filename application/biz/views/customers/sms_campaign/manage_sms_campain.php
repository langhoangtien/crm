<?php $this->load->view("partial/header"); ?>

<div class="manage_buttons">
	<div class="manage-row-options hidden" >
		<div class="email_buttons text-center">
			 <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
			<a href="javascript:;" id="delete_multi" class="btn btn-danger btn-lg"><?php echo lang('common_clear_selection'); ?></a>
			<?php } ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<div class="search no-left-border">
				<input type="text" class="form-control data-n9-s" name ='s_keywords' id='search' data-table="mail_template" value="" placeholder="<?php echo lang('common_search'); ?>"/>
			</div>
			<div class="clear-block <?php echo (!isset($search)||$search=='') ? 'hidden' : ''  ?>">
				<a class="clear" href="<?php echo site_url($controller_name.'/clear_state_mail'); ?>">
					<i class="ion ion-close-circled"></i>
				</a>	
			</div>
		</div>
		
		<div class="col-md-7">	
			<div class="buttons-list">
				<div class="pull-right-btn">
				<?php
					if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {
				?>
					<a href="javascript:;" onclick="update_sms_campain(-1);" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới sms"><span class=""><?php echo lang('customers_add_new') ?></span></a>
					<a href="<?php echo base_url() . 'customers'; ?>" class="btn btn-primary btn-lg" title="Danh sách khách hàng"><span class=""><?php echo lang('customers_list_of_customers') ?></span></a>
				<?php
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
				<ul class="nav nav-tabs">
					<li>
						<a id ="template_list" href="<?php echo base_url().'customers/manage_sms/1'?>">
							<h3 class="panel-title space_title">
								<?php echo lang('customers_sample_form_sms'); ?>
								<span class="badge bg-primary tip-left" id="count_template_list"><?php echo $total_rows_sms_temp;?></span>
							</h3>
						</a>
					</li>
					<li class = "active">
						<a id ="campain_list"  href="<?php echo base_url().'customers/manage_sms_campain/1'?>">
							<h3 style="color:red;" class="panel-title space_title">
								<?php echo lang('customers_sms_campaign_list'); ?>
								<span class="badge bg-primary tip-left" id="count_campain_list">0</span>
							</h3>
						</a>
					</li>
					<li>
						<li>
						<a id ="send_history_list" href="<?php echo base_url().'customers/manage_sms_history_input'?>">
							<h3 class="panel-title space_title" >
							<?php echo lang('customers_history_sms'); ?>
								<span class="badge bg-primary tip-left" id="count_campain_list">x</span>
							</h3>
						</a>
					</li>
					</li>
					
				</ul>
			</div>
			<div  class="panel-body nopadding table_holder table-responsive" >
				<table class="tablesorter table table-hover" id="sortable_table">
					<thead>
						<tr>
							<th><input type="checkbox" id="select_all" /><label for="select_all"><span></span></label></th>
							<th><?php echo lang('customers_id_sms') ?></th>
							<th><?php echo lang('customers_campaign_name') ?></th>
							<th><?php echo lang('customers_title_sms') ?></th>
							<th><?php echo lang('customers_time_of_submission') ?></th>
							<th><?php echo lang('customers_repeat') ?></th>
							<th><?php echo lang('customers_active') ?></th>
						</tr>
					</thead>
						<tbody>
						</tbody>
				</table>
			</div>			
			</div>
			<div id="_pagination"  class="panel-options custom"> 
			</div>
	</div>
</div>
<script type="text/javascript">
	function update_sms_campain(id) {
			window.location.href = BASE_URL + 'customers/view_sms_campain/'+id;
		}

$(document).ready(function(){
	
  var table_columns = ["", "sms_campain_id", 'sms_campain_name','', ''];
	enable_sorting("<?php echo site_url("$controller_name/manage_sms_campain/".$_SESSION['arrParamsSmsCampain']['page']."/t"); ?>",table_columns,0,"sms_campain_id",'asc', '#sortable_table','#_pagination',false);
	enable_select_all();
	enable_checkboxes();
	enable_row_selection();
	enable_search_2('<?php echo ("$controller_name/manage_sms_campain/".$_SESSION['arrParamsSmsCampain']['page']);?>',400,'#sortable_table tbody','#_pagination');
		var tabId = 'count_campain_list';
		$('.nav-tabs li a').click(function(e)
		{
			tabId = 'count_'+$(this).attr('id');
			// change_the_pagination(href,tabId);
		});
			/* 
	* EVENT 
	*/
	var baseUrl ='<?php echo $baseUrl;?>';
	
	// callback function when pagination changed
	function change_the_pagination(href,tabId)
	{
		$.ajax({
			url: href+'/t',
			type: 'POST',
			data:{tabId: tabId},
			beforeSend: function()
			{
				$('#sortable_table tbody').html('<img src="assets/img/ajax-loader.gif"  width="16" height="16" />');
			},
			success:function (data){
				$('#sortable_table tbody').html(JSON.parse(data)['manage_table']);
				$('#_pagination').html(JSON.parse(data)['pagination']);
				$('#'+tabId).html(JSON.parse(data)['total_row']);
				
			},
				
				
		});
	}
			
		// Event on first load page
			if(window.location.href == baseUrl)
			{
			
				change_the_pagination('customers/manage_sms_campain/1',tabId);
			}
			else{
					
				change_the_pagination(window.location.href,tabId);
			}
			
	// Event change page view when pagination click
		jQuery("body").on('click','.linkClicked',function(e){
			e.preventDefault();
			href = $(this).attr('href');
			change_the_pagination(href,tabId);
		});
		
		
		 $('#delete_multi').click(function(){
				var selected = get_selected_values();
				bootbox.confirm('<?php echo lang("customers_confirm_delete");?>', function(result)
				{
						if (result)
						{
								$.ajax({
										type: "POST",
										url: '<?php echo site_url("$controller_name/manage_sms_campain_delete");?>',
										data: {
												items : selected
										},
										success: function(string){
												show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
												window.location.href = '<?php echo site_url("$controller_name/manage_sms_campain/1");?>';
										}
								});

						}
				});
		})
		
});

</script>
<?php 
	if(!empty($_SESSION['notice'])) {
		$notice = $_SESSION['notice'];
		unset($_SESSION['notice']);
?>
<script type="text/javascript">
	$( document ).ready(function() {
		toastr.success('<?php echo $notice; ?>', 'Thông báo');
	});
</script>
<?php 
	}
?>
<?php $this->load->view("partial/footer"); ?>
