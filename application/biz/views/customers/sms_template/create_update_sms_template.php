<?php $this->load->view("partial/header"); ?>

<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display: none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>

	<div class="col-md-12">
			<?php echo form_open('customers/save_sms/'.$info_sms->id,array('id'=>'sms_form','class'=>'form-horizontal')); 	?>

			<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i> 
		                    <?php echo lang("customers_sms_basic_information"); ?>
							<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
					<br />
					<small>(<?php echo lang('customers_sms_note_basic_information'); ?>)</small>
				</h3>
				<ul class="nav nav-tabs">
					<li class = "active">
						<a href ="#tab1" data-toggle = "tab" >
							<h3 class="panel-title space_title">
								<?php echo lang('customers_mail_temp_words_supplier_and_customer');?>
								<span class="badge bg-primary tip-left" id="count_template_list">12</span>
							</h3>
						</a>
					</li>
					<li>
						<a href ="#tab2" data-toggle = "tab" >
							<h3 class="panel-title space_title">
								<?php echo lang('customers_mail_temp_words_contract');?>
								<span class="badge bg-primary tip-left" id="count_template_list">24</span>
							</h3>
						</a>
					</li>
					<li>
						<a href ="#tab3" data-toggle = "tab" >
							<h3 class="panel-title space_title">
								<?php echo lang('customers_mail_temp_words_invoice');?>
								<span class="badge bg-primary tip-left" id="count_template_list">17</span>
							</h3>
						</a>
					</li>
						<li>
						<a href ="#tab4" data-toggle = "tab">
							<h3 class="panel-title space_title">
								<?php echo lang('customers_mail_temp_words_company');?>
								<span class="badge bg-primary tip-left" id="count_template_list">12</span>
							</h3>
						</a>
					</li>
				</ul>
            <div style =" border:1px solid #ddd;border-top:none; " class="tab-content" >
              
                <div  id="tab1" class ="panel-body nopadding table_holder table-responsive tab-pane fade in active">
									<table>
										<tr>
											<td>
												<ul>
														<li class="li_char">- {TEN_NCC}: Tên Nhà cung cấp</li>
														<li class="li_char">- {DIA_CHI_1_NCC}: Địa chỉ 1 NCC</li>
														<li class="li_char">- {DIA_CHI_2_NCC}: Địa chỉ 2 NCC</li>
														<li class="li_char">- {SDT_NCC}: Số điện thoại NCC</li>
														<li class="li_char">- {TEN_KH}: Tên khách hàng</li>
												</ul>
											</td>
											<td>
												<ul>
													<li class="li_char">- {CT_KH}: Tên công ty KH</li>
													<li class="li_char">- {DIA_CHI_1_KH}: Địa chỉ 1 KH</li>
													<li class="li_char">- {DIA_CHI_2_KH}: Địa chỉ 2 KH</li>
													<li class="li_char">- {SDT_KH}: Số điện thoại KH</li>
													<li class="li_char">- {DD_KH}: Đại diện</li>
												</ul>	
											</td>
											<td>
												<ul>
													<li class="li_char">- {CHUCVU_KH}: Chức vụ</li>
													<li class="li_char">- {TKNH_KH}: Tài khoản ngân hàng</li>
													<li class="li_char">- {EMAIL_KH}: Địa chỉ email khách hàng</li>
												</ul>
											</td>
										</tr>
									</table>
								</div>
                <div  id="tab2" class ="tab-pane fade">
											<table class="table table_holder tablesorter table-hover table-responsive">
									<tr>
										<td>
											<ul>
												<li class="li_char">- {TEN_HD}: Tên hợp đồng</li>
												<li class="li_char">- {MA_HD}: Mã hợp đồng</li>
												<li class="li_char">- {NGAY_BĐ_HD}: Ngày bắt đầu hợp đồng</li>
												<li class="li_char">- {NGAY_KY_HD}: Ngày ký hợp đồng</li>
												<li class="li_char">- {NGAY_HET_HD}: Ngày hết hợp đồng</li>
												<li class="li_char">- DATA_GD: class của bảng thông tin giai đoạn thanh toán</li>
												<li class="li_char">- DATA_GD 1 3: lấy ra thông tin giai đoan thánh toán 1 và 3</li>
												<li class="li_char">- DATA_GH: class của bảng thông tin giao hàng</li>
											</ul>
										</td>
										<td>
											<ul>
												<li class="li_char">- {STT}: Số thư tự</li>
												<li class="li_char">- {CHIET_KHAU}: Chiết khấu</li>
												<li class="li_char">- {THUE}: Thuế</li>
												<li class="li_char">- {TEN_HH}: Tên hàng hóa</li>
												<li class="li_char">- {MA_HH}: Mã hàng hóa</li>
												<li class="li_char">- {DVT}: Đơn vị tính</li>
												<li class="li_char">- {SL}: Số lượng</li>
												<li class="li_char">- {MO_TA_HH}: Mô tả sản phẩm</li>
												<li class="li_char">- {DON_GIA}: Đơn giá</li>
											</ul>	
										</td>
										<td>
											<ul>
												<li class="li_char">- {HD_BANG_CHU}: Tổng giá trị đơn hàng bằng chữ</li>
												<li class="li_char">- {HD_BANG_SO}: Tổng giá trị đơn hàng bằng số</li>
												<li class="li_char">- {TEN_GD}: Tên giai đoạn</li>
												<li class="li_char">- {NGAY_TT_GD}: Ngày thanh toán</li>
												<li class="li_char">- {SO_TIEN_GD}: Số tiền thanh toán</li>
												<li class="li_char">- {VAT_GD}: VAT</li>
												<li class="li_char">- {CT_GH}: Công ty giao hàng</li>
												<li class="li_char">- {DD_GH}: Địa điểm giao hàng</li>
												<li class="li_char">- {TG_GH}: Thời gian giao hàng</li>
											</ul>
										</td>
									</tr>
									</table>
								</div>
                <div  id="tab3" class ="tab-pane fade">
									<table class="table table_holder tablesorter table-hover table-responsive">
											<tr>
												<td>
													<ul>
														<li class="li_char">- {ORDER_CODE}: Mã hóa đơn</li>
														<li class="li_char">- {STT}: STT</li>
														<li class="li_char">- {MA_HH}: Mã HH</li>
														<li class="li_char">- {TEN_HH}: Tên HH, DV</li>
														<li class="li_char">- {DVT}: ĐVT</li>
														<li class="li_char">- {SL}: Số lượng</li>
														<li class="li_char">- {MO_TA_HH}: Mô tả sản phẩm</li>
                                                        <li class="li_char">- {GIAM_GIA}: Giảm giá</li>
													</ul>
												</td>
												<td>
													<ul>
														<li class="li_char">- {DON_GIA}: Đơn giá</li>
														<li class="li_char">- {CHIET_KHAU}: Chiết khấu</li>
														<li class="li_char">- {DG-CK}: Đơn giá - Chiết khấu</li>
														<li class="li_char">- {THUE}: Thuế</li>
														<li class="li_char">- {THANH_TIEN}: Thành tiền</li>
														<li class="li_char">- {TONG_TIEN}: Tổng tiền</li>
													</ul>	
												</td>
												<td>
													<ul>
														<li class="li_char">- {TONG_DH}: Tổng giá trị đơn hàng</li>
														<li class="li_char">- {TIEN_DA_THANH_TOAN}: Tiền đã thanh toán</li>
														<li class="li_char">- {TIEN_TRA_LAI}: Tiền trả lại</li>
														<li class="li_char">- {VAT}: VAT</li>
														<li class="li_char">- {DATE} - {MONTH} - {YEAR} : Ngày - Tháng - Năm Hiện tại</li>
													</ul>
												</td>
											</tr>
										</table>
								</div>
                  <div  id="tab4" class ="tab-pane fade">
										<table class="table table_holder tablesorter table-hover table-responsive">
												<tr>
													<td>
														<ul>
															<li class="li_char">- {LOGO}: Logo</li>
															<li class="li_char">- {NAME_COMPANY}: Tên CT</li>
															<li class="li_char">- {ADDRESS_COMPANY}: Địa chỉ CT</li>
															<li class="li_char">- {EMAIL_COMPANY}: Email CT</li>
														</ul>
													</td>
													<td>
														<ul>
															<li class="li_char">- {TEL_COMPANY}: Điện thoại CT</li>
															<li class="li_char">- {FAX_COMPANY}: Fax</li>
															<li class="li_char">- {WEBSITE_COMPANY}: Website</li>
															<li class="li_char">- {SALE_OFFICE_COMPANY}: Điểm giao dịch</li>
														</ul>	
													</td>
													<td>
														<ul>
															<li class="li_char">- {ACCOUNT_BANK_COMPANY}: Tài khoản ngân hàng</li>
															<li class="li_char">- {SALE_EMP_NAME}: Tên NV bán hàng</li>
															<li class="li_char">- {SALE_EMP_PHONE}: Điện thoại NV bán hàng</li>
															<li class="li_char">- {SALE_EMP_EMAIL}: Mail nhân viên bán hàng</li>
														</ul>
													</td>
												</tr>
											</table>				
									</div>
            </div>
			</div>
			<div class="panel-body">
			<!--<div class="col-sm-12 col-md-12 col-lg-12">
						<div class= "col-xs-12 col-sm-12 col-md-5 col-lg-3">
							<input type="checkbox" name="chk_temp_for_birth" id="chk_temp_for_birth" value="1">
							<label for="chk_temp_for_birth"><span></span></label>	
							<label for="chk_active">Mẫu cho sinh nhật</label>	
						
						</div>	
						<div class= "col-xs-12 col-sm-12 col-md-7 col-lg-6">
							<input type="checkbox" name="chk_temp_for_no" id="chk_temp_for_no" value="1">
							<label for="chk_temp_for_no"><span></span></label>	
							<label for="chk_temp_for_birth">Mẫu cho hết hạn hợp đồng </label>
						</div>	
					</div>	-->
					<div class="clearfix"></div>
				<div class="row">
					<div class="col-md-12">
						<?php echo form_label(lang('customers_sms_title').' :', 'title',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2')); ?>
						<div class="form-group">
						
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control',
										'name'=>'sms_title',
										'id'=>'sms_title',
										'value'=>$info_sms->title)
									);?>
								</div>
						</div>
						<?php echo form_label(lang('customers_sms_description').' :', 'message',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2  ')); ?>
						<div class="form-group">
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_textarea(array(
										'name'=>'sms_message',
										'id'=>'sms_message',
										'class'=>'form-control text-area',
										'value'=>$info_sms->message,
										'rows'=>'5',
										'cols'=>'17',
										'onkeyup' => 'countChar(this)')		
									);?>
								</div>
						</div>

						<div class="form-group">
								<?php 
								echo form_label(lang('customers_sms_num_character').' :', 'title',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'sms_num_char',
										'id'=>'sms_num_char',
										'value'=>$info_sms->number_char,
										'style' => 'width: 50px !important; border: none; text-align: center;',
										'readonly' => 'readonly')
									);?>
								</div>
						</div>

						<div class="form-group">
								<?php 
								echo form_label(lang('customers_sms_num_message').' :', 'title',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'sms_num_mess',
										'id'=>'sms_num_mess',
										'value'=>$info_sms->number_message,
										'style' => 'width: 50px !important; border: none; text-align: center;',
										'readonly' => 'readonly')
									);?>
								</div>
						</div>

					</div>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-12">
				<?php echo form_hidden('redirect', $redirect); ?>
				<div class="form-actions pull-right">
					<?php
					echo form_button(array(
					    'name' => 'cancel',
					    'id' => 'cancel',
						 'class' => 'submit_button btn btn-danger',
					    'value' => 'true',
					    'content' => lang('common_cancel')
					));
					?>
					
					<?php
					echo form_submit(array(
						'name'=>'submitf',
						'id'=>'submitf',
						'value'=>lang('common_submit'),
						'class'=>' submit_button btn btn-primary')
					);
					?>
				</div>
			</div>
		</div>
			<?php echo form_close();?>
	</div>
	<!-- /row -->
