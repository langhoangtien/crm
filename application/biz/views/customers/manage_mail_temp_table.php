
<?php if($results != null):?>

<?php foreach($results as $result):?>
	<tr>				
		<td>
			<input type="checkbox" name="customer_<?php echo $result['mail_id'];?>" id="customer_<?php echo $result['mail_id'];?>" value="<?php echo $result['mail_id'];?>">
			<label for="customer_<?php echo $result['mail_id'];?>"><span></span></label>		
		</td>
		<td>
			<?php echo $result['mail_id'] ;?>
		</td>
		<td>
			<?php echo $result['mail_title'];?>
		</td>
		<td>
			<a href="javascript:;" onclick="edit_mail_template(<?php echo $result['mail_id']; ?>)"><?php echo lang('common_edit'); ?></a>
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
