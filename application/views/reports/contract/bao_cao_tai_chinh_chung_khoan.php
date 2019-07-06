<?php $this->load->view("partial/header"); ?>
<!-- <script type="text/javascript" src="assets/js/plugins/exports/table2excel.js"></script> -->
<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>">Báo cáo/</a>
	<a href="<?php echo site_url('reports/report_contract');?>" > Báo cáo tổng hợp hợp đồng</a>

	<div class="pull-right">
		<form action="<?php echo base_url().'reports/dowload_excel_finance_stock'; ?>" method="post">
			<input type="hidden" name="input_month" id="input_month_ex" value="">
			<input type="hidden" name="input_year" id="input_year_ex" value="">
			<button class="btn btn-success" id="export-btn" type="submit" style="display: none; margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
	</div>
</div>
<ul class="nav nav-tabs">
	<li class="active"><a data-toggle="tab" href="#home">Hoạt động tư vấn tài chính và tư vấn đầu tư chứng khoán</a></li>
	<li><a data-toggle="tab" href="#menu1">Hoạt động bảo lãnh phát hành chứng khoán</a></li>
</ul>

<div class="tab-content">
	<div id="home" class="tab-pane fade in active">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-piluku">
					<h3 class="text-center">Báo cáo hoạt động tư vấn tài chính và tư vấn đầu tư chứng khoán</h3>
					<div class="panel-body">
						<div class="form-group col-md-3 col-md-offset-3">
							<label>Chọn tháng</label>
							<select name="" id="input_month" class="form-control" onchange="xuat_bao_cao_tai_chinh();">
								<option value="0">Chọn</option>
								<?php 
								for ($i=1; $i <=12 ; $i++) { 
									echo "<option value='".$i."'>".$i."</option>";
								}
								?>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label>Chọn năm</label>
							<select name="" class="form-control" id="input_year" onchange="xuat_bao_cao_tai_chinh();">
								<option value="0">Chọn</option>
								<?php
								$year_current=date('Y');
								for($i=$year_current-5;$i<=$year_current+5;$i++){
									if ($i==date('Y')) {
										echo '<option value="'.$i.'" selected>'.$i.'</option>';
									}else{
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
								}

								?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive" id="table-export-contract">
					<table class="table tablesorter table-reports table-bordered display table-hover" id="table_report_contract_cus">
						<thead 
						>
						<tr>
							<th align="center" colspan="13" id="title-export1">THỐNG KÊ HOẠT ĐỘNG TƯ VẤN TÀI CHÍNH VÀ TƯ VẤN ĐẦU TƯ CHỨNG KHOÁN
							</th>
						</tr>
						<tr>
							<th align="center"  colspan="13"><?php echo $name_location; ?></th>
						</tr>
						<tr>
							<th align="center"  colspan="13">Tháng:<span id="date_time_m"></span>/<span id="date_time_y"></span></th>
						</tr>
						<tr style="background: #79797970;">
							<th align="center" width="15%" style="font-weight: 600;">Loại tư vấn</th>
							<th align="center" style="font-weight: 600;">Số hợp đồng đã ký đầu kỳ</th>
							<th align="center" style="font-weight: 600;">Số hợp đồng đã thanh lý trong kỳ</th>
							<th align="center" style="font-weight: 600;">Số hợp đồng ký mới trong kỳ</th>
							<th align="center" style="font-weight: 600;">Số hợp đồng còn hiệu lực cuối kỳ</th>
							<th align="center" style="font-weight: 600;">Phí thu được trong tháng</th>
						</tr>
					</thead>
					<tbody id="tbody-report-stock">
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="menu1" class="tab-pane fade">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<h3 class="text-center">Hoạt động bảo lãnh phát hành chứng khoán</h3>
				<div class="panel-body">
					<div class="table-responsive" id="table-export-contract">
						<table class="table tablesorter table-reports table-bordered display table-hover">
							<thead>
								<tr style="background: #bfbfbf70;">
									<th align="center" width="5%">TT</th>
									<th align="center">Tên tổ chức phát hành</th>
									<th align="center">Loại chứng khoán bảo lãnh</th>
									<th align="center">Hình thức bảo lãnh</th>
									<th align="center">Tổng giá trị bảo lãnh</th>
									<th align="center">Thời gian bảo lãnh (từ….đến….)</th>
									<th class="center">Vốn chủ sở hữu *</th>
									<th class="center">Tổng giá trị vốn hoạt động ròng *</th>
									<th class="center">Phí bảo lãnh thu được</th>
									<th class="center" width="10%">Ghi chú</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$stt=1;
								if (!empty($bao_lanh_chung_khoan)) {
									foreach ($bao_lanh_chung_khoan as $key => $value) {
										?>
										<tr>
											<td><?php echo $stt; ?></td>
											<td><?php echo $value['ten_kh'];?></td>
											<td>
											</td>
											<td>
												<?php echo $value['ten_dv']; ?>
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td>
												<?php echo $value['code']; ?>
											</td>
										</tr>
										<?php
										$stt++;
									}
								}else{
									?>
									<tr>
										<td colspan="10" rowspan="" headers="" class="text-center">Không có dữ liệu</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
</div>

<a href="javascript:;" style="display: none;" id="current-date1"><?php echo Date('d-m-Y'); ?></a>
<script type="text/javascript">
	function xuat_bao_cao_tai_chinh(){
		month = $('#input_month').val();
		year = $('#input_year').val();
		check = 'stock';
		if (month!='0' && year!='0') {
			$('#export-btn').fadeIn();
			$('#date_time_m').html(month);
			$('#date_time_y').html(year);
			$('#input_month_ex').val(month);
			$('#input_year_ex').val(year);
			console.log(month, year);
			$.ajax({
				url: "<?php echo base_url().'reports/get_report_contract' ?>",
				type: 'post',
				dataType: 'html',
				data: {month: month, year:year, check:check},
			})
			.done(function(data) {
				if (data!=null) {
					$('#tbody-report-stock').html(data);
				}
			})
			.fail(function() {
			})
			.always(function() {
			});
		}else{
			$('#export-btn').fadeOut();
			$('#tbody-report').html('<tr><td colspan="13" class="text-center">Không có dữ liệu!</td></tr>');
		}
	}
</script>
<?php $this->load->view("partial/footer"); ?>