<style>
.progress-bar {
    position: relative;
    width: 220px;
    height: 20px;
    line-height: 20px;
    overflow: hidden;
    font-weight: bold;
    font-size: 12px;
    box-shadow: 0 4px 10px -5px rgba(0, 0, 0, 0.25);
    float: none !important;
    margin: 0 auto;
}

.progress-bar span {
	position: absolute;
	left: 50%;
	transform: translate(-50%, 0);
    width: 220px;
}

.progress-text {
	float: left; height: 18px; line-height: 18px; margin-left: 10px;
}

.progress-bar .bar.positive {
    left: 0;
    -webkit-animation: animate-positive 1s;
    animation: animate-positive 1s;
}

.progress-bar .bar.negative {
    right: 0;
    width: 20%;
    -webkit-animation: animate-negative 1s;
    animation: animate-negative 1s;
}

.progress-bar .bar {
    position: absolute;
    top: 0;
    height: 100%;
    overflow: hidden;
}

.progress-bar .bar.positive span {
    left: 0;
    color: white;
}
.progress-bar .bar span {
    position: absolute;
    display: block;
    width: 150px;
    height: 100%;
    text-align: center;
}

.progress-bar .bar.negative span {
    right: 0;
    color: #fff;
}

#tbl_tasks .treegrid-expander {
    height: 13px !important;
}
#tbl_tasks .task-name {
    cursor: pointer;
}

</style>
<?php if (!empty($records)) { ?>
<table id="tbl_tasks" class="tree table tablesorter table-hover locations-level">
	<thead>
		<tr>
			<th>Dự án</th>
			<th>Ưu tiên</th>
			<th>Bắt đầu</th>
			<th>Kết thúc</th>
			<th>Tiến độ</th>
			<th>Tình trạng</th>
			<th>Phụ trách</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($records as $record) {
		$treegridParent = !empty($record['parent_id']) ? 'treegrid-parent-' . $record['parent_id'] : '';
		$positive = $record['progress'];
		$negative = 100 - $positive;
		$p_color = $record['p_color'];
		$n_color = $record['color'];
		?>
		<tr class="treegrid-<?php echo $record['id']; ?> <?php echo $treegridParent; ?>">
			<input type="hidden" name="task_id" value="<?php echo $record['id']; ?>" />
			<td><span class="task-name"><?php echo $record['name'];?></span></td>
    		<td><?php echo $record['prioty'];?></td>
    		<td><?php echo $record['start_date'];?></td>
    		<td><?php echo $record['end_date'];?></td>
    		<td align="center">
    			<div class="clearfix">
    				<div class="progress-bar" style="float: left;">
    					<div class="bar positive" style="width: <?php echo $positive; ?>%; background: <?php echo $p_color; ?>">
    					</div>
    					<div class="bar negative" style="width: <?php echo $negative; ?>'%; background: <?php echo $n_color; ?>">
    					</div>
    					<span><?php echo $positive; ?>% - <?php echo $record['note']; ?></span>
    				</div>
    			</div>
    		</td>
    		<td><?php echo $record['trangthai'];?></td>
    		<td><?php echo $record['implement'];?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else { ?>
<p style="
    font-size: 18px;
    font-style: italic;
    color: red;
    text-align: center;
">Không có bản ghi nào</p>
<?php } ?>
<script type="text/javascript">
var HISTORY_TRANS = {
	init: function()
	{
		$('.tree').treegrid({
			 treeColumn: 0,
			 'initialState': 'collapsed',
		 });

        $('#tbl_tasks .task-name').unbind('click').bind('click', function(){
            var selectedRow = $(this).closest('tr');
            var _data = {};
            _data['task_id'] = $(selectedRow).find('input[name="task_id"]').val();
            coreAjax.call(
                '<?php echo site_url("tasks/taskDetail");?>',
                _data,
                function(response)
                {
                    $('#MODAL_task_detail').remove();
                    $('body').append(response.html);
                    $('#MODAL_task_detail').modal('show');
                }
            );
        });
	}
}
$(document).ready(function () {
	HISTORY_TRANS.init();
});
</script>
