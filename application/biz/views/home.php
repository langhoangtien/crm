<?php $this->load->view("partial/header"); 
$this->load->helper('demo');
// var_dump($loinhuan);die();
?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<style type="text/css">
tr.delivery_warning_lv1 {
	background-color: #<?php echo $this->config->item('color_warning_level1'); ?> !important;
}

tr.delivery_warning_lv2 {
	background-color: #<?php echo $this->config->item('color_warning_level2'); ?> !important;
}

tr.delivery_warning_lv3 {
	background-color: #<?php echo $this->config->item('color_warning_level3'); ?> !important;
}
</style>

<?php if ($can_show_mercury_activate) { ?>
	<!-- mercury activation message -->
	<div class="row " id="mercury_container">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					<a id="dismiss_mercury" href="<?php echo site_url('home/dismiss_mercury_message') ?>" class="pull-right text-danger"><?php echo lang('common_dismiss'); ?></a>
					<div id="mercury_activate_container">
						<a href="https://4biz.vn/mercury_activate.php" target="_blank"><?php echo img(
							array(
								'src' => base_url().'assets/img/mercury_logo.png',
								'class'=>'hidden-print',
								'id'=>'mercury-logo',
							)); ?>
						</a>
						<h3><a href="https://4biz.vn/mercury_activate.php" target="_blank"><?php echo lang('common_credit_card_processing'); ?></a></h3>
						<a href="https://4biz.vn/mercury_activate.php" class="mercury_description" target="_blank">
							<?php echo lang('home_mercury_activate_promo_text');?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php  } ?>

<?php 
$this->load->helper('demo');
if (!is_on_demo_host() && !$this->config->item('hide_test_mode_home')) { ?>
	<?php if($this->config->item('test_mode')) { ?>
		<div class="alert alert-danger">
			<strong><?php echo lang('common_in_test_mode'); ?>. <a href="sales/disable_test_mode"></strong>
				<a href="<?php echo site_url('home/disable_test_mode'); ?>" id="disable_test_mode"><?php echo lang('common_disable_test_mode');?></a>
			</div>
		<?php } ?>

		<?php if(!$this->config->item('test_mode')) { ?>
			<div class="row " id="test_mode_container">
				<div class="col-md-12">
					<div class="panel">
						<div class="panel-body text-center">
							<a id="dismiss_test_mode" href="<?php echo site_url('home/dismiss_test_mode') ?>" class="pull-right text-danger"><?php echo lang('common_dismiss'); ?></a>
							<strong><?php echo anchor(site_url('home/enable_test_mode'), '<i class="ion-ios-settings-strong"></i> '.lang('common_enable_test_mode'),array('id'=>'enable_test_mode')); ?></strong>
							<p><?php echo lang('common_test_mode_desc')?></p>
						</div>
					</div>
				</div>
			</div>

		<?php } ?>
	<?php } ?>

	<div class="text-center">					

	<?php if (!$this->config->item('hide_dashboard_statistics') /* && (!$this->agent->is_mobile() || $this->agent->is_tablet()) */) { ?>
		<div class="row">
			<div class="col-md-3 col-sm-6 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-aqua">
					<div class="inner">
						<h3><?php echo $total_contracts; ?></h3>

						<p>Số HĐ đang thực hiện</p>
					</div>
					<div class="icon">
						<i class="ion ion-bag"></i>
					</div>
					<a href="<?php echo site_url('contracts/index/customer?status=not-liquidated') ?>" class="small-box-footer">Xem chi tiết</a>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-md-3 col-sm-6 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-green">
					<div class="inner">
						<h3><?php echo $total_tasks; ?></h3>

						<p>Tổng số dự án đang thực hiện</p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
					<a href="<?php echo site_url('tasks') ?>" class="small-box-footer">Xem chi tiết</a>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-md-3 col-sm-6 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo $total_customers; ?></h3>

						<p>Tổng khách hàng</p>
					</div>
					<div class="icon">
						<i class="ion ion-ios-people"></i>
					</div>
					<a href="<?php echo site_url('customers') ?>" class="small-box-footer">Xem chi tiết</a>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-md-3 col-sm-6 col-xs-6">
				<!-- small box -->
				<div class="small-box bg-red">
					<div class="inner">
						<h3><?php echo $total_sales; ?></h3>

						<p>Tổng nhu cầu khách hàng</p>
					</div>
					<div class="icon">
						<i class="ion ion-ios-list"></i>
					</div>
					<?php 
					if ($this->Employee->has_module_action_permission('sales','view_scope_all',$this->Employee->get_logged_in_employee_info()->person_id) || $this->Employee->has_module_action_permission('sales','view_scope_location',$this->Employee->get_logged_in_employee_info()->person_id)) {
						?>
						<a href="<?php echo site_url('sales/suspended') ?>" class="small-box-footer">Xem chi tiết</a>
						<?php
					}else{
						?>
						<a href="<?php echo site_url('sales/suspended/1') ?>" class="small-box-footer">Xem chi tiết</a>
						<?php
					}
					?>

				</div>
			</div>
			<!-- ./col -->
		</div>
	</div>

<?php } ?>


