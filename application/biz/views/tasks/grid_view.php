<?php $this->load->view("partial/header"); ?>
    <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>
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
<?php
    $this->load->library('MY_System_Info');
    $info 			 = new MY_System_Info();
    $user_info 		 = $info->getInfo();
?>
<div class="manage_buttons">
    <div class="cl">
        <div class="pull-left">
            <div action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
                <div class="search no-left-border">
                    <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" id="search_keywords" value="" placeholder="Nhập text" autocomplete="off">
                    <button name="search_press" style="float: left;" id="search_press" class="btn btn-primary">Tìm kiếm</button>
                    <button name="btn_advance_project" id="btn_advance_project" class="btn btn-primary">Tìm kiếm nâng cao</button>

                    <input type="hidden" id="s_keywords" />
                    <input type="hidden" id="s_date_start" value="all" />
                    <input type="hidden" id="s_date_start_radio" value="simple" />
                    <input type="hidden" id="s_date_start_from" value="" />
                    <input type="hidden" id="s_date_start_to" value="" />
                    <input type="hidden" id="s_date_end" value="all" />
                    <input type="hidden" id="s_date_end_radio" value="simple" />
                    <input type="hidden" id="s_date_end_from" value="" />
                    <input type="hidden" id="s_date_end_to" value="" />
                    <input type="hidden" id="s_trangthai" value="" />
                    <input type="hidden" id="s_customer" value="" />
                    <input type="hidden" id="s_implement" value="" />
                    <input type="hidden" id="s_xem" value="" />
                    <input type="hidden" id="s_join" value="" />
                    <div id="s_trangthai_html" style="display: none;"></div>
                    <div id="s_customer_html" style="display: none;"></div>
                    <div id="s_implement_html" style="display: none;"></div>
                    <div id="s_join_html" style="display: none;"></div>
                    <div id="s_xem_html" style="display: none;"></div>
                </div>
                <div class="clear-block hidden">
                    <i class="ion ion-close-circled"></i>
                </div>
            </div>
        </div>
        <div class="pull-right">
            <div class="buttons-list">
                <div class="pull-right-btn">
                    <div class="piluku-dropdown">
                       
                      <a href="<?php echo base_url('tasks/task_log') ?>" class="btn btn-primary">LOG</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="cl"></div>
    </div>
</div>
<div class="container-fluid" id="project_grid_list">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="gantt_title">
				<h3 class="panel-title">
					<span class="tieude active"><a href="<?php echo base_url() . 'tasks'; ?>">Danh mục dự án</a></span>
                    <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_chart'; ?>">Lược đồ</a></span>
                    <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_list'; ?>">Công việc liên quan</a></span>
                    <span class="tieude"><a href="<?php echo base_url() . 'contracts/index/customer'; ?>">Quản lý hợp đồng</a></span>
                    <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_no_revenue'; ?>">Công việc không tạo doanh thu</a></span>
                    <span class="tieude"><a href="<?php echo base_url() . 'tasks/personal'; ?>">Ghi chép</a></span>
					<i class="fa fa-spinner fa-spin" id="loading_1"></i>	
				</h3>
			</div>
			<div class="panel-body" style="padding: 15px 0;">
				
				<div class="table-responsive">
					<table class="table tablesorter table-reports table-bordered table-tree" id="project_grid_table">
						<thead>
							<tr align="center" style="font-weight:bold">
								<td align="center" style="width: 3%;">STT</td>
								<td align="center" style="width: 12%" data-field="name">Tên Dự án</td>
								<td align="center" style="width: 12%" data-field="name">Tình trạng</td>
								<td align="center" style="width: 12%" data-field="name">Tiến độ</td>
								<td align="center" style="width: 5%;" data-field="prioty">Ưu tiên</td>
								<td align="center" style="width: 10%;">Người phụ trách</td>
								<td align="center" style="width: 10%;">Người tham gia</td>
                                <td align="center" style="width: 10%;">Nhu cầu liên quan</td>			
								<td align="center" style="width: 10%;">Hợp đồng liên quan</td>
                                <td align="center" style="width: 10%" data-field="date_start">Ngày bắt đầu</td>
                                <td align="center" style="width: 10%;" data-field="date_end">Ngày kết thúc dự kiến</td>
                                <td align="center" style="width: 10%;" data-field="date_finish">Ngày kết thúc</td>
								<!-- <td align="center" style="width: 10%;">Vấn đề phát sinh</td> -->			
								<td align="center" style="width: 10%;">Ghi chú</td>			
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					<table style="display: none;" id="table_project_grid_template">
						<tr class="row_project_grid_template">
							<td class="row_project_stt" align="center"></td>
							<td class="row_project_name"></td>
							<td class="row_project_status"></td>
							<td class="row_project_progress" style="text-align: left !important;"></td>
							<td class="row_project_prioty"></td>
							<td class="row_project_implement"></td>
							<td class="row_project_person_join"></td>
                             <td class="row_project_sale_link"></td>
							<td class="row_project_contract_link"></td>
                            <td class="row_project_start_date"></td>
                            <td class="row_project_end_date"></td>
                            <td class="row_project_finish_date"></td>
							<!-- <td class="row_project_problem"></td> -->
							<td class="row_project_note"></td>
						</tr>
					</table>
				</div>
			</div>
		</div>	
	</div>
