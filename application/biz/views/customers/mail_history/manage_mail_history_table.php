
<?php if($results != null):?>

<?php $i = $STT+1; foreach($results as $result):?>
	<tr>				
		<td>
			<input type="checkbox" name="mailhistory_<?php echo $result['mh_id'];?>" id="mailhistory_<?php echo $result['mh_id'];?>" value="<?php echo $result['mh_id'];?>">
			<label for="mailhistory_<?php echo $result['mh_id'];?>"><span></span></label>
	  </td>
		<td><?php echo $i;?></td>
		<td><?php echo $result['receive_person'];?></td>
		<td><?php echo $result['mh_email'];?>	</td>
		<td><?php echo $result['mh_title'];?>	</td>
		<td><?php echo $result['mh_time'];?></td>
		<td><?php echo $result['status_'];?></td>
		<td class="center"><a href="javascript:;" onclick="view_mail(<?php echo $result['mh_id']; ?>)">Xem</a></td>
	</tr>
<?php $i++; endforeach;?>
<?php else:?>
	<tr>
		<td  colspan="16">
			<span class="col-md-12 text-center text-warning"><?php echo lang('common_no_persons_to_display');?></span>
		</td>
	</tr>
	<?php endif;?>
