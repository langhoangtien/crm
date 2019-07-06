<?php
class BizStockOut extends CI_Model
{
	function getStockOutItems($sale_id)
	{

		$this->db->from('stock_out');
		$this->db->join('stock_out_items', 'stock_out_items.stock_out_id = stock_out.id');
		$this->db->where('stock_out.sale_id',$sale_id);

		$result = $this->db->get()->result_array();

		$result_final = $this->tinh_ra_so_luong_da_xuat($result);
		return $result_final;
	}


	# Số lượng đã xuất kho được quy đổi về đơn vị gốc

	/**
	 * @param array $data
	 * @return array
     */

	public function tinh_ra_so_luong_da_xuat($data = array()){

		foreach($data as $row)
		{
			$data[$row['item_id']]['thay_doi'] = false;
			static $kiem_tra_don_vi_tinh;
			if(!empty($kiem_tra_don_vi_tinh)) $kiem_tra_don_vi_tinh = $row['measure_id'];
			$itemInfo = $this->Item->get_info($row['item_id']);
			$don_vi_tinh_goc = $itemInfo->measure_id;
			$so_luong_da_quy_doi = $this->doi_don_vi_tinh($row['item_id'],$row['measure_id'],$row['qty'],$don_vi_tinh_goc);

			if($kiem_tra_don_vi_tinh != $row['measure_id']) $data[$row['item_id']]['thay_doi'] = true;
			$data[$row['item_id']]['qty'] += $so_luong_da_quy_doi;
		}

		return $data;
	}

	public function doi_don_vi_tinh($itemId = 0, $don_vi_hien_tai = 0,$so_luong = 0,$don_vi_can_quy_doi = null){

        $this->load->model('ItemMeasures');
        if($don_vi_can_quy_doi == null) $don_vi_can_quy_doi = -1;

        $don_vi_quy_doi = $this->ItemMeasures->lay_ra_don_vi_quy_doi($itemId, $don_vi_hien_tai,$don_vi_can_quy_doi);
   
    	if($don_vi_quy_doi == -1|| $don_vi_can_quy_doi == $don_vi_hien_tai && $don_vi_quy_doi[0]['measure_id'] == $don_vi_hien_tai) {
    		$so_luong_duoc_thay_doi = $so_luong;
    	} 
    	elseif($don_vi_quy_doi[0]['measure_id'] == $don_vi_can_quy_doi && $don_vi_can_quy_doi != $don_vi_hien_tai) {
        	$so_luong_duoc_thay_doi = $so_luong*$don_vi_quy_doi[0]['qty_converted'];


		}
        elseif($don_vi_quy_doi[0]['measure_converted_id'] == $don_vi_can_quy_doi && $don_vi_can_quy_doi != $don_vi_hien_tai) {
        	$so_luong_duoc_thay_doi = $so_luong/$don_vi_quy_doi[0]['qty_converted'];
		}

        return round($so_luong_duoc_thay_doi,3);
    }
	
	public function getInfoBySaleId($saleID = 0)
	{
		$this->db->from('stock_out');
		$this->db->where('sale_id', $saleID);
		return $this->db->get()->row();
	}
	
	public function getInfo($id = 0) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->from('stock_out');
		$this->db->where('id', $id);
		$this->db->where('location_id', $location_id);
		
		$stockOutInfo = $this->db->get()->row();
		
		$this->db->select('items.*, stock_out_items.qty as stockOut_totalQty, stock_out_items.measure_id as stockOut_measureId');
		$this->db->from('items');
		$this->db->join('stock_out_items', 'stock_out_items.item_id = items.item_id');
		$this->db->where('stock_out_id', $stockOutInfo->id);
		$stockOutInfo->items = $this->db->get()->result();
		
