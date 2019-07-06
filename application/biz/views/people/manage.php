
<?php $this->load->view("partial/header"); ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/modal.js" ></script>
<style type="text/css">.dnone{display: none}</style>
<?php $this->load->model('Customer'); ?>
<?php 
$start_of_time =  date('d-m-Y', 0);
$today = date('d-m-Y');

?>
<script type="text/javascript">
$(document).ready(function (){
    var table_columns = ["", "id", 'title','', ''];
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
	enable_search('<?php echo site_url("$controller_name/suggest_sms");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode('Bạn muốn xóa SMS này?'); ?>,<?php echo json_encode(lang($controller_name . "_none_selected")); ?>);
    $(".delete_sms_mail_tmp").click(function(){
        var id = $(this).attr("data-id");
        var parent = $(this).parent().parent();
        var data = "ids=" + id;
        $.ajax({
            type: "post",
            url: '<?php echo site_url("$controller_name/delete_sms_mail_id");?>',
            data: data,
            success: function(data){
                $(parent).remove();
			//$(".number_mail").html(data);
            }
        });
        return false;
    });     


	//delete_sms_tmp_all
	$('.delete_all_tmp').click(function(){
	  $.ajax({
	            type: "post",
	            url: '<?php echo site_url("$controller_name/delete_tmp_all");?>',
	            success: function(data){
	                $('table tbody').html("<tr><td colspan='3' style='text-align: center'>Không có khách hàng nào</td></tr>")
	            }
	        });
	})
});
</script>
<script type="text/javascript">

	$(document).ready(function() 
	{ 		
		<?php if ($controller_name == 'suppliers') { ?>
			var table_columns = ['','person_id', 'company_name','name','head','email','phone_number','total'];
			
			<?php } else { ?>
				var table_columns = ['','<?php echo $this->db->dbprefix('people'); ?>'+'.person_id','last_name','email','phone_number'];
			<?php } ?>

				// enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>",table_columns, <?php echo $per_page; ?>, <?php echo json_encode($order_col);?>, <?php echo json_encode($order_dir);?>);
				enable_select_all();
				enable_checkboxes();
				enable_row_selection();
				enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
				
				enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
				enable_cleanup(<?php echo json_encode(lang($controller_name."_confirm_cleanup"));?>);

				<?php if ($this->session->flashdata('manage_success_message')) { ?>
					show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);

        <?php $this->session->unset_userdata(['manage_success_message']);} ?>

				$('#delete_cus').click(function(){
					var selected = get_selected_values();
					if (selected.length == 0) {
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_labels')); ?>);
						return false;
					}

					bootbox.confirm('Bạn có chắc muốn xóa không?', function(result)
					{
						if (result)
						{
							var _data = {};
							_data['items'] = selected;
							coreAjax.call(
								'<?php echo site_url("$controller_name/deletes");?>',
								_data,
								function(response){
									show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
									window.location.href = '<?php echo site_url($controller_name);?>';
								}
							);
						}
					});
					

				})

                $('#delete_multi').click(function(){
                    var selected = get_selected_values();
                    if (selected.length == 0) {
                        bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_labels')); ?>);
                        return false;
                    }

                    bootbox.confirm('Bạn có chắc muốn xóa không?', function(result)
                    {
                        if (result)
                        {
                            $.ajax({
                                type: "POST",
                                url: '<?php echo site_url("$controller_name/deletes");?>',
                                data: {
                                    items : selected
                                },
                                success: function(string){
                                    show_feedback('success', 'Xóa thành công', true ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
                                    window.location.href = '<?php echo site_url($controller_name);?>';
                                }
                            });

                        }
                    });
                })
			
				$('#labels').click(function()
				{
					var selected = get_selected_values();
					if (selected.length == 0)
					{
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_labels')); ?>);
						return false;
					}

					$(this).attr('href','<?php echo site_url("$controller_name/mailing_labels");?>/'+selected.join('~'));
				});

				$('#sendSMS').click(function(){
					var selected = get_selected_values();
					if (selected.length == 0 || selected.length >1)
					{
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_sms')); ?>);
						return false;
					}

					$(this).attr('href','<?php echo site_url("$controller_name/send_sms");?>/'+selected['0']);
				});
				$('#sendMail').click(function(){
                    $.ajax({
                        type: "GET",
                        url: BASE_URL + 'customers/send_mail',
                        success: function(html){
                            $('#quick_modal').addClass('size-700');
                            $('#quick_modal').html(html);
                            $('#quick_modal').modal('toggle');
					}
                    });
				});	

				$('#sendSms_list').click(function(){
					//var selected = get_selected_values();
					
					$(this).attr('href','<?php echo site_url("$controller_name/send_sms_list");?>');
				});			
				
				$('#sendMail_list').click(function(){
                    $.ajax({
                        type: "GET",
                        url: BASE_URL + 'customers/send_mail?type=list',
                        success: function(html){
                            $('#quick_modal').addClass('size-700');
                            $('#quick_modal').html(html);
                            $('#quick_modal').modal('toggle');
                        }
                    });
				});	


				$('#check_list_send_mail').click(function()
				{
					var selected = get_selected_values();
					if (selected.length == 0)
					{
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_labels')); ?>);
						return false;
					}

					$('.dnone').show();
					$('.total_send').text(selected.length);
					var _data = {};
					_data['items'] = selected;
					_data['ck'] = 'send_mail';
					coreAjax.call(
						'<?php echo site_url("$controller_name/save_list_send_all");?>',
						_data,
						function(response)
						{
							// TODO
							
						}
					);
				});
				$('#check_list_send_sms').click(function()
				{
					var selected = get_selected_values();
					if (selected.length == 0)
					{
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_labels')); ?>);
						return false;
					}

					$('.dnone').show();
					$('.total_send').text(selected.length);
					var _data = {};
					_data['items'] = selected;
					_data['ck'] = 'send_sms';
					coreAjax.call(
						'<?php echo site_url("$controller_name/save_list_send_all");?>',
						_data,
						function(response)
						{
							// TODO
							console.log(response);
						}
					);
				});
		}); 
</script>


<div class="manage_buttons">
<div class="manage-row-options hidden">
	<div class="email_buttons text-center">
		<?php if ($controller_name =='customers') { ?>
		<a class="btn btn-primary btn-lg" title="<?php echo (lang('customers_sms_send_sms'));?>" id="sendSMS" href="<?php echo current_url(). '#'; ?>"  data-toggle="modal" data-target="#myModal">
			<span class=""><?php echo (lang('customers_sms_send_sms')); ?></span>
		</a>
		<?php } ?>

		<?php if ($controller_name =='customers') { ?>
		<a class="btn btn-primary btn-lg" title="<?php echo lang("common_email");?>" id="sendMail">
			<span class=""><?php echo lang('common_email'); ?></span>
		</a>
		<?php } ?>
		
		<?php if ($controller_name =='customers') { ?>
		<a class="btn btn-primary btn-lg check_list_send_mail" id="check_list_send_mail">
				<span><?php echo lang('customers_mail_add_mail_temp'); ?></span>
			</a>
		<?php } ?>

		<?php if ($controller_name =='customers') { ?>	
			<a class="btn btn-primary btn-lg check_list_send_sms" id="check_list_send_sms">
				<span class=""><?php echo 'Danh sách sms tạm'; ?></span>
			</a>
		<?php } ?>

        <?php if($controller_name != 'suppliers'){ ?>
		
		<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
		<?php echo anchor("$controller_name#",
			'<span class="">Xóa lựa chọn</span>'
			,array('id'=>'delete_cus', 'class'=>'btn btn-red btn-lg disabled_ delete_inactive ','title'=>lang("common_delete"))); ?>
		<?php } ?>

        <?php }else {
        ?>
            <a href="javascript:;" id="delete_multi" class="btn btn-red btn-lg disabled_ delete_inactive " title="Xóa"><span class="">Xóa lựa chọn</span></a>
        <?php
        }?>


	</div>
