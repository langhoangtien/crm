<?php $this->load->view("partial/header"); ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/modal.js" ></script>

<style type="text/css">.dnone{display: none}</style>
<?php $this->load->model('Customer'); ?>
<?php 
$start_of_time =  date('d-m-Y', 0);
$today = date('d-m-Y');                                   
?>
<script type="text/javascript">
$(document).ready(function (){
  var table_columns = ["", "<?php echo $this->db->dbprefix('people'); ?>"+".person_id", 'last_name','', ''];
	enable_sorting("<?php echo site_url("$controller_name/update_group_SMS_email/$smsmail_group_id/1/t"); ?>",table_columns,0,'people.person_id','asc', '#sortable_table','#_pagination');
	enable_select_all();
	enable_checkboxes();
	enable_row_selection();
	enable_search_2('<?php echo ("$controller_name/update_group_SMS_email/$smsmail_group_id/1");?>',400,'#sortable_table tbody','#_pagination');
	var save_btn = <?php echo ($smsmail_group_id==0)? 'true': 'false';?>;
	(save_btn == false )?$('#create_smsMail_group').hide():'';
	$('.delete_all_tmp ,#list_send_totalRows').hide()
	//delete_sms_tmp_all
	$('.delete_all_tmp').click(function(){
	  $.ajax({
	            type: "post",
	            url: '<?php echo site_url("$controller_name/delete_tmp_all");?>',
				data:{tempt:'groupSMSEmail'},
	            success: function(data){
	                $('table tbody').html("<tr><td colspan='3' style='text-align: center'>Không có khách hàng nào</td></tr>")
	            }
	        });
	})
	
	
	$('#list_send').click(function(){
		
		$('.delete_all_tmp ,#list_send_totalRows,#create_smsMail_group').show();
		$('#list_send_totalRows').show();
		$('#all_items').removeClass('selected');
		$(this).addClass('selected');
		change_the_pagination('customers/update_group_SMS_email/<?php echo $smsmail_group_id;?>/1',1)
	});
	$('#all_items').click(function(){
		(save_btn == false )?$('#create_smsMail_group').hide():'';
		$('.delete_all_tmp ,#list_send_totalRows').hide()
		$('#totalRows').show();
		$('#list_send').removeClass('selected');
		
		$(this).addClass('selected');
		change_the_pagination('customers/update_group_SMS_email/<?php echo $smsmail_group_id;?>/1')
	});
	
	/* 
	* EVENT 
	*/
	var baseUrl ='<?php echo $baseUrl;?>';
	
	
	// callback function when pagination changed
	function change_the_pagination(href,tempt = 0)
	{
		$.ajax({
			url: href+'/t',
			type: 'POST',
			data:{tempt: tempt},
			beforeSend: function()
			{
				$('#sortable_table tbody').html('<img src="assets/img/ajax-loader.gif"  width="16" height="16" />');
			},
			success:function (data){
				$('#sortable_table tbody').html(JSON.parse(data)['manage_table']);
				$('#_pagination').html(JSON.parse(data)['pagination']);
				if(tempt == 1)
				{
					$('#list_send_totalRows').html(JSON.parse(data)['total_row']);
					$('#totalRows').hide();
				}
				else{
					
					$('#totalRows').html(JSON.parse(data)['total_row']);
					$('#list_send_totalRows').hide();
				}
				
			},
				
				
		});
	}
  
	// Event on first load page
	if(window.location.href == baseUrl)
	{
	
		change_the_pagination('customers/update_group_SMS_email/<?php echo $smsmail_group_id;?>/1');
	}
	else{
			
		change_the_pagination(window.location.href);
	}
	
	// Event change page view when pagination click
	jQuery("body").on('click','.linkClicked',function(e){
		e.preventDefault();
		href = $(this).attr('href');
		if($('#list_send').hasClass('selected'))
		{
			change_the_pagination(href,1);
			
		}
		else
		{
			change_the_pagination(href);
		}
	});



				$('#create_smsMail_group,#create_smsMail_group_top,#save_smsMail_group').click(function(){
					var group_sms_email_name = $('#group_sms_email_name').val();
					var group_sms_email_description = $('#group_sms_email_description').val();
					console.log(group_sms_email_description);
					var selected = get_selected_values();
					if(group_sms_email_name <= 0 )
						{
							toastr.warning('<?php echo  lang('customers_check_group_name');?>', 'Warning');
						}
					else 
						{
							$.ajax({
								type: "POST",
								url: BASE_URL + 'customers/save_group_customer/<?php echo $smsmail_group_id ?>',
								data:
								{
									items: selected,
									session: 'sms_mail_update',
									txt_group_description: group_sms_email_description ,
									txt_group_name: group_sms_email_name 
								
								},
								beforeSend: function() {
									$('.mask').show();
								},
								success: function(response){
									toastr.success(JSON.parse(response)['msg'],'Success');
									window.location = <?php echo json_encode(site_url('customers/manage_group_SMS_email_detail/')); ?>+"/"+JSON.parse(response)['smsmail_group_id'];
								}
							});
						}
				});	
				var total_rows_send;
				$('#check_list_send_mail').click(function()
				{
					var selected = get_selected_values();
					if (selected.length == 0)
					{
						bootbox.alert(<?php echo json_encode(lang('common_must_select_customer_for_labels')); ?>);
						return false;
					}

					$('.dnone').show();
					$('#list_send_totalRows').hide();
					var _data = {};
					_data['items'] = selected;
					_data['ck'] = 'send_mail';
					_data['session'] = 'sms_mail_update';
					coreAjax.call(
						'<?php echo site_url("$controller_name/save_list_send_all");?>',
						_data,
						function(response)
						{
							$('.total_send').html(response.totalRows);
							
						}
					);
				});
});

	
</script>


