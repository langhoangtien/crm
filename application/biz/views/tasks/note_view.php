<?php 
	$note = $item['note'];
	if(!empty($item['reply'])) {
		$user_pheduyet_name = $item['user_pheduyet_name'];
		$reply			    = $item['reply'];
		if(!empty($note))
			$note = $note . '&#13;&#10;@'.$user_pheduyet_name . ' : ' . $reply;
		else 
			$note = '@'.$user_pheduyet_name . ' : ' . $reply;
	}
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Ghi chú</h4>
        </div>
        <div class="modal-body">
            <form method="POST" name="task_form" id="progress_form" class="form-horizontal">
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
                <div class="clearfix hang" style="margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="col-md-3 col-lg-2 control-label">Ghi chú</label>
                                <div class="col-md-9 col-lg-10">
                                    <textarea name="note" class="form-control" style="margin-bottom: 0;"><?php echo $note; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>