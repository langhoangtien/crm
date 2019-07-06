<?php
require_once (APPPATH.'/libraries/Receiving_lib.php');
class BizReceiving_lib extends Receiving_lib
{
	function clear_all()
	{
		$this->clear_mode();
		$this->empty_cart();
		$this->delete_supplier();
		$this->delete_task();
		$this->delete_location();
		$this->delete_comment();
		$this->delete_suspended_receiving_id();
		$this->clear_deleted_taxes();
		$this->clear_selected_payment();
		$this->clear_change_receiving_date_enable();
		$this->clear_change_receiving_date();
		$this->delete_change_recv_id();
		$this->clear_po();
		$this->clear_email_receipt();
		$this->clear_payments();
		$this->clear_receiving_store_payment();
        $this->clear_store_account_payment_value();
	}

	public function clear_receiving_store_payment() {
		$this->CI->session->unset_userdata('receiving_store_payment');
	}
	
	function clear_payments()
	{
		$this->CI->session->unset_userdata('recv_payments');
	}
	
	public function delete_payment($id)
	{
		if ($this->CI->session->userdata('recv_payments') != NULL) {
			$payments = $this->get_payments();
			unset($payments[$id]);
			$this->CI->session->set_userdata('recv_payments', $payments);
		}
	}
	
	public function add_payment($paymentType = '', $amount = 0)
	{
		if ($this->CI->session->userdata('recv_payments') === NULL) {
			$this->set_payments([]);
		}
		$payments = $this->get_payments();
		$payment = [
		'payment_type' => $paymentType,
		'amount' => $amount,
		];
		$payments[] = $payment;
		$this->CI->session->set_userdata('recv_payments', $payments);
	}
	
	function set_payments($cart_data = [])
	{
		$this->CI->session->set_userdata('recv_payments',$cart_data);
	}
	
	public function get_payments()
	{
		if(empty($this->CI->session->userdata('recv_payments')))
		{
			return array();
		}
		return $this->CI->session->userdata('recv_payments');
	}

    public function get_payment_total() {
        $payments = $this->get_payments();
        $result = 0;
        if(!empty($payments)) {
            foreach($payments as $val)
						{
							if($val['payment_type']!= lang('common_debt_supplier'))
							{
								$result = $result + $val['amount'];
							}
                
						}
        }

        return $result;
    }
    
    # Hàm sắp xếp mảng
	function sap_xep_mang(&$array, $subkey="id", $sort_ascending=false) {

        if (count($array))
            $temp_array[key($array)] = array_shift($array);

        foreach($array as $key => $val){
            $offset = 0;
            $found = false;
            foreach($temp_array as $tmp_key => $tmp_val)
            {
                if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
                {
                    $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
                                                array($key => $val),
                                                array_slice($temp_array,$offset)
                                              );
                    $found = true;
                }
                $offset++;
            }
            if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
        }

        if ($sort_ascending) $array = array_reverse($temp_array);
        else $array = $temp_array;

