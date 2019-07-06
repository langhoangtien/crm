<div class="form-group">
    <?php
    echo form_label('Văn bản :', '',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
    <div class="col-sm-9 col-md-9 col-lg-10 margin-bottom-10">
        <div class="input-group date">
            <span class="input-group-addon" onclick="remove(this);" style="background: #489ee7; color: white;"><i class="ion-trash-b"></i></span>
            <select name="document[]" id="document_<?php echo $quantity; ?>">
                <?php
                foreach($slb_template as $key => $val) {
                    ?>
                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
    </div>
    <script type="text/javascript">
        $('#document_<?php echo $quantity; ?>').select2();
    </script>
</div>


