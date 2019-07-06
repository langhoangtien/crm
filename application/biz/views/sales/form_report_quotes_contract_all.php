<?php $this->load->view("partial/header"); ?>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/biz/ckeditor/ckeditor.js'; ?>"></script>
<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display: none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	
	<div class="col-md-12">
		<?php echo form_open('', array('id' => 'form_report_quotes_constract','class'=>'form-horizontal')); ?>
		<input type="hidden" name="sale_id" value="<?php echo $sale_id; ?>" />
		<input type="hidden" name="ds_id_hd" id="ds_id_hd" value="<?php echo (!empty($_GET['ds'])) ? $_GET['ds'] : '0'; ?>">
		
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i> 
		            <?php echo lang('sale_export_email_file'); ?>
				</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
								<?php 
								echo form_label(lang('sale_select_quote_type').' :', '',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<select name="select_quotes_contract" id="select_quotes_contract" class="form-control form-inps">
										<option value="0">-- <?php echo lang('sale_select_quote_type'); ?> --</option>
                                <?php
                                if(!empty($qc_type)) {
                                    foreach($qc_type as $val) {
                               ?>
                                        <option value="<?php echo $val['id']; ?>"><?php echo $val['title']; ?></option>
                                <?php
                                    }
                                }
                                ?>
									</select>
									<span for="select_quotes_contract" class="text-danger errors"></span>
								</div>
								
								
						</div>
						<div class="form-group" style="display: none;">
								<?php 
								echo form_label(lang('sale_select_quote').' :', '',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
				                    <select id="select_quote" name="select_quote" class="form-control form-inps">
				                    	<option value="0">-- <?php echo lang('sale_select_quote'); ?> --</option>
					                </select>
									<span for="select_quote" class="text-danger errors"></span>
								</div>
						</div>

					</div>
				</div>
				
				<div class="form-actions pull-right">
					<?php
					echo form_button(array(
					    'name' => 'cancel',
					    'id' => 'cancel',
						 'class' => 'submit_button btn btn-danger',
					    'value' => 'true',
					    'content' => 'Quay lại'
					));
					?>
					<input id="btn_export" type="button" value="Thực hiện" style="width: 86px; height: 32px;" class=" submit_button btn btn-primary"/>
				</div>
			</div>
		</div>
			<?php echo form_close();?>
	</div>
	<!-- /row -->
</div>
</div>
<script type="text/javascript">
	CKEDITOR.config.allowedContent = true;
	CKEDITOR.config.removeFormatAttributes = '';
	CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
    function CK_jQ() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }

  //   function exportData(data) {
		// if(data.flag == 'false') {
		// 	$.each(data.errors, function( index, value ) {	
		// 		element = $( '[name="'+index+'"]' );
		// 		group = element.closest('.form-group');
		// 		group.addClass('has-error');
		// 		group.find('span[for="'+index+'"]').text(value);
		// 	});
		// }else {
		// 	window.location.href = BASE_URL + 'sales/do_make_quotes_contract_type/?sale='+data.sale+'&quote='+data.quote+'&ds_id='+data.ds_id;
		// 	toastr.success(data.msg, 'Thông báo');
		// }
  //   }

    function reset_error() {
    	$('.has-error').removeClass('has-error');
    	$('span.errors').text('');
    }

  //   $( document ).ready(function() {
  //       $( "#btn_export" ).click(function() {
  //       	reset_error();
  //       	CK_jQ();
  //       	var checkOptions = {
		//         url : '<?php echo 'sales/export_constract/'; ?>',
		//         dataType: "json",  
		//         success: exportData
		//     };
		//     $("#form_report_quotes_constract").ajaxSubmit(checkOptions); 
		//     return false; 
  //     	});

  //       $( "#cancel" ).click(function() {
  //           window.location.href = BASE_URL + 'sales/suspended';
  //     	});

		$("body").on('change', '#select_quotes_contract', function(){
			reset_error();
		    $.ajax({
              type:"POST",
              url: '<?php echo site_url("sales/quotes_contract_type_get"); ?>',
              data: {area:$('#select_quotes_contract').val()},
              success: function(string) {
				  var form_group   = $('#select_quote').closest('.form-group');;
				  var value = $('#select_quotes_contract').val();
				  
				  if(value != '0') {
					  form_group.show();
				  }else{
					  form_group.hide();
				  }
		 
				  $( "#select_quote" ).replaceWith(string );
              }
            });
		});
  //   }); 


$('#btn_export').click(function(){
	var sale_id = <?php echo $sale_id ?>;
	var select_quote = $('#select_quote').val();
	window.location.href = BASE_URL + '/sales/export_constract?sale_id='+sale_id+'&select_quote='+select_quote;

});

</script>
<?php $this->load->view("partial/footer"); ?>

<style type="text/css">
    #table_char{
        width: 90%;
        border-collapse: collapse;
        float: right;
    	margin-right: 10px;
    }
    #table_char tr th{
        text-align: center;
        border: 1px solid #CDCDCD;
        padding: 5px 0px;
    	width: 50%;
    }

    #table_char tr td{
        padding: 5px;
        border: 1px solid #CDCDCD;
        vertical-align: top;
    }
    .li_char{
        padding: 4px 0px;
    }
</style>