<style type="text/css">
    #pdf_content {
        width: 700px;
        display: block;
        overflow: hidden;
        position: relative;
        padding: 20px;
        font-size: 12px;
    }
    #table-responsive{
        max-width: 700px;
    }
    #pdf_logo img {
        max-height: 70px;
    }
    #company_name {
        text-transform: uppercase;
        font-weight: bold;
        color: #002FC2
    }
    #pdf_content span {
        color: #002FC2;
    }
    #pdf_title {
        width: 100%;
        text-align: center;
        text-transform: uppercase;
        font-weight: bold;
        font-size: 16px;
        margin-top: 12px;
    }
    #pdf_tbl_items {
        border-collapse: collapse;
        font-size: 12px;
        margin: 10px 0;
    }
    #pdf_tbl_items tboby {
        display: table-row-group;
        vertical-align: middle;
        border-color: inherit;
    }
    #pdf_tbl_items tr {
        display: table-row;
        vertical-align: inherit;
        border-color: inherit;
    }

    #pdf_tbl_items th, #pdf_tbl_items td {
        border: 1px solid #000;
        padding: 3px;
    }

    #pdf_signature {
        min-height: 150px;
    }
    #pdf_signature div {
        text-align: center;
    }
    #pdf_signature lable {
        font-size: 14px;
        font-weight: bold;
    }

    .fl {
        float: left;
    }
    .fr {
        float: right;
    }
    .clb {
        clear: both;
    }
    .w50 {
        width: 50%;
    }

    .w20 {
        width: 20%;
    }

    .w100 {
        width: 100%;
    }
    .pb20 {
        padding-bottom: 20px;
    }

    .pt20 {
        padding-top: 20px;
    }

    #pdf_header h3, #pdf_header p {
        text-align: center;
    }
    #pdf_footer {
        text-align: center;
    }
    #pdf_content table td, #pdf_content table th {
        text-align: right;
        height: auto !important;
    }
    p {
        margin: 3px 0;
    }
    .w150px {
        width: 150px;
    }
    .fontI {
        font-style: italic;
    }
    .border-bottom{
        border-bottom: 1px dotted rgb(0, 0, 0) !important;
    }
    .border-left{
        border-left: none !important;
    }
    .border-right{
        border-right: none!important;
    }
    .border-top{
        border-top: none !important;
    }
    #policy{
        font-weight: bold;
        text-align: center;
        font-size: 1.3em;
        margin-top: 10px;
    }
    .text-center{
        direction: rtl !important;
        text-align: center !important;
    }
    .text-bold{
        font-weight: bold !important;
    }
    table th, table td{
        line-height: normal !important;
    }
        /* Medium Devices, Desktops */
    @media only screen and (max-width : 992px) {

    }

        /* Small Devices, Tablets */
    @media only screen and (max-width : 768px) {
        .table-responsive{
            max-width: 700px;
        }
    }
    @media only screen and (max-width : 767px) and (max-width: 481px) {
        .table-responsive{
            max-width: 700px;
        }
    }

        /* Extra Small Devices, Phones */
    @media only screen and (max-width : 480px) {
        .table-responsive{
            max-width: 300px;
        }
    }

        /* Custom, iPhone Retina */
    @media only screen and (max-width : 320px) {
        .table-responsive{
            max-width: 284px;
        }
    }
        /*@media screen and (min-device-width: 481px) and (max-device-width: 768px)*/

</style>
<?php
// $payment_total = $cart[0]['price'];
$supplier_name = $supplier;

if(!empty($supplier_address_1))
    $supplier_address = $supplier_address_1;
elseif(!empty($supplier_address_2))
    $supplier_address = $supplier_address_2;
else
    $supplier_address = '';
?>
<div id="pdf_content">
    <div id="pdf_header">
        <div>
            <div id="pdf_logo" class="fl">
                <?php if($this->config->item('company_logo')) {?>
                    <?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?>
                <?php } ?>
            </div>
            <div id="pdf_company">
                <p id="company_name"><?php echo $this->config->item('company'); ?></p>
                <p><span><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></span></p>
                <p>Điện Thoại: <span><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></span></p>
                <?php if($this->config->item('website')) { ?>
                    <p>Website: <span><?php echo $this->config->item('website'); ?></span></p>
                <?php } ?>
            </div>
        </div>
        <div class="clb">
            <div class="fr w150px">
                <p>Số: <?php echo $receiving_id; ?></p>

            </div>
        </div>
    </div>
    <div id="pdf_title" class="clb">
        <p>HÓA ĐƠN THANH TOÁN CÔNG NỢ</p>
    </div>

    <div id="pdf_customer">
        <p>Ngày: <span><?php echo date(get_date_format(), strtotime($transaction_time)); ?></span></p>

        <p>Nhà cung cấp:  <span><?php echo $supplier_name; ?></span> </p>
        <p>Địa chỉ:  <span><?php echo $supplier_address; ?></span> </p>
        <?php
        if($store_account_payment_value == 1)
            $label = 'Số tiền trả nhà cung cấp';
        else
            $label = 'Số tiền nhà cung cấp trả';

        ?>
        <p><?php echo $label;?><span>  <?php echo to_currency($payment_total); ?></span> </p>
        </p>
    </div>

    <div>
        <p>Số tiền viết bằng chữ: <span><?php echo getStringNumber((int)$payment_total)?></span></p>
    </div>
        <p>Tổng tiền còn nợ <span><?php echo to_currency($balance)?></span></p>
    <div class="clb">
        <div class="fr">
            <p>Ngày ..... tháng ..... năm .......</p>
        </div>
    </div>
    <div id="pdf_signature" class="w100 clb">
        <div class="w20 fl">
            <p><lable>Người lập phiếu</lable></p>
            <p class="fontI">(ký, họ tên)</p>
        </div>
        <div class="w20 fl">
            <p><lable>Người nhận hàng</lable></p>
            <p class="fontI">(ký, họ tên)</p>
        </div>
        <div class="w20 fl">
            <p><lable>Thủ kho</lable></p>
            <p class="fontI">(ký, họ tên)</p>
        </div>
        <div class="w20 fl">
            <p><lable>Kế toán trưởng</lable></p>
            <p class="fontI">(ký, họ tên)</p>
        </div>
        <div class="w20 fl">
            <p><lable>Giám đốc</lable></p>
            <p class="fontI">(ký, họ tên)</p>
        </div>
    </div>
    <div id="pdf_footer" class="w100 clb">
        <p class="fontI">(Cần kiểm tra đối chiếu khi lập, giao, nhận hàng hóa)</p>
    </div>
</div>