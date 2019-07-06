
<?php if(!empty($category_child_total) && !empty($category_child)) { ?>
<!--danh mục con -->
<li id='category_child'>
    <select name="s_category_child" class="form-control data-n9-s" data-table="items_list" id="s_category_child">
        <option value="-1">Tất cả danh mục con</option>
<?php

    foreach($category_child_total as $val) {
?>
        <option value="<?php echo $val['id']; ?>" <?php if($val['id'] == $selected_category_child ) echo 'selected'; ?>><?php echo $val['name']; ?></option>
<?php
    }
?>
    </select>
</li>
<?php } ?>