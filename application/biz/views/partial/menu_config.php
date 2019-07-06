
<div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
 <!-- page-quick-sidebar-wrapper -->
    
        <a href="javascript:void(0)" class="page-quick-sidebar-toggler" id="config">
            <i class="page-quick-sidebar-toggler ion ion-gear-b"></i>
        </a>


    <div class="page-quick-sidebar">
       
        <ul class="nav nav-tabs">
             
            <li class="active">
                <a href="javascript:;" data-target="#cai_dat_ban_hang" data-toggle="tab" aria-expanded="true"> Bán hàng
                    <!-- <span class="badge badge-danger">2</span> -->
                </a>
            </li>
            <li class="">
                <a href="javascript:;" data-target="#cai_dat_hoa_don" data-toggle="tab" aria-expanded="false"> Hóa đơn
                    <!-- <span class="badge badge-success">7</span> -->
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> ...
                    <!-- <i class="fa fa-angle-down"></i> -->
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a href="javascript:;" data-target="#setting_cho_cong_ty" data-toggle="tab" aria-expanded="true">
						<i class="icon-bell"></i> Cài đặt thông tin công ty </a>
                    </li>
                    <li>
                        <a href="javascript:;" data-target="#cai_dat_tai_khoan" data-toggle="tab" aria-expanded="true">
						<i class="icon-bell"></i> Cài đặt tài khoản </a>
                    </li>
  
                    <li>
                        <a href="javascript:;" data-target="#cai_dat_may_in" data-toggle="tab" aria-expanded="false">
						<i class="icon-speech"></i> Cài đặt máy in </a>
                    </li>
                    <li>
                        <a href="javascript:;" data-target="#cai_dat_hien_thi" data-toggle="tab" aria-expanded="false">
						<i class="icon-speech"></i> Cài đặt hiển thị </a>
                    </li>
                    <li class="divider"></li>
                    <li class="">
                        <a href="javascript:;" data-target="#cai_dat_he_thong" data-toggle="tab" aria-expanded="false">
						<i class="icon-settings"></i> Cài đặt hệ thống </a>
                    </li>
                </ul>
            </li>
            <form class="sidebar-search  " action="page_general_search_3.html" method="POST">

            <div class="input-group">
                <input type="text" class="form-control" id="tim-kiem-menu-config" placeholder="Tìm kiếm ...">
            </div>
        </form>
        </ul>
    <?php echo form_open_multipart('config/save/',array('id'=>'config_form','class'=>'form-horizontal', 'autocomplete'=> 'off')); ?>
        <div class="tab-content">
						<div class="tab-pane active page-quick-sidebar-settings" id="cai_dat_ban_hang">
                <div class="page-quick-sidebar-settings-list">
                    <div class="m-heading-1 border-green m-bordered">
						<h3><i class="lagger ion ion-android-cart"></i> Cài đặt bán hàng</h3>
						<p> Phần cài đặt cấu hình cho bán hàng
								<!-- <a class="btn red btn-outline" href="http://jqueryvalidation.org" target="_blank">the official documentation</a> -->
						</p>
                    </div>
                    <ul class="list-items borderless">
                        <!-- Hiển thị giao diện bán hàng -->
                        <li> 
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right"  title="Chọn ID của mặt hàng để được hiển thị trên giao diện bán hàng!">Hiển thị giao diện bán hàng</a>
                            <?php echo form_dropdown('id_to_show_on_sale_interface', 
                                array(
                                    'number'        => lang('common_item_number_expanded'),
                                    'product_id'    => lang('common_product_id'),
                                    'id'            => lang('common_item_id')
                                    ),
                                $this->config->item('id_to_show_on_sale_interface'), 'class="form-control" id="id_to_show_on_sale_interface"')
                                ?>
                        </li>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('disable_quick_complete_sale'); ?>!">Tắt hoàn thành nhanh đơn hàng</a>
                            <input name="disable_quick_complete_sale" id="disable_quick_complete_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('disable_quick_complete_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                          <!-- Xác nhận hoàn thành đơn hàng -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Tắt popup thông báo xác nhận hoàn thành đơn hàng!">Tắt xác nhận hoàn thành đơn</a>
                            <input name="disable_confirmation_sale" id="disable_confirmation_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('disable_confirmation_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                           
                        </li>
                         <li>
                            <?php echo lang('config_payment_types'); ?>
                            <p></p>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <a href="#" class="btn btn-primary payment_types"><?php echo lang('common_cash'); ?></a> 
                                <a href="#" class="btn btn-primary payment_types"><?php echo lang('common_check'); ?></a> 
                                <a href="#" class="btn btn-primary payment_types"><?php echo lang('common_giftcard'); ?></a> 
                                <a href="#" class="btn btn-primary payment_types"><?php echo lang('common_debit'); ?></a> 
                                <a href="#" class="btn btn-primary payment_types"><?php echo lang('common_credit'); ?></a>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                         <!-- Tự định nghĩa phương thức thanh toán thay thế cho lựa chọn ở trên -->
                        <li>
                             <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Tự định nghĩa phương thức thanh toán thay thế cho lựa chọn ở trên !">Thêm phương thức thanh toán</a>
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'additional_payment_types',
                                'id'=>'additional_payment_types',
                                'size'=> 40,
                                'value'=>$this->config->item('additional_payment_types')));?>
                        </li>
                         <!-- Mặc Định Kiểu Thanh Toán : -->
                        <li>
                            <?php echo lang('config_default_payment_type'); ?>
                            <?php echo form_dropdown('default_payment_type', $payment_options, $this->config->item('default_payment_type'),'class="form-control" id="default_payment_type"'); ?>
                        </li>
                         <!-- Không Thực Hiện Được Vì Tên Đã Tồn Tại -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Hiển thị nhiều sản phẩm cùng tên trên giao diện bán hàng !">Kiểm tra tên trùng lặp</a>
                             <input name="do_not_group_same_items" id="do_not_group_same_items" type="checkbox" data-on-text="Bật" <?php if($this->config->item('do_not_group_same_items')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                            
                        </li>
                            <!-- Không Cho Phép Bán Dưới Giá Vốn  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_do_not_allow_below_cost'); ?>!">Không bán hàng dưới giá vốn</a>

                             <input name="do_not_allow_below_cost" id="do_not_allow_below_cost" type="checkbox" data-on-text="Bật" <?php if($this->config->item('do_not_allow_below_cost')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                            
                        </li> 
                        <!-- Không Cho Phép, Ra Khỏi Kho Để Bán -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Không hco phết bán khi sản phẩm hết hàng tồn kho !">Không bán hàng khi hết hàng</a>
                             <input name="do_not_allow_out_of_stock_items_to_be_sold" id="do_not_allow_out_of_stock_items_to_be_sold" type="checkbox" data-on-text="Bật" <?php if($this->config->item('do_not_allow_out_of_stock_items_to_be_sold')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li> 
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Là SP có giá vốn bằng giá bán khi lập hóa đơn!">SP không có lợi nhuận</a>
                           
                            <select class="form-control" name="config_adjusted_cost_price[]" id="config_adjusted_cost_price" multiple>
                                <?php
                                if(!empty($slb_service_items)) {
                                    foreach($slb_service_items as $val) {
                                ?>
                                    <option value="<?php echo $val['item_id']; ?>"<?php if(in_array($val['item_id'], $config_adjusted_cost_price)) echo ' selected'; ?>><?php echo $val['name']; ?></option>
                                <?php
                                    }
                                }
                                ?>

                             </select>
                        </li>

                         <!-- Vô Hiệu Hóa Kiểm Tra Thẻ Quà Tặng : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_disable_giftcard_detection'); ?>!">Tắt kiểm tra thẻ quà tặng</a>
                             <input name="disable_giftcard_detection" id="disable_giftcard_detection" type="checkbox" data-on-text="Bật" <?php if($this->config->item('disable_giftcard_detection')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Tính Toán Lợi Nhuận Của Thẻ Quà Tặng Khi : -->
                        <li>
                             <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_calculate_profit_for_giftcard_when'); ?>!">Tính lợi nhuận của thẻ</a>
                             <?php echo form_dropdown('calculate_profit_for_giftcard_when', array(
                                ''  => lang('common_do_nothing'),
                                'redeeming_giftcard'   => lang('config_redeeming_giftcard'), 
                                'selling_giftcard'  => lang('config_selling_giftcard'),
                                ),
                                 $this->config->item('calculate_profit_for_giftcard_when'), 'class="form-control" id="calculate_profit_for_giftcard_when"');
                            ?>
                        </li>
                             <!-- Tài Khoản Ghi Nợ Tại Điểm Bán Hàng : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Hiển thị tài khoản ghi nợ của khách hàng khi bán hàng !">Tài khoản ghi nợ</a>
                                <input name="customers_store_accounts" id="customers_store_accounts" type="checkbox" data-on-text="Bật" <?php if($this->config->item('customers_store_accounts')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
     
                         <!-- Khóa Đơn Hàng K/H Khi Vượt Quá Hạn Mức Công Nợ  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Khóa đơn hàng khi vượt quá công nợ !">Khóa ĐH khi vượt quá công nợ</a>

                            <input name="disable_store_account_when_over_credit_limit" id="disable_store_account_when_over_credit_limit" type="checkbox" data-on-text="Bật" <?php if($this->config->item('disable_store_account_when_over_credit_limit')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                         <!-- Kích Hoạt Tính Năng, Khách Hàng Thân Thiết : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_hide_points_on_receipt'); ?>!">Ẩn điểm của khách hàng</a>
                                <input name="hide_points_on_receipt" id="hide_points_on_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_points_on_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_enable_customer_loyalty_system'); ?>!">Khách hàng thân thiết</a>
                                <input name="enable_customer_loyalty_system" id="enable_customer_loyalty_system" type="checkbox" data-on-text="Bật" <?php if($this->config->item('enable_customer_loyalty_system')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                         <!-- Lựa chọn chương trình : -->
                        <li>
                            <?php echo lang('config_loyalty_option'); ?>
                            <?php echo form_dropdown('loyalty_option', 
                             array(
                                'simple'=> lang('config_simple'),
                                'advanced'=>lang('config_advanced'),
                            ), $this->config->item('loyalty_option') ? $this->config->item('loyalty_option') : '20', 'class="form-control" id="loyalty_option"');
                                ?>
                        </li>
                        <!-- Mỗi điểm tương ứng bao nhiêu tiền : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_point_value'); ?>!">Điểm thưởng</a>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-inps',
                                'name'=>'point_value',
                                'id'=>'point_value',
                                'value'=>$this->config->item('point_value') ? to_currency_no_money($this->config->item('point_value')) : ''));?>
                        </li>
                        <!-- Tiêu chí số tiền quy ra điểm : -->
                        <li>
                            <?php
                                $spend_amount_for_points = '';
                                $points_to_earn= '';
                                if (strpos($this->config->item('spend_to_point_ratio'),':') !== FALSE)
                                {
                                list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
                                }
                            ?>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Một điểm tương ứng bao nhiêu tiền !">Điểm/tiền</a>
                            <?php echo form_input(array(
                                    'class'=>'validate form-control form-inps',
                                    'name'=>'spend_amount_for_points',
                                    'id'=>'spend_amount_for_points',
                                    'placeholder' => lang('config_loyalty_explained_spend_amount'),
                                    'value'=>$spend_amount_for_points));?>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-inps',
                                'name'=>'points_to_earn',
                                'id'=>'points_to_earn',
                                'placeholder' => lang('config_loyalty_explained_points_to_earn'),
                                'value'=>$points_to_earn));?>
                        </li>
                        <!-- Số lần mua hàng để được giảm giá : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_number_of_sales_for_discount'); ?>!">Giảm giá mua hàng</a>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-inps',
                                'name'=>'number_of_sales_for_discount',
                                'id'=>'number_of_sales_for_discount',
                                'value'=>$this->config->item('number_of_sales_for_discount')));?>
                                    
                                   
                        </li>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_discount_percent_earned'); ?>!">Phần trăm giảm giá</a>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-inps',
                                'name'=>'discount_percent_earned',
                                'id'=>'discount_percent_earned',
                                'value'=>$this->config->item('discount_percent_earned')));?>
                        </li>
                        <li>
                            Số lượng mặc định bán hàng
                            <?php echo form_input(array(
                                'class'=>'valid form-control form-inps',
                                'type' => 'text',
                                'name'=>'config_default_sale_quantity',
                                'id'=>'config_default_sale_quantity',
                                'value'=>$this->config->item('config_default_sale_quantity')));?>

                        </li>
                        <li>
                            Hóa đơn VAT
                            <select class="form-control" name="config_vat_order" id="config_vat_order">
                                <option value="0"<?php if($this->config->item('config_vat_order') == 0) echo ' selected'; ?>>Không</option>
                                <option value="1"<?php if($this->config->item('config_vat_order') == 1) echo ' selected'; ?>>Có</option>
                             </select>
                         </li>
                         <!-- Thay Đổi Ngày Bán Hàng, Khi Hoàn Thành Đơn Hàng Đặt Hàng : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Thay đổi ngày bán hàng khi hoàn thành!">Thay đổi ngày bán hàng</a>
                                <input name="change_sale_date_when_completing_suspended_sale" id="change_sale_date_when_completing_suspended_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('change_sale_date_when_completing_suspended_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Thay Đổi Ngày Bán Với Đơn Hàng Mới -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_change_sale_date_for_new_sale'); ?>!">Thay đổi số ngày bán</a>
                             <input name="change_sale_date_for_new_sale" id="change_sale_date_for_new_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('change_sale_date_for_new_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
												<li>
													 <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right"><?php echo lang('config_sales_add_more_cus_for_visa')?></a>
													 <input name="config_sales_add_more_cus_for_visa" id="config_sales_add_more_cus_for_visa" type="checkbox" data-on-text="Bật" <?php if($this->config->item('sales_add_more_cus_for_visa')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
								        </li>        
												</ul>

						<div class="m-heading-1 border-green m-bordered">
								<h3><i class="lagger ion ion-erlenmeyer-flask"></i> Cài đặt đặt hàng</h3>
								<p> Phần cài đặt cấu hình cho đặt hàng
								</p>
						</div>
  
						<ul class="list-items borderless">
                            <li>
                                <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Thay đổi ngày khi đặt hàng!">Thay đổi ngày khi đặt hàng</a>
                                    <input name="change_sale_date_when_suspending" id="change_sale_date_when_suspending" type="checkbox" data-on-text="Bật" <?php if($this->config->item('change_sale_date_when_suspending')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                            </li>
                             <!-- Cảnh Báo Các Đơn Đặt Hàng Quá Hạn : -->
                            <li>
                                <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('common_show_warning_modal_order_sale'); ?>!">Cảnh báo đơn hàng quá hạn</a>

                                 <input name="show_warning_modal_order_sale" id="show_warning_modal_order_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('show_warning_modal_order_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                            </li>    
                           
                            <!-- Thời Gian Cảnh Báo Level 1: -->
                            <li>
                                Thời gian cảnh báo level 1
                                <?php echo form_dropdown('day_warning_level1', array(
                                '0'    => 'Quá hạn ',
                                '1'    => '1 Ngày',
                                '2'  => '2 Ngày',
                                '3'  => '3 Ngày',
                                '4'  => '4 Ngày',
                                '5'  => '5 Ngày',
                                '6'  => '6 Ngày',
                                '7'  => '7 Ngày',
                                ),
                                $this->config->item('day_warning_level1'), 'class="form-control" id="day_warning_level1"'); ?>
                            </li>
                            <li>
                                Màu sắc cảnh báo level 1
                                <?php echo form_input(array(
                                'class'=>'form-control form-inps jscolor',
                                'name'=>'color_warning_level1',
                                'id'=>'color_warning_level1',
                                'value'=>$this->config->item('color_warning_level1')));?>
                            </li>
                            <!-- Thời Gian Cảnh Báo Level 2: -->
                            <li>
                                Thời gian cảnh báo level 2
                                <?php echo form_dropdown('day_warning_level2', array(
                                '0'    => 'Quá hạn ',
                                '1'    => '1 Ngày',
                                '2'  => '2 Ngày',
                                '3'  => '3 Ngày',
                                '4'  => '4 Ngày',
                                '5'  => '5 Ngày',
                                '6'  => '6 Ngày',
                                '7'  => '7 Ngày',
                                ),
                                $this->config->item('day_warning_level2'), 'class="form-control" id="day_warning_level2"'); ?>
                            </li>
                            <li>
                                Màu sắc cảnh báo level 2
                                <?php echo form_input(array(
                                'class'=>'form-control form-inps jscolor',
                                'name'=>'color_warning_level2',
                                'id'=>'color_warning_level2',
                                'value'=>$this->config->item('color_warning_level2')));?>
                            </li>
                            <!-- Thời Gian Cảnh Báo Level 3: -->
                            <li>
                                Thời gian cảnh báo level 3
                                <?php echo form_dropdown('day_warning_level3', array(
                                '0'    => 'Quá hạn ',
                                '1'    => '1 Ngày',
                                '2'  => '2 Ngày',
                                '3'  => '3 Ngày',
                                '4'  => '4 Ngày',
                                '5'  => '5 Ngày',
                                '6'  => '6 Ngày',
                                '7'  => '7 Ngày',
                                ),
                                $this->config->item('day_warning_level3'), 'class="form-control" id="day_warning_level3"'); ?>
                            </li>
                            <li>
                                Màu sắc cảnh báo level 3
                                <?php echo form_input(array(
                                    'class'=>'form-control form-inps jscolor',
                                    'name'=>'color_warning_level3',
                                    'id'=>'color_warning_level3',
                                    'value'=>$this->config->item('color_warning_level3')));?>
                            </li>
                           

                    </ul><!-- end Cài đặt bán hàng -->
                    
                    <div class="m-heading-1 border-blue m-bordered">
                            <h3><i class="lagger ion ion-ios-home-outline"></i> Cài đặt kho</h3>
                            <p> Phần cài đặt cấu hình cho kho
                                <!-- <a class="btn red btn-outline" href="http://jqueryvalidation.org" target="_blank">the official documentation</a> -->
                            </p>
                    </div>
                    <ul class="list-items borderless">
                        <!-- Cảnh Báo Các Sản Phẩm Hết Hạn  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_show_warning_expire_time'); ?>!">Cảnh báo sản phẩm hết hạn</a>
                             <input name="config_show_warning_expire_time" id="config_show_warning_expire_time" type="checkbox" data-on-text="Bật" <?php if($this->config->item('config_show_warning_expire_time')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Thời Gian Cảnh Bảo Trước (Ngày): -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_expire_time'); ?>!">Số ngày cảnh báo</a>
                            <?php echo form_input(array(
                            'name'=>'config_expire_time',
                            'id'=>'config_expire_time',
                            'class'=>'form-control',
                            'value'=>$this->config->item('config_expire_time')));?>
                        </li>
                       
                        <!-- Hiển Thị Cảnh Báo Các Sản Phẩm Dưới Hạn Mức Tồn Kho : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="<?php echo lang('config_highlight_low_inventory_items_in_items_module'); ?>!">Cảnh báo dưới mức tồn</a>
                             <input name="highlight_low_inventory_items_in_items_module" id="highlight_low_inventory_items_in_items_module" type="checkbox" data-on-text="Bật" <?php if($this->config->item('highlight_low_inventory_items_in_items_module')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                      
                        
                    </ul>
                </div>
            </div><!-- end cai_dat_ban_hang -->
            <div class="tab-pane page-quick-sidebar-settings" id="cai_dat_hoa_don">
                <div class="page-quick-sidebar-settings-list">
                    <div class="m-heading-1 border-green m-bordered">
						<h3><i class="lagger ion ion-document-text"></i> Dịch vụ hàng hóa</h3>
						<p> Phần cài đặt cấu hình dịch vụ hàng hóa
								<a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
						</p>
                    </div>
                    <ul class="list-items borderless">
                        <li> 
                            Mã hóa đơn bán hàng
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'sale_prefix',
                                'id'=>'sale_prefix',
                                'value'=>$this->config->item('sale_prefix')));?>
                        </li>
                        <li> 
                            Mã hóa đơn nhập hàng
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                            'name'=>'receive_prefix',
                            'id'=>'receive_prefix',
                            'value'=>$this->config->item('receive_prefix')));
                            ?>
                        </li>
                        <!-- Tiêu đề hóa đơn bán hàng -->
                        <li> 
                            <?php echo lang('config_override_receipt_title'); ?>
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'override_receipt_title',
                                'id'=>'override_receipt_title',
                                'value'=>$this->config->item('override_receipt_title')));?>
                        </li>
                        <!-- Hiển Thị ID Trên Mã Vạch  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_id_to_show_on_barcode'); ?>!">Mã vạch hiển thị</a>
                            <?php echo form_dropdown('id_to_show_on_barcode', array(
                                'id'   => lang('common_item_id'),
                                'number'  => lang('common_item_number_expanded'),
                                'product_id'    => lang('common_product_id'),
                                ),
                                $this->config->item('id_to_show_on_barcode'), 'class="form-control" id="id_to_show_on_barcode"')
                                ?>
                        </li>
                        <li> 
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_show_item_id_on_receipt'); ?>!">ID hàng hóa</a>

                            <input name="show_item_id_on_receipt" id="show_item_id_on_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('show_item_id_on_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-off-color="danger" data-off-text="Tắt" data-size="small" data-handle-width="35" data-label-width="16">
                        
                        </li>
                        <!-- Ẩn Mã Vạch Trên Hóa Đơn : -->
                        <li>
                            <?php echo lang('config_hide_barcode_on_sales_and_recv_receipt'); ?>
                            <input name="hide_barcode_on_sales_and_recv_receipt" id="hide_barcode_on_sales_and_recv_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_barcode_on_sales_and_recv_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                           
                        </li>
                         <!-- Tự Động Hiển Thị Thông Tin Ghi Chú Trong Hóa Đơn -->
                        <li>
                             <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_automatically_show_comments_on_receipt'); ?>!">Hiển thị ghi chú</a>
                            <input name="automatically_show_comments_on_receipt" id="automatically_show_comments_on_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('automatically_show_comments_on_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <li>
                            Hóa đơn bán hàng có dịch vụ
                            <input name="sale_order_has_service" id="sale_order_has_service" type="checkbox" data-on-text="Bật" <?php if($this->config->item('sale_order_has_service')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                         <!-- Mẫu Hóa Đơn Bán Hàng : -->
                        <li>
                            <?php echo lang('config_template_sale'); ?>
                            <?php 
                                $arr = array('0'    =>  lang('config_template_default'));
                                if(!empty($qc_types)) {
                                    foreach ($qc_types as $key => $v) {
                                        $arr[$v['id_quotes_contract']] = $v['title_quotes_contract'];
                                    }
                                }
                                
                                echo form_dropdown('config_template_sale', $arr,
                                        $this->config->item('config_template_sale'), 'class="form-control" id="config_template_sale"');
                            ?>
                        </li>
                        <li>
                                Mẫu đơn đặt hàng
                                <?php
                                    $arr = array('0'    =>  lang('config_template_default'));
                                    if(!empty($qc_types)) {
                                        foreach ($qc_types as $key => $v) {
                                            $arr[$v['id_quotes_contract']] = $v['title_quotes_contract'];
                                        }
                                    }
                                    echo form_dropdown('config_template_order_sale', $arr,
                                     $this->config->item('config_template_order_sale'), 'class="form-control" id="config_template_order_sale"');
                                ?>
                        </li>
                        <!-- Nhóm Tất Cả Các Loại Thuế Vào Hóa Đơn -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_group_all_taxes_on_receipt'); ?>!">Nhóm các loại thuế</a>
                             <input name="group_all_taxes_on_receipt" id="group_all_taxes_on_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('group_all_taxes_on_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                           
                        </li>
                        
                         <!-- Nhắc Nhập Mã CCV Khi Sử Dụng Thẻ Tín Dụng : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_prompt_for_ccv_swipe'); ?>!">Nhắc nhập mã CCV</a>
                            <input name="prompt_for_ccv_swipe" id="prompt_for_ccv_swipe" type="checkbox" data-on-text="Bật" <?php if($this->config->item('prompt_for_ccv_swipe')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Thông Tin Chính Sách : -->
                        </ul>

						<ul class="list-items-extend">
                        <li>
                            <a href="javascript:void(0)"><?php echo lang('common_return_policy'); ?></a>
                            <p></p>
                            <?php echo form_textarea(array(
                                'name'=>'return_policy',
                                'id'=>'return_policy',
                                'class'=>'form-control text-area',
                                'rows'=>'4',
                                'cols'=>'30',
                                'value'=>$this->config->item('return_policy')));?>
                        </li>
                        <!-- Thông Báo / Khuyến Mãi : -->
                        <li>
                            <a href="javascript:void(0)"><?php echo lang('common_announcement_special'); ?></a>
                            <p></p>
                            <?php echo form_textarea(array(
                                'name'=>'announcement_special',
                                'id'=>'announcement_special',
                                'class'=>'form-control text-area',
                                'rows'=>'4',
                                'cols'=>'30',
                                'value'=>$this->config->item('announcement_special')));?>
                        </li>            
                   
                    </ul>
                    
                </div>
            </div><!-- end cai_dat_hoa_don -->
            <div class="tab-pane page-quick-sidebar-settings" id="cai_dat_he_thong">
                <div class="page-quick-sidebar-settings-list">
                    <div class="m-heading-1 border-green m-bordered">
                        <h3><i class="lagger ion ion-ios-cog-outline"></i> Cài đặt hệ thống</h3>
                        <p> Phần cài đặt hệ thống
                            <a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
                        </p>
                    </div>
                  
                    <ul class="list-items borderless">
                        <li> 
                            Mã khách hàng
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'ma_khach_hang_prefix',
                                'id'=>'ma_khach_hang_prefix',
                                'value'=>$this->config->item('ma_khach_hang_prefix')));?>
                        </li>
                        <li> 
                            Mã khách hàng bắt đầu từ
               
                            <?php 
                            echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'ma_khach_hang_bat_dau_tu',
                                'id'=>'ma_khach_hang_bat_dau_tu',
                                'value'=>$this->config->item('ma_khach_hang_bat_dau_tu')));
                                ?>

                        </li>
                        <li>
                            Giá nhập hàng
                            <?php
                            $arr = array('cost_price'   =>  'Giá vốn', 'unit_price'=>'Giá bán');

                            echo form_dropdown('config_price_imported', $arr,
                                $this->config->item('config_price_imported'), 'class="form-control" id="config_price_imported"');
                            ?>
                        </li>
                        
                        <!-- Tình Toán Giá Trung Bình Từ Giá Nhập Đầu Vào Hàng Hóa  -->
                        <li>
                             <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Tính trung bình giá vốn khi nhập hàng!">Tính bình quân giá nhập hàng</a>
                             <input name="calculate_average_cost_price_from_receivings" id="calculate_average_cost_price_from_receivings" type="checkbox" data-on-text="Bật" <?php if($this->config->item('calculate_average_cost_price_from_receivings')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <li>
                            <a href="javascript:void(0)"><?php echo lang('config_averaging_method'); ?></a>
                            <?php echo form_dropdown('averaging_method_', array('moving_average' => lang('config_moving_average'), 'historical_average' => lang('config_historical_average'), 'dont_average' => lang('config_dont_average_use_current_recv_price')), $this->config->item('averaging_method'),'class="form-control" id="averaging_method_"'); ?>
                        </li>
                        <!-- Luôn Tính Giá Trị Trung Bình Của Chi Phí, Cho Bán Hàng/ Nhập Hàng  -->
                        <li>
                             <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_always_use_average_cost_method'); ?>!">Tính giá trị trung bình</a>
                             <input name="always_use_average_cost_method" id="always_use_average_cost_method" type="checkbox" data-on-text="Bật" <?php if($this->config->item('always_use_average_cost_method')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Tự động nhận email -->
                        <li>
                            <?php echo lang('config_automatically_email_receipt'); ?>
                             <input name="automatically_email_receipt" id="automatically_email_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('automatically_email_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                          <!-- Định Dạng Bảng Tính : -->
                        <li>
                            <?php echo lang('config_spreadsheet_format'); ?>
                            <?php echo form_dropdown('spreadsheet_format', array('CSV' => lang('config_csv'), 'XLSX' => lang('config_xlsx')), $this->config->item('spreadsheet_format'),'class="form-control" id="spreadsheet_format"'); ?>
                        </li>
                        <!-- Định Dạng Tiêu Đề Thư : -->
                        <li>
                            <?php echo lang('config_mailing_labels_type'); ?>
                            <?php echo form_dropdown('mailing_labels_type', array('pdf' => 'PDF', 'excel' => 'Excel'), $this->config->item('mailing_labels_type'),'class="form-control" id="mailing_labels_type"'); ?>
                        </li>
                         <!-- Import dữ liệu -->
                        <li>
                            <?php echo 'Import dữ liệu'; ?>
                            <?php echo form_dropdown('import_quantity', $slb_import_quantity, $this->config->item('import_quantity'), 'class="form-control form-inps" id ="import_quantity"');?>

                        </li>

                        <!-- Ngôn Ngữ -->
                        <li>
                            Ngôn Ngữ
                            <?php echo form_dropdown('language', array(
                            'vietnam'    => 'Việt Nam',
                            'english'  => 'English',
                            ),
                            $this->Appconfig->get_raw_language_value(), 'class="form-control" id="language"');
                            ?>
                        </li>
                        <!-- Định Dạng Ngày : -->
                        <li>
                            Định Dạng Ngày
                            <?php echo form_dropdown('date_format', array(
                                'middle_endian'    => '12/30/2000',
                                'little_endian'  => '30-12-2000',
                                'big_endian'   => '2000-12-30'), $this->config->item('date_format'), 'class="form-control" id="date_format"');
                            ?>
                        </li>
                        <!-- Định Dạng Thời Gian : -->
                        <li>
                            Định Dạng Thời Gian
                            <?php echo form_dropdown('time_format', array(
                                '12_hour'    => '1:00 PM',
                                '24_hour'  => '13:00'
                                ), $this->config->item('time_format'), 'class="form-control" id="time_format"');
                            ?>
                        </li>
                        <!-- Bật Âm Thanh Cảnh Báo Có Tin Nhắn : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_enable_sounds'); ?>!">Âm thanh cảnh báo</a>
                            <input name="enable_sounds" id="enable_sounds" type="checkbox" data-on-text="Bật" <?php if($this->config->item('enable_sounds')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                    </ul>

                    <div class="m-heading-1 border-green m-bordered">
                        <h3><i class="lagger ion ion-speakerphone"></i> Cài đặt giao ca</h3>
                        <p> Phần cài đặt giao ca
                            <a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
                        </p>
                    </div>

                    <ul class="list-items borderless">
                         <!-- Theo Dõi Tiền Ghi Sổ (Giao Ca)  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('common_track_cash'); ?>!">Theo dõi tiền ghi sổ</a>
                             <input name="track_cash" id="track_cash" type="checkbox" data-on-text="Bật" <?php if($this->config->item('track_cash')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <li>
                            <?php echo lang('common_category'); ?>
                            <?php echo form_dropdown('shift_category_id', $categories, $this->config->item('shift_category_id'), 'class="form-control form-inps" id ="shift_category_id"');?>
                        </li>
                        <li>
                            Người nhận
                             <?php echo form_dropdown('shift_user_id', 
                                 $employees, $this->config->item('shift_user_id') ? $this->config->item('shift_user_id') : '', 'class="form-control" id="shift_user_id"');
                                ?>
                        </li>
                        <!-- Hết Thời Gian Đăng Nhập Hệ Thống : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_phppos_session_expiration'); ?>!">Hết thời gian đăng nhập</a>
                            <?php echo form_dropdown('phppos_session_expiration',$phppos_session_expirations, $this->config->item('phppos_session_expiration')!==NULL ? $this->config->item('phppos_session_expiration') : 0,'class="form-control" id="phppos_session_expiration"'); ?>
                        </li>
                    </ul>
					<div class="m-heading-1 border-green m-bordered">
							<h3><i class="lagger ion ion-email"></i> Cài đặt SMS và Email</h3>
							<p> Phần cài đặt SMS và Email
									<a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
							</p>
					</div>
					<ul class="list-items borderless">
                        <!-- Tên Brandname : -->
                        <li>
                            <?php echo lang('config_brand_name'); ?>
                             <input type="text" data-index="1" class="form-control" name="sms_brand_name" value="<?php echo H($this->config->item('config_sms_brand_name')); ?>" />

                        </li>
                        <!-- Tài Khoản SMS -->
                        <li>
                            <?php echo lang('config_sms_user'); ?>
                            <input type="text" data-index="1" class="form-control" name="sms_user" value="<?php echo H($this->config->item('config_sms_user')); ?>" />
                        </li>
                        <!-- Mật Khẩu SMS : -->
                        <li>
                            <?php echo lang('config_sms_pass'); ?>
                            <input type="password" data-index="1" class="form-control" name="sms_pass" value="<?php echo H($this->config->item('config_sms_pass')); ?>" />
                        </li>
                        <!-- Email : -->
                        <li>
                            <?php echo lang('config_email_account'); ?>
                            <?php echo form_input(array(
                            'class'=>'valid form-control form-inps',
                            'type' => 'text',
                            'name'=>'email_account',
                            'id'=>'email_account',
                            'value'=>$this->config->item('config_email_account')));?>
                        </li>
                        <!-- Mật Khẩu : -->
                        <li>
                            <?php echo lang('config_email_pass'); ?>
                            <?php echo form_input(array(
                            'class'=>'valid form-control form-inps',
                            'type' => 'password',
                            'name'=>'email_pass',
                            'id'=>'email_pass',
                            'value'=>$this->config->item('config_email_pass')));?>
                        </li>
                        <!-- Nhập Lại Mật Khẩu : -->
                        <li>
                            <?php echo lang('config_email_pass_again'); ?>
                            <?php echo form_input(array(
                            'class'=>'valid form-control form-inps',
                            'type' => 'password',
                            'name'=>'email_pass_again',
                            'id'=>'email_pass_again',
                            'value'=>$this->config->item('config_email_pass')));?>
                        </li>
                    </ul>
				</div>
            </div><!-- end cai_dat_he_thong -->
            <div class="tab-pane page-quick-sidebar-settings" id="setting_cho_cong_ty">
                <div class="page-quick-sidebar-settings-list">
                    <!-- Thông tin công ty -->
                    <div class="m-heading-1 border-green m-bordered">
                        <h3><i class="lagger ion ion-ios-information-outline"></i> Cài đặt thông tin công ty</h3>
                        <p> Phần cài đặt thông tin cho công ty
                            <a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
                        </p>
                    </div>
                    <ul class="list-items-extend">
                        
                    </ul>
                    <ul class="list-items borderless">
                        <li> Ảnh, logo đại diện 
                           <input type="file" name="company_logo" id="company_logo" class="filestyle" data-input="false" style="position: absolute; clip: rect(0px 0px 0px 0px);""> 
                        </li>
                        <li>
                            <!-- Xóa logo -->
                            Xóa logo
                            <input  name="delete_logo" id="delete_logo" type="checkbox" data-on-text="True" class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="OFF" style="float: right;">
                         
                        </li>
                    </ul>
                    <ul class="list-items-extend">
                        <!-- Tên công ty -->
                        <li> 
                            <?php echo lang('common_company'); ?>
                            <p></p>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-control-extend form-inps',
                                'name'=>'company',
                                'id'=>'company',
                                'value'=>$this->config->item('company')));
                            ?>
                        </li>
                        <!-- Website -->
                        <li> 
                            <?php echo lang('config_website'); ?>
                            <p></p>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-control-extend form-inps',
                                'name'=>'website',
                                'id'=>'website',
                                'value'=>$this->config->item('website')));
                            ?>
                        </li>
                    </ul><!-- Thông tin công ty -->
                   
                    <div class="m-heading-1 border-green m-bordered">
                        <h3><i class="lagger ion ion-social-usd-outline"></i> Cài đặt thuế và ngoại tệ</h3>
                        <p> Phần cài đặt thuế và ngoại tệ
                            <a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
                        </p>
                    </div>
                    <ul class="list-items borderless">
                       <li>
                            <!-- Giá đã bao gồm thuế -->
                           <?php echo lang('common_prices_include_tax'); ?>
                            <input name="prices_include_tax" id="prices_include_tax" type="checkbox" data-on-text="Bật" <?php if($this->config->item('prices_include_tax')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <li> 
                            <!-- Tính Thuế Giao Nhận -->
                            <?php echo lang('config_charge_tax_on_recv'); ?>
                           <input name="charge_tax_on_recv" id="charge_tax_on_recv" type="checkbox" data-on-text="Bật" <?php if($this->config->item('charge_tax_on_recv')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Tỉ Lệ Thuế -->
                        <li> 
                            <?php echo lang('common_default_tax_rate_1'); ?>
                            <?php echo form_input(array(
                                'class'=>'form-control form-inps-tax',
                                'placeholder' => lang('common_tax_percent'),
                                'name'=>'default_tax_1_rate',
                                'id'=>'default_tax_1_rate',
                                'size'=>'4',
                                'value'=>$this->config->item('default_tax_1_rate')));
                            ?>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-control-extend form-inps',
                                'name'=>'default_tax_1_name',
                                'id'=>'default_tax_1_name',
                                'value'=>$this->config->item('default_tax_1_name')!==NULL ? $this->config->item('default_tax_1_name') : lang('common_sales_tax_1')));
                            ?>
                        </li>
                        <li> 
                            <?php echo lang('common_default_tax_rate_2'); ?>
                                <?php echo form_input(array(
                                    'class'=>'form-control form-inps-tax',  
                                    'name'=>'default_tax_2_rate',
                                    'placeholder' => lang('common_tax_percent'),
                                    'id'=>'default_tax_2_rate',
                                    'size'=>'4',
                                    'value'=>$this->config->item('default_tax_2_rate')));?>

                                <?php echo form_input(array(
                                    'class'=>'form-control form-inps',
                                    'name'=>'default_tax_2_name',
                                    'placeholder' => lang('common_tax_name'),
                                    'id'=>'default_tax_2_name',
                                    'size'=>'10',
                                    'value'=>$this->config->item('default_tax_2_name')!==NULL ? $this->config->item('default_tax_2_name') : lang('common_sales_tax_2')));
                                ?>

                        </li>
                        <li>
                            <!-- Tích lũy -->
                            <?php echo lang('common_cumulative'); ?>
                           <input name="default_tax_2_cumulative" id="default_tax_2_cumulative" type="checkbox" data-on-text="Bật" <?php if($this->config->item('default_tax_2_cumulative')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Bao Gồm Thuế Trên Mã Vạch?  -->
                        <li> 
                            <?php echo lang('config_barcode_price_include_tax'); ?>
                            <input name="barcode_price_include_tax" id="barcode_price_include_tax" type="checkbox" data-on-text="Bật" <?php if($this->config->item('barcode_price_include_tax')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Kí Hiệu Tiền Tệ  -->
                        <li> 
                            <?php echo lang('config_currency_symbol'); ?>

                            <?php echo form_input(array(
                                'class'=>'form-control form-inps',
                                'name'=>'currency_symbol',
                                'id'=>'currency_symbol',
                                'value'=>$this->config->item('currency_symbol')));?>
                        </li>
                        <li> 
                            <?php echo lang('config_number_of_decimals'); ?>
                            <?php echo form_dropdown('number_of_decimals', array(
                                ''  => lang('config_let_system_decide'),
                                '0'    => '0',
                                '1'    => '1',
                                '2'    => '2',
                                '3'    => '3',
                                '4'    => '4',
                                '5'    => '5',
                            ),
                            $this->config->item('number_of_decimals')===NULL ? '' : $this->config->item('number_of_decimals') , 'class="form-control" id="number_of_decimals"');
                            ?>
                        </li>
                         <!-- Dấu Phân Cách Đơn Vị Nghìn  -->
                        <li> 
                           <?php echo lang('config_thousands_separator'); ?>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-inps',
                                'name'=>'thousands_separator',
                                'id'=>'thousands_separator',
                                'value'=>$this->config->item('thousands_separator') ? $this->config->item('thousands_separator') : ','));?>
                        </li>
                        <!-- Dấu Thập Phân : -->
                        <li>
                            <?php echo lang('config_decimal_point'); ?>
                            <?php echo form_input(array(
                                'class'=>'validate form-control form-inps',
                                'name'=>'decimal_point',
                                'id'=>'decimal_point',
                                'value'=>$this->config->item('decimal_point') ? $this->config->item('decimal_point') : '.'));?>
                        </li>
                    </ul>
                </div>
            </div><!-- end setting_cho_cong_ty -->

            <div class="tab-pane page-quick-sidebar-settings" id="cai_dat_may_in">
                 <div class="page-quick-sidebar-settings-list">
                    <!-- Thông tin công ty -->
                    <div class="m-heading-1 border-green m-bordered">
                        <h3><i class="lagger ion ion-android-cart"></i> Cài đặt máy in</h3>
                        <p> Phần cài đặt máy in
                            <a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
                        </p>
                    </div>
                    <ul class="list-items borderless">
                         <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Tự động in hóa đơn sau khi hoàn thành đơn nhập hàng !">In hóa đơn sau khi nhập hàng</a>                          

                            <input name="print_after_receiving" id="print_after_receiving" type="checkbox" data-on-text="Bật" <?php if($this->config->item('print_after_receiving')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                           
                        </li>
                        <!-- Tự động in sau khi hoàn thành đơn hàng -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Tự động in sau khi hoàn thành đơn hàng !"> <?php echo lang('config_print_after_sale'); ?></a>
                             <input name="print_after_sale" id="print_after_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('print_after_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                         <!-- In Hóa Đơn Sau Khi Nhận Đặt Hàng  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Tự động in hóa đơn sau khi hoàn thành đơn nhập hàng !">In hóa đơn sau khi nhận hàng</a>
                            <input name="show_receipt_after_suspending_sale" id="show_receipt_after_suspending_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('show_receipt_after_suspending_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                          <!-- Tự Động In Hai Hóa Đơn Cho Các Giao Dịch Thẻ Tín Dụng  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_automatically_print_duplicate_receipt_for_cc_transactions'); ?>!">Thẻ tín dụng</a>
                            <input name="automatically_print_duplicate_receipt_for_cc_transactions" id="automatically_print_duplicate_receipt_for_cc_transactions" type="checkbox" data-on-text="Bật" <?php if($this->config->item('automatically_print_duplicate_receipt_for_cc_transactions')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Chọn Kích Thước Giấy In : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_sales_receipt_pdf_size'); ?>!">Kích thước giấy in</a>
                            <?php echo form_dropdown('config_sales_receipt_pdf_size', array(
                                'a4'   => 'A4', 
                                'a5'  => 'A5',
                                'a8'    =>  'A8',
                                'a58'   =>'A58'
                            ),
                        $this->config->item('config_sales_receipt_pdf_size'), 'class="form-control" id="config_sales_receipt_pdf_size"');
                        ?>
                        </li>
                    </ul>
                </div>
            </div><!-- end cai_dat_may_in -->
            <div class="tab-pane page-quick-sidebar-settings" id="cai_dat_tai_khoan">
                 <div class="page-quick-sidebar-settings-list">
                    <div class="m-heading-1 border-green m-bordered">
                        <h3><i class="lagger ion ion-person-stalker"></i> Cài đặt tài khoản</h3>
                        <p> Phần cài đặt tài khoản
                            <a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
                        </p>
                    </div>
                    <ul class="list-items borderless">
                         <!-- Chọn Người Bán Hàng Trong Đơn Hàng : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_select_sales_person_during_sale'); ?>!">Hiển thị nhân viên bán hàng</a>
                             <input name="select_sales_person_during_sale" id="select_sales_person_during_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('select_sales_person_during_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Mặc Định Nhân Viên Bán Hàng : -->
                        <li> 
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_default_sales_person'); ?>!">Mặc định nhân viên</a>
                            
                            <?php echo form_dropdown(
                                'default_sales_person',
                                array('logged_in_employee' => lang('common_logged_in_employee'),
                                'not_set' => lang('common_not_set')),
                                $this->config->item('default_sales_person'),
                                'class="form-control" id="default_sales_person"'); ?>
                        </li>
                        <!-- Hiển Thị Nhân Viên Tư Vấn Ở Màn Hình Bán Hàng : -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Hiển thị nhân viên từ vấn ở giao diện bán hàng!">Hiển thị nhân viên tư vấn</a>
                            <input name="config_show_sale_supporter" id="config_show_sale_supporter" type="checkbox" data-on-text="Bật" <?php if($this->config->item('config_show_sale_supporter')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Kích Hoạt Chế Độ Tính Giờ : -->
                        <li>
                             <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_enable_timeclock'); ?>!">Chế độ tính giờ</a>
                                <input name="timeclock" id="timeclock" type="checkbox" data-on-text="Bật" <?php if($this->config->item('timeclock')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                         <!-- Chế Độ Dùng Thử (Không Lưu Đơn Hàng): -->
                        <li>
                             <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_require_employee_login_before_each_sale'); ?>!">Chế độ dùng thử</a>
                                <input name="require_employee_login_before_each_sale__" id="require_employee_login_before_each_sale__" type="checkbox" data-on-text="Bật" <?php if($this->config->item('require_employee_login_before_each_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Kích Hoạt Tính Năng Chuyển Đổi Người Dùng Nhanh (Mật Khẩu Không Bắt Buộc)  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Cho phép người dùng thay đổi các tài khoản !">Chuyển đổi người dùng nhanh</a>
                                <input name="fast_user_switching" id="fast_user_switching" type="checkbox" data-on-text="Bật" <?php if($this->config->item('fast_user_switching')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Yêu Cầu Nhân Viên Đăng Nhập Trước Mỗi Lần Bán  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Yêu cầu nhân viên đăng nhập trước khi bán hàng!">Yêu cầu nhân viên đăng nhập</a>
                                <input name="require_employee_login_before_each_sale" id="require_employee_login_before_each_sale" type="checkbox" data-on-text="Bật" <?php if($this->config->item('require_employee_login_before_each_sale')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                        <!-- Giữ Cùng Một Điểm Bán Hàng, Khi Chuyển Tài Khoản Nhân Viên  -->
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_keep_same_location_after_switching_employee'); ?>!">Giữ 1 điểm bán hàng</a>
                                <input name="keep_same_location_after_switching_employee" id="keep_same_location_after_switching_employee" type="checkbox" data-on-text="Bật" <?php if($this->config->item('keep_same_location_after_switching_employee')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                        </li>
                    </ul>
                    <div class="m-heading-1 border-green m-bordered">
                        <h3><i class="lagger ion ion-ios-rose-outline"></i> Cài đặt hoa hồng</h3>
                        <p> Phần cài đặt hoa hồng
                            <a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
                        </p>
                    </div>
						<ul class="list-items borderless">
                         <!-- Tỉ Lệ Mặc Định Tiền Hoa Hồng (Phần Trăm Hoa Hồng Dựa Trên Giá Bán Hoặc Lợi Nhuận Của Một Sản Phẩm): -->
                        
                        <!-- Phương Thức Tính Phần Trăm Hoa Hồng -->
                        <li>
                            Tính hoa hồng khi
                            <select name="commission_time_method" id="commission_time_method" class="select_form form-control">
                                <option value="order"<?php if($this->config->item('commission_time_method') == 'order') echo ' selected'; ?>>Hoàn thành đơn hàng</option>
                                <option value="contract"<?php if($this->config->item('commission_time_method') == 'contract') echo ' selected'; ?>>Kết thúc hợp đồng</option>
                            </select>
                        </li>
                        <li>
    
                            Tính theo
                            <select name="commission_method" id="commission_method" class="select_form form-control">
                                <option value="order"<?php if($this->config->item('commission_method') == 'order') echo ' selected'; ?>>Đơn hàng</option>
                            </select>
                        </li>
                        <li>

                        Tỷ suất tối thiểu
                            <?php echo form_input(array(
                                'class'=>'valid form-control form-inps',
                                'type' => 'text',
                                'name'=>'min_profit',
                                'id'=>'min_profit',
                                'value'=>(float)$this->config->item('min_profit')));?>
                        </li>
                        <li>

                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Lợi nhuận tối thiểu để tính hoa hồng !">Lợi nhuận tối thiểu</a>
                            <?php echo form_input(array(
                                'class'=>'valid form-control form-inps',
                                'type' => 'text',
                                'name'=>'min_profit_commission',
                                'id'=>'min_profit_commission',
                                'value'=>(float)$this->config->item('min_profit_commission')));?>

                        </li>
                        <li> 
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('common_commission_default_rate'); ?>!">Tỉ lệ tiền hoa hồng</a>
                            <?php echo form_input(array(
                            'name'=>'commission_default_rate',
                            'id'=>'commission_default_rate',
                            'class'=>'form-control',
                            'value'=>$this->config->item('commission_default_rate')));?>
                        </li>
                        <li>
                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('common_commission_percent_calculation'); ?>!">Phần trăm hoa hồng</a>
                            <?php echo form_dropdown('commission_percent_type', array(
                                'selling_price'  => lang('common_unit_price'),
                                'profit'    => lang('common_profit'),
                                ),
                                $this->config->item('commission_percent_type'),
                                array('id' => 'commission_percent_type','class'=>"form-control"))
                            ?>
                        </li>
                        </ul>
                </div>
            </div><!-- end cai_dat_tai_khoan -->
            <!-- Cài đặt hiển thị -->
            <div class="tab-pane page-quick-sidebar-settings" id="cai_dat_hien_thi">
			<div class="page-quick-sidebar-settings-list">
            <!-- Thông tin công ty -->
			<div class="m-heading-1 border-green m-bordered">
							<h3><i class="lagger ion ion-ios-paper-outline"></i> Cài đặt hiển thị</h3>
							<p> Phần cài đặt hiển thị
									<a class="btn red btn-outline" href="javascript:void(0)" target="_blank">Tài liệu hướng dẫn</a>
							</p>
			</div>
			<ul class="list-items borderless">
                 <li>
                Tên TK khách nợ 
                    <?php echo form_input(array(
                        'class'=>'form-control form-inps',
                        'name'=>'customer_balance',
                        'id'=>'customer_balance',
                        'value'=>$this->config->item('customer_balance')));?>
                </li>
                <li>
                Tên TK nợ khách
                    <?php echo form_input(array(
                        'class'=>'form-control form-inps',
                        'name'=>'customer_balance_2',
                        'id'=>'customer_balance_2',
                        'value'=>$this->config->item('customer_balance_2')));?>
                </li>
                <li>
                Tên TK nợ NCC
                    <?php echo form_input(array(
                        'class'=>'form-control form-inps',
                        'name'=>'supplier_balance',
                        'id'=>'supplier_balance',
                        'value'=>$this->config->item('supplier_balance')));?>
                </li>
                <li>
                Tên TK NCC nợ
                    <?php echo form_input(array(
                        'class'=>'form-control form-inps',
                        'name'=>'supplier_balance_2',
                        'id'=>'supplier_balance_2',
                        'value'=>$this->config->item('supplier_balance_2')));?>

                </li>
                <!-- Tự động trỏ chuột -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_auto_focus_on_item_after_sale_and_receiving'); ?>!">Tự động trỏ chuột</a>
                    <input name="auto_focus_on_item_after_sale_and_receiving" id="auto_focus_on_item_after_sale_and_receiving" type="checkbox" data-on-text="Bật" <?php if($this->config->item('auto_focus_on_item_after_sale_and_receiving')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Hiển thị số lượng các đơn -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Hiển thị số lượng các đơn bán hàng!">Số lượng đơn bán hàng</a>
                    <?php echo form_dropdown('number_of_recent_sales', 
                     array(
                        '1'=>'1',
                        '2'=>'2',
                        '5'=>'5',
                        '10'=>'10',
                        '20'=>'20',
                        '50'=>'50'
                        ), $this->config->item('number_of_recent_sales') ? $this->config->item('number_of_recent_sales') : '10', 'class="form-control" id="number_of_recent_sales"');
                        ?>

                </li>
                <!-- Ẩn hóa đơn bán hàng -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Ẩn hóa đơn bán hàng gần đây cho khách hàng !">Ẩn hóa đơn bán hàng</a>
                    <input name="hide_customer_recent_sales" id="hide_customer_recent_sales" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_customer_recent_sales')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_round_cash_on_sales'); ?>!">Làm tròn số thập phân</a>
                     <input name="round_cash_on_sales" id="round_cash_on_sales" type="checkbox" data-on-text="Bật" <?php if($this->config->item('round_cash_on_sales')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                 <!-- Ẩn Các Đơn Mua Hàng Đang Đặt Hàng, Trong Báo Cáo : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_hide_suspended_recv_in_reports'); ?>!">Ẩn đơn hàng</a>
                     <input name="hide_suspended_recv_in_reports" id="hide_suspended_recv_in_reports" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_suspended_recv_in_reports')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Luôn Hiển Thị Chọn Nhanh Sản Phẩm/ Dịch Vụ : -->
                <li>
                     <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Luôn hiển thị màn hình chọn nhanh trên giao diện bán hàng !">Luôn hiển thị POS</a>
                     <input name="always_show_item_grid" id="always_show_item_grid" type="checkbox" data-on-text="Bật" <?php if($this->config->item('always_show_item_grid')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Ẩn Sản Phẩm Hết Trong Kho, Trên Màn Hình Chọn Nhanh : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Ẩn sản phẩm hết hàng tồn kho trên giao diện màn hình chọn nhanh !">Ẩn sản phẩm hết hàng trên POS</a>
                     <input name="hide_out_of_stock_grid" id="hide_out_of_stock_grid" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_out_of_stock_grid')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Mặc Định Chọn Nhanh Dịch Vụ/ Hàng Hóa : -->
                <li> 
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Mặc định hiển thị màn hình chọn nhanh !">Mặc định hiển thị POS</a>
                     <?php echo form_dropdown('default_type_for_grid', array(
                        'categories'  => lang('reports_categories'), 
                        'tags'  => lang('common_tags'),
                        'restaurant_place'  => 'Sơ đồ bàn',
                    ),
                    $this->config->item('default_type_for_grid'), 'class="form-control" id="default_type_for_grid"');
                    ?>
                </li>
                 <!-- Chuyển Đến Màn Hình Bán Hàng/ Nhận Hàng Sau Khi In Hóa Đơn : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Chuyển đến giao diện bán hàng/ nhập hàng sau khi in hóa đơn !">Chuyển nhanh giao diện</a>

                    <input name="redirect_to_sale_or_recv_screen_after_printing_receipt" id="redirect_to_sale_or_recv_screen_after_printing_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Độ Dài Văn Bản  -->
                <li>
                    <?php echo lang('config_receipt_text_size'); ?>
                    <?php echo form_dropdown('receipt_text_size', $receipt_text_size_options, $this->config->item('receipt_text_size'),'class="form-control" id="receipt_text_size"'); ?>
                </li>
                 <!-- Khóa Tính Năng Thông Báo : -->
                <li>
                    Tắt thông báo
                    <input name="disable_sale_notifications" id="disable_sale_notifications" type="checkbox" data-on-text="Bật" <?php if($this->config->item('disable_sale_notifications')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                 <!-- Ẩn Số Dư Tiền Trong Hóa Đơn, Tại Điểm Bản Hàng -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_hide_store_account_balance_on_receipt'); ?>!">Ẩn số dư tiền</a>
                     <input name="hide_store_account_balance_on_receipt" id="hide_store_account_balance_on_receipt" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_store_account_balance_on_receipt')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Báo Cáo Theo Thứ Tự : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_report_sort_order'); ?>!">Theo thứ tự</a>
                    <?php echo form_dropdown('report_sort_order', array('asc' => lang('config_asc'), 'desc' => lang('config_desc')), $this->config->item('report_sort_order'),'class="form-control" id="report_sort_order"'); ?>
                </li>
                <!-- Ẩn Tiền Đặt Cọc Trong Báo Cáo : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('common_hide_layaways_sales_in_reports'); ?>!">Ẩn tiền đặt cọc</a>
                    <input name="hide_layaways_sales_in_reports" id="hide_layaways_sales_in_reports" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_layaways_sales_in_reports')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                 <!-- Ẩn Tài Khoản Công Nợ K/H Trong Báo Cáo : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_hide_store_account_payments_in_reports'); ?>!">Ẩn tài khoản công nợ</a>
                    <input name="hide_store_account_payments_in_reports" id="hide_store_account_payments_in_reports" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_store_account_payments_in_reports')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Ẩn Tài Khoản Thanh Toán Trong Báo Cáo Tổng Hợp  -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_hide_store_account_payments_from_report_totals'); ?>!">Ẩn tài khoản thanh toán</a>
                    <input name="hide_store_account_payments_from_report_totals" id="hide_store_account_payments_from_report_totals" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_store_account_payments_from_report_totals')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Ẩn Giá Trên Mã Vạch -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_hide_price_on_barcodes'); ?>!">Ẩn giá trên mã vạch</a>
                        <input name="hide_price_on_barcodes" id="hide_price_on_barcodes" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_price_on_barcodes')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>   
                <!-- Số Mục Trên Một Trang : -->
                <li>
                     Số mục trên 1 trang
                    <?php echo form_dropdown('number_of_items_per_page', 
                     array(
                        '20'=>'20',
                        '50'=>'50',
                        '100'=>'100',
                        '200'=>'200',
                        '500'=>'500'
                        ), $this->config->item('number_of_items_per_page') ? $this->config->item('number_of_items_per_page') : '20', 'class="form-control" id="number_of_items_per_page"');
                        ?>
                </li>
                <!-- Số Dịch Vụ/ Hàng Hóa Trên Màn Hình Chọn Nhanh : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_number_of_items_in_grid'); ?>!">Số hàng hóa</a>
                    
                        <?php 
                        $numbers = array();
                        foreach(range(1, 50) as $number) 
                        { 
                            $numbers[$number] = $number;
                            
                        }
                        ?> 
                    <?php echo form_dropdown('number_of_items_in_grid', 
                         $numbers, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : '14', 'class="form-control" id="number_of_items_in_grid"');
                        ?>
                </li>
                <!-- Mặc Định Mục Mới Như Các Dịch Vụ : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_default_new_items_to_service'); ?>!">Mặc định mục mới</a>
                    <input name="default_new_items_to_service" id="default_new_items_to_service" type="checkbox" data-on-text="Bật" <?php if($this->config->item('default_new_items_to_service')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Ẩn Số Liệu Thống Kê Của Bảng Điều Khiển (Dashboard) : -->
                 <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_hide_dashboard_statistics'); ?>!">Ẩn số lượng thống kê</a>
                    <input name="hide_dashboard_statistics" id="hide_dashboard_statistics" type="checkbox" data-on-text="Bật" <?php if($this->config->item('hide_dashboard_statistics')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Hiển Thị Chuyển Đổi Ngôn Ngữ  -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_show_language_switcher'); ?>!">Chuyển đổi ngôn ngữ</a>
                    <input name="show_language_switcher" id="show_language_switcher" type="checkbox" data-on-text="Bật" <?php if($this->config->item('show_language_switcher')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                </li>
                <!-- Hiển Thị Đồng Hồ Hệ Thống : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_show_clock_on_header'); ?> <Chỉ hiển thị trên thiết bị có màn hình lớn> !">Đồng hồ hệ thống</a>
                    <input name="show_clock_on_header" id="show_clock_on_header" type="checkbox" data-on-text="Bật" <?php if($this->config->item('show_clock_on_header')) echo 'checked'; ?> class="BSswitch" data-on-color="success" data-size="small" data-handle-width="35" data-label-width="16" data-off-color="danger" data-off-text="Tắt">
                    <div class="clearfix"></div>
                   
                </li>
                <!-- Cách thức tính trung bình : -->
                <li>
                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php echo lang('config_averaging_method'); ?>!">Tính trung bình</a>
               
                    <?php echo form_dropdown('averaging_method', array('moving_average' => lang('config_moving_average'), 'historical_average' => lang('config_historical_average'), 'dont_average' => lang('config_dont_average_use_current_recv_price')), $this->config->item('averaging_method'),'class="form-control" id="averaging_method"'); ?>
                </li>
            </ul>
            </div>
            </div><!-- end cai_dat_hien_thi -->
        </div> <!-- end tab content -->
        <!-- Modal -->
        <div class="inner-content hidden">
            <button class="btn btn-success">
                <i class="icon-settings"></i> Lưu thay đổi</button>
        </div>
        <?php echo form_close(); ?> 
    </div> <!-- sidebar end -->
</div>

<script type="text/javascript">

    $('[data-toggle="tooltip"]').tooltip();   
    $('#config_adjusted_cost_price').selectize();
    $(document).mouseup(function (e)
    {
            var container = $(".page-quick-sidebar-wrapper");

        if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.removeClass('page-quick-sidebar-open');
            $('.inner-content').addClass('hidden');
            $('#tim-kiem-menu-config').val(""); 
            $(".list-items > li").show();
        }
    });

    $('.BSswitch').bootstrapSwitch('state');

   
    
    $('body').on('click', '#config', function () {
        $('.page-quick-sidebar-wrapper').toggleClass('page-quick-sidebar-open');
        $('.inner-content').toggleClass('hidden');
        $('#tim-kiem-menu-config').val(""); 
        $(".list-items > li").show();
    });
    $(".page-quick-sidebar-wrapper").getNiceScroll().remove();
        setTimeout(function() {
          $(".page-quick-sidebar-wrapper").niceScroll();
        }, 200);
    


    <?php
    $deleted_payment_types = $this->config->item('deleted_payment_types');
    $deleted_payment_types = explode(',',$deleted_payment_types);

    foreach($deleted_payment_types as $deleted_payment_type)
    {
    ?>
        $( ".payment_types" ).each(function() {
            if ($(this).text() == <?php echo json_encode($deleted_payment_type); ?>)
            {
                $(this).removeClass('btn-primary');         
                $(this).addClass('deleted btn-danger');         
            }
        });
    <?php
    }
    ?>
    save_deleted_payments();

    $(".payment_types").click(function(e)
    {
        e.preventDefault();
        $(this).toggleClass('btn-primary');
        $(this).toggleClass('deleted btn-danger');
        save_deleted_payments();
    });

    function save_deleted_payments()
    {
        $(".deleted_payment_types").remove();
        
        var deleted_payment_types = [];
        $( ".payment_types.deleted" ).each(function() {
            deleted_payment_types.push($(this).text());
        });
        $("#config_form").append('<input class="deleted_payment_types" type="hidden" name="deleted_payment_types" value="'+deleted_payment_types.join()+'" />');
        
    }

    var submitting = false;
    $('#config_form').validate({
        submitHandler:function(form)
        {
            if (submitting) return;
            submitting = true;
            $(form).ajaxSubmit({
            success:function(response)
            {
                //Don't let the tiers be double submitted, so we change the name
                $('.tiers_to_edit').filter(function() {
                    return parseInt($(this).data('index')) < 0;
                }).attr('name','tiers_added[]');
                
                if(response.success)
                {
                    show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
                }
                else
                {
                    show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
                    
                }
                submitting = false;
            },
            dataType:'json'
        });

        },
        
    });
          
    $("#tim-kiem-menu-config").on("keyup", function () {
        if (this.value.length > 0) {   
          $(".list-items > li").hide().filter(function () {
            return $(this).text().toLowerCase().indexOf($("#tim-kiem-menu-config").val().toLowerCase()) != -1;
          }).show(); 
        }  
        else { 
          $(".list-items > li").show();
        }
    })





    // $('.bootstrap-switch-label').bootstrapSwitch('state', true);
</script>