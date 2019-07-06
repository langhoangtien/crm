<?php $this->load->view("partial/header");?>
<div class="row">
	<div class="col-md-12">
		<table class="table">
	<thead>
		<tr>
			<th>STT</th>
			<th>Tên hợp đồng</th>
			<th>Trạng thái mới</th>
			<th>Người thay đổi</th>
			<th>Thời gian</th>
			<th>Lý do</th>
		</tr>
	</thead>
	<tbody>
		<?php $i=0; foreach ($list as $key => $value) { $i++ ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $value['name']; ?></td>
				<td><?php echo $value['status']; ?></td>
				<td><?php echo $value['username']; ?></td>
				<td><?php echo date("d-m-Y H:i:s",strtotime($value['time'])); ?></td>
				<td><?php echo $value['type']; ?></td>
			</tr>
		<?php } ?>
	</tbody>

</table>
	</div>


	<div class="col-md-2 col-md-offset-5"><?php echo $pagination ?></div>

</div>

<?php $this->load->view("partial/footer");?>
