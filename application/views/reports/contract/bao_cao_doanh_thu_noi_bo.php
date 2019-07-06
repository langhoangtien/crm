<?php $this->load->view("partial/header"); ?>
<!-- <script type="text/javascript" src="assets/js/plugins/exports/table2excel.js"></script> -->
<div class="panel-heading">
	<a href="<?php echo site_url('reports');?>">/ Báo cáo</a>
	<a href="javascript:;" >/Báo cáo quản trị phòng</a>
	<div class="pull-right">
		<form action="<?php echo base_url().'ReportInternal/exportWord'; ?>" method="post">
			<input type="hidden" name="contract_id" id="contract_id" value="">
			<button class="btn btn-success" id="export-btn-person" type="submit" style="display: none; margin-top: -6px;"><i class="fa fa-file-excel-o"></i> Xuất báo cáo</button>
		</form>
	</div>
</div>
<div class="box">
	<div class="box-body">
		<div class="row">
			<h3 class="text-center">Biên bản phân chia doanh thu hợp đồng</h3>
			<div class="panel-body">
				<div class="form-group col-md-3 col-md-offset-4" id="select_time_input">
					<label>Chọn hợp đồng:</label>
					<select name="" id="select_contract" onchange="xuat_bao_cao_ca_nhan();">
						<option value="0">Chọn</option>
						<?php  
						foreach ($all_contract as $key => $value) {
							echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
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
	<div class="row">
		
	</div>	
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#select_contract').select2();
	});
	function xuat_bao_cao_ca_nhan(){
		contract_id = $('#select_contract').val();
		if (contract_id==0) {
			error = '<p class="text-center" style="font-style: italic;color: red;">Chưa chọn hợp đồng!</p>'
			$('#main-export-office').html(error);
		}else{
			$('#contract_id').val(contract_id);
			$('#export-btn-person').fadeIn();
			$.ajax({
				url: "<?php echo base_url().'ReportInternal/export_data'?>",
				type: 'POST',
				dataType: 'html',
				data: {contract_id: contract_id},
			})
			.done(function(data) {
				$('#main-export-office').html(data);
			})
			.fail(function() {
			})
			.always(function() {
			});
		}
	}

</script>
<?php $this->load->view("partial/footer"); ?>