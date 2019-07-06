<?php $this->load->view("partial/header"); ?>
 <link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/tasks/css/responsive.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/task-core.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/tasks/js/script.js" ></script>

<div class="row">
	<div class="col-md-12">
		<table class="table" id="thongbaopheduyet">
			<thead>
				<tr>
					<th>STT</th>
					<th>Tên dự án</th>
					<th>Ngày hoàn thành</th>
					<th>Phê duyệt nhanh</th>
				</tr>
			</thead>

			<tbody>
				<?php $i=1; foreach ($result as $key => $value) {?>
				<tr>
				<td><?php echo $i; $i++ ?></td>
				<td> <a href="javascript:;" onclick="edit_task_grid(<?php echo $value['id']; ?>);"><?php echo $value['name'] ?></a></td>
				<td><?php echo date("m-d-Y",strtotime($value['date_finish'])); ?></td>
				<td><a class="btn btn-primary" onclick="pheduyet_nhanh(<?php echo $value['id'] ?>)">Phê duyệt</a></td>
			</tr>
		<?php } ?>
			</tbody>
		</table>
	</div>

</div>

<div id="my_modal" class="modal fade bs-example-modal-lg search-advance-form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">

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


<script>
	function pheduyet_nhanh(id){
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
    				type:'post',
    				dataType:"json",
    				url: '<?php echo base_url('tasks/pheduyet_nhanh') ?>',
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




	$('#thongbaopheduyet').dataTable({
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
</script>
<?php $this->load->view("partial/footer"); ?>