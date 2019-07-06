<?php $this->load->view("partial/header"); ?>
<?php
//echo count($location);die();
?>
<div class="box">
    <div class="box-body">
        <div class="col-md-12">
            <h2 class="text-center">Thống kê KPI</h2>
        </div>
        <div class="row">
            <form method="post">
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <select class="select2" id="tk_employee" onchange="load_data();">
                        <option value="" class="text-center">---Chọn nhân viên---</option>
                        <?php 
                        foreach ($employees as $key => $value) {
                            echo '<option value="'.$value['employee_id'].'" class="text-center">'.$value['username'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="form-group">
                        <select class="form-control input_tk" id="tk_datetime" onchange="chon_moc_thoi_gian();">
                            <option value="">---Chọn mốc thời gian---</option>
                            <option value="month">Tháng</option>
                            <option value="months">Quý</option>
                            <option value="year">Năm</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6 select_month">
                    <div class="form-group">
                        <select class="form-control input_tk" id="tk_thang_quy" disabled onchange="load_data();">
                            <option value="">---Chọn---</option>
                        </select>
                        <div class="err-tk"></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6 select_year">
                    <div class="form-group">
                        <div class="form-group">
                            <select class="form-control input_tk" id="tk_year" disabled onchange="load_data();">
                                <option value="">---Chọn năm---</option>
                                <?php
                                foreach ($year as $key => $value) {
                                    echo '<option value"'.$value.'">'.$value.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table tablesorter table-reports table-bordered" id="grid_table_kpipersion">
                        <thead>
                            <th align="center" style="width: 30%;" data-field="name">Tên dự án</th>
                            <th align="center" style="width: 50%">Tên công việc</th>
                            <th align="center" style="width: 20%">Tỷ lệ</th>
                        </thead>
                        <tbody>
                            <td style="padding: 0;" id="ten_du_an"></td>
                            <td></td>
                            <td></td>
                        </tbody>
                    </table>
                </div>  
            </div>
        </div>
    </div>
</div>
<style type="text/css">
.input_tk{
    border-radius: 0px;
    height: 40px;
}
</style>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#grid_table_kpipersion').DataTable( {
            "order": [[ 0, "desc" ]]
        });
        $('.select2').select2();
    });
    
    function chon_moc_thoi_gian(){
        value = $('#tk_datetime').val();
        switch(value) {
            case 'month':
            html ='<option value="">---Chọn tháng---</option>';
            for (var i = 1; i <=12; i++) {
                html+='<option value="'+i+'">'+i+'</option>';
            }
            $('#tk_thang_quy').html(html);
            $('.select_month').fadeIn(500);
            $('.select_year').fadeIn(600);
            $('#tk_year').prop('disabled',false);
            $('#tk_thang_quy').prop('disabled',false);
            break;
            case 'months':
            html ='<option value="">---Chọn quý---</option>';
            html +='<option value="1-2-3">1</option>';
            html +='<option value="4-5-6">2</option>';
            html +='<option value="7-8-9">3</option>';
            html +='<option value="10-11-12">4</option>';
            $('#tk_thang_quy').html(html);
            $('.select_month').fadeIn(500);
            $('.select_year').fadeIn(600);
            $('#tk_year').prop('disabled',false);
            $('#tk_thang_quy').prop('disabled',false);
            break;
            case 'year':
            $('#tk_thang_quy').prop('disabled', true);
            $('#tk_year').prop('disabled', false);
            $('.select_month').fadeOut(500);
            $('.select_year').fadeIn(600);
            load_data();
            break;
        }
        
    }

    var nhan_vien ="";
    var thang_quy ="";
    var nam ="";

    function check(){
        nhan_vien = $('#tk_employee').val();
        thang_quy = $('#tk_thang_quy').val();
        nam = $('#tk_year').val();
        if (nhan_vien!='' && nam!='') {
            return true;
        }else{
            return false;
        }
    }

    function load_data(){
        check();
        if (check()) {
            dk = $('#tk_datetime').val();
            nhan_vien = $('#tk_employee').val();
            nam = $('#tk_year').val();

            switch(dk) {
                case 'month':
                thang = $('#tk_thang_quy').val();
                load_view_data(nhan_vien,nam,thang);
                break;
                case 'months':
                quy = $('#tk_thang_quy').val();
                load_view_data(nhan_vien,nam,quy);
                break;
                case 'year':
                load_view_data(nhan_vien,nam);
                break;
            }
        }
    }

    function load_view_data(ma_nv,nam,thoigian=null,){
        $.ajax({
            url: "<?php echo base_url().'kpiPerson/load_data_statstics';?>",
            type: 'post',
            dataType: 'json',
            data: {ma_nv: ma_nv, thoi_gian: thoigian,nam:nam},
        })
        .done(function(data) {
            // console.log(data.name);
            $('#ten_du_an').html(data.name);
        })
        .fail(function() {
            // console.log("error");
        })
        .always(function() {
            // console.log("complete");
        });
    }
</script>


















