<?php
    if(!empty($items)) {
        foreach($items as $val) {
            $title = $val['title'];
            $email = $val['email'];
            $full_name = $val['first_name'] . ' ' . $val['last_name'];
            if($val['status'] == 1)
                $status = 'Đã gửi';
            else
                $status = 'Chưa gửi';

            $time = $val['time_format'];
?>
            <tr>
                <td><?php echo $title; ?></td>
                <td class="center"><?php echo $time; ?></td>
                <td class="center"><?php echo $email; ?></td>
                <td class="center"><?php echo $full_name; ?></td>
                <td class="center"><?php echo $status; ?></td>
                <td class="center"><a href="javascript:;" onclick="view_mail(<?php echo $val['id']; ?>)">Xem</a></td>
            </tr>

<?php
        }
    }else {
?>
        <tr style="cursor: pointer;"><td colspan="6"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
    }
?>