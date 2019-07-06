<style>
/* remove border radius for the tab */
#exTab1 .nav-pills > li > a {
  border-radius: 0;
}


#MODAL_task_detail .manage-table table th {
    text-align: center;
    font-weight: bold;
    cursor: pointer;
}

#MODAL_task_detail .manage-table table td.center {
    text-align: center;
}

#MODAL_task_detail .manage-table table tr:last-child td {
    border-bottom: 0;
}

#MODAL_task_detail .manage-table table td {
    border-top: 1px solid #d7dce5;
    border-bottom: 1px solid #d7dce5;
    padding: 4px 10px;
}

#MODAL_task_detail .manage-table table td.bold {
    font-weight: bold;
}

#MODAL_task_detail .manage-table table td:last-child {
    padding-left: 10px;
}

.x-info{
	background:#fff
}
.x-info > tbody > tr > th,.x-info > tbody > tr > td{
	border:1px solid #CBCBCD;
	padding:8px;
	color:#292929
}
.x-info-section{
	font-weight:bold;
	text-align:center;
	background:#DCDCDF;
	color:#333!important;
	text-align:left;
	padding:8px 10px!important
}

.x-info-label{
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f9f9f9), color-stop(1, #f9f9f9));
	padding-right:10px !important;
	text-align:right;
	width:15%;
	font-weight:bold;
	color:#333;
    border: 1px solid #d7dce5 !important;
}

.x-info-label.no-border-left {
	border-left: 0 !important;
}

.x-info-label-last{
	border-bottom-color:#CBCBCD!important
}

.x-info-content{
	width:35%;
	border-right: 0 !important;
	line-height: 17px;
    border-right: 1px solid #d7dce5 !important;
}

.x-info-content span.root {
	font-weight: bold;
}
</style>

<?php
$id   		= $task_info['id'];
$name   	= $task_info['name'];
$color   	= $task_info['color'];
$detail 	= nl2br($task_info['detail']);
$progress 	= $task_info['progress'];
$percent 	= $task_info['percent'];
$parent 	= $task_info['parent'];
$project_id = $task_info['task_info'];
$date_start = $task_info['date_start'];
$date_end 	= $task_info['date_end'];
$duration 	= $task_info['duration'];
$trangthai  = $task_info['trangthai'];
$prioty 	= $task_info['prioty'];
$pheduyet   = $task_info['pheduyet'];
$date_finish= $task_info['date_finish'];
$pheduyet_note = nl2br($task_info['pheduyet_note']);
$project_name  = $task_info['name'];
$created_by_name = $task_info['created_by_name'];

$task_permission = [];

$btnPheduyet = true;
$is_create_task = false; // có được cấp quyền tạo việc hay không
if($parent > 0) {
    $title = 'Công việc thuộc "'.$parent_item['name'].'"';
    $congviec_title = 'Tên công việc';
} else {
	$title = 'Dự án "'.$task_info['name'].'"';
    $congviec_title = 'Tên dự án';	
}


if(in_array('permission_create_task', $task_permission))
    $is_create_task = true;
$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');

if($pheduyet >= 0)
    $btnPheduyet = false;

if($pheduyet == -1)
    $name_ext = ' (Chờ phê duyệt)';
elseif($pheduyet == 0)
    $name_ext = ' (Không được phê duyệt)';

$styleBasic    = ' style="display: block"';
$styleDetail   = ' style="margin-top: -10px;"';

if($pheduyet == 0) {
    $styleBasic  = '';
    $styleDetail = ' style="display: block; margin-top: -10px;"';
}
?>

?>
<div class="modal fade" id="MODAL_task_detail" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document" style="width: 95%">
		<div class="modal-content" style="border-radius: 0px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span class="ti-close" aria-hidden="true"></span>
				</button>
				<h4 class="modal-title"><?php echo $title ?></h4>
			</div>
			<div class="modal-body" style="min-height: 400px;">
				<div class="row">
					<div class="col-md-12">
						<div id="exTab1">	
							<ul  class="nav nav-pills" style="border-bottom: 1px solid #ccc;">
								<li class="active">
									<a href="#2a" data-toggle="tab">Chi tiết</a>
								</li>

								<li >
					        		<a  href="#1a" data-toggle="tab">Tiến độ <span class="badge bg-primary tip-left"><?php echo $progress_total; ?></span></a>
								</li>

								<li >
					        		<a  href="#3a" data-toggle="tab">Yêu cầu phê duyệt <span class="badge bg-primary tip-left">0</span></a>
								</li>

								<li >
					        		<a  href="#4a" data-toggle="tab">Phê duyệt<span class="badge bg-primary tip-left">0</span></a>
								</li>
								
							</ul>

							<div class="tab-content clearfix" style="    margin-bottom: 25px;">
								<div class="tab-pane" style="    margin-top: 20px; text-align: center;" id="4a">
									<p style="
    font-size: 18px;
    font-style: italic;
    color: red;
">Không có bản ghi nào</p>
								</div>
								<div class="tab-pane" style="    margin-top: 20px; text-align: center;" id="3a">
									<p style="
    font-size: 18px;
    font-style: italic;
    color: red;
">Không có bản ghi nào</p>
								</div>

							  	<div class="tab-pane" style="    margin-top: 20px;" id="1a">
				          			<table class="table">
				          				<thead>
				          					<tr>
				          						<th>STT</th>
				          						<th>Tên</th>
				          						<th>Tiến độ</th>
				          						<th>Tình trạng</th>
				          						<th>Ưu tiên</th>
				          						<th>Tài khoản</th>
				          						<th>Thời gian</th>
				          					</tr>
				          				</thead>
						              <tbody>
						    			<?php foreach($progress_items as $i => $item) {?>
						                <tr>
						                  <td><?php echo $i + 1;?></td>
						                  <td><?php echo $item['task_name']; ?></td>
						                  <td><?php echo $item['progress']; ?></td>
						                  <td><?php echo $item['trangthai']; ?></td>
						                  <td><?php echo $item['prioty']; ?></td>
						                  <td><?php echo $item['username']; ?></td>
						                  <td><?php echo $item['created']; ?></td>
						                </tr>
						                <?php } ?>
								  </tbody>
						        </table>
								</div>
								<div class="tab-pane manage-table active" id="2a">
				          			<table width="100%" cellpadding="7" class="x-info" style="border:0">
								        <tbody>
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
								</div>
							</div>
						  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
var TASK_DETAIL = {
	
	init: function()
	{
		
	}
}
$(document).ready(function () {
	TASK_DETAIL.init();
});
</script>