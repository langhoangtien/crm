<?php  
$row = count($nguoi_tham_gia_da)+1;
?>
<h4>Mã KPI:<span id="detail_kpi" class="text-success">
    <?php echo $kpi_approve->kpi_id; ?>
</span></h4>
<div class="table-responsive">
    <table class="table tablesorter table-reports table-bordered">
        <thead>
            <th align="center" style="width: 5%" data-field="name">Mã dự án</th>
            <th align="center" style="width: 25%;" data-field="prioty">Tên dự án</th>
            <th class="text-center" style="width: 8%;">Người phê duyệt</th>
            <th class="text-center" style="width: 8%;">Ngày phê duyệt</th>
            <th align="center" style="width: 12%;" data-field="prioty">Người phụ trách</th>
            <th align="center" style="width: 12%" data-field="date_start">Tên nhân viên</th>
            <!-- <th align="center" style="width: 12%;">Vai trò</th> -->
            <th align="center" style="width: 12%;" data-field="date_end">Chức vụ</th>
            <th align="center" style="width: 12%;">Tỷ lệ <span>(%)</span></th>
        </thead>
        <tbody>
            <tr>
                <td rowspan="<?php echo $row; ?>"><?php echo $kpi_approve->task_id; ?></td>
                <td rowspan="<?php echo $row; ?>">
                    <?php echo $kpi_approve->name; ?>
                </td>
                <td rowspan="<?php echo $row; ?>">
                    <?php
                    if (!empty($check_completed)) {
                        if (!empty($nguoi_phe_duyet)) {
                            foreach ($nguoi_phe_duyet as $key => $value) {
                                echo  !empty($value['username'])?$value['username']:'';
                            }
                        }
                    }
                    ?>
                </td>
                <td rowspan="<?php echo $row; ?>">
                    <?php  
                    if (!empty($check_completed)) {
                        echo date('d-m-Y',strtotime($kpi_approve->date_approve));
                    }
                    ?>
                </td>
                <td rowspan="<?php echo $row; ?>">
                    <?php  
                    if (!empty($nguoi_tham_gia_da)) {
                        foreach ($nguoi_tham_gia_da as $key => $value) {
                            if ($value['is_implement']==1) {
                                echo $value['username'].'<br>';
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php 
            if (!empty($nguoi_tham_gia_da)) {
                foreach ($nguoi_tham_gia_da as $key => $value) {
                    ?>
                    <tr>
                        <td><?php 
                        echo $value['username'];
                        ?></td>
                        <td><?php echo $value['chucvu']; ?></td>
                        <td class="text-center">
                            <?php
                            if (!empty($this->Kpi_Person->get_kpi_task($kpi_approve->task_id))) {
                                $arr_ty_le = json_decode($kpi_approve->ratio);
                                $arr_imployee_id=json_decode($kpi_approve->employee_id,true);
                                if (count($arr_imployee_id)>0) {
                                    if (in_array($value['employee_id'],$arr_imployee_id)) {
                                        for ($j=0; $j < count($arr_imployee_id) ; $j++) {
                                            if ($arr_imployee_id[$j] == $value['employee_id']) {
                                                echo $arr_ty_le[$j];
                                            }
                                        }
                                    }else{
                                        echo '<span class="text-danger">Chưa được chia KPI !</span>';
                                    }
                                }
                                
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }else{
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
            }
            ?>

        </tbody>
    </table>
</div>