<?php $this->load->view("partial/header"); ?>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/autoNumeric.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-n9-autocomplete.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-n9-autocomplete.js" ></script>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<?php
    $thousands_separator = $this->config->item('thousands_separator');
    $decimal_point       = $this->config->item('decimal_point');
    $number_of_decimals  = $this->config->item('number_of_decimals');

    $sale_mode = $this->sale_lib->get_mode();
?>
<div class="manage-row-options hidden">
	<div class="email_buttons text-center">
	<div class= "col-lg-8 form-group">
		<input id="inputItemName" class ="form-control" type="text" placeholder="nhập tên sản phẩm" >
	</div>
	<div class= "form-group col-lg-4 ">
		<a class="btn btn-primary btn-lg" title="Lưu tên sản phẩm" id="saveItemName">
			 <span class="">Lưu</span>
		</a>
	</div>
	</div>
</div>
<div id="sales_page_holder">
 <div id="sale-grid-big-wrapper" class="clearfix register">
	<div class="row">
		<div class="clearfix" id="category_item_selection_wrapper">
  <div class="">
		<div class="spinner" id="grid-loader" style="display:none">
		  <div class="rect1"></div>
		  <div class="rect2"></div>
		  <div class="rect3"></div>
		</div>

  		<div class="text-center">
	  		<div id="grid_selection" class="btn-group" role="group">
				<a href="javascript:void(0);" class="<?php echo $this->config->item('default_type_for_grid') == 'categories' || !$this->config->item('default_type_for_grid') ? 'btn active' : '';?> btn btn-grid" id="by_category"><?php echo lang('reports_categories')?></a>
				<!-- <a href="javascript:void(0);" class="<?php echo $this->config->item('default_type_for_grid') == 'tags' ? 'btn active' : '';?> btn btn-grid" id="by_tag"><?php echo lang('common_tags')?></a> -->
			</div>
		</div>

    	<div id="grid_breadcrumbs"></div>
		<div id="category_item_selection" class="row register-grid"></div>
		<div class="pagination hidden-print alternate text-center"></div>
	</div>
</div>
	</div>
</div>

<div id="register_container" class="sales clearfix">
  <?php 
   $this->load->view("sales/register"); 
  ?>
</div>

</div>

<script type="text/javascript">
    $('#delete_customer, #finish_sale, .input-group.supporter, .register-items-form').hide();
    $('input, select, textarea').attr('disabled', 'disabled');
    $('a.xeditable, a.delete-item, #supporter_select_list').each(function() {
        $(this).replaceWith('<div>'+$(this).text()+'</div>');
    });
