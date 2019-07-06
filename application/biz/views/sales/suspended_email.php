<?php $this->load->view("partial/header"); ?>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/biz/ckeditor/ckeditor.js'; ?>"></script>
<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display: none">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
	</div>
	
	<div class="col-md-12">
		<?php echo form_open(base_url() . 'sales/send_sale_mail', array('id' => 'form_sale_mail','class'=>'form-horizontal', 'enctype'=>'multipart/form-data')); ?>
        <input type="hidden" name="sale_id" value="<?php echo $sale_id; ?>" />
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="ion-edit"></i> 
		            <?php echo lang('sale_email'); ?>
				</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
                        <div class="form-group" style="">
                            <label class="required col-md-3 col-lg-2 control-label">Tiêu đề</label>
                            <div class="col-md-9 col-lg-10">
                                <input type="text" name="title" id="title" value="" class="form-control">
                                <span for="title" class="text-danger"></span>
                            </div>
                        </div>
						<div class="form-group">
								<?php 
								echo form_label(lang('sale_select_template').' :', '',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
				                    <select id="select_template" name="select_template" class="form-control form-inps">
				                    	<option value="0">-- <?php echo lang('sale_select_template'); ?> --</option>
<?php 
			foreach($list_mail as $val) {
?>
										 <option value="<?php echo $val['id']; ?>"><?php echo $val['title']; ?></option>
<?php 
			}
?>   
					                </select>
									<span for="select_template" class="text-danger errors"></span>
								</div>
						</div>
						
						<div class="form-group" style="margin-bottom: 28px;">
							<label class="col-md-3 col-lg-2 control-label">File Upload</label>
							<div class="col-md-9 col-lg-10">
								<input type="file" name="file_upload" id="file_upload" class="filestyle file_upload" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
								<span for="file_upload" class="text-danger"></span>
							</div>
						</div>
						
						<div class="form-group" style="display: none;">
							<label class="col-md-3 col-lg-2 control-label"><?php echo lang('file_name'); ?></label>
							<div class="col-md-9 col-lg-10">		
								<input type="text" name="file_name" id="file_name" value="" class="form-control">
								<span for="file_name" class="text-danger"></span>
							</div>
						</div>

                        <div class="form-group">
                            <label class="col-md-3 col-lg-2 control-label">Khách hàng</label>
                            <div class="col-md-9 col-lg-10">
                                <input type="text" name="customer_name" value="<?php echo $sale_info['customer']; ?>" class="form-control" readonly="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 col-lg-2 control-label">Email</label>
                            <div class="col-md-9 col-lg-10">
                                <input type="text" name="email" value="<?php echo $sale_info['customer_email']; ?>" class="form-control">
                                <span for="email" class="text-danger"></span>
                            </div>
                        </div>
						
						<div class="form-group">
							<?php 
							echo form_label('Nội dung:', 'content_email',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_textarea(array(
									'name'=>'content_email',
									'id'=>'content_email',
									// 'class'=>'form-control text-area ckeditor',
									'class'=>'form-control text-area',
									'value'=>!empty($info_quotes_contract->content_email)?$info_quotes_contract->content_email:'',
									// 'cols'		=> '40',
									// 'rows' =>'14',
								));?>
													<?php echo display_ckeditor($ckeditor);?>						
								<div style="margin-top: 5px;">
									<span for="content_email" class="text-danger"></span>
								</div>
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
					    'content' => lang('common_cancel')
					));
					?>
					<input id="btn_email" type="button" value="Thực hiện" style="width: 86px; height: 32px; margin-left: 8px;" class=" submit_button btn btn-primary"/>
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
    CKEDITOR.config.extraPlugins = 'dialogadvtab';
    function CK_jQ() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }

    function mailData(data) {
        $('.mask').hide();
		if(data.flag == 'false') {
			$.each(data.errors, function( index, value ) {
				element = $( '[name="'+index+'"]' );
				group = element.closest('.form-group');
				group.addClass('has-error');
				group.find('span[for="'+index+'"]').text(value);
			});
		}else {
			window.location.href = BASE_URL + 'sales/suspended';
		}
    }

    function reset_error() {
    	$('.has-error').removeClass('has-error');
    	$('span.errors').text('');
    }

    $( document ).ready(function() {
 
        $( "#btn_email" ).click(function() {
        	reset_error();
        	CK_jQ();
            $('.mask').show();
        	var checkOptions = {
			        url : '<?php echo 'sales/send_sale_mail/'; ?>',
			        dataType: "json",
			        success: mailData
			    };
		    $("#form_sale_mail").ajaxSubmit(checkOptions); 
		    return false; 
      	});

		$("body").on('change', '#select_template', function(){
			reset_error();
            var title       = $( "#select_template option:selected" ).text();
			var template_id = $('#select_template').val();
            $('#title').val(title);
			if(template_id == 0) {
				CKEDITOR.instances['content_email'].setData('');
			}else {
				$.ajax({	
					type: "POST",
					url: BASE_URL + 'sales/load_template_mail',
					beforeSend: function() {
						CKEDITOR.instances['content_email'].setData('');
			       },
					data: {
						template_id : $('#select_template').val(),
						sale_id : <?php echo $sale_id;  ?>
					},
					success: function(string){
				    	CKEDITOR.instances['content_email'].insertHtml(string);
				    }
				});
			}
		});

		$( "#file_upload" ).change(function() {
			 var yourstring = $(this).val();
			 var filename = yourstring.replace(/^.*[\\\/]/, '');
			 var filename = filename.substr(0, filename.lastIndexOf('.')) || filename;

			 if(filename){
				 $('#file_name').closest('.form-group').show();
				 $('#file_name').val(filename);
			 }else
				 $('#file_name').closest('.form-group').hide();
		});
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