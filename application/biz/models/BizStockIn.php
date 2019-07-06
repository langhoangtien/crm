<?php
class BizStockIn extends CI_Model
{
	function getStockInItems($recv_id)
	{
		$this->db->from('stock_in');
		$this->db->join('stock_in_items', 'stock_in_items.stock_in_id = stock_in.id');
		$this->db->where('stock_in.recv_id',$recv_id);

		$result = $this->db->get()->result_array();

		$result_final = $this->tinh_ra_so_luong_da_nhap($result);
		return $result_final;
	}

	# Số lượng đã nhập kho được quy đổi về đơn vị gốc

	/**
	 * @param array $data
	 * @return array
	 */
	public function tinh_ra_so_luong_da_nhap($data = array()){

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
	
	public function getInfoByRecvId($recvId = 0)
	{
		$this->db->from('stock_in');
		$this->db->where('recv_id', $recvId);
		return $this->db->get()->row();
	}
	
	public function getInfo($id = 0) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->from('stock_in');
		$this->db->where('id', $id);
		$this->db->where('location_id', $location_id);
		
		$stockInInfo = $this->db->get()->row();
		
		$this->db->select('items.*, stock_in_items.qty as stockIn_totalQty, stock_in_items.measure_id as stockIn_measureId');
		$this->db->from('items');
		$this->db->join('stock_in_items', 'stock_in_items.item_id = items.item_id');
		$this->db->where('stock_in_id', $stockInInfo->id);
		$stockInInfo->items = $this->db->get()->result();
		
		return $stockInInfo;
	}
	
	public function getHistory($search = []) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->select('stock_in.*, CONCAT(employee.first_name, " ", employee.last_name) as employee , suppliers.company_name as supplier');
		$this->db->from('stock_in');
		$this->db->join('people as employee', 'stock_in.employee_id = employee.person_id', 'left');
		$this->db->join('suppliers', 'stock_in.supplier_id = suppliers.person_id', 'left');
		$this->db->where('stock_in.location_id', $location_id);
		
		if (!empty($search['start_date'])) {
			$this->db->where('created_time >= ', $search['start_date']);
		}
	
		if (!empty($search['end_date'])) {
			$this->db->where('created_time <= ', $search['end_date'] . ' 23:59:59');
		}
	
		$this->db->order_by('stock_in.id');
		
		$history = $this->db->get()->result_array();
	
		for($k=0;$k<count($history);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('stock_in_items', 'stock_in_items.item_id = items.item_id');
			$this->db->where('stock_in_id', $history[$k]['id']);
	
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
	
			$history[$k]['items'] = implode(', ', $item_names);
		}
	
