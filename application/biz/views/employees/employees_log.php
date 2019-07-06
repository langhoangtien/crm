<?php $this->load->view("partial/header");?>
<div class="row">
	<div class="col-md-12">
		<table class="table">
	<thead>
		<tr>
			<th>STT</th>
			<th>Tài khoản</th>
			<th>Thời gian</th>
			<th>Hành động</th>
			<th>IP</th>
		</tr>
	</thead>
	<tbody>
		<?php $i=($this->uri->segment(3,1)-1)*10+1; foreach ($list as $key => $value) {  ?>
			<tr>
				<td><?php echo $i; $i++?></td>
				<td><?php echo $value['username']; ?></td>
				<td><?php echo $value['time']; ?></td>
				<td><?php echo $value['log_type']; ?></td>
				<td><?php echo $value['ip']; ?></td>
			</tr>
		<?php } ?>
	</tbody>

</table>
	</div>


	<div class="col-md-4 col-md-offset-5"><?php echo $pagination ?></div>

</div>

<?php $this->load->view("partial/footer");?>
