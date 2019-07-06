<?php
$slb_customers = item_select_customers();
?>
<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Khách hàng :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select name="customer_id" id="customer_id">
            <?php
            foreach($slb_customers as $key => $val) {
                ?>
                <option value="<?php echo $key; ?>"<?php if($customer_id == $key) echo ' selected'; ?>><?php echo $val; ?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<script type="text/javascript">
    $('#customer_id').select2();
</script>