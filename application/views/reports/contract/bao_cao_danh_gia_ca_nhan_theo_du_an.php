<?php $this->load->view("partial/header"); ?>
<!-- <script type="text/javascript" src="assets/js/plugins/exports/table2excel.js"></script> -->

<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>">/ Báo cáo</a>
	<a href="javascript:;" >/Danh sách kpi đánh giá cá nhân theo dự án</a>
	<div class="pull-right">
		<form action="<?php echo base_url().'ReportsKpi/download'; ?>" method="post">
			<input type="hidden" name="date_start" id="input_start" value="">
			<input type="hidden" name="date_end" id="input_end" value="">
			<button class="btn btn-success" id="export-btn" type="submit" style="display: none; margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<h3 class="text-center">Danh sách kpi đánh giá cá nhân theo dự án</h3>
			<div class="panel-body">
				<form autocomplete="off">
					<div class="form-group col-md-3 col-md-offset-3">
						<label>Từ ngày:</label>
						<input type="text" id="date_start" class="form-control date_input" onchange="xuat_bao_cao();">
					</div>
					<div class="form-group col-md-3" id="select_month_quater">
						<label>Đến ngày:</label>
						<input type="text" id="date_end" class="form-control date_input" onchange="xuat_bao_cao();">
					</div>
				</form>
			</div>
		</div>
	</div>
	<hr>
</div>
<div class="row" id="result">
	<div class="text-center text-danger" id="tb">
		
	</div>
</div>
<script type="text/javascript">
	$('.date_input').datepicker({
		format: "dd-mm-yyyy",
	});
	function xuat_bao_cao(){
		date_start = $('#date_start').val();
		date_end = $('#date_end').val();
		s = new Date(formatDate(date_start));
		e = new Date(formatDate(date_end));
		if (date_start !='' && date_end !='') {
			if (parseInt(s.getTime()) > parseInt(e.getTime())) {
				toastr.error('Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc!');
				$('#export-btn').fadeOut();
			}else{
				$('#input_start').val(date_start);
				$('#input_end').val(date_end);
				$('#export-btn').fadeIn();
				$.ajax({
					url: "<?php echo base_url().'ReportsKpi/export_data'?>",
					type: 'POST',
					dataType: 'html',
					data: {date_start:date_start, date_end: date_end},
				})
				.done(function(data) {
					$('#tb').html('Xuất báo cáo để xem số liệu!');
				})
				.fail(function() {
				})
				.always(function() {
				});
			}
		}

	}
</script>

<script src="<?php echo base_url().'assets/js/report.js' ?>" type="text/javascript"></script>
<?php $this->load->view("partial/footer"); ?>