$(document).ready(function()
{
	<?php if ($this->config->item('require_employee_login_before_each_sale') && isset($dont_switch_employee) && !$dont_switch_employee) { ?>
		$('#switch_user').trigger('click');
	<?php } ?>

	$(window).load(function()
	{
		setTimeout(function()
		{
		<?php if ($fullscreen) { ?>
			$('.fullscreen').click();
		<?php }
		else {
		?>
		$('.dismissfullscreen').click();
		<?php
		} ?>

		}, 0);
	});
	<?php if ($this->config->item('always_show_item_grid')) { ?>
		$(".show-grid").click();
	<?php } ?>

 	var current_category_id = null;
	var current_tag_id = null;

  var categories_stack = [{category_id: 0, name: <?php echo json_encode(lang('common_all')); ?>}];

  function updateBreadcrumbs()
  {
     var breadcrumbs = '';
     for(var k = 0; k< categories_stack.length;k++)
     {
       var category_name = categories_stack[k].name;
       var category_id = categories_stack[k].category_id;

       breadcrumbs += (k != 0 ? ' &raquo ' : '' )+'<a href="javascript:void(0);"class="category_breadcrumb_item" data-category_id = "'+category_id+'">'+category_name+"</a>";
     }

     $("#grid_breadcrumbs").html(breadcrumbs);
  }

  $(document).on('click', ".category_breadcrumb_item",function()
  {
      var clicked_category_id = $(this).data('category_id');
      var categories_size = categories_stack.length;
      current_category_id = clicked_category_id;

      for(var k = 0; k< categories_size; k++)
      {
        var current_category = categories_stack[k]
        var category_id = current_category.category_id;

        if (category_id == clicked_category_id)
        {
          if (categories_stack[k+1] != undefined)
          {
            categories_stack.splice(k+1,categories_size - k - 1);
          }
          break;
        }
      }

      if (current_category_id != 0)
      {
        loadCategoriesAndItems(current_category_id,0);
      }
      else
      {
        loadTopCategories();
      }
  });

	function loadTopCategories()
	{
		$('#grid-loader').show();
		$.get('<?php echo site_url("sales/categories");?>', function(json)
		{
			processCategoriesResult(json);
		}, 'json');
	}

	function loadTags()
	{
		$('#grid-loader').show();
		$.get('<?php echo site_url("sales/tags");?>', function(json)
		{
			processTagsResult(json);
		}, 'json');
	}

  function loadCategoriesAndItems(category_id, offset)
  {
    $('#grid-loader').show();
    current_category_id = category_id;
    //Get sub categories then items
    $.get('<?php echo site_url("sales/categories_and_items/");?>/'+current_category_id+'/'+offset, function(json)
    {
        processCategoriesAndItemsResult(json);
    }, "json");
  }

  function loadTagItems(tag_id, offset)
  {
     $('#grid-loader').show();
	  current_tag_id = tag_id;
     //Get sub categories then items
     $.get('<?php echo site_url("sales/tag_items/");?>/'+tag_id+'/'+offset, function(json)
     {
         processTagItemsResult(json);
     }, "json");
  }

	$(document).on('click', ".pagination.categories a", function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);

		$.get('<?php echo site_url("sales/categories/0");?>/'+offset, function(json)
		{
			processCategoriesResult(json);

		}, "json");
	});

	$(document).on('click', ".pagination.tags a", function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);

		$.get('<?php echo site_url("sales/tags");?>/'+offset, function(json)
		{
			processTagsResult(json);

		}, "json");
	});


	$(document).on('click', ".pagination.categoriesAndItems a", function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
	  	loadCategoriesAndItems(current_category_id, offset);
	 });

 	$(document).on('click', ".pagination.items a", function(event)
 	{
 		$('#grid-loader').show();
 		event.preventDefault();
 		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
 	  	loadTagItems(current_tag_id, offset);
 	 });

	$('#category_item_selection_wrapper').on('click','.category_item.category', function(event)
	{
      event.preventDefault();
      current_category_id = $(this).data('category_id');
      var category_obj = {category_id: current_category_id, name: $(this).find('p').text()};
      categories_stack.push(category_obj);
      loadCategoriesAndItems($(this).data('category_id'), 0);
	});

	$('#category_item_selection_wrapper').on('click','.category_item.tag', function(event)
	{
      event.preventDefault();
		current_tag_id = $(this).data('tag_id');
      loadTagItems($(this).data('tag_id'), 0);
	});

	$('#category_item_selection_wrapper').on('click','#by_category', function(event)
	{
	 	current_category_id = null;
		current_tag_id = null;
		$("#grid_breadcrumbs").html('');
		$('.btn-grid').removeClass('active');
		$(this).addClass('active');
		categories_stack = [{category_id: 0, name: <?php echo json_encode(lang('common_all')); ?>}];
		loadTopCategories();
	});

	$('#category_item_selection_wrapper').on('click','#by_tag', function(event)
	{
	 	current_category_id = null;
		current_tag_id = null;
		$('.btn-grid').removeClass('active');
		$(this).addClass('active');
		$("#grid_breadcrumbs").html('');
		loadTags();
	});


	$('#category_item_selection_wrapper').on('click','.category_item.item', function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
		$.post('<?php echo site_url("sales/add");?>', {item: $(this).data('id') }, function(response)
		{
			<?php
			if (!$this->config->item('disable_sale_notifications'))
			{
				echo "show_feedback('success', ".json_encode(lang('common_successful_adding')).", ".json_encode(lang('common_success')).");";
			}

			?>
			$('#grid-loader').hide();
			$("#register_container").html(response);
			$('.show-grid').addClass('hidden');
			$('.hide-grid').removeClass('hidden');
		});
	});

	$("#category_item_selection_wrapper").on('click', '#back_to_categories', function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();

    //Remove element from stack
    categories_stack.pop();

    //Get current last element
    var back_category = categories_stack[categories_stack.length - 1];

    if (back_category.category_id != 0)
    {
      loadCategoriesAndItems(back_category.category_id,0);
    }
    else
    {
      loadTopCategories();
    }
  });

	$("#category_item_selection_wrapper").on('click', '#back_to_tags', function(event)
	{
		$('#grid-loader').show();
		event.preventDefault();
	   loadTags();
	});


	function processCategoriesResult(json)
	{

		$("#category_item_selection_wrapper .pagination").removeClass('categoriesAndItems').removeClass('tags').removeClass('items').addClass('categories');
		$("#category_item_selection_wrapper .pagination").html(json.pagination);

		$("#category_item_selection").html('');

		for(var k=0;k<json.categories.length;k++)
		{
			 var category_item = $("<div/>").attr('class', 'category_item category col-md-2 register-holder categories-holder col-sm-3 col-xs-6').data('category_id',json.categories[k].id).append('<p> <i class="ion-archive"></i> '+json.categories[k].name+'</p>');
			$("#category_item_selection").append(category_item);
		}

    	updateBreadcrumbs();
		$('#grid-loader').hide();
	}

	function processTagsResult(json)
	{
		$("#category_item_selection_wrapper .pagination").removeClass('categoriesAndItems').removeClass('categories').removeClass('items').addClass('tags');
		$("#category_item_selection_wrapper .pagination").html(json.pagination);

		$("#category_item_selection").html('');

		for(var k=0;k<json.tags.length;k++)
		{
			 var tag_item = $("<div/>").attr('class', 'category_item tag col-md-2 register-holder tags-holder col-sm-3 col-xs-6').data('tag_id',json.tags[k].id).append('<p> <i class="ion-pricetags"></i> '+json.tags[k].name+'</p>');
			$("#category_item_selection").append(tag_item);
		}

		$('#grid-loader').hide();
	}

  function processCategoriesAndItemsResult(json)
  {
	 $("#category_item_selection").html('');
    var back_to_categories_button = $("<div/>").attr('id', 'back_to_categories').attr('class', 'category_item register-holder no-image back-to-categories col-md-2 col-sm-3 col-xs-6 ').append('<p>&laquo; '+<?php echo json_encode(lang('common_back_to_categories')); ?>+'</p>');
    $("#category_item_selection").append(back_to_categories_button);

    for(var k=0;k<json.categories_and_items.length;k++)
    {
		 if (json.categories_and_items[k].type == 'category')
		 {
       	var category_item = $("<div/>").attr('class', 'category_item category col-md-2 register-holder categories-holder col-sm-3 col-xs-6').data('category_id',json.categories_and_items[k].id).append('<p> <i class="ion-archive"></i> '+json.categories_and_items[k].name+'</p>');
      	$("#category_item_selection").append(category_item);
		 }
		 else if (json.categories_and_items[k].type == 'item')
		 {
	       var image_src = json.categories_and_items[k].image_src;
	       var prod_image = "";
	       var image_class = "no-image";
	       var item_parent_class = "";
	       if (image_src != '' ) {
	         var item_parent_class = "item_parent_class";
	         var prod_image = '<img src="'+image_src+'" alt="" />';
	         var image_class = "";
	       }

	       var item = $("<div/>").attr('class', 'category_item item col-md-2 register-holder ' + image_class + ' col-sm-3 col-xs-6  '+item_parent_class).attr('data-id', json.categories_and_items[k].id).append(prod_image+'<p>'+json.categories_and_items[k].name+'<br /><span class="text-bold">'+(json.categories_and_items[k].price ? '('+(json.categories_and_items[k].different_price ? '<span style="text-decoration: line-through;">'+json.categories_and_items[k].regular_price+'</span> ': '')+json.categories_and_items[k].price+')' : '')+'</span></p>');
	       $("#category_item_selection").append(item);

		 }
	 }

    $("#category_item_selection_wrapper .pagination").removeClass('categories').removeClass('tags').removeClass('items').addClass('categoriesAndItems');
    $("#category_item_selection_wrapper .pagination").html(json.pagination);

    updateBreadcrumbs();
    $('#grid-loader').hide();

  }

  function processTagItemsResult(json)
  {
 	 $("#category_item_selection").html('');
     var back_to_categories_button = $("<div/>").attr('id', 'back_to_tags').attr('class', 'category_item register-holder no-image back-to-categories col-md-2 col-sm-3 col-xs-6 ').append('<p>&laquo; '+<?php echo json_encode(lang('common_back_to_tags')); ?>+'</p>');
     $("#category_item_selection").append(back_to_categories_button);

     for(var k=0;k<json.items.length;k++)
     {
 	       var image_src = json.items[k].image_src;
 	       var prod_image = "";
 	       var image_class = "no-image";
 	       var item_parent_class = "";
 	       if (image_src != '' ) {
 	         var item_parent_class = "item_parent_class";
 	         var prod_image = '<img src="'+image_src+'" alt="" />';
 	         var image_class = "";
 	       }

 	       var item = $("<div/>").attr('class', 'category_item item col-md-2 register-holder ' + image_class + ' col-sm-3 col-xs-6  '+item_parent_class).attr('data-id', json.items[k].id).append(prod_image+'<p>'+json.items[k].name+'<br /> <span class="text-bold">'+(json.items[k].price ? '('+(json.items[k].different_price ? '<span style="text-decoration: line-through;">'+json.items[k].regular_price+'</span> ': '')+json.items[k].price+')' : '')+'</span></p>');
 	       $("#category_item_selection").append(item);

 	 }

     $("#category_item_selection_wrapper .pagination").removeClass('categories').removeClass('tags').removeClass('categoriesAndItems').addClass('items');
     $("#category_item_selection_wrapper .pagination").html(json.pagination);

     $('#grid-loader').hide();
  }

  <?php if ($this->config->item('default_type_for_grid') == 'tags') {  ?>
	  loadTags();
	<?php
	}else
	{
	?>
	loadTopCategories();
	<?php
	}
	?>
});

