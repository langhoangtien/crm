
<?php if($results != null):?>

<?php foreach($results as $result):?>
<tr>				
	<td>
		<input type="checkbox" name="customer_<?php echo $result['id'];?>" id="customer_<?php echo $result['id'];?>" value="<?php echo $result['id'];?>">
		<label for="customer_<?php echo $result['id'];?>"><span></span></label>		
	</td>
	<td>
		<?php echo $result['id'] ;?>
	</td>
	<td>
		<?php echo $result['title'];?>
	</td>
	<td>
	</td>
	<td>
	<a href="javascript:;" onclick="edit_sms_template(<?php echo $result['id']; ?>)"><?php echo lang('common_edit'); ?></a>
	</td>
</tr>
<?php endforeach;?>
<?php else:?>
<tr>
<td  colspan="16">
	<span class="col-md-12 text-center text-warning"><?php echo lang('common_no_persons_to_display');?></span>
</td>
</tr>
<?php endif;?>
