<?php $this->load->view("partial/header");?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/autoNumeric.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-n9-gid.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/contract.js" ></script>
<?php
    $array_session = array('sales_model_filter', 'receiving_model_filter', 'contract_payment_filter', 'contract_delivery_filter', 'contract_payment_detail_filter');
    foreach($array_session as $session) {
        if(isset($_SESSION[$session]))
            unset($_SESSION[$session]);
    }
    $type = $item['type'];
?>
<div class="row manage-table type-2">
    <?php echo form_open('', array('id' => 'contract_form','class'=>'form-horizontal two-cols', 'enctype'=>"multipart/form-data")); ?>
    <div class="col-md-12">
        <input type="hidden" name="id" id="contract_id" value="<?php echo $id; ?>" />
        <input type="hidden" name="option" value="<?php echo $option; ?>" />
        <input type="hidden" name="type" value="<?php echo $type; ?>" />
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon-edit"></i>
                    Thông tin hợp đồng
                </h3>
            </div>

            <div class="panel-body">
                <div class="row" id="contract_section">
                    <?php $this->load->view('contracts/view/' . $type . '_section');?>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($customer_info)): ?>
    <?php
        $customer_fullname     = $customer_info['first_name'] . ' ' . $customer_info['last_name'];
        $customer_company_name = $customer_info['company_name'];
        $customer_address      = $customer_info['address_1'];
        if (empty($customer_address)) {
            $customer_address = $customer_info['address_2'];
        }
        $customer_phone_number = $customer_info['phone_number'];
    ?>
    <div class="col-md-12" id="customer_section">
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon-edit"></i>
                    Thông tin khách hàng
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6" style="padding-right: 5px;">
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Họ và tên: </label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <p><?php echo $customer_fullname; ?></p>
                            </div>
                        </div>
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Địa chỉ: </label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <p><?php echo $customer_address; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-left: 5px;">
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Công ty: </label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <p><?php echo $customer_company_name; ?></p>
                            </div>
                        </div>
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3">Số điện thoại: </label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <p><?php echo $customer_phone_number; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div id="request_section" class="col-md-12">
        <div class="panel panel-piluku">
        </div>
        <div class="pagination hidden-print alternate text-center data-n9-pagination" data-table="contract_payment" style="display: none;"></div>
        <div class="pagination hidden-print alternate text-center data-n9-pagination" data-table="contract_delivery" style="display: none;"></div>
        <div class="button-control">
            <a class="btn btn-primary btn-lg" onclick="back_list(); return false;">Quay lại</a>
        </div>
    </div>
    <?php echo form_close();?>
</div>
<div class="modal fade box-modal" id="quick_modal">
</div>
<div id="my_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
</div>
<div id="my_table" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
</div>
<div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
<script type="text/javascript">
    var action         = '<?php echo $action; ?>';
    var sale_prefix    = '<?php echo $this->config->item('sale_prefix'); ?>';
    var receive_prefix = '<?php echo $this->config->item('receive_prefix'); ?>';
    var last_id        = '<?php echo $last_id; ?>';
    var page           =  <?php echo $page; ?>;
    var option         = '<?php echo $option; ?>';
    var list_type       = '<?php echo $list_type; ?>';
    var currency_symbol = '<?php echo $this->config->item('currency_symbol'); ?>';
    window.setTimeout(function() {
        $("#request_section").find("button,input,textarea").attr('disabled', 'disabled');
        $("#request_section").find("a.text-danger").hide();
        $("#request_section").find(".editable, a[href='javascript:;']").each(function() {
            $(this).replaceWith("<span>"+$(this).text()+"</span>");
        });
    }, 1500);
</script>
<style type="text/css">
    .data-n9-table th {
        text-align: center;
    }

    .data-n9-table th[data-field] {
        cursor: pointer;
    }

    .data-n9-table td.center {
        text-align: center;
    }

    .data-n9-table td.right {
        text-align: right;
    }

    .data-n9-table td.bold {
        font-weight: bold;
    }

    .panel-piluku > .panel-heading h3 {
        position: relative;
    }

    .loading {
        position: absolute;
        bottom: -91px;
        left: -9px;
    }
</style>
<?php
if(isset($_SESSION['notice'])) {
    $notice = $_SESSION['notice'];
    unset($_SESSION['notice']);
    ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            toastr.success('<?php echo $notice; ?>', 'Thông báo');
        });
    </script>
<?php
}
?>
<?php $this->load->view("partial/footer");?>