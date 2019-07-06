
<?php
// var_dump($item['is_implement']); die();
 // var_dump($list_jo); die();
// var_dump($suppliers); die();
// echo "<pre>";
// var_dump($item);die();
$id = $item['id'];
$name = $item['name'];
$color = $item['color'];
$detail = nl2br($item['detail']);
$progress = $item['progress'];
$percent = $item['percent'];
$parent = $item['parent'];
$project_id = $item['project_id'];
$date_start = $item['date_start'];
$date_end = $item['date_end'];
$duration = $item['duration'];
$trangthai = $item['trangthai'];
$prioty = $item['prioty'];
$pheduyet = $item['pheduyet'];
$date_finish = $item['date_finish'];
$pheduyet_note = nl2br($item['pheduyet_note']);
$project_name = $project_item['name'];
$created_by_name = $item['created_by_name'];

$task_permission = $user_info['task_permission'];

$arr_join_name = $item['is_join'];
$name_join_task  ='';
if (!empty($arr_join_name)) {
	foreach ($arr_join_name as $key => $value) {
		$name_join_task .= $value['username'].',';
	}
}
// echo "<pre>";var_dump($join_name); die();
$btnPheduyet = true;
$is_create_task = false; // có được cấp quyền tạo việc hay không

if ($parent > 0) {
	$title = 'Công việc thuộc "' . $parent_item['name'] . '"';
	$congviec_title = 'Tên công việc';

    // check phê duyệt
	if (! in_array($user_info['id'], $item['is_pheduyet_parent']))
		$btnPheduyet = false;
} else {
	$title = 'Dự án "' . $item['name'] . '"';
	$congviec_title = 'Tên dự án';

	$btnPheduyet = false;
}

if (in_array('permission_create_task', $task_permission))
	$is_create_task = true;
$trangthai_arr = array(
	'Chưa thực hiện',
	'Đang thực hiện',
	'Hoàn thành',
	'Đóng/dừng',
	'Không thực hiện'
);
$prioty_arr = array(
	'Rất cao',
	'Cao',
	'Trung bình',
	'Thấp',
	'Rất thấp'
);

if ($pheduyet >= 0)
	$btnPheduyet = false;

if ($pheduyet == - 1)
	$name_ext = ' (Chờ phê duyệt)';
elseif ($pheduyet == 0)
	$name_ext = ' (Không được phê duyệt)';

$styleBasic = ' style="display: block"';
$styleDetail = ' style="margin-top: -10px;"';

if ($pheduyet == 0) {
	$styleBasic = '';
	$styleDetail = ' style="display: block; margin-top: -10px;"';
}
?>

