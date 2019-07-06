<?php
$this->load->model('reports/Specific_customer');
$this->load->library('sale_lib');
$tab = 1;

if (isset($_SESSION['tab-click'])) {
	$tab = $_SESSION['tab-click'];
}
	 
?>
<?php $this->load->view("partial/header"); ?>
<div id="urlid" data-url="<?php echo current_url(); ?>"></div>
<div class="row">
	<?php foreach($overall_summary_data as $name=>$value) { ?>
	    <div class="col-md-3 col-xs-12 col-sm-6 ">
	        <div class="info-seven primarybg-info">
	            <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
	            <?php if($name == 'total_orders' || $name == 'TONG_SO_MAT_HANG') echo $value; 
	            	  else echo to_currency($value); 
	            ?>
	            <p><?php echo lang('reports_'.$name); ?></p>
	        </div>
	    </div>
	<?php }?>
</div>

<?php if(isset($pagination) && $pagination) {  ?>
	<div class="pagination hidden-print alternate text-center" id="pagination_top" >
		<?php echo $pagination;?>
	</div>
<?php }  ?>
	
	
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku reports-printable">
			<div class="panel-heading">
				<h3 class="panel-title space_title">		
					<?php if (isset($controller_path) && $controller_path == 'specific_customer') {?>
					<span id="Giaodich" class="<?php echo ($tab == 3) ? 'selected' : '';?> item-tabs">
					<?php echo lang('lich_su_giao_dich'); ?>
					</span>
					<span id="Baogia" class="<?php echo ($tab == 2) ? 'selected' : '';?> item-tabs">
					<?php echo lang('lich_su_bao_gia') . ' / hợp đồng'; ?>
					</span>
					<span id="Muahang" class="<?php echo ($tab == 1) ? 'selected' : '';?> item-tabs">
					<?php echo lang('lich_su_mua_hang'); ?>
					</span>									
					<span id="Guimail" class="<?php echo ($tab == 4) ? 'selected' : '';?> item-tabs">
					<?php echo lang('lich_su_gui_mail'); ?>
					</span>
					<span id="Guisms" class="<?php echo ($tab == 5) ? 'selected' : '';?> item-tabs">
					<?php echo lang('lich_su_gui_sms'); ?>
					</span>
					<span id="Thuchi" class="<?php echo ($tab == 6) ? 'selected' : '';?> item-tabs">
					<?php echo lang('lich_su_thu_chi'); ?></span>
					<span id="Congno" class="<?php echo ($tab == 7) ? 'selected' : '';?> item-tabs">
					<?php echo lang('cong_no'); ?></span>	
					<?php }?>	

					<?php 
					if ($this->uri->segment(2) == 'detailed_receivings' && $this->uri->segment(5) > -1) { ?>
							<span class="selected item-tabs">
							<?php echo lang('Lịch sử nhập hàng'); ?>
							</span>
					<?php } ?>	
				</h3>
				<div >
				<?php echo lang('reports_reports'); ?> - <?php echo $title ?>
				<small class="reports-range"><?php echo $subtitle ?></small>
				</div>				
			</div>
			<div class="panel-body Baogia <?php echo ($tab == 2) ? '' : 'hidden';?>">
				<div class="table-responsive">
					<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
						<thead>
							<tr>
								<td><?php echo lang('ngay_mua_hang');?></td>
								<td><?php echo lang('ma_bao_gia') . ' / Hợp đồng'; ?></td>								
								<td><?php echo lang('nhan_vien'); ?></td>
								<td><?php echo lang('gia_tri_don_hang'); ?></td>
								<td>Báo giá</td>
								<td><?php echo lang('trang_thai');?></td>
								<td class="hidden"><?php echo lang('xoa');?></td>
							</tr>
						</thead>
						<tbody>
						
						<?php
						$total = 0;
						$taxes = 0;
						foreach ($sale_materials as $key => $item) { ?>
						<tr>
							<td style="text-align:center;">
							 <?php echo date(get_date_format() . ' ' .get_time_format(), strtotime($item['delivery_date'])); ?>
							</td>
							<td>LTDH <?php echo $item['sale_id']; ?></td>
							<td><?php echo $item['last_name'] . ' ' . $item['first_name']; ?></td>
							<td>
							<?php 
							$total = (($item['item_unit_price'] * $item['percent']) / 100) + $item['item_unit_price'];
							echo NumberFormatToCurrency($total);							
								?>
							</td>
							<td>
								<?php 
									$querys = $this->Sale->getMailByPerson($item['customer_id']);
									$counts = 1;
									foreach ($querys as $key => $value) {

										if ($value['file'] != '') { 
											?>
											<a class="view" title="title" href="<?php echo site_url('reports/view/'. $value['id']) ?>" data-toggle="modal" data-target="#myModal">Lần <?php echo $counts; ?>
											</a>

											<?php 
											if ($counts % 5 == 0) {
												echo '<br />' ;
											}
											$counts = $counts + 1;									
										 }
									}
								?>
							</td>
							<td style="text-align:center;">Đã gửi</td>
							<td class="hidden">
								<a href="reports/deletedItem/<?php echo $item['item_id'] ;?>" id="deletedItem">Xóa</a>
							</td>
						</tr>
						<?php
						}
						?>
						
						
						</tbody>
					</table>
				</div>				
			</div>

			<div class="panel-body Muahang <?php echo ($tab == 1) ? '' : 'hidden';?>">
				<div class="table-responsive">
				<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
					<thead>
						<tr align="center" style="font-weight:bold">
							<td class="hidden-print"><a href="#" class="expand_all" >+</a></td>
							<?php foreach ($headers['summary'] as $header) { ?>
							<td align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></td>
							<?php } ?>

						</tr>
					</thead>
					<tbody>
						<?php foreach ($summary_data as $key=>$row) { ?>

						<tr>
							<td class="hidden-print"><a href="#" class="expand" style="font-weight: bold;">+</a></td>
							<?php foreach ($row as $cell) { ?>
							<td align="<?php echo $cell['align']; ?>"><?php echo $cell['data']; ?></td>
							<?php } ?>
						</tr>
						<tr>
							<td colspan="<?php echo count($headers['summary']) + 1; ?>" class="innertable" style="display:none;">
								<table class="table table-bordered">
									<thead>
										<tr>
											<?php foreach ($headers['details'] as $header) { ?>
											<th align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></th>
											<?php } ?>
										</tr>
									</thead>

									<tbody>

										<?php foreach ($details_data[$key] as $row2) { ?>
											<tr>
												<?php foreach ($row2 as $cell) { ?>
												<td align="<?php echo $cell['align']; ?>"><?php echo $cell['data']; ?></td>
												<?php } ?>
											</tr>
										<?php   } ?>
									</tbody>
								</table>


								<?php
								echo (isset($details_data['chi_phi_list'])?$details_data['chi_phi_list'][$key]:'');
								?>
								<?php if(!empty($listExpensesOfReceiving[$key])):?>
									<table class="table table-bordered">
											<thead>
												<tr>
													<?php foreach ($headers['expenses'] as $header) { ?>
													<th align="<?php echo $header['align']; ?>"><?php echo $header['data']; ?></th>
													<?php } ?>
										    </tr>
											</thead>
											<tbody>
								<?php $total_expenses_money =0;

											 foreach ($listExpensesOfReceiving[$key] as $expenseOfReceiving) { ?>
											   <tr>
														  <td><?php echo $expenseOfReceiving['expense_description']; ?></td>
															<td><?php echo ($expenseOfReceiving['expense_type']==1)?lang('reports_expense_type_1'):lang('reports_expense_type_2'); ?></td>
															<td><?php echo to_currency($expenseOfReceiving['expense_amount']); ?></td>
															<td><?php echo  to_currency($expenseOfReceiving['expense_tax']); ?></td>
											   </tr>
												 <?php ($expenseOfReceiving['expense_type']==1)? $total_expenses_money+=$expenseOfReceiving['expense_amount']:$total_expenses_money-=$cell['expense_amount'];?>
										    <?php } ?>
													<tr>
															<td class="right" colspan="3">Tổng chi phí</td>
														  <td class="right"><?php echo to_currency($total_expenses_money);?></td>
													</tr>
											</tbody>
									</table>
								<?php endif;?>
                                <?php if(!empty($details_data_discount[$key])):?>
									<table class="table table-bordered">
                                        <tbody>
                                            <?php foreach ($details_data_discount[$key] as $data_discount) { ?>
                                                <tr>
                                                    <?php foreach ($data_discount as $cell_data_discount) { ?>
                                                    <td align="<?php echo $cell_data_discount['align']; ?>"><?php echo $cell_data_discount['data']; ?></td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
													
                                        </tbody>
									</table>
								<?php endif;?></td>
						</tr>


						<?php } ?>
					</tbody>
				</table>
				</div>
			</div>


			<div class="panel-body Giaodich <?php echo ($tab == 3) ? '' : 'hidden';?>">
				<div class="table-responsive">
				<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
					<thead>
						<tr>
							<td><?php echo lang('ma_giao_dich'); ?></td>
							<td><?php echo lang('ngay_giao_dich'); ?></td>
							<td><?php echo lang('nhan_vien_giao_dich'); ?></td>
							<td><?php echo lang('ten_giao_dich'); ?></td>
							<td><?php echo lang('chi_tiet_giao_dich'); ?></td>
							<td><?php echo lang('tien_do'); ?></td>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
				</div>	
			</div>


			<div class="panel-body Guimail <?php echo ($tab == 4) ? '' : 'hidden';?>">
				<div class="table-responsive">
				<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
					<thead>
						<tr align="center" style="font-weight:bold">
							<td><?php echo lang('tieu_de');?></td>
							<td style="width:150px;"><?php echo lang('thoi_gian');?></td>
							<td><?php echo lang('ghi_chu'); ?></td>
							<td><?php echo lang('nguoi_gui'); ?></td>
							<td><?php echo lang('trang_thai'); ?></td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($send_mail as $key => $row) { ?>
							<tr>
							<td><?php echo $row['title']; ?></td>
							<td style="text-align:center;">
							<?php echo date(get_date_format().' '.get_time_format(), strtotime($row['time'])); ?>
							</td>
							<td></td>
							<td>
								<?php
								$models = $this->Specific_customer->getHistorySendEmailById($row['employee_id']);
								foreach ($models as $v) {
									echo $v->first_name . ' ' . $v->last_name;
								}
								?>
							</td>
							<td><?php if ($row['status'] == 1) {
								echo 'Gửi thành công';
							}else{
								echo 'Gửi thất bại';
							} ?>
								
							</td>
							<td style="text-align:center">
							<a class="view" title="title" href="<?php echo  site_url('reports/view/'. $row['id']) ?>"  data-toggle="modal" data-target="#myModal">Xem
							<span class="hidden"><?php echo $row['status']; ?></span>
							</a>
							</td>
							</tr>
						
						
						<?php } ?>
					</tbody>
				</table>
				</div>	
			</div>


			<div class="panel-body Guisms <?php echo ($tab == 5) ? '' : 'hidden';?>">
				<div class="table-responsive">
				<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
					<thead>
						<tr align="center" style="font-weight:bold">
							<td><?php echo lang('tieu_de');?></td>
							<td style="width:150px;"><?php echo lang('thoi_gian');?></td>
							<td><?php echo lang('ghi_chu'); ?></td>
							<td><?php echo lang('nguoi_gui'); ?></td>
							<td><?php echo lang('trang_thai'); ?></td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($send_sms as $key => $row) { ?>
							<tr>
							<td><?php echo $row['title']; ?></td>
							<td style="text-align:center;">
							<?php echo date(get_date_format().' '.get_time_format(), strtotime($row['time'])); ?>
							</td>
							<td></td>
							<td>
								<?php
								$models = $this->Specific_customer->getHistorySendEmailById($row['employee_id']);
								foreach ($models as $v) {
									echo $v->first_name . ' ' . $v->last_name;
								}
								?>
							</td>
							<td><?php if ($row['status'] == 1) {
								echo 'Gửi thành công';
							}else{
								echo 'Gửi thất bại';
							} ?>
								
							</td>
							<td style="text-align:center">
							<a class="view" title="title" href="<?php echo  site_url('reports/viewsms/'. $row['id']) ?>"  data-toggle="modal" data-target="#myModal">Xem
							<span class="hidden"><?php echo $row['status']; ?></span>
							</a>
							</td>
							</tr>
						
						
						<?php } ?>
					</tbody>
				</table>
				</div>	
			</div>


			<div class="panel-body Thuchi <?php echo ($tab == 6) ? '' : 'hidden';?>">
				<div class="table-responsive">
				<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
					<thead>
						<tr>
							<td><?php echo lang('ma_thu_chi'); ?></td>
							<td><?php echo lang('ngay_thu'); ?></td>
							<td><?php echo lang('ghi_chu'); ?></td>
							<td><?php echo lang('khoan_thu'); ?></td>
							<td><?php echo lang('khoan_chi'); ?></td>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
				</div>	
			</div>


			<div class="panel-body Congno <?php echo ($tab == 7) ? '' : 'hidden';?>">
				<div class="table-responsive">
				<table class="table table-hover detailed-reports table-reports table-bordered  tablesorter" id="sortable_table">
					<thead>
						<tr>
							<td>ID</td>
							<td>Thời gian</td>
							<td>ID đơn hàng</td>
							<td>Sổ ghi nợ</td>
							<td>Số tiền đã thanh toán</td>
							<td>Bảng cân đối</td>
							<td>Sản phẩm</td>
							<td>Ghi chú</td>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($datas as $row) { ?>
							<tr>
							<td style="text-align:center;"><?php echo $row['sno'];?></td>
							<td>
							<?php echo date(get_date_format().'-'.get_time_format(), strtotime($row['date'])); ?>
							</td>
							<td>
							<?php
							echo anchor('sales/receipt/'.$row['sale_id'], $this->config->item('sale_prefix').' '.$row['sale_id'], array('target' => '_blank'));
							?>
							</td>
							<td>
							<?php
							echo ($row['transaction_amount']) > 0 ? to_currency($row['transaction_amount']) : to_currency(0);
							?>
							<td><?php
							echo $row['transaction_amount'] < 0 ? to_currency($row['transaction_amount'] * -1)  : to_currency(0)
							?></td>
							<td><?php echo ($row['balance']) > 0 ? to_currency($row['balance']) : to_currency(0);?></td>
							<td><?php echo $row['items'];?></td>
							<td><?php echo $row['comment'];?></td>
							</tr>

							<?php } ?>
					</tbody>
				</table>
				</div>	
			</div>
			<div class="text-center">
				<button class="btn btn-primary text-white hidden-print" id="print_button"  > 
					<?php echo lang('common_print'); ?> 
				</button>	
			</div>
		</div>
	</div>
</div>

	<?php if(isset($pagination) && $pagination) {  ?>
		<div class="pagination hidden-print alternate text-center" id="pagination_top" >
			<?php echo $pagination;?>
		</div>
	<?php }  ?>
</div>

 <div id="sua_xoa_don_nhap_hang" class="modal fade in" tabindex="-1" data-width="400">
	<div class="modal-dialog">
	    <div class="modal-content">
	    </div>
	</div>
</div>

<script type="text/javascript" language="javascript">
$(document).ready(function()
{	
	$('a.edit').on('click', function() {
	    $.ajax({
	        url: this.href,
	        type: 'GET',
	        dataType:'json',
	        cache: false,
	        success: function(result) {
				$('#sua_xoa_don_nhap_hang').modal('toggle');
	            $('#sua_xoa_don_nhap_hang .modal-content').html(result.html).find('.modal').modal({
	                show: true
	            });
	        }
	    });

	    return false;
	});

	$('#sua_xoa_don_nhap_hang').modal('hide');


	$(".tablesorter a.expand").click(function(event)
	{
		$(event.target).parent().parent().next().find('td.innertable').toggle();
		
		if ($(event.target).text() == '+')
		{
			$(event.target).text('-');
		}
		else
		{
			$(event.target).text('+');
		}
		return false;
	});
	
	$(".tablesorter a.expand_all").click(function(event)
	{
		$('td.innertable').toggle();
		
		if ($(event.target).text() == '+')
		{
			$(event.target).text('-');
			$(".tablesorter a.expand").text('-');
		}
		else
		{
			$(event.target).text('+');
			$(".tablesorter a.expand").text('+');
		}
		return false;
	});
	
});

function print_report()
{
	window.print();
}
$(document).ready(function()
{
	$('#print_button').click(function(e){
		e.preventDefault();
		print_report();
	});
});


$("#Baogia").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');
	$('.panel-body').addClass('hidden');
	$('.Baogia').removeClass('hidden');
	var ids = "2";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});

