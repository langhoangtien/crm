<?php
require_once (APPPATH . "models/Receiving.php");
class BizReceiving extends Receiving
{
	public function getTransactions($recv_id, $sup_id) {
		$this->db->from('receivings_transactions as st');
		$this->db->where('st.recv_id', $recv_id);
        $this->db->where('st.supplier_id', $sup_id);
		return $this->db->get()->result_array();
	}
	
	function doApproved($rec_id)
	{
	    $this->db->where('receiving_id', $rec_id);
	    $this->db->update('receivings', ['transfer_status' => 'approved']);
	}
	
	public function getSaleForStockIn($search = '')
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
	
		$this->db->from('receivings');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id', 'left');
		$this->db->join('people', 'suppliers.person_id = people.person_id', 'left');
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.suspended', 1);
		$this->db->where_in('receivings.is_stock_in', [0, 2]);
		$this->db->where('receivings.location_id', $location_id);
		$this->db->like('receivings.receiving_id', $search);
		$this->db->order_by('receivings.receiving_id');
		
		$recvs = [];
		foreach ($this->db->get()->result() as $row) {
			$recvs[] = array(
					'receiving_id'=>$row->receiving_id,
					'label' => $row->receiving_id.' ('.$row->receiving_time.')',
					'image' => base_url()."assets/img/item.png" ,
					'category' => '',
					'item_number' => '',
			);
		}
		return $recvs;
	}
	
	public function getDetailReceivingsByLocationId($locationId = 0, $search = []) {
		
		$this->db->select('receivings.*, items.*, receivings_items.*, categories.name as category, receivings_items.description as receivings_items_description');
		$this->db->from('receivings');
		$this->db->join('receivings_items', 'receivings.receiving_id = receivings_items.receiving_id');
		$this->db->join('items', 'items.item_id = receivings_items.item_id');
		$this->db->join('categories', 'categories.id = items.category_id');
		
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.suspended', '0');
		$this->db->where('receivings.location_id', $locationId);
		$this->db->where('receivings.transfer_to_location_id is NULL');
		
		if (!empty($search['start_date'])) {
			$this->db->where('receivings.receiving_time >= ', $search['start_date']);
		}
			
		if (!empty($search['end_date'])) {
			$this->db->where('receivings.receiving_time <= ', $search['end_date']);
		}
		
		$this->db->order_by('receivings.receiving_time');
		
		return $this->db->get()->result_array();
		
	}
	function get_receiving_items_taxes($receiving_id, $line = FALSE)
	{
		$item_where = '';
	
		if ($line)
		{
			$item_where = 'and '.$this->db->dbprefix('receivings_items').'.line = '.$line;
		}
	
		$query = $this->db->query('SELECT name, percent, cumulative, item_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
				'FROM '. $this->db->dbprefix('receivings_items_taxes'). ' JOIN '.
				$this->db->dbprefix('receivings_items'). ' USING (receiving_id, item_id, line) '.
				'WHERE '.$this->db->dbprefix('receivings_items_taxes').".receiving_id = $receiving_id".' '.$item_where.' '.
				'ORDER BY '.$this->db->dbprefix('receivings_items').'.line,'.$this->db->dbprefix('receivings_items').'.item_id,cumulative,name,percent');
				return $query->result_array();
	}
	
	public function getHistoryTransfers ($search = array()) {
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->from('receivings');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id', 'left');
		$this->db->join('people', 'suppliers.person_id = people.person_id', 'left');
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.transfer_status', 'approved');
		
		if (!empty($search['transfer_dimension']) && $search['transfer_dimension'] == 'from')
		{
			$this->db->where('receivings.transfer_to_location_id > 0');
			$this->db->where('receivings.location_id > 0');
			$this->db->where('receivings.location_id', $location_id);
		} elseif (!empty($search['transfer_dimension']) && $search['transfer_dimension'] == 'to') {
			$this->db->where('receivings.transfer_to_location_id > 0');
			$this->db->where('receivings.location_id > 0');
			$this->db->where('receivings.transfer_to_location_id', $location_id);
		} else {
			$this->db->where('receivings.transfer_to_location_id > 0');
			$this->db->where('receivings.location_id > 0');
		}
		
		if (!empty($search['start_date'])) {
			$this->db->where('receiving_time >= ', $search['start_date']);
		}
		
		if (!empty($search['end_date'])) {
			$this->db->where('receiving_time <= ', $search['end_date'] . ' 23:59:59');
		}
		
		$this->db->order_by('receiving_id');
		
		$transferingList = $this->db->get()->result_array();
		
		for($k=0;$k<count($transferingList);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
			$this->db->where('receiving_id', $transferingList[$k]['receiving_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
				
			$transferingList[$k]['items'] = implode(', ', $item_names);
		}
		
		return $transferingList;
	}
	
	public function getHistoryTransfersByAllItems($search = []) {
		$this->db->from('receivings_items');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->join('items', 'receivings_items.item_id = items.item_id');
		
		if (!empty($search['start_date'])) {
			$this->db->where('receiving_time >= ', $search['start_date']);
		}
		
		if (!empty($search['end_date'])) {
			$this->db->where('receiving_time <= ', $search['end_date']);
		}
		
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.transfer_status', 'approved');
		$this->db->where('receivings.transfer_to_location_id > 0');
		$this->db->where('receivings.location_id > 0');
		$this->db->order_by('receiving_time');
		return $this->db->get()->result_array();
	}
	
	public function getAllInfo($recId = 0)
	{
	    $this->db->select("receivings.*, people.*");
	    $this->db->from('receivings');
	    $this->db->join('employees', 'receivings.employee_id = employees.person_id', 'left');
	    $this->db->join('people', 'employees.person_id = people.person_id', 'left');
	    $this->db->where('receivings.deleted', 0);
	    $this->db->where('receivings.receiving_id',$recId);
	    // $this->db->where('receivings.transfer_status', 'pending');
	    // $this->db->where('receivings.transfer_to_location_id > 0');
	    // $this->db->where('receivings.location_id > 0');
	    // $this->db->where('transfer_to_location_id', $location_id);
	    $result = $this->db->get()->result_array();
	    if (isset($result[0])) {
	        $recInfo = $result[0];
	        $item_names = array();
	        $this->db->select('name');
	        $this->db->from('items');
	        $this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
	        $this->db->where('receiving_id', $recInfo['receiving_id']);
	        foreach($this->db->get()->result_array() as $row)
	        {
	            $item_names[] = $row['name'];
	        }
	        $recInfo['items'] = implode(',', $item_names);
	        return $recInfo;
	    }
	    return null;
	}
	
	public function getAllTransferings()
	{		
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();		
		
		$this->db->from('receivings');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id', 'left');
		$this->db->join('people', 'suppliers.person_id = people.person_id', 'left');
		$this->db->where('receivings.deleted', 0);
		// $this->db->where('receivings.transfer_status', 'pending');
		$this->db->where('receivings.transfer_to_location_id > 0');
		$this->db->where('receivings.location_id > 0');
		// $this->db->where('transfer_to_location_id', $location_id);
		$this->db->order_by('receiving_id');

		$transferingList = $this->db->get()->result_array();

		for($k=0; $k<count($transferingList); $k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
			$this->db->where('receiving_id', $transferingList[$k]['receiving_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			
			$transferingList[$k]['items'] = implode('<br/> ', $item_names);
		}
		
		return $transferingList;
	}
	
	public function StockIn($recId = 0, $stockProcess = 1) {
		$this->db->where('receiving_id', $recId);
		$this->db->update('receivings', array('is_stock_in' => $stockProcess));
	}
	
	public function removeTransferPending($recId = 0, $employee_id = -1)
	{
		$this->db->where('receiving_id', $recId);
		if( $this->db->update('receivings', array('deleted' => 1,'deleted_by'=>$employee_id)) )
		{
			$this->db->delete('receivings_items', array('receiving_id' => $recId));
		}
	}

    function get_payment_from_receiving($arrParams = null, $options = null) {
        $this->db->select('*')
                 ->from('receivings_transactions');

        if($arrParams['receiving_id'] > 0) {
            $this->db->where('recv_id', $arrParams['receiving_id']);
        }

        $query = $this->db->get();

        $result = $query->result_array();

        $this->db->flush_cache();
        return $result;
    }

    function get_debt_payment_amount_from_receiving($arrParams = null, $options = null) {
        $result = 0;
        $payments = $this->get_payment_from_receiving($arrParams);

        if(!empty($payments)) {
            foreach($payments as $val) {
                if($val['payment_type'] == 'Sổ ghi nợ')
                    $result = $result + $val['transaction_amount'];
            }
        }

        return $result;
    }

    function get_minus_liability_amount_from_receiving($arrParams = null, $options = null) {
        $result = 0;
        $payments = $this->get_payment_from_receiving($arrParams);

        if(!empty($payments)) {
            foreach($payments as $val) {
                if($val['payment_type'] == 'Trừ công nợ')
                    $result = $result + $val['transaction_amount'];
            }
        }

        return $result;
    }

    public function get_payment_total_from_receiving($arrParams = null) {
        $result = 0;
        $this->db->select("t.transaction_amount AS payment_amount_total")
                 ->from('receivings_transactions AS t')
                 ->join('receivings AS r', 'r.receiving_id = t.recv_id')
                 ->where('r.receiving_id', $arrParams['receiving_id'])
                 ->where('r.deleted', 0);

        $query = $this->db->get();

        $result_tmp = $query->row_array();
        $this->db->flush_cache();

        if(!empty($result_tmp)) {
            $result = $result_tmp['payment_amount_total'];
        }else
            $result = 0;

        return $result;
    }

	public function save ($data,$supplier_id,$employee_id,$comment,$payment_type,$receiving_id=false, $suspended = 0, $mode='receive',$change_receiving_date = false, $is_po = 0, $location_id=-1, $return = 0, $store_account_payment = 0, $is_vat = 0,$suppl_info = NULL)
	{

        if($receiving_id == false){
            $is_insert = true;
        }
        else {
            $old_debit_payment         = $this->get_debt_payment_amount_from_receiving(array('receiving_id'=>$receiving_id));
            $old_receiving_info        = $this->receiving_lib->get_receive($receiving_id);
            $old_receiving_total_value = $old_receiving_info['gia_tri_don_hang'];
            $old_payment_total         = $this->get_payment_total_from_receiving(array('receiving_id'=>$receiving_id));


            $is_insert = false;
        }

		$items = $data['cart'];
		$data['amount_tendered'] = isset($data['amount_tendered']) ? $data['amount_tendered'] : $data['total'];
		
		if(count($items)==0)
			return -1;

		//we need to check the sale library for deleted taxes during sale
		$this->load->library('receiving_lib');

		$deleted_taxes = $this->receiving_lib->get_deleted_taxes();

		$receivings_data = array(
		'supplier_id'=> $supplier_id > 0 ? $supplier_id : null,
		'employee_id'=>$employee_id,
		'payment_type'=>$payment_type,
		'no_dau'=>isset($data['no_dau'])?$data['no_dau']:0,
		'no_cuoi'=>isset($data['no_cuoi'])?$data['no_cuoi']:0,
		'comment'=>$comment,
		'suspended' => $suspended,
		'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
		'transfer_to_location_id' => $location_id > 0 ? $location_id : NULL,
		'deleted' => 0,
		'deleted_by' => NULL,
		'deleted_taxes' =>  $deleted_taxes? serialize($deleted_taxes) : NULL,
		'is_po' => $is_po,
		'return' => $return,
		'store_account_payment' => $store_account_payment,
		'is_vat' => $is_vat,
		'task_id' =>$data['task_id']

		);
			
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		if($change_receiving_date) 
		{
			$receiving_time = strtotime($change_receiving_date);
			if($receiving_time !== FALSE)
			{
				$receivings_data['receiving_time']=date('Y-m-d H:i:s', strtotime($change_receiving_date));
			}
		}
		else
		{
			$receivings_data['receiving_time'] = date('Y-m-d H:i:s');			
		}
		
		
		$isUpdateQty = true;
		
		if ($receiving_id)
		{
			$previous_receiving_items = $this->get_receiving_items($receiving_id)->result_array();
			//Delete previoulsy receving so we can overwrite data
			
			$recvInfo = $this->Receiving->get_info($receiving_id)->row();
			
			if(!empty($recvInfo->is_stock_in))
			{
				$isUpdateQty = false;
			}
			
			if(!empty($recvInfo->is_stock_in) && $recvInfo->is_stock_in == 2) {
				$receivings_data['suspended'] = $recvInfo->suspended;
			}
			
			$this->delete($receiving_id, true, $isUpdateQty);
			
			$this->db->where('receiving_id', $receiving_id);
			$this->db->update('receivings', $receivings_data);

            $is_insert = false;
		}
		else
		{
			$previous_receiving_items = array();
			$this->db->insert('receivings',$receivings_data);
			$receiving_id = $this->db->insert_id();
		}

        $config_adjusted_cost_price = $this->config->item('config_adjusted_cost_price');
        if(!empty($config_adjusted_cost_price))
            $config_adjusted_cost_price = unserialize($config_adjusted_cost_price);
        else
            $config_adjusted_cost_price = array();

		foreach($items as $line=>$item)
		{
            if($return == 1) {
                $item['quantity'] = $item['quantity'] * (-1);
                if(!empty($item['quantity_received']))
                    $item['quantity_received'] = $item['quantity_received'] * (-1);
            }

			$cur_item_info = $this->Item->get_info($item['item_id']);
			$cur_item_location_info = $this->Item_location->get_info($item['item_id']);
			$cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
			
			$qtyOriginal = $item['quantity'];
			$qtyOriginalReceived = $item['quantity_received'];
			
			if( $cur_item_info->measure_id != $item['measure_id'] /* && ($mode == 'receive' || $mode == 'purchase_order') */)
			{
				$convertedValue = $this->ItemMeasures->getConvertedValue($item['item_id'], $item['measure_id']);
				$cost_price = $cost_price * (int)$convertedValue->cost_price_percentage_converted / 100;
				$unit_price = $item['price'] * (int)$convertedValue->unit_price_percentage_converted / 100;
				$totalQty = $item['quantity'] = $item['quantity'] * (int)$convertedValue->qty_converted;
				$item['quantity_received'] = $item['quantity_received'] * (int)$convertedValue->qty_converted;
				$item['price'] = $unit_price / (int)$convertedValue->qty_converted;
			}
			
			$item_unit_price_before_tax = $item['price'];
			
			$expire_date = NULL;
			
			if ($item['expire_date'])
			{
				$expire_date = date('Y-m-d', strtotime($item['expire_date']));				
			}
			
			$quantity_received = 0;
			
			if ($suspended != 0 && $item['quantity_received'] !== NULL)
			{
				$quantity_received = $item['quantity_received'];
			}
			elseif($suspended==0)
			{
				$quantity_received = $item['quantity'];
			}

            if($store_account_payment == 1) {
                $item['item_cost_price'] = $item['price'];
            }
			
			$receivings_items_data = array
			(
				'receiving_id'=>$receiving_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'], // qty is converted to base measure
				'quantity_received'=>$quantity_received,
				'measure_id' => $item['measure_id'],
				'measure_qty' => $qtyOriginal, // qty by measure
				'measure_qty_received' => $qtyOriginalReceived, // qty by measure
				'discount_percent'=>$item['discount'],
				// 'item_cost_price' => $cost_price,
				'item_cost_price' => 0,
				'item_unit_price'=>$item['price'],
				'expire_date' => $expire_date,
			);

            if(in_array($item['item_id'], $config_adjusted_cost_price)) {
                $receivings_items_data['item_cost_price'] = $receivings_items_data['item_unit_price'];

            }

			$this->db->insert('receivings_items',$receivings_items_data);

			// TODO
			if ($suspended == 0 && $mode != 'transfer' && $store_account_payment == 0)
			{
				if ($this->config->item('calculate_average_cost_price_from_receivings'))
				{
					$receivings_items_data['item_unit_price_before_tax'] = $item_unit_price_before_tax;
					$this->calculate_and_update_average_cost_price_for_item($item['item_id'], $receivings_items_data);
					unset($receivings_items_data['item_unit_price_before_tax']);
				}
			}
			
			//Update stock quantity IF not a service item
			// TODO -- HERE
			if (!$cur_item_info->is_service && $mode != 'transfer' )
			{
				//If we have a null quanity set it to 0, otherwise use the value
				$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
				
				//This means we never adjusted quantity_received so we should accept all
				if ($suspended == 0 && $item['quantity_received'] === NULL)
				{
					$inventory_to_add = $item['quantity'];
				}
				else
				{
					if ($suspended == 0)
					{

						//Editing sale; doesn't have option to partial receive
						if ($this->receiving_lib->get_change_recv_id())
						{
							$inventory_to_add = $item['quantity'];
						}
						else
						{
							if($return == 1){
								if($item['quantity']<0){
									$inventory_to_add = $item['quantity'];
								} else {
									$inventory_to_add = -$item['quantity'];
								}

								$comment = 'Trả lại hàng cho nhà cung cấp - xuất kho ';
							} else {
								$previous_amount_received = $this->_get_quantity_received($previous_receiving_items, $item['item_id']);
								$inventory_to_add = $previous_amount_received + $item['quantity'] - $item['quantity_received'];
							}

						}
					}
					else
					{
						$inventory_to_add = $item['quantity_received'];
					}
					
				}
				// abc
				if ($isUpdateQty && $inventory_to_add !=0)
				{
					$this->Item_location->save_quantity($cur_item_location_info->quantity + $inventory_to_add, $item['item_id']);
					$recv_remarks ='RECV '.$receiving_id;
					if(empty($comment)) 
						// $comment = '... '.$recv_remarks;
						$comment = "Nhập hàng trực tiếp cho nhà cung cấp".'_'.$recv_remarks;
					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$item['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$comment,
						'trans_inventory'=>$inventory_to_add,
						'location_id'=>$this->Employee->get_logged_in_employee_current_location_id()
					);
					$this->Inventory->insert($inv_data);
				}
			}
				
			// echo $return;
			// 		echo 'vao day';die;
			if ($this->config->item('charge_tax_on_recv') && $mode != 'transfer' && $store_account_payment == 0)
			{
				foreach($this->Item_taxes_finder->get_info($item['item_id'],'receiving') as $row)
				{
					$tax_name = $row['percent'].'% ' . $row['name'];
	
					//Only save sale if the tax has NOT been deleted
					if (!in_array($tax_name, $this->receiving_lib->get_deleted_taxes()))
					{	
						$this->db->insert('receivings_items_taxes', array(
							'receiving_id' 	=>$receiving_id,
							'item_id' 	=>$item['item_id'],
							'line'      =>$item['line'],
							'name'		=>$row['name'],
							'percent' 	=>$row['percent'],
							'cumulative'=>$row['cumulative']
						));
					}
				}
			}
		}
		$transaction_amount = 0;
        $debt_amount        = 0;
		if (!empty($data['payments'])) {
			// Remove old payments type
            if($is_insert == false) {
                $this->db->delete('receivings_transactions', array('recv_id' => $receiving_id));
            }
			
			foreach ($data['payments'] as $payment) {
                $transaction_amount = $transaction_amount + $payment['amount'];

				$receivingTransactionsData = [
						'recv_id' => $receiving_id,
						'transaction_amount' => $payment['amount'],
						'datetime' => date('Y-m-d H:i:s'),
						'payment_type' => $payment['payment_type'],
						'comment' => $data['comment'],
				];

                $receivingTransactionsData['balance'] = 0;
				
				if (!empty($data['supplier_id'])) {
					$receivingTransactionsData['supplier_id'] = $data['supplier_id'];
				}

				$this->ReceivingsTransactions->save($receivingTransactionsData);
			}

            $debit_payment   = $this->receiving_lib->get_debit_payment();
            $receiving_total = $this->receiving_lib->get_total();
            $payment_total   = $this->receiving_lib->get_payment_total();

            if($this->receiving_lib->get_mode() == 'receive' || $this->receiving_lib->get_mode == 'purchase_order') {
                if($is_insert == false) {
                    if($old_debit_payment != $debit_payment) {
                        $new_balance = $suppl_info->balance - $old_debit_payment + $debit_payment;

                        $this->db->where('person_id', $suppl_info->person_id);
                        $this->db->update('suppliers',array('balance'=>$new_balance));

                        $store_supplier_accounts_data = array(
                            'supplier_id'        => $supplier_id,
							'receiving_id'       => $receiving_id,
                            'transaction_amount' => $debit_payment - $old_debit_payment,
							'date'               => date('Y-m-d H:i:s'),
                            'balance'            => $new_balance,
							'balance_2'          => $suppl_info->balance_2,
                            'options'            => 1,
							'comment'            => 'Sửa đơn hàng'
                        );

                        $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);
                    }

                    $subtraction_1 = $payment_total - $receiving_total;
                    $subtraction_2 = $old_payment_total - $old_receiving_total_value;
                    if($subtraction_1 != $subtraction_2) {
                        if($payment_total > $receiving_total) {
                            if($old_payment_total > $old_receiving_total_value) {
                                $transaction_amount = - ($old_payment_total - $old_receiving_total_value) + ($payment_total - $receiving_total);
                                $new_balance_2 = $suppl_info->balance_2 + $transaction_amount;
                                $comment_account_transaction = 'Sửa đơn hàng - Thanh toán dư tiền';
                            }else {
                                $transaction_amount = $payment_total - $receiving_total;
                                $new_balance_2 = $suppl_info->balance_2 + $transaction_amount;
                                $comment_account_transaction = 'Sửa đơn hàng';
                            }

                            $this->db->where('person_id', $suppl_info->person_id);
                            $this->db->update('suppliers',array('balance_2'=>$new_balance_2));

                            $store_supplier_accounts_data = array(
                                'supplier_id'        => $supplier_id,
								'receiving_id'       => $receiving_id,
                                'transaction_amount' => $transaction_amount,
								'date'               => date('Y-m-d H:i:s'),
                                'balance'            => $suppl_info->balance,
								'balance_2'          => $new_balance_2,
                                'options'            => 2,
								'comment'            => $comment_account_transaction
                            );

                            $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);

                        }else {
                            if($old_payment_total > $old_receiving_total_value) {
                                $transaction_amount = $old_receiving_total_value - $old_payment_total;
                                $new_balance_2 = $suppl_info->balance_2 + $transaction_amount;
                                $comment_account_transaction = 'Sửa đơn hàng';

                                $this->db->where('person_id', $suppl_info->person_id);
                                $this->db->update('suppliers',array('balance_2'=>$new_balance));

                                $store_supplier_accounts_data = array(
                                    'supplier_id'        => $supplier_id,
									'receiving_id'       => $receiving_id,
                                    'transaction_amount' => $transaction_amount,
									'date'               => date('Y-m-d H:i:s'),
                                    'balance'            => $suppl_info->balance,
									'balance_2'          => $new_balance_2,
                                    'options'            => 2,
									'comment'            => $comment_account_transaction
                                );

                                $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);
                            }
                        }
                    }

                }
                else {
                    if($debit_payment > 0) {
                        $new_balance = $suppl_info->balance + $debit_payment;
                        $this->db->where('person_id', $suppl_info->person_id);
                        $this->db->update('suppliers',array('balance'=>$new_balance));

                        $store_supplier_accounts_data = array(
                            'supplier_id'        => $supplier_id,
							'receiving_id'       => $receiving_id,
                            'transaction_amount' => $debit_payment,
							'date'               => date('Y-m-d H:i:s'),
                            'balance'            => $new_balance,
							'balance_2'          => $suppl_info->balance_2,
                            'options'            => 1,
							'comment'  => ''
                        );

                        $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);
                    }

                    if($payment_total > $receiving_total) {
                        $new_balance_2 = $suppl_info->balance_2 + ($payment_total - $receiving_total);

                        $this->db->where('person_id', $suppl_info->person_id);
                        $this->db->update('suppliers',array('balance_2'=>$new_balance_2));

                        $store_supplier_accounts_data = array(
                            'supplier_id'        => $supplier_id,
							'receiving_id'       => $receiving_id,
                            'transaction_amount' => $payment_total - $receiving_total,
							'date'               => date('Y-m-d H:i:s'),
                            'balance'            => $suppl_info->balance,
							'balance_2'          => $new_balance_2,
                            'options'            => 2,
							'comment'            => lang('receivings_balances'),
                        );

                        $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);
                    }
                }
            }elseif($this->receiving_lib->get_mode() == 'return') {

                if($is_insert == false) {
                    if($old_debit_payment != $debit_payment) {
                        $new_balance_2 = $suppl_info->balance_2 - $old_debit_payment + $debit_payment;

                        $this->db->where('person_id', $suppl_info->person_id);
                        $this->db->update('suppliers',array('balance_2'=>$new_balance_2));

                        $store_supplier_accounts_data = array(
                            'supplier_id'        => $supplier_id,
							'receiving_id'       => $receiving_id,
                            'transaction_amount' => $debit_payment - $old_debit_payment,
							'date'               => date('Y-m-d H:i:s'),
                            'balance'            => $suppl_info->balance,
							'balance_2'          => $new_balance_2,
                            'options'            => 2,
							'comment'            => 'Sửa đơn hàng'
                        );

                        $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);
                    }


                }else {
                    if($debit_payment > 0) {
                        $new_balance_2 = $suppl_info->balance_2 + $debit_payment;
                        $this->db->where('person_id', $suppl_info->person_id);
                        $this->db->update('suppliers',array('balance_2'=>$new_balance_2));

                        $store_supplier_accounts_data = array(
                            'supplier_id'        => $supplier_id,
							'receiving_id'       => $receiving_id,
                            'transaction_amount' => $debit_payment,
							'date'               => date('Y-m-d H:i:s'),
                            'balance'            => $suppl_info->balance,
							'balance_2'          => $new_balance_2,
                            'options'            => 2,
							'comment'            => ''
                        );

                        $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);
                    }
                }
            }
		}

        // thanh toán công nợ
        if($supplier_id > 0 && $store_account_payment) {
            // update balance
            $store_account_payment_amount = $this->receiving_lib->get_total();

            if($store_account_payment == 1) {
                $new_balance = $suppl_info->balance - $store_account_payment_amount;

                $this->db->where('person_id', $suppl_info->person_id);
                $this->db->update('suppliers',array('balance'=>$new_balance));

            }else {
                $new_balance_2 = $suppl_info->balance_2 - $store_account_payment_amount;

                $this->db->where('person_id', $suppl_info->person_id);
                $this->db->update('suppliers',array('balance_2'=>$new_balance_2));
            }

            $store_supplier_accounts_data = array(
                'supplier_id'        => $supplier_id,
				'receiving_id'       => $receiving_id,
                'transaction_amount' => -$store_account_payment_amount,
				'date'               => date('Y-m-d H:i:s'),
                'balance'            => $this->Supplier->get_info($supplier_id)->balance,
				'balance_2'          => $this->Supplier->get_info($supplier_id)->balance_2,
                'options'            => $store_account_payment,
				'comment'            => ''
            );

            $this->db->insert('store_supplier_accounts', $store_supplier_accounts_data);

            $receiving_store_payment = $this->receiving_lib->get_receiving_store_payment();
            if(!empty($receiving_store_payment)) {
                $sno_id = $this->db->insert_id();
                $receiving_store_account_data = array();
                foreach($receiving_store_payment as $key => $amount_value) {
                    $tmp = array();
                    $tmp['sno_id']       = $sno_id;
                    $tmp['receiving_id'] = $key;
                    $tmp['amount']       = $amount_value;

                    $receiving_store_account_data[] = $tmp;
                }

                $this->db->insert_batch('receivings_store_supplier_accounts',$receiving_store_account_data);
            }
        }

		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}

		return $receiving_id;
	}
	
	public function approvedTransfer(
		$items, // 1
		$supplier_id, // 2
		$employee_id, // 3
		$comment, // 4
		$payment_type, // 5
		$receiving_id=false, // 6 
		$change_receiving_date = false, // 7
		$is_po = 0, // 8
		$location_from_id=-1, // 9
        $location_to_id = -1 //10
	)
	{
		if(count($items)==0)
			return -1;
	
		//we need to check the sale library for deleted taxes during sale
		$this->load->library('receiving_lib');
		$deleted_taxes = $this->receiving_lib->get_deleted_taxes();

		$receivings_data = array(
				'supplier_id'=> $supplier_id > 0 ? $supplier_id : null,
				'employee_id'=>$employee_id,
				'payment_type'=>$payment_type,
				'comment'=>$comment,
				'location_id' => $location_from_id,
				'transfer_to_location_id' => $location_to_id,
				'deleted' => 0,
				'deleted_by' => NULL,
				'deleted_taxes' =>  $deleted_taxes? serialize($deleted_taxes) : NULL,
				'is_po' => $is_po,
		);
				
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		if($change_receiving_date)
		{
			$receiving_time = strtotime($change_receiving_date);
			if($receiving_time !== FALSE)
			{
				$receivings_data['receiving_time']=date('Y-m-d H:i:s', strtotime($change_receiving_date));
			}
		}
		else
		{
			$receivings_data['receiving_time'] = date('Y-m-d H:i:s');
		}
	
	
		$previous_receiving_items = $this->get_receiving_items($receiving_id)->result_array();
		//Delete previoulsy receving so we can overwrite data
		// TODO
		$this->db->where('receiving_id', $receiving_id);
		$this->db->update('receivings', array('transfer_status' => 'approved'));

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);
			
			
			$cur_item_location_info = $this->Item_location->get_info($item['item_id'], $location_from_id);
			$cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
			
			if( $cur_item_info->measure_id != $item['measure_id'] /* && ($mode == 'receive' || $mode == 'purchase_order') */)
			{
				$convertedValue = $this->ItemMeasures->getConvertedValue($item['item_id'], $item['measure_id']);
				$cost_price = $cost_price * (100 + (int)$convertedValue->cost_price_percentage_converted ) / 100;
				$totalQty = $item['quantity'] = $item['quantity'] * (int)$convertedValue->qty_converted;
				$item['price'] = $item['price'] / (int)$convertedValue->qty_converted;
			}
			
			$item_unit_price_before_tax = $item['price'];
				
			$expire_date = NULL;
				
			if ($item['expire_date'])
			{
				$expire_date = date('Y-m-d', strtotime($item['expire_date']));
			}
				
			$quantity_received = 0;
				
			if ($item['quantity_received'] !== NULL)
			{
				$quantity_received = $item['quantity_received'];
			}
			else
			{
				$quantity_received = $item['quantity'];
			}

			$receivings_items_data = array
			(
				'receiving_id'=>$receiving_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'],
				'quantity_received'=>$quantity_received,
				'discount_percent'=>$item['discount'],
				'item_cost_price' => $cost_price,
				'item_unit_price'=>$item['price'],
				'expire_date' => $expire_date,
			);
	
			// TODO
			if ($this->config->item('calculate_average_cost_price_from_receivings'))
			{
				$receivings_items_data['item_unit_price_before_tax'] = $item_unit_price_before_tax;
				$this->calculate_and_update_average_cost_price_for_item($item['item_id'], $receivings_items_data);
				unset($receivings_items_data['item_unit_price_before_tax']);
			}
				
			//Update stock quantity IF not a service item
			// TODO -- HERE
			if (!$cur_item_info->is_service)
			{
				//If we have a null quanity set it to 0, otherwise use the value
				$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
				//This means we never adjusted quantity_received so we should accept all
				if ($item['quantity_received'] === NULL)
				{
					$inventory_to_add = $item['quantity'];
				}
				else
				{
					//Editing sale; doesn't have option to partial receive
					if ($this->receiving_lib->get_change_recv_id())
					{
						$inventory_to_add = $item['quantity'];
					}
					else
					{
						$previous_amount_received = $this->_get_quantity_received($previous_receiving_items, $item['item_id']);
						$inventory_to_add = $previous_amount_received;
			
					}
				}
				// HERE YOU ARE!
				#----------------------------------------------------------------------------------#	
										
										# Thực hiện Nhập kho
			
				#----------------------------------------------------------------------------------#	
				if ($inventory_to_add !=0)
				{
						$location_quantity = 0;
					$is_insert         = true;
					if($this->Item_location->get_location_quantity($item['item_id'],$location_to_id)!= null && !empty($this->Item_location->get_location_quantity($item['item_id'],$location_to_id)))
					{
						$location_quantity = $this->Item_location->get_location_quantity($item['item_id'],$location_to_id);
						$is_insert = false;
						
					}
					$this->Item_location->update_chuyen_kho_noi_bo($location_quantity + $item['quantity'],$item['item_id'], $location_to_id,$is_insert);
					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$item['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>'Nhập kho chuyển kho nội bộ',
						'trans_inventory'=>-$inventory_to_add,
						'location_id'=>$location_from_id
					);
					$this->Inventory->insert($inv_data);
				}
			}

			#----------------------------------------------------------------------------------#	
										
										# thực hiện xuất kho

			#----------------------------------------------------------------------------------#	

			// TODO
			if($location_to_id && $cur_item_location_info->quantity !== NULL && !$cur_item_info->is_service)
			{

				$this->Item_location->update_chuyen_kho_noi_bo($this->Item_location->get_location_quantity($item['item_id'],$location_from_id) + ($item['quantity'] * -1),$item['item_id'],$location_from_id);

				if (!isset($inv_data))
					{
						$inv_data = array
						(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item['item_id'],
							'trans_user'=>$employee_id,
							'trans_comment'=>'Xuất kho chuyển kho nội bộ',
						);
				}

				//Change values from $inv_data above and insert
				$inv_data['trans_comment'] = 'Xuất kho chuyển kho nội bộ';
				$inv_data['trans_inventory'] = $item['quantity'] * 1;
				$inv_data['location_id']=$location_to_id;
				$this->Inventory->insert($inv_data);
			}

			if ($this->config->item('charge_tax_on_recv'))
			{
				foreach($this->Item_taxes_finder->get_info($item['item_id'],'receiving') as $row)
				{
					$tax_name = $row['percent'].'% ' . $row['name'];

					//Only save sale if the tax has NOT been deleted
					if (!in_array($tax_name, $this->receiving_lib->get_deleted_taxes()))
					{
						$this->db->insert('receivings_items_taxes', array(
								'receiving_id' 	=>$receiving_id,
								'item_id' 	=>$item['item_id'],
								'line'      =>$item['line'],
								'name'		=>$row['name'],
								'percent' 	=>$row['percent'],
								'cumulative'=>$row['cumulative']
						));
					}
				}
			}
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		return $receiving_id;
	}
	function get_temp_table_data()
	{
		return $this->db->get('receivings_items_temp')->result_array();
	}
	public function create_receivings_items_temp_table($params)
	{
		set_time_limit(0);
	
	
		$location_ids = implode(',',Report::get_selected_location_ids());
	
		$where = '';
	
		if (isset($params['start_date']) && isset($params['end_date']))
		{
			$where = 'WHERE receiving_time BETWEEN "'.$params['start_date'].'" and "'.$params['end_date'].'"'.' and '.$this->db->dbprefix('receivings').'.location_id IN ('.$location_ids.')';
			//Added for detailed_suspended_report, we don't need this for other reports as we are always going to have start + end date
			if (isset($params['force_suspended']) && $params['force_suspended'])
			{
				$where .=' and suspended != 0';
			}
			elseif ($this->config->item('hide_suspended_recv_in_reports'))
			{
				$where .=' and suspended = 0';
			}
		}
		else
		{
			//If we don't pass in a date range, we don't need data from the temp table
			$where = 'WHERE location_id IN ('.$location_ids.')';
				
			if ($this->config->item('hide_suspended_recv_in_reports'))
			{
				$where .=' and suspended = 0';
			}
		}
		if (isset($params['supplier_id']) && $params['supplier_id']!= -1)
		{
			$supplier_id = $params['supplier_id'];
			$where .=' and '.$this->db->dbprefix('receivings').'.supplier_id='.$supplier_id;
		}
		if (!empty($params['sale_type']) && $params['sale_type']!= 'all')
		{
			$sale_type = $params['sale_type'];
			if($sale_type == 'sales')
			{
				$return = 0;
			}
			elseif($sale_type == 'returns')
			{
				$return = 1;
			}
			$where .=' and '.$this->db->dbprefix('receivings').'.return='.$return;
		}
	
		
		$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;

		$this->db->query("CREATE TEMPORARY TABLE ".$this->db->dbprefix('receivings_items_temp')."
		(SELECT ".$this->db->dbprefix('receivings').".location_id as location_id, ".$this->db->dbprefix('receivings').".deleted as deleted,".$this->db->dbprefix('receivings').".deleted_by as deleted_by, receiving_time, date(receiving_time) as receiving_date, ".$this->db->dbprefix('receivings_items').".receiving_id, comment,".$this->db->dbprefix('receivings').".payment_type as payment_type,".$this->db->dbprefix('receivings').".employee_id,
		".$this->db->dbprefix('items').".item_id, ".$this->db->dbprefix('receivings').".supplier_id,".$this->db->dbprefix('receivings').".return, quantity_purchased,quantity_received, ". $this->db->dbprefix('receivings_items') . ".measure_id, ".$this->db->dbprefix('receivings_items').".measure_qty, ".$this->db->dbprefix('receivings_items').".measure_qty_received, item_cost_price, item_unit_price,".$this->db->dbprefix('categories').".name as category,".$this->db->dbprefix('categories').".id as category_id,
		discount_percent, ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100),".$decimals.") as subtotal,
		(ROUND(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100,".$decimals."))+(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as total,
		(item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100)
		+(((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100)*(SUM(CASE WHEN cumulative != 1 THEN percent ELSE 0 END)/100) + (item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100))
		*(SUM(CASE WHEN cumulative = 1 THEN percent ELSE 0 END))/100) as tax,
		ROUND((item_unit_price*quantity_purchased-item_unit_price*quantity_purchased*discount_percent/100),".$decimals.") - (item_cost_price*quantity_purchased) as profit,
		".$this->db->dbprefix('receivings_items').".line as line, serialnumber, ".$this->db->dbprefix('receivings_items').".description as description
		FROM ".$this->db->dbprefix('receivings_items')."
		INNER JOIN ".$this->db->dbprefix('receivings')." ON  ".$this->db->dbprefix('receivings_items').'.receiving_id='.$this->db->dbprefix('receivings').'.receiving_id'."
		INNER JOIN ".$this->db->dbprefix('items')." ON  ".$this->db->dbprefix('receivings_items').'.item_id='.$this->db->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('receivings_items_taxes')." ON  "
				.$this->db->dbprefix('receivings_items').'.receiving_id='.$this->db->dbprefix('receivings_items_taxes').'.receiving_id'." and "
				.$this->db->dbprefix('receivings_items').'.item_id='.$this->db->dbprefix('receivings_items_taxes').'.item_id'." and "
				.$this->db->dbprefix('receivings_items').'.line='.$this->db->dbprefix('receivings_items_taxes').'.line'. "
		LEFT OUTER JOIN ".$this->db->dbprefix('categories')." ON  ".$this->db->dbprefix('categories').'.id='.$this->db->dbprefix('items').'.category_id'." 
	
		
		$where
				GROUP BY ".$this->db->dbprefix('receivings').".receiving_id, item_id, line)");

				
	}
	
	function getMeasureOnRecvItem($recvId, $ItemId)
	{
		$this->db->from('receivings_items');
		$this->db->join('measures', 'measures.id = receivings_items.measure_id', 'left');
		$this->db->where('receiving_id', $recvId);
		$this->db->where('item_id', $ItemId);
		$result = $this->db->get();
		if($result->num_rows() > 0)
		{
			$row = $result->result();
			return $row[0];
		}
	
		return FALSE;
	}

    public function getInfo($receiving_id)
    {
        $this->db->from('receivings');
        $this->db->where('receiving_id',$receiving_id);
        $result = $this->db->get()->result_array();

        if (isset($result[0])) {
            return $result[0];
        }
        return null;
    }

    function get_receiving_info($receiving_id, $options = null) {
        if($options == null) {
            $this->db -> select('*')
                      -> from('receivings')
                      -> where('receiving_id', $receiving_id);

            $query = $this->db->get();

            $result = $query->row_array();

            $this->db->flush_cache();
        }

        return $result;
    }

    function count_item($arrParams = null, $options = null) {
        switch ($options['task']) {
            case 'vat_order':
               $result = $this->count_vat_order($arrParams);
                break;
            default:
                $location_id 	= $this->Employee->get_logged_in_employee_current_location_id();
                $receive_prefix = $this->config->item('receive_prefix');

                $this->db -> select('COUNT(r.receiving_id) AS totalItem')
                          -> from('receivings AS r')
                          -> where('r.location_id', $location_id)
                          -> where('r.deleted', 0);

                if($options['task'] == 'suspended')
                    $this->db->where('r.suspended', 1);

                if(!empty($arrParams['keywords'])) {
                    $keywords = trim($arrParams['keywords']);
                    $this->db->where("CONCAT_WS('$receive_prefix', ' ', r.receiving_id) LIKE '%$keywords%'");

                    $_SESSION['receivings_model_filter']['keywords'] = $keywords;
                }

                if(!empty($arrParams['start_date'])) {
                    $start_date = date("Y-m-d", strtotime($arrParams['start_date'])) . ' 00:00:00';
                    $this->db->where("r.receiving_time >= '$start_date'");

                    $_SESSION['receivings_model_filter']['start_date'] = $arrParams['start_date'];
                }else
                    $_SESSION['receivings_model_filter']['start_date'] = '';

                if(!empty($arrParams['end_date'])) {
                    $end_date = date("Y-m-d", strtotime($arrParams['end_date'])) . ' 23:59:59';
                    $this->db->where("r.receiving_time <= '$end_date'");

                    $_SESSION['receivings_model_filter']['end_date'] = $arrParams['end_date'];
                }else
                    $_SESSION['receivings_model_filter']['end_date'] = '';

                $query = $this->db->get();

                $result = $query->row()->totalItem;

                $this->db->flush_cache();

        }
        return $result;
    }

    function count_vat_order($arrParams = null) {
        $location_id 	= $this->Employee->get_logged_in_employee_current_location_id();
        $receive_prefix = $this->config->item('receive_prefix');

        $this->_sale_tmp_ids = array();

        $supplier_id = $arrParams['supplier_id'];
        $this->db -> select('sv.sale_id')
                  -> from('receivings_vat_relationships AS rv')
                  -> join('receivings AS r', 'rv.receiving_id = r.receiving_id AND r.is_vat = 1')
                  -> where('r.supplier_id', $supplier_id);

        $query = $this->db->get();

        $result_tmp = $query->result_array();
        $this->db->flush_cache();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $this->_receiving_tmp_ids[] = $val['receiving_id'];
        }

        $this->db -> select('COUNT(r.receiving_id) AS total_item')
                  -> select("DATE_FORMAT(r.receiving_id, '%d-%m-%Y %H:%i') AS receiving_time_format", FALSE)
                  -> from('receivings AS r')
                  -> where('r.suspended', 0)
                  -> where('r.store_account_payment', 0)
                  -> where('r.is_vat', 0)
                  -> where('r.location_id', $location_id)
                  -> where('r.deleted', 0)
                  -> where('r.supplier_id', $supplier_id)
                  -> where('(SELECT SUM(transaction_amount) FROM phppos_receivings_transactions WHERE recv_id = r.receiving_id) > (SELECT SUM(ttotal) FROM phppos_receivings_items_temp WHERE receiving_id = r.receiving_id)');

        if(!empty($this->_receiving_tmp_ids)) {
            $this->db->where('r.receiving_id NOT IN ('.implode(', ', $this->_receiving_tmp_ids).')');
        }

        $query = $this->db->get();

        $result = $query->row()->total_item;

        $this->db->flush_cache();

    }

    function list_item($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $paginator         = $arrParams['paginator'];
            $receive_prefix    = $this->config->item('receive_prefix');
            $location_id = $this->Employee->get_logged_in_employee_current_location_id();

            $this->db->select("r.*,CONCAT('$receive_prefix', ' ', r.receiving_id) as ma_don_nhap_hang")
                     ->select("DATE_FORMAT(r.receiving_time, '%d-%m-%Y %H:%i:%s') AS receiving_time_format", FALSE)
                     ->from('receivings AS r')
                     ->where('r.location_id', $location_id)
                     ->where('r.deleted', 0);

            if($options['task'] == 'suspended')
                $this->db->where('r.suspended', 1);

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where("CONCAT_WS('$receive_prefix', ' ', r.receiving_id) LIKE '%$keywords%'");

                $_SESSION['receivings_model_filter']['keywords'] = $keywords;
            }

            if(!empty($arrParams['start_date'])) {
                $start_date = date("Y-m-d", strtotime($arrParams['start_date'])) . ' 00:00:00';
                $this->db->where("r.receiving_time >= '$start_date'");

                $_SESSION['receivings_model_filter']['start_date'] = $arrParams['start_date'];
            }else
                $_SESSION['receivings_model_filter']['start_date'] = '';

            if(!empty($arrParams['end_date'])) {
                $end_date = date("Y-m-d", strtotime($arrParams['end_date'])) . ' 23:59:59';
                $this->db->where("r.receiving_time <= '$end_date'");

                $_SESSION['receivings_model_filter']['end_date'] = $arrParams['end_date'];
            }else
                $_SESSION['receivings_model_filter']['end_date'] = '';


            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);

                $_SESSION['receivings_model_filter']['col']   = $arrParams['col'];
                $_SESSION['receivings_model_filter']['order'] = $arrParams['order'];
            }

            $_SESSION['receivings_model_filter']['current_page'] = $page = $arrParams['page'];

            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();

            $result = $query->result_array();

            $this->db->flush_cache();
            if(!empty($result)) {
                $result = $this->do_receiving_info($result, $options);
            }
        }else {
            switch ($options['task']) {
                case 'vat_order':
                    $result = $this->list_item_vat_order($arrParams);
                    break;

                default:

            }
        }

        return $result;
    }

    function list_item_vat_order($arrParams = null) {
        $paginator         = $arrParams['paginator'];
        $receive_prefix    = $this->config->item('receive_prefix');
        $location_id = $this->Employee->get_logged_in_employee_current_location_id();

        $supplier_id = $arrParams['supplier_id'];

    }

    function do_receiving_info($result, $options) {
        if(!empty($result)) {
            $item_model      = $this->model_load_model('Item');
        	$supplier_model   = $this->model_load_model('Supplier');
            $currency_symbol = $this->config->item('currency_symbol');
            $supplier_ids = array();

            foreach($result as $val) {
                $receiving_ids[] = $val['receiving_id'];
                if($val['supplier_id'] > 0)
                    $supplier_ids[] = $val['supplier_id'];
            }

            $supplier_ids = array_unique($supplier_ids);
            if(count($supplier_ids) > 0) {
                $suppliers_info = $supplier_model->get_info_by_ids($supplier_ids);
            }

            $this->db->select('i.name, i.product_id, i.category_id, ri.quantity_purchased, ri.item_unit_price, ri.discount_percent, ri.receiving_id, ri.item_id, ri.line, i.tax_included')
                     ->from('receivings_items AS ri')
                     ->join('items AS i', 'ri.item_id = i.item_id')
                     ->where('ri.receiving_id IN ('.implode(', ', $receiving_ids).')');

            $query = $this->db->get();
            $tmp = $query->result_array();

            $this->db->flush_cache();

            $discount_items = array();
            $discount_items_total = 0;
            if(!empty($tmp)) {
                $discount_id = $this->Item->get_item_id_for_flat_discount_item();
                foreach($tmp as $val) {
                    if($val['item_id'] == $discount_id) {
                        $discount_items[$val['receiving_id']][] = $val;
                    }else
                        $receiving_items[$val['receiving_id']][] = $val;
                }
            }

            $this->db->select('rt.receiving_id, rt.item_id, rt.line, rt.name, rt.percent, rt.cumulative', 'i.tax_included')
                    ->from('receivings_items_taxes AS rt')
                    ->join('items as i', 'rt.item_id = i.item_id')
                    ->where('rt.receiving_id IN ('.implode(', ', $receiving_ids).')');

            $query = $this->db->get();
            $tmp = $query->result_array();

            if(!empty($tmp)) {
                foreach($tmp as $val) {
                    $tax_items[$val['receiving_id']][] = $val;
                }
            }

            if($options['validate_contract'] == true) {
                $contract_modal       = $this->model_load_model('Contract');
                $receiving_ids_in_contract = $contract_modal->get_receiving_id_in_contract($receiving_ids);
            }

            foreach($result as &$value) {
                $receiving_id = $value['receiving_id'];
                $total_all = 0;
                $tax_minus = 0; //all tax is added to the total sale order
                $item_price_line = array();
                if(isset($receiving_items[$receiving_id])) {
                    foreach($receiving_items[$receiving_id] as &$item) {
                        $line = $receiving_id . '-' . $item['item_id'] .  '-' . $item['line'];

                        $item['price'] =  $this->price_in_sale($item, $tax_items[$receiving_id]);
                        $item['discount_price'] = ($item['price'] * $item['discount_percent'] )/100;
                        $item['total'] = ($item['price'] - $item['discount_price']) * $item['quantity_purchased'];
                        $item_price_line[$line] = $item['total'];
                        $total_all = $total_all + $item['total'];
                    }
                }

                $value['tax_info']  = '';

                if(isset($tax_items[$receiving_id])) {
                    $tax_info = array();
                    $tax_info_full = array();
                    foreach($tax_items[$receiving_id] as $tax) {
                        $line = $receiving_id . '-' . $tax['item_id'] .  '-' . $tax['line'];

                        $percent = $tax['percent'];
                        $name = $tax['name'];
                        $price = $item_price_line[$line];
                        $tax_value = ($percent * $price)/100;

                        $percent_format = number_format($percent,2);

                        if(!isset($tax_info[$percent_format . '% ' . $name]))
                            $tax_info[$percent_format . '% ' . $name] =  $tax_value;
                        else
                            $tax_info[$percent_format . '% ' . $name] =  $tax_info[$name] + $tax_value;

                        if($tax['tax_included'] == 0) {
                            $tax_minus = $tax_minus + $tax_value;
                        }
                    }

                    if(count($tax_info)>0) {
                        foreach($tax_info as $key => &$t_value) {
                            $t_value = to_currency($t_value);
                            $tax_info_full[] = $key . ' : ' . $t_value;
                        }
                    }

                    $value['tax_info'] = implode('<br />', $tax_info_full);
                }

                $value['discount_total'] = 0;
                if(isset($discount_items[$receiving_id])) {;
                    foreach($discount_items[$receiving_id] as $discount_item){
                        $value['discount_total'] = $value['discount_total'] + abs($discount_item['item_unit_price']);
                    }
                }

                $value['discount_total'] = to_currency($value['discount_total']);
                $value['total_all'] = $total_all - $value['discount_total'] + $tax_minus;
                $value['total_all'] = to_currency(abs($value['total_all']));

                $value['supplier_name'] = $suppliers_info[$value['supplier_id']]['company_name'];

                if($options['validate_contract'] == true) {
                    if(in_array($receiving_id, $receiving_ids_in_contract))
                        $value['select'] = 'false';
                    else
                        $value['select'] = 'true';
                }else
                    $value['select'] = 'true';
            }
        }

        return $result;
    }

    protected function price_in_sale($item, $taxes) {
        if($item['tax_included'] == 0)
            $price =  $item['item_unit_price'];
        else {
            $price = $item['item_unit_price'];
            if(!empty($taxes)) {
                $percent = 0;
                foreach($taxes as $tax) {
                    if($item['item_id'] == $tax['item_id'] && $item['line'] == $tax['line'])
                        $percent = $percent + $tax['percent'];
                }

                $dividend = (100 + $percent)/100;
                $price = $price / $dividend;
            }
        }

        return $price;
    }

    function count_receiving_store_payment($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $this->load->library('receiving_lib');
            $supplier_id = $this->receiving_lib->get_supplier();

            $payment_type = lang('common_store_account');
            $location_id  = $this->Employee->get_logged_in_employee_current_location_id();

            $this->db -> select('COUNT(r.receiving_id) AS totalItem')
                      -> from('receivings_transactions AS rt')
                      -> join('receivings AS r', 'rt.recv_id = r.receiving_id')
                      -> where('r.location_id', $location_id)
                      -> where('r.supplier_id', $supplier_id)
                      -> where('rt.payment_type = \''.$payment_type.'\'')
                      -> where('rt.transaction_amount > 0');

            $store_account_payment_value = $this->receiving_lib->get_store_account_payment_value();
            if($store_account_payment_value == 1) {
                $this->db->where('r.return != 1');
            }else {
                $this->db->where('r.return', 1);
            }

            $query = $this->db->get();

            $result = $query->row()->totalItem;

            $this->db->flush_cache();

            return $result;
        }
    }

	function get_receiving_store_payment_items($arrParams = null, $options = null) {
		$this->load->library('sale_lib');
		$receiving_prefix  = $this->config->item('receive_prefix');
		$payment_type = lang('common_store_account');
		$location_id  = $this->Employee->get_logged_in_employee_current_location_id();
		$supplier_id  = $this->receiving_lib->get_supplier();

		$receiving_ids = implode(',', $arrParams['receiving_ids']);

        $store_account_payment_value = $this->receiving_lib->get_store_account_payment_value();
        if($store_account_payment_value == 1) {
            $where = "r.return != 1";
        }else {
            $where = "r.return = 1";
        }

		$query = $this->db->query('SELECT '.$this->db->dbprefix('receivings').'.receiving_id,  DATE_FORMAT('.$this->db->dbprefix('receivings').'.receiving_time, \'%d-%m-%Y %H:%i:%s\') AS receiving_time_format,
                                    IFNULL((SELECT SUM(amount) FROM '.$this->db->dbprefix('receivings_store_supplier_accounts').' WHERE receiving_id = '.$this->db->dbprefix('receivings').'.receiving_id),0) AS da_thanh_toan,
                                    IFNULL((SELECT SUM(transaction_amount) FROM '.$this->db->dbprefix('receivings_transactions').' WHERE recv_id = '.$this->db->dbprefix('receivings').'.receiving_id AND payment_type = \''.$payment_type.'\'),0) AS so_tien_no
                                    FROM '.$this->db->dbprefix('receivings').'
                                    WHERE '.$this->db->dbprefix('receivings').'.receiving_id IN (
                                        SELECT r.receiving_id
                                        FROM '.$this->db->dbprefix('receivings_transactions').' AS rt
                                        JOIN '.$this->db->dbprefix('receivings').' As r ON rt.recv_id = r.receiving_id
                                        WHERE r.location_id = '.$location_id.'
                                        AND r.supplier_id = '.$supplier_id.'
                                        AND rt.payment_type = \''.$payment_type.'\'
                                        AND '.$where.'
                                        AND r.receiving_id IN ('.$receiving_ids.')
                                    )');

		$result_tmp = $query->result_array();
		$result = array();

		if(!empty($result_tmp)) {
			$receiving_store_payment = $this->receiving_lib->get_receiving_store_payment();
			foreach($result_tmp as $val) {
				$receiving_id = $val['receiving_id'];
				if(isset($receiving_store_payment[$receiving_id]))
					$val['amount'] = $receiving_store_payment[$receiving_id];
				else
					$val['amount'] = 0;

				$val['con_lai'] = $val['so_tien_no'] - $val['da_thanh_toan'];

				$val['receiving_code'] = $receiving_prefix . ' ' . $receiving_id;

				$result[$receiving_id] = $val;
			}
		}

		return $result;
	}

    function list_receiving_store_payment($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $this->load->library('receiving_lib');
            $paginator = $arrParams['paginator'];
            $supplier_id = $this->receiving_lib->get_supplier();

            $payment_type = lang('common_store_account');
            $location_id  = $this->Employee->get_logged_in_employee_current_location_id();

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $arrParams['col'];
                $order = $arrParams['order'];

                $order_by = $col . ' ' . $order;

                $_SESSION['receiving_store_payment_modal_filter']['col']   = $arrParams['col'];
                $_SESSION['receiving_store_payment_modal_filter']['order'] = $arrParams['order'];
            }

            $_SESSION['receiving_store_payment_modal_filter']['current_page'] = $page = $arrParams['page'];

            $limit = $paginator['per_page'];
            $offset = ($page - 1)*$paginator['per_page'];

            $store_account_payment_value = $this->receiving_lib->get_store_account_payment_value();
            if($store_account_payment_value == 1) {
                $where = "r.return != 1";
            }else {
                $where = "r.return = 1";
            }

            $query = $this->db->query('SELECT '.$this->db->dbprefix('receivings').'.receiving_id,  DATE_FORMAT('.$this->db->dbprefix('receivings').'.receiving_time, \'%d-%m-%Y %H:%i:%s\') AS receiving_time_format,
                                    IFNULL((SELECT SUM(amount) FROM '.$this->db->dbprefix('receivings_store_supplier_accounts').' WHERE receiving_id = '.$this->db->dbprefix('receivings').'.receiving_id),0) AS da_thanh_toan,
                                    IFNULL((SELECT SUM(transaction_amount) FROM '.$this->db->dbprefix('receivings_transactions').' WHERE recv_id = '.$this->db->dbprefix('receivings').'.receiving_id AND payment_type = \''.$payment_type.'\'),0) AS so_tien_no
                                    FROM '.$this->db->dbprefix('receivings').'
                                    WHERE '.$this->db->dbprefix('receivings').'.receiving_id IN (
                                        SELECT r.receiving_id
                                        FROM '.$this->db->dbprefix('receivings_transactions').' AS rt
                                        JOIN '.$this->db->dbprefix('receivings').' As r ON rt.recv_id = r.receiving_id
                                        WHERE r.location_id = '.$location_id.'
                                        AND r.supplier_id = '.$supplier_id.'
                                        AND rt.payment_type = \''.$payment_type.'\'
                                        AND '.$where.'
                                    )
                                    ORDER BY ' . $order_by . ' LIMIT ' . $limit .' OFFSET ' . $offset);

            $result_tmp = $query->result_array();
            $result = array();

            if(!empty($result_tmp)) {
                $receiving_store_payment = $this->receiving_lib->get_receiving_store_payment();
                foreach($result_tmp as $val) {
                    $receiving_id = $val['receiving_id'];
                    if(isset($receiving_store_payment[$receiving_id]))
                        $val['amount'] = $receiving_store_payment[$receiving_id];
                    else
                        $val['amount'] = 0;

                    $result[$receiving_id] = $val;
                }
            }

        }

        return $result;
    }

	function get_store_supplier_accounts($arrParams = null, $options = null) {
		$this->db -> select('*')
				  -> from('receivings_transactions')
				  -> where('recv_id', $arrParams['receiving_id']);
				  // -> order_by('sno_id', 'ASC');

		$query = $this->db->get();

		$result = $query->result_array();

		$this->db->flush_cache();

		return $result;
	}

    function create_receivings_items_temp_table_n9($arrParams = null, $options = null) {
        $where = (!empty($arrParams['where'])) ? $arrParams['where'] : '';
        $query = "CREATE TEMPORARY TABLE ".$this->db->dbprefix('receivings_items_temp')."(
                    SELECT ri.receiving_id, ri.item_id, ri.line, r.suspended, r.return, r.store_account_payment,
                    ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100 AS ttotal,
                    (ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100)*(SUM(CASE WHEN rit.cumulative != 1 THEN rit.percent ELSE 0 END)/100)
                    +(((ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100)*(SUM(CASE WHEN rit.cumulative != 1 THEN rit.percent ELSE 0 END)/100)
                    + (ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100)) *(SUM(CASE WHEN rit.cumulative = 1 THEN rit.percent ELSE 0 END))/100) AS tax
                    FROM ".$this->db->dbprefix('receivings_items')." as ri
                    LEFT JOIN ".$this->db->dbprefix('receivings')." as r ON ri.receiving_id = r.receiving_id
                    LEFT JOIN ".$this->db->dbprefix('receivings_items_taxes')." as rit ON rit.receiving_id = ri.receiving_id AND rit.item_id = ri.item_id AND rit.line = ri.line
                    WHERE $where
                    GROUP BY ri.receiving_id, ri.item_id, ri.line
                )";

        $this->db->query($query);
    }


    function get_list($id=null){
    	$list = $this->get_list_task($this->Employee->get_logged_in_employee_info()->id);
    	$this->db->select('r.receiving_id,ri.item_unit_price,ri.item_id,i.name,t.name as task_name,s.company_name,r.receiving_time,ri.line');
    	$this->db->from('phppos_receivings as r');
    	$this->db->join('phppos_receivings_items as ri', 'ri.receiving_id = r.receiving_id');
    	$this->db->join('phppos_suppliers as s', 's.person_id = r.supplier_id');
    	$this->db->join('phppos_items as i', 'i.item_id = ri.item_id');
    	$this->db->join('phppos_tasks as t', 't.id = r.task_id');
    	if($this->_scopeOfView == 'view_scope_owner')
    	{
    		if(empty($list)){
    			$this->db->get()->row();
    			return [];
    		}

    		$this->db->where_in('t.id', $list);
    	}
    	if($this->_scopeOfView == 'view_scope_location')
    	{
    		$this->db->where('r.location_id', $this->Employee->get_logged_in_employee_current_location_id());
    		if(!empty($list))
    		$this->db->or_where_in('t.id', $list);
    	}
    	$this->db->where('r.deleted', 0);
    	if(!empty($id))
    	{
    		$this->db->where('r.receiving_id', $id);
    	}
    	return $this->db->get()->result_array();

    }

    function get_list_task($id){
    	$this->db->select('tu.task_id');
    	$this->db->from('phppos_task_user_relations as tu');
    	$this->db->where('tu.user_id', $id);
    	$this->db->group_start();
    		$this->db->where('tu.is_join', 1);
    		$this->db->or_where('tu.is_implement',1);
    	$this->db->group_end();	
    	$this->db->group_by('tu.task_id');
    	$result = $this->db->get()->result_array();
    	$data = array();
    	foreach ($result as $key => $value) {
    		$data[$key] =$value['task_id'];
    	}
    	return $data;
    }

    
}
?>