<?php if (!$this->agent->is_mobile()) { ?>
	var last_focused_id = null;

	setTimeout(function(){$('#item').focus();}, 10);
<?php } ?>
</script>


<script>
//Keyboard events...only want to load once
$(document).keyup(function(event)
{
	var mycode = event.keyCode;

	//tab
	if (mycode == 9)
	{
		var $tabbed_to = $(event.target);

		if ($tabbed_to.hasClass('xeditable'))
		{
			$tabbed_to.trigger('click').editable('show');
		}
	}

});

$(document).keydown(function(event)
{
	var mycode = event.keyCode;

	//F2
	if (mycode == 113)
	{
		$("#item").focus();
	}

	//F4
	if (mycode == 115)
	{
		event.preventDefault();
		$("#finish_sale_alternate_button").click();
		$("#finish_sale_button").click();
	}

	//F7
	if (mycode == 118)
	{
		event.preventDefault();
		$("#amount_tendered").focus();
		$("#amount_tendered").select();
	}

	//ESC
	if (mycode == 27)
	{
		event.preventDefault();
		$("#cancel_sale_button").click();
	}
});

$(document).ready(function(){
	<?php if ($this->session->flashdata('send_mail_cutomers')) { ?>
		show_feedback('success', <?php echo json_encode($this->session->flashdata('send_mail_cutomers')); ?>, <?php echo json_encode(lang('common_success')); ?>);
	<?php } ?>
});
</script>

