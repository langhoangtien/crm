<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<!-- <script type="text/javascript" src="<?php echo base_url() . 'assets/js/jquery-n9-gid.js'; ?>"></script> -->
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/contract-list.js" ></script>

<?php
$key_filter = 'contract_'.$option.'_filter';
$filter   = isset($_SESSION[$key_filter])?$_SESSION[$key_filter]:'';
// var_dump($key_filter);
$keywords = isset($filter['keywords'])?$filter['keywords']:'';
$s_type   = isset($filter['type'])?$filter['type']:'all';
if(!isset($filter['col'])) {
    $field_sort = 'date_signing';
    $class_sort = 'headerSortDown';
}else {
    $field_sort = $filter['col'];
    if($filter['order'] == 'ASC')
        $class_sort = 'headerSortUp';
    else
        $class_sort = 'headerSortDown';
}

$link_list    = base_url() . 'contracts/index/'.$option;
$link_circle  = base_url() . 'contracts/circle/'.$option;
$link_expired = base_url() . 'contracts/expired/'.$option;
?>


<style>
.gantt_title .tieude {
    padding: 10px 10px;
    background-color: #555;
    line-height: 40px;
    color: #fff;
    font-size: 14px;
}
.gantt_title .tieude a {
    color: #fff;
}

.gantt_title .active {
    background-color: #e64d27;
}
</style>  

<div class="manage_buttons">
    <div class="manage-row-options hidden" data-table="chan-doi">
        <div class="email_buttons text-center">
            <?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
                <a href="javascript:;" data-table="chan-doi" data-url="<?php echo base_url() . 'contracts/contract_delete'; ?>" class="btn btn-primary btn-red btn-lg"><?php echo "Xóa" ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="row hide">
        <div class="col-md-6 hide">
            <div id="search_form">
                <div class="search search-items no-left-border">
                    <ul class="list-inline">
                        <li>
                            <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                            <input type="text" class="form-control" name ='search' id='search' value="<?php echo $keywords;?>" placeholder="Tìm kiếm hợp đồng"/>
                            <input type="hidden" name="s_keywords" class="data-n9-s" data-table="pos_contract" value="<?php echo $keywords;?>" />
                            <input type="hidden" name="s_option" class="data-n9-s" data-table="pos_contract" value="<?php echo $option;?>" />
                        </li>
                        <li>
                            <select name="s_type" class="form-control data-n9-s" data-table="pos_contract" id="search_type">
                                <?php $s_types['all']='Trạng thái hợp đồng';
                                $s_types = array_merge($s_types, lang('contract_status')) ;
                                ?>
                                <?php foreach ($s_types as $key => $value) { 
                                   $selected = ($key==$s_type) ? "selected" : "";              
                                   echo  '<option value="'.$key.'" '.$selected.'>' .$value.'</option>';
                               } ?>
                           </select>
                       </li>
                   </ul>
               </div>
           </div>
       </div>


   </div>
</div>

<div class="container-fluid">
    <div class="row manage-table">
        <div class="panel panel-piluku">

           <div class="gantt_title">
            <h3 class="panel-title">
                <span class="tieude"><a href="<?php echo base_url() . 'tasks/grid'; ?>">Danh mục dự án</a></span>
                <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_chart'; ?>">Lược đồ</a></span>
                <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_list'; ?>">Công việc liên quan</a></span>
                <span class="tieude active">Quản lý hợp đồng</span>
                <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_no_revenue'; ?>">Công việc không tạo doanh thu</a></span>
                <span class="tieude"><a href="<?php echo base_url() . 'tasks/personal'; ?>">Ghi chép</a></span>

                <a href="<?php echo base_url('contracts/export_excel_contract') ?>" class="btn btn-primary pull-right " style="color: white; margin-left: 5px;">Xuất file</a>
                <a href="<?php echo base_url('contracts/list_status_changing') ?>" style="float: right;color: white" class="btn btn-primary">LOG</a>
            </h3>
        </div>


        <div class="panel-heading">
            <h3 class="panel-title">
                <span class="title first active">Danh sách hợp đồng</span>
                <span class="badge bg-primary tip-left" id="count_pos_contract"><?php echo $t ?></span>
            </h3>
        </div>

        <div class="panel-body nopadding table_holder table-striped table-bordered table-bordered table-tree table-responsive" >
         <table id="example" class="table vivi tablesorter table-reports table-bordered table-tree" data-table="chan-doi">
            <thead>
                <tr>
                    <th class="leftmost" style="width: 20px;">
                        <input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
                    </th>
                    <th>Số hiệu hợp đồng</th>
                    <th>Tên hợp đồng</th>
                    <th>Tên dịch vụ</th>
                    <th>Khách hàng</th>
                    <td>Giá trị hợp đồng</td>
                    <td>Giá trị đã nghiệm thu/Thanh lý</td>
                    <th>Trạng thái hợp đồng</th>
                    <th>Ngày ký</th>
                    <th>Người phụ trách</th>
                    <th>Người tham gia</th>
                    <th>File</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>
            <tbody>
               <?php foreach ($items as  $item) { ?>
                  <tr style="cursor: pointer;">
                     <td class="cb"><input type="checkbox" value="<?php echo $item['id'] ?>" class="file_checkbox"><label><span></span></label></td>
                     <td class="cb"><?php echo $item['code'] ?></td>
                     <td class="cb"><?php echo $item['name'] ?></td>
                     <td class="cb center"><?php echo $item['item_name'] ?> </td>
                     <td class="cb"><?php echo $item['customer_name'] ?></td>
                     <td style="text-align: right;" class="cb"><?php echo $item['price'] ?></td>
                     <td style="text-align: right;" class="cb"><?php echo $item['payment_price'] ?></td>
                     <td class="cb center"><?php echo $item['status'] ?></td>
                     <td class="cb center"><?php echo $item['date_signing'] ?></td>
                     <td class="cb center"><?php echo $item['implement'] ?></td>
                     <td class="center"><?php echo $item['join'] ?></td>
                     <td class="center"><a href="javascript:;" onclick="download_contract_file(<?php echo $item['id'] ?>);"><i class="ti-download"></i></a></td>
                     <td class="center"><a href="<?php echo base_url('contracts/view/customer/'.$item['id']) ?>">Sửa</a></td>
                 </tr>
             <?php } ?>
         </tbody>
     </table>
 </div>

