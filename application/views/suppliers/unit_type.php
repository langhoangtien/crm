<?php $this->load->view("partial/header"); ?>



<div class="row">
	<div class="col-md-12">
		<h3>Quản lý loại đơn vị</h3>
	</div>
	
	<div class="col-md-4">
		<div class="form-group">
			<select  class="form-control unit" name="" id="">
				<?php foreach($unit_type as $value) {?>
				<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
			<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-md-1">
		<div class="form-group">
			<button id="btn" type="button" class="btn btn-info form-control"  data-target="#myM">Sửa</button>
		</div>
	</div>
	<div class="col-md-1">
		<div class="form-group">
			<button type="button" class="del btn btn-info form-control"  data-target="#myM">Xóa</button>
		</div>
	</div>


</div>

<div class="row">
	
	<div class="col-md-4">
		<div class="form-group">
			<label for="" class="">Thêm mới</label>
			<input class="form-control add" type="text">
			<button class="btn add-unit">Thêm</button>
		</div>
	</div>
	

</div>


<div id="myM" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
      	<div class="form-group">
	      	<input class="unit_name form-control" type="text" value="">
      	</div>
        
      </div>
		
      <div class="modal-footer">
      	<button class="btn save btn-danger">Lưu lại</button>
      </div>
    </div>

  </div>
</div>


<script>
	$(document).ready(function(){



$('.add-unit').click(function(){
				$.ajax({
				type:'post',
				url:"<?php echo base_url('suppliers/add_unit_type')?>",
				dataType:'text',
				data: {
					name: $('.add').val(),					
				},
				success: function(result){
					window.location.reload();
				},
			});
		});




		$('#btn').click(function(){
			$('#myM').toggleClass('fade');
			$('#myM').toggle();
			var unit = $('.unit option:selected').text();
			$('.unit_name').val(unit)

		});


		$('.close').click(function(){
			$('#myM').toggleClass('fade');
			$('#myM').toggle();
		});


		$('.save').click(function(){
				$.ajax({
				type:'post',
				url:"<?php echo base_url('suppliers/save_unit_type')?>",
				dataType:'text',
				data: {
					id: $('.unit').val(),
					name: $('.unit_name').val(),					
				},
				success: function(result){
					window.location.reload();
				},
			});
		});

			$('.del').click(function(){

			
					bootbox.confirm("Bạn có chắc chắn muốn xóa không", function(re){
				if(re)
				{	

				$.ajax({
				type:'post',
				url:"<?php echo base_url('suppliers/del_unit_type')?>",
				dataType:'text',
				data: {
					id: $('.unit').val(),					
				},
				success: function(result){
					window.location.reload();
				},
				});

				}

 				});
			

		});


	});
</script>



<?php $this->load->view("partial/footer"); ?>
