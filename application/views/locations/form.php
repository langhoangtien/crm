<?php $this->load->view("partial/header"); ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/location.js" ></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-n9-autocomplete.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-n9-autocomplete.js" ></script>
<?php if ( 0 && isset($needs_auth) && $needs_auth) {?>
	<?php echo form_open('locations/check_auth',array('id'=>'location_form_auth','class'=>'form-horizontal')); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					<h3 style="margin-left: 80px;"><a href="http://4biz.vn/buy_additional.php" target="_blank"><?php echo lang('locations_purchase_additional_licenses'); ?> &raquo;</a></h3>
					<?php if (validation_errors()) {?>
				        <div class="alert alert-danger">
				            <strong><?php echo lang('common_error'); ?></strong>
				            <?php echo validation_errors(); ?>
				        </div>
			        <?php } ?>
					<div class="form-group">
						<?php echo form_label(lang('locations_purchase_email').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'purchase_email',
								'id'=>'purchase_email')
							);?>
						</div>	
					</div>
					<div class="form-actions pull-right">
						<?php
						echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>lang('common_submit'),
							'class'=>'submit_button btn btn-primary')
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php form_close(); ?>
<?php } else {?>

	<?php echo form_open_multipart('locations/save/'.$location_info->location_id,array('id'=>'location_form_n9','class'=>'form-horizontal','autocomplete'=> 'off')); ?>
		<input type="hidden" name="location_id" value="<?php echo $location_id ; ?>" />
        <div class="row" id="form">
			<div class="spinner" id="grid-loader" style="display:none">
			  <div class="rect1"></div>
			  <div class="rect2"></div>
			  <div class="rect3"></div>
			</div>
			<div class="col-md-12">				
				<div class="panel panel-piluku">
					<div class="panel-heading">
		                <h3 class="panel-title">
		                    <i class="ion-edit"></i> 
		                    <?php echo lang("locations_basic_information"); ?>
	    					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
		                </h3>
			        </div>

					<div class="panel-body">

						<div class="form-group">
							<?php echo form_label(lang('locations_name').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="name" class="text-danger errors"></p>
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'name',
									'id'=>'name',
									'value'=>$location_info->name)
								);?>
							</div>
						</div>

						<div class="form-group locations_type">
							<?php echo form_label(lang('location_parent').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<select class="form-control" name="location_parent">
									<?php  
										foreach($locations as $location)
										{
										    if ($location['id'] == $location_info->location_id) continue;
										    
											$selected = ($location_info->parent_id == $location['id']) ? 'selected' : '';
											echo '<option value="'.$location['id'].'" '.$selected.'> '.$location['name'].'</option>';
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="form-group locations_type">
							<?php echo form_label(lang('locations_type').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php foreach ($types as $type) {
								$checked = ($type['code'] == $location_info->type) ? 'checked="checked"' : ''; 
								?>
                                <div>
                                	<input type="radio" name="location_type" <?php echo $checked;?> value="<?php echo $type['code']; ?>" id="locations_type_<?php echo $type['code']; ?>">
									<label for="locations_type_<?php echo $type['code']; ?>"><span></span></label>
									<label for="locations_type_<?php echo $type['code']; ?>" style="font-weight: inherit"> <?php echo $type['label']; ?></label>
                                </div>
                                <?php } ?>
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_color').' :', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'color',
									'id'=>'color',
									'value'=>$location_info->color)
								);?>
							</div>
						</div>


						<div class="form-group">
							<?php echo form_label(lang('locations_address').' :', 'address',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="address" class="text-danger errors"></p>
								<?php echo form_textarea(array(
									'name'=>'address',
									'id'=>'address',
									'class'=>'form-control text-area',
									'rows'=>'4',
									'cols'=>'30',
									'value'=>$location_info->address));?>								
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_phone').' :', 'phone',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="phone" class="text-danger errors"></p>
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'phone',
									'id'=>'phone',
									'value'=>$location_info->phone)
								);?>
							</div>
						</div>
					
						<div class="form-group">
							<?php echo form_label(lang('locations_fax').' :', 'fax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'fax',
									'id'=>'fax',
									'value'=>$location_info->fax)
								);?>
							</div>
						</div>
						
						<div class="form-group">
							<?php echo form_label(lang('locations_sale_office').' :', 'sale_office',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'sale_office',
									'id'=>'sale_office',
									'value'=>$location_info->sale_office)
								);?>
							</div>
						</div>
						
						<div class="form-group">
							<?php echo form_label(lang('locations_account_bank').' :', 'account_bank',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'account_bank',
									'id'=>'account_bank',
									'value'=>$location_info->account_bank)
								);?>
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_email').' :', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'type'=>'text',
									'class'=>'form-control form-inps',
									'name'=>'email',
									'id'=>'email',
									'value'=>$location_info->email)
								);?>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('common_return_policy').' :', 'return_policy',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'return_policy',
								'id'=>'return_policy',
								'class'=>'form-control text-area',
								'rows'=>'4',
								'cols'=>'30',
								'value'=>$location_info->return_policy));?>
							</div>
						</div>
						
						
						<div class="form-group">
							<?php echo form_label(lang('reports_employees').' :', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
                                <p for="employees" class="text-danger errors"></p>
								<!-- <input type="text" id="emp" class="emp form-control" name="emp[]" /> -->
								<select class="form-control" name="employees[]" id="employees" multiple>
									<?php  
										foreach($employees as $person_id => $employee)
										{
											$selected = ($employee['has_access'] == true) ? 'selected' : '';
											echo '<option value="'.$person_id.'" '.$selected.'> '.$employee['name'].'</option>';
										}
									?>
								</select>		
							</div>
						</div>
<?php if($location_id == -1): ?>
    <div id="group_1_section" class="big-section">
        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm :</label>
            <div class="col-sm-9 col-md-9 col-lg-10" style="padding-top: 7px;">
                <span class="bold">Bán hàng</span>
                <input type="hidden" name="location_group_id[]" value="1">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Danh sách :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <input type="text" id="group_1" name="group_1" class="form-control form-inps" placeholder="" autocomplete="off" data-url="<?php echo base_url(); ?>ajax/emp_list">
                <div class="n9-autocomplete-result-list" id="group_1_select_list">
                    List:
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Mặc định :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <input type="text" id="group_1_default" name="group_1_default" class="form-control form-inps" placeholder="" autocomplete="off" data-url="<?php echo base_url(); ?>ajax/emp_list">
                <div class="n9-autocomplete-result-list" id="group_1_default_select_list">
                    List:
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tỷ lệ :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <input type="text" name="commission_percent[]" value="0" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tính theo :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <select name="commission_percent_type[]" class="form-control">
                    <option value="profit">Lợi nhuận</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="display: none;">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Trang thái :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <select name="status[]" class="form-control">
                    <option value="active" selected="">Hiển thị</option>
                    <option value="unactive">Ẩn</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="display: none;">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label" style="visibility: hidden;">Nhóm :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <a href="javascript:;" onclick="remove_group_section(this);">[Xóa nhóm]</a>
            </div>
        </div>
        <script type="text/javascript">
            $( document ).ready(function() {
                n9_autocomplete('group_1')
                n9_autocomplete('group_1_default');
            });

        </script>
    </div>

    <div id="group_2_section" class="big-section">
        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm :</label>
            <div class="col-sm-9 col-md-9 col-lg-10" style="padding-top: 7px;">
                <span class="bold">Giao hàng</span>
                <input type="hidden" name="location_group_id[]" value="2">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Danh sách :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <input type="text" id="group_2" name="group_2" class="form-control form-inps" placeholder="" autocomplete="off" data-url="<?php echo base_url(); ?>ajax/emp_list">
                <div class="n9-autocomplete-result-list" id="group_2_select_list">
                    List:
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Mặc định :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <input type="text" id="group_2_default" name="group_2_default" class="form-control form-inps" placeholder="" autocomplete="off" data-url="<?php echo base_url(); ?>ajax/emp_list">
                <div class="n9-autocomplete-result-list" id="group_2_default_select_list">
                    List:
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tỷ lệ :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <input type="text" name="commission_percent[]" value="0" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tính theo :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <select name="commission_percent_type[]" class="form-control">
                    <option value="profit">Lợi nhuận</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="display: none;">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Trang thái :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <select name="status[]" class="form-control">
                    <option value="active" selected="">Hiển thị</option>
                    <option value="unactive">Ẩn</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="display: none;">
            <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label" style="visibility: hidden;">Nhóm :</label>
            <div class="col-sm-9 col-md-9 col-lg-10">
                <a href="javascript:;" onclick="remove_group_section(this);">[Xóa nhóm]</a>
            </div>
        </div>
        <script type="text/javascript">
            $( document ).ready(function() {
                n9_autocomplete('group_2')
                n9_autocomplete('group_2_default');
            });

        </script>
    </div>
<?php endif; ?>
<?php if(isset($location_group_list)): ?>
    <?php
        foreach($location_group_list as $location_group) {
            $group_id = $location_group['group_id'];
            $employee_item_list = array();
            foreach($location_group['employee_list'] as $employee_item) {
                $link_detail = base_url() . 'employees/view/'.$employee_item['id'];
                $employee_item_list[]= '<span class="key">
                                            <a href="javascript:;" class="delete-payment"><i class="icon ion-android-cancel"></i></a>
                                            <a href="'.$link_detail.'" target="_blank">'.$employee_item['name'].' </a><input type="hidden" name="n9-autocomplete-item[group_'.$location_group['group_id'].'][]" value="'.$employee_item['id'].'">
                                        </span>';
            }
            $employee_item_list = implode('', $employee_item_list);

            $default_list = array();
            if(!empty($location_group['default_list'])) {
                foreach($location_group['default_list'] as $employee_item) {
                    $link_detail = base_url() . 'employees/view/'.$employee_item['id'];
                    $default_list[]= '<span class="key">
                                                <a href="javascript:;" class="delete-payment"><i class="icon ion-android-cancel"></i></a>
                                                <a href="'.$link_detail.'" target="_blank">'.$employee_item['name'].' </a><input type="hidden" name="n9-autocomplete-item[group_'.$location_group['group_id'].'_default][]" value="'.$employee_item['id'].'">
                                          </span>';
                }
            }

            $default_list = implode('', $default_list);

            if($group_id == 1 || $group_id == 2)
                $css_style = ' style="display: none;"';
            else
                $css_style = '';
    ?>
            <div id="group_<?php echo $location_group['group_id']; ?>_section" class="big-section">
                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm :</label>
                    <div class="col-sm-9 col-md-9 col-lg-10" style="padding-top: 7px;">
                        <span class="bold"><?php echo $location_group['group_name']; ?></span>
                        <input type="hidden" name="location_group_id[]" value="<?php echo $location_group['group_id']; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Danh sách :</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <input type="text" id="group_<?php echo $location_group['group_id']; ?>" name="group_<?php echo $location_group['group_id']; ?>" class="form-control form-inps" placeholder="" autocomplete="off"  data-url="<?php echo base_url().'ajax/emp_list'; ?>"/>
                        <div class="n9-autocomplete-result-list" id="group_<?php echo $location_group['group_id']; ?>_select_list">
                            List:<?php echo $employee_item_list; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Mặc định :</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <input type="text" id="group_<?php echo $location_group['group_id']; ?>_default" name="group_<?php echo $location_group['group_id']; ?>_default" class="form-control form-inps" placeholder="" autocomplete="off"  data-url="<?php echo base_url().'ajax/emp_list'; ?>"/>
                        <div class="n9-autocomplete-result-list" id="group_<?php echo $location_group['group_id']; ?>_default_select_list">
                            List:<?php echo $default_list; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tỷ lệ :</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <input type="text" name="commission_percent[]" value="<?php echo (float)$location_group['commission_percent']; ?>" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tính theo :</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <select name="commission_percent_type[]" class="form-control">
                            <option value="profit"<?php if($location_group['commission_percent_type'] == 'profit') echo ' selected'; ?>>Lợi nhuận</option>
                        </select>
                    </div>
                </div>


                <div class="form-group"<?php echo $css_style; ?>>
                    <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Trang thái :</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <select name="status[]" class="form-control">
                            <option value="active"<?php if($location_group['status'] == 'active') echo ' selected'; ?>>Hiển thị</option>
                            <option value="unactive"<?php if($location_group['status'] == 'unactive') echo ' selected'; ?>>Ẩn</option>
                        </select>
                    </div>
                </div>


                <div class="form-group"<?php echo $css_style; ?>>
                    <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label" style="visibility: hidden;">Nhóm :</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <a href="javascript:;" onclick="remove_group_section(this);">[Xóa nhóm]</a>
                    </div>
                </div>
                <script type="text/javascript">
                    $( document ).ready(function() {
                        n9_autocomplete('group_<?php echo $location_group['group_id']; ?>')
                        n9_autocomplete('group_<?php echo $location_group['group_id']; ?>_default');
                    });

                </script>
            </div>

    <?php
        }
    ?>
<?php endif; ?>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 col-md-3 col-lg-2 control-label" style="visibility: hidden;">Tạo thêm nhóm :</label>
                            <a href="javascript:void(0);" onclick="frm_location_group_without_db(-1);" id="btn_add_group_section">Thêm nhóm</a>
                        </div>

						<div class="form-group add-register-table">	
							<?php echo form_label(lang('locations_registers').' :', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="table-responsive m-lr-15">
								<table id="price_registers" class="table">
									<thead>
										<tr>
										<th><?php echo lang('common_register_name'); ?> </th>
										<th><?php echo lang('common_delete'); ?></th>
										</tr>
									</thead>
									
									<tbody>
									<p for="register" class="text-danger errors"></p>
									<?php foreach($registers->result() as $register) { ?>
										<tr><td><input type="text" class="form-control" name="registers_to_edit[<?php echo $register->register_id; ?>]" value="<?php echo H($register->name); ?>" /></td><td>
										<a class="delete_register" href="javascript:void(0);" data-register-id='<?php echo $register->register_id; ?>'> <?php echo lang('common_delete'); ?></a>
									</td></tr>
									<?php } ?>
									<!-- abc -->
									</tbody>
								</table>
								<a href="javascript:void(0);" id="add_register"><?php echo lang('locations_add_register'); ?></a>
							</div>
						</div>
						

						<div class="form-group">
							<?php echo form_label("<a href='https://4biz.vn/mercury_activate.php' target='_blank'>".lang('locations_enable_credit_card_processing').'</a>:', 'enable_credit_card_processing',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
								'name'=>'enable_credit_card_processing',
								'id'=>'enable_credit_card_processing',
								'value'=>'1',
								'checked'=>$location_info->enable_credit_card_processing));?>
								<label for="enable_credit_card_processing"><span></span></label>
							</div>
						</div>

						<div id="merchant_information">
							
							<div class="form-group">	
								<?php echo form_label(lang('locations_credit_card_processor').' :', 'credit_card_processor',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_dropdown('credit_card_processor', array('mercury' => 'Vantiv/Mercury','stripe' => 'Stripe','braintree' => 'Braintree'), $location_info->credit_card_processor, 'class="form-control" id="credit_card_processor"');
									?>
								</div>
							</div>
							
							<div id="emv_info">
								<div class="form-group">	
								<?php echo form_label(lang('locations_emv_merchant_id').' :', 'emv_merchant_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'emv_merchant_id',
										'id'=>'emv_merchant_id',
										'autocomplete'=>'off',
										'value'=>$location_info->emv_merchant_id));?>
									</div>
								</div>
						
								<div class="form-group">	
								<?php echo form_label(lang('locations_com_port').' :', 'com_port',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'com_port',
										'id'=>'com_port',
										'autocomplete'=>'off',
										'value'=>$location_info->com_port));?> (<?php echo lang('locations_com_9_is_default');?>)
									</div>
								</div>


								<div class="form-group">	
								<?php echo form_label(lang('locations_listener_port').' :', 'listener_port',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'listener_port',
										'id'=>'listener_port',
										'autocomplete'=>'off',
										'value'=>$location_info->listener_port));?> (<?php echo lang('locations_3333_is_default_port_for_listener');?>)
									</div>
								</div>
						
						
								<div class="form-group">	
								<?php echo form_label('&nbsp;', 'locations_init_mercury_emv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
								 <div id="ajax-loader" style="text-align:center;display:none"><?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>
									<button type="button" id="locations_init_mercury_emv" class="btn btn-primary btn-block"><?php echo lang('locations_init_mercury_emv'); ?></button>
									</div>
								</div>							
						</div>
							
						<div id="mercury_hosted_checkout_info">
							<div class="form-group">	
							<?php echo form_label(lang('locations_hosted_checkout_merchant_id').' :', 'hosted_checkout_merchant_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'hosted_checkout_merchant_id',
									'id'=>'hosted_checkout_merchant_id',
									'autocomplete'=>'off',
									'value'=>$location_info->hosted_checkout_merchant_id));?>
								</div>
							</div>

							<div class="form-group">	
							<?php echo form_label(lang('locations_hosted_checkout_merchant_password').' :', 'hosted_checkout_merchant_password',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'name'=>'hosted_checkout_merchant_password',
									'id'=>'hosted_checkout_merchant_password',
									'autocomplete'=>'off',
									'class'=>'form-control form-inps',
									'value'=>$location_info->hosted_checkout_merchant_password));?>
								<span id="hosted_checkout_merchant_password_note"><?php echo lang('locations_mercury_password_note'); ?></span>
								</div>
							</div>
						</div>

						<div id="stripe_info">						
							<div class="form-group">	
								<?php echo form_label('<a href="https://support.stripe.com/questions/which-currencies-does-stripe-support" target="_blank">'.lang('locations_currency_code').'</a>:', 'stripe_currency_code',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
									'name'=>'stripe_currency_code',
									'id'=>'stripe_currency_code',
									'value'=>$location_info->stripe_currency_code ? $location_info->stripe_currency_code : 'usd'));?>
								</div>
							</div>
						
							<div class="form-group">	
							<?php echo form_label(lang('locations_stripe_private').' :', 'stripe_private',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'stripe_private',
									'id'=>'stripe_private',
									'autocomplete'=>'off',
									'value'=>$location_info->stripe_private));?>
								</div>
							</div>
							
							<div class="form-group">	
							<?php echo form_label(lang('locations_stripe_public').' :', 'stripe_public',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'stripe_public',
									'id'=>'stripe_public',
									'autocomplete'=>'off',
									'value'=>$location_info->stripe_public));?>
								</div>
							</div>
						</div>
						
						<div id="braintree_info">						
							<div class="form-group">	
								<?php echo form_label(lang('common_merchant_id').' :', 'braintree_merchant_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
									'name'=>'braintree_merchant_id',
									'id'=>'braintree_merchant_id',
									'value'=>$location_info->braintree_merchant_id));?>
								</div>
							</div>
						
							<div class="form-group">	
							<?php echo form_label(lang('locations_braintree_public_key').' :', 'braintree_public_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'braintree_public_key',
									'id'=>'braintree_public_key',
									'autocomplete'=>'off',
									'value'=>$location_info->braintree_public_key));?>
								</div>
							</div>
							
							<div class="form-group">	
							<?php echo form_label(lang('locations_braintree_private_key').' :', 'braintree_private_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'braintree_private_key',
									'id'=>'braintree_private_key',
									'autocomplete'=>'off',
									'value'=>$location_info->braintree_private_key));?>
								</div>
							</div>
						</div>						
					</div>
					
						<div class="form-group">	
							<?php echo form_label(lang('locations_receive_stock_alert').' :', 'receive_stock_alert',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'receive_stock_alert',
								'id'=>'receive_stock_alert',
								'value'=>'1',
								'checked'=>$location_info->receive_stock_alert));?>
								<label for="receive_stock_alert"><span></span></label>
							</div>
						</div>

						<div class="form-group" id="stock_alert_email_container">	
						<?php echo form_label(lang('locations_stock_alert_email').' :', 'stock_alert_email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
								'type'=>'text',
								'class'=>'form-control form-inps',
								'name'=>'stock_alert_email',
								'id'=>'stock_alert_email',
								'value'=>$location_info->stock_alert_email));?>
							</div>
						</div>
	
						<div class="form-group">	
							<?php echo form_label(lang('common_default_tax_rate_1').' :', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-4 col-md-4 col-lg-5">
								<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'placeholder' => lang('common_tax_name'),
								'name'=>'default_tax_1_name',
								'id'=>'default_tax_1_name',
								'size'=>'10',
								'value'=>$location_info->default_tax_1_name ? $location_info->default_tax_1_name : lang('common_sales_tax_1')));?>
							</div>

							<div class="col-sm-4 col-md-4 col-lg-5">
								<?php echo form_input(array(
								'class'=>'form-control form-inps-tax',
								'placeholder' => lang('common_tax_percent'),
								'name'=>'default_tax_1_rate',
								'id'=>'default_tax_1_rate',
								'size'=>'4',
								'value'=>$location_info->default_tax_1_rate));?>
								<div class="tax-percent-icon">%</div>
							</div>
						</div>

						<div class="form-group">	
							<?php echo form_label(lang('common_default_tax_rate_2').' :', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-4 col-md-4 col-lg-5">
								<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'placeholder' => lang('common_tax_name'),
								'name'=>'default_tax_2_name',
								'id'=>'default_tax_2_name',
								'size'=>'10',
								'value'=>$location_info->default_tax_2_name ? $location_info->default_tax_2_name : lang('common_sales_tax_2')));?>
							</div>

							<div class="col-sm-4 col-md-4 col-lg-5">
								<?php echo form_input(array(
								'class'=>'form-control form-inps-tax'	,
								'placeholder' => lang('common_tax_percent'),
								'name'=>'default_tax_2_rate',
								'id'=>'default_tax_2_rate',
								'size'=>'4',
								'value'=>$location_info->default_tax_2_rate));?>
								<div class="tax-percent-icon">%</div>
								<div class="clear"></div>
								<?php echo form_checkbox('default_tax_2_cumulative', '1', $location_info->default_tax_2_cumulative ? true : false, 'class="cumulative_checkbox" id="default_tax_2_cumulative"');  ?>
								<label for="default_tax_2_cumulative"><span></span></label>
								<span class="cumulative_label">
									<?php echo lang('common_cumulative'); ?>
								</span>
							</div>
						</div>
					
						<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3" style="display: <?php echo $location_info->default_tax_3_rate ? 'none' : 'block';?>">
							<a href="javascript:void(0);" class="show_more_taxes"><?php echo lang('common_show_more');?> &raquo;</a>
						</div>
					
						<div class="more_taxes_container" style="display: <?php echo $location_info->default_tax_3_rate ? 'block' : 'none';?>">
							
							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_3').' :', 'default_tax_3_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_3_name',
									'id'=>'default_tax_3_name',
									'size'=>'10',
									'value'=>$location_info->default_tax_3_name ? $location_info->default_tax_3_name : ''));?>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_percent'),
									'name'=>'default_tax_3_rate',
									'id'=>'default_tax_3_rate',
									'size'=>'4',
									'value'=>$location_info->default_tax_3_rate));?>%
								</div>
							</div>

							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_4').' :', 'default_tax_4_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_4_name',
									'id'=>'default_tax_4_name',
									'size'=>'10',
									'value'=>$location_info->default_tax_4_name ? $location_info->default_tax_4_name : ''));?>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'default_tax_4_rate',
									'placeholder' => lang('common_tax_percent'),
									'id'=>'default_tax_4_rate',
									'size'=>'4',
									'value'=>$location_info->default_tax_4_rate));?>%
								</div>
							</div>

							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_5').' :', 'default_tax_5_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_5_name',
									'id'=>'default_tax_5_name',
									'size'=>'10',
									'value'=>$location_info->default_tax_5_name ? $location_info->default_tax_5_name : ''));?>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'default_tax_5_rate',
									'placeholder' => lang('common_tax_percent'),
									'id'=>'default_tax_5_rate',
									'size'=>'4',
									'value'=>$location_info->default_tax_5_rate));?>%
								</div>
							</div>
						</div><!--End more Taxes Container-->		

						<div class="form-group">	
							<?php echo form_label(lang('locations_timezone').' :', 'timezone',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('timezone', $all_timezones, $location_info->timezone, 'class="form-control" id="timezone"');
								?>
							</div>
						</div>

						<div class="form-group">	
						<?php echo form_label(anchor('http://mailchimp.com', lang('locations_mailchimp_api_key'), array('target' => '_blank')).' :', 'mailchimp_api_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'mailchimp_api_key',
								'id'=>'mailchimp_api_key',
								'value'=>$location_info->mailchimp_api_key));?>
							</div>
						</div>
						<?php echo form_hidden('redirect', $redirect); ?>

						<div class="form-actions pull-right">
							<?php
							if ($purchase_email)
							{
								echo form_hidden('purchase_email', $purchase_email);
							}
							
