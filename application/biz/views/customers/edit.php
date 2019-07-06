<?php $this->load->view("partial/header");?>

<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display:none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	<!-- form đăng ký thêm mới khách hàng?   -->
	<?php echo form_open_multipart('customers/save/'.$person_info->person_id,array('id'=>'customer_form','class'=>'form-horizontal')); 	?>
	<?php echo validation_errors(); ?>
	<div class="panel panel-piluku">
		<div class="col-md-5"><!-- col-md-5 open -->
			<div class="tab-content">
				<div class="panel-heading header-tab">
					<h3 class="panel-title">
						<i class="ion-edit"></i> 
						<?php echo lang("customers_basic_information"); ?>
						<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
					</h3>
				</div>

				<!-- kiểm tra phân quyền   -->    
				<?php if($person_info->person_id)  { ?>
					<div class="panel">
						<div class="panel-body">
							<div class="user-badge">
								
								<div class="user-badge-details">
									<?php echo $person_info->first_name.' '.$person_info->last_name; ?>
									<?php if($this->config->item('customers_store_accounts')) { ?>
										<div class="amount">
											<?php echo lang('customers_store_account_balance').': '; ?>
											<?php echo $person_info->balance ? to_currency($person_info->balance) : '0.00'; ?>
										</div>
									<?php } ?>
									<?php
									if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
									{
										?>
										<div class="amount">								
											<?php echo lang('common_sales_until_discount').': '; ?>
											<?php 
											$sales_until_discount = $this->config->item('number_of_sales_for_discount') - $person_info->current_sales_for_discount;
											
											echo to_quantity($sales_until_discount); ?>
										</div>
										
										<?php
									}
									?>

									<?php
									if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
									{
										list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);

										?>
										<div class="amount">
											<?php echo lang('common_points').': '; ?>
											<?php echo to_quantity($person_info->points); ?>
										</div>
										
										<div class="amount">
											<?php echo lang('customers_amount_to_spend_for_next_point').': '; ?>
											<?php echo to_currency($spend_amount_for_points - $person_info->current_spend_for_points); ?>
										</div>								
										
										<?php
									}
									?>
								</div>
								<!--								<ul class="list-inline pull-right">-->
									<!--									--><?php
//										$six_months_ago = date('Y-m-d', strtotime('-6 months'));
//										$today = date('Y-m-d').'%2023:59:59';
//									?>
<!--									<li><a href="--><?php //echo site_url('reports/specific_customer/'.$six_months_ago.'/'.$today.'/'.$person_info->person_id.'/all/0'); ?><!--" class="btn btn-success">--><?php //echo lang('common_view_report'); ?><!--</a></li>-->
<!--									--><?php //if ($person_info->email) { ?>
	<!--										<li><a id="send_mail" class="btn btn-primary">--><?php //echo lang('common_send_email'); ?><!--</a></li>-->
	<!--									--><?php //} ?>
	<!--								</ul>-->
</div>
</div>
</div>
<?php } ?>

<div class="panel-body">
	<?php $this->load->view("people/form_basic_info"); ?>
</div>
</div>
</div><!-- col-md-5 close -->




