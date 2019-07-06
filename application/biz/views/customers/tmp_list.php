<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/customer.js'; ?>"></script>
<?php
$key_filter        = 'customer_tmp_filter';
$filter            = $_SESSION[$key_filter];
$keywords          = $filter['keywords'];
$customer_type     = $filter['customer_type'];

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
$link_birthday = base_url() . 'customers/birthday';
$link_balance  = base_url()  . 'customers/balance';
?>
    <div class="manage_buttons">
        <div class="manage-row-options hidden" data-table="customer_tmp_list">
            <div class="email_buttons text-center">
                <a class="btn btn-primary btn-lg" title="Gửi E-Mail" id="sendMail">
                    <span class="">Gửi E-Mail</span>
                </a>
				<a class="btn btn-primary btn-lg" title="Gửi SMS" id="sendSMS">
                    <span class="">Gửi SMS</span>
                </a>
                <a class="btn btn-primary btn-lg" title="Gửi E-Mail" id="btn_remove_from_tmp_list">		
                    <span class="">Xóa khỏi DS Tạm</span>
                </a>
                <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
                <a href="javascript:;" data-table="customer_tmp_list" data-url="<?php echo base_url() . 'customers/deletes'; ?>" class="btn btn-red btn-lg">Xóa khách hàng</a>
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
                                <input type="text" class="form-control" name ='search' id='search' value="<?php echo $keywords;?>" placeholder="Tìm kiếm khách hàng"/>
                                <input type="hidden" name="s_keywords" class="data-n9-s" data-table="customer_tmp_list" value="<?php echo $keywords;?>" />
                            </li>
                            <li>
                                <select name="s_customer_type" class="form-control data-n9-s" data-table="customer_tmp_list" id="s_customer_type">
                            <?php
                            if(!empty($slb_customer_type)) {
                                $slb_customer_type[-1] = 'Loại khách hàng';
                                foreach($slb_customer_type as $key => $val) {
                            ?>
                                    <option value="<?php echo $key; ?>"<?php if($customer_type == $key) echo ' selected'; ?>><?php echo $val; ?></option>
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
                        <a href="<?php echo base_url().'customers/view/-1'; ?>" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới"><span class="">Thêm mới</span></a>
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
                        <span class="title first"><a href="<?php echo $link_list; ?>">Danh sách</a></span>
                        <span class="badge bg-primary tip-left" id="count_customer_list">0</span>

                        <span class="title first active">Danh sách tạm</span>
                        <span class="badge bg-primary tip-left" id="count_customer_tmp_list">0</span>

                        <span class="title"><a href="<?php echo $link_birthday; ?>">Sinh nhật</a></span>
                        <span class="badge bg-primary tip-left" id="count_birthday">0</span>

                        <span class="title"><a href="<?php echo $link_balance; ?>">Công nợ</a></span>
                        <span class="badge bg-primary tip-left" id="count_balance">0</span>

                        <i class="fa fa-spinner fa-spin loading" id="customer_list_loading" style="display: none;"></i>
                    </h3>
                </div>
                <div class="panel-body nopadding table_holder table-responsive" >
                    <table id="tbl_customer" class="tablesorter table table-hover data-n9-table"  data-callback="true" data-table="customer_tmp_list" data-url="<?php echo base_url() . 'customers/tmp_list_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                        <thead>
                        <tr>
                            <th class="leftmost" style="width: 20px;">
                                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                            </th>
                            <th data-field="id" style="width: 10%;"<?php if($field_sort == 'id') echo ' class="header '.$class_sort.'"'; ?>>ID</th>
                            <th data-field="first_name" <?php if($field_sort == 'first_name') echo ' class="header '.$class_sort.'"'; ?>>Tên</th>
                            <th data-field="email" style="width: 20%;"<?php if($field_sort == 'email') echo ' class="header '.$class_sort.'"'; ?>>Email</th>
                            <th data-field="balance" style="width: 15%;"<?php if($field_sort == 'balance') echo ' class="header '.$class_sort.'"'; ?>><?php echo $this->config->item('customer_balance'); ?></th>
                            <th data-field="balance_2" style="width: 15%;"<?php if($field_sort == 'balance_2') echo ' class="header '.$class_sort.'"'; ?>><?php echo $this->config->item('customer_balance_2'); ?></th>
                            <th style="width: 100px;">&nbsp;</th>
                            <th style="width: 50px;">&nbsp;</th>
                            <th style="width: 50px;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="text-center data-n9-pagination" data-table="customer_tmp_list"></div>
        </div>
    </div>
    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <script type="text/javascript">
        $( document ).ready(function() {
            load_list('customer_tmp_list');

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
                load_list('customer_tmp_list', 1);
            }

            $('body').on('change','#s_tier_id',function(){
                load_list('customer_tmp_list', 1);
            });

            $('body').on('change','#s_customer_type',function(){
                load_list('customer_tmp_list', 1);
            });

            $('body').on('change','#s_employee_id',function(){
                load_list('customer_tmp_list', 1);
            });
        });

        function n9_grid_callback(data_table,result) {
            $('#count_customer_list').text(result.count_list);
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

        .loading {
            position: absolute;
            bottom: -91px;
            left: -9px;
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
<?php $this->load->view("partial/footer"); ?>