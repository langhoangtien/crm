<?php $this->load->view("partial/header"); ?>
<link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/personal.js" ></script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<table class="table">
				<thead>
					<tr>
						<th>STT</th>
						<th>Tên công việc</th>
						<th>Ngày hoàn thành</th>
						<th>Phê duyệt</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=1; foreach ($list as $key => $value) {?>
							<tr>
								<td><?php echo $i;$i++ ?></td>
								<td><a href="javascript:;" onclick="update_personal_task('edit', 'norevenue', <?php echo $value['id'] ?>)"><?php echo $value['name'] ?></a></td>
								<td><?php echo date("d-m-Y",strtotime($value['date_finish'])) ?></td>
								<td><a onclick="pheduyet_nhanh(<?php echo $value['id'] ?>)" class="btn btn-primary">Phê duyệt</a></td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

    </div>
    <div class="modal fade box-modal" id="quick_modal">
    </div>



    <script>
	function pheduyet_nhanh(id){
		bootbox.confirm({
    message: "Bạn có muốn phê duyệt công việc này không?",
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
    				dataType:"json",
    				url: '<?php echo base_url('tasks/fast_approve_norevenue') ?>',
    				data: {
    					id:id
    				},

    			})
    			.done(function(res){
    			
    				// console.log(res);
    				if(res.flag =='warning')
    				{

    					toastr.warning('Bạn không có quyền phê duyệt dự án này!', 'Cảnh báo');
    				}
    				else{
    					toastr.success('Phê duyệt thành công', 'Thông báo');
				    	location.reload();	
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
	}
</script>
