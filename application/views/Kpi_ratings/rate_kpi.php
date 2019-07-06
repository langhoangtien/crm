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
            <span id="all_items"> Tỷ lệ KPI </span>
        </h3>
    </div>
    <div>
       <table class="abc">
           <tr>
                <th></th>
               <th>Từ</th>
               <th>Đến</th>
               <th>Điểm</th>
               <th></th>
           </tr>
          
            <?php
            foreach ($rate as $k => $val) {
            ?>
            <tr>
                <td>
                    <input name="chec" type="checkbox" value="<?php echo $k ?>" style="display: block;width: 30px;" id='<?php echo $k ?>'> 
                </td>
                <td><input style="text-align: right;" class="form-control form-inps" disabled="" type="" name="" id="start_<?php echo $k ?>" value="<?php echo $val['start'].$val['start_rate'] ; ?>"></td>
                <td><input style="text-align: right;" class="form-control form-inps" disabled="" type="" name="" id="end_<?php echo $k ?>" value="<?php echo $val['end'].$val['end_rate'] ; ?>"></td>
                <td><input style="text-align: right;" class="form-control form-inps" disabled="" type="" name="" id="point_<?php echo $k ?>" value="<?php echo $val['point'] ; ?>"></td>
                <td><a class="save_<?php echo $k ?>" style="display: none" onclick="edit(<?php echo $k ?>)">Lưu</a> <a class="save_<?php echo $k ?>" style="display: none" onclick="deletes(<?php echo $k ?>)"> | xóa</a></td>
           </tr>
           <?php
             }
           ?>
          <!-- <body class="abc"></body> -->
       </table>
       <input class="add_staged" type="button" name="" value="thêm mơi">
    </div>
</div> 
<script type="text/javascript">
   
    $(document).ready(function() {
        var count = 0;
        $('.add_staged').click(function(e){
           
            count += 1;
            var html = '';
            html += '<tr class="block_staged" id="row_'+count+'">';
            html += '<td></td>';
            html += '<td><input class="form-control form-inps" type="" id= "add_start_'+count+'" name="add_start_'+count+'"></td>';
            html += '<td><input class="form-control form-inps" type="" id="add_end_'+count+'" name="add_end_'+count+'"></td>';
            html += '<td><input class="form-control form-inps" type="" id="add_point_'+count+'" name="add_point_'+count+'"></td>';
            html += '<td class="save_'+count+'"><a onclick="save('+count+')">Lưu</a> | <a onclick="delete_data('+count+')">xóa</a></td>';
           
            html += '</tr';
            $('.abc').append(html);
            //alert(html);
        });

        $(document).on('click','#remove_staged',function(){
            $(this).parent().parent().remove();
        });
    });
   
    function save(id) {
        add_start = document.getElementById('add_start_'+id).value;
        add_end = document.getElementById('add_end_'+id).value;
        add_point = document.getElementById('add_point_'+id).value;
        $.ajax({
            type: "POST",
            url: BASE_URL + 'kpi_ratings/save_rate', 
            data: {
                start:add_start,
                end:add_end,
                point:add_point
                },
            success: function(html){
                console.log(html);
                alert("Thực hiện thành công");
                location.reload();
            }
        });
    }
    function edit(id) {
        start = document.getElementById('start_'+id).value;
        end = document.getElementById('end_'+id).value;
        point = document.getElementById('point_'+id).value;
        $.ajax({
            type: "POST",
            url: BASE_URL + 'kpi_ratings/edit', 
            data: {
                id:id,
                start:start,
                end:end,
                point:point
                },
            success: function(html){
                $('#'+id).filter(':checkbox').prop('checked',false);
                $("#start_"+id).prop('disabled', true);
                $("#end_"+id).prop('disabled', true);
                $("#point_"+id).prop('disabled', true);
                $(".save_"+id).hide();
                alert("Thực hiện thành công");
                location.reload();
            }
        });
    }
    function deletes(id) {
        $.ajax({
            type: "POST",
            url: BASE_URL + 'kpi_ratings/delete', 
            data: {
                id:id,
                },
            success: function(html){
                alert("Xóa thành công");
                location.reload();
                // $('#'+id).filter(':checkbox').prop('checked',false);
                // $("#start_"+id).prop('disabled', true);
                // $("#end_"+id).prop('disabled', true);
                // $("#point_"+id).prop('disabled', true);
                // $(".save_"+id).hide();
            }
        });
    }

    function delete_data(id){
        $("#row_"+id).hide();
    }
   
    $(function() { 
    $('input[type="checkbox"]').bind('click',function() {
    if($(this).is(':checked')) {
        $("#start_"+$(this).val()).prop('disabled', false);
        $("#end_"+$(this).val()).prop('disabled', false);
        $("#point_"+$(this).val()).prop('disabled', false);
        $(".save_"+$(this).val()).show();
     }
     else{
        $("#start_"+$(this).val()).prop('disabled', true);
        $("#end_"+$(this).val()).prop('disabled', true);
        $("#point_"+$(this).val()).prop('disabled', true);
        $(".save_"+$(this).val()).hide();
     }
    });
    });
   
</script> 