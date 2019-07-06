		<thead>
			<tr>
					<th rowspan="4">STT</th>
					<th rowspan="4" data-field="sale_time" class="header headerSortDown">code</th>
					<th rowspan="4">Ngày</th>
                    <th rowspan="4">Danh thu</th>
                    <th rowspan="4">Lợi nhuận trước hoa hồng</th>
			</tr>
			<tr>		
					<th colspan ="<?php echo $colspan_comission?>" ><strong><?php echo lang('reports_summary_commission')?></strong></th>		
			</tr>
			
			<tr>
			<?php foreach($count_comission_in_group as $_group_id => $value):?>
					<th colspan ="<?php echo $value?>" ><strong><?php echo $group_list[$_group_id]['name']?></strong></th>
			<?php endforeach;?>
			
					<th colspan ="<?php echo $count_colspan_commission_by_employee?>" ><strong><?php echo lang('reports_summary_commission_by_employee')?></strong></th>
			</tr>
			
			<tr>
				<?php foreach($group as $_group_id => $_employee_ids):?>
				<?php foreach($_employee_ids as $_employee_id):?>
						<th><?php echo $employee_list[$_employee_id]['first_name'] . ' ' . $employee_list[$_employee_id]['last_name']?></th>
				<?php endforeach;?>
				<?php endforeach;?>
				<?php foreach($employee_ids as $_employee_id):?>
					<th><?php echo $employee_list[$_employee_id]['first_name'] . ' ' . $employee_list[$_employee_id]['last_name']?></th>
				<?php endforeach;?>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		
			<?php foreach($items as $key => $val): ?>
			<tr>
		        <td><?php echo $STT ?></td>
                <td><?php echo $val['code']?></td>
                <td><?php echo explode(' ', $val['sale_time_format'])[0]; $STT++?></td>
                <td><?php echo to_currency($val['income'])?></td>
                <td><?php echo to_currency($val['profit_before_charging_commission'])?></td>	
			<?php foreach($group as $group_id => $emp_ids): ?>
			<?php foreach($emp_ids as $emp_id): ?>
			
            <td><?php echo isset($sale_commission[$val['sale_id'].'-'.$group_id.'-'.$emp_id]) ? to_currency($sale_commission[$val['sale_id'].'-'.$group_id.'-'.$emp_id]) : 0; ?></td>

      <?php endforeach;?>	
			<?php endforeach;?>	
		
			<?php $total_commission_employee  = 0;?>
			<?php foreach($employee_ids as $key => $employee_id): ?>
						<?php $total_commission_employee  += isset($sale_commission_by_employee[$val['sale_id']. '-' . $employee_id]) ? $sale_commission_by_employee[$val['sale_id'] . '-' . $employee_id] : 0;?>
			      <td><?php echo isset($sale_commission_by_employee[$val['sale_id']. '-' . $employee_id]) ?  to_currency($sale_commission_by_employee[$val['sale_id'] . '-' . $employee_id]) : 0;?></td>
			<?php endforeach;?>
			      <td><?php echo  to_currency($total_commission_employee) ?></td>
					
			
			</tr>
			<?php endforeach;?>	
			


         
		</tbody>
	