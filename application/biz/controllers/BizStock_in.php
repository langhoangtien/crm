<?php
require_once (APPPATH . "controllers/Secure_area.php");

class BizStock_in extends Secure_area
{
	const STOCK_IN_SESSION_KEY = 'STOCK_IN_DETAIL';
	
	function __construct()
	{
		parent::__construct('stock_in');
		$this->load->library('MySession');
		
		$this->load->model('Item');
		$this->load->model('Receiving');
		$this->load->model('Measure');
		$this->load->model('StockIn');
		$this->load->model('Customer');
		$this->load->library('receiving_lib');
		$this->load->model('Item_kit');
		$this->load->model('Supplier');
		
		$this->lang->load('receivings');
		
	}

	public function history()
	{
		$data = array();
		$start_date = $this->input->get('start_date');
		
		if (empty($start_date)) {
			$data['start_date'] = date('d-m-Y', strtotime("-30 days"));
			$search['start_date'] = date('Y-m-d', strtotime("-30 days"));
		} else {
			$data['start_date'] = $this->input->get('start_date_formatted');
			$search['start_date'] = $this->input->get('start_date');
		}
	
		$end_date = $this->input->get('end_date');
	
		if (empty($end_date)) {
			$data['end_date'] = date('d-m-Y');
			$search['end_date'] = date('Y-m-d');
		} else {
			$data['end_date'] = $this->input->get('end_date_formatted');
			$search['end_date'] = $this->input->get('end_date');
		}

		$data['history'] = $this->StockIn->getHistory($search);
		
		$this->load->view('stock_in/history', $data);
	}
	
	public function index() {
		$recvId = $this->input->get('recvId');
		$StockInMode = !empty($this->mysession->getValue('StockInMode')) ? $this->mysession->getValue('StockInMode') : 'by_recv';
		$this->mysession->setValue('StockInMode', $StockInMode);

		$data['stock_in_data'] = $this->luu_du_lieu_nhap_kho($recvId);
		$employee = null;
		if (!empty($data['stock_in_data']['employee'])) {
			$employee = $this->Employee->get_info($data['stock_in_data']['employee']);
		}
		
		$supplier = null;
		if (!empty($data['stock_in_data']['supplier'])) {
			$supplier = $this->Supplier->get_info($data['stock_in_data']['supplier']);
		}
		
		$data['employee'] = $employee;
		$data['supplier'] = $supplier;
		$data['recv_id'] = $recvId;

		$data['selected_tml'] = $this->load->view('stock_in/partials/selected_items', $data['stock_in_data'], TRUE);
		$this->load->view('stock_in/index', $data);
		}	

	public function store_item() {
		$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);
		$recvId = $this->input->post('receiving_id');
		$itemId = $this->input->post('item_id');

		$stockInMode = $this->mysession->getValue('stockInMode');
		if (isset($stockInMode) && $stockInMode == 'by_recv') {
			$stockOutData['add'] = true;
			$stockInData = $this->luu_du_lieu_nhap_kho($recvId);

		} else {
			$stockOutData['add'] = false;
			if (isset($stockInData['items'][$itemId]))
			{
				$stockInData['items'][$itemId]->totalQty ++;
			}
			# Nếu không có dữ liệu nhập hàng sẽ vào đây
			else {
				if($this->receiving_lib->is_valid_item_kit($itemId))
				{
					$itemKit = $this->Item_kit->get_info($itemId);
					$stockInData['items'][$itemId] = $itemKit;
			
					$stockInData['items'][$itemId]->itemType = 'kit';
			
				} else {

					$stockInData['items'][$itemId] = $this->Item->get_info($itemId);


					// $stockInData['items'][$recvId]->totalQty = $item->measure_qty - $StockInItems[$recvId]['qty'];
					$stockInData['items'][$itemId]->qtyStockIn = !empty($StockInItems[$itemId]['qty']) ? $StockInItems[$itemId]['qty'] : 0;
					// $stockInData['items'][$recvId]->qtyOrigin = $item->measure_qty;
					 $stockInData['items'][$itemId]->qtyOrigin = 0;
		
				}

				$stockInData['items'][$itemId]->totalQty = 1;
			}
		}
		
        $supplierId = $stockInData['supplier'];
		if(isset($supplierId)){
			$add = TRUE;

		} else {
			$add = false;
		}
		$supplier = $this->Supplier->get_info($supplierId);
		if($supplier->image_id == null) $avatar = base_url().'/assets/assets/images/avatar-default.jpg'; 
		else $avatar = base_url().'app_files/view/39';

