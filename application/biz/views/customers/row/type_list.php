<?php
if(!empty($items)) {
    foreach($items as $val) {
        if($page > 1)
            $ext = '/' . $page;
        else
            $ext = '';

        $id         = $val['customer_type_id'];
        $code       = $val['code'];
        $name       = $val['name'];
        $desc       = nl2br($val['desc']);

        $link_edit = base_url() . 'customers/view_type_list/' . $id . $ext;
?>
        <tr style="cursor: pointer;">
            <td class="cb"><input type="checkbox" value="<?php echo $id; ?>" class="file_checkbox"><label><span></span></label></td>
            <td class="cb center"><?php echo $id; ?></td>
            <td class="cb"><?php echo $code; ?></td>
            <td><a href="<?php echo $link_edit; ?>"><?php echo $name; ?></a></td>
            <td class="cb"><?php echo $desc; ?></td>
            <td class="center"><a href="<?php echo $link_edit; ?>" class="update-person" title="Cập nhật">Sửa</a></td>
        </tr>

<?php
    }
}else {
?>
    <tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
}
?>