//							echo form_submit(array(
//								'name'=>'submitf',
//								'id'=>'submitf',
//								'value'=>lang('common_submit'),
//								'class'=>'submit_button btn btn-primary')
//							);
							?>
                            <input type="button" name="submitf" value="Thực hiện" id="submitf" class="submit_button btn btn-primary" onclick="save_location();"/>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php echo form_close(); ?>
<?php }?>

<div class="modal fade box-modal" id="quick_modal">
</div>

<script type='text/javascript'>
	var submitting = false;
		//validation and submit handling
		$(document).ready(function()
		{	

// 			$('.locations_type input[type="checkbox"]').change(function(){
// 				$('.locations_type input[type="checkbox"]').not( "#" + $(this).attr('id') ).attr('checked', false);
// 				$('.locations_type input[name="location_type"]').val($(this).val());
// 			});
			
			$("#locations_init_mercury_emv").click(function()
			{
				$("#ajax-loader").show();
				$("#locations_init_mercury_emv").hide();							
				
				var emv_merchant_id = $("#emv_merchant_id").val();
				var com_port = $("#com_port").val();
				var listener_port = $("#listener_port").val();
				
				$.post('<?php echo site_url("locations/save_emv_data/".$location_info->location_id);?>', 
				{emv_merchant_id: emv_merchant_id, com_port: com_port, listener_port:listener_port }, function(response) {
					
					if(response.success)
					{
				   	 var data = {};
				   	 <?php
				   	 foreach($mercury_emv_param_download_init_params['post_data'] as $name=>$value)
				   	 {
				   		 if ($name && $value)
				   		 {
				   		 ?>
				  	 		 data['<?php echo $name; ?>'] = '<?php echo $value; ?>';
				   	 	 <?php 
				   		 }
				   	 }
				   	 ?>

 				   	data['ComPort'] = com_port;
				   	data['MerchantID'] = emv_merchant_id;
						
						mercury_emv_param_download(<?php echo json_encode($mercury_emv_param_download_init_params['post_host']); ?>, listener_port, data, <?php echo json_encode(lang('locations_init_device_success')); ?>, <?php echo json_encode(lang('locations_unable_to_init_device'));?>, function()
						{
							$("#ajax-loader").hide();
							$("#locations_init_mercury_emv").show();							
						});
					}
					else
					{
						$("#ajax-loader").hide();
						$("#locations_init_mercury_emv").show();
					}
				}, 'json');
			});
			$('#employees').selectize();
			
         $('#color').colorpicker();
			
			$(".delete_register").click(function()
			{
				$("#location_form_n9").append('<input type="hidden" name="registers_to_delete[]" value="'+$(this).data('register-id')+'" />');
				$(this).parent().parent().remove();
			});
	
			$("#add_register").click(function()
			{
				$("#price_registers tbody").append('<tr><td><input type="text" class="registers_to_add form-control" name="registers_to_add[]" value="" /></td><td>&nbsp;</td></tr>');
			});
						
			if ($("#location_form_auth").length == 1)
			{
			    setTimeout(function(){$(":input:visible:first","#location_form_auth").focus();},100);
			}
			else
			{
			    setTimeout(function(){$(":input:visible:first","#location_form").focus();},100);				
			}
			var submitting = false;
			$('#location_form').validate({
				submitHandler:function(form)
				{
					if (submitting) return;
					submitting = true;
$('#grid-loader').show();
					$(form).ajaxSubmit({
					success:function(response)
					{
						//Don't let the registers be double submitted, so we change the name
						$(".registers_to_add").attr('name', 'registers_added[]');
						
$('#grid-loader').hide();
						submitting = false;						
						show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> +' #' + response.location_id : <?php echo json_encode(lang('common_error')); ?>);
						
						
						if(response.redirect==2 && response.success)
						{
							window.location.href = '<?php echo site_url('locations'); ?>';
						}
						else
						{
							$("html, body").animate({ scrollTop: 0 }, "slow");
						}
										
					},
					<?php if(!$location_info->location_id) { ?>
					resetForm: true,
					<?php } ?>
					dataType:'json'
				});

				},
				ignore: '',
				errorClass: "text-danger",
				errorElement: "p",
				errorPlacement: function(error, element) {
				    error.insertBefore(element);
				},
					highlight:function(element, errorClass, validClass) {
						$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
					},
					unhighlight: function(element, errorClass, validClass) {
						$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
					},
				rules:
				{
					name:
					{
						required:true,
					},
					phone:
					{
						required:true
					},
					address:
					{
						required:true
					},
					timezone:
					{
						required: true
					},
					
					"employees[]": "required",
					
					
		   		},
				messages:
				{
					name:
					{
						required:<?php echo json_encode(lang('locatoins_name_required')); ?>,

					},
					phone:
					{
						required:<?php echo json_encode(lang('locations_phone_required')); ?>,
						number:<?php echo json_encode(lang('locations_phone_valid')); ?>
					},
					address:
					{
						required:<?php echo json_encode(lang('locations_address_required')); ?>
					},
					timezone:
					{
						required:<?php echo json_encode(lang('locations_timezone_required_field')); ?>
					},
					"employees[]": <?php echo json_encode(lang('locations_one_employee_required')); ?>,
						
				}
			});
			
			$("#enable_credit_card_processing").change(check_enable_credit_card_processing).ready(check_enable_credit_card_processing);

			$("#credit_card_processor").change(check_credit_card_processor).ready(check_credit_card_processor);
			
			function check_enable_credit_card_processing()
			{
				if($("#enable_credit_card_processing").prop('checked'))
				{
					$("#merchant_information").show();
				}
				else
				{
					$("#merchant_information").hide();
				}

			}
			
			function check_credit_card_processor()
			{
				var cc_processor = $("#credit_card_processor").val();
				if (cc_processor == 'mercury')
				{
					$("#emv_info").show();
					$("#mercury_hosted_checkout_info").show();
					$("#stripe_info").hide();
					$("#braintree_info").hide();
					
				}
				else if (cc_processor == 'stripe')
				{
					$("#emv_info").hide();
					$("#mercury_hosted_checkout_info").hide();
					$("#stripe_info").show();
					$("#braintree_info").hide();
				}
				else if (cc_processor == 'braintree')
				{
					$("#emv_info").hide();
					$("#mercury_hosted_checkout_info").hide();
					$("#stripe_info").hide();
					$("#braintree_info").show();
				}
			}
			
			$("#receive_stock_alert").change(check_enable_stock_alert).ready(check_enable_stock_alert);
			
			function check_enable_stock_alert()
			{
				if($("#receive_stock_alert").prop('checked'))
				{
					$("#stock_alert_email_container").show();
				}
				else
				{
					$("#stock_alert_email_container").hide();
				}

			}
			
		});

</script>
<?php $this->load->view('partial/footer'); ?>