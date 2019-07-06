<?php

?>

<!--table 3-->

<div class="container">
    <h3>
        TH/KH Doanh thu VCB giao
    </h3>

</div>

<table class="table" id="table_kpi_profit">
    <tbody>
    <tr>
        <td class="" rowspan="2">Khu vực</td>
        <td class="" style="text-align: center;" colspan="5">TH/KH doanh thu VCB giao</td>
        <td class="" style="text-align: center;" colspan="5">TH/KH lợi nhuận VCB giao</td>
    </tr>
    <tr>
        <td class="" style="text-align: center;">Quý I(%)</td>
        <td class="" style="text-align: center;">Quý II(%)</td>
        <td class="" style="text-align: center;">Quý III(%)</td>
        <td class="" style="text-align: center;">Quý IV(%)</td>
        <td class="" style="text-align: center;">Lũy Kế(%)</td>
        <td class="" style="text-align: center;">Quý I(%)</td>
        <td class="" style="text-align: center;">Quý II(%)</td>
        <td class="" style="text-align: center;">Quý III(%)</td>
        <td class="" style="text-align: center;">Quý IV(%)</td>
        <td class="" style="text-align: center;">Lũy Kế(%)</td>
    </tr>
    <?php
    if ($location) {
        $index = 1;

        foreach ($location as $key => $value) {
          
            $value_data = json_decode($kpi_thkh_convert[$key]);
            $total_revenue=0;
            $total_profit=0;
             echo '<tr>
                     <td width="9%">' . $value . '</td>';
                     for ($i=1; $i < 5; $i++) {
                     $revenue = 0; 
                      if($view_data[$key]['revenue'][$i+3]['value']>0)
                      $revenue = ($data_revenue_profit[$key][$i]['revenue']/$view_data[$key]['revenue'][$i+3]['value'])*100;
                      if($i==1) $kvt1+=round($revenue,2);
                      if($i==2) $kvt2+=round($revenue,2);
                      if($i==3) $kvt3+=round($revenue,2);
                      if($i==4) $kvt4+=round($revenue,2);
                      echo' <td width="9%" style="text-align: right;">'.number_format(round($revenue,2),2).'</td>';
                      $total_revenue+=round($revenue,2);
                     }
                     echo'
                     <td width="9%" style="text-align: right;">' .number_format(round($total_revenue,2),2). '</td>';
                     for ($i=1; $i <5 ; $i++) { 
                        $profit = 0; 
                         if($view_data[$key]['profit'][$i+3]['value']>0)
                          $profit = ($data_revenue_profit[$key][$i]['profit']/$view_data[$key]['profit'][$i+3]['value'])*100;
                        echo' <td width="9%" style="text-align: right;">'.number_format(round($profit,2),2).'</td>';
                        if($i==1) $kvt5+=round($profit,2);
                        if($i==2) $kvt6+=round($profit,2);
                        if($i==3) $kvt7+=round($profit,2);
                        if($i==4) $kvt8+=round($profit,2);
                        $total_profit+=round($profit,2);
                     }
                     echo'<td width="9%" style="text-align: right;">' .number_format(round($total_profit,2),2). '</td>
                  </tr>';
            $index++;
        }
    }

    ?>

    <tr>
        <td>Khối tư vấn</td>
        <td style="text-align: right;"><?php echo number_format(round($kvt1,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt2,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt3,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt4,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt1,2)+round($kvt2,2)+round($kvt3,2)+round($kvt4,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt5,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt6,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt7,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt8,2),2) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($kvt5,2)+round($kvt6,2)+round($kvt7,2)+round($kvt8,2),2) ?></td>
    </tr>
    </tbody>
</table>



