<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        $stt = 0;
        foreach($items as $item) { $stt++?>
            <tr>
                <td class="text-center"><?php echo $stt; ?></td>
                <td class="text-left"><a href="javascript:void(0)" target="_blank"><?php echo $item['code']; ?></a></td>
                <td class="text-left"><?php echo $item['company_name']; ?></td>
                <td class="text-right" id="tong-no-dau-ky"><?php echo number_format($item['tai_khoan_khach_no'],2); ?></td>
                <td class="text-right" id="tong-no-cuoi-ky"><?php echo  number_format($item['tai_khoan_no_khach'],2); ?></td>
            </tr>

<?php } } else { ?>
        <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>