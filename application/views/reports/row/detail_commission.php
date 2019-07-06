<?php
    if(!empty($items)) {
        $sale_prefix = $this->config->item('sale_prefix');
        foreach($items as $val) {

            $sale_id      = $val['sale_id'];
            $code         = isset($val['code']) ? $val['code'] : $sale_prefix . ' ' . $sale_id;
            $sale_time    = $val['sale_time_format'];
            $order_value  = to_currency($val['order_value'] + $val['tax_value'] + $val['thu_value']);
            $profit       = $val['order_value'] + $val['thu_value'] - $val['chi_value'] - $val['cost_value'] - $val['commission'] - $val['point_payment'] - $val['gift_card_payment'];
            $profit       = to_currency($profit);
            $comment      = $val['comment'];
            $payment_type = $val['payment_type'];
			$commission   = '';
					
						
            foreach (explode(',',$employee_ids) as  $employee_id) { 
              if(isset($val['employee_commission_in_sale'][$employee_id]))
                {
                    $commission .= $val['employee_commission_in_sale'][$employee_id]['last_name'] .' '.$val['employee_commission_in_sale'][$employee_id]['first_name'].' :'.to_currency($val['employee_commission_in_sale'][$employee_id]['commission']).'<br>';
                }
                  
            }
          

            $link_sale       = base_url() . 'sales/receipt/' . $sale_id;
            $link_commission = base_url() . 'reports/detail_commission/'.$sale_id;
?>
            <tr data-tree="<?php echo $sale_id; ?>">
                <td class="hidden-print" style="width: 25px; text-align: center; padding-left: 4px;"><a href="javascript:;" class="expand_all">+</a></td>
                <td class="center"><?php echo $sale_time; ?></td>
                <td><a href="<?php echo $link_sale; ?>" target="_blank"><?php echo $code; ?></a></td>
                <td class="right"><?php echo $order_value; ?></td>
                <td class="right"><?php echo $profit; ?></td>
                <td class="right"><?php echo ($commission=='')?0:$commission; ?></td>
                <td><?php echo $comment; ?></td>
                <td align="center">
<?php if($val['commission'] > 0): ?>
                    <a href="<?php echo $link_commission; ?>" target="_blank" class="hidden-print"><i class="ion-printer" style="font-size: 20px;"></i></a>
                    &nbsp
<?php endif; ?>

                    <a href="<?php echo $link_sale; ?>" target="_blank"><i class="ion-document-text" style="font-size: 20px;"></i></a>
                </td>
            </tr>

            <tr data-parent="<?php echo $sale_id; ?>">
                <td colspan="8" class="innertable" style="display: none;">

                </td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>