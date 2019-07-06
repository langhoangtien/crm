<?php
	if(!empty($items)) {
		$stt=1;
    foreach($items as $val) {
			$item_id				    = $val['item_id'];
			$product_id                 = $val['product_id'];
			$item_number           	 = $val['item_number'];
			$name       			 = $val['ten_san_pham'];
			$category                = $val['category'];
			$size          		     = $val['size'];
			$cost_price              = $val['gia_von'];
			$unit_price              = $val['gia_ban'];
			$items_quantity          = $val['so_luong_item'];
			$items_total_quantity    = $val['total_quantity'];
			$reorder_level			 = $val['location_reorder_level'];
			$xoay_kho				 = $val['xoay_kho'];
			$stop_producing			= $val['stop_producing'];
			if(!empty($val['image_id']))
			$link_image = base_url() . 'app_files/view/' . $val['image_id'];
			else
			$link_image = base_url() . 'assets/assets/images/items-default.jpg';
			
			if ($items_quantity !== NULL && ($items_quantity<=0 || $items_quantity<=$reorder_level)) 
					$low_inventory_class = "text-danger low-inventory";
			else 
				$low_inventory_class = "";
		?>
		<tr>
			<td class="cb"><input type="checkbox" id="item_<?php echo $item_id; ?>" value="<?php echo $item_id; ?>" class="file_checkbox"><label><span></span></label></td>
			<td class="text-left"><?php echo $stt; ?></td>
			<td class="text-left"><?php echo $item_number; ?></td>
			<td class="text-left">
				<a class=" <?php '.$low_inventory_class.' ?> item-name" href="#" data-item-url ="<?php echo base_url();?>/home/view_item_modal/<?php echo $item_id?>/2" ><?php echo $name ?></a>
			</td>
			<td class="text-left"><?php echo $category; ?></td>
			<td class="text-left"><?php echo $size; ?></td>
            <?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id)):?>
			<td class="text-left"><?php echo to_currency($cost_price); ?></td>
            <?php endif;?>
			<td class="text-left"><?php echo to_currency($unit_price); ?></td>
           
        <?php if ( $this->config->item('company_user') == 'remHaMy'):?>
            <td class="text-left" style = "<?php echo $xoay_kho?'color: green;':'';?>"><?php echo $xoay_kho?'Có xoay khổ':'Không xoay khổ'; ?></td>
			<td class="text-left" style = "<?php echo $stop_producing?'color: red;':'';?>"><?php echo $stop_producing? 'Dừng sản xuất':''; ?></td>
        <?php else:?>
            <td class="text-left"><?php echo round($items_quantity,3); ?></td>
			<td class="text-left not-selectable" onclick="ITEM_LIST.clickEventOnQtyCell(this)"><a><?php echo round($items_total_quantity,3); ?></a></td>
        <?php endif;?>
            

			<td class="text-left not-selectable""><a href="<?php echo base_url(); ?>/items/inventory/<?php echo $item_id; ?>/1" class="update-person" title="Tồn Kho">Hàng tồn kho</a></td>
			<td class="text-left not-selectable"><a href="<?php echo base_url(); ?>/items/clone_item/<?php echo $item_id; ?>/2" class="update-person" title="Clone">Clone</a></td>
			<td class="text-left not-selectable"><a href="<?php echo base_url(); ?>/items/view/<?php echo $item_id; ?>/2" class="update-person" title="Sửa">Sửa</a></td>
			<td class="text-left not-selectable"><a href="<?php echo $link_image; ?>" class="rollover"><img src="<?php echo $link_image; ?>" alt="<?php echo $name; ?>" class="img-polaroid avatar" width="45"></a></td>
		</tr>
		
		<?php
		$stt++;
		}
		}else {
	?>
	<tr style="cursor: pointer;"><td colspan="8"><div class="col-log-12" style="text-align: center; color: #efcb41;">Không có dữ liệu hiển thị</div></td></tr>
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
<script>
	$('.item-name').click( function(e){
		e.preventDefault();
		$('#itemsModal').remove();
        
		$.ajax({
			url: $(this).attr('data-item-url') ,
			type:"post",
			dataType:'json',
			success: function(response)
			{
				$('#itemsModal').remove();
				$('body').append(response);
				$('#itemsModal').modal('show');
			}
		}); 
	});
</script>