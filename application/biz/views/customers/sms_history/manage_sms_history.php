<?php $this->load->view("partial/header"); ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
		<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li>
						<a id ="template_list" href="<?php echo base_url().'customers/manage_sms/1'?>">
							<h3 class="panel-title space_title">
								<?php echo lang('customers_sample_form_sms'); ?>
								<span class="badge bg-primary tip-left" id="count_template_list"><?php echo $total_rows_sms_temp;?></span>
							</h3>
						</a>
					</li>
					<li>
						<a id ="campain_list"  href="<?php echo base_url().'customers/manage_sms_campain/1'?>">
							<h3 class="panel-title space_title">
								<?php echo lang('customers_sms_campaign_list'); ?>
								<span class="badge bg-primary tip-left" id="count_campain_list"><?php echo $total_sms_campain;?></span>
							</h3>
						</a>
					</li>
					<li class = "active">
						<a id ="send_history_list" href="<?php echo base_url().'customers/manage_sms_history_input'?>">
							<h3 style="color:red;" class="panel-title space_title" >
								<?php echo lang('customers_history_sms'); ?>
								<span class="badge bg-primary tip-left" id="count_history_list">x</span>
							</h3>
						</a>
					</li>
					
				</ul>
			</div>
			<div class="panel-heading">
			
				<?php echo lang('reports_date_range'); ?>
			</div>
			<div class="panel-body">
				<?php
				if(isset($error))
				{
					echo "<div class='error_message'>".$error."</div>";
				}
				?>
				<form  class="form-horizontal form-horizontal-mobiles">

					<div class="form-group">
						<?php echo form_label(lang('reports_fixed_range').' :', 'simple_radio',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?>

						<div class="col-sm-9 col-md-2 col-lg-2">
							<input type="radio" name="report_type" id="simple_radio" value='simple' checked='checked'/>
							<label for="simple_radio"><span></span></label>
							<?php echo form_dropdown('report_date_range_simple',$report_date_range_simple, '', 'id="report_date_range_simple" class="form-control"'); ?>
						</div>
					</div>

					<div id='report_date_range_complex'>
						<div class="form-group">
							<?php echo form_label(lang('reports_custom_range').' :', 'complex_radio',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?>

							<div class="col-sm-9 col-md-9 col-lg-10">
								<input type="radio" name="report_type" id="complex_radio" value='complex' />
								<label for="complex_radio"><span></span></label>
								<div class="row">
									<div class="col-md-6">
										<div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           <?php echo lang('reports_from'); ?>
					                       	</span>
		                                    <input type="text" class="form-control start_date" name="start_date" id="start_date">
		                                </div>
									</div>
									<div class="col-md-6">
										<div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    <?php echo lang('reports_to'); ?>
			                                </span>
		                                    <input type="text" class="form-control end_date" name="end_date" id="end_date">
		                                </div>	
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group">
							<?php echo form_label('Kiểu xem:', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?> 
							<div class="col-sm-9 col-md-9 col-lg-10">
								<!--<input type="radio" name="viewType" id="viewType_groupSMSEmail" value='1' /> Theo nhóm &nbsp;
								<label for="viewType_groupSMSEmail"><span></span></label>
									<?php//echo form_dropdown('smsmail_groups',$smsmail_groups, '', 'id="smsmail_groups" class="form-control"'); ?>
								<input type="radio" name="viewType" id="viewType_campaign" value='0' checked='checked' /> Theo chiến dịch  &nbsp;
								<label for="viewType_campaign"><span></span></label>
									<?php// echo form_dropdown('sms_campains',$sms_campains, '', 'id="sms_campains" class="form-control"'); ?>-->
								<input type="radio" checked name="viewType" id="all" value='0' /> Tất cả  &nbsp;
								<label for="all"><span></span></label>
							</div>
						</div>
						<div class="form-group">
							<?php echo form_label(lang('reports_export_to_excel').' :', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?> 
							<div class="col-sm-9 col-md-9 col-lg-10">
								<input type="radio" name="export_excel" id="export_excel_yes" value='1' /> <?php echo lang('common_yes'); ?>  &nbsp;
								<label for="export_excel_yes"><span></span></label>
								<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> <?php echo lang('common_no'); ?> &nbsp;
								<label for="export_excel_no"><span></span></label>
							</div>
						</div>

						<div class="form-actions pull-right">
							<?php
							echo form_button(array(
								'name'=>'generate_report',
								'id'=>'generate_report',
								'content'=>lang('common_submit'),
								'class'=>'btn btn-primary submit_button')
							);
							?>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" language="javascript">
	$(document).ready(function()
	{
		

		$("#generate_report").click(function()
		{
			var export_excel = 0;
			var view_type = 0;
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}
			if ($("#viewType_campaign").prop('checked'))
			{
				view_type = 'cmp_'+$('#viewType_campaign').val();
			}
			if ($("#viewType_groupSMSEmail").prop('checked'))
			{
				view_type = 'grp_'+$('#smsmail_groups').val();
			}

			if ($("#simple_radio").prop('checked'))
			{
				window.location = 'customers/manage_sms_history_detail/'+$("#report_date_range_simple option:selected").val()+ '/' +view_type+ '/'+ export_excel;
			}
			else
			{
				var start_date = $("#start_date").val();
				var end_date = $("#end_date").val();

				window.location = 'customers/manage_sms_history_detail'+'/'+start_date + '/'+ end_date + '/' +view_type+ '/'+ export_excel;
			}
		});

		$("#smsmail_groups").change(function()
		{
			$("#viewType_groupSMSEmail").prop('checked', true);
		});
		$("#sms_campains").change(function()
		{
			$("#viewType_campaign").prop('checked', true);
		});
		
		$("#report_date_range_simple").change(function()
		{
			$("#simple_radio").prop('checked', true);
		});
		$("#start_date").click(function(){
			$("#complex_radio").prop('checked', true);
		}); 
		$("#end_date").click(function(){
			$("#complex_radio").prop('checked', true);
		});    
        

        date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
        date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
	});
</script>
<?php $this->load->view("partial/footer"); ?>