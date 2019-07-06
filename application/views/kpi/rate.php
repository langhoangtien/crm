<?php $this->load->view("partial/header"); ?>

<div class="container">
	<form method="post" id="rate" action="<?php echo base_url('kpi/update_rate') ?>">
	<div class="row">
	<h3>Tỷ lệ KPI</h3>

			<div class="col-sm-8">
				<table class="table">
					<thead>
						<tr>
							<th>Từ(%)</th>
							<th>Đến(%)</th>
							<th>Điểm</th>
							<th>Hành động</th>
						</tr>
					</thead>
					<tbody class="level">
						<?php foreach ($rates as $key => $rate) {
						 ?>
						<tr>
							
							<td><input class="form-control" value="<?php echo $rate['rate_start'] ?>" type="text" name="rate_start[]"></td>
							<td><input value="<?php echo $rate['rate_end'] ?>" class="form-control" name="rate_end[]" type="text"></td>
							<td><input value="<?php echo $rate['point'] ?>" class="form-control" name="rate_point[]" type="text"></td>
							<td class="del-rate"><button class="btn btn-primary">Xóa</button></td>
						</tr>
					<?php } ?>
					</tbody>
					<tbody><tr class="add-rate">
						<td colspan="4"><span style="width: 100%;" class="btn btn-primary">Thêm mới</span></td>
					</tr>
				</tbody></table>
				<input class="submit btn btn-primary" type="submit" value="Lưu">
			</div>
	</div>
</form>
</div>
<script>
	$(document).on('click','.add-rate',function(){
			$('.level').append('<tr></td><td><input name="rate_start[]" value="" class="form-control" type="text"></td><td><input value="" name="rate_end[]" class="form-control" type="text"></td><td><input value="" name="rate_point[]" class="form-control" type="text"></td><td class="del-rate"><button class="btn btn-primary">Xóa</button></td></tr>');

		});


	$(document).on("click", ".del-rate", function() {
  $(this).parent().remove();
		});




$("#rate").validate({
  rules: {
    // simple rule, converted to {required:true}
    "rate_start[]": {
    	required:true,
    	digit:true
    },
    // compound rule
    "rate_end[]": {
      required:true,
      digit:true
    },
    "rate_point[]": {
      required:true,
      digit:true
    }
  },
  messages: {
  	"rate_start[]":{
  		required:"Bắt buộc nhập",
  	},
  	"rate_end[]":{
  		required:"Bắt buộc nhập",
  	}
  }
});



</script>
<?php $this->load->view("partial/footer"); ?>