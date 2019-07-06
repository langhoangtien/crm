<?php
class BizSaleOffline extends CI_Model {
	protected $data = [];
	
	protected $customers = [];
	
	protected $customerIdMapping = [];
	
	protected $errors = [];
	
	protected $failedRecords = [];
	
	protected $mapping_sale = [
// 		'record_type',
		'sale_time',
		'customer_id',
		'employee_id',
		'sold_by_employee_id',
		'comment',
		'show_comment_on_receipt',
		'sale_id',
		'payment_type',
		'cc_ref_no',
		'auth_code',
		'deleted_by',
		'deleted',
		'suspended',
		'store_account_payment',
		'was_layaway',
		'was_estimate',
		'location_id',
		'register_id',
		'tier_id',
		'points_used',
		'points_gained',
		'did_redeem_discount',
		'signature_image_id',
		'deleted_taxes',
		'deliverer',
		'delivery_date',
		'supporter',
		'is_stock_out',
		'offline'
	];
	
	protected $mapping_sale_item = [
// 		'record_type',
		'sale_id',
		'item_id',
		'description',
		'serialnumber',
		'line',
		'quantity_purchased',
		'measure_id',
		'measure_qty',
		'item_cost_price',
		'item_unit_price',
		'discount_percent',
		'commission'
	];
	
	
	protected $mapping_sale_item_kit = [
// 		'record_type',
		'sale_id',
		'item_kit_id',
		'description',
		'line',
		'quantity_purchased',
		'item_kit_cost_price',
		'item_kit_unit_price',
		'discount_percent',
		'commission',
	];
	
	protected $mapping_store_account = [
	// 		'record_type',
			'sno',
			'customer_id',
			'sale_id',
			'transaction_amount',
			'date',
			'balance',
			'comment',
	];
	
	protected $mapping_sale_payment = [
// 		'record_type',
		'payment_id',
		'sale_id',
		'payment_type',
		'payment_amount',
		'auth_code',
		'ref_no',
		'cc_token',
		'acq_ref_data',
		'process_data',
		'entry_method',
		'aid',
		'tvr',
		'iad',
		'tsi',
		'arc',
		'cvm',
		'tran_type',
		'application_label',
		'truncated_card',
		'card_issuer',
		'payment_date'			
	];
	
	protected $mapping_customer = [
	// 		'record_type',
			'id',
			'attribute_set_id',
			'person_id',
			'account_number',
			'override_default_tax',
			'company_name',
			'balance',
			'credit_limit',
			'points',
			'current_spend_for_points',
			'current_sales_for_discount',
			'taxable',
			'tax_certificate',
			'cc_token',
			'cc_preview',
			'card_issuer',
			'tier_id',
			'created_by',
			'created_location_id',
			'deleted',
			'type_customer',
			'sex',
			'family_info',
			'company_manage_name',
			'company_birth_date',
			'position',
			'code_tax',
			'created_time',
			'offline',
	];
	
	protected $mapping_person = [
			// 		'record_type',
			'first_name',
			'last_name',
			'phone_number',
			'email',
			'address_1',
			'address_2',
			'city',
			'state',
			'zip',
			'country',
			'comments',
			'image_id',
			'person_id',
			'birth_date',
	];
	
	const ERROR_MESSAGE = 'DU LIEU KHONG CHINH XAC.';
	
	public function setCustomersData($customersData = []) {
		$this->customers = $customersData;
		return $this;
	}
	
