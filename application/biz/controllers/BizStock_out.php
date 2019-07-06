<?php
require_once (APPPATH . "controllers/Secure_area.php");

class BizStock_out extends Secure_area
{
	const STOCK_OUT_SESSION_KEY = 'STOCK_OUT_DETAIL';
	
	function __construct()
	{
		parent::__construct('stock_out');
		$this->load->library('MySession');
		
		$this->load->model('Item');
		$this->load->model('Sale');
		$this->load->model('Measure');
		$this->load->model('StockOut');
		$this->load->model('Customer');
		$this->load->library('sale_lib');
		$this->load->model('Item_kit');
		
		$this->lang->load('receivings');
		
	}
	
	public function index() {
		$saleId = $this->input->get('sId');
		$stockOutMode = !empty($this->mysession->getValue('stockOutMode')) ? $this->mysession->getValue('stockOutMode') : 'by_sale';
		$this->mysession->setValue('stockOutMode', $stockOutMode);

		$data['stock_out_data'] = $this->luu_du_lieu_xuat_kho($saleId);



		$deliverer = null;
		if (!empty($data['stock_out_data']['deliverer'])) {
			$deliverer = $this->Employee->get_info($data['stock_out_data']['deliverer']);
		}
		
		$customer = null;
		if (!empty($data['stock_out_data']['customer'])) {
			$customer = $this->Customer->get_info($data['stock_out_data']['customer']);
		}
		
		$data['deliverer'] = $deliverer;
		$data['customer'] = $customer;
		$data['sale_id'] = $saleId;
		$data['selected_tml'] = $this->load->view('stock_out/partials/selected_items', $data['stock_out_data'], TRUE);
		$this->load->view('stock_out/index', $data);
	}

	public function store_item()
	{
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$saleID = $this->input->post('sale_id');
		$itemId = $this->input->post('item_id');

		$stockOutMode = $this->mysession->getValue('stockOutMode');
		# Nếu mode là theo đơn hàng
		if (isset($stockOutMode) && $stockOutMode == 'by_sale') {


			$stockOutData = $this->luu_du_lieu_xuat_kho($saleID);

		# Nếu mode là trực tiếp

		} else {
			$stockOutData['add'] = false;
			if (isset($stockOutData['items'][$itemId]))
			{
				$stockOutData['items'][$itemId]->totalQty ++;

			}
			# Nếu không có dữ liệu bán hàng sẽ vào đây
			else {
				if($this->sale_lib->is_valid_item_kit($itemId))
				{
					$itemKit = $this->Item_kit->get_info($itemId);
					$stockOutData['items'][$itemId] = $itemKit;
					$stockOutData['items'][$itemId]->itemType = 'kit';
			
				} else {
					$stockOutData['items'][$itemId] = $this->Item->get_info($itemId);

					$stockOutData['items'][$itemId]->qtyStockOut = !empty($stockOutItems[$itemId]['qty']) ? $stockOutItems[$itemId]['qty'] : 0;
					 $stockOutData['items'][$itemId]->qtyOrigin = 0;
		
				}

				$stockOutData['items'][$itemId]->totalQty = 1;
			}
		}
		
		$customerId = $stockOutData['customer'];
		if(isset($customerId)){
			$add = TRUE;
			
		} else {
			$add = false;
		}

		$customer = $this->Customer->get_info($customerId);
		if($customer->image_id == null) $avatar = base_url().'/assets/assets/images/avatar-default.jpg'; 
		else $avatar = base_url().'app_files/view/39';

		$ten_khach_hang = $customer->first_name . ' ' . $customer->last_name;

		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		echo json_encode(array(
				'success' => true, 
				'html' => $this->load->view('stock_out/partials/selected_items', $stockOutData, TRUE),
				'add'=>$add,
				'ten_khach_hang' => $ten_khach_hang,
				'email'=>$customer->email,
				'avatar'=>$avatar
		));
	}

