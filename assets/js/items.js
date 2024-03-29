/*
*luongpham
*bắt sự kiện checkbox kho
* 01/06/2017
*/
function enable_checkboxes_item()
{
	jQuery('body').on('click', '#select_all, #tbl_items tbody :checkbox',checkbox_click);
}

function enable_delete_item(confirm_message,none_selected_message)
{
	//Keep track of enable_delete has been called
	if(!enable_delete_item.enabled)
	enable_delete_item.enabled=true;
	
	$('#delete').click(function(event)
	{
		event.preventDefault();
		if($("#tbl_items tbody :checkbox:checked").length >0)
		{
			bootbox.confirm(confirm_message, function(result)
			{
				if (result)
				{
					do_delete($("#delete").attr('href'));
				}
			});
		}
	});
}
enable_delete_item.enabled=false;

/*
*luongpham
*xóa ajax sp kho 
*01/06/2017
*/
function do_delete_item(url)
{
	//If delete is not enabled, don't do anything
	if(!enable_delete_item.enabled)
	return;
	
	var row_ids = get_selected_values();
	var selected_rows = get_selected_rows();
	$.post(url, { 'ids[]': row_ids },function(response)
	{
		//delete was successful, remove checkbox rows
		if(response.success)
		{
			show_feedback('success', response.message,COMMON_SUCCESS);
			$(".manage-row-options").addClass("hidden");
			$(selected_rows).each(function(index, dom)
			{
				$(this).find("td").addClass({backgroundColor:"#FF0000"},1200,"linear")
				.end().animate({opacity:0},1200,"linear",function()
				{
					$(this).remove();
					//Re-init sortable table as we removed a row
					update_sortable_table();
					
				});
			});	
		}
		else
		{
			show_feedback('error', response.message,COMMON_ERROR);
		}
		
		
	},"json");
}

/*
*luongpham
*check ajax tất cả sp kho 
*01/06/2017
*/
function enable_select_all_item()
{
	//Keep track of enable_select_all has been called
	if(!enable_select_all_item.enabled)
	enable_select_all_item.enabled=true;
	
	$('#select_all').click(function()
	{

		if($(this).prop('checked'))
		{	
			$('#selectall').show('medium');
			$("#tbl_items tbody :checkbox").each(function()
			{
				$(this).prop('checked',true);
				$(this).parent().parent().find("td").addClass('selected').css("backgroundColor","");
				
			});
		}
		else
		{

			$('#selectall').hide('medium');
			$('#selectnone').hide('medium');
			$("#tbl_items tbody :checkbox").each(function()
			{
				$(this).prop('checked',false);
				$(this).parent().parent().find("td").removeClass('selected');				
			});    	
		}
		});	
}
enable_select_all_item.enabled=false;

/*
*luongpham
*check ajax 1 sp kho 
*01/06/2017
*/
function enable_row_selection_item(rows)
{
	// Keep track of enable_row_selection has been called
	if(!enable_row_selection_item.enabled)
	enable_row_selection_item.enabled=true;
	
	if(typeof rows =="undefined")
	rows=$("#tbl_items tbody tr");
	
	$(rows).on('click',rows,function row_click(event)
	{
		if($(event.target).hasClass('not-selectable') || $(event.target).closest('td').hasClass('not-selectable'))
		{
			return;
		}
		
		var checkbox = $(this).find(":checkbox");
		checkbox.prop('checked',!checkbox.prop('checked'));
		do_email(enable_email.url);
		
		if(checkbox.prop('checked'))
		{
			$(this).find("td").addClass('selected').css("backgroundColor","");
		}
		else
		{
			$(this).find("td").removeClass('selected').css("backgroundColor","");
		}

		determine_checkbox_status();
});
}
enable_row_selection_item.enabled=false;

/*
*luongpham
*bỏ ajax sp kho 
*01/06/2017
*/
function enable_cleanup_item(confirm_message)
{
	if(!enable_cleanup.enabled)
	enable_cleanup.enabled=true;
	
	$('#cleanup').click(function(event)
	{
		do_cleanup(event, confirm_message);
	});
}	
enable_cleanup_item.enabled=false;

function do_cleanup_item(event, confirm_message)
{
	event.preventDefault();
	
	if(!enable_cleanup_item.enabled)
	return;
	
	bootbox.confirm(confirm_message, function(result)
	{
		if (result)
		{
			$.post($('#cleanup').attr('href'), {},function(response)
			{
				show_feedback('success', response.message,COMMON_SUCCESS);
				
			}, 'json');	
		}
	});
}

