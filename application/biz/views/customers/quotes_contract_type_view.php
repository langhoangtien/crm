<?php $this->load->view("partial/header");?>
<?php
    $title  = $item['title'];
    $code   = $item['code'];
    $status = $item['status'];
    if(!isset($item))
        $status = 1;

    $linkRedirect = base_url() . 'customers/quotes_contract_type_list';
    if($page > 1)
       $linkRedirect = $linkRedirect . '/' . $page;
?>
<div class="row">
	<div class="col-md-12">
		<?php echo form_open('customers/quotes_constract_type_save/', array('id' => 'quotes_contract_form','class'=>'form-horizontal')); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="icon-edit"></i>
					<?php echo lang('customers_quotes_contract_type_add_info');?>
					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
				</h3>
			</div>
		
		<div class="panel-body">
			<div class="row ">
				<div class="col-md-12">
					<div class="form-group">
						<?php 
						echo form_label(lang('customers_quotes_contract_title').' :', 'title',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>

						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'title',
								'id'=>'title',
								'class'=>'form-control',
								'value'=>$title)
							);?>
                            <span for="title" class="text-danger errors"></span>
						</div>
					</div><!-- end form-group -->

					<div class="form-group">
						<?php
						echo form_label(lang('customers_quotes_contract_code').' :', 'code', array('class' => 'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'code',
								'id'=>'code',
								'class'=>'form-control',
								'value'=>$code
							));?>
                            <span for="code" class="text-danger errors"></span>
						</div>						
					</div>

					<div class="form-group">
						<?php 
						echo form_label(lang('customers_quotes_contract_status').' :', 'status', array('class' => 'wide col-md-3 col-sm-3 col-lg-2 control-label'));
						?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							
						<?php echo form_checkbox(array(
                            'name'=>'status',
                            'id'=>'status',
                            'class'=>'status-checkbox',
                            'value'=>1,
                            'checked'=>$status)
                        );?>
                        <label for="status"><span></span></label>

						</div>
					</div><!-- end -->
                    <div class="form-actions pull-right">
                        <input type="button" name="submitf" value="Thực hiện" id="submitf" class="submit_button btn btn-primary">
                    </div>
				</div>
			</div>
		</div>
		<?php echo form_close();?>
		</div>

	</div>
</div>
<?php $this->load->view("partial/footer");?>
<script type="text/javascript">
function n9_ajax_process(data) {
    if(data.flag == 'false') {
        var first_key = Object.keys(data.errors)[0];
        $.each(data.errors, function( index, value ) {
            element = $( '[name="'+index+'"]' );
            group = element.closest('.form-group');
            group.addClass('has-error');
            group.find('span[for="'+index+'"]').text(value);
        });

        $( '[name="'+first_key+'"]' ).focus();
    }else {
        window.location.href = '<?php echo $linkRedirect; ?>';
    }
}
$( document ).ready(function() {
    $( "#submitf" ).click(function() {
        var checkOptions = {
            url : BASE_URL+'customers/quotes_constract_type_save',
            dataType: "json",
            success: n9_ajax_process
        };
        $('.has-error').removeClass('has-error');
        $('span.errors').text('');
        $('#quotes_contract_form').ajaxSubmit(checkOptions);
        return false;
    });

    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            $( "#submitf" ).trigger( "click" );
            event.preventDefault();

            return false;
        }
    });
});
</script>