<script type="text/javascript">
    function handling_add(input_name, id, name, url) {
        var res = input_name.split("_"); 
    	var group_id = res[1];
        $.ajax({
            type: "POST",
            url: url,
            data: {
                id          : id,
                group_id    : group_id
            },
            beforeSend : function (){
                $('.mask').show();
            },
            success: function(string){
                $('.mask').hide();
                var data = $.parseJSON(string);
                if(data.flag == 'false')
                	toastr.error(data.msg, 'Thông báo');
                else {
		            var item_html = item_template(input_name, id, name);

		            $('#'+input_name+'_select_list').append( item_html );
                }
            }
        });
    }

    function handling_delete(input_name, id, url) {
        var res = input_name.split("_"); 
    	var group_id = res[1];

        $.ajax({
            type: "POST",
            url: url,
            data: {
                id          : id,
                group_id    : group_id
            },
            beforeSend : function (){
                $('.mask').show();
            },
            success: function(string){
                $('.mask').hide();
            }
        });
    }

    function n9_grid_callback(data_table, result) {
    	if(data_table == 'sale_store_payment_modal') {
    		$('#total_conlai').html(result.con_lai);
    	}
    }
$(document).ready(function(){
	var thousands_separator = '<?php echo $thousands_separator; ?>';
	var decimal_point 		= '<?php echo $decimal_point; ?>';
	var number_of_decimals  = '<?php echo $number_of_decimals; ?>';
  
});

function load_sale_order_modal(type) {
	if(type == 'store_account_payment')
		var modal_url = BASE_URL + 'sales/modal_store_payment';

	if(type == 'vat_order')
		var modal_url = BASE_URL + 'sales/modal_vat_order';

    $.ajax({
        type: "POST",
        url: modal_url,
        data: {
        },
        success: function(html){
        	if(html == 'other-sale')
        		toastr.error('Bạn đang ở chế độ bán hàng khác', 'Lỗi');
        	else if(html == 'no-customer')
        		toastr.error('Chưa chọn khách hàng', 'Lỗi');
        	else {
	            $('#my_table').addClass('size-1000');
	            $('#my_table').html(html);
	            $('#my_table').modal('toggle');
        	}
        }
    }); 
}
</script>
<style>
.manage-row-options .email_buttons {
	margin-left: 0px;
	z-index: 1;
}
</style>
<?php
if(isset($_SESSION['sale_store_payment_modal_filter']))
	unset($_SESSION['sale_store_payment_modal_filter']);

if(isset($_SESSION['sale_vat_order_modal_filter']))
	unset($_SESSION['sale_vat_order_modal_filter']);

?>
<?php

if(isset($_SESSION['notice'])) 
{

?>

<script>
	toastr.success('Tạo nhu cầu khách hàng thành công', 'Thành công');
</script>

<?php 
unset($_SESSION['notice']); 
}  ?>
<div class="modal fade box-modal" id="quick_modal">
</div>
<div id="my_table" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
</div>
<?php $this->load->view("partial/footer"); ?>