/*
*luongpham
*ajax hiện option của sp kho 
*01/06/2017
*/
function determine_checkbox_status()
{
	if ($("#tbl_items tbody :checkbox:checked").length > 0)
	{
		$(".manage-row-options").removeClass("hidden");
		$("#email").removeClass("disabled");
		$("#delete").removeClass("disabled");
		$("#generate_barcodes").removeClass("disabled");
		$("#generate_barcode_labels").removeClass("disabled");
		$("#bulk_edit").removeClass("disabled");
	}
	else
	{
		$(".manage-row-options").addClass("hidden");
		$("#email").addClass("disabled");
		$("#delete").addClass("disabled");
		$("#generate_barcodes").addClass("disabled");
		$("#generate_barcode_labels").addClass("disabled");
		$("#bulk_edit").addClass("disabled");
	}
}



// ----------------------------------------------------------------------------------
var count_click = 0;
function checkbox_click(event)
{
	do_email(enable_email.url);
	if($(event.target).prop('checked'))
	{
		$(event.target).parent().parent().find("td").addClass('selected').css("backgroundColor","");		
	}
	else
	{
		$(event.target).parent().parent().find("td").removeClass('selected');		
	}
	
	determine_checkbox_status();
}

function enable_search(suggest_url,confirm_search_message)
{
	//Keep track of enable_email has been called
	if(!enable_search.enabled)
		enable_search.enabled=true;

	$("#search").focus();
	$('#search').click(function()
    {
    	$(this).attr('value','');
    });

	$( "#search" ).autocomplete({
		source: suggest_url + "/suggest",
		delay: 150,
		autoFocus: false,
		minLength: 0,
		select: function( event, ui ) 
		{
			if (DISABLE_QUICK_EDIT)
			{
				event.preventDefault();
				$(this).val(ui.item.label);
				do_search(true);				
			}
			else
			{
				window.location.href = suggest_url + '/view/' + ui.item.value + '/2';
			}
		},
	}).data("ui-autocomplete")._renderItem = function (ul, item) {
	    return $("<li class='customer-badge suggestions'></li>")
	    .data("item.autocomplete", item)
	    .append('<a href="' + suggest_url + '/view/' + item.value + '/2"  class="suggest-item"><div class="avatar">' +
				'<img src="' + item.avatar + '" alt="">' +
				'</div>' +
				'<div class="details">' +
				'<div class="name">' + 
						item.label +
						'</div>' + 
					'<span class="email">' +
						item.subtitle + 
					'</span>' +
				'</div></a>')
	        .appendTo(ul);
	 };


	 $('#search').bind('keypress', function(e) {
			if(e.keyCode==13){
				e.preventDefault();
		        $('#search').autocomplete('close');
				$('#search').val($(this).val());
				$('#search_form').submit();
				count_click += 1;
				if (count_click == 2) {
					$("th").click();
					count_click = 0;
				}
			}
		});

	 $('#ui-id-1').click(function(){
	 	$("th").click();
	 });

	$('#search_form').submit(function(event)
	{
		event.preventDefault();

		if(get_selected_values().length >0)
		{
			bootbox.confirm(confirm_search_message, function(result)
			{
				if (result)
				{
					do_search(true);
				}
			});
		}
		else
		{
			do_search(true);
		}
	});
}
enable_search.enabled=false;

		// Table sort

// Fire event search after specific time 
// @return void
// @param href : callback link
// @param setTimeSend : specific time
// @param tableHTML :  table tag (id) name 
// @param paginationHTML : pagination tag (id) name

function enable_search_2(href,setTimeSend,tableHTML,paginationHTML, totalRows_)
{
	var timer;
	if(!enable_search.enabled)
	enable_search.enabled=true;
	if(typeof totalRows_ ==='undefined') 
	{
		totalRows = '#totalRows'
	}
	else
	{
		totalRows = totalRows_;
	}
	$("#search").focus();
	$('#search').click(function()
	{
		$(this).attr('value','');
	});
	$('#search').keyup(function()
	{
		
		if(this.value.length >=0)
		{
			if(timer){
				clearTimeout(timer);
			}
			timer = setTimeout(function(){
				$.ajax({
					url: href+'/t',
					type: 'POST',
					data:{search: $('#search').val()},
					beforeSend: function()
					{
						$(tableHTML).html('<img src="assets/img/ajax-loader.gif"  width="16" height="16" />');
					},
					success:function (data){
						$(tableHTML).html(JSON.parse(data)['manage_table']);
						$(paginationHTML).html(JSON.parse(data)['pagination']);
						$(totalRows).html(JSON.parse(data)['total_row']);
					},
				});
			},setTimeSend);
		}
	});
	
}
enable_search_2.enabled=false;

