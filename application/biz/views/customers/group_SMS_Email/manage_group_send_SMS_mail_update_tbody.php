<?php if($type == 'main' && $results != null):?>

<?php $i = 1; foreach($results as $result):?>
	<tr>				
		<td>
			<input type="checkbox" name="customer_<?php echo $result['person_id'];?>" id="customer_<?php echo $result['person_id'];?>" value="<?php echo $result['person_id'];?>">
			<label for="customer_<?php echo $result['person_id'];?>"><span></span></label>		
		</td>
		<td>
			<?php echo $i ;?>
		</td>
		<td>
			<?php echo $result['last_name'].' '.$result['first_name'];?>
		</td>
		<td>
			<?php echo $result['phone_number'];?>
		</td>
		<td>
			<?php echo $result['email'];?>
		</td>
		

	</tr>
	
<?php $i++; endforeach;?>
<?php elseif($type == 'tempt' &&  !is_string($results) && $results != ''):?>
<?php $i = 1; foreach($results as $result):?>
	<tr>				
		<td>
			
		</td>
		<td>
			<?php echo $i ;?>
		</td>
		<td>
			<?php echo $result['name'];?>
		</td>
		<td>
			<?php echo $result['phone_number'];?>
		</td>
		<td>
			<?php echo $result['email'];?>
		</td>
		

	</tr>
	
<?php $i++; endforeach;?>
<?php else:?>
			<tr>
				<td  colspan="16">
					<span class="col-md-12 text-center text-warning"><?php echo lang('common_no_persons_to_display');?></span>
				</td>
			</tr>
<?php endif;?>