<style type="text/css">
.text-left{
	text-align:left !important;
}
	#pdf_content {
		width: 700px;
		display: block;
		overflow: hidden;
		position: relative;
		padding: 20px;
		font-size: 12px;
	}
        #table-responsive{
            max-width: 700px;
        }
	#pdf_logo img {
		max-height: 70px;
	}
	#company_name {
		text-transform: uppercase;
		font-weight: bold;
		color: #002FC2
	}
	#pdf_content span {
		color: #002FC2;
	}
	#pdf_title {
		width: 100%;
		text-align: center;
		text-transform: uppercase;
		font-weight: bold;
		font-size: 16px;
		margin-top: 12px;
	}
	#pdf_tbl_items {
		border-collapse: collapse;
		font-size: 12px;
		margin: 10px 0;
	}
	#pdf_tbl_items tboby {
		display: table-row-group;
		vertical-align: middle;
		border-color: inherit;
	}
	#pdf_tbl_items tr {
		display: table-row;
		vertical-align: inherit;
		border-color: inherit;
	}

	#pdf_tbl_items th, #pdf_tbl_items td {
		border: 1px solid #000;
		padding: 3px;
	}

	#pdf_signature {
		min-height: 150px;
	}
	#pdf_signature div {
		text-align: center;
	}
	#pdf_signature lable {
		font-size: 14px;
		font-weight: bold;
	}

	.fl {
		float: left;
	}
	.fr {
		float: right;
	}
	.clb {
		clear: both;
	}
	.w50 {
		width: 50%;
	}

	.w20 {
		width: 20%;
	}

	.w100 {
		width: 100%;
	}
	.pb20 {
		padding-bottom: 20px;
	}

	.pt20 {
		padding-top: 20px;
	}

	#pdf_header h3, #pdf_header p {
		text-align: center;
	}
	#pdf_footer {
		text-align: center;
	}
	#pdf_content table td, #pdf_content table th {
		text-align: right;
		height: auto !important;
	}
	p {
		margin: 3px 0;
	}
	.w150px {
		width: 150px;
	}
	.fontI {
		font-style: italic;
	}
        .border-bottom{
                border-bottom: 1px dotted rgb(0, 0, 0) !important;
        }
        .border-left{
                border-left: none !important;
        }
        .border-right{
                border-right: none!important;
        }
        .border-top{
                border-top: none !important;
        }
        #policy{
                font-weight: bold;
                text-align: center;
                font-size: 1.3em;
                margin-top: 10px; 
        }
        .text-center{
            text-align: center !important;
        }
        .text-bold{
            font-weight: bold !important;
        }
        table th, table td{
            line-height: normal !important;
        }
    /* Medium Devices, Desktops */
    @media only screen and (max-width : 992px) {

    }

    /* Small Devices, Tablets */
    @media only screen and (max-width : 768px) {
        .table-responsive{
               max-width: 700px;
            }
    }
    @media only screen and (max-width : 767px) and (max-width: 481px) {
        .table-responsive{
               max-width: 700px;
            }
    }

    /* Extra Small Devices, Phones */ 
    @media only screen and (max-width : 480px) {
        .table-responsive{
                max-width: 300px;
            } 
    }

    /* Custom, iPhone Retina */ 
    @media only screen and (max-width : 320px) {
        .table-responsive{
                max-width: 284px;
            } 
    }
        /*@media screen and (min-device-width: 481px) and (max-device-width: 768px)*/

