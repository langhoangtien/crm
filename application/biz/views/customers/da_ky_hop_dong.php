<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/customer.js'; ?>"></script>
<?php
$key_filter        = 'customer_index_filter';
$filter            = isset($_SESSION[$key_filter])?$_SESSION[$key_filter]:'';
$keywords          = !empty($filter['keywords'])?$filter['keywords']:'';
$customer_type     = !empty($filter['customer_type'])?$filter['keywords']:'';
$employee_id       = !empty($filter['employee_id'])?$filter['keywords']:'';

if(!isset($filter['col'])) {
    $field_sort = 'id';
    $class_sort = 'headerSortDown';
}else {
    $field_sort = $filter['col'];
    if($filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}

$link_list     = base_url() . 'customers';
$link_tmp      = base_url() . 'customers/tmp_list';
$link_birthday = base_url() . 'customers/birthday';
$link_balance  = base_url()  . 'customers/balance';


$link_khach_hang_moi  = base_url()  . 'customers/khach_hang_moi';
$link_khach_hang_tiem_nang  = base_url()  . 'customers/khach_hang_tiem_nang';
$link_bao_gia_hop_dong  = base_url()  . 'customers/bao_gia_hop_dong';
$link_da_ky_hop_dong  = base_url()  . 'customers/da_ky_hop_dong';
$link_khach_hang_fail  = base_url()  . 'customers/khach_hang_fail';

if(!empty($_SESSION['customer_tmp_ids']))
    $css_style = ' style="display: inline-block"';
else
    $css_style = ' style="display: none"';
?>
    <div class="manage_buttons">
        <div class="manage-row-options hidden" data-table="customer_list">
            <div class="email_buttons text-center">
                <a class="btn btn-primary btn-lg" title="Gửi E-Mail" id="sendMail">
                     <span class=""><?php echo lang('common_send_email'); ?></span>
                </a>
                <a class="btn btn-primary btn-lg" title="Gửi SMS" id="sendSMS">
                    <span class=""><?php echo lang('common_send_sms'); ?></span>
                </a>                    
                <a class="btn btn-primary btn-lg" title="Thêm vào DS tạm" id="btn_add_tmp_list">
                    <span class=""><?php echo lang('common_add_temp_list'); ?></span>
                </a>
                <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
                <a href="javascript:;" data-table="customer_list" data-url="<?php echo base_url() . 'customers/deletes'; ?>" class="btn btn-red red btn-lg"><?php echo lang('common_delete'); ?></a>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div id="search_form">
                <div class="search search-items no-left-border">
                        <ul class="list-inline">
                            <li>
                                <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                                  <input type="text" class="form-control" name ='search' id='search' value="<?php echo $keywords;?>" placeholder="<?php echo lang('common_search'); ?>"/>
                                <input type="hidden" name="s_keywords" class="data-n9-s" data-table="customer_list" value="<?php echo $keywords;?>" />
                            </li>
                            <li>
                                <select name="s_employee_id" class="form-control data-n9-s" data-table="customer_list" id="s_employee_id">
                                     <option value="-1"><?php echo lang('employees_list'); ?></option>
                            <?php
                            if(!empty($employees)) {
                                foreach($employees as $val) {
                            ?>
                                    <option value="<?php echo $val['person_id']; ?>"<?php if($employee_id == $val['person_id']) echo ' selected'; ?>><?php echo $val['username']; ?></option>
                            <?php
                                }
                            }
                            ?>
                                </select>
                            </li>
                            <!--test -->
                            <li>
                                <select name="s_category_id" class="form-control data-n9-s" data-table="customer_list" id="s_category_id">
                                    <option value="-1"><?php echo lang('customers_filter_categorie'); ?></option>
                            <?php

                            if(!empty($category)) {
                                foreach($category as $val) {
                            ?>
                                    <option value="<?php echo $val['id']; ?>"><?php echo lang('common_customers_'.$val['name']); ?></option>
                            <?php
                                }
                            }
                            ?>
                                </select>
                            </li>
                            <!--danh mục con -->
                            <li>
                                <select name="s_category_child" class="form-control data-n9-s" data-table="customer_list" id="s_category_child">
                                   <option value="-1"><?php echo lang('customers_filter_categorie_child'); ?></option>
                            <?php

                            if(!empty($category_child)) {
                                foreach($category_child as $val) {
                            ?>
                                    <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
                            <?php
                                }
                            }
                            ?>
                                </select>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="buttons-list">
                    <div class="pull-right-btn">
                        <?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)): ?>
                         <a href="<?php echo base_url().'customers/view/-1'; ?>" id="new-person-btn" class="btn purple btn-lg" title="Thêm mới"><span class=""><?php echo lang('common_add'); ?></span></a>
                        <?php endif; ?>
                        <?php $this->load->view("customers/partial/customer_menu"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row manage-table">
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    <h3 class="panel-title">
                       <span class="title first"><?php echo lang('common_list'); ?></span>
                        <span class="badge bg-primary tip-left" id="count_customer_list">0</span>

                        <span class="title first" id="count_tmp_list_title"<?php echo $css_style;?>><a href="<?php echo $link_tmp; ?>"><?php echo lang('common_temp_list'); ?></a></span>
                        <span class="badge bg-primary tip-left" id="count_tmp_list"<?php echo $css_style;?>>0</span>

                        <span class="title"><a href="<?php echo $link_balance; ?>"><?php echo lang('common_balance'); ?></a></span>
                        <span class="badge bg-primary tip-left" id="count_balance">0</span>
                        
                        <span class="title"><a href="<?php echo $link_khach_hang_moi; ?>"><?php echo 'Khách hàng mới'; ?></a></span>
                        <span class="badge bg-primary tip-left" id="kh_tiem_nang">0</span>
                        
                        <span class="title"><a href="<?php echo $link_khach_hang_tiem_nang; ?>"><?php echo 'Khách hàng tiềm năng'; ?></a></span>
                        <span class="badge bg-primary tip-left" id="kh_tiem_nang">0</span>
                        
                        <span class="title"><a href="<?php echo $link_bao_gia_hop_dong; ?>"><?php echo 'Báo giá & Hợp đồng'; ?></a></span>
                        <span class="badge bg-primary tip-left" id="tiep_can">0</span>
                        
                        <span class="title first active"><?php echo 'Đã ký hợp đồng'; ?></span>
                        <span class="badge bg-primary tip-left" id="ky_hop_dong">0</span>
                        
                        <span class="title"><a href="<?php echo $link_khach_hang_fail; ?>"><?php echo 'Khách hàng fail'; ?></a></span>
                        <span class="badge bg-primary tip-left" id="ky_hop_dong">0</span>
                        
                    </h3>
                </div>
                <div class="panel-body nopadding table_holder table-responsive" >
                    <table id="tbl_customer" class="tablesorter table table-hover data-n9-table"  data-callback="true" data-table="customer_list" data-url="<?php echo base_url() . 'customers/list_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                        <thead>
                        <tr>
                            <th class="leftmost" style="width: 20px; text-align: left;">
                                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                            </th>
                            <th data-field="code" style="width: 10%;text-align: left;"<?php if($field_sort == 'code') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('common_code'); ?></th>
                            <th data-field="last_name" style="width: 20%;text-align: left;"<?php if($field_sort == 'last_name') echo ' class="text-left '.$class_sort.'"'; ?>><?php echo lang('common_last_name'); ?></th>
                            <th data-field="email" style="width: 20%;text-align: left;"<?php if($field_sort == 'email') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('common_email'); ?></th>
                            <th data-field="phone_number" style="width: 15%;text-align: left;"<?php if($field_sort == 'phone_number') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('common_phone_number'); ?></th>
                            <th data-field="address_1" style="width: 15%;text-align: left;"<?php if($field_sort == 'address_1') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('common_address'); ?></th>
                            <!-- <th style="width: 100px;">&nbsp;</th> -->
                            <th style="width: 50px;">&nbsp;</th>
                            <th style="width: 50px;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="text-center data-n9-pagination" data-table="customer_list"></div>
        </div>
    </div>
    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <script type="text/javascript">
        $( document ).ready(function() {
            load_list('customer_list');

            // search
            var typingTimer;
            $('body').on('keyup','#search',function(){
                var s_keywords = $('[name="s_keywords"]');
                s_keywords.val($(this).val());
                clearTimeout(typingTimer);
                typingTimer = setTimeout(startSearch, 500);
            });

            $('body').on('keydown','#search',function(){
                clearTimeout(typingTimer);
            });

            function startSearch () {
                load_list('customer_list', 1);
            }

            $('body').on('change','#s_customer_type',function(){
                load_list('customer_list', 1);
            });

            $('body').on('change','#s_employee_id',function(){
                load_list('customer_list', 1);
            });
            $('body').on('change','#s_category_id',function(){
                load_list('customer_list', 1);
            });
            $('body').on('change','#s_category_child',function(){
                load_list('customer_list', 1);
            });
        });

        function n9_grid_callback(data_table,result) {
            $('#count_tmp_list').text(result.count_tmp);
            $('#count_birthday').text(result.count_birthday);
            $('#count_balance').text(result.count_balance);
        }
    </script>
    <style>
        .data-n9-table th {
            text-align: center;
        }

        .data-n9-table th[data-field] {
            cursor: pointer;
        }

        .data-n9-table td.center {
            text-align: center;
        }

        .panel-piluku > .panel-heading h3 {
            position: relative;
        }


    </style>
    </div>

<?php
if(!empty($_SESSION['notice'])) {
    $notice = $_SESSION['notice'];
    unset($_SESSION['notice']);
    ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            toastr.success('<?php echo $notice; ?>', 'Thông báo');
        });
    </script>
<?php
}
?>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/quick-nav.js'; ?>"></script>
<?php $this->load->view("partial/footer"); ?>