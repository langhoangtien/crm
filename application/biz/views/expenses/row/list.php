<?php
if(!empty($items)) {
	// var_dump($items);die;
	$i = $STT+1;
	// echo "<pre>";
	// print_r($items);
	foreach($items as $val) {
		$expense_id					= $val['id'];
		$expense_type               = $val['expense_type'];
		// $expense_options               = $val['expense_options'];
		$expense_description        = $val['expense_description'];
		$category              		= $val['category'];
		$expense_date       		= $val['expense_date'];
		$expense_amount          	= $val['expense_amount'];
		$expense_tax            	= $val['expense_tax'];
		$employee_recv            	= $val['employee_recv'];
		$employee_appr              = $val['employee_appr'];

		if ($val['expense_type'] == 1) {
			$expense_type = 'Chi';
		} else {
			$expense_type = 'Thu';
		}
		?>
		<tr>
			<td class="cb"><input type="checkbox" id="expense_<?php echo $expense_id; ?>" value="<?php echo $expense_id; ?>" class="file_checkbox"><label><span></span></label></td>
			<td class="text-left"><?php echo $i;?></td> 
			<!-- <td class="text-left"><?php echo $expense_type; ?></td> -->
			<td class="text-left" width="20%;"><?php echo $expense_description; ?></td>
			<!-- <td class="text-left"><?php //echo $category; ?></td> -->
			<td class="text-left"><?php echo date('d/m/Y', strtotime($expense_date)); ?></td>
			<td class="text-left"><?php echo to_currency($expense_amount); ?></td>
			<!-- <td class="text-left"><?php //echo to_currency($expense_tax); ?></td> -->
			<td class="text-left"><?php echo (!empty($val['first_name_recv'])?$val['first_name_recv']:$val['last_name_recv']); ?></td>
			<td class="text-left"><?php echo (!empty($val['first_name_appr'])?$val['first_name_appr']:$val['last_name_appr']); ?></td>
			<!-- <td class="text-left not-selectable">
			<a href="<?php //echo base_url(); ?>/expenses/reprint/<?php //echo $expense_id; ?>/" class="update-person" title="Reprint"><?php //echo lang('expenses_print'); ?> / </a>
				<a href="<?php //echo base_url(); ?>/expenses/export_excel/<?php //echo $expense_id; ?>/" class="update-person" title="Export">  <?php //echo lang('expenses_excel_output'); ?></a>
			</td> -->
			<td class="text-left not-selectable"><a href="<?php echo base_url(); ?>/expenses/view/<?php echo $expense_id; ?>/2" class="update-person" title="Sửa"> <?php echo lang('expenses_update'); ?></a></td>
		</tr>
		
		<?php
			$i++;
	}
}else {
	?>
	<tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
	<?php
}
?>
<style>
	.inline {
		display: inline;
	}
	.link-button:hover
	{
		color: #23527c;
	}
	.link-button {
		background: none;
		border: none;
		color: blue;
		text-decoration: none;
		cursor: pointer;
		font-family: Arial;
		font-size: 14px;
		letter-spacing: normal;
		color: #337ab7;
	}
	.link-button:focus {
		outline: none;
	}
	.link-button:active {
		color:red;
	}
</style>
