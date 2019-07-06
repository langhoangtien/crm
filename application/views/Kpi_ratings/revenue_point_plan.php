<table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2">Khu vực</th> 
                <th colspan="4">Doanh thu kế hoạch</th> 
            </tr>
            <tr>
                <th>Bảo lãnh, đại lý phát hành trái phiếu</th>
                <th>Bảo lãnh, đại lý phát hành cổ phiếu</th>
                <th>M&A</th>
                <th>Tư vấn khác</th>
            </tr>
        </thead> 
        <tbody id="fixedcolumnbody-detail">
            <?php
            foreach ($location as $k => $val) {
            ?>
                <tr>
                    <td><?php echo $val['name'] ?></td>
                    <td style="text-align: right;"><input class="input_inventory" type="" style="text-align: right;" value="<?php echo $view_data[$val['location_id']]['tp'] ?>" name="tp_<?php echo $val['location_id'] ;?>" id="tp_<?php echo $val['location_id'] ;?>"></td>
                    <td style="text-align: right;"><input class="input_inventory" type="" style="text-align: right;" value="<?php echo $view_data[$val['location_id']]['cp'] ?>" name="cp_<?php echo $val['location_id'] ;?>" id="cp_<?php echo $val['location_id'] ;?>"></td>
                    <td style="text-align: right;"><input class="input_inventory" type="" style="text-align: right;" value="<?php echo $view_data[$val['location_id']]['ma'] ?>" name="ma_<?php echo $val['location_id'] ;?>" id="ma_<?php echo $val['location_id'] ;?>"></td>
                    <td style="text-align: right;"><input class="input_inventory" type="" style="text-align: right;" value="<?php echo $view_data[$val['location_id']]['tv'] ?>" name="tv_<?php echo $val['location_id'] ;?>" id="tv_<?php echo $val['location_id'] ;?>"></td>
                </tr>
            <?php
            }
            ?>    
        </tbody>
    </table>

    <div class="col-md-5" style="text-align: right;"><input class="submit_button btn btn-primary" type="button" name="" value="Xóa" onclick="delete_data()"></div>
    <div class="col-md-5"><input class="submit_button btn btn-primary" type="button" name="" value="Cập nhật" onclick="save()"></div>
