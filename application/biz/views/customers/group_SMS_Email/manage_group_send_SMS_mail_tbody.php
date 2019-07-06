<?php if($results != null):?>
<?php $i = 1;?>
<?php foreach($results as $result):?>
	<tr>				
		<td>
			<input type="checkbox" name="group_<?php echo $result->smsmail_group_id;?>" id="group_<?php echo $result->smsmail_group_id;?>" value="<?php echo $result->smsmail_group_id;?>">
			<label for="group_<?php echo $result->smsmail_group_id;?>"><span></span></label>		
		</td>
		<td><?php echo $i;?></td>
		<!--<td><?php //echo $result->smsmail_group_id;?></td>-->
		<td><?php echo $result->name;?></td>
		<td><?php echo $result->description;?></td>
		<td><?php echo $customers_in_group[$result->smsmail_group_id]?></td>
		<td colspan="2">
			<a id ="<?php echo $result->smsmail_group_id;?>" class="btn btn-primary btn-lg btn-detail-smsmail-group "  title="<?php echo lang('common_detail');?>"  href="<?php echo ("$controller_name/manage_group_SMS_email_detail"."/".$result->smsmail_group_id);?>"><?php echo lang('common_detail');?></a>
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
