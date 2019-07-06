<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        $stt = 0;
        foreach($items as $item) { $stt++?>
            <tr>
                <td class="text-center"><?php echo $stt; ?></td>
                <td class="text-left"><?php echo $item['code']; ?></td>
                <td class="text-left"><?php echo $item['first_name'].' '.$item['last_name']; ?></td>
                <td class="text-left"><?php echo $item['customer_type_name']; ?></td>
                <td class="text-right" id="tong-no-dau-ky"><?php echo number_format($item['opening_balance'],2); ?></td>
                <td class="text-center"><?php echo number_format($item['debit'],2); ?></td>
                <td class="text-center"><?php echo number_format($item['manual_or_excel_debit'],2); ?></td>
                <td class="text-center"><?php echo number_format($item['credit'],2); ?></td>
                <td class="text-center"><?php echo number_format($item['manual_or_excel_credit'],2); ?></td>
                <td class="text-right" id="tong-no-cuoi-ky"><?php echo  number_format($item['closing_balance'],2); ?></td>
            </tr>

<?php } } else { ?>
        <tr style="cursor: pointer;"><td colspan="10"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php } ?>