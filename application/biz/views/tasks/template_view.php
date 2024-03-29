<?php $this->load->view("partial/header"); ?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/template.js" ></script>

<div class="manage_buttons">
<div class="manage-row-options hidden" data-table="template_task">
	<div class="email_buttons text-center">		
		<a href="javascript:;" class="btn btn-red " title="Xóa" onclick="delete_template();"><span class="btn btn-primary">Xóa</span></a>
	</div>
</div>
<div class="cl">
	<div class="pull-left">
		<form action="" id="search_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
			<div class="search no-left-border">
				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" class="form-control ui-autocomplete-input" name="search" id="s_keywords" value="" placeholder="Tìm kiếm lộ trình mẫu" autocomplete="off">
			</div>
			<div class="clear-block hidden">
			    <i class="ion ion-close-circled"></i>
			</div>
		</form>
	</div>
	<div class="pull-right">
		<div class="buttons-list" style="padding-top: 0;">
			<div class="pull-right-btn">
				<a href="<?php echo base_url() . 'tasks/templateAdd' ?>" id="new-person-btn" class="btn btn-primary " title="Thêm mới" style="margin-top: 16px;"><span class="">Thêm mới</span></a>
               <!--  <a href="<?php echo base_url() . 'tasks' ?>" class="btn btn-primary " title="Công việc dự án" style="margin-top: 16px;"><span class="">Công việc dự án</span></a>
                <a href="<?php echo base_url() . 'tasks/personal' ?>" class="btn btn-primary " title="Công việc cá nhân" style="margin-top: 16px;"><span class="">Ghi chép</span></a> -->
            </div>
		</div>				
	</div>
    <div class="cl"></div>
</div>
</div>
<div class="container-fluid">
	<div class="row manage-table" id="template_list">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title">
					Lộ trình mẫu
					<span class="badge bg-primary tip-left" id="count_template">0</span>
					<span class="panel-options custom">
					</span>
				</h3>
				<i class="fa fa-spinner fa-spin" id="loading_1"></i>
			</div>
			<div class="panel-body nopadding table_holder table-responsive">
				<table class="tablesorter table  table-hover my-table" id="template_table" data-table="template_task">
					<thead>
						<tr>
							<th class="leftmost" style="width: 20px;">
								<input type="checkbox"><label for="select_all" class="check_tatca"><span></span></label>
							</th>
							<th data-field="name">STT</th>
							<th data-field="name">Tên lộ trình mẫu</th>
							<!-- <th data-field="name">Người khởi tạo</th> -->
							<th style="width: 20%;" data-field="modified">Cập nhật mới</th>
							<th style="width: 20%;" data-field="username">Cập nhật bởi</th>
							<th style="width: 100px;">Cập nhật</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>			
			</div>	
		</div>	
	</div>
</div>

<script type="text/javascript">
$( document ).ready(function() {
	load_list('template', 1);
   // search
   var typingTimer;       
   $('body').on('keyup','#s_keywords',function(){
	   clearTimeout(typingTimer);
	   typingTimer = setTimeout(startSearch, 300);
   });
   
   $('body').on('keydown','#s_keywords',function(){
	   clearTimeout(typingTimer);
   });
   
   function startSearch () {
	  load_list('template', 1);
   }
});
</script>
<?php
if(isset($_SESSION['notice'])) {
?>
<script type="text/javascript">
    $( document ).ready(function() {
        toastr.success('<?php echo $_SESSION['notice']; ?>', 'Thông báo');
    });
</script>
<?php
    unset($_SESSION['notice']);
}
?>
<?php $this->load->view("partial/footer"); ?>