<?php 

$currentUserFullname = $this->Employee->get_logged_in_employee_info()->first_name;

?>

<div class="row">
	<div class="col-md-12"
	style="text-align: right; padding-bottom: 20px;">
	<div class="btn-group">
		<?php if ($this->Employee->has_module_action_permission('home', 'view_scope_all', $this->Employee->get_logged_in_employee_info()->person_id)||$this->Employee->has_module_action_permission('home', 'view_scope_location', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
			<button id="filter" style="width: 186px; text-align: left;"
			type="button" class="btn btn-default dropdown-toggle"
			data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="text_filter"><?php echo $currentUserFullname; ?></span><span class="caret"
			style="float: right; margin-top: 8px;"></span>
		</button>
	<?php } ?>
	<ul class="dropdown-menu"
	style="right: 0; left: auto; border-radius: 0;">
	<?php foreach ($employees as $employee) {?>
		<li>
			<a class="employee_filter" data-id="<?php echo $employee['id']?>" style="padding: 3px 10px; width: 184px;"><?php echo $employee['employee_name']; ?></a>
		</li>
	<?php } ?>
</ul>
</div>
</div>
</div>

<div class="box box-danger">
	<div class="box-body">
		<div class="row" style="margin-bottom: 100px;">
			<div class="" id="chart-box-1">
				<div class="col-md-6 chart-box" style="height: 350px;">
					<div id="chart1"></div>
					

				</div>
			</div>
			<div class="" id="chart-box-3">
				<div class="col-md-6 chart-box" style="height: 350px;">
					<div id="chart3"></div>

				</div>
			</div>
		</div>
		<a class="btn btn-primary view_more" style="float: right;" href="<?php echo base_url('reports/specific_employees_d13/'.$this->Employee->get_logged_in_employee_info()->id) ?>">Xem thêm</a>
	</div>
</div>
<div class="box box-primary">
	<div class="box-body">
		<div class="row">
			<div class="" id="chart-box-11">
				<div class="col-md-6 chart-box" style="">
					<div id="chart11">
						<div>
							<h4 style="line-height: 33px;">TOP DỰ ÁN</h4>
						</div>
						<div id="top_duan"></div>
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
			<div class="" id="chart-box-2">
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

			<div class="col-md-12" style="text-align: right; padding-bottom: 20px;">
				<div class="btn-group">

					<select id="location" style="width: 186px; text-align: left;" type="button" class="form-control"> 
						<?php foreach ($locations as $key => $value) { ?>
							<option value="<?php echo $value['location_id'] ?>"><?php echo $value['name']; ?></option>
						<?php  } ?>
					</select>

				</div>
			</div>
			<div class="" id="chart-box-15">
				<div class="col-md-12 chart-box" style="">
					<div id="topdanhthu">

					</div>

				</div>
			</div>

			<div class="" id="chart-box-13">
				<div class="col-md-12 chart-box" style="">
					<div id="toploinhuan">

					</div>

				</div>
			</div>
		</div>
	</div>
</div>


<!-- /.box-body -->
<div class="box-footer text-center">
	<!-- <a href="javascript:void(0)" class="uppercase">View All Users</a> -->
</div>
<!-- /.box-footer -->



<style>.highcharts-title {
	font-size: 1.5rem;
	font-family: Helvetical, Arial
	/*font-weight: bold;*/

}</style>




<!-- Location Message to employee -->
<?php if($this->config->item('cap_nhat_du_lieu') == 0) $this->load->view('update'); ?>

<script>



	var categories =<?php echo json_encode($list['categories']) ?>;
	var categories_task = <?php echo json_encode($list_task['categories'])  ?>;
	var series = <?php echo json_encode($list['series']) ?>;
	var series_task =<?php echo json_encode($list_task['series'])  ?>;

	var hehe = new Highcharts.chart('chart1', {
		chart: {
			type: 'line'
		},
		credits: {
			enabled: false
		},
		title: {
			text: '<h4 style="padding-bottom: 15px;">BIỂU ĐỒ DOANH THU CÁ NHÂN</h4>',
			align:'left'
		},
		subtitle: {

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


	var hoho = new Highcharts.chart('chart3', {
		chart: {
			type: 'line'
		},
		credits: {
			enabled: false
		},
		title: {
			text: '<h4 style="padding-bottom: 15px;">SỐ LƯỢNG DỰ ÁN</h4>',
			align:'left'
		},
		subtitle: {

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



	$(document).ready(function(){

		var url   = BASE_URL + 'tasks/tasks_statistic';


		load_kpi_chart_data();

		$('.employee_filter').unbind('click').bind('click', function(){
			$('#chart-box-1 .mask').show();
			$('#chart-box-3 .mask').show();
			$('#chart-box-11 .mask').show();
			$('#chart-box-2 .mask').show();
			$('#filter .text_filter').text($(this).text());
			var _data = {
				time:'quater',
				number:4,
				id:$(this).data('id'),
			};
			var url_rp = BASE_URL+'reports/specific_employees_d13/'+ $(this).data('id');
			$('.view_more').attr('href',url_rp);
			coreAjax.callWithoutMask(
				'<?php echo site_url("reports/employee_graph_filter");?>',
				_data,
				function(response)
				{
					hehe.series[0].update(response.series);
				}
				);

			coreAjax.callWithoutMask(
				'<?php echo site_url("reports/employee_graph_filter_task");?>',
				_data,
				function(response)
				{
					hoho.series[0].update(response.series);
				}
				);

			coreAjax.callWithoutMask(
				'<?php echo site_url("home/getTopDUAN");?>',
				_data,
				function(response)
				{
					$('#top_duan').html(response.html);
					$('#chart-box-11 .mask').hide();
				}
				);

			_data['employee_id'] = $(this).data('id');
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



		function load_kpi_chart_data(){


			let dt={};
			let d = new Date();
			let location = $('#location').val();
			let doanhthuth;
			let loinhuanth;
			let doanhthukh;
			let loinhuankh;
			let ppr_doanhthu =[];
			let ppr_loinhuan=[];
			let year = d.getFullYear();
			$.ajax({
				type: 'POST',
				url: '<?php echo site_url('kpi/kpi_global') ?>',
				dataType: 'json',
				data:{
					year: year,
					type: 'result',
					kpi: 1,
				},
				success: function(response) {
					dt = response.dt;
					$.each(dt,function(index,item){

						if(item.location_id == location)
						{
							location_data = item.location_data;
							kpi_vcbs =item.kpi_vcbs;
						}


					});

					doanhthuth =  Object.keys(location_data).slice(0,4).map(key => parseInt(location_data[key]));
					loinhuanth =  Object.keys(location_data).slice(5,9).map(key => parseInt(location_data[key]));
					doanhthukh = Object.keys(kpi_vcbs).slice(0,4).map(key => parseFloat(kpi_vcbs[key]));
					loinhuankh = Object.keys(kpi_vcbs).slice(5,9).map(key => parseFloat(kpi_vcbs[key]));

					$.each(doanhthukh,function(index,item){
						let ppr;
						if(item == 0)
						{
							ppr =0;
						}
						else{
							ppr = doanhthuth[index]/item*100;          
							ppr = Math.round(ppr); 
						}

						ppr_doanhthu.push(ppr);

					});

					$.each(loinhuankh,function(index,item){
						let ppr;
						if(item == 0)
						{
							ppr =0;
						}
						else{
							ppr = loinhuanth[index]/item*100;
							ppr = Math.round(ppr);
						}

						ppr_loinhuan.push(ppr);

					});

					console.log(doanhthukh);
					console.log(doanhthuth);
					console.log(ppr_doanhthu);
					Highcharts.chart('toploinhuan', {
						title: {
							text: '<h4>BIỂU ĐỒ LỢI NHUẬN</h4>'
						},
						credits: {
							enabled: false
						},
						xAxis: {
							categories: <?php echo $categories ?>
						},
  yAxis: [{ // Primary yAxis
  	labels: {
  		format: '{value}Tỷ đồng',
  		style: {
  			color: Highcharts.getOptions().colors[2]
  		}
  	},
  	title: {
  		text: '',
  		style: {
  			color: Highcharts.getOptions().colors[2]
  		}
  	},
  	opposite: true

    }, { // Secondary yAxis
    	gridLineWidth: 0,
    	title: {
    		text: 'VNĐ',
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	},
    	labels: {
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	}

    }, { // Tertiary yAxis
    	gridLineWidth: 0,
    	title: {
    		text: '',
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	},
    	labels: {
    		format: '{value} %',
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	},
    	opposite: true
    }],
    labels: {
    	items: [{
    		style: {
    			left: '50px',
    			top: '18px',
    			color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
    		}
    	}]
    },
    series: [{
    	type: 'column',
    	name: 'Lợi nhuận thực hiện',
    	yAxis: 1,
    	data: loinhuanth,
    	color: Highcharts.getOptions().colors[2]
    }, {
    	type: 'column',
    	name: 'Lợi nhuận kế hoạch',
    	yAxis: 1,
    	data: loinhuankh,
    	color: Highcharts.getOptions().colors[0]
    },
    {
    	type: 'spline',
    	name: 'Tỷ lệ TH/KH',
    	data: ppr_loinhuan,
    	yAxis: 2,
    	marker: {
    		lineWidth: 2,
    		lineColor: Highcharts.getOptions().colors[3],
    		fillColor: 'white'
    	}
    }]
});

					Highcharts.chart('topdanhthu', {
						title: {
							text: 'BIỂU ĐỒ DOANH THU'
						},
						credits: {
							enabled: false
						},
						xAxis: {
							categories: <?php echo $categories ?>
						},
  yAxis: [{ // Primary yAxis
  	labels: {
  		format: '{value}Tỷ đồng',
  		style: {
  			color: Highcharts.getOptions().colors[2]
  		}
  	},
  	title: {
  		text: '',
  		style: {
  			color: Highcharts.getOptions().colors[2]
  		}
  	},
  	opposite: true

    }, { // Secondary yAxis
    	gridLineWidth: 0,
    	title: {
    		text: 'VNĐ',
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	},
    	labels: {
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	}

    }, { // Tertiary yAxis
    	gridLineWidth: 0,
    	title: {
    		text: '',
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	},
    	labels: {
    		format: '{value} %',
    		style: {
    			color: Highcharts.getOptions().colors[1]
    		}
    	},
    	opposite: true
    }],
    labels: {
    	items: [{
    		style: {
    			left: '50px',
    			top: '18px',
    			color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
    		}
    	}]
    },
    series: [{
    	type: 'column',
    	name: 'Doanh thu thực hiện',
    	yAxis: 1,
    	data: doanhthuth,
    	color: Highcharts.getOptions().colors[7]
    }, {
    	type: 'column',
    	name: 'Doanh thu kế hoạch',
    	yAxis: 1,
    	data: doanhthukh,
    	color: Highcharts.getOptions().colors[4]
    },
    {
    	type: 'spline',
    	name: 'Tỷ lệ TH/KH',
    	data: ppr_doanhthu,
    	yAxis: 2,
    	marker: {
    		lineWidth: 2,
    		lineColor: Highcharts.getOptions().colors[3],
    		fillColor: 'white'
    	}
    }]
});

				}
			});


}

// Thay đổi location
$(document).on('change load','#location',function(){
	load_kpi_chart_data();

});

var _data = {};
_data['id'] = 'chart1';


coreAjax.callWithoutMask(
	'<?php echo site_url("home/getTopDUAN");?>',
	_data,
	function(response)
	{
		$('#top_duan').html(response.html);
		$('#chart-box-11 .mask').hide();
	}
	);

var _data= {};
_data['employee_id'] = <?php echo $this->Employee->get_logged_in_employee_info()->id  ?>;
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
<!-- Location Message to employee -->



$('#dTableB').DataTable({
	"sPaginationType": "bootstrap",
	"bFilter": false,
	"bInfo": false,
	"iDisplayStart ": 10,
	"iDisplayLength": 10,
	"bLengthChange": false
});

$('#dTableA').DataTable({
	"sPaginationType": "bootstrap",
	"bFilter": false,
	"bInfo": false,
	"iDisplayStart ": 10,
	"iDisplayLength": 10,
	"bLengthChange": false
});

<?php if ($show_warning_orders_modal){ ?>
	$('#warning_orders_modal').modal('show');
<?php } ?>

<?php if ($config_show_warning_expire_time){ ?>
	$('#warning_expire_modal').modal('show');
<?php } ?>

$("#dismiss_mercury").click(function(e){
	e.preventDefault();
	$.get($(this).attr('href'));
	$("#mercury_container").fadeOut();

});

$("#dismiss_test_mode").click(function(e){
	e.preventDefault();
	$.get($(this).attr('href'));
	$("#test_mode_container").fadeOut();
});

<?php if($choose_location && count($authenticated_locations) > 1) { ?>

	$('#choose_location_modal').modal('show');

	$(".set_employee_current_location_after_login").on('click',function(e)
	{
		e.preventDefault();

		var location_id = $(this).data('location-id');
		$.ajax({
			type: 'POST',
			url: '<?php echo site_url('home/set_employee_current_location_id'); ?>',
			data: { 
				'employee_current_location_id': location_id, 
			},
			success: function(){

				window.location = <?php echo json_encode(site_url('home')); ?>;
			}
		});

	});

<?php } ?>


<?php if(isset($month_sale) && !isset($month_sale['message'])){ ?>
	var data = {
		labels: <?php echo $month_sale['day'] ?>,
		datasets: [
		{
			fillColor : "#5d9bfb",
			strokeColor : "#5d9bfb",
			highlightFill : "#5d9bfb",
			highlightStroke : "#5d9bfb",
			data: <?php echo $month_sale['count'] ?>
		}
		]
	};
	var ctx = document.getElementById("charts").getContext("2d");
	var myBarChart = new Chart(ctx).Bar(data, {
		responsive : true
	});
<?php } ?>



$('.piluku-tabs a').on('click',function(e) {
	e.preventDefault();
	$('.piluku-tabs li').removeClass('active');
	$(this).parent('li').addClass('active');
	var type = $(this).attr('data-type');
	$.post('<?php echo site_url("home/sales_widget/'+type+'"); ?>', function(res)
	{
		var obj = jQuery.parseJSON(res);
		if(obj.message)
		{
			$(".chart").html(obj.message);
			return false;
		}

		renderChart(obj.day, obj.count);

		myBarChart.update();
	});
});

function renderChart(label,data){

	$(".chart").html("").html('<canvas id="charts" width="400" height="400"></canvas>');
	var lineChartData = {
		labels : label,
		datasets : [
		{
			fillColor : "#5d9bfb",
			strokeColor : "#5d9bfb",
			highlightFill : "#5d9bfb",
			highlightStroke : "#5d9bfb",
			data : data
		}
		]

	}
	var canvas = document.getElementById("charts");
	var ctx = canvas.getContext("2d");

	myLine = new Chart(ctx).Bar(lineChartData, {
		responsive: true,
		maintainAspectRatio: false
	});
}
});



</script>

<?php $this->load->view("partial/footer"); ?>