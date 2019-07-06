<?php $this->load->view("partial/header"); ?>

<div class="row report-listing">
	<div class="col-md-6">
		<div class="panel-body">
			<div class="list-group">
				<!-- Thống kê hợp đồng -->
				<a href="<?php echo site_url('reports/report_contract');?>" class="list-group-item" id="report_contract"><i class="icon ti-receipt"></i>Đối chiếu hợp đồng</a>
				<a href="<?php echo site_url('reports/finance_stock');?>" class="list-group-item"><i class="icon ti-receipt"></i>Tổng hợp hợp đồng</a>
				<a href="<?php echo site_url('reportBusiness/view');?>" class="list-group-item"><i class="icon ti-receipt"></i> Báo cáo gửi phòng Phân tích</a>
				<a href="<?php echo site_url('ReportPersons/view');?>" class="list-group-item"><i class="icon ti-receipt"></i>Báo cáo cá nhân</a>
				<a href="<?php echo site_url('ReportOffices/view');?>" class="list-group-item"><i class="icon ti-receipt"></i>Báo cáo quản trị phòng</a>
				<a href="<?php echo site_url('ReportManagers/view');?>" class="list-group-item"><i class="icon ti-receipt"></i>Báo cáo quản trị tổng thể</a>
				<a href="<?php echo site_url('ReportInternal/view');?>" class="list-group-item"><i class="icon ti-receipt"></i>Biên bản phân chia doanh thu hợp đồng</a>
				<a href="<?php echo site_url('reports/revenue');?>" class="list-group-item"><i class="icon ti-receipt"></i>Báo cáo dự báo doanh thu</a>
				<a href="<?php echo site_url('ReportsKpi/view');?>" class="list-group-item"><i class="icon ti-receipt"></i>Dánh sách KPI đánh giá cá nhân theo dự án</a>
				<a href="<?php echo site_url('ReportsPermissions/view');?>" class="list-group-item"><i class="icon ti-receipt"></i>Báo cáo xác nhận quyền truy cập hệ thống</a>
			</div>
		</div> <!-- /panel -->
	</div>
</div>
<script type="text/javascript">
	$('.parent-list a').click(function(e){
		e.preventDefault();
		$('.parent-list a').removeClass('active');
		$(this).addClass('active');
		var currentClass='.child-list .'+ $(this).attr("id");
		$('.child-list .page-header').html($(this).html());
		$('.child-list .list-group').addClass('hidden');
		$(currentClass).removeClass('hidden');
		$('html, body').animate({
			scrollTop: $("#report_selection").offset().top
		}, 500);
	});
</script>
<?php $this->load->view("partial/footer"); ?>
