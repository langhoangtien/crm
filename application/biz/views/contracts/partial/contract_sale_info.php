<?php
    $cart = $sale_info['cart'];
    $taxes = $sale_info['taxes'];
    $currency_symbol = $this->config->item('currency_symbol');
    $discount_items  = array();
    foreach($cart as $key => $item) {
        if($item['item_id'] == $discount_id) {
            $discount_items[] = $item;
            unset($cart[$key]);
        }
    }

    $tong_donhang = $sale_info['total'];
    $total = 0;

    if(!empty($cart)) {
        foreach($cart as $item) {
            $name = $item['name'];
            $price = to_currency($item['unit_price']);
            $quantity = (float)(abs($item['quantity']));
            $measure = $item['measure'];
            $total = abs($item['unit_price']) * abs($item['quantity']);
            $discount = number_format($item['discount'], 2).' %';
            $total = $total - $item['discount']*$total/100;
            $total_all = $total_all + $total;
            $total = to_currency($total);

            if(isset($item['item_id'])){
                $link_detail = base_url() . 'home/view_item_modal/' . $item['item_id'];
            }elseif(isset($item['item_kit_id']))
                $link_detail = base_url() . 'home/view_item_kit_modal/' . $item['item_kit_id'];
            ?>
            <tr style="cursor: pointer;">
                <td><a class="" href="<?php echo $link_detail; ?>" data-toggle="modal" data-target="#myModal"><?php echo $name; ?></a></td>
                <td class="cb"><?php echo $price; ?></td>
                <td class="cb center"><?php echo $quantity; ?></td>
                <td class="cb center"><?php echo $measure; ?></td>
                <td class="cb center"><?php echo $discount; ?></td>
                <td class="cb right bold;"><?php echo $total; ?></td>
            </tr>

        <?php
        }
    }
    $total_all = to_currency($total_all);
    $info = array();
    $info[] = 'Tổng tiền: <span class="bold">'.$total_all.'</span>';

    if(count($taxes) > 0) {
        foreach($taxes as $key => $val)
            $info[] = $key . ' : ' . '<span class="bold">'.to_currency($val).'</span>';
    }

    if(count($discount_items)>0) {
        $discount_total = 0;
        foreach($discount_items as $item)
            $discount_total = $discount_total + abs($item['price'])*abs($item['quantity']);

        $info[] = 'Giảm giá: ' . '<span class="bold">'.to_currency($discount_total).'</span>';
    }

    $info[] = 'Tổng giá trị đơn hàng: ' . '<span class="bold">'.to_currency(abs($tong_donhang)).'</span>';
    $info = implode('<br />', $info);
    ?>
    <tr class="footer">
        <td colspan="6">
            <div class="col-log-12" style="text-align: right;">
                <?php echo $info; ?>
            </div>
        </td>
    </tr>