function do_search(show_feedback,on_complete)
{	
	//If search is not enabled, don't do anything
	if(!enable_search.enabled)
		return;
		
	if(show_feedback)
		$('#spinner').show();
	
		$('#search_form').ajaxSubmit({
			success:function(response)
			{
				if(typeof on_complete=='function')
					on_complete();
				$('#tbl_items tbody').html(response.manage_table);
				$('.clear-block').removeClass('hidden');
				if(response.pagination == "")
				{
					$('.pagination').addClass('hidden');
				}
				else
				{
					$('.pagination').removeClass('hidden');	
					$('.pagination').html(response.pagination);		
				}
				$('#spinner').hide();
				update_sortable_table();	
				enable_row_selection();		
				$('#tbl_items tbody :checkbox').click(checkbox_click);
				$("#select_all").attr('checked',false);
				
				if (typeof response.count_items !== 'undefined') {
					$('span#count_items').text(response.count_items);
				}
				if (typeof response.count_low_inventory !== 'undefined') {
					$('span#count_low_inventory').text(response.count_low_inventory);
				}
				
				if (typeof response.totalQty !== 'undefined') {
					$('span#totalQty').text(response.totalQty);
				}
				if (typeof response.totalQtyAllLoc !== 'undefined') {
					$('span#totalQtyAllLoc').text(response.totalQtyAllLoc);
				}
			},
			dataType: 'json'
		});
		
}

function enable_email(email_url)
{
	//Keep track of enable_email has been called
	if(!enable_email.enabled)
		enable_email.enabled=true;

	//store url in function cache
	if(!enable_email.url)
	{
		enable_email.url=email_url;
	}
	
	$('#select_all, #tbl_items tbody :checkbox').click(checkbox_click);
}
enable_email.enabled=false;
enable_email.url=false;

function do_email(url)
{
	//If email is not enabled, don't do anything
	if(!enable_email.enabled)
		return;

	$.post(url, { 'ids[]': get_selected_values() },function(response)
	{
		$('#email').attr('href',response);
	});

}

function enable_checkboxes()
{
		jQuery('body').on('click', '#select_all, #tbl_items tbody :checkbox',checkbox_click);
}

function enable_delete(confirm_message,none_selected_message)
{
	//Keep track of enable_delete has been called
	if(!enable_delete.enabled)
		enable_delete.enabled=true;
	
	$('#delete').click(function(event)
	{
		event.preventDefault();
		if($("#tbl_items tbody :checkbox:checked").length >0)
		{
			bootbox.confirm(confirm_message, function(result)
			{
				if (result)
				{
					do_delete($("#delete").attr('href'));
				}
			});
		}
	});
}
enable_delete.enabled=false;

function do_delete(url)
{
	//If delete is not enabled, don't do anything
	if(!enable_delete.enabled)
		return;
	
	var row_ids = get_selected_values();
	var selected_rows = get_selected_rows();
	$.post(url, { 'ids[]': row_ids },function(response)
	{
		//delete was successful, remove checkbox rows
		if(response.success)
		{
			show_feedback('success', response.message,COMMON_SUCCESS);
			$(".manage-row-options").addClass("hidden");
			$(selected_rows).each(function(index, dom)
			{
				$(this).find("td").addClass({backgroundColor:"#FF0000"},1200,"linear")
				.end().animate({opacity:0},1200,"linear",function()
				{
					$(this).remove();
					//Re-init sortable table as we removed a row
					update_sortable_table();
					
				});
			});	
		}
		else
		{
			show_feedback('error', response.message,COMMON_ERROR);
		}
		

	},"json");
}

function enable_select_all()
{
	//Keep track of enable_select_all has been called
	if(!enable_select_all.enabled)
		enable_select_all.enabled=true;

	$('#select_all').click(function()
	{
		if($(this).prop('checked'))
		{	
			$('#selectall').show('medium');
			$("#tbl_items tbody :checkbox").each(function()
			{
				$(this).prop('checked',true);
				$(this).parent().parent().find("td").addClass('selected').css("backgroundColor","");

			});
		}
		else
		{
			$('#selectall').hide('medium');
			$('#selectnone').hide('medium');
			$("#tbl_items tbody :checkbox").each(function()
			{
				$(this).prop('checked',false);
				$(this).parent().parent().find("td").removeClass('selected');				
			});    	
		}
	 });	
}
enable_select_all.enabled=false;

