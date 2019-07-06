<?php $this->load->view("partial/header"); ?>
<?php
?>  
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h3 class="text-center">
                   <?php 
                   if (empty($check_duyet_da->ratio)) {
                    // update
                    echo 'Duyệt dự án';
                }else{
                    echo 'Cập nhật tỷ lệ dự án';
                }
                ?>
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3 col-sm-6 col-xs-6">
                Mã KPI:
                <input type="text" name="code_kpi" id="code_kpi" disabled class="text-left text-white form-control" value="<?php echo $tasks_update->kpi_id ?>" style="background: #3CBC8D;">
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 tb_loi_luu_kpi">
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table tablesorter table-reports table-bordered" id="grid_table_kpi_approve">
                <thead>
                    <th align="center" style="width: 10%" data-field="name">Mã dự án</th>
                    <th align="center" style="width: 10%;" data-field="prioty">Tên dự án</th>
                    <th align="center" style="width: 10%" data-field="date_start">Tên nhân viên
                    </th>
                    <th align="center" style="width: 10%" data-field="date_start">Vai trò
                    </th>
                </th>
                <th align="center" style="width: 10%;" data-field="date_end">Chức vụ</th>
                <th align="center" style="width: 10%;">Tỷ lệ <span>(%)</span></th>
                <!-- <th align="center" style="width: 10%;">Hành động</th>     -->
            </thead>
            <tbody id="body_table_approve">
                <?php  
                $row = count($nguoi_tham_gia_da)+count($nguoi_phu_trach)+1;
                if (count($nguoi_tham_gia_da)>1) {

                    ?>
                    <tr class="row_project_grid_template" data-id-project="<?php echo $name_tasks->id; ?>">
                        <!-- ma du an -->
                        <td class="row_project_project_id" rowspan="<?php echo $row; ?>" align="center">
                            <?php
                            echo $name_tasks->id;
                            ?>
                        </td>
                        <!-- ten du an -->
                        <td class="row_project_name" rowspan="<?php echo $row; ?>" align="center">
                            <?php echo $name_tasks->name; ?>
                        </td>
                    </tr>
                    <?php
                }
                $i=0;
                if (!empty($nguoi_tham_gia_da)) {
                    foreach ($nguoi_tham_gia_da as $key => $value) {

                        ?>
                        <tr>
                            <?php  
                            if (count($nguoi_tham_gia_da)==1) {
                                ?>
                                <td class="row_project_project_id"  align="center">
                                    <?php
                                    echo $name_tasks->id;
                                    ?>
                                </td>
                                <!-- ten du an -->
                                <td class="row_project_name" align="center">
                                    <?php echo $name_tasks->name; ?>
                                </td>
                                <?php
                            }
                            ?>
                            <td class="row_<?php echo $i;?>" data-id="<?php echo $value['employee_id']; ?>"><?php echo $value['username']; ?></td>
                            <td>
                                <?php  
                                if ($value['is_implement']==1 && $value['is_join']==0) {
                                    echo "Người phụ trách";
                                }
                                if($value['is_join']==1 && $value['is_implement']==0){
                                    echo "Người tham gia";
                                }
                                if($value['is_join']==1 && $value['is_implement']==1){
                                    echo "Người tham gia, Người phụ trách";
                                }
                                ?>
                            </td>
                            <td><?php echo $value['chucvu'];?></td>
                            <td class="text-center">
                                <?php 
                                if (!empty($this->Kpi_Person->get_kpi_task((int)$this->uri->segment(3))->ratio)) {
                                    $arr_ty_le = json_decode($tasks_update->ratio);
                                    $arr_imployee_id=json_decode($tasks_update->employee_id);
                                    for ($j=0; $j < count($arr_imployee_id) ; $j++) {
                                        if ($arr_imployee_id[$j] == $value['employee_id']) {
                                            echo '<input type="number" value="'.$arr_ty_le[$j].'"  name="progess" max="100" min="0" id="ty_le_'.$i.'">';
                                        }
                                    }
                                    if (!in_array($value['employee_id'], $arr_imployee_id)) {
                                        $j++;
                                        echo '<input type="number"  name="progess" max="100" min="0" id="ty_le_'.$i.'">';
                                    }
                                }else{
                                    echo '<input type="number"  name="progess" max="100" min="0" id="ty_le_'.$i.'">';
                                }
                                ?> 
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div> 
    <a href="<?php echo base_url().'kpiPerson'; ?>" class="btn btn-primary pull-left"><i class="fa fa-backward"></i> Trở về trang trước</a> 
    <button class="btn btn-success pull-right" onclick="luu_thuc_hien(0);"><i class="fa fa-save"></i> Lưu cập nhật </button>

</div>
</div>
</div>
<style type="text/css">
.ul{
    margin:0;
    padding: 0;
}
.ul li{
    margin:0;
    padding: 0;
    border-bottom: 1px solid #ccc;
    height: 35px;
}
.ul li:last-child{
    border-bottom: none;
}
.ul li span:hover{
    cursor: pointer;
    border: 1px solid #ccc;
    border-radius: 3px;
    background: #ccc;
}
.add_employee_to_project:hover{
    cursor: pointer;
}
</style>

<script type="text/javascript">

    function luu_thuc_hien(check){
        var arr_employee_id = new Array();
        var arr_ty_le   = new Array();
        var thongbaoloi = '<div class="tb-errors alert alert-danger text-danger err_kpi_save col-md">Lỗi!Tổng tỷ lệ của dự án KHÔNG được lớn hơn 100% và KHÔNG được nhỏ hơn 0!</div>';
        count_li = $('#body_table_approve tr').length;
        // arr_ty_le       = parseFloat($('#ratio').val());
        if (count_li==1) {
            arr_employee_id[0]  = $('.row_0').attr('data-id');
            arr_ty_le[0] = parseFloat($('#ty_le_0').val());
        }else{
            for (var i = 0; i < count_li-1; i++) {
                arr_employee_id[i]  = $('.row_'+i).attr('data-id');

                if ($('#ty_le_'+i).val()=='') {
                    arr_ty_le[i] =0;
                }else{
                    arr_ty_le[i] = parseFloat($('#ty_le_'+i).val());
                }
            }
        }
        

        sum=0;
        for (var i = 0; i < arr_ty_le.length; i++) {
            sum+=(arr_ty_le[i]);
        }
        console.log(arr_ty_le.length);
        // console.log(arr_ty_le);
        if (sum>100 || sum<0) {
            $('.tb_loi_luu_kpi').html(thongbaoloi);
            $('#ty_le_0').focus();
        }else{

            task_id  = parseInt($('.row_project_project_id').text());
            $.ajax({
                url: "<?php echo base_url().'kpiPerson/luu_phe_duyet';?>",
                type: 'POST',
                dataType: 'json',
                data: {task_id: task_id,arr_employee_id:arr_employee_id,arr_ty_le:arr_ty_le, check:check},
            })
            .done(function(data) {
                if(data.success==true){
                    toastr.success('Lưu cập nhật thành công');
                }else{
                    $('.tb_loi_luu_kpi').html(thongbaoloi);
                }
            })
            .fail(function() {
                var thongbaoloi = '<div class="tb-errors alert alert-danger text-danger err_kpi_save col-md">Lỗi! Quá trình phê duyệt đang gặp sự cố!</div>';
                $('.tb_loi_luu_kpi').html(thongbaoloi);
            })
            .always(function() {
            });
        }
        setTimeout(function(){
            $('.tb_loi_luu_kpi .tb-errors').fadeOut();
        }, 10000);
        
    }
    
    $('[id^="ty_le_"]').keypress(function(event) {
        /* Act on the event */
        var thongbaoloi = '<div class="alert alert-warning text-danger err_kpi_save">CHÚ Ý! Tỷ lệ phần trăm của dự án KHÔNG được lớn hơn 100% và KHÔNG được nhỏ hơn 0!</div>';
        length_tyle = $(this).val().length;
        count = $('.ul li input').length;
        tyle = ((parseFloat($(this).val())*10));
        if (length_tyle+1>2 || parseFloat($(this).val())>100) {
            $('.tb_loi_luu_kpi').html(thongbaoloi);
        }
    });
    function xoa_ty_le(){
        for (var i = 0; i < $('#ty_le_kpi ul li').length; i++) {
            $('#ty_le_'+i).val('');
        }
        $('#ty_le_0').focus();
    }
</script>


















