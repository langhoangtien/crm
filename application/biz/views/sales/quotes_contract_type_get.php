<select id="select_quote" name="select_quote" class="form-control form-inps">
	<option value="0">-- <?php echo lang('sale_select_quote'); ?> --</option>
<?php 
	if(!empty($areas)) {

		foreach($areas as $val) {
?>
	<option value="<?php echo $val->id_quotes_contract; ?>"><?php echo $val->title_quotes_contract; ?></option>
<?php 
		}
	}
?>
</select>