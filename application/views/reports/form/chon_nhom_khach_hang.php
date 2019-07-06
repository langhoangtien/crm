<?php
$slb = nhom_khach_hang();
?>
<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm khách hàng :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select class="form-control" name="nhom_khach_hang" id="nhom_khach_hang">
            <?php
            foreach($slb as $key => $val) {
                ?>
                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>