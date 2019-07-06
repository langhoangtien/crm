<?php $this->load->view("partial/header");
	$controller_name="items";
 ?>
	
	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 <?php echo lang('receivings_list_of_suspended'). ' '.lang('common_and'). ' '.lang('receivings_purchase_orders'); ?>
					</h3>
				</div>
				<div class="panel-body nopadding table_holder table-responsive">
					<div class="col-lg-12" style="padding-top: 20px;">
						<div id="filter-by-category-block" class="col-lg-4 form-group" style="padding-left: 0px;">
							<label for="by-category">Lọc Loại Đơn Hàng</label>
						</div>
					</div>
	
						<table class="table table-bordered table-striped table-hover data-table" id="dTable">
						<thead>
							<tr>
								<th><?php echo lang('receivings_id'); ?></th>
								<th><?php echo lang('common_date'); ?></th>
								<th class="hide">Loại Đơn Hàng</th>
								<th><?php echo lang('common_supplier'); ?></th>
								<th><?php echo lang('reports_items'); ?></th>
								<th><?php echo lang('common_comments'); ?></th>
								<th><?php echo lang('common_unsuspend'); ?></th>
								<th><?php echo lang('receivings_receipt'); ?></th>
								<th>Nhập kho</th>
								<th><?php echo lang('common_delete'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
					foreach ($suspended_receivings as $suspended_receiving)
					{
					?>
						<tr>
							<td>RECV <?php echo $suspended_receiving['receiving_id'];?></td>
							<td><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($suspended_receiving['receiving_time']));?></td>
							<td class="hide">
								<?php if ($suspended_receiving['is_stock_in'] == 1) { 
									echo 'Đã nhập hàng';
								} elseif ($suspended_receiving['is_stock_in'] == 2){
									echo 'Đang nhập hàng';
								} else {
									echo 'Chờ nhập hàng';
								}
								
									
								?>							
								</td>
							<td>
								<?php
								if (isset($suspended_receiving['supplier_id']))
								{
									$supplier = $this->Supplier->get_info($suspended_receiving['supplier_id']);
									echo $supplier->company_name.' ('.$supplier->first_name. ' '. $supplier->last_name.')';
								}
								else
								{
								?>
									&nbsp;
								<?php
								}
								?>
							</td>
							<td><?php echo $suspended_receiving['items'];?></td>
							<td><?php echo $suspended_receiving['comment'];?></td>
							<td >
								
									<?php 
									echo form_open('receivings/unsuspend');
									echo form_hidden('suspended_receiving_id', $suspended_receiving['receiving_id']);
									?>
									<input type="submit" name="submit" value="<?php echo lang('common_unsuspend'); ?>" id="submit_unsuspend" class="btn btn-primary">
									<?php echo form_close(); ?>
								
							</td>
							<td>
								<?php 
								echo form_open('receivings/receipt/'.$suspended_receiving['receiving_id'], array('method'=>'get', 'class' => 'form_receipt_suspended_recv'));
								?>
								<input type="submit" name="submit" value="<?php echo lang('common_recp'); ?>" id="submit_receipt" class="btn blue">
								<?php echo form_close(); ?>
							</td>
							
							<td>
							<?php 
							
							if ($suspended_receiving['is_stock_in'] != 1) {
								echo '<a style="margin:0 5px;" href="'. site_url("stock_in?recvId=" . $suspended_receiving['receiving_id']) .'" class="btn btn-primary">   Nhập kho</a>';
							} else {

								echo '<input type="button" class="btn yellow" disabled value="Đã nhập kho">';
							}
							
							?>
							</td>
							<td>
								<?php if (empty($suspended_receiving['is_stock_in'])) {  ?>
								<?php
							 	echo form_open('receivings/delete_suspended_receiving', array('class' => 'form_delete_suspended_recv'));
								echo form_hidden('suspended_receiving_id', $suspended_receiving['receiving_id']);
								?>
								<input type="submit" name="submitf" value="<?php echo lang('common_delete'); ?>" id="submit_delete" class="btn btn-danger">
								<?php echo form_close(); ?>
								<?php } ?>
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
<?php $this->load->view("partial/footer"); ?>



<script type="text/javascript">
	
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

$('#dTable').dataTable({
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
	"sPaginationType": "bootstrap"
});
</script>