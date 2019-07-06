<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<style type="text/css">

</style>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-n9-gid.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/expenses.js" ></script>
<?php
if(isset($_SESSION['sales_model_filter']))
    unset($_SESSION['sales_model_filter']);
?>
<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
		<div class="panel panel-piluku">
	        <div class="panel-heading font-red-sunglo">
                <h3 class="panel-title">
                    <i class="ion-edit"></i> <?php echo lang('expenses_new'); ?><small>(Các trường màu đỏ là cần nhập)</small>
                </h3>
	        </div>
		 	<?php echo form_open('expenses/save/'.$expense_info->id,array('id'=>'expenses_form')); ?>
	            <div class="form-body">
	            	<?php
		                if(!isset($expense_info)) $expense_option = 'other';
		                else $expense_option = $expense_info->expense_options;
	                ?>
	                <!-- Hình thức -->
	                <!-- LOAI CHI PHI DA BO -->
	                <div class="form-group form-md-line-input" id='f_expenses_type'" style="display: none;">
	                    <select name="expenses_type" class="form-control" id="expenses_type">
	                        <option value="1"<?php if($expense_info->expense_type == 1) echo ' selected'; ?>><?php echo lang('expenses_expenditures'); ?></option>
	                        <!-- <option value="-1"<?php //if($expense_info->expense_type == -1) echo ' selected'; ?>><?php //echo lang('expenses_receipts'); ?></option> -->
	                    </select>
	                    <label for="form_control_1"><?php echo lang('expenses_type'); ?></label>
	                </div>
	                 
	                <!-- Loại thu chi -->
	                <div class="form-group form-md-line-input" id='f_expenses_options'">
	                    <select name="expenses_options" class="form-control" id="expenses_options"<?php if($expense_info->sale_id > 0) echo ' readonly="1"'; ?>>
	                            <option value="other"<?php if($expense_info->expense_options == 'other') echo ' selected'; ?>>Chi phí chung</option>
	                           <!--  <option value="receiving"<?php //if($expense_info->expense_options == 'receiving') echo ' selected'; ?>><?php //echo lang('expenses_import_costs'); ?></option> -->
	                            <option value="sale"<?php if($expense_info->expense_options == 'sale') echo ' selected'; ?>>Doanh thu chia cho phòng ban khác</option>
	                    </select>
	                    <label style="color: #e7505a;" for="form_control_1">Loại chi phí</label>
	                    <!-- <label style="color: #e7505a;" for="form_control_1"><?php echo lang('expenses_type_of_pay'); ?></label> -->
	                    
	                    <!-- expenses_type_of_pay -->
	                </div>
	                 <div class="form-group form-md-line-input" id='f_expenses_date'">
					    <label style="color: #e7505a;" for="form_control_1"><?php echo lang('expenses_date'); ?></label>
					  	<div class="input-group date">
					    	<span class="input-group-addon"><i class="ion-calendar"></i></span>
					    	<?php echo form_input(array(
					      		'name'=>'expenses_date',
								'id'=>'expenses_date_input',
								'class'=>'form-control form-inps datepicker',
								'value'=>$expense_info->expense_date ? date("d-m-Y",strtotime($expense_info->expense_date)) : "")
					    	);?> 
					    </div>  
					</div>
					
					<!-- Số tiền -->
					<div class="form-group form-md-line-input" id='f_expenses_amount'>
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
								'name'=>'expenses_amount',
								'id'=>'expenses_amount_input',
								'value'=>$expense_info->expense_amount? to_currency_no_money($expense_info->expense_amount) : '',
								'placeholder'=>'Điền số tiền thu chi'
							)
						);?>
						<label style="color: #e7505a;" for="form_control_1"><?php echo lang('expenses_amount');?></label>
	                    <span class="help-block">Số tiền thu chi, bắt buộc nhập</span>
	                    <span for="f_expenses_amount" id="email2-error" class="help-block help-block-error has-error"></span>
	                 
					</div>
					<!-- thuế -->
					<!-- <div class="form-group form-md-line-input" id='f_expenses_tax'>
						<?php// echo form_input(array(
							// 'class'=>'form-control form-inps',
							// 	'name'=>'expenses_tax',
							// 	'id'=>'expenses_tax_input',
							// 	'value'=>$expense_info->expense_tax? to_currency_no_money($expense_info->expense_tax) : to_currency_no_money(0),
							// 	'placeholder'=>'Tiền thuế'
							)
						//);?>
						<label for="form_control_1"><?php //echo lang('common_tax');?></label>
	                    <span class="help-block">Tiền thuế</span>
	                    <span for="f_expenses_tax" id="email2-error" class="help-block help-block-error has-error"></span>
					</div> -->
					<!-- mô tả -->
					<div class="form-group form-md-line-input" id='f_expenses_description'">
						<!-- <?php //echo form_input(array(
							// 'class'=>'form-control',
							// 'name'=>'expenses_description',
							// 'id'=>'expenses_description_input',
							// 'value'=>$expense_info->expense_description,
							// 'placeholder'=>'Mô tả')
						//);?>
						<label  for="form_control_1"><?php //echo lang('Mô tả');?></label>
	                    <span class="help-block">Mô tả</span>
	                    <span for="f_expenses_description" id="email2-error" class="help-block help-block-error has-error"></span> -->

	                     <textarea class="form-control" name="expenses_description" rows="3"><?php echo $expense_info->expense_description; ?></textarea>
	                    <label for="form_control_1"><?php echo lang('Mô tả'); ?></label>
	                    <span class="help-block">Mô tả</span>
					</div>

					<!-- lý do -->
					<div class="form-group form-md-line-input" id='f_expense_reason'">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
								'name'=>'expense_reason',
								'id'=>'expenses_reason_input',
								'value'=>$expense_info->expense_reason?$expense_info->expense_reason:set_value('expense_reason'),
							'placeholder'=>'Lý do')
						);?>
						<label  for= "form_control_1"><?php echo lang('common_reason');?></label>
	                    <span class="help-block">Diễn dải lý do thu chi</span>
	                    <span for="f_expense_reason" id="email2-error" class="help-block help-block-error has-error"></span>
					</div>

	
	                <div id="receiver_section">
	                </div>

	                <!-- Nhân viên -->
	                <div class="form-group form-md-line-input" id='f_employee_id'">
	              
	                    <select name="employee_id" id="employee_id" class="form-control" >
	                            	<option value="-1"><?php echo lang('expenses_select_personnel'); ?></option>
								<?php
								foreach($employees as $key => $val) {
									?>
									<option value="<?php echo $key; ?>"<?php if($expense_info->employee_id == $key) echo ' selected'; ?>><?php echo $val; ?></option>
									<?php
								}
								?>
						</select>
	                	<label  for="form_control_1"><?php echo lang('expenses_executor'); ?></label>
	                	<span for="f_employee_id" id="email2-error" class="help-block help-block-error has-error"></span>
	                </div>

	                <!-- Nhân viên xác nhận-->
	                <div class="form-group form-md-line-input" id='f_approved_employee_id'">
	              		<select name="approved_employee_id" id="approved_employee_id" class="form-control" >
	                            	<option value="-1"><?php echo lang('expenses_select_approver'); ?></option>
								<?php
								foreach($employees as $key => $val) {
									?>
									<option value="<?php echo $key; ?>"<?php if($expense_info->approved_employee_id == $key) echo ' selected'; ?>><?php echo $val; ?></option>
									<?php
								}
								?>
						</select>
	                	<label  for="form_control_1"><?php echo lang('Người phê duyệt') ?></label>
	                	<span for="f_approved_employee_id" id="email2-error" class="help-block help-block-error has-error"></span>
	                </div>

	                <!-- Ghi chú -->
	                <div class="form-group form-md-line-input" id='f_expenses_note'">
	                    <textarea class="form-control" name="expenses_note" rows="3"><?php echo $expense_info->expense_note; ?></textarea>
	                    <label for="form_control_1"><?php echo lang('common_note'); ?></label>
	                    <span class="help-block">Ghi chú</span>
	                </div>

	                <!-- Hình thức thanh toán -->
	                <div class="form-group form-md-line-input" id='f_payment_type'">
	              		<select name="payment_type" class="form-control" id="payment_type">
								<option value="-1"><?php echo lang('expenses_select_payments');?></option>
							<?php
								foreach($payment_options as $key => $val) {
								?>
									<option value="<?php echo $key; ?>"<?php if($expense_info->payment_type == $key) echo 'selected'; ?>><?php echo $val; ?></option>
								<?php
								}
							?>
						</select>
	                  
	                	<label  for="form_control_1"> <?php echo lang('expenses_payments'); ?></label>
	                </div>
	                <span for="f_payment_type" id="email2-error" class="help-block help-block-error has-error"></span>
	            </div>
	            <?php echo form_hidden('redirect', $redirect_code); ?>
	            <?php
				//Only allow removal from register for NEW expenses
				if ($this->config->item('track_cash') && !$expense_info->id)
				{
				?>	
					<div class="row">
						<div class="form-group">
						<?php echo form_label(lang('common_remove_cash_from_register').' :', 'cash_register_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
									<?php echo form_dropdown('cash_register_id', $registers, '' , 'id="cash_register_id" class=""'); ?>
							</div>
						</div>
					
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo anchor(site_url('sales/open_drawer'), '<i class="ion-android-open"></i> '.lang('common_pop_open_cash_drawer'),array('class'=>'', 'target' => '_blank')); ?>
						</div>
					
					</div>
				<script type="text/javascript">
					$( document ).ready(function() {
						$('body').on('change','#cash_register_id',function(){
							var cash_value = $('#cash_register_id').val();
							if(cash_value > 0)
								$('#payment_type').val('Tiền mặt');

						});
					});
				</script>
				<?php } ?>

				<div class="form-actions pull-right">
					<?php
					echo form_submit(array(
						'name'=>'submitf',
						'id'=>'submitf',
						'value'=>lang('common_submit'),
						'class'=>'btn btn-primary submit_button btn-large')
						);
						?>
				</div>
		<?php echo form_close(); ?>
	        
	    </div>
	
	</div> <!-- end col-md-12 -->
</div>

<script type='text/javascript'>

	function load_receiver_section(options) {
	    var expense_id = <?php echo $expense_id; ?>;
	    $.ajax({
	        type: "POST",
	        url: BASE_URL + 'expenses/load_receiver_section',
	        data: {
				options : options,
	            expense_id : expense_id
	        },
	        success: function(html){
	            $('#receiver_section').html(html);
	        }
	    });
	}

	var submitting = false;
	//validation and submit handling
	$(document).ready(function()
	{
		load_receiver_section('<?php echo $expense_option; ?>');

	    $('body').on('change','#expenses_options',function(){
	        var options = $('#expenses_options').val();
	        load_receiver_section(options);
	    });

		$('#category_id').selectize({
			create: true,
			render: {
		      option_create: function(data, escape) {
					var add_new = <?php echo json_encode(lang('common_new_category')) ?>;
		        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
		      }
			}
		});
		 
		function kiem_tra_truoc_khi_hoan_thanh(data){

			$form = $('#expenses_form');
			$data_form = $form.serialize();
				$.ajax({
		           	type: "POST",
		            url: 'expenses/kiem_tra_truoc_khi_luu_hoan_thanh/',
		            dataType: 'json',
		            async: false,
		            data: $data_form,
		            success: function (response) {
		            	if(response.flag) {
		            		res = response;
		            		show_feedback(response.success ? 'success' : 'error',response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
		            	} else {
		            		res = response;
		            		$('.form-md-line-input').removeClass('has-error');
			            	$('.form-md-line-input').addClass('has-success');
			            	$('span[for').addClass('hidden');
			                $.each(response.errors, function( index, value ) {
				                element = $( '#f_'+index ); 
				                element.addClass('has-error');
				                group = element.closest('.form-group');
				                group.find('span[for="f_'+index+'"]').removeClass('hidden');
			                	group.find('span[for="f_'+index+'"]').text(value);
				            	$("html, body").animate({ scrollTop: 0 }, "slow");
				            });
		            		show_feedback(response.success ? 'success' : 'error',response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
		            	}
		            },
		            error: function (response) {
		                show_feedback(false ? 'success' : 'error','Liên hệ với 4biz để được khắc phục sớm nhất',response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode('Lỗi cơ sở dữ liệu'); ?>);
		            },
	        	});

			return res.flag;
            

		}   

	    $("#submitf").click(function(e)
			{
				e.preventDefault();
				$form = $('#expenses_form');
				$data_form = $form.serialize();
				console.log($data_form);
	            $form.ajaxSubmit({
	                // You can change the url option to desired target
	                url: 'expenses/save/<?php echo $expense_info->id; ?>',
	                dataType: 'json',
	                data: $data_form,
	                beforeSubmit: kiem_tra_truoc_khi_hoan_thanh,
	                success: function(response) {
	                    // Process the response returned by the server ...
	                    show_feedback(response.success ? 'success' : 'error',response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
	                    window.location.reload();
	                    window.location="./expenses";
	                }
	            });
				return false;
			
			});

		var JS_DATE_FORMAT ='DD-MM-YYYY';
		date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
  		// date_time_picker_field($('.datepicker'), DATE_FORMAT);

		$('#expenses_amount_input').autoNumeric('init', { mDec: <?php echo $this->config->item('number_of_decimals')?>, aDec: '<?php echo $this->config->item('decimal_point'); ?>', aSep: '<?php echo $this->config->item('thousands_separator'); ?>'});
		$('#expenses_tax_input').autoNumeric('init', { mDec: <?php echo $this->config->item('number_of_decimals')?>, aDec: '<?php echo $this->config->item('decimal_point'); ?>', aSep: '<?php echo $this->config->item('thousands_separator'); ?>'});

		$("#cash_register_id").select2();
		var sale_prefix = '<?php echo $this->config->item('sale_prefix'); ?>';
		var receive_prefix  = '<?php echo $this->config->item('receive_prefix'); ?>';

	})
</script>
<div class="modal fade box-modal" id="quick_modal">
</div>
<div id="my_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
</div>
<div id="my_table" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
</div>
<?php $this->load->view('partial/footer')?>
