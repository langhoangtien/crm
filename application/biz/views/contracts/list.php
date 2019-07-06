<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/contract-list.js" ></script>
<?php
$key_filter = 'contract_'.$option.'_filter';
$filter   = isset($_SESSION[$key_filter])?$_SESSION[$key_filter]:'';
// var_dump($key_filter);
$keywords = isset($filter['keywords'])?$filter['keywords']:'';
$s_type   = isset($filter['type'])?$filter['type']:'all';
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


<style>
    .gantt_title .tieude {
        padding: 10px 10px;
        background-color: #555;
        line-height: 40px;
        color: #fff;
        font-size: 14px;
    }
    .gantt_title .tieude a {
        color: #fff;
    }
    
    .gantt_title .active {
        background-color: #e64d27;
    }
</style>  

<div class="manage_buttons">
    <div class="manage-row-options hidden" data-table="pos_contract">
        <div class="email_buttons text-center">
            <?php //if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
            <a href="javascript:;" data-table="pos_contract" data-url="<?php echo base_url() . 'contracts/contract_delete'; ?>" class="btn btn-red btn-lg"><?php echo lang('common_clear_selection'); ?></a>
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
                            <input type="hidden" name="s_keywords" class="data-n9-s" data-table="pos_contract" value="<?php echo $keywords;?>" />
                            <input type="hidden" name="s_option" class="data-n9-s" data-table="pos_contract" value="<?php echo $option;?>" />
                        </li>
                        <li>
                            <select name="s_type" class="form-control data-n9-s" data-table="pos_contract" id="search_type">
                                <?php $s_types['all']='Trạng thái hợp đồng';
                                $s_types = array_merge($s_types, lang('contract_status')) ;
                                  ?>
                                <?php foreach ($s_types as $key => $value) { 
                                             $selected = ($key==$s_type) ? "selected" : "";              
                               echo  '<option value="'.$key.'" '.$selected.'>' .$value.'</option>';
                                  } ?>
                            </select>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="buttons-list">
                <div class="pull-right-btn">
                    <!-- <a href="javascript:;" id="new-person-btn" class="btn btn-primary active" onclick="add_contract();" title="Thêm mới"><span class=""><?php echo lang('contracts_add_new'); ?></span></a> -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row manage-table">
        <div class="panel panel-piluku">

             <div class="gantt_title">
                    <h3 class="panel-title">
                        <span class="tieude"><a href="<?php echo base_url() . 'tasks/grid'; ?>">Danh mục dự án</a></span>
                        <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_chart'; ?>">Lược đồ</a></span>
                         <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_list'; ?>">Công việc liên quan</a></span>
                         <span class="tieude active">Quản lý hợp đồng</span>
                         <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_no_revenue'; ?>">Công việc không tạo doanh thu</a></span>
                        <span class="tieude"><a href="<?php echo base_url() . 'tasks/personal'; ?>">Ghi chép</a></span>
                        
    
                    </h3>
                </div>


            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="title first active">Danh sách hợp đồng</span>
                    <span class="badge bg-primary tip-left" id="count_pos_contract">0</span>
                </h3>
            </div>
            <div class="panel-body nopadding table_holder table-striped table-bordered table-bordered table-tree table-responsive" >
                <table class="tablesorter table table-hover data-n9-table" data-table="pos_contract" data-url="<?php echo base_url() . 'contracts/contract_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                    <thead>
                    <tr>
                        <th class="leftmost" style="width: 20px;">
                            <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                        </th>
                        <th data-field="code" style=""<?php if($field_sort == 'code') echo ' class="header '.$class_sort.'"'; ?>>Số hiệu hợp đồng</th>
                        <th data-field="name"<?php if($field_sort == 'name') echo ' class="header '.$class_sort.'"'; ?>>Tên hợp đồng</th>
                        <th>Tên dịch vụ</th>
                        <th style="">Khách hàng</th>
                        <td>Giá trị hợp đồng</td>
                        <td>Giá trị đã nghiệm thu/Thanh lý</td>
                        <th data-field="status"<?php if($field_sort == 'status') echo ' class="header '.$class_sort.'"'; ?>>Trạng thái</th>
                        <th data-field="date_signing" style=""<?php if($field_sort == 'date_signing') echo ' class="header '.$class_sort.'"'; ?>>Ngày ký</th>
                        <th style="">Người phụ trách</th>
                        <th style="" >Người tham gia</th>
                        <th style="">File</th>
                        <th style="">Ghi chú</th>
                        <th style="">Cập nhật</th>
                    </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>

        </div>
        <div class="text-center data-n9-pagination" data-table="pos_contract"></div>
    </div>
</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<script type="text/javascript">
    var option = '<?php echo $option; ?>';
    function add_contract() {
        var page = $('table[data-table="pos_contract"]').attr('data-currentPage');
        if(page > 1)
            window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/-1/'+page;
        else
            window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/-1';
    }

    function edit_contract(id) {
        var page = $('table[data-table="pos_contract"]').attr('data-currentPage');
        if(page > 1)
            window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/'+id+'/'+page;
        else
            window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/'+id;
    }

    function send_mail_contract(id) {
        var page = $('table[data-table="pos_contract"]').attr('data-currentPage');
        if(page == 1)
            var link_email = BASE_URL + 'contracts/email/'+option+'/'+id;
        else
            var link_email = BASE_URL + 'contracts/email/'+option+'/'+id+'/'+page;

        window.location.href = link_email;
    }

    $( document ).ready(function() {
        load_list('pos_contract');

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
            load_list('pos_contract', 1);
        }
        
        $('body').on('change','#search_type',function(){
            load_list('pos_contract', 1);
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