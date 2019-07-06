<div id="filter-block" style="margin-top: 20px;"></div>
<table class="table table-bordered table-striped table-hover data-table" id="listView">
	<thead>
		<tr>
			<?php foreach ($cols as $col) { ?>
			<th><?php echo $col['label'] ?></th>
			<?php } ?>
			<th>Trạng thái phê duyệt</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($tblRows as $row) { ?>
		<tr data_row_id="<?php echo $row['id'];?>">
		    <input type="hidden" name="object_id" value="<?php echo $row['object_id'];?>" />
		    <input type="hidden" name="step_code" value="<?php echo $row['step_code'];?>" />
			<?php foreach ($cols as $col) { ?>
			<td><?php echo $row[$col['field']] ?></td>
			<?php } ?>
			<td>
				<?php if ($row['approved'] == 1) { ?>
    				<i class="icon-success ion-checkmark-circled"></i> <span>Đã phê duyệt</span>
    			<?php } elseif ($row['approved'] == -1) { ?>
    				<i class="icon-error ion-close-circled"></i> <span>Không phê duyệt</span>
    			<?php } else { ?>
    				<span>Chờ phê duyệt</span>
    			<?php }?> 
			</td>
			<td>
				<button type="button" class="btn btn-default btn-approve-statues">D/S phê duyệt</button>
			</td>
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
			"sPaginationType": "bootstrap",
			initComplete: function () {
	            this.api().column(1).every( function () {
	                var column = this;
	                var select = $('<select id="filter-subject" name="step" style="width: 200px; padding: 5px;"><option value="">--- Tất cả ---</option></select>')
	                    .appendTo( $('#filter-block') )
	                    .on( 'change', function () {
	                        var val = $.fn.dataTable.util.escapeRegex(
	                            $(this).val()
	                        );
	                        
	                        column
	                            .search( val ? '^'+val+'$' : '', true, false )
	                            .draw();
	                    } );
	 
	                column.data().unique().sort().each( function ( d, j ) {
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } );
	            } );
	        },
		});
	},
}

$( document ).ready(function() {
	LIST_VIEW.init();

	$('#listView tbody').on('click', 'td .btn-approve-statues', function(){
		var _data = {};
		_data['step_code'] = $(this).closest('tr').find('input[name="step_code"]').val();
		_data['obj_id'] = $(this).closest('tr').find('input[name="object_id"]').val();		
		coreAjax.call(
			'approver_groups/view_approve_statuses',
			_data,
			function(response)
			{
				if(response.success)
				{
					$('#approveStatusesModal').remove();
					$('body').append(response.html);
					$('#approveStatusesModal').modal('show');
				}
			}
		);
	});
});
</script>