<?php
    $id   		= $item['id'];
    $name   	= $item['name'];
    $color   	= $item['color'];
    $detail 	= nl2br($item['detail']);
    $progress 	= $item['progress'];
    $percent 	= $item['percent'];
    $parent 	= $item['parent'];
    $project_id = $item['project_id'];
    $date_start = $item['date_start'];
    $date_end 	= $item['date_end'];
    $duration 	= $item['duration'];
    $trangthai  = $item['trangthai'];
    $prioty 	= $item['prioty'];
    $pheduyet   = $item['pheduyet'];
    $date_finish= $item['date_finish'];
    $created_by_name = $item['created_by_name'];
    $pheduyet_note = nl2br($item['pheduyet_note']);
    $project_name  = $project_item['name'];

    $task_permission = $user_info['task_permission'];

    $btnPheduyet = true;
    $is_create_task = false; // có được cấp quyền tạo việc hay không
    if($parent > 0) {
        $title = 'Công việc thuộc "'.$parent_item['name'].'"';
        $congviec_title = 'Tên công việc';

        if(in_array('permission_all_task', $task_permission))
            $is_create_task = true;
        else {
            $project_implement = array();
            if(!empty($project_relation)) {
                foreach($project_relation as $val) {
                    if($val['is_implement'] == 1)
                        $project_implement[] = $val['user_id'];
                }
            }

            if(in_array('permission_brand_task', $task_permission) && in_array($user_info['id'], $project_implement))
                $is_create_task = true;
        }

        //check phê duyệt
        if(!in_array($user_info['id'], $item['is_pheduyet_parent']))
            $btnPheduyet = false;
    }else{
        $title = 'Dự án "'.$item['name'].'"';
        $congviec_title = 'Tên dự án';
        if(in_array('permisson_project', $task_permission))
            $is_create_task = true;

        $btnPheduyet = false;
    }
    $trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng');
    $prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
    if($pheduyet >= 0)
        $btnPheduyet = false;

    if($pheduyet == -1)
        $name_ext = ' (Chờ phê duyệt)';
    elseif($pheduyet == 0)
        $name_ext = ' (Không được phê duyệt)';
?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
            <h4 class="modal-title"><?php echo $title; ?></h4>
        </div>
 <?php if($btnPheduyet == true):?>
         <div class="toolbars">
             <ul class="list clearfix">
                 <li class="btn-cancel"><a href="javascript:;" onclick="cancel('full', 'new');"><i class="fa fa-times-circle"></i>Đóng</a></li>
                 <li class="btn-pheduyet"><a href="javascript:;" onclick="pheduyet();"><i class="fa fa-gavel"></i>Duyệt</a></li>
             </ul>
         </div>
 <?php endif;?>

        <div class="arrord_nav">
<?php
    if($pheduyet == 1 || $pheduyet == 2) {
        $styleProgress  = ' style="display: block;"';
        $styleDetail    = ' style="margin-top: -10px;"';
?>
        <ul class="list clearfix">
            <li data-id="progress_manager" class="active"><span class="title">Tiến độ</span></li>
            <li data-id="detail_manager"><span class="title">Chi tiết</span></li>
        </ul>
<?php
    }else {
        $styleProgress  = '';
        $styleDetail    = ' style="display: block; margin-top: -10px;"';
?>
        <ul class="list clearfix">
            <li data-id="detail_manager" class="active"><span class="title">Chi tiết</span></li>
        </ul>
    <?php
    }
?>

        </div>
        <div class="modal-body">
            <form method="POST" name="task_form" id="task_form" class="form-horizontal">
                <input type="hidden" name="id" id="task_id" value="<?php echo $id; ?>" />
                <input type="hidden" name="parent" value="<?php echo $parent; ?>" />
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
<?php if($pheduyet == 1 || $pheduyet == 2): ?>
                <div class="manage-table tabs" id="progress_manager"<?php echo $styleProgress; ?>>
                    <div class="control clearfix">
                        <div class="pull-left">
                            <select name="fields" id="s_task_id" class="form-control" id="fields">
                                <?php
                                if(!empty($slbTasks)) {
                                    foreach($slbTasks as $val) {
                                        ?>
                                        <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
                                    <?php
                                    }
                                }

                                ?>


                            </select>
                        </div>
                        <div class="pull-right">
                            <div class="buttons-list">
                                <div class="pull-right-btn">
                                    <a href="javascript:;" id="new-person-btn" onclick="add_tiendo();" class="btn btn-primary" title="Cập nhật tiến độ"><span class="">Cập nhật tiến độ</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
               
                 
                  
                  
                </div>
