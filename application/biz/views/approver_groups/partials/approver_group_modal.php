<style type="text/css">
.modal-dialog .modal-body table tr th {
	border: 1px solid #d7dce5;
}

#list_selected, #list_available {
	min-height: 20px;
	list-style-type: none;
	padding: 5px 0 0 0;
	overflow-y: auto;
	height: 290px;
}

#list_selected li, #list_available li {
	margin: 5px 0;
	padding: 5px;
}
</style>
<!-- Modal -->
<div class="modal fade" id="approverGroupModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content" style="height: 600px;">
			<input type="hidden" name="approver_group_id" value="<?php echo $approver_group_id; ?>"/>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
				<h4 class="modal-title">Thông tin nhóm phê duyệt</h4>
			</div>
			<div class="modal-body form-horizontal">
				<div class="form-group">
					<label for="group_name"
						class="col-sm-3 col-md-3 col-lg-2 control-label">Tên nhóm:</label>
					<div class="col-sm-9 col-md-9 col-lg-10 input-field">
						<input type="text" name="group_name" value="<?php echo !empty($groupInfo) ? $groupInfo['name'] : '';?>" class=""
							style="width: 100%" id="group_name">
					</div>
				</div>
				<div class="form-group">
					<label for="group_code"
						class="col-sm-3 col-md-3 col-lg-2 control-label">Công đoạn:</label>
					<div class="col-sm-9 col-md-9 col-lg-10 input-field">
					<?php 
					$disabled = $isCreate ? '' : 'disabled';
					?>
							<select name="step_code" class="form-control" id="step_code" style="width: 100%" <?php echo $disabled; ?>>
                                <option value="">----- Lựa chọn công đoạn</option>
                                <?php foreach ($steps as $step) { 
                                $selected = (!empty($groupInfo) && $step['code'] == $groupInfo['code']) ? 'selected' : '';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $step['code']?>"><?php echo $step['label']?></option>
                                <?php } ?>
                            </select>
					</div>
				</div>
				<div class="form-group">
					<?php echo form_label('Active :', 'approver_group_active',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_checkbox(array(
						'name'=>'approver_group_active',
						'id'=>'approver_group_active',
						'value'=>'active',
						'checked' => (bool) $groupInfo['active']));?>
						<label for="approver_group_active"><span></span></label>
					</div>
				</div>
				<div>
					<div class="col-md-6">
						<div style="text-align: center;">
							<label>D/S nhân viên</label>
						</div>
						<div style="position: relative;">
							<input type="text" name="q" style="width: 100%"/>
							<span id="search-icon" style="position: absolute; top:8px; right:10px; cursor: pointer" class="glyphicon glyphicon-search" aria-hidden="true"></span>
						</div>
						<ul id="list_available" class="connectedSortable">
						<?php foreach ($availableEmployees as $employee) { ?>
							<li data-id="<?php echo $employee['id']; ?>" class="ui-state-default"><?php echo $employee['first_name'] . ' ' . $employee['last_name']?></li>
						<?php } ?>
						</ul>
					</div>
					<div class="col-md-6">
						<div style="text-align: center;">
							<label>D/S nhân viên trong nhóm</label>
						</div>
						<ul id="list_selected" class="connectedSortable">
							<?php if(!empty($groupInfo['employees'])) {?>
    							<?php foreach ($groupInfo['employees'] as $employee) { ?>
        							<li data-id="<?php echo $employee['employee_id']; ?>" class="ui-state-default"><?php echo $employee['first_name'] . ' ' . $employee['last_name']?></li>
        						<?php } ?>							
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
				<button type="button" class="btn btn-default btn-save">Lưu</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready(function() {
	$( "#list_available, #list_selected" ).sortable({
		connectWith: ".connectedSortable",
		stop: function( event, ui ) {
			var i = 1;
			$('#list_selected li').each(function(){
				$(this).find('span.badge').remove();
				$(this).append('<span class="badge">'+ i +'</span>');
				i++;
			});
		}
	}).disableSelection();
	

	$('#search-icon').unbind('click').bind('click', function(){
		var _data = {};
		_data['search'] = $('input[name="q"]').val();
		coreAjax.call(
			'<?php echo site_url("employees/search_v1");?>',
			_data,
			function(response)
			{
				if(response.success && response.employees.length) {
					var employeeAvailableIds = [];
					$('#list_available li').each(function(){
						employeeAvailableIds.push(parseInt($(this).data('id')));
					});
					var employeeSelectedIds = [];
					$('#list_selected li').each(function(){
						employeeSelectedIds.push(parseInt($(this).data('id')));
					});
					var hasNewItem = false;

					var availableEmployees = [];
					response.employees.forEach(function(employee){
						if (employeeSelectedIds.indexOf(parseInt(employee.id)) == -1) {
							availableEmployees.push(employee);
						}
					});
					if (availableEmployees.length) {
						$('#list_available').html('');
					}
					availableEmployees.forEach(function(employee){
						$('#list_available').append('<li data-id="'+ employee.id +'" class="ui-state-default">'+ employee.first_name + ' ' + employee.last_name +'</li>');
					});
					$( "#list_available, #list_selected" ).sortable({
						connectWith: ".connectedSortable"
					}).disableSelection();
				}
			}
		);
	});
	
	$('input[name="q"]').on('keyup', function (e) {
	    if (e.keyCode == 13) {
	    	var _data = {};
			_data['search'] = $('input[name="q"]').val();
			coreAjax.call(
				'<?php echo site_url("employees/search_v1");?>',
				_data,
				function(response)
				{
					if(response.success && response.employees.length) {
						var employeeAvailableIds = [];
						$('#list_available li').each(function(){
							employeeAvailableIds.push(parseInt($(this).data('id')));
						});
						var employeeSelectedIds = [];
						$('#list_selected li').each(function(){
							employeeSelectedIds.push(parseInt($(this).data('id')));
						});
						var hasNewItem = false;

						var availableEmployees = [];
						response.employees.forEach(function(employee){
							if (employeeSelectedIds.indexOf(parseInt(employee.id)) == -1) {
								availableEmployees.push(employee);
							}
						});
						if (availableEmployees.length) {
							$('#list_available').html('');
						}
						availableEmployees.forEach(function(employee){
							$('#list_available').append('<li data-id="'+ employee.id +'" class="ui-state-default">'+ employee.first_name + ' ' + employee.last_name +'</li>');
						});
						$( "#list_available, #list_selected" ).sortable({
							connectWith: ".connectedSortable"
						}).disableSelection();
					}
				}
			);
	    }
	});
	
	$('#approverGroupModal .btn-save').unbind('click').bind('click', function(){
		var employeeIds = [];
		$('#list_selected li').each(function(){
			employeeIds.push($(this).data('id'));
		});
		var _data = {};
		_data['id'] = $('input[name="approver_group_id"]').val();
		_data['name'] = $('input[name="group_name"]').val();
		_data['code'] = $('select[name="step_code"]').val();
		_data['active'] = 0;
		if ($('input[name="approver_group_active"]').is(':checked'))
		{
			_data['active'] = 1;
		}
		_data['employee_ids'] = employeeIds;

		if (_data['name'].length && _data['code'].length && _data['employee_ids'].length) {
    		coreAjax.call(
    			'<?php echo site_url("approver_groups/save");?>',
    			_data,
    			function(response)
    			{
    				if(response.success)
    				{
    					$('#approverGroupModal').modal('hide');
    					$('#approverGroupModal').remove();
    					location.reload();
    				}
    			}
    		);
		} else {
			alert('Dữ liệu nhóm phê duyệt không hợp lệ!');
		}
	});
});
</script>
