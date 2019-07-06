<?php $this->load->view("partial/header"); ?>
<?php $y = date("Y") ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<select class="form-control sl select" name="select" id="">
					<option value>---Chọn loại---</option>
					<option value="revenue">Điểm doanh thu</option>
					<option value="profit">Điểm lợi nhuận</option>
					<option value="total">Tổng hợp</option>
				</select>
			</div>
		</div>

			<div class="col-md-3">
			<div class="form-group">
				<select class="form-control sl type" name="type" id="">
					<option value>---Chọn loại---</option>
					<option value="plan">Kế hoạch</option>
					<option value="result">Kết quả</option>
				</select>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<select class="form-control sl year" name="year" id="">
					<option value>---Chọn năm---</option>
					<?php for ($i=-8;$i<3;$i++) {?>
					<option value="<?php echo $y+$i  ?>"><?php echo $y+$i  ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<select class="form-control sl quater" name="" id="">
					<option value>Cả năm</option>
					<option value="1">Quý I</option>
					<option value="2">Quý II</option>
					<option value="3">Quý III</option>
					<option value="4">Quý IV</option>
				</select>
			</div>
		</div>
		<div class="col-md-2 location_list">
			<select class="form-control sl location_id_list" name="" id="">
				<?php foreach ($locations as $key => $value) {?>
					<option value="<?php echo $value['location_id'] ?>"><?php echo $value['name']; ?></option>
<?php } ?>
			</select>
		</div>		
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-bordered" id="room">
				<thead class="thead">
				</thead>
				<tbody class="global_kpi">

				</tbody>
			</table>

		</div>
	</div>
</div>

<div id="kpi_edit" class="modal fade search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
            <h4 class="modal-title"> Cập nhật</h4>
        </div>
      
      
        <div class="modal-body">
           <div class="form-group">
           	<input class="value form-control" type="text">
           	<input type="hidden" class="location">
           	<input type="hidden" class="group">
           </div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary save" data-dismiss="modal">Lưu</button></div>
    </div>
</div>

</div>

<div id="density_edit" class="modal fade search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
            <h4 class="modal-title"> Cập nhật</h4>
        </div>
      
      
        <div class="modal-body">
           <div class="form-group">
           	<input class="van form-control" type="text">
           	<input type="hidden" class="group_den">
           </div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary density-save" data-dismiss="modal">Lưu</button></div>
    </div>
</div>

</div>
<div id="total_plan_edit" class="modal fade search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
            <h4 class="modal-title"> Cập nhật</h4>
        </div>
      
      
        <div class="modal-body">
           <div class="form-group">
           	<input class="total_plan_value form-control" type="text">
           	<input type="hidden" class="total_plan_name">
           	<input type="hidden" class="total_plan_tpe">
           </div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary total_plan_save" data-dismiss="modal">Lưu</button></div>
    </div>
</div>

</div>
<button style="float: right;" onclick="window.print()" id="print" class="btn btn-primary print">In</button>
<script src="<?php echo base_url('assets/js/kpi.js') ?>"></script>

<style>
	th{
		text-align: center;
	}
	.info th,.info {
		font-weight: bold;
		text-align: center
	}
	.inf {

		text-align: center
	}
	.inr{
		text-align: left;
	}
	.vcb{
		font-weight: bold !important;
	}
	.rp,.density,.total_plan{
		color: #25699a !important;
		cursor: pointer;
	}
	td{
		text-align: right;
	}

	@media print {
  	#print,.sl {
    display: none;
  }
}
</style>
<?php $this->load->view("partial/footer"); ?>