<?php endif; ?>

                <div class="manage-table tabs" id="detail_manager" <?php echo $styleDetail; ?>>
                        <table width="100%" cellpadding="7" class="x-info" style="border:0">
                            <tbody>
                            <tr>
                                <td class="x-info-top" colspan="4" style="padding-left: 5px; padding-right: 10px; font-size: 16px; border: 0 !important;">
                                    <span class="tl" style="font-weight: bold;"><i class="fa fa-pencil"></i> Thông tin chi tiết</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="x-info-label"><?php echo $congviec_title;  ?></td>
                                <td class="x-info-content" style="font-weight: bold;" colspan="3"><?php echo $name . $name_ext; ?></td>
                            </tr>
                            <?php if($pheduyet == 0 && !empty($pheduyet_note)):?>
                                <tr>
                                    <td class="x-info-label">Lý do</td>
                                    <td class="x-info-content" colspan="3"><?php echo $pheduyet_note; ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php
                            if(!empty($item['customers'])){
                                foreach($item['customers'] as $val)
                                    $customer_names[] = $val['name'];

                                $customer_names = implode(', ', $customer_names);

                            }
                            ?>
                            <tr>
                                <td class="x-info-label">Khách hàng</td>
                                <td class="x-info-content" style="font-weight: bold;" colspan="3"><?php echo $customer_names; ?></td>
                            </tr>
                            <tr>
                                <td class="x-info-label">Bắt đầu</td>
                                <td class="x-info-content"><?php echo $date_start; ?></td>
                                <td class="x-info-label">Kết thúc</td>
                                <td class="x-info-content"><?php echo $date_end; ?></td>
                            </tr>
                            <?php if($trangthai == 2):?>
                                <tr>
                                    <td class="x-info-label">Thực tế</td>
                                    <td class="x-info-content" colspan="3" style="font-weight: bold;"><?php echo $date_finish; ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="x-info-label">Tình trạng</td>
                                <td class="x-info-content"><?php echo $trangthai_arr[$trangthai]; ?></td>
                                <td class="x-info-label">Dự án</td>
                                <td class="x-info-content"><?php echo $project_name; ?></td>
                            </tr>
                            <tr>
                                <td class="x-info-label">Tiến độ</td>
                                <td class="x-info-content"><?php echo $progress; ?>%</td>
                                <td class="x-info-label">Mức ưu tiên</td>
                                <td class="x-info-content"><?php echo $prioty_arr[$prioty]; ?></td>
                            </tr>
                            <tr>
                                <td class="x-info-label">Phụ trách</td>
                                <td class="x-info-content">
                                    <?php
                                    if(!empty($item['is_implement'])) {
                                        foreach($item['is_implement'] as $key => $val) {
                                            $implement_ids = array();
                                            $keyArr = explode('-', $key);

                                            if($keyArr[0] == $id)
                                                $implement_ids[] = $val['id'];

                                            $implement[$val['id']] = $val['username'];
                                        }

                                        foreach($implement as $user_id => $user_name) {
                                            if(in_array($user_id, $implement_ids))
                                                $implement_names[] = '<span class="root">'.$user_name.'</span>';
                                            else
                                                $implement_names[] = '<span>'.$user_name.'</span>';
                                        }

                                        $implement_names = implode(', ', $implement_names);
                                        echo $implement_names;
                                    }
                                    ?>
                                </td>
                                <td class="x-info-label">Người được xem</td>
                                <td class="x-info-content">
                                    <?php
                                    if(!empty($item['is_xem'])) {
                                        foreach($item['is_xem'] as $key => $val) {
                                            $xem_ids = array();
                                            $keyArr = explode('-', $key);

                                            if($keyArr[0] == $id)
                                                $xem_ids[] = $val['id'];

                                            $xem[$val['id']] = $val['username'];
                                        }

                                        foreach($xem as $user_id => $user_name) {
                                            if(in_array($user_id, $xem_ids))
                                                $xem_names[] = '<span class="root">'.$user_name.'</span>';
                                            else
                                                $xem_names[] = '<span>'.$user_name.'</span>';
                                        }

                                        $xem_names = implode(', ', $xem_names);
                                        echo $xem_names;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="x-info-label">Người tạo</td>
                                <td class="x-info-content" colspan="3"><span class="root"><?php echo $created_by_name; ?></span></td>
                            </tr>
                            <tr>
                                <td class="x-info-label">Mô tả</td>
                                <td class="x-info-content" colspan="3"><?php echo $detail; ?></td>
                            </tr>
                            <tr>
                                <td class="x-info-label" style="border-bottom: inherit; border-bottom: 1px solid #d7dce5;"">Tài liệu đính kèm</td>
                                <td class="x-info-content" colspan="3" style="vertical-align: middle; border-bottom: 1px solid #d7dce5;">
                                    <ul class="attach-file">
                                        <?php
                                        if(!empty($item['files'])) {
                                            $upload_dir = base_url() . 'assets/tasks/files/';
                                            foreach($item['files'] as $val) {
                                                $file_name = $val['file_name'];
                                                $size      = $val['size'] . ' Bytes';
                                                $link      = $upload_dir . $file_name;
                                                ?>
                                                <li><a href="<?php echo $link; ?>" target="_blank"><?php echo $file_name; ?> (<?php echo $size; ?>)</a></li>
                                            <?php
                                            }
                                        }else {
                                            ?>
                                            <li>Không có File đính kèm.</li>
                                        <?php
                                        }
                                        ?>

                                    </ul>
                                </td>
                            </tr>
                            </tbody>

                        </table>
                        <?php if($no_comment != true): ?>
                            <div id="comment_section">
                                <div class="title"><i class="fa fa-comment"></i> Ý kiến thảo luận</div>
                                <div method="POST" id="task_comment" class="frm-comment fn-comment">
                                    <input type="hidden" name="task_id" id="task_id" value="<?php echo $id; ?>" />
                                    <input type="hidden" name="parent" id="parent" value="<?php echo $parent; ?>" />
                                    <p class="avatar"><img class="fn-useravatar" src="http://data.ht/images/no-avatar.png"></p>
                                    <div class="wrap-comment">
                                        <textarea name="content" id="comment_content" cols="30" rows="10"></textarea>
                                        <p class="frm-checkbox" style="display: none;">
                                            <span>Đính kèm</span>
                                        </p>
                                        <input type="button" value="Bình luận" name="btnSubmit" id="btnComment" class="button btn-dark-blue pull-right" />
                                    </div>
                                </div>
                                <ul id="commentList" class="list-comment"></ul>
                                <div class="phantrang"></div>

                            </div>
                        <?php endif; ?>
                    </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        load_list('progress', 1);
        countTiendo();
        $('#color').colorpicker({color: '<?php echo $color; ?>'});
        $('#add_navigation .title').click(function(e){
            if(!$( this ).hasClass( "active" )) {
                $('#add_navigation .active').parent().find('.content').slideUp();
                $('#add_navigation .active').removeClass('active');
                $(this).addClass('active');

                var content_show = $(this).attr('data-id');
                $('#add_navigation #'+ content_show).slideDown();
            }
        });
        $( "#my_modal .arrord_nav ul.list > li" ).click(function() {
            $( "#my_modal .arrord_nav ul.list > li" ).removeClass('active');
            var data_id = $(this).attr('data-id');
            $('#my_modal .tabs').hide();
            $(this).addClass('active');
            $('#'+data_id).show();
        });

        var task_id = $('#task_id').val();
        load_comment(task_id, 1);
    });
</script>