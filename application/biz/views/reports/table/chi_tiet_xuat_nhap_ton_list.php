<?php
    if(!empty($items)) {
        foreach ($items as $value) {
?>
            <tr>
                <td class="text-center" colspan="1"><?php echo $value['trans_date']; ?></td>
                <td class="text-center"><?php echo $value['MA_SAN_PHAM']; ?></td>
                <td class="text-center"><?php echo $value['ton_dau']; ?></td>
                <td class="text-center"><?php echo round($value['xuat_kho'],4); ?></td>
                <td class="text-center"><?php echo round($value['nhap_kho'],4); ?></td>
                <td class="text-center"><?php echo $value['ton_cuoi']; ?></td>
                <td class="text-center" style="text-align: left;"><?php echo $value['trans_comment']; ?></td>
                <td class="text-center"><?php echo $value['location_id']; ?></td>

            </tr>
        <?php } ?>
           
<?php } else { ?>
        <tr style="cursor: pointer;"><td colspan="10"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>

