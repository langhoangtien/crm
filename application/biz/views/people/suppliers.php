<?php
$tab = 1;
if (isset($_SESSION['tab-click'])) {
	$tab = $_SESSION['tab-click'];
	if ($tab > 3) {
		$tab = 1;
	}
}
?>
<?php $this->load->view("partial/header"); ?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku reports-printable">
			<div class="panel-heading">
				<h3 class="panel-title">										
					<span id="NhapHang" class="<?php echo ($tab == 1) ? 'selected' : '';?> item-tabs">
					<?php echo 'Lịch sử nhập hàng' ?>
					</span>									
					<span id="Thuchi" class="<?php echo ($tab == 2) ? 'selected' : '';?> item-tabs">
					<?php echo 'Lịch sử thu chi'; ?>
					</span>
					<span id="Congno" class="<?php echo ($tab == 3) ? 'selected' : '';?> item-tabs">
					<?php echo 'Công nợ'; ?>
					</span>
				</h3>
			</div>
		

			<div class="panel-body NhapHang <?php echo ($tab == 1) ? '' : 'hidden';?>">
				<div class="table-responsive">
					<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
						<thead>
							<tr>
								<td>Mã ĐH</td>
								<td>Ngày mua</td>
								<td>Mặt hàng</td>
								<td>Nhân viên</td>
								<td>Tổng giá trị ĐH</td>
								<td>Chiết khấu</td>
								<td>Hình thức thanh toán</td>
								<td>Còn nợ</td>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>	
			</div>

			<div class="panel-body Thuchi <?php echo ($tab == 2) ? '' : 'hidden';?>">
				<div class="table-responsive">
					<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
						<thead>
							<tr>
								<td>Mã thu chi</td>
								<td>Ngày thu</td>
								<td>Ghi chú</td>
								<td>Khoản thu</td>
								<td>Khoản chi</td>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>	
			</div>
			<div class="panel-body Congno <?php echo ($tab == 3) ? '' : 'hidden';?>">
				<div class="table-responsive">
					<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
						<thead>
							<tr>
								<td>Mã đơn hàng</td>
								<td>Ngày mua hàng</td>
								<td>Mặt hàng</td>
								<td><?php echo lang('nhan_vien'); ?></td>
								<td><?php echo lang('gia_tri_don_hang'); ?></td>
								<td><?php echo lang('hinh_thuc_thanh_toan'); ?></td>
								<td><?php echo lang('chiet_khau'); ?></td>
								<td><?php echo lang('cong_no'); ?></td>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>	
			</div>
		</div>
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer");?>

<script type="text/javascript">
	
$("#NhapHang").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');
	$('.panel-body').addClass('hidden');
	$('.NhapHang').removeClass('hidden');
	var ids = "1";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});


$("#Thuchi").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Thuchi').removeClass('hidden');
	var ids = "2";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});


$("#Congno").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Congno').removeClass('hidden');
	var ids = "3";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});
</script>