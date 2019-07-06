<?php $this->load->view("partial/header");?>
<div class="row">
	<div class="col-md-12">
		<table class="table" id="tableU">
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
	$('#tableU').DataTable(datatableOption);
</script>
<?php $this->load->view("partial/footer");?>