<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        $sale_vat_relationship = $this->sale_lib->get_sale_vat_relationship();
        if(empty($sale_vat_relationship)) $sale_vat_relationship = array();

        foreach($items as $item) {
            $sale_id = $item['sale_id'];
            if(!empty($item['code']))
                $code = $item['code'];
            else
                $code = $sale_prefix . ' ' . $item['sale_id'];

            $link_order_detail = base_url() . 'sales/receipt/' . $item['sale_id'];

            $sale_time     = $item['sale_time_format'];
            $order_value   = to_currency($item['order_value']);
            $surplus       = to_currency($item['payment_value'] - $item['order_value']);

            if(in_array($sale_id, $sale_vat_relationship))
                $checked = ' checked="true"';
            else
                $checked = '';
?>
            <tr>
                <td class="cb"><input type="checkbox" value="<?php echo $sale_id; ?>" class="file_checkbox"<?php echo $checked; ?>><label><span></span></label></td>
                <td><a href="<?php echo $link_order_detail; ?>" target="_blank"><?php echo $code; ?></a></td>
                <td class="center"><?php echo $sale_time; ?></td>
                <td class="right"><?php echo $order_value; ?></td>
                <td class="right"><?php echo $surplus; ?></td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="5"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>