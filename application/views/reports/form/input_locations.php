<div class="form-group">
    <label for="" class="col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label">Các điểm bán hàng :</label>
    <div class="col-sm-9 col-md-9 col-lg-10">
        <ul id="reports_locations_list" class="list-inline">
            <?php
            if(!empty($locations)) {
                $locationIds = Report::get_selected_location_ids();
                foreach($locations as $val) {
                    $location_id = $val['location_id'];
                    $location_name = $val['name'];
                    if(in_array($location_id, $locationIds))
                        $checked = ' checked="checked"';
                    else
                        $checked = '';
                    ?>
                    <li>
                        <input type="checkbox" name="selected_location_ids[]" value="<?php echo $location_id;?>" id="selected_location_ids<?php echo $location_id;?>" class="reports_selected_location_ids_checkboxes"<?php echo $checked;?>>
                        <label for="reports_selected_location_ids1"><span></span><?php echo $location_name; ?></label>
                    </li>
                <?php
                }
            }
            ?>

        </ul>
    </div>
</div>
