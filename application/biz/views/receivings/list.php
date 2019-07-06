<?php $this->load->view("partial/header"); ?>

<div class="container">

	<div class="pull-left">


	</div>

	<div class="pull-right">
		<div class="buttons-list">
			<div class="pull-right-btn">																
				<a href="<?php echo base_url('receivings') ?>" class="btn btn-primary" title="Mới"><span class="">Thêm mới</span></a>							
			</div>
		</div>				
	</div>
</div>
<div class="container-fluid">
<div class="table-responsive">
	<table class="table tablesorter table-reports table-bordered display table-hover" id="dTableR">
		<thead>
			<tr>	
				<td>STT</td>
				<td>Tên bên thứ ba</td>
				<td>Tên dịch vụ</td>
				<td>Chi phí</td>
				<td>Tên dự án</td>
				<!-- <td>Chi tiết</td> -->
			</tr>
		</thead>
		<tbody>
			<?php $i=1; foreach ($list as $key => $value) {  ?>
				<tr>
					<td><?php echo $i; $i++; ?></td>
					<td><?php echo $value['company_name'] ?></td>
					<td><ul>
						<?php  foreach ($value['name']  as $key2 => $value2) {
							?>
							<li><?php echo $value2 ?></li>
						<?php  }?>
					</ul></td>
					<td style="text-align: right;cursor: pointer;"><ul>
						<?php  foreach ($value['item_unit_price']  as $key3 => $value3) {
							?>
							<li><span receiving="<?php echo $value['id'] ?>"class="edit_receiving" line=<?php echo $value['line'][$key3] ?> data-toggle="modal" data-target="#receiving_edit" price="<?php echo number_format($value3) ?>"><?php echo number_format($value3) ?>	</span></li>
						<?php } ?>
					</ul></td>
					<td><?php echo $value['task_name'] ?></td>
					<!-- <td><button class="btn btn-primary">Xóa</button></td> -->
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
</div>


<div id="receiving_edit" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="z-index: 9999050; display: none;" aria-hidden="true"><div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true" class="x-close">×</span></button>
				<h4 class="modal-title"> Sửa chi phí</h4>
			</div>


			<div class="modal-body">
				<div class="form-group">
					<label for="">Chi phí: </label>
					<input class="price form-control" type="text">
					<input class="line" type="hidden" value="">
					<input type="hidden" class="receiving_id">
				</div>
			</div>
			<div class="modal-footer"><button class="btn btn-primary save">Lưu</button></div>
		</div>
	</div>

</div>
<script>

	$('#dTableR').DataTable(datatableOption);
	$(document).on('click','.edit_receiving',function(){							
		$('.price').val($(this).attr('price'));
		$('.line').val($(this).attr('line'));
		$('.receiving_id').val($(this).attr('receiving'));
					// $('.price').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999'});

				});


	$('.price').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999'});

	$('.save').click(function(){

		var line = $('.line').val();
		var price = $('.price').val();
		var id = $('.receiving_id').val();

		$.ajax({
			type:"POST",
			url:BASE_URL+'/receivings/edit_r',
			dataType:"json",
			data:{
				line:line,
				price:price,
				id:id
			},
			success:function(result){
				if(result.flag)
				{
					toastr.success("Cập nhật thành công","Thông báo");
					$('#receiving_edit').toggle();
					window.location.reload();
				}
			}

		});
	});
</script>

<style>
.edit_receiving{
	color:#3b79af;
}
</style>
<?php $this->load->view("partial/footer"); ?>