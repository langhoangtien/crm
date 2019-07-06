<style type="text/css">
#listView .selected {
	background-color: #ccc !important;
}
</style>

<table class="transfer_pending table table-bordered table-striped table-hover data-table" id="listView">
	<thead>
		<tr>
			<?php foreach ($cols as $col) { ?>
			<th><?php echo $col['label'] ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($tblRows as $row) { ?>
		<tr data-id="<?php echo $row['id'];?>">
			<?php foreach ($cols as $col) { ?>
			<td><?php echo $row[$col['field']] ?></td>
			<?php } ?>
		</tr>
		<?php } ?>
	</tbody>
</table>

<script type="text/javascript">
var LIST_VIEW = {
	_datatable : null,
	init: function()
	{
		LIST_VIEW.initDataTable();
	},
	initDataTable: function()
	{
		LIST_VIEW._datatable = $('#listView').DataTable({
			"sPaginationType": "bootstrap"
		});

		$('#listView tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('selected') ) {
	            $(this).removeClass('selected');
	        }
	        else {
	            LIST_VIEW._datatable.$('tr.selected').removeClass('selected');
	            $(this).addClass('selected');
	        }
	        if ( $(this).hasClass('selected') ) {
	        	$('#delete_approver_group, #edit_approver_group').show();
	        } else {
	        	$('#delete_approver_group, #edit_approver_group').hide();
	        }
	    } );
	},
}

$( document ).ready(function() {
	LIST_VIEW.init();
	$('#delete_approver_group, #edit_approver_group').hide();
});
</script>