function enable_row_selection(rows)
{
	//Keep track of enable_row_selection has been called
	if(!enable_row_selection.enabled)
		enable_row_selection.enabled=true;
	
	if(typeof rows =="undefined")
		rows=$("#tbl_items tbody tr");
	
	rows.hover(
		function row_over()
		{
			$(this).css("cursor","pointer");
		},
		
		function row_out()
		{
			if(!$(this).find("td").hasClass("selected"))
			{
				$(this).find("td").removeClass('over');
			}
		}
	);
	
	rows.click(function row_click(event)
	{
		if($(event.target).hasClass('not-selectable') || $(event.target).closest('td').hasClass('not-selectable'))
		{
			return;
		}

		var checkbox = $(this).find(":checkbox");
		checkbox.prop('checked',!checkbox.prop('checked'));
		do_email(enable_email.url);
		
		if(checkbox.prop('checked'))
		{
			$(this).find("td").addClass('selected').css("backgroundColor","");
		}
		else
		{
			$(this).find("td").removeClass('selected').css("backgroundColor","");
		}
		
		determine_checkbox_status();
	});
}
enable_row_selection.enabled=false;

function update_sortable_table()
{
	//let tablesorter know we changed <tbody> and then triger a resort
	$("#tbl_items").trigger("update");

	if(typeof $("#tbl_items")[0].config!="undefined")
	{
		var sorting = $("#tbl_items")[0].config.sortList; 		
		$("#tbl_items").trigger("sorton",[sorting]);
	}
}

function update_row(row_id,url)
{
	$.post(url, { 'row_id': row_id },function(response)
	{
		//Replace previous row
		var row_to_update = $("#tbl_items tbody tr :checkbox[value="+row_id+"]").parent().parent();
		row_to_update.replaceWith(response);	
		reinit_row(row_id);
		highlight_row(row_id);
	});
}

function reinit_row(checkbox_id)
{
	var new_checkbox = $("#tbl_items tbody tr :checkbox[value="+checkbox_id+"]");
	var new_row = new_checkbox.parent().parent();
	enable_row_selection(new_row);
	//Re-init some stuff as we replaced row
	update_sortable_table();
	//re-enable e-mail
	new_checkbox.click(checkbox_click);	
}

function highlight_row(checkbox_id)
{
	var new_checkbox = $("#tbl_items tbody tr :checkbox[value="+checkbox_id+"]");
	var new_row = new_checkbox.parent().parent();

	new_row.find("td").animate({backgroundColor:"#e1ffdd"},"slow","linear")
		.animate({backgroundColor:"#e1ffdd"},5000)
		.animate({backgroundColor:"#e9e9e9"},"slow","linear");
}

function get_selected_values()
{
	var selected_values = new Array();
	$("#tbl_items tbody :checkbox:checked").each(function()
	{

		selected_values.push($(this).val());
	});
	return selected_values;
}

function get_selected_rows() 
{ 
	var selected_rows = new Array(); 
	$("#tbl_items tbody :checkbox:checked").each(function() 
	{ 
		selected_rows.push($(this).parent().parent()); 
	}); 
	return selected_rows; 
}

function get_visible_checkbox_ids()
{
	var row_ids = new Array();
	$("#tbl_items tbody :checkbox").each(function()
	{
		row_ids.push($(this).val());
	});
	return row_ids;
}

function determine_checkbox_status()
{
	if ($("#tbl_items tbody :checkbox:checked").length > 0)
	{
		$(".manage-row-options").removeClass("hidden");
		$("#email").removeClass("disabled");
		$("#delete").removeClass("disabled");
		$("#generate_barcodes").removeClass("disabled");
		$("#generate_barcode_labels").removeClass("disabled");
		$("#bulk_edit").removeClass("disabled");
	}
	else
	{
		$(".manage-row-options").addClass("hidden");
		$("#email").addClass("disabled");
		$("#delete").addClass("disabled");
		$("#generate_barcodes").addClass("disabled");
		$("#generate_barcode_labels").addClass("disabled");
		$("#bulk_edit").addClass("disabled");
	}
}

function enable_cleanup(confirm_message)
{
	if(!enable_cleanup.enabled)
		enable_cleanup.enabled=true;
	
	$('#cleanup').click(function(event)
	{
		do_cleanup(event, confirm_message);
	});
}	
enable_cleanup.enabled=false;

function do_cleanup(event, confirm_message)
{
	event.preventDefault();
	
	if(!enable_cleanup.enabled)
		return;

	bootbox.confirm(confirm_message, function(result)
	{
		if (result)
		{
			$.post($('#cleanup').attr('href'), {},function(response)
			{
				show_feedback('success', response.message,COMMON_SUCCESS);
			
			}, 'json');	
		}
	});
}


