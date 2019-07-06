<div class="panel-heading">
	<h3 class="panel-title space_title">
		<span id="all_items"> Số lượng nhân viên </span>
		<span class="badge bg-primary tip-left"><?php echo $totalRecords; ?></span>
	</h3>
</div>
<div class="panel-body nopadding table-responsive">
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
    		<?php foreach ($records as $record) {?>
    		<tr data-id="<?php echo $record['person_id'];?>">
    			<td class="cb">
    				<input type="checkbox" name="ids[]" value="<?php echo $record['person_id'];?>" id="item_<?php echo $record['person_id'];?>">
    				<label for="item_<?php echo $record['person_id'];?>"><span></span></label>
    			</td>
    			<td class="text-left"><span class="view-detail" style="cursor: pointer; color: #337ab7"><?php echo $record['first_name'] . ' ' . $record['last_name'];?></span></td>
    			<td class="text-left"><?php echo $record['email'];?></td>
    			<td class="text-left"><?php echo $record['phone_number'];?></td>
    			<td class="text-left not-selectable"><a href="<?php echo base_url(); ?>employees/clone_employee/<?php echo $record['person_id'];?>/1" class="update-person" title="Clone">Clone</a></td>
    			<td class="text-left not-selectable"><a href="<?php echo base_url(); ?>employees/view/<?php echo $record['person_id'];?>/2" class="update-person" title="Sửa">Sửa</a></td>
    			<td class="text-left not-selectable"><a href="<?php echo base_url(); ?>/assets/assets/images/avatar-default.jpg" class="rollover"><img src="<?php echo base_url(); ?>assets/assets/images/avatar-default.jpg" alt="Rau cải ngọt" class="img-polaroid avatar" width="45"></a></td>
    		</tr>
    		<?php } ?>
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
var LIST_VIEW_EMPLOYEES = {
	init: function() {
		LIST_VIEW_EMPLOYEES.clickEventOnPaging();
		LIST_VIEW_EMPLOYEES.clickEventOnCheckbox();
		LIST_VIEW_EMPLOYEES.clickEventOnCheckboxAll();
		LIST_VIEW_EMPLOYEES.sortByCol();
		
		var cookie = $.cookie('LIST_VIEW_EMPLOYEES_COOKIE');
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


		$('#tbl_items .view-detail').unbind('click').bind('click', function(){
			var row = $(this).closest('tr');
			var _data = {};
			_data['employee_id'] = $(row).data('id');
			coreAjax.call(
				'<?php echo site_url("employees/modalDetail");?>',
				_data,
				function(response)
				{
					if(response.success)
					{
						window.location.href = SITE_URL + 'reports/specific_employees/' + $(row).data('id');
						// window.open(SITE_URL + 'reports/specific_employees/1','_blank');
					}
				}
			);
		});
	},
	request: function(params) {
		coreAjax.call(
			'<?php echo site_url("employees/build_list_view");?>',
			params,
			function(response)
			{
				if(response.success)
				{
					$('#list_view').html(response.html);
					LIST_VIEW_EMPLOYEES.init();
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
			_data['sort_by'] = sortByField;
			_data['order_by'] = orderBy;
			LIST_VIEW_EMPLOYEES.request(_data);
		});
	},
	addSelectedItems: function(selectedIds) {
		var cookie = $.cookie('LIST_VIEW_EMPLOYEES_COOKIE');
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
		$.cookie('LIST_VIEW_EMPLOYEES_COOKIE', JSON.stringify(cookieObj));
	},
	removeSelectedItems: function(selectedIds) {
		var cookie = $.cookie('LIST_VIEW_EMPLOYEES_COOKIE');
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
		$.cookie('LIST_VIEW_EMPLOYEES_COOKIE', JSON.stringify(cookieObj));
	},
	clickEventOnPaging: function(element)
	{
		$('ul.pagination li a').unbind('click').bind('click', function(){
			var _data = {};
			_data['page'] = $(this).closest('li').data('page');
			_data['search'] = $('#search').val();
			LIST_VIEW_EMPLOYEES.request(_data);
		});
	},
	clickEventOnCheckbox: function(element) {
		$('#tbl_items tbody input[type="checkbox"]').unbind('change').bind('change', function(){
			var showManageRowOptions = false;
			if ($(this).is(':checked')) {
				LIST_VIEW_EMPLOYEES.addSelectedItems([$(this).val()]);
			} else {
				LIST_VIEW_EMPLOYEES.removeSelectedItems([$(this).val()]);
			}
			
			if (LIST_VIEW_EMPLOYEES.showManageRowOptions()) {
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
				LIST_VIEW_EMPLOYEES.addSelectedItems(selected_values);
			} else {
				LIST_VIEW_EMPLOYEES.removeSelectedItems(selected_values);
			}
			
			$('#tbl_items tbody input[type="checkbox"]').each(function(){
				$(this).prop('checked', checked);
			});

			if (LIST_VIEW_EMPLOYEES.showManageRowOptions()) {
				$('#manage-row-options').removeClass('hidden');
			} else {
				$('#manage-row-options').addClass('hidden');
			}
			
		});
	},
	showManageRowOptions: function() {
		var cookie = $.cookie('LIST_VIEW_EMPLOYEES_COOKIE');
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
	LIST_VIEW_EMPLOYEES.init();
});
</script>