<?php
$slb = array(
    '1' => $this->config->item('customer_balance'),
    '2' => $this->config->item('customer_balance_2'),
);
?>
<div class="form-group">
    <label class="col-sm-3 col-md-3 col-lg-2 control-label">Công nợ :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <select class="form-control" name="customer_balance_options" id="customer_balance_options">
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