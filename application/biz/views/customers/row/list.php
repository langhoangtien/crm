<?php
if(!empty($items)) { 
    // echo $this->db->last_query(); 
   // echo "<pre>"; print_r($items);die();
    $watcher_customer = array();
    foreach($items as $val) {
        $code               = $val['code'];
        $fullname           = $val['first_name'] . ' ' . $val['last_name'];
        $phone_number       = $val['phone_number'];
        $email              = $val['email'];
        $person_id          = $val['person_id'];
        $dia_chi            = $val['address_1'];
        $watcher_customer   = json_decode($val['watcher_manager']);
        // echo "<pre>"; print_r($watcher_customer); die();
        //get location
        $index = 0;
        $arrArea = [];
        $location = '';
        foreach ($getGeographical as $key){
            if ($key->customer_id == $person_id ){
                $arrArea[$index] = $key->geographical_area_id;
                $index++;
            }
        }
        for( $i =0; $i < $index; $i++){
            foreach ($geographical_area_data as $key){
                if($key->id == $arrArea[$i]){
                    $location .= $key->name . ' ';
                }
            }
        }
        //end get locations

        // $head_name          = substr($val['head_name'], 0, strlen($val['head_name']) - 2);
        $head_name          = ltrim($val['head_name'],",");
        if(!empty($val['image_id']))
            $link_image = base_url() . 'app_files/view/' . $val['image_id'];
        else
            $link_image = base_url() . 'assets/assets/images/avatar-default.jpg';
        ?>
        <tr>
            <td class="cb"><input type="checkbox" value="<?php echo $person_id; ?>" class="file_checkbox"><label><span></span></label></td>
            <td class="text-left"><?php echo $code; ?></td>
            <td class="text-left"><?php echo $location; ?></td>
            <!--            <td class="text-left"><a href="--><?php //echo $link_image; ?><!--" class="short-list-rollover"><img src="--><?php //echo $link_image; ?><!--" alt="--><?php //echo $fullname; ?><!--" class="img-polaroid avatar" width="45"></a></td>-->
            <td class="text-left" style="width: 20%;overflow:hidden;">
                <?php 
                if ($this->Employee->has_module_action_permission('customers','add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {
                    if ($this->Employee->get_logged_in_employee_info()->person_id == $val['person_id_create'] || $this->Employee->get_logged_in_employee_info()->group_id==1 || $this->Employee->get_logged_in_employee_info()->group_id==8) {
                        ?>
                        <a href="<?php echo base_url(); ?>customers/view/<?php echo $person_id; ?>/2"><?php echo $fullname ?></a>
                        <?php
                    }else{
                        ?>
                        <a href="<?php echo base_url(); ?>customers/view/<?php echo $person_id; ?>/3"><?php echo $fullname ?></a>
                        <?php
                    }
                }else{
                    ?>
                    <a href="<?php echo base_url(); ?>customers/view/<?php echo $person_id; ?>/3"><?php echo $fullname ?></a>
                    <?php
                }
                ?>
                
            </td>

            <td class="text-left"><?php echo (isset($val['authorized_capital']) && $val['authorized_capital']!='') ? number_format($val['authorized_capital'], 0, '', ',') : '0'; ?></td>
            <td class="text-left"><?php echo (isset($val['total_assets']) && $val['total_assets']!='') ? number_format($val['total_assets'], 0, '', ',') : '0'; ?></td>
            <td class="text-left"><?php echo (isset($val['total_revenue']) && $val['total_revenue']!='') ? number_format($val['total_revenue'], 0, '', ',') : '0'; ?></td>
            <td class="text-left"><?php echo (isset($val['total_profit']) && $val['total_profit']!='') ? number_format($val['total_profit'], 0, '', ',') : '0'; ?></td>
            <td class="text-left"><?php echo $head_name; ?></td>
            <td class="text-left"><a href="mailto:<?php echo $val['head_email']; ?>" class="underline"><?php echo $val['head_email']; ?></a></td>
            <td class="text-left"><?php echo $val['head_phone']; ?></td>
            <td class="text-left"><?php echo $val['created_time']; ?></td>
            <td class="text-left"><?php echo $val['created_by']; ?></td>
            <!-- <?php  
            if (in_array($this->Employee->get_logged_in_employee_info()->person_id,$watcher_customer)) {
                ?>
                <td class="text-left"><a href="<?php echo base_url(); ?>customers/view/<?php echo $person_id; ?>/3" class="update-person" title="Cập nhật"><?php echo lang('common_edit'); ?></a></td>
                <?php
            }else{
                if ($this->Employee->has_module_action_permission('customers','add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {
                  if ($this->Employee->get_logged_in_employee_info()->person_id == $val['person_id_create'] || $this->Employee->get_logged_in_employee_info()->group_id==1 || $this->Employee->get_logged_in_employee_info()->group_id==8) {
                    ?>
                    <td class="text-left"><a href="<?php echo base_url(); ?>customers/view/<?php echo $person_id; ?>/2" class="update-person" title="Cập nhật"><?php echo lang('common_edit'); ?></a></td>
                    <?php
                }
            }else{
              ?>
              <td class="text-left"></td>
              <?php
          }
      }
      ?> -->
      <td class="text-center"><a href="<?php echo base_url('reports/customer/'.$person_id) ?>">Xem</a></td>
  </tr>
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
