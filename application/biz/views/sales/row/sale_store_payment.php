 <?php
 if (!empty($data)) {
     
    foreach ($data as $key => $value) {
?> 
            <tr>
                <td><a href="<?php echo $link_order_detail; ?>" target="_blank"><?php echo $value['code']; ?></a></td>
                <td class="center"><?php echo $value['sale_time']; ?></td>
                <td class="right"><?php echo $value['so_tien_no']; ?></td>
                <td class="right"><?php echo $value['da_thanh_toan']; ?></td>
                <td class="right"><?php echo $value['con_lai']; ?></td>
                <td class="center"><a href="#" class="x_update_store_payment" data-type="text" data-value="<?php echo $value['amount']; ?>" data-limit="<?php echo $value['con_lai_value']; ?>" data-pk="<?php echo $value['sale_id'];?>" data-url="<?php echo base_url('sales/update_payment_store'); ?>" data-title="Cập nhật công nợ"><?php echo to_currency_without_unit($value['amount']); ?></a> <?php echo $this->config->item('currency_symbol'); ?></td>
            </tr>
<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>