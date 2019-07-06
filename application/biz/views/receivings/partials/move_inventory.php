<style type="text/css">
	#pdf_content {
		width: 700px;
		display: block;
		overflow: hidden;
		position: relative;
		padding: 20px;
		font-size: 12px;
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
            direction: rtl !important;
            text-align: center !important;
        }
        .text-bold{
            font-weight: bold !important;
        }

</style>
<div id="pdf_content">
	<div id="pdf_header">
		<div id="pdf_logo">
			<?php if($this->config->item('company_logo')) {?>
				<?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
			<?php } ?>
		</div>
		<div id="pdf_company">
			<p id="company_name"><?php echo $this->config->item('company'); ?></p>
			<p><span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
			<p>Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span>, Fax: <span><?php echo $this->Location->get_info_for_key('fax', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
			<?php if($this->config->item('website')) { ?>
				<p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
			<?php } ?>
		</div>
		<div class="w100">
			<div id="pdf_short_info" class="w150px">
				<p>Số: <?php echo $receiving_id; ?></p>
				<p>Ngày: <span><?php echo date(get_date_format(), strtotime($transaction_time)); ?></span></p>
			</div>
		</div>
	</div>
	<div id="pdf_title" class="clb">
                <p>PHIẾU XUẤT CHI NHÁNH Số : <span><?php if(preg_match('/([0-9]+)/', $receiving_id,$number_receiving)) echo($number_receiving[1]);else echo $receiving_id; ?></span></p>
	</div>
	<p>Nhân viên: <?php echo $employee; ?></p>
	<div id="pdf_customer">
		
		<!-- <p>Đơn vị cung cấp:</p> -->
		<p>Ghi chú: <?php echo nl2br($comment); ?></p>
	</div>
	<div class="w100 clb table-responsive">
		<table id="pdf_tbl_items" class="w100 table">
			<tbody>
				<tr>
					<th>STT</th>
                    <th class="text-center">Mã MH</th>
										<th class="text-center">Mặt hàng</th>
                    <th class="text-center">Kho chuyển</th>
                    <th class="text-center">Kho nhận</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-center">Đơn vị tính</th>

				</tr>
				<?php 	
				$stt = 0; 
                $sum_quantity = 0;
                $sum_money  = 0;
                ?>
				<?php foreach(array_reverse($cart, true) as $line=>$item) { 
                $sum_quantity +=abs($item['quantity']);
                $sum_money += abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100);
                ?>
					<?php $stt ++; ?>
					<tr>
						<td class="text-center"><?php echo $stt; ?></td>
                        <td class="text-center"><?php echo H($item['product_id']);?></td>
						<td class="text-center"><?php echo $item['name']; ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></td>
                        <td class="text-center"><strong><?php echo $transfer_from_location;?></strong></td>
                        <td class="text-center"><strong><?php echo $transfer_to_location?></strong></td>
						<td class="text-center"><?php echo to_quantity_abs($item['quantity']); ?></td>
                        <td class="text-center"><?php echo $item['measure']; ?></td>
					</tr>
					<?php if (!$item['description']=="" ||(isset($item['serialnumber']) && $item['serialnumber'] !="") ) {?>
					<tr>
						<td colspan="7">
							<?php if(!$item['description']==""){ ?>
								<div class="invoice-desc"><?php echo $item['description']; ?></div>
							<?php } ?>

							<?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
		                    	<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
		                    <?php } ?>
						</td>
					</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<div class="clb">
		<div id="pdf_sinnature_time">
			<p>Ngày ..... tháng ..... năm .......</p>
		</div>
	</div>
	<div id="pdf_signature" class="w100">
		<table class="w100">
			<tr>
				<th>Thủ kho</th>
				<th>Người vận chuyển</th>
				<th>Người nhận</th>
				<th>Kế toán trưởng</th>
                <th>Giám đốc</th>
			</tr>
			<tr>
				<td><p class="fontI">(ký, họ tên)</p></td>
				<td><p class="fontI">(ký, họ tên)</p></td>
				<td><p class="fontI">(ký, họ tên)</p></td>
				<td><p class="fontI">(ký, họ tên)</p></td>
                <td><p class="fontI">(ký, họ tên)</p></td>
			</tr>
		</table>
	</div>
	<div id="pdf_footer" class="w100">
		<p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
	</div>
</div>