</div>
<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<div id="advance_task_search" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="x-close">×</span></button>
                <h4 class="modal-title" id="my_search_task">Tìm kiếm công việc cho "<span>[Tên dự án]</span>"</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-horizontal-mobiles">
                    <input type="hidden" name="curret_project_id" id="current_project_id" value="0" />
                
                    <div class="report_date_range_complex">
                        <div class="form-group">
                            <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Ngày bắt đầu</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           Từ
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_start_from" id="adv_date_start_from" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    Đến
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_start_to" id="adv_date_start_to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
          
                    <div class="report_date_range_complex">
                        <div class="form-group">
                            <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Ngày kết thúc:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           Từ
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_end_from" id="adv_date_end_from">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    Đến
                                            </span>
                                            <input type="text" class="form-control date_time" name="adv_date_end_to" id="adv_date_end_to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Dự án:</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" id="adv_name" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Trạng thái :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="trangthai" id="trangthai_list" x-title="Trang thái" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="trangthai_result" class="quick_search">
                                <div class="result" style="top: 27px; display: none;">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Khách hàng :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="customer" id="customer_list" x-title="Khách hàng" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="customer_result" class="quick_search">
                                <div class="result" style="top: 27px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Phụ trách :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="implement" id="implement_list" x-title="Người phụ trách" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="implement_result" class="quick_search">
                                <div class="result" style="top: 27px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Theo dõi :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="xem" id="xem_list" x-title="Người được xem" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="xem_result" class="quick_search">
                                <div class="result" style="top: 27px;">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Người tham gia :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <div class="x-select-users" x-name="join" id="join_list" x-title="Người tham gia" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                <input type="text" autocomplete="off" id="join" class="quick_search">
                                <div class="result" style="top: 27px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="task_section">
                        <label for="" class="col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label">Công việc :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <ul class="list-inline">
                                <li>
                                    <input type="checkbox" name="status[]" value="-1" id="status_-1" class="reports_selected_location_ids_checkboxes">
                                    <label for="status_-1"><span></span>Chờ xử lý</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="status[]" value="0" id="status_0" class="reports_selected_location_ids_checkboxes">
                                    <label for="status_0"><span></span>Không phê duyệt</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="status[]" value="1-2" id="status_1_2" class="reports_selected_location_ids_checkboxes">
                                    <label for="status_1_2"><span></span>Đã phê duyệt</label>
                                </li>
                           </ul>
                        </div>
                    </div>
                    <div class="form-group" id="progress_section">
                        <label for="" class="col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label">Tiến độ :</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            <ul class="list-inline">
                                <li>
                                    <input type="checkbox" name="progress[]" value="-1" id="progress_-1" class="reports_selected_location_ids_checkboxes">
                                    <label for="progress_-1"><span></span>Chờ xử lý</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="progress[]" value="0" id="progress_0" class="reports_selected_location_ids_checkboxes">
                                    <label for="progress_0"><span></span>Không phê duyệt</label>
                                </li>
                                <li>
                                    <input type="checkbox" name="progress[]" value="1-2" id="progress_1_2" class="reports_selected_location_ids_checkboxes">
                                    <label for="progress_1_2"><span></span>Đã phê duyệt</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 5px;">
                        <div class="form-actions pull-right">
                            <input type="button" name="submitf" value="Thực hiện" id="btn_search_advance" style="margin-right: 16px;" class=" submit_button btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="task_report" class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> <h4 class="modal-title" id="my_report_task">Thống kê <span>[Dự án]</span></h4> </div>
            <div class="modal-body">
                <ul>
                    <li class="all">Công việc: <a onclick="do_change_advance_search(this,'all');">0</a></li>
                    <li class="implement">Phụ trách: <a onclick="do_change_advance_search(this,'implement');">0</a></li>
                    <li class="xem">Theo dõi: <a onclick="do_change_advance_search(this,'xem');">0</a></li>
                    <li class="cancel">Đóng dừng: <a onclick="do_change_advance_search(this,'cancel');">0</a></li>
                    <li class="not-done">Không thực hiện: <a onclick="do_change_advance_search(this,'not-done');">0</a></li>
                    <li class="unfulfilled">Chưa thực hiện: <a onclick="do_change_advance_search(this,'unfulfilled');">0</a></li>
                    <li class="processing">Đang tiến hành: <a onclick="do_change_advance_search(this,'processing');">0</a></li>
                    <li class="slow_proccessing">Chậm tiến độ: <a onclick="do_change_advance_search(this,'slow_proccessing');">0</a></li>
                    <li class="finish">Đã hoàn thành: <a onclick="do_change_advance_search(this,'finish');">0</a></li>
                    <li class="slow-finish">Đã hoàn thành nhưng chậm tiến độ: <a onclick="do_change_advance_search(this,'slow-finish');">0</a></li>
                </ul>
             </div>
        </div>
    </div>
