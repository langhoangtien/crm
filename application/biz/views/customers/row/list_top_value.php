<?php
if(!empty($items)) {
    
    $stt = 0;
    foreach($items as $val) {
        $stt++;
        $code               = $val['code'];
        $fullname           = $val['first_name'] . ' ' . $val['last_name'];
        $phone_number       = $val['phone_number'];
        $email              = $val['email'];
        $person_id          = $val['person_id'];
        $dia_chi            = $val['address_1'];
        $head_name          = $val['head_name'];
        $head_name          = substr($val['head_name'], 0, strlen($val['head_name']) - 2);
        if(!empty($val['image_id']))
            $link_image = base_url() . 'app_files/view/' . $val['image_id'];
        else
            $link_image = base_url() . 'assets/assets/images/avatar-default.jpg';
?>
        <?php if($page === 'topContractValue'){?>
        <tr>
            <td class="cb"><?php echo $stt;?></td>
            <td class="text-left">
                <form method="post" action="<?php echo base_url(); ?>reports/report_filter?action=specific_cus"" class="inline">
                <input type="hidden" name="report_type" value="simple">
                <input type="hidden" name="report_date_range_simple" value="all">
                <input type="hidden" name="customer_id" value="<?php echo $person_id; ?>">
                <input type="hidden" name="selected_location_ids[]" value="<?php echo $this->Employee->get_logged_in_employee_current_location_id()?>">
                <button type="submit" name="submit_param" class="link-button" style="text-align: left;">
                    <?php echo $fullname; ?>
                </button>
                </form>
            </td>
            <td class="text-left"><?php echo $val['code_don_hang']; ?></td>
            <td class="text-left"><?php echo $val['total_all']; ?></td>
        </tr>
        <?php }else{?>
            <tr>
                <td class="cb"><?php echo $stt;?></td>
                <td class="text-left">
                    <form method="post" action="<?php echo base_url(); ?>reports/report_filter?action=specific_cus"" class="inline">
                    <input type="hidden" name="report_type" value="simple">
                    <input type="hidden" name="report_date_range_simple" value="all">
                    <input type="hidden" name="customer_id" value="<?php echo $person_id; ?>">
                    <input type="hidden" name="selected_location_ids[]" value="<?php echo $this->Employee->get_logged_in_employee_current_location_id()?>">
                    <button type="submit" name="submit_param" class="link-button" style="text-align: left;">
                        <?php echo $fullname; ?>
                    </button>
                    </form>
                </td>
                <td class="text-left"><?php echo $val['num_of_contract']; ?></td>
                <td class="text-left"><?php echo $val['total_all']; ?></td>
            </tr>
        <?php }?>

<?php
    }
}else {
?>
     <tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;"><?php echo lang('common_no_data'); ?></div></td></tr>
<?php
}
?>
<style>
.inline {
  display: inline;
}
.link-button:hover
{
	color: #23527c;
}
.link-button {
  background: none;
  border: none;
  color: blue;
  text-decoration: none;
  cursor: pointer;
  font-family: Arial;
  font-size: 14px;
	letter-spacing: normal;
	color: #337ab7;
}
.link-button:focus {
  outline: none;
}
.link-button:active {
  color:red;
}
</style>