<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content" style="border-radius: 0;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
			aria-label="Close">
			<span aria-hidden="true" class="x-close">×</span>
		</button>
		<h4 class="modal-title"><?php echo $title; ?></h4>
	</div>
	<div class="modal-body">
		<ul class="nav nav-tabs">
            <li role="presentation" class="hidden"><a data-toggle="tab" href="#basic_manager">Cơ bản</a></li>
            <li role="presentation" class="active"><a data-toggle="tab" href="#progress_manager">Tiến độ</a></li>
            <li role="presentation"><a data-toggle="tab" href="#file_manager">Tài liệu</a></li>
            <li role="presentation" class="hidden"><a data-toggle="tab" href="#tranfer-manager">Chuyển công việc</a></li>
			<li role="presentation"><a data-toggle="tab" href="#supplier_manager">Bên thứ 3</a></li>
            <li role="presentation"><a data-toggle="tab" href="#detail_manager">Chi tiết</a></li>
			<li role="presentation"><a data-toggle="tab" href="#workload">Tỷ trọng công việc</a></li>
		</ul>
		<form method="POST" name="task_form" id="task_form" class="form-horizontal">
			<div class="tab-content" style="max-height: 700px; overflow-y: auto;">
				<input type="hidden" name="id" id="task_id"
				value="<?php echo $id; ?>" /> <input type="hidden" name="parent"
				value="<?php echo $parent; ?>" /> <input type="hidden"
				name="project_id" value="<?php echo $project_id; ?>" />
				<div class="tab-pane fade in" id="basic_manager">
					<div class="clearfix hang" style="margin-bottom: 10px;">
						<div class="col-lg-12">
							<div class="form-group">
								<label for="name"
								class="col-md-3 col-lg-2 control-label required"><?php echo $congviec_title; ?></label>
								<div class="col-md-9 col-lg-10">
									<input type="text" name="name" value="<?php echo $name; ?>"
									class="form-control" /> <span for="name"
									class="text-danger errors"></span>
								</div>
							</div>
						</div>
						<div class="col-lg-12" style="display: none;">
							<div class="form-group">
								<label for="first_name" class="col-md-3 col-lg-2 control-label">Màu
								sắc</label>
								<div class="col-md-9 col-lg-10">
									<input type="text" name="color" id="color"
									value="<?php echo $color; ?>" class="form-control" /> <span
									for="color" class="text-danger errors"></span>
								</div>
							</div>
						</div>
						<?php if($parent > 0):?>
							<div class="col-lg-12">
								<div class="form-group">
									<label for="first_name" class="col-md-3 col-lg-2 control-label ">Tỷ
									lệ</label>
									<div class="col-md-9 col-lg-10">
										<input type="number" name="percent"
										value="<?php echo $percent; ?>" class="form-control" /> <span
										for="percent" class="text-danger errors"></span>
									</div>
								</div>
							</div>
						<?php endif;?>
						<div class="col-lg-12">
							<div class="form-group">
								<label for="first_name" class="col-md-3 col-lg-2 control-label ">Mô
								tả</label>
								<div class="col-md-9 col-lg-10">
									<textarea name="detail" class="form-control"><?php echo $detail; ?></textarea>
									<span for="detail" class="text-danger errors"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix hang">
						<div id="add_navigation">
							<div class="title active" style="border-top: 1px solid #ccc;"
							data-id="thietlap_content">Thông tin</div>
							<div id="thietlap_content" class="content">
								<div class="row">
									<div class="col-lg-6" style="padding-right: 10px">
										<div class="form-group">
											<label class="col-md-3 col-lg-4 control-label required">Ngày bắt
											đầu</label>
											<div class="col-md-9 col-lg-8">
												<input type="text" name="date_start" id="date_start"
												class="form-control datepicker"
												value="<?php echo $date_start; ?>" /> <span
												for="date_start" class="text-danger errors"></span>
											</div>
										</div>
									</div>
									<div class="col-lg-6" style="padding-left: 10px;">
										<div class="form-group">
											<label class="col-md-3 col-lg-4 control-label required">Ngày kết
											thúc dự kiến</label>
											<div class="col-md-9 col-lg-8">
												<input type="text" name="date_end" id="date_end"
												class="form-control datepicker"
												value="<?php echo $date_end; ?>" /> <span for="date_end"
												class="text-danger errors"></span>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Khách hàng</label>
											<div class="col-md-9 col-lg-10">
												<div class="form-control"><?php if(!empty($cus)){echo $cus;} ?></div>
												
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Người được xem</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="xem" id="xem_list"
												x-title="Ng??i ???c xem"
												style="display: inline-block; width: 100%;"
												onclick="foucs(this);">
												<?php
												if (! empty($item['is_xem'])) {
													foreach ($item['is_xem'] as $key => $val) {
														$keyArr = explode('-', $key);
														if ($keyArr[0] == $id) {
															$user_id = $val['id'];
															$user_name = $val['username'];
															?>
															<span class="item"><input
																type="hidden" name="xem[]" class="xem"
																id="xem_<?php echo $user_id; ?>"
																value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span
																class="x" onclick="delete_item(this);"></span></span>

																<?php
															}
														}
													}
													?>
													<input type="text" autocomplete="off"
													id="xem_result" class="quick_search" />
													<div class="result"></div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Người phụ trách</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="implement"
												id="implement_list" x-title="Ng??i ph? trách"
												style="display: inline-block; width: 100%;"
												onclick="foucs(this);">
												<?php
												if (! empty($item['is_implement'])) {
													foreach ($item['is_implement'] as $key => $val) {
														$keyArr = explode('-', $key);
														if ($keyArr[0] == $id) {
															$user_id = $val['id'];
															$user_name = $val['username'];
															?>
															<span class="item"><input
																type="hidden" name="implement[]" class="implement"
																id="implement_<?php echo $user_id; ?>"
																value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span
																class="x" onclick="delete_item(this);"></span></span>

																<?php
															}
														}
													}
													?>


													<input type="text" autocomplete="off"
													id="implement_result" class="quick_search" />
													<div class="result"></div>
												</div>
											</div>
										</div>
									</div>


									<!-- JOIN -->

									<div class="col-lg-12">
										<div class="form-group">
											<label class="col-md-3 col-lg-2 control-label">Người tham gia</label>
											<div class="col-md-9 col-lg-10">
												<div class="x-select-users" x-name="join" id="join_list"
												x-title="Người tham gia"
												style="display: inline-block; width: 100%;"
												onclick="foucs(this);">
												<?php
												if (! empty($item['is_join'])) {
													foreach ($item['is_join'] as $key => $val) {
														$keyArr = explode('-', $key);
														if ($keyArr[0] == $id) {
															$user_id = $val['id'];
															$user_name = $val['username'];
															?>
															<span class="item"><input
																type="hidden" name="join[]" class="join"
																id="join_<?php echo $user_id; ?>"
																value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span
																class="x" onclick="delete_item(this);"></span></span>

																<?php
															}
														}
													}
													?>



													<input type="text" autocomplete="off"
													id="join_result" class="quick_search" />
													<div class="result"></div>
												</div>
											</div>
										</div>
									</div>

									<!-- JOIN -->


									
									<?php if($is_create_task == true):?>
										<div class="col-lg-12">
											<div class="form-group">
												<label class="col-md-3 col-lg-2 control-label">Người phê duyệt tiến
												độ</label>
												<div class="col-md-9 col-lg-10">
													<div class="x-select-users" x-name="progress_list"
													id="progress_list" x-title=""
													style="display: inline-block; width: 100%;"
													onclick="foucs(this);">
													<?php
													if (! empty($item['is_progress'])) {
														foreach ($item['is_progress'] as $key => $val) {
															$keyArr = explode('-', $key);
															if ($keyArr[0] == $id) {
																$user_id = $val['id'];
																$user_name = $val['username'];
																?>
																<span class="item"><input
																	type="hidden" name="progress_task[]" class="progress_task"
																	id="progress_task_<?php echo $user_id; ?>"
																	value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span
																	class="x" onclick="delete_item(this);"></span></span>

																	<?php
																}
															}
														}
														?>
														<input type="text"
														autocomplete="off" id="progress_result"
														class="quick_search" />
														<div class="result"></div>
													</div>
												</div>
											</div>
										</div>
									<?php endif;?>
								</div>
							</div>
							<?php if($is_create_task == true):?>
								<!-- <div class="title" data-id="permission_content">Cấp quyền</div> -->
								<div id="permission_content" class="content">
									<div class="row">
										<div class="col-lg-12">
											<div class="form-group">
												<label class="col-md-3 col-lg-2 control-label">Cập nhật CV
												con</label>
												<div class="col-md-9 col-lg-10">
													<div class="x-select-users" x-name="create_task_list"
													id="create_task_list" x-title=""
													style="display: inline-block; width: 100%;"
													onclick="foucs(this);">
													<?php
													if (! empty($item['is_create_task'])) {
														foreach ($item['is_create_task'] as $key => $val) {
															$keyArr = explode('-', $key);
															if ($keyArr[0] == $id) {
																$user_id = $val['id'];
																$user_name = $val['username'];
																?>
																<span class="item"><input
																	type="hidden" name="create_task[]" class="create_task"
																	id="create_task_<?php echo $user_id; ?>"
																	value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span
																	class="x" onclick="delete_item(this);"></span></span>

																	<?php
																}
															}
														}
														?>
														<input type="text"
														autocomplete="off" id="create_task_result"
														class="quick_search" />
														<div class="result"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-lg-12">
											<div class="form-group">
												<label class="col-md-3 col-lg-2 control-label">Phê duyệt CV</label>
												<div class="col-md-9 col-lg-10">
													<div class="x-select-users" x-name="pheduyet_task_list"
													id="pheduyet_task_list" x-title=""
													style="display: inline-block; width: 100%;"
													onclick="foucs(this);">
													<?php
													if (! empty($item['is_pheduyet'])) {
														foreach ($item['is_pheduyet'] as $key => $val) {
															$keyArr = explode('-', $key);
															if ($keyArr[0] == $id) {
																$user_id = $val['id'];
																$user_name = $val['username'];
																?>
																<span class="item"><input
																	type="hidden" name="pheduyet_task[]" class="pheduyet_task"
																	id="pheduyet_task_<?php echo $user_id; ?>"
																	value="<?php echo $user_id; ?>"><a><?php echo $user_name; ?></a>&nbsp;&nbsp;<span
																	class="x" onclick="delete_item(this);"></span></span>

																	<?php
																}
															}
														}
														?>
														<input type="text"
														autocomplete="off" id="pheduyet_result"
														class="quick_search" />
														<div class="result"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>

					</div>
				</div>


				<!-- Phê duyệt -->
				<?php if($pheduyet == 1 || $pheduyet == 2): ?>
					<div class="tab-pane fade in active" id="progress_manager">
						<div class="row" style="padding: 15px 0;">
							<div class="col-md-8">
								<select name="fields" id="s_task_id" class="form-control"
								id="fields">
								
								<?php
								if (! empty($slbTasks)) {
									foreach ($slbTasks as $val) {
										?>
										<option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
										<?php
									}
								}

								?>
							</select>
						</div>
						<div class="col-md-1">
							<a href="javascript:;" id="new-person-btn" onclick="add_tiendo();" class="btn btn-primary" title="Cập nhật tiến độ"><span class="">Cập nhật</span></a>
						</div>
						<div class="col-md-1"><?php if(($item['level'] <1)&& $trangthai !=2) {?>
							<input class="btn btn-success finish-task" value="Hoàn thành" type="button">
						<?php } ?>
					</div>
				</div>

				<div class="container-fluid"><h5>Tiến độ và phê duyệt</h5></div>

				<div id="exTab2" class="container-fluid">	
					<ul class="nav nav-tabs">
						<li class="active">
							<a  href="#tiendo" data-toggle="tab">Tiến độ</a>
						</li>
						<li>
							<a  href="#lichsu" data-toggle="tab">Lịch sử</a>
						</li>
						<li><a href="#pheduyet" data-toggle="tab">Phê duyệt</a>
						</li>
					</ul>

					<div class="tab-content ">
						<div class="tab-pane active" id="tiendo">
							<table class="table" id="tableT">
								<thead>
									<tr>
										<th>STT</th>
										<th>Tên công việc</th>
										<th>Tiến độ</th>
										<th>Tình trạng</th>
										<th>Ưu tiên</th>
										<th>Người phụ trách</th>
										<th>Người tham gia</th>
										<th>Ngày bắt đầu</th>
										<th>Ngày kết thúc dự kiến</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1; foreach ($progress_list as $key => $value) { ?>
										<tr>
											<td><?php echo $i;$i++ ?></td>
											<td><?php if($value['level']==0) {echo '<strong>'.$value['name'].'</strong>';}else{echo $value['name'];} ?></td>
											<td><?php echo $value['progress'] ?></td>
											<td><?php echo $value['trangthai'] ?></td>
											<td><?php echo $value['prioty'] ?></td>
											<td><?php echo $value['joins'] ?></td>
											<td><?php echo $value['implements'] ?></td>
											<td><?php echo $value['date_start'] ?></td>
											<td><?php echo $value['date_end'] ?></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>

						<div class="tab-pane" id="lichsu">
							<table class="table" id="tableP">
								<thead>
									<tr>
										<th>Tên công việc</th>
										<th>Tiến độ</th>
										<th>Tình trạng</th>
										<th>Ưu tiên</th>
										<th>Người thay đổi</th>
										<th>Ngày cập nhật</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($log)) :?>
										<?php foreach($log as $value){ ?>
											<tr>
												<td><?php echo $value['name'] ?></td>
												<td><?php echo $value['progress'] ?></td>
												<td><?php echo $value['trangthai'] ?></td>
												<td><?php echo $value['prioty'] ?></td>
												<td><?php echo $value['username'] ?></td>
												<td><?php echo $value['time'] ?></td>
											</tr>
										<?php } ?>
									<?php endif; ?>
								</tbody>
							</table>


						</div>
						<div class="tab-pane" id="pheduyet">
							<?php if($parent == 0){ ?>


								<table class="table ">
									<thead>
										<tr>
											<th>Tên dự án</th>
											<th>Trạng thái</th>
											<th>Phê duyệt</th>
											<th>Thời gian hoàn thành</th>
											<th>Thời gian phê duyệt</th>
											<th>Người phê duyệt</th>
										</tr>


									</thead>
									<tbody>
										<tr>
											<td><?php echo $name ?></td>
											<td><?php echo $trangthai_arr[$trangthai] ?></td>
											<td><?php if($pheduyet !=1 && $check){ ?>
												<span class="btn btn-primary pd-bt">Xác nhận</span>
											<?php } else if($pheduyet ==1) { ?>
												<span>Đã phê duyệt</span>
											<?php }
											else { ?>
												<span>Chờ phê duyệt</span>
											<?php } ?>
										</td>
										<td><?php if($progress ==100) {echo $date_finish; }?>
									</td>
									<td><?php if($pheduyet==1) echo date("d-m-Y",strtotime($item['date_pheduyet'])) ?></td>
									<td><?php if($pheduyet==1) echo $user_pheduyet ?></td>
								</tr>
							</tbody>
						</table>

					<?php } ?>
				</div>

			</div>
		</div>

	</div>
