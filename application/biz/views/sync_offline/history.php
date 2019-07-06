<?php $this->load->view("partial/header");
 ?>
	<div class="container-fluid">
		<div class="spinner" id="grid-loader" style="display :none">
	        <div class="rect1"></div>
	        <div class="rect2"></div>
	        <div class="rect3"></div>
	    </div>
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 <span>Dữ liệu OFFLINE</span>
					</h3>
				</div>
				<div style="padding: 15px;" class="panel-body nopadding table_holder table-responsive">
					<div class="col-md-12" style="padding-top: 20px;">
						<?php 
							echo form_open('sync_offline/history', array('method'=>'get', 'class' => ''));
						?>
						<div class="col-md-3">
								<div class="input-group input-daterange" id="reportrange">
                                    <span class="input-group-addon bg">
			                           <?php echo lang('reports_from'); ?>
			                       	</span>
                                    <input type="text" class="form-control start_date" name="start_date" id="start_date" value="<?php echo $start_date;?>">
                                </div>
							</div>
							<div class="col-md-3">
								<div class="input-group input-daterange" id="reportrange1">
                                    <span class="input-group-addon bg">
	                                    <?php echo lang('reports_to'); ?>
	                                </span>
                                    <input type="text" class="form-control end_date" name="end_date" id="end_date" value="<?php echo $end_date;?>">
                                </div>	
							</div>
							<div class="col-md-3">
 								<div class="form-actions pull-left"> 
									<button style="height: 38px;" type="submit" id="search" class="btn btn-primary submit_button">Thực hiện</button>
								</div>
							</div>
						</div>
						<?php echo form_close(); ?>
					<div class="col-md-12" style="padding-top: 20px;">
						<table class="transfer_pending table table-bordered table-striped table-hover data-table" id="dTableA">
							<thead>
								<tr>
									<th><?php echo '#ID'; ?></th>
									<th><?php echo 'Thời gian'; ?></th>
									<th><?php echo 'Khách hàng'; ?></th>
									<th><?php echo 'Sản phẩm'; ?></th>
									
									<th><?php echo 'Thanh toán'; ?></th>
								</tr>
							</thead>
							<tbody>
							<?php if($offline_sale) {?>
									<?php foreach ($offline_sale as $sale) {?>
									<tr>
										<td><?php echo $sale['sale_id']; ?></td>
										<td><?php echo $sale['sale_time']; ?></td>
										<td><?php echo $sale['first_name'] . ' ' . $sale['last_name']?></td>
										<td><?php echo $sale['items']; ?></td>
										<td><?php echo $sale['payment_type']; ?></td>
									</tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
<script type='text/javascript'>

var SYNC_OFFLINE = {
		_datatable : null,
		init: function()
		{
			SYNC_OFFLINE.initDataTable();
			
			date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT);
			date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT);
		},
		initDataTable: function()
		{
			SYNC_OFFLINE._datatable = $('#dTableA').DataTable({
				"sPaginationType": "bootstrap"
			});
		}
	}
    // Validation and submit handling
    $(document).ready(function () {
    	SYNC_OFFLINE.init();
    	
        $(".wrapper").addClass("mini-bar");
        $(".wrapper.mini-bar .left-bar").hover(
            function() {
                $(this).parent().removeClass('mini-bar');
            }, function() {
                $(this).parent().addClass('mini-bar');
            }
        );
        var submitting = false;
        $('#offline_sale_form').validate({
            submitHandler: function (form) {
                if (submitting) return;
                submitting = true;
                $('#grid-loader').show();
                $(form).ajaxSubmit({
                    success: function (response) {
                        $('#grid-loader').hide();
                        if (!response.success) {
                            show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
                        }
                        else {
                            // $("#import-result").html(response.html);
                        	location.reload();
                        }
                        submitting = false;
                    },
                    dataType: 'json',
                    resetForm: false
                });

            },
            errorLabelContainer: "#error_message_box",
            wrapper: "li",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass('error');
                $(element).parents('.form-group').addClass('success');
            },
            rules: {
                file_path:"required"
            },
            messages: {
                file_path:<?php echo json_encode(lang('common_full_path_to_excel_file_required')); ?>
            }
        });
    });
</script>
<?php $this->load->view("partial/footer"); ?>