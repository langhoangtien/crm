<?php $this->load->view("partial/header"); ?>
<!-- <script type="text/javascript" src="assets/js/plugins/exports/table2excel.js"></script> -->

<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>"> Báo cáo</a>
	<a href="javascript:;" >/ Báo cáo gửi phòng phân tích</a>
	<div class="pull-right">
		<form action="<?php echo base_url().'ReportBusiness/download'; ?>" method="post">
			<input type="hidden" name="input_month" id="input_month_ex" value="">
			<input type="hidden" name="input_year" id="input_year_ex" value="">
			<input type="hidden" name="date_start" id="input_date_start">
			<input type="hidden" name="date_end" id="input_date_end">
			<input type="hidden" name="check" id="id_check" value="">
			<button class="btn btn-success" id="export-btn" type="submit" style="display: none; margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">

			<h3 class="text-center">Báo cáo gửi phòng phân tích</h3>
			<div class="panel-body">
				<div class="form-group col-md-2 col-md-offset-3">
					<label>Chọn:</label>
					<input type="hidden" name="" value="<?php echo $location_id; ?>" id="location_id">
					<select name="" id="select_time" class="form-control" >
						<option value="0">Chọn</option>
						<option value="THANG">Tháng</option>
						<option value="QUY">Quý</option>
						<option value="NAM">Năm</option>
						<!-- <option value="TD">Từ ngày đến ngày</option> -->
					</select>
				</div>
				<div class="form-group col-md-2" id="select_time_input">
					<label>Chọn tháng:</label>
					<select name="" id="input_month" class="form-control" onchange="xuat_bao_cao();">
						<option value="0">Chọn</option>
					</select>
				</div>
				<div class="form-group col-md-2" id="select_time_input1">
					<label>Chọn năm:</label>
					<select name="" class="form-control" id="input_year" onchange="xuat_bao_cao();">
						<option value="0">Chọn</option>
						<?php
						$year_current=date('Y');
						for($i=2013;$i<=2023;$i++){
							if ($i==$year_current) {
								echo '<option value="'.$i.'" selected>'.$i.'</option>';
							}else{
								echo '<option value="'.$i.'" >'.$i.'</option>';
							}
							
						}
						?>
					</select>
				</div>
				<p style="font-style: italic; color: red;" id="error" class="text-center"></p>
			</div>
		</div>
	</div>