</div>
	<div class="cl">
		<div class="pull-left">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off', 'class' => 'form-inline')); ?>
				<div class="search no-left-border">
					<select class="form-control unit_type" name="unit_type">
						<option value="">Tìm kiếm theo loại đơn vị</option>
						<?php foreach ($all_unit_type as $value) {
							
						 ?>
						<option value="<?php echo $value['id']?>"><?php echo $value['name']?></option>
										<?php }?>	
					</select>
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
				</div>
				
				<?php if(isset($type) && $type == 'customer') { ?>
				<div class="form-group">
				<!-- quản lý nhân viên -->
																						  
					<label style="margin-left: 10px;"><?php echo lang('customers_filter_created_by'); ?></label>
					<?php echo form_dropdown('created_by', $employees, $selected_employee,'class="form-control"');?>
				<!-- danh mục -->
					<label><?php echo lang('customers_filter_categorie'); ?></label>
					<?php echo form_dropdown('category_id', $category, $selected_category,'class="form-control"');?>
				<!-- danh mục con -->
					<label style="margin-left: 10px;"><?php echo lang('customers_filter_categorie_child'); ?></label>
					<?php echo form_dropdown('category_child_id', $category_child, $selected_category_child,'class="form-control customers_filter_categorie_child"'); ?>
				</div>																																	 
				<?php } ?>
				<div class="clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
					<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			</form>
		</div>
		<div class="pull-right">
			<?php
			$page = $this->router->fetch_class();
			//$countAll = $this->Customer->count_all_customers(); ?>
			<?php if ($page == 'customers' && $this->Customer->count_all_customers() >= MAX_CUSTOMER) { ?>
				<div class="col-md-9" style="margin-top: 30px;">
					<strong style="float: left;"><a href="/#" target="_blank"><?php echo lang('customers_adding_location_requires_addtional_license'); ?></a></strong>
				</div>
			<?php } ?>
			
			<?php if ($page == 'employees' && $this->Employee->count_all() >= MAX_EMPLOYEE) { ?>
				<div class="col-md-9" style="margin-top: 30px;">
					<strong style="float: left;"><a href="/#" target="_blank"><?php echo lang('employees_adding_location_requires_addtional_license'); ?></a></strong>
				</div>
			<?php } ?>
			
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php if($page == 'employees') {
					?>
					<?php echo anchor('/approver_groups',
                          '<span class="">Quản lý nhóm phê duyệt</span>',
                          array('target' => '_blank', 'id' => 'approver_groups_manage', 'class'=>'btn btn-primary btn-lg', 'title' => lang('groups_manage')));?>
                          
	                    <?php if ($this->Employee->has_module_action_permission('groups', 'search', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
	                    <?php echo anchor('/groups',
	                                      '<span class="">'.lang('groups_manage').'</span>',
	                                      array('target' => '_blank', 'id' => 'new-person-btn', 'class'=>'btn btn-primary btn-lg', 'title' => lang('groups_manage')));?>
	                    <?php endif; ?>
	
	                    <?php if ($this->Employee->has_module_action_permission('departments', 'search', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
	                    <?php echo anchor('/departments',
	                                      '<span class="">'.lang('departments_manage').'</span>',
	                                      array('target' => '_blank', 'id' => 'new-person-btn', 'class'=>'btn btn-primary btn-lg', 'title' => lang('departments_manage')));?>
	                    <?php endif; ?>
	               <?php }?>

					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
						<?php if ($page == 'customers' && $this->Customer->count_all_customers() < MAX_CUSTOMER ||
								$page == 'employees' && $this->Employee->count_all() < MAX_EMPLOYEE || $page == 'suppliers') { ?>
							<?php echo anchor("$controller_name/view/-1/",
								'<span class="">'.lang($controller_name.'_new').'</span>',
								array('id' => 'new-person-btn', 'class'=>'btn btn-primary btn-lg', 'title'=>lang($controller_name.'_new')));?>
						<?php } ?>
					<?php } ?>
					<div class="piluku-dropdown">
						
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<i class="ion-android-more-horizontal"></i>
						</button>
						<ul class="dropdown-menu" role="menu">
							<!-- <li>
								<?php echo anchor("$controller_name/categories",
									'<span class="">'.lang("items_manage_categories").'</span>',
									array('class'=>'',
									'title'=>lang('items_manage_categories')));
								?>
							</li> -->
							<li>			 
								<?php if ($controller_name =='customers') {  
								?>
								<?php echo anchor("$controller_name/manage_sms/",
									'<span class="">'.lang('customers_sms_menu_link').'</span>',
									array('class'=>'hidden-xs','title'=>lang('customers_sms_menu_link')));
								} ?>
							</li>
							<li>
								<?php if ($controller_name == 'customers' || $controller_name == 'suppliers') { ?>
									<?php
                                    if($controller_name == 'customers')
                                        $contract_option = 'customer';
                                    elseif($controller_name == 'suppliers')
                                        $contract_option = 'supplier';

									echo anchor(base_url() . 'contracts/index/'.$contract_option, '<span>Quản lý hợp đồng</span>',
									array('class' => 'hidden-xs', 'title' => 'Quản lý hợp đồng') );
									?>								
								<?php } ?>
							</li>
							<li>
								<?php if ($controller_name =='customers') {  
								?>
								<?php echo anchor("$controller_name/manage_mail",
									'<span class="">'.lang('customers_mail_menu_link').'</span>',
									array('class'=>'hidden-xs','title'=>lang('customers_mail_menu_link')));
								} ?>
							</li>
							<li>
								<?php if ($controller_name =='customers') { ?>
								<?php echo anchor("$controller_name/quotes_contract",
									'<span class="">'.lang('customers_quotes_contract_menu_link').'</span>',
									array('class'=>'hidden-xs','title'=>lang('customers_quotes_contract_menu_link')));
								} ?>
							</li>
													
							<li>
								<?php if ($controller_name == 'employees' || $controller_name =='customers' || $controller_name == 'suppliers') {
								?>
								<?php echo anchor("$controller_name/excel_import/",
									'<span class="">'.lang('common_excel_import').'</span>',
									array('class'=>'hidden-xs','title'=>lang('common_excel_import')));
								} ?>
							</li>
							<li>
								<?php
								if ($controller_name == 'customers' || $controller_name == 'employees' || $controller_name == 'suppliers') {
									echo anchor("$controller_name/excel_export",
										'<span class="">'.lang('common_excel_export').'</span>',
										array('class'=>'hidden-xs import','title'=>lang('common_excel_export')));

								}
								?>
							</li>
							<li>
								<?php if ($controller_name =='customers' or $controller_name =='employees' or $controller_name =='suppliers') {?>
									<?php echo 
									anchor("$controller_name/cleanup",
										'<span class="">'.lang($controller_name."_cleanup_old_customers").'</span>',
										array('id'=>'cleanup', 
											'class'=>'','title'=> lang($controller_name."_cleanup_old_customers"))); 
											?>
								<?php } ?>
							</li>
						</ul>
					</div>
				</div>
			</div>				
		</div>
        <div class="cl"></div>
	</div>
</div>

	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
				<h3 class="panel-title space_title">

					<span id="all_items" class="<?php if(isset($infocustomers)) echo ($infocustomers == 1) ? 'selected' : '' ?> item-tabs">
					<?php echo lang('common_list_of').' '.lang('module_'.$controller_name); ?>	
					</span>

					<span title="<?php if(isset($total_rows)) echo $total_rows; ?> total <?php echo $controller_name?>" class="badge bg-primary tip-left"><?php if(isset($total_rows)) echo $total_rows; ?></span>

					<?php if (isset($total_rows_send) && $total_rows_send > 0) { 
						$dnone = 'dblock';
					} else{
						$dnone = 'dnone';
					}?>
					<span id="list_send" class="<?php if(isset($infocustomers)) echo ($infocustomers == 2) ? 'selected' : '' ?> item-tabs <?php echo $dnone; ?>">
						<span class=""><?php echo lang('customers_list_add_send');?></span>
					</span>									
					<span title="<?php echo isset($total_rows_send);?> total <?php echo $controller_name?>" class="badge bg-primary tip-left <?php echo $dnone; ?> total_send"><?php echo $total_rows_send; ?></span>
					
					<?php if ($controller_name == 'customers') { ?>
						
					
					<span id="birth" class="<?php echo ($infocustomers == 3) ? 'selected' : '' ?> item-tabs">
						<span>Sinh nhật</span>
					</span>
					<span title="<?php echo isset($total_birth);?> total <?php echo $controller_name; ?>" class="badge bg-primary tip-left total_send"><?php echo $total_birth; ?></span>
					
					<span id="nos" class="<?php echo ($infocustomers == 4) ? 'selected' : '' ?> item-tabs">
						<span>Nợ</span>
					</span>
					<span title="<?php echo isset($total_sus['count(*)']);?> total <?php echo $controller_name; ?>" class="badge bg-primary tip-left total_send"><?php echo $total_sus['count(*)']; ?></span>

					<?php } ?>
					
					<span class="panel-options custom">
						<?php if($pagination) {  ?>
							<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
								<?php echo $pagination;?>		
							</div>
						<?php }  ?>
					</span>
				</h3>
			</div>
				<div class="panel-body nopadding table_holder table-responsive" >
					<?php
						if (isset($infocustomers) && $infocustomers == 3) { ?>							
							<div id="menutest" class="menu-togged">           
									<div style="position: absolute;width: 91%;margin-left: 37px;height: 38px;"></div>                     
                                    <table class="mytable_ ckallBrith_" id="sortable_table" cellspacing="0" style="width: 100%; margin: 0px !important;">
                                        <thead>
                                        	<th style="width:42px;">
                                        		<input type="checkbox" class="ckall" name="ckall" id="select_all"/>
                                        		<label for="select_all"><span></span></label>
                                        	</th>
                                            <th class="click-one">Tên</th>
                                            <th class="click-one" style="text-align:left;">Ngày sinh nhật</th>
                                            <th class="click-one" style="text-align:left; width:100px;">Gửi E-Mail</th>
                                            <th class="click-one" style="text-align:left; width:100px;">Gửi SMS</th>
                                        </thead>                                       
                                        <tbody>
                                        <?php  if ($customer != null){                                         
                                            foreach ($customer as $customer1){
                                                $customer2=$this->Customer->findPerson($customer1['person_id']);
                                                $customer3 = $this->Customer->get_customer_mail_auto($customer1['person_id']);
                                            ?>
                                            <tr>
                                            	<td>                                           
													<input type='checkbox' id='birth_<?php echo $customer1['person_id'];?>' value='<?php echo $customer1['person_id'];?>'/>
													<label for='birth_<?php echo $customer1['person_id'];?>'><span class="birth_" data-birth='<?php echo $customer1['person_id'];?>'></span></label>
                                            	</td>
                                                <td>
                                                    <a class="a-menu" href="<?php echo site_url('reports/specific_'.( 'customer').'/'.$start_of_time.'/'.$today.'/'.$customer2[0]['person_id'].'/all/0') ?>"><?php echo $customer2[0]['first_name'].' '.$customer2[0]['last_name']; ?></a>
                                                </td>
                                                <td>
                                                    <?php echo date("d-m-Y", strtotime($customer2[0]['birth_date'])); ?>
                                                </td>
                                                <td style="text-align: left;">
                                                    <?php
                                                    $activeCount = $this->Customer->getCountSendMail($customer1['person_id']);
                                                    if ($activeCount > 0) {
                                                        echo "Đã gửi";
                                                    }else{
                                                        echo "Chưa gửi";
                                                    }
                                                    ?>
                                                    
                                                </td>
                                                <td> <?php
                                                	$activeCountsms = $this->Customer->getCountSendSms($customer1['person_id']);
                                                    if ($activeCountsms > 0) {
                                                        echo "Đã gửi";
                                                    }else{
                                                        echo "Chưa gửi";
                                                    }
                                                    ?></td>
                                            </tr>
                                            <?php
                                            }
                                        }else{
                                            echo "<tr>";
                                            echo "<td style='text-align: center' colspan='3'>Không có khách hàng sinh nhật</td>";
                                            echo "</tr>";
                                        }
                                        ?>                                            
                                        </tbody>                                        
                                    </table>                                    
                            </div>
						<?php }elseif(isset($infocustomers) && $infocustomers == 4){ ?>
							<div id="menutest5" class="menu-togged">
                                <table class="mytable" cellspacing="0" style="width: 100%; margin: 0px !important;">
                                    <thead>
                                        <tr>
                                        	<th>ID</th>
                                            <th>Tên</th>
                                            <th>E-Mail</th>
                                            <th>SĐT</th>
                                            <th>Công nợ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($suspends_date != null){   
                                        foreach ($suspends_date->result() as $suspend_date){
                                        ?>
                                            <tr>
                                            	<td><?php echo $suspend_date->person_id;?></td>                                            	
                                                <td>
                                                   <?php echo $suspend_date->first_name . ' ' . $suspend_date->last_name;?>
                                                </td>
                                                <td><?php echo $suspend_date->email;?></td>
                                                <td><?php echo $suspend_date->phone_number; ?></td>
                                                
                                                <td><?php echo to_currency($suspend_date->balance) ;?></td>
                                            </tr>
                                        <?php }                                  
                                    }else{
                                        echo "<tr>";
                                            echo "<td style='text-align: center' colspan='4'>Không có đơn hàng nợ</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>   
                            
						<?php }else{ ?>
						<?php echo $manage_table; ?>			
						<?php } ?>
				</div>	
		</div>	
		<?php if($pagination) {  ?>
		<div class="text-center">
		<div class="pagination hidden-print alternate text-center" id="pagination_bottom" >
			<?php echo $pagination;?>
		</div>
		<?php } ?>
		</div>
	</div>
</div>
<script type="text/javascript">
var ITEM_LIST = {
	init: function()
	{
		$('#all_items').unbind('click').bind('click', function() {
			if (!$(this).hasClass('selected')) {
				window.location.href = '<?php echo site_url($controller_name.'/index'); ?>';
			}
		});

		// TODO
		$('#list_send').unbind('click').bind('click', function() {
			if (!$(this).hasClass('selected')) {
				window.location.href = '<?php echo site_url($controller_name.'/index?send_all=1');?>';
				$('.item-tabs').removeClass('selected');				
				$(this).addClass('selected');
			}
		});

		$('#birth').unbind('click').bind('click', function(){
			if (!$(this).hasClass('selected')) {
				window.location.href = '<?php echo site_url($controller_name. '/index?birth=1'); ?>'
				$('.item-tabs').removeClass('selected');
				$(this).addClass('selected');
			}
		});

		$('#nos').unbind('click').bind('click', function(){
			if(!$(this).hasClass('selected')){
				window.location.href = '<?php echo site_url($controller_name . '/index?no=1'); ?>';
				$('.item-tabs').removeClass('selected');
				$(this).addClass('selected');
			}
		});
	},
}

var CUSTOMER_MANAGE = {
	init: function()
	{
		
		CUSTOMER_MANAGE.changeEventOnCreateBy();
		CUSTOMER_MANAGE.changeEventOnCategory();
		CUSTOMER_MANAGE.changeEventOnCategory_child();
	},
	
	changeEventOnCreateBy: function()
	{
		$('#search_form [name="created_by"]').change(function(){
			$('#search_form').submit();
		});
	},
	changeEventOnCategory: function()
	{
		$('#search_form [name="category_id"]').change(function(){
			$('#search_form').submit();
		});
	},
	changeEventOnCategory_child: function()
	{
		$('#search_form [name="category_child_id"]').change(function(){
			$('#search_form').submit();
		});
	}
}

$( document ).ready(function() {
	CUSTOMER_MANAGE.init();
	ITEM_LIST.init();

	$('#select_all_sms').click(function(){
		var idck = '';
	 	if ($(this).prop('checked')) {
	 		$("#sortable_table_ tbody .cksms").each(function(){
		 		$(this).prop('checked', true);
		 		$(this).parent().parent().find("td").addClass('selected').css("backgroundColor", "");
		 		idck += $(this).val() + '~';
		 	});
		 	var data = "ids=" + idck;
	        $.ajax({
	            type: "post",
	            url: '<?php echo site_url("$controller_name/delete_sms_mail_check_sms_id");?>/' + idck,
	            data: data,
	            success: function(data){
	               // $(parent).remove();
				//$(".number_mail").html(data);
	            }
	        });
	 	}else{
			$("#sortable_table_ tbody .cksms").each(function(){
	 			$(this).prop('checked', false);
	 			$(this).parent().parent().find("td").removeClass('selected');
	 			idck += $(this).val() + '~';
	 		});
	 		var data = "ids=" + idck;
			$.ajax({
				type: "post",
				url: '<?php echo site_url("$controller_name/delete_sms_mail_uncheck_sms_id") ?>/' + idck
				,
				success: function(data){
					//TODU
				}
			});
	 	}
	 });

    $( ".mclick" ).click(function() {
        var data_mail = $(this).attr('data-mail');
        var customer_chk = $('#customer_'+data_mail);
        if (customer_chk.prop('checked') == true){
            var action = "uncheck";
        }else{
            var action = "check";
        }

        $.ajax({
            type: "post",
            url: BASE_URL + 'customers/mail_checkbox',
            data : {
                customer_id : data_mail,
                action : action
            },
            success: function(string){
            }
        });
    });

	//email
	$('#select_all').click(function()
	{
		var idck = '';
		if($(this).prop('checked'))
		{	
			$("#sortable_table_ tbody .ckmail").each(function()
			{
				$(this).prop('checked',true);
				$(this).parent().parent().find("td").addClass('selected').css("backgroundColor","");
				idck += $(this).val() + '~';
			});
			
	        var data = "ids=" + idck;
	        $.ajax({
	            type: "post",
	            url: '<?php echo site_url("$controller_name/delete_sms_mail_check_id");?>/' + idck,
	            data: data,
	            success: function(data){
	            }
	        });
	        //return false;
		}
		else
		{
			$("#sortable_table_ tbody .ckmail").each(function()
			{
				$(this).prop('checked',false);
				$(this).parent().parent().find("td").removeClass('selected');	
				idck += $(this).val() + '~';
			});  
			//uncheck
			var data = "ids=" + idck;
			$.ajax({
				type: "post",
				url: '<?php echo site_url("$controller_name/delete_sms_mail_uncheck_id") ?>/' + idck
				,
				success: function(data){
					//TODU
				}
			});
		}
	 });

    $(".sclick").click(function(){
        var id = $(this).attr("data-sms");
        var data = "ids=" + id;
        $.ajax({
            type: "post",
            url: '<?php echo site_url("$controller_name/check_one_sms_id");?>/' + id,
            data: data,
            success: function(data){
            }
        });
    }); 

    //sinh nhat
    $(".birth_").click(function(){
    	var id = $(this).attr("data-birth");
    	var data = "ids=" + id;
    	$.ajax({
    		type: "post",
    		url: '<?php echo site_url("$controller_name/birthcheck"); ?>/' + id,
    		data: data,
    		success: function(data){}
    	});
    });

	$('#ckall').click(function()
	{
		var idck = '';
		if($(this).prop('checked'))
		{	
			$("#ckallBrith tbody :checkbox").each(function()
			{
				$(this).prop('checked',true);
				$(this).parent().parent().find("td").addClass('selected').css("backgroundColor","");
				idck += $(this).val() + '~';
			});
			
	        var data = "ids=" + idck;
	        $.ajax({
	            type: "post",
	            url: '<?php echo site_url("$controller_name/birth_add");?>/' + idck,
	            data: data,
	            success: function(data){
	            	console.log();
	            }
	        });
	        //return false;
		}
		else
		{
			$("#ckallBrith tbody :checkbox").each(function()
			{
				$(this).prop('checked',false);
				$(this).parent().parent().find("td").removeClass('selected');	
				idck += $(this).val() + '~';
			});  
			
			var data = "ids=" + idck;
			$.ajax({
				type: "post",
				url: '<?php echo site_url("$controller_name/birth_remove") ?>/' + idck
				,
				success: function(data){
					//TODU
				}
			});
		}
	});

	$('.click-one').click(function(even){
		even.stopPropagation(); // St
		return true;
	});
	

});

</script>
<style type="text/css">
#sortable_table tr th {
    white-space: normal;
}
</style>
<div class="modal fade box-modal" id="quick_modal">
</div>

<?php $this->load->view("partial/footer"); ?>