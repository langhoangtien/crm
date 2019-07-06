<?php $this->load->view('partial/header'); ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>	
	<div class="container">
		<table id="tableIz" class="table">
			<thead>
				<tr>
					<th>STT</th>
					<th>Tên công việc</th>
					<th>Tên giai đoạn</th>
					<th>Tên hợp đồng</th>
					<th>Ngày kết thúc dự kiến</th>
					<th>Ngày hoàn thành</th>
				</tr>
			</thead>
			<tbody>
				<?php $i=1; foreach ($list as $key => $value) { ?>
				<tr>
					<th><?php echo $i;$i++ ?></th>
					<td><a href="javascript:;" onclick="edit_task_grid(<?php echo $value['task_id'] ?>);"><?php echo $value['task_name']; ?></a></td>
					<td><?php echo $value['contract_payment_name'] ?></td>
					<td><a href="<?php echo base_url('contracts/view/customer/'.$value['contract_id']) ?>"><?php echo $value['contract_name'] ?></a></td>
					<td><?php echo $value['date_end'] ?></td>
					<td><?php echo $value['date_finish'] = $value['date_finish'] == "00-00-0000" ? "" : $value['date_finish'] ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

	<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

   </div>
	<script>
			$('#tableIz').DataTable(datatableOption);
	</script>
<?php $this->load->view('partial/footer'); ?>
