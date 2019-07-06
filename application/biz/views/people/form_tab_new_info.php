<div class="col-md-7">
    <!-- thêm mới ảnh đại diện -->
    <?php
    $company_form_customize = [];
    $company_form_customize[''] = 'Không';
    foreach ($company_form as $key => $value) {
        $company_form_customize[$key] = $value['name'];
    }
    ?>
    <div class="tab-content">
        <div class="panel-heading header-tab">
            <h3 class="panel-title">
                <i class="ion-ios-person"></i>
                <?php echo lang('common_choose_logo'); ?>
            </h3>
        </div>
        <div class="form-group">
            <?php echo form_label('', 'image_id', array('class' => '')); ?>
            <div class="col-sm-9 col-md-9 col-lg-10">

                <div class="list-unstyled avatar-list" style="padding-top: 15px;">

                    <div style="float: left;">
                        <?php //echo $person_info->image_id ? '<div id="avatar">' . img(array('src' => site_url('app_files/view/' . $person_info->image_id), 'class' => 'avatar', 'style' => "height: 143px;",)) . '</div>' : '<div id="avatar">' . img(array('src' => base_url() . 'assets/img/avatar.png', 'class' => 'img-polaroid', 'style' => "height: 143px;", 'id' => 'image_empty')) . '</div>'; ?>
                        <?php echo $person_info->image_id ? '<div id="avatar">' . img(array('src' => site_url('app_files/view/' . $person_info->image_id), 'class' => 'avatar', 'style' => "height: 143px;",)) . '</div>' : '<div id="avatar"></div>'; ?>
                    </div>
                    <div style="float: left; padding: 50px 15px;">
                        <label for="image_id" class="btn btn-primary filestyle">Chọn logo</label>
                        <div style="display: none">
                            <input type="file" name="image_id" id="image_id" class="filestyle" data-input="false" >
                        </div>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>

            <?php if ($person_info->image_id) { ?>

                <div class="form-group">
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_label(lang('common_del_image') . ' :', 'del_image', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label', 'style' => 'text-align: left')); ?>

                        <?php echo form_checkbox(array(
                            'name' => 'del_image',
                            'id' => 'del_image',
                            'class' => 'delete-checkbox',
                            'value' => 1
                        ));
                        echo '<label for="del_image"><span></span></label> ';

                        ?>
                    </div>
                </div>

            <?php } ?>
        </div>


        <div class="tab-content">
            <div class="panel-heading header-tab">
                <h3 class="panel-title">
                    <i class="ion-edit"></i>
                    <?php echo lang("detail_customers_basic_information"); ?>
                    <small>(<?php echo lang('common_fields_required_message'); ?>)</small>
                    <p></p>
                </h3>
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle='tab' href="#home">Phân loại khách hàng</a></li>
                    <li><a data-toggle='tab' href="#menu1">Người đại diện</a></li>
                    .
                    <!--					<li><a data-toggle='tab' href="#menu2">Tùy chọn</a></li>-->
                </ul>
            </div>
            <!-- menu home -->
            <div id='home' class="tab-pane panel-body fade in active">
                <!-- nhóm khách hàng -->
                <div class="form-group clearfix">
                    <?php echo form_label(lang('customers_group') . '', 'customer_type', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-9">
                        <?php echo form_dropdown('customer_type', $type_customers, $person_info->type_customer, 'class="form-control" id="customer_type"'); ?>
                    </div>
                </div>
                <!-- phân cấp loại khách hàng -->
                <?php if (!empty($tiers)) { ?>
                    <div class="form-group clearfix">
                        <?php echo form_label(lang('customers_tier_type') . '', 'tier_id', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                        <div class="col-sm-9 col-md-9 col-lg-9">
                            <?php echo form_dropdown('tier_id', $tiers, $person_info->tier_id, 'class="form-control" id="tier_id"'); ?>
                        </div>
                    </div>
                <?php } ?>
                <!-- ngành nghề kinh doanh -->
                <?php
                ?>
                <div class="form-group clearfix">
                    <?php echo form_label('Ngành nghề kinh doanh' . '', 'business_type', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-9">
                        <p for="business_type" class="text-danger errors"></p>
                        <select class="form-control" name="business_type[]" id="business_type">
                            <?php
                            foreach ($business_type as $id => $value) {
                                $selected = ($value['has_access'] == true) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $id ?>" <?php echo $selected ?>>

                                    <?php echo $value['name'] ?>

                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <!--Mặt hàng kinh doanh -->
                <div class="form-group clearfix">
                    <?php echo form_label('Lĩnh vực kinh doanh' . '', 'business_item', array('class' => ' col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-9">
                        <?php echo form_input(array(
                            'class' => 'form-control',
                            'name' => 'business_item',
                            'id' => 'business_item',
                            'value' => $person_info->business_item,
                            'placeholder' => ''
                        )
                    ); ?>
                </div>
            </div>

            <!-- khu vực địa lý -->
            <div class="form-group clearfix">
                <?php echo form_label('Khu vực' . '', 'geographical_area', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <p for="geographical_area" class="text-danger errors"></p>
                    <select class="form-control" name="geographical_area[]" id="geographical_area">
                        <?php
                        foreach ($geographical_area as $id => $value) {
                            $selected = ($value['has_access'] == true) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $id ?>" <?php echo $selected ?>>

                                <?php echo $value['name'] ?>

                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Hình thức công ty -->
            <div class="form-group clearfix">
                <?php echo form_label(lang('common_customers_company_form') . '', 'company_form_id', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <?php echo form_dropdown('company_form_id', $company_form_customize, $person_info->company_form_id, 'class="form-control" id="company_form"'); ?>
                </div>
            </div>

            <!-- Nguồn kh -->
            <div class="form-group clearfix">
                <?php echo form_label(lang('customers_from'), 'customer_reference', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <p for="customer_reference" class="text-danger errors"></p>
                    <select class="form-control" name="customer_reference" id="customer_reference">
                        <?php
                        foreach ($customer_reference as $id => $value) {
                            $selected = ($person_info->reference_by == $value['id']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $value['id'] ?>" <?php echo $selected ?>><?php echo $value['name'] ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Von dieu le -->
            <div class="form-group clearfix">
                <?php echo form_label(lang('customers_authorized_capital'), 'authorized_capital', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <?php echo form_input(array(
                        'name' => 'authorized_capital',
                        'id' => 'authorized_capital',
                        'class' => 'form-control',
                        'value' => $person_info->authorized_capital,
                        'placeholder' => lang('customers_authorized_capital'))
                    ); ?>
                </div>
            </div>

            <!-- Tong TS -->
            <div class="form-group clearfix">
                <?php echo form_label(lang('customers_total_assets'), 'total_assets', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <?php echo form_input(array(
                        'name' => 'total_assets',
                        'id' => 'total_assets',
                        'class' => 'form-control',
                        'value' => $person_info->total_assets,
                        'placeholder' => lang('customers_total_assets'))
                    ); ?>
                </div>
            </div>

            <!-- Tong doanh thu -->
            <div class="form-group clearfix">
                <?php echo form_label(lang('customers_total_revenue'), 'total_revenue', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <?php echo form_input(array(
                        'name' => 'total_revenue',
                        'id' => 'total_revenue',
                        'class' => 'form-control',
                        'value' => $person_info->total_revenue,
                        'placeholder' => lang('customers_total_revenue'))
                    ); ?>
                </div>
            </div>

            <!-- Tong loi nhuan-->
            <div class="form-group clearfix">
                <?php echo form_label(lang('customers_total_profit'), 'total_profit', array('class' => 'col-sm-3 col-md-3 col-lg-3 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-9">
                    <?php echo form_input(array(
                        'name' => 'total_profit',
                        'id' => 'total_profit',
                        'class' => 'form-control',
                        'value' => $person_info->total_profit,
                        'placeholder' => lang('customers_total_profit'))
                    ); ?>
                </div>
            </div>



            <!-- <a class="btn btn-primary" href="<?php echo base_url(); ?>customers/categories"><i
                class="icon ti-direction-alt"></i> Quay lại danh mục</a> -->
            </div>


            <!-- menu 2 -->
            <div id='menu1' class="tab-pane panel-body fade">
                <div class="form-group clearfix">
                    <?php echo form_label('Tên' . '', 'thong_tin_lien_he_name', array('class' => ' col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'class' => 'form-control',
                            'name' => 'thong_tin_lien_he[]',
                            'id' => 'thong_tin_lien_he_name',
                            'value' => isset($thong_tin_lien_he[0]['name_more']) ? $thong_tin_lien_he[0]['name_more'] : '',
                            'placeholder' => 'Tên'
                        )
                    ); ?>
                </div>
            </div>
            <div class="form-group clearfix">
                <?php echo form_label(lang('common_phone_number') . '', 'thong_tin_lien_he_sdt', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-10">
                    <?php echo form_input(array(
                        'class' => 'form-control',
                        'name' => 'thong_tin_lien_he[]',
                        'id' => 'thong_tin_lien_he_sdt',
                        'placeholder' => 'Điện thoại',
                        'value' => isset($thong_tin_lien_he[0]['sdt']) ? $thong_tin_lien_he[0]['sdt'] : '')); ?>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <?php echo form_label(lang('customers_sex'), 'thong_tin_lien_he_sex', array('class' => ' col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                    
                        <?php  echo form_dropdown('thong_tin_lien_he[]', $sex,isset($thong_tin_lien_he[0]['sex']) && $thong_tin_lien_he[0]['sex'] >0  ? $thong_tin_lien_he[0]['sex'] : '', array(
                            'class' => 'form-control',
                            'name' => 'thong_tin_lien_he[]',
                            'id' => 'thong_tin_lien_he_sex',
                            'value' => isset($thong_tin_lien_he[0]['sex']) && $thong_tin_lien_he[0]['sex'] >0  ? $thong_tin_lien_he[0]['sex'] : '')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo form_label(lang('customers_birth_date'), 'thong_tin_lien_he_birthday', array('class' => ' col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <?php echo form_input(array(
                                'class' => 'form-control datepicker',
                                'name' => 'thong_tin_lien_he[]',
                                'type' => 'text',
                                'id' => 'thong_tin_lien_he_birthday',
                                'value' => isset($thong_tin_lien_he[0]['birthday']) && $thong_tin_lien_he[0]['birthday'] != '1950-01-01' ? date('d-m-Y', strtotime($thong_tin_lien_he[0]['birthday'] != '' ? $thong_tin_lien_he[0]['birthday'] : date('d-m-Y'))) : '')
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <?php echo form_label('Email' . '', 'thong_tin_lien_he_email', array('class' => ' col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <?php echo form_input(array(
                                'class' => 'form-control',
                                'name' => 'thong_tin_lien_he[]',
                                'id' => 'thong_tin_lien_he_email',
                                'value' => isset($thong_tin_lien_he[0]['email_more']) ? $thong_tin_lien_he[0]['email_more'] : '',
                                'placeholder' => 'Email'
                            )
                        ); ?>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <?php echo form_label('Chức vụ' . '', 'thong_tin_lien_he_phongban', array('class' => ' col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'class' => 'form-control',
                            'name' => 'thong_tin_lien_he[]',
                            'id' => 'thong_tin_lien_he_phongban',
                            'value' => isset($thong_tin_lien_he[0]['phongban']) ? $thong_tin_lien_he[0]['phongban'] : '',
                            'placeholder' => 'Chức vụ'
                        )
                    ); ?>
                </div>
            </div>
            <div class="form-group clearfix">
                <?php echo form_label('Ghi chú' . '', 'thong_tin_lien_he_note', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                <div class="col-sm-9 col-md-9 col-lg-10">
                    <?php echo form_textarea(array(
                        'name' => 'thong_tin_lien_he[]',
                        'id' => 'thong_tin_lien_he_note',
                        'class' => 'form-control text-area',
                        'value' => isset($thong_tin_lien_he[0]['note']) ? $thong_tin_lien_he[0]['note'] : '',
                        'rows' => '3',
                        'cols' => '17')
                    ); ?>
                </div>
            </div>
            <!-- thêm thông tin người đầu mối -->

            <div class="panel-heading header-tab">
                <h3 class="panel-title">
                    <i class="ion-edit"></i>
                    <?php echo lang('customers_head_info'); ?>
                    <p></p>
                </h3>
            </div>


        </h3>
        <table class="tablesorter table table-hover" id="sortable_table">
            <thead>
                <tr>

                    <th>STT</th>
                    <th>Họ tên</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Phòng ban</th>
                    <th>Ghi chú</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody id='them_dong_moi'>
                <?php if (count($thong_tin_dau_moi) < 1){
                    echo $table_thong_tin_dau_moi;
                } else 
                { 
                    $stt =1;
                    ?>
                <?php foreach ($thong_tin_dau_moi as $key => $value) { ?>

                    <tr>
                        <td style="width: 6%;">
                            <input type="text" name="stt" value="<?php echo $stt ?>"
                            class="form-control input-sm"></td>
                            <td><input data-toggle="popover" data-trigger="hover"
                             data-content="<?php echo $value['name'] ?>" data-placement="top" type="text"
                             name="thong_tin_dau_moi[]" value="<?php echo $value['name'] ?>"
                             class="form-control input-sm mytext" id="thong_tin_lien_he_name" placeholder="Tên">
                         </td>

                         <td>
                            <input data-toggle="popover" data-trigger="hover"
                            data-content="<?php echo $value['phone'] ?>" data-placement="top"
                            name="thong_tin_dau_moi[]" value="<?php echo $value['phone'] ?>"
                            class="form-control input-sm mytext" id="thong_tin_lien_he_sdt" placeholder="SĐT">

                        </td>

                        <td><input data-toggle="popover" data-trigger="hover"
                         data-content="<?php echo $value['email'] ?>" data-placement="top" type="text"
                         name="thong_tin_dau_moi[]" value="<?php echo $value['email'] ?>"
                         class="form-control input-sm mytext" id="thong_tin_lien_he_email"
                         placeholder="Email"></td>

                         <td><input data-toggle="popover" data-trigger="hover"
                             data-content="<?php echo $value['department'] ?>" data-placement="top" type="text"
                             name="thong_tin_dau_moi[]" value="<?php echo $value['department'] ?>"
                             class="form-control input-sm mytext" id="thong_tin_lien_he_phongban"
                             placeholder="Chức vụ"></td>
                             <td><input data-toggle="popover" data-trigger="hover"
                                 data-content="<?php echo $value['note'] ?>" data-placement="top" type="text"
                                 name="thong_tin_dau_moi[]" value="<?php echo $value['note'] ?>"
                                 class="form-control input-sm mytext" id="thong_tin_lien_he_note"
                                 placeholder="Ghi chú"></td>

                                 <td>
                                    <button type="button" class="btn btn-danger xoa_dong"><i class=""></i>Xóa</button>
                                </td>
                            </tr>
                            <?php $stt++;} ?><!-- end foreach -->
                            <?php } ?> <!-- end else -->
                        </tbody>

                    </table>
                    <button type="button" class="btn btn-primary btn-block" onclick="them_dong_moi()">Thêm dòng</button>
                </div> <!-- end tab người đại diện -->
                <!-- menu 3 -->
                <div id='menu2' class="tab-pane panel-body fade">
                    <?php
                    if ($this->config->item('customers_store_accounts') && $this->Employee->has_module_action_permission('customers', 'edit_store_account_balance', $this->Employee->get_logged_in_employee_info()->person_id)) {
                        ?>
                        <div class="form-group clearfix">
                            <?php echo form_label($this->config->item('customer_balance') . '', 'balance', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                    'name' => 'balance',
                                    'id' => 'balance',
                                    'class' => 'form-control balance',
                                    'value' => $person_info->balance ? to_currency_no_money($person_info->balance) : '0.00')
                                ); ?>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <?php echo form_label($this->config->item('customer_balance_2') . '', 'balance_2', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                    'name' => 'balance_2',
                                    'id' => 'balance_2',
                                    'class' => 'form-control balance',
                                    'value' => $person_info->balance_2 ? to_currency_no_money($person_info->balance_2) : '0.00')
                                ); ?>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <?php echo form_label(lang('common_credit_limit') . '', 'credit_limit', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                    'name' => 'credit_limit',
                                    'id' => 'credit_limit',
                                    'class' => 'form-control credit_limit',
                                    'value' => $person_info->credit_limit ? to_currency_no_money($person_info->credit_limit) : '')
                                ); ?>
                            </div>
                        </div>
                        <?php
                    } elseif ($this->config->item('customers_store_accounts')) {
                        ?>
                        <div class="form-group quantity-input">
                            <?php echo form_label(lang('customers_store_account_balance') . '', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <h5><?php echo $person_info->balance ? to_currency($person_info->balance) : to_currency(0); ?></h5>
                            </div>
                        </div>


                        <div class="form-group quantity-input">
                            <?php echo form_label(lang('common_credit_limit') . '', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <h5><?php echo $person_info->credit_limit ? to_currency($person_info->credit_limit) : lang('common_none'); ?></h5>
                            </div>
                        </div>


                        <?php
                    }

                    if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple') {
                        $sales_until_discount = $this->config->item('number_of_sales_for_discount') - $person_info->current_sales_for_discount;

                        if ($this->Employee->has_module_action_permission('customers', 'edit_customer_points', $this->Employee->get_logged_in_employee_info()->person_id)) {
                            ?>
                            <div class="form-group quantity-input">
                                <?php echo form_label(lang('common_sales_until_discount') . '', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <?php echo form_input(array(
                                        'name' => 'sales_until_discount',
                                        'id' => 'sales_until_discount',
                                        'class' => 'form-control sales_until_discount',
                                        'value' => to_quantity($sales_until_discount))
                                    ); ?>
                                </div>
                            </div>

                            <?php
                        } else {
                            ?>
                            <div class="form-group quantity-input">
                                <?php echo form_label(lang('common_sales_until_discount') . '', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <h5><?php echo to_quantity($sales_until_discount); ?></h5>
                                </div>
                            </div>
                            <?php
                            echo form_hidden('sales_until_discount', $sales_until_discount);
                            ?>
                            <?php
                        }
                    }

                    if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced') {
                        list($spend_amount_for_points, $points_to_earn) = explode(":", $this->config->item('spend_to_point_ratio'), 2);

                        if ($this->Employee->has_module_action_permission('customers', 'edit_customer_points', $this->Employee->get_logged_in_employee_info()->person_id)) {
                            ?>
                            <div class="form-group quantity-input">
                                <?php echo form_label(lang('customers_amount_to_spend_for_next_point') . '', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <?php echo form_input(array(
                                        'name' => 'amount_to_spend_for_next_point',
                                        'id' => 'amount_to_spend_for_next_point',
                                        'class' => 'form-control amount_to_spend_for_next_point',
                                        'value' => to_currency_no_money($spend_amount_for_points - $person_info->current_spend_for_points))
                                    ); ?>
                                </div>
                            </div>

                            <?php
                        } else {
                            ?>
                            <div class="form-group quantity-input">
                                <?php echo form_label(lang('customers_amount_to_spend_for_next_point') . '', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <h5><?php echo to_currency($spend_amount_for_points - $person_info->current_spend_for_points); ?></h5>
                                </div>
                            </div>
                            <?php
                            echo form_hidden('amount_to_spend_for_next_point', to_currency_no_money($spend_amount_for_points - $person_info->current_spend_for_points));
                            ?>
                            <?php
                        }
                    }

                    ?>
                    <div class="form-group override-taxes-container">
                        <?php echo form_label(lang('customers_override_default_tax_for_sale') . ' :', '', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <?php echo form_checkbox(array(
                                'name' => 'override_default_tax',
                                'id' => 'override_default_tax',
                                'class' => 'override_default_tax_checkbox delete-checkbox',
                                'value' => 1,
                                'checked' => (boolean)$person_info->override_default_tax));
                                ?>
                                <label for="override_default_tax"><span></span></label>
                            </div>
                        </div>

                        <div class="tax-container main <?php if (!$person_info->override_default_tax) {
                            echo 'hidden';
                        } ?>">
                        <div class="form-group">
                            <?php echo form_label(lang('common_tax_1') . ' :', 'tax_percent_1', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                    'name' => 'tax_names[]',
                                    'id' => 'tax_percent_1',
                                    'size' => '8',
                                    'class' => 'form-control margin10 form-inps',
                                    'placeholder' => lang('common_tax_name'),
                                    'value' => isset($customer_tax_info[0]['name']) ? $customer_tax_info[0]['name'] : ($this->Location->get_info_for_key('default_tax_1_name') ? $this->Location->get_info_for_key('default_tax_1_name') : $this->config->item('default_tax_1_name')))
                                ); ?>
                            </div>
                            <label class="col-sm-3 col-md-3 col-lg-2 control-label wide" for="tax_percent_name_1">&nbsp;</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                    'name' => 'tax_percents[]',
                                    'id' => 'tax_percent_name_1',
                                    'size' => '3',
                                    'class' => 'form-control form-inps-tax',
                                    'placeholder' => lang('common_tax_percent'),
                                    'value' => isset($customer_tax_info[0]['percent']) ? $customer_tax_info[0]['percent'] : '')
                                ); ?>
                                <div class="tax-percent-icon">%</div>
                                <div class="clear"></div>
                                <?php echo form_hidden('tax_cumulatives[]', '0'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo form_label(lang('common_tax_2') . ' :', 'tax_percent_2', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                    'name' => 'tax_names[]',
                                    'id' => 'tax_percent_2',
                                    'size' => '8',
                                    'class' => 'form-control form-inps margin10',
                                    'placeholder' => lang('common_tax_name'),
                                    'value' => isset($customer_tax_info[1]['name']) ? $customer_tax_info[1]['name'] : ($this->Location->get_info_for_key('default_tax_2_name') ? $this->Location->get_info_for_key('default_tax_2_name') : $this->config->item('default_tax_2_name')))
                                ); ?>
                            </div>
                            <label class="col-sm-3 col-md-3 col-lg-2 control-label text-info wide">&nbsp;</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <?php echo form_input(array(
                                    'name' => 'tax_percents[]',
                                    'id' => 'tax_percent_name_2',
                                    'size' => '3',
                                    'class' => 'form-control form-inps-tax',
                                    'placeholder' => lang('common_tax_percent'),
                                    'value' => isset($customer_tax_info[1]['percent']) ? $customer_tax_info[1]['percent'] : '')
                                ); ?>
                                <div class="tax-percent-icon">%</div>
                                <div class="clear"></div>
                                <?php echo form_checkbox('tax_cumulatives[]', '1', (isset($customer_tax_info[1]['cumulative']) && $customer_tax_info[1]['cumulative']) ? (boolean)$customer_tax_info[1]['cumulative'] : (boolean)$this->config->item('default_tax_2_cumulative'), 'class="cumulative_checkbox" id="tax_cumulatives"'); ?>
                                <label for="tax_cumulatives"><span></span></label>
                                <span class="cumulative_label">
                                    <?php echo lang('common_cumulative'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3"
                        style="visibility: <?php echo isset($customer_tax_info[2]['name']) ? 'hidden' : 'visible'; ?>">
                        <a href="javascript:void(0);" class="show_more_taxes"><?php echo lang('common_show_more'); ?>
                    &raquo;</a>
                </div>
                <div class="more_taxes_container"
                style="display: <?php echo isset($customer_tax_info[2]['name']) ? 'block' : 'none'; ?>">
                <div class="form-group">
                    <?php echo form_label(lang('common_tax_3') . ' :', 'tax_percent_3', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'name' => 'tax_names[]',
                            'id' => 'tax_percent_3',
                            'size' => '8',
                            'class' => 'form-control form-inps margin10',
                            'placeholder' => lang('common_tax_name'),
                            'value' => isset($customer_tax_info[2]['name']) ? $customer_tax_info[2]['name'] : ($this->Location->get_info_for_key('default_tax_3_name') ? $this->Location->get_info_for_key('default_tax_3_name') : $this->config->item('default_tax_3_name')))
                        ); ?>
                    </div>
                    <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'name' => 'tax_percents[]',
                            'id' => 'tax_percent_name_3',
                            'size' => '3',
                            'class' => 'form-control form-inps-tax margin10',
                            'placeholder' => lang('common_tax_percent'),
                            'value' => isset($customer_tax_info[2]['percent']) ? $customer_tax_info[2]['percent'] : '')
                        ); ?>
                        <div class="tax-percent-icon">%</div>
                        <div class="clear"></div>
                        <?php echo form_hidden('tax_cumulatives[]', '0'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo form_label(lang('common_tax_4') . ' :', 'tax_percent_4', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'name' => 'tax_names[]',
                            'id' => 'tax_percent_4',
                            'size' => '8',
                            'class' => 'form-control  form-inps margin10',
                            'placeholder' => lang('common_tax_name'),
                            'value' => isset($customer_tax_info[3]['name']) ? $customer_tax_info[3]['name'] : ($this->Location->get_info_for_key('default_tax_4_name') ? $this->Location->get_info_for_key('default_tax_4_name') : $this->config->item('default_tax_4_name')))
                        ); ?>
                    </div>
                    <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'name' => 'tax_percents[]',
                            'id' => 'tax_percent_name_4',
                            'size' => '3',
                            'class' => 'form-control form-inps-tax',
                            'placeholder' => lang('common_tax_percent'),
                            'value' => isset($customer_tax_info[3]['percent']) ? $customer_tax_info[3]['percent'] : '')
                        ); ?>
                        <div class="tax-percent-icon">%</div>
                        <div class="clear"></div>
                        <?php echo form_hidden('tax_cumulatives[]', '0'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo form_label(lang('common_tax_5') . ' :', 'tax_percent_5', array('class' => 'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'name' => 'tax_names[]',
                            'id' => 'tax_percent_5',
                            'size' => '8',
                            'class' => 'form-control  form-inps margin10',
                            'placeholder' => lang('common_tax_name'),
                            'value' => isset($customer_tax_info[4]['name']) ? $customer_tax_info[4]['name'] : ($this->Location->get_info_for_key('default_tax_5_name') ? $this->Location->get_info_for_key('default_tax_5_name') : $this->config->item('default_tax_5_name')))
                        ); ?>
                    </div>
                    <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">&nbsp;</label>
                    <div class="col-sm-9 col-md-9 col-lg-10">
                        <?php echo form_input(array(
                            'name' => 'tax_percents[]',
                            'id' => 'tax_percent_name_5',
                            'size' => '3',
                            'class' => 'form-control form-inps-tax margin10',
                            'placeholder' => lang('common_tax_percent'),
                            'value' => isset($customer_tax_info[4]['percent']) ? $customer_tax_info[4]['percent'] : '')
                        ); ?>
                        <div class="tax-percent-icon">%</div>
                        <div class="clear"></div>
                        <?php echo form_hidden('tax_cumulatives[]', '0'); ?>
                    </div>
                </div>
            </div> <!--End more Taxes Container-->
            <div class="clear"></div>
        </div>

    </div><!-- end tab tùy chọn -->
</div>

