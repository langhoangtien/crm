<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<?php
$key_filter = 'services_filter';
$filter   = $_SESSION[$key_filter];
$keywords = $filter['keywords'];
$s_type   = $filter['type'];

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
?>
<div class="manage_buttons">
    <div class="manage-row-options hidden" data-table="services">
        <div class="email_buttons text-center">
            <?php //if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
            <a href="javascript:;" data-table="services" data-url="<?php echo base_url() . 'items/delete_services'; ?>" class="btn btn-red btn-lg">Xóa</a>
            <?php //} ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">

            <div id="search_form">
                <div class="search search-items no-left-border">
                    <ul class="list-inline">
                        <li>
                            <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                            <input type="text" class="form-control" name ='search' id='search' value="<?php echo $keywords;?>" placeholder="Tìm kiếm dịch vụ"/>
                            <input type="hidden" name="s_keywords" class="data-n9-s" data-table="services" value="<?php echo $keywords;?>" />
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="buttons-list">
                <div class="pull-right-btn">
                    <?php //if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
                    <a href="javascript:;" id="new-person-btn" class="btn btn-primary btn-lg" onclick="add_services();" title="Thêm mới"><span class="">Thêm mới</span></a>
                    <div class="piluku-dropdown">
                        <button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="ion-android-more-horizontal"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="<?php echo base_url() . 'items'; ?>" class="" title="Quản lý Kho"><span class="">Quản lý Kho</span></a>
                            </li>

                           <!--  <li>
                                <a href="<?php //echo base_url() .'item_kits'; ?>" class="" title="Quản lý BOM & Gói sản phẩm"><span class="">Quản lý BOM & Gói sản phẩm</span></a>
                            </li>
 -->
                            <li>
                                <a href="<?php echo base_url() . 'items/categories'; ?>" class="" title="Quản lý Danh mục"><span class="">Quản lý Danh mục</span></a>
                            </li>

                            <!-- <li>
                                <a href="<?php echo base_url() .'items/manage_tags'; ?>" class="" title="Quản lý Nhóm"><span class="">Quản lý Nhóm</span></a>						</li> -->
                            </li>
                        </ul>
                    </div>
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
                    <span class="title">Dịch vụ</span>
                    <span class="badge bg-primary tip-left" id="count_services">0</span>
                    <i class="fa fa-spinner fa-spin loading" id="services_loading" style="display: none;"></i>
                </h3>
            </div>
            <div class="panel-body nopadding table_holder table-responsive" >
                <table class="tablesorter table table-hover data-n9-table" data-table="services" data-url="<?php echo base_url() . 'items/services_store/'; ?>" data-currentPage="<?php echo $currrent_page; ?>">
                    <thead>
                    <tr>
                        <th class="leftmost">
                            <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                        </th>
                        <th data-field="id" width="15%"<?php if($field_sort == 'id') echo ' class="header '.$class_sort.'"'; ?>>STT</th>
                        <th data-field="code" width="15%" style="text-align: left;"<?php if($field_sort == 'code') echo ' class="header '.$class_sort.'"'; ?>>Mã biểu mẫu</th>
                        <th width="50%" style="text-align: left;" data-field="name"<?php if($field_sort == 'name') echo ' class="header '.$class_sort.'"'; ?>>Tên dịch vụ</th>
                        <th width="20%"> Cập nhật </th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center data-n9-pagination" data-table="services"></div>
    </div>
</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<script type="text/javascript">
    function add_services() {
        var page = $('table[data-table="services"]').attr('data-currentPage');
        window.location.href = BASE_URL + 'items/view_service/-1/'+page;
    }

    $( document ).ready(function() {
        load_list('services');

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
            load_list('services', 1);
        }

        $('body').on('change','#search_type',function(){
            load_list('services', 1);
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
