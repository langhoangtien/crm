<?php $this->load->view("partial/header"); ?>

    <link href="<?php echo base_url(); ?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet"
          type="text/css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/n9-modal.css" type="text/css" media="screen"/>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/js/contract-list.js"></script>
    <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>

    <div class="row">
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_purchase_order_value">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p><?php echo lang('customers_all_orders_value') ?></p>
            </div>
        </div>

        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_purchase_profit">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Lợi nhuận</p>
            </div>
        </div>

        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_mail_total"
             style="display: none">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Tổng số Mail</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_contract_suspended_mail_total"
             style="display: none">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Tổng số Mail</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_contract_total"
             style="display: none">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Tổng số hợp đồng</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_expenses_thu_total"
             style="display: none">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Thu</p>
            </div>
        </div>

        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_expenses_chi_total"
             style="display: none">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Chi</p>
            </div>
        </div>

        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_balance_start"
             style="display: none">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Tổng nợ đầu</p>
            </div>
        </div>


        <div class="col-md-3 col-xs-12 col-sm-6 summary-data more-total" id="specific_cus_balance_end"
             style="display: none">
            <div class="info-seven primarybg-info">
                <div class="logo-seven hidden-print"><i class="ti-widget dark-info-primary"></i></div>
                <span class="total">0</span>
                <p>Tổng nợ cuối</p>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 text-right">
        <div class="pull-right-btn" style="display: inline-block">
            <a href="<?php echo base_url() . 'sales/suspended'; ?>" class="btn btn-primary" title="Thêm mới"
               style="background-color: #337ab7!important;"><span
                        class=""><?php echo lang('customers_create_customer_need'); ?></span></a>

        </div>
    </div>
<?php
if (!empty($filter['start_date']))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['start_date']));

if (!empty($filter['end_date']))
    $time_arr[] = date('d-m-Y H:i:s', strtotime($filter['end_date']));

$filter['location_ids'] = !empty($filter['selected_location_ids']) ? implode(',', $filter['selected_location_ids']) : '';
?>
    <div class="row manage-table type-2">
        <div class="col-md-12">
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    <h5 class="panel-title">
                  <!--       <span class="title active" data-tab="projects_categories" id="tab_projects_categories">Danh mục dự án</span> -->
                        <span class="title" data-tab="tab_history_trans" id="tab_history_trans">Danh mục dự án</span>
                                                <span class="title" data-tab="specific_cus_purchase">Lịch sử nhu cầu</span>
                  <!--       <span class="title" data-tab="specific_cus_contract_history">Lịch sử nhu cầu</span> -->
<!--                        <span class="title" data-tab="specific_cus_mail">Lịch sử gửi mail</span>-->
                     <!--    <span class="title" data-tab="specific_cus_expenses">Lịch sử thu chi</span> -->
