<?php
$filter   = $_SESSION['contract_payment_detail_filter'];
if(isset($filter['current_page']))
    $current_page = $filter['current_page'];
else
    $current_page = 1;

$keywords = $filter['keywords'];
if(!isset($filter['col'])) {
    $field_sort = 'id';
    $class_sort = 'headerSortUp';
}else {
    $field_sort = $filter['col'];
    if($filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">
                Chi tiết thanh toán <span id="count_contract_payment_detail" title="total suppliers" class="badge bg-primary tip-left">0</span>
                <i class="fa fa-spinner fa-spin loading" id="contract_payment_detail_loading" style="display: none;"></i>
            </h4>
        </div>
        <div class="modal-body">
            <input type="hidden" class="data-n9-s" data-table="contract_payment_detail" name="s_payment_id" id="search" value="<?php echo $payment_id; ?>" />
            <div class="control row">
                <div class="col-xs-6 left">
                    <button name="btn_date" id="btn_payment_edit" class="btn btn-primary btn-lg" onclick="contract_payment_detail_frm(-1);">Thêm mới</button>
                </div>
                <div class="col-xs-6 right">
                    <button name="btn_payment_detail_del" id="btn_payment_detail_del" class="btn btn-primary btn-lg btn-red right" data-url="<?php echo base_url() . 'contracts/contract_payment_detail_delete'; ?>" data-table="contract_payment_detail" data-table-reload="contract_payment" data-param="<?php echo $payment_id; ?>">Xóa</button>
                </div>
            </div>
            <div class="panel-body nopadding table_holder table-responsive">
                <table class="tablesorter table table-hover data-n9-table" data-table="contract_payment_detail" data-scroll="false" data-url="<?php echo base_url() . 'contracts/contract_payment_detail_store/'; ?>" data-currentpage="<?php echo $current_page; ?>">
                    <thead>
                    <tr>
                        <th class="leftmost" style="width: 20px;">
                            <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                        </th>
                        <th data-field="id" style="width: 8%;"<?php if($field_sort == 'id') echo ' class="header '.$class_sort.'"'; ?>>ID</th>
                        <th data-field="name"<?php if($field_sort == 'name') echo ' class="header '.$class_sort.'"'; ?>>Tiêu đề</th>
                        <th data-field="price" style="width: 15%;"<?php if($field_sort == 'price') echo ' class="header '.$class_sort.'"'; ?>>Số tiền</th>
                        <th data-field="note" style="width: 30%;"<?php if($field_sort == 'note') echo ' class="header '.$class_sort.'"'; ?>>Ghi chú</th>
                        <th style="width: 100px;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">...</div></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center data-n9-pagination" data-table="contract_payment_detail">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        load_list('contract_payment_detail');
    });
</script>