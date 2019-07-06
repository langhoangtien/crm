<?php 
if ($input['check']=='THANG' || $input['check'] =='QUY') {
	if ($input['check']=='QUY') {
		$title='BÁO CÁO GỬI PHÒNG PHÂN TÍCH QUÝ '.$input['month'].'/'.$input['year'];
		$name_td = 'Qúy '.$input['month'].'/'.$input['year'];
	}elseif ($input['check']=='THANG'){
		$title='BÁO CÁO GỬI PHÒNG PHÂN TÍCH THÁNG '.$input['month'].'/'.$input['year'];
		$name_td = 'Tháng '.$input['month'].'/'.$input['year'];
	}
	?>
	<div class="col-md-12">
		<h4 class="text-center" style="font-weight: 600;"><?php echo $title; ?></h4>
		<h5 style="font-weight: 600;">1. Kết quả hoạt động kinh doanh</h5>
		<p>Danh sách các deal ghi nhận doanh thu trong kỳ (phân loại các deal có doanh thu dài hạn và doanh thu bất thường)</p>
		<div class="table-responsive" id="table-export-1">
			<table class="table tablesorter table-reports table-bordered display table-hover" id="table_report_contract_cus">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="15%">Chỉ tiêu chính</th>
						<th width="14%"><?php echo $name_td; ?></th>
						<th width="14%">Thực hiện lũy kế</th>
						<th width="14%">Kế hoạch năm <?php echo $input['year']; ?></th>
						<th width="14%">% hoàn thành kế hoạch</th>
						<th width="14%">Cùng kỳ</th>
						<th width="14%">%yoy</th>
					</tr>
				</thead>
				<tbody id="plan_work_business">
					<tr>
						<td>Bão lãnh đại lý phát hành TP</td>
						<td class="text-left"><?php echo $name_td; ?></td>
						<td class="text-right" ></td>
						<td class="text-right" id="plan_y_tp"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
					</tr>
					<tr>
						<td>Bão lãnh đại lý phát hành CP</td>
						<td class="text-left"><?php echo $name_td; ?></td>
						<td class="text-right"></td>
						<td class="text-right" id="plan_y_cp"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
					</tr>
					<tr>
						<td>Tư vấn khác</td>
						<td class="text-left"><?php echo $name_td; ?></td>
						<td class="text-right"></td>
						<td class="text-right" id="plan_y_tvk"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
					</tr>
					<tr>
						<td>Tư vấn M&A</td>
						<td class="text-left"><?php echo $name_td; ?></td>
						<td class="text-right"></td>
						<td class="text-right" id="plan_y_ma"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
					</tr>
					<tr>
						<td style="font-weight: 600;">Tổng cộng</td>
						<td></td>
						<td class="text-left"></td>
						<td class="text-right" id="sum_plan"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<p>Đánh giá hiệu quả tại các địa bàn, nguyên nhân:</p>
		<div class="table-responsive">
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="25%">Địa bàn</th>
						<th width="25%">Tên hợp đồng</th>
						<th width="25%">Tên dịch vụ</th>
						<th width="25%">Giá trị hợp đồng</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if (!empty($contract_HSC)) {
						?>
						<tr>
							<td rowspan="<?php echo count($contract_HSC)+1; ?>" class="text-left" style="font-weight:600">Hội Sở Chính</td>
						</tr>
						<?php  

						foreach ($contract_HSC as $key => $value) {
							?>
							<tr>
								<td><?php echo $value['name_contract']; ?></td>
								<td><?php echo $value['item_name']; ?></td>
								<td class="text-right"><?php echo number_format($value['co_vat']); ?></td>
							</tr>
							<?php
						}
					}else{
						?>
						<tr>
							<td class="text-left" style="font-weight:600">Hội Sở Chính</td>
							<td colspan="3" class="text-center">Không có dữ liệu!</td>
						</tr>
						<?php  
					}
					?>
					<?php 
					if (!empty($contract_DN)) {
						?>
						<tr>
							<td rowspan="<?php echo count($contract_DN)+1; ?>" class="text-left" style="font-weight:600">Đà Nẵng</td>
						</tr>
						<?php  

						foreach ($contract_DN as $key => $value) {
							?>
							<tr>
								<td><?php echo $value['name_contract']; ?></td>
								<td><?php echo $value['item_name']; ?></td>
								<td class="text-right"><?php echo number_format($value['co_vat']); ?></td>
							</tr>
							<?php
						}
					}else{
						?>
						<tr>
							<td class="text-left" style="font-weight:600">Đà Nẵng</td>
							<td colspan="3" class="text-center">Không có dữ liệu!</td>
						</tr>
						<?php  
					}
					?>
					<?php 
					if (!empty($contract_HCM)) {
						?>
						<tr>
							<td rowspan="<?php echo count($contract_HCM)+1; ?>" class="text-left" style="font-weight:600">Hồ Chí Minh</td>
						</tr>
						<?php  

						foreach ($contract_HCM as $key => $value) {
							?>
							<tr>
								<td><?php echo $value['name_contract']; ?></td>
								<td><?php echo $value['item_name']; ?></td>
								<td class="text-right"><?php echo number_format($value['co_vat']); ?></td>
							</tr>
							<?php
						}
					}else{
						?>
						<tr>
							<td class="text-left" style="font-weight:600">Hồ Chí Minh</td>
							<td colspan="3" class="text-center">Không có dữ liệu!</td>
						</tr>
						<?php  
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="table-responsive">
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="15%">Địa bàn</th>
						<th width="14%"><?php echo $name_td; ?></th>
						<th width="14%">Thực hiện lũy kế</th>
						<th width="14%">Kế hoạch năm <?php echo $input['year']; ?></th>
						<th width="14%">% hoàn thành kế hoạch</th>
						<th width="14%">Cùng kỳ</th>
						<th width="14%">%yoy</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>HSC</td>
						<td clas><?php echo $name_td; ?></td>
						<td><?php echo number_format($tongluyke[0]); ?></td>
						<td class="text-right"><?php echo (!empty($tong_HSC)?number_format($tong_HSC):0); ?></td>
						<td class="text-right"><?php  
						echo !empty($tongluyke[0])&& !empty($tong_HSC)?(round($tongluyke[0]/$tong_HSC,2)):0;
						?></td>
						<td class="text-right">
							<?php echo !empty($cungky_t3)?$cungky_t3[0]:0; ?>
						</td>
						<td></td>
					</tr>
					<tr>
						<td>HCM</td>
						<td clas><?php echo $name_td; ?></td>
						<td><?php echo number_format($tongluyke[2]); ?></td>
						<td class="text-right"><?php echo (!empty($tong_HCM)?number_format($tong_HCM):0); ?></td>
						<td class="text-right">
							<?php  
							echo !empty($tongluyke[2])&& !empty($tong_HCM)?(round($tongluyke[2]/$tong_HCM,2)):0;
							?>
						</td>
						<td class="text-right"><?php echo !empty($cungky_t3)?$cungky_t3[2]:0; ?></td>
						<td></td>
					</tr>
					<tr>
						<td>Đà Nẵng</td>
						<td clas><?php echo $name_td; ?></td>
						<td><?php echo number_format($tongluyke[1]); ?></td>
						<td class="text-right"><?php echo (!empty($tong_DN)?number_format($tong_DN):0); ?></td>
						<td class="text-right"><?php  
						echo !empty($tongluyke[1])&& !empty($tong_DN)?(round($tongluyke[1]/$tong_DN,2)):0;
						?></td>
						<td class="text-right"><?php echo !empty($cungky_t3)?$cungky_t3[1]:0; ?></td>
						<td></td>
					</tr>
					<tr>
						<td>Tổng cộng</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
		<h5 style="font-weight: 600;">2. Khó khăn và vướng mắc khi thực hiện công việc trong hoạt động kinh doanh:</h5>
		<p>Kết thúc <?php echo $name_td; ?>, Phòng đang gặp phải những khó khăn, vấn đề phát sinh như sau:</p>
		<h5 style="font-weight: 600;">3. Kế hoạch hoạt động kinh doanh <?php echo $name_td; ?></h5>
		<p>Doanh thu <?php echo $name_td; ?> của toàn hệ thống ước đạt … đồng, trong đó:</p>
		<div class="table-responsive">
			<table class="table tablesorter table-reports table-bordered display table-hover">
				<thead>
					<tr style="background: #bfbfbf;">
						<th width="15%">Tên dự án</th>
						<th width="15%">Tên dịch vụ</th>
						<th width="15%">Tên hợp đồng</th>
						<th width="15%">Tổng giá trị hợp đồng</th>
						<th width="15%">Tổng doanh thu các giai đoạn đã nghiệm thu</th>
						<th width="15%">Dự kiến doanh thu <?php echo $name_td; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php  
					if (!empty($KHKD)) {
						foreach ($KHKD as $key => $value) {
							?>
							<tr>
								<td><?php echo $value['name_task']; ?></td>
								<td><?php echo $value['ten_dv']; ?></td>
								<td><?php echo $value['name_contract']; ?></td>
								<td class="text-right"><?php echo number_format($value['co_vat']); ?></td>
								<td class="text-right">
									<?php  
									if (!empty($contract_done)) {
										foreach ($contract_done as $key => $value_done) {
											echo ($value['sale_id']==$value_done['sale_id'])?$value_done['co_vat']:0;
										}
									}else{
										echo 0;
									}
									?>
								</td>
								<td></td>
							</tr>
							<?php  
						}
					}else{
						echo '<tr><td colspan="6" rowspan="" headers="" class="text-center">Không có dữ liệu!</td></tr>';
					}
					?>

				</tbody>
			</table>
		</div>
		<p>Các chương trình hành động khác:</p>
		<p>Định hướng giải quyết khó khăn tồn động và đề xuất:</p>
	</div>
	<div class="col-md-12">
		<h4 class="pull-right" style="font-weight: 600;">PHÒNG TƯ VẤN TÀI CHÍNH DOANH NGHIỆP</h4>
	</div>
	<?php  
}else{
	?>
	<div class="col-md-12">
		<div class="text-center" style="font-weight: 500; font-size: 14px; font-style: italic; color: red;">Xuất báo cáo để xem dữ liệu</div>
	</div>
	<?php  
}
?>