<?php $this->load->view("partial/header"); ?>
<?php
$linkRedirect = base_url() . 'customers/manage_mail';
if($page > 1)
    $linkRedirect = $linkRedirect . '/' . $page;
?>

<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display: none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	
	<div class="col-md-12">
		<?php echo form_open('', array('id' => 'manage_mail_form','class'=>'form-horizontal')); ?>
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i> 
		                    <?php echo lang('common_list_of').' '.lang('module_customers_mail'); ?>
							<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
				</h3>
			</div>
			  <h3 class="panel-title" style="padding:15px 20px;"><?php echo lang('customers_quotes_contract_replate_info');?></h3>
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
														<li class="li_char">- {BANGCHU} : Tổng tiền bằng chữ</li>
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
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
							<?php	echo form_label(lang('customers_manage_mail_title').' :', 'title',array('class'=>'required col-sm-3 col-md-3 col-lg-2')); ?>
						<div class="form-group">
							
								<div class="col-sm-12 col-md-12 col-lg-12" >
									<?php echo form_input(array(
                                            'name'=>'mail_title',
                                            'id'=>'mail_title',
																						'class'=>'form-control',
                                            'value'=>$mail_info->mail_title)
                                    );?>
                   <span for="mail_title" class="text-danger errors"></span>
								</div>
						</div>
						
					
						<?php echo form_label(lang('customers_manage_mail_content').' :', 'content',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 ')); ?>
						<div class="form-group">
								<div class="col-sm-12 col-md-12 col-lg-12">
									<?php echo form_textarea(array(
                                            'name'=>'mail_content',
                                            'id'=>'mail_content',
                                            'class'=>'form-control text-area',
                                            'value'=>$mail_info->mail_content)
                                    );?>
                                    <?php echo display_ckeditor($ckeditor);?>
									<div style="margin-top: 5px;">
										<span for="mail_content" class="text-danger errors"></span>
									</div>
								</div>
						</div>
					</div>
					<div class="col-sm-12 col-md-12 col-lg-12">
						<div class= "col-xs-12 col-sm-12 col-md-5 col-lg-3">
							<input type="checkbox" name="chk_temp_for_birth" id="chk_temp_for_birth" value="1">
							<label for="chk_temp_for_birth"><span></span></label>	
							<label for="chk_active">Mẫu cho sinh nhật</label>	
						
						</div>	
						<div class= "col-xs-12 col-sm-12 col-md-7 col-lg-6">
							<input type="checkbox" name="chk_temp_for_no" id="chk_temp_for_no" value="1">
							<label for="chk_temp_for_no"><span></span></label>	
							<label for="chk_temp_for_no">Mẫu cho hết hạn hợp đồng </label>
						</div>	
					</div>	
				</div>
				
				<div class="form-actions pull-right">
					<?php
					echo form_button(array(
					    'name'  	=> 'cancel',
					    'id' 			=> 'cancel',
							'class'   => 'submit_button btn btn-danger',
					    'value'	  => 'true',
					    'content' => lang('common_cancel')
					));
					?>
					
					 <?php
                     echo form_hidden('row_id', $mail_info->mail_id);
					 ?>
					 <input type="button" name="submit" value="<?php echo lang('common_submit'); ?>" id="btn_mail" style="margin-right:10px" class="submit_button float_right btn btn-primary">
				</div>
			</div>
		</div>
			<?php echo form_close();?>
	</div>
	<!-- /row -->
</div>
</div>
<script type='text/javascript'>
  CKEDITOR.config.allowedContent = true;
  CKEDITOR.config.removeFormatAttributes = '';
  CKEDITOR.config.extraPlugins = 'dialogadvtab';
  function CK_jQ() {
	 for (instance in CKEDITOR.instances) {
	     CKEDITOR.instances[instance].updateElement();
	 }
  }

  function reset_form() {
      $('#mail_title').val('');
   	  for ( instance in CKEDITOR.instances ){
	 	    CKEDITOR.instances[instance].setData('');
	  }
  }

  function mailData(data) {
	  if(data.flag == 'false') {
		    var first_key = Object.keys(data.errors)[0];
			$.each(data.errors, function( index, value ) {	
				element = $( '[name="'+index+'"]' );
				group = element.closest('.form-group');
				group.addClass('has-error');
				group.find('span[for="'+index+'"]').text(value);
			});	

			$( '[name="'+first_key+'"]' ).focus();
	 }else {
		 window.location.href = '<?php echo $linkRedirect; ?>';
	 }
  }
	
	
  $(document).ready(function()
  {			
	
	    
      $( "#btn_mail" ).click(function() {
        	$('.has-error').removeClass('has-error');
        	$('span.errors').text('');
        	CK_jQ();
        	var checkOptions = {
  			        url : BASE_URL + 'customers/save_mail_template/<?php echo $mail_info->mail_id; ?>',
  			        dataType: "json",  
  			        success: mailData
  			    };
  		    $("#manage_mail_form").ajaxSubmit(checkOptions);	
  		    // return false; 
      	});
  });
	
	
	
</script>
<style type="text/css">
   #schedule0{ height:100px;}
	 @media(max-width:767)
	 {
		 .nav-tabs li {
			 float:none;
			 border:1px solid transparent;
			 
		 }
		 .nav-tabs li.active a{
			 border:none;
		 }
		 .nav li a:hover, .nav li a:focus, .nav-tabs li.active a, .nav-tabs li.active a:hover, nav-tabs li.active a:focus
		 {
			 background:none;
			 border:none;
		 }
		 
	 }
</style>
<?php $this->load->view("partial/footer"); ?>