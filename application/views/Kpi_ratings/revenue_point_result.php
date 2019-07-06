<table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2">Khu vực</th> 
                <th colspan="4">Doanh thu Kết quả</th> 
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
            $total_242 = $total_243 = $total_246 =$total_0=0;
            foreach ($location as $k => $val) {
            ?>
                <tr>
                    <td><?php echo $val['name'] ?></td>
                    <td style="text-align: right;"><?php 
                        if(!empty($report_contracts[$val['location_id']][1][$year_sub]))
                        echo number_format($report_contracts[$val['location_id']][1][$year_sub]); ?></td>
                    <td style="text-align: right;"><?php 
                        if(!empty($report_contracts[$val['location_id']][2][$year_sub]))
                        echo number_format($report_contracts[$val['location_id']][2][$year_sub]); ?></td>
                    <td style="text-align: right;"><?php 
                        if(!empty($report_contracts[$val['location_id']][7][$year_sub]))
                        echo number_format($report_contracts[$val['location_id']][7][$year_sub]); ?></td>
                    <td style="text-align: right;"><?php 
                         if(!empty($report_contracts[$val['location_id']][0][$year_sub]))
                        echo number_format($report_contracts[$val['location_id']][0][$year_sub]); ?></td>
                </tr>
            <?php
            if(!empty($report_contracts[$val['location_id']][1][$year_sub]))
                $total_242+=$report_contracts[$val['location_id']][1][$year_sub];
            if(!empty($report_contracts[$val['location_id']][2][$year_sub]))
                $total_243+=$report_contracts[$val['location_id']][2][$year_sub];
            if(!empty($report_contracts[$val['location_id']][7][$year_sub]))
                $total_246+=$report_contracts[$val['location_id']][7][$year_sub];
            if(!empty($report_contracts[$val['location_id']][0][$year_sub]))
                $total_0+=$report_contracts[$val['location_id']][0][$year_sub];
            }
            ?>
            <tr>
                <td>Khối tư vấn(Tổng)</td>
                <td style="text-align: right;"><?php echo number_format($total_242) ?></td>
                <td style="text-align: right;"><?php echo number_format($total_243) ?></td>
                <td style="text-align: right;"><?php echo number_format($total_246) ?></td>
                <td style="text-align: right;"><?php echo number_format($total_0) ?></td>
            </tr><tr>
                <td>TH/KH(%)</td>
                <td style="text-align: right;"><?php                      
                        echo (count($view_data)>0 && $view_data['tp']>0 && !empty($total_242) && !empty($view_data['tp']))?round(($total_242/$view_data['tp'])*100,2):0;
                    ?>
                </td>
                <td style="text-align: right;"><?php echo (count($view_data)>0 && $view_data['cp']>0 && !empty($total_243) && !empty($view_data['cp']))?round(($total_243/$view_data['cp'])*100,2):0;?></td>
                <td style="text-align: right;"><?php echo (count($view_data)>0 && $view_data['ma']>0 && !empty($total_246) && !empty($view_data['ma']))?round(($total_246/$view_data['ma'])*100,2):0;?></td>
                <td style="text-align: right;"><?php echo (count($view_data)>0 && $view_data['tv']>0 && !empty($total_0) && !empty($view_data['tv']))?round(($total_0/$view_data['tv'])*100,2):0;?></td>
            </tr><tr>
                <td>Điểm</td>
                <td style="text-align: right;"><?php echo $arr_temp['tp'] ?></td>
                <td style="text-align: right;"><?php echo $arr_temp['cp'] ?></td>
                <td style="text-align: right;"><?php echo $arr_temp['ma'] ?></td>
                <td style="text-align: right;"><?php echo $arr_temp['tv'] ?></td>
            </tr> <tr>
                <td>Tỉ trọng</td>
                <td style="text-align: right;"><input id="itp" style="text-align: right;" type="" value="<?php echo $view_tt['tp']?>" name="tp" class="input_inventory" onchange="sum_load(<?php echo $arr_temp['tp'] ?>,'tp','itp')"></td>
                <td style="text-align: right;"><input id="icp" style="text-align: right;" type=""  name="cp" value="<?php echo $view_tt['cp']?>" class="input_inventory" onchange="sum_load(<?php echo $arr_temp['cp'] ?>,'cp','icp')"></td>
                <td style="text-align: right;"><input id="ima" style="text-align: right;" type="" name="ma" value="<?php echo $view_tt['ma']?>" class="input_inventory" onchange="sum_load(<?php echo $arr_temp['ma'] ?>,'ma','ima')"></td>
                <td style="text-align: right;"><input id="itv" style="text-align: right;" type="" name="tv" value="<?php echo $view_tt['tv']?>" class="input_inventory" onchange="sum_load(<?php echo $arr_temp['tv'] ?>,'tv','itv')"></td>
                
            </tr> <tr>
                <td>Điểm doanh thu thực hiện</td>
                <td style="text-align: right;"><samp id='tp'><?php echo $view_tt['tp']*$arr_temp['tp'] ?></samp></td>
                <td style="text-align: right;"><samp id='cp'><?php echo $view_tt['cp']*$arr_temp['cp'] ?></samp></td>
                <td style="text-align: right;"><samp id='ma'><?php echo $view_tt['ma']*$arr_temp['ma'] ?></samp></td>
                <td style="text-align: right;"><samp id='tv'><?php echo $view_tt['tv']*$arr_temp['tv'] ?></samp></td>
            </tr>   
        </tbody>
    </table>

    <div class="col-md-5" style="text-align: right;"><input class="submit_button btn btn-primary" type="button" name="" value="In"></div>
    <div class="col-md-5"><input class="submit_button btn btn-primary" type="button" name="" onclick="save()" value="Cập nhật"></div>
 <script type="text/javascript">
        
       function sum_load(diem,vw,item){
            tt = document.getElementById(item).value;
            th = parseInt(diem)*parseInt(tt);
            $('#'+vw).html(th);
        }
    </script>
