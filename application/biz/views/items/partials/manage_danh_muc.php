
<div class="">
			 <nav class="quick-nav">
            <!-- <a class="quick-nav-trigger" href="#0">
                <span aria-hidden="true"></span>
            </a> -->
            <ul>
            	<?php if ($this->Employee->has_module_action_permission($controller_name, 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					 <li>
                    	<a href="<?php echo base_url().$controller_name.'/categories' ?>" class="active">
                        <span><?php echo lang("items_manage_categories"); ?></span>
                        <i class="fa fa-sitemap" aria-hidden="true"></i>
                    	</a>
                	</li>
				<?php } ?>	
				
				<?php if ($this->Employee->has_module_action_permission($controller_name, 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					<li>
                    	<a href="<?php echo base_url().$controller_name.'/manage_tags' ?>" class="active">
                        <span><?php echo lang("items_manage_tags"); ?></span>
                        <i class="fa fa-object-group" aria-hidden="true"></i>
                    	</a>
                	</li>
				<?php } ?>
				
				<li>
					<li>
                    	<a href="<?php echo base_url().$controller_name.'/services' ?>" class="active">
                        <span>Quản lý dịch vụ</span>
                        <i class="fa fa-server" aria-hidden="true"></i>
                    	</a>
                	</li>
				</li>
				<li>
					<li>
                    	<a href="<?php echo base_url().$controller_name.'/manage_measures' ?>" class="active">
                         <span><?php echo lang("items_manage_measures"); ?></span>
                        <i class="fa fa-gavel" aria-hidden="true"></i>
                    	</a>
                	</li>
					
				</li>
				
				<?php if ($this->Employee->has_module_action_permission($controller_name, 'count_inventory', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					<li>
                    	<a href="<?php echo base_url().$controller_name.'/count' ?>" class="active">
                         <span><?php echo lang("items_count_inventory"); ?></span>
                        <i class="fa fa-check-square-o" aria-hidden="true"></i>
                    	</a>
                	</li>
				<?php } ?>
				
				<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>	
					<li>
                    	<a href="<?php echo base_url().$controller_name.'/excel_import/' ?>" class="active">
                         <span><?php echo lang("common_excel_import"); ?></span>
                        <i class="fa fa-table" aria-hidden="true"></i>
                    	</a>
                	</li>
					<li>
                    	<a href="<?php echo base_url().$controller_name.'/excel_export/' ?>" class="active">
                        <span><?php echo lang("common_excel_export"); ?></span>
                       <i class="fa fa-download" aria-hidden="true"></i>
                    	</a>
                	</li>
					
				<?php }?>
				
				<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					<li>
                    	<a href="<?php echo base_url().$controller_name.'/cleanup/' ?>" class="active">
                        <span><?php echo lang("items_cleanup_old_items"); ?></span>
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    	</a>
                	</li>

				<?php }?>
				<li>
                	<a href="<?php echo base_url().$controller_name.'/history_transfer/' ?>" class="active">
                    <span><?php echo lang("items_history_transfer"); ?></span>
                    <i class="fa fa-history" aria-hidden="true"></i>
                	</a>
            	</li>
            </ul>
            <!-- <span aria-hidden="true" class="quick-nav-bg"></span> -->
        </nav>
        <div class="quick-nav-overlay"></div>
			<div class="buttons-list items-buttons">
				<div class="pull-right-btn">
                    <?php echo anchor("$controller_name/categories",'<span class="">'.'Quản lý nhóm dịch vụ'.'</span>',array('class'=>'btn btn-primary','title'=>'Quản lý loại'));?>
                    <?php echo anchor("$controller_name/services",'<span class="">'.'Quản lý biểu mẫu gán dịch vụ'.'</span>',array('class'=>'btn btn-primary','title'=>'Quản lý biểu mẫu gán dịch vụ'));?>
                    <?php   if ($this->Employee->has_module_action_permission('tasks', 'update_task_template', $this->Employee->get_logged_in_employee_info()->person_id)) {
                    echo anchor("/tasks/template/",'<span class="">'.'Thiết kế lộ trình mẫu'.'</span>',array('class'=>'btn btn-primary','title'=>'Thiết kế DS mẫu')); }?>
                    <?php echo anchor("$controller_name/view/-1/",'<span class="">'.lang($controller_name.'_new').'</span>',array('class'=>'btn btn-primary','title'=>lang($controller_name.'_new')));?>
                        <?php if ($this->Employee->has_module_action_permission('customers', 'manage_quote', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
                     <?php echo anchor("customers/quotes_contract",'<span class="">Quản lý văn bản</span>',array('class'=>'btn btn-primary','title'=>lang($controller_name.'_new'))); }?>
                    <div class="piluku-dropdown">

                        
                     </div>
                </div>
			</div>
		</div>