function enable_sorting(sort_url,table_columns, per_page, order_col, order_dir,tableHTML_ , paginationHTML_)
{
	if(typeof tableHTML_ ==='undefined') {tableHTML = '#tbl_items';} else{ tableHTML = tableHTML_;}
	if(typeof paginationHTML_ ==='undefined') {paginationHTML ='.pagination';}else{ paginationHTML = paginationHTML_;}
	if(!enable_sorting.enabled)
	{
		enable_sorting.enabled=true;
	}
	var offset=0;
	if($("#pagination_top").find('strong').text() > 0 )
	{
		offset = ($("#pagination_top").find('strong').text() - 1) * per_page;
	}

	//Set default headers based on order_col and order_dir	
	var sort_index = table_columns.indexOf(order_col);
if (order_dir == 'asc')
	{
		$(tableHTML +' tr th').removeClass('header headerSortUp').removeClass('header headerSortDown');
		$(tableHTML +' tr th').eq(sort_index).addClass('header headerSortUp');
	}
	else
	{
		$(tableHTML +' tr th').removeClass('header headerSortUp').removeClass('header headerSortDown');
		$(tableHTML +' tr th').eq(sort_index).addClass('header headerSortDown');	
	}
	
	$(tableHTML +' tr th').click(function()
	{
		if (table_columns[$(this).parent().children().index($(this))])
		{
			$(tableHTML +' tbody').html('<img src="assets/img/ajax-loader.gif"  width="16" height="16" />');

			if ($(this).hasClass('headerSortUp'))
			{	
				do_sorting(sort_url, 0, table_columns[$(this).parent().children().index($(this))], "desc",tableHTML,paginationHTML);
				$(tableHTML +' tr th').removeClass('header headerSortUp').removeClass('header headerSortDown');
				$(this).removeClass('header headerSortUp').addClass('header headerSortDown');
			}
			else
			{				
				do_sorting(sort_url, 0, table_columns[$(this).parent().children().index($(this))], "asc",tableHTML,paginationHTML);
				$(tableHTML +' tr th').removeClass('header headerSortUp').removeClass('header headerSortDown');
				$(this).removeClass('header headerSortUp').addClass('header headerSortUp');
			}
		}
	});	
	
	$(document).on('click', ".pagination a", function(event)
	{
		event.preventDefault();
		$(".manage-row-options").addClass("hidden");
		var offset = !is_int($(this).attr('href').substring($(this).attr('href').lastIndexOf('/')+1)) ? 0 : $(this).attr('href').substring($(this).attr('href').lastIndexOf('/') + 1);
		
		var table_column_index = $(tableHTML +' tr th.header').parent().children().index($(tableHTML +' tr th.header'));
		var sort_dir = $(tableHTML +' tr th.headerSortDown').length == 1 ? 'desc' : 'asc';
		do_sorting($(this).attr('href'), offset, table_column_index >=0 ? table_columns[table_column_index] : 0, sort_dir);
	});
	
}

 enable_sorting.enabled=false;

function do_sorting(sort_url, offset, order_col, order_dir,tableHTML_,paginationHTML_)
{
	if(typeof tableHTML_ ==='undefined') {tableHTML = '#tbl_items';} else{ tableHTML = tableHTML_;}
	if(typeof paginationHTML_ ==='undefined') {paginationHTML ='.pagination';}else{ paginationHTML = paginationHTML_;}
	
	var params = { "search": $("#search").val(), "offset" : offset , "order_col" : order_col, "order_dir" : order_dir};
	
	if ($("#category_id").length == 1)
	{
		params['category_id'] = $("#category_id").val();
	}
	
	if ($("#fields").length == 1)
	{
		params['fields'] = $("#fields").val();
	}
	
	$.post(sort_url,params, function(response)
	{	
		$(tableHTML+' tbody').html(response.manage_table);
		$(paginationHTML).html(response.pagination);
		
		
		// re-init elements in new table, as table tbody children were replaced
		update_sortable_table();	
		enable_row_selection();		
		$(tableHTML +' tbody :checkbox').click(checkbox_click);
		$("#select_all").prop('checked',false);
	}, "json");
}

$(document).on('click', ".btn-clear-selection", function(event){
	event.preventDefault();
	$("#tbl_items tbody :checkbox").each(function()
	{
		$(this).prop('checked',false);
		$(this).parent().parent().find("td").removeClass('selected');				
	});    	
	$(".manage-row-options").addClass("hidden");
});
