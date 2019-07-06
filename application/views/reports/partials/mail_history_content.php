
<div class="modal-dialog" id="mail_history_frame">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">
                <?php echo $item[0]['mh_title']; ?>
<?php 
    if(!empty($item['file'])) {
        $link_file = base_url() . 'document/files/store_' . $item[0]['employee_id'] . '/' . $item[0]['file'];
?>
        <a href="<?php echo $link_file; ?>" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
<?php
    }
?>
            </h4>
						<h5>Người gửi: <?php echo $item[0]['send_person']?></h5>
						<h5>Người nhận: <?php echo $item[0]['receive_person'] .'('. $item[0]['mh_email'].')'?></h5>
						<h5>Thời gian: <?php echo $item[0]['mh_time']?></h5>
        </div>
        <div class="modal-body">
            <div style="overflow: auto">
                <?php echo $item[0]['mh_content']; ?>
            </div>

        </div>
    </div>
</div>