<div class="manage_buttons">
	<div class="manage-row-options hidden">
		<div class="email_buttons text-center">
			<?php if ($controller_name =='customers') { ?>
			<a class="btn btn-primary btn-lg check_list_send_mail" id="check_list_send_mail">
					<span><?php echo lang('common_add') .' '.lang('customers_list_add_send'); ?></span>
				</a>
				
			<a class="btn btn-primary btn-lg" title="title" id="create_smsMail_group_top" href="javascript:;"><span class=""><?php echo lang('common_save'); ?></span></a>
			<?php } ?>
		</div>
	</div>
	<div class="cl">
				<!-- Search box-->
				<div class="col-md-3 col-lg-3 col-sm-3" >
					<div class="search no-left-border">
						<label ><?php echo lang('common_search');?></label>
						<input type="text" class="form-control" name ='search' id='search' value="" placeholder="<?php echo lang('common_search'); ?> "/>
					</div>
				</div>

				<div class="col-md-2 col-lg-2 col-sm-2" >
					<div class="search no-left-border">
						<a class="btn btn-primary btn-lg" title="Lưu nhóm" id="save_smsMail_group" href="javascript:;"><span class="">Lưu nhóm</span></a>
					</div>	
				</div>
				<div class="col-md-12 col-lg-12 col-sm-12" >
					<div class="search no-left-border">
						<label ><?php echo lang('customers_create_group_name_label');?></label>
						<input  class="form-control" name ='group_sms_email_name' id='group_sms_email_name' value="<?php echo isset($customer_detail->name)? $customer_detail->name:'';?>" placeholder="<?php echo lang('customers_create_group_name_label'); ?> "/>
					</div>
					<div class="search no-left-border">
						<label id="lb_group_description"><?php echo lang('customers_create_group_description');?></label>	
						<textarea type="text" class="form-control" name ='group_sms_email_description' id='group_sms_email_description' placeholder="<?php echo lang('customers_create_group_description'); ?>"><?php echo isset($customer_detail->description)? $customer_detail->description:'';?></textarea>
					</div>
				</div>
       
	</div>
</div>

	<div class="container-fluid">
	
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<h4 class="alert alert-warning"><?php echo lang('customers_group_emailSMS_add');?></h3>
				<div class="panel-heading">
					<h3 class="panel-title space_title">

						<span id="all_items" class="selected item-tabs">
						<?php echo lang('common_list_of').' '.lang('module_'.$controller_name); ?>	
						</span>

						<span id ="totalRows" total <?php echo $controller_name?>" class="totalRows badge bg-primary tip-left"><?php echo $total_rows; ?></span>

						<?php if ($total_rows_send > 0):?> 
						<?php 	$dnone = 'dblock';?>
						<?php else: ?>
						<?php $dnone = 'dnone';?>
						<?php endif;?>
						<span  id="list_send" class=" item-tabs <?php echo $dnone; ?>">
							<span class=""><?php echo lang('customers_list_add_send');?></span>
						</span>									
						<span id="list_send_totalRows" title="<?php echo isset($total_rows_send);?> total <?php echo $controller_name?>" class="badge bg-primary tip-left totalRows <?php echo $dnone; ?> total_send"><?php echo $total_rows_send; ?></span>
						
					</h3>
				</div>
				
				<div class="panel-body nopadding table_holder table-responsive" >
					<table class="tablesorter table table-hover" id="sortable_table">
						<thead>
							<tr>
								<th><input type="checkbox" id="select_all" /><label for="select_all"><span></span></label></th>
								<th class='leftmost'>STT</th>
								<th class='leftmost'><?php echo lang('customers_sms_tmp_name')?></th>
								<th class='leftmost'><?php echo lang('customers_sms_tmp_phonenumber')?></th>
								<th class='leftmost'>Email</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5" class=" a-menu">
								<a class= 'btn btn-primary btn-lg delete_all_tmp'><?php echo lang('customers_del_list')?></a>
								<a class="btn btn-primary btn-lg" title="title" id="create_smsMail_group" href="javascript:;"><span class="">Lưu nhóm</span></a>
								</td>
							</tr>
						</tfoot>
					</table>
					<div id="_pagination" class="panel-options custom">
					</div>
				</div>	
			</div>	
		
		</div>
	</div>
</div>

<style type="text/css">
#sortable_table tr th {
    white-space: normal;
}
.search{
	display:block !important;
}
#save_smsMail_group{
	margin-top: 1.7rem;
}

</style>
<div class="modal fade box-modal" id="quick_modal">
</div>
<?php $this->load->view("partial/footer"); ?>