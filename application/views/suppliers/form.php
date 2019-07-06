<?php $this->load->view("partial/header"); ?>


<div class="row">
	<div class="spinner" id="grid-loader" style="display:none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	<?php echo form_open('suppliers/save/'.$person_info->person_id,array('id'=>'supplier_form','class'=>'form-horizontal')); ?>
	<div class="col-md-5">
		
		<?php if($person_info->person_id)  { ?>
			<div class="panel">
				<div class="panel-body ">
					<div class="user-badge">
						<?php echo $person_info->image_id ? '<div class="user-badge-avatar">'.img(array('src' => site_url('app_files/view/'.$person_info->image_id),'class'=>'img-polaroid img-polaroid-s')).'</div>' : '<div class="user-badge-avatar">'.img(array('src' => base_url('assets/assets/images/avatar-default.jpg'),'class'=>'img-polaroid','id'=>'image_empty')).'</div>'; ?>
						<div class="user-badge-details">
							<?php echo $person_info->company_name; ?>
							<p><?php echo $person_info->first_name.' '.$person_info->last_name; ?></p>
						</div>
						<ul class="list-inline pull-right">
							<!-- <?php
							$six_months_ago = date('Y-m-d', strtotime('-6 months'));
							$today = date('Y-m-d').'%2023:59:59';
							?>
							<li><a href="<?php echo site_url('reports/specific_supplier/'.$six_months_ago.'/'.$today.'/'.$person_info->person_id.'/all/0'); ?>" class="btn btn-success"><?php echo lang('common_view_report'); ?></a></li>
							<?php if ($person_info->email) { ?>
								<li><a href="mailto:<?php echo $person_info->email; ?>" class="btn btn-primary"><?php echo lang('common_send_email'); ?></a></li>
							<?php } ?>  -->
						</ul>
					</div>
				</div>
			</div>
		<?php } ?>
		

		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i>
					<?php echo lang("suppliers_basic_information"); ?>
					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
				</h3>
			</div>

			<div class="panel-body">
				<div class="form-group">

					<?php echo form_label(lang('suppliers_company_name').' :', 'company_name', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'company_name',
							'id'=>'company_name',
							'value'=>$person_info->company_name)
						);?>
					</div>
				</div>

				<?php $this->load->view("people/form_basic_info1"); ?>
			



				<?php if ($this->config->item('charge_tax_on_recv')) { ?>


					<div class="form-group override-taxes-container">
						<?php echo form_label(lang('supplier_override_default_tax_for_recv').' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'override_default_tax',
								'id'=>'override_default_tax',
								'class' => 'override_default_tax_checkbox delete-checkbox',
								'value'=>1,
								'checked'=>(boolean)$person_info->override_default_tax));
								?>
								<label for="override_default_tax"><span></span></label>
							</div>
						</div>

						<div class="tax-container main <?php if (!$person_info->override_default_tax){echo 'hidden';} ?>">
							<div class="form-group">
								<?php echo form_label(lang('common_tax_1').' :', 'tax_percent_1',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'tax_names[]',
										'id'=>'tax_percent_1',
										'size'=>'8',
										'class'=>'form-control margin10 form-inps',
										'placeholder' => lang('common_tax_name'),
										'value'=> isset($supplier_tax_info[0]['name']) ? $supplier_tax_info[0]['name'] : ($this->Location->get_info_for_key('default_tax_1_name') ? $this->Location->get_info_for_key('default_tax_1_name') : $this->config->item('default_tax_1_name')))
									);?>
								</div>
								<label class="col-sm-3 col-md-3 col-lg-2 control-label wide" for="tax_percent_name_1">&nbsp;</label>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'tax_percents[]',
										'id'=>'tax_percent_name_1',
										'size'=>'3',
										'class'=>'form-control form-inps-tax',
										'placeholder' => lang('common_tax_percent'),
										'value'=> isset($supplier_tax_info[0]['percent']) ? $supplier_tax_info[0]['percent'] : '')
									);?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
									<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
								</div>
							</div>

							<div class="form-group">
								<?php echo form_label(lang('common_tax_2').' :', 'tax_percent_2',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'tax_names[]',
										'id'=>'tax_percent_2',
										'size'=>'8',
										'class'=>'form-control form-inps margin10',
										'placeholder' => lang('common_tax_name'),
										'value'=> isset($supplier_tax_info[1]['name']) ? $supplier_tax_info[1]['name'] : ($this->Location->get_info_for_key('default_tax_2_name') ? $this->Location->get_info_for_key('default_tax_2_name') : $this->config->item('default_tax_2_name')))
									);?>
								</div>
								<label class="col-sm-3 col-md-3 col-lg-2 control-label text-info wide">&nbsp;</label>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'name'=>'tax_percents[]',
										'id'=>'tax_percent_name_2',
										'size'=>'3',
										'class'=>'form-control form-inps-tax',
										'placeholder' => lang('common_tax_percent'),
										'value'=> isset($supplier_tax_info[1]['percent']) ? $supplier_tax_info[1]['percent'] : '')
									);?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
									<?php echo form_checkbox('tax_cumulatives[]', '1', (isset($supplier_tax_info[1]['cumulative']) && $supplier_tax_info[1]['cumulative']) ? (boolean)$supplier_tax_info[1]['cumulative'] : (boolean)$this->config->item('default_tax_2_cumulative'), 'class="cumulative_checkbox" id="tax_cumulatives"'); ?>
									<label for="tax_cumulatives"><span></span></label>
									<span class="cumulative_label">
										<?php echo lang('common_cumulative'); ?>
									</span>
								</div>
							</div>

							<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3"  style="visibility: <?php echo isset($supplier_tax_info[2]['name']) ? 'hidden' : 'visible';?>">
								<a href="javascript:void(0);" class="show_more_taxes"><?php echo lang('common_show_more');?> &raquo;</a>
							</div>
							<div class="more_taxes_container" style="display: <?php echo isset($supplier_tax_info[2]['name']) ? 'block' : 'none';?>">
								<div class="form-group">
									<?php echo form_label(lang('common_tax_3').' :', 'tax_percent_3',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'tax_names[]',
											'id'=>'tax_percent_3',
											'size'=>'8',
											'class'=>'form-control form-inps margin10',
											'placeholder' => lang('common_tax_name'),
											'value'=> isset($supplier_tax_info[2]['name']) ? $supplier_tax_info[2]['name'] : ($this->Location->get_info_for_key('default_tax_3_name') ? $this->Location->get_info_for_key('default_tax_3_name') : $this->config->item('default_tax_3_name')))
										);?>
									</div>
									<label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'tax_percents[]',
											'id'=>'tax_percent_name_3',
											'size'=>'3',
											'class'=>'form-control form-inps-tax margin10',
											'placeholder' => lang('common_tax_percent'),
											'value'=> isset($supplier_tax_info[2]['percent']) ? $supplier_tax_info[2]['percent'] : '')
										);?>
										<div class="tax-percent-icon">%</div>
										<div class="clear"></div>
										<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
									</div>
								</div>

								<div class="form-group">
									<?php echo form_label(lang('common_tax_4').' :', 'tax_percent_4',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'tax_names[]',
											'id'=>'tax_percent_4',
											'size'=>'8',
											'class'=>'form-control  form-inps margin10',
											'placeholder' => lang('common_tax_name'),
											'value'=> isset($supplier_tax_info[3]['name']) ? $supplier_tax_info[3]['name'] : ($this->Location->get_info_for_key('default_tax_4_name') ? $this->Location->get_info_for_key('default_tax_4_name') : $this->config->item('default_tax_4_name')))
										);?>
									</div>
									<label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'tax_percents[]',
											'id'=>'tax_percent_name_4',
											'size'=>'3',
											'class'=>'form-control form-inps-tax',
											'placeholder' => lang('common_tax_percent'),
											'value'=> isset($supplier_tax_info[3]['percent']) ? $supplier_tax_info[3]['percent'] : '')
										);?>
										<div class="tax-percent-icon">%</div>
										<div class="clear"></div>
										<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
									</div>
								</div>

								<div class="form-group">
									<?php echo form_label(lang('common_tax_5').' :', 'tax_percent_5',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'tax_names[]',
											'id'=>'tax_percent_5',
											'size'=>'8',
											'class'=>'form-control  form-inps margin10',
											'placeholder' => lang('common_tax_name'),
											'value'=> isset($supplier_tax_info[4]['name']) ? $supplier_tax_info[4]['name'] : ($this->Location->get_info_for_key('default_tax_5_name') ? $this->Location->get_info_for_key('default_tax_5_name') : $this->config->item('default_tax_5_name')))
										);?>
									</div>
									<label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'name'=>'tax_percents[]',
											'id'=>'tax_percent_name_5',
											'size'=>'3',
											'class'=>'form-control form-inps-tax margin10',
											'placeholder' => lang('common_tax_percent'),
											'value'=> isset($supplier_tax_info[4]['percent']) ? $supplier_tax_info[4]['percent'] : '')
										);?>
										<div class="tax-percent-icon">%</div>
										<div class="clear"></div>
										<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
									</div>
								</div>
							</div> <!--End more Taxes Container-->
							<div class="clear"></div>
						</div>

					<?php } ?>



					<?php echo form_hidden('redirect', $redirect); ?>

					<div class="form-actions pull-right">

						<?php
						if ($redirect == 1)
						{
							echo form_button(array(
								'name' => 'cancel',
								'id' => 'cancel',
								'class' => 'btn btn-danger',
								'value' => 'true',
								'content' => lang('common_cancel')
							));

						}
						?>

						<?php
						echo form_submit(array(
							'name'=>'submit',
							'id'=>'submit',
							'value'=>'Lưu',
							'class'=>'btn btn-primary submit_button btn-large')
					);
					?>



						<?php
						echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>'Lưu đóng',
							'class'=>'btn btn-primary submit_button btn-large')
					);
					?>
				</div>
			</div>
		</div>

		
	</div>
	

	<!-- THEM MOI -->