</div>

</div>
</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<script type="text/javascript">
    var option = '<?php echo $option; ?>';
    function edit_contract(id) {
        var page = $('table[data-table="pos_contract"]').attr('data-currentPage');
        if(page > 1)
            window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/'+id+'/'+page;
        else
            window.location.href = BASE_URL + 'contracts/view/<?php echo $option; ?>/'+id;
    }



    $( document ).ready(function() {
        $('#example').DataTable({
             "pageLength": 20,
             "language": {
                "paginate": {
                 "first":      "First",
                 "last":       "Last",
                 "next":       "&gt;",
                 "previous":   "&lt",
                 "class":"vi"
             },
             "search":         "Tìm kiếm:",
         },

     });


    });


    $('body').on('click','.vivi tbody tr td.cb',function(){

     var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
     var table = checkbox.closest('table.vivi');
     var data_table = table.attr('data-table');
     if (checkbox.prop('checked') == true){ 
       checkbox.prop('checked', false);
   }else{
    $('.manage-row-options').show();
    checkbox.prop('checked', true);
}

var checked_box = table.find('.file_checkbox:checked');
if(checked_box.length == 0) 
 $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
else
 $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
});


    $('body').on('click','table.vivi label.check_tatca',function(){
        console.log('ducang')
        var checkbox = $(this).closest('th').find('input[type="checkbox"]'); 
        var table = checkbox.closest('table.vivi');
        var data_table = table.attr('data-table');
        if (checkbox.prop('checked') == true){ 
            $('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
            checkbox.prop('checked', false);
            table.find('td input[type="checkbox"]').prop('checked', false);
        }else{
           $('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
           checkbox.prop('checked', true);
           table.find('td input[type="checkbox"]').prop('checked', true);
       }
   });

    $('body').on('click','.btn-red[data-table]',function(){
        var data_table = $(this).attr('data-table');
        var url        = $(this).attr('data-url');
        var table      = $('table[data-table="'+data_table+'"]');
        var checkbox   = table.find('.file_checkbox:checked');
        var manage_row = $(this).parents('.manage-row-options');

        var cid = new Array();
        $(checkbox).each(function( index ) {
            cid[cid.length] = $(this).val();
        });

        var data = new Object();
        data.cid = cid;

        var param = $(this).attr('data-param');
        var reload = $(this).attr('data-table-reload');

        if (typeof param !== typeof undefined && param !== false) {
            data.param = param;
        }

        if(cid.length == 0) {
            toastr.warning('Phải chọn ít nhất 1 bản ghi', 'Cảnh báo');
        }else {
            bootbox.confirm('Bạn có chắc muốn xóa không?', function(result){
                if (result){
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: function(string){
                            var result = $.parseJSON(string);
                            if(result.flag == 'warning')
                                toastr.warning(result.msg, 'Cảnh báo');
                            else if(result.flag == 'true') {
                                toastr.success(result.msg, 'Thông báo');
                                location.reload();
                            }

                            manage_row.addClass('hidden');
                        }
                    });
                }
            });
        }
    });





</script>
<style>
.data-n9-table th {
    text-align: center;
}

.data-n9-table th[data-field] {
    cursor: pointer;
}

.data-n9-table td.center {
    text-align: center;
}

.panel-piluku > .panel-heading h3 {
    position: relative;
}

.loading {
    position: absolute;
    bottom: -91px;
    left: -9px;
}


.sorting:after,.sorting_desc:after,.sorting_asc:after{
    content: "" !important;
}
#example_length,#example_info{
	display: none;
}


#example_paginate ul .paginate_button a{
	font-weight: bold;
}


</style>
</div>

<?php
if(!empty($_SESSION['notice'])) {
    $notice = $_SESSION['notice'];
    unset($_SESSION['notice']);
    ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            toastr.success('<?php echo $notice; ?>', 'Thông báo');
        });
    </script>
    <?php
}
?>
<?php $this->load->view("partial/footer"); ?>