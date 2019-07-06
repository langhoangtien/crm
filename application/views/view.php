<?php $this->load->view("partial/header"); ?>
<div class="container">
	<div class="row">
		<table class="table">
			<thead>
				<tr>
					<th>Menu id</th>
					<th>Danh mục chức năng</th>
					<?php foreach ($list as $key => $value) {?>
						<th><?php echo $value['username'] ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($list as $key => $value) {?>
					<tr></tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>