<?php $this->load->view("partial/header"); ?>
<div class="row">
	<div class="col-md-6">
		<form method="post" id="rank-lv" action="<?php echo base_url('employees/level_save') ?>">
		<div class="row">

			<div class="col-sm-4">
				<h4>Ngạch</h4>
				<div class="form-group">
					<select name="parent_id" class="form-control rank_value" id="rank_select">
						<?php foreach ($ranks as $value) {
							# code...
						
						echo '<option value="'.$value["id"].'">'.$value["name"].'</option>';
						}?>
					</select>


			
					<a class="del-rank" href="javascript:;"><span class="glyphicon glyphicon-trash">Xóa</span></a>
					<a data-toggle="modal" data-target="#my_modal" class="r edit-rank"><span class="glyphicon glyphicon-edit">Sửa</span></a>
					<a data-toggle="modal" data-target="#my_modal" class="r add-rank"><span class="glyphicon glyphicon-plus">Thêm</span></a>
				</div>

							
						
			</div>
			
			
			<div class="col-sm-8">
				<table class="tablesorter table table-hover">
					<thead>
						<tr>
							<th>Cấp bậc</th>
							<th>Lương cơ bản</th>
							<th>Hành động</th>
							<th></th>
						</tr>
					</thead>
					<tbody class="level">
						<?php foreach ($level2 as $value) {
							# code...
						 ?>
						<tr>
							
							<td><input class="form-control" value="<?php echo $value['name'] ?>" type="text" name="level_name[]"></td>
							<td><input value="<?php echo $value['salary'] ?>" class="form-control level_input" name="level_salary[]" type="text"></td>
							<td class="del-level" ><button class="btn">Xóa</button></td>
							<td><input class="form-control" value="<?php echo $value['id'] ?>" type="hidden" name="level_id[]"></td>

						</tr>
					<?php } ?>
						
					</tbody>
						<tr class="add-level">
							<td colspan="4"><span style="width: 100%;" class="btn btn-primary">Thêm mới</span></td>
						</tr>
				</table>
				<button class="save-ranks btn btn-success">Lưu lại</button>
			</div>
		</div>
	</form>

	</div>
<div class="col-md-4">
<table class="tablesorter table table-hover table-ranks">
	<thead>
		<tr class="ranks">
			<th>Ngạch</th>
			<th>Cấp bậc</th>
			<th>Lương cơ bản</th>
		</tr>
	</thead>
	<tbody>

		<?php foreach ($ranks as $value) {

//Đếm số phần tử mà parent id = value
$count = 0;
foreach ($level as $lv) {
    if ($lv ['parent_id'] == $value['id']) {
        $count++;
    }
}
			?>


			<tr class="ranks-top">
				<?php if($count>0) { ?>
			<td rowspan="<?php echo $count;  ?>" class="ranks"><?php echo $value['name'] ?></td>
			<?php }
			foreach ($level as $value2) {
				if($value2['parent_id']==$value['id']){
					$rowspan = "";
			
		 ?>
		
			<td><?php echo $value2['name'] ?></td>
			<td><?php echo to_currency($value2['salary'])?></td>
		</tr>
	<?php }}} ?>
		
	</tbody>
</table>
</div>
</div>





<style>
	.ranks {
		border-bottom: 2px solid black;
	}
	.table-ranks{
		border: 2px solid black;
	}
	.ranks-top {
	border-top: 2px solid black;	
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		var action ="add";
		$(document).on('click','.add-level',function(){
			$('.level').append('<tr></td><td><input name="level_name[]" value="Bậc " class="form-control" type="text"></td><td><input value="1000000" name="level_salary[]" class="form-control level_input" type="text"></td><td class="del-level"><button class="btn">Xóa</button></td><td><input name="level_id[]" value="" class="form-control" type="hidden"></tr>');

			$('.level_input').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999'});
		});

		$('.level').on("click", ".del-level", function() {
  $(this).parent().remove();
		});



		$(document).on('change','.rank_value',function(){
			parent_id = $('.rank_value').val();
			$.ajax({
				method:"POST",
				url:'<?php echo base_url('employees/get_level')?>',
				data:{'id':parent_id},
				success: function(result){
					
					load_ngach(load_lai_dinh_dang,result);
				}
			});


		});


		$('.edit-rank').click(function(){
			var rank_value = $('.rank_value :selected').text();
			var rank_id = $('.rank_value').val();
			$('.rank_name').val(rank_value);
			$('.rank_id').val(rank_id);
			action ="edit";
		});


		$('.add-rank').click(function(){
			$('.rank_name').val("");
			action = "add";
		});




<?php if(isset($_SESSION['notice'])){ ?>

toastr.success('<?php echo $_SESSION['notice']; ?>', 'Thông báo');

<?php unset($_SESSION['notice']); }?>


$('.level_input').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999'});

function load_ngach(callback,result){
	$('.level').html(result);
	console.log('1');
	callback();
}

function load_lai_dinh_dang(){
	$('.level_input').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ',', vMax: '999999999999999'});
	console.log('2');
}
$('.s_rank').click(function(){

	$.ajax({
		method:"POST",
		url:'<?php echo base_url('employees/save_rank')?>',
		data:
		{
		'id':$('.rank_id').val(),
		'name':$('.rank_name').val(),
		'action':action
		},
		success: function(response){
			show_feedback(response=="success" ? 'success':'error',response=="success"  ? 'Lưu thành công' : 'Có lỗi với dữ liệu');
			setTimeout(location.reload(),1500);
			
		}
	});


});


$('.del-rank').click(function(){
	let name_rank = $("#rank_select option:selected").text();
	bootbox.confirm("Bạn có muốn xóa tất cả cấp bậc của "+name_rank+" không?", function(result){ 
		if(result)
		{
				$.ajax({
				method:"POST",
				url:'<?php echo base_url('employees/save_rank')?>',
				data:
				{
				'id':$('.rank_value').val(),
				'action':"delete"
				},
				success: function(response){
					show_feedback(response=="success" ? 'success':'error',response=="success"  ? 'Xóa thành công' : 'Có lỗi với dữ liệu, xóa thất bại');
					setTimeout(location.reload(),1500);
					
				}
			});
		}

	 });
});


// validate
$("#rank-lv").validate({
  rules: {
    // simple rule, converted to {required:true}
    "level_name[]": {
    	required:true,
    	maxlength:200,
    },
    // compound rule
    "level_salary[]": {
      required: true,
      maxlength:22,
    }
  },
  messages: {
  	"level_name[]":{
  		required:"Bắt buộc nhập tên cấp bậc",
  		maxlength:"Độ dài không được quá 200 ký tự",
  	},
  	"level_salary[]":{
  		required:"Bắt buộc nhập số tiền lương",
  		maxlength:"Độ dài không được quá 22 ký tự",
  	}
  }
});




	});
</script>


<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="z-index: 9999050; display: none;" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="x-close">×</span></button>
					<h4 class="modal-title">Thêm/ Sửa Ngạch </h4>
				</div>


				<div class="modal-body">
					<div class="form-group">
						<input class="form-control rank_name" type="text" value="">
						<input class="rank_id" type="hidden" value="">
					</div>

				</div>
				 <div class="modal-footer">
          			<button type="button" class="s_rank btn btn-default" data-dismiss="modal">Lưu</button>
        		</div>
			</div>
		</div>

	</div>


<?php $this->load->view("partial/footer"); ?>
