<div class="panel-heading">
	<h3 class="panel-title space_title">
		<span class="title first active" id="all_items">Danh mục dịch vụ</span>
		<span class="badge bg-primary tip-left" id="count_all_items"><?php echo $totalRecords; ?></span>
	</h3>
</div>
<div class="table-responsive">
	<?php  
	// var_dump($this->Service->get_document(null,'ĐKCTĐC')['document']); 
	// var_dump(explode(',',$this->Service->get_document(null,'ĐKCTĐC')['document']));
	// echo strlen($this->Service->get_document(null,'ĐKCTĐCC')['document']) ;
	?>
	<table id="tbl_items" class="table table-hover tablesorter">
		<thead>
			<tr>
				<th class="leftmost" style="width: 20px;">
					<input type="checkbox" name="select_all" value="select_all" id="select_all">
					<label for="select_all"><span></span></label>
				</th>
				<?php foreach ($headers as $header) { 
					$sorted = !empty($header['field']) && $header['field'] == $sortBy ? true : false;
					?>
					<th class="<?php echo !empty($header['class']) ? $header['class'] : '';?>" data-sort-by="<?php echo !empty($orderBy) ? $orderBy : '';?>" data-sorted="<?php echo !empty($sorted) ? 1 : 0;?>" data-sortable="<?php echo !empty($header['sortable']) ? 1 : 0;?>" data-field="<?php echo !empty($header['field']) ? $header['field'] : '';?>" style="<?php echo !empty($header['style']) ? $header['style'] : '';?>"><?php echo !empty($header['name']) ? $header['name'] : '';?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php  
		// echo "<pre>";
		// print_r($records); die();
			?>

			<?php
			$stt=1;
			 foreach ($records as $record) {

				if(!empty($record['image_id']))
					$link_image = base_url() . 'app_files/view/' . $record['image_id'];
				else
					$link_image = base_url() . 'assets/assets/images/items-default.jpg';
				?>
				<tr>
					<td class="cb">
						<input type="checkbox" name="ids[]" value="<?php echo $record['item_id'];?>" id="item_<?php echo $record['item_id'];?>">
						<label for="item_<?php echo $record['item_id'];?>"><span></span></label>
					</td>
					<td class="text-left"><?php echo $stt;?></td>
					<td class="text-left"><?php echo $record['product_id'];?></td>
					<td class="text-left">
						<!-- 	<a class="item-name" onclick="ITEM_LIST.clickEventOnNameCell(this)" data-item-url="<?php echo base_url(); ?>home/view_item_modal/<?php echo $record['item_id'];?>/2"><?php echo $record['name'];?></a> -->
						<?php echo $record['name'];?>
					</td>
					<td class="text-left"><?php echo $record['category'];?></td>

					<td class="text-left"><?php echo ($record['unit_price_interval']);?></td>
					<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id)):?>
						<td class="text-left"><?php echo ($record['cost_price_interval']); ?></td>
					<?php endif;?>

					<td class="text-left not-selectable">
						<?php 
						$doc = $this->Service->get_document(null,$record['product_id'])['document'];
						$person_id = $this->Employee->get_logged_in_employee_info()->person_id;

						if (strlen($doc)>0) {
							$arr_doc = explode(',',$doc);
							$quotes_contract = $this->Customer->get_list_quotes_contract($arr_doc);
							foreach ($quotes_contract as $key => $vl) {
								echo "- <a href='".base_url()."sales/export_constract/?select_quote=$vl[id_quotes_contract]&ds_id=0'>".$vl['title_quotes_contract']."</a>";
								echo "<br>";
							}
						}

						// foreach ($record['document'] as $k => $val) {
						// 	echo "<a href='/sales/do_make_quotes_contract_type/?sale=1&quote=$val[id_quotes_contract]&ds_id=0'>".$val['title_quotes_contract']."</a>";
						// 	echo "<br>";
						// }
						?>
					</td>
					<!--			<td class="text-left not-selectable"><a href="--><?php //echo base_url(); ?><!--items/clone_item/--><?php //echo $record['item_id'];?><!--/2" class="update-person" title="Clone">Clone</a></td>-->
					<td class="text-left not-selectable"><a href="<?php echo base_url(); ?>items/view/<?php echo $record['item_id'];?>/2" class="update-person" title="Sửa">Sửa</a></td>
					<!--			<td class="text-left not-selectable"><a href="--><?php //echo $link_image; ?><!--" class="rollover"><img src="--><?php //echo $link_image; ?><!--" alt="--><?php //echo $record['name']; ?><!--" class="img-polaroid avatar" width="45"></a></td>-->
				</tr>
			<?php  $stt++;} ?>
		</tbody>
	</table>