<!-- load view tab col-md-7 -->
<?php $this->load->view('people/form_tab_new_info') ?>
<!-- submit buttom -->
<?php if($person_info->cc_token && $person_info->cc_preview) { ?>
	<div class="control-group">	
		<?php echo form_label(lang('customers_delete_cc_info').'', 'delete_cc_info',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
		<div class="col-sm-9 col-md-9 col-lg-10">
			<?php echo form_checkbox('delete_cc_info', '1', FALSE, 'id="delete_cc_info"');?>
			<label for="delete_cc_info"><span></span></label>
		</div>
	</div>
<?php } ?>

<?php echo form_hidden('redirect_code', $redirect_code); ?>

<div class="form-actions pull-right">
	<?php
	if ($redirect_code == 1)
	{
		echo form_button(array(
			'name' => 'cancel',
			'id' => 'cancel',
			'class' => 'submit_button btn btn-danger',
			'value' => 'true',
			'content' => lang('common_cancel')
		));

	}
	?>
	<?php 
	if (!$redirect_code == 2) 
	{ 
		?>
		<input type="submit" name="submitf" value="Lưu" id="submitf" onclick="action_button(1)" class="submit_button btn btn-primary">
		<?php 
	} 
	?>

	<?php
	if ($redirect_code == 2 || $redirect_code == 1) {
		echo form_submit(array(
			'name'=>'submitf',
			'id'=>'submitf1',
			'value'=>'Lưu đóng',
			'class'=>' submit_button btn btn-primary')
	);
	}
	
	?>
</div>
</div> <!-- end md-7 -->
</form>
<?php echo form_close(); ?>
</div> <!-- end panel panel-piluku -->

</div><!-- /row -->	

<div class="modal fade box-modal" id="quick_modal">
</div>

<?php  
	if ($redirect_code == 3) {
		?>
		<style type="text/css">
			.selectize-control.multi .selectize-input.disabled > div{
				color: #000;
			}
		</style>
		<script type='text/javascript'>
			$(document).ready(function() {
				$('input').prop('disabled',true);
				$('select[name="created_by"]').prop('disabled',true);
				$('select').prop('disabled',true);
				$('textarea').prop('disabled',true);
				$('button').prop('disabled',true);

			});
		</script>
		<?php
	}
?>
<script type='text/javascript'>
	$('#send_mail').on('click',function(e){
		e.preventDefault();

		$.ajax({
			type: "GET",
			url: BASE_URL + 'customers/send_mail',
			success: function(html){
				$('#quick_modal').addClass('size-700');
				$('#quick_modal').html(html);
				$('#quick_modal').modal('toggle');
			}
		});
	});
	function send_mail() {
		var template_email_id = $('#template_email_id').val();
		if(template_email_id == -1)
			toastr.warning('Bạn phải chọn một mẫu email!', 'Cảnh báo');
		else {
			var segment = window.location.pathname.split( '/' );
			var cid  = new Array(segment[4]);
			$.ajax({
				type: "POST",
				url: BASE_URL + 'customers/do_send_mail',
				data: {
					cid                 : cid,
					template_email_id   : template_email_id
				},
				beforeSend: function() {
					$('.mask').show();
				},
				success: function(string){
					$('.mask').hide();
					var result = JSON.parse(string);

					if(result['flag'] == 'false')
						toastr.error(result['msg'], 'Lỗi');
					else{
						toastr.success(result['msg'], 'Thông báo');
					}

					$('#quick_modal').modal('toggle');
				}
			});
		}
	}
	// $('#geographical_area').selectize({
	// 	 create: true,
 //    	 sortField: 'text'
		// create: function(input) {
		// 	return {
		// 		value:input,
		// 		text: input
		// 	}
		// },
	// });
	// $('#business_type').selectize({
	// 	 create: true,
 //    	 sortField: 'text'
		// create: function(input) {
		// 	return {
		// 		value:input,
		// 		text: input
		// 	}
		// }
	// });
	$('#exchange_form').selectize({
		plugins: ['remove_button'],
		delimiter: ',',
		persist: false,
		// create: function(input) {
		// 	return {
		// 		value:input,
		// 		text: input
		// 	}
		// }
	});

	// thêm dòng mới mỗi lần bấm click
	function them_dong_moi(){
		// tăng dần giá trị cho input
		$('#them_dong_moi tr input[name="stt"]').each(function(ind) {
			$(this).val(ind +1);
		});
		// lấy giá trị mới nhất để hiển thị
		var dem = $('#them_dong_moi tr input[name="stt"]:last').val();
		if (dem==null) {
			dem=0;
		}
		$('#them_dong_moi').append('<?php echo $table_thong_tin_dau_moi; ?>');
		$('#them_dong_moi tr:last-child td:first-child').html('<input type="text" name="stt" value="'+(parseInt(dem)+1)+'" class="form-control input-sm">');
	}
	// xóa dòng đang hiển thị
	$('table').on('click','.xoa_dong',function(){
		$(this).parent().parent().remove();
		$('#them_dong_moi tr input[name="stt"]').each(function(ind) {
			$(this).val(ind +1);
		});

	})

	date_time_picker_field($('.datepicker'), 'DD-MM-YYYY');
	$(".override_default_tax_checkbox").change(function(){
		$(this).parent().parent().next().toggleClass('hidden')
	});
	
	check_taxable();
	$("#taxable").change(check_taxable);
	
	function check_taxable(){
		if ($("#taxable").prop('checked'))
		{
			$("#tax_certificate_holder").hide();
		}
		else
		{
			$("#tax_certificate_holder").show();
		}
	}

	$('#image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

	//validation and submit handling

	$(document).ready(function()
	{
        // authorized_capital
        $('#authorized_capital').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '99999999999999999',});
        $('#total_assets').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '99999999999999999'});
        $('#total_revenue').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '99999999999999999'});
        $('#total_profit').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '99999999999999999'});
        $("#cancel").click(cancelCustomerAddingFromSale);

        setTimeout(function(){$(":input:visible:first","#customer_form").focus();},100);
        $('#customer_form').validate({
        	submitHandler:function(form)
        	{
        		$.post('<?php echo site_url("customers/check_duplicate");?>', {
        			name: $('#last_name').val(), 
        			id : <?php echo ($person_info->person_id) ? $person_info->person_id : -1; ?>
        		},
        		function(data) {
        			var action_button_value = data.action_button;
					// nếu kết quả trả về là true, => last_name đã tồn tại

					if(data.duplicate) {
						bootbox.confirm('Một khách hàng có tên '+$('#last_name').val()+' đã tồn tại bạn có muốn tiếp tục lưu không', function(result)
						{
							if (result)
							{
								doCustomerSubmit(form,action_button_value);
							} 
							element = $( '[name="last_name"]' );  
							group = element.closest('.form-group');
							group.addClass('has-error');
							$('span[for="last_name"]').removeAttr('class')
							$('span[for="last_name"]').attr('class','alert-warning')
							group.find('span[for="last_name"]').text('Khách hàng này đã tồn tại');
							$("html, body").animate({ scrollTop: 0 }, "slow");
						})
					}else{

						show_feedback('success','Wait a Second...');
						doCustomerSubmit(form,action_button_value);
					}
					
				} , "json")
        		.error(function() { 
        		});

        	}
        });


    });



	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}

	function doCustomerSubmit(form,action_button_value = false)
	{
		$('.has-error').removeClass('has-error');
		$('span.errors').text('');

		var checkOptions = {
			data: {
				action_button_value : action_button_value,
				id : <?php echo ($person_info->person_id) ? $person_info->person_id : -1; ?>
			},
			url : '<?php echo 'customers/kiem_tra_truoc_khi_luu_khach_hang/'; ?>',
			dataType: "json",
			success: thong_bao_loi
		};

    // kiểm tra vailidate trước khi save
    $("#customer_form").ajaxSubmit(checkOptions);

    function thong_bao_loi(data) {
    	if(data.flag == false) {
    		$.each(data.errors, function( index, value ) {
    			element = $( '[name="'+index+'"]' );  
    			group = element.closest('.form-group');
    			group.addClass('has-error');
    			group.find('span[for="'+index+'"]').text(value);
                // console.log(value);
                $("#grid-loader").hide();
                $("html, body").animate({ scrollTop: 0 }, "slow");
            });
    		show_feedback(data.success ? 'success' : 'error',data.message,data.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
    	} else {
        	// kiểm tra nếu đồng ý save sẽ chạy $(form).ajaxSubmit
        	$("#grid-loader").show();
        	$(form).ajaxSubmit({
        		success:function(response)
        		{
        			$("#grid-loader").hide();
        			show_feedback(response.success ? 'success' : 'error',response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

        			if(response.redirect_code==1 && response.success)
        			{ 
        				$.post('<?php echo site_url("sales/select_customer");?>', {customer: response.person_id}, function()
        				{
        					window.location.href = '<?php echo site_url('sales/index/1'); ?>';
        				});
        			}
        			else if(response.redirect_code==2 && response.success)
        			{
        				window.location.href = '<?php echo site_url('customers'); ?>';
        			}
        			else
        			{
        				if(data.action_button_value) {
							// $("html, body").animate({ scrollTop: 0 }, "slow");
							location.reload();
						} else {
							window.location.href = '<?php echo site_url('customers'); ?>';
						}
					}
				},
				<?php if(!$person_info->person_id) { ?>
					resetForm: false,
				<?php } ?>
				dataType:'json'
			});
        }	
    }

}

function cancelCustomerAddingFromSale()
{
	bootbox.confirm(<?php echo json_encode(lang('customers_are_you_sure_cadncel')); ?>, function(response)
	{
		if (response)
		{
			window.location = <?php echo json_encode(site_url('sales')); ?>;
		}
	});
}

function action_button($what_action = null){
	
	$.ajax({
		url: '<?php echo site_url("customers/action_button");?>',
		type: 'GET',
		data: {
			action_button: $what_action
		},
		success : function(){

		}
	})	
}
$('.mytext').popover();
</script>

<?php $this->load->view("partial/footer"); ?>
