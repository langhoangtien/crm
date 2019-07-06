<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<?php
$constract_type_filter = $_SESSION['constract_type_filter'];
if(!isset($constract_type_filter['col'])) {
    $field_sort = 'code';
    $class_sort = 'headerSortUp';
}else {
    $field_sort = $constract_type_filter['col'];
    if($constract_type_filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}
?>
<div class="manage_buttons">
    <div class="manage-row-options hidden" data-table="constract_type">
        <div class="email_buttons text-center">
            <?php //if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
                <a href="javascript:;" data-table="constract_type" data-url="<?php echo base_url() . 'customers/quotes_contract_type_delete'; ?>" class="btn btn-red btn-lg"><?php echo lang('common_clear_selection'); ?></a>
            <?php //} ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="search no-left-border">
                <input type="text" class="form-control data-n9-s" name ='s_keywords' id='search' data-table="constract_type" value="<?php echo $_SESSION['constract_type_filter']['keywords']; ?>" placeholder="Tìm kiếm loại văn bản"/>
            </div>
            <div class="clear-block <?php echo (!isset($search)||$search=='') ? 'hidden' : ''  ?>">
                <a class="clear" href="<?php echo site_url($controller_name.'/clear_state_mail'); ?>">
                    <i class="ion ion-close-circled"></i>
                </a>
            </div>
        </div>

        <div class="col-md-7">
            <div class="buttons-list">
                <div class="pull-right-btn">
                    <?php //if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
                    <a href="javascript:;" onclick="add_constract_type();" id="new-person-btn" class="btn btn-primary btn-lg" title="Thêm mới"><span class="">Thêm mới</span></a>
                    <a href="<?php echo base_url() . 'customers/quotes_contract';?>" class="btn btn-primary btn-lg" title="Thêm mới"><span class="">Mẫu văn bản</span></a>
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
                    Danh sách loại văn bản
                    <span class="badge bg-primary tip-left" id="count_constract_type">0</span>
                    <i class="fa fa-spinner fa-spin loading" id="constract_type_loading" style="display: none;"></i>
                </h3>
            </div>
            <div class="panel-body nopadding table_holder table-responsive" >
                <table class="tablesorter table  table-hover data-n9-table" data-table="constract_type" data-url="<?php echo base_url() . 'customers/quotes_constract_type_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                    <thead>
                    <tr>
                        <th class="leftmost">
                            <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                        </th>
                        <th width="15%" data-field="code"<?php if($field_sort == 'code') echo ' class="header '.$class_sort.'"'; ?>>Mã</th>
                        <th width="40%" data-field="title"<?php if($field_sort == 'title') echo ' class="header '.$class_sort.'"'; ?>>Tiêu đề</th>
                        <th width="15%" data-field="status" <?php if($field_sort == 'status') echo ' class="header '.$class_sort.'"'; ?>>Trạng thái</th>
                        <th width="20%">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
        <div class="text-center data-n9-pagination" data-table="constract_type"></div>
    </div>
</div>
<script type="text/javascript">
    function add_constract_type() {
        var page = $('table[data-table="constract_type"]').attr('data-currentPage');
        if(page > 1)
            window.location.href = BASE_URL + 'customers/quotes_contract_type_view/-1/'+page;
        else
            window.location.href = BASE_URL + 'customers/quotes_contract_type_view/-1';
    }

    function edit_constract_type(id) {
        var page = $('table[data-table="constract_type"]').attr('data-currentPage');
        if(page > 1)
            window.location.href = BASE_URL + 'customers/quotes_contract_type_view/'+id+'/'+page;
        else
            window.location.href = BASE_URL + 'customers/quotes_contract_type_view/'+id;
    }

    $( document ).ready(function() {
        load_list('constract_type');

        // search
        var typingTimer;
        $('body').on('keyup','#search',function(){
            clearTimeout(typingTimer);
            typingTimer = setTimeout(startSearch, 500);
        });

        $('body').on('keydown','#search',function(){
            clearTimeout(typingTimer);
        });

        function startSearch () {
            load_list('constract_type', 1);
        }

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
