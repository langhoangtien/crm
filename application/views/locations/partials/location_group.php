<?php
if(!isset($commission_percent))
    $commission_percent = 0;
?>
<div id="group_<?php echo $location_group_id; ?>_section" class="big-section">
    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm :</label>
        <div class="col-sm-9 col-md-9 col-lg-10" style="padding-top: 7px;">
            <span class="bold"><?php echo $group_info['name']; ?></span>
            <input type="hidden" name="location_group_id[]" value="<?php echo $location_group_id; ?>" />
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Danh sách :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <input type="text" id="group_<?php echo $location_group_id; ?>" name="group_<?php echo $location_group_id; ?>" class="form-control form-inps" placeholder="" autocomplete="off"  data-url="<?php echo base_url().'ajax/emp_list'; ?>"/>
            <div class="n9-autocomplete-result-list" id="group_<?php echo $location_group_id; ?>_select_list">
                List:
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Mặc định :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <input type="text" id="group_<?php echo $location_group_id; ?>_default" name="group_<?php echo $location_group_id; ?>_default" class="form-control form-inps" placeholder="" autocomplete="off"  data-url="<?php echo base_url().'ajax/emp_list'; ?>"/>
            <div class="n9-autocomplete-result-list" id="group_<?php echo $location_group_id; ?>_default_select_list">
                List:
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Trạng thái :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <select name="status[]" class="form-control">
                <option value="active"<?php if($status == 'active') echo ' selected'; ?>>Hiển thị</option>
                <option value="unactive"<?php if($status == 'unactive') echo ' selected'; ?>>Ẩn</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tỷ lệ :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <input type="text" name="commission_percent[]" value="<?php echo $commission_percent; ?>" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label">Tính theo :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <select name="commission_percent_type[]" class="form-control">
                <option value="profit"<?php if($commission_percent_type == 'profit') echo ' selected'; ?>>Lợi nhuận</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="" class="col-sm-3 col-md-3 col-lg-2 control-label" style="visibility: hidden;">Nhóm :</label>
        <div class="col-sm-9 col-md-9 col-lg-10">
            <a href="javascript:;" onclick="remove_group_section(this);">[Xóa nhóm]</a>
        </div>
    </div>
    <script type="text/javascript">
        $( document ).ready(function() {
            n9_autocomplete('group_<?php echo $location_group_id; ?>')
            n9_autocomplete('group_<?php echo $location_group_id; ?>_default');
        });

    </script>
</div>
