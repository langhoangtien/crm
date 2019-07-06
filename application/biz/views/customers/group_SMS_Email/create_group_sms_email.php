
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo lang('customers_group_send_email_SMS_new'); ?></h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="frm_create_cus_gr" id="frm_create_cus_gr" class="form-horizontal" enctype="multipart/form-data">
                <div class="clearfix hang">
                    <div class="row">
						<div class="spinner" id="grid-loader" style="display:none">
						  <div class="rect1"></div>
						  <div class="rect2"></div>
						  <div class="rect3"></div>
						</div>
                        <div class="col-lg-12">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label"><?php echo lang('customers_create_group_name_label');?></label>
                                <div class="col-md-3 col-lg-3">
                                    <input id="txt_group_name" type="text" maxlength = "120" name="txt_group_name">
                                </div>
								 <label id="lb_group_description" class="col-md-3 col-lg-2 control-label"><?php echo lang('customers_create_group_description');?></label>
                                <div class="col-md-3 col-lg-3">
                                    <input id="txt_group_description" type="text" maxlength = "120" name="txt_group_name">
                                </div>
							</div>
							 <div class="form-group" style="margin-bottom: 0;">	
								<label class="col-md-3 col-lg-2 control-label"><?php echo lang('customers_add_to_group_name_label');?></label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="group_id" id="group_id" class="form-control">
											<option value="0">---<?php echo lang('customers_add_to_group_name_label');?>---</option>
										<?php
										foreach($list_customer_group as $group) {
										?>
											<option value="<?php echo $group->smsmail_group_id; ?>"><?php echo $group->name; ?></option>
										<?php
										}
										?>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <div class="form-acions">
							<?php
									echo form_submit(array(
										'name'=>'submit',
										'id'=>'submit',
										'value'=>lang('common_submit'),
										'class'=>'btn btn-primary btn-block float_right btn-lg')
									);

							?>
						</div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$('#submit').click(function()
	{
		create_group();
	});
	$('#txt_group_name').focusin(function()
	{
		$('#group_id').val('0');
		$('#lb_group_description,#txt_group_description').fadeIn(400);
		
	});
	$('#group_id').focusin(function()
	{
		$('#txt_group_name').val('');
		$('#lb_group_description,#txt_group_description').fadeOut(400);
	});
    function create_group() {
        var txt_group_name = $('#txt_group_name').val();
		var slbx = $('#group_id').val();
		var txt_group_description = $('#txt_group_description').val();
        if(txt_group_name <= 0 && slbx == 0 )
            toastr.warning('<?php echo  lang('customers_check_group_name');?>', 'Warning');
        else {
            $.ajax({
                type: "POST",
                url: BASE_URL + 'customers/save_group_customer',
                data: {
                    txt_group_name   : txt_group_name,
					slbx :slbx,
					txt_group_description:txt_group_description,
					session: '<?php echo $session;?>',
                },
                beforeSend: function() {
                    $('.mask').show();
                },
                success: function(data){
                    $('.mask').hide();
                    var result = $.parseJSON(data);
                    if(result.flag == 'false')
					{
						toastr.error(result.msg, 'Error');
					}
                    else
                    {
						toastr.success(result.msg,'Success');
						window.location = <?php echo json_encode(site_url('customers/manage_group_send_SMS_email')); ?>;
					}
                    $('#quick_modal').modal('toggle');
                }
            });
        }
    }

</script>