<?php 
	$task_permission = array();
	
	$task_permission = $user_info['task_permission'];
	
	$is_create_task = false;
	$task_name = $customer_name;
	// if($project_id > 0) {
	// 	$title = 'Công việc mới thuộc "'.$project_name.'"';
	// 	$congviec_title = 'Tên công việc';
	// 	$project_id = $project_id;
	// 	$parent = $project_id;
	// 	$task_name = $work_name;
		
	// }else{
	// 	$title = 'Dự án mới';
	// 	$congviec_title = 'Tên dự án';
	// 	$parent = $project_id;
	// }

    if(in_array('permission_create_task', $task_permission)){
        $is_create_task = true;

    }
	
	$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
	$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');

?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
            <h4 class="modal-title"><?php echo "Dự án mới" ?></h4>
        </div>
        <div class="toolbars">
            <ul class="list clearfix">
                <li class="btn-save"><a href="javascript:;" onclick="add_congviec();"><i class="fa fa-floppy-o"></i>Lưu</a></li>
                <li class="btn-cancel"><a href="javascript:;" onclick="cancel('full', 'new');"><i class="fa fa-times-circle"></i>Đóng</a></li>
            </ul>
        </div>
        <div class="arrord_nav">
            <ul class="list clearfix">
                <li class="active" data-id="basic_manager"><span class="title">Cơ bản</span></li>
            </ul>
        </div>
        <div class="modal-body">
            <form method="POST" name="task_form" id="task_form" class="form-horizontal">
                <input type="hidden" name="parent" value="<?php echo $parent; ?>" />
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                <input type="hidden" name="current_type" id="current_type" value="new" />
                <input type="hidden" name="sale_id" id="sale_id" value="<?php echo $sale_id;?>" />
                <div class="tabs" id="basic_manager" style="display: block;">
                    <div class="clearfix hang" style="margin-bottom: 10px;">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name" class="col-md-3 col-lg-2 control-label required"><?php echo "Tên dự án"; ?></label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="name" value="<?php echo $customer_info['code'].'-'.$work_name;?>" class="form-control">
                                    <span for="name" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12" style="display: none;">
                            <div class="form-group">
                                <label for="color" class="col-md-3 col-lg-2 control-label">Màu sắc</label>
                                <div class="col-md-9 col-lg-10">
                                    <input type="text" name="color" id="color" value="#489ee7" class="form-control" />
                                    <span for="color" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>
                        <?php if($parent > 0):?>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="percent" class="col-md-3 col-lg-2 control-label ">Tỷ lệ</label>
                                    <div class="col-md-9 col-lg-10">
                                        <input type="number" name="percent" value="<?php echo $percent; ?>" class="form-control" />
                                        <span for="percent" class="text-danger errors"></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="task_template" class="col-md-3 col-lg-2 control-label ">Lộ trình</label>
                                <div class="col-md-9 col-lg-10">
                                    <select name="task_template" id="task_template" class="form-control" id="timezone">
                                        <option value="0">Chọn lộ trình</option>
                                        <?php
                                        if(!empty($task_template)) {
                                            foreach($task_template as $val) {
                                                ?>
                                                <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
                                            <?php
                                            }
                                        }
                                        ?>

                                    </select>
                                    <span for="task_template" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label ">Mô tả</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="detail" class="form-control"></textarea>
                                    <span for="detail" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix hang">
                        <div id="add_navigation">
                            <div class="title active" style="border-top: 1px solid #ccc;" data-id="thietlap_content">Thông tin</div>
                            <div id="thietlap_content" class="content">
                                <div class="row">
                                    <div class="col-lg-6" style="padding-right: 10px">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-4 control-label required"> Ngày Bắt đầu</label>
                                            <div class="col-md-9 col-lg-8">
                                                <input type="text" name="date_start" value="<?php echo date("d-m-Y") ?>" id="date_start" class="form-control datepicker" />
                                                <span for="date_start" class="text-danger errors"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6" style="padding-left: 10px;">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-4 control-label required">Ngày kết thúc dự kiến</label>
                                            <div class="col-md-9 col-lg-8">
                                                <input type="text" name="date_end" id="date_end" class="form-control datepicker" />
                                                <span for="date_end" class="text-danger errors"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-lg-6" style="padding-right: 10px">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-4 control-label">Trạng thái</label>
                                            <div class="col-md-9 col-lg-8">
                                                <select name="trangthai" class="form-control">
                                                    <?php
                                                    foreach($trangthai_arr as $key => $val) {
                                                        ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                                    <?php
                                                    }
                                                    ?>

                                                </select>
                                                <input type="text" class="form-control" name="trangthai_name" id="trangthai_name" value="Chưa thực hiện" readonly="true" style="display: none;" />
                                                <input type="number" name="progress" value="0" class="form-control"/>
                                                <span for="progress" class="text-danger errors"></span>
                                            </div>

                                        </div>

                                    </div> -->
                                    <div class="col-lg-6" style="padding-left: 10px">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-4 control-label">Mức độ ưu tiên</label>
                                            <div class="col-md-9 col-lg-8">
                                                <select name="prioty" class="form-control">
                                                    <?php
                                                    foreach($prioty_arr as $key => $val) {
                                                        ?>
                                                        <option value="<?php echo $key; ?>"<?php if($key == 2) echo ' selected'; ?>><?php echo $val; ?></option>
                                                    <?php
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-2 control-label">Khách hàng</label>
                                            <div class="col-md-9 col-lg-10">
                                            	<div class="form-control">
                                            		<?php echo $customer_name; ?>
                                            		<input type="hidden" name="customer" value="<?php echo $customer_id;?>" />
                                            	</div>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-2 control-label">Người phụ trách</label>
                                            <div class="col-md-9 col-lg-10">
                                                <div class="x-select-users" x-name="implement" id="implement_list" x-title="Người phụ trách" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                                    <?php 
                                                    if (!empty($supporters)) {
                                                    	foreach ($supporters as $item) {
                                                    ?>
                                                    <span class="item"><?php echo ($item->first_name. ' ' . $item->last_name);?></span>
                                                    <input type="hidden" name="implement[]" value="<?php echo $item->employee_id;?>" />
                                                    <?php 
                                                    }}
                                                    ?>
                                                    <input type="text" autocomplete="off" id="implement_result" class="quick_search" readonly="readonly"/>
                                                    <div class="result">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                     <?php if($is_create_task == true):?>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="col-md-3 col-lg-2 control-label">Người phê duyệt tiến độ</label>
                                                <div class="col-md-9 col-lg-10">
                                                    <div class="x-select-users" x-name="progress_list" id="progress_list" x-title="" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                                        <input type="text" autocomplete="off" id="progress_result" class="quick_search" />
                                                        <div class="result">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif;?>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-2 control-label">Người tham gia</label>
                                            <div class="col-md-9 col-lg-10">
                                                <div class="x-select-users" x-name="join" id="join_list" x-title="Người tham gia" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                                   
                                                    <input type="text" autocomplete="off" id="join_result" class="quick_search"/>
                                                    <div class="result">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                   
                                      <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="col-md-3 col-lg-2 control-label">Người được xem</label>
                                            <div class="col-md-9 col-lg-10">
                                                <div class="x-select-users" x-name="xem" id="xem_list" x-title="Người được xem" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                                    <input type="text" autocomplete="off" id="xem_result" class="quick_search" />
                                                    <div class="result">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>

                        </div>
                    </div>
                </div>
             </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $('#add_navigation .title').click(function(e){
            if(!$(this).hasClass('active')) {
                $('#add_navigation .active').parent().find('.content').slideUp();
                $('#add_navigation .active').removeClass('active');
                $(this).addClass('active');

                var content_show = $(this).attr('data-id');
                $('#add_navigation #'+ content_show).slideDown();
            }
        });
    });


    $('#task_template').change(function(){
        var id = $('#task_template').val();
        var start = $('#date_start').val();
        $.ajax({
            url: "<?php echo base_url('tasks/get_duration_template') ?>",
            data: {
                id: id,
                start:start
            },
            method: "post",
            dataType: "json",
            success: function(respon){
                $('#date_end_formatted').val(respon.end);
                $('#date_end').val(respon.e);
            }
        })
    });
</script>