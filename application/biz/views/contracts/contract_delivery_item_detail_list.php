<?php
if(!empty($items))
    $count = count($items);
else
    $count = 0;
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">
                Danh sách sản phẩm <span title="total suppliers" class="badge bg-primary tip-left"><?php echo $count; ?></span>
            </h4>

        </div>
        <div class="modal-body">
            <div class="panel-body nopadding table_holder table-responsive">
                <table class="tablesorter table table-hover data-n9-table" id="tbl_delivery_items" data-scroll="false">
                    <thead>
                    <tr>
                        <th>Tên</th>
                        <th style="width: 20%;">Đơn giá</th>
                        <th style="width: 15%;">Số lượng</th>
                        <th style="width: 100px;">Đơn vị</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($items)) {
                        foreach($items as $item) {
                            if(isset($item['item_id'])){
                                $link_detail = base_url() . 'home/view_item_modal/' . $item['item_id'];
                            }elseif(isset($item['item_kit_id']))
                                $link_detail = base_url() . '4biz2016/home/view_item_kit_modal/' . $item['item_kit_id'];

                            $name = $item['name'];
                            $price = to_currency($item['price']);
                            $quantity = (float)($item['quantity']);
                            $measure = $item['measure'];
                            ?>
                            <tr>
                                <td><a class="" href="<?php echo $link_detail; ?>" data-toggle="modal" data-target="#myModal"><?php echo $name; ?></a></td>
                                <td class="right">
                                    <?php echo $price; ?>
                                </td>
                                <td class="center">
                                    <?php echo $quantity; ?>
                                </td>
                                <td class="center">
                                    <?php echo $measure; ?>
                                </td>
                            </tr>
                        <?php
                        }
                    }else {
                        ?>
                        <tr style="cursor: pointer;"><td colspan="4"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>