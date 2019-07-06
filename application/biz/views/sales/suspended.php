<?php $this->load->view("partial/header");
$controller_name="items";
?>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/new-task.js" ></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />
<style type="text/css">
tr.delivery_warning_lv1 {
	background-color: #<?php echo $this->config->item('color_warning_level1'); ?> !important;
}

tr.delivery_warning_lv2 {
	background-color: #<?php echo $this->config->item('color_warning_level2'); ?> !important;
}

tr.delivery_warning_lv3 {
	background-color: #<?php echo $this->config->item('color_warning_level3'); ?> !important;
}
</style>
<div class="container-fluid">

	<div class="pull-right">
		<div class="buttons-list">
			<div class="pull-right-btn">					
				<a href="<?php echo base_url('sales') ?>" class="btn btn-success" title="Thêm mới"><span class="">Thêm mới</span></a>							
			</div>
		</div>				
	</div>



	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title hidden-print">
					<?php echo lang('sales_list_of_suspended_sales'); ?>
				</h3>
			</div>
			<div class="panel-body nopadding table_holder table-responsive" >
				<br/>
				<form action="<?php echo base_url() ?>sales/suspended" method="post">
					<div class="row">
						<div class="form-group col-md-6">
							<label class="col-md-3 control-label">Từ ngày</label>
							<div class="col-md-9 cmp-inps">
								<div class="input-group date">
									<input type="text" name="from_date" id="from_date" class="form-control" value="<?php echo $from_date;?>" style="z-index: 0;" />
									<span class="input-group-addon bg">
										<i class="ion ion-ios-calendar-outline"></i>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label class="col-md-3 control-label">Đến ngày</label>
							<div class="col-md-9 cmp-inps">
								<div class="input-group date" style="z-index: 0;">
									<input type="text" name="to_date" id="to_date" class="form-control" value="<?php echo $to_date;?>" />
									<span class="input-group-addon bg">
										<i class="ion ion-ios-calendar-outline"></i>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-6">
							<label class="col-md-3 control-label"><?php echo lang('sale_suspended_service');?></label>
							<div class="col-md-9 cmp-inps">
								<select id="service_id" name="service_id" class="form-control">
									<option value="">-- Tất cả --</option>
									<?php 
									if (!empty($service_list)):
										foreach($service_list as $item) {
											if (!empty($item->name)) {
												?>
												<option value="<?php echo $item->item_id;?>" <?php echo ($item->item_id == $service_id? 'selected="selected"' : ''); ?>><?php echo $item->name;?></option>
												<?php 
											}}
										endif;
										?>
									</select>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label class="col-md-3 control-label"><?php echo lang('sale_suspended_status');?></label>
								<div class="col-md-9 cmp-inps">
									<select id="status_id" name="status_id" class="form-control">
										<option value="">-- Tất cả--</option>
										<?php 
										if (!empty($status_list)) :
											foreach($status_list as $status) {
												?>
												<option value="<?php echo $status->status_id;?>" <?php echo ($status->status_id == $status_id)? 'selected="selected"' : '' ?>><?php echo $status->status_name;?></option>
											<?php } 
										endif; ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label class="col-md-3 control-label"><?php echo lang('sale_person_create');?></label>
								<div class="col-md-9 cmp-inps">
									<select id="employee_created_id" name="employee_created_id" class="form-control">
										<option value="">-- Chọn nhân viên --</option>
										<?php 
										if (!empty($employee_list)) :
											foreach ($employee_list as $employee) {
												?>
												<option value="<?php echo $employee['person_id'];?>" <?php echo ($employee['id'] == $employee_created_id)? 'selected="selected"' : '' ?>><?php echo $employee['employee_name']; ?></option>
												<?php 
											}
										endif; 
										?>
									</select>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label class="col-md-3 control-label"><?php echo lang('sale_person_implement');?></label>
								<div class="col-md-9 cmp-inps">
									<select id="employee_imp_id" name="employee_imp_id" class="form-control">
										<option value="">-- Chọn nhân viên --</option>
										<?php 
										if (!empty($employee_list)) :
											foreach ($employee_list as $employee) {
												?>
												<option value="<?php echo $employee['id'];?>" <?php echo ($employee['id'] == $employee_imp_id)? 'selected="selected"' : '' ?>><?php echo $employee['employee_name']; ?></option>
												<?php 
											}
										endif; 
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<label class="col-md-3 control-label"><?php echo lang('customers_name');?></label>
								<div class="col-md-9 cmp-inps">
									<input type="text" class="form-control form-inps" id="customer_name" name="customer_name" value="<?php echo $customer_name;?>" />
								</div>
							</div>
							<div class="form-group col-md-6">
								<div class="col-md-9 "></div>
								<div class="col-md-3 cmp-inps">
									<div class="input-group">
										<input id="btnSearch" type="submit" class=" btn btn-primary btn-block form-inps" value="Tìm kiếm..." style="text-align: left;"/>
									</div>
								</div>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-md-12 text-right">
							<form action="<?php echo base_url();?>sales" method="post">
								<a href="<?php echo base_url(); ?>sales/orders" class="btn btn-primary"><?php echo lang('sale_list_suspended_is_closed');?></a>
								<input type="hidden" name="r" value="new" />
							</form>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h3 class="panel-title">
								<span class="title first active">Danh sách nhu cầu khách hàng</span>
								<span class="badge bg-primary tip-left" id="count_pos_contract"><?php echo count($suspended_sales);?></span>
							</h3>

						</div>
					</div>
					<table class="table table-bordered table-striped table-hover data-table" id="dTable">

						<thead>
							<tr>
								<th class="text-center">STT</th>
								<th class="text-center">Thời gian khởi tạo</th>
								<th class="text-center">Tên KH</th>
								<th class="text-center">Tên dịch vụ</th>
								<th class="text-center">Nhóm dịch vụ</th>
								<th class="text-center">Quy mô</th>                        
								<th class="text-center">Phí dự kiến</th>
								<th class="text-center">Người khởi tạo</th>
								<th class="text-center">Người phụ trách</th>
								<th class="text-center">Trạng thái</th>
								<th class="text-center">Tính năng</th>
								<th class="text-center" style="color: blue;" class="">Mở rộng</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$stt = 1;
				// echo $this->Employee->get_logged_in_employee_info()->id; die();
							// echo "<pre>"; print_r($suspended_sales); die();
							foreach ($suspended_sales as $suspended_sale)
							{
								?>
								<tr class="<?php echo empty($suspended_sale['is_stock_out']) ? getStatusOfDelivery($suspended_sale['delivery_date']) : '' ?>">
									<input type="hidden" name="sale_id" value="<?php echo $suspended_sale['sale_id'];?>" />
									<td class="text-center"><?php echo $stt++;?></td>
									<td class="text-center">
										<?php echo date('d/m/Y',strtotime($suspended_sale['sale_time']));?>
									</td>
									<td>
										<?php
										if (isset($suspended_sale['customer_id']))
										{
											$customer = $this->Customer->get_info($suspended_sale['customer_id']);
											$company_name = $customer->company_name;
											if($company_name) {
												echo $customer->first_name. ' '. $customer->last_name.' ('.$customer->company_name.')';
											}
											else {
												echo $customer->first_name. ' '. $customer->last_name;
											}
										}
										else
										{
											?>
											&nbsp;
											<?php
										}
										?>
									</td>

									<td><!-- Ten dịch vụ -->
										<?php 
										$lst_items = $this->Sale->get_sale_items($suspended_sale['sale_id'])->result();
										$total_cost = 0;
										if (!empty($lst_items)) {
											$str_item = '';
											foreach ($lst_items as $item) {
												if ($str_item != '') {
													$str_item .= ',';
												}
												$str_item .= $item->item_name;
												$total_cost += $item->item_cost_price;
											}
											echo $str_item;
										}
										?>                	       
									</td>

									<td><!-- Loại dịch vụ -->
										<?php 
										$categories = $this->Sale->get_sale_items_categories($suspended_sale['sale_id']);
										if (!empty($categories)) {
											$str_item_type = '';
											foreach($categories as $cat) {
												if ($str_item_type != '') {
													$str_item_type .= ',';
												}
												$str_item_type .= $cat->name;
											}
											echo $str_item_type;
										}
										?>
									</td>
									<td><!-- Quy mô -->
										<?php echo $suspended_sale['comment_term']; ?>
									</td>
									<td class="text-right"><!-- Phí dự kiến -->
										<?php echo number_format($suspended_sale['calculatedPrice']);?>
									</td>
									<td><!-- Người khởi tạo -->
										<?php 
										$sale_creator = $this->Employee->get_info($suspended_sale['employee_id']);
										echo $sale_creator->first_name . ' '  . $sale_creator->last_name;
										?>
									</td>
									<td><!-- Người phụ trách -->
										<?php 
										$supporters_ = $this->Sale->get_employee_by_sale($suspended_sale['sale_id']);
										$str_supporter = '';
										if (!empty($supporters_)) {
											foreach ($supporters_ as $support) {
												echo($support->first_name) . '<br/>';
											}
										}
										?>
									</td>
									<td><!-- Trạng thái -->
										<?php 
										if (!empty($status_list)):
											foreach ($status_list as $status) {
												if ($suspended_sale['sale_status_id'] == $status->status_id) {
													echo $status->status_name;
													break;
												}
											}
										endif;
										?>
									</td>
									<td>
										<?php
										if ($suspended_sale['task_id']!=null) {
											$employee_is_view = $this->db->select('*')->from('phppos_task_user_relations')->where('(is_implement=1 OR is_pheduyet=1 OR is_join=1 OR is_progress=1)')->where('task_id',$suspended_sale['task_id'])->get()->result_array();
										}

										$arr_is_view = array();
										if (!empty($employee_is_view)) {
											foreach ($employee_is_view as $key => $value) {
												$arr_is_view[] = $value['user_id'];
											}
										}
										// lay person nguoi phu trach
										$arr_employee_implement= array();
										if (!empty($supporters_)) {
											foreach ($supporters_ as $support) {
												$arr_employee_implement[] = $support->person_id;
											}
										}

										$person_id_create = $this->Sale->get_info($suspended_sale['sale_id'])->row()->employee_id;
										// echo "<pre>"; print_r($this->Employee->get_logged_in_employee_info());
										if ($suspended_sale['suspended'] == 1) {
											if (in_array($this->Employee->get_logged_in_employee_info()->person_id,$arr_employee_implement) || $this->Employee->get_logged_in_employee_info()->group_id==1 ||$this->Employee->get_logged_in_employee_info()->group_id==8) {
												echo '<a style="margin:0 5px;" href="javascript:void(0);" dataid="'. $suspended_sale['sale_id'] .'" class="btn btn-primary btn-create-project">Tạo dự án</a>';
											}
										} else if ($suspended_sale['suspended'] == 2) {
											if (in_array($this->Employee->get_logged_in_employee_info()->person_id,$arr_employee_implement) || in_array($this->Employee->get_logged_in_employee_info()->id,$arr_is_view) || $this->Employee->get_logged_in_employee_info()->group_id==1 ||$this->Employee->get_logged_in_employee_info()->group_id==8) {
												echo '<a title="Tạo hợp đồng" style="margin:0 5px;" href="'. base_url() .'contracts/view/customer/-1/'. $suspended_sale['sale_id'] .'" class="btn btn-primary">Tạo hợp đồng</a>';
											}
										} else {
											echo '<p>Đã tạo dự án</p>
											<p>Đã tạo hợp đồng</p>';
										}
										
										?>
									</td>


									<td>
										<div class="">
											<?php  
											if (in_array($this->Employee->get_logged_in_employee_info()->person_id,$arr_employee_implement) || $this->Employee->get_logged_in_employee_info()->person_id == $person_id_create || $this->Employee->get_logged_in_employee_info()->group_id==1 ||$this->Employee->get_logged_in_employee_info()->group_id==8)
											{
												?>
												<a title="Chỉnh sửa" class="bibi" href="<?php echo base_url() . 'sales/unsuspend/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-folder-open"></span></a>
												<?php
											}
											?>
											
											<!-- <a title="In" class="btn btn-default" href="<?php echo base_url() . 'sales/receipt/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a> -->

											<a title="Xuất file" class="bibi" href="<?php echo base_url() . 'sales/report_quotes_contract_all/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-file"></span></a>

											<?php
											if ($this->Employee->has_module_action_permission('sales', 'delete_sale', $this->Employee->get_logged_in_employee_info()->person_id)){ 
                                    # if (!isset($isUseForContract[$suspended_sale['sale_id']])) {?>
                                    	<a title="Xóa" class="bibi delete_s " href="<?php echo base_url() . 'sales/delete_suspended_sale/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                    <?php }?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>



        </div>
    </div>
</div>
</div>

<div id="my_modal" class="modal fade search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

</div>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
	$(".form_delete_suspended_sale").submit(function()
	{
		var formDelete = this;
		bootbox.confirm(<?php echo json_encode(lang("sales_delete_confirmation")); ?>, function(result)
		{
			if (result)
			{
				formDelete.submit();
			}
		});

		return false;

	});

	$(".delete_s").click(function()
	{
		var formDelete = $(this).attr('href');
		bootbox.confirm(<?php echo json_encode(lang("Bạn có muốn xóa không?")); ?>, function(result)
		{
			if (result)
			{
				window.location.href=formDelete;
			}
		});

		return false;

	});



	$(".form_email_receipt_suspended_sale").ajaxForm({success: function()
		{
			bootbox.alert("<?php echo lang('common_receipt_sent'); ?>");
		}});

	$('#dTable').dataTable({
		"searching":		false,
		aaSorting : [[0, 'asc']],
		"lengthChange": false,
		initComplete: function () {
			this.api().column(2).every( function () {
				var column = this;
				var select = $('<select id="by-category" class="select2" name="subject" style="width: 100%; padding: 5px 3px;"><option value="">Tất Cả</option></select>')
				.appendTo( $('#filter-by-category-block') )
				.on( 'change', function () {
					var val = $.fn.dataTable.util.escapeRegex(
						$(this).val()
						);

					column
					.search( val ? '^'+val+'$' : '', true, false )
					.draw();
				} );

				column.data().unique().sort().each( function ( d, j ) {
					select.append( '<option value="'+d+'">'+d+'</option>' )
				} );
			} );
		},
	// "sPaginationType": "bootstrap"
	"pageLength": 20,
	"language": {
		"paginate": {
			"first":      "First",
			"last":       "Last",
			"next":       "&gt;",
			"previous":   "&lt",
			"class":"vi"
		},
		"search":         "Tìm kiếm:",
	},
});

</script>
<script type="text/javascript">
	$( document ).ready(function() {
		<?php if(!empty($_SESSION['notice'])) {
			$notice = $_SESSION['notice'];
			unset($_SESSION['notice']); ?>
			toastr.success('<?php echo $notice; ?>', 'Thông báo');
		<?php } ?>
		$('#dTable tbody').on('click', 'td .btn_approve', function(){
			$('#dTable tr').removeClass('row-selected');
			var _data = {};
			_data['obj_id'] = $(this).closest('tr').find('input[name="sale_id"]').val();
			_data['step_code'] = 'bao_gia';
			coreAjax.call(
				'<?php echo site_url("approver_groups/view_approve_statuses");?>',
				_data,
				function(response)
				{
					if(response.success)
					{
						$('#approveStatusesModal').remove();
						$('body').append(response.html);
						$('#approveStatusesModal').modal('show');
					}
				}
				);
		});

		$('#from_date').datetimepicker({format: 'DD-MM-YYYY', locale: 'vi_VN', defaultDate: null, ignoreReadonly: IS_MOBILE ? true : false});
		$('#to_date').datetimepicker({format: 'DD-MM-YYYY', locale: 'vi_VN', defaultDate: null, ignoreReadonly: IS_MOBILE ? true : false});

		$(document).on('click', '.btn_approve', function() {
			var saleId = $(this).attr('dataid');
			$.post('<?php echo base_url()?>sales/approve', {sale_id : saleId}, function() {
				location.reload();
			});
		});
	});
</script>
<style>
.icon.control {
	font-size: 16px;
	
}
.input-group .form-control{
	z-index: 1;
}
.bibi{
	width: 50%;
}
.glyphicon {
	font-size: 20px;
}


.sorting:after,.sorting_desc:after,.sorting_asc:after{
	content: "" !important;
}
#dTable_length,.dataTables_info{
	display: none;
}


#dTable_paginate ul .paginate_button a{
	font-weight: bold;
}
</style>