<div class="panel-body nopadding table_holder table-responsive tabs" id="contract_sales"<?php echo $display_css; ?>>
<?php if($action == 'add') { ?>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_sales" style="border-top: 0">
        <thead>
        <tr>
            <th>Tên</th>
            <th style="width: 20%;">Giá</th>
            <th style="width: 15%;">Số lượng</th>
            <th style="width: 10%;">Đơn vị tính</th>
            <th style="width: 10%;">Chiết khấu</th>
            <th style="width: 20%;">Thành tiền</th>
        </tr>
        </thead>
        <tbody>
            <tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
        </tbody>

    </table>
<?php }else {
?>
    <table class="tablesorter table table-hover data-n9-table" data-table="contract_sales" style="border-top: 0">
        <thead>
        <tr>
            <th>Tên</th>
            <th style="width: 20%;">Giá</th>
            <th style="width: 15%;">Số lượng</th>
            <th style="width: 10%;">Đơn vị tính</th>
            <th style="width: 10%;">Chiết khấu</th>
            <th style="width: 20%;">Thành tiền</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
<script type="text/javascript">
    $( document ).ready(function() {
        load_contract_sale_info(<?php echo $contract_id; ?>);
    });
</script>
<?php
}?>
</div>