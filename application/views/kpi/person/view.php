<?php $this->load->view("partial/header"); ?>
<?php
//echo count($location);die();
?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-4">
                <h2>Kpi cá nhân theo dự án</h2>
            </div>
            <div class="col-md-4">

            </div>
            <div class="col-md-4">
                <div class="btn_add">
                    <?php  
                    if ($this->Employee->has_module_action_permission('kpi_person','add_update', $person_id)) {
                        ?>
                        <button class="btn btn-success pull-right" onclick="create_new_kpi();"> <i class="ion-plus"></i> Thêm mới kpi</button>  
                    <?php } ?>                        
                </div>

            </div>
        </div>
        <?php  
        if (!empty($thongbao)) {
            
        }else{
        ?>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table tablesorter table-reports table-bordered" id="grid_table_kpipersion">
                    <thead>
                        <th align="center" style="width: 5%">STT</th>
                        <th align="center" style="width: 8%">Mã dự án</th>
                        <th align="center" style="width: 10%;">Mã KPI</th>
                        <th align="center" style="width: 15%;">Tên dự án</th>
                        <th align="center" style="width: 15%">Khách hàng</th>
                        <th align="center" style="width: 10%;">Trạng thái</th>
                        <th align="center" style="width: 15%;">Thao tác</th>
                        <th align="center" style="width: 8%;">Xóa</th>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($tasks)) {
                            $i=1;
                            foreach ($tasks as $value) {
                                $kpi_id = $this->Kpi_Person->get_kpi_task($value['id'])->kpi_id;
                                ?>
                                <tr class="row_project_grid_template">
                                    <td class="text-center"><?php echo $i; ?></td>
                                    <td class="row_project_projectid" align="center"><?php echo $value['id']; ?></td>
                                    <td align="center" id="kpi_id_<?php echo $value['id']; ?>" class="detail_kpi_person">
                                        <a href="javascript:;" title="Chi tiết">
                                           <?php echo $kpi_id; ?>
                                       </a>
                                   </td>
                                   <td>

                                    <?php echo $this->Kpi_Person->find_name_task($value['id'])->name; ?>
                                    
                                </td>
                                <td class="row_customer_tasks">
                                    <ul style="padding: 0;">
                                        <?php
                                        $last_name = $this->Kpi_Person->get_info_customer($value['id'])->last_name;
                                        $first_name = $this->Kpi_Person->get_info_customer($value['id'])->first_name;
                                        if (empty($last_name)) {
                                            echo $first_name;
                                        }
                                        echo $last_name;
                                        ?>
                                    </ul>
                                </td>
                                <td class="row_project_start_date text-center">
                                    <?php 
                                    $this->Kpi_Person->get_status_complete($value['id']);
                                    ?>
                                </td>
                                <td class="text-center action-kpi-person<?php echo $value['id'];?>">
                                    <?php 

                                    $nguoi_phe_duyet = $this->Kpi_Person->check_task(array('user_id'=>$employee_id,'is_progress'=>true));
                                    $check_completed = $this->Kpi_Person->check_completed($value['id']);
                                    $nguoi_tham_gia = $this->Kpi_Person->check_task(array('user_id'=>$employee_id));
                                    if (empty($check_completed)) {
                                        if(!empty($kpi_id)) {
                                            if (!empty($this->Kpi_Person->get_kpi_task($value['id'])->ratio)) {
                                                if ($this->Employee->has_module_action_permission('kpi_person','approve', $person_id)) {
                                                    ?>
                                                    <button type="button" class="btn btn-success" onclick="modal_approve('<?php echo $value['id']; ?>');"  data-id="<?php echo $value['id']; ?>"><i class="ti-check"></i> Phê duyệt</button>
                                                    <?php
                                                }
                                            }
                                            if ($this->Employee->has_module_action_permission('kpi_person','add_update', $person_id)) {
                                                if (!empty($nguoi_tham_gia)) {
                                                    foreach ($nguoi_tham_gia as $key => $value_d) {
                                                        if ($value_d['task_id']==$value['id']) {
                                                            ?>
                                                            <a href="<?php echo base_url().'kpiPerson/update/'.$value['id']; ?>" class="btn btn-primary"><i class="fa fa-save"></i> Sửa</a>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                            <?php
                                        }
                                    }else{
                                        echo '<span class="text-success">Đã phê duyệt</span>';
                                    }
                                    ?>

                                </td>
                                <td class="text-center">
                                    <?php
                                    if (empty($check_completed)) {
                                        if(!empty($kpi_id)) {
                                            if (!empty($this->Kpi_Person->get_kpi_task($value['id'])->ratio)) {  
                                                if ($this->Employee->has_module_action_permission('kpi_person','approve', $person_id)) {
                                                    ?>
                                                    <button class="btn btn-danger" id="btn-delete_kpi_<?php echo $value['id']; ?>" onclick='del_kpi_person(<?php echo $value['id'] ?>);'> <i class=" fa fa-trash-o"></i> Xóa</button>  
                                                    <?php 
                                                }else{
                                                    echo '<span class="text-danger">Bạn chưa được cấp quyền!</span>';
                                                }
                                            }
                                        }
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
    </div>
    <?php  
        }
    ?>
    <!-- canh bao xoa du lieu -->
    <div class="modal fade" id="modal-check-PD">
        <div class="modal-dialog modal-sm" style="top:20%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="text-danger" id="title-tb-delele-kpi-person">Bạn có chắc chắn phê duyệt không?</h5>
                </div>
                <input type="hidden" id="task_id_PD" name="task_id">;
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btn-PD-kpi"><i class="ti-check"></i> Phê duyệt</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-close"></i> Hủy</button>
                </div>
            </div>
        </div>
    </div>
    <!-- canh bao xoa du lieu -->
    <div class="modal fade" id="modal-id-del-kpi">
        <div class="modal-dialog modal-sm" style="top:20%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="text-danger" id="title-tb-delele-kpi-person">Xác nhận xóa dữ liệu kpi cá nhân?</h5>
                </div>
                <input type="hidden" id="task_id_delete">
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btn-delete-kpi-person"><i class="fa fa-trash-o"></i> Xóa</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-close"></i> Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tạo mới kpi -->
    <div class="modal fade" id="modal_create_new_kpi">
        <div class="modal-dialog modal-lg" style="top:20%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Tạo mới kpi cá nhân</h4>
                </div>
                <div class="modal-body" id="body_new_kpi">
                    <div class="col-md-12">
                        <label class="col-md-2">Chọn dự án</label>
                        <div class="col-md-8 form-group">
                            <select class="form-control" id="select_name_tasks">
                            </select>
                            <div id="tb_create_new_kpi"></div>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <label class="col-md-2">Mã Kpi</label>
                        <div class="col-md-8 form-group">
                            <input type="text" name="code_kpi" id="create_new_kpi" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-save-create-new-kpi" onclick="save_create_new_kpi_person();"><i class="fa fa-save"></i> Lưu</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- modal detail kpi -->
    <div class="modal fade" id="detail-kpiperson">
        <div class="modal-dialog" style="top:20%;width: 70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Chi tiết Kpi cá nhân</h4>
                </div>
                <div class="modal-body" id="table_detail_kpi">


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Đóng</button>
                </div>
            </div>
        </div>
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
.action-kpi-person a{
    margin-right: 5px;
}
</style>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#grid_table_kpipersion').DataTable();
        // click vao phe duye da

        
        // chap nhan phe duyet 
        $('#btn-PD-kpi').on('click', function(event) {
            event.preventDefault();
            task_id = $('#task_id_PD').val();
            $.ajax({
                url: "<?php echo base_url().'kpiPerson/pheduyet'; ?>",
                type: 'POST',
                dataType: 'JSON',
                data: {task_id: task_id},
            })
            .done(function(data) {
                if (data.success==true) {
                    toastr.success('Phê duyệt thành công!');
                    $('#modal-check-PD').modal('hide');
                    $('#btn-delete_kpi_'+task_id).fadeOut();
                    $('.action-kpi-person'+task_id).html('<span class="text-success">Đã phê duyệt</span>');
                }else{
                    toastr.error('Phê duyệt không thành công!');
                }
            })
            .fail(function() {
            })
            .always(function() {
            });
            
        });
    });

    // $('#PD_task').on('click', function(event) {
    //     event.preventDefault();

    // });

    function modal_approve(id){
     $('#modal-check-PD').modal('toggle');
     $('#task_id_PD').val(id);
 }

 $('#btn-delete-kpi-person').on('click', function(event) {
    event.preventDefault();
    $('#modal-id-del-kpi').modal('hide');
    task_id =$('#task_id_delete').val();
    $.ajax({
        url: "<?php echo base_url().'kpiPerson/delete'; ?>",
        type: 'post',
        dataType: 'json',
        data: {task_id: task_id},
    })
    .done(function(data) {
        if (data.success==true) {
            $('#btn-delete_kpi_'+task_id).fadeOut();
            $('#kpi_id_'+task_id).html('');
            $('.action-kpi-person'+task_id).html('');
            toastr.success('Xóa thành công');
        }else{
            toastr.error('Bạn không được xóa!');
        }
    })
    .fail(function() {
                // console.log("error");
            })
    .always(function() {
                // console.log("complete");
            });

});
 $('.detail_kpi_person a').on('click', function(event) {
    event.preventDefault();
    /* Act on the event */
    kpi_id      = $(this).closest('tr').find('td:eq(2)').text();
    project_id  = parseInt($(this).closest('tr').find('td:eq(1)').text());
    project_name= $(this).closest('tr').find('td:eq(3)').text();
    $('#detail_kpi').html(kpi_id);
    $('#detail_project_id').html(project_id);
    $('#detail_project_name').html(project_name);
    $('#detail-kpiperson').modal('show');
    $.ajax({
        url: "<?php echo base_url().'kpiPerson/detail'; ?>",
        type: 'post',
        dataType: 'html',
        data: {project_id: project_id, kpi_id: kpi_id },
    })
    .done(function(data) {
        $('#table_detail_kpi').html(data);
    })
    .fail(function() {
    })
    .always(function() {
    });
});

 function create_new_kpi(){
    create = 1;
    $('#modal_create_new_kpi').modal('toggle');
    $.ajax({
        url: "<?php echo base_url().'kpiPerson/index'; ?>",
        type: 'post',
        dataType: 'json',
        data: {create: create},
    })
    .done(function(rs) {
        console.log(rs);
        html ='<option value="">Chọn</option>';
        if (rs.tasks.length >0) {
            for (var i = 0; i < rs.tasks.length; i++) {
                html +='<option value="'+rs.tasks[i].id+'">';
                html +=rs.tasks[i].name;
                html +='</option>';
            }
            $('#select_name_tasks').html(html);
            $('#create_new_kpi').val(rs.code_kpi);
        }
    })
    .fail(function() {
            // console.log("error");
        })
    .always(function() {
            // console.log("complete");
        });
}

function save_create_new_kpi_person(){
    task_id = $('#select_name_tasks').val();
    kpi_id = $('#create_new_kpi').val();
    if (task_id=='') {
        err = '<div class="alert alert-danger err_kpi_save">Chưa chọn dự án!</div>';
        $('#tb_create_new_kpi').html(err);
    }else{
        $.ajax({
            url: "<?php echo base_url().'kpiPerson/save_create_new_kpi'; ?>",
            type: 'post',
            dataType: 'json',
            data: {task_id: task_id,kpi_id:kpi_id},
        })
        .done(function(data) {

            if (data.exists==true) {
                err = '<div class="text-danger err_kpi_save">Mã KPI của dự án đã tồn tại!</div>';
                $('#tb_create_new_kpi').html(err);
            }else{
                if (data.success==true) {
                    $('#modal_create_new_kpi').modal('hide');
                    kpi = '<a href="javascript:;" title="Chi tiết">'+kpi_id+'</a>';

                    $('#kpi_id_'+task_id).html(kpi);
                    window.location.href= "<?php echo base_url().'kpiPerson/duyetda/'?>"+task_id;

                }else{
                    toastr.error('Bạn không thể tạo kpi');
                }
            }
        })
        .fail(function() {
            // console.log("error");
        })
        .always(function() {
            // console.log("complete");
        });
    }

    setTimeout(function(){
        $('#tb_create_new_kpi div').fadeOut();
    }, 5000);
}
    // xoa kpi
    function del_kpi_person(task_id){
        $('#modal-id-del-kpi').modal('toggle');
        $('#task_id_delete').val(task_id);
    }

</script>


















