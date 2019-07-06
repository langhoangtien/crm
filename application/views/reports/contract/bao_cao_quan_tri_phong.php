<?php $this->load->view("partial/header"); ?>
<!-- <script type="text/javascript" src="assets/js/plugins/exports/table2excel.js"></script> -->
<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>">Báo cáo</a>
	<a href="javascript:;" >/Báo cáo quản trị phòng</a>
	<div class="pull-right">
		<form action="<?php echo base_url().'ReportOffices/exportword'; ?>" method="post">
			<input type="hidden" name="input_month" id="input_month_ex" value="">
			<input type="hidden" name="input_year" id="input_year_ex" value="">
			<input type="hidden" name="check" id="id_check">
			<button class="btn btn-success" id="export-btn-person" type="submit" style="display: none; margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<h3 class="text-center">Báo cáo quản trị phòng</h3>
			<input type="hidden" name="" id="id_locations" value="<?php echo $location_id; ?>">
			<div class="panel-body">
				<div class="form-group col-md-2 col-md-offset-3">
					<label>Chọn thời gian</label>
					<select name="" id="select_time" class="form-control">
						
						<option value="THANG">Tháng</option>
						<option value="QUY">Quý</option>
						<option value="NAM">Năm</option>
					</select>
				</div>
				<div class="form-group col-md-2" id="select_time_input">
					<label>Chọn tháng</label>
					<select name="" id="input_month" class="form-control" onchange="xuat_bao_cao_ca_nhan();">
					</select>

				</div>
				<div class="form-group col-md-2">
					<label>Chọn năm</label>
					<select name="" class="form-control" id="input_year" onchange="xuat_bao_cao_ca_nhan();">
						<option value="0">Chọn</option>
						<?php
						$year_current=date('Y');
						for($i=$year_current-5;$i<=$year_current+5;$i++){
							if ($i==date('Y')) {
								echo '<option value='.$i.' selected>'.$i.'</option>';
							}else{
								echo '<option value='.$i.'>'.$i.'</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box-body">
	<div class="row">
		<div class="col-md-12" id="main-export-office">

		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#id_employee').select2();
		var date = new Date();
		html='<option value="0">Chọn</option>';
		for (var i = 1; i <=12; i++) {
			html+='<option value="'+i+'">'+i+'</option>';
		}
		$('#input_month').html(html);
		$('#id_check').val('THANG');
		$('#input_month_ex').val('');
		$('#input_year_ex').val(date.getFullYear());
	});

	$('#select_time').change(function(){
		value = $(this).val();
		var date = new Date();
		html='<option value="0">Chọn</option>';
		if (value=='QUY') {
			
			html+='<option value="1">Quý 1</option>';
			html+='<option value="2">Quý 2</option>';
			html+='<option value="3">Quý 3</option>';
			html+='<option value="4">Quý 4</option>';
			$('#select_time_input').html('<label>Chọn quý</label><select name="" id="input_quater" class="form-control" onchange="xuat_bao_cao_ca_nhan();">'+html+'</select>');
			$('#id_check').val('QUY');
			$('#select_time_input').fadeIn();
		}else if(value=='THANG'){
			for (var i = 1; i <=12; i++) {
				html+='<option value="'+i+'">'+i+'</option>';
			}
			$('#select_time_input').html('<label>Chọn tháng</label><select name="" id="input_month" class="form-control" onchange="xuat_bao_cao_ca_nhan();">'+html+'</select>');
			$('#select_time_input').fadeIn();
			$('#id_check').val('THANG');
		}else{
			for (i=2013; i < 2023; i++) {
				if (i==date.getFullYear()) {
					html += '<option value="'+i+'" selected>'+i+'</option>';
				}else{
					html += '<option value="'+i+'">'+i+'</option>';
				}
			}
			$('#input_year').html(html);
			$('#select_time_input').fadeOut();
			$('#id_check').val('NAM');
		}

		if (value=='THANG' ||  value=='QUY') {
			$('#input_month_ex').val('');
		}
		$('#main-export-office').fadeOut();
	});

	function xuat_bao_cao_ca_nhan(){
		$('#export-btn-person').fadeIn();
		check =$('#select_time').val();
		if (check=='QUY') {
			month=$('#input_quater').val();
			$('#id_check').val('QUY');
		}else if (check =='NAM') {
			month='0';
			$('#id_check').val('NAM');
		}else{
			month=$('#input_month').val();
			$('#id_check').val('THANG');
		}
		year =$('#input_year').val();
		$('#input_month_ex').val(month);
		$('#input_year_ex').val(year);
		id_lc = $('#id_locations').val();
		// console.log(month, year, check);
		if (year>0) {
			$.ajax({
				url: "<?php echo base_url().'ReportOffices/export_data' ?>",
				type: 'POST',
				dataType: 'html',
				data: {month: month, year:year, check:check},
			})
			.done(function(data) {
				$('#main-export-office').fadeIn();
				$('#main-export-office').html(data);
				// console.log(data);
				// Lay ke kế hoach
				if (check=='QUY' || check =='NAM' || check=='THANG') {
					if (check=='THANG') {
						if (month<=3) {
							month = 1;
						}else if (month>3&&month<=6) {
							month =2;
						}else if (month>6 && month<=9) {
							month =3;
						}else if (month>9 && month <=12) {
							month =4;
						}
					}
					$.ajax({
						url: "<?php echo base_url().'kpi/kpi_room' ?>",
						type: 'POST',
						dataType: 'json',
						data: {select: 'revenue', tp:'plan', year:year,quater:month},
					})
					.done(function(rs) {
						// console.log($('#id_locations').val());
						// console.log(rs.dt);

						if (rs.dt.length>0) {
							for (let i = 0; i < rs.dt.length; i++) {
								if (rs.dt[i].location_id==id_lc) {
									plan_tp = rs.dt[i].location_data[1]['value'];
									plan_cp = rs.dt[i].location_data[2]['value'];
									plan_ma = rs.dt[i].location_data[3]['value'];
									plan_tvk = rs.dt[i].location_data[4]['value'];
									sum_plan = Math.round(plan_tp)+Math.round(plan_cp)+Math.round(plan_ma)+Math.round(plan_tvk);
									$('#sum_plan').html(sum_plan);
									$('#sum_plan').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});

									$('#plan_tp').html(plan_tp);
									$('#plan_cp').html(plan_cp);
									$('#plan_ma').html(plan_ma);
									$('#plan_tvk').html(plan_tvk);
									$('#plan_tp').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});
									$('#plan_cp').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});
									$('#plan_ma').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});
									$('#plan_tvk').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});
								}
							}
							// THUC TE
							$.ajax({
								url: "<?php echo base_url().'kpi/kpi_room' ?>",
								type: 'POST',
								dataType: 'json',
								data: {select: 'revenue', tp:'result', year:year,quater:month},
							})
							.done(function(rss) {
						// console.log(rss);
						// lay diem ty trong;
						$('#density_tp').html(rss.density[1]);
						$('#density_cp').html(rss.density[2]);
						$('#density_ma').html(rss.density[3]);
						$('#density_tvk').html(rss.density[4]);
						sum_density = 0;
						// if (rss.density.length >0) {
						// 	// for (var i = 1; i <= rss.density.length; i++) {
						// 	// 	sum_density +=rss.density[i];
						// 	// }

						// }
						$('#sum_density').html(parseFloat(rss.density[1]) + parseFloat(rss.density[2]) + parseFloat(rss.density[3]) + parseFloat(rss.density[4]));

						if (rss.dt.length>0) {
							for (let i = 0; i < rss.dt.length; i++) {
								if (rss.dt[i].location_id==id_lc) {
									var result_tp = Math.round(rss.dt[i].location_data[1]);
									var result_cp = Math.round(rss.dt[i].location_data[2]);
									var result_ma = Math.round(rss.dt[i].location_data[3]);
									var result_tvk = Math.round(rss.dt[i].location_data[4]);

									$('#result_tp').html(result_tp);
									$('#result_cp').html(result_cp);
									$('#result_ma').html(result_ma);
									$('#result_tvk').html(result_tvk);
									$('#result_tp').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});
									$('#result_cp').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});
									$('#result_ma').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});
									$('#result_tvk').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});

									if (plan_tp>0) {
										a= Math.round(((result_tp/(plan_tp))*100)*100)/100;
										get_point(a,'point_tp');
									}else{
										a='N/A';
										$('#point_tp').html('0');
										
									}

									if (plan_cp>0) {
										b= Math.round(((result_cp/(plan_cp))*100)*100)/100;
										get_point(b,'point_cp');
									}else{
										b='N/A';
										$('#point_cp').html('0');
									}
									if (plan_ma>0) {
										c= Math.round(((result_ma/Math.round(plan_ma))*100)*100)/100;
										get_point(c,'point_ma');
									}else{
										c='N/A';
										$('#point_ma').html('0');
									}
									if (plan_tvk>0) {
										d =Math.round(((result_tvk/Math.round(plan_tvk))*100)*100)/100;
										get_point(d,'point_tvk');
									}else{
										d='N/A';
										$('#point_tvk').html('0');
									}

									$('#result_plan_tp').html(a);
									$('#result_plan_cp').html(b);
									$('#result_plan_ma').html(c);
									$('#result_plan_tvk').html(d);

									sum_result = Math.round(result_tp)+Math.round(result_cp)+Math.round(result_ma)+Math.round(result_tvk);
									$('#sum_result').html(sum_result);

									if (sum_plan>0) {
										sum1 = Math.round(((sum_result/sum_plan)*100)*100)/100;
										get_point(sum1,'sum_point');
									}else{
										sum1='N/A';
										$('#sum_point').html('0');
									}
									
									$('#sum_result_plan').html(sum1);
									$('#sum_result').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});

								}
							}
						}

					})
							.fail(function() {
							})
							.always(function() {
							});
						}
					})
.fail(function() {
})
.always(function() {
});
}

})
.fail(function() {
})
.always(function() {
});
}


}
function get_point(percent,id){
	$.ajax({
		url: BASE_URL+'Kpi/convert_rate_d7',
		type: 'POST',
		dataType: 'json',
		data: {rate: percent},
	})
	.done(function(data) {
		$('#'+id).html(data);
	})
	.fail(function() {
	})
	.always(function() {
	});
	
}

</script>
<?php $this->load->view("partial/footer"); ?>