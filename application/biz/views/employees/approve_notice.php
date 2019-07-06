<?php $this->load->view("partial/header"); ?>
<style type="text/css">
.panel table p{
	margin: 0;
}
</style>
<div class="container-fluid">
	<div class="row manage-table">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<h3 class="panel-title hidden-print">Danh sách yêu cầu phê duyệt </h3>
			</div>
			<div class="panel-body nopadding table_holder table-responsive">
					<?php echo $listViewHtml; ?>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript">
var APPROVER_GROUPS = {
	binClickEventCreateGroup: function()
	{
		$('#create_approver_group').unbind('click').bind('click', function(){
			var _data = {};
			coreAjax.call(
				'<?php echo site_url("approver_groups/view");?>',
				_data,
				function(response)
				{
					if(response.success)
					{
						$('#approverGroupModal').remove();
						$('body').append(response.html);
						$('#approverGroupModal').modal('show');
					}
				}
			);
		})
	}
}
$( document ).ready(function() {
	
});
</script>