<?php $this->load->view("partial/header");?>
<div class="row">
	<div class="col-md-12">
		<table class="table">
	<thead>
		<tr>
			<th>STT</th>
			<th>Tên công việc</th>
			<th>Trạng thái mới</th>
			<th>Tiến độ mới</th>
			<th>Người thay đổi</th>
			<th>Thời gian</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($list as $key => $value) { $i++ ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $value['name']; ?></td>
				<td><?php echo $value['trangthai']; ?></td>
				<td><?php echo $value['progress']; ?></td>
				<td><?php echo $value['username']; ?></td>
				<td><?php echo $value['time']; ?></td>
			</tr>
		<?php } ?>
	</tbody>

</table>
	</div>

	<div class="col-md-4"></div>
	<div class="col-md-4"><?php echo $pagination ?></div>

</div>

<?php $this->load->view("partial/footer");?>
