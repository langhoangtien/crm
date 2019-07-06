<table class="table" id="stock_in_selected">
    <thead>
      <tr>
        <th></th>
        <th><?php echo lang('common_name'); ?></th>
          <?php if($add){ ?>
        <th style="text-align: center;""><?php echo 'Đã nhập/Tổng số lượng'; ?></th>
          <?php } ?>
        <th class="text-center"><?php echo lang('common_quantity'); ?></th>
        <th class="text-center"><?php echo lang('common_measure'); ?></th>
      </tr>
    </thead>
    <tbody>
        <?php if (isset($items)) { ?>
    	    <?php foreach ($items as $item) { ?>
      <tr>
        <td style="text-align: center; cursor: pointer;">
        	<input type="hidden" name="item_id" value="<?php echo $item->item_id; ?>" />
        	<?php if (empty($mode) || $mode != 'by_sale') { ?>
        		<span class="glyphicon glyphicon-remove-sign icon-remove" aria-hidden="true"></span>
        	<?php } ?>
        </td>
        <td><?php echo $item->name; ?></td>
      <?php if($add){ ?>
            <td id="so-luong" class="text-center">
            <?php
                  echo round($item->qtyStockIn,2) . '/' . round($item->qtyOrigin,2);
            ?></td>
        <?php } ?>

        <td class="text-center">
            <div id="deo-hieu" style="display: none;"><?php echo (round($item->qtyOrigin,2)-round($item->qtyStockOut,2)); ?></div>
            <?php if (!empty($mode) && $mode == 'by_recv') { ?>
                <a href="#" class="so-luong-nhap-kho xeditable" data-type="text" data-value-origin="<?php echo round($item->qtyOrigin,2); ?>" data-number-type="unsigned" data-validate-number="true"  data-pk="1" data-name="quantity" data-url="<?php echo site_url('stock_in/edit_item/' . $item->item_id); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item->totalQty); ?></a>
            <?php } else {?>
            <a href="#" class="so-luong-nhap-kho xeditable" data-type="text" data-number-type="unsigned" data-validate-number="true"  data-pk="1" data-name="quantity" data-url="<?php echo site_url('stock_in/edit_item/' . $item->item_id); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item->totalQty); ?></a>
            <?php } ?>
        </td>

        <td class="text-center">
            <?php $measure = $this->Measure->getInfo($item->measure_id); ?>
            <a class="measure_item xeditable" data-type="select"  data-validate-number="true"  data-value="<?php if(isset($measure->id)) echo $measure->id; ?>" data-pk="2" data-source="<?php echo site_url("items/measures/" . $item->item_id);?>" data-name="measure" data-url="<?php echo site_url('stock_in/edit_item/' . $item->item_id); ?>" data-title="<?php echo lang('common_measure') ?>"><?php echo empty($measure) ? '' : $measure->name; ?></a>
        </td>
        </td>
      </tr>
      <?php } ?>
        <?php } else {?>
            <tr style="cursor: pointer;"><td colspan="5"><div class="col-log-12" style="text-align: center; color: darkred;">Bạn đã nhập đủ số lượng, quay lại đơn hàng tạm dừng để hoàn thành đơn <a href="<?php echo base_url().'receivings/unsuspend/'.$recv_id; ?>">click here</a></div></td></tr>
        <?php } ?>
    </tbody>
  </table>
 <script type="text/javascript" language="javascript">
 $(document).ready(function(){
		$('.xeditable').editable({
            ajaxOptions: {
                dataType: 'json'
            },
            value:0,
	    	validate: function(value) {

		    	if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
						return <?php echo json_encode(lang('common_only_numbers_allowed')); ?>;
	            } else if ($(this).data('number-type') == 'unsigned' && value < 0) {
	            	return <?php echo json_encode('Không được phép nhập số âm'); ?>;
	            }
                var tong_so_luong = Number($(this).prev().closest("div#deo-hieu").text());
                 <?php if (!empty($mode) && $mode == 'by_sale') { ?>
                     if($(this).data('name') == 'quantity' && value > tong_so_luong) {

                    return <?php echo json_encode('Số lượng không được vượt quá số lượng cần nhập, số lượng cần nhập ra là '); ?>+tong_so_luong;
                }
                <?php } ?>
                    
               
	        },
	    	success: function(response, newValue) {
                //Reset lại ô số lượng
                $(this).parent().prev().find('.so-luong-nhap-kho').text(response.tong_so_luong);
                // Thay đổi lại số lượng so sánh
                var vitri = $(this).parent().prev().find('.so-luong-nhap-kho');
                var vitri1 = $(this).parent().prev().find('#deo-hieu');
                var vitri2 = $(this).parent().parent().find('#so-luong');
                vitri.attr('data-value-origin',response.tong_so_luong);
                vitri1.text(response.tong_so_luong);
                vitri2.html(response.so_luong_quy_doi);

            }
	    });



		$('.measure_item .xeditable').editable({
	    	success: function(response, newValue) {
				 last_focused_id = $(this).attr('id');
				 $("#register_container").html(response);
			}
	    });

	    $('#stock_in_selected .icon-remove').unbind('click').bind('click', function(){

	    	var rowSelected = $(this).closest('tr');
	    	
	    	var _data = {};
			_data['item_id'] = $(rowSelected).find('input[name="item_id"]').val();
			coreAjax.call(
				'<?php echo site_url("stock_in/remove_item");?>',
				_data,
				function(response)
				{
					$(rowSelected).remove();
				}
			);
		});
	});
 </script>