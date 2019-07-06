<?php
$thousands_separator = $this->config->item('thousands_separator');
$decimal_point       = $this->config->item('decimal_point');
$number_of_decimals  = $this->config->item('number_of_decimals');

$date                = $information['date'];
$contract_payment_id = $information['contract_payment_id'];
$company_name        = $information['company_name'];
$address             = $information['address'];

$btn_submit = '<a href="javascript:;" onclick="save_contract_delivery();" class="btn btn-primary">Lưu</a>';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Cập nhật yêu cầu giao hàng</h4>
        </div>
        <div class="modal-body" style="padding-bottom: 5px;">
            <form method="POST" action="" name="contract_delivery_form" id="contract_delivery_form" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="sale_id" value="<?php echo $sale_id; ?>"/>
                <input type="hidden" name="contract_delivery_id" value="<?php echo $delivery_id; ?>"/>
                <div class="clearfix hang" id="delivery_info">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-md-2 control-label left">Giai đoạn</label>
                                <div class="col-md-10">
                                    <select name="contract_payment_id" class="form-control">
                                <?php
                                foreach($slb_contract_payment as $key => $val) {
                                ?>
                                    <option value="<?php echo $key; ?>"<?php if($key == $contract_payment_id) echo ' selected'; ?>><?php echo $val; ?></option>
                                 <?php
                                }
                                ?>

                                    </select>

                                    <span for="contract_payment_id" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label left">Thời gian</label>
                                <div class="col-md-10">
                                    <input type="text" name="date" id="contract_delivery_date" value="<?php echo $date; ?>" class="form-control">
                                    <span for="date" class="text-danger errors"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label left">Công ty</label>
                                <div class="col-md-10">
                                    <input type="text" name="company_name" value="<?php echo $company_name; ?>" class="form-control">
                                    <span for="company_name" class="text-danger errors"></span>
                                </div>
                            </div>

                            <div class="form-group end">
                                <label class="col-md-2 control-label left">Địa chỉ
                                </label>
                                <div class="col-md-10">
                                    <input type="text" name="address" value="<?php echo $address; ?>" class="form-control">
                                    <span for="address" class="text-danger errors"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php
    if($delivery_id == -1) {
?>
        <div class="clearfix hang">
            <div class="row">
                <div class="col-lg-12">
                    <table class="tablesorter table table-hover data-n9-table" id="tbl_delivery_items">
                        <thead>
                        <tr>
                            <th>Tên</th>
                            <th style="width: 15%;">Đơn giá</th>
                            <th style="width: 20%;">Còn lại/Tổng SL</th>
                            <th style="width: 15%;">Số lượng</th>
                            <th style="width: 100px;">Đơn vị</th>
                            <th style="width: 100px;">Control</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!empty($items)) {
                            foreach($items as $key => $item) {
                                if(isset($item['item_id'])){
                                    $link_detail = base_url() . 'home/view_item_modal/' . $item['item_id'];
                                }elseif(isset($item['item_kit_id']))
                                    $link_detail = base_url() . 'home/view_item_kit_modal/' . $item['item_kit_id'];

                                $name       = $item['name'];
                                $measure_id = $item['measure_id'];
                                $price      = to_currency($item['price']);
                                $limit      = number_format($item['limit'], 2);

                                $dg       = number_format($item['dg'], 2);
                                $quantity = number_format($item['quantity'], 2);
                                $measure  = $item['measure'];

                                $link_url = base_url() . 'ajax/update_delivery_quantity';
                                ?>
                                <tr style="cursor: pointer;">
                                    <td><a class="" href="<?php echo $link_detail; ?>" data-toggle="modal" data-target="#myModal"><?php echo $name; ?></a></td>
                                    <td class="right cb"><?php echo $price; ?></td>
                                    <td class="center cb"><span id="delivered_<?php echo $key; ?>" class="delivered_info"><?php echo number_format($item['limit'], 2); ?>/<?php echo $quantity; ?></span></td>
                                    <td class="center">
                                        <a href="javascript:;" class="x_delivery" data-limit="<?php echo $limit; ?>" data-type="text" data-value="1" data-validate-number="true" data-pk="<?php echo $key; ?>" data-source="" data-url="<?php echo $link_url; ?>" data-title="Số lượng">1</a>
                                        <input type="hidden" name="quantity[<?php echo $key; ?>]" id="delivery_quantity_<?php echo $key; ?>" value="1" />
                                    </td>
                                    <td class="center cb">
                                        <?php echo $measure; ?>
                                        <input type="hidden" name="measure_id[<?php echo $key; ?>]" value="<?php echo $measure_id; ?>" />
                                    </td>
                                    <td class="center">
                                        <a href="javascript:;" onclick="delete_row(this);">Xóa</a>
                                    </td>
                                </tr>
                            <?php
                            }
                        }else {
                            ?>
                            <tr style="cursor: pointer;"><td colspan="7"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<?php
    }
?>

            </form>

        </div>
        <div class="modal-footer" style="padding-top: 0;">
            <?php echo $btn_submit; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    function delete_row(obj){
        $(obj).closest('tr').remove();
    }
    $( document ).ready(function() {
        $('#tbl_delivery_items .check_tatca').trigger('click');
        date_time_picker_field($('#contract_delivery_date'), JS_DATE_FORMAT);
        $('.price').autoNumeric('init', { mDec: <?php echo $number_of_decimals; ?>, aDec: '<?php echo $decimal_point; ?>', aSep: '<?php echo $thousands_separator; ?>'});

        $('.x_delivery').editable({
            validate: function(value) {
                if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
                    return 'Phải nhấp số';
                }else if ($(this).data('number-type') == 'unsigned' && value < 0) {
                    return 'Không được nhấp số âm';
                }else {
                    var limit = $(this).attr('data-limit');
                    if(value > limit)
                        return 'Số lượng không được vượt quá '+limit;
                }
            },
            success: function(response, newValue) {
                var res = $.parseJSON(response);
                $('#delivery_quantity_'+res.pk).val(res.value);
            }
        });
    });
</script>