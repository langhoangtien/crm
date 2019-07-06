<?php $this->load->view("partial/header"); ?>
<?php
//echo count($location);die();
?>

<div class="container">

        <div class="row">
            <!-- <h3>KPI</h3> -->
            <div class="col-md-12">
                <div class="col-md-3">
                    <a href="<?php echo site_url('KpiPerson/index'); ?>" class="btn btn-block btn-info" id="button_kpi_persion">KPI Đánh giá cá nhân</a>
                </div><?php if($this->Employee->has_module_action_permission('kpi','view_kpi_room',$this->Employee->get_logged_in_employee_info()->person_id)){ ?>
                <div class="col-md-3">
                    <a href="./kpi/room" class="btn btn-block btn-info">KPI Đánh giá phòng</a>
                </div>
            <?php } ?>
            <?php if($this->Employee->has_module_action_permission('kpi','view_kpi_gobal',$this->Employee->get_logged_in_employee_info()->person_id)){ ?>
                <div class="col-md-3">
                    <a href="<?php echo site_url('kpi/total'); ?>" class="btn btn-block btn-info" id="button_kpi_persion">KPI Tổng quát</a>
                </div>
            <?php } ?>
                <div class="col-md-3">
                    <a href="./kpi/rate" class="btn btn-block btn-info">Tỷ lệ KPI</a>
                </div>
            </div>
        </div>
</div>
     
<?php $this->load->view("partial/footer"); ?>









