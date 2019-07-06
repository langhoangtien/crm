<?php $this->load->view("partial/header"); ?>
<!-- <script type="text/javascript" src="assets/js/plugins/exports/table2excel.js"></script> -->
<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>">Báo cáo</a>
	<a href="javascript:;" >/ Báo cáo cá nhân</a>
	<div class="pull-right">
		<form action="<?php echo base_url().'ReportPersons/download'; ?>" method="post">
			<input type="hidden" name="input_month" id="input_month_ex" value="">
			<input type="hidden" name="input_year" id="input_year_ex" value="">
			<input type="hidden" name="date_start" id="input_date_start">
			<input type="hidden" name="date_end" id="input_date_end">
			<input type="hidden" name="employee_id" id="input_employee_id">
			<input type="hidden" name="check" id="id_time">
			<button class="btn btn-success" id="export-btn-person" type="submit" style="display: none; margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<h3 class="text-center">Báo cáo hoạt động kinh doanh nhân viên</h3>
			<div class="panel-body">
				<div class="form-group col-md-2 col-md-offset-3">
					<label>Chọn thời gian</label>
					<select name="" id="select_time" class="form-control">
						<!-- <option value="NULL">Chọn</option> -->
						<option value="THANG">Tháng</option>
						<option value="QUY">Quý</option>
						<option value="NAM">Năm</option>
						<option value="TD">Từ ngày đến ngày</option>
					</select>
				</div>
				<div class="form-group col-md-2" id="select_time_input">
					<input type="hidden" name="" id="location_id" value="<?php echo $location_id; ?>">
					<label>Chọn tháng</label>
					<select name="" id="input_month" class="form-control" onchange="xuat_bao_cao_ca_nhan();">
					</select>
				</div>
				<div class="form-group col-md-2" id="select_time_input1">
					<label>Chọn năm</label>
					<select name="" class="form-control" id="input_year" onchange="xuat_bao_cao_ca_nhan();">
						<option value="0">Chọn</option>
						<?php
						$year_current=date('Y');
						for($i=$year_current-5;$i<=$year_current+5;$i++){
							if ($i==$year_current) {
								echo '<option value="'.$i.'" selected>'.$i.'</option>';
							}else{
								echo '<option value="'.$i.'" >'.$i.'</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			<p style="font-style: italic; color: red;" id="error" class="text-center"></p>
		</div>
	</div>
</div>
<div class="row" style="margin-bottom: 15px;">
	<div class="col-md-3 col-md-offset-4">
		<label for="select_employee">Chọn nhân viên:</label>
		<select name="select_employee" id="id_employee" onchange="xuat_bao_cao_ca_nhan();">
			<option value="0">Chọn</option>
			<?php 
			if (!empty($employee)) {
				foreach ($employee as $key => $value) {
					echo "<option value='".$value['id']."'>".$value['employee_name']."</option>";
				}
			}
			?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-md-12" id="main-export">
		
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var date = new Date();
		$('#id_employee').select2();
		$('#input_year_ex').val(date.getFullYear());

		html='<option value="0">Chọn</option>';
		for (var i = 1; i <=12; i++) {
			html+='<option value="'+i+'">'+i+'</option>';
		}
		$('#input_month').html(html);
		
	});

	$('#select_time').change(function(){
		var date = new Date();
		$('#main-export').fadeOut();
		value = $(this).val();
		html='<option value="0">Chọn</option>';
		if (value=='QUY') {
			html+='<option value="1">Quý 1</option>'
			html+='<option value="2">Quý 2</option>'
			html+='<option value="3">Quý 3</option>'
			html+='<option value="4">Quý 4</option>';
			$('#select_time_input').html('<label>Chọn quý</label><select name="" id="input_month" class="form-control" onchange="xuat_bao_cao_ca_nhan();">'+html+'</select>');
			$('#id_time').val('QUY');
			$('#input_month_ex').val('');
		}else if(value=='THANG'){
			// thang
			for (var i = 1; i <=12; i++) {
				html+='<option value="'+i+'">'+i+'</option>';
			}
			$('#input_month_ex').val('');
			$('#select_time_input').html('<label>Chọn tháng</label><select name="" id="input_month" class="form-control" onchange="xuat_bao_cao_ca_nhan();">'+html+'</select>');
			$('#id_time').val('THANG');
		}else if(value=='TD'){
			$('#select_time_input').fadeIn();
			// thoi diem
			$('#select_time_input').html('<label>Từ ngày:</label><input type="text" name="date_start" id="input_month_start" class="form-control datepicker" onchange="xuat_bao_cao_ca_nhan();">');
			$('#select_time_input1').html('<label>Đến ngày:</label><input type="text" name="date_end" id="input_month_end" class="form-control datepicker" onchange="xuat_bao_cao_ca_nhan();">');

			$('#input_month_start').datepicker({
				format: "dd-mm-yyyy",
			});
			$('#input_month_end').datepicker({
				format: "dd-mm-yyyy",
			});
			$('#id_time').val('TD');
		}else{
			$('#id_time').val('NAM');
			$('#select_time_input').fadeOut();
			html='<option value="0">Chọn</option>';
			for (var i = 2013; i <2023; i++) {
				if (i==date.getFullYear()) {
					html += '<option value="'+i+'" selected>'+i+'</option>';
				}else{
					html += '<option value="'+i+'">'+i+'</option>';
				}
				
			}
			$('#select_time_input1').html('<label>Chọn năm:</label><select name="" class="form-control" id="input_year" onchange="xuat_bao_cao_ca_nhan();">'+html+'</select>');
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
			$('#select_time_input1').html('<label>Chọn năm:</label><select name="" class="form-control" id="input_year" onchange="xuat_bao_cao_ca_nhan();">'+html+'</select>');
		}
		
	});

	function xuat_bao_cao_ca_nhan(){
		
		value =$('#select_time').val();
		error = false;
		$('#error').html('');
		if (value=='QUY') {
			check='QUY';
			$('#id_time').val('QUY');
		}else if (value=='TD') {
			date_start = $('#input_month_start').val();
			date_end = $('#input_month_end').val();
			s = new Date(formatDate(date_start));
			e = new Date(formatDate(date_end));
			if (parseInt(s.getTime()) > parseInt(e.getTime())) {
				error = true;
			}
			// console.log(parseInt(s.getTime()),parseInt(e.getTime()));
			month = '0';
			check ='TD';
			year ='0';
			$('#input_month_ex').val('0');
			$('#input_year_ex').val('0');
			$('#id_time').val('TD');
		}else if ($('#select_time').val()=='NAM') {
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

		if (value=='QUY' || value=='THANG') {
			month = $('#input_month').val();
			year =$('#input_year').val();
			$('#input_month_ex').val(month);
			$('#input_year_ex').val(year);
			date_start = '0';
			date_end = '0';
		}
		$('#input_date_end').val(date_end);
		$('#input_date_start').val(date_start);

		id_employee =$('#id_employee').val();
		// console.log(month,year,check,id_employee,date_start,date_end);	
		$('#input_employee_id').val(id_employee);
		if (error==true) {
			toastr.error('Ngày bắt đầu không được lớn hơn ngày kết thúc!');
		}else{
			err =false;
			if (value=='THANG' || value == 'QUY' || value=='NAM') {
				if (year=='0') {
					err=true;
				}
			}
			if (err==false) {
				if (id_employee!=0) {
					location_id = $('#location_id').val();
					$.ajax({
						url: "<?php echo base_url().'ReportPersons/export_data' ?>",
						type: 'POST',
						dataType: 'html',
						data: {month: month, year:year, id_employee: id_employee, check:check,date_start:date_start, date_end:date_end },
					})
					.done(function(data) {
						if (data!=null) {
							$('#export-btn-person').fadeIn();
							$('#main-export').fadeIn();
							$('#main-export').html(data);
						// month='';
						// year='';
						// check='';
						// id_employee='';
						// date_start=='';
						// date_end='';

						switch(check) {
							case 'THANG':
							time='month';
							title = 'Tháng';
							number = 12;
							break;
							case 'QUY':
							title = 'Quý';
							time='quater';
							number = 4;
							break;
							case 'NAM':
							title = 'Năm';
							time='year';
							number = 5;
							break;
						}
						if (check!='TD') {
							$.ajax({
								url: "<?php echo base_url().'reports/employee_graph_filter' ?>",
								type: 'POST',
								dataType: 'json',
								data: {time: time, location: location_id,colleague:id_employee,number:number, value: year},
							})
							.done(function(rs) {
								html ='';
								if (rs.categories.length >0) {
									if (check=='NAM') {
										month=5;
									}
									for (var i = 0; i < month;i++) {
										html += '<tr>';
										html +='<td>'+rs.categories[i]+'</td>';
										html +='<td class="text-right">'+rs.series['data'][i]+'</td>';
										html +='</tr>';
										
									}
								}
								$('#doanh_thu_nhan_vien').html(html);
							})
							.fail(function() {
							})
							.always(function() {
							});
						}
						
					}

				})
					.fail(function() {
					})
					.always(function() {
					});
				}else{
					$('#tbody-report-person').html('<tr><td colspan="8" class="text-center">Không có dữ liệu!</td></tr>');
				}
			}
			
		}
		
	}

</script>
<script src="<?php echo base_url().'assets/js/report.js' ?>" type="text/javascript"></script>
<?php $this->load->view("partial/footer"); ?>