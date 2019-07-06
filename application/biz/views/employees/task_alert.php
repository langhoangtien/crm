<?php $this->load->view("partial/header");?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>
<div class="row">
	<div class="col-md-12">
		<table class="table" id="tableDG">
	<thead>
		<tr>
			<th>STT</th>
			<th>Dự án</th>
			<th>Trạng thái</th>
			<th>Ngày kết thúc</th>
		</tr>
	</thead>
	<tbody>
		<?php $i=0; foreach ($list as $key => $value) { $i++ ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><a href="javascript:;" onclick="edit_task_grid(<?php echo $value['id'] ?>);"><?php echo $value['name']; ?></a></td>
				<td><?php echo $value['trangthai']; ?></td>
				<td><?php echo $value['date_end']; ?></td>
			</tr>
		<?php } ?>
	</tbody>

</table>
	</div>



</div>


<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<script>

	  var datatableOption = {
                            "bFilter": false,
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
	$('#tableDG').DataTable(datatableOption);
</script>
<?php $this->load->view("partial/footer");?>