		return $stockOutInfo;
	}
	
	public function getHistory($search = []) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->select('stock_out.*, CONCAT(employee.first_name, " ", employee.last_name) as employee , CONCAT(customer.first_name, " ", customer.last_name) as customer');
		$this->db->from('stock_out');
		$this->db->join('people as employee', 'stock_out.deliverer_id = employee.person_id', 'left');
		$this->db->join('people as customer', 'stock_out.customer_id = customer.person_id', 'left');
		$this->db->where('stock_out.location_id', $location_id);
		
		if (!empty($search['start_date'])) {
			$this->db->where('created_time >= ', $search['start_date']);
		}
	
		if (!empty($search['end_date'])) {
			$this->db->where('created_time <= ', $search['end_date'] . ' 23:59:59');
		}
	
		$this->db->order_by('stock_out.id');
		
		$history = $this->db->get()->result_array();
	
		for($k=0;$k<count($history);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('stock_out_items', 'stock_out_items.item_id = items.item_id');
			$this->db->where('stock_out_id', $history[$k]['id']);
	
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
	
			$history[$k]['items'] = implode(', ', $item_names);
		}
	
		return $history;
	}
	
	function existsBySaleId($sale_id)
	{
		$this->db->from('stock_out');
		$this->db->where('sale_id',$sale_id);
		$query = $this->db->get();
	
		return ($query->num_rows()==1);
	}

	/**
	 * @param array $stockOutData
	 * @return int
     */
	public function save($stockOutData = []) {
		$stockOutData['sale_id'] = !empty($stockOutData['sale_id']) ? $stockOutData['sale_id'] : 0;
		$stockOutMode = $this->mysession->getValue('stockOutMode');
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$stockOutRecord = [
			'customer_id' => empty($stockOutData['customer']) ? 0 :  $stockOutData['customer'],
			'deliverer_id' => empty($stockOutData['deliverer']) ? 0 : $stockOutData['deliverer'],
			'location_id' => $location_id,
			'comment	' => empty($stockOutData['comment']) ? '' : $stockOutData['comment'],
			'created_time' => date('Y-m-d H:i:s'),
		];
		

		$isFinishStockOut = 0;

		if (!empty($stockOutData['sale_id']))
		{
			$stockInfo = $this->getInfoBySaleId($stockOutData['sale_id']);
			$stockOutRecord['sale_id'] = $stockOutData['sale_id'];
			$saleItems = $this->Sale->get_sale_items($stockOutData['sale_id'])->result_array();
			# Nếu đã xuất kho rồi
			if(!empty($stockInfo)){

				$stockOutItems = $this->getStockOutItems($stockOutData['sale_id']);
				// Validate sale

				foreach ($saleItems as $saleItem) {
					if (!empty($stockOutData['items'][$saleItem['item_id']])) {
						$item_id = $saleItem['item_id'];
						$isFinishStockOut = 2; # đang trong tiến trình xuất kho
						$don_vi_tinh_goc = $this->Item->lay_don_vi_goc($saleItem['item_id']); # đơn vị gốc
						# Lấy ra số lượng tiếp tục xuất kho và đơn vị tính mới
						$so_luong_xuat_kho = $stockOutData['items'][$saleItem['item_id']]->totalQty;
						$don_vi_item_xuat_kho = $stockOutData['items'][$saleItem['item_id']]->measure_id;
						$so_luong_xuat_kho_quy_doi = $so_luong_xuat_kho;

						# Lấy ra số lượng đã xuất kho và đơn vị tính
						$so_luong_da_xuat_kho = !empty($stockOutItems[$saleItem['item_id']]['qty']) ? $stockOutItems[$saleItem['item_id']]['qty'] : 0;
					
						# Thực hiện đổi về đơn vị tính gốc
						$this->load->model('ItemMeasures');

						if($don_vi_item_xuat_kho != '-1' && $don_vi_item_xuat_kho != null) {

							$so_luong_xuat_kho_quy_doi = $this->ItemMeasures->quy_doi_ra_don_vi_cua_san_pham($item_id,$so_luong_xuat_kho,$don_vi_item_xuat_kho,$don_vi_tinh_goc);
						}

						if (round($saleItem['quantity_purchased'],3) == ($so_luong_da_xuat_kho + $so_luong_xuat_kho_quy_doi)) {
							# Đã hoàn thành
							# Đã hoàn thành
							# Đã hoàn thành
							$isFinishStockOut = 1; # Đã hoàn thành
							# Đã hoàn thành
							# Đã hoàn thành
							# Đã hoàn thành
						} elseif((round($saleItem['quantity_purchased'],3) < ($so_luong_da_xuat_kho + $so_luong_xuat_kho_quy_doi))) {

							$error = "Bạn đã chọn quá số lượng cần xuất, tổng số lượng đã chọn là " . ($so_luong_xuat_kho_quy_doi) . ",vui lòng kiểm tra lại đơn vị tính";
							echo json_encode(['success' => false, 'message' => $error]);
							return;
						}

					} else {
						# Chưa hoàn thành
						# Chưa hoàn thành
						# Chưa hoàn thành
						# Chưa hoàn thành
						$isFinishStockOut = 0;
					}
				}

				# Nếu chưa có data xuất kho
				# Nếu chưa có data xuất kho
				# Nếu chưa có data xuất kho
				# Nếu chưa có data xuất kho
			} else {

				# Thực hiện đổi về đơn vị tính gốc
				$this->load->model('ItemMeasures');
				// Validate sale
				foreach ($saleItems as $saleItem) {
					$item_id = $saleItem['item_id'];
					$don_vi_tinh_goc = $this->Item->lay_don_vi_goc($saleItem['item_id']); # đơn vị gốc

					# Lấy ra số lượng tiếp tục xuất kho và đơn vị tính mới
					$so_luong_xuat_kho = $stockOutData['items'][$saleItem['item_id']]->totalQty;
					$don_vi_item_xuat_kho = $stockOutData['items'][$saleItem['item_id']]->measure_id;
					if($don_vi_item_xuat_kho != '-1' && $don_vi_item_xuat_kho != NULL) {

						$so_luong_xuat_kho = $this->ItemMeasures->quy_doi_ra_don_vi_cua_san_pham($item_id,$so_luong_xuat_kho,$don_vi_item_xuat_kho,$don_vi_tinh_goc);
					}

					if (round($saleItem['quantity_purchased'],3) == ($so_luong_xuat_kho)) {
								# Đã hoàn thành
								$isFinishStockOut = 1; # Đã hoàn thành
								# Đã hoàn thành

					} elseif((round($saleItem['quantity_purchased'],3) < ($so_luong_xuat_kho))) {
						$error = "Bạn đã chọn quá số lượng cần xuất, tổng số lượng đã chọn là " . ($so_luong_xuat_kho) . ",vui lòng kiểm tra lại đơn vị tính";
						echo json_encode(['success' => false, 'message' => $error]);
						return;
					} else {
						$isFinishStockOut = 2; # đang trong tiến trình
					}
				}
			}
		}
		// TODO

		$this->db->insert('stock_out',$stockOutRecord);
		$stockId = $this->db->insert_id();


		foreach ($stockOutData['items'] as $item) {
	
			if(!empty($stockOutData['items'][$item->item_id])){
				$don_vi_tinh = $stockOutData['items'][$item->item_id]->measure_id;
			} $don_vi_tinh = $item->measure_id;

			$stockItemsData = [
					'stock_out_id' => $stockId,
					'item_id' => empty($item->item_id) ? 0 : $item->item_id,
					'item_kit_id' => empty($item->item_kit_id) ? 0 : $item->item_kit_id,
					'qty' => round($item->totalQty,3),
					'measure_id' => $don_vi_tinh,
			];
			
			$this->db->insert('stock_out_items',$stockItemsData);
			

			$stock_recorder_check=false;
			$out_of_stock_check=false;


			if (!empty($item->item_id)) {
			
				$cur_item_info = $this->Item->get_info($item->item_id);
				$cur_item_location_info = $this->Item_location->get_info($item->item_id);
					
				//checks if the quantity is greater than reorder level
				if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $item->reorder_level)
				{
					$stock_recorder_check=true;
				}
					
				//checks if the quantity is greater than 0
				if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
				{
					$out_of_stock_check=true;
				}
 
				if (!$cur_item_info->is_service)
				{
					$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
					$itemInfo = $this->Item->get_info($item->item_id);
					$don_vi_tinh_goc = $itemInfo->measure_id;
					
					$so_luong_can_quy_doi = $item->totalQty;
					$don_vi_tinh_cua_so_luong_can_xuat = $item->measure_id;
					$so_luong_da_quy_doi = $this->ItemMeasures->quy_doi_ra_don_vi_cua_san_pham($item->item_id,$so_luong_can_quy_doi,$don_vi_tinh_cua_so_luong_can_xuat,$don_vi_tinh_goc);

					$cur_item_location_info = $this->Item_location->get_info($item->item_id);
					$so_luong_thay_doi = $cur_item_location_info->quantity - $so_luong_da_quy_doi;
					
					$this->Item_location->update_chuyen_kho_noi_bo($so_luong_thay_doi, $item->item_id);

					# Lưu thông tin xuất kho vào inventory
					# abc
					
					$sale_prefix = $this->config->item('sale_prefix');
					if($stockOutMode=="free_style")
					{
						$comment ='Xuất kho trực tiếp cho khách hàng';
					}
					else if($stockOutMode=="by_sale")
					{
						$comment = "Xuất kho theo đơn hàng cho khách hàng"."_". $sale_prefix.' '.$stockOutData['sale_id'];
					}

					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>empty($item->item_id) ? 0 : $item->item_id,
						'trans_user'=>empty($stockOutData['deliverer']) ? 0 : $stockOutData['deliverer'],
						// 'trans_comment'=>'Xuất kho trực tiếp cho khách hàng....hello',
						'trans_comment' => $comment,
						'trans_inventory'=>-round($so_luong_da_quy_doi,3),
						'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
					);
					
					$this->Inventory->insert($inv_data);
				}


			} elseif (!empty($item->item_kit_id)) {
				$cur_item_kit_info = $this->Item_kit->get_info($item->item_kit_id);
				$cur_item_kit_location_info = $this->Item_kit_location->get_info($item->item_kit_id);
					
				foreach($cur_item_kit_info as $item_kit_item)
				{
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);
					$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
					$this->Item_location->save_quantity($cur_item_location_info->quantity - ((int) $item->totalQty * $item_kit_item->quantity), $item_kit_item->item_id);
				}
			}
		}
		
		// $isFinishStockOut = 2 - stocking out ...

		if ($stockOutMode == 'by_sale') {
			$this->Sale->StockOut($stockOutData['sale_id'], $isFinishStockOut);
		}
		return $stockId;
	}
}