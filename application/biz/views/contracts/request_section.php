<?php
$tab_arr = array(
    'sales'    => 'Yêu cầu sản phẩm',
    'delivery' => 'Yêu cầu giao hàng',
    'payment'  => 'Yêu cầu thanh lý / nghiệm thu',
    'other'    => 'Yêu cầu khác',
    'supplier' => 'Bên thứ ba'

);
if($action == 'add') {
    unset($tab_arr['delivery']);
    if($type == 'rule') {
        unset($tab_arr['sales']);
    }
}else {
    if($type == 'rule') {
        unset($tab_arr['sales']);
        unset($tab_arr['delivery']);
    }
}
?>
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php 
            reset($tab_arr);
            $first_key = key($tab_arr);
            foreach($tab_arr as $key => $tab_name) {
                ?>
                <span class="title<?php if($key == $first_key) echo ' active'; ?>" data-tab="contract_<?php echo $key; ?>"><?php echo $tab_name; ?></span>
            <?php
            }
            ?>
            <i class="fa fa-spinner fa-spin loading" id="contract_payment_loading" style="display: none;"></i>
            <i class="fa fa-spinner fa-spin loading" id="contract_delivery_loading" style="display: none;"></i>
        </h3>
    </div>
<?php
foreach($tab_arr as $key => $tab_name) {
    if($first_key == $key)
        $display_css = ' style="display: block"';
    else
        $display_css = ' style="display: none"';

    ?>

    <?php $this->load->view("contracts/partial/contract_$key", array('display_css'=>$display_css));?>
<?php
// echo "$key";
}
?>