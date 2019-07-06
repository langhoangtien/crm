<?php $this->load->view("partial/header"); ?>
<?php $sms_templates_ = isset($sms_templates)?json_encode($sms_templates):0;?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
	<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<label>Tên chiến dịch</label>
		<input id="sms_campain_name" value ="<?php echo (empty($sms_campain['sms_campain_name']))? '': $sms_campain['sms_campain_name'];?>" type="text" class="form-control">
	</div>
	<div class="form-group">
		<div class ="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<label>Đối tượng khách hàng</label>
			<select id="group_smsmail" name="group_smsmail"class="form-control">
				<?php foreach($groups_sms_email as $group_sms_email):?>
					<option value="<?php echo $group_sms_email->smsmail_group_id;?>" <?php echo (!empty($sms_campain['smsmail_group_id'])&&$sms_campain['smsmail_group_id']==$group_sms_email->smsmail_group_id)? 'selected': '';?>><?php echo $group_sms_email->name;?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class =" form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<label>Template</label>
			<select id="selectedTemp" class="form-control">
				<?php foreach($sms_templates as $sms_template):?>
					<option value="<?php echo $sms_template['id'];?>" <?php echo (!empty($sms_campain['sms_id'])&&$sms_campain['sms_id']==$sms_template['id'])? 'selected': '';?>><?php echo $sms_template['title'];?></option>
				<?php endforeach;?>
			</select>
		</div>
		
		
		<?php echo form_label('Nội dung tin nhắn', 'content',array('class'=>' col-sm-3 col-md-3 col-lg-2 ')); ?>

		<div class="form-group col-sm-12 col-md-12 col-lg-12">
			<?php echo form_textarea(array(
																			'name'		=> 'sms_content',
																			'id'			=> 'sms_content',
																			'class'		=> 'form-control text-area',
																			'value'		=> '',
															));?>
												 <?php echo display_ckeditor($ckeditor);?>
			<div style="margin-top: 5px;">
				<span for="mail_content" class="text-danger errors"></span>
			</div>
		</div>
	</div>
	<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<label><?php echo (empty($sms_campain['sms_id']))? 'Cài đặt thời gian chiến dịch:': 'Chỉnh sửa thời gian chiến dịch:';?></label>
		<?php if(!empty($sms_campain['sms_id'])):?>
		<?php 	$daysOfWeeks = array('Thứ hai','Thứ ba','Thứ tư','Thứ năm', 'Thứ sáu','Thứ bảy', 'Chủ nhật');?>
		<?php if($sms_campain['send_day_of_week']=='*'):?>
		<?php	 $daysOfWeek 	 = '';?>
		<?php else:?>
		<?php 	$daysOfWeek  ='';?>
		<?php for($i=0 ;$i<count(explode(',',$sms_campain['send_day_of_week']));$i++):?>
		<?php	 $daysOfWeek 	.= $daysOfWeeks[explode(',',$sms_campain['send_day_of_week'])[$i]].',';?>
		<?php endfor;?>
		<?php 	$daysOfWeek = ' Thứ: '.$daysOfWeek;?>
		<?php endif;?>
		<?php 	$daysOfMonth  = ($sms_campain['send_day_of_month']=='*')?'':', Ngày: '.$sms_campain['send_day_of_month'];?>
		<?php		$month				= ($sms_campain['send_month']=='*')?'':', Tháng: '.$sms_campain['send_month'];?>
		<?php		$hours				= ($sms_campain['send_hours']=='*')?'':', Giờ: '.$sms_campain['send_hours'];?>
		<?php 	$minutes			= ($sms_campain['send_minutes']=='*')?'':' Phút: '.$sms_campain['send_minutes'];?>
		<input type="checkbox" name="chk_edit_enable" id="chk_edit_enable">
		<label for="chk_edit_enable"><span></span></label>	
		<?php echo $minutes.$hours.$daysOfWeek.$daysOfMonth.$month;?>		
	<?php endif;?>
	
	<div class="form-group <?php echo (!empty($sms_campain['sms_id']))? 'editable':''?>"  >
		<ul class="nav nav-tabs" disabled>
			<li class = "active" disabled>
					<a href ="#schedule0" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_timer');?>
							<span class="glyphicon tabActive"> &#xe013 </span>
						</h3>
					</a>
				</li>
				<li >
					<a href ="#schedule1" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_minutes');?>
							<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
				<li>
					<a href ="#schedule2" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_everyday');?>
								<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
				<li>
					<a href ="#schedule3" data-toggle = "tab" >
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_everyday_of_month_at_time');?>
								<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
					<li>
					<a href ="#schedule4" data-toggle = "tab">
						<h3 class="panel-title space_title">
							<?php echo lang('common_repeat_userdefine');?>
								<span class="glyphicon tabActive">&#xe013 </span>
						</h3>
					</a>
				</li>
		</ul>
	</div>
						
	<div style =" border-bottom :1px solid #ddd;border-top:none; "  class=" tab-content form-group <?php echo (!empty($sms_campain['sms_id']))? 'editable':''?> " >
		<div id ="schedule0" class="panel-body nopadding table_holder tab-pane fade in active form-group">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-group" >	
				<?php if(empty($sms_campain['sms_id'])):?>			
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
					<div class =" col-xs-12 col-sm-4 col-md-3 col-lg-2">
						<label>Lặp lại</label>
					</div>
					<div class =" col-xs-12 col-sm-4 col-md-3 col-lg-2">
						<select id="postMinutes_schedule1" class="form-control">
							<?php for($i=1;$i<= 60;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div >
					<div class =" col-xs-12 col-sm-4 col-md-3 col-lg-2">
						<label class="radio-inline">
							phút&nbsp&nbsp&nbsp một lần.
						</label>
					</div>
				</div>
			</div>
		<div  id ="schedule2" class=" panel-body nopadding table_holder tab-pane fade  form-group">
				<div class ="col-xs-12 col-sm-9 col-md-9 col-lg-10">
					<div class ="col-sm-5 col-md-3 col-lg-2">
						<label class="radio-inline">
							Lặp lại mỗi ngày vào lúc
						</label>
					</div>
					<div class =" col-xs-12 col-sm-2 col-md-3 col-lg-2">
						<select id="postHours_schedule2" class="form-control">
							<?php for($i=0;$i<24;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div >
					<div class =" col-xs-12 col-sm-2 col-md-3 col-lg-2">
						<label class="radio-inline">
							:
						</label>
					</div>
					<div class ="col-xs-12 col-sm-2 col-md-3 col-lg-2">
						<select id="postMinutes_schedule2" class="form-control">
							<?php for($i=0;$i< 60;$i+=5):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div>
				</div>
			</div>
		<div  id ="schedule3" class="panel-body nopadding table_holder tab-pane fade  form-group">
			
				<div class ="col-xs-12 col-sm-9 col-md-9 col-lg-10">
					
					<div class ="col-xs-12 col-xs-4  col-sm-3 col-md-2 col-lg-4">
					<label class="radio-inline">
							Lặp lại mỗi tháng vào ngày
						</label>
						<select id="postDays_schedule3"  class="form-control">
							<?php for($i=1;$i<=30;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div >
					<div class ="  col-xs-4  col-sm-2 col-md-2 col-lg-2">
						<label class="radio-inline">
								giờ
						</label>
					</div>
					<div class =" col-xs-4 col-sm-3 col-md-2 col-lg-2">
						<select id="postHours_chedule3"  class="form-control">
							<?php for($i=0;$i<24;$i++):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div >
					<div class =" col-xs-1  col-sm-1 col-md-1 col-lg-1">
						<label class="radio-inline">
							:
						</label>
					</div>
					<div class =" col-xs-2  col-sm-3 col-md-3 col-lg-2">
						<select id="postMinutes_schedule3" class="form-control">
							<?php for($i=0;$i< 60;$i+=5):?>
							<?php echo '<option value="'.$i.'">'.$i.'</option>';?>
							<?php endfor;?>
						</select>
					</div>
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
	<input type="checkbox" name="chk_active" id="chk_active" <?php echo (!empty($sms_campain['active'])&&$sms_campain['active']==1)? 'checked': '';?>>
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
	
		<?php echo "var temp_content = $sms_templates_;";?>
		if(typeof(temp_content['message']) != 'undefined')
		{
			CKEDITOR.instances['sms_content'].setData(temp_content[$('#selectedTemp').val()]['message']);			
		}

	$('#selectedTemp').on('change',function(){
		 CKEDITOR.instances['sms_content'].setData(temp_content[$(this).val()]['message']);
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
		if($('#chk_edit_enable').prop('checked')==true)
		{
			$('.editable').show();
		}
		else
		{
			$('.editable').hide();
		}
	}); 
	
		var id 			 = 	'<?php echo (empty($sms_campain['sms_campain_id']))?-1:$sms_campain['sms_campain_id'];?>';
		var redirect = 	'<?php echo site_url("customers/manage_sms_campain/");?>'+'/'+id;
		$('#save').click(function(){
		
		
		var campain_id 				 		=  id;
		var schedule          		=  $('ul.nav li.active a').attr("href").replace('#','');
		var sms_campain_name 		 	=  $('#sms_campain_name').val();
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
			if(postdatetime[0] == '')
			{
				toastr.warning('Bạn chưa chọn thời gian', 'Warning');
				ajaxFoward = false;
			}
		}
		if(sms_campain_name == '')
		{
				toastr.warning('Bạn chưa nhập tên chiến dịch', 'Warning');
				ajaxFoward = false;
		}
		
		if(ajaxFoward)
			{
				$.ajax({
					url: 'customers/save_sms_campain',
					type:'POST',
					data: {
						campain_id						: campain_id,
						sms_campain_name			: sms_campain_name,
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
						show_feedback('success', ' Thành công', true ? 'Thành công' :'Lỗi');
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