<?php endif; ?>

<div class="tab-pane fade" id="file_manager">
	<div class="manage-row-options 2 hidden" data-table="file-personal">
		<div class="control">
			<a href="javascript:;" class="btn btn-primary delete_inactive"
			title="Sửa" onclick="edit_file();"><span class="">Sửa</span></a>
			<a href="javascript:;" class="btn btn-delete btn-primary"
			onclick="delete_file();">Xóa lựa chọn</a>
		</div>
	</div>
	<div class="control clearfix" style="padding: 15px 0;">
		<div class="pull-right">
			<div class="buttons-list">
				<div class="pull-right-btn">
					<a href="javascript:;" id="new-person-btn"
					onclick="add_file();" class="btn btn-primary"
					title="Thêm mới tiến độ"><span class="">Thêm mới File</span></a>
				</div>
			</div>
		</div>
	</div>

	<div class="panel-heading">
		<h3 class="panel-title">
			<span class="tieude active">Danh sách tài liệu</span> <span
			id="count_tailieu" title="total suppliers"
			class="badge bg-primary tip-left">0</span> <i
			class="fa fa-spinner fa-spin" id="loading_2"></i>
		</h3>
	</div>

	<div
	class="panel-body nopadding table_holder table-responsive table_list">
	<table class="tablesorter table table-hover" id="sortable_table" data-table="file-personal">
		<thead>
			<tr>
				<th style="width: 50px;"><input type="checkbox"><label><span
					class="check_tatca"></span></label></th>
					<th data-field="name">Tên tài liệu</th>
					<th style="width: 20%;" data-field="file_name">Tên file</th>
					<th style="width: 14%;" data-field="size">Kích thước</th>
					<th style="width: 14%;" data-field="created">Ngày tạo</th>
					<th style="width: 10%;" data-field="username">Người tạo</th>
					<th style="width: 14%;" data-field="modified">Cập nhật cuối</th>
					<th style="width: 10%;">Cập nhật bởi</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>


