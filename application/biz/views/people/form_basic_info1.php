<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<?php echo form_label(lang('common_email'), 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label '.($controller_name == 'employees' || $controller_name == 'login' ? 'required' : 'not_required'))); ?>
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
		<?php  
		if ($controller_name != 'login') {
			?>
			<div class="form-group">
				<?php echo form_label(lang('common_website'), 'website',array('class'=>' col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_input(array(
						'class'=>'form-control',
						'name'=>'website',
						'id'=>'website',
						'value'=>$person_info->website)
					);?>
				</div>
			</div>
		<?php } ?>
		<?php if ($controller_name == "customers") { ?>
			<div class="form-group">	
				<?php echo form_label(lang('customers_type'), 'customer_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_dropdown('customer_type', $type_customers, $person_info->type_customer, 'class="form-control" id="customer_type"');?>			

				</div>
			</div>

			<div class="form-group">	
				<?php echo form_label(lang('customers_position'), 'position',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_input(array(
						'class'=>'form-control',
						'name'=>'position',
						'id'=>'position',
						'value'=>$person_info->position));?>
					</div>
				</div>
			<?php } ?>

			<?php if ($controller_name == "customers") { ?>
				<div class="form-group">	
					<?php echo form_label(lang('customers_sex'), 'sex',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('sex', $sex, $person_info->sex, 'class="form-control" id="sex"');?>			

					</div>
				</div>

				<div class="form-group">	
					<?php echo form_label(lang('customers_family_info'), 'family_info',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_textarea(array(
							'name'=>'family_info',
							'id'=>'family_info',
							'class'=>'form-control text-area',
							'value'=>$person_info->family_info,
							'rows'=>'3',
							'cols'=>'17')		
						);?>
					</div>
				</div>
			<?php } ?>

			<div class="form-group">	
				<?php echo form_label(lang('common_phone_number'), 'phone_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_input(array(
						'class'=>'form-control',
						'name'=>'phone_number',
						'id'=>'phone_number',
						'value'=>$person_info->phone_number));?>
					</div>
				</div>






				<div class="form-group">	
					<?php echo form_label(lang('common_address_1'), 'address_1',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control',
							'name'=>'address_1',
							'id'=>'address_1',
							'value'=>$person_info->address_1));?>
						</div>
					</div>
					
					<!-- <div class="form-group">	
						<?php echo form_label('Tên ngân hàng', 'name_bank',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control',
								'name'=>'name_bank',
								'id'=>'name_bank',
							));?>
						</div>
					</div> -->

					<?php 
					if ($controller_name != 'login') {
						?>
						<div class="form-group">	
							<?php echo form_label('Số tài khoản ngân hàng', 'account_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control',
									'name'=>'account_number',
									'id'=>'account_number',
									'value'=>$person_info->account_number));?>
								</div>
							</div>
						<?php } ?>
						<?php 
						if ($controller_name != 'login') {
							?>
							<div class="form-group">	
								<?php echo form_label('Mã số thuế', 'tax_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control',
										'name'=>'tax_number',
										'id'=>'tax_number',
										'value'=>$person_info->tax_number));?>
									</div>
								</div>
							<?php } ?>
							<?php 
							if ($controller_name != 'login') {
								?>
								<div class="form-group">	<?php $value_birth_date = (!empty($person_info->birth_date))? date('d-m-Y',strtotime($person_info->birth_date)) : "";  ?>
								<?php echo form_label(lang('suppliers_founded_date'), 'birth_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control',
										'name'=>'birth_date',
										'id'=>'birth_date',
										'value'=>$value_birth_date));?>
									</div>
								</div>
							<?php } ?>
							<?php 
							if ($controller_name != 'login') {
								?>
								<div class="form-group">	
									<?php echo form_label('Vốn điều lệ', 'charter_capital',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'class'=>'form-control ',
											'name'=>'charter_capital',
											'id'=>'charter_capital',
											'value'=>$person_info->charter_capital));?>
										</div>
									</div>
								<?php } ?>
								<?php 
								if ($controller_name != 'login') {
									?>
									<div class="form-group">	
										<?php echo form_label('Số ĐKKD', 'zip',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'form-control ',
												'name'=>'business_registration_number',
												'id'=>'business_registration_number',
												'value'=>$person_info->business_registration_number));?>
											</div>
										</div>

										<div class="form-group"><?php $value_registration_date = (!empty($person_info->registration_date))? date('d-m-Y',strtotime($person_info->registration_date)) : "";  ?>
										<?php echo form_label('Ngày cấp', 'registration_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'form-control',
												'name'=>'registration_date',
												'id'=>'registration_date',
												'value'=>$value_registration_date));?>
											</div>
										</div>
									<!-- <div class="form-group">
										<?php echo form_label('Nơi cấp ĐKKD', 'add_dkkd',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
											<?php echo form_input(array(
												'class'=>'form-control',
												'name'=>'add_dkkd',
												'id'=>'add_dkkd',
											));?>
										</div>
									</div> -->
									


<!-- 			<div class="form-group">	
<?php echo form_label('Dự án liên quan', 'task-relationship',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
		<?php echo form_dropdown('task-relationship', array(''=>'Dự án liên quan','1'=>'Dự án 1','2'=>'Dự án 2'),'', 'class="form-control" id="task-relationship"');?>
	</div>
</div> -->


<div class="form-group">	
	<?php echo form_label(lang('common_comments'), 'comments',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
	<div class="col-sm-9 col-md-9 col-lg-10">
		<?php echo form_textarea(array(
			'name'=>'comments',
			'id'=>'comments',
			'class'=>'form-control text-area',
			'value'=>$person_info->comments,
			'rows'=>'5',
			'cols'=>'17')		
		);?>
	</div>
</div>
<?php } ?>
</div><!-- /col-md-12 -->
</div><!-- /row -->