<?php $this->load->view("partial/header"); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>

<div class="row">

<!-- Tình trạng cv -->
	<div class="col-md-12" id="chart-box-2">
					<div class="col-md-6 chart-box" style="">
						<div id="chart2">
							<div>
								<h4 style="line-height: 33px;">TÌNH TRẠNG CÔNG VIỆC</h4>
							</div>
						<div id="task_summary">
								<table class="table" style="margin-top: 10px;">
									<tbody>
										<tr>
											<td>Công
													việc: <span id="summary_all">-</span>
											</td>
										</tr>
										<tr>
											<td>Phụ
													trách: <span id="summary_implement">-</span>
											</td>
										</tr>
										<tr>
											<td>Theo
													dõi: <span id="summary_xem">-</span>
											</td>
										</tr>
										<tr>
											<td>Đóng
													dừng: <span id="summary_cancel">-</span>
											</td>
										</tr>
										<tr>
											<td>Chưa
													thực hiện: <span id="summary_unfulfilled">-</span>
											</td>
										</tr> 
										<tr>
											<td>Đang
													tiến hành: <span id="summary_processing">-</span>
											</td>
										</tr>
										<tr>
											<td>Chậm
													tiến độ: <span id="summary_slow_proccessing">-</span>
											</td>
										</tr>
										<tr>
											<td>Đã
													hoàn thành: <span id="summary_finish">-</span>
											</td>
										</tr>
										<tr>
											<td>Đã
													hoàn thành nhưng chậm tiến độ: <span
													id="summary_slow_finish">-
											
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="mask" style="position: absolute; height: 100%;">
							<div class="ajax_spinner" style="top: 35%; left: 45%;">
								<div class="bounce1"></div>
								<div class="bounce2"></div>
								<div class="bounce3"></div>
							</div>
						</div>
					</div>
				</div>


<!-- TÌnh trnagj công việc -->

</div>

<div class="row">

	<div class="col-md-12">
		<h3>Thống kê lợi nhuận nhân viên</h3>
	</div>

</div>
<!-- Biểu đồ -->
<?php $y = date('Y') ?>