</div>
</div>

<script type='text/javascript'>
//validation and submit handling
$(document).ready(function(){
	$("#cancel").click(cancelCustomerAddingSMS);
    setTimeout(function(){$(":input:visible:first","#sms_form").focus();},100);
    var submitting = false;
    $('#sms_form').validate({
    	submitHandler:function(form){
            if (submitting) return;
            submitting = true;
            $(form).ajaxSubmit({
            	success:function(response){
            		submitting = false;
								show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
					
                    if(response.success){
                    	window.location.href = '<?php echo site_url('customers/manage_sms'); ?>';
                    }
                },
                dataType:'json'
            });
        },
        errorClass: "text-danger",
		errorElement: "span",
			highlight:function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
			},
        wrapper: "li",
        rules:{
            sms_title:{
                required: true,                
            },
            sms_message:{
                required: true,
                maxlength: 460
            },
        },
        messages:{
        	sms_title:{
                required: <?php echo json_encode(lang('customers_sms_title_required'));?>,                
            },
            sms_message:{
                required: <?php echo json_encode(lang('customers_sms_title_required'));?>,
                maxlength: <?php echo json_encode(lang('customers_sms_message_maxlength'));?>
            },
        }
    });
    
});
function countChar(input){
    var len = input.value.length;
    $("#sms_num_char").val(len);
    if(len <= 156){
        $("#sms_num_mess").val(1);
    }else{
        var number_mess = (1 + Math.ceil((len - 156)/152));
        $("#sms_num_mess").val(number_mess);
    }
}

function cancelCustomerAddingSMS()
{
	bootbox.confirm(<?php echo json_encode(lang('customers_sms_are_you_sure_cancel')); ?>, function(response)
	{
		if (response)
		{
			window.location = <?php echo json_encode(site_url('customers/manage_sms')); ?>;
		}
	});
}
</script>

<?php $this->load->view("partial/footer"); ?>