<table class="table" id="table_kpi_profit">
    <tbody>
    <tr>
        <td class="" rowspan="2">Khu vực</td>
        <td class="" style="text-align: center;" colspan="5">KPI lợi nhuận HĐTV VCBS giao</td>
        <td class="" style="text-align: center;" colspan="5">KPI lợi nhuận VCB giao</td>
    </tr>
    <tr>
        <td class="" style="text-align: center;">Quý I</td>
        <td class="" style="text-align: center;">Quý II</td>
        <td class="" style="text-align: center;">Quý III</td>
        <td class="" style="text-align: center;">Quý IV</td>
        <td class="" style="text-align: center;">Tổng</td>
        <td class="" style="text-align: center;">Quý I</td>
        <td class="" style="text-align: center;">Quý II</td>
        <td class="" style="text-align: center;">Quý III</td>
        <td class="" style="text-align: center;">Quý IV</td>
        <td class="" style="text-align: center;">Tổng</td>
    </tr>
    <?php
    if ($location) {
        $index = 1;

        foreach ($location as $key => $value) {
            $value_data = json_decode($kpi_profit_convert[$key]);
            echo '<tr>
                     <td width="9%">' . $value . '</td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '1" value="' . (isset($value_data[0]->value) ? $value_data[0]->value : '') . '"></td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '2" value="' . (isset($value_data[1]->value) ? $value_data[1]->value : '') . '"></td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '3" value="' . (isset($value_data[2]->value) ? $value_data[2]->value : '') . '"></td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '4" value="' . (isset($value_data[3]->value) ? $value_data[3]->value : '') . '" ></td>
                     <td width="9%" style="text-align: right;">' . ($value_data[0]->value + $value_data[1]->value + $value_data[2]->value + $value_data[3]->value) . '</td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '5" value="' . (isset($value_data[4]->value) ? $value_data[4]->value : '') . '"></td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '6" value="' . (isset($value_data[5]->value) ? $value_data[5]->value : '') . '"></td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '7" value="' . (isset($value_data[6]->value) ? $value_data[6]->value : '') . '"></td>
                     <td width="9%"><input placeholder="nhập dữ liệu" style="width: 90%;text-align: right;" class="input_inventory" name="profit' . $index . '8" value="' . (isset($value_data[7]->value) ? $value_data[7]->value : '') . '"></td>
                     <td width="9%" style="text-align: right;">' . ($value_data[4]->value + $value_data[5]->value + $value_data[6]->value + $value_data[7]->value) . '</td>
                  </tr>';
            $index++;
        }
    }
    ?>

   <!--  <tr>
        <td>Khối tư vấn</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr> -->
    </tbody>
</table>

