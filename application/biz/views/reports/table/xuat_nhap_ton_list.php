<?php
    if(!empty($items)) {
        foreach ($items as $value) {
?>
            <tr>
                <td class="text-center" colspan="1"><?php echo $value['ma_san_pham']; ?></td>
                <td class="text-center"><?php echo $value['ten_san_pham']; ?></td>
                <td class="text-center"><?php echo round($value['ton_dau_ky'],2); ?></td>
                <td class="text-center"><?php echo to_currency($value['ton_dau_ky']*$value['gia_von']); ?></td>
                <td class="text-center"><?php echo round($value['so_luong_nhap_kho'],2); ?></td>
                <td class="text-center"><?php echo to_currency($value['so_luong_nhap_kho']*$value['gia_von']); ?></td>
                <td class="text-center"><?php echo round($value['so_luong_xuat_kho'],2); ?></td>
                <td class="text-center"><?php echo to_currency($value['so_luong_xuat_kho']*$value['gia_von']); ?></td>
                <td class="text-center"><?php echo round($value['ton_cuoi_ky'],2); ?></td>
                <td class="text-center"><?php echo to_currency($value['ton_cuoi_ky']*$value['gia_von']); ?></td>
            </tr>
        <?php } ?>
           
<?php } else { ?>
        <tr style="cursor: pointer;"><td colspan="10"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>

