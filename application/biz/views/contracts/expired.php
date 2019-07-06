<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/contract-list.js" ></script>
<?php
$key_filter = 'contract_'.$option.'_expired_filter';
$filter   = $_SESSION[$key_filter];
$keywords = $filter['keywords'];
$s_type   = $filter['type'];

if(!isset($filter['col'])) {
    $field_sort = 'date_signing';
    $class_sort = 'headerSortDown';
}else {
    $field_sort = $filter['col'];
    if($filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}

$link_list    = base_url() . 'contracts/index/'.$option;
$link_circle  = base_url() . 'contracts/circle/'.$option;
$link_expired = base_url() . 'contracts/expired/'.$option;
?>
<div class="manage_buttons">
    <div class="manage-row-options hidden" data-table="pos_expired_contract">
        <div class="email_buttons text-center">
            <?php //if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
            <a href="javascript:;" data-table="pos_expired_contract" data-url="<?php echo base_url() . 'contracts/contract_delete'; ?>" class="btn btn-red btn-lg"><?php echo lang('common_clear_selection'); ?></a>
            <?php //} ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">

            <div id="search_form">
                <div class="search search-items no-left-border">
                    <ul class="list-inline">
                        <li>
                            <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                            <input type="text" class="form-control" name ='search' id='search' value="<?php echo $keywords;?>" placeholder="Tìm kiếm hợp đồng"/>
                            <input type="hidden" name="s_keywords" class="data-n9-s" data-table="pos_expired_contract" value="<?php echo $keywords;?>" />
                            <input type="hidden" name="s_option" class="data-n9-s" data-table="pos_expired_contract" value="<?php echo $option;?>" />
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="buttons-list">
                <div class="pull-right-btn">
                    <?php 
                    $customerClass = ($option == 'customer') ? 'active' : '';
                    $supplierClass = ($option == 'supplier') ? 'active' : '';
                    ?>
                    <a class="btn btn-primary <?php echo $customerClass;?>" href="<?php echo base_url(); ?>contracts/index/customer">Hợp đồng KH</a>
                    <a class="btn btn-primary <?php echo $supplierClass;?>" href="<?php echo base_url(); ?>contracts/index/supplier">Hợp đồng NCC</a>
                    <a href="javascript:;" id="new-person-btn" class="btn btn-primary" onclick="add_contract();" title="Thêm mới"><span class=""><?php echo lang('contracts_add_new'); ?></span></a>
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
                    <span class="title first"><a href="<?php echo $link_list; ?>"><?php echo lang('contracts_list') ?></a></span>
                    <span class="badge bg-primary tip-left" id="count_pos_contract"><?php echo $list_count; ?></span>
                    <span class="title"><a href="<?php echo $link_circle; ?>"><?php echo lang('contracts_service_cycle') ?></a></span>
                    <span class="badge bg-primary tip-left" id="count_pos_circle_contract"><?php echo $circle_count; ?></span>
                    <span class="title active"><?php echo lang('contracts_contract_expiration') ?></span>
                    <span class="badge bg-primary tip-left" id="count_pos_expired_contract">0</span>
                    <i class="fa fa-spinner fa-spin loading" id="pos_circle_contract_loading" style="display: none;"></i>
                </h3>
            </div>
            <div class="panel-body nopadding table_holder table-responsive" >
                <table class="tablesorter table table-hover data-n9-table" data-table="pos_expired_contract" data-url="<?php echo base_url() . 'contracts/contract_expired_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                    <thead>
                    <tr>
                        <th class="leftmost" style="width: 20px;">
                            <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                        </th>
                        <th data-field="code" style="width: 15%;"<?php if($field_sort == 'code') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('contracts_id') ?></th>
                        <th data-field="name"<?php if($field_sort == 'name') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('contracts_name') ?></th>
        <?php if($option == 'customer'): ?>
                        <th style="width: 10%;"><?php echo lang('contracts_customer') ?></th>
                        <th data-field="sale_id" style="width: 10%;"<?php if($field_sort == 'sale_id') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('contracts_order_number') ?></th>
        <?php endif; ?>
        <?php if($option == 'supplier'): ?>
                        <th style="width: 10%;">Nhà cung cấp</th>
                        <th data-field="receiving_id" style="width: 10%;"<?php if($field_sort == 'receiving_id') echo ' class="header '.$class_sort.'"'; ?>>Số đơn hàng</th>
        <?php endif; ?>

                        <th data-field="date_signing" style="width: 10%;"<?php if($field_sort == 'date_signing') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('contracts_sign_day') ?></th>
                        <th data-field="status"<?php if($field_sort == 'status') echo ' class="header '.$class_sort.'"'; ?>><?php echo lang('contracts_status') ?></th>
                        <th style="width: 5%;"><?php echo lang('contracts_download_file') ?></th>
                        <th style="width: 5%;">Email</th>
                        <th style="width: 100px;">&nbsp;</th>
                        <th style="width: 100px;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="text-center data-n9-pagination" data-table="pos_expired_contract"></div>
    </div>
</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<script type="text/javascript">
    var option = '<?php echo $option; ?>';
    function add_contract() {
        var page = $('table[data-table="pos_expired_contract"]').attr('data-currentPage');
        window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/-1/'+page+'/expired';

    }

    function edit_contract(id) {
        var page = $('table[data-table="pos_expired_contract"]').attr('data-currentPage');
        window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/'+id+'/'+page+'/expired';
    }

    function send_mail_contract(id) {
        var page = $('table[data-table="pos_expired_contract"]').attr('data-currentPage');
        var link_email = BASE_URL + 'contracts/email/'+option+'/'+id+'/'+page+'/expired';

        window.location.href = link_email;
    }

    $( document ).ready(function() {
        load_list('pos_expired_contract');

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
            load_list('pos_expired_contract', 1);
        }

        $('body').on('change','#search_type',function(){
            load_list('pos_expired_contract', 1);
        });
    });
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
