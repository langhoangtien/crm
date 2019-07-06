<span><h4>Danh sách bên thứ 3 (<?php echo $list; ?>)</h4></span>
	<table class="tablesorter table table-hover" id="table_d13">
		<thead>
			<tr>
				<th><input type="checkbox" id="select_all"><label for="select_all"><span></span></label></th>
				<th class="sort" order="person_id">Mã bên thư ba</th>
				<th class="sort" order="company_name">Tên bên thứ ba</th>
				<th class="sort" order="name">Loại đơn vị</th>
				<th class="sort" order="head">Người đầu mối</th>
				<th class="sort" order="email">Email</th>
				<th class="sort" order="phone_number">Số điện thoại</th>
				<th class="sort" order="total">Tổng chi phí cho bên thứ 3</th>
				<th>Cập nhật</th>
			</tr>
		</thead>
		<tbody><?php $day = date('Y-m-d 23:59:59'); $lday = date('Y-m-d',strtotime('-6 month')) ?>
			<?php foreach($suppliers as $supplier){ ?>
			<tr style="cursor: pointer;">
				<td class="cb"><input type="checkbox" id="person_<?php echo $supplier['person_id'] ?>" value="<?php echo $supplier['person_id'] ?>"><label for="person_<?php echo $supplier['person_id'] ?>"><span></span></label></td>
				<td><?php echo $supplier['person_id'] ?></td>
				<td><a href="<?php echo base_url('reports/supplier/'.$supplier['person_id']) ?>"><?php echo $supplier['company_name'] ?></a></td>
				<td><?php echo $supplier['name'] ?></td>
				<td><?php echo $supplier['head'] ?></td>
				<td><a href="mailto:<?php echo $supplier['email'] ?>"><?php echo $supplier['email'] ?></a></td>
				<td><?php echo $supplier['phone_number'] ?></td>
				<td class="total"><?php echo to_currency($supplier['total']) ?></td>
				<td><a href="<?php echo base_url('suppliers/view/'.$supplier['person_id']) ?>" class=" ">Sửa</a></td>
			</tr><?php } ?>
		</tbody>
	</table>


	<div style="text-align: center;">
		<?php echo $pagination ?>
	</div>