        return $array;
    }

	function copy_entire_receiving($receiving_id, $is_receipt = false)
	{
		$this->empty_cart();
		$this->delete_supplier();
		$receiving_taxes = $this->get_taxes($receiving_id);
        $supplier_id = $this->CI->Receiving->get_supplier($receiving_id)->person_id;
        $receiving_info = $this->CI->Receiving->getInfo($receiving_id);
		foreach($this->CI->Receiving->get_receiving_items($receiving_id)->result() as $row)
		{
            if($receiving_info['return'] == 1) {
                $row->quantity_purchased = $row->quantity_purchased * (-1);
                $row->quantity_received = $row->quantity_received * (-1);
            }

			$item_info = $this->CI->Item->get_info($row->item_id);
			$price_to_use = $row->item_unit_price;
			$this->add_item(
					$row->item_id,
					$row->quantity_purchased,
					$row->quantity_received,
					$row->discount_percent,
					$price_to_use,
					$row->description,
					$row->serialnumber,
					$row->expire_date,
					TRUE,
					$row->line,
					$receiving_id);
				
		}
		$this->set_supplier($supplier_id);
	
		$recv_info = $this->CI->Receiving->get_info($receiving_id)->row_array();
		$this->set_comment($recv_info['comment']);
		$this->set_location($recv_info['transfer_to_location_id']);
		
		$payments = $this->CI->Receiving->getTransactions($receiving_id, $supplier_id);
		foreach ($payments as $payment) {
			$this->add_payment($payment['payment_type'], $payment['transaction_amount']);
		}
		
		if ($recv_info['transfer_to_location_id'])
		{
			$this->set_mode('transfer');
		}
		$this->set_deleted_taxes($this->CI->Receiving->get_deleted_taxes($receiving_id));

        if($receiving_info['return'] == 1) {
			$this->set_mode('return');
		}

		if($receiving_info['store_account_payment'] == 1) {
			$this->set_mode('store_account_payment');
		}
	}
	
	function add_item(
			$item_id,
			$quantity=1,
			$quantity_received=NULL,
			$discount=0,
			$price=null,
			$description=null,
			$serialnumber=null,
			$expire_date= null,
			$force_add = FALSE,
			$line = FALSE,
			$receiving_id = 0)
	{
		//make sure item exists in database.
		if(!$force_add && !$this->CI->Item->exists(does_contain_only_digits($item_id) ? (int)$item_id : -1))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}

		//Get items in the receiving so far.
		$items = $this->get_cart();

        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the list. Since items can be deleted, we can't use a count. we use the highest key + 1.

        $maxkey=0;                       //Highest key so far
        $itemalreadyinsale=FALSE;        //We did not find the item yet.
		$insertkey=0;                    //Key to use for new entry.
		$updatekey=0;                    //Key to use to update(quantity)

		foreach ($items as $item)
		{
            //We primed the loop so maxkey is 0 the first time.
            //Also, we have stored the key in the element itself so we can compare.
            //There is an array function to get the associated key for an element, but I like it better
            //like that!

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id']==$item_id)
			{
				$itemalreadyinsale=TRUE;
				$updatekey=$item['line'];
			}
		}

		$insertkey=$maxkey+1;

		$cur_item_info = $this->CI->Item->get_info($item_id);

		$cur_item_location_info = $this->CI->Item_location->get_info($item_id);
		
		$default_cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
		
		if ($expire_date === NULL && $cur_item_info->expire_days !== NULL)
		{
			$expire_date = date(get_date_format(), strtotime('+ '.$cur_item_info->expire_days. ' days'));
		}
		elseif($expire_date !== NULL)
		{
			$expire_date = date(get_date_format(),strtotime($expire_date));
		}
		else
		{
			$expire_date = NULL;
		}
		
		$measure = $this->CI->Measure->getInfo($cur_item_info->measure_id);
		if ($receiving_id) {
			$measureOnRecv = $this->CI->Receiving->getMeasureOnRecvItem($receiving_id, $item_id);
			if ($measureOnRecv && $measureOnRecv->id && $measureOnRecv->id != $measure->id) {
				$quantity = $measureOnRecv->measure_qty;
				$price = $this->getPriceByMeasureConverted($item_id, (int) $measureOnRecv->measure_id);
				$measure = $measureOnRecv;
			}
		}
		
		//array records are identified by $insertkey and item_id is just another field.
		$item = array(($line === FALSE ? $insertkey : $line)=>
		array(
			'item_id'=>$item_id,
			'line'=>$line === FALSE ? $insertkey : $line,
			'name'=>$this->CI->Item->get_info($item_id)->name,
			'size'=>$this->CI->Item->get_info($item_id)->size,
			'cost_price_interval'=>$this->CI->Item->get_info($item_id)->cost_price_interval,
			'item_number'=>$cur_item_info->item_number,
			'product_id' => $cur_item_info->product_id,
			'description'=>$description!=null ? $description: $this->CI->Item->get_info($item_id)->description,
			'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
			'allow_alt_description'=>$this->CI->Item->get_info($item_id)->allow_alt_description,
			'is_serialized'=>$this->CI->Item->get_info($item_id)->is_serialized,
			'quantity'=>$quantity,
			'measure_id'=>$cur_item_info->measure_id,
			'measure' => !empty($measure) ? $measure->name : lang('common_not_set'),
			'cur_quantity' => $cur_item_location_info->quantity,
			'quantity_received' => $quantity_received,
			'discount'=>$discount,
			'price'=>$price!=null ? $price: $default_cost_price,
			'expire_date' => $expire_date,
			'cost_price_preview' => $this->calculate_average_cost_price_preview($item_id, $price!=null ? $price: $default_cost_price, $quantity,$discount),
			)
		);

		
		//Item already exists
		if($itemalreadyinsale && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
		{
			$items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
			$items[$updatekey]['cost_price_preview']=$this->calculate_average_cost_price_preview($item_id, $price!=null ? $price: $default_cost_price, $quantity,$discount);
		}
		else
		{
			//add to existing array
			$items+=$item;
		}

		$this->set_cart($items);
		return true;

	}
	
	function edit_item($line,$description = NULL,$serialnumber = NULL,$expire_date= null, $quantity = NULL,$quantity_received=NULL,$discount = NULL,$price = NULL, $measureId = NULL )
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			
			if ($description !== NULL ) {
				$items[$line]['description'] = $description;
			}
			if ($serialnumber !== NULL ) {
				$items[$line]['serialnumber'] = $serialnumber;
			}
				
			if ($expire_date !== NULL ) {
	
				if ($expire_date == '')
				{
					$items[$line]['expire_date'] = NULL;
				}
				else
				{
					$items[$line]['expire_date'] =  date(get_date_format(),strtotime($expire_date));
				}
			}
				
			if ($quantity_received !== NULL ) {
				$items[$line]['quantity_received'] = $quantity_received;
			}
				
			if ($quantity !== NULL ) {
				$items[$line]['quantity'] = $quantity;
			}
			if ($discount !== NULL ) {
				$items[$line]['discount'] = $discount;
			}
			
			if ($price !== NULL ) {
				$items[$line]['price'] = $price;
			}
			
			if ($measureId /* && ($this->get_mode() == 'receive' || $this->get_mode() == 'purchase_order') */) {
				$items[$line]['measure_id'] = (int) $measureId;
				$measure = $this->CI->Measure->getInfo((int) $measureId);
				$items[$line]['measure'] = $measure->name;
				$itemObj = $this->CI->Item->get_info($items[$line]['item_id']);
				if($measureId != $itemObj->measure_id) {
					$items[$line]['price'] = $this->getPriceByMeasureConverted($items[$line]['item_id'], (int) $measureId);
				} else {
					$items[$line]['price'] = $itemObj->cost_price;
				}
			}
			$items[$line]['cost_price_preview']=$this->calculate_average_cost_price_preview($items[$line]['item_id'], $items[$line]['price'], $items[$line]['quantity'],$items[$line]['discount']);
			
			$this->set_cart($items);
				
			return true;
		}
	
		return false;
	}

    function discount_all($percent_discount) {
        $items = $this->get_cart();

        foreach(array_keys($items) as $key)
        {
            if ((isset($items[$key]['item_id']) && $items[$key]['item_id'] != $this->CI->Item->get_item_id_for_flat_discount_item()) || isset($items[$key]['item_kit_id']))
            {
                $items[$key]['discount'] = $percent_discount;
            }
        }
        $this->set_cart($items);
    }

    function get_line_for_flat_discount_item()
    {
        $item_id_for_flat_discount_item = $this->CI->Item->get_item_id_for_flat_discount_item();

        $items = $this->get_cart();
        foreach ($items as $line=>$item )
        {
            if (isset($item['item_id']) && $item['item_id'] == $item_id_for_flat_discount_item)
            {
                return $line;
            }
        }

        return FALSE;

    }

    function get_discount_all_percent() {
        $percent_discount = NULL;
        $first_item = NULL;

        $line_for_fixed_discount = $this->get_line_for_flat_discount_item();
        $items = $this->get_cart();

        if (count($items) > 0)
        {
            foreach ($items as $line=>$item )
            {
                if ($line != $line_for_fixed_discount)
                {
                    $first_item = $items[$line];
                    break;
                }
            }
            $percent_discount = $first_item['discount'];

            foreach ($items as $line=>$item )
            {
                if ($line != $line_for_fixed_discount)
                {
                    if ($item['discount'] == $percent_discount)
                    {
                        $percent_discount = $item['discount'];
                    }
                    else
                    {
                        $percent_discount = NULL;
                        break;
                    }
                }
            }
        }
        return $percent_discount;
    }

    function get_discount_all_fixed()
    {
        $line_for_fixed_discount = $this->get_line_for_flat_discount_item();

        if ($line_for_fixed_discount)
        {
            $cart = $this->get_cart();
            $item = $cart[$line_for_fixed_discount];

            return to_currency_no_money($item['price'] * -$item['quantity']);
        }

        return NULL;
    }
	
	protected function getPriceByMeasureConverted($itemId = 0, $measureConvertedId = 0){
		$itemObj = $this->CI->Item->get_info($itemId);
		$convertedValue = $this->CI->ItemMeasures->getConvertedValue($itemId, $measureConvertedId);
		return $itemObj->cost_price * $convertedValue->qty_converted * $convertedValue->cost_price_percentage_converted / 100;
	}

    function get_receiving($receiving_id) {
        $this->save_current_recv_state();

        $this->CI->load->model('Receiving');
        $this->CI->load->model('Employee');
        $this->CI->load->model('Supplier');
        $this->CI->load->model('Location');

        $receiving_info = $this->CI->Receiving->get_info($receiving_id)->row_array();
        $this->copy_entire_receiving($receiving_id, TRUE);
        $data['supplier_id'] = $this->CI->config->item('receive_prefix') . ' ' . $receiving_id;
        $data['cart']=$this->get_cart();
        $data['subtotal']=$this->get_subtotal($receiving_id);
        $data['taxes']=$this->get_taxes($receiving_id);
        $data['total']=$this->get_total($receiving_id);
        $data['mode'] = $this->get_mode();
        $data['receipt_title']=lang('receivings_receipt');
        $data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($receiving_info['receiving_time']));
        $supplier_id=$this->get_supplier();
        $emp_info=$this->CI->Employee->get_info($receiving_info['employee_id']);
        $data['payment_type']=$receiving_info['payment_type'];
        $data['payments'] = $this->get_payments();

        $data['override_location_id'] = $receiving_info['location_id'];
        $data['suspended'] = $receiving_info['suspended'];
        $data['comment'] = $receiving_info['comment'];
        $data['is_po'] = $receiving_info['is_po'];
        $data['discount_exists'] = $this->_does_discount_exists($data['cart']);

        $data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

        if($supplier_id!=-1)
        {
            $supplier_info=$this->CI->Supplier->get_info($supplier_id);

            $data['supplier']=$supplier_info->company_name;
            if ($supplier_info->first_name || $supplier_info->last_name)
            {
                $data['supplier'] .= ' ('.$supplier_info->first_name.' '.$supplier_info->last_name.')';
            }

            $data['supplier_address_1'] = $supplier_info->address_1;
            $data['supplier_address_2'] = $supplier_info->address_2;
            $data['supplier_city'] = $supplier_info->city;
            $data['supplier_state'] = $supplier_info->state;
            $data['supplier_zip'] = $supplier_info->zip;
            $data['supplier_country'] = $supplier_info->country;
            $data['supplier_phone'] = $supplier_info->phone_number;
            $data['supplier_email'] = $supplier_info->email;

        }

        $data['receiving_id']= $receiving_id;
        $data['receiving_id_raw']=$receiving_id;

        $current_location = $this->CI->Location->get_info($receiving_info['location_id']);
        $data['transfer_from_location'] = $current_location->name;

        if ($receiving_info['transfer_to_location_id'] > 0)
        {
            $transfer_to_location = $this->CI->Location->get_info($receiving_info['transfer_to_location_id']);
            $data['transfer_to_location'] = $transfer_to_location->name;

            $transfer_from_location = $this->CI->Location->get_info($receiving_info['location_id']);
            $data['transfer_from_location'] = $transfer_from_location->name;

            $data['mode'] = 'transfer';
        }
        if($receiving_info['suspended']>0){
            if($receiving_info['suspended']==1) $data['mode']= 'purchase_order';
        }

        $this->clear_all();

        //Restore previous state saved above
        $this->restore_current_recv_state();

        return $data;
    }

    function get_delivery_items($cart, $delivery, $options = null) {
        $items = array();
        if(!empty($cart)) {
            foreach($cart as $item) {
                if($item['quantity'] > 0){
                    if(isset($item['item_id']))
                        $key = 'i-'.$item['item_id'].'-'.$item['line'];
                    elseif(isset($item['item_kit_id']))
                        $key = 'k-'.$item['item_id'].'-'.$item['line'];

                    $item['dg']    = 0;
                    $item['limit'] = $item['quantity'];
                    if(isset($delivery[$key])){
                        $item['dg']       = $delivery[$key]['s_quantity'];
                        $item['limit']    = $item['quantity'] - $item['dg'];
                    }

                    if($options == 'all')
                        $items[$key] = $item;
                    elseif($item['limit'] > 0) {
                        $items[$key] = $item;
                    }

                }
            }
        }

        return $items;
    }

	public function get_receiving_store_payment() {
		$result = $this->CI->session->userdata('receiving_store_payment');
		if(empty($result))
			$result = array();

		return $result;
	}

	function get_last_amount_from_sale_store_payment() {
		$debt_payment = $this->get_debt_payment();
		$all_amount = $this->get_all_amount_from_receiving_store_payment();

		return $debt_payment-$all_amount;
	}

	public function get_debt_payment() {
		$items = $this->get_cart();
		$result = $items[1]['price'];

		return $result;
	}

	public function get_all_amount_from_receiving_store_payment() {
		$receiving_store_payment = $this->CI->session->userdata('receiving_store_payment');
		$result = 0;
		if(!empty($receiving_store_payment)) {
			foreach($receiving_store_payment as $val)
				$result = $result + $val;
		}

		return $result;
	}

    function get_minus_liability(){ // trừ công nợ
        $result = 0;
        $payments = $this->get_payments();
        foreach($payments as $val) {
            if($val['payment_type'] == lang('minus_liability'))
                $result = $result + $val['amount'];
        }

        return $result;
    }

    function get_debit_payment() {
        $result = 0;
        $payments = $this->get_payments();
        if(!empty($payments)) {
            foreach($payments as $val) {
                if($val['payment_type'] == 'Sổ ghi nợ')
                    $result = $result + $val['amount'];
            }
        }

        return $result;
    }

    function get_store_account_payment_value() {
        return $this->CI->session->userdata('supplier_store_account_payment_value');
    }

    function set_store_account_payment_value($value = null) {
        $this->CI->session->set_userdata('supplier_store_account_payment_value', $value);
    }

    function clear_store_account_payment_value() {
        $this->CI->session->unset_userdata('supplier_store_account_payment_value');
    }

	public function update_receiving_store_payment($receiving_id, $amount) {
		$receiving_store_payment = $this->CI->session->userdata('receiving_store_payment');
		$receiving_store_payment[$receiving_id] = $amount;

		$this->CI->session->set_userdata('receiving_store_payment', $receiving_store_payment);
	}

    function get_receive($receiving_id, $options = null) {
        global $cached_receiving_info;
        if($options == null && isset($cached_receiving_info)) {
            return $cached_receiving_info;
        }

        $result['info'] = $info = $this->CI->Receiving->get_receiving_info($receiving_id);
        $sql = "    SELECT ri.receiving_id, ri.item_id, ri.line, r.suspended, r.return, r.store_account_payment,
                    ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100 AS ttotal,
                    (ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100)*(SUM(CASE WHEN rit.cumulative != 1 THEN rit.percent ELSE 0 END)/100)
                    +(((ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100)*(SUM(CASE WHEN rit.cumulative != 1 THEN rit.percent ELSE 0 END)/100)
                    + (ri.item_unit_price*ri.quantity_received-ri.item_unit_price*ri.quantity_received*ri.discount_percent/100)) *(SUM(CASE WHEN rit.cumulative = 1 THEN rit.percent ELSE 0 END))/100) AS tax
                    FROM ".$this->CI->db->dbprefix('receivings_items')." as ri
                    LEFT JOIN ".$this->CI->db->dbprefix('receivings')." as r ON ri.receiving_id = r.receiving_id
                    LEFT JOIN ".$this->CI->db->dbprefix('receivings_items_taxes')." as rit ON rit.receiving_id = ri.receiving_id AND rit.item_id = ri.item_id AND rit.line = ri.line
                    WHERE ri.receiving_id = $receiving_id
                    GROUP BY ri.receiving_id, ri.item_id, ri.line";

        $query     =   $this->CI->db->query($sql);

        $result_tmp    =   $query->result_array();

        $this->CI->db->flush_cache();

        $gia_tri_don_hang = 0;
        if(!empty($result_tmp)) {
            foreach($result_tmp as $key => $val) {
                $gia_tri_don_hang = $val['ttotal'] + $val['tax'];
            }
        }

        $result['cart'] = $result_tmp;
        $result['gia_tri_don_hang'] = $gia_tri_don_hang;

        if($options == null) $cached_receiving_info = $result;

        return $result;
    }

    function _does_discount_exists($cart) {
        foreach($cart as $line=>$item)
        {
            if( (isset($item['discount']) && $item['discount']>0 ) || (isset($item['discount_percent']) && $item['discount_percent']>0 ) )
            {
                return TRUE;
            }
        }

        return FALSE;
    }
		function get_payments_totals()
    {
        $subtotal = 0;
				$payments = $this->get_payments();
        foreach($payments as $payment)
        {
					if($payment['payment_type'] ==  lang('common_debt_supplier'))
						{
             $subtotal-=$payment['amount'];
						}
						else
						{
             $subtotal += $payment['amount'];
						}
        }

        return to_currency_no_money($subtotal);
    }

}
?>