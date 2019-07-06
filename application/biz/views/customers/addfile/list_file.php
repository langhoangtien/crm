

<div class="modal fade box-modal" id="quick_modal">
</div>

<form method="POST" name="task_form" id="task_form" class="form-horizontal">

<div class="modal-dialog" role="document" style="width: 900px;">
    <div class="modal-content" style="border-radius: 0;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
            aria-label="Close">
            <span aria-hidden="true" class="x-close">×</span>
        </button>
        <h4 class="modal-title">Danh sách các file</h4>
    </div>

    <div class="modal-body">
        <!-- <ul class="nav nav-tabs">
          <li role="presentation"><a data-toggle="tab" href="#file_manager">Tài liệu</a></li>
      </ul> -->

      <div class="tab-pane" id="file_manager">
        <!-- <div class="manage-row-options 2">
            <div class="control">
                <a href="javascript:;" class="btn btn-red delete_inactive"
                title="Sửa" onclick="edit_file();"><span class="">Sửa</span></a>
                <a href="javascript:;" class="btn btn-delete"
                onclick="delete_file();">Xóa lựa chọn</a>
            </div>
        </div> -->
        <div class="control clearfix" style="padding: 15px 0;">
            <div class="pull-right">
                <div class="buttons-list">
                    <div class="pull-right-btn">
                        <a href="javascript:;" id="new-person-btn"
                        onclick="add_file_customer();" class="btn btn-primary"
                        title="Thêm mới tiến độ"><span class="">Thêm mới File</span></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-heading">
            <h3 class="panel-title">
                <span class="tieude active">Danh sách tài liệu</span> 
                <span id="count_tailieu" title="total suppliers"
                class="badge bg-primary tip-left"><?php echo count($list) ?></span> 
                <!-- <i class="fa fa-spinner fa-spin" id="loading_2"></i> -->
            </h3>
        </div>

        <div
        class="panel-body nopadding table_holder table-responsive table_list">
        <table class="tablesorter table table-hover" id="sortable_table">
            <thead>
                <tr>
                    <th style="width: 50px;"></th>
                        <th data-field="name">Tên tài liệu</th>
                        <th style="width: 20%;" data-field="file_name">Tên file</th>
                        <th style="width: 14%;" data-field="size">Kích thước</th>
                        <th style="width: 14%;" data-field="created">Ngày tạo</th>
                        <th style="width: 10%;" data-field="username">Người tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(!empty($list) && $list != ''){
                            foreach ($list as $key => $value) {
                               echo '<tr>'.
                                    '<td>
                                    <a href="customers/deletefile?file_id='.$value->id.
                                    '&person_id='.$person_id.
                                    '&file_name='.$value->file_name.'" >Xóa</a>
                                    </td>'.
                                    '<th data-field="name">'.$value->name.'</th>'.
                                    '<th data-field="name"><a href="customers/downloadfile?file_name='
                                    .$value->file_name.'&person_id='.$person_id.'" >'.$value->file_name.'</a></th>'.
                                    '<th data-field="name">'.$value->size.'</th>'.
                                    '<th data-field="name">'.$value->created.'</th>'.
                                    '<th data-field="name">'.$value->username.'</th>'.
                                    '</tr>';    
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal-footer">

    <a type="button" class="btn btn-primary" data-dismiss="modal"><i
        class="fa fa-times-circle" style="padding: 0 5px;"></i>Đóng</a>

 
</div>

<input type="hidden" id="person_id_onlistFile" value="<?php echo $person_id ?>">

</div>

</div>
</form>


<script lang="javascript">
    function add_file_customer() {
        var person_id = $('#person_id_onlistFile').val();
        var url = BASE_URL + 'customers/addfile'
        $.ajax({
            type: "GET",
            url: url,
            data: {
                person_id : person_id
            },
            success: function(html){
                $('#quick_modal').html(html);
                $('#quick_modal').modal('toggle');
            }
        });
    }

</script>