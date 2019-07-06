<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/items.js'; ?>"></script>
<?php
	$key_filter        = 'count_low_inventory';
	$filter            = isset($_SESSION[$key_filter])?$_SESSION[$key_filter]:'';
	$keywords          = !empty($filter['keywords'])?$filter['keywords']:'';
// $items_arena       = !empty($filter['items_arena'])?$filter['keywords']:'';
	$category_id       = !empty($filter['category_id'])?$filter['category_id']:'';
	$category_child    = !empty($filter['category_child'])?$filter['category_child']:'';
	
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
	
	$link_list     = base_url() . 'items';
	$link_birthday = base_url() . 'items/low_inventory';
	
	// if(!empty($_SESSION['items_tmp_ids']))
	// $css_style = ' style="display: inline-block"';
	// else
	// $css_style = ' style="display: none"';
?>
<script type="text/javascript">
	$(document).ready(function()
	{		
		enable_select_all_item();
		enable_checkboxes_item();
		enable_row_selection_item();
		enable_delete_item(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
		enable_cleanup_item(<?php echo json_encode(lang("items_confirm_cleanup"));?>);

		$('#generate_barcodes').click(function()
		{
			var selected = get_selected_values();
			if (selected.length == 0)
			{
				bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
				return false;
			}

			$(this).attr('href','<?php echo site_url("items/generate_barcodes");?>/'+selected.join('~'));
		});

		$('#generate_barcode_labels').click(function()
		{
			var selected = get_selected_values();
			if (selected.length == 0)
			{
				bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
				return false;
			}

			$(this).attr('href','<?php echo site_url("items/generate_barcode_labels");?>/'+selected.join('~'));
		});

		<?php if ($this->session->flashdata('manage_success_message')) { ?>
			show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);
			<?php } ?>
		});

function post_bulk_form_submit(response)
{
	window.location.reload();
}

function select_inv()
{	
	bootbox.confirm(<?php echo json_encode(lang('items_select_all_message')); ?>, function(result)
	{
		if (result)
		{
			$('#select_inventory').val(1);
			$('#selectall').css('display','none');
			$('#selectnone').css('display','block');
			$.post('<?php echo site_url("items/select_inventory");?>', {select_inventory: $('#select_inventory').val()});
		}
	});
}
function select_inv_none()
{
	$('#select_inventory').val(0);
	$('#selectnone').css('display','none');
	$('#selectall').css('display','block');
	$.post('<?php echo site_url("items/clear_select_inventory");?>', {select_inventory: $('#select_inventory').val()});	
}

$.post('<?php echo site_url("items/clear_select_inventory");?>', {select_inventory: $('#select_inventory').val()});	

</script>
<div class="manage_buttons">
	<div class="manage-row-options hidden">
		<div class="email_buttons items">
			<?php echo anchor("$controller_name/bulk_edit/",'<span class="">'.lang("items_bulk_edit").'</span>',array('id'=>'bulk_edit','data-toggle'=>'modal','data-target'=>'#myModal','class' => 'btn btn-primary btn-lg  disabled','title'=>lang('items_edit_multiple_items'))); ?>
			
			<?php echo anchor("$controller_name/generate_barcode_labels",'<span class="">'.lang("common_barcode_labels").'</span>',array('id'=>'generate_barcode_labels','class' => 'btn btn-primary btn-lg  disabled','title'=>lang('common_barcode_labels')));?>
			
			<?php echo anchor("$controller_name/generate_barcodes",'<span class="">'.lang("common_barcode_sheet").'</span>',array('id'=>'generate_barcodes','class' => 'btn btn-primary btn-lg  disabled','target' => '_blank','title'=>lang('common_barcode_sheet')));?>
			
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
				<?php echo anchor("$controller_name/delete",'<span class="">'.lang("common_delete").'</span>',array('id'=>'delete','class'=>'btn btn-red btn-lg disabled','title'=>lang("common_delete")));?>
			<?php } ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-8">
			<div class="search search-items no-left-border">
				<ul class="list-inline">
					<li>
						<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
						<input type="text" class="form-control" name ='search' id='search' value="<?php echo $keywords;?>" placeholder="Tìm kiếm kho"/>
						<input type="hidden" name="s_keywords" class="data-n9-s" data-table="low_inventory_list" value="<?php echo $keywords;?>" />
					</li>		
					
					<!--test -->
					<li>
						<select name="s_category_id" class="form-control data-n9-s" data-table="low_inventory_list" id="s_category_id">
							<option value="-1"><?php echo lang('items_filter_categorie'); ?></option>
							<?php
								if(!empty($category)) {
									foreach($category as $val) {
									?>
									<option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
									<?php
									}
								}
							?>
						</select>
					</li>
					<!--danh mục con -->
					<li>
						<select name="s_category_child" class="form-control data-n9-s" data-table="low_inventory_list" id="s_category_child">
							<option value="-1"><?php echo lang('items_filter_subcategorie'); ?></option>
							<?php
								
								if(!empty($category_child)) {
									foreach($category_child as $val) {
									?>
									<option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
									<?php
									}
								}
							?>
						</select>
					</li>
				</ul>
			</div>
		</div>
		<?php $this->load->view('items/partials/manage_danh_muc'); ?>
	</div>
</div>


