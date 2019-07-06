<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        foreach($items as $val) {
            $sale_id     = $val['sale_id'];
            $code        = !empty($val['code']) ? $val['code'] : $sale_prefix . ' ' . $sale_id;
            $sale_time   = $val['sale_time_format'];
            $order_value = to_currency($val['order_value'] + $val['tax_value'] + $val['thu_value']);
            $profit      = $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['commission'] - $val['point_payment'] - $val['gift_card_payment'];
            $profit      = to_currency($profit);
            $comment     = $val['comment'];
            $payment_type  = $val['payment_type'];

            $link_sale = base_url() . 'sales/receipt/' . $sale_id;

?>
            <tr data-tree="<?php echo $sale_id; ?>">
                <td class="hidden-print" style="width: 25px; text-align: center; padding-left: 4px;"><a href="javascript:;" class="expand_all">+</a></td>
                <td class="center"><?php echo $sale_time; ?></td>
                <td><a href="<?php echo $link_sale; ?>" target="_blank"><?php echo $code; ?></a></td>
                <td class="right"><?php echo $order_value; ?></td>
                <td class="right"><?php echo $profit; ?></td>
                <td><?php echo $payment_type; ?></td>
                <td><?php echo $comment; ?></td>
            </tr>
            <tr data-parent="<?php echo $sale_id; ?>">
                <td colspan="7" class="innertable" style="display: none;">

                </td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>