</div>
<?php if(!empty($records)) {?>
	<div style="text-align: center;">
		<ul class="pagination">
			<li data-page="1">
				<a aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<?php foreach ($pagination['displayed_pages'] as $page) {?>
				<li data-page="<?php echo $page; ?>" class="<?php echo $page == $pagination['current_page'] ? 'active' : '';?>"><a><?php echo $page; ?></a></li>
			<?php } ?>
			<li data-page="<?php echo $pagination['total_page']; ?>">
				<a aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</div>
<?php } else { ?>
	<div style="text-align: center;
	padding-top: 20px;
	font-size: 18px;
	font-style: italic;
	color: red;">
	<p>Không tìm thấy sản phẩm nào!</p>
</div>
<?php } ?>
<script type="text/javascript">
	var LIST_VIEW = {
		init: function() {
			LIST_VIEW.clickEventOnPaging();
			LIST_VIEW.clickEventOnCheckbox();
			LIST_VIEW.clickEventOnCheckboxAll();
			LIST_VIEW.sortByCol();

			var cookie = $.cookie('LIST_VIEW_COOKIE');
			if (typeof cookie != 'undefined') {
				cookieObj = JSON.parse(cookie);
				$('#tbl_items tbody input[type="checkbox"]').each(function(){
					if (cookieObj['ids'].indexOf($(this).val()) >= 0) {
						$(this).prop('checked', true);
					}
				});
			}

			$('#tbl_items thead th').each(function()
			{
				if ($(this).data('sortable') == 1) {
					if ($(this).data('sorted') == 1) {
						if ($(this).data('sort-by') == 'desc') {
							$(this).addClass('headerSortUp');
						} else {
							$(this).addClass('headerSortDown');
						}
					} else {
						$(this).addClass('headerSort');
					}
				}
			});
		},
		request: function(params) {
			coreAjax.call(
				'<?php echo site_url("items/build_list_view");?>',
				params,
				function(response)
				{
					if(response.success)
					{
						$('#list_view').html(response.html);
						LIST_VIEW.init();
					}
				}
				);
		},
		sortByCol: function() {
			$('#tbl_items thead th[data-sortable="1"]').unbind('click').bind('click', function(){
				var sortByField = $(this).data('field');
				var orderBy = 'desc';
				if ($(this).hasClass('headerSortDown')) {
					orderBy = 'desc';
				} else if ($(this).hasClass('headerSortUp')) {
					orderBy = 'asc';
				}
				var _data = {};
				_data['page'] = 1;
				_data['search'] = $('#search').val();
				_data['category'] = $('#s_category_id').val();
				_data['low_inventory'] = $('#chkbox_low_inventory').is(':checked') ? 1 : 0;
				_data['sort_by'] = sortByField;
				_data['order_by'] = orderBy;
				LIST_VIEW.request(_data);
			});
		},
		addSelectedItems: function(selectedIds) {
			var cookie = $.cookie('LIST_VIEW_COOKIE');
			cookieObj = {};
			if (typeof cookie == 'undefined') {
				cookieObj = {};
				cookieObj['ids'] = [];
			} else {
				cookieObj = JSON.parse(cookie);
			}
			for(var i = 0; i < selectedIds.length; i++ ) {
				if (cookieObj['ids'].indexOf(selectedIds[i]) == -1 ) {
					cookieObj['ids'].push(selectedIds[i]);
				}
			}
			$.cookie('LIST_VIEW_COOKIE', JSON.stringify(cookieObj));
		},
		removeSelectedItems: function(selectedIds) {
			var cookie = $.cookie('LIST_VIEW_COOKIE');
			cookieObj = {};
			if (typeof cookie == 'undefined') {
				cookieObj = {};
				cookieObj['ids'] = [];
			} else {
				cookieObj = JSON.parse(cookie);
			}

			var oldIds = cookieObj['ids'];
			cookieObj['ids'] = [];
			for(var i = 0; i < oldIds.length; i++ ) {
				if (selectedIds.indexOf(oldIds[i]) == -1) {
					cookieObj['ids'].push(oldIds[i]);
				}
			}
			$.cookie('LIST_VIEW_COOKIE', JSON.stringify(cookieObj));
		},
		clickEventOnPaging: function(element)
		{
			$('ul.pagination li a').unbind('click').bind('click', function(){
				var _data = {};
				_data['page'] = $(this).closest('li').data('page');
				_data['search'] = $('#search').val();
				_data['category'] = $('#s_category_id').val();
				LIST_VIEW.request(_data);
			});
		},
		clickEventOnCheckbox: function(element) {
			$('#tbl_items tbody input[type="checkbox"]').unbind('change').bind('change', function(){
				var showManageRowOptions = false;
				if ($(this).is(':checked')) {
					LIST_VIEW.addSelectedItems([$(this).val()]);
				} else {
					LIST_VIEW.removeSelectedItems([$(this).val()]);
				}

				if (LIST_VIEW.showManageRowOptions()) {
					$('#manage-row-options').removeClass('hidden');
				} else {
					$('#manage-row-options').addClass('hidden');
				}
			});
		},
		clickEventOnCheckboxAll: function(element) {
			$('#tbl_items input[type="checkbox"]#select_all').unbind('change').bind('change', function(){
				var selected_values = new Array();
				$('#tbl_items tbody input[type="checkbox"]').each(function()
				{
					selected_values.push($(this).val());
				});

				var checked = false;
				if ($(this).is(':checked')) {
					checked = true;
					LIST_VIEW.addSelectedItems(selected_values);
				} else {
					LIST_VIEW.removeSelectedItems(selected_values);
				}

				$('#tbl_items tbody input[type="checkbox"]').each(function(){
					$(this).prop('checked', checked);
				});

				if (LIST_VIEW.showManageRowOptions()) {
					$('#manage-row-options').removeClass('hidden');
				} else {
					$('#manage-row-options').addClass('hidden');
				}

			});
		},
		showManageRowOptions: function() {
			var cookie = $.cookie('LIST_VIEW_COOKIE');
			cookieObj = {};
			if (typeof cookie == 'undefined') {
				return false;
			} else {
				cookieObj = JSON.parse(cookie);
				return cookieObj['ids'].length;
			}
		}
	}

	$( document ).ready(function() {
		LIST_VIEW.init();
	});
</script>