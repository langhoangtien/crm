<?php
$slb_employees = item_select_employee();
?>
<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Nhân viên :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select class="form-control" name="employees[]" id="employees" multiple>
            <?php
            if(!empty($slb_employees)) {
                foreach($slb_employees as $key => $val) {
                    ?>
                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                <?php
                }
            }
            ?>

        </select>
    </div>
</div>
<script type="text/javascript">
    $('#employees').selectize();
</script>