$("#Muahang").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Muahang').removeClass('hidden');
	var ids = "1";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});


$("#Giaodich").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Giaodich').removeClass('hidden');
});


$("#Guimail").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Guimail').removeClass('hidden');
	var ids = "4";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});

$("#Guisms").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Guisms').removeClass('hidden');
	var ids = "5";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});

$("#Thuchi").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Thuchi').removeClass('hidden');
	var ids = "6";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});


$("#Congno").click(function(){
	$('span').removeClass('selected');
	$(this).addClass('selected');

	$('.panel-body').addClass('hidden');
	$('.Congno').removeClass('hidden');
	var ids = "7";
	$.ajax({
		type: "post",
		url: '<?php echo site_url('reports/tab');?>/' + ids,
		success: function(data){
		}
	});
});

$('.view').click(function(){
	$(this).attr('href');
});	

$("#deletedItem").click(function(){

	var item_id = $(this).attr("data-id-item");
	var url = $('#urlid').attr("data-url");
	bootbox.confirm('<?php echo lang('customers_quotes_contract_confirm_delete'); ?>', function(result){
		if(result){
			$.ajax({
				type: "post",
				url: '<?php echo site_url('reports/deletedItem') ?>/' + item_id,
				success: function() {
					window.location;
				}
			});
		}
	});
});



</script>
<style type="text/css">
	.view:hover{
		text-decoration: underline;
	}
</style>
<?php $this->load->view("partial/footer"); ?>