<div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<select class="form-control time-value" name="" id="time-value">
				<?php for($i=0;$i<10;$i++){ 
					echo '<option value="'.($y-$i).'">'.($y-$i).'</option>';
					}?>
			</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
		<select name="" id="number-contract" class="form-control number-contract">
			<option value="">Số lượng</option>
			<?php for($i=1;$i<13;$i++){ ?>
				<option value="<?php echo $i ?>"><?php echo $i ?></option>
			<?php } ?>
		</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select name="" id="" class="form-control revenue time-revenue">
				<option value="">Chọn thời gian</option>
				<option value="month">Theo tháng</option>
				<option value="quater">Theo quý</option>
				<option value="year">Theo năm</option>
			</select>
		</div>
	</div>
	<?php if($view) { ?>
	<div class="col-md-2">
		<div class="form-group">
			<select name="" id="" class="form-control revenue location-revenue">
				<option value="">Khu vực</option>
				<?php foreach ($location as  $value) {
					
				 ?>
				<option value="<?php echo $value['location_id']  ?>"><?php echo $value['name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
<?php } ?>
	<div class="col-md-2">
		<div class="form-group">
			<button class="form-control btn-primary btn bibi">Thực hiện</button>
		</div>
	</div>
	<?php if($view) { ?>
	<div class="col-md-2">
		<div class="form-group">
			<select class="form-control colleague-revenue" name="" id="">
				<option value="">Thêm mới nhân viên</option>
				<?php foreach ($list_employees as $value) {
					
				 ?>
				<option value="<?php echo $value['id']; ?>"><?php echo $value['employee_name'] ?></option>
				<?php } ?>
			</select>
			
		</div>
	</div>
<?php } ?>
</div>
<div class="row">
	<div class="col-md-12" id="employees-graph">
		

	</div>

</div>
<div class="row">
	<div class="col-md-12">
		<h3>Thống kê sô lượng dự án nhân viên tham gia, phụ trách</h3>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select class="form-control value-task" name="" id="">
				<?php for($i=0;$i<10;$i++){ 
					echo '<option value="'.($y-$i).'">'.($y-$i).'</option>';
					}?>
			</select>
	</div>
	</div>

		<div class="col-md-2">
		<div class="form-group">
		<select name="" id="number-contract" class="form-control number-task">
			<option value="">Số lượng</option>
			<?php for($i=1;$i<13;$i++){ ?>
				<option value="<?php echo $i ?>"><?php echo $i ?></option>
			<?php } ?>
		</select>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<select name="" id="" class="form-control task time-task">
				<option value="">Chọn thời gian</option>
				<option value="month">Theo tháng</option>
				<option value="quater">Theo quý</option>
				<option value="year">Theo năm</option>
			</select>
		</div>
	</div>
	<?php if($view) { ?>
	<div class="col-md-2">
		<div class="form-group">
			<select name="" id="" class="form-control task location-task">
				<option value="">Khu vực</option>
				<?php foreach ($location as  $value) {
					
				 ?>
				<option value="<?php echo $value['location_id']  ?>"><?php echo $value['name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
<?php } ?>
	<div class="col-md-2">
		<div class="form-group">
			<button class="form-control btn-primary btn bobo">Thực hiện</button>
	
		</div>
	</div>
	<?php if($view) { ?>
		<div class="col-md-2">
		<div class="form-group">
			<select class="form-control colleague-task" name="" id="">
				<option value="">Thêm mới nhân viên</option>
				<?php $bb= '<option value ="">Thêm mới nhân viên</option>';
				 foreach ($list_employees as $value) {
					$bb .= '<option value="'.$value['id'].'">'.$value['employee_name'].'</option>';
				 ?>
				<option value="<?php echo $value['id']; ?>"><?php echo $value['employee_name'] ?></option>
				<?php } ?>
			</select>
			
			
		</div>
	</div>
<?php } ?>
</div>
<div class="row">
	<div class="col-md-12" id="employees-graph-2">
		
	</div>

</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel-body table-responsive" id="tableF">
			<table class="table table-hover tablesorter" id="tableJ">
				<thead>
					<tr>
						<th>STT</th>
						<th>Dự án tham gia</th>
						<th>Trạng thái</th>
						<th>Tiến độ</th>
						<th>Hợp đồng liên quan</th>
						<th>Người phụ trách</th>
						<th>Người tham gia</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=1; foreach ($tasks as $key => $value) { ?>
					<tr>
						<td><?php echo $i;$i++; ?></td>
						<td><a href="javascript:;" onclick="edit_task_grid(<?php echo $value['id'] ?>);"><?php echo $value['name']; ?></a></td>
						<td><?php echo $value['trangthai'] ?></td>
						<td><?php echo $value['progress'] ?></td>
						<td><a href="<?php echo base_url('contracts/view/customer/'.$value['contract_id']) ?>"><?php echo $value['contract_name'] ?></a></td>
						<td><?php echo $value['implements'] ?></td>
						<td><?php echo $value['joins'] ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-2"></div>
</div>
<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

</div>
<!-- Ajax for result revenue employee -->
<script>
	$(document).ready(function(){
		$('.employee-btn').click(function(){
			$.ajax({
				url: '<?php echo base_url('reports/employee_total_revenue') ?>',
				dataType:'text',
				type:'post',
				data: {
					start_date: $('#start_date').val(),
					end_date:$('#end_date').val(),
					location_id : $('.location_id').val(),
					id: $('.id-employee').val(),
					
				},

				success: function(result){
					$('.employee-result').html(result);
				},
			});
		});

//DỰ ÁN

	$.ajax({
			url:"<?php echo base_url('employees/history_trans') ?>",
			dataType: "json",
			type: "post",
			data: {
				
				s_employee_id:<?php echo $info['person_id']; ?>,
			},
			success: function(response){			
				 $('.tabs').css({'display': 'none'});
                 $('#history_trans').html(response.html);
                 $('#history_trans').css({'display': 'block'});
			},
		});

});
//Onload

</script>

<script>
	
	$(document).ready(function(){
		
		var _data= {};
		_data['employee_id'] = <?php echo $info['id']; ?>;
	    coreAjax.callWithoutMask(
            '<?php echo site_url("tasks/tasks_statistic");?>',
            _data,
            function(response)
            {
                $('#summary_all').text(response.all);
                $('#summary_cancel').text(response.cancel);
                $('#summary_finish').text(response.finish);
                $('#summary_implement').text(response.implement);
                $('#summary_not_done').text(response.not_done);
                $('#summary_processing').text(response.processing);
                $('#summary_slow_finish').text(response.slow_finish);
                $('#summary_slow_proccessing').text(response.slow_proccessing);
                $('#summary_unfulfilled').text(response.unfulfilled);
                $('#summary_xem').text(response.xem);
                
                $('#chart-box-2 .mask').hide();
            }
        );

	});
</script>

<!-- BIỂU ĐỒ DOANH THU NHÂN VIÊN -->

 <script type="text/javascript" language="javascript">
	var bb ='<?php echo $bb;  ?>';
	var categories =<?php echo json_encode($list['categories']) ?>;
	var categories_task = <?php echo json_encode($list_task['categories'])  ?>;
	var series = <?php echo json_encode($list['series']) ?>;
	var series_task =<?php echo json_encode($list_task['series'])  ?>;

var hehe = new Highcharts.chart('employees-graph', {
    chart: {
        type: 'line'
    },
     credits: {
      enabled: false
    },
    title: {
        text: 'Thống kê doanh thu'
    },
    subtitle: {
        text: 'Source: Lifetek.vn'
    },
    xAxis: {
        categories: categories
    },
    yAxis: {
        title: {
            text: 'VNĐ'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: false
        }
    },
    series: [series]
});


var hoho = new Highcharts.chart('employees-graph-2', {
    chart: {
        type: 'line'
    },
     credits: {
      enabled: false
    },
    title: {
        text: 'Số lượng dự án'
    },
    subtitle: {
        text: 'Source: Lifetek.vn'
    },
    xAxis: {
        categories: categories_task
    },
    yAxis: {
        title: {
            text: 'Dự án'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: false
        }
    },
    series: [series_task]
});

$(document).on('click','.bibi',function(){
		$('.colleague-revenue').html(bb);
	$.ajax({
		url:"<?php echo base_url('reports/employee_graph_filter') ?>",
		dataType: "json",
		type: "post",
		data: {
			time: $('.time-revenue').val(),
			location : $('.location-revenue').val(),
			colleague : $('.colleague-revenue').val(),
			id: <?php echo $this->uri->segment(3,1) ?>,
			number: $('#number-contract').val(),
			value: $('#time-value').val(),

		},
		success: function(result){

			series = result.series;
			categories = result.categories;
			console.log(result.series);
				// console.log(result.categories);
    			// hehe.series[0].update(result.series);

    			if(typeof result.location == 'undefined')
    			{ 
    				while(hehe.series.length > 0)
    					hehe.series[0].remove(true);
    				hehe.xAxis[0].update({categories: categories});
    				hehe.addSeries(series);
	    			// 	hehe.update({
	    			// 	series: [series],
	    			// 	xAxis: { categories: categories}
	    			// });	    			
	    		}
	    		else
	    		{
	    			while(hehe.series.length > 0)
	    				hehe.series[0].remove(true);
	    			hehe.xAxis[0].update({categories: categories});
	    			series.forEach(function(item,index){
    				// hehe.series[index].update(item);
    				hehe.addSeries(item);
    			});
	    		}

	    	},
	    });
});

		$('.colleague-revenue').change(function(){

			$.ajax({
				url:"<?php echo base_url('reports/employee_graph_filter') ?>",
				dataType: "json",
				type: "post",
				data: {
					time: $('.time-revenue').val(),
					colleague : $('.colleague-revenue').val(),
					id: <?php echo $this->uri->segment(3,1) ?>,
					number: $('#number-contract').val(),
					value: $('#time-value').val(),
				},
				success: function(result){

					hehe.addSeries(result.series);

				},
			});

			$('option:selected', this).remove();
		});
$(document).on('click','.bobo',function(){
	$('.colleague-task').html(bb);
	$.ajax({
		url:"<?php echo base_url('reports/employee_graph_filter_task') ?>",
		dataType: "json",
		type: "post",
		data: {
			time: $('.time-task').val(),
			location : $('.location-task').val(),
			colleague : $('.colleague-task').val(),
			id: <?php echo $this->uri->segment(3,1) ?>,
			number: $('.number-task').val(),
			value: $('.value-task').val(),

		},
		success: function(result){

			series_task = result.series;
			categories_task = result.categories;
			console.log(result.series);
				// console.log(result.categories);
    			// hehe.series[0].update(result.series);

    			if(typeof result.location == 'undefined')
    			{ 
    				while(hoho.series.length > 0)
    					hoho.series[0].remove(true);
    				hoho.xAxis[0].update({categories: categories_task});
    				hoho.addSeries(series_task);
	    			// 	hehe.update({
	    			// 	series: [series],
	    			// 	xAxis: { categories: categories}
	    			// });	    			
	    		}
	    		else
	    		{
	    			while(hoho.series.length > 0)
	    				hoho.series[0].remove(true);
	    			hoho.xAxis[0].update({categories: categories_task});
	    			series_task.forEach(function(item,index){
    				// hehe.series[index].update(item);
    				hoho.addSeries(item);
    			});
	    		}

	    	},
	    });
});

	$('.colleague-task').change(function(){

			$.ajax({
				url:"<?php echo base_url('reports/employee_graph_filter_task') ?>",
				dataType: "json",
				type: "post",
				data: {
					time: $('.time-task').val(),
					colleague : $('.colleague-task').val(),
					id: <?php echo $this->uri->segment(3,1) ?>,
					number: $('.number-task').val(),
					value: $('.value-task').val(),
				},
				success: function(result){

					hoho.addSeries(result.series);

				},
			});

			$('option:selected', this).remove();
		});


  var datatableOption = {
                            "bFilter": false,
                            "bInfo": false,
                            "iDisplayStart ": 10,
                            "iDisplayLength": 10,
                            "bLengthChange": false,
                            "lengthChange": false,
                            "pageLength": 20,
                            "language": {
                                "paginate": {
                                    "first":      "First",
                                    "last":       "Last",
                                    "next":       "&gt;",
                                    "previous":   "&lt",
                                    "class":"vi"
                                    },
                                "search":         "Tìm kiếm:",
                            },
                        };
	$('#tableJ').DataTable(datatableOption);
</script>

<?php $this->load->view("partial/footer"); ?>