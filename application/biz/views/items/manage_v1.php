<?php $this->load->view("partial/header"); ?>

<div class="manage_buttons">
	<div class="manage-row-options hidden" id="manage-row-options">
		<div class="email_buttons items">
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
				<?php //echo
					// anchor("$controller_name/bulk_edit/",
					// '<span class="">'.lang("items_bulk_edit").'</span>',
					// array('id'=>'bulk_edit','data-toggle'=>'modal','data-target'=>'#myModal',
					// 'class' => 'btn btn-primary btn-lg',
					// 'title'=>lang('items_edit_multiple_items'))); 
				?>
			<?php } ?>
			
			<?php 
				// echo anchor("$controller_name/generate_barcode_labels",'<span class="">'.lang("common_barcode_labels").'</span>',array('id'=>'generate_barcode_labels','class' => 'btn btn-primary btn-lg','title'=>lang('common_barcode_labels')));
				?>
			
			<?php 
				//echo anchor("$controller_name/generate_barcodes",'<span class="">'.lang("common_barcode_sheet").'</span>',array('id'=>'generate_barcodes','class' => 'btn btn-primary btn-lg','target' => '_blank','title'=>lang('common_barcode_sheet')));
			?>
			
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>	
				<a href="javascript:;" id="delete" class="btn btn-red red btn-lg">Xóa</a>
			<?php } ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="search search-items no-left-border">
				<ul class="list-inline">
					<li>
						<select name="s_category_id" class="form-control data-n9-s" id="s_category_id">
    					<?php foreach ($categories as $id => $category) { ?>
    						<option value="<?php echo $id; ?>"><?php echo $category; ?></option>
    					<?php } ?>
						</select>
					</li>
					<li>
						<input type="text" class="form-control" name ='search' id='search' value="<?php echo $keywords;?>" placeholder="Từ khóa ..."/>
					</li>
					<li class="hide">
						<input type="checkbox" id="chkbox_low_inventory">
						<label for="chkbox_low_inventory"><span></span></label>
						<label style="cursor: pointer;" for="chkbox_low_inventory">Dưới hạn mức tồn kho</label>
					</li>
				</ul>
			</div>
		</div>
		<?php  $this->load->view('items/partials/manage_danh_muc'); ?>
	</div>
</div>


<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku" id="list_view"></div>
	</div>
</div>

<script type="text/javascript">
var ITEM_LIST = {
	init: function() {
		var _data = {};
		_data['page'] = 1;
		_data['search'] = '';
		_data['category'] = 0;
		_data['low_inventory'] = 0;
		coreAjax.call(
			'<?php echo site_url("items/build_list_view");?>',
			_data,
			function(response)
			{
				if(response.success)
				{
					$('#list_view').html(response.html);
				}
			}
		);
		
		ITEM_LIST.typingSearchQuery();
		ITEM_LIST.changeCategory();
		ITEM_LIST.checkOnLowInventory();
	},
	typingSearchQuery: function()
	{
		$('#search').change(function(e) {
			var _data = {};
			_data['page'] = 1;
			_data['search'] = $('#search').val();
			_data['category'] = $('#s_category_id').val();
			_data['low_inventory'] = $('#chkbox_low_inventory').is(':checked') ? 1 : 0;
			coreAjax.call(
				'<?php echo site_url("items/build_list_view");?>',
				_data,
				function(response)
				{
					if(response.success)
					{
						$('#list_view').html(response.html);
					}
				}
			);
		});
	},
	checkOnLowInventory: function()
	{
		$('#chkbox_low_inventory').change(function(e) {
			var _data = {};
			_data['page'] = 1;
			_data['search'] = $('#search').val();
			_data['category'] = $('#s_category_id').val();
			_data['low_inventory'] = $('#chkbox_low_inventory').is(':checked') ? 1 : 0;
			coreAjax.call(
				'<?php echo site_url("items/build_list_view");?>',
				_data,
				function(response)
				{
					if(response.success)
					{
						$('#list_view').html(response.html);
					}
				}
			);
		});
	},
	changeCategory: function()
	{
		$('#s_category_id').change(function(e) {
			var _data = {};
			_data['page'] = 1;
			_data['search'] = $('#search').val();
			_data['category'] = $('#s_category_id').val();
			_data['low_inventory'] = $('#chkbox_low_inventory').is(':checked') ? 1 : 0;
			coreAjax.call(
				'<?php echo site_url("items/build_list_view");?>',
				_data,
				function(response)
				{
					if(response.success)
					{
						$('#list_view').html(response.html);
					}
				}
			);
		});
	},
	clickEventOnNameCell: function(element)
	{
		var viewUrl = $(element).data('item-url');
		coreAjax.call(
			viewUrl,
			{},
			function(response)
			{
				$('#itemsModal').remove();
				$('body').append(response);
				$('#itemsModal').modal('show');
			}
		);
	},
	clickEventOnQtyCell: function(element)
	{
		var _data = {};
		_data['item_id'] = $(element).closest('tr').find('input[type="checkbox"]').val();
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
	},
	getSelectedItems: function() {
		var cookie = $.cookie('LIST_VIEW_COOKIE');
		if (typeof cookie != 'undefined') {
			cookieObj = JSON.parse(cookie);
			return cookieObj['ids'];
		}
		return [];
	}
}

$( document ).ready(function() {
	// TODO
	$.removeCookie('LIST_VIEW_COOKIE');
	ITEM_LIST.init();

	$('#generate_barcodes').click(function()
	{
		var selected = ITEM_LIST.getSelectedItems();
		if (selected.length == 0)
		{
			bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
			return false;
		}

		$(this).attr('href','<?php echo site_url("items/generate_barcodes");?>/'+selected.join('~'));
	});

	$('#generate_barcode_labels').click(function()
	{
		var selected = ITEM_LIST.getSelectedItems();
		if (selected.length == 0)
		{
			bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
			return false;
		}

		$(this).attr('href','<?php echo site_url("items/generate_barcode_labels");?>/'+selected.join('~'));
	});
	
	$('#manage-row-options #delete').click(function(event)
	{
		event.preventDefault();
		var selected = ITEM_LIST.getSelectedItems();
		if (selected.length >0)
		{
			bootbox.confirm(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>, function(result)
			{
				if (result)
				{
					var _data = {};
					_data['cid'] = selected;
					coreAjax.call(
						'<?php echo site_url("items/delete");?>',
						_data,
						function(response)
						{
							if(response.flag == 'true')
							{
								show_feedback('success', response.msg, COMMON_SUCCESS);
								ITEM_LIST.init();
								LIST_VIEW.removeSelectedItems(selected);
								if (LIST_VIEW.showManageRowOptions()) {
									$('#manage-row-options').removeClass('hidden');
								} else {
									$('#manage-row-options').addClass('hidden');
								}
							} else {
								show_feedback('error', response.msg, COMMON_ERROR);
							}
						}
					);
				}
			});
		}
	});
});
</script>


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
