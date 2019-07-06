<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/items.js'; ?>"></script>
<?php
	$key_filter        = 'count_sale_expense';
	$filter            = isset($_SESSION[$key_filter])?$_SESSION[$key_filter]:'';
	$keywords          = !empty($filter['keywords'])?$filter['keywords']:'';
	
	if(!isset($filter['col'])) {
		$field_sort = 'id';
		$class_sort = 'headerSortDown';
		}else {
		$field_sort = $filter['col'];
		if($filter['order'] == 'ASC')
		$class_sort = 'headerSortUp';
		else
		$class_sort = 'headerSortDown';
	}
	
	$link_list     			= base_url() . 'expenses';
	$link_sale_expense 		= base_url() . 'expenses/sale_expense';
	$link_receiving_expense = base_url() . 'expenses/receiving_expense';
	
?>
<script type="text/javascript">
	
</script>


<div class="manage_buttons">
	<div class="manage-row-options hidden" data-table="expenses_list">
		<div class="email_buttons items">
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>	
				<a href="javascript:;" data-table="expenses_list" data-url="<?php echo base_url() . 'expenses/delete_item'; ?>" class="btn btn-red red btn-lg"><?php echo lang('expenses_delete'); ?></a>
			<?php } ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="search search-items no-left-border">
				<ul class="list-inline">
					<li>
						<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
						<input type="text" class="form-control" name ='search' id='search' value="" placeholder="<?php echo lang('expenses_search'); ?>"/>
						<input type="hidden" name="s_keywords" class="data-n9-s" data-table="expenses_list" value=""/>
					</li>		
					
					<!--test -->
				</ul>
			</div>
		</div>
		<div class="col-md-4">
			<div class="buttons-list">
				<div class="pull-right-btn">
					
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
						
						<?php echo anchor("$controller_name/view/-1/",'<span class="">'.lang($controller_name.'_new').'</span>',
						array('class'=>'btn btn-primary btn-lg', 'title'=>lang($controller_name.'_new')));?> 
					<?php } ?>
					
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>	
						
						<!-- <a href="javascript:;" data-table="expenses_list" data-url="<?php //echo base_url() . 'expenses/delete_item'; ?>" class="btn btn-red red btn-lg"><?php //echo lang('expenses_delete'); ?></a> -->
						
					<?php } ?>
					
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title space_title">
					<span class="title" id="all_items"><a href="<?php echo $link_list; ?>">Chi phí chung</a></span>
					<span class="badge bg-primary tip-left" id="count_in_expense">0</span>
					
					<span class="title first active" id="sale_expense"> Doanh thu chia cho phòng ban khác </span>
					<span class="badge bg-primary tip-left" id="count_sale_expense">0</span>
					
					<!-- <span class="title" id="receiving_expense"><a href="<?php //echo $link_receiving_expense ?>"><?php //echo lang('expenses_import_costs'); ?></a></span>
					<span class="badge bg-primary tip-left" id="count_receiving_expense">0</span> -->
					
					<i class="fa fa-spinner fa-spin fa-3x fa-fw loading" id="items_list_loading" style="display: none;"></i>
				</h3>
			</div>
			
			<table id="tbl_items" class="tablesorter table table-hover data-n9-table "  data-callback="true" data-table="expenses_list" data-url="<?php echo base_url() . 'expenses/sale_store/' ?>" data-currentPage="<?php echo $current_page; ?>">
				<thead>
					<tr>
						<th class="leftmost" style="width: 20px;">
							<input type="checkbox"><label for="select_all" class="check_tatca" id="select_all"><span></span></label>
						</th>
						<th class="hr-lbl" data-field="id" style="text-align: left;"><?php echo lang('expenses_code'); ?></th>
						<th class="hr-lbl" data-field="id" style="text-align: left; "<?php if($field_sort == 'sale_id') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('Số hiệu hợp đồng') ?></th>
						<!-- <th class="hr-lbl" data-field="expense_type" style="text-align: left;"<?php if($field_sort == 'expense_type') echo ' class="text-left '.$class_sort.'"'; ?>><?php echo lang('expenses_type'); ?></th> -->
						<th class="hr-lbl" data-field="expense_description" style="text-align: left;width: 15%;"<?php if($field_sort == 'expense_description') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('Mô tả'); ?></th>
						<!-- <th class="hr-lbl" data-field="category" style="text-align: left;"<?php if($field_sort == 'category') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('expenses_category'); ?></th> -->
						<th class="hr-lbl" data-field="expense_date" style="text-align: left;"<?php if($field_sort == 'expense_date') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('expenses_date'); ?></th>
						<th class="hr-lbl" data-field="expense_amount" style="text-align: left;"<?php if($field_sort == 'expense_amount') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('Chi phí'); ?></th>
						<!-- <th class="hr-lbl" data-field="expense_tax" style="text-align: left;"<?php //if($field_sort == 'expense_tax') echo ' class="header '.$class_sort.'"'; ?>><?php //echo lang('VAT'); ?></th> -->
						<th class="hr-lbl" data-field="cus_name" style="text-align: left;"<?php if($field_sort == 'cus_name') echo ' class="text-left '.$class_sort.'"'; ?>><?php echo lang('expenses_customer'); ?></th>
						<th class="hr-lbl" data-field="employee_recv" style="text-align: left;"<?php if($field_sort == 'employee_recv') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('expenses_executor'); ?></th>
						<th class="hr-lbl" data-field="employee_appr" style="text-align: left;"<?php if($field_sort == 'employee_appr') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('expenses_approver'); ?></th>
						<!-- <th class="hr-lbl" style="text-align: left;"><?php //echo lang('expenses_print'); ?> / <?php //echo lang('expenses_excel_output'); ?></th> -->
						<th class="hr-lbl" style="text-align: left;"><?php echo lang('Cập nhật'); ?></th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
			
		</div>
		<div class="text-center data-n9-pagination" data-table="expenses_list"></div>
	</div>
</div>	
<script type="text/javascript" >
	$( document ).ready(function() {
		load_list_expenses('expenses_list');
		
		// search
		var typingTimer;
		$('body').on('keyup','#search',function(){
			var s_keywords = $('[name="s_keywords"]');
			s_keywords.val($(this).val());
			clearTimeout(typingTimer);
			typingTimer = setTimeout(startSearch, 500);
		});
		
		$('body').on('keydown','#search',function(){
			clearTimeout(typingTimer);
		});
		
		function startSearch () {
			load_list_expenses('expenses_list', 1);
		}
	});
	
	function n9_grid_callback(data_table,result) {
		$('#count_in_expense').text(result.count_in_expense);
		$('#count_sale_expense').text(result.count);    
		$('#count_receiving_expense').text(result.count_receiving_expense);                   
	}
</script>

<?php $this->load->view("partial/footer"); ?>
