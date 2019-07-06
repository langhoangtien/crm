<?php $this->load->view("partial/header"); 
$this->load->helper('demo');
?>
<script type="text/javascript">
$(document).ready(function()
{
	var table_columns = ["","location_id","name",'','phone','email',''];
	
	 enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>",table_columns, <?php echo $per_page; ?>, <?php echo json_encode($order_col);?>, <?php echo json_encode($order_dir); ?>);
	 enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
    enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
	 <?php if ($this->session->flashdata('manage_success_message')) { ?>
		show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);
	 <?php } ?>


	 $('.tree').treegrid({
		 treeColumn: 1
	 });

	$('table.locations-level #select_all').unbind('click').bind('click', function(){
		if ($(this).is(':checked')) {
			$('table.locations-level tbody input[type="checkbox"]').prop('checked', true);
			$('a#delete_locations').removeClass('hidden');
		} else {
			$('table.locations-level tbody input[type="checkbox"]').prop('checked', false);
			$('a#delete_locations').addClass('hidden');
		}
	});
	$('table.locations-level tbody input[type="checkbox"]').unbind('click').bind('click', function(){
		if ($('table.locations-level tbody input[type="checkbox"]:checked').length) {
			$('a#delete_locations').removeClass('hidden');
		} else {
			$('a#delete_locations').addClass('hidden');
		}
	});


	$('a#delete_locations').click(function()
	{
		bootbox.confirm(<?php echo json_encode(lang('locations_confirm_delete')); ?>, function(result)
		{
			if (result)
			{
				var _data = {};
				_data['ids'] = [];
				$('table.locations-level tbody input[type="checkbox"]:checked').each(function(){
					_data['ids'].push($(this).data('id'))
				});
				coreAjax.call(
					'<?php echo site_url("locations/delete");?>',
					_data,
					function(response)
					{
						if(response.success)
						{
							location.reload();
						}
					}
				);
			}
		});
		
		return false;
	})
});

</script>
<div class="manage_buttons" style="padding-bottom: 20px;">
<div class="manage-row-options hidden">
	<div class="email_buttons text-center">
		
		<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>					
			<?php echo 
				anchor("$controller_name/delete",
				'<span class="">'.lang('common_delete').'</span>',
				array('id'=>'delete', 
					'class'=>'btn btn-red btn-lg tip-bottom disabled','title'=>lang("common_delete"))); 
			?>
		<?php } ?>
	</div>
</div>

	<div class="row">
		<div class="col-md-4" style="display: none">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off')); ?>
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
				</div>
				<div class="clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
					<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			<?php echo form_close() ?>
			
		</div>
		<div class="col-md-12">
		<?php 
		$countAll = $this->Location->count_all();
		if ($countAll < MAX_LOCATION) {
		?>
			<div class="buttons-list">
				<div class="pull-right-btn">
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>	
        				<a id="delete_locations" href="javascript:;" data-table="items_list" data-url="<?php echo base_url() . 'location/delete'; ?>" class="btn btn-red red btn-lg hidden">Xóa</a>
        			<?php } ?>
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
						<?php echo 
						anchor("$controller_name/view/-1/",
						'<span class="">'.lang($controller_name.'_new').'</span>',
						array('class'=>'btn btn-primary', 
							'title'=>lang($controller_name.'_new'),
							'id' => 'new_location_btn'));
						?>
					<?php } ?>
				</div>
			</div>
			<?php } else { ?>
				<div class="col-md-12" style="margin-top: 30px;">
					<strong style="float: right;"><a href="/#" target="_blank"><?php echo lang('locations_adding_location_requires_addtional_license'); ?></a></strong>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo lang('common_list_of').' '.lang('module_'.$controller_name); ?>
					<span title="<?php echo $total_rows; ?> total <?php echo $controller_name?>" class="badge bg-primary tip-left"><?php echo $total_rows; ?></span>
					<span class="panel-options custom">
						<?php if($pagination) {  ?>
							<div class="pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_top" >
								<?php echo $pagination;?>
							</div>
						<?php }  ?>
					</span>

				</h3>
			</div>
			<div class="panel-body nopadding table_holder table-responsive" >
				<table class="tree table tablesorter table-hover locations-level">
					<thead>
						<tr>
							<th>
								<input type="checkbox" id="select_all"><label for="select_all"><span></span></label>
							</th>
							<th>Tên</th>
							<th>Địa chỉ</th>
							<th>Số điện thoại</th>
							<th>Email</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($locations as $i => $location) { 
						$treegridParent = !empty($location['parent_id']) ? 'treegrid-parent-' . $location['parent_id'] : '';
						?>
    					<tr class="treegrid-<?php echo $location['location_id']; ?> <?php echo $treegridParent; ?>">
    						<td>
    							<input type="checkbox" data-id="<?php echo $location['location_id'];?>" id="select_<?php echo $location['location_id'];?>"><label for="select_<?php echo $location['location_id'];?>"><span></span></label>
    						</td>
    						<td><?php echo $location['name']; ?></td>
    						<td><?php echo $location['address']; ?></td>
    						<td><?php echo $location['phone']; ?></td>
    						<td><?php echo $location['email']; ?></td>
    						<td>
    							<?php echo anchor($controller_name. '/view/'. $location['location_id'].'/2', lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update')));?>
    						</td>
    					</tr>
    					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php if($pagination) {  ?>
<div class="row pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_bottom" >
	<?php echo $pagination;?>
</div>
<?php } ?>
</div>
<?php if (!is_on_demo_host()) { ?>
	<script type="text/javascript">
	
	</script>	
<?php } ?>
<?php
if(!empty($_SESSION['notice'])) {
    $notice = $_SESSION['notice'];
    unset($_SESSION['notice']);
    ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            toastr.success('<?php echo $notice; ?>', 'Thông báo');
        });
    </script>
<?php
}
?>
<?php $this->load->view("partial/footer"); ?>