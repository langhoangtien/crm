<?php $this->load->view("partial/header"); ?>
<style>
    input, select, textarea {
        border: 1px solid #fff !important;
    }
    #customer_form * {
        font-size: 14px !important;
    }
</style>
<div class="row form-horizontal" id="customer_form">
    <?php echo validation_errors(); ?>
    <div class="panel panel-piluku">
        <div class="col-md-5">
            <div class="tab-content">
                <div class="panel-heading header-tab">
                    <h3 class="panel-title">
                        <i class="ion-edit"></i>
                        <?php echo lang("customers_basic_information"); ?>
                        <small>(<?php echo lang('common_fields_required_message'); ?>)</small>
                    </h3>
                </div>
                <?php if ($person_info->person_id): ?>
                <div class="panel">
                    <div class="panel-body">
                        <div class="user-badge">
                            <div class="user-badge-details">
                                <?php echo $person_info->first_name . ' ' . $person_info->last_name; ?>
                                <?php if($this->config->item('customers_store_accounts')): ?>
                                <div class="amount">
                                    <?php echo lang('customers_store_account_balance').': '; ?>
                                    <?php echo $person_info->balance ? to_currency($person_info->balance) : '0.00'; ?>
                                </div>
                                <?php endif ?>
                                <?php if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple'): ?>
                                <div class="amount">
                                <?php
                                    echo lang('common_sales_until_discount').': ';
                                    $sales_until_discount = $this->config->item('number_of_sales_for_discount') - $person_info->current_sales_for_discount;
                                    echo to_quantity($sales_until_discount);
                                ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced'): ?>
                                <?php list($spend_amount_for_points, $points_to_earn) = explode(":", $this->config->item('spend_to_point_ratio'),2); ?>
                                <div class="amount">
                                    <?php echo lang('common_points').': '; ?>
                                    <?php echo to_quantity($person_info->points); ?>
                                </div>
                                <div class="amount">
                                    <?php echo lang('customers_amount_to_spend_for_next_point').': '; ?>
                                    <?php echo to_currency($spend_amount_for_points - $person_info->current_spend_for_points); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif ?>
                <div class="panel-body">
                    <?php $this->load->view("people/form_basic_info"); ?>
                </div>
            </div>
        </div>
        <?php $this->load->view('people/form_tab_new_info') ?>
    </div>
</div>
<script type='text/javascript'>
    $("input, select, textarea").attr("disabled", "disabled");
    $("#customer_form .btn").hide();
</script>
<?php $this->load->view("partial/footer"); ?>
