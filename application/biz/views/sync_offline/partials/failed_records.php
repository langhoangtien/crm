<style type="text/css">
.customer-recent-sales .modal-body table tr th {
	border: 1px solid #d7dce5;
}
</style>


<table class="table table-bordered table-striped table-hover data-table"
	id="dTableA">
	<thead>
		<th>#ID</th>
		<th><?php echo 'Thời gian bán hàng'; ?></th>
		<th><?php echo 'Thanh toán'; ?></th>
	</thead>
	<tbody>
          <?php foreach ($failedRecords as $index => $record) { ?>
            <tr>
			<td><?php echo $record['sale']['sale_id'];?></td>
			<td><?php echo $record['sale']['sale_time'];?></td>
			<td><?php echo $record['sale']['payment_type'];?></td>
		</tr>
          <?php } ?>
          </tbody>
</table>

<script>
	$(document).ready(function(){
		$('#dTableA').DataTable({
			"sPaginationType": "bootstrap",
			"bFilter": false,
			"bInfo": false,
			"iDisplayStart ": 10,
		    "iDisplayLength": 10,
		    "bLengthChange": false
		});
	});
</script>
