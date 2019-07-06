
	<?php
	$current_employee_editing_self = $this->Employee->get_logged_in_employee_info()->person_id == $person_info->person_id;																																																											 
	foreach($all_modules->result() as $module)
	{					 
		if($module->sort != 1)			 
		{
			$checkbox_options = array(
			'name' => 'permissions[]',
			'id' => 'permissions'.$module->module_id,
			'value' => $module->module_id,
			'checked' => ($this->Employee->has_module_group_permission($module->module_id,$group_id))||($this->Employee->has_module_permission($module->module_id,$person_info->person_id, false)),
			'class' => 'module_checkboxes '
			); 
			if($this->Employee->has_module_group_permission($module->module_id, $group_id)){
				$checkbox_options['disabled'] = 'disabled';
			}
			if ($logged_in_employee_id != 1)
			{
				if(($current_employee_editing_self && $checkbox_options['checked']) || !$this->Employee->has_module_permission($module->module_id,$logged_in_employee_id, false))
				{
					$checkbox_options['disabled'] = 'disabled';
					
					//Only send permission if checked
					if ($checkbox_options['checked'] && $checkbox_options['disabled']!= 'disabled')
					{
						echo form_hidden('permissions[]', $module->module_id);
					}
				}
			}
			
	?>
	<li>	
	<?php echo form_checkbox($checkbox_options).'<label for="permissions'.$module->module_id.'"><span></span></label>'; ?>
	<span class="text-success"><?php echo lang('module_'.$module->module_id);?>:</span>
	<span class="text-warning"><?php echo lang('module_'.$module->module_id.'_desc');?></span>
		<ul class="list-unstyled list-permission-actions">
		<?php
		$module_actions = $this->Module_action->get_module_actions($module->module_id)->result();
		foreach($module_actions as $module_action)
		{
			$has_module_group_action_permission =$this->Employee->has_module_group_action_permission($module->module_id, $module_action->action_id, $group_id);
			$checkbox_options = array(
			'name' => 'permissions_actions[]',
			'data-module-checkbox-id' => 'permissions'.$module->module_id,
			'module' => $module->module_id,
			'action' => $module_action->action_id,
			'class' => 'module_action_checkboxes',
			'id' => 'permissions_actions'.$module_action->module_id."|".$module_action->action_id,
			'value' => $module_action->module_id."|".$module_action->action_id,
			'checked' => $has_module_group_action_permission||($this->Employee->has_module_action_permission($module->module_id, $module_action->action_id, $person_info->person_id, false)),
			
			);
			if($has_module_group_action_permission){
				$checkbox_options['disabled'] = 'disabled';
			} 		
			if ($logged_in_employee_id != 1)
			{
				if(($current_employee_editing_self && $checkbox_options['checked']) || (!$this->Employee->has_module_action_permission($module->module_id,$module_action->action_id,$logged_in_employee_id, true)))
				{
					$checkbox_options['disabled'] = 'disabled';
					
					//Only send permission if checked
					if ($checkbox_options['checked'] && $checkbox_options['disabled']!= 'disabled')
					{
						echo form_hidden('permissions_actions[]', $module_action->module_id."|".$module_action->action_id);
					}
				}							
			}
			?>
			<li>
			<?php echo form_checkbox($checkbox_options).'<label for="permissions_actions'.$module_action->module_id."|".$module_action->action_id.'"><span></span></label>'; ?>
			<span class="text-info"><?php echo lang($module_action->action_name_key);?></span>
			</li>
		<?php
		}
		?>
		</ul>
	</li>
	<?php
		}
	}
	?>
