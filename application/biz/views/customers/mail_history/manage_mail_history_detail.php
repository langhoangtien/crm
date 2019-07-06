<?php $this->load->view("partial/header"); ?>

<div class="manage_buttons">
	<div class="manage-row-options hidden" data-table="mail_template">
		<div class="email_buttons text-center">
			 <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
			<a href="javascript:;" id="delete_multi" class="btn btn-red btn-lg"><?php echo lang('common_clear_selection'); ?></a><?php } ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<div class="search no-left-border">
				<input type="text" class="form-control data-n9-s" name ='s_keywords' id='search' value="" placeholder="<?php echo lang('common_search'); ?>"/>
			</div>
			<div class="clear-block <?php echo (!isset($search)||$search=='') ? 'hidden' : ''  ?>">
				<a class="clear" href="<?php echo site_url($controller_name.'/clear_state_mail'); ?>">
					<i class="ion ion-close-circled"></i>
				</a>	
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
						<a id ="template_list" href ="<?php echo base_url().'customers/manage_mail/1'?>">
							<h3 class="panel-title space_title">
								Danh sách mẫu email
								<span class="badge bg-primary tip-left" id="count_template_list"><?php echo $total_rows_mail_temp;?></span>
							</h3>
						</a>
					</li>
					<li>
						<a id ="campain_list" href ="<?php echo base_url().'customers/manage_mail_campain/1';?>">
							<h3 class="panel-title space_title">
							Danh sách chiến dịch email
								<span class="badge bg-primary tip-left" id="count_campain_list"><?php echo $total_mail_campain;?></span>
							</h3>
						</a>
					</li>
					<li class = "active">
						<a id ="history_list" href ="<?php echo base_url().'customers/manage_mail_history_input/';?>">
							<h3 class="panel-title space_title">
								Lịch sử email
								<span class="badge bg-primary tip-left" id="count_history_list">x</span>
							</h3>
						</a>
					</li>
					
				</ul>
			</div>
			<div  class="panel-body nopadding table_holder table-responsive" >
				<table class="tablesorter table table-hover" id="sortable_table">
					<thead>
						<tr>
							<th><input type="checkbox" id="select_all" /><label for="select_all"><span></span></label></th>
							<th>STT</th>
							<th>Tên khách hàng</th>
							<th>Email/Sdt</th>
							<th>Tiêu đề </th>
							<th>Thời gian</th>
							<th>Trạng thái</th>
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
<div class="modal fade box-modal" id="quick_modal">
</div>
<div id="my_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
</div>
<div id="my_table" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
</div>
<div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
<script type="text/javascript">
function view_mail(mail_history_id) {
			$.ajax({
					type: "POST",
					url: BASE_URL + 'reports/mail_history_content',
					data: {
							mail_history_id : mail_history_id
					},
					success: function(html){
							$('#quick_modal').addClass('size-800');
							$('#quick_modal').html(html);
							$('#quick_modal').modal('toggle');
					}
			});
		}
$(document).ready(function(){
	var baseUrl = '<?php echo $baseUrl?>';
	var search_text = '<?php echo $_SESSION['arrParamsMailHistory']['search_text'];?>';
	var tabId = '#count_history_list';
	var table_columns = ["", "p.last_name", 'mh_title','', ''];
	enable_sorting(baseUrl+'/'+'<?php echo $_SESSION['arrParamsMailHistory']['page']?>' +"/t",table_columns,0,"p.last_name",'asc', '#sortable_table','#_pagination',false);
	enable_select_all();
	enable_checkboxes();
	enable_row_selection();
	enable_search_2(baseUrl+'/1',400,'#sortable_table tbody','#_pagination',tabId);
	$('#search').val(search_text);

	/* 
	* EVENT 
	*/
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
			
				change_the_pagination(baseUrl+'/1',tabId);
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
				bootbox.confirm(' Bạn có chắc chắn xóa?', function(result)
				{
						if (result)
						{
								$.ajax({
										type: "POST",
										url: '<?php echo site_url("$controller_name/manage_mail_history_delete");?>',
										data: {
												items : selected
										},
										success: function(data){
											if(JSON.parse(data)['flag']=='true')
											{
												show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
											}
											else
											{
												show_feedback('error','Xóa thất bại',JSON.parse(data)['msg']);
											}
												window.location.href = baseUrl;
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
