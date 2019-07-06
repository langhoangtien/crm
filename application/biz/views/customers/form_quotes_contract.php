<?php $this->load->view("partial/header");


    $title   = isset($item['title_quotes_contract'])?$item['title_quotes_contract']:'';
    $cat_id   = isset($item['cat_quotes_contract'])?$item['cat_quotes_contract']:'';
    $content  = isset($item['content_quotes_contract'])?$item['content_quotes_contract']:'';

    $linkRedirect = base_url() . 'customers/quotes_contract';
    if(isset($page) && $page > 1)
        $linkRedirect = $linkRedirect . '/' . $page;

?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.css">
 
    <!-- Include Editor style. -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@2.9.3/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/froala-editor@2.9.3/css/froala_style.min.css" rel="stylesheet" type="text/css" />

<div class="row" id="form">
    <div class="spinner" id="grid-loader" style="display: none">
        <div class="rect1"></div>
        <div class="rect2"></div>
        <div class="rect3"></div>
    </div>

    <div class="col-md-12">
        <?php echo form_open('', array('id' => 'quotes_contract_form23','class'=>'form-horizontal')); ?>
        <input type="hidden" name="id" value="<?php echo $id;?>" />
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="ion-edit"></i>
                    <?php echo lang('common_list_of').' '.lang('module_customers_quotes_contract'); ?>
                    <small>(<?php echo lang('common_fields_required_message'); ?>)</small>
                </h3>
            </div>
            <!-- <fieldset id="item_basic_info"> -->
                <h3 class="panel-title" style="padding:15px 20px;"><?php echo lang('customers_quotes_contract_replate_info');?></h3>
               <ul class="nav nav-tabs">
                   <li class = "active">
                        <a href ="#tab1" data-toggle = "tab" >
                            <h3 class="panel-title space_title">
                                <?php echo lang('customers_quotes_contract_temp_words_supplier_and_customer');?>
                                <span class="badge bg-primary tip-left" id="count_template_list">13</span>
                            </h3>
                        </a>
                    </li>
                    <li>
                        <a href ="#tab2" data-toggle = "tab" >
                            <h3 class="panel-title space_title">
                                <?php echo lang('customers_quotes_contract_temp_words_contract');?>
                                <span class="badge bg-primary tip-left" id="count_template_list">24</span>
                            </h3>
                        </a>
                    </li>
                    <li>
                        <a href ="#tab3" data-toggle = "tab" >
                            <h3 class="panel-title space_title">
                                <?php echo lang('customers_quotes_contract_temp_words_invoice');?>
                                <span class="badge bg-primary tip-left" id="count_template_list">18</span>
                            </h3>
                        </a>
                    </li>
                    <li>
						<a href ="#tab4" data-toggle = "tab">
								<h3 class="panel-title space_title">
										<?php echo lang('customers_quotes_contract_temp_words_company');?>
										<span class="badge bg-primary tip-left" id="count_template_list">12</span>
								</h3>
						</a>
                    </li>
					<?php if($this->config->item('sales_add_more_cus_for_visa')):?>
						<li>
							<a href ="#tab5" data-toggle = "tab">
								<h3 class="panel-title space_title">
									<?php echo lang('customers_service');?>
									<span class="badge bg-primary tip-left" >1</span>
								</h3>
							</a>
						</li>
      	<?php endif;?>
               </ul>  
            <!-- </fieldset> -->
            <div style =" border:1px solid #ddd;border-top:none;" class="tab-content">
                <div id="tab1" class ="panel-body nopadding table_holder table-responsive tab-pane fade in active">
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

                <div id="tab2" class ="tab-pane fade">
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

                <div id="tab3" class ="tab-pane fade">
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
																												<li class="li_char">- {MO_TA_HH}: Mô tả sản phẩm</li>                                                      <li class="li_char">- {GIAM_GIA}: Giảm giá</li>
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

                <div id="tab4" class ="tab-pane fade">
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
								<?php if($this->config->item('sales_add_more_cus_for_visa')):?>
									<div  id="tab5" class ="tab-pane fade">
										<table class="table table_holder tablesorter table-hover table-responsive">
												<tr>
													<td>
														<ul>
															<li class="li_char">- {MORE_CUSTOMER_IN_SERVICE}: Thêm khách hàng làm visa.</li>
														</ul>
													</td>
												</tr>
											</table>				
									</div>
									<?php endif;?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                       
                            <?php
                            echo form_label(lang('customers_quotes_contract_title').' :', 'title',array('class'=>'required col-sm-3 col-md-3 col-lg-2')); ?>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <?php echo form_input(array(
                                        'name'=>'title_quotes_contract',
                                        'id'=>'title_quotes_contract',
                                        'class'=>'form-control',
                                        'value'=> $title
                                ));
                                ?>
                                <span for="title_quotes_contract" class="text-danger errors"></span>
                            </div>
                        </div>
                       
                            <?php
                            echo form_label(lang('customers_quotes_contract_type').' :', 'type',array('class'=>'required col-sm-3 col-md-3 col-lg-2')); ?>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <select name="cat_quotes_contract" id="cat_quotes_contract" class="select_form form-control">
                                    <option value="0"><?php echo lang('customers_quotes_contract_select_quote'); ?></option>
                                    <?php
                                    if(!empty($qc_types)) {
                                        foreach($qc_types as $val) {
                                    ?>
                                            <option value="<?php echo $val['id']; ?>"<?php if($cat_id  == $val['id']) echo ' selected'; ?>><?php echo $val['title']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>

                                </select>
                                <span for="cat_quotes_contract" class="text-danger errors"></span>
                            </div>
                        </div>
                      
                            <?php
                            echo form_label(lang('customers_quotes_contract_content').' :', 'content',array('class'=>'required col-sm-3 col-md-3 col-lg-2')); ?>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <?php echo form_textarea(array(
                                        'name'=>'content_quotes_contract',
                                        'id'=>'content_quotes_contract',
                                        'class'=>'form-control text-area',
                                        'value'=>$content
                                ));?>
                              
                                <div style="margin-top: 5px;">
                                    <span for="content_quotes_contract" class="text-danger errors"></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions pull-right">
                    <input id="btn_quote" type="button" value="Thực hiện" style="width: 86px; height: 32px;" class=" submit_button btn btn-primary"/>
                </div>
            </div>
        </div>
        <?php echo form_close();?>
    </div>
    <!-- /row -->
</div>
</div>
<script type="text/javascript">
   

     
        $("#btn_quote").click(function() {
            $.ajax({
                url : '<?php echo 'customers/quotes_contract_save/'; ?>',
                dataType: "json",
                type:'POST',
                data:{
                    title_quotes_contract: $('#title_quotes_contract').val(),
                    content_quotes_contract: $('#content_quotes_contract').val(),
                    cat_quotes_contract: $('#cat_quotes_contract').val(),
                    id:<?php echo $id ?>

                },
                success: function(data){
                  if(data.flag == 'false') {
                    $.each(data.errors, function( index, value ) {
                        element = $( '[name="'+index+'"]' );
                        group = element.closest('.form-group');
                        group.addClass('has-error');
                        group.find('span[for="'+index+'"]').text(value);
                    });
                }else {
                    window.location.href = '<?php echo $linkRedirect; ?>';
                }
                }
            });
       
            // var checkOptions = {
            //     url : '<?php echo 'customers/quotes_contract_save/'; ?>',
            //     dataType: "json",
            //     success: quoteData
            // };
            // $("#quotes_contract_form23").ajaxSubmit(checkOptions);
            // return false;
        });

        // $(window).keydown(function(event){
        //     if(event.keyCode == 13) {
        //         $( "#btn_quote" ).trigger( "click" );
        //         event.preventDefault();

        //         return false;
        //     }
        // });


    function quoteData(data) {
        if(data.flag == 'false') {
            $.each(data.errors, function( index, value ) {
                element = $( '[name="'+index+'"]' );
                group = element.closest('.form-group');
                group.addClass('has-error');
                group.find('span[for="'+index+'"]').text(value);
            });
        }else {
            window.location.href = '<?php echo $linkRedirect; ?>';
        }
    }
</script>
<style type="text/css">
    /*#table_char{
        width: 90%;
        border-collapse: collapse;
        float: right;
        margin-right: 10px;
    }
    #table_char tr th{
        text-align: center;
        border: 1px solid #CDCDCD;
        padding: 5px 0px;
        width: 50%;
    }

    #table_char tr td{
        padding: 5px;
        border: 1px solid #CDCDCD;
        vertical-align: top;
    }
    .li_char{
        padding: 4px 0px;
    }*/
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
   <!-- Include external JS libs. -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/mode/xml/xml.min.js"></script>
 
    <!-- Include Editor JS files. -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@2.9.3/js/froala_editor.pkgd.min.js"></script>
 
    <!-- Initialize the editor. -->
    <script> $(function() { $('textarea').froalaEditor() }); </script>
<?php $this->load->view("partial/footer"); ?>

