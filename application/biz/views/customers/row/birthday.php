<?php
if(!empty($items)) {
    foreach($items as $val) {
        $code               = $val['code'];
        $id                 = $val['id'];
        $fullname           = $val['first_name'] . ' ' . $val['last_name'];
        $phone_number       = $val['phone_number'];
        $birthday           = $val['birth_date_format'];
        $email              = $val['email'];
        $person_id          = $val['person_id'];
        if(!empty($val['image_id']))
            $link_image = base_url() . 'app_files/view/' . $val['image_id'];
        else
            $link_image = base_url() . 'assets/assets/images/avatar-default.jpg';
?>
        <tr>
            <td class="cb"><input type="checkbox" value="<?php echo $person_id; ?>" class="file_checkbox"><label><span></span></label></td>
            <td class="text-left"><?php echo $code; ?></td>
            <td class="text-left"><a href="<?php echo base_url(); ?>reports/specific_cus?cus_id=<?php echo $person_id; ?>" target="_blank" class="underline"><?php echo $fullname; ?></a></td>
            <td class="text-left"><a href="mailto:<?php echo $email; ?>" class="underline"><?php echo $email; ?></a></td>
            <td class="text-left"><?php echo $phone_number; ?></td>
            <td class="text-left"><?php echo $birthday; ?></td>
           <!--  <td class="text-center"><a href="<?php echo base_url(); ?>/customers/pay_now/<?php echo $person_id; ?>" title="Thanh toán" class="btn btn-primary">Thanh toán</a></td> -->
            <td class="text-left"><a href="<?php echo base_url(); ?>customers/view/<?php echo $person_id; ?>/2" class="update-person" title="Cập nhật"><?php echo lang('common_edit'); ?></a></td>
            <td class="text-left"><a href="<?php echo $link_image; ?>" class="rollover"><img src="<?php echo $link_image; ?>" alt="<?php echo $fullname; ?>" class="img-polaroid avatar" width="45"></a></td>
        </tr>

<?php
    }
}else {
?>
    <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
<?php
}
?>
