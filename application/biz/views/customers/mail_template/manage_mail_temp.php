<?php $this->load->view("partial/header"); ?>
	
<div class="manage_buttons">
	<div class="manage-row-options hidden" data-table="mail_template">
		<div class="email_buttons text-center">
			 <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
			<a href="javascript:;" id="delete_multi" class="btn btn-danger btn-lg"><?php echo lang('common_clear_selection'); ?></a><?php } ?>
						 
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
					<a href="javascript:;" onclick="add_mail_template();" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới mail"><span class="">Thêm mới</span></a>
					<a href="<?php echo base_url() . 'customers'; ?>" class="btn btn-primary btn-lg" title="Danh sách khách hàng"><span class="">Danh sách khách hàng</span></a>
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
				<h3 class="panel-title">
					<span class="title first active">	<a style=" color:red;" href ="<?php echo base_url().'customers/manage_mail/1'?>">Danh sách mẫu email</a></span>																			 
					<span class="badge bg-primary tip-left" id="count_template_list">0</span>
						
					<span class="title"><a id ="campain_list" href ="<?php echo base_url().'customers/manage_mail_campain/1';?>"><?php echo lang('common_list_of')." ".lang('customers_mail_campain');?></a></span>
					<span class="badge bg-primary tip-left" id="count_campain_list"><?php echo $total_mail_campain;?></span>

					<span class="title"><a id ="history_list" href ="<?php echo base_url().'customers/manage_mail_history_input/';?>"><?php echo lang('customers_mail_send_history');?></a></span>
					<span class="badge bg-primary tip-left" id="count_campain_list">x</span>

					<i class="fa fa-spinner fa-spin loading" id="customer_list_loading" style="display: none;"></i>
				</h3>
			</div>
			<div  class="panel-body nopadding table_holder table-responsive" >
				<table class="tablesorter table table-hover" id="sortable_table">
					<thead>
						<tr>
																								 
							<th><input type="checkbox" id="select_all" /><label for="select_all"><span></span></label></th>
						
							<th>STT</th>
							<th><?php echo lang('customers_manage_mail_title')?></th>
							<th>Danh sách nhóm nhận(Mặc định)</th>
							<th></th>
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
	function add_mail_template() {
			edit_mail_template(-1);
		}

	function edit_mail_template(id) {
			window.location.href = BASE_URL + 'customers/view_mail/'+id;
	}
$(document).ready(function(){
	
	var table_columns = ["", "mail_id", 'mail_title','', ''];
	var search_text = '<?php echo $_SESSION['arrParamsMailTemp']['search_text'];?>';
	var tabId = '#count_template_list';
	enable_sorting("<?php echo site_url("$controller_name/manage_mail/".$_SESSION['arrParamsMailTemp']['page']."/t"); ?>",table_columns,0,"mail_id",'asc', '#sortable_table','#_pagination',false);
	enable_select_all();
	enable_checkboxes();
	enable_row_selection();
	enable_search_2('<?php echo ("$controller_name/manage_mail/1");?>',400,'#sortable_table tbody','#_pagination',tabId);
	$('#search').val(search_text);
	/* 
	* EVENT 
	*/
	var baseUrl ='<?php echo $baseUrl;?>';
	// callback function when pagination changed
	function change_the_pagination(href,tabId)
	{
		$.ajax({
			url	: href+'/t',
			type: 'POST',
			data:{tabId: tabId},
			beforeSend: function()
			{
				$('#sortable_table tbody').html('<img src="assets/img/ajax-loader.gif"  width="16" height="16" />');
			},
			success:function (data){
				$('#sortable_table tbody').html(JSON.parse(data)['manage_table']);
				$('#_pagination').html(JSON.parse(data)['pagination']);
				$(tabId).html(JSON.parse(data)['total_row']);
				
			},
		});
	}
			
		// Event on first load page
			if(window.location.href == baseUrl)
			{
			
				change_the_pagination('customers/manage_mail/1',tabId);
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
				bootbox.confirm('Xóa mẫu mail sẽ xóa các chiến dịch sử dụng mẫu mail này. Bạn có chắc chắn xóa?', function(result)
				{
						if (result)
						{
								$.ajax({
										type: "POST",
										url: '<?php echo site_url("$controller_name/mail_template_delete");?>',
										data: {
												items : selected
										},
										success: function(string){
												show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
												window.location.href = '<?php echo site_url("$controller_name/manage_mail/1");?>';
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
