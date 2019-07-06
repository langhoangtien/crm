<?php
$slb_loations = item_select_group_of_location();
?>
<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select class="form-control" name="group_id" id="group_id">
            <?php
            foreach($slb_loations as $key => $val) {
                ?>
                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>