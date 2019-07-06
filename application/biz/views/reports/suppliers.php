<?php $this->load->view("partial/header"); ?>
<?php $status = lang('contract_status');?>
<div class="main-content">
	<div id="urlid" data-url=""></div>
	<div class="row">
		<!-- <div class="col-md-3 col-xs-12 col-sm-6 ">
			<div class="info-seven primarybg-info">
				<div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
				65.000.000 VNĐ	            <p>Tổng tiền hóa đơn</p>
			</div>
		</div>
 -->
		
	</div>

	
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-piluku reports-printable">
				<div class="panel-heading">
					<h3 class="panel-title space_title">		
						
						
					</h3>
					<div>
						Báo cáo - <?php echo $info['company_name'] ?>			
					</div>				
				</div>
		

				<div class="panel-body Muahang ">
					<div class="table-responsive">
						<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
							<thead>
								<tr align="center" style="font-weight:bold">
									<td align="left">Tên dịch vụ</td>
									<td align="left">Dự án liên quan</td>
									<td align="left">Hợp đồng liên quan</td>
									<td align="left">Trạng thái hợp đồng</td>
									<td align="left">Giá trị hợp đồng</td>
									<td align="left">Giá trị đã nghiệm thu/ thanh lý</td>
									<td align="right">Chi phí cho bên thứ ba</td>
									<td align="right">Người khởi tạo</td>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($supplier as $key => $value) {
									
								 ?>
								<tr>
									<td style="text-align: center"><?php echo  $value['item_name'] ?></td>
									<td style="text-align: center"><?php echo  $value['task_name'] ?></td>
									<td style="text-align: center"><?php echo  $value['contract_name'] ?></td>
									<td style="text-align: center"><?php echo  $status[$value['status']] ?></td>
									<td style="text-align: center"><?php echo  ((empty($value['total_value'])? $value['total_value']: to_currency($value['total_value']))); ?></td>
									<td style="text-align: center"><?php echo  ((empty($value['total_value_done'])? $value['total_value_done']: to_currency($value['total_value_done']))); ?></td>
									<td style="text-align: center"><?php echo  to_currency($value['item_unit_price'])?></td>
									<td style="text-align: center"><?php echo  $value['username'] ?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
									
			</div>
		</div>
	</div>

</div>
<script>
	$('#sortable_table').dataTable({
			"searching":		false,
			"lengthChange": false,
			// "sPaginationType": "bootstrap",
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
	});
</script>
<?php $this->load->view("partial/footer"); ?>