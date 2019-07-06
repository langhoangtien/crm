<?php
$filter   = $_SESSION['sales_model_filter'];
if(isset($filter['current_page']))
    $current_page = $filter['current_page'];
else
    $current_page = 1;

$keywords = $filter['keywords'];
if(!isset($filter['col'])) {
    $field_sort = 'sale_id';
    $class_sort = 'headerSortDown';
}else {
    $field_sort = $filter['col'];
    if($filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}

if(!isset($filter['start_date']))
    $filter['start_date'] = date("d-m-Y");

if(!isset($filter['end_date']))
    $filter['end_date'] = date("d-m-Y");

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">
                Danh sách hóa đơn <span id="count_sale_modal" title="total suppliers" class="badge bg-primary tip-left">0</span>
                <i class="fa fa-spinner fa-spin loading" id="sale_modal_loading" style="display: none;"></i>
            </h4>
        </div>
        <div class="modal-body">
            <div class="control row">
                <div class="col-xs-6 left">
                    <input data-table="sale_modal" type="text" name="s_start_date" value="<?php echo $filter['start_date']; ?>" id="s_date_start" class="form-control data-n9-s datepicker">
                    <input data-table="sale_modal" type="text" name="s_end_date" value="<?php echo $filter['end_date']; ?>" id="s_date_end" class="form-control data-n9-s datepicker">
                    <button name="btn_date" id="btn_date" class="btn btn-primary btn-lg">Thực hiện</button>
                </div>
                <div class="col-xs-6 right">
                    <input type="text" class="form-control data-n9-s" data-table="sale_modal" name="s_keywords" id="search" value="<?php echo $filter['keywords']; ?>" placeholder="Tìm kiếm mã đơn hàng">
                </div>
            </div>
            <div class="panel-body nopadding table_holder table-responsive">
                <table class="tablesorter table table-hover data-n9-table" data-table="sale_modal" data-scroll="false" data-url="<?php echo base_url() . 'sales/modal_store/'; ?>" data-currentpage="<?php echo $current_page; ?>">
                    <thead>
                    <tr>
                        <th data-field="sale_id" style="width: 15%;"<?php if($field_sort == 'sale_id') echo ' class="header '.$class_sort.'"'; ?>>Mã đơn hàng</th>
                        <th data-field="sale_time"<?php if($field_sort == 'sale_time') echo ' class="header '.$class_sort.'"'; ?>>Ngày</th>
                        <th data-field="customer_id" style="width: 15%;"<?php if($field_sort == 'customer_id') echo ' class="header '.$class_sort.'"'; ?>>Khách hàng</th>
                        <th style="width: 15%;">Giá trị</th>
                        <th style="width: 20%;">Thuế</th>
                        <th style="width: 10%;">Giảm giá</th>
                        <th style="width: 100px;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="text-center data-n9-pagination" data-table="sale_modal">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function startSearch () {
        load_list('sale_modal', 1);
    }
    $( document ).ready(function() {
        load_list('sale_modal');
        date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);

        // search
        var typingTimer;
        $('body').on('keyup','#search',function(){
            clearTimeout(typingTimer);
            typingTimer = setTimeout(startSearch, 500);
        });

        $('body').on('keydown','#search',function(){
            clearTimeout(typingTimer);
        });

        $('body').on('click','#btn_date',function(){
            load_list('sale_modal', 1);
        });
    });
</script>