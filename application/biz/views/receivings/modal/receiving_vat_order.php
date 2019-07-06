<?php
$filter   = $_SESSION['receiving_vat_order_modal_filter'];
if(isset($filter['current_page']))
    $current_page = $filter['current_page'];
else
    $current_page = 1;

$keywords = $filter['keywords'];
if(!isset($filter['col'])) {
    $field_sort = 'receiving_id';
    $class_sort = 'headerSortDown';
}else {
    $field_sort = $filter['col'];
    if($filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}

$thousands_separator = $this->config->item('thousands_separator');
$decimal_point       = $this->config->item('decimal_point');
$number_of_decimals  = $this->config->item('number_of_decimals');

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">
                Danh sách hóa đơn <span id="count_receiving_vat_order_modal" title="total suppliers" class="badge bg-primary tip-left">0</span>
                <i class="fa fa-spinner fa-spin loading" id="receiving_vat_order_modal_loading" style="display: none;"></i>
            </h4>
        </div>
        <div class="modal-body">
            <div class="control row" style="padding-bottom: 5px;">
            </div>
            <div class="panel-body nopadding table_holder table-responsive">
                <table class="tablesorter table table-hover data-n9-table" data-callback="true" data-table="receiving_vat_order_modal" data-scroll="false" data-url="<?php echo base_url() . 'receivings/modal_store_payment_store/'; ?>" data-currentpage="<?php echo $current_page; ?>">
                    <thead>
                        <tr>
                            <th class="leftmost" style="width: 20px;">
                                #
                            </th>
                            <th style="width: 15%;" data-field="receiving_id"<?php if($field_sort == 'receiving_id') echo ' class="header '.$class_sort.'"'; ?>>Mã đơn hàng</th>
                            <th data-field="sale_time"<?php if($field_sort == 'receiving_time') echo ' class="header '.$class_sort.'"'; ?>>Ngày</th>
                            <th style="width: 20%;">Tổng giá trị</th>
                            <th style="width: 20%;">Thanh toán dư</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="text-center data-n9-pagination" data-table="receiving_vat_order_modal">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        load_list('receiving_vat_order_modal');
    });
</script>