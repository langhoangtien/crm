<?php
$slb_suppliers = item_select_suppliers();
?>
<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Nhà cung cấp :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select name="supplier_id" id="supplier_id">
            <?php
            foreach($slb_suppliers as $key => $val) {
                ?>
                <option value="<?php echo $key; ?>"<?php if($supplier_id == $key) echo ' selected'; ?>><?php echo $val; ?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<script type="text/javascript">
    $('#supplier_id').select2();
</script>