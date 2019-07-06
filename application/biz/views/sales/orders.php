<?php $this->load->view("partial/header");
$controller_name="sales";
?>

<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title hidden-print">
					Danh sách nhu cầu đã đóng
				</h3>
			</div>
			<div style="min-height: 410px;" class="panel-body nopadding table_holder table-responsive">
				<div class="col-md-12" style="padding-top: 20px;">
					<?php
					echo form_open('sales/orders', array('method'=>'post', 'class' => 'form_receipt_suspended_recv'));
					?>
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
								<div class="input-group date">
									<input type="text" name="to_date" id="to_date" class="form-control" value="<?php echo $to_date;?>" style="z-index: 0;"/>
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
			        							// var_dump($employee_list); die();
										if (!empty($employee_list)) :
											foreach ($employee_list as $employee) {
												?>
												<option value="<?php echo $employee['person_id'];?>" <?php echo ($employee['person_id'] == $employee_created_id)? 'selected="selected"' : '' ?>><?php echo $employee['employee_name']; ?></option>
												<?php
											}
										endif;
										?>
									</select>
								</div>
							</div>
							<?php
							// echo "<pre>";
							// print_r($employee_list);
							 ?>
							<div class="form-group col-md-6">
								<label class="col-md-3 control-label"><?php echo lang('sale_person_implement');?></label>
								<div class="col-md-9 cmp-inps">
									<select id="employee_imp_id" name="employee_imp_id" class="form-control">
										<option value="">-- Chọn nhân viên --</option>
										<?php
										if (!empty($employee_list)) :
											foreach ($employee_list as $employee) 
											{
												?>
												<option value="<?php echo $employee['person_id'] ?>" <?php echo ($employee['person_id'] == $employee_imp_id)? 'selected="selected"' : '' ?>><?php echo $employee['employee_name']; ?></option>
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
										<input id="btnSearch" type="submit" class="btn btn-primary btn-block form-inps" value="Tìm kiếm..." style="text-align: left;"/>
									</div>
								</div>
							</div>
						</div>
						<?php echo form_close(); ?>
					</div>
					<div class="col-md-12">
						<h3 class="panel-title">
							<span class="title first active">Danh sách nhu cầu đã đóng</span>
							<span class="badge bg-primary tip-left" id="count_pos_contract"><?php echo count($orders);?></span>
						</h3>
					</div>
					<div class="col-md-12">
						<table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableA">
							<colgroup>
								<col width="5%">
								<col width="5%">
								<col width="10%">
								<col width="15%">
								<col width="15%">
								<col width="10%">
								<col width="5%">
								<col width="5%">
								<col width="10%">
								<col width="5%">
								<col width="5%">
							</colgroup>
							<thead>
								<tr>
									<th class="text-center">STT</th>
									<th class="text-center">Thời gian khởi tạo</th>
									<th class="text-center">Tên KH</th>
									<th class="text-center">Tên dịch vụ</th>
									<th class="text-center">Loại dịch vụ</th>
									<th class="text-center">Quy mô</th>
									<th class="text-center">Phí dự kiến</th>
									<th class="text-center">Người khởi tạo</th>
									<th class="text-center">Người phụ trách</th>
									<th class="text-center">Trạng thái</th>
									<th class="text-center">Mở rộng</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$stt = 1;
								foreach ($orders as $suspended_sale)
								{
									?>
									<tr class="<?php echo empty($suspended_sale['is_stock_out']) ? getStatusOfDelivery($suspended_sale['delivery_date']) : '' ?>">
										<input type="hidden" name="sale_id" value="<?php echo $suspended_sale['sale_id'];?>" />
										<td class="text-center"><?php echo $stt++;?></td>
										<td class="text-center">
											<?php echo date("d-m-Y",strtotime($suspended_sale['sale_time']));?>
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
											<?php $employees = $this->Sale->get_employee_by_sale($suspended_sale['sale_id']); ?>
											<?php foreach ($employees as $employee): ?>
												<p><?php echo $employee->first_name; ?></p>
											<?php endforeach; ?>
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
											<div class="btn-group">
												<a title="Chỉnh sửa" class="" href="<?php echo base_url() . 'sales/unsuspend/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span></a>
												<!-- <a class="" href="<?php echo base_url() . 'sales/receipt/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-print" aria-hidden="true"></span></a> -->

												<a class="" href="<?php echo base_url() . 'sales/report_quotes_contract_all/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-file"></span></a>

												<?php
												if ($this->Employee->has_module_action_permission('sales', 'delete_suspended_sale', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>

													<a class="delete_s" href="<?php echo base_url() . 'sales/delete_suspended_sale/'.$suspended_sale['sale_id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
												<?php } ?>
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
	</div>
	<?php $this->load->view("partial/footer"); ?>

	<script type="text/javascript">


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
		$(".form_delete_suspended_recv").submit(function()
		{
			var form = this;

			bootbox.confirm(<?php echo json_encode(lang("receivings_delete_confirmation")); ?>, function(result)
			{
				if (result)
				{
					form.submit();
				}
			});

			return false;
		});

		var SALES_ORDERS = {
			_datatable : null,
			init: function()
			{
				SALES_ORDERS.initDataTable();

				$('#from_date').datetimepicker({format: 'DD-MM-YYYY', locale: 'vi_VN', defaultDate: null, ignoreReadonly: IS_MOBILE ? true : false});
				$('#to_date').datetimepicker({format: 'DD-MM-YYYY', locale: 'vi_VN', defaultDate: null, ignoreReadonly: IS_MOBILE ? true : false});
			},
			initDataTable: function()
			{
				SALES_ORDERS._datatable = $('#dTableA').DataTable({
					"searching":		false,
					"lengthChange": false,
			// "sPaginationType": "bootstrap",
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
			}
		}

		$( document ).ready(function() {
			SALES_ORDERS.init();
		});

	</script>
	<style>
	.glyphicon {
		font-size: 20px;
	}



</style>