<?php
if(!empty($items)) {
    $receive_prefix = $this->config->item('receive_prefix');
    foreach($items as $item) {
        $code = $receive_prefix . ' ' . $item['receiving_id'];

        $link_detail = base_url() . 'receivings/receipt/' . $item['receiving_id'];

        $receiving_time     = $item['receiving_time_format'];
        $so_tien_no    = to_currency($item['so_tien_no']);
        $da_thanh_toan = to_currency($item['da_thanh_toan']);
        $con_lai_value = $item['so_tien_no'] - $item['da_thanh_toan'];
        $con_lai       = to_currency($item['so_tien_no'] - $item['da_thanh_toan']);
        $amount        = $item['amount'];
        ?>
        <tr>
            <td><a target="_blank" href="<?php echo $link_detail; ?>"><?php echo $code; ?></a></td>
            <td class="center"><?php echo $receiving_time; ?></td>
            <td class="right"><?php echo $so_tien_no; ?></td>
            <td class="right"><?php echo $da_thanh_toan; ?></td>
            <td class="right"><?php echo $con_lai; ?></td>
            <td class="center">
                <a href="#" class="x_update_store_payment" data-type="text" data-value="<?php echo $amount; ?>" data-limit="<?php echo $con_lai_value; ?>" data-pk="<?php echo $item['receiving_id'];?>" data-url="<?php echo base_url('receivings/update_payment_store'); ?>"><?php echo to_currency_without_unit($amount); ?></a> <?php echo $this->config->item('currency_symbol'); ?>
            </td>
        </tr>
        <?php
    }
}else {
    ?>
    <tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
    <?php
}
?>