<?php
$slb_loations = item_select_group_of_location(null, array('task'=>'not-all'));
?>
<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Nhóm :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select class="form-control" name="group_ids[]" id="group_ids" multiple>
            <?php
            if(!empty($slb_loations)) {
                foreach($slb_loations as $key => $val) {
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
    $('#group_ids').selectize();
</script>