<?php $this->load->view("partial/header"); ?>
<?php $mail_templates_ = json_encode((!empty($mail_templates))?$mail_templates:'');?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
	<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label>Tên chiến dịch</label>
		<input id="mail_campain_name" value ="<?php echo (empty($mail_campain['mail_campain_name']))? '': $mail_campain['mail_campain_name'];?>" type="text" class="form-control">
	</div>
	<div class="form-group">
		<div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<label>Đối tượng khách hàng</label>
			<select id="group_smsmail" name="group_smsmail"class="form-control">
		
				<?php foreach($groups_sms_email as $group_sms_email):?>
					<option value="<?php echo $group_sms_email->smsmail_group_id;?>" <?php echo (!empty($mail_campain['smsmail_group_id'])&&$mail_campain['smsmail_group_id']==$group_sms_email->smsmail_group_id)? 'selected': '';?>><?php echo $group_sms_email->name;?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class =" form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<label>Template</label>
			<select id="selectedTemp" class="form-control">
				<?php foreach($mail_templates as $mail_template):?>
					<option value="<?php echo $mail_template['mail_id'];?>" <?php echo (!empty($mail_campain['mail_id'])&&$mail_campain['mail_id']==$mail_template['mail_id'])? 'selected': '';?>><?php echo $mail_template['mail_title'];?></option>
				<?php endforeach;?>
			</select>
		</div>
		
		
		<?php echo form_label(lang('customers_manage_mail_content').' :', 'content',array('class'=>' col-sm-3 col-md-3 col-lg-2 ')); ?>

		<div class="form-group col-sm-12 col-md-12 col-lg-12">
			<?php echo form_textarea(array(
																			'name'		=> 'mail_content',
																			'id'			=> 'mail_content',
																			'class'		=> 'form-control text-area',
																			'value'		=> (!empty($mail_campain['mail_id']) && !empty($mail_templates))? $mail_templates[$mail_campain['mail_id']]['mail_content']:array_values($mail_templates)[0]['mail_content'],
															));?>
												 <?php echo display_ckeditor($ckeditor);?>
												
				<span for="mail_content" class="text-danger errors"></span>
			</div>
		</div>
	</div>
	<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label><?php echo (empty($mail_campain['mail_id']))? 'Cài đặt thời gian chiến dịch:': 'Chỉnh sửa thời gian chiến dịch:';?></label>
		<?php if(!empty($mail_campain['mail_id'])):?>
		<?php 	$daysOfWeeks = array('Thứ hai','Thứ ba','Thứ tư','Thứ năm', 'Thứ sáu','Thứ bảy', 'Chủ nhật');?>
		<?php if($mail_campain['send_day_of_week']=='*'):?>
		<?php	 $daysOfWeek 	 = '';?>
		<?php else:?>
		<?php 	$daysOfWeek  ='';?>

		<?php for($i=0 ;$i<count(explode(',',$mail_campain['send_day_of_week']));$i++):?>
		<?php	 $daysOfWeek 	.= $daysOfWeeks[explode(',',$mail_campain['send_day_of_week'])[$i]].',';?>
		<?php endfor;?>
																								
		<?php endif;?>
		<?php 	$daysOfMonth  = ($mail_campain['send_day_of_month']=='*')?'':' Ngày: '.$mail_campain['send_day_of_month'];?>
		<?php		$month				= ($mail_campain['send_month']=='*')?'':' Tháng: '.$mail_campain['send_month'];?>
		<?php		$hours				= ($mail_campain['send_hours']=='*')?'':' Giờ: '.$mail_campain['send_hours'];?>
		<?php 	$minutes			= ($mail_campain['send_minutes']=='*')?'':' Phút: '.$mail_campain['send_minutes'];?>
		
		<?php if(($mail_campain['send_minutes'] != '*' && $mail_campain['send_hours'] != '*' && $mail_campain['send_day_of_month'] != '*' && $mail_campain['send_month'] != '*')&& $mail_campain['iterative_time'] == 0 ):?>
		<?php $schedule = 'tab_schedule0'?>
		<?php elseif($mail_campain['send_minutes'] != '*' && $mail_campain['send_hours'] == '*' && $mail_campain['send_day_of_month'] == '*' && $mail_campain['send_month'] == '*'):?>
		<?php $schedule = 'tab_schedule1'?>
		<?php elseif($mail_campain['send_minutes'] != '*' && $mail_campain['send_hours'] != '*' && $mail_campain['send_day_of_month'] == '*' && $mail_campain['send_month'] == '*'):?>
		<?php $schedule = 'tab_schedule2'?>
		<?php elseif($mail_campain['send_minutes'] != '*' && $mail_campain['send_hours'] != '*' && $mail_campain['send_day_of_month'] != '*' && $mail_campain['send_month'] == '*'):?>
		<?php $schedule = 'tab_schedule3'?>
		<?php else:?>
		<?php $schedule = 'tab_schedule4'?>
		<?php endif;?>
		
		<input type="checkbox" name="chk_edit_enable" id="chk_edit_enable">
		<label for="chk_edit_enable"><span></span></label>	
		<?php echo $minutes.$hours.$daysOfWeek.$daysOfMonth.$month;?>		
	  <?php endif;?>
	
	<div class="form-group <?php echo (!empty($mail_campain['mail_id']))? 'editable':''?>"  >
		<ul id = "schedule_list" class="nav nav-tabs" disabled>
			<li class = "active" id = "tab_schedule0" disabled>
					<a href ="#schedule0" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_timer');?>
							<span class="glyphicon tabActive"> &#xe013 </span>
						</h3>
					</a>
				</li>
				<li id = "tab_schedule1" >
					<a href ="#schedule1" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_minutes');?>
							<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
				<li id = "tab_schedule2">
					<a href ="#schedule2" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_everyday');?>
								<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
				<li id = "tab_schedule3">
					<a href ="#schedule3" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_everyday_of_month_at_time');?>
								<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
					<li id = "tab_schedule4">
					<a href ="#schedule4" data-toggle = "tab">
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_userdefine');?>
								<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
		</ul>
	</div>
						
	<div style =" border-bottom :1px solid #ddd;border-top:none; "  class=" tab-content form-group <?php echo (!empty($mail_campain['mail_id']))? 'editable':''?> " >
		<div id ="schedule0" class="panel-body nopadding table_holder tab-pane fade in active form-group">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-group" >	
				<?php if(empty($mail_campain['mail_id'])):?>			
					<button id="appendTime" class="btn btn-info">+</button>
				<?php endif;?>
			</div>
			<div class ="col-xs-12 col-sm-5 col-md-4 col-lg-3">
				<div class = "input-group date datetimepicker" >
					<input type="text" class="form-control postdatetime_schedule0">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
					<span class="input-group-addon closeTimeSet">
						<span class="glyphicon"> &#xe014 </span>
					</span>
				</div >
			</div>
		</div>
		<div id ="schedule1"class="panel-body nopadding table_holder tab-pane fade form-group">
				<div class ="col-sm-12 col-md-12 col-lg-12">			 
						Lặp lại:&nbsp														 
						<select id="postMinutes_schedule1">
							<?php for($i=1;$i<= 60;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>	
							phút  một lần.
				</div>
			</div>
		<div  id ="schedule2" class=" panel-body nopadding table_holder tab-pane fade  form-group">
				<div class ="col-xs-12 col-sm-9 col-md-9 col-lg-10">
																	
							Lặp lại mỗi ngày vào lúc:&nbsp														 
						<select id="postHours_schedule2">
							<?php for($i=0;$i<24;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
							giờ&nbsp										
						<select id="postMinutes_schedule2">
							<?php for($i=0;$i< 60;$i+=5):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
						phút.
				</div>
			</div>
		<div  id ="schedule3" class="panel-body nopadding table_holder tab-pane fade  form-group">
			
				<div class ="col-xs-12 col-sm-9 col-md-9 col-lg-10">
							Lặp lại mỗi tháng vào ngày:&nbsp&nbsp
						<select id="postDays_schedule3">
							<?php for($i=1;$i<=30;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
								lúc &nbsp															
						<select id="postHours_schedule3">
							<?php for($i=0;$i<24;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
						giờ&nbsp&nbsp															 
						<select id="postMinutes_schedule3">
							<?php for($i=0;$i< 60;$i+=5):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
						phút.
				</div>
			</div>
		<div  id ="schedule4" class="panel-body nopadding table_holder tab-pane fade  form-group">
				<div class =" col-sm-9 col-md-9 col-lg-10">
				
					<div class ="col-xs-5 col-sm-3 col-md-3 col-lg-3">
					<?php echo lang('common_days_of_month');?>
						<select id="postDays_schedule4" multiple class="form-control">
							<?php for($i=1;$i<=31;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div>	
					<div class =" col-xs-5 col-sm-3 col-md-3 col-lg-3">
					<?php echo lang('common_days_of_week');?>
						<select id="postDaysOfWeek_schedule4" multiple class="form-control">
							
							<?php $daysOfWeek = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');?>
							<?php for($i=0;$i<=6;$i++):?>
							<?php echo '<option value="'.$i.'">'.$daysOfWeek[$i].'</option>';?>
							<?php endfor;?>
						</select>
					</div>
					<div class ="col-xs-5 col-sm-3 col-md-3 col-lg-2">
					<?php echo lang('common_months');?>
						<select id="postMonths_schedule4" multiple class="form-control">
							<?php for($i=1;$i<13;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div>
					<div class ="col-xs-5 col-sm-3 col-md-3 col-lg-2">
						<?php echo lang('common_hours');?>
						<select id="postHours_schedule4" multiple class="form-control">
							<?php for($i=0;$i< 24;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div>
					<div class ="col-xs-5 col-sm-3 col-md-3 col-lg-2">
						<?php echo lang('common_minutes');?>
						<select id="postMinutes_schedule4" multiple class="form-control">
							<?php for($i=0;$i< 60;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div>										
				</div>
			</div>
	</div>
	</div>
	
<div class =" form-group col-sm-12 col-md-12 col-lg-12">
	<label for="chk_active">Kích hoạt chiến dịch: </label>	
	<input type="checkbox" name="chk_active" id="chk_active" <?php echo (!empty($mail_campain['active'])&&$mail_campain['active']==1)? 'checked': '';?>>
	<label for="chk_active"><span></span></label>		
	
</div>
<div class ="col-sm-12 col-md-12 col-lg-12">
	<button id="save" class="btn btn-info"> Lưu </button>
</div>
</div>

<script type="text/javascript">
	// CKEDITOR.config.allowedContent = true;
	// CKEDITOR.config.removeFormatAttributes = '';
	// CKEDITOR.config.extraPlugins = 'dialogadvtab';
		
	$(document).ready(function(){
		$('ul.nav li.active').siblings().children().children().children('.tabActive').hide();
	});

	$('#selectedTemp').on('change',function(){
		 <?php echo "var temp_content = $mail_templates_;";?>
		 console.log(temp_content);
		 CKEDITOR.instances['mail_content'].setData(temp_content[$(this).val()]['mail_content']);
	});

	$('ul.nav li').click(function(){
		$('.tabActive').show();
		$(this).siblings().children().children().children('.tabActive').hide();
		});
		
	$(function(){$('.datetimepicker').datetimepicker();});
	$(document).on('click','.closeTimeSet',function(){ $(this).parent().parent().remove(); });
	
	$(document).on('click','#appendTime',function(){
			$("#schedule0 div:eq(0)").after('<div class ="col-xs-12 col-sm-5 col-md-4 col-lg-3"><div class = "input-group date datetimepicker"><input type="text" class="form-control postdatetime_schedule0"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span><span class="input-group-addon closeTimeSet"><span class="glyphicon"> &#xe014 </span></span></div ></div>');
			$('.datetimepicker').datetimepicker();
		});
	
	$('.editable').hide();
	
	$('#chk_edit_enable').on('click',function(){
		var set_active_tab_schedule = '<?php echo empty($schedule)?'':$schedule;?>';
		var set_active_schedule = '<?php echo empty($schedule)?'':str_replace('tab_','',$schedule);?>';
		var set_minutes         = '<?php echo empty($mail_campain['send_minutes'])? 0:$mail_campain['send_minutes'];?>';
		var set_hours           = '<?php echo empty($mail_campain['send_hours'])? 0:$mail_campain['send_hours'];?>';
		var set_dayOfWeek       = '<?php echo empty($mail_campain['send_day_of_week'])? 0:$mail_campain['send_day_of_week'];?>';
		var set_months          = '<?php echo empty($mail_campain['send_month'])? 0:$mail_campain['send_month'];?>';
		var set_day             = '<?php echo empty($mail_campain['send_day_of_month'])? 0:$mail_campain['send_day_of_month'];?>';
		if($('#chk_edit_enable').prop('checked')==true)
		{ 
			$('.editable').show();
			$('#'+set_active_tab_schedule).siblings().removeClass('active');
			$('#'+set_active_tab_schedule).addClass('active');
			$('.tabActive').show();
			$('#'+set_active_tab_schedule).siblings().children().children().children('.tabActive').hide();
			$('#'+set_active_tab_schedule)
			$('#'+set_active_schedule).siblings().removeClass('in active');
			$('#'+set_active_schedule).addClass('in active');
			if(set_active_schedule=='schedule0')
			{
				$('.postdatetime_schedule0').datetimepicker({
        defaultDate:  new Date(set_months+'/'+set_day+'/'+ new Date().getFullYear()+' '+set_hours+':'+set_minutes)
			});
			}
			else
			{
				$('#postMinutes_'+set_active_schedule).val(set_minutes.split(','));
				$('#postHours_'+set_active_schedule).val(set_hours.split(','));
				$('#postDaysOfWeek_'+set_active_schedule).val(set_dayOfWeek.split(','));
				$('#postMonths_'+set_active_schedule).val(set_months.split(','));
				$('#postDays_'+set_active_schedule).val(set_day.split(','));
			}
		}
		else
		{
			$('.editable').hide();
		}
	}); 
	
		var id 			 = 	'<?php echo (empty($mail_campain['mail_campain_id']))?-1:$mail_campain['mail_campain_id'];?>';
		var redirect = 	'<?php echo site_url("customers/manage_mail_campain/");?>';
		$('#save').click(function(){
		
		
		var campain_id 				 		=  id;
		var schedule          		=  $('ul#schedule_list.nav>li.active>a').attr("href").replace('#','');
		var mail_campain_name 		=  $('#mail_campain_name').val();
		var group_smsmail		  		=  $('#group_smsmail').val();
		var selectedTemp		 	 	  =  $('#selectedTemp').val();
		var postdatetime 					=  [];
		var postMinutes				 	  =  $('#postMinutes_'+schedule).val();
		var postHours				 	  	=  $('#postHours_'+schedule).val();
		var postDaysOfWeek 				=  $('#postDaysOfWeek_'+schedule).val();
		var postMonths       		  =  $('#postMonths_'+schedule).val();
		var postDays 							=  $('#postDays_'+schedule).val();
		var chk_active 						=  ($('#chk_active').prop('checked')==true)? 1:0;
		var chk_edit_enable				=  ($('#chk_edit_enable').prop('checked')==true)? 1:0;
		var ajaxFoward  					=  true;
		$('.postdatetime_'+schedule).each(function(){
			postdatetime.push($(this).val());
		});
		
		//validation
		if(schedule=='schedule0')
		{
			if(chk_edit_enable == 1 && postdatetime[0] == '')
			{
				toastr.warning('Bạn chưa chọn thời gian', 'Warning');
				ajaxFoward = false;
			}
			if(id == -1 && postdatetime[0] == '')
			{
				toastr.warning('Bạn chưa chọn thời gian', 'Warning');
				ajaxFoward = false;
			}
		}
		if($('#mail_campain_name').val()== '' )
		{
				toastr.warning('Bạn chưa nhập tên chiến dịch', 'Warning');
				ajaxFoward = false;
		}
		else if($('#group_smsmail').val() ==='')
		{
				toastr.warning('Bạn chưa chọn nhóm gửi.', 'Warning');
				ajaxFoward = false;			
		}
		else if($('#selectedTemp').val()==='')
		{
				toastr.warning('Bạn chưa chọn mẫu gửi', 'Warning');
				ajaxFoward = false;			
		}
		
		
		if(ajaxFoward)
			{
				$.ajax({
					url: 'customers/save_mail_campain',
					type:'POST',
					data: {
						campain_id						: campain_id,
						mail_campain_name			: mail_campain_name,
						group_smsmail					: group_smsmail,
						selectedTemp					: selectedTemp,
						postdatetime					: postdatetime,
						postMinutes						: postMinutes,
						postHours							: postHours,
						postDaysOfWeek				: postDaysOfWeek,
						postMonths						: postMonths,
						postDays							: postDays,
						chk_edit_enable 		  : chk_edit_enable,
						chk_active						: chk_active,
					},
					success:function (data){
						
							show_feedback('success', 'Thêm/Chỉnh sửa thành công', true ? 'Thành công' :'Lỗi');
							window.location.href = 	redirect;
					},
				})
			}
	});



</script>
<style>
 .closeTimeSet:hover{
	 color:#bc2328;
	 
 }
 li.active a h3{
	 color:#bc2328;
 }
</style>


