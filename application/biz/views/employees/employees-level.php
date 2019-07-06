


<?php if(isset($level)){ foreach ($level as $value) {
							
	?>
	<tr>
		<td><input class="form-control" value="<?php echo $value['name'] ?>" type="text" name="level_name[]"></td>
		<td><input value="<?php echo $value['salary'] ?>" class="form-control level_input" name="level_salary[]" type="text"></td>
		<td class="del-level" ><button class="btn">Xóa</button></td>
		<td><input value="<?php echo $value['id'] ?>" class="form-control" name="level_id[]" type="hidden"></td>

	</tr>
<?php } }

elseif (isset($rank)) {
	foreach ($rank as  $value) {
	
 	
  ?>

<option value="<?php echo $value['id'] ?>"><?php echo $value['name']  ?></option>
<?php

}
}


?>