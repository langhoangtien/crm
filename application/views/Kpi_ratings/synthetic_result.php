<table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th colspan="2">Chỉ tiêu</th>
                <th>Trọng số(%)</th>
                <th>Điểm</th>
            </tr>
        </thead> 
        <tbody id="fixedcolumnbody-detail">
            <tr>
                <td rowspan="2">1</td>
                <td rowspan="2">Tài chính</td>
                <td style="padding-left: 12px;">Doanh thu</td>
                <td style="text-align: right;"><?php echo ($view_data['TC_DT']) ?></td>
                <th style="text-align: right;"></th>
            </tr>
            <tr>
                <td>Lợi nhuận</td>
                <td style="text-align: right;"><?php echo number_format($view_data['TC_LN']) ?></td>
                <th style="text-align: right;"><?php echo ($d_ln) ?></th>
            </tr>
            <tr>
                <td rowspan="2">2</td>
                <td rowspan="2">Khách hàng</td>
                <td style="padding-left: 12px;">Đánh giá của KH bên ngoài về chất lượng dịch vụ</td>
                <td style="text-align: right;"><?php echo number_format($view_data['KH_BN']) ?></td>
                <th style="text-align: right;"><input style="text-align: right;" onchange="sum_load()" type="" value="<?php echo number_format($view_data_kq['D_DG_BN']) ?>" id='D_DG_BN' name="D_DG_BN" class="input_inventory"></th>
            </tr>
            <tr>
                <td>Đánh giá của KH nội bộ về chất lượng dịch vụ</td>
                <td style="text-align: right;"><?php echo number_format($view_data['KH_NB']) ?></td>
                <th style="text-align: right;"><input style="text-align: right;" onchange="sum_load()" type="" value="<?php echo number_format($view_data_kq['D_DG_NB']) ?>" id='D_DG_NB' name="D_DG_NB" class="input_inventory"></th>
            </tr>
            <tr>
                <td>3</td>
                <td colspan="2">Quy trình, nội quy,quy định nội bộ</td>
                <td style="text-align: right;"><?php echo number_format($view_data['QT']) ?></td>
                <th style="text-align: right;"><input style="text-align: right;" onchange="sum_load()" type="" value="<?php echo number_format($view_data_kq['D_QT']) ?>" id='D_QT' name="D_QT" class="input_inventory"></th>
            </tr>
             <tr>
                <td>4</td>
                <td colspan="2">Đào tạo và phát triển</td>
                <td style="text-align: right;"><?php echo number_format($view_data['DT']) ?></td>
                <th style="text-align: right;"><input style="text-align: right;" onchange="sum_load()" type="" value="<?php echo number_format($view_data_kq['D_DT']) ?>" id='D_DT' name="D_DT" class="input_inventory"></th>
            </tr> 
            <tr>
                <td colspan="3">Tổng</td>
                <td style="text-align: right;"><?php echo number_format($view_data['TH']) ?></td>
                <th style="text-align: right;"><samp id="total"></samp></th>
            </tr>
        </tbody>
    </table>

    <div class="col-md-5" style="text-align: right;"><input class="submit_button btn btn-primary" type="button" name="" value="Xóa" onclick="delete_data()"></div>
    <div class="col-md-5"><input class="submit_button btn btn-primary" type="button" name="" value="Cập nhật" onclick="save()"></div>
    <script type="text/javascript">
        
       function sum_load(){
            var th=0;
            var temp = ['D_DG_BN','D_DG_NB','D_QT','D_DT'];
            temp.forEach(function(item, index, array) {
              th += parseInt(document.getElementById(item).value);
            });
            $('#total').html(th);
        }
        $(document).ready(function () {
            sum_load();
        });
    </script> 
