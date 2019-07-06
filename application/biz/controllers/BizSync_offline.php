<?php

require_once (APPPATH . "controllers/Secure_area.php");

class BizSync_offline extends Secure_area 
{
	function __construct()
	{
		ini_set('max_execution_time', 300);
		
		parent::__construct('sync_offline');
		
		$this->load->model('Sale');
		$this->load->model('SaleOffline');
		$this->load->library('MySession');
	}
	
	public function index() {
		$this->load->view('sync_offline/index');
	}
	
	public function history() {
		$data = array();
		$start_date = $this->input->get('start_date');
		if (empty($start_date)) {
			$data['start_date'] = date('d-m-Y', strtotime("-7 days"));
			$search['start_date'] = date('Y-m-d', strtotime("-7 days"));
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
		
		$this->mysession->setValue('offline_search', $search);
		
		$data['offline_sale'] = $this->Sale->getOffline($search);
		
		$this->load->view('sync_offline/history', $data);
	}
	
	public function sale() {
		// TODO
		$file_info = pathinfo($_FILES['file_path']['name']);
		if ($file_info['extension'] != 'biz') {
			echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
			return;
		}
		
		if ($_FILES['file_path']['error'] != UPLOAD_ERR_OK) {
			$msg = lang('common_excel_import_failed');
			echo json_encode(array('success' => false, 'message' => $msg));
			return;
		} else {
			// $_FILES['file_path']['tmp_name']
			if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE) {
				$offlineData = [];
				
				$offlineCustomerData = [];
				
				$count = 0;
				$_count = 0;
				$saleInfo = [];
				$customerInfo = [];
				
				while (($line = fgets($handle)) !== false) {
					
					if (strpos($line, 'customer' . BIZ_SEPARATOR) === FALSE) {
						if (strpos($line, 'person' . BIZ_SEPARATOR) !== FALSE) {
							$customerInfo['person'] = $this->extractOfflineData($line);
						}
					} else {
						if ($_count) {
							$offlineCustomerData[$_count] = $customerInfo;
						}
						$_count  ++;
						$customerInfo = [];
						$customerInfo['customer'] = $this->extractOfflineData($line);
					}
					
					if (strpos($line, 'sale' . BIZ_SEPARATOR) === FALSE) {
						
						if (strpos($line, 'sale_item' . BIZ_SEPARATOR) !== FALSE) {
							$saleInfo['sale_items'][] = $this->extractOfflineData($line);
						}
						
						if (strpos($line, 'sale_payment' . BIZ_SEPARATOR) !== FALSE) {
							$saleInfo['sale_payments'][] = $this->extractOfflineData($line);
						}
						
						if (strpos($line, 'sale_item_kit' . BIZ_SEPARATOR) !== FALSE) {
							$saleInfo['sale_item_kits'][] = $this->extractOfflineData($line);
						}
						
						if (strpos($line, 'sale_store_account' . BIZ_SEPARATOR) !== FALSE) {
							$saleInfo['sale_store_accounts'][] = $this->extractOfflineData($line);
						}
						
					} else {
						if ($count) {
							$offlineData[$count] = $saleInfo;
						}
						$count  ++;
						$saleInfo = [];
						$saleInfo['sale'] = $this->extractOfflineData($line);
					}
				}
				
				if (!empty($customerInfo) && $_count) {
					$offlineCustomerData[$_count] = $customerInfo;
				}
				
				if (!empty($saleInfo) && $count) {
					$offlineData[$count] = $saleInfo;
				}
				
				
				$this->SaleOffline->setCustomersData($offlineCustomerData)->setData($offlineData)->sync();
				
				if (!$this->SaleOffline->getErrors()) {
					$data['failedRecords'] = $this->SaleOffline->getFailedRecords();
					
					$html = $this->load->view('sync_offline/partials/failed_records', $data, TRUE);
					echo json_encode(array('success' => true, 'msg_html' => $html, 'has_failed' => !empty($data['failedRecords'])));
					return;
				} else {
					echo json_encode(array('success' => false, 'message' => 'Dữ liệu không chính xác. Vui lòng kiểm tra lại dữ liệu', 'error_msg' => $this->SaleOffline->getErrors()));
					return;
				}
			} else {
				echo json_encode(array('success' => false, 'message' => lang('common_upload_file_not_supported_format')));
				return;
			}
		}
		echo json_encode(array('success' => true, 'message' => 'xxx'));
	}
	
	protected function extractOfflineData ($offlineText = '') {
		$data = explode(BIZ_SEPARATOR, $offlineText);
		if (!empty($data[0])) {
			unset($data[0]);
		}
		return $data;
	}
	
}
?>