<table class="table table-hover tablesorter" id="table_d13">
		<thead>
    		<tr>
    			<th class="leftmost" style="width: 20px;">
    				<input type="checkbox" name="select_all" value="select_all">
    				<label id="select_all"><span></span></label>
    			</th>
    			<th class="text-left hr-lbl headerSort" order ="id">STT</th>
    			<th class="text-left hr-lbl headerSort" order ="location_name">Khu vực</th>
    			<th class="text-left hr-lbl headerSort" order ="first_name">Tên nhân sự</th>
    			<th class="text-left hr-lbl headerSort" order ="group_name">Chức vụ</th>
    			<th class="text-left hr-lbl headerSort" order ="rank">Ngạch</th>
    			<th class="text-left hr-lbl headerSort" order ="level">Cấp bậc</th>
    			<th class="text-left hr-lbl headerSort" order ="email">Email</th>
    			<th class="text-left hr-lbl headerSort" order ="phone_number">SĐT</th>
    			<th class="text-left hr-lbl headerSort" order ="hire_date">Thời gian vào làm việc tại VCB</th>
    			<th class="text-left hr-lbl">Cập nhật</th>
    		</tr>
    	</thead>
    	<tbody>
<?php $i=$offset +1; foreach ($employees as  $employee) { ?>

    			<tr>
					<td class="cb">
                        <input type="checkbox" name="ids[<?php echo $employee['id'] ?>]" value="<?php echo $employee['id'] ?>" id="item_<?php echo $employee['id'] ?>">
                     <label><span></span></label>
                    </td>
    				<td><?php echo $i; $i++ ?></td>
    				<td><?php echo $employee['location_name'] ?></td>
    				<td><a href="<?php echo base_url('reports/specific_employees_d13/'.$employee['employee_id']) ?>"><?php echo $employee['first_name'] ?></a></td>
    				<td><?php echo $employee['group_name'] ?></td>
    				<td><?php echo $employee['rank'] ?></td>
    				<td><?php echo $employee['level'] ?></td>
    				<td><?php echo $employee['email'] ?></td>
    				<td><?php echo $employee['phone_number'] ?></td>
    				<td><?php echo $employee['hire_date'] ?></td>
    				<td><a href="<?php echo base_url('employees/view/'.$employee['id']) ?>">Sửa</a></td>
    			</tr>
    			<?php } ?>

    	</tbody>
	</table>
	<!-- ppagionation -->


<div style="text-align: center;">
	<?php //echo $pagination ?>
</div>