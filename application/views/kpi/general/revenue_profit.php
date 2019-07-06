<?php
?>
<!--table 1-->
<div class="container">
    <h3>
        Doanh thu và lợi nhuận thực hiện
    </h3>

</div>

<table class="table" id="table_kpi_profit">
    <tbody>
    <tr>
        <td class="" rowspan="2">Khu vực</td>
        <td class="" style="text-align: center;" colspan="5">Doanh thu thực hiện</td>
        <td class="" style="text-align: center;" colspan="5">Lợi nhuận thực hiện</td>
    </tr>
    <tr>
        <td class="" style="text-align: center;">Quý I</td>
        <td class="" style="text-align: center;">Quý II</td>
        <td class="" style="text-align: center;">Quý III</td>
        <td class="" style="text-align: center;">Quý IV</td>
        <td class="default" style="text-align: center;">Lũy Kế</td>
        <td class="" style="text-align: center;">Quý I</td>
        <td class="" style="text-align: center;">Quý II</td>
        <td class="" style="text-align: center;">Quý III</td>
        <td class="" style="text-align: center;">Quý IV</td>
        <td class="default" style="text-align: center;">Lũy Kế</td>
    </tr>
    <?php
    if ($location) {
        $index = 1;
        // var_dump($data_revenue_profit);
        foreach ($location as $key => $value) {
            $mau = json_decode($kpi_revenue_begin[$key]);
            $tola = 0;
            $tola1 = 0;
            echo '<tr>
                   <td width="9%">' . $value . '</td>';
            for ($i=1; $i <5 ; $i++) { 
              echo ' <td width="9%" style="text-align: right;">'.number_format(round($data_revenue_profit[$key][$i]['revenue'],2)).'</td>';
              $tola += round($data_revenue_profit[$key][$i]['revenue'],2);
            }                  
            echo' <td width="9%" style="text-align: right;">' . number_format(round($tola,2)). '</td>';
            for ($i=1; $i < 5; $i++) { 
              echo ' <td width="9%" style="text-align: right;">'.number_format(round($data_revenue_profit[$key][$i]['profit'],2)).'</td>';
              $tola1 += round($data_revenue_profit[$key][$i]['profit'],2);
            } 
                echo '<td width="9%" style="text-align: right;">' .number_format(round($tola1,2)). '</td>
                </tr>';
            $index++;
        }
    }
    ?>
    <tr>
        <td>Khối tư vấn</td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[1]['revenue'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[2]['revenue'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[3]['revenue'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[4]['revenue'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[1]['revenue'],2)+round($ktv[2]['revenue'],2)+round($ktv[3]['revenue'],2)+round($ktv[4]['revenue'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[1]['profit'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[2]['profit'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[3]['profit'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[4]['profit'],2)) ?></td>
        <td style="text-align: right;"><?php echo number_format(round($ktv[1]['profit'],2)+round($ktv[2]['profit'],2)+round($ktv[3]['profit'],2)+round($ktv[4]['profit'],2)) ?></td>
    </tr>
    </tbody>
</table>
