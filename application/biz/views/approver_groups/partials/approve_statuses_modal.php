<style type="text/css">
</style>
<!-- Modal -->
<div class="modal fade" id="approveStatusesModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<input type="hidden" name="selected_obj_id" value="<?php echo $obj_id; ?>" />
			<input type="hidden" name="step_code" value="<?php echo $step_code; ?>" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
				<h4 class="modal-title">Chi tiết trạng thái phê duyệt</h4>
			</div>
			<div class="modal-body form-horizontal">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Nhân viên</th>
							<th>Thứ tự phê duyệt</th>
							<th>Trạng thái</th>
							<th>Ý kiến</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($approve_statuses as $approve_statuse) { ?>
						<tr>
							<td><?php echo $approve_statuse['first_name'] . ' ' . $approve_statuse['last_name']; ?></td>
							<td><?php echo $approve_statuse['order']; ?></td>
							<td>
								<?php if ($approve_statuse['approved'] == 1) { ?>
									<i class="icon-success ion-checkmark-circled"></i> <span>Đã phê duyệt</span>
								<?php } elseif ($approve_statuse['approved'] == -1) { ?>
									<i class="icon-error ion-close-circled"></i> <span>Không phê duyệt</span>
								<?php } else { ?> 
									<span>Chờ phê duyệt</span>
								<?php } ?>
								
								
							</td>
							<td style="width: 250px;">
								<?php if ($user_info->id == $approve_statuse['employee_id'] && $approve_statuse['approved'] != 1) {?>
									<textarea name="comment" rows="3" style="width: 100%"><?php echo $approve_statuse['comment']; ?></textarea>
								<?php } else { ?>
									<?php echo $approve_statuse['comment']; ?>
								<?php } ?>
								
							</td>
							<td>
								<?php if ($user_info->id == $approve_statuse['employee_id'] && $approve_statuse['approved'] != 1) {?>
									<?php 
    								    $disabled = $approve_statuse['allow_approve'] ? '' : 'disabled';
    								?>
    								<div>
										<button type="button" class="btn btn-primary btn-approve" <?php echo $disabled; ?>><?php echo lang('common_approve'); ?></button>
									</div>
									<div style="margin-top: 3px;">
										<button type="button" class="btn btn-danger btn-disapprove" <?php echo $disabled; ?>>Không phê duyệt</button>
									</div>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready(function() {
	$('.btn-approve').unbind('click').bind('click', function(){
		var _data = {};
		_data['obj_id'] = $('input[name="selected_obj_id"]').val();
		_data['comment'] = $('textarea[name="comment"]').val();
		_data['step_code'] = $('input[name="step_code"]').val();
		coreAjax.call(
			'<?php echo site_url("approver_groups/approve");?>',
			_data,
			function(response)
			{
				if(response.success)
				{
					$('#approveStatusesModal').remove();
					location.reload();
				}
			}
		);
	});

	$('.btn-disapprove').unbind('click').bind('click', function(){
		var _data = {};
		_data['obj_id'] = $('input[name="selected_obj_id"]').val();
		_data['comment'] = $('textarea[name="comment"]').val();
		_data['step_code'] = $('input[name="step_code"]').val();
		coreAjax.call(
			'<?php echo site_url("approver_groups/disapprove");?>',
			_data,
			function(response)
			{
				if(response.success)
				{
					$('#approveStatusesModal').remove();
					location.reload();
				}
			}
		);
	});
});
</script>
