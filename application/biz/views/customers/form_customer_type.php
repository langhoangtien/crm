<?php $this->load->view("partial/header");?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<?php

$name        =  $item['name'];
$code        =  $item['code'];
$desc        =  $item['desc'];

?>
<div class="row">
    <div class="col-md-12">
        <?php echo form_open('', array('id' => 'frm_customer_type','class'=>'form-horizontal')); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon-edit"></i>
                    Thông tin loại khách hàng
                    <small>(<?php echo lang('common_fields_required_message'); ?>)</small>
                </h3>
            </div>

            <div class="panel-body">
                <div class="row ">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                            echo form_label('Tên :', 'name',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>

                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                        'name'=>'name',
                                        'class'=>'form-control',
                                        'value'=>$name)
                                );?>
                                <span for="name" class="text-danger errors"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php
                            echo form_label('Mã :', 'code',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>

                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                        'name'=>'code',
                                        'class'=>'form-control',
                                        'value'=>$code)
                                );?>
                                <span for="code" class="text-danger errors"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php
                            echo form_label('Mô tả :', 'desc',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <textarea name="desc" cols="17" rows="5" class="form-control  text-area"><?php echo $desc; ?></textarea>
                                <span for="desc" class="text-danger errors"></span>
                            </div>
                        </div>

                        <div class="form-actions pull-right">
                            <button name="cancel" type="button" onclick="back_list();" class="submit_button btn btn-danger" value="true" style="margin-right: 5px;">Quay lại</button>
                            <input type="button" name="submitf" value="Thực hiện" id="submitf" onclick="save_item();" class="submit_button btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close();?>
        </div>

    </div>
</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<script type="text/javascript">
    var page = <?php echo $page; ?>;

    function reset_form_error(form_id) {
        $('#'+form_id+' .has-error').removeClass('has-error');
        $('#'+form_id+' p.errors').text('');
    }

    function save_item() {
        reset_form_error('frm_customer_type');
        var url = BASE_URL + 'customers/type_list_save';

        var checkOptions = {
            url : url,
            dataType: "json",
            success: save_item_data
        };

        $("#frm_customer_type").ajaxSubmit(checkOptions);
        return false;
    }

    function save_item_data(data) {
        if(data.flag == 'false') {
            var first_key = Object.keys(data.errors)[0];
            $.each(data.errors, function( index, value ) {
                element = $( '#frm_customer_type [name="'+index+'"]' );
                group = element.closest('.form-group');
                group.addClass('has-error');
                group.find('span[for="'+index+'"]').text(value);
            });

            $( '#frm_service [name="'+first_key+'"]' ).focus();

        }else {
            back_list();
        }
    }

    function back_list() {
        var url_redirect = BASE_URL + 'customers/type_list';
        if(page > 1)
            url_redirect = url_redirect + '/' + page;

        window.location.href = url_redirect;
    }


</script>
<?php $this->load->view("partial/footer");?>
