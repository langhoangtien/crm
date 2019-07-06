<?php $this->load->view("partial/header"); ?>




<?php $this->load->view('customers/categories_form') ?>
	
	</div>

			
<script type='text/javascript'>
$('.panel-collapse').collapse('hide')
$(document).on('click', "#edit_danh_muc_khach_hang",function()
{	
	var customers_table = $(this).parents().data('customers_table');
	// lấy id của danh mục 
	var category_id = $(this).parents().data('category_id');
	bootbox.prompt({
	  title: <?php echo json_encode('Xin mời nhập tên muốn sửa'); ?>,
	  value: $(this).parents().data('name'),
	  callback: function(category_name) {
		  
	  	if (category_name)
	  	{
			$.post('<?php echo site_url("customers/tao_moi_categories");?>'+'/'+category_id, {category_name : category_name, customers_table: customers_table, category_id: category_id},function(response) {
						$('#childrent-1').html(response['danh_muc_con']);
						$('#childrent-2').html(response['danh_muc_con_2']);
			}, "json");
	  	}
	  }
	});
});
//thêm mới danh mục con
$(document).on('click', "#them_moi_danh_muc_con",function()
{	
	// bảng để chèn vào
	var customers_table = $(this).parents().data('customers_table');
	// parrent id sẽ đưuọc đặt bằng với id của cha
	var parrent_id = $(this).parents().data('category_id');
	bootbox.prompt(<?php echo json_encode('Xin mời nhập tên danh mục'); ?>, function(category_name)
	{
		if (category_name)
		{
			$.post('<?php echo site_url("customers/tao_moi_categories");?>', {category_name : category_name, customers_table: customers_table, parrent_id: parrent_id},function(response) {
					$('#childrent-1').html(response['danh_muc_con']);
					$('#childrent-2').html(response['danh_muc_con_2']);
			}, "json");

		}
	});
});
// thêm mới danh mục khách hàng
$(document).on('click', ".them_moi_danh_muc",function()
{	
	// bảng để chèn vào
	var customers_table = $(this).data('customers_table');

	bootbox.prompt(<?php echo json_encode('Xin mời nhập tên danh mục'); ?>, function(category_name)
	{
		if (category_name)
		{
			$.post('<?php echo site_url("customers/tao_moi_categories");?>', {category_name : category_name, customers_table: customers_table},function(response) {
						$('#childrent-1').html(response['danh_muc_con']);
			}, "json");

		}
	});
});
// mở tab
$(document).on('click', ".mo_tab_moi",function()
{	

	// append tab mới
	var customers_table = $(this).data('customers_table');
	var parrent_id = $(this).data('parrent_id');
	var category_id = $(this).data('category_id');
	var aria_controls = $(this).data('aria_controls');
	
		$.post('<?php echo site_url("customers/mo_danh_muc_con");?>', {customers_table: customers_table, category_id:category_id,parrent_id:parrent_id},function(response) {
			// để gán vào tab thứ nhất
			if(response['danh_muc_con'] != ''){
				$('#childrent-1').html(response['danh_muc_con']);
			}			
			// để gán vào tab thứ 2
				$('#childrent-2').html(response['danh_muc_con_2']);
			}, "json");

	event.preventDefault();		

	});


function xoa_danh_muc(category_id,customers_table,parrent_id)
{
	// nếu parrent_id=0 nghĩa là mục cha
	if(parrent_id == 0) {
		bootbox.confirm(<?php echo json_encode('Bạn có chắc chắn muốn xóa danh mục này'); ?>, function(result)
		{
			if(result)
			{
				$.post('<?php echo site_url("customers/xoa_danh_muc_con");?>', {category_id : category_id, customers_table: customers_table,parrent_id:parrent_id},function(response) {
						$('#childrent-1').html(response['danh_muc_con']);
						$('#childrent-2').html(response['danh_muc_con_2']);

				}, "json");
			}
		});
	}
	 else
	{
		bootbox.confirm(<?php echo json_encode('Bạn có chắc chắn muốn xóa danh mục này'); ?>, function(result)
		{
			if(result)
			{
				$.post('<?php echo site_url("customers/xoa_danh_muc_con");?>', {category_id : category_id, customers_table: customers_table},function(response) {
						$('#childrent-1').html(response['danh_muc_con']);
						$('#childrent-2').html(response['danh_muc_con_2']);

				}, "json");
			}
		});
	}
}
 

// $('.list-group').click(function()
// {
// 	if ($(this).children().index($(this)))
// 	{
// 		console.log($(this).children().index($(this)));

// 		if ($(this).hasClass('selected-tab'))
// 		{	
// 			$('#sortable_table tr th').removeClass('selected-tab').removeClass('header headerSortDown');
// 			$(this).removeClass('header headerSortUp').addClass('header headerSortDown');
// 		}
// 		else
// 		{				
// 			$('#sortable_table tr th').removeClass('header headerSortUp').removeClass('header headerSortDown');
// 			$(this).removeClass('header headerSortUp').addClass('selected-tab');
// 		}
// 	}
// });	
</script>
<?php $this->load->view('partial/footer'); ?>
