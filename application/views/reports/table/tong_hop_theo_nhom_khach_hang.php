<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        $stt = 0;
        foreach($items as $item) { $stt++?>
            <tr>
                <td class="text-center"><?php echo $stt?></a></td>
                <td class="text-center"><?php echo $item['nhom_khach_hang']; ?></td>
                <td class="text-center"><?php echo number_format($item['tong_khach_no'],2); ?></td>
                <td class="text-center"><?php echo number_format($item['tong_no_khach'],2); ?></td>
            </tr>

<?php } } else { ?>
        <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>