<div class="form-group form-md-line-input" id="f_receiving_id">
	<!-- <input type="text" value="<?php //if(isset($ma_don_nhap_hang)) echo $ma_don_nhap_hang; else echo ''; ?>" class="form-control" id="receiving_code" readonly="true">
    <input type="hidden" value="<?php  //if(isset($receiving_id)) echo $receiving_id; ?>" name="receiving_id" id="receiving_id" /> -->
	<label style="color: #e7505a;" for="form_control_1">Hợp đồng</label>

	<!-- <span class="input-group-addon" onclick="load_receiving_modal();">Chọn hóa đơn <i class="ion-clipboard"></i></span>
	<span for="f_receiving_id" id="email2-error" class="help-block help-block-error has-error"></span> -->
	<select class="form-control" name="contract_id" id="contract_id">
		<option value="-1">Chọn hợp đồng</option>
		<?php
		foreach ($row_constant as $k => $val): ?>
			<option value="<?php echo $val['id'] ?>" <?php if($contract_id==$val['id']) echo "selected" ?>><?php echo $val['name'];?></option>
		<?php endforeach; ?>
	</select>
	<span for="f_expenses_options" id="email2-error" class="help-block help-block-error has-error"></span>
</div>