<?php
    if(!empty($items)) {
        foreach($items as $val) {
            $full_name = $val['first_name'] . ' ' . $val['last_name'];
            $status = $val['_status'];
            $time   = $val['time_format'];
						$type   = $val['contract_suspended_type'];
						$code   = $val['contract_suspended_code'];
						$total  = $val['contract_suspended_total']; 
?>
            <tr>
                <td class="center"><?php echo  $time; ?></td>
                <td class="center"><?php echo $type; ?></td>
                <td class="center"><?php echo $code; ?></td>
                <td class="center"><?php echo $full_name; ?></td>
								<td class="center"><?php echo $total; ?></td>
								<td class="center"><?php echo $status; ?></td>
                <td class="center"><a href="javascript:;" onclick="view_mail(<?php echo $val['id']; ?>)">Xem</a></td>
								<td class="center"><a href="javascript:;" onclick="delete_mail(<?php echo $val['id']; ?>)">Xóa</a></td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="9"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>