<div class="col-md-12">
	<h4 class="text-center" style="font-weight: 600;"><?php echo $title; ?></h4>
	<h5 style="font-weight: 600;">Danh sách KPI đánh giá cá nhân theo dự án từ ngày đến ngày</h5>
	<div class="table-responsive" id="table-export-1">
		<table class="table tablesorter table-reports table-bordered display table-hover" id="table_report_contract_cus">
			<thead>
				<tr>
					<td rowspan="3">Tên dự án</td>
					<td rowspan="3">Tổng doanh thu</td>
					<td rowspan="3">Thu chia cho phòng ban</td>
					<td rowspan="3">Doanh thu thực hiện</td>
					<td rowspan="3">Chi phí bên thứ ba</td>
					<td rowspan="3">Lợi nhuận tạm tính</td>
					<td rowspan="3">Hệ số K</td>
				</tr>
				<tr>
					<td colspan="2">Nhân viên A</td>
				</tr>
				<tr>
					<td>Đóng góp %</td>
					<td>Kpi đống góp (đồng)</td>
				</tr>
			</thead>
			<tbody id="tbody-report">
				<?php  
					if (!empty($contract)) {
						foreach ($contract as $key => $value) {
							?>
								<tr>
									<td><?php echo $value['name_task']; ?></td>

								</tr>
							<?php
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>