		$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);
		echo json_encode(array(
				'success' => true, 
				'html' => $this->load->view('stock_in/partials/selected_items', $stockInData, TRUE),
				'add'=>$add,
				'ten_nha_cung_cap' => $supplier->company_name,
				'email'=>$supplier->email,
				'avatar'=>$avatar
		));
	}

	public function luu_du_lieu_nhap_kho($recvId){
		$stockInData = array();
		if (!empty($recvId)) {
			$StockInMode = !empty($this->mysession->getValue('StockInMode')) ? $this->mysession->getValue('StockInMode') : 'by_recv';
			$stockInData = [];
			$recvInfo = $this->Receiving->get_info($recvId)->row();
			if (!empty($recvInfo)) {
				$StockInItems = $this->StockIn->getStockInItems($recvId);
				$stockInData['mode'] = !empty($StockInMode) ? $StockInMode:'by_recv';
				$stockInData['supplier'] = $recvInfo->supplier_id;
				$items = $this->Receiving->get_receiving_items($recvId)->result();

				foreach ($items as $item) {
					$itemInfo = $this->Item->get_info($item->item_id);
					$don_vi_tinh_goc = $itemInfo->measure_id;
					# Loại bỏ phần giảm giá trong đơn hàng
					if($item->quantity_purchased < 0) continue;

					# đổi đơn vị tính trước khi kiểm tra
					$so_luong_can_xuat = $item->measure_qty;
					if(isset($StockInItems[$item->item_id])){
						$don_vi_tinh_cua_so_luong_can_xuat = $item->measure_id;
						$so_luong_can_xuat = $this->doi_don_vi_tinh($item->item_id,$don_vi_tinh_cua_so_luong_can_xuat,$so_luong_can_xuat,$don_vi_tinh_goc);
					}



					# Kiểm tra nếu số lượng xuất kho lớn hơn số lượng thực tế thì bỏ qua
					if(isset($StockInItems[$item->item_id]) && $StockInItems[$item->item_id]['qty'] >= $so_luong_can_xuat)
					{
						continue;
					}

					# lấy thông tin của sản phẩm

					$stockInData['items'][$item->item_id] = $this->Item->get_info($item->item_id);
					if(isset($StockInItems[$item->item_id]) && !empty($StockInItems[$item->item_id]['qty'])){
						$so_luong_da_xuat_kho = $StockInItems[$item->item_id]['qty'];
					}

					else $so_luong_da_xuat_kho = 0;


					$stockInData['items'][$item->item_id]->totalQty = $so_luong_can_xuat - $so_luong_da_xuat_kho;

					# Số lượng đã xuất kho
					$stockInData['items'][$item->item_id]->qtyStockIn = $so_luong_da_xuat_kho;

					# Tổng số lượng cần phải xuất kho
					$stockInData['items'][$item->item_id]->qtyOrigin = $so_luong_can_xuat;

					# Đơn vị gốc cần quy đổi về để trừ kho
					$stockInData['items'][$item->item_id]->measure_converted = $don_vi_tinh_goc;

					if(!empty($StockInItems[$item->item_id]) && $StockInItems[$item->item_id]['thay_doi']){
						$stockInData['items'][$item->item_id]->measure_id = $don_vi_tinh_goc;
					} elseif(!empty($item->measure_id)) {
						$stockInData['items'][$item->item_id]->measure_id = $item->measure_id;
					} else {
						$stockInData['items'][$item->item_id]->measure_id = '-1';
					}


				}
				$stockInData['add'] = TRUE;

				$stockInData['recv_id'] = $recvId;
				$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);
			}
		}
		return $stockInData;
	}
	
	public function change_mode() {
		$this->mysession->clear_stock();
		$stockInMode = $this->input->post('mode');
		# Lưu lại chế độ vào session
		$this->mysession->setValue('stockInMode', $stockInMode);
		echo json_encode(['success' => 1,'message'=>'Chuyển chế độ']);
	}
	
	function search()
	{
		$stockInMode = $this->mysession->getValue('stockInMode');
		// var_dump($stockInMode);
		if (isset($stockInMode) && $stockInMode == 'by_recv') {
			// session_write_close();
			$suggestions = $this->Receiving->getSaleForStockIn($this->input->get('term'));
			// var_dump($suggestions);
			echo json_encode($suggestions);
		} else {
			//allow parallel searchs to improve performance.
			session_write_close();
			$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100);
			$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),100));
			echo json_encode($suggestions);
		}
	}
	
	public function edit_item($recvId = 0)
	{
		$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);
		$so_luong_da_nhap = $stockInData['items'][$recvId]->qtyStockIn;

		$tong_so_luong = $stockInData['items'][$recvId]->qtyOrigin;
		$measure = 0;

		if ($this->input->post('name') == 'quantity')
		{
			$qty = $this->input->post('value');

		} elseif ($this->input->post('name') == 'measure') {
			$measure = $this->input->post('value');
		}
		
		if (!isset($stockInData['items'][$recvId]))
		{
			$stockInData['items'][$recvId] = $this->Item->get_info($recvId);
		}
		
		if (isset($qty)) {
			$stockInData['items'][$recvId]->totalQty = $qty;
		}
		
		if (!empty($measure)) {
			# lấy đơn vị tính hiện tại
			$don_vi_tinh_hien_tai = $stockInData['items'][$recvId]->measure_id;
			# chuyển sang đơn vị tính mới cần quy đổi
			$don_vi_can_quy_doi = $stockInData['items'][$recvId]->measure_id = $measure;


			# Đổi đơn vị tính
			$so_luong_da_nhap = $this->doi_don_vi_tinh($recvId, $don_vi_tinh_hien_tai,$so_luong_da_nhap,$don_vi_can_quy_doi);

			$tong_so_luong = $this->doi_don_vi_tinh($recvId, $don_vi_tinh_hien_tai,$tong_so_luong,$don_vi_can_quy_doi);
			// die;

			$stockInData['items'][$recvId]->qtyStockIn = $so_luong_da_nhap;
			$stockInData['items'][$recvId]->qtyOrigin = $tong_so_luong;
			# Lưu lại số lượng thay đổi sau khi đổi đơn vị tính
			$stockInData['items'][$recvId]->totalQty = (round($tong_so_luong,3)-round($so_luong_da_nhap,3));

		}
		
		$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);
		echo json_encode(array(
				'so_luong_quy_doi'=>round($so_luong_da_nhap,3).'/'.round($tong_so_luong,3),

				'tong_so_luong'=>(round($tong_so_luong,3)-round($so_luong_da_nhap,3))
			)
		);

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

	public function loai_bo_nhan_vien_da_chon(){
		$key = $this->input->post('key');
		$this->mysession->unsetValue($key);
	}

	public function cancel() {
		$this->mysession->clear_stock();
		redirect('stock_in');
	}
	
	public function remove_item() {
		$itemId = $this->input->post('item_id');
		
		$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);
		if (isset($stockInData['items'][$itemId])) {
			unset($stockInData['items'][$itemId]);
		}
		$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);
		
		echo json_encode(['success' => 1]);
	}
	
	public function select_employee() {
		$employeeId = $this->input->post('employee_id');
		
		$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);
		$stockInData['employee'] = $employeeId;
		$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);
		$employee = $this->Employee->get_info($employeeId);

		if($employee->image_id == null) $avatar = base_url().'/assets/assets/images/avatar-default.jpg'; 
		else $avatar = base_url().'app_files/view/'.$employee->image_id;

		echo json_encode(['success' => 1, 'ten_nhan_vien' => $employee->first_name . ' ' . $employee->last_name,'email'=>$employee->email,'avatar'=>$avatar]);
	}
	
	public function select_supplier() {
		$supplierId = $this->input->post('supplier_id');
		
		$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);
		$stockInData['supplier'] = $supplierId;
		$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);
		
		$supplier = $this->Supplier->get_info($supplierId);

		if($supplier->image_id == null) $avatar = base_url().'/assets/assets/images/avatar-default.jpg'; 
		else $avatar = base_url().'app_files/view/39';
		echo json_encode(['success' => 1, 'ten_nha_cung_cap' => $supplier->company_name,'email'=>$supplier->email,'avatar'=>$avatar]);
	}
	
	public function kiem_tra_truoc_khi_hoan_thanh(){
		$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);


		$success = TRUE;
		$error = '';
		if(empty($stockInData['supplier'])){
			
			$error = 'Bạn chưa nhập tên nhà cung cấp';
			$success = false;
			echo json_encode(['success' => $success, 'message' => $error]);
			return TRUE;

		} elseif(empty($stockInData['employee'])){
			$error = 'Bạn chưa nhập tên nhân viên nhập hàng';
			$success = false;
			echo json_encode(['success' => $success, 'message' => $error]);
			return TRUE;
		} 
		else return false;

	}

	/**
	 *
     */
	public function finish() {
		$kiemtra = $this->kiem_tra_truoc_khi_hoan_thanh();
		if($kiemtra) return;


		$post = $this->input->post();
		$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);
		$stockInData['comment'] = $this->input->post('comment');
		
		$stockInId = $this->StockIn->save($stockInData);
		$stockInData['stock_in_id'] = $stockInId;
		$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);

		$error = 'Nhập kho hoàn thành';
		$success = TRUE;
		echo json_encode(['success' => $success, 'message' => $error,'stockInId'=>$stockInId]);
	}
	
	public function pre_print($id = 0) {
		if (!empty($this->mysession->getValue(self::STOCK_IN_SESSION_KEY))) {
			$stockInData = $this->mysession->getValue(self::STOCK_IN_SESSION_KEY);
		} elseif (!empty($id)) {
			$stockInData = [];
			$stockIn = $this->StockIn->getInfo($id);
			
			$stockInData['employee'] = $stockIn->employee_id;
			$stockInData['supplier'] = $stockIn->supplier_id;
			$stockInData['comment'] = $stockIn->comment;
			$stockInData['stock_in_id'] = $id;
			foreach ($stockIn->items as $item) {
				$stockInData['items'][$item->item_id] = $item;
				$stockInData['items'][$item->item_id]->totalQty = $item->stockIn_totalQty;
				if (!empty($item->stockIn_measureId)) {
					$stockInData['items'][$item->item_id]->measure_id = $item->stockIn_measureId;
				}
			}
			$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, $stockInData);
		}
		 else {
			// redirect('stock_in');
		}

		$data['stock_in_data'] = $stockInData;
		$data['pdf_block_html'] = $this->load->view('stock_in/partials/pdf', $stockInData, TRUE);
		$this->mysession->setValue(self::STOCK_IN_SESSION_KEY, null);
		$this->load->view('stock_in/pre_print', $data);
		
		
	}
}
