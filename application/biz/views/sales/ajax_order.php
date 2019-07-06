<table class="tablesorter table table-hover data-n9-table" data-table="contract_sales" style="border-top: 0;">
    <thead>
    <tr>
        <th>Tên</th>
        <th style="width: 20%;">Giá</th>
        <th style="width: 15%;">Số lượng</th>
        <th style="width: 10%;">Đơn vị tính</th>
        <th style="width: 10%;">Chiết khấu</th>
        <th style="width: 20%;">Thành tiền</th>
    </tr>
    </thead>
    <tbody>
<?php
    $currency_symbol = $this->config->item('currency_symbol');
    $discount_items  = array();
    foreach($cart as $key => $item) {
        if($item['item_id'] == $discount_id) {
            $discount_items[] = $item;
            unset($cart[$key]);
        }
    }

    $tong_donhang = $total;
    $total = 0;
    if(!empty($cart)) {
        foreach($cart as $item) {
            $name = $item['name'];
            $price = to_currency($item['price']);
            $quantity = $item['quantity'];
            $measure = $item['measure'];
            $total = $item['price'] * $item['quantity'];
            $total = $total - $item['discount']*$total/100;
            $total_all = $total_all + $total;
            $total = to_currency($total);
            if(isset($item['item_id'])){
                $link_detail = base_url() . 'home/view_item_modal/' . $item['item_id'];
            }elseif(isset($item['item_kit_id']))
                $link_detail = base_url() . '4biz2016/home/view_item_kit_modal/' . $item['item_kit_id'];

?>
        <tr style="cursor: pointer;">
            <td>
                <a class="" href="<?php echo $link_detail; ?>" data-toggle="modal" data-target="#myModal"><?php echo $name; ?></a>
            </td>
            <td class="cb"><?php echo $price; ?></td>
            <td class="cb center"><?php echo to_quantity($quantity); ?></td>
            <td class="cb center"><?php echo $measure; ?></td>
            <td class="cb center"><?php echo number_format($item['discount'],'2','.',','); ?>%</td>
            <td class="cb right bold;"></span><?php echo $total; ?></td>
        </tr>
<?php
        }

    }

    $total_all = to_currency($total_all);
    $info[] = 'Tổng tiền: ' . '<span class="bold">'.$total_all.'</span>';

    if(count($taxes) > 0) {
        foreach($taxes as $key => $val)
            $info[] = $key . ' : ' . '<span class="bold">'.to_currency($val).'</span>';
    }

    if(count($discount_items)>0) {
        $discount_total = 0;
        foreach($discount_items as $item)
            $discount_total = $discount_total + $item['price']*abs($item['quantity']);

        $info[] = 'Giảm giá: ' . '<span class="bold">'.to_currency($discount_total).'</span>';
    }

    $info[] = 'Tổng giá trị đơn hàng: ' . '<span class="bold">'.to_currency($tong_donhang).'</span>';
    $info = implode('<br />', $info);
?>

        <tr class="footer">
            <td colspan="6">
                <div class="col-log-12" style="text-align: right;">
                      <?php echo $info; ?>
                </div>
            </td>
        </tr>
    </tbody>

</table>