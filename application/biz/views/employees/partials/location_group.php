<div id="location_<?php echo $location_id; ?>_<?php echo $group_id; ?>_section" class="big-section">
    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm :</label>
        <div class="col-sm-9 col-md-9 col-lg-10" style="padding-top: 7px;">
            <span class="bold"><?php echo $group_info['name']; ?></span>
            <input type="hidden" name="lc_location_id[]" value="<?php echo $location_id; ?>">
            <input type="hidden" name="lc_group_id[]" value="<?php echo $group_id; ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tỷ lệ :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <input type="text" name="lc_percent[]" value="<?php echo $commission_percent; ?>" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tính theo :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <select name="lc_percent_type[]" class="form-control">
                <option value="selling_price"<?php if($commission_percent_type == 'selling_price') echo ' selected'; ?>>Giá bán</option>
                <option value="profit"<?php if($commission_percent_type == 'profit') echo ' selected'; ?>>Lợi nhuận</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label" style="visibility: hidden;">Nhóm :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <a href="javascript:;" onclick="remove_location_group(this);">[Xóa]</a>
        </div>
    </div>
</div>