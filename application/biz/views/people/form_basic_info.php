<div class="row">
    <div class="col-md-12">
        <?php
        echo set_value('code');

        // echo '<pre>';
        // print_r($person_info);
        // echo '</pre>';
        // die();
        ?>

        <div class="modal fade box-modal" id="my_modal">
        </div>

        <div class="form-group">
            <?php echo form_label('Mã khách hàng' . '', 'code', array('class' => 'required col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
            <div class="col-sm-9 col-md-9 col-lg-9">
                <?php echo form_input(array(
                    'class' => 'form-control input-sm',
                    'name' => 'code',
                    'id' => 'code',
                    'value' => ($person_info->code) ? $person_info->code : set_value('code'),
                    'placeholder' => lang('common_code'),
                )
            ); ?>
            <span for="code" class="alert-danger errors"></span>
        </div>

    </div>

    <div class="form-group">
        <?php
        $required = ($controller_name == "suppliers") ? "" : "required";
        echo form_label('Tên khách hàng' . '', 'last_name', array('class' => $required . ' col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array(
                'class' => 'form-control',
                'name' => 'last_name',
                'id' => 'last_name',
                'value' => $person_info->first_name ? html_entity_decode($person_info->first_name) . ' ' . html_entity_decode($person_info->last_name) : html_entity_decode($person_info->last_name),
                'placeholder' => lang('common_last_name'),
                'maxlength' => 200
            )); ?>
            <span for="last_name" class="alert-danger errors"></span>
        </div>

    </div>
    <!-- điện thoại -->
    <div class="form-group">
        <?php echo form_label('Điện thoại' . '', 'phone_number', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array(
                'class' => 'form-control',
                'name' => 'phone_number',
                'id' => 'phone_number',
                'value' => $person_info->phone_number,
                'placeholder' => lang('common_phone_number'),
                'maxlength' => 15
            )); ?>
            <span for="phone_number" class="alert-danger errors"></span>
        </div>
    </div>
    <!-- email -->
    <div class="form-group">
        <?php echo form_label('Email' . '', 'email', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ' . ($controller_name == 'employees' || $controller_name == 'login' ? 'required' : 'not_required'))); ?>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array(
                'class' => 'form-control',
                'name' => 'email',
                'type' => 'text',
                'id' => 'email',
                'value' => $person_info->email,
                'placeholder' => lang('common_email'),
                'maxlength' => 50
            )
        ); ?>
        <span for="email" class="alert-danger errors"></span>
    </div>
</div>
<!-- giới tính -->
<?php if ($controller_name == "customers") { ?>

    <!-- website-->
    <div class="form-group">
        <?php echo form_label(lang('common_website') . '', 'c_website', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array(
                'class' => 'form-control',
                'name' => 'website',
                'id' => 'c_website',
                'value' => $person_info->website,
                'placeholder' => lang('common_website')
            )); ?>
        </div>
    </div>

    <!-- địa chỉ-->
    <div class="form-group">
        <?php echo form_label(lang('common_address') . '', 'address_1', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array(
                'class' => 'form-control',
                'name' => 'address_1',
                'id' => 'address_1',
                'value' => $person_info->address_1,
                'placeholder' => lang('common_address'),
                    // 'maxlength' => 100
            )); ?>
        </div>
    </div>

    <!-- số tài khoản ngân hàng -->
    <div class="form-group">
        <?php echo form_label(lang('customers_account_number'), 'account_number', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
        <div class="col-sm-9 col-md-9 col-lg-9">
            <?php echo form_input(array(
                'name' => 'account_number',
                'id' => 'account_number',
                'class' => 'company_names form-control',
                'value' => $person_info->account_number,
                'placeholder' => lang('customers_account_number')
            )
        ); ?>
        <span for="account_number" class="alert-danger errors"></span>
    </div>
</div>
<!-- mã số thuế -->
<div class="form-group">
    <?php echo form_label(lang('customers_code_tax'), 'code_tax', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-9">
        <?php echo form_input(array(
            'name' => 'code_tax',
            'id' => 'code_tax',
            'class' => 'form-control',
            'value' => $person_info->code_tax,
            'placeholder' => lang('customers_code_tax'))
        ); ?>
    </div>
</div>

<!-- authorized_capital-->


<!-- số đăng ký kinh doanh -->
<div class="form-group">
    <?php echo form_label(lang('customers_business_registration'), 'business_registration',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-9">
        <?php echo form_input(array(
            'name'=>'business_registration',
            'id'=>'business_registration',
            'class'=>'form-control',
            'value'=>$person_info->business_registration,
            'placeholder'=>lang('customers_code_bussiness_register')
        )
    );?>
</div>
</div>

<!-- first date registration -->
<div class="form-group">
    <?php echo form_label(lang('customers_first_date_registration'), 'first_date_registration', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-9">
        <?php echo form_input(array(
            'class' => 'form-control datepicker',
            'name' => 'first_date_registration',
            'type' => 'text',
            'id' => 'first_date_registration',
            'value' => $person_info->first_date_registration != '1950-01-01' && $person_info->first_date_registration != '' ? date('d-m-Y', strtotime($person_info->first_date_registration != '' ? $person_info->first_date_registration : date('d-m-Y'))) : '')
        ); ?>
    </div>
</div>

<!-- latest change registration -->
<div class="form-group">
    <?php echo form_label(lang('customers_last_updated_registration'), 'last_updated_registration', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-9">
        <?php echo form_input(array(
            'class' => 'form-control datepicker',
            'name' => 'last_updated_registration',
            'type' => 'text',
            'id' => 'last_updated_registration',
            'value' => $person_info->last_updated_registration != '1950-01-01' && $person_info->first_date_registration != '' ? date('d-m-Y', strtotime($person_info->last_updated_registration != '' ? $person_info->last_updated_registration : date('d-m-Y'))) : '')
        ); ?>
    </div>
</div>

<!-- nhân viên quản lý -->
<div class="form-group">
    <?php echo form_label(lang('employee_manager').'', 'created_by',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-9">
        <?php
        if($person_info->person_id)
            $created_by = $person_info->created_by;
        else $created_by = $this->Employee->get_logged_in_employee_info()->person_id;
        ?>
        <?php echo form_dropdown('created_by', $employee_manager,$created_by,'class="form-control" id="created_by"');?>
    </div>
</div>

<!--        người được xem-->
<div class="form-group">
    <?php echo form_label(lang('watcher_manager').'', 'watcher_manager',array('class'=>'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-9">
        <?php
        $requirect_code=  $this->uri->segment(4);
        $employees = $this->Employee->get_list_employees_by_location();
        foreach ($employees as $key => $value) {
            $employee_manager[$value['person_id']] = $value['employee_name'];
        }
        // echo "<pre>";print_r($person_info);
        if(!empty($person_info->person_id)){
            $watcher_manager = $person_info->watcher_manager;
            if ($requirect_code==3) {
                echo form_multiselect('watcher_manager[]', $employee_manager,$watcher_manager,'class="form-control selectize" id="watcher_manager" disabled');
            }else{
                echo form_multiselect('watcher_manager[]', $employee_manager,$watcher_manager,'class="form-control selectize" id="watcher_manager"');
            }
            

            // else $watcher_manager = $this->Employee->get_logged_in_employee_info()->person_id;
        }
        else{
            if (!empty($employees)) {
                foreach ($employees as $key => $value) {
                    $watcher_manager[$value['person_id']] = $value['employee_name'];
                }
            }else{
                $watcher_manager= array();
            }
            if ($requirect_code==3) {
                echo form_multiselect('watcher_manager[]',$watcher_manager,$employee_manager,'class="form-control selectize" id="watcher_manager" disabled');
            }else{
                echo form_multiselect('watcher_manager[]',$watcher_manager,$employee_manager,'class="form-control selectize" id="watcher_manager"');
            }
            
        }
        ?>

    </div>
</div>

<!-- mô tả -->
<div class="form-group">
    <?php echo form_label(lang('common_comments') . '', 'comments', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-9">
        <?php echo form_textarea(array(
            'name' => 'comments',
            'id' => 'comments',
            'class' => 'form-control text-area',
            'value' => $person_info->comments,
            'rows' => '5',
            'cols' => '17')
        ); ?>
    </div>
</div>

<!--            upload file-->
<div class="col-sm-9 col-md-9 col-lg-10">
    <div style="float: left; padding: 0px 15px;">
        <?php  
        if (($this->uri->segment(1) =='customers') && ((int)$this->uri->segment(3)<1)) {

        }else{ ?>
            <p class="btn btn-primary" type="button" onclick="openListFile()">Tài liệu đính kèm</p>
            <!-- <button type="button" data-toggle="modal" data-target="#modal_add_file_customer" class="btn btn-primary">Thêm file</button> -->
        <?php } ?>
    </div>
    <input type="hidden" id="person_id" value="<?php echo $person_info->person_id ?>">
</div>

<?php 
// $this->load->view('customers/addfile/addfile_view');
?>
</div><!-- /col-md-12 -->

<?php } ?>
</div><!-- /row -->


<script lang="javascript">
    function openListFile() {
        var person_id = $('#person_id').val();
        var url = BASE_URL + 'customers/listfile'
        $.ajax({
            type: "GET",
            url: url,
            data: {
                person_id : person_id
            },
            success: function(html){
                $('#my_modal').html(html);
                $('#my_modal').modal('toggle');
            }
        });
    }

        // $(document).on('click','#close_popup',function(){
        //     $('.modal-content').hide();
        // });

        $(function() {
          $('.selectize').selectize({
             plugins: ['remove_button'],
             delimiter: ',',
             persist: false,
             create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
      });
  </script>



  