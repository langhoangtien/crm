<?php $this->load->view("partial/header");?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-n9-autocomplete.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-n9-autocomplete.js" ></script>
<div class="register">
    <div class="register-right">
        <div class="customer-form supporter">
            <div class="input-group supporter">
                <span class="input-group-addon">
                    <a href="http://localhost.vn9/4biz2016/employees/view/-1" class="none" title="Khách hàng mới" id="new-customer" tabindex="-1"><i class="ion-person-add"></i></a>
                </span>
                <span class="ui-helper-hidden-accessible"></span>
                <input type="text" id="supporter" name="supporter" class="add-customer-input ui-autocomplete-input" placeholder="Nhân viên tư vấn" autocomplete="off" data-delete-url="<?php echo base_url() . 'ajax/delete_item'; ?>" data-url="<?php echo base_url().'ajax/ajax'; ?>"/>
            </div>
            <div class="n9-autocomplete-result-list" id="supporter_select_list">
                Nhân viên tư vấn:
            </div>
        </div>
    </div>
</div>

<div class="register">
    <div class="register-right">
        <div class="customer-form supporter">
            <div class="input-group supporter">
            <span class="input-group-addon">
                <a href="http://localhost.vn9/4biz2016/employees/view/-1" class="none" title="Khách hàng mới" id="new-customer" tabindex="-1"><i class="ion-person-add"></i></a>
            </span>
                <span class="ui-helper-hidden-accessible"></span>
                <input type="text" id="customer" name="customer" class="add-customer-input ui-autocomplete-input" placeholder="Khách hàng" autocomplete="off" data-delete-url="<?php echo base_url() . 'ajax/delete_item'; ?>" data-url="<?php echo base_url().'ajax/ajax'; ?>"/>
            </div>
            <div class="n9-autocomplete-result-list" id="customer_select_list">
                Khách hàng:
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $( document ).ready(function() {
        n9_autocomplete('supporter');
        n9_autocomplete('customer');
    });
</script>
<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all n9-autocomplete-result" id="result_supporter">
</ul>
<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all n9-autocomplete-result" id="result_customer">
</ul>
<?php $this->load->view("partial/footer");?>