	/**
	 * @param $saleId yêu cầu sale id để lấy dữ liệu
	 * @return array
     */
	public function luu_du_lieu_xuat_kho($saleId){
		$stockOutData = array();
		if (!empty($saleId)) {
			$stockOutMode = $this->mysession->getValue('stockOutMode');
			$stockOutData = [];
			$saleInfo = $this->Sale->get_info($saleId)->row();
			if (!empty($saleInfo)) {
				$stockOutItems = $this->StockOut->getStockOutItems($saleId);
				$stockOutData['mode'] = $stockOutMode;
				$stockOutData['customer'] = $saleInfo->customer_id;
				$items = $this->Sale->get_sale_items($saleId)->result();

				foreach ($items as $item) {
					$itemInfo = $this->Item->get_info($item->item_id);
					$don_vi_tinh_goc = $itemInfo->measure_id;
					# Loại bỏ phần giảm giá trong đơn hàng
					if($item->quantity_purchased < 0) continue;

					# đổi đơn vị tính trước khi kiểm tra
					$so_luong_can_xuat = $item->measure_qty;
					if(isset($stockOutItems[$item->item_id])){
						$don_vi_tinh_cua_so_luong_can_xuat = $item->measure_id;
						$so_luong_can_xuat = $this->doi_don_vi_tinh($item->item_id,$don_vi_tinh_cua_so_luong_can_xuat,$so_luong_can_xuat,$don_vi_tinh_goc);
					}



					# Kiểm tra nếu số lượng xuất kho lớn hơn số lượng thực tế thì bỏ qua
					if(isset($stockOutItems[$item->item_id]) && $stockOutItems[$item->item_id]['qty'] >= $so_luong_can_xuat)
					{
						continue;
					}

					# lấy thông tin của sản phẩm

					$stockOutData['items'][$item->item_id] = $this->Item->get_info($item->item_id);
					if(isset($stockOutItems[$item->item_id]) && !empty($stockOutItems[$item->item_id]['qty'])){
						$so_luong_da_xuat_kho = $stockOutItems[$item->item_id]['qty'];
					}

					else $so_luong_da_xuat_kho = 0;


					$stockOutData['items'][$item->item_id]->totalQty = $so_luong_can_xuat - $so_luong_da_xuat_kho;

					# Số lượng đã xuất kho
					$stockOutData['items'][$item->item_id]->qtyStockOut = $so_luong_da_xuat_kho;

					# Tổng số lượng cần phải xuất kho
					$stockOutData['items'][$item->item_id]->qtyOrigin = $so_luong_can_xuat;

					# Đơn vị gốc cần quy đổi về để trừ kho
					$stockOutData['items'][$item->item_id]->measure_converted = $don_vi_tinh_goc;
					
					if(!empty($stockOutItems[$item->item_id]) && $stockOutItems[$item->item_id]['thay_doi']){
						$stockOutData['items'][$item->item_id]->measure_id = $don_vi_tinh_goc;
					} elseif(!empty($item->measure_id)) {
						$stockOutData['items'][$item->item_id]->measure_id = $item->measure_id;
					} else {
						$stockOutData['items'][$item->item_id]->measure_id = '-1';
					}

				}
				$stockOutData['add'] = TRUE;
				$stockOutData['sale_id'] = $saleId;
				$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
			}
		}
		return $stockOutData;
	}
	
	public function change_mode() {
		$this->mysession->clear_stock();
		$stockOutMode = $this->input->post('mode');
		# Lưu lại chế độ vào session
		$this->mysession->setValue('stockOutMode', $stockOutMode);
		echo json_encode(['success' => 1,'message'=>'Chuyển chế độ']);
	}
	
