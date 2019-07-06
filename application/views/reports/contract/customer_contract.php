<?php $this->load->view("partial/header"); ?>
<!-- <script type="text/javascript" src="assets/js/plugins/exports/table2excel.js"></script> -->
<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>">Báo cáo/</a>
	<a href="<?php echo site_url('reports/report_contract');?>" > Báo cáo đối chiếu hợp đồng</a>
	<div class="pull-right">
		<form action="<?php echo base_url().'reports/dowload_excel_contract'; ?>" method="post">
			<input type="hidden" name="input_month" id="input_month_ex" value="">
			<input type="hidden" name="input_year" id="input_year_ex" value="">
			<button class="btn btn-success" id="export-btn" type="submit" style="display: none;margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
		
	</div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('reports_report_input'); ?>
			</div><h3 class="text-center">Báo cáo đối chiếu hợp đồng</h3>
			<div class="panel-body">

				<!-- <div class="form-group col-md-3 col-md-offset-2">
					<label>Chọn thời gian:</label>
					<select name="" class="form-control" id="select_time">
						<option value="THANG">Tháng</option>
						<option value="TD">Từ ngày đến ngày</option>
					</select>
				</div> -->
				<div class="form-group col-md-3 col-md-offset-3" id="select_time_month">
					<label>Chọn tháng:</label>
					<select name="" id="input_month"  class="form-control" onchange="xuat_bao_cao();">
						<option value="0">Chọn</option>
						<?php 
						for ($i=1; $i <=12 ; $i++) { 
							echo "<option value='".$i."'>".$i."</option>";
						}
						?>
					</select>
				</div>
				<div class="form-group col-md-3" id="select_time_year">
					<label>Chọn năm:</label>
					<select name="" class="form-control"  onchange="xuat_bao_cao();" id="input_year">
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
				<thead>
					<tr>
						<th align="center" colspan="13" id="title-export1">THỐNG KÊ HỢP ĐỒNG TƯ VẤN
						</th>
					</tr>
					<tr>
						<th align="center"  colspan="13"><?php echo $name_location; ?></th>
					</tr>
					<tr>
						<th align="center"  colspan="13" id="thoigian_tk"></th>
					</tr>
					<tr style="background: #bfbfbf;">
						<th align="center" style="font-weight: 600; vertical-align: middle;">STT</th>
						<th align="center" width="7%" style="font-weight: 600; vertical-align: middle; margin: 0 auto;">Ngày ký HĐ</th>
						<th align="center" width="10%" style="font-weight: 600; vertical-align: middle;">Số hợp đồng</th>
						<th align="center" width="10%" style="font-weight: 600; vertical-align: middle;">Nội dung</th>
						<th align="center" width="8%" style="font-weight: 600; vertical-align: middle;">Đối tác</th>
						<th align="center"  width="8%" style="font-weight: 600; vertical-align: middle;">Giá trị hợp đồng chưa VAT</th>
						<th align="center" width="8%" style="font-weight: 600; vertical-align: middle;">VAT</th>
						<th align="center" width="10%" style="font-weight: 600; vertical-align: middle;">Hiện trạng HĐ</th>
						<th align="center" width="10%" style="font-weight: 600; vertical-align: middle;">Ngày ký thanh lý/nghiệm thu</th>
						<th align="center" width="10%" style="font-weight: 600; vertical-align: middle;">Số tiền đã thu trong tháng (Đvt:VNĐ)</th>
						<th align="center" width="8%" style="font-weight: 600; vertical-align: middle;">Số tiền thanh toán dồn tích (Đvt: VNĐ)</th>
						<th align="center" width="8%" style="font-weight: 600; vertical-align: middle;">CP dồn tích trả cho bên thứ 3 để thực hiện HĐ</th>
						<th align="center" width="6%" style="font-weight: 600; vertical-align: middle;">Ghi chú</th> 
					</tr>
				</thead>
				<tbody id="tbody-report">

				</tbody>
			</table>
		</div>
	</div>
</div>
<a href="javascript:;" style="display: none;" id="current-date1"><?php echo Date('d-m-Y'); ?></a>
<script type="text/javascript">
	$(document).ready(function() {
	});

	$('#select_time').change(function(){
		value = $(this).val();
		$('#main-export-office').fadeOut();
		html='<option value="0">Chọn</option>';
		if (value=='QUY') {
			html+='<option value="1">Quý 1</option>'
			html+='<option value="2">Quý 2</option>'
			html+='<option value="3">Quý 3</option>'
			html+='<option value="4">Quý 4</option>';
			$('#select_time_month').html('<label>Chọn quý:</label><select name="" id="input_month" class="form-control" onchange="xuat_bao_cao();">'+html+'</select>');
			$('#id_time').val('QUY');
		}else if(value=='THANG'){
			// thang
			for (var i = 1; i <=12; i++) {
				html+='<option value="'+i+'">'+i+'</option>';
			}

			$('#select_time_month').html('<label>Chọn tháng</label><select name="" id="input_month" class="form-control" onchange="xuat_bao_cao();">'+html+'</select>');
			$('#id_time').val('THANG');
		}else if(value=='TD'){
			$('#select_time_month').fadeIn();
			// thoi diem
			$('#select_time_month').html('<label>Từ ngày:</label><input type="text" name="date_start" id="input_month_start" class="form-control datepicker" onchange="xuat_bao_cao();">');
			$('#select_time_year').html('<label>Đến ngày:</label><input type="text" name="date_end" id="input_month_end" class="form-control datepicker" onchange="xuat_bao_cao();">');

			$('#input_month_start').datepicker({
				format: "dd-mm-yyyy",
			});
			$('#input_month_end').datepicker({
				format: "dd-mm-yyyy",
			});
			$('#id_time').val('TD');
			
		}else{
			$('#select_time_month').fadeOut();
			html='<option value="0">Chọn</option>';
			for (var i = 2013; i <2023; i++) {
				html += '<option value="'+i+'">'+i+'</option>';
			}
			$('#select_time_year').html('<label>Chọn năm:</label><select name="" class="form-control" id="input_year" onchange="xuat_bao_cao();">'+html+'</select>');
		}
		if (value=='QUY' || value=='THANG') {
			$('#select_time_month').fadeIn();
			html='<option value="0">Chọn</option>';
			for (var i = 2013; i <2023; i++) {
				html += '<option value="'+i+'">'+i+'</option>';
			}
			$('#select_time_year').html('<label>Chọn năm:</label><select name="" class="form-control" id="input_year" onchange="xuat_bao_cao();">'+html+'</select>');
		}
		
	});

	function xuat_bao_cao(){
		$('#export-btn').fadeIn();
		month = $('#input_month').val();
		year = $('#input_year').val();
		$('#input_month_ex').val(month);
		$('#input_year_ex').val(year);
		$.ajax({
			url: "<?php echo base_url().'reports/get_report_contract' ?>",
			type: 'post',
			dataType: 'html',
			data: {month: month, year:year, check:null},
		})
		.done(function(data) {
			if (data!=null) {
				$('#tbody-report').html(data);
			}else{
				$('#tbody-report').html('<tr><td colspan="13" rowspan="" headers="" class="text-center">Không có dữ liệu</td></tr>');
			}
		})
		.fail(function() {
		})
		.always(function() {
		});
	}
</script>
<script src="<?php echo base_url().'assets/js/report.js' ?>" type="text/javascript"></script>
<?php $this->load->view("partial/footer"); ?>