<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
	$(document).ready(function() 
	{ 
		$('#delete_multi').click(function(){
			// var selected = EMPLOYEES_MANAGE.getSelectedItems();
			var selected = [];
			$('.cb input:checked').each(function(){
				selected.push($(this).val());
			});
			console.log(selected);
			if (selected.length == 0) {
				bootbox.alert(<?php echo json_encode('Bạn phải chọn ít nhất 1 bản ghi!'); ?>);
				return false;
			}
			bootbox.confirm('Bạn có chắc muốn xóa không?', function(result)
			{
				if (result)
				{
					$.ajax({
						type: "POST",
						url: '<?php echo site_url("$controller_name/deletes");?>',
						data: {
							items : selected
						},
						success: function(string){
							show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
							window.location.href = '<?php echo site_url($controller_name);?>';
						}
					});

				}
			});
		})

	}); 
</script>

<div class="row">
	<div class="col-md-3">
		<div class="form-group">
			<input id="search_1" name="search_1" type="text" class="form-control fitler" placeholder="Tìm kiếm">
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select class="form-control fitler "  name="rank_1" id="rank_1">
				<option value="" >Ngạch</option>
				<?php foreach ($ranks as  $rank) {
					
					?>
					<option value="<?php echo $rank['id'] ?>"><?php echo $rank['name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select class="form-control fitler "  name="level_1" id="level_1">
				<option value="" >Cấp bậc</option>
			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select  class="form-control fitler " name="group_1" id="group_1">
				<option value="">Chức danh</option>
				<?php foreach ($groups as  $group) {
					?>	
					<option value="<?php echo $group['group_id']?>"><?php echo $group['name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<select class="form-control fitler " name="hire_date_1" id="hire_date_1">
				<option value="">Thời gian làm việc tại VCBS</option>
				<option value="0-1">Nhỏ hơn 1 năm</option>
				<option value="1-3">1 đến 3 năm</option>
				<option value="3-5">3 đến 5 năm</option>
				<option value="5-10">5 đến 10 năm</option>
				<option value="10-100">Lớn hơn 10 năm</option>
			</select>
		</div>
	</div>
</div>
<div class="manage_buttons">	
	<div class="cl">

		<div class="pull-left">

			<div class="manage-row-options hidden">
				<div class="email_buttons text-center">
					<a href="javascript:;" id="delete_multi" class="btn btn-red btn-lg disabled_ delete_inactive " title="Xóa"><span class="btn btn-primary">Xóa</span></a>
				</div>
			</div>
		</div>
		<div class="pull-right">
			<div class="buttons-list">
				<div class="pull-right-btn">					
					<?php if ($this->Employee->has_module_permission('groups', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
						<?php echo anchor('/groups',
							'<span class="">'.lang('groups_manage').'</span>',
							array('id' => 'new-person-btn', 'class'=>'btn btn-primary', 'title' => lang('groups_manage')));?>
						<?php endif; ?>

						<?php if ($this->Employee->has_module_permission('departments', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
							<?php echo anchor('/departments',
								'<span class="">'.lang('departments_manage').'</span>',
								array('id' => 'new-person-btn', 'class'=>'btn btn-primary', 'title' => lang('departments_manage')));?>
							<?php endif; ?>

							<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<?php if ($this->Employee->count_all() < MAX_EMPLOYEE) { ?>
									<?php echo anchor("$controller_name/view/-1/",
										'<span class="">'.lang($controller_name.'_new').'</span>',
										array('id' => 'new-person-btn', 'class'=>'btn btn-primary', 'title'=>lang($controller_name.'_new')));?>
									<?php } ?>
								<?php } ?>

								
								<?php echo anchor("$controller_name/ranks",
									'<span class="">Quản lý cấp bậc</span>',
									array('class'=>'btn btn-primary','title'=>'Quản lý cấp bậc')); ?>


									<a href="<?php echo base_url('employees/employees_log') ?>"class="btn-primary btn" title="Log"><span class="">LOG</span></a>

								</div>
							</div>				
						</div>

					</div>

					<div class="container-fluid">
						<div class="row manage-table">
							<div class="panel panel-piluku" id="list_view">

							</div>	
						</div>
					</div>

					<!-- table -->
					<div class="panel-body sorting nopadding table-responsive">
						<table class="table table-hover tablesorter" id="table_d13">
							<thead>
								<tr>
									<th class="leftmost" style="width: 20px;">
										<input type="checkbox" name="select_all" id="see" value="select_all"">
										<label id="select_all"><span></span></label>
									</th>
									<th class="text-left hr-lbl headerSort" order ="id">STT</th>
									<th class="text-left hr-lbl headerSort" order ="location_name">Khu vực</th>
									<th class="text-left hr-lbl headerSort" order ="first_name">Tên nhân sự</th>
									<th class="text-left hr-lbl headerSort" order ="group_name">Chức vụ</th>
									<th class="text-left hr-lbl headerSort" order ="rank">Ngạch</th>
									<th class="text-left hr-lbl headerSort" order ="level">Cấp bậc</th>
									<th class="text-left hr-lbl headerSort" order ="email">Email</th>
									<th class="text-left hr-lbl headerSort" order ="phone_number">SĐT</th>
									<th class="text-left hr-lbl headerSort" order ="hire_date">Thời gian vào làm việc tại VCBS</th>
									<th class="text-left hr-lbl">Cập nhật</th>
								</tr>
							</thead>
							<tbody>
								<?php $i=1; foreach ($employees as  $employee) { ?>

									<tr>
										<td class="cb">
											<input type="checkbox" name="ids[<?php echo $employee['id'] ?>]" value="<?php echo $employee['id'] ?>" id="item_<?php echo $employee['id'] ?>">
											<label><span></span></label>
										</td>
										<td><?php echo $i; $i++ ?></td>
										<td><?php echo $employee['location_name'] ?></td>
										<td><a href="<?php echo base_url('reports/specific_employees_d13/'.$employee['employee_id']) ?>"><?php echo $employee['first_name'] ?></a></td>
										<td><?php echo $employee['group_name'] ?></td>
										<td><?php echo $employee['rank'] ?></td>
										<td><?php echo $employee['level'] ?></td>
										<td><?php echo $employee['email'] ?></td>
										<td><?php echo $employee['phone_number'] ?></td>
										<td><?php echo $employee['hire_date'] ?></td>
										<td><a href="<?php echo base_url('employees/view/'.$employee['id']) ?>">Sửa</a></td>
										
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<!-- ppagionation -->


<!-- <div style="text-align: center;">
	<?php echo $pagination ?>
</div> -->

<!-- pagination -->

</div>


<!-- end table -->


<script>
	$(document).ready(function(){
		var hire_date_1=$('.hire_date_1').val(),rank_1=$('.rank_1').val(),level_1= $('#level_1').val(),group_1=$('.group_1').val(),search_1=$('.search_1').val(), order = "id", order_by = "desc";



		$('#rank_1').change(function(){
			$.ajax({
				type:'post',
				url:"<?php echo base_url('employees/level') ?>",
				dataType:'json',
				data: {
					rank_1: $('#rank_1').val(),					
				},
				success: function(result){
					var html='<option value="">Cấp bậc</option>';
					console.log(typeof result);
					if(Array.isArray(result))
					{
						$.each(result,function(key,item){
							html +='<option value="'+item.id+'">'+item.name+'</option>';
						});
					}
					$('#level_1').html(html);
					$("#level_1").val("");
					$("#level_1").trigger("change");
				},
			});


		});


		$('.fitler').change(function(){
			$('.manage-row-options').addClass('hidden');
			$.ajax({
				type:'post',
				url:"<?php echo base_url('employees/sorting_v1') ?>",
				dataType:'text',
				data: {
					hire_date_1: $('#hire_date_1').val(),
					rank_1: $('#rank_1').val(),
					group_1: $('#group_1').val(),
					search_1: $('#search_1').val(),
					level_1:  $('#level_1').val(),
					order:order,
					order_by:order_by,

				},
				success: function(result){
					$('.sorting').html(result);
				},
			});

		});

		
		$(document).on('click','.pagi a',function(event){
			$('.manage-row-options').addClass('hidden');
			event.preventDefault();	
			$.ajax({
				type:'post',
				url:"<?php echo base_url('employees/sorting_v1')?>"+'/'+ $(this).attr('data-ci-pagination-page'),
				dataType:'text',
				data: {
					hire_date_1: $('#hire_date_1').val(),
					rank_1: $('#rank_1').val(),
					group_1: $('#group_1').val(),
					search_1: $('#search_1').val(),
					level_1: $('#level_1').val(),
					order:order,
					order_by:order_by,


				},
				success: function(result){
					$('.sorting').html(result);
					$('.manage-row-options').addClass('hidden');
				},
			});
			
		});


		$(document).on('click','.headerSort',function(){
			$('.manage-row-options').addClass('hidden');
			order = $(this).attr('order');
			order_by = (order_by =="desc") ? "asc" :"desc";
			$.ajax({
				type:'post',
				url:"<?php echo base_url('employees/sorting_v1') ?>",
				dataType:'text',
				data: {
					hire_date_1: $('#hire_date_1').val(),
					rank_1: $('#rank_1').val(),
					group_1: $('#group_1').val(),
					search_1: $('#search_1').val(),
					level_1: $('#level_1').val(),
					order:order,
					order_by:order_by,

				},
				success: function(result){
					$('.sorting').html(result);
				},
			});
			
		});


// ĐẾM SỐ CHECK BOX

$('body').on('click','#table_d13 tbody tr td.cb',function(){

	var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
	var table = $('#table_d13 tbody');
	if (checkbox.prop('checked') == true){ 
		checkbox.prop('checked', false);
	}else{
		$('.manage-row-options').show();
		checkbox.prop('checked', true);
	}

	var checked_box = table.find('input:checked');
	if(checked_box.length == 0) 
		$('.manage-row-options').addClass('hidden');
	else
		$('.manage-row-options').removeClass('hidden');
});

	// check all
	$('body').on('click','#select_all',function(){
		console.log('ducang')
		var checkbox = $(this).closest('th').find('input[type="checkbox"]'); 
		var table = $('#table_d13 tbody');

		if (checkbox.prop('checked') == true){ 
			$('.manage-row-options').addClass('hidden');
			checkbox.prop('checked', false);
			table.find('td input[type="checkbox"]').prop('checked', false);
		}else{
			$('.manage-row-options').removeClass('hidden');
			checkbox.prop('checked', true);
			table.find('td input[type="checkbox"]').prop('checked', true);
		}
	});



});

</script>

<style>
.tablesorter .headerSort {
	background-repeat: no-repeat;
	background-position: center right;
	background-image:none;
	cursor: pointer;
</style>
<?php $this->load->view("partial/footer"); ?>