</div>
<style>
.modal .modal-title {
    font-weight: bold;
}

.search-advance-form {
    font-family: Arial;
}
.search-advance-form span.x-close {
    font-size: 21px !important;
}
.detailed-reports i.fa-search {
	font-size: 16px;
    margin-right: 0;
}
</style>
<script type="text/javascript">
var user_id = <?php echo $user_info['id']; ?>;
var user_name = '<?php echo $user_info['username']; ?>';
var current_project_id = 0;

var data_table = $('#project_grid_table').attr('data-table');
$( document ).ready(function() {
	load_list('project-grid', 1);
    //sort
    $('body').on('click','table [data-field]',function(){
        var attr     = $(this).attr('data-field');
        var table    = $(this).closest('table');
        var table_id = table.attr('id');
        if($(this).hasClass('header')) {
            if($(this).hasClass('headerSortUp')){
                $(this).removeClass('headerSortUp');
                $(this).addClass('headerSortDown');
            }else {
                $(this).removeClass('headerSortDown');
                $(this).addClass('headerSortUp');
            }
        }else {
            table.find('td').removeClass('header');
            table.find('td').removeClass('headerSortUp');
            table.find('td').removeClass('headerSortDown');
            $(this).addClass('header headerSortUp');
        }

        if(table_id == 'project_grid_table') {
            if(data_table == 'task_list')
                load_list(data_table, 1);
            else
                load_list('project-grid', 1);
        }else {
            var tr_parent = table.closest('[data-parent]');
            var project_id = tr_parent.attr('data-parent');
            load_task_childs(project_id, 1);
        }
    });

    $(document).on('click','.pagination a',function(){
        var page = $(this).attr('data-page');
        load_list('project-grid', page);
        return false;
    });


});
</script>

<?php $this->load->view("partial/footer"); ?>