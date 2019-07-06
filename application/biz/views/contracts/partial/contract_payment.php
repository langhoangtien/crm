<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_payment"<?php echo $display_css; ?>>
<?php
    if($type == 'rule')
        $colspan = 6;
    else
        $colspan = 7;
?>
<?php if($action == 'add') { ?>
    <div class="clearfix top-control">
        <div class="col-xs-12 col-md-6 pull-left">
            <input class="btn btn-primary btn-lg" type="button" onclick="contract_payment_form_without_db(-1);" value="Thêm mới" />
        </div>
        <div class="col-xs-6 pull-right">
            <input type="button" value="Xóa" class="btn btn-red btn-lg btn-right" onclick="delete_contract_payment_without_db();"/>
        </div>
    </div>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_payment">
        <thead>
        <tr>
            <th class="leftmost" style="width: 20px;">
                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
            </th>
            <th style="width: 7%;">STT</th>
            <th>Tên giai đoạn nghiệm thu thanh lý</th>
            <th style="width: 15%;">Công việc</th>
            <th style="width: 15%;">Ngày ký biên bản</th>
            <th style="width: 20%;">Số tiền</th>
             <th style="width: 20%;">Trạng thái</th>
<?php //if($type != 'rule'):   ?>
            <th style="width: 10%;">VAT</th>
<?php //endif; ?>
            <th style="width: 100px;">Cập nhật</th>
        </tr>
        </thead>
        <tbody>
            <tr style="cursor: pointer;"><td colspan="<?php echo $colspan; ?>"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
        </tbody>
    </table>
<?php } else { ?>
<?php
    $field_sort = 'id';
    $class_sort = 'headerSortUp';
?>
    <div class="clearfix top-control">
        <div class="col-xs-12 col-md-6 pull-left">
            <input type="button" class="btn btn-primary btn-lg" onclick="contract_payment_frm(-1);" value="Thêm mới"/>
        </div>
        <div class="col-xs-6 pull-right">
            <input  type="button" class="btn btn-red btn-lg btn-right" data-table="contract_payment"  data-url="<?php echo base_url() . 'contracts/contract_payment_delete' ?>" value="Xóa"/>
        </div>
        <input type="hidden" name="s_contract_id" class="data-n9-s" data-table="contract_payment" value="<?php echo $contract_id; ?>" />
    </div>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_payment" data-url="<?php echo base_url() . 'contracts/contract_payment_store/' ?>" data-currentPage="1">
        <thead>
        <tr>
            <th class="leftmost" style="width: 20px;">
                <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
            </th>
            <th style="width: 7%;" data-field="id"<?php if($field_sort == 'id') echo ' class="header '.$class_sort.'"'; ?>>ID</th>
            <th data-field="name"<?php if($field_sort == 'name') echo ' class="header '.$class_sort.'"'; ?>>Tên giai đoạn nghiệm thu thanh lý</th>
            <th style="width: 15%;" data-field="task_name">Công việc</th>
            <th style="width: 15%;" data-field="date_payment"<?php if($field_sort == 'date_payment') echo ' class="header '.$class_sort.'"'; ?>>Ngày ký biên bản</th>
            <th style="width: 20%;">Số tiền</th>
    <?php // if($type != 'rule'):   ?>
            <th style="width: 15%;">Trạng thái </th>
            <th style="width: 10%;" data-field="vat"<?php if($field_sort == 'vat') echo ' class="header '.$class_sort.'"'; ?>>VAT</th>
    <?php// endif; ?>
            <th style="width: 100px;">Cập nhật</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
<script type="text/javascript">
$( document ).ready(function() {
    load_list('contract_payment',1,'disabled');
});
</script>
<?php } ?>

</div>