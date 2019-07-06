<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
	$(document).ready(function() 
	{ 
        $('#delete_multi').click(function(){
        	var selected = EMPLOYEES_MANAGE.getSelectedItems();
            if (selected.length == 0) {
                bootbox.alert(<?php echo json_encode('Bạn phải chọn ít nhất 1 bản ghi!'); ?>);
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
	}); 
</script>

<div class="row">
	<div class="col-md-3">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search">
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<select class="form-control" name="" id="">
				<option value="">Cấp bậc chuyên viên</option>
				<option value="">Ngạch 1</option>
				<option value="">Ngạch 2</option>
				<option value="">Ngạch 3</option>
			</select>
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<select class="form-control" name="" id="">
				<option value="">Chức danh</option>
				<option value="">Giám đốc</option>
				<option value="">Lãnh đạo</option>
				<option value="">Quản lý</option>
			</select>
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group">
			<select class="form-control" name="" id="">
				<option value="">Thời gian làm việc tại VCB</option>
				<option value="">3 năm</option>
				<option value="">1 năm</option>
				<option value="">dưới 1 năm</option>
			</select>
		</div>
	</div>
</div>
<div class="manage_buttons">
<div class="manage-row-options hidden" id="manage-row-options">
	<div class="email_buttons text-center">
		<a href="javascript:;" id="delete_multi" class="btn btn-red disabled_ delete_inactive " title="Xóa"><span class="">Xóa lựa chọn</span></a>
	</div>
</div>








	<div class="cl">

		<div class="pull-left">
			<!-- <div class="search no-left-border">
				<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
			</div>
			 -->
				
			
		</div>
		<div class="pull-right">
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php echo anchor('/approver_groups',
                          '<span class="">Quản lý nhóm phê duyệt</span>',
                          array('target' => '_blank', 'id' => 'approver_groups_manage', 'class'=>'btn btn-primary', 'title' => lang('groups_manage')));?>
                          
	                    <?php if ($this->Employee->has_module_action_permission('groups', 'search', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
	                    <?php echo anchor('/groups',
	                                      '<span class="">'.lang('groups_manage').'</span>',
	                                      array('target' => '_blank', 'id' => 'new-person-btn', 'class'=>'btn btn-primary', 'title' => lang('groups_manage')));?>
	                    <?php endif; ?>
	
	                    <?php if ($this->Employee->has_module_action_permission('departments', 'search', $this->Employee->get_logged_in_employee_info()->person_id)) :?>
	                    <?php echo anchor('/departments',
	                                      '<span class="">'.lang('departments_manage').'</span>',
	                                      array('target' => '_blank', 'id' => 'new-person-btn', 'class'=>'btn btn-primary', 'title' => lang('departments_manage')));?>
	                    <?php endif; ?>

					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
						<?php if ($this->Employee->count_all() < MAX_EMPLOYEE) { ?>
							<?php echo anchor("$controller_name/view/-1/",
								'<span class="">'.lang($controller_name.'_new').'</span>',
								array('id' => 'new-person-btn', 'class'=>'btn btn-primary', 'title'=>lang($controller_name.'_new')));?>
						<?php } ?>
					<?php } ?>
					<div class="piluku-dropdown">
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<i class="ion-android-more-horizontal"></i>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li>
								<?php echo anchor("$controller_name/excel_import/",
									'<span class="">'.lang('common_excel_import').'</span>',
									array('class'=>'hidden-xs','title'=>lang('common_excel_import'))); ?>
							</li>
							<li>
								<?php
								echo anchor("$controller_name/excel_export",
									'<span class="">'.lang('common_excel_export').'</span>',
									array('class'=>'hidden-xs import','title'=>lang('common_excel_export'))); ?>
							</li>
							<li>
								<?php 
								echo anchor("$controller_name/cleanup",
									'<span class="">'.lang($controller_name."_cleanup_old_customers").'</span>',
									array('id'=>'cleanup', 'class'=>'','title'=> lang($controller_name."_cleanup_old_customers"))); ?>
							</li>
						</ul>
					</div>
				</div>
			</div>				
		</div>
        
	</div>
</div>

	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku" id="list_view">
				
			</div>	
		</div>
	</div>
</div>

<?php $this->load->view("partial/footer"); ?>