	function search()
	{
		$stockOutMode = $this->mysession->getValue('stockOutMode');
		if (isset($stockOutMode) && $stockOutMode == 'by_sale') {

			$suggestions = $this->Sale->getSaleForStockOut($this->input->get('term'));
			echo json_encode($suggestions);
		} else {
			# allow parallel searchs to improve performance.
			session_write_close();
			$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100);
			$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),100));
			echo json_encode($suggestions);
		}
	}

	/**
	 * @param int $itemId
     */
	public function edit_item($itemId = 0)
	{
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$measure = 0;

		$so_luong_da_xuat = $stockOutData['items'][$itemId]->qtyStockOut;

		$tong_so_luong = $stockOutData['items'][$itemId]->qtyOrigin;


		if ($this->input->post('name') == 'quantity')
		{
			$qty = $this->input->post('value');

		} elseif ($this->input->post('name') == 'measure') {
			$measure = $this->input->post('value');
		}
		
		if (!isset($stockOutData['items'][$itemId]))
		{
			$stockOutData['items'][$itemId] = $this->Item->get_info($itemId);
		}
		
		if (isset($qty)) {
			$stockOutData['items'][$itemId]->totalQty = $qty;
		}
		if (!empty($measure)) {
			# lấy đơn vị tính hiện tại
			$don_vi_tinh_hien_tai = $stockOutData['items'][$itemId]->measure_id;
			# chuyển sang đơn vị tính mới cần quy đổi
			$don_vi_can_quy_doi = $stockOutData['items'][$itemId]->measure_id = $measure;


			# Đổi đơn vị tính
			$so_luong_da_xuat = $this->doi_don_vi_tinh($itemId, $don_vi_tinh_hien_tai,$so_luong_da_xuat,$don_vi_can_quy_doi);

			$tong_so_luong = $this->doi_don_vi_tinh($itemId, $don_vi_tinh_hien_tai,$tong_so_luong,$don_vi_can_quy_doi);
			// die;

			$stockOutData['items'][$itemId]->qtyStockOut = $so_luong_da_xuat;
			$stockOutData['items'][$itemId]->qtyOrigin = $tong_so_luong;
			# Lưu lại số lượng thay đổi sau khi đổi đơn vị tính
			$stockOutData['items'][$itemId]->totalQty = (round($tong_so_luong,3)-round($so_luong_da_xuat,3));
		}
		
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		echo json_encode(array(
			'so_luong_quy_doi'=>round($so_luong_da_xuat,3).'/'.round($tong_so_luong,3),
			
			'tong_so_luong'=>(round($tong_so_luong,3)-round($so_luong_da_xuat,3))
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
	
	public function cancel() {
		$this->mysession->clear_stock();
		redirect('stock_out');
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

		$data['history'] = $this->StockOut->getHistory($search);
		
		$this->load->view('stock_out/history', $data);
	}

	public function remove_item() {
		$itemId = $this->input->post('item_id');
		
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		if (isset($stockOutData['items'][$itemId])) {
			unset($stockOutData['items'][$itemId]);
		}
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		
		echo json_encode(['success' => 1]);
	}
	
	public function loai_bo_nhan_vien_da_chon(){
		$key = $this->input->post('key');
		$this->mysession->unsetValue($key);
	}

	public function select_delivery() {
		$delivererId = $this->input->post('deliverer_id');
		
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$stockOutData['deliverer'] = $delivererId;
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		$deliverer = $this->Employee->get_info($delivererId);

		if($deliverer->image_id == null) $avatar = base_url().'/assets/assets/images/avatar-default.jpg'; 
		else $avatar = base_url().'app_files/view/'.$deliverer->image_id;

		echo json_encode(['success' => 1, 'ten_nhan_vien' => $deliverer->first_name . ' ' . $deliverer->last_name,'email'=>$deliverer->email,'avatar'=>$avatar]);
	}
	
	public function select_customer() {
		$customerId = $this->input->post('customer_id');
		
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$stockOutData['customer'] = $customerId;
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		
		$customer = $this->Customer->get_info($customerId);
		if($customer->image_id == null) $avatar = base_url().'/assets/assets/images/avatar-default.jpg'; 
		else $avatar = base_url().'app_files/view/39';
		echo json_encode(['success' => 1, 'ten_khach_hang' => $customer->first_name . ' ' . $customer->last_name,'email'=>$customer->email,'avatar'=>$avatar]);
	}
	
	public function kiem_tra_truoc_khi_hoan_thanh(){

		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$success = TRUE;
		$error = '';

		if(empty($stockOutData['items'])){
			$error = 'Chưa có sản phẩm nào trong giỏ hàng';
			$success = false;
			echo json_encode(['success' => $success, 'message' => $error]);
			return TRUE;

		} elseif(empty($stockOutData['customer'])){
			$error = 'Bạn chưa nhập tên khách hàng';
			$success = false;
			echo json_encode(['success' => $success, 'message' => $error]);
			return TRUE;

		} elseif(empty($stockOutData['deliverer'])){
			$error = 'Bạn chưa nhập tên nhân viên xuất hàng';
			$success = false;
			echo json_encode(['success' => $success, 'message' => $error]);
			return TRUE;
		} 

		else return false;

	}

	public function finish() {
		$kiemtra = $this->kiem_tra_truoc_khi_hoan_thanh();
		if($kiemtra) return;

		$post = $this->input->post();
		$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
		$stockOutData['comment'] = $this->input->post('comment');

		$stockOutId = $this->StockOut->save($stockOutData);
		$stockOutData['stock_out_id'] = $stockOutId;
		$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);

		$error = 'Xuất kho hoàn thành';
		$success = TRUE;
		if(isset($stockOutId))
		echo json_encode(['success' => $success, 'message' => $error,'stockOutId'=>$stockOutId]);
	}
	
	public function pre_print($id = 0) {
		if (!empty($id)) {
			$stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
			$stockOut = $this->StockOut->getInfo($id);
			
			$stockOutData['customer'] = $stockOut->customer_id;
			$stockOutData['deliverer'] = $stockOut->deliverer_id;
			$stockOutData['comment'] = $stockOut->comment;
			
			foreach ($stockOut->items as $item) {
				$stockOutData['items'][$item->item_id] = $item;
				$stockOutData['items'][$item->item_id]->totalQty = $item->stockOut_totalQty;
				if (!empty($item->stockOut_measureId)) {
					$stockOutData['items'][$item->item_id]->measure_id = $item->stockOut_measureId;
				}
			}
			
			$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, $stockOutData);
		}
		
		if (!empty($this->mysession->getValue(self::STOCK_OUT_SESSION_KEY))) {
			$data['stock_out_data'] = $stockOutData = $this->mysession->getValue(self::STOCK_OUT_SESSION_KEY);
			$data['pdf_block_html'] = $this->load->view('stock_out/partials/pdf', $stockOutData, TRUE);
			$this->mysession->setValue(self::STOCK_OUT_SESSION_KEY, null);
			$this->load->view('stock_out/pre_print', $data);
		} else {

		}
		
	}
}
