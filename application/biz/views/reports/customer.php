<?php $this->load->view('partial/header') ?>

<div class="row">
	<div class="col-md-12">
		<table class="table" id="tableK">
	<thead>
		<tr>
			<th>STT</th>
			<th>Tên khách hàng</th>
			<th>Thời gian tạo nhu cầu</th>
			<th>Mã hợp đồng</th>
			<th>Tên hợp đồng</th>
			<th>Trạng thái hợp đồng</th>
			<th>Ngày ký hợp đồng</th>
			<th>Tên dự án</th>
			<th>Xem</th>
		</tr>
	</thead>
	<tbody>
		<?php $i=0; foreach ($list as $key => $value) { $i++ ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $value['last_name']; ?></td>
				<td><?php echo date("d-m-Y",strtotime($value['sale_time'])); ?></td>
				<td><?php echo $value['code']; ?></td>
				<td><a href="<?php echo base_url();?>contracts/view/customer/<?php echo $value['contract_id']; ?>" title=""><?php echo $value['contract_name']; ?></a></td>
				<td><?php echo $value['status']; ?></td>
				<td>
					<?php if(!empty($value['date_signing'])) {
					echo date("d-m-Y",strtotime($value['date_signing']));} ?>
						
					</td>
				<td><?php echo $value['task_name']; ?></td>
				<td> <a href="<?php echo base_url('sales/unsuspend/'.$value['sale_id']) ?>">Xem</a></td>
			</tr>
		<?php } ?>
	</tbody>

</table>
	</div>



</div>

<script>

	  var datatableOption = {
                            "bFilter": false,
                            "bInfo": false,
                            "iDisplayStart ": 10,
                            "iDisplayLength": 10,
                            "bLengthChange": false,
                            "lengthChange": false,
                            "pageLength": 20,
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
                        };
	$('#tableK').DataTable(datatableOption);
</script>


<!-- <div id="exTab2" class="container">	
<ul class="nav nav-tabs">
			<li class="active">
        <a  href="#1" data-toggle="tab">Nhu cầu</a>
			</li>
			<li><a href="#2" data-toggle="tab">Hợp đồng và nhu cầu</a>
			</li>
			<li><a href="#3" data-toggle="tab">Dự án</a>
			</li>
		</ul>

			<div class="tab-content ">
			  <div class="tab-pane active" id="1">
          <h3>Standard tab panel created on bootstrap using nav-tabs</h3>
				</div>
				<div class="tab-pane" id="2">
          <h3>Notice the gap between the content and tab after applying a background color</h3>
				</div>
        <div class="tab-pane" id="3">
          <h3>add clearfix to tab-content (see the css)</h3>
				</div>
			</div>
  </div> -->

<?php $this->load->view('partial/footer') ?>