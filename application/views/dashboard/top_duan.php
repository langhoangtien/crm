<table class="table" style="margin-top: 10px;">
	<tbody>
		<?php foreach($list as $i => $record) {?>
        <tr>
			<td>
        		<?php echo $i +1; ?>
        	</td>
			<td><a href="<?php echo base_url(); ?>tasks"><?php echo $record['name']; ?></a>
			</td>
			<td>
      		<?php echo date("d/m/Y",strtotime($record['date_start']));?>
      	 </td>
			<td>
      		<?php echo date("d/m/Y",strtotime($record['date_end']));?>
      	 </td>
      	 <td>
      		<?php echo round($record['progress'],2) . '%';?>
      	 </td>
		</tr>
        <?php } ?>
  </tbody>
</table>
<script type="text/javascript">
var DASHBOARD_TOP_DUAN = {
	init: function()
	{}
}
$(document).ready(function () {
	DASHBOARD_TOP_DUAN.init();
});
</script>