	public function sync() {
		
		// TODO BEGIN sync customer data
		$offlineCustomers = [];
		$this->validateOfflineCustomerData($this->customers, $offlineCustomers);
		
		if (!empty($offlineCustomers)) {
			foreach ($offlineCustomers as $offlineId => $cusInfor) {
				$this->db->insert('people', $cusInfor['person']);
				$personId = $this->db->insert_id();
				$cusInfor['customer']['person_id'] = $personId;
				$this->db->insert('customers', $cusInfor['customer']);
				$this->customerIdMapping[$offlineId] = $personId;
			}
		}
		// END
		
		if (empty($this->data)) {
			$this->setError(self::ERROR_MESSAGE);
			return;
		}
		
		$offlineSales = [];
		$this->validateOfflineData($this->data, $offlineSales);
		
		if (!empty($offlineSales)) {
			foreach ($offlineSales as $saleData) {
				$this->db->insert('sales', $saleData['sale']);
				$sale_id = $this->db->insert_id();
				
				$customer = $this->Customer->get_info($saleData['sale']['customer_id']);
				$balance = $customer->balance;
				
				if (isset($saleData['sale']['suspended']) && $saleData['sale']['suspended'] != 2) {
					foreach ($saleData['store_accounts'] as $storeAccount) {
						$storeAccount['sale_id'] = $sale_id;
						$balance = $storeAccount['balance'] = $balance + $storeAccount['transaction_amount'];
						$this->db->insert('store_accounts', $storeAccount);
					}
				}
				
				if (!in_array($saleData['sale']['customer_id'], $this->customerIdMapping)) {
					$this->Customer->update_balance($saleData['sale']['customer_id'], $balance);
					$point = $customer->points + $saleData['sale']['points_gained'] - $saleData['sale']['points_used'];
					$this->Customer->update_point($saleData['sale']['customer_id'], $point);
				}
				
				foreach ($saleData['payments'] as $salePayment) {
					$salePayment['sale_id'] = $sale_id;
					$this->db->insert('sales_payments', $salePayment);
				}
				
				$this->load->helper('items');
				foreach ($saleData['items'] as $saleItem) {
					$saleItem['sale_id'] = $sale_id;
					$saleItemConvertedQty = $saleItem['quantity_purchased'];
					if (isset($saleItem['measure_qty']) && isset($saleItem['measure_id'])) {
						$saleItemConvertedQty = getQtyOfItemByMeasure($saleItem['item_id'], $saleItem['measure_qty'], $saleItem['measure_id']);
					}
					
					$qty = $this->Item_location->get_location_quantity($saleItem['item_id']);
					$newQty = $qty - $saleItemConvertedQty;
					$this->Item_location->save_quantity($newQty, $saleItem['item_id'], $saleData['sale']['location_id']);
					$this->db->insert('sales_items', $saleItem);
				}
				
				foreach ($saleData['sale_item_kits'] as $saleItemKit) {
					$saleItemKit['sale_id'] = $sale_id;
					$listItems = $this->Item_kit_items->get_info($saleItemKit['item_kit_id']);
					$listItemsOfBom = [];
					foreach ($this->Item_kit->getKitBomItems($saleItemKit['item_kit_id']) as $kitBom) {
						$tmpItems = $this->Item_kit_items->get_info($kitBom->bom_id);
						foreach ($tmpItems as $tmpItem) {
							$tmpItem->quantity = $tmpItem->quantity * $kitBom->quantity;
						}
							
						$listItemsOfBom = array_merge($listItemsOfBom, $tmpItems);
					}
					$listItems = array_merge($listItems, $listItemsOfBom);
					foreach ($listItems as $kitItem) {
						$saleItemConvertedQty = $kitItem->quantity;
						if (!empty($kitItem->measure_id)) {
							$saleItemConvertedQty = getQtyOfItemByMeasure($kitItem->item_id, $kitItem->quantity, $kitItem->measure_id);
						}
						$qty = $this->Item_location->get_location_quantity($kitItem->item_id);
						$newQty = $qty - $saleItemConvertedQty;
						$this->Item_location->save_quantity($newQty, $kitItem->item_id, $saleData['sale']['location_id']);
					}
					
					$this->db->insert('sales_item_kits', $saleItemKit);
				}
			}
		}
	}
	
	protected function validateOfflineCustomerData($offlineData = [], &$offlineCustomers) {
		foreach ($offlineData as $cusInfo) {
			$cusData = [];
			$valid = true;
			$offlineId = 0;
			if ($customer = array_combine($this->mapping_customer, $cusInfo['customer'])) {
				$offlineId = $customer['person_id'];
				if ($valid && !empty($customer['created_time']) && $this->Customer->checkExistByCreatedTime($customer['created_time'])) {
					$valid = false;
					$customerExists = $this->Customer->getByCreatedTime($customer['created_time']);
					if (!empty($customerExists)) {
						$this->customerIdMapping[$offlineId] = $customerExists['person_id'];
					}
				}
				unset($customer['id']);
				if (isset($customer['tier_id']) && empty($customer['tier_id'])) {
					unset($customer['tier_id']);
				}
				
				if (isset($customer['account_number']) && empty($customer['account_number'])) {
					unset($customer['account_number']);
				}
				$cusData['customer'] = $customer;
			}
			
			if ($valid && $person = array_combine($this->mapping_person, $cusInfo['person'])) {
				unset($person['person_id']);
				unset($person['image_id']);
				$cusData['person'] = $person;
			}
			if ($valid) {
				$offlineCustomers[$offlineId] = $cusData;
			}
		}
	}
	
