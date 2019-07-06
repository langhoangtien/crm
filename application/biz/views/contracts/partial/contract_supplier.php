
<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_supplier" style="display: none;">

    <table class="table table-bordered table-striped data-n9-table" data-table="contract_supplier" data-url="http://localhost/lifetek-crm/contracts/contract_payment_store/" data-currentpage="1">
        <thead>
			<tr>
				<th>STT</th>
				<th>Tên bên thứ ba</th>
				<th>Chi phí</th>
				<th>Phí dự kiến</th>
			</tr>
		</thead>
		<tbody class="sp">
			<?php $i=1; if(is_array($suppliers)&&(!empty($suppliers))){ foreach ($suppliers as $key => $value) {if(!empty($value['company_name'])){
			 ?>
			 <tr>
			 	<td><?php echo $i++  ?></td>
			 	<td><?php echo $value['company_name'] ?></td>
			 	<td><?php echo number_format($value['item_unit_price'])?></td>
			 	<td><?php echo $value['cost_price_interval'] ?></td>
			 </tr>
			<?php }}} ?>
		</tbody>
    </table>


</div>



<style>
	.sp tr td {text-align: center}
</style>