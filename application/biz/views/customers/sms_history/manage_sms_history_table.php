
<?php if($results != null):?>

<?php $STT = $STT+1; foreach($results as $result):?>
	<tr>				
		<td>
			<input type="checkbox" name="smshistory_<?php echo $result['id'];?>" id="smshistory_<?php echo $result['id'];?>" value="<?php echo $result['id'];?>">
			<label for="smshistory_<?php echo $result['id'];?>"><span></span></label>		
		</td>
		<td>
			<?php echo $STT ;?>
		</td>
		<td>
			<?php echo $result['last_name'] .' '.$result['first_name'];?>
		</td>
		<td>
			
		</td>
		<td>
			<?php echo $result['title'];?>
		</td>
		<td>
			<?php echo $result['content'];?>
		</td>
		<td>
			<?php echo $result['time'];?>
		</td>
		<td>
			<?php echo $result['status_'];?>
		</td>
	</tr>
<?php $STT++; endforeach;?>
<?php else:?>
	<tr>
		<td  colspan="16">
			<span class="col-md-12 text-center text-warning"><?php echo lang('common_no_persons_to_display');?></span>
		</td>
	</tr>
	<?php endif;?>