<!-- CHUYEN CONG VIEC -->


<div class="tab-pane fade" id="tranfer-manager">
	<div class="clearfix hang" style="margin-bottom: 10px;">
		<div class="col-lg-12">


		</div>

		<!-- CHON NGUOI THAY THE -->
		<div class="col-lg-12">
			<div class="form-group">
				<h4>Chọn người thay thế</h4>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Người phụ trách</label>
					<select class="form-control" name="im_tranfer" id="im_tranfer">
						<option value="">Chọn</option>
						<?php if(!empty($item['is_implement'])){ foreach($item['is_implement'] as $im) { ?>
							<option value="<?php echo $im['id'] ?>"><?php echo $im['username'] ?></option>
						<?php }} ?>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Người thay thế</label>
					<select class="form-control" name="" id="list_implement_tranfer" multiple="multiple">
						<option value="">Chọn</option>
						<?php if(!empty($list_im)){ foreach($list_im as $joi) { ?>
							<option value="<?php echo $joi['id'] ?>"><?php echo $joi['username'] ?></option>
						<?php }} ?>
					</select>
				</div>
			</div>


			<div class="col-md-6">
				<div class="form-group">
					<label>Người tham gia</label>
					<select class="form-control" name="" id="jo_tranfer">
						<option value="">Chọn</option>
						<?php if(!empty($item['is_join'])){ foreach($item['is_join'] as $jo) { ?>
							<option value="<?php echo $jo['id'] ?>"><?php echo $jo['username'] ?></option>
						<?php }} ?>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>Người thay thế</label>
					<select class="form-control selectized" name="" id="list_join_tranfer" multiple="multiple">
						<option value="">Chọn</option>
						<?php if(!empty($list_jo)){ foreach($list_jo as $joi) { ?>
							<option value="<?php echo $joi['id'] ?>"><?php echo $joi['username'] ?></option>
						<?php }} ?>
					</select>
				</div>
			</div>




		</div>
		<span id ="joint" style="float: right;" class="btn btn-primary">Cập nhật </span>
		<hr>
		<div class="col-lg-12">
			<table class="table table-striped  table-bordered" id="chuyencv">
				<thead>
					<tr>
						<th>STT</th>
						<th>Người thay thế</th>
						<th>Người bị thay thế</th>
						<th>Thời gian</th>
						<th>Loại</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=1; foreach ($tranfers as  $tranfer) {

						?>
						<tr>
							<td><?php echo $i++ ?></td>
							<td><?php echo $tranfer['list_tranfer'] ?></td>
							<td><?php echo $tranfer['tranfer_person'] ?></td>
							<td><?php echo date("d-m-Y",strtotime($tranfer['time'])) ?></td>
							<td><?php echo ($tranfer['type']= $tranfer['type']=="join" ? "Tham gia" : "Phụ trách") ?></td>
						</tr>
					<?php } ?>
				</tbody>

			</table>
		</div>

		<!-- CHON NGUOI THAY THE -->
		<hr>
		<!-- <p><strong>Ghi chú:</strong> <span style="color: red;"></span></p> -->
	</div>
