<?php

$level =array();
$rank=array();
$rank[""]="Chọn Ngạch";
foreach ($ranks as $key => $value) {
	$rank[$value['id']]=$value['name'];

}
if($rank_id)
{

foreach ($levels as $key => $value) 
{
	if($value['parent_id']==$rank_id)
	{
		$level[$value['id']]=$value['name'];
	}
}

}

 ?>

<div class="row">
	<div class="col-md-12">
		<div class="form-group">	
						<?php echo form_label(lang('common_employee_number'), 'employee_number',array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'name'=>'employee_number',
							'id'=>'employee_number',
							'class'=>'form-control',
							'value'=>$person_info->employee_number));?>
						</div>
		</div>


		<div class="form-group">
			<?php 
			echo form_label('Họ và tên', 'first_name',array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_input(array(
					'class'=>'form-control',
					'name'=>'first_name',
					'id'=>'first_name',
					'value'=>$person_info->first_name)
				);?>
			</div>
		</div>
		<div class="form-group">	
			<?php echo form_label(lang('customers_sex'), 'sex',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_dropdown('sex', array('1'=>'Nữ','2'=>'Nam'), $person_info->sex, 'class="form-control" id="sex"');?>			
			</div>
		</div>

		<div class="form-group offset1">
			<?php echo form_label(lang('employees_birthday'), 'birthday',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				
					
					<?php echo form_input(array(
						'name'=>'birthday',
						'id'=>'birthday',
						'class'=>'form-control',
						'value'=>$person_info->birthday ? date('d-m-Y', strtotime($person_info->birthday)) : '')
					);?> 
				
			</div>
		</div>



		<div class="form-group">
			<?php echo form_label(lang('common_email'), 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_input(array(
					'class'=>'form-control',
					'name'=>'email',
					'type'=>'text',
					'id'=>'email',
					'value'=>$person_info->email)
				);?>
			</div>
		</div>
		
		
		<div class="form-group">
			<?php 
			echo form_label(lang('common_indentity_card'), 'indentity_card',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_input(array(
					'class'=>'form-control',
					'name'=>'indentity_card',
					'id'=>'indentity_card',
					'value'=>$person_info->chung_minh_thu)
				);?>
			</div>
		</div>
		<div class="form-group">
			<?php 
			echo form_label(lang('common_phone_number'), 'phone_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_input(array(
					'class'=>'form-control',
					'name'=>'phone_number',
					'id'=>'phone_number',
					'value'=>$person_info->phone_number)
				);?>
			</div>
		</div>

		<div class="form-group">	
			<?php echo form_label(lang('common_contact_address'), 'address_1',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_textarea(array(
					'name'=>'address_1',
					'id'=>'address_1',
					'class'=>'form-control text-area',
					'value'=>$person_info->address_1,
					'rows'=>'2',
					'cols'=>'10')		
				);?>
			</div>
		</div>

		

		<div class="form-group offset1">
			<?php echo form_label('Thời gian vào VCBS', 'hire_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">	
					<?php echo form_input(array(
						'name'=>'hire_date',
						'id'=>'hire_date',
						'class'=>'form-control',
						'value'=>$person_info->hire_date ? date('d-m-Y', strtotime($person_info->hire_date)) : '')
					);?> 
				
			</div>
		</div>

		<div class="form-group">
			<?php 
			echo form_label(lang('common_rank'), 'rank',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_dropdown('rank', $rank, $rank_id, 'class="form-control" id="rank"');?>	
			</div>
		</div>
		<div class="form-group">
			<?php 
			echo form_label(lang('common_level'), 'level',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
			<div class="col-sm-9 col-md-9 col-lg-10">
				<?php echo form_dropdown('level', $level, $person_info->level_id, 'class="form-control" id="level"');?>	
			</div>
		</div>


	<div class="form-group">	
<?php echo form_label(lang('common_comments'), 'comments',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
	<?php echo form_textarea(array(
		'name'=>'comments',
		'id'=>'comments',
		'class'=>'form-control text-area',
		'value'=>$person_info->comments,
		'rows'=>'4',
		'cols'=>'10')		
	);?>
	</div>
</div>

	</div><!-- /col-md-12 -->
</div><!-- /row -->


	<script>	
	$('#rank').change(function(){
			rank = $('#rank').val();
			$.ajax({
				method:"POST",
				url:'<?php echo base_url('employees/get_level')?>',
				data:{'rank_id':rank},
				success: function(result){
					$('#level').html(result);
				}
			});
		});



	</script>