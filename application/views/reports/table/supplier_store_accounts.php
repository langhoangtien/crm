
<?php
    if(!empty($items)) {
        $receive_prefix = $this->config->item('receive_prefix');
        foreach($items as $value) {
            if(!empty($value['code']))
                $code = $value['code'];
            elseif($value['receiving_id'] > 0)
                $code = $receive_prefix . ' ' . $value['receiving_id'];
            else
                $code = '';

            if(!empty($code))
                $link_order_detail = base_url() . 'receivings/receipt/' . $value['receiving_id'];
            else
                $link_order_detail = '#';
?>
            <tr>
                <td class="center"><?php echo $value['date']; ?></td>
                <td class="center"><a href="<?php echo $link_order_detail; ?>" target="_blank"><?php echo $code; ?></a></td>
                <td class="right"><?php echo to_currency($value['no_dau']); ?></td>
                <td class="right"><?php echo number_format($value['ghi_no'],2); ?></td>
                <td class="right"><?php echo number_format($value['ghi_co'],2); ?></td>
                <td class="right"><?php echo to_currency($value['no_cuoi']); ?></td>
                <td><?php echo $value['comment']; ?></td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>