<?php
$trangthai_arr = array('Chưa thực hiện', 'Đang thực hiện', 'Hoàn thành', 'Đóng/dừng', 'Không thực hiện');
$prioty_arr    = array('Rất cao', 'Cao', 'Trung bình', 'Thấp', 'Rất thấp');
$id_admin      = $user_info['id'];
?>

<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
                <h4 class="modal-title"><?php if($type == 'personal') echo "Thêm mới Công việc cá nhân"; if($type=="norevenue") echo "Thêm mới công việc không tạo doanh thu" ?></h4>
            </div>
            <div class="toolbars">
                <ul class="list clearfix">
                    <?php if($type=="norevenue") {?>
                        <li class="btn-save"><a href="javascript:;" onclick="add_norevenue_task();"><i class="fa fa-floppy-o"></i>Lưu</a></li>
                    <?php } else { ?>
                        <li class="btn-save"><a href="javascript:;" onclick="add_personal_task();"><i class="fa fa-floppy-o"></i>Lưu</a></li>
                    <?php } ?>
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
                    <div class="tabs" id="basic_manager" style="display: block;">
                        <div class="clearfix hang" style="margin-bottom: 10px;">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="name" class="col-md-3 col-lg-2 control-label required">Tên công việc</label>
                                    <div class="col-md-9 col-lg-10">
                                        <input type="text" name="name" value="" class="form-control" />
                                        <span for="name" class="text-danger errors"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="first_name" class="col-md-3 col-lg-2 control-label ">Mô tả</label>
                                    <div class="col-md-9 col-lg-10">
                                        <textarea name="detail" class="form-control"></textarea>
                                        <span for="detail" class="text-danger errors"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6" style="padding-right: 10px">
                                <div class="form-group">
                                    <label class="col-md-3 col-lg-4 control-label required">Bắt đầu</label>
                                    <div class="col-md-9 col-lg-8">
                                        <input type="text" name="date_start" id="date_start" class="form-control" />
                                        <span for="date_start" class="text-danger errors"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6" style="padding-left: 10px;">
                                <div class="form-group">
                                    <label class="col-md-3 col-lg-4 control-label required">Kết thúc</label>
                                    <div class="col-md-9 col-lg-8">
                                        <input type="text" name="date_end" id="date_end" class="form-control" />
                                        <span for="date_end" class="text-danger errors"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- <script type="text/javascript">
                                $('#date_start').datetimepicker({
                                    format:'DD-MM-YYYY HH:mm:ss'
                                });
                                $('#date_end').datetimepicker({
                                    format:'DD-MM-YYYY HH:mm:ss'
                                });

                            </script> -->
                            <div class="col-lg-6" style="padding-right: 10px">
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

                                    </div>

                                </div>

                            </div>
                            <div class="col-lg-6" style="padding-left: 10px">
                                <div class="form-group">
                                    <label class="col-md-3 col-lg-4 control-label">Ưu tiên</label>
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
                                    <label for="progress" class="col-md-3 col-lg-2 control-label required">Tiến độ</label>
                                    <div class="col-md-9 col-lg-10">
                                      <input type="number" name="progress" value="0" min="0" max="100" class="form-control"/>
                                      <span for="progress" class="text-danger errors"></span> 
                                  </div>
                              </div>
                          </div>


                          <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Khách hàng</label>
                                <div class="col-md-9 col-lg-10">
                                    <div class="x-select-users" x-name="customer" id="customer_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                        <input type="text" autocomplete="off" id="customer_result" class="quick_search" />
                                        <div class="result">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- # Nếu là công việc không tạo doanh thu -->
                        <?php if($type =="norevenue") { ?>

                       
                            <input type="hidden" value="2" name="type_task">

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-md-3 col-lg-2 control-label">Người phụ trách</label>
                                    <div class="col-md-9 col-lg-10">
                                        <div class="x-select-users" x-name="implement" id="implement_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                            <input type="text" autocomplete="off" id="implement_result" class="quick_search" />
                                            <div class="result">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-md-3 col-lg-2 control-label">Người phê duyệt</label>
                                    <div class="col-md-4 col-lg-3">
                                        <select name="approved" id="" class="form-control">
                                            <option value="">Chọn</option>
                                            <?php foreach ($employees as $value) {
                                                echo '<option value="'.$value['id'].'">'.$value['username'].'</option>';
                                            } ?>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        

                             <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-md-3 col-lg-2 control-label">Người tham gia</label>
                                    <div class="col-md-9 col-lg-10">
                                        <div class="x-select-users" x-name="join" id="join_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                            <input type="text" autocomplete="off" id="join_result" class="quick_search" />
                                            <div class="result">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                        <div class="col-lg-12" style="display: none;">
                            <div class="form-group">
                                <label class="col-md-3 col-lg-2 control-label">Theo dõi</label>
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
            </form>
        </div>
    </div>
</div>
