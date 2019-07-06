<?php
if(!empty($items)) {
    $stt=1;
    foreach($items as $val) {
        $id   = $val['id'];
        $code = $val['code'];
        $name = $val['name'];
        $min_profit = to_quantity($val['min_profit']) . '%';

        $link_edit = base_url() . 'items/view_service/'.$id;
        if($page > 1)
            $link_edit = $link_edit . '/' . $page;
        ?>
        <tr style="cursor: pointer">
            <td class="cb"><input type="checkbox" value="<?php echo $id; ?>" class="file_checkbox"><label><span></span></label></td>
            <td class="cb center"><?php echo $stt; ?></td>
            <td class="cb"><?php echo $code; ?></td>
            <td class="cb">
                <?php
                $name_dv = $this->db->select('*')->from('phppos_items')->where('product_id',$name)->get()->row_array()['name'];
                echo $name_dv;
                ?>
            </td>
            <td class="center" style="padding: 4px;"><a href="<?php echo $link_edit; ?>">Sửa</a></td>
        </tr>
        <?php
        $stt++;
    }
}else {
    ?>
    <tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
    <?php
}
?>