		return $history;
	}
	function existsByRecvId($recv_id)
	{
		$this->db->from('stock_in');
		$this->db->where('recv_id',$recv_id);
		$query = $this->db->get();
	
		return ($query->num_rows()==1);
	}
	
	public function save($stockInData = []) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$stockInData['recv_id'] = !empty($stockInData['recv_id']) ? $stockInData['recv_id'] : 0;
		$stockInMode = $this->mysession->getValue('stockInMode');
		$stockInRecord = [
			'supplier_id' => empty($stockInData['supplier']) ? 0 :  $stockInData['supplier'],
			'employee_id' => empty($stockInData['employee']) ? 0 : $stockInData['employee'],
			'location_id' => $location_id,
			'comment	' => empty($stockInData['comment']) ? '' : $stockInData['comment'],
			'created_time' => date('Y-m-d H:i:s'),
		];


		$isFinishStockIn = 0;
		if (!empty($stockInData['recv_id'])) {
			# Lấy dữ liệu kho
			$stockInfo = $this->getInfoByRecvId($stockInData['recv_id']);
			$stockInRecord['recv_id'] = $stockInData['recv_id'];
			$recvItems = $this->Receiving->get_receiving_items($stockInData['recv_id'])->result_array();

			if (!empty($stockInfo)) {
				// Validate sale

				$stockInItems = $this->getstockInItems($stockInData['recv_id']);
				foreach ($recvItems as $recvItem) {
					if (!empty($stockInData['items'][$recvItem['item_id']])) {
						$item_id = $recvItem['item_id'];
						$isFinishStockIn = 2; # đang trong tiến trình xuất kho
						$don_vi_tinh_goc = $this->Item->lay_don_vi_goc($recvItem['item_id']); # đơn vị gốc

						# Lấy ra số lượng đã nhập kho và đơn vị tính
						$so_luong_da_nhap_kho = !empty($stockInItems[$recvItem['item_id']]['qty']) ? $stockInItems[$recvItem['item_id']]['qty'] : 0;



						# Lấy ra số lượng tiếp tục nhập kho và đơn vị tính mới
						$so_luong_nhap_kho = $stockInData['items'][$recvItem['item_id']]->totalQty;
						$don_vi_item_nhap_kho = $stockInData['items'][$recvItem['item_id']]->measure_id;
						$so_luong_nhap_kho_quy_doi = $so_luong_nhap_kho;

						# Thực hiện đổi về đơn vị tính gốc
						$this->load->model('ItemMeasures');
						if($don_vi_item_nhap_kho != '-1' && $don_vi_item_nhap_kho != null) {


							$so_luong_nhap_kho_quy_doi = $this->ItemMeasures->quy_doi_ra_don_vi_cua_san_pham($item_id,$so_luong_nhap_kho,$don_vi_item_nhap_kho,$don_vi_tinh_goc);


						}

						if (round($recvItem['quantity_purchased'],3) == ($so_luong_da_nhap_kho + $so_luong_nhap_kho)) {
							# Đã hoàn thành
							# Đã hoàn thành
							# Đã hoàn thành
							$isFinishStockOut = 1; # Đã hoàn thành
							# Đã hoàn thành
							# Đã hoàn thành
							# Đã hoàn thành
						} elseif((round($recvItem['quantity_purchased'],3) < ($so_luong_da_nhap_kho + $so_luong_nhap_kho))) {

							$error = "Bạn đã chọn quá số lượng cần xuất, tổng số lượng đã chọn là " . ($so_luong_nhap_kho) . ",vui lòng kiểm tra lại đơn vị tính";
							echo json_encode(['success' => false, 'message' => $error]);
							return;
						}

					} else {

						# Chưa hoàn thành
						# Chưa hoàn thành
						# Chưa hoàn thành
						# Chưa hoàn thành
						$isFinishStockIn = 0;
					}
				}
			}
			# Nếu chưa có data nhập kho
			# Nếu chưa có data nhập kho
			# Nếu chưa có data nhập kho
			# Nếu chưa có data nhập kho
			else {

				# Thực hiện đổi về đơn vị tính gốc
				$this->load->model('ItemMeasures');
				// Validate sale
				foreach ($recvItems as $recvItem) {
					$item_id = $recvItem['item_id'];
					$don_vi_tinh_goc = $this->Item->lay_don_vi_goc($recvItem['item_id']); # đơn vị gốc
				
					# Lấy ra số lượng tiếp tục xuất kho và đơn vị tính mới
					$so_luong_nhap_kho = $stockInData['items'][$recvItem['item_id']]->totalQty;
					$don_vi_item_nhap_kho = $stockInData['items'][$recvItem['item_id']]->measure_id;
					if($don_vi_item_nhap_kho != '-1' && $don_vi_item_nhap_kho != NULL) {

						$so_luong_nhap_kho = $this->ItemMeasures->quy_doi_ra_don_vi_cua_san_pham($item_id, $so_luong_nhap_kho, $don_vi_item_nhap_kho, $don_vi_tinh_goc);
					}

					if (round($recvItem['quantity_purchased'],3) == ($so_luong_nhap_kho)) {
						# Đã hoàn thành
						$isFinishStockIn = 1; # Đã hoàn thành
						# Đã hoàn thành

					} elseif((round($recvItem['quantity_purchased'],3) < ($so_luong_nhap_kho))) {
						$error = "Bạn đã chọn quá số lượng cần nhập, tổng số lượng đã chọn là " . ($so_luong_nhap_kho) . ",vui lòng kiểm tra lại đơn vị tính";
						echo json_encode(['success' => false, 'message' => $error]);
						return;
					} else {
						$isFinishStockIn = 2; # đang trong tiến trình
					}
				}
			}
		}
		

		$this->db->insert('stock_in',$stockInRecord);
		$stockId = $this->db->insert_id();
		
		foreach ($stockInData['items'] as $item) {

			$stockItemsData = [
					'stock_in_id' => $stockId,
					'item_id' => empty($item->item_id) ? 0 : $item->item_id,
					'item_kit_id' => empty($item->item_kit_id) ? 0 : $item->item_kit_id,
					'qty' => (int) $item->totalQty,
					'measure_id' => empty($item->measure_id) ? 0 : (int) $item->measure_id,
			];
			$this->db->insert('stock_in_items',$stockItemsData);

			
			$stock_recorder_check=false;
			$out_of_stock_check=false;
			
			if (!empty($item->item_id)) {
			
				$cur_item_info = $this->Item->get_info($item->item_id);
				$cur_item_location_info = $this->Item_location->get_info($item->item_id);
					
				//checks if the quantity is greater than reorder level
				if(empty($item->reorder_level)) 
					$reorder_level = $item->reorder_level;
				else $reorder_level = 0;

				if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
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
					$don_vi_tinh_cua_so_luong_can_nhap = $item->measure_id;

					$so_luong_da_quy_doi = $this->ItemMeasures->quy_doi_ra_don_vi_cua_san_pham($item->item_id,$so_luong_can_quy_doi,$don_vi_tinh_cua_so_luong_can_nhap,$don_vi_tinh_goc);

					$cur_item_location_info = $this->Item_location->get_info($item->item_id);
					$so_luong_thay_doi = $cur_item_location_info->quantity + $so_luong_da_quy_doi;

					$this->Item_location->update_chuyen_kho_noi_bo($so_luong_thay_doi, $item->item_id);
					# Lưu thông tin xuất kho vào inventory
					# abc

					$reciever_prefix = $this->config->item('receive_prefix'); 
					if($stockInMode == "free_style")
					{
						$comment = "Nhập kho trực tiếp cho nhà cung cấp";
					}
					else if($stockInMode == "by_recv")
					{
						$comment ="Nhập kho theo đơn cho nhà cung cấp"."_".$reciever_prefix." ".$stockInData['recv_id'];
					}

					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>empty($item->item_id) ? 0 : $item->item_id,
						'trans_user'=>empty($stockInData['supplier']) ? 0 : $stockInData['supplier'],
						// 'trans_comment'=> 'Xuất kho trực tiếp cho nhà cung cấp' ,
						'trans_comment' => $comment,
						'trans_inventory'=>round($so_luong_da_quy_doi,3),
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
					$this->Item_location->save_quantity($cur_item_location_info->quantity + ((int) $item->totalQty * $item_kit_item->quantity), $item_kit_item->item_id);
				}
			}
		}

		// $isFinishStockOut = 2 - stocking in ...
		$stockInMode = $this->mysession->getValue('stockInMode');
		if ($stockInMode == 'by_recv') {
			$this->Receiving->StockIn($stockInData['recv_id'], $isFinishStockIn);
		}
		return $stockId;
	}
}