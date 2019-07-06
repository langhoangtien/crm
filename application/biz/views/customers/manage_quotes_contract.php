<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<?php
$filter   = isset($_SESSION['quotes_constract_filter'])?$_SESSION['quotes_constract_filter']:'';
$keywords = isset($filter['keywords'])?$filter['keywords']:'';
if(!isset($filter['col'])) {
    $field_sort = 'created';
    $class_sort = 'headerSortDown';
}else {
    $field_sort = $filter['col'];
    if($filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}
?>
<div class="manage_buttons">
    <div class="manage-row-options hidden" data-table="constract">
        <div class="email_buttons text-center">
            <?php //if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
            <a href="javascript:;" data-table="constract" data-url="<?php echo base_url() . 'customers/quotes_contract_delete'; ?>" class="btn btn-red btn-lg"><?php echo lang('common_clear_selection'); ?></a>
            <?php //} ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="search no-left-border">
                <input type="text" class="form-control data-n9-s" name ='s_keywords' id='search' data-table="constract" value="<?php echo $keywords;?>" placeholder="Tìm kiếm mẫu văn bản"/>
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
                    <a href="javascript:;" id="new-person-btn" class="btn btn-primary btn-lg" onclick="add_constract();" title="Thêm mới"><span class="">Thêm mới</span></a>
                    <a href="<?php echo base_url() . 'customers/quotes_contract_type_list';?>" class="btn btn-primary btn-lg" title="Loại mẫu văn bản"><span class="">Loại văn bản</span></a>
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
                    Danh sách mẫu văn bản
                    <span class="badge bg-primary tip-left" id="count_constract">0</span>
                    <i class="fa fa-spinner fa-spin loading" id="constract_loading" style="display: none;"></i>
                </h3>
            </div>
            <div class="panel-body nopadding table_holder table-responsive" >
                <table class="tablesorter table table-hover data-n9-table" data-table="constract" data-url="<?php echo base_url() . 'customers/quotes_constract_store/' ?>" data-currentPage="<?php echo $currrent_page; ?>">
                    <thead>
                    <tr>
                        <th class="leftmost" style="width: 20px;">
                            <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                        </th>
                        <th data-field="title"<?php if($field_sort == 'title') echo ' class="header '.$class_sort.'"'; ?>>Tiêu đề</th>
                        <th data-field="constract_type_name" style="width: 40%;"<?php if($field_sort == 'constract_type_name') echo ' class="header '.$class_sort.'"'; ?>>Loại văn bản</th>
                        <th data-field="created" style="width: 15%;"<?php if($field_sort == 'created') echo ' class="header '.$class_sort.'"'; ?>>Ngày tạo</th>
                        <th style="width: 100px;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="text-center data-n9-pagination" data-table="constract"></div>
    </div>
</div>
<script type="text/javascript">
    function add_constract() {
        var page = $('table[data-table="constract"]').attr('data-currentPage');
        if(page > 1)
            window.location.href = BASE_URL + 'customers/quotes_contract_view/-1/'+page;
        else
            window.location.href = BASE_URL + 'customers/quotes_contract_view/-1';
    }

    function edit_constract(id) {
        var page = $('table[data-table="constract"]').attr('data-currentPage');
        if(page > 1)
            window.location.href = BASE_URL + 'customers/quotes_contract_view/'+id+'/'+page;
        else
            window.location.href = BASE_URL + 'customers/quotes_contract_view/'+id;
    }

    $( document ).ready(function() {
        load_list('constract');

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
            load_list('constract', 1);
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