	protected function validateOfflineData($offlineData = [], &$offlineSales) {
		foreach ($offlineData as $saleInfo) {
			$valid = true;
			$saleData = [];
			
			if ($sale = array_combine($this->mapping_sale, $saleInfo['sale'])) {
				
				if (!empty($this->customerIdMapping)) {
					if (array_key_exists($sale['customer_id'], $this->customerIdMapping)) {
						$sale['customer_id'] = $this->customerIdMapping[$sale['customer_id']];
					}
				}
				
				if ($valid && !empty($sale['sale_time']) && $this->Sale->checkExistBySaleTime($sale['sale_time'])) {
					$valid = false;
				}
				
				if ($valid && !empty($sale['location_id']) && !$this->Location->exists($sale['location_id'])) {
					$valid = false;
				}
				
				if ($valid && !empty($sale['customer_id']) && !$this->Customer->exists($sale['customer_id'])) {
					$valid = false;
				}
				
				if ($valid) {
					unset($sale['sale_id']);
					
					if (isset($sale['deleted_by']) && empty($sale['delete_by'])) {
						unset($sale['deleted_by']);
					}
					
					if (isset($sale['tier_id']) && empty($sale['tier_id'])) {
						unset($sale['tier_id']);
					}
					if (isset($sale['signature_image_id']) && empty($sale['signature_image_id'])) {
						unset($sale['signature_image_id']);
					}
				}
				
			} else {
				// $this->setError(self::ERROR_MESSAGE . print_r($saleInfo['sale'], true));
				$valid = false;
			}
			$saleData['sale'] = $sale;
			
			if (!empty($saleInfo['sale_items'])) {
				foreach ($saleInfo['sale_items'] as $saleItemData) {
					if ($saleItem = array_combine($this->mapping_sale_item, $saleItemData)) {
						$saleData['items'][] = $saleItem;
					} else {
						// $this->setError(self::ERROR_MESSAGE . print_r($saleItemData, true));
						$valid = false;
					}
				}
			}
			if (!empty($saleInfo['sale_item_kits'])) {
				foreach ($saleInfo['sale_item_kits'] as $saleItemKitData) {
					if ($saleItemKit = array_combine($this->mapping_sale_item_kit, $saleItemKitData)) {
						$saleData['item_kits'][] = $saleItemKit;
					} else {
						// $this->setError(self::ERROR_MESSAGE . print_r($saleItemKitData, true));
						$valid = false;
					}
				}
			}
			
			if (!empty($saleInfo['sale_payments'])) {
				foreach ($saleInfo['sale_payments'] as $salePaymentData) {
					if ($salePayment = array_combine($this->mapping_sale_payment, $salePaymentData)) {
						if (isset($salePayment['payment_id'])) {
							unset($salePayment['payment_id']);
						}
						$saleData['payments'][] = $salePayment;
					} else {
						// $this->setError(self::ERROR_MESSAGE . print_r($salePaymentData, true));
						$valid = false;
					}
				}
			}
			
			if (!empty($saleInfo['sale_store_accounts'])) {
				foreach ($saleInfo['sale_store_accounts'] as $saleStoreAccountData) {
					if ($saleStoreAccount = array_combine($this->mapping_store_account, $saleStoreAccountData)) {
						if (isset($saleStoreAccount['sno'])) {
							unset($saleStoreAccount['sno']);
						}
						$saleData['store_accounts'][] = $saleStoreAccount;
					} else {
						// $this->setError(self::ERROR_MESSAGE . print_r($salePaymentData, true));
						$valid = false;
					}
				}
			}
				
			if ($valid) {
				$offlineSales[] = $saleData;
			} else {
				$this->failedRecords[] = $saleData;
			}
		}
	}
	
	public function getFailedRecords() {
		return $this->failedRecords;
	}
	
	public function setData($data = []) {
		$this->data = $data;
		return $this;
	}
	
	public function setError($msg = '') {
		$this->errors[] = $msg;
	}
	
	public function getErrors() {
		return $this->errors;
	}
}