</div>
<div class="row" id="main-export-office">
	
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var date = new Date();
		html='<option value="0">Chọn</option>';
		for (var i = 1; i <=12; i++) {
			html+='<option value="'+i+'">'+i+'</option>';
		}
		$('#input_month').html(html);
		$('#id_check').val('THANG');
		$('#input_month_ex').val('0');
		$('#input_year_ex').val(date.getFullYear());
	});

	$('#select_time').change(function(){
		value = $(this).val();
		var date = new Date();
		// $('#main-export-office').fadeOut();
		html='<option value="0">Chọn</option>';
		if (value=='QUY') {
			html+='<option value="1">Quý 1</option>'
			html+='<option value="2">Quý 2</option>'
			html+='<option value="3">Quý 3</option>'
			html+='<option value="4">Quý 4</option>';
			$('#select_time_input').html('<label>Chọn quý:</label><select name="" id="input_month" class="form-control" onchange="xuat_bao_cao();">'+html+'</select>');
			$('#id_time').val('QUY');
		}else if(value=='THANG'){
			// thang
			for (var i = 1; i <=12; i++) {
				html+='<option value="'+i+'">'+i+'</option>';
			}

			$('#select_time_input').html('<label>Chọn tháng</label><select name="" id="input_month" class="form-control" onchange="xuat_bao_cao();">'+html+'</select>');
			$('#id_time').val('THANG');
		}else if(value=='TD'){
			$('#select_time_input').fadeIn();
			// thoi diem
			$('#select_time_input').html('<label>Từ ngày:</label><input type="text" name="date_start" id="input_month_start" class="form-control datepicker" onchange="xuat_bao_cao();">');
			$('#select_time_input1').html('<label>Đến ngày:</label><input type="text" name="date_end" id="input_month_end" class="form-control datepicker" onchange="xuat_bao_cao();">');

			$('#input_month_start').datepicker({
				format: "dd-mm-yyyy",
			});
			$('#input_month_end').datepicker({
				format: "dd-mm-yyyy",
			});
			$('#id_time').val('TD');
			
		}else{
			$('#select_time_input').fadeOut();
			html='<option value="0">Chọn</option>';
			for (var i = 2013; i <2023; i++) {
				if (i==date.getFullYear()) {
					html += '<option value="'+i+'" selected>'+i+'</option>';
				}else{
					html += '<option value="'+i+'">'+i+'</option>';
				}
				
			}
			$('#select_time_input1').html('<label>Chọn năm:</label><select name="" class="form-control" id="input_year" onchange="xuat_bao_cao();">'+html+'</select>');
		}
		if (value=='QUY' || value=='THANG') {
			$('#select_time_input').fadeIn();
			html='<option value="0">Chọn</option>';
			for (var i = 2013; i <2023; i++) {
				if (i==date.getFullYear()) {
					html += '<option value="'+i+'" selected>'+i+'</option>';
				}else{
					html += '<option value="'+i+'">'+i+'</option>';
				}
			}
			$('#select_time_input1').html('<label>Chọn năm:</label><select name="" class="form-control" id="input_year" onchange="xuat_bao_cao();">'+html+'</select>');
		}
		
	});

	function xuat_bao_cao(){
		// $('#main-export-office').fadeIn();
		check =$('#select_time').val();
		error = false;
		$('#error').html('');
		if (check=='QUY') {
			check='QUY';
			$('#id_time').val('QUY');
		}else if (check=='TD') {
			date_start = $('#input_month_start').val();
			date_end = $('#input_month_end').val();
			if (date_start != '' && date_end!='') {
				s = new Date(formatDate(date_start));
				e = new Date(formatDate(date_end));
				if (parseInt(s.getTime()) > parseInt(e.getTime())) {
					error = true;
				}
			}
			month = '0';
			check ='TD';
			year ='0';
			$('#id_time').val('TD');
		}else if (check=='NAM') {
			month = '0';
			date_start = '0';
			date_end = '0';
			year =$('#input_year').val();
			check ='NAM';
			$('#input_year_ex').val(year);
		}else{
			$('#id_time').val('THANG');
			check='THANG';
		}

		if (check=='QUY' || check=='THANG') {
			month = $('#input_month').val();
			year =$('#input_year').val();
			$('#input_month_ex').val(month);
			$('#input_year_ex').val(year);
			date_start = '0';
			date_end = '0';
		}
		$('#input_date_end').val(date_end);
		$('#input_date_start').val(date_start);

		if (error==true) {
			toastr.error('Ngày kết thúc không được lớn hơn ngày bắt đầu!');
			$('#export-btn').fadeOut();
			return;
		}else{
			$('#id_check').val(check);
			// console.log(check, month, year, date_start, date_end);
			if ((check=='NAM' || check=='TD' || check=='THANG' || check=='QUY')) {
				if ((check=='NAM' || check=='THANG' || check=='QUY')) {
					if (year!=0) {
						$('#export-btn').fadeIn();
						html = '<div class="text-center" style="font-weight: 500; font-size: 14px; font-style: italic; color: red;">Xuất báo cáo để xem dữ liệu</div>';
					}else{
						$('#export-btn').fadeOut();
						html = '';
					}
				}else{
					$('#export-btn').fadeIn();
					html = '<div class="text-center" style="font-weight: 500; font-size: 14px; font-style: italic; color: red;">Xuất báo cáo để xem dữ liệu</div>';
				}
				$('#main-export-office').html(html);
			}
		}
		
		
	}
	

	// Xuat bang ket qua hoat dong kd
	function plan_ajax(year){
		return $.ajax({
			url: BASE_URL+'kpi/kpi_room',
			type: 'post',
			dataType: 'json',
			data: {select: 'revenue', tp: 'plan', year: year,quater: null },
		});
	}

	function revenue_ajax_quater1(year){
		return $.ajax({
			url: BASE_URL+'kpi/kpi_room',
			type: 'post',
			dataType: 'json',
			data: {select: 'revenue', tp: 'result', year: year,quater:1 },
		});
	}
	function revenue_ajax_quater2(year){
		return $.ajax({
			url: BASE_URL+'kpi/kpi_room',
			type: 'post',
			dataType: 'json',
			data: {select: 'revenue', tp: 'result', year: year,quater:2 },
		});
	}
	function revenue_ajax_quater3(year){
		return $.ajax({
			url: BASE_URL+'kpi/kpi_room',
			type: 'post',
			dataType: 'json',
			data: {select: 'revenue', tp: 'result', year: year,quater:3 },
		});
	}
	function revenue_ajax_quater4(year){
		return $.ajax({
			url: BASE_URL+'kpi/kpi_room',
			type: 'post',
			dataType: 'json',
			data: {select: 'revenue', tp: 'result', year: year,quater:4 },
		});
	}
</script>
<script src="<?php echo base_url().'assets/js/report.js' ?>" type="text/javascript"></script>
<?php $this->load->view("partial/footer"); ?>