<?php $this->load->view('suppliers/info-detail') ?>


	<!-- THEM MOI -->


<?php  echo form_close(); ?>

	</div>
</div>

<script type='text/javascript'>

//validation and submit handling
$(document).ready(function()
{	
$('#charter_capital').autoNumeric('init', { mDec: 0, aDec: '.', aSep: ','});

var DATE_FORMAT = 'DD-MM-YYYY';
var now = new Date();
var nd = now.getDate();
var nm = now.getMonth() + 1;
var ny = now.getFullYear();
now = nd+'-'+nm+'-'+ny;

$('#birth_date').datepicker({ format: 'dd-mm-yyyy' });
$('#registration_date').datepicker({ format: 'dd-mm-yyyy' });
$('#thong_tin_lien_he_birthday').datepicker({ format: 'dd-mm-yyyy' });



$.validator.addMethod("maxDate", function(value, element,params) {

	if(!value) 
	return true;
	var today = params.split("-");
    var dd = today[0];
    var mm = parseInt(today[1]);
    var yyyy = today[2];
   
    today = mm + '/' + dd + '/' + yyyy;   
   
    var iDate = value.split("-");
    var id = iDate[0];
    var im = parseInt(iDate[1]);
    var iy = iDate[2];
    iDate = im + '/' + id + '/' + iy;  
    var curDate = new Date(today);
    var inputDate = new Date(iDate);
    console.log('Ngay hien tai:'+curDate); 
    console.log('Ngay nhap vao:'+inputDate); 
    if (inputDate < curDate)
		return true;
	return false;
}, "Ngày nhập không thể lớn hơn ngày hiện tại");


jQuery.validator.addMethod("website", function(value, element) {
 
  return this.optional( element ) || /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test( value );
}, 'Nhập không đúng định dạng website');



// Validate name
// Ex: name:true
jQuery.validator.addMethod("ten", function(value, element) {
 
  return this.optional( element ) || /^[a-zA-Z]+(([a-zA-Z ])?[a-zA-Z]*)*$/.test( value );
}, 'Nhập không đúng định dạng tên');



// $('#birth_date').setAttribute("max", today);

var save ='suppliers';

	$('#submitf').click(function(){
		 save ='suppliers_close';
		
	});

	date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);
	$(".override_default_tax_checkbox").change(function()
	{
		$(this).parent().parent().next().toggleClass('hidden')
	});

	$("#cancel").click(cancelAddSupplier);

	setTimeout(function(){$(":input:visible:first","#supplier_form").focus();},100);
	var submitting = false;
	$('#image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

	$('#supplier_form').validate({
		submitHandler:function(form)
		{
			$('#grid-loader').show();
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
				
				success:function(response)
				{
					// var ket_qua = json_decode(response);
					console.log(response);
					
					$('#grid-loader').hide();
					submitting = false;
					show_feedback(response.success ? 'success' : 'error',response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

					if(response.success) {
						
						<?php if($test == 1) { ?>
							$.post('<?php echo site_url("receivings/select_supplier");?>', {supplier: response.person_id}, function()
							{
								window.location.href = '<?php echo site_url('receivings/index/1'); ?>';
							});
							
						<?php } else {  ?>
								
									if(save=='suppliers')
									{
										window.location.href = BASE_URL + 'suppliers/view/'+response.person_id;
									}
									else
									{
										window.location.href = BASE_URL + 'suppliers';
									}
							
																											
						<?php } ?>
					}

				},

				<?php if(!$person_info->person_id) { ?>
					resetForm: true,
				<?php } ?>
				dataType:'json'
			});

		},
		errorClass: "text-danger",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
		},
		rules:
		{
			<?php if(!$person_info->person_id) { ?>
				account_number:
				{
					remote:
					{
						url: "<?php echo site_url('suppliers/account_number_exists');?>",
						type: "post"

					},
					digits:true,

				},
			<?php } ?>
			company_name: "required",
			"email":{
				email:true
			},
			"phone_number":{
				digits:true,
				minlength: 10,
				maxlength:11,
			},
			"website":{
				maxlength:255,
				website:true,
			},
			"address_1":{
				maxlength:255,
			},
		
			"account_number":{
				digits:true,
			},
			"charter_capital":{
				maxlength:255,
			},
			"business_registration_number":{
				maxlength:255,
			},
			"tax_number":{
				digits:true,
				maxlength:20,
			},
			"comments":
			{
				maxlength:500,
			},
			"phone_more":{
				digits:true,
				minlength: 10,
				maxlength:11,
			},
			"email_more": {
				email:true,
			},
			"position_more":
			{
				maxlength:255,
			},
			"note_more": {
				maxlength:500,
			},
			"name_more":{
				maxlength:255,
			},
			"name_head[]":{
				maxlength:255,
			},
			"phone_head[]":{
				digits:true,
				minlength: 10,
				maxlength:11,
			},
			"position_head[]":{
				maxlength:255,
			},
			"note_head[]":{
				maxlength:500,
			},
			"email_head[]":{
				email:true,
			},
			"business_registration_number":{
				digits:true,
			},
			"birth_date":{
				"maxDate":now,
			},
			"registration_date":{
				"maxDate":now,
			},




		},
		messages:
		{
			<?php if(!$person_info->person_id) { ?>
				account_number:
				{
					remote: <?php echo json_encode(lang('common_account_number_exists')); ?>
				},
			<?php } ?>
			company_name: <?php echo json_encode(lang('suppliers_company_name_required')); ?>,
			last_name: <?php echo json_encode(lang('common_last_name_required')); ?>,
			email: "Nhập email đúng định dạng",
			address_1:"Bạn không được nhập quá 255 ký tự",
			account_number:"Nhập đúng định dạng số tài khoản",
			charter_capital:"Bạn không được nhập quá 255 ký tự",
			comments:"Bạn không được nhập quá 500 ký tự",
			tax_number:"Nhập đúng định dạng mã số thuế",
			phone_number:"Nhập đúng định dạng số điện thoại",
			phone_more:"Nhập đúng định dạng số điện thoại",
			email_more:"Nhập email đúng định dạng",
			position_more:"Bạn không được nhập quá 255 ký tự",
			note_more:"Bạn không được nhập quá 500 ký tự",
			name_more:"Nhập không dúng định dạng tên",
			"name_head[]":"Không đúng định dạng tên",
			"phone_head[]":"Nhập đúng định dạng số điện thoại",
			"position_head[]":"Bạn không được nhập quá 255 ký tự",
			"note_head[]":"Bạn không được nhập quá 500 ký tự",
			"email_head[]":"Nhập email đúng định dạng",
			"business_registration_number":"Số ĐKKD không đúng",
			


		},
	});
});

function cancelAddSupplier()
{
	bootbox.confirm(<?php echo json_encode(lang('suppliers_are_you_sure_cancel')); ?>,function(result)
	{
		if (result)
		{
			window.location = <?php echo json_encode(site_url('receivings')); ?>;
		}
	});

}



 	
	

   




</script>

<!-- VALIDATE -->



<?php $this->load->view('partial/footer')?>