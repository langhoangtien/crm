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
                <th>Khối tư vấn(tổng)</th>
            </tr>
        </thead> 
        <tbody id="fixedcolumnbody-detail">
            <?php
            foreach ($view_row as $k_row => $val_row){
            ?>
            <tr>
                <td><?php echo $val_row ?></td>
                 <?php
                 $total=0;
                 // echo "<pre>";
                 // var_dump($data_temp);
                foreach ($location as $k => $val){
                ?>
                <td style="text-align: right;"><?php
                  if($k_row=='CPHBTB'){
                    if(!empty($data_temp[$k_row][$val['location_id']][$year_sub])) echo number_format(round($data_temp[$k_row][$val['location_id']][$year_sub],2));
                     if(!isset($data_temp[$k_row][$val['location_id']][$year_sub]))
                        $data_temp[$k_row][$val['location_id']][$year_sub]=0;
                    if($data_temp[$k_row][$val['location_id']][$year_sub]!=0)
                        $total += round($data_temp[$k_row][$val['location_id']][$year_sub],2);
                  }
                  else{
                        if($k_row=='TDT' || $k_row=='CPC' || $k_row=='DTCPB'){
                            if(!empty($data_temp[$k_row][$val['location_id']][$year_sub])) echo number_format(round($data_temp[$k_row][$val['location_id']][$year_sub],2));
                            $total += round($data_temp[$k_row][$val['location_id']][$year_sub],2);
                        }
                        else{
                            if($k_row=='THKH'){
                              if(!empty($data_temp[$k_row][$val['location_id']])) echo number_format(round($data_temp[$k_row][$val['location_id']],2),2);   
                              $total += round($data_temp[$k_row][$val['location_id']],2);
                            }                            
                            else{
                                if(!empty($data_temp[$k_row][$val['location_id']])) echo number_format(round($data_temp[$k_row][$val['location_id']],2));
                                if(!isset($data_temp[$k_row][$val['location_id']]))
                                $data_temp[$k_row][$val['location_id']]=0;
                                if($data_temp[$k_row][$val['location_id']]!=0)
                                $total += round($data_temp[$k_row][$val['location_id']],2);
                            }
                                                
                        }
                    }
                  ?></td>
                <?php 
                }
                ?>
                <td style="text-align: right;"><?php echo number_format(round($total,2)); ?></td>
            </tr>
            <?php
            }
            ?>  
        </tbody>
    </table>

    <div class="col-md-5" style="text-align: right;"><input class="submit_button btn btn-primary" type="button" name="" value="In"></div>
    <div class="col-md-5"><input class="submit_button btn btn-primary" type="button" name="" value="Cập nhật"></div>
