<div class="col-md-7">
	<!-- thêm mới ảnh đại diện -->
	<!-- anh dai dien -->
	<div class="tab-content">
		<div class="panel-heading header-tab">
			<h3 class="panel-title">Chọn logo</h3>
		</div>	
		<div class="form-group">	
			<div class="col-sm-12 col-md-12 col-lg-12">
				<ul class="list-unstyled avatar-list">
					
					<li style="display: inline-block;">
						<?php echo $person_info->image_id ? '<div id="avatar">'.img(array('src' => site_url('app_files/view/'.$person_info->image_id),'class'=>'img-polaroid img-polaroid-s', 'style' => "height: 150px;")).'</div>' : '<div id="avatar">'.img(array('src' => base_url().'assets/img/avatar.png','class'=>'img-polaroid','id'=>'image_empty', 'style' => "height: 150px;")).'</div>'; ?>		
					</li>	

					<li style="display: inline-block; padding-left: 30px">
						<input  type="file" name="image_id" accept="image/*" id="image_id" class="filestyle" data-input="false" tabindex="-1" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);">
					</li>	
				</ul>
			</div>
		</div>
		<?php if($person_info->image_id) {  ?>

			<div class="form-group">
				<?php echo form_label(lang('common_del_image'), 'del_image',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_checkbox(array(
						'name'=>'del_image',
						'id'=>'del_image',
						'class'=>'delete-checkbox', 
						'value'=>1
					));
					echo '<label for="del_image"><span></span></label> ';

					?>
				</div>
			</div>

		<?php }  ?>

		
	</div>
	<!-- anh dai dien -->
	<!-- chi tiet -->

	<div class="tab-content">
		<div class="panel-heading header-tab">
			<h3 class="panel-title">
				<i class="ion-edit"></i> 
				Chi tiết thông tin					<small>(Các trường màu đỏ là cần nhập)</small>
				<p></p>
			</h3>
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#home">Phân loại bên thứ ba</a></li>
				<li class=""><a data-toggle="tab" href="#menu1">Người đại diện</a></li>.
				<!--					<li><a data-toggle='tab' href="#menu2">Tùy chọn</a></li>-->
			</ul>
		</div>
		<!-- menu home -->
		<div id="home" class="tab-pane panel-body fade active in">


			<!-- loại đơn vị -->
			<div class="form-group clearfix">	
				<?php echo form_label('Loại đơn vị', 'unit_type',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
				<div class="col-sm-9 col-md-9 col-lg-9">
					<?php echo form_dropdown('unit_type', $unit_type, $person_info->unit_type_id, 'class="form-control" id="unit_type"');?>			
				</div>
			</div>

			<!-- khu vực địa lý -->
			<div class="form-group clearfix">
				<?php echo form_label('Đầu mối VCBS quản lý'.'', 'geographical_area',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
				<div class="col-sm-9 col-md-9 col-lg-9">
					<p for="geographical_area" class="text-danger errors"></p>
					<select class="form-control" name="geographical_area[]" id="geographical_area">
						<?php  
						foreach($geographical_area as $id => $value)
							{ 	$selected = ($value['has_access'] == true) ? 'selected' : '';
						?>
						<option value="<?php echo $id ?>" <?php echo $selected ?>>

							<?php echo $value['name'] ?>

						</option>
					<?php }	?>
				</select>		
			</div>
		</div>

		<!-- Ngành nghe kinh doanh -->

		<div class="form-group clearfix">
			<?php echo form_label('Ngành nghề kinh doanh'.'', 'business_type',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-9">
				<p for="business_type" class="text-danger errors"></p>
				<select class="form-control" name="business_type[]" id="business_type">
					<?php  
					foreach($business_type as $id => $value)
						{ 	$selected = ($value['has_access'] == true) ? 'selected' : '';
					?>
					<option value="<?php echo $id ?>" <?php echo $selected ?>>

						<?php echo $value['name'] ?>

					</option>
				<?php }	?>
			</select>		
		</div>
	</div>
	<!-- Hình thức công ty -->
	
	<div class="form-group clearfix">	
		<?php echo form_label('Hình thức công ty', 'company_form',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
		<div class="col-sm-9 col-md-9 col-lg-9">
			<?php echo form_dropdown('company_form', $company_form, $person_info->company_form_id, 'class="form-control" id="customer_type"');?>			
		</div>
	</div>


	<!-- Chọn bộ thuộc tính -->
	<div id="attribute_sets">
	</div>
	<a class="btn btn-info" href="<?php echo base_url('suppliers')?>"><i class="icon ti-direction-alt"></i> Quay lại danh mục</a>
</div>
<!-- menu 2 -->
<div id="menu1" class="tab-pane panel-body fade">
	<div class="form-group clearfix">
		<label for="thong_tin_lien_he_name" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Tên</label>				<div class="col-sm-9 col-md-9 col-lg-10">
			<input type="text" name="name_more" value="<?php echo $thong_tin_lien_he['name_more']?>" class="form-control" id="thong_tin_lien_he_name" placeholder="Tên">
		</div>
	</div>
	<div class="form-group clearfix">	
		<label for="" class="col-sm-3 col-md-3 col-lg-2 control-label ">Số điện thoại</label>				<div class="col-sm-9 col-md-9 col-lg-10">
			<input type="text" name="phone_more" value="<?php echo $thong_tin_lien_he['phone_more']?>" class="form-control" id="thong_tin_lien_he_sdt" placeholder="Điện thoại">
		</div>
	</div>

	<div class="form-group clearfix">
		<label for="thong_tin_lien_he_sex" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Giới tính</label>				<div class="col-sm-9 col-md-9 col-lg-10">

			<select name="sex_more" class="form-control" id="thong_tin_lien_he_sex">
				<option value="1" <?php if($value['sex_more']==1){echo "selected";}  ?>>Nam</option>
				<option value="2" <?php if($value['sex_more']==2){echo "selected";}  ?>>Nữ</option>
			</select>
		</div>
	</div>
	<div class="form-group clearfix">
		<label for="thong_tin_lien_he_birthday" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Ngày sinh</label>                <div class="col-sm-9 col-md-9 col-lg-10">
			<input type="text" name="birth_date_more" value="<?php echo $thong_tin_lien_he['birth_date_more']?>" class="form-control" id="thong_tin_lien_he_birthday">
		</div>
	</div>
	<div class="form-group clearfix">
		<label for="thong_tin_lien_he_email" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Email</label>				<div class="col-sm-9 col-md-9 col-lg-10">
			<input type="text" name="email_more" value="<?php echo $thong_tin_lien_he['email_more']?>" class="form-control" id="thong_tin_lien_he_email" placeholder="Email">
		</div>
	</div>
	<div class="form-group clearfix">
		<label for="thong_tin_lien_he_phongban" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Chức vụ</label>				<div class="col-sm-9 col-md-9 col-lg-10">
			<input type="text" name="position_more" value="<?php echo $thong_tin_lien_he['position_more']?>" class="form-control" id="thong_tin_lien_he_phongban" placeholder="Chức vụ">
		</div>
	</div>
	<div class="form-group clearfix">	
		<label for="thong_tin_lien_he_note" class="col-sm-3 col-md-3 col-lg-2 control-label ">Ghi chú</label>				<div class="col-sm-9 col-md-9 col-lg-10">
			<textarea name="note_more" cols="17" rows="3" id="thong_tin_lien_he_note" class="form-control text-area"><?php echo $thong_tin_lien_he['note_more']?> </textarea>
		</div>
	</div>
	<!-- thêm thông tin người đầu  -->

	<div class="panel-heading header-tab">
		<h3 class="panel-title">
			<i class="ion-edit"></i>
			Thông tin người đầu mối		 			<p></p>
		</h3>

	</div>



	<table class="tablesorter table table-hover" id="sortable_table">
		<thead>
			<tr>

				<th>STT</th>
				<th>Họ tên</th>
				<th>SĐT</th>
				<th>Email</th>
				<th>Phòng ban</th>
				<th>Ghi chú</th>
				<th>Xóa</th>
			</tr>
		</thead>
		<tbody id="them_dong_moi">
			<?php 
			$i=1; 
			foreach ($thong_tin_dau_moi as $value) { ?>

				<!-- end if -->
				<tr>
					<td style="width: 6%;"><input type="text" name="stt" value="<?php echo $i ?>" class="form-control input-sm"></td>
					<td><input data-toggle="popover" data-trigger="hover" data-content="" data-placement="top" type="text" name="name_head[]" value="<?php echo $value['name_head']?>" class="form-control input-sm mytext" id="thong_tin_lien_he_name" placeholder="Tên" data-original-title="" title=""></td>

					<td>
						<input data-toggle="popover" data-trigger="hover" data-content="" data-placement="top" type="text" name="phone_head[]" value="<?php echo $value['phone_head']?>" class="form-control input-sm mytext" id="thong_tin_lien_he_sdt" placeholder="SĐT" data-original-title="" title="">

					</td>

					<td><input data-toggle="popover" data-trigger="hover" data-content="" data-placement="top" type="text" name="email_head[]" value="<?php echo $value['email_head']?>" class="form-control input-sm mytext" id="thong_tin_lien_he_email" placeholder="Email" data-original-title="" title=""></td>

					<td><input data-toggle="popover" data-trigger="hover" data-content="" data-placement="top" type="text" name="position_head[]" value="<?php echo $value['position_head']?>" class="form-control input-sm mytext" id="thong_tin_lien_he_phongban" placeholder="Chức vụ" data-original-title="" title=""></td>
					<td><input data-toggle="popover" data-trigger="hover" data-content="" data-placement="top" type="text" name="note_head[]" value="<?php echo $value['note_head']?>" class="form-control input-sm mytext" id="thong_tin_lien_he_note" placeholder="Ghi chú" data-original-title="" title=""></td>

					<td><button type="button" class="btn btn-danger xoa_dong"><i class=""></i>Xóa</button></td>
					<?php $i++; }?>
				</tr>
				<!-- end foreach -->
				<!-- end else -->

				<tr><td style="width: 6%;"><input type="text" name="stt" value="1" class="form-control input-sm"></td><td><input type="text" name="name_head[]" value="" class="form-control" id="thong_tin_lien_he_name0" placeholder="Tên"></td><td><input type="text" name="phone_head[]" value="" class="form-control" id="thong_tin_lien_he_sdt0" placeholder="SĐT"></td><td><input type="text" name="email_head[]" value="" class="form-control" id="thong_tin_lien_he_email0" placeholder="Email"></td><td><input type="text" name="position_head[]" value="" class="form-control" id="thong_tin_lien_he_phongban0" placeholder="Chức vụ"></td><td><input type="text" name="note_head[]" value="" class="form-control" id="thong_tin_lien_he_note0" placeholder="Ghi chú"></td><td><button type="button" class="btn btn-danger xoa_dong"><i class=""></i>Xóa</button></td></tr>


			</tbody>
			<!-- <button style="float: right;" class="btn btn-primary submit_button btn-large" >  Thêm mới </button> -->

		</table>
					<button type="button" class="btn btn-primary btn-block" onclick="them_dong_moi()">Thêm dòng</button>
	</div> <!-- end tab người đại diện -->
	<!-- menu 3 -->

</div>

<!-- chi tiet -->
</div>


<script type='text/javascript'>

	// $('#geographical_area').selectize({
	// 	plugins: ['remove_button'],
	// 	delimiter: ',',
	// 	persist: false,
		// create: function(input) {
		// 	return {
		// 		value:input,
		// 		text: input
		// 	}
	 	// },
	// });
	// $('#business_type').selectize({
	// 	plugins: ['remove_button'],
	// 	delimiter: ',',
	// 	persist: false,
		// create: function(input) {
		// 	return {
		// 		value:input,
		// 		text: input
		// 	}
		// }
	// });
	$('#exchange_form').selectize({
		plugins: ['remove_button'],
		delimiter: ',',
		persist: false,
		// create: function(input) {
		// 	return {
		// 		value:input,
		// 		text: input
		// 	}
		// }
	});

	// thêm dòng mới mỗi lần bấm click
	function them_dong_moi(){
		// tăng dần giá trị cho input
		$('#them_dong_moi tr input[name="stt"]').each(function(ind) {
			$(this).val(ind +1);
		});
		// lấy giá trị mới nhất để hiển thị
		var dem = $('#them_dong_moi tr input[name="stt"]:last').val();
		if (dem==null) {
			dem=0;
		}
		$('#them_dong_moi').append('<tr><td style="width: 6%;"><input type="text" name="stt" value="" class="form-control"></td><td><input type="text" type="text" name="name_head[]" value="" class="form-control" id="thong_tin_lien_he_name0" placeholder="Tên"></td><td><input type="text" name="phone_head[]" value="" class="form-control" id="thong_tin_lien_he_sdt0" placeholder="SĐT"></td><td><input type="text" name="email_head[]" value="" class="form-control" id="thong_tin_lien_he_email0" placeholder="Email"></td><td><input type="text" name="position_head[]" value="" class="form-control" id="thong_tin_lien_he_phongban0" placeholder="Chức vụ"></td><td><input type="text" name="note_head[]" value="" class="form-control" id="thong_tin_lien_he_note0" placeholder="Ghi chú"></td><td><button type="button" class="btn btn-danger xoa_dong"><i class=""></i>Xóa</button></td></tr>')
		$('#them_dong_moi tr:last-child td:first-child').html('<input type="text" name="stt" value="'+(parseInt(dem)+1)+'" class="form-control input-sm">');
	}
	// xóa dòng đang hiển thị
	$('table').on('click','.xoa_dong',function(){
		$(this).parent().parent().remove();
		$('#them_dong_moi tr input[name="stt"]').each(function(ind) {
			$(this).val(ind +1);
		});

	})

	date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
	$(".override_default_tax_checkbox").change(function(){
		$(this).parent().parent().next().toggleClass('hidden')
	});
	
	check_taxable();
	$("#taxable").change(check_taxable);
	
	function check_taxable(){
		if ($("#taxable").prop('checked'))
		{
			$("#tax_certificate_holder").hide();
		}
		else
		{
			$("#tax_certificate_holder").show();
		}
	}

	$('#image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

	//validation and submit handling

	$(document).ready(function()
	{
        // authorized_capital
        $('#authorized_capital').autoNumeric('init', { mDec: 2, aDec: '.', aSep: ','});
        $("#cancel").click(cancelCustomerAddingFromSale);

        setTimeout(function(){$(":input:visible:first","#customer_form").focus();},100);
        $('#customer_form').validate({
        	submitHandler:function(form)
        	{
        		$.post(BASE_URL+'customers/check_duplicate', {
        			name: $('#last_name').val(), 
        			id : 192			},
        			function(data) {
        				var action_button_value = data.action_button;
					// nếu kết quả trả về là true, => last_name đã tồn tại
					if(data.duplicate) {
						bootbox.confirm('Một khách hàng có tên '+$('#last_name').val()+' đã tồn tại bạn có muốn tiếp tục lưu không', function(result)
						{
							if (result)
							{
								doCustomerSubmit(form,action_button_value);
							} 
							element = $( '[name="last_name"]' );  
							group = element.closest('.form-group');
							group.addClass('has-error');
							$('span[for="last_name"]').removeAttr('class')
							$('span[for="last_name"]').attr('class','alert-warning')
							group.find('span[for="last_name"]').text('Khách hàng này đã tồn tại');
							$("html, body").animate({ scrollTop: 0 }, "slow");
						})
					} else {
						show_feedback('success','Wait a Second...');
						doCustomerSubmit(form,action_button_value);
					}
					
				} , "json")
        		.error(function() { 
        		});

        	}
        });


    });




	function doCustomerSubmit(form,action_button_value = false)
	{
		$('.has-error').removeClass('has-error');
		$('span.errors').text('');
		var checkOptions = {
			data: {
				action_button_value : action_button_value,
				id : 192	},
				url : 'customers/kiem_tra_truoc_khi_luu_khach_hang/',
				dataType: "json",
				success: thong_bao_loi
			};
    // kiểm tra vailidate trước khi save
    $("#customer_form").ajaxSubmit(checkOptions);

    function thong_bao_loi(data) {
    	if(data.flag == false) {
    		$.each(data.errors, function( index, value ) {
    			element = $( '[name="'+index+'"]' );  
    			group = element.closest('.form-group');
    			group.addClass('has-error');
    			group.find('span[for="'+index+'"]').text(value);
                // console.log(value);
                $("#grid-loader").hide();
                $("html, body").animate({ scrollTop: 0 }, "slow");
            });
    		show_feedback(data.success ? 'success' : 'error',data.message,data.success ? "Tha\u0300nh c\u00f4ng" : "L\u1ed7i ! Vui l\u00f2ng ki\u1ec3m tra l\u1ea1i");
    	} else {
        	// kiểm tra nếu đồng ý save sẽ chạy $(form).ajaxSubmit
        	$("#grid-loader").show();
        	$(form).ajaxSubmit({
        		success:function(response)
        		{
        			$("#grid-loader").hide();
        			show_feedback(response.success ? 'success' : 'error',response.message,response.success ? "Tha\u0300nh c\u00f4ng" : "L\u1ed7i ! Vui l\u00f2ng ki\u1ec3m tra l\u1ea1i");

        			if(response.redirect_code==1 && response.success)
        			{ 
        				$.post(BASE_URL+'sales/select_customer', {customer: response.person_id}, function()
        				{
        					window.location.href = BASE_URL+'sales/index/1';
        				});
        			}
        			else if(response.redirect_code==2 && response.success)
        			{
        				window.location.href = BASE_URL+'customers';
        			}
        			else
        			{
        				if(data.action_button_value) {
							// $("html, body").animate({ scrollTop: 0 }, "slow");
							location.reload();
						} else {
							window.location.href = BASE_URL+'customers';
						}
					}
				},
				dataType:'json'
			});
        }	
    }

}

function cancelCustomerAddingFromSale()
{
	bootbox.confirm("customers_are_you_sure_cadncel", function(response)
	{
		if (response)
		{
			window.location = BASE_URL+"sales";
		}
	});
}
function action_button($what_action = null){
	$.ajax({
		url: BASE_URL+'customers/action_button',
		type: 'GET',
		data: {
			action_button: $what_action
		},
		success : function(){

		}
	})	
}
$('.mytext').popover();
</script>
