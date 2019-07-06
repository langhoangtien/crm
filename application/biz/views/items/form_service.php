<?php $this->load->view("partial/header");?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<?php
    $name          = $item['name'];
    $code          = $item['code'];
    $description   = $item['description'];
    $min_profit               = isset($item['min_profit']) ? (float)($item['min_profit']) : 0;
    $min_profit_commission    = isset($item['min_profit_commission']) ? (float)($item['min_profit_commission']) : 0;

    $sale_template = $item['sale_template'];
    $override_profit_commission = $item['override_profit_commission'];

    $quantity = 1;
    if(isset($item['document']) && !empty($item['document'])) {
        $document = explode(',', $item['document']);
        $quantity = count($document) + 1;
    }

    if($override_profit_commission == 1)
       $style = ' style="display: block"';
    else
       $style = ' style="display: none"';
   $service_id = $this->uri->segment(3);
   $code_dv = $this->Service->get_item_service($service_id)['name'];
   // echo "<pre>";
   // print_r($this->Service->get_item_service($service_id)); die();
   // echo "<pre>";
   // print_r($list_items_cate); die();
?>
<div class="row">
    <div class="col-md-12">
        <?php echo form_open('', array('id' => 'frm_service','class'=>'form-horizontal')); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon-edit"></i>
                    Thông tin dịch vụ
                    <small>(<?php echo lang('common_fields_required_message'); ?>)</small>
                </h3>
            </div>

            <div class="panel-body">
                <div class="row ">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                            echo form_label('Tên dịch vụ :', 'name',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>

                            <div class="col-sm-9 col-md-9 col-lg-10 input-group date">
                                <?php 
                                    // echo form_input(array(
                                    //     'name'=>'name',
                                    //     'class'=>'form-control',
                                    //     'value'=>$name)
                                    // );
                                ?>
                                <select name="name" class="form-control form-inps">
                                    <option value=""></option>
                                    <?php
                                        foreach ($list_items_cate as $val) {
                                     ?>  
                                        <option value="<?php echo $val[product_id] ?>" <?php if($code_dv==$val['product_id']) echo 'selected'; ?>><?php echo $val['name'] ?></option>
                                     <?php      
                                         } 
                                    ?>
                                    
                                </select>
                              
                                <span for="name" class="text-danger errors"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php
                            echo form_label('Mã loại văn bản:', 'code',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>

                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php 
                                    // echo form_input(array(
                                    //     'name'=>'code',
                                    //     'class'=>'form-control',
                                    //     'value'=>$code)
                                    //)
                                ;?>
                                <!-- <span for="code" class="text-danger errors"></span> -->
                                <select id="code" name="code" class="form-control form-inps" onchange="edit_code()">
                                    <option value=""></option>
                                    <?php
                                    
                                    foreach ($data_code as $k => $val) {
                                    ?>
                                        <option value="<?php echo $val['code'] ?>" <?php if($code==$val['code']) echo 'selected'?>><?php echo $val['code'].' - '.  $val['title'] ;?></option>
                                    <?php        
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php
                            echo form_label('Mô tả :', 'description',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <textarea name="description" cols="17" rows="5" class="form-control  text-area"><?php echo $description; ?></textarea>
                                <span for="description" class="text-danger errors"></span>
                            </div>
                        </div>

                        <div class="form-group" style="display: none;">
                            <?php
                            echo form_label('Thay đổi cấu hình lợi nhuận và hoa hồng :', 'override_profit_commission',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <select name="override_profit_commission" id="override_profit_commission" class="select_form form-control">
                                    <option value="0"<?php if($override_profit_commission == 0) echo ' selected'; ?>>Không</option>
                                    <option value="1"<?php if($override_profit_commission == 1) echo ' selected'; ?>>Có</option>
                                </select>
                                <span for="override_profit_commission" class="text-danger errors"></span>
                            </div>
                        </div>

                        <div id="profit_commission_section"<?php echo $style; ?>>
                            <div class="form-group">
                                <?php
                                echo form_label('Phần trăm tối thiểu để tính lợi nhuận :', 'min_profit',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>

                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <?php echo form_input(array(
                                            'name'=>'min_profit',
                                            'class'=>'form-control',
                                            'value'=>$min_profit)
                                    );?>
                                    <span for="min_profit" class="text-danger errors"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('Lợi nhuận tối thiểu để tính hoa hồng :', 'min_profit_commission',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>

                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <?php echo form_input(array(
                                            'name'=>'min_profit_commission',
                                            'class'=>'form-control',
                                            'value'=>$min_profit_commission)
                                    );?>
                                    <span for="min_profit_commission" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>
<?php if(isset($document)): ?>
    <?php
    foreach($document as $stt => $selected) {
?>
                        <div class="form-group">
                            <?php
                            echo form_label('Văn bản :', '',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10 margin-bottom-10">
                                <div class="input-group date">
                                    <span class="input-group-addon" onclick="remove(this);" style="background: #489ee7; color: white;"><i class="ion-trash-b"></i></span>
                                    <select name="document[]" id="document_<?php echo $stt; ?>">
                                        <?php
                                        foreach($slb_template as $key => $val) {
                                            ?>
                                            <option value="<?php echo $key; ?>"<?php if($selected == $key) echo ' selected'; ?>><?php echo $val; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $('#document_<?php echo $stt; ?>').select2();
                            </script>
                        </div>

<?php
    }
    ?>
<?php endif; ?>
                        <div class="form-group" style="margin-top: -10px;">
                            <input type="hidden" name="quantity" id="quantity" value="<?php echo $quantity; ?>" />
                            <label for="email" class="col-sm-3 col-md-3 col-lg-2 control-label" style="visibility: hidden;">Tạo thêm nhóm :</label>
                            <div class="col-sm-9 col-lg-10">
                                <a href="javascript:void(0);" onclick="frm_add_document();" id="btn_add_group_section">Thêm văn bản</a>
                            </div>
                        </div>

                        <div class="form-actions pull-right">
                            <button name="cancel" type="button" onclick="back_list();" class="submit_button btn btn-danger" value="true" style="margin-right: 5px;">Quay lại</button>
                            <input type="button" name="submitf" value="Thực hiện" id="submitf" onclick="save_service();" class="submit_button btn btn-primary">
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
    var code = '<?php echo $code; ?>';
    $( document ).ready(function() {
        $('#sale_template').select2();
    });

    function remove(obj) {
        var form_group = $(obj).closest('.form-group');
        form_group.remove();
    }

    function reset_form_error(form_id) {
        $('#'+form_id+' .has-error').removeClass('has-error');
        $('#'+form_id+' p.errors').text('');
    }

    function save_service() {
        reset_form_error('frm_service');
        var url = BASE_URL + 'items/service_save';

        var checkOptions = {
            url : url,
            dataType: "json",
            success: save_service_data
        };

        $("#frm_service").ajaxSubmit(checkOptions);
        return false;
    }

    function save_service_data(data) {
        if(data.flag == 'false') {
            var first_key = Object.keys(data.errors)[0];
            $.each(data.errors, function( index, value ) {
                element = $( '#frm_service [name="'+index+'"]' );
                group = element.closest('.form-group');
                group.addClass('has-error');
                group.find('span[for="'+index+'"]').text(value);
            });

            $( '#frm_service [name="'+first_key+'"]' ).focus();

        }else if(data.flag == 'error-document') {
            toastr.error(data.msg, 'Lỗi');
        }else {
            back_list();
        }
    }

    function back_list() {
        var url_redirect = BASE_URL + 'items/services';
        if(page > 1)
            url_redirect = url_redirect + '/' + page;

        window.location.href = url_redirect;
    }
    function edit_code(){
        code = document.getElementById("code").value;
    }
    function frm_add_document() {
        var quantity = parseInt($('#quantity').val());
        $.ajax({
            type: "POST",
            url: BASE_URL + 'items/document_input',
            data: {
                quantity          : quantity,
                code : code
            },
            beforeSend: function() {
                $('.mask').show();
            },
            success: function(html){
                $('.mask').hide();
                var btn_parent = $('#btn_add_group_section').closest('.form-group');
                btn_parent.before(html);

                quantity = quantity + 1;
                $('#quantity').val(quantity);
            }
        });
    }
</script>
<script type="text/javascript">
    $( document ).ready(function() {
        $('body').on('change','#override_profit_commission',function(){
            var select_value = $(this).val();
            if(select_value == 1)
                $('#profit_commission_section').show();
            else
                $('#profit_commission_section').hide();
        });
    });
</script>
<?php $this->load->view("partial/footer");?>
