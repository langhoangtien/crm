<table class="table table-bordered">
        <thead>
           
            <tr>
                <th></th>
                <?php
                foreach ($location as $k => $val) {
                ?>
                    <th><?php echo $val['name'] ?></th>
               <?php    
                }
                ?>
                <th>Khối tư vẫn(tổng)</th>
            </tr>
        </thead> 
        <tbody id="fixedcolumnbody-detail">
            <?php
            foreach ($view_row as $k_row => $val_row) {
            ?>
            <tr>
                <td><?php echo $val_row ?></td>
                 <?php
                foreach ($location as $k => $val) {
                ?>
                <td style="text-align: right;"><input style="text-align: right;" type="" value="<?php echo number_format( $view_data[$val['location_id']][$k_row] )?>" name="<?php echo $k_row.'_'.$val['location_id'] ?>" class="input_inventory"></td>
                <?php 
                }
                ?>
                <td><input type="" name=""></td>
            </tr>
            <?php
            }
            ?>  
        </tbody>
    </table>

    <div class="col-md-5" style="text-align: right;"><input class="submit_button btn btn-primary" type="button" name="" value="Xóa" onclick="delete_data()"></div>
    <div class="col-md-5"><input class="submit_button btn btn-primary" type="button" name="" value="Cập nhật" onclick="save()"></div>
