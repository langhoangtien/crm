<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Nhân viên :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select name="employee_id" id="employee_id">
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
<script type="text/javascript">
    $('#employee_id').select2();
</script>