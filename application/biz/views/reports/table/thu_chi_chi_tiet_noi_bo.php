<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        $stt = 0;
        foreach($items as $item) { $stt++?>
            <tr>
                <td class="text-center"><?php echo $item['id']; ?></td>
                <td class="text-center"><?php echo $item['expense_date']; ?></td>
                <td class="text-center"><?php echo $item['expense_description']; ?></td>
                <td class="text-center"><?php echo $item['expense_reason']; ?></td>
                <td class="text-center"><?php echo $item['nhan_vien']; ?></td>
                <td class="text-center"><?php echo $item['nhan_vien_phe_duyet']; ?></td>
                <td class="text-center"><?php echo number_format($item['tien_thu'],2); ?></td>
                <td class="text-center"><?php echo number_format($item['tien_chi'],2); ?></td>
            </tr>
           
<?php } } else { ?>
        <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>