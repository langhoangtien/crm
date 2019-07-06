<?php $this->load->view("partial/header"); ?>
	<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/gantt/codebase/dhtmlxgantt.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/scripts/gantt/codebase/skins/dhtmlxgantt_meadow.css" type="text/css" />
	
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css?_=<?php echo time(); ?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

	<script src="<?php echo base_url();?>assets/scripts/gantt/codebase/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js?_=<?php echo time(); ?>" ></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task.js?_=<?php echo time(); ?>" ></script>
    <script src="<?php echo base_url();?>assets/scripts/gantt/codebase/ext/dhtmlxgantt_tooltip.js" type="text/javascript" charset="utf-8"></script>
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
	<div class="clearfix" id="task_control">
		<div class="pull-left">
			<div action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
				<div class="search no-left-border" style="padding-left: 5px;">
					<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" name="search" id="search_keywords" value="" placeholder="Tìm kiếm dự án" autocomplete="off" style="border: 0;">
                    <select class="form-control" id="search_date_type"><option value="0" selected="selected">-- Thời gian --</option><option value="today">Trong ngày</option><option value="weekend">Trong tuần</option><option value="month">Trong tháng</option><option value="year">Trong năm</option> </select>
                    <button name="btn_advance_project" id="btn_advance_project" class="btn btn-primary">Tìm kiếm nâng cao</button>

                    <input type="hidden" id="s_keywords" value="">
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
                    <input type="hidden" id="s_join" value="" />
                    <input type="hidden" id="s_xem" value="" />
                    <div id="s_trangthai_html" style="display: none;"></div>
                    <div id="s_customer_html" style="display: none;"></div>
                    <div id="s_implement_html" style="display: none;"></div>
                    <div id="s_join_html" style="display: none;"></div>
                    <div id="s_xem_html" style="display: none;"></div>

                </div>
				<div class="clear-block hidden">
					<a class="clear" href="javascript:;">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>
			</div>
		</div>
		<div class="pull-right">
				<div class="buttons-list">
					<div class="pull-right-btn">
                    
						</div>
					</div>
				</div>				
			</div>
	</div>
	<div class="gantt_title">
		<h3 class="panel-title">
			
			<span class="tieude"><a href="<?php echo base_url() . 'tasks/grid'; ?>">Danh mục dự án</a></span>
            <span class="tieude active"><a href="<?php echo base_url() . 'tasks/task_chart'; ?>">Lược đồ</a></span>
            <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_list'; ?>">Công việc liên quan</a></span>
            <span class="tieude"><a href="<?php echo base_url() . 'contracts/index/customer'; ?>">Quản lý hợp đồng</a></span>
            <span class="tieude"><a href="<?php echo base_url() . 'tasks/task_no_revenue'; ?>">Công việc không tạo doanh thu</a></span>
             <span class="tieude"><a href="<?php echo base_url() . 'tasks/personal'; ?>">Ghi chép</a></span>
			<i class="fa fa-spinner fa-spin" id="loading_1"></i>
			<span class="panel-options custom" id="gantt_pagination">
			</span>
		</h3>
	</div>
    <div class="row">
        <div class="col-md-4"></div>
         <div class="col-md-4">
                <div class="form-group">
        <select class="form-control" name="" id="filter">
            <option value="1">Theo ngày</option>
            <option value="2">Theo tuần</option>
            <option value="3">Theo tháng</option>
            <option value="4">Theo năm</option>
        </select> 
    </div>
         </div>
          <div class="col-md-4"></div>
      
    </div>
   
	<div id="gantt_here" style='width:100%; min-height: 500px; padding-top: 10px;'></div>
	<div>
		<input type="hidden" name="start_date_original" id="start_date_original" />
		<input type="hidden" name="start_date_drag" id="start_date_drag" />
		<input type="hidden" name="end_date_drag" id="end_date_drag" />
	</div>

    <div id="my_modal" class="modal fade search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

    </div>

    <div class="modal fade box-modal" id="quick_modal">
    </div>
    <div id="advance_project_search" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="x-close">×</span></button>
                    <h4 class="modal-title" id="my_search_task">Tìm kiếm dự án</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal form-horizontal-mobiles">
                    
                        <div class="report_date_range_complex">
                            <div class="form-group">
                                <label for="complex_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Ngày bắt đầu :</label>
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
                                <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label  ">Ngày kết thúc :</label>
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
                            <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Tiêu đề:</label>
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
                            <label for="simple_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Tham gia :</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <div class="x-select-users" x-name="join" id="join_list" x-title="Người tham gia" style="display: inline-block; width: 100%;" onclick="foucs(this);">
                                    <input type="text" autocomplete="off" id="join_result" class="quick_search">
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

                        <div class="form-group" style="margin-bottom: 5px;">
                            <div class="form-actions pull-right">
                                <input type="button" name="submitf" value="Thực hiện" id="btn_p_search_advance" style="margin-right: 16px;" class=" submit_button btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

	<script type="text/javascript">


 function setScaleConfig(value) {
        switch (value) {
            case "1":
                gantt.config.scale_unit = "day";
                gantt.config.step = 1;
                gantt.config.date_scale = "%d %M";
                gantt.config.subscales = [];
                gantt.config.scale_height = 27;
                gantt.templates.date_scale = null;
                break;
            case "2":
                var weekScaleTemplate = function (date) {
                    var dateToStr = gantt.date.date_to_str("%d %M");
                    var startDate = gantt.date.week_start(new Date(date));
                    var endDate = gantt.date.add(gantt.date.add(startDate, 1, "week"), -1, "day");
                    return dateToStr(startDate) + " - " + dateToStr(endDate);
                };

                gantt.config.scale_unit = "week";
                gantt.config.step = 1;
                gantt.templates.date_scale = weekScaleTemplate;
                gantt.config.subscales = [
                    {unit: "day", step: 1, date: "%D"}
                ];
                gantt.config.scale_height = 50;
                break;
            case "3":
                gantt.config.scale_unit = "month";
                gantt.config.date_scale = "%F, %Y";
                gantt.config.subscales = [
                    {unit: "day", step: 1, date: "%j, %D"}
                ];
                gantt.config.scale_height = 50;
                gantt.templates.date_scale = null;
                break;
            case "4":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.date_scale = "%Y";
                gantt.config.min_column_width = 50;

                gantt.config.scale_height = 90;
                gantt.templates.date_scale = null;


                gantt.config.subscales = [
                    {unit: "month", step: 1, date: "%M"}
                ];
                break;
        }
    }

    setScaleConfig('4');





	$( document ).ready(function() {
        gantt.config.order_branch = true;
        gantt.config.order_branch_free = true;
		load_task(1);

		gantt.templates.quick_info_date = function(start, end, task){
		       return gantt.templates.task_time(start, end, task);
		};

		

// 		gantt.attachEvent("onBeforeTaskMove", function(id, parent, tindex){
// 		    var task = gantt.getTask(id);

// 			console.log(id + ',' + parent);

// 		    if(task.parent != parent)
// 		        return false;
// 		    return true;
// 		});

// 		gantt.attachEvent("onRowDragStart", function(id, target, e) {
// 		    console.log('id: ' + id + ', target: ' + target);
// 		    console.log(target);
// 		    return true;
// 		});
		
		gantt.attachEvent("onBeforeRowDragEnd", function(id, parentId, tindex) {
		    var task = gantt.getTask(id);
// 		    var taskParent = gantt.getTask(parentId);
		    //compare level
// 		    console.log(gantt.getTaskIndex(id));
// 		    console.log(taskParent);
		    if (task.parent != parentId)
			    return false;
			    
			$.post('<?php echo base_url()?>tasks/update_task_pos', {
					id: id,
					parent_id: parentId,
					index: gantt.getTaskIndex(id)
				},
				function(data) {
					load_task(1);	
			});
	        
		    return true;
		});

        $('body').on('click','#my_modal .manage-table table th',function(){
            var thElement = $('#my_modal .manage-table table th');
            var attr = $(this).attr('data-field');
            if (typeof attr !== typeof undefined && attr !== false) {
                if($(this).hasClass('header')) {
                    if($(this).hasClass('headerSortUp')){
                        $(this).removeClass('headerSortUp');
                        $(this).addClass('headerSortDown');
                    }else {
                        $(this).removeClass('headerSortDown');
                        $(this).addClass('headerSortUp');
                    }
                }else {
                    thElement.removeClass('header');
                    thElement.removeClass('headerSortUp');
                    thElement.removeClass('headerSortDown');
                    $(this).addClass('header headerSortUp');
                }

                var li_element = $('.arrord_nav ul li.active');
                var className  = li_element.attr('data-id');
                if(className == 'progress_manager') {
                    var content_id = $('#progress_manager span.tieude.active').attr('data-id');
                    if(content_id == 'progress_danhsach') {
                        load_list('progress', 1);
                    }else if(content_id == 'request_list')
                        load_list('request', 1);
                    else if(content_id == 'pheduyet_list'){
                        load_list('pheduyet', 1);
                    }
                }else
                    load_list('file', 1);
            }
        });

    });




$('#filter').change(function(){

      value= $('#filter').val();

        setScaleConfig(value);
        gantt.render();  
});



	</script>


    <style>
        .gantt_grid_head_add {display: none;}
    </style>
<?php $this->load->view("partial/footer"); ?>