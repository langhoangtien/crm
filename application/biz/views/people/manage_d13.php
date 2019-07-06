<?php $this->load->view("partial/header"); ?>

<!-- SEARCH AND FILTER -->
<div class="row">
<div class="col-md-6">
	<div class="col-sm-6">
		<div class="form-group">
			<select class="form-control unit_type filter" name="" id="">
				<option value="">Tìm kiếm theo loại đơn vị</option>
				<?php foreach ($all_unit_type as  $value) {
					# code...
				 ?>
				<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
			<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			<input class="form-control search filter" type="text" placeholder="Search">
		</div>
	</div>
</div>
<div class="col-md-6">
	
</div>


</div>

<!-- DELETE AND ADD -->
<div class="row">
	<div class="col-md-1">
		<button class="del_all btn btn-primary hidden" id="delete_multi">Xóa</button>
	</div>
	<div class="col-dm-11">
	<div class="manage_buttons">
	
		<div class="cl">
			<div class="pull-left">

			</div>
			<div class="pull-right">


				<div class="buttons-list">
					<div class="pull-right-btn">

						<a href="<?php echo base_url('suppliers') ?>/view/-1" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới bên thứ ba"><span class="">Thêm mới bên thứ ba</span></a>																<div class="piluku-dropdown">

							<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="ion-android-more-horizontal"></i>
							</button>
							<ul class="dropdown-menu" role="menu">

							
									<li>
										<a href=" <?php echo base_url('suppliers/unit_type') ?> " type="button" class=""><span class="">Thêm mới loại đơn vị</span></a>
									</li>

									<!-- <li>
										<a href="<?php echo base_url('suppliers/excel_export') ?>" class="hidden-xs import" title="Xuất tệp excel"><span class="">Xuất tệp excel</span></a>
									</li> -->
									<!-- <li>
										<a href="<?php echo base_url('suppliers/cleanup') ?>" id="cleanup" class="" title="Xóa các bên thứ ba cũ"><span class="">Xóa các bên thứ ba cũ</span></a>

									</li> -->
										</ul>
									</div>
								</div>
							</div>				
						</div>
						
					</div>
				</div>

				</div>


</div>


<!-- TABLE -->
<?php 
$six_months_ago = date('Y-m-d', strtotime('-6 months'));
$today = date('Y-m-d').'%2023:59:59';?>
<div class="row panel">
	<div class="col-md-12 panel panel-piluku">
	<div class="col-md-12 panel-body nopadding table_holder table-responsive">
	<span><h4>Danh sách bên thứ 3 (<?php echo $list; ?>)</h4></span>
	<table class="tablesorter table table-hover" id="table_d13">
		<thead>
			<tr>
				<th><input type="checkbox" id="select_all"><label for="select_all"><span></span></label></th>
				<th class="sort" order="person_id">Mã bên thứ ba</th>
				<th class="sort" order="company_name">Tên bên thứ ba</th>
				<th class="sort" order="name">Loại đơn vị</th>
				<th class="sort" order="head">Người đầu mối</th>
				<th class="sort" order="email">Email</th>
				<th class="sort" order="phone_number">Số điện thoại</th>
				<th class="sort" order="total">Tổng chi phí cho bên thứ 3</th>
				<th>Cập nhật</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($suppliers as $supplier){ ?>
			<tr style="cursor: pointer;">
				<td class="cb"><input type="checkbox" id="person_<?php echo $supplier['person_id'] ?>" value="<?php echo $supplier['person_id'] ?>"><label for="person_<?php echo $supplier['person_id'] ?>"><span></span></label></td>
				<td><?php echo $supplier['person_id'] ?></td>
				<td><a href="<?php echo base_url('reports/supplier/'.$supplier['person_id']) ?>"><?php echo $supplier['company_name'] ?></a></td>
				<td><?php echo $supplier['name'] ?></td>
				<td><?php echo $supplier['head'] ?></td>
				<td><a href="mailto:<?php echo $supplier['email'] ?>"><?php echo $supplier['email'] ?></a></td>
				<td><?php echo $supplier['phone_number'] ?></td>
				<td class="total"><?php echo to_currency($supplier['total']) ?></td>
				<td><a href="<?php echo base_url('suppliers/view/'.$supplier['person_id']) ?>" class=" ">Sửa</a></td>
			</tr><?php } ?>
		</tbody>
	</table>


	<div style="text-align: center;">
		<?php echo $pagination ?>
	</div>

	</div>
</div>
</div>




<!-- AJAX -->


<script>
	$(document).ready(function(){

var order = "person_id", order_by = "asc";
	$(document).on('click','.pagi a',function(event){
		$('.del_all').addClass('hidden');
			event.preventDefault();	

			$.ajax({
				type:'post',
				url:"<?php echo base_url('suppliers/sorting_d13')?>"+'/'+ $(this).attr('data-ci-pagination-page'),
				dataType:'text',
				data: {
					unit_type: $('.unit_type').val(),
					search: $('.search').val(),
					order:order,
					order_by:order_by,
				},
				success: function(result){
					$('.table-responsive').html(result);
					$('.del_all').addClass('hidden');
				},
			});
			
		});

	// LỌC

	$('.filter').change(function(event){
		$('.del_all').addClass('hidden');
			event.preventDefault();	
			$.ajax({
				type:'post',
				url:"<?php echo base_url('suppliers/sorting_d13')?>"+'/'+ $(this).attr('data-ci-pagination-page'),
				dataType:'text',
				data: {
					unit_type: $('.unit_type').val(),
					search: $('.search').val(),
					order:order,
					order_by:order_by,
				},
				success: function(result){
					$('.table-responsive').html(result);
				},
			});
			
		});


// SORT


	$(document).on('click','.sort',function(event){
		$('.del_all').addClass('hidden');
			order = $(this).attr('order');
			order_by = (order_by =="desc") ? "asc" :"desc";
			$.ajax({
				type:'post',
				url:"<?php echo base_url('suppliers/sorting_d13')?>"+'/'+ $(this).attr('data-ci-pagination-page'),
				dataType:'text',
				data: {
					unit_type: $('.unit_type').val(),
					search: $('.search').val(),
					order:order,
					order_by:order_by,
				},
				success: function(result){
					$('.table-responsive').html(result);
				},
			});
			
		});



// CHECK BOX

	$(document).on('click','#select_all',function()
	{
		if($(this).prop('checked'))
		{	
			$('.del_all').removeClass('hidden');
			$("#table_d13 tbody :checkbox").each(function()
			{
				$(this).prop('checked',true);
				$(this).parent().parent().find("td").addClass('selected').css("backgroundColor","");

			});
		}
		else
		{
			$('.del_all').addClass('hidden');
			$("#table_d13 tbody :checkbox").each(function()
			{
				$(this).prop('checked',false);
				$(this).parent().parent().find("td").removeClass('selected');				
			});    	
		}
	 });



// ĐẾM SỐ CHECK BOX

	$(document).on('click','.tablesorter label',function(){
		var checked =  $('.tablesorter input:checkbox:checked').length;
		
		if($(this).parent().find('input').prop('checked'))
		{
			checked--;
		}
		else
		{
			checked++;
		}
		if(checked>0)
		{
			$('.del_all').removeClass('hidden');
		}
		else
		{
			$('.del_all').addClass('hidden');
		}
	});




// CONFIRM

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
						url: '<?php echo site_url("suppliers/deletes");?>',
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




<!-- MODAL -->


<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- MODAL -->


<style>
	.sort {
		cursor: pointer;
	}
</style>
			
<?php $this->load->view("partial/footer"); ?>