<!--                        <span class="title" data-tab="specific_cus_balance">Lịch sử công nợ</span>-->
                      
                        <span class="title" data-tab="specific_cus_contract">Hợp đồng</span>
                        <span class="title" data-tab="specific_cus_detail" onclick="openDetailCustomer()">Xem chi tiết thông tin KH</span>

                        <i class="fa fa-spinner fa-spin loading" id="specific_cus_purchase_loading"
                           style="display: none;"></i>
                        <i class="fa fa-spinner fa-spin loading" id="specific_cus_mail_loading"
                           style="display: none;"></i>
                        <i class="fa fa-spinner fa-spin loading" id="specific_cus_expenses_loading"
                           style="display: none;"></i>
                        <i class="fa fa-spinner fa-spin loading" id="specific_cus_balance_loading"
                           style="display: none;"></i>
                        <i class="fa fa-spinner fa-spin loading" id="sspecific_cus_contract_loading"
                           style="display: none;"></i>
                        <i class="fa fa-spinner fa-spin loading" id="specific_cus_contract_suspended_loading"
                           style="display: none;"></i>
                    </h5>
                </div>

                <div id="specific_cus_detail" class="tabs" style="display: none;">
                    <div class="row">
                        <div class="row" style="padding-bottom: 10px; padding-left: 10px;">
                            <div class="panel-heading header-tab">
                                <h3 class="panel-title">
                                    <i class="ion-edit"></i>
                                    <?php echo lang("customers_basic_information"); ?>
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php
                            if ($filter['customer_id']) {
                                echo $person_info->image_id ? '<div id="avatar">' . img(array('src' => site_url('app_files/view/' . $person_info->image_id), 'class' => 'avatar', 'style' => "height: 143px;",)) . '</div>' : '<div id="avatar">' . img(array('src' => base_url() . 'assets/img/avatar.png', 'class' => 'img-polaroid', 'style' => "height: 143px;", 'id' => 'image_empty')) . '</div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Mã khách hàng: </label>' . ' ' . $person_info->code . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Tên: </label>' . ' ' . $person_info->first_name . ' ' . $person_info->last_name . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Điện thoại: </label>' . ' ' . $person_info->phone_number . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Email: </label>' . ' ' . $person_info->email . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Website: </label>' . ' ' . $person_info->website . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Địa chỉ: </label>' . ' ' . $person_info->address_1 . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>STK ngân hàng : </label>' . ' ' . $person_info->account_number . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Mã số thuế: </label>' . ' ' . $person_info->code_tax . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Số ĐKKD: </label>' . ' ' . $person_info->business_registration . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Ngày ĐKKD lần đầu: </label>' . ' ' . date('d-m-Y', strtotime($person_info->first_date_registration)) . '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Ngày ĐKKD thay đổi gần nhất: </label>' . ' ' . date('d-m-Y', strtotime($person_info->last_updated_registration)). '</div></div>';
                                echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Người chăm sóc: </label>' . ' ' . $employee_manager[$person_info->created_by] . '</div></div>';
                            }
                            ?>
                        </div>

                        <div class="tab-content">
                            <div class="panel-heading header-tab">
                                <h3 class="panel-title">
                                    <i class="ion-edit"></i>
                                    <?php echo lang("detail_customers_basic_information"); ?>
                                    <p></p>
                                </h3>
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle='tab' href="#menu1">Phân loại khách hàng</a></li>
                                    <li><a data-toggle='tab' href="#menu2">Người đại diện</a></li>
                            </div>
                            <div id="menu1" class="col-md-6 tab-pane panel-body fade in active">
                                <?php
                                if ($filter['customer_id']) {
                                    //nganh nghe kinh doanh
                                    $string_business_type = '';
                                    foreach ($person_info->business_type as $key) {
                                        $string_business_type .= $business_type[$key->business_type_id]['name'] . '--';
                                    }

                                    //nguon kh
                                    $string_customer_reference = '';
                                    foreach ($customer_reference as $key) {
                                        if ($key['id'] === $person_info->reference_by) {
                                            $string_customer_reference = $key['name'];
                                            break;
                                        }
                                    }

                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Nhóm khách hàng: </label>' . ' ' . $type_customers[$person_info->type_customer] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Phân loại khách hàng: </label>' . ' ' . $price_tiers[$person_info->tier_id] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Ngành nghề kinh doanh: </label>' . ' ' . $string_business_type . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Mặt hàng kinh doanh: </label>' . ' ' . $person_info->business_item . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Khu vực: </label>' . ' ' . $geographical_area[$person_info->geographical[0]->geographical_area_id]['name'] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Hình thức công ty: </label>' . ' ' . $company_form[$person_info->company_form_id]['name'] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Nguồn KH : </label>' . ' ' . $string_customer_reference . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Vốn điều lệ: </label>' . ' ' . $person_info->authorized_capital . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Tổng tài sản: </label>' . ' ' . $person_info->total_assets . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Tổng doanh thu: </label>' . ' ' . $person_info->total_revenue . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Lợi nhuận sau thuế: </label>' . ' ' . $person_info->total_profit . '</div></div>';

                                }
                                ?>

                            </div>
                            <div id='menu2' class="col-md-6 tab-pane panel-body fade">
                                <?php
                                if ($filter['customer_id']) {
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Tên: </label>' . ' ' . $thong_tin_lien_he[0]['name_more'] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Số điện thoại: </label>' . ' ' . $thong_tin_lien_he[0]['sdt'] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Giới tính: </label>' . ' ' . $sex[$thong_tin_lien_he[0]['sex']] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Ngày sinh: </label>' . ' ' . date('d-m-Y', strtotime($thong_tin_lien_he[0]['birthday'])) . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Email: </label>' . ' ' . $thong_tin_lien_he[0]['email_more'] . '</div></div>';
                                    echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Chức vụ: </label>' . ' ' . $thong_tin_lien_he[0]['phongban'] . '</div></div>';

                                }

                                ?>
                                <div class="col-md-6 form-group" style="padding-top: 20px;">
                                    <div class="col-sm-9 col-md-9 col-lg-9">
                                        <strong><?php echo lang('customers_head_info'); ?></strong>
                                    </div>
                                            <?php
                                                if ($filter['customer_id']) {
                                                    $stt = 0;
                                                    foreach ($thong_tin_dau_moi as $key){
                                                        echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>STT: </label>' . ' ' . $stt . '</div></div>';
                                                        echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Họ tên: </label>' . ' ' . $key['name'] . '</div></div>';
                                                        echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>SĐT: </label>' . ' ' . $key['phone'] . '</div></div>';
                                                        echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Email: </label>' . ' ' . $key['email'] . '</div></div>';
                                                        echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Phòng ban: </label>' . ' ' . $key['department'] . '</div></div>';
                                                        echo '<div class="form-group"><div class="col-sm-9 col-md-9 col-lg-9"><label>Ghi chú: </label>' . ' ' . $key['note'] . '</div></div>';
                                                        $stt++;
                                                    }
                                                }

                                            ?>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>


     <!--            <div id="projects_categories" class="tabs"></div> -->
                <div id="history_trans" class="tabs"></div>
                <div id="specific_cus_contract_history" class="tabs"></div>

                <div id="specific_cus_purchase" class="panel-body nopadding table_holder table-responsive tabs"
                     style="display: none;">
                    <input type="hidden" class="data-n9-s" name="s_start_date"
                           value="<?php echo isset($filter['start_date']) ? $filter['start_date'] : ''; ?>"
                           data-table="specific_cus_purchase">
                    <input type="hidden" class="data-n9-s" name="s_end_date"
                           value="<?php echo isset($filter['end_date']) ? $filter['end_date'] : ''; ?>"
                           data-table="specific_cus_purchase">
                    <input type="hidden" class="data-n9-s" name="s_customer_id"
                           value="<?php echo $filter['customer_id']; ?>" data-table="specific_cus_purchase">
                    <input type="hidden" class="data-n9-s" name="s_option" value="purchase"
                           data-table="specific_cus_purchase">
                    <input type="hidden" class="data-n9-s" name="s_location_ids"
                           value="<?php echo $filter['location_ids']; ?>" data-table="specific_cus_purchase">

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table data-table="specific_cus_purchase" data-currentpage="1" data-callback="true"
                                   data-url="<?php echo base_url(); ?>reports/specific_cus_store/"
                                   class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only table-tree">
                                <thead>
                                <tr>
                                    <th class="hidden-print" style="width: 25px;"><a href="#"
                                                                                     class="expand_all">&nbsp;</a></th>
                                    <th style="width: 15%;" data-field="sale_time" class="header headerSortDown">Thời
                                        gian
                                    </th>
                                    <th style="width: 10%;">Mã đơn hàng</th>
                                    <th style="width: 15%;">Tổng giá trị</th>
                                    <th style="width: 15%;">Lợi nhuận</th>
                                    <th style="width: 20%;">Thanh toán</th>
                                    <th>Ghi chú</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div id="specific_cus_mail" class="panel-body nopadding table_holder table-responsive tabs"
                     style="display: none;">
                    <input type="hidden" class="data-n9-s" name="s_start_date"
                           value="<?php echo isset($filter['start_date']) ? $filter['start_date'] : ''; ?>"
                           data-table="specific_cus_mail">
                    <input type="hidden" class="data-n9-s" name="s_end_date"
                           value="<?php echo isset($filter['end_date']) ? $filter['end_date'] : ''; ?>"
                           data-table="specific_cus_mail">
                    <input type="hidden" class="data-n9-s" name="s_customer_id"
                           value="<?php echo $filter['customer_id']; ?>" data-table="specific_cus_mail">
                    <input type="hidden" class="data-n9-s" name="s_option" value="mail" data-table="specific_cus_mail">

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table data-table="specific_cus_mail" data-currentpage="1" data-callback="true"
                                   data-url="<?php echo base_url(); ?>reports/specific_cus_store/"
                                   class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only"
                                   id="sortable_table">
                                <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th style="width: 20%;" data-field="time" class="header headerSortDown">Thời gian
                                    </th>
                                    <th style="width: 20%;">Email nhận</th>
                                    <th style="width: 20%;">Người gửi</th>
                                    <th style="width: 10%;">Trạng thái</th>
                                    <th style="width: 100px;">Nội dung</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div id="specific_cus_expenses" class="panel-body nopadding table_holder table-responsive tabs"
                     style="display: none;">
                    <input type="hidden" class="data-n9-s" name="s_start_date"
                           value="<?php echo isset($filter['start_date']) ? $filter['start_date'] : ''; ?>"
                           data-table="specific_cus_expenses">
                    <input type="hidden" class="data-n9-s" name="s_end_date"
                           value="<?php echo isset($filter['end_date']) ? $filter['end_date'] : ''; ?>"
                           data-table="specific_cus_expenses">
                    <input type="hidden" class="data-n9-s" name="s_location_ids"
                           value="<?php echo $filter['location_ids']; ?>" data-table="specific_cus_expenses">
                    <input type="hidden" class="data-n9-s" name="s_customer_id"
                           value="<?php echo $filter['customer_id']; ?>" data-table="specific_cus_expenses">
                    <input type="hidden" class="data-n9-s" name="s_option" value="expenses"
                           data-table="specific_cus_expenses">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table data-table="specific_cus_expenses" data-callback="true" data-currentpage="1"
                                   data-url="<?php echo base_url(); ?>reports/specific_cus_store/"
                                   class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only"
                                   id="sortable_table">
                                <thead>
                                <tr>
                                    <th style="width: 20%;" data-field="expense_date" class="header headerSortDown">
                                        Ngày
                                    </th>
                                    <th style="width: 10%;">Loại</th>
                                    <th style="width: 20%;">Số tiền</th>
                                    <th style="width: 10%;">Thuế</th>
                                    <th>Diễn giải</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div id="specific_cus_balance" class="panel-body nopadding table_holder table-responsive tabs"
                     style="display: none;">
                    <input type="hidden" class="data-n9-s" name="s_start_date"
                           value="<?php echo isset($filter['start_date']) ? $filter['start_date'] : ''; ?>"
                           data-table="specific_cus_balance">
                    <input type="hidden" class="data-n9-s" name="s_end_date"
                           value="<?php echo isset($filter['end_date']) ? $filter['end_date'] : ''; ?>"
                           data-table="specific_cus_balance">
                    <input type="hidden" class="data-n9-s" name="s_location_ids"
                           value="<?php echo $filter['location_ids']; ?>" data-table="specific_cus_balance">
                    <input type="hidden" class="data-n9-s" name="s_customer_id"
                           value="<?php echo $filter['customer_id']; ?>" data-table="specific_cus_balance">
                    <input type="hidden" class="data-n9-s" name="s_option" value="balance"
                           data-table="specific_cus_balance">

                    <div class="panel-body">
                        <div class="table-responsive">
                            <div class="clearfix top-control">
                                <div class="clearfix">
                                    <div class="form-group">
                                        <select name="s_tai_khoan" class="form-control data-n9-s"
                                                data-table="specific_cus_balance" id="s_tai_khoan">
                                            <option value="1"><?php echo $this->config->item('customer_balance'); ?></option>
                                            <option value="2"><?php echo $this->config->item('customer_balance_2'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table data-table="specific_cus_balance" data-callback="true" data-currentpage="1"
                                   data-url="<?php echo base_url(); ?>reports/specific_cus_store/"
                                   class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only"
                                   id="sortable_table">
                                <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Mã đơn hàng</th>
                                    <th>Nợ đầu</th>
                                    <th>Ghi nợ</th>
                                    <th>Ghi có</th>
                                    <th>Nợ cuối</th>
                                    <th>Ghi chú</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div id="specific_cus_contract" class="panel-body nopadding table_holder table-responsive tabs"
                     style="display: none;">
                    <input type="hidden" class="data-n9-s" name="s_start_date"
                           value="<?php echo isset($filter['start_date']) ? $filter['start_date'] : ''; ?>"
                           data-table="specific_cus_contract">
                    <input type="hidden" class="data-n9-s" name="s_end_date"
                           value="<?php echo isset($filter['end_date']) ? $filter['end_date'] : ''; ?>"
                           data-table="specific_cus_contract">
                    <input type="hidden" class="data-n9-s" name="s_customer_id"
                           value="<?php echo $filter['customer_id']; ?>" data-table="specific_cus_contract">
                    <input type="hidden" class="data-n9-s" name="s_option" value="contract"
                           data-table="specific_cus_contract">

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table data-table="specific_cus_contract" data-currentpage="1" data-callback="true"
                                   data-url="<?php echo base_url(); ?>reports/specific_cus_store/"
                                   class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only"
                                   id="sortable_table">
                                <thead>
                                <tr style="cursor: pointer;">
                                    <th class="cb">Mã</th>
                                    <th class="cb">Tên</th>
                                    <th class="cb">Tên khách hàng</th>
                                    <th class="cb center">Ngày kí</th>
                                    <th class="cb center">Loại</th>
                                    <th class="cb center">Trạng thái</th>
                                    <th class="center">Tải File</th>
                                    <th class="center">Email</th>
                                    <th class="center" style="padding: 4px;"></th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div id="specific_cus_contract_suspended"
                     class="panel-body nopadding table_holder table-responsive tabs" style="display: none;">
                    <input type="hidden" class="data-n9-s" name="s_start_date"
                           value="<?php echo isset($filter['start_date']) ? $filter['start_date'] : ''; ?>"
                           data-table="specific_cus_contract_suspended">
                    <input type="hidden" class="data-n9-s" name="s_end_date"
                           value="<?php echo isset($filter['end_date']) ? $filter['end_date'] : ''; ?>"
                           data-table="specific_cus_contract_suspended">
                    <input type="hidden" class="data-n9-s" name="s_customer_id"
                           value="<?php echo $filter['customer_id']; ?>" data-table="specific_cus_contract_suspended">
                    <input type="hidden" class="data-n9-s" name="s_option" value="contract_suspended"
                           data-table="specific_cus_contract_suspended">

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table data-table="specific_cus_contract_suspended" data-currentpage="1"
                                   data-callback="true" data-url="<?php echo base_url(); ?>reports/specific_cus_store/"
                                   class="data-n9-table table table-bordered table-striped table-reports tablesorter stacktable large-only"
                                   id="sortable_table">
                                <thead>
                                <tr style="cursor: pointer;">
                                    <th class="cb">Thời gian</th>
                                    <th class="cb">Loại</th>
                                    <th class="cb">Mã đơn hàng/Hợp đồng</th>
                                    <th class="cb">Nhân viên</th>
                                    <th class="cb center">Giá trị đơn hàng</th>
                                    <th class="cb center">Trạng thái</th>
                                    <th class="center" style="padding: 4px;"></th>
                                    <th class="center" style="padding: 4px;"></th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top"
         data-table="specific_cus_purchase">
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top"
         data-table="specific_cus_mail">
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top"
         data-table="specific_cus_expenses">
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top"
         data-table="specific_cus_balance">
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top"
         data-table="specific_cus_contract">
    </div>
    <div class="pagination hidden-print alternate text-center data-n9-pagination" id="pagination_top"
         data-table="specific_cus_contract_suspended">
    </div>


    <script>
        function openDetailCustomer() {
            <?php if($filter['customer_id']){ ?>
            document.getElementById('specific_cus_detail').style.display = '';
            <?php }?>

        }
    </script>

    <script type="text/javascript">
        function edit_contract(id) {
            window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/' + id;
        }

        $(document).ready(function () {
            load_list('specific_cus_purchase');
            var _data = {};
            _data['s_customer_id'] = $('[name="s_customer_id"]').val();
            coreAjax.call(
                '<?php echo site_url("customers/history_trans");?>',
                _data,
                function (response) {
                    $('.tabs').css({'display': 'none'});
                    $('#history_trans').html(response.html);
                    $('#history_trans').css({'display': 'block'});
                }
            );
            $('#tab_history_trans').unbind('click').bind('click', function () {
                $("[data-tab]").removeClass('active');
                $(this).addClass('active');

                var _data = {};
                _data['s_customer_id'] = $('[name="s_customer_id"]').val();
                coreAjax.call(
                    '<?php echo site_url("customers/history_trans");?>',
                    _data,
                    function (response) {
                        $('.tabs').css({'display': 'none'});
                        $('#history_trans').html(response.html);
                        $('#history_trans').css({'display': 'block'});
                    }
                );
            });

            <?php if($filter['customer_id'] == -1) :?>
            toastr.error('Phải chọn một khách hàng', 'Lỗi');
            <?php endif; ?>

            $('body').on('click', '[data-tab]', function () {
                $("[data-tab]").removeClass('active');
                var data_id = $(this).attr('data-tab');

                $('.manage-table.type-2 .tabs').hide();
                $(this).addClass('active');
                $('[data-table="' + data_id + '"] tbody').html('');
                $('#' + data_id).show();

                load_list(data_id);

                $('.data-n9-pagination').hide();

                $('.data-n9-pagination[data-table="' + data_id + '"]').show();
            });

            $('body').on('change', '#s_tai_khoan', function () {
                load_list('specific_cus_balance');
            });

            <?php if($filter['customer_id'] == -1) :?>
            toastr.error('Phải chọn một khách hàng', 'Lỗi');
            <?php endif; ?>

            // order detail
            $('body').on('click', '.table-tree .expand_all', function () {
                var symbol = $(this).text();
                var tr_element = $(this).closest('tr');
                var table_element = $(this).closest('table');
                var id = tr_element.attr('data-tree');

                var tr_child = table_element.find('tr[data-parent="' + id + '"]');

                if (symbol == '-') {
                    tr_child.find('.innertable').css('display', 'none');
                    $(this).text('+');

                } else {
                    tr_child.find('.innertable').css('display', 'table-cell');
                    $(this).text('-');

                    $.ajax({
                        type: "POST",
                        url: BASE_URL + 'reports/specific_cus_order_detail',
                        data: {
                            sale_id: id
                        },
                        success: function (string) {
                            tr_child.find('.innertable').html(string);
                        }
                    });
                }
            });
        });

        function n9_grid_callback(data_table, result) {
            $('.more-total').hide();
            $.each(result.total_list, function (index, value) {
                $('#' + index).show();
                $('#' + index + ' .total').text(value);
            });
        }

        function view_mail(mail_history_id) {
            $.ajax({
                type: "POST",
                url: BASE_URL + 'reports/mail_history_content',
                data: {
                    mail_history_id: mail_history_id
                },
                success: function (html) {
                    $('#quick_modal').addClass('size-800');
                    $('#quick_modal').html(html);
                    $('#quick_modal').modal('toggle');
                }
            });
        }

        function delete_mail(mail_history_id) {
            var mail_history_id_ = [mail_history_id];
            $.ajax({
                type: "POST",
                url: BASE_URL + 'customers/manage_mail_history_delete',
                data: {
                    items: mail_history_id_
                },
                success: function (html) {
                    if (JSON.parse(html)['flag'] == 'true') {
                        toastr.success(JSON.parse(html)['msg'], 'Thông báo');
                    }
                    else {
                        toastr.error(JSON.parse(html)['msg'], 'Thông báo');

                    }

                    load_list('specific_cus_contract_suspended');

                }
            });
        }

        $('#print_button').click(function (e) {
            e.preventDefault();
            print_report();
        });

        $('#print_excel').click(function (e) {
            window.location.href = "<?php echo $url_print; ?>";
        });

    </script>
    <style type="text/css">
        .manage-table.type-2 .top-control {
            padding-top: 0;
            padding-bottom: 0px;
        }

        .manage-table.type-2 .panel-heading {
            height: 20px;
            line-height: 20px;
            position: relative;
        }

        .manage-table.type-2 .loading {
            bottom: -38px;
            left: 20px;
            position: absolute;
        }

        .manage-table.type-2 .panel-heading .panel-title {
            height: 20px;
            line-height: 20px;
        }

        .manage-table .panel-body {
            padding: 15px;
        }

        .manage-table.type-2 .tabs {
            padding-top: 20px;
            padding-bottom: 0;
            border-top: 0;
        }

        .manage-table tr th {
            line-height: 27px !important;
        }

        .manage-table tr td {
            height: initial !important;
        }

        .manage-table tr td:last-child, .manage-table tr th:last-child {
            padding-left: 4px;
        }

        #s_options {
            float: right;
            width: 200px;
        }


    </style>
    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <div id="my_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel">
    </div>
    <div id="my_table" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel">
    </div>
    <div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
    <?php $this->load->view("partial/footer"); ?>