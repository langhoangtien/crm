<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        foreach($items as $val) {
            if(!empty($item['code']))
                $code = $item['code'];
            elseif($item['sale_id'] > 0)
                $code = $sale_prefix . ' ' . $item['sale_id'];
            else
                $code = '';

            if(!empty($code))
                $link_order_detail = base_url() . 'sales/receipt/' . $item['sale_id'];
            else
                $link_order_detail = '#';

            $date = $item['date_format'];

            if($item['transaction_amount'] >= 0) {
                $ghi_no = to_currency($item['transaction_amount']);
                $ghi_co = 0;
            }else {
                $ghi_no = 0;
                $ghi_co = to_currency(abs($item['transaction_amount']));
            }

            if($item['options'] == 1) {
                $no_dau = to_currency(($item['balance']) - $item['transaction_amount']);
                $no_cuoi = to_currency($item['balance']);
            }else {
                $no_dau = to_currency(($item['balance_2']) - $item['transaction_amount']);
                $no_cuoi = to_currency($item['balance_2']);
            }

            $comment = nl2br($item['comment']);
?>
            <tr>
                <td class="center"><?php echo $date; ?></td>
                <td class="center"><a href="<?php echo $link_order_detail; ?>" target="_blank"><?php echo $code; ?></a></td>
                <td class="right"><?php echo $no_dau; ?></td>
                <td class="right"><?php echo $ghi_no; ?></td>
                <td class="right"><?php echo $ghi_co; ?></td>
                <td class="right"><?php echo $no_cuoi; ?></td>
                <td><?php echo $comment; ?></td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>