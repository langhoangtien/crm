<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script>
<style type="text/css">
    th{
        text-align: center;
    }
    table{
        border: 1px;
    }
</style>
<div class="container-fluid">
    <div class="row manage-table">
        <div class="panel panel-piluku"><div class="panel-heading">
        <h3 class="panel-title space_title">
            <span id="all_items"> KPI xếp hạng đánh giá phòng </span>
        </h3>
            </div>
    <div>
        <div class="row">
            <div class="col-md-3">
                <select id="point" class="form-control" onchange="load_data()">
                    <option value="0">--Chọn loại--</option>
                    <option value="DDT">Điểm doanh thu</option>
                    <option value="DLN">Điểm lợi nhuận</option>
                    <option value="TH">Tổng hợp</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="point_sub" class="form-control" onchange="load_data()">
                    <option value="0">--Chọn loại--</option>
                    <option class="view_kh" value="KH">Kế hoạch</option>
                    <option value="KQ">Thực hiện</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="year" class="form-control" onchange="load_data()">
                    <option value="0">--Chọn năm--</option>
                    <?php foreach ($arr_year as $k => $val){?>
                    <option value="<?php echo $k ?>" <?php echo ($k==$year)?'selected="selected"':'' ?>><?php echo $val ?></option>
                <?php }?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="year_sub" class="form-control" onchange="load_data()">
                    <option value="0">--Chọn quý--</option>
                    <option value="1">Quý I</option>
                    <option value="2">Quý II</option>
                    <option value="3">Quý III</option>
                    <option value="4">Quý IV</option>
                    <option value="5">Cả năm</option>
                </select>
            </div>
        </div>
    </div>
<div class="panel-body nopadding table-responsive" id="list_view">
    
</div>
</div> 
<script type="text/javascript">
    var year_sub = "";
    var year ="";
    var point = "";
    var point_sub = "";
    function check_data(){
        year_sub = document.getElementById('year_sub').value;
        year = document.getElementById('year').value;
        point = document.getElementById('point').value;
        point_sub = document.getElementById('point_sub').value;
        if(year_sub!=0&&year!=0&&point!=0&&point_sub!=0)
            return true;
        else
            return false;
    }

    function load_data(){
    check_data();
    if(point=='DLN')
        $(".view_kh").hide();
    else
        $(".view_kh").show();
        if(check_data()){
           $.ajax({
            type: "POST",
            url: BASE_URL + 'kpi_ratings/view',
            data: {
                year_sub : year_sub,
                year : year,
                point:point,
                point_sub:point_sub
            },
            success: function(html){
                $('#list_view').html(html);
                if(point_sub=='KH' && point=='DLN')
                $('#list_view').html("");
            }
        });
        }
        else{
            $('#list_view').html("");
        }
    }

    function save(argument) {
        year_sub = document.getElementById('year_sub').value;
        year = document.getElementById('year').value;
        point = document.getElementById('point').value;
        point_sub = document.getElementById('point_sub').value;
        var arr_data = $('input.input_inventory').serializeArray();
        $.ajax({
            type: "POST",
            url: BASE_URL + 'kpi_ratings/save',
            data: {
                year_sub : year_sub,
                year : year,
                point:point,
                point_sub:point_sub,
                arr_data:arr_data
            },
            success: function(html){

                // console.log(html);
            }
        });
        alert("cập nhật thành công");
    }

    function delete_data(){
        arr_data = $('input.input_inventory').serializeArray();
        jQuery.each( arr_data, function( i, field ) {
            $('input[name='+field.name+']').val('');
        });
        
    }
</script> 