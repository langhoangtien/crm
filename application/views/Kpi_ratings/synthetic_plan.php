<table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th colspan="2">Chỉ tiêu</th>
                <th>Trọng số(%)</th>
            </tr>
        </thead> 
        <tbody id="fixedcolumnbody-detail">
            <tr>
                <td rowspan="2">1</td>
                <td rowspan="2">Tài chính</td>
                <td style="padding-left: 12px;">Doanh thu</td>
                <td style="text-align: right;"><input style="text-align: right;" onchange="sum_load()" type="" value="<?php echo number_format($view_data['TC_DT']) ?>" name="TC_DT" id="TC_DT" class="input_inventory"></td>
            </tr>
            <tr>
                <td>Lợi nhuận</td>
                <td style="text-align: right;"><input style="text-align: right;" type="" onchange="sum_load()" value="<?php echo number_format($view_data['TC_LN']) ?>" name="TC_LN" id="TC_LN" class="input_inventory"></td>
            </tr>
            <tr>
                <td rowspan="2">2</td>
                <td rowspan="2">Khách hàng</td>
                <td style="padding-left: 12px;">Đánh giá của KH bên ngoài về chất lượng dịch vụ</td>
                <td style="text-align: right;"><input style="text-align: right;" type="" onchange="sum_load()" value="<?php echo number_format($view_data['KH_BN']) ?>" name="KH_BN" id='KH_BN' class="input_inventory"></td>
            </tr>
            <tr>
                <td>Đánh giá của KH nội bộ về chất lượng dịch vụ</td>
                <td style="text-align: right;"><input style="text-align: right;" type="" onchange="sum_load()" value="<?php echo number_format($view_data['KH_NB']) ?>" name="KH_NB" id='KH_NB' class="input_inventory"></td>
            </tr>
            <tr>
                <td>3</td>
                <td colspan="2">Quy trình, nội quy,quy định nội bộ</td>
                <td style="text-align: right;"><input style="text-align: right;" type="" onchange="sum_load()" value="<?php echo number_format($view_data['QT']) ?>" id='QT' name="QT" class="input_inventory"></td>
            </tr>
             <tr>
                <td>4</td>
                <td colspan="2">Đào tạo và phát triển</td>
                <td style="text-align: right;"><input style="text-align: right;" type="" onchange="sum_load()" value="<?php echo number_format($view_data['DT']) ?>" id='DT' name="DT" class="input_inventory"></td>
            </tr> 
            <tr>
                <td colspan="3">Tổng</td>
                <td style="text-align: right;"><input id="total" type="hidden" value="<?php echo number_format($view_data['TH']) ?>" name="TH" class="input_inventory"><samp class ="total"><?php echo number_format($view_data['TH']) ?></samp></td>
            </tr>
        </tbody>
    </table>

    <div class="col-md-5" style="text-align: right;"><input class="submit_button btn btn-primary" type="button" name="" value="Xóa" onclick="delete_data()"></div>
    <div class="col-md-5"><input class="submit_button btn btn-primary" type="button" name="" value="Cập nhật" onclick="save()"></div>
    <script type="text/javascript">
        
       function sum_load(){
            var th=0;
            var temp = ['TC_DT','TC_LN','KH_BN','KH_NB','QT','DT'];
            temp.forEach(function(item, index, array) {
              th += parseInt(document.getElementById(item).value);
            });
            $('#total').val(th);
            $('.total').html(th);
        }
    </script>
