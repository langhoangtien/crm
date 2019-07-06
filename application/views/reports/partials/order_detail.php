<?php
    $sale_id = $info['sale_id'];
?>
<table class="table table-bordered" id="order_detail_<?php echo $sale_id; ?>">
    <thead>
        <tr>
            <th>Tên</th>
            <th style="width: 15%;">Đơn giá</th>
            <th style="width: 20px;">Số lượng</th>
            <th style="width: 5%;">Đơn vị</th>
            <th style="width: 15%;">Giảm giá</th>
            <th style="width: 15%;">Thành tiền</th>
            <th style="width: 15%">Ghi chú</th>
        </tr>
    </thead>
    <tbody>
<?php
    $tong_tien = 0;
    $tong_giam_gia = 0;
    if(!empty($cart)) {

        foreach($cart as $val) {
            $product_name = $val['name'];
            if(isset($val['measure_qty'])) $quantity = $val['measure_qty'];
            else $quantity = '';
            
            $don_gia = $val['quantity_exchange'] * $val['unit_price'];
            if($val['tax_included'])
                $don_gia = $don_gia + $val['tax_by_unit'];

            $so_luong      = $val['quantity'];
            $giam_gia      = $don_gia * $so_luong * $val['discount_percent']/100;
            $tong_giam_gia = $tong_giam_gia + $giam_gia;
            $thanh_tien    = $don_gia * $so_luong - $giam_gia;
            $tong_tien     = $tong_tien + $thanh_tien;

            $thanh_tien  = to_currency($thanh_tien);
            $giam_gia    = to_currency($giam_gia);
            $don_gia     = to_currency($don_gia);
            $description = $val['description'];
            $don_vi      = $val['measure_name'];
?>
            <tr>
                <td><?php echo $val['name']; ?></td>
                <td class="right"><?php echo $don_gia; ?></td>
                <td class="center"><?php echo (float)$so_luong; ?></td>
                <td class="center"><?php echo $don_vi; ?></td>
                <td class="right"><?php echo (float)$val['discount_percent'].' %'; ?></td>
                <td class="right"><?php echo $thanh_tien; ?></td>
                <td><?php echo $description; ?></td>
            </tr>
<?php
        }
    }
?>
<?php if($giam_gia_ca_don_hang > 0): ?>
<?php $tong_giam_gia = $tong_giam_gia + $giam_gia_ca_don_hang; ?>
    <tr>
        <td colspan="6" class="right">Giảm giá đơn hàng</td>
        <td class="left"><?php echo to_currency($giam_gia_ca_don_hang); ?></td>
    </tr>
<?php endif; ?>
<?php $tong_tien = $tong_tien - $giam_gia_ca_don_hang; ?>
    <tr>
        <td colspan="6" class="right">Tổng tiền</td>
        <td class="left"><?php echo to_currency($tong_tien); ?></td>
    </tr>
<?php if($hoa_hong > 0): ?>
    <tr>
        <td colspan="6" class="right">Hoa hồng tổng đơn hàng</td>
        <td class="left"><?php echo to_currency($hoa_hong); ?></td>
    </tr>
<?php endif; ?>

    </tbody>
</table>
<?php
    if(!empty($chi_phi_sp_list)) {
?>
        <table class="table table-bordered" id="order_expense_<?php echo $sale_id; ?>">
            <thead>
            <tr>
                <th>Diễn giải</th>
                <th style="width: 100px;">Loại</th>
                <th style="width: 15%;">Số tiền</th>
                <th style="width: 15%;">Thuế</th>
            </tr>
            </thead>
            <tbody>
        <?php
        foreach($chi_phi_sp_list as $val) {
            $description = $val['expense_description'];
            if(empty($description))
                $description = $val['category_name'];

            if($val['expense_type'] == 1)
                $expense_type = 'Chi';
            elseif($val['expense_type'] == -1)
                $expense_type = 'Thu';

            $expense_amount = to_currency($val['expense_amount']);
            $expense_tax    = to_currency($val['expense_tax']);

        ?>
            <tr>
                <td class="left"><?php echo $description; ?></td>
                <td class="center"><?php echo $expense_type;?></td>
                <td class="right"><?php echo $expense_amount; ?></td>
                <td class="right"><?php echo $expense_tax; ?></td>
            </tr>
        <?php
        }
        ?>
                <tr>
                    <td class="right" colspan="3">Tổng chi phí</td>
                    <td class="right"><?php echo to_currency($chi_phi_sp);?></td>
                </tr>
            </tbody>
        </table>
<?php
    }
?>
