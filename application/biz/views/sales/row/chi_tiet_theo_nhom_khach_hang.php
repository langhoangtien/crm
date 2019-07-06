<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        $_SESSION['tong_no_dau_ky'] = 0;
        $_SESSION['tong_no_cuoi_ky'] = 0;
        foreach($items as $item) { ?>
            <tr>
                <td class="text-left"><a href="javascript:void(0)" target="_blank"><?php echo $item['code']; ?></a></td>
                <td class="text-left"><?php echo $item['last_name']; ?></td>
                <td class="text-left"><?php echo $item['phone_number']; ?></td>
                <td class="text-left"><?php echo $item['email']; ?></td>
                <td class="text-left"><?php echo $item['address_1']; ?></td>
                <td class="text-right" id="tong-no-dau-ky"><?php echo number_format($item['no_dau_ky'],2); ?></td>
                <td class="text-right"><?php echo number_format($item['ghi_no'],2); ?></td>
                <td class="text-right"><?php echo number_format($item['ghi_co'],2); ?></td>
                <td class="text-right" id="tong-no-cuoi-ky"><?php echo number_format($item['no_cuoi_ky'],2); ?></td>
            </tr>

<?php } } else { ?>
        <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>