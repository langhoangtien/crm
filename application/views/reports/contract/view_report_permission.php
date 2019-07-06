<?php $this->load->view("partial/header"); ?>
<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>">Báo cáo</a>
	<a href="javascript:;" >/Báo cáo xác nhận quyền truy cập hệ thống</a>
	<div class="pull-right">
		<form action="<?php echo base_url().'ReportsPermissions/download'; ?>" method="post">
			<input type="hidden" name="employee_id" id="employee_id" value="">
			<button class="btn btn-success" id="export-btn" type="submit" style=" margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<h3 class="text-center">Báo cáo xác nhận quyền truy cập hệ thống</h3>
			<div class="panel-body">
				<form autocomplete="off">
					<div class="form-group col-md-3 col-md-offset-4" id="select_month_quater">
						<label>Chọn nhân viên</label>
						<select name="" class="" id="id_employee" onchange="xuat_bao_cao();">
							<option value="">Tất cả</option>
							<?php  
							if (!empty($employee_all)) {
								foreach ($employee_all as $key => $value) {
									echo "<option value='".$value['person_id']."'>".$value['employee_name']."</option>";
								}
							}
							?>
						</select>
					</div>

				</form>
			</div>
		</div>
	</div>
	<hr>
</div>
<script>
	$('#id_employee').select2();
	function xuat_bao_cao(){
		$('#employee_id').val($('#id_employee').val());
	}
</script>
<?php $this->load->view("partial/footer"); ?>