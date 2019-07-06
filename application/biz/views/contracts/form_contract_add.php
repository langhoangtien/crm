<?php $this->load->view("partial/header");?>
<link href="<?php echo base_url();?>assets/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/n9-modal.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/autoNumeric.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-n9-gid.js" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/contract.js" ></script>
<?php 

if($page > 1){
    $linkRedirect = $linkRedirect . '/' . $page;
}

$array_session = array('sales_model_filter', 'receiving_model_filter', 'contract_payment_filter', 'contract_delivery_filter', 'contract_payment_detail_filter');
foreach($array_session as $session) {
    if(isset($_SESSION[$session]))
        unset($_SESSION[$session]);
}
?>
    <div class="row manage-table type-2">
        <?php echo form_open('', array('id' => 'contract_form','class'=>'form-horizontal two-cols', 'enctype'=>"multipart/form-data")); ?>
        <div class="col-md-12">
            <input type="hidden" name="id" id="contract_id" value="<?php echo $id; ?>" />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input id="vivu" type="hidden" name="sale_id" value="<?php echo $sale_id;?>" />
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="icon-edit"></i>
                        Thông tin hợp đồng
                    </h3>
                </div>

                <div class="panel-body">
                    <div class="row" id="contract_section">
                    </div>
                </div>
            </div>
        </div>
<?php if($option == 'customer') : ?>
        <div class="col-md-12" id="customer_section" style="display: none;">
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
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Họ và tên :</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="customer_fullname" class="form-control" readonly="true">
                            </div>
                        </div>
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Địa chỉ :</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="customer_address" class="form-control" readonly="true" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-left: 5px;">
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Công ty :</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="customer_company_name" class="form-control" readonly="true" />

                            </div>
                        </div>
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Số điện thoại:</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="customer_phone" class="form-control" readonly="true"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php endif; ?>

<?php if($option == 'supplier') : ?>
        <div class="col-md-12" id="supplier_section" style="display: none;">
        <div class="panel panel-piluku">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon-edit"></i>
                    Thông tin nhà cung cấp
                </h3>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6" style="padding-right: 5px;">
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Họ và tên :</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="supplier_fullname" class="form-control" readonly="true">
                            </div>
                        </div>
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Địa chỉ :</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="supplier_address" class="form-control" readonly="true" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-left: 5px;">
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Công ty :</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="supplier_company_name" class="form-control" readonly="true" />

                            </div>
                        </div>
                        <div class="form-group hang">
                            <label for="title" style="text-align: left;" class="wide col-sm-3 col-md-3 col-lg-3 control-label">Số điện thoại:</label>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" value="" id="supplier_phone" class="form-control" readonly="true"/>
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
            <div class="button-control">
                <input type="button" class="btn btn-primary btn-lg" onclick="back_list();" value="Quay lại"/>
                <input type="button" class="btn btn-primary btn-lg" onclick="save_contract('save');" value="Lưu"/>
                <input type="button" class="btn btn-primary btn-lg" onclick="save_contract('save-close');" value="Lưu đóng"/>
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
        var page           = <?php echo $page; ?>;
        var option         = '<?php echo $option; ?>';
        var list_type       = '<?php echo $list_type; ?>';
        var currency_symbol = '<?php echo $this->config->item('currency_symbol'); ?>';
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