</div>

<!-- END CHUYEN CONG VIEC -->

<!-- BEN THU 3 -->

<div class="tab-pane fade" id="supplier_manager">
	<div class="clearfix hang" style="margin-bottom: 10px;">
		<div class="col-lg-12">

			<table class="tablesorter table table-hover" id="benthu3">
				<thead>
					<tr>
						<th>STT</th>
						<th>Tên chi phí</th>
						<th>Đơn vị thứ 3</th>
						<th>Chi phí</th>
						<th>Chi phí dự kiến</th>
					</tr>
				</thead>
				<tbody><?php $i=1; foreach ($suppliers as $supplier) {

					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $supplier['item_name'] ?></td>
						<td><?php echo $supplier['company_name'] ?></td>
						<td><?php echo number_format($supplier['cost']) ?></td>
						<td><?php echo $supplier['cost_price_interval']?></td>
					</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>

	</div>
</div>


<!-- BEN THU 3 -->

<!-- Tỷ trong cv -->


<div class="tab-pane fade" id="workload">
	<div class="clearfix hang" style="margin-bottom: 10px;">
		<div class="col-lg-12">
			<?php if(!empty($child)){ ?>
				<form id="mimi">
					<table class="tablesorter table table-hover">
						<thead>
							<tr>
								<th>STT</th>
								<th>Tên công việc</th>
								<th>Ngày</th>
								<th>Tỷ lệ</th>
								<th>Tỷ trọng</th>
							</tr>
						</thead>
						<tbody>
							<?php //echo "<pre>"; var_dump($child);die(); ?>
							<?php $i=1;foreach ($child as $key => $value) {

								?>
								<tr>
									<td><?php echo $i++  ?><input type="hidden" class="child_id" name="child_id[]" value="<?php echo $value['id'] ?>"></td>
									<td><?php echo $value['name'] ?></td>
									<td><?php echo $value['t'];?></td>
									<td><?php echo round($value['t']/$time*100,2) ?></td>
									<td><div class="form-group col-md-6"><input disabled="disabled" value="<?php echo $value['percent'] ?>" name="workload_list" class="workload_list" class="form-control" type="text"></div>
									</td>
								</tr>
							<?php } ?>
							<tr>
								<td></td>
								<td></td>
								<td><input class="sum_h" type="hidden"></td>
								<td><input value="Cập nhật tỷ trọng"class="btn hidden hehe btn-primary" type="button"></td>
								<td class="sum_t">Tổng:</td>
							</tr>
						</tbody>
					</table>
				</form>
			<?php } ?>
		</div>

	</div>
</div>

<script>


	
	$('.workload_list').change(function(){
		var t=0;	
		$('.workload_list').each(function(){
			t += parseFloat($(this).val());
		});

		$('.sum_t').text('Tổng: '+t);
		$('.sum_h').val(t);

	});

	$('.hehe').click(function(){

		var h = $('.sum_h').val();
		parseInt(h);
		if(h !=100)
		{
			toastr.error("Tổng tỷ trọng phải bằng 100%", 'Thông báo');
		}
		else
		{

			var child_id = new Array()
			$('.child_id').each(function(index,value){
				child_id.push($(this).val());
			});
			var workload_list = new Array();
			$('.workload_list').each(function(index,value){
				workload_list.push($(this).val());
			});

			$.ajax({
				type:'post',
				url:"<?php echo base_url('tasks/task_percent') ?>",
				dataType:"json",
				data: {
					parent_id:$('#task_id').val(),			
					child_id:child_id,
					workload_list:workload_list
				},
				success: function(result){
					if(result.flag=='success')	
					{
						toastr.success(result.notice,"Thông báo");
					}
					else{
						toastr.error(result.notice,"Thông báo");
					}			
					
				},
			});
		}

		return false;
	});

</script>

<!-- ty trong cv -->

<div class="tab-pane fade" id="detail_manager">
	<table width="100%" cellpadding="7" class="x-info"
	style="border: 0">
	<tbody>
		<tr>
			<td class="x-info-top" colspan="4"
			style="padding-left: 5px; padding-right: 10px; font-size: 16px; border: 0 !important;">
			<span class="tl" style="font-weight: bold;"><i
				class="fa fa-pencil"></i> Thông tin chi tiết</span>
			</td>
		</tr>
		<tr>
			<td class="x-info-label"><?php echo $congviec_title;  ?></td>
			<td class="x-info-content" style="font-weight: bold;"
			colspan="3"><?php echo $name . $name_ext; ?></td>
		</tr>
		<?php if($pheduyet == 0 && !empty($pheduyet_note)):?>
			<tr>
				<td class="x-info-label">Lý do</td>
				<td class="x-info-content" colspan="3"><?php echo $pheduyet_note; ?></td>
			</tr>
		<?php endif; ?>
		<?php
		if (! empty($item['customers'])) {
			foreach ($item['customers'] as $val)
				$customer_names[] = $val['name'];

			$customer_names = implode(', ', $customer_names);
		}
		?>
		<tr>
			<td class="x-info-label">Khách hàng</td>
			<td class="x-info-content" style="font-weight: bold;"
			colspan="3"><?php echo $customer_names; ?></td>
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
				<td class="x-info-content" colspan="3"
				style="font-weight: bold;"><?php echo $date_finish; ?></td>
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
				if (! empty($item['is_implement'])) {
					foreach ($item['is_implement'] as $key => $val) {
						$implement_ids = array();
						$keyArr = explode('-', $key);

						if ($keyArr[0] == $id)
							$implement_ids[] = $val['id'];

						$implement[$val['id']] = $val['username'];
					}

					foreach ($implement as $user_id => $user_name) {
						if (in_array($user_id, $implement_ids))
							$implement_names[] = '<span class="root">' . $user_name . '</span>';
						else
							$implement_names[] = '<span>' . $user_name . '</span>';
					}

					$implement_names = implode(', ', $implement_names);
					echo $implement_names;
				}
				?>
			</td>
			<td class="x-info-label">Người được xem</td>
			<td class="x-info-content">
				<?php
				if (! empty($item['is_xem'])) {
					foreach ($item['is_xem'] as $key => $val) {
						$xem_ids = array();
						$keyArr = explode('-', $key);

						if ($keyArr[0] == $id)
							$xem_ids[] = $val['id'];

						$xem[$val['id']] = $val['username'];
					}

					foreach ($xem as $user_id => $user_name) {
						if (in_array($user_id, $xem_ids))
							$xem_names[] = '<span class="root">' . $user_name . '</span>';
						else
							$xem_names[] = '<span>' . $user_name . '</span>';
					}

					$xem_names = implode(', ', $xem_names);
					echo $xem_names;
				}
				?>
			</td>
		</tr>
		<tr>
			<td class="x-info-label">Người tham gia</td>
			<td class="x-info-content" colspan="3"><span class="root"><?php echo rtrim($name_join_task,","); ?></span></td>
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
			<td class="x-info-label"
			style="border-bottom: inherit; border-bottom: 1px solid #d7dce5;"">Tài
			liệu đính kèm</td>
			<td class="x-info-content" colspan="3"
			style="vertical-align: middle; border-bottom: 1px solid #d7dce5;">
			<ul class="attach-file">
				<?php
				if (! empty($item['files'])) {
					$upload_dir = base_url() . 'assets/tasks/files/';
					foreach ($item['files'] as $val) {
						$file_name = $val['file_name'];
						$size = $val['size'] . ' Bytes';
						$link = $upload_dir . $file_name;
						?>
						<li><a href="<?php echo $link; ?>"
							target="_blank"><?php echo $file_name; ?> (<?php echo $size; ?>)</a></li>
							<?php
						}
					} else {
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
		<div class="title">
			<i class="fa fa-comment"></i> Ý kiến thảo luận
		</div>
		<div method="POST" id="task_comment"
		class="frm-comment fn-comment">
		<input type="hidden" name="task_id" id="task_id"
		value="<?php echo $id; ?>" /> <input type="hidden" name="parent"
		id="parent" value="<?php echo $parent; ?>" />
		<p class="avatar">
			<img class="fn-useravatar"
			src="http://data.ht/images/no-avatar.png">
		</p>
		<div class="wrap-comment">
			<textarea name="content" id="comment_content" cols="30"
			rows="10"></textarea>
			<p class="frm-checkbox" style="display: none;">
				<span>Đính kèm</span>
			</p>
			<input type="button" value="Bình luận" name="btnSubmit"
			id="btnComment" class="button btn-dark-blue pull-right" />
		</div>
	</div>
	<ul id="commentList" class="list-comment"></ul>
</div>
<?php endif; ?>
</div>
</div>
</form>
</div>
<div class="modal-footer">
			<!-- <a type="button" style="float: left;" class="btn btn-primary"
				onclick="delete_congviec(<?php echo $id; ?>);">
				<i class="fa fa-times" style="padding: 0 5px;"></i>Xóa</a>  -->
				<a class="btn btn-primary" onclick="edit_congviec();"><i
					class="fa fa-floppy-o" style="padding: 0 5px;"></i> Lưu</a> 
					<a type="button" class="btn btn-primary" data-dismiss="modal"><i
						class="fa fa-times-circle" style="padding: 0 5px;"></i>Đóng</a>

						<?php if($btnPheduyet == true):?>
							<a class="btn btn-default" onclick="pheduyet();"><i
								class="fa fa-gavel" style="padding: 0 5px;"></i>Duyệt</a>
							<?php endif;?>
						</div>

					</div>

					<script type="text/javascript">
						$( document ).ready(function() {
							var tt=0;
							$('.workload_list').each(function(){
								tt += parseFloat($(this).val());

							});

							$('.sum_t').text('Tổng: '+tt);
							$('.sum_h').val(tt);
							load_list('progress', 1);
							load_list('file', 1);
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




							var jo;
							var im; 

							var $select = $("#list_join_tranfer").selectize({
								onChange: function(value){jo=value;}
							});

							var $select2 = $("#list_implement_tranfer").selectize({
								onChange: function(value2){im=value2;}
							});


							$('#joint').click(function(){

								$.ajax({
									type:'post',
									url:"<?php echo base_url('tasks/task_tranfer') ?>",
									dataType:'text',
									data: {
										im:im,
										jo:jo,
										implement_t:$('#im_tranfer').val(),
										join_t:$('#jo_tranfer').val(),
										task_id:$('#task_id').val(),

									},
									success: function(result){
										var $select2 = $("#list_implement_tranfer").selectize();
										var $select = $('#list_join_tranfer').selectize();
										var control = $select[0].selectize;
										var control2 = $select2[0].selectize;
										control.clear();
										control2.clear();
										console.log(result);


										toastr.success("Chuyển công việc thành công","Thông báo");


										$('#my_modal').modal('toggle');
									},
								});
							});


							$('.finish-task').click(function(){





								bootbox.confirm({
									message: "Bạn có muốn hoàn thành công việc này?",
									buttons: {
										confirm: {
											label: 'Có',
										},
										cancel: {
											label: 'Không',
										}
									},
									callback: function (result) {
										if(result)
										{
											$.ajax({
												type:'post',
												url: '<?php echo base_url('tasks/task_finish') ?>',
												data: {
													id:$('#s_task_id').val()
												},

											})
											.done(function(result){

												toastr.success('Cập nhật thành công!', 'Thông báo');
												$('#my_modal').modal('toggle');

												var data_table = $('#project_grid_table').attr('data-table');

												if($('#current_project_id').length){
													var project_id = $('#task_form [name="project_id"]').val();
													load_task_childs(project_id, 1);
												}else if(data_table == 'task_list') {
													load_list('task_list', 1);
												}else
												load_task(1, 'clearAll');

											})
											.error(function(){
												bootbox.alert('Không có hồi đáp!');
											})
											.always(function(){
    				// console.log("gfhg");
    			});


										}
									}
								});


							})

						});





						$('.pd-bt').click(function(){


							bootbox.confirm({
								message: "Bạn có muốn phê duyệt dự án này?",
								buttons: {
									confirm: {
										label: 'Có',
									},
									cancel: {
										label: 'Không',
									}
								},
								callback: function (result) {
									if(result)
									{
										$.ajax({
											dataType:'json',
											type:'post',
											url: '<?php echo base_url('tasks/pheduyet_task') ?>',
											data: {
												id:$('#task_id').val()
											},

										})
										.done(function(result){
											console.log(result);
											if(result.flag=="warning")
											{
												toastr.warning(result.notice,'Cảnh báo');
												alert('hgh');
											}
											if(result.flag=="success")
											{
												toastr.success(result.notice,'Thông báo');
												$('#my_modal').modal('toggle');
												load_list('task_list', 1);
											}


										})
										.error(function(){
											bootbox.alert('Không có hồi đáp!');
										})
										.always(function(){
    				// console.log("gfhg");
    			});


									}
								}
							});


						});

						$('#tableP,#tableT,#chuyencv,#benthu3').dataTable({
							"searching":		false,
							"lengthChange": false,
			// "sPaginationType": "bootstrap",
			"pageLength": 10,
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





						$('body').on('click','table[data-table] td.cb',function(){
							var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
							var table = checkbox.closest('[data-table]');
							var data_table = table.attr('data-table');

							if (checkbox.prop('checked') == true){
								checkbox.prop('checked', false);
							}else{
								checkbox.prop('checked', true);
							}

							var checked_box = table.find('.file_checkbox:checked');
							if(checked_box.length == 0){
								$('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
							}else {
								$('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
							}

						});

    // check all
    $('body').on('click','table[data-table] .check_tatca',function(){
    	var checkbox = $(this).closest('th').find('input[type="checkbox"]');
    	var table = checkbox.closest('[data-table]');
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

    	var checked_box = table.find('.file_checkbox:checked');
    	if(checked_box.length == 0){
    		$('.manage-row-options[data-table="'+data_table+'"]').addClass('hidden');
    	}else {
    		$('.manage-row-options[data-table="'+data_table+'"]').removeClass('hidden');
    	}
    });</script>

