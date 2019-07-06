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
$link_link_bao_gia_hop_dong  = base_url()  . 'customers/bao_gia_hop_dong';
$link_da_ky_hop_dong  = base_url()  . 'customers/da_ky_hop_dong';
$link_khach_hang_fail  = base_url()  . 'customers/khach_hang_fail';


//echo '<pre>';
//print_r(lang('customers_list_updated'));
//echo '</pre>';
//die();


if(!empty($_SESSION['customer_tmp_ids']))
    $css_style = ' style="display: inline-block"';
else
    $css_style = ' style="display: none"';
?>
    <div class="container-fluid">
        <div class="row manage-table">
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    <h3 class="panel-title">
						<h4><?php echo lang('customers_short_title');?>(<span id="countAll"></span>)</h4>
                    </h3>
                </div>
                <div class="panel-body nopadding table_holder table-responsive" >
                    <table id="tbl_customer" class="tablesorter table table-hover data-n9-table"  data-callback="true" data-table="customer_list" data-url="<?php echo base_url() . 'customers/short_list_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                        <thead>
                        <tr>
                            <th data-field="last_name" style="width: 10%;text-align: left;"<?php if($field_sort == 'last_name') echo ' class="text-left '.$class_sort.'"'; ?>><?php echo lang('common_customers_name'); ?></th>
                            <th style="width: 10%;text-align: left;"><?php echo 'Khu vực'; ?></th>
                            <th style="width: 10%;text-align: left;"><?php echo lang('customers_man_head'); ?></th>

                            <th data-field="email" style="width: 8%;text-align: left;"<?php if($field_sort == 'email') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('common_email'); ?></th>
                            <th data-field="phone_number" style="width: 10%;text-align: left;"<?php if($field_sort == 'phone_number') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('common_phone_number_short'); ?></th>

                            <th style="width: 10%;text-align: left;"><?php echo lang('employee_manager'); ?></th>
                            <!-- <th style="width: 5%;text-align: left"><?php echo lang('customers_list_updated'); ?></th> -->
                            <th style="width: 10%;text-align: center">Xem nhu cầu</th>
                        </tr>
                        </thead>
                        <tbody style="word-break: break-all">

                        </tbody>
                    </table>
                </div>

            </div>
<!--            <div class="text-center data-n9-pagination" data-table="customer_list"></div>-->
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
            $('#countAll').html(result.count);
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