<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title space_title">
					<span class="title" id="all_items"><a href="<?php echo $link_list; ?>" >Danh sách kho</a></span>
					<span class="badge bg-primary tip-left" id="count_all_items">0</span>
					
					<span class="title first active" id="low_inventory">Sản phẩm dưới hạn mức tồn kho</span>
					<span class="badge bg-primary tip-left" id="count_low_inventory">0</span>
					
					<i class="fa fa-spinner fa-spin loading" id="customer_list_loading" style="display: none;"></i>
				</h3>
			</div>
			
			<table id="tbl_items" class="tablesorter table table-hover data-n9-table"  data-callback="true" data-table="low_inventory_list" data-url="<?php echo base_url() . 'items/low_inventory_list/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
				<thead>
					<tr>
						<th class="leftmost" style="width: 20px;">
							<input type="checkbox"><label for="select_all" class="check_tatca" id="select_all"><span></span></label>
						</th>
						<th class="hr-lbl" data-field="item_id" style="text-align: left;"<?php if($field_sort == 'item_id') echo ' class="header '.$class_sort.'"'; ?>>Mã sản phẩm</th>
						<th class="hr-lbl" data-field="item_number" style="text-align: left;"<?php if($field_sort == 'item_number') echo ' class="text-left '.$class_sort.'"'; ?>>Mã vạch</th>
						<th class="hr-lbl" data-field="name" style="text-align: left;"<?php if($field_sort == 'name') echo ' class="header '.$class_sort.'"'; ?>>Tên</th>
						<th class="hr-lbl" data-field="category" style="text-align: left;"<?php if($field_sort == 'category') echo ' class="header '.$class_sort.'"'; ?>>Danh mục</th>
						<th class="hr-lbl" data-field="size" style="text-align: left;"<?php if($field_sort == 'size') echo ' class="header '.$class_sort.'"'; ?>>Kích thước</th>
						<th class="hr-lbl" data-field="cost_price" style="text-align: left;"<?php if($field_sort == 'cost_price') echo ' class="header '.$class_sort.'"'; ?>>Giá vốn</th>
						<th class="hr-lbl" data-field="unit_price" style="text-align: left;"<?php if($field_sort == 'unit_price') echo ' class="header '.$class_sort.'"'; ?>>Giá bán</th>
						<th class="hr-lbl" data-field="items_quantity" style="text-align: left;"<?php if($field_sort == 'items_quantity') echo ' class="header '.$class_sort.'"'; ?>>Số lượng</th>
						<th class="hr-lbl" data-field="items_total_quantity" style="text-align: left;"<?php if($field_sort == 'items_total_quantity') echo ' class="header '.$class_sort.'"'; ?>>Tổng số lượng</th>
						<th class="hr-lbl" data-field="inventory" style="text-align: left;"<?php if($field_sort == 'inventory') echo ' class="header '.$class_sort.'"'; ?>>Hàng tồn kho</th>
						<th class="hr-lbl" data-field="clone" style="text-align: left;"<?php if($field_sort == 'clone') echo ' class="header '.$class_sort.'"'; ?>>Tạo bản sao</th>
						<th class="hr-lbl" data-field="edit" style="text-align: left;"<?php if($field_sort == 'edit') echo ' class="header '.$class_sort.'"'; ?>>Sửa</th>
						<th class="hr-lbl" style="width: 50px;">&nbsp;</th>
						<th class="hr-lbl" style="width: 50px;">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
			
		</div>
		<div class="text-center data-n9-pagination" data-table="low_inventory_list"></div>
	</div>
</div>


<script type="text/javascript">
	var ITEM_LIST = {
	
	clickEventOnQtyCell: function(element)
	{
		var _data = {};
		_data['item_id'] = $(element).closest('tr').find('input[type="checkbox"]').val();
		console.log(_data);
		coreAjax.call(
			'<?php echo site_url("items/qty_location");?>',
			_data,
			function(response)
			{
				if(response.success)
				{
					$('#qtyLocationModal').remove();
					$('body').append(response.html);
					$('#qtyLocationModal').modal('show');
				}
			}
		);
	}
}

	$( document ).ready(function() {
		load_list_item('low_inventory_list');
		
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
			load_list_item('low_inventory_list', 1);
		}
		
		// $('body').on('change','#s_items_arena',function(){
			// load_list_item('low_inventory_list', 1);
		// });
		$('body').on('change','#s_category_id',function(){
			load_list_item('low_inventory_list', 1);
		});
		$('body').on('change','#s_category_child',function(){
			load_list_item('low_inventory_list', 1);
		});
	});
	
	
	function n9_grid_callback(data_table,result) {
		$('#count_all_items').text(result.count_all_items);
		$('#count_low_inventory').text(result.count);                      
	}
	
	
</script>
<style>
	.data-n9-table th {
	text-align: center;
	}
	
	.data-n9-table th[data-field] {
	cursor: pointer;
	}
	
	.data-n9-table td.center {
	text-align: center;
	}
	
	.panel-piluku > .panel-heading h3 {
	position: relative;
	}
	
	.loading {
	position: absolute;
	bottom: -91px;
	left: -9px;
	}
</style>
</div>

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
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/quick-nav.js'; ?>"></script>

<?php $this->load->view("partial/footer"); ?>
