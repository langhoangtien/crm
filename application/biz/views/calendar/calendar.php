<?php $this->load->view("partial/header");?>
<link href='<?php echo base_url();?>assets/css/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo base_url();?>assets/css/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo base_url();?>assets/js/calendar/moment.min.js'></script>
<script src='<?php echo base_url();?>assets/js/calendar/fullcalendar.min.js'></script>
<script src='<?php echo base_url();?>assets/js/calendar/locale-all.js'></script>
<link href="<?php echo base_url();?>assets/css/font-awesome.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/personal.js" ></script>
<style>

 #calendar {
    max-width: 900px;
    margin: 0 auto;
  }
.btn {
	color: #fff !important;
}
.btn-active {
	background: #E64D27 !important;
}

.panel-heading {
	font-size: 18px;
}
.btn-save {
	display: none;
}
</style>

<div class="container-fluid">
        <div class="row manage-table">
            <div class="panel panel-piluku">
            	<div class="gantt_title">
                    <h3 class="panel-title">
                        <a class="btn btn-primary" href="<?php echo base_url() . 'tasks/grid'; ?>"><span>Danh mục dự án</span></a>
                        <a class="btn btn-primary" href="<?php echo base_url() . 'tasks/task_chart'; ?>"><span>Lược đồ</span></a>
                         <a class="btn btn-primary" href="<?php echo base_url() . 'tasks/task_list'; ?>"><span>Công việc liên quan</span></a>
                         <a class="btn btn-primary" href="<?php echo base_url() . 'contracts/index/customer'; ?>"><span>Quản lý hợp đồng</span></a>
                         <a class="btn btn-primary" href="<?php echo base_url() . 'tasks/task_no_revenue'; ?>"><span>Công việc không tạo doanh thu</span></a>
    
                    </h3>
                </div>
                <div class="panel-heading">
                    Lịch biểu
                </div>
                 <div class="panel-body">
                 	<br/>
                 	<div class="row">
                 		<div class="col-md-12">
                 			<button class="btn btn-active" disabled="disabled">Calendar</button>
                 			<a class="btn btn-primary" href="<?php echo base_url() . 'tasks/personal'; ?>"><span>Danh sách</span></a>
                 		</div>
                 	</div>
                 	<br/>
                 	<div class="row">
                 		<div class="col-md-12">
		                 	<div id='calendar'></div>
                 		</div>
                 	</div>
                 </div>
            </div>
        </div>
</div>
<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

    </div>
<script>

  $(document).ready(function() {
	
    var calendar = $('#calendar').fullCalendar({
	        	header: {
	            	left: 'prev,next today',
	            	center: 'title',
	            	right: 'listDay,listWeek,month'
	       		},
	          	views: {
	              listDay: { buttonText: 'Ngày' },
	              listWeek: { buttonText: 'Tuần' },
	              month: { buttonText: 'Tháng' },
	            },
	            defaultView: 'month',
	      		editable: true,
	      		locale: 'vi',
	      		eventLimit: true, // allow "more" link when too many events
	      		eventSources: [{
					url: '<?php base_url()?>calendars/view_event',
					color: 'yellow',
					textColor: 'blue'
          		}],
          		eventClick: function(calEvent, jsEvent, view) {
              		if (calEvent.id == undefined) return false;
          			update_personal_task('edit', 'personal', calEvent.id);
              	}
    });
	
  });

</script>
<?php $this->load->view("partial/footer");?>