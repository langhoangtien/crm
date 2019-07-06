<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_delivery"<?php echo $display_css; ?>>
    <div class="clearfix top-control">
        <div class="col-xs-12 col-md-6 pull-left">
            <input type="button" value="Thêm mới" class="btn btn-primary btn-lg submitf" onclick="contract_delivery_frm(-1);" />
        </div>
        <div class="col-xs-6 pull-right">
            <input type="button" value="Xóa" class="btn btn-red btn-lg btn-right" data-table="contract_delivery" data-url="<?php echo base_url() . 'contracts/contract_delivery_delete' ?>" />
        </div>
        <input type="hidden" name="s_contract_id" class="data-n9-s" data-table="contract_delivery" value="<?php echo $contract_id; ?>" />
    </div>
<?php if($action == 'add'){ ?>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_delivery">
        <thead>
        <tr>
            <th class="leftmost" style="width: 20px;">
                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
            </th>
            <th style="width: 7%;">STT</th>
            <th>Giai đoạn</th>
            <th style="width: 15%;">Thời gian giao hàng</th>
            <th style="width: 15%;">Đơn vị</th>
            <th style="width: 20%;">Địa điểm</th>
            <th style="width: 200px;">&nbsp</th>
        </tr>
        </thead>
        <tbody>
            <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
        </tbody>
    </table>
<?php }else { ?>
<?php
$filter   = $_SESSION['contract_delivery_filter'];
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
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_delivery" data-url="<?php echo base_url() . 'contracts/contract_delivery_store/' ?>" data-currentPage="1">
        <thead>
        <tr>
            <th class="leftmost" style="width: 20px;">
                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
            </th>
            <th data-field="id" style="width: 7%;"<?php if($field_sort == 'id') echo ' class="header '.$class_sort.'"'; ?>>ID</th>
            <th data-field="payment_name"<?php if($field_sort == 'payment_name') echo ' class="header '.$class_sort.'"'; ?>>Giai đoạn</th>
            <th data-field="date" style="width: 15%;"<?php if($field_sort == 'date') echo ' class="header '.$class_sort.'"'; ?>>Thời gian giao hàng</th>
            <th data-field="company_name" style="width: 15%;"<?php if($field_sort == 'company_name') echo ' class="header '.$class_sort.'"'; ?>>Công ty</th>
            <th data-field="address" style="width: 20%;"<?php if($field_sort == 'address') echo ' class="header '.$class_sort.'"'; ?>>Địa điểm</th>
            <th style="width: 10%;">Sản phẩm</th>
            <th style="width: 100px;">&nbsp</th>
        </tr>
        </thead>
        <tbody>
        <tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
        </tbody>
    </table>
    <script type="text/javascript">
        $( document ).ready(function() {
            load_list('contract_delivery',1,'disabled');
        });
    </script>
<?php } ?>
</div>