</style>
<div id="pdf_content">
	<div id="pdf_header">
		<div>
			<div id="pdf_logo" class="fl">
				<?php if($this->config->item('company_logo')) {?>
					<?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
				<?php } ?>
			</div>
			<div id="pdf_company">
				<p id="company_name"><?php echo $this->config->item('company'); ?></p>
				<p><span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
				<p>Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
				<?php if($this->config->item('website')) { ?>
					<p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
				<?php } ?>
			</div>
		</div>
		<div class="clb">
			<div class="fr w150px">
				<p>Số: <?php echo $sale_id; ?></p>
				<p>Ngày: <span><?php echo date(get_date_format(), strtotime($transaction_time)); ?></span></p>
			</div>
		</div>
	</div>
	<div id="pdf_title" class="clb">
		<p>BÁO GIÁ SẢN PHẨM</p>
	</div>

	<div id="pdf_customer">
			<div style ="float:left;width:50%;">
            <p>Người báo giá:<span> <?php echo $employee; ?></span> </p>
            <p>Điện thoại:<span> <?php echo $employee_phone; ?></span>
            <p>Email<span>: <?php echo $employee_email; ?> </p></span> 
            <p>Address<span>: <?php echo $employee_address; ?> </p></span>
			<?php  if($this->config->item('company_user') != 'remHaMy'):?>
            <p>Quản lý: </p>
			<p>Kho: <?php if ($this->Location->count_all() > 1) { ?><span><?php echo $this->Location->get_info_for_key('name', isset($override_location_id) ? $override_location_id : FALSE); ?></span><?php } ?></p>
			<p>Địa chỉ kho: <span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
			<?php endif;?>
			</div>
			<div style ="float:left;width:50%;">
				<p>Họ tên khách hàng: <?php if ($customer) { ?> <span><?php echo $customer; ?></span> <?php } ?></p>
				<p>Địa chỉ:<?php if ($customer_address_1) { ?> <span><?php echo $customer_address_1; ?></span> <?php } ?></p>
				<p>Điện thoại:<?php if ($customer_phone) { ?> <span><?php echo $customer_phone; ?></span> <?php } ?></p>
				<p>Email:<?php if ($customer_email) { ?> <span><?php echo $customer_email; ?></span> <?php } ?></p>
				<p>Ghi chú: <?php echo nl2br($comment); ?></p>
			</div>
			<div style ="clear:both;"></div>
	</div>	
	<div style ="clear:both; margin-top:1rem;">
	<?php  if($this->config->item('company_user') == 'remHaMy'):?>
	<p><i>Công ty chúng tôi xin trân trọng gửi tới Quý khách bảng tự toán rèm các loại rèm cao cấp mà Quý khách đang quan tâm. Các sản phẩm này đã và đang được đánh giá cao về chất lượng và giá trị sử dụng rộng rãi trong gia đình, khách sạn, văn phòng.</i></p>
	<?php endif;?>
	</div>
	<div class="w100 clb table-responsive">
		<table id="pdf_tbl_items" class="w100 table">
			<tbody>
				<tr>
					<th class ="text-center">STT</th>
					<th class="text-center">Mã MH</th>
					<th class ="text-center"><?php echo lang('common_item_name'); ?></th>
					<th class="text-center"><?php echo lang('common_unit_report')?></th>
					<th class="text-center"><?php echo lang('common_quantity'); ?></th>
					<th class ="text-center"><?php echo lang('common_unit_sales').' ('.$this->config->item('currency_symbol').')'; ?></th>
					<th class="text-center"><?php echo lang('common_unit_discount').' %';?></th>
                    <?php  if($this->config->item('company_user') != 'remHaMy'):?>
					<th class="text-center"><?php echo lang('reports_taxes') .' %'?></th>
                    <?php endif;?>
					<th class ="text-center"><?php echo lang('common_unit_total').' ('.$this->config->item('currency_symbol').')'; ?></th>
				</tr>

				<?php
					if ($discount_item_line = $this->sale_lib->get_line_for_flat_discount_item())
					{
						$discount_item = $cart[$discount_item_line];
						unset($cart[$discount_item_line]);
						array_unshift($cart,$discount_item);
					}
				 
				$number_of_items_sold = 0;
				$stt = 0;
				if(empty($itemsContainsLine)) {
					foreach(array_reverse($cart, true) as $line => $item)
					{
						$stt ++;
						 if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
						 {
							 $number_of_items_sold = $number_of_items_sold + $item['quantity'];
						 }
						 
						$item_number_for_receipt = false;
						
						if ($this->config->item('show_item_id_on_receipt'))
						{
							switch($this->config->item('id_to_show_on_sale_interface'))
							{
								case 'number':
								$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
								break;
							
								case 'product_id':
								$item_number_for_receipt = array_key_exists('product_id', $item) ? H($item['product_id']) : ''; 
								break;
							
								case 'id':
								$item_number_for_receipt = array_key_exists('item_id', $item) ? H($item['item_id']) : 'KIT '.H($item['item_kit_id']); 
								break;
							
								default:
								$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
								break;
							}
						}
					?>

						<tr>
							<td class ="text-center"><?php echo $stt; ?></td>
							<td class ="text-center"><?php echo H($item['product_id']);?></td>
							<td class ="text-left"><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
							<td class ="text-center"><?php echo isset($item['measure']) ? $item['measure'] : ''; ?></td>
							<td><?php echo to_quantity(abs($item['quantity'])); ?></td>
							<td><?php echo NumberFormatToCurrency($item['price']); ?></td>
							<td><?php echo to_quantity($item['discount']);?></td>
							<td><?php echo to_quantity($item['tax_included']);?></td>
							<td><?php echo NumberFormatToCurrency(abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?></td>
						</tr>
						<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
						<tr>
												<td class="border-bottom text-bold" colspan="9">
								<?php if(!$item['description']==""){ ?>
									<div class="invoice-desc"><?php echo $item['description']; ?></div>
								<?php } ?>

								<?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
									<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					<?php }
				} else {
					foreach($itemsContainsLine as $itemContainLine) {
						?> <tr><td colspan="9" style = "font-weight: bold; text-align:left;">Sản phẩm: <?php echo $itemContainLine['itemName']?></td></tr><?php
						$eachItemsTotal = 0;
                        $stt = 0;
						foreach ($itemContainLine['line'] as $line1) {
							
							foreach($cart as $line => $item) {
								
								if($line1 == $line) {
											$stt ++;
											 if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
											 {
												 $number_of_items_sold = $number_of_items_sold + $item['quantity'];
											 }
											$eachItemsTotal += abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100);
											$item_number_for_receipt = false;
											
											if ($this->config->item('show_item_id_on_receipt'))
											{
												switch($this->config->item('id_to_show_on_sale_interface'))
												{
													case 'number':
													$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
													break;
												
													case 'product_id':
													$item_number_for_receipt = array_key_exists('product_id', $item) ? H($item['product_id']) : ''; 
													break;
												
													case 'id':
													$item_number_for_receipt = array_key_exists('item_id', $item) ? H($item['item_id']) : 'KIT '.H($item['item_kit_id']); 
													break;
												
													default:
													$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
													break;
												}
											}
										?>

											<tr>
												<td class ="text-center"><?php echo $stt; ?></td>
												<td class ="text-center"><?php echo H($item['product_id']);?></td>
												<td class ="text-left"><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
												<td class ="text-center"><?php echo isset($item['measure']) ? $item['measure'] : ''; ?></td>
												<td><?php echo to_quantity(abs($item['quantity'])); ?></td>
												<td><?php echo NumberFormatToCurrency($item['price']); ?></td>
												<td><?php echo to_quantity($item['discount']);?></td>
												<td><?php echo NumberFormatToCurrency(abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?></td>
											</tr>
											<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
											<tr>
																	<td class="border-bottom text-bold" colspan="9">
													<?php if(!$item['description']==""){ ?>
														<div class="invoice-desc"><?php echo $item['description']; ?></div>
													<?php } ?>

													<?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
														<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
													<?php } ?>
												</td>
											</tr>
											<?php } break;
								}
							}
						}
						?> <tr><td colspan="9" style = "font-weight: bold;">Tổng tiền rèm: <?php echo NumberFormatToCurrency($eachItemsTotal)?></td></tr><?php
					}
				    foreach($cart as $line=>$item) { 
						$flag = true;
						foreach ($itemsContainsLine as $itemContainsLine) {
							foreach($itemContainsLine['line'] as $line1) {
								if($line1 == $line) {
									$flag = false;
								}
							}
						}
						if ($flag) {
							$stt ++;
						 if ($item['name'] != lang('sales_store_account_payment') && $item['name'] != lang('common_discount'))
						 {
							 $number_of_items_sold = $number_of_items_sold + $item['quantity'];
						 }
						 
						$item_number_for_receipt = false;
						
						if ($this->config->item('show_item_id_on_receipt'))
						{
							switch($this->config->item('id_to_show_on_sale_interface'))
							{
								case 'number':
								$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
								break;
							
								case 'product_id':
								$item_number_for_receipt = array_key_exists('product_id', $item) ? H($item['product_id']) : ''; 
								break;
							
								case 'id':
								$item_number_for_receipt = array_key_exists('item_id', $item) ? H($item['item_id']) : 'KIT '.H($item['item_kit_id']); 
								break;
							
								default:
								$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
								break;
							}
						}
					?>

						<tr>
							<td class ="text-center"><?php echo $stt; ?></td>
							<td class ="text-center"><?php echo H($item['product_id']);?></td>
							<td class ="text-left"><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
							<td class ="text-center"><?php echo isset($item['measure']) ? $item['measure'] : ''; ?></td>
							<td><?php echo to_quantity(abs($item['quantity'])); ?></td>
							<td><?php echo NumberFormatToCurrency($item['price']); ?></td>
							<td><?php echo to_quantity($item['discount']);?></td>
							<td><?php echo NumberFormatToCurrency(abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?></td>
						</tr>
						<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
						<tr>
												<td class="border-bottom text-bold" colspan="9">
								<?php if(!$item['description']==""){ ?>
									<div class="invoice-desc"><?php echo $item['description']; ?></div>
								<?php } ?>

								<?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
									<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
								<?php } ?>
							</td>
						</tr>
						<?php } 
						}
				}}?>
				
				 <?php if ($this->config->item('group_all_taxes_on_receipt')) { ?>
					<?php 
					$total_tax = 0;
					foreach($taxes as $name=>$value) 
					{
						$total_tax+=$value;
				 	}
					?>
					<tr>
                                            <td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_tax').': '; 
                                                echo NumberFormatToCurrency($total_tax); ?></td>
					</tr>
				<?php }else {?>
					<?php foreach($taxes as $name=>$value) { ?>
						<tr>
							<td  class="border-bottom border-top text-bold" colspan="9"><?php echo $name.': ';  
                                                        echo NumberFormatToCurrency($value); ?></td>
						</tr>
					<?php }; ?>
				<?php } ?>

				
				<tr>
                                    <td  class="border-bottom border-top text-bold" colspan="9"><?php echo lang('common_total').': '; echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ? NumberFormatToCurrency(round_to_nearest_05($total)) : NumberFormatToCurrency($total); ?></td>
				</tr>

				<?php foreach($payments as $payment_id => $payment) { ?>
				<tr>
					<td><?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit'))) { ?>
						<?php echo $payment['card_issuer']. ': '.$payment['truncated_card']; ?>
					<?php } else { ?>
						<?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?>
					<?php } ?></td>
					<td><?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  to_currency(round_to_nearest_05($payment['payment_amount'])) : to_currency($payment['payment_amount']); ?></td>
				</tr>
				<?php } ?>

				<?php foreach($payments as $payment) {?>
					<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?>
						<?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>
						<tr>
							<td class="border-bottom border-top text-bold" colspan="9"><?php echo lang('sales_giftcard_balance'); 
                                                        echo $payment['payment_type'];
                                                        echo to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row))); ?></td>
						</tr>
					<?php }?>
				<?php }?>

				<?php if ($amount_change >= 0) {?>
					<tr>
						<td class="border-bottom border-top text-bold"  colspan="9"><?php echo lang('common_change_due'); 
                                                echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  ': '.to_currency(round_to_nearest_05($amount_change)) : ': '.to_currency($amount_change); ?></td>
					</tr>
				<?php } ?>

				<?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>
					<tr>
						<td class="border-bottom border-top text-bold"  colspan="9"><?php echo lang('sales_customer_account_balance').': '; 
                                                echo to_currency($customer_balance_for_sale); ?></td>
					</tr>
				<?php } ?>

				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($sales_until_discount) && !$this->config->item('hide_sales_to_discount_on_receipt') && $this->config->item('loyalty_option') == 'simple') {?>
					<tr>
						<td class="border-bottom border-top text-bold"  colspan="9"><?php echo lang('common_sales_until_discount').': '; ; 
                                                echo $sales_until_discount <= 0 ? lang('sales_redeem_discount_for_next_sale') : to_quantity($sales_until_discount); ?></td>
					</tr>
				<?php } ?>
				<?php if ($this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt') && $this->config->item('loyalty_option') == 'advanced') {?>
					<tr>
						<td class="border-bottom border-top text-bold"  colspan="9"><?php echo lang('common_points').': '; ; 
                                                echo to_quantity($customer_points); ?></td>
					</tr>
				<?php } ?>

				<?php if ($ref_no) { ?>
					<tr>
						<td class="border-bottom border-top text-bold"  colspan="9"><?php echo lang('sales_ref_no'); ?></td>
						<td><?php echo lang('sales_ref_no'); ?></td>
					</tr>
				<?php }

				if (isset($auth_code) && $auth_code) { ?>
					<tr>
						<td class="border-bottom border-top text-bold"  colspan="9"><?php echo lang('sales_auth_code').': '; 
                                                echo $auth_code; ?></td>
					</tr>
				<?php } ?>
                                        <tr ><td colspan="9" style="border-left: none;border-right: none; border-bottom: none;"></td></tr>
			</tbody>
		</table>
	</div>
	<div>
            <p>Số tiền viết bằng chữ: <span><?php echo getStringNumber($total)?></span></p>
	</div>
    <?php if ($this->config->item('company_user') == 'remHaMy'):?>
    <div>
            <p><b>Các điều khoản thương mại:</b></p>
            <p><span><?php echo $saleComments['comment_term']?></span></p>
           
	</div>
    <div>
            <p><b>Thời gian bảo hành sản phẩm:</b></p>
             <p><span><?php echo $saleComments['comment_guarantee']?></span></p>
	</div>
    <div>
            <p><b>Thanh toán:</b></span></p>
             <p><span><?php echo $saleComments['comment_payment']?></span></p>
	</div>
    <?php endif;?>
        <div id="policy"><?php echo $this->config->item('return_policy'); ?></div>
	<div class="clb">
		<div class="fr">
			<p>Ngày ..... tháng ..... năm .......</p>
		</div>
	</div>
	<div id="pdf_signature" class="w100 clb">
    <?php if ($this->config->item('company_user') == 'remHaMy'):?>
		<div class="w20 fl">
			<p><lable>Người lập phiếu</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
		<div class="w20 fl">
			<p></p>
			<p class="fontI"><p>
		</div>
		<div class="w20 fl">
			<p></p>
			<p class="fontI"><p>
		</div>
		<div class="w20 fl">
			<p></p>
			<p class="fontI"><p>
		</div>
		<div class="w20 fl">
			<p><lable>Giám đốc</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>        
    <?php else:?>
		<div class="w20 fl">
			<p><lable>Người lập phiếu</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
		<div class="w20 fl">
			<p><lable>Người nhận hàng</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
		<div class="w20 fl">
			<p><lable>Thủ kho</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
		<div class="w20 fl">
			<p><lable>Kế toán trưởng</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
		<div class="w20 fl">
			<p><lable>Giám đốc</lable></p>
			<p class="fontI">(ký, họ tên)<p>
		</div>
     <?php endif;?>
	</div>
	<div id="pdf_footer" class="w100 clb">
		<p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
	</div>
</div>