<?php $this->load->view("partial/header"); ?>
<link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/personal.js" ></script>
<div class="container">
	
		<div class="col-md-12">
			<table class="table" id="tableKO">
				<thead>
					<tr>
						<th>STT</th>
						<th>Tên công việc</th>
						<th>Người phụ trách</th>
						<th>Người tham gia</th>
						<th>Người phê duyệt</th>
						<th>Người thay đổi</th>
						<th>Thời gian</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=1; foreach ($list as $key => $value) { ?>
					<tr>
						<td><?php echo $i; $i++ ?></td>
						<td><a href="javascript:;" onclick="update_personal_task('edit', 'norevenue', <?php echo $value['task_id'] ?>)"><?php echo $value['name']; ?></a></td>
						<td><?php echo $value['implements']; ?></td>
						<td><?php echo $value['joins']; ?></td>
						<td><?php echo $value['approved']; ?></td>
						<td><?php echo $value['username']; ?></td>
						<td><?php echo $value['time']; ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

 <div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

    </div>

    <script>
    	var datatableOption = {
                            // "bFilter": false,
                            "bInfo": false,
                            "iDisplayStart ": 10,
                            "iDisplayLength": 10,
                            "bLengthChange": false,
                            "lengthChange": false,
                            "pageLength": 20,
                            "language": {
                            	"paginate": {
                            		"first":      "First",
                            		"last":       "Last",
                            		"next":       "&gt;",
                            		"previous":   "&lt",
                            		"class":"vi"
                            	},
                            	"search":         "Tìm kiếm:",
                            },
                        };

                        $('#tableKO').DataTable(datatableOption);
    </script>
<?php $this->load->view("partial/footer"); ?>