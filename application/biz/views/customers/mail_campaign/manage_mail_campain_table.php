			<?php $daysOfWeeks = array('Thứ hai','Thứ ba','Thứ tư','Thứ năm', 'Thứ sáu','Thứ bảy', 'Chủ nhật');?>
			<?php if($results != null):?>
			<?php var_dump($STT+1);?>
			<?php $STT = $STT+1; foreach($results as $result):?>
			<?php if($result['send_day_of_week']=='*'):?>
			<?php	 $daysOfWeek 	 = '';?>
			<?php else:?>
			<?php 	$daysOfWeek  ='';?>
			<?php for($i=0 ;$i<count(explode(',',$result['send_day_of_week']));$i++):?>
			<?php	 $daysOfWeek 	.= $daysOfWeeks[explode(',',$result['send_day_of_week'])[$i]].',';?>
			<?php endfor;?>
																								
			<?php endif;?>
		
			<?php $daysOfMonth  = ($result['send_day_of_month']=='*')? '' : $result['send_day_of_month'];?>
			<?php $month				= ($result['send_month']=='*')? ''				: $result['send_month'];?>
			<?php $hours				= ($result['send_hours']=='*')? ''				: $result['send_hours'];?>
			<?php $minutes			= ($result['send_minutes']=='*')? ''			: $result['send_minutes'];?>
			<?php if(count(explode(',',$result['send_day_of_month']))>1 || count(explode(',',$result['send_month']))>1 || count(explode(',',$result['send_hours']))>1 || count(explode(',',$result['send_minutes']))>1 ||$daysOfWeek  !=''):?>
			<?php $datetime		  = 'Phút: '.$minutes.'<br> Giờ: '.$hours. '<br>Thứ: '.$daysOfWeek.'<br>Ngày: '.$daysOfMonth.'<br>Tháng: '.$month;?>
			<?php $iterative_time				= ($result['iterative_time'] == 0)?'Không':'Hàng năm';?>
			<?php elseif($minutes == '' && $hours =='' && $month =='' && $daysOfMonth ==''):?>
			<?php $datetime 	  = '1 phút';?>
			<?php $iterative_time				= ($result['iterative_time'] == 0)?'Không':'Mỗi 1 Phút';?>
			<?php elseif($hours =='' && $month =='' && $daysOfMonth ==''):?>
			<?php $datetime 	  = $minutes.' Phút';?>
			<?php $iterative_time 		  = 'Mỗi '.$minutes.' Phút';?>
			<?php elseif($month =='' && $daysOfMonth=='' && $minutes!='' && $hours!=''):?>
			<?php $datetime  		= $hours.' giờ '.$minutes.' Phút ';?>
			<?php $iterative_time				= ($result['iterative_time'] == 0)?'Không':'Hàng ngày';?>
			<?php elseif($month =='' && $minutes!='' && $hours!='' && $daysOfMonth !='' ):?>
			<?php $datetime 	  = $hours.' giờ '.$minutes.' Phút , Ngày '.$daysOfMonth;?>
			<?php $iterative_time				= ($result['iterative_time'] == 0)? 'Không':'Hàng tháng';?>
			<?php elseif($minutes != '' && $hours !='' && $month !='' && $daysOfMonth !='' && $result['iterative_time'] == 0):?>
			<?php $datetime  		= $hours.':'.$minutes .' Ngày '.$daysOfMonth.' Tháng '.$month;?>
			<?php $iterative_time				= ($result['iterative_time'] == 0)? 'Không':'Hàng năm';?>
			<?php else:?>
			<?php $datetime		  = 'Phút: '.$minutes.'<br> Giờ: '.$hours.$daysOfWeek.'<br>Ngày: '.$daysOfMonth.'<br>Tháng: '.$month;?>
			<?php $iterative_time				= ($result['iterative_time'] == 0)?'Không':'Có';?>
			<?php endif;?>
			<tr>				
				<td>
					<input type="checkbox" name="mail_campain_<?php echo $result['mail_campain_id'];?>" id="mail_campain_<?php echo $result['mail_campain_id'];?>" value="<?php echo $result['mail_campain_id'];?>">
					<label for="mail_campain_<?php echo $result['mail_campain_id'];?>"><span></span></label>		
				</td>
				<td>
					<?php echo $STT;?>
				</td>
				<td>
					<?php echo $result['mail_campain_name'] ;?>
				</td>
				<td>
					<?php echo $result['mail_title'];?>
				</td>
				<td>
					<?php echo $result['name'];?>
				</td>
				<td>
					<?php echo $datetime;?>
				</td>
				<td>
				<?php if($result['smsmail_group_id']>0):?>
					<?php echo $iterative_time;?>
				<?php endif;?>
				</td>
				<td>
					<?php echo ($result['active']==1)?'Có':'Không';?>
				</td>
				<td>
					<?php if($result['smsmail_group_id']>0):?>
						<a href="javascript:;" onclick="update_mail_campain(<?php echo $result['mail_campain_id']; ?>)"><?php echo lang('common_edit'); ?></a>
					<?php endif;?>
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