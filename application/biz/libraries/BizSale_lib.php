<?php
require_once (APPPATH.'/libraries/Sale_lib.php');

class BizSale_lib extends Sale_lib
{
    function clear_all()
    {
        $this->clear_mode();
        $this->empty_cart();
        $this->clear_comment();
        $this->clear_show_comment_on_receipt();
        $this->clear_change_sale_date();
        $this->clear_change_sale_date_enable();
        $this->clear_email_receipt();
        $this->empty_payments();
		$this->empty_customers();														 
        $this->delete_customer(false);
        $this->delete_suspended_sale_id();
        $this->delete_change_sale_id();
        $this->delete_partial_transactions();
        $this->clear_save_credit_card_info();
        $this->clear_use_saved_cc_info();
        $this->clear_prompt_for_card();
        $this->clear_selected_tier_id();
        $this->clear_deleted_taxes();
        $this->clear_cc_info();
        $this->clear_sold_by_employee_id();
        $this->clear_selected_payment();
        $this->clear_invoice_no();
        $this->clear_redeem();
        $this->clear_deliverer();
        $this->clear_delivery_date();
        $this->clear_sale_time_date();
        $this->clear_supporter();
        $this->clear_group_employees();
        $this->clear_service_id();
        $this->clear_sale_code();
        $this->clear_commission_info();
        $this->clear_sale_store_payment();
        $this->clear_sale_payment_history();
        $this->clear_store_account_payment_value();
        $this->clear_sale_vat_relationship();
        $this->clear_visa_code();
        $this->clear_items_bi_xoa();
		$this->clearCartItemsAttributeValue();
		$this->clearCartItemsAttribute();
		$this->clearItemContainsLine();
		$this->clearCartItemsAttributeSet();
        $this->clearCartItemsCategory();
        $this->clearSaleComments();
        $this->clear_id_sale_return();
        $this->clear_supporter_list();
        $this->clear_sale_status();
        $this->clear_temp_service();
        $this->clear_sale_creator();
    }

    function set_sale_vat_relationship($sale_id = null) {
        $tmp = $this->CI->session->userdata('sale_vat_relationship');
        if(!empty($tmp)) {
            if(!in_array($sale_id, $tmp))
                $tmp[] = $sale_id;
        }else
            $tmp = array($sale_id);

        $this->CI->session->set_userdata('sale_vat_relationship', $tmp);
    }

    function set_store_account_payment_value($value = null) {
        $this->CI->session->set_userdata('store_account_payment_value', $value);
    }

    function set_sale_payment_history($sale_id) {
        $sale_payment_history = $this->CI->Sale->get_payment_history(array('sale_id'=>$sale_id));
        $result = array();
        if(!empty($sale_payment_history)) {
            foreach($sale_payment_history as $key => $val) {
                $tmp = array();
                $tmp['title'] = 'Thanh toán lần ' . ($key+1);
                $tmp['amount'] = to_currency($val['amount']);
                $result[] = $tmp;
            }
        }

        $this->CI->session->set_userdata('sale_payment_history', $result);
    }

    function get_sale_payment_history() {
        return $this->CI->session->userdata('sale_payment_history');
    }

    function clear_sale_payment_history() {
        $this->CI->session->unset_userdata('sale_payment_history');
    }

    function get_last_amount_from_sale_store_payment() {
        $debt_payment = $this->get_debt_payment();
        $all_amount = $this->get_all_amount_from_sale_store_payment();

        return $debt_payment-$all_amount;
    }

    public function get_debt_payment() {
        $items = $this->get_cart();
        $result = $items[1]['price'];

        return $result;
    }

    public function get_all_amount_from_sale_store_payment() {
        $sale_store_payment = $this->CI->session->userdata('sale_store_payment');
        $result = 0;
        if(!empty($sale_store_payment)) {
            foreach($sale_store_payment as $val)
                $result = $result + $val;
        }

        return $result;
    }

    public function get_sale_store_payment() {
        $result = $this->CI->session->userdata('sale_store_payment');
        if(empty($result))
            $result = array();

        return $result;
    }

    public function get_store_account_paymment_comment() {
        $sale_store_payment = $this->get_sale_store_payment();
        $result = '';
        if(!empty($sale_store_payment)) {
            $result = array();
            $sale_ids = array_keys($sale_store_payment);
            $sale_info_list = $this->CI->Sale->get_items(array('sale_ids'=>$sale_ids));

            foreach($sale_store_payment as $key => $val) {
                if(!empty($sale_info_list[$key]['code']))
                    $sale_code = $sale_info_list[$key]['code'];
                else
                    $sale_code = $this->CI->config->item('sale_prefix') . ' ' . $key;

                $amount = to_currency($val);
                $result[] = "$sale_code: $amount";
            }

            $result = implode('; ', $result);
        }

        return $result;
    }

    function get_store_account_payment_value() {
        return $this->CI->session->userdata('store_account_payment_value');
    }

    public function update_sale_store_payment($sale_id, $amount) {
        $sale_store_payment = $this->CI->session->userdata('sale_store_payment');
        $sale_store_payment[$sale_id] = $amount;

        $this->CI->session->set_userdata('sale_store_payment', $sale_store_payment);
    }

    function clear_items_bi_xoa(){
         $this->CI->session->unset_userdata('items_bi_xoa');
    }

    function clear_sale_vat_relationship($sale_id = null) {
        if($sale_id > 0) {
            $tmp = $this->CI->session->userdata('sale_vat_relationship');
            if(!empty($tmp)) {
                if(($key = array_search($sale_id, $tmp)) !== false) {
                    unset($tmp[$key]);
                }

                $this->CI->session->set_userdata('sale_vat_relationship', $tmp);
            }

        }else {
            $this->CI->session->unset_userdata('sale_vat_relationship');
        }
    }

    function clear_store_account_payment_value() {
        $this->CI->session->unset_userdata('store_account_payment_value');
    }
    public function clear_visa_code(){
        $this->CI->session->unset_userdata('visa_code');
    }

    public function clear_sale_store_payment() {
        $this->CI->session->unset_userdata('sale_store_payment');
    }

    public function clear_commission_info() {
        $this->CI->session->unset_userdata('commission_info');
    }

    public function clear_sale_code() {
        $this->CI->session->unset_userdata('sale_code');
    }

    public function clear_sale_time_date()
    {
        $this->CI->session->unset_userdata('sale_time_date');
    }

    public function clear_delivery_date()
    {
        $this->CI->session->unset_userdata('sale_delivery_date');
    }

    public function set_sale_time_date($sale_time_date)
    {
        $this->CI->session->set_userdata('sale_time_date', $sale_time_date);
    }

    public function set_delivery_date($delivery_date)
    {
        $this->CI->session->set_userdata('sale_delivery_date', $delivery_date);
    }

    function get_sale_vat_relationship() {
        return $this->CI->session->userdata('sale_vat_relationship');
    }

    public function get_sale_time_date()
    {
        return $this->CI->session->userdata('sale_time_date');
    }

    public function get_delivery_date()
    {
        return $this->CI->session->userdata('sale_delivery_date');
    }

    public function clear_deliverer()
    {
        $this->CI->session->unset_userdata('sale_deliverer_id');
    }

    public function clear_supporter()
    {
        $this->CI->session->unset_userdata('sale_supporter_id');
    }

    public function clear_group_employees() {
        $this->CI->session->unset_userdata('group_employees');
    }

    public function clear_service_id() {
        $this->CI->session->unset_userdata('service_id');
    }

    public function set_deliverer($deliverer_id)
    {
        $this->CI->session->set_userdata('sale_deliverer_id', $deliverer_id);
    }

    public function get_deliverer()
    {
        return $this->CI->session->userdata('sale_deliverer_id');
    }

    /**
     * @author ToiNT
     */
    public function set_supporter($supporter_id)
    {
        $this->CI->session->set_userdata('sale_supporter_id', $supporter_id);
    }
    
    /**
     * @author ToiNT
     */
    public function clear_supporter_list() {
        $this->CI->session->unset_userdata('supporter_list');
    }
    
    /**
     * @author ToiNT
     */
    public function get_supporter_list() {
        return $this->CI->session->userdata('supporter_list');
    }
    
    /**
     * @author ToiNT
     */
    public function set_temp_service($temp_id) {
        $this->CI->session->set_userdata('temp_service_id', $temp_id);
    }
    
    /**
     * @author ToiNT
     */
    public function get_temp_service() {
        return $this->CI->session->userdata('temp_service_id');
    }
    
    public function clear_temp_service() {
        $this->CI->session->unset_userdata('temp_service_id');
    }
    
    /**
     * @author ToiNT
     */
    public function set_sale_status($status_id) {
        $this->CI->session->set_userdata('sale_status_id', $status_id);
    }
    
    /**
     * @author ToiNT
     */
    public function clear_sale_status() {
        $this->CI->session->unset_userdata('sale_status_id');
    }
    
    /**
     * @author ToiNT
     */
    public function get_sale_status() {
        return $this->CI->session->userdata('sale_status_id');
    }
    
    /**
     * @author ToiNT
     */
    public function set_sale_creator($employee_id) {
        $employee_info = $this->CI->Employee->get_information($employee_id);
        $this->CI->session->set_userdata('sale_creator', $employee_info);
    }
    
    /**
     * @author ToiNT
     */
    public function clear_sale_creator() {
        $this->CI->session->unset_userdata('sale_creator');
    }
    
    /**
     * @author ToiNT
     */
    public function get_sale_creator() {
        return $this->CI->session->userdata('sale_creator');
    }
    
    public function delete_employee_from_group($employee_id, $group_id) {
        $group_employees = $this->CI->session->userdata('group_employees');
        if(isset($group_employees[$group_id])) {
            $list = $group_employees[$group_id]['list'];
            if(!empty($list)) {
                foreach($list as $key => $val) {
                    if($val['id'] == $employee_id)
                        $key_del = $key;
                }

                if(isset($key_del)){
                    unset($group_employees[$group_id]['list'][$key_del]);
                    $group_employees[$group_id]['default'] = false;
                    $this->CI->session->set_userdata('group_employees', $group_employees);

                    if($group_id == 2)
                        $this->CI->session->unset_userdata('sale_deliverer_id');
                }
            }
        }
    }
    
    /**
     * @author ToiNT
     */
    public function set_supporter_for_list($employee_id) {
        $employee_info = $this->CI->Employee->get_information($employee_id);
        if (!empty($employee_info)) {
            $supporter_list = $this->CI->session->userdata('supporter_list');
            if (isset($supporter_list) && !empty($supporter_list[$employee_id])) {
                $response = array('flag'=>'true');
                return $response;
            }
            $employee = array(
                'id' => $employee_id,
                'name' => $employee_info['first_name'] .  ' ' . $employee_info['last_name']
            );
            if (empty($supporter_list)) {
                $supporter_list = [];
            }
            
            $supporter_list[$employee_id] = $employee;
            
            $this->CI->session->set_userdata('supporter_list', $supporter_list);
        }
    }
    
    /**
     * @author ToiNT
     */
    public function delete_supporter_list($employee_id) {
        $supporter_list = $this->get_supporter_list();
        if (!empty($supporter_list)) {
            $new_supporter_list = [];
            foreach($supporter_list as $item) {
                if ($item['id'] != $employee_id) {
                    $new_supporter_list[$item['id']] = $item;
                }
            }
            $this->CI->session->set_userdata('supporter_list', $new_supporter_list);
        }
    }
    
    public function set_employee_for_group($employee_id, $group_id) {
        $group_employees = $this->CI->session->userdata('group_employees');

        if(isset($group_employees[$group_id])) {
            $employee_info = $this->CI->Employee->get_information($employee_id);
            $list = $group_employees[$group_id]['list'];

            $flag = true;
            if(!empty($list)) {
                foreach($list as $item) {
                    if($employee_id == $item['id']) {
                        $flag = false;
                        break;
                    }
                }
            }

            if($flag == false) {
                $response = array('flag'=>'true');
                return $response;
            }

            if($group_id == 2) {
                $list_emp_ids = array();
                if(!empty($list)) {
                    foreach($list as $val)
                        $list_emp_ids[] = $val['id'];

                    if(count($list_emp_ids)>=1) {
                        $response = array('flag'=>'false', 'msg'=>'Chỉ được một người giao hàng.');
                        return $response;
                    }

                    $this->CI->session->set_userdata('sale_deliverer_id', $employee_id);
                }

            }

            $list[] = array(
                'id' => $employee_id,
                'name' => $employee_info['first_name'] .  ' ' . $employee_info['last_name']
            );

            $group_employees[$group_id]['list'] = $list;
            $this->CI->session->set_userdata('group_employees', $group_employees);

            $response = array('flag'=>'true');

            return $response;

        }
    }

    public function set_service_id($service_id) {
        $this->CI->session->set_userdata('service_id', $service_id);
    }

    public function set_group_employees($location_groups, $options = null) {
        $group_employees = $this->CI->session->userdata('group_employees');
   
        if(empty($group_employees)) {
            foreach($location_groups as $key => $val) {
                if ($options == null) {
                    $array[$key]['list'] = isset($location_groups[$key]['default_list'])?$location_groups[$key]['default_list']:'';
                }
                   
                elseif($options['task'] == 'change_sale')
                    $array[$key]['list'] = $location_groups[$key]['list'];
                    
                if($key == 2 && !empty($array[$key]['list'])) {
                    $delivery_id = $array[$key]['list'][0]['id'];
                    $this->CI->session->set_userdata('sale_deliverer_id', $delivery_id);
                }
            }

            $this->CI->session->set_userdata('group_employees', $array);

        }
    }

    public function set_sale_code($sale_code) {
        $this->CI->session->set_userdata('sale_code', $sale_code);
    }

    public function get_supporter()
    {
        return $this->CI->session->userdata('sale_supporter_id');
    }

    public function get_group_employees() {
        return $this->CI->session->userdata('group_employees');
    }
     //Alain Multiple Payments
    function get_payments()
    {
        if($this->CI->session->userdata('payments') === NULL || $this->CI->session->userdata('payments') == ''){
            $this->set_payments(array());
        }
        return $this->CI->session->userdata('payments');
    }

    //Alain Multiple Payments
    function set_payments($payments_data)
    {
        $this->CI->session->set_userdata('payments',$payments_data);
        $this->update_register_cart_data();
    }
    
    function set_emp_supporter_list($sale_id) {
        if (!empty($sale_id)) {
            $supporters = $this->CI->Sale->get_employee_list_from_sale(array('sale_id' => $sale_id));
            if (!empty($supporters)) {
                foreach ($supporters as $emp) {
                    $this->set_supporter_for_list($emp['employee_id']);
                }
            }
        }
    }
    
    
    
    function copy_entire_sale($sale_id, $is_receipt = false)
    {
        $this->empty_cart();
        $this->delete_customer(false);
        $sale_taxes = $this->get_taxes($sale_id);

        $sale_info = $this->CI->Sale->getInfo($sale_id);
        $sale_info_tmp = $this->get_sale($sale_id);
        $cart_tmp      = $sale_info_tmp['cart'];
        $cart          = array();
        $this->set_emp_supporter_list($sale_id);
        
        if(!empty($cart_tmp)) {
            foreach($cart_tmp as $val) {
                $cart[$val['line']] = $val;
            }
        }
        foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
        {
            $item_info = $this->CI->Item->get_info($row->item_id);
            $price_to_use = $row->item_unit_price;
            //If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
            if ($item_info->tax_included && empty($sale_taxes) && !$is_receipt)
            {
                $this->CI->load->helper('items');
                $price_to_use = get_price_for_item_including_taxes($row->item_id, $row->item_unit_price);
            }
            elseif($item_info->tax_included)
            {
                $this->CI->load->helper('items');

                $price_to_use = get_price_for_item_including_taxes($row->line, $row->item_unit_price,$sale_id);
            }

            if($sale_info['return'] == 1)
                $row->quantity_purchased = $row->quantity_purchased * (-1);
						
						$price_to_use_override =null;
            if(isset($cart[$row->line])) {
                $item = $cart[$row->line];
                $don_gia = $item['quantity_exchange'] * $item['unit_price'];
                if($item['tax_included'])
                    $don_gia = $don_gia + $item['tax_by_unit'];

                $price_to_use_override = $don_gia;
            }
            $this->add_item(
                $row->item_id,
                $row->quantity_purchased,
                $row->discount_percent,
                $price_to_use,
                $row->item_cost_price,
                $row->description,
                $row->serialnumber,
                TRUE,
                $row->line,
                FALSE, 
                $sale_id, 
                $price_to_use_override,
                $row->item_name,
                $row->calculatedPrice
                );
                
            $cartItemsCategory[$row->line] = $item_info->category_id;
        }
        if (!empty($cartItemsCategory)) {
            $this->setCartItemsCategory($cartItemsCategory);
        }
        foreach($this->CI->Sale->get_sale_item_kits($sale_id)->result() as $row)
        {

            $item_kit_info = $this->CI->Item_kit->get_info($row->item_kit_id);
            $price_to_use = $row->item_kit_unit_price;

            //If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
            if ($item_kit_info->tax_included && empty($sale_taxes) && !$is_receipt)
            {
                $this->CI->load->helper('item_kits');
                $price_to_use = get_price_for_item_kit_including_taxes($row->item_kit_id, $row->item_kit_unit_price);
            }
            elseif ($item_kit_info->tax_included)
            {
                $this->CI->load->helper('item_kits');
                $price_to_use = get_price_for_item_kit_including_taxes($row->line, $row->item_kit_unit_price,$sale_id);
            }

            if($sale_info['return'] == 1)
                $row->quantity_purchased = $row->quantity_purchased * (-1);

            $this->add_item_kit('KIT '.$row->item_kit_id,$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->item_kit_cost_price,$row->description, TRUE, $row->line, FALSE);
        }

        # dữ liệu xem lại hóa đơn xem tại bảng sales_payments
        foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
        {
            $data_hoa_don = $this->CI->Sale->get_sale_payments($sale_id);
            $this->add_payment($row->payment_type,$row->payment_amount,$row->no_dau,$row->no_cuoi,$row->payment_date, $row->truncated_card, $row->card_issuer, $row->auth_code, $row->ref_no, $row->cc_token, $row->acq_ref_data, $row->process_data, $row->entry_method, $row->aid, $row->tvr, $row->iad, $row->tsi, $row->arc, $row->cvm, $row->tran_type, $row->application_label,$sale_info['is_stock_out'],$sale_info['suspended']);

        }
	   $this->add_more_customer_to_service_contract($this->CI->Sale->get_customer_to_service($sale_id)->result_array());

        $this->update_register_cart_data();

        $customer_info = $this->CI->Sale->get_customer($sale_id);
        $this->set_customer($customer_info->person_id, false);

        $this->setSaleComments($this->CI->Sale->getSaleComments($sale_id));
        $this->set_comment($this->CI->Sale->get_comment($sale_id));
        $this->set_comment_on_receipt($this->CI->Sale->get_comment_on_receipt($sale_id));

        $this->set_sold_by_employee_id($this->CI->Sale->get_sold_by_employee_id($sale_id));
        $this->set_deleted_taxes($this->CI->Sale->get_deleted_taxes($sale_id));

        $saleInfo = $this->CI->Sale->getInfo($sale_id);

        $this->set_deliverer($saleInfo['deliverer']);
        $this->set_delivery_date($saleInfo['delivery_date']);
        $this->set_service_id($saleInfo['service_id']);
        $this->set_temp_service($saleInfo['service_id']);
        $this->set_sale_status($saleInfo['sale_status_id']);
        $this->set_sale_code($saleInfo['code']);
        $this->set_sale_creator($saleInfo['employee_id']);
        
        if($saleInfo['assigment'] == 1)
            $this->set_mode('assigment');

        if($saleInfo['return'] == 1)
            $this->set_mode('return');

        if($saleInfo['is_vat'] == 1)
            $this->set_mode('vat_order');

        if($saleInfo['store_account_payment'] == 1 || $saleInfo['store_account_payment'] == 2) {
            $this->set_mode('store_account_payment');
            $this->set_store_account_payment_value($saleInfo['store_account_payment']);
        }
		
		// only setting for Rem Ha My
		if($this->CI->config->item('company_user') == 'remHaMy') {
			
			$this->clearItemContainsLine();
			$this->CI->db->select('*')
				->from('sale_item_contains_line_rem_ha_my')
				->where('sale_id', $sale_id);
			$listItemsContainsLine = $this->CI->db->get()->result_array();
			$sessionItemContainsLine = [];
			foreach ($listItemsContainsLine as $itemContainsLine) {
				$this->CI->db->select('*')
					->from('item_line_rem_ha_my')
					->where('createdItemPossition', $itemContainsLine['id']);
				$listLine = $this->CI->db->get()->result_array();
				$listLineForSession = [];
				foreach ($listLine as  $line) {
					$listLineForSession[] = $line['line'];
					if (!empty($line['attribute_set_id'])) {
						$itemAttributes     = $this->CI->Attribute_set->get_attributes($line['attribute_set_id']);
						$cartItemsAttribute[$line['line']] = $itemAttributes;	
						$this->setCartItemsAttribute($cartItemsAttribute);
						$cartItemsAttributeSet[$line['line']] = $line['attribute_set_id'];
						$this->setCartItemsAttributeSet($cartItemsAttributeSet);
					}

					
					$this->CI->db->select('*')
						->from('sale_item_attribute_value_rem_ha_my')
						->where('itemLineid', $line['id']);
						
					$listLineAttributeValue = $this->CI->db->get()->result_array();
					$attribute_values = [];
					foreach ($listLineAttributeValue as  $lineAttributeValue) {
						$attribute_values[$lineAttributeValue['attributeId']] = new stdClass();
						$attribute_values[$lineAttributeValue['attributeId']]->entity_value = $lineAttributeValue['attributeValue'];
					}
					$cartItemsAttributeValue[$line['line']] = $attribute_values;
					$this->setCartItemsAttributeValue($cartItemsAttributeValue);
				}
				$itemsContainsLineForSession['itemName'] = $itemContainsLine['createdItemName'];
				$itemsContainsLineForSession['line']     = $listLineForSession;
				$sessionItemContainsLine[] = $itemsContainsLineForSession;
			}
			
			$this->setItemContainsLine($sessionItemContainsLine);
		}


    }

    # lưu dữ liệu bán hàng vào session sau đó show ra
    function add_payment($payment_type = '',$payment_amount = '',$no_dau = 0,$no_cuoi = 0,$payment_date = false, $truncated_card = '', $card_issuer = '', $auth_code = '', $ref_no = '', $cc_token='', $acq_ref_data = '', $process_data = '', $entry_method='', $aid= '',$tvr='',$iad='', $tsi='',$arc='',$cvm='',$tran_type='',$application_label = '',$is_stock_out='',$suspended = false)
    {

        $payments= $this->get_payments();

        $payment = array(
            'payment_type'=>$payment_type,
            'payment_amount'=>$payment_amount,
            'no_dau'=>$no_dau, // hiển thị nợ đầu
            'no_cuoi'=>$no_cuoi, // hiển thị nợ cuối
            'payment_date' => $payment_date !== FALSE ? $payment_date : date('Y-m-d H:i:s'),
            'truncated_card' => $truncated_card,
            'card_issuer' => $card_issuer,
            'auth_code' => $auth_code,
            'ref_no' => $ref_no,
            'cc_token' => $cc_token,
            'acq_ref_data' => $acq_ref_data,
            'process_data' => $process_data,
            'entry_method' => $entry_method,
            'aid' => $aid,
            'tvr' => $tvr,
            'iad' => $iad,
            'tsi' => $tsi,
            'arc' => $arc,
            'cvm' => $cvm,
            'tran_type' => $tran_type,
            'application_label' => $application_label,
            'is_stock_out'=>$is_stock_out,
            'suspended'=>$suspended
        );

        $payments[]=$payment;
        $this->set_payments($payments);
        return true;
    }


    function edit_item(
                    $line,
                    $description = NULL,
                    $serialnumber = NULL,
                    $quantity = NULL,
                    $discount = NULL,
                    $price = NULL,
                    $cost_price = NULL,
                    $measureId = NULL, 
                    $name = null, 
                    $calculatedPrice = null,
                    $cost_price_interval = null,
                    $unit_price_interval=null
                    )
    {
        $items = $this->get_cart();
        if(isset($items[$line]))
        {
            if ($name !== NULL ) {
                $items[$line]['name'] = $name;
            }
            if ($description !== NULL ) {
                $items[$line]['description'] = $description;
            }
            if ($serialnumber !== NULL ) {
                $items[$line]['serialnumber'] = $serialnumber;
            }
            if ($quantity !== NULL ) {
                $items[$line]['quantity'] = $quantity;
            }
            if ($discount !== NULL ) {
                $items[$line]['discount'] = $discount;
            }
            if ($price !== NULL ) {
                $items[$line]['price'] = round($price, 2);
            }
            if ($cost_price_interval !== NULL ) {
                $items[$line]['cost_price_interval'] = ($cost_price_interval);
            }
            if ($unit_price_interval !== NULL ) {
                $items[$line]['unit_price_interval'] = ($unit_price_interval);
            }
            if ($cost_price !== NULL ) {
                $items[$line]['cost_price'] = $cost_price;
            }
            if ($calculatedPrice !== NULL ) {
                $items[$line]['calculatedPrice'] = $calculatedPrice;
            }

            if ($measureId /* && ($this->get_mode() == 'receive' || $this->get_mode() == 'purchase_order') */) {
                $items[$line]['measure_id'] = (int) $measureId;
                $measure = $this->CI->Measure->getInfo((int) $measureId);
                $items[$line]['measure'] = $measure->name;
                $itemObj = $this->CI->Item->get_info($items[$line]['item_id']);
                if($measureId != $itemObj->measure_id) {
                    $items[$line]['price'] = $this->getPriceByMeasureConverted($items[$line]['item_id'], (int) $measureId);
                } else {
                    $items[$line]['price'] = $itemObj->unit_price;
                }
            }

            $this->set_cart($items);

            return true;
        }

        return false;
    }

    protected function getPriceByMeasureConverted($itemId = 0, $measureConvertedId = 0){
        $itemObj = $this->CI->Item->get_info($itemId);
        $convertedValue = $this->CI->ItemMeasures->getConvertedValue($itemId, $measureConvertedId);
        if(isset($itemObj->unit_price) && isset($convertedValue->qty_converted)) {
        $result = $itemObj->unit_price * $convertedValue->qty_converted * $convertedValue->unit_price_percentage_converted / 100;
        } else $result ='';
        return $result;
    }

    function add_item(
        $item_id,
        $quantity=1,
        $discount=0,
        $price=null,
        $cost_price = null,
        $description=null,
        $serialnumber=null,
        $force_add = FALSE,
        $line = FALSE,
        $update_register_cart_data = TRUE,
        $saleId = 0,
        $price_to_use_override = NULL,
        $item_name = null,
        $calculatedPrice = 0
    )
    {
        $store_account_item_id = $this->CI->Item->get_store_account_item_id();


        //Do NOT allow item to get added unless in store_account_payment mode
        if ($force_add && $this->get_mode() !=='store_account_payment' && $store_account_item_id == $item_id)
        {
            return FALSE;
        }

        //make sure item exists
        if(!$this->CI->Item->exists(does_contain_only_digits($item_id) ? (int)$item_id : -1))
        {
                
            //try to get item id given an item_number
            $item_id = $this->CI->Item->get_item_id($item_id);
            if(!$item_id) { 
                return false;
            }
        }
        else
        {
            $item_id = (int)$item_id;
        }

        if ($this->CI->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
        {
            if ($force_add && $this->will_be_out_of_stock($item_id,$quantity))
            { 
                return FALSE;
            }
        }

        $item_info = $this->CI->Item->get_info($item_id);
        $item_location_info = $this->CI->Item_location->get_info($item_id);

        //Alain Serialization and Description

        //Get all items in the cart so far...
        $items = $this->get_cart();
        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

        $maxkey=0;                       //Highest key so far
        $itemalreadyinsale=FALSE;        //We did not find the item yet.
        $insertkey=0;                    //Key to use for new entry.
        $updatekey=0;                    //Key to use to update(quantity)

        foreach ($items as $item)
        {
            //We primed the loop so maxkey is 0 the first time.
            //Also, we have stored the key in the element itself so we can compare.

            if($maxkey <= $item['line'])
            {
                $maxkey = $item['line'];
            }

            if(isset($item['item_id']) && $item['item_id']==$item_id)
            {
                $itemalreadyinsale=TRUE;
                $updatekey=$item['line'];

                if($item_info->description==$items[$updatekey]['description'] && $item_info->name==lang('common_giftcard'))
                { 
                    return false;
                }
            }
        }

        $insertkey=$maxkey+1;

        $today =  strtotime(date('Y-m-d'));
        $price_to_use= $this->get_price_for_item($item_id);

        $item_info = $this->CI->Item->get_info($item_id);
        $item_location_info = $this->CI->Item_location->get_info($item_id);
        $measure = $this->CI->Measure->getInfo($item_info->measure_id);
        $listTag = $this->CI->Tag->getTagsByItemId($item_id);
        $supplierName = $this->CI->Supplier->get_information($item_info->supplier_id)['company_name'];

        if ($saleId) {
            $measureOnSale = $this->CI->Sale->getMeasureOnSaleItem($saleId, $item_id);
            if($measureOnSale && $measureOnSale->id && $measureOnSale->id != $measure->id) {
                $quantity = $measureOnSale->measure_qty;
                $price = $this->getPriceByMeasureConverted($item_id, (int) $measureOnSale->measure_id);
                $measure = $measureOnSale;
            }
        }

        $cost_price_to_use = ($item_location_info && $item_location_info->cost_price) ? $item_location_info->cost_price : $item_info->cost_price;

        // override item price
        if(!empty($price_to_use_override))
            $item_price = $price_to_use_override;
        else
            $item_price = $price!=null ? $price:$price_to_use;
        //array/cart records are identified by $insertkey and item_id is just another field.
        $item = array(($line === FALSE ? $insertkey : $line)=>
        array(
            'item_id'=>$item_id,
            'line'=>$line === FALSE ? $insertkey : $line,
            'name'=>!empty($item_name)?$item_name:$item_info->name,
            'change_cost_price' =>$item_info->change_cost_price,
            'cost_price' => $cost_price!=null ? $cost_price : $cost_price_to_use,
            'size' => $item_info->size,
            'item_number'=>$item_info->item_number,
            'product_id' => $item_info->product_id,
            'description'=>$description!=null ? $description: $item_info->description,
            'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
            'allow_alt_description'=>$item_info->allow_alt_description,
            'is_serialized'=>$item_info->is_serialized,
            'quantity'=>$quantity,
            'measure_id'=>isset($measure->id)?$measure->id:'',
            'measure' => !empty($measure) ? $measure->name : lang('common_not_set'),
            'cur_quantity' => $item_location_info->quantity,
            'discount'=>$discount,
            'price'=>round($item_price,2),
            'cost_price_interval'=>$item_info->cost_price_interval,
            'unit_price_interval'=>$item_info->unit_price_interval,
            'tax_included'=> $item_info->tax_included,
            'xoay_kho' => $item_info->xoay_kho,
            'stop_producing' => $item_info->stop_producing, 
            'item_category_id' => $item_info->category_id,
            'calculatedPrice' => $calculatedPrice?$calculatedPrice:$item_price,
            'supplier_id' => $item_info->supplier_id?$item_info->supplier_id:'',
            'supplier_name' => $supplierName?$supplierName:'',
            'tag_id' => isset($listTag['id'])?implode($listTag['id']):'',
            'tag_name' => isset($listTag['name'])?implode($listTag['name']):'',
            'category_name' => $item_info->category_name
        )
        );
        //Item already exists and is not serialized, add to quantity
        if($itemalreadyinsale && ($item_info->is_serialized ==0) && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
        {
            $items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
        } else {
            //add to existing array
            $items+=$item;
        }

        $this->set_cart($items,$update_register_cart_data);
        return true;

    }

    function _does_discount_exists($cart)
    {
        foreach($cart as $line=>$item)
        {
            if( (isset($item['discount']) && $item['discount']>0 ) || (isset($item['discount_percent']) && $item['discount_percent']>0 ) )
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    function get_cart_from_sale_id($sale_id) {
        $items = $this->CI->Sale->get_items_by_sale($sale_id);
        $item_kits = $this->CI->Sale->get_items_kit_by_sale($sale_id);
        $result = array();
        $measure_list = $this->CI->Measure->list_item(array('all'=>true), array('task'=>'all'));
        if(!empty($items)) {
            foreach($items as $item) {
                $tmp = array();
                $params = array(
                    'item_id' => $item['item_id'],
                    'sale_id' => $sale_id,
                    'line'    => $item['line']
                );
                $taxes = $this->CI->Sale->get_item_taxes($params, array('task'=>'by-item-line'));
                $price = $this->CI->Sale->price_in_sale($item, $taxes);

                $tmp['item_id']     = $item['item_id'];
                $tmp['line']        = $item['line'];
                $tmp['name']        = $item['item_name'];

                $tmp['cost_price']  = $item['item_cost_price'];
                $tmp['unit_price']  = $item['item_unit_price'];
                $tmp['product_id']  = $item['product_id'];
                $tmp['description'] = $item['description'];
                $tmp['quantity']    = $item['measure_qty'];
                $tmp['measure']     = $measure_list[$item['measure_id']]['name'];

                $tmp['discount']    = $item['discount_percent'];
                $tmp['price']       = $price * $item['quantity_purchased'];
                $tmp['tax_included'] = $item['tax_included'];

                $result[] = $tmp;
            }
        }
        if(!empty($item_kits)) {
            foreach($item_kits as $item_kit) {
                $tmp = array();
                $params = array(
                    'item_kit_id' => $item_kit['item_kit_id'],
                    'sale_id' => $sale_id,
                    'line'    => $item_kit['line']
                );
                $taxes = $this->CI->Sale->get_items_kit_taxes($params, array('task'=>'by-item-kit-line'));
                $price = $this->CI->Sale->price_in_sale($item_kit, $taxes);


                $tmp['item_kit_id'] = $item_kit['item_kit_id'];
                $tmp['line']        = $item_kit['line'];
                $tmp['name']        = $item_kit['item_kit_name'];

                $tmp['cost_price']  = $item_kit['item_kit_cost_price'];
                $tmp['unit_price']  = $item['item_kit_unit_price'];
                $tmp['product_id']  = $item_kit['product_id'];
                $tmp['description'] = $item_kit['description'];
                $tmp['quantity']    = $item_kit['quantity_purchased'];

                $tmp['discount']    = $item_kit['discount_percent'];
                $tmp['price']       = $price * $item_kit['quantity_purchased'];
                $tmp['tax_included'] = $item_kit['tax_included'];

                $result[] = $tmp;

            }
        }

        return $result;
    }

    function get_sale($sale_id, $options = null) {
        global $cached_sale_info;
        if($options == null && isset($cached_sale_info)) {
            return $cached_sale_info;
        }

        $result['info'] = $info = $this->CI->Sale->getInfo($sale_id);
        $sql = " SELECT m.* FROM (
                        SELECT si.sale_id, si.item_id, NULL AS item_kit_id, si.line, si.quantity_purchased, (si.quantity_purchased/si.measure_qty) AS quantity_exchange, si.item_unit_price AS unit_price, m.name AS measure_name,
                        si.item_cost_price*si.quantity_purchased AS tcost,
                        si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100 AS ttotal,

                        (si.item_unit_price*(si.quantity_purchased/si.measure_qty))*(SUM(CASE WHEN sit.cumulative != 1 THEN sit.percent ELSE 0 END)/100)
                        +(((si.item_unit_price*(si.quantity_purchased/si.measure_qty))*(SUM(CASE WHEN sit.cumulative != 1 THEN sit.percent ELSE 0 END)/100)
                        + (si.item_unit_price*(si.quantity_purchased/si.measure_qty)))*(SUM(CASE WHEN sit.cumulative = 1 THEN sit.percent ELSE 0 END))/100) AS tax_by_unit,

                        (si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100)*(SUM(CASE WHEN sit.cumulative != 1 THEN sit.percent ELSE 0 END)/100)
                        +(((si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100)*(SUM(CASE WHEN sit.cumulative != 1 THEN sit.percent ELSE 0 END)/100)
                        + (si.item_unit_price*si.quantity_purchased-si.item_unit_price*si.quantity_purchased*si.discount_percent/100)) *(SUM(CASE WHEN sit.cumulative = 1 THEN sit.percent ELSE 0 END))/100) AS tax,
                        s.suspended, s.store_account_payment, s.was_layaway, s.return, COALESCE(si.item_name,i.name)  AS name  , i.tax_included, si.measure_qty AS quantity, si.discount_percent, si.description,
                        cate.id as category_id
                        FROM ".$this->CI->db->dbprefix('sales_items')." AS si
                        
                        LEFT JOIN ".$this->CI->db->dbprefix('sales')." AS s ON s.sale_id = si.sale_id
                        LEFT JOIN ".$this->CI->db->dbprefix('measures')." AS m ON si.measure_id = m.id
                        LEFT JOIN ".$this->CI->db->dbprefix('items')." AS i ON si.item_id = i.item_id
                        LEFT JOIN ".$this->CI->db->dbprefix('categories')." AS cate ON cate.id = i.category_id
                        LEFT JOIN ".$this->CI->db->dbprefix('sales_items_taxes')." AS sit ON si.sale_id = sit.sale_id AND si.item_id = sit.item_id AND si.line = sit.line
                        WHERE si.sale_id = $sale_id
                        GROUP BY si.sale_id, si.item_id, si.line

                        UNION ALL

                        SELECT sk.sale_id, NULL AS item_id, sk.item_kit_id, sk.line, sk.quantity_purchased, sk.quantity_purchased AS quantity_exchange , sk.item_kit_unit_price AS unit_price, NULL AS measure_name,
                        sk.item_kit_cost_price*sk.quantity_purchased AS tcost,
                        sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100 AS ttotal,

                        (sk.item_kit_unit_price*sk.quantity_purchased)*(SUM(CASE WHEN skt.cumulative != 1 THEN skt.percent ELSE 0 END)/100)
                        +(((sk.item_kit_unit_price*sk.quantity_purchased)*(SUM(CASE WHEN skt.cumulative != 1 THEN skt.percent ELSE 0 END)/100)
                        + (sk.item_kit_unit_price*sk.quantity_purchased)) *(SUM(CASE WHEN skt.cumulative = 1 THEN skt.percent ELSE 0 END))/100) AS tax_by_unit,

                        (sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100)*(SUM(CASE WHEN skt.cumulative != 1 THEN skt.percent ELSE 0 END)/100)
                        +(((sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100)*(SUM(CASE WHEN skt.cumulative != 1 THEN skt.percent ELSE 0 END)/100)
                        + (sk.item_kit_unit_price*sk.quantity_purchased-sk.item_kit_unit_price*sk.quantity_purchased*sk.discount_percent/100)) *(SUM(CASE WHEN skt.cumulative = 1 THEN skt.percent ELSE 0 END))/100) AS tax,

                        s.suspended, s.store_account_payment, s.was_layaway, s.return, k.name as name, k.tax_included, sk.quantity_purchased AS quantity, sk.discount_percent, sk.description,
                        cate.id as category_id
                        FROM ".$this->CI->db->dbprefix('sales_item_kits')." AS sk
                        LEFT JOIN ".$this->CI->db->dbprefix('sales')." AS s ON s.sale_id = sk.sale_id
                        LEFT JOIN ".$this->CI->db->dbprefix('item_kits')." AS k ON sk.item_kit_id = k.item_kit_id
                        LEFT JOIN ".$this->CI->db->dbprefix('categories')." AS cate ON cate.id = k.category_id
                        LEFT JOIN ".$this->CI->db->dbprefix('sales_item_kits_taxes')." AS skt ON sk.sale_id = skt.sale_id AND sk.item_kit_id = skt.item_kit_id AND sk.line = skt.line
                        WHERE sk.sale_id = $sale_id
                        GROUP BY sk.sale_id, sk.item_kit_id, sk.line
                    ) AS m";


        $query     =   $this->CI->db->query($sql);
        $result_tmp    =   $query->result_array();

        $this->CI->db->flush_cache();

        $discount_id = $this->CI->Item->create_or_update_flat_discount_item();

        $loi_nhuan_gop = 0;
        $doanh_thu     = 0;
        $giam_gia      = 0;
        $gia_tri_don_hang = 0;
        if(!empty($result_tmp)) {
            foreach($result_tmp as $key => $val) {
                $gia_tri_don_hang += $val['ttotal'] + $val['tax'];
                $loi_nhuan_gop    = $loi_nhuan_gop + ($val['ttotal'] - $val['tcost']);

                if($val['item_id'] != $discount_id)
                    $doanh_thu     = $doanh_thu + $val['ttotal'];
                else {
                    unset($result_tmp[$key]);
                    $giam_gia      = $giam_gia + $val['ttotal'];
                }
            }
        }
        $cart = thay_doi_key_theo_line($result_tmp);
        

        $result['cart'] = $cart;

        $giam_gia = abs($giam_gia);
        $result['giam_gia_ca_don_hang'] = $giam_gia;

        $gift_card_payment_from_sale = $this->CI->Sale->get_gift_card_payment_from_sale(array('sale_id'=>$sale_id));
        $point_payment_from_sale     = $this->CI->Sale->get_point_payment_from_sale(array('sale_id'=>$sale_id));

        $sale_expenses = $this->CI->Expense->get_items(array('sale_id'=>$sale_id), array('task'=>'by-sale-or-receiving'));

        $chi_phi_sp      = 0;
        $chi_phi_sp_list = array();
        if(!empty($sale_expenses)) {
            foreach($sale_expenses as $val) {
                $chi_phi_sp_list[] = $val;
                $chi_phi_sp        = $chi_phi_sp + ($val['expense_amount'] + $val['expense_tax'])*$val['expense_type'];
            }
        }

        $loi_nhuan_gop = $loi_nhuan_gop - $gift_card_payment_from_sale - $point_payment_from_sale - $chi_phi_sp;
        $loi_nhuan     = $loi_nhuan_gop;
        if($doanh_thu == 0) $doanh_thu = 1;
        $ty_suat       = $loi_nhuan / $doanh_thu;

        // hoa hồng
        if($options['included_commission'] == true) {
            $hoa_hong = 0;
            if($info['commission_status'] == 1) {
                $this->CI->db -> select("SUM(commission) AS sum_commission")
                              -> from('sales_commission')
                              -> where('sale_id', $info['sale_id']);

                $query = $this->CI->db->get();

                $sale_commission = $query->row_array();

                $this->CI->db->flush_cache();
                $hoa_hong = !empty($sale_commission['sum_commission']) ? $sale_commission['sum_commission'] : 0;
            }

            $result['hoa_hong'] = $hoa_hong;
        }

        $result['chi_phi_sp_list']  = $chi_phi_sp_list;
        $result['loi_nhuan_gop']    = $loi_nhuan_gop;
        $result['loi_nhuan']        = $loi_nhuan;
        $result['ty_suat']          = $ty_suat;
        $result['doanh_thu']        = $doanh_thu;
        $result['chi_phi_sp']       = $chi_phi_sp;
        $result['gia_tri_don_hang'] = $gia_tri_don_hang;

        if($options == null) $cached_sale_info = $result;

        return $result;
    }

    function getSale($sale_id, $config)
    {
        $this->CI->load->helper('sale');
        $this->CI->load->model('Sale');
        $this->CI->load->model('Customer');
        $this->CI->load->model('Tier');
        $this->CI->load->model('Register');
        $this->CI->load->model('Employee');

        $data['sale_id'] = $this->CI->config->item('sale_prefix') . ' ' . $sale_id;
        $data['is_sale'] = FALSE;
        $sale_info = $this->CI->Sale->get_info($sale_id)->row_array();

        $data['service_id']            = $sale_info['service_id'];
        $data['code']                  = $sale_info['code'];
        $data['location_id']           = $sale_info['location_id'];
        $data['min_profit_commission'] = !empty($sale_info['min_profit_commission'])?$sale_info['min_profit_commission']:'';

        $data['service_code'] = '';
        if(!empty($data['service_id'])) {
            $service_info = $this->CI->Service->get_item(array('id'=>$data['service_id'], 'all'=>true));
            $data['service_code'] = $service_info['code'];
        }

        $data['cart']= $this->get_cart_from_sale_id($sale_id);

        $customer_id= $sale_info['customer_id'];

        // [4biz] Get customer balance before make orther

        if($customer_id > 0)
        {
            $cust_info=$this->CI->Customer->get_info($customer_id);
            if ($cust_info->balance !=0)
            {
                $data['customer_balance_for_sale_before'] = $cust_info->balance;
            }
        }

        $data['payments'] = $this->CI->Sale->get_payment_from_sale(array('sale_id'=>$sale_id));
				$data['more_customers_in_service'] = $this->CI->Sale->get_customer_service_from_sale(array('sale_id'=>$sale_id));																																														 
        $data['is_sale_cash_payment'] = $this->CI->Sale->get_cash_payment_from_sale(array('sale_payments'=> $data['payments']));

        $data['show_payment_times'] = TRUE;
        $data['signature_file_id'] = $sale_info['signature_image_id'];


        $tier_id = $sale_info['tier_id'];
        $tier_info = $this->CI->Tier->get_info($tier_id);
        $data['tier'] = $tier_info->name;
        $data['register_name'] = $this->CI->Register->get_register_name($sale_info['register_id']);
        $data['override_location_id'] = $sale_info['location_id'];
        $data['deleted'] = $sale_info['deleted'];

        $data['taxes']=$this->get_taxes($sale_id);
        $data['subtotal']=$this->get_subtotal(false, array('cart'=>$data['cart']), array('task'=>'self'));

        $data['total']=$this->get_total(false, array('cart'=>$data['cart'], 'taxes'=>$data['taxes']), array('task'=>'self'));

        $data['receipt_title']= $config->item('override_receipt_title') ? $config->item('override_receipt_title') : lang('sales_receipt');
        $data['comment'] = $this->CI->Sale->get_comment($sale_id);
        $data['show_comment_on_receipt'] = $this->CI->Sale->get_comment_on_receipt($sale_id);
        $data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
        $customer_id=$this->get_customer();

        $emp_info=$this->CI->Employee->get_info($sale_info['employee_id']);
        $sold_by_employee_id=$sale_info['sold_by_employee_id'];
        $sale_emp_info=$this->CI->Employee->get_info($sold_by_employee_id);
        $data['sale_emp_info'] = $sale_emp_info;
        $data['payment_type']=$sale_info['payment_type'];
        $data['amount_change']=$this->get_amount_due($sale_id) * -1;
        $data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
        $data['ref_no'] = $sale_info['cc_ref_no'];
        $data['auth_code'] = $sale_info['auth_code'];
        $data['discount_exists'] = $this->_does_discount_exists($data['cart']);
        $data['customer_id'] = $sale_info['customer_id'];


        if($sale_info['customer_id']!=-1)
        {
            $cust_info=$this->CI->Customer->get_info($sale_info['customer_id']);
            $data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
            $data['customer_address_1'] = $cust_info->address_1;
            $data['customer_address_2'] = $cust_info->address_2;
            $data['customer_city'] = $cust_info->city;
            $data['customer_company_name'] = $cust_info->company_name;
            $data['customer_state'] = $cust_info->state;
            $data['customer_zip'] = $cust_info->zip;
            $data['customer_country'] = $cust_info->country;
            $data['customer_phone'] = $cust_info->phone_number;
            $data['customer_email'] = $cust_info->email;
            $data['customer_points'] = $cust_info->points;
            $data['customer_position'] = $cust_info->position;
            $data['customer_account_number'] = $cust_info->account_number;
            $data['sales_until_discount'] = $config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;

            if ($cust_info->balance !=0)
            {
                $data['customer_balance_for_sale'] = $cust_info->balance;
            }
        }

        $data['sale_id']=$config->item('sale_prefix').' '.$sale_id;
        $data['sale_id_raw']=$sale_id;
        $data['store_account_payment'] = FALSE;

        foreach($data['cart'] as $item)
        {
            if ($item['name'] == lang('sales_store_account_payment'))
            {
                $data['store_account_payment'] = TRUE;
                break;
            }
        }

        if ($sale_info['suspended'] > 0)
        {
            if ($sale_info['suspended'] == 1)
            {
                $data['sale_type'] = lang('common_layaway');
            }
            elseif ($sale_info['suspended'] == 2)
            {
                $data['sale_type'] = lang('common_estimate');
            }
        }

        $data['employee_list'] = $this->CI->Sale->get_employee_list_from_sale(array('sale_id'=>$sale_id));

        return $data;
    }

    function get_sale_code() {
        return $this->CI->session->userdata('sale_code');
    }

    function get_service_id() {
        return $this->CI->session->userdata('service_id');
    }

    function get_delivery_items($cart, $delivery, $options = null) {
        $items = array();
        $discount_id = $this->CI->Item->get_item_id_for_flat_discount_item();

        if(!empty($cart)) {
            foreach($cart as $item) {
                if($item['item_id'] != $discount_id){
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

    function create_sale_code() {
        $service_id = $this->get_service_id();
        if(!empty($service_id)) {
						$day 	 = date('d');						 
            $month = date('m');
            $year  = date("y");

            $service_info = $this->CI->Service->get_item(array('id'=>$service_id, 'all'=>true));
            $service_code = $service_info['code'];

            $quantity = $this->CI->Sale->get_quantity_sale_of_current_month(array('service_id'=>$service_id));
            $quantity = $quantity + 1;
            if($quantity <= 99)
                $quantity = '00'.$quantity;

            $result = $service_code.$year.$month.$day.$quantity;
        }

        return !empty($result) ? $result:'';
    }

    function update_employee_commission($emp_group_arr, $sale_id_raw, $change_sale = false, $isSuspendedBefore = false) {
        $order_sale = $this->get_sale($sale_id_raw);
        $commission_info['commission_time_method'] = $order_sale['info']['commission_time_method'];
        $commission_info['commission_method']      = $order_sale['info']['commission_method'];
        $commission_info['min_profit']             = $order_sale['info']['min_profit'];
        $commission_info['commission_status']      = $order_sale['info']['commission_status'];

        if($commission_info['commission_method'] == 'order') {
            $sales_commission = array();
            foreach($emp_group_arr as $val) {
                $tmp = array();
                $tmp['sale_id']     = $sale_id_raw;
                $tmp['employee_id'] = $val['employee_id'];
                $tmp['group_id']    = $val['group_id'];

                $location_id = $this->CI->Employee->get_logged_in_employee_current_location_id();
                $employee_id = $val['employee_id'];
                $group_id    = $val['group_id'];

                $location_group_employees_info = $this->CI->Location->location_group_employees_info(array('location_id'=>$location_id, 'group_id'=>$group_id));
                $tmp['commission_percent'] = $location_group_employees_info['commission_percent'];
                $tmp['commission_percent_type'] = $location_group_employees_info['commission_percent_type'];

                if($tmp['commission_percent'] > 0 && $order_sale['ty_suat'] >= ($commission_info['min_profit'] * 0.01)) {
                    if($tmp['commission_percent_type'] == 'profit') {
                        $tmp['commission'] = $tmp['commission_percent'] * $order_sale['loi_nhuan'] * 0.01;
                    }elseif($tmp[''] == 'selling_price')
                        $tmp['commission'] = $tmp['commission_percent'] * $order_sale['doanh_thu'] * 0.01;

                    $sales_commission[] = $tmp;
                }
            }

            if(!empty($sales_commission)) {
                $this->CI->db->insert_batch('sales_commission', $sales_commission);
            }

            if($change_sale == false) {
                if($commission_info['commission_time_method'] == 'order'){
                    $this->CI->db->where("sale_id",$sale_id_raw);
                    $this->CI->db->update('sales', array('commission_status'=> 1));
                }else {
                    $this->CI->db->where("sale_id",$sale_id_raw);
                    $this->CI->db->update('sales', array('commission_status'=> 0));
                }
            }else {
                if ($isSuspendedBefore) {
                    $this->CI->db->where("sale_id",$sale_id_raw);
                    $this->CI->db->update('sales', array('commission_status'=> 1));
                }
                else {
                    $this->CI->db->where("sale_id",$sale_id_raw);
                    $this->CI->db->update('sales', array('commission_status'=> $order_sale['info']['commission_status']));
                }
 
            }
        }
    }

    function _update_employee_commission($commission_info, $emp_group_arr, $sale_id_raw) {
        if($commission_info['commission_method'] == 'order') {
            $sales_commission = array();
            foreach($emp_group_arr as $val) {
                $tmp = array();
                $tmp['sale_id']     = $sale_id_raw;
                $tmp['employee_id'] = $val['employee_id'];
                $tmp['group_id']    = $val['group_id'];

                $location_id = $this->CI->Employee->get_logged_in_employee_current_location_id();
                $employee_id = $val['employee_id'];
                $group_id    = $val['group_id'];

                $location_group_employees_info = $this->CI->Location->location_group_employees_info(array('location_id'=>$location_id, 'group_id'=>$group_id));
                $tmp['commission_percent'] = $location_group_employees_info['commission_percent'];
                $tmp['commission_percent_type'] = $location_group_employees_info['commission_percent_type'];

                if($commission_info['commission_time_method'] == 'order')
                    $tmp['status'] = 1;
                else
                    $tmp['status'] = 0;

                $tmp['commission'] = 0;

                if($tmp['commission_percent'] > 0) {
                    $sales_commission[] = $tmp;
                }

            }

            if(count($sales_commission) > 0)
                $this->CI->db->insert_batch('sales_commission', $sales_commission);

        }elseif($commission_info['commission_method'] == 'items') {
            $sales_items_commission = array();
            $items = $this->CI->Sale->get_items_by_sale($sale_id_raw);
            foreach($items as $item) {
                foreach($emp_group_arr as $val) {
                    $tmp = array();
                    $tmp['sale_id'] = $item['sale_id'];
                    $tmp['item_id'] = $item['item_id'];
                    $tmp['line']    = $item['line'];

                    $tmp['employee_id'] = $val['employee_id'];
                    $tmp['group_id']    = $val['group_id'];
                    $tmp['status']      = 1;

                    if($item['commission_percent'] != '' || $item['commission_fixed'] != '') {
                        $tmp['commission'] = $item['commission_fixed'];
                        $tmp['commission_percent_type'] = 'fixed';
                        $tmp['commission_percent'] = 0;
                    }else {
                        $location_id = $this->CI->Employee->get_logged_in_employee_current_location_id();
                        $employee_id = $tmp['employee_id'];
                        $group_id    = $tmp['group_id'];

                        $employee_location_commission_info = $this->CI->Employee->get_employee_location_commission_info(array('location_id'=>$location_id, 'group_id'=>$group_id, 'employee_id'=>$employee_id));
                        if(!empty($employee_location_commission_info)) {
                            $tmp['commission'] = 0;
                            $tmp['commission_percent']      = $employee_location_commission_info['commission_percent'];
                            $tmp['commission_percent_type'] = $employee_location_commission_info['commission_percent_type'];
                        }else {
                            $location_group_employees_info = $this->CI->Location->location_group_employees_info(array('location_id'=>$location_id, 'group_id'=>$group_id));

                            if(!empty($location_group_employees_info)) {
                                $tmp['commission'] = 0;
                                $tmp['commission_percent']      = $location_group_employees_info['commission_percent'];
                                $tmp['commission_percent_type'] = $location_group_employees_info['commission_percent_type'];

                            }else
                                $tmp['commission'] = 0;
                        }
                    }

                    if($tmp['commission'] > 0 || $tmp['commission_percent'] > 0)
                        $sales_items_commission[] = $tmp;

                    if(count($sales_items_commission) > 0)
                        $this->CI->db->insert_batch('sales_items_commission', $sales_items_commission);

                }
            }

            $sales_item_kits_commission = array();
            $item_kits = $this->CI->Sale->get_items_kit_by_sale($sale_id_raw);
            foreach($item_kits as $item_kit) {
                foreach($emp_group_arr as $val) {
                    $tmp = array();
                    $tmp['sale_id']     = $item_kit['sale_id'];
                    $tmp['item_kit_id'] = $item_kit['item_kit_id'];
                    $tmp['line']        = $item_kit['line'];

                    $tmp['employee_id'] = $val['employee_id'];
                    $tmp['group_id'] = $val['group_id'];

                    if($item_kit['commission_percent'] != '' || $item_kit['commission_fixed'] != '') {
                        $tmp['commission'] = $item_kit['commission_fixed'];
                        $tmp['commission_percent_type'] = 'fixed';
                        $tmp['commission_percent'] = 0;
                    }else {
                        $location_id = $this->CI->Employee->get_logged_in_employee_current_location_id();
                        $employee_id = $tmp['employee_id'];
                        $group_id    = $tmp['group_id'];

                        $employee_location_commission_info = $this->CI->Employee->get_employee_location_commission_info(array('location_id'=>$location_id, 'group_id'=>$group_id, 'employee_id'=>$employee_id));
                        if(!empty($employee_location_commission_info)) {
                            $tmp['commission'] = 0;
                            $tmp['commission_percent']      = $employee_location_commission_info['commission_percent'];
                            $tmp['commission_percent_type'] = $employee_location_commission_info['commission_percent_type'];
                        }else {
                            $location_group_employees_info = $this->CI->Location->location_group_employees_info(array('location_id'=>$location_id, 'group_id'=>$group_id));

                            if(!empty($location_group_employees_info)) {
                                $tmp['commission'] = 0;
                                $tmp['commission_percent']      = $location_group_employees_info['commission_percent'];
                                $tmp['commission_percent_type'] = $location_group_employees_info['commission_percent_type'];

                            }else
                                $tmp['commission'] = 0;
                        }
                    }

                    if($tmp['commission'] > 0 || $tmp['commission_percent'] > 0)
                        $sales_item_kits_commission[] = $tmp;

                    if(count($sales_item_kits_commission) > 0)
                        $this->CI->db->insert_batch('sales_item_kits_commission', $sales_item_kits_commission);
                }
            }

        }
    }

    function check_employee_change_status($old_employee_list, $new_employee_list) {
        $old_employee_tmp = $new_employee_tmp = array();
        if(!empty($old_employee_list)) {
            foreach($old_employee_list as $val) {
                $group_id    = $val['group_id'];
                $employee_id = $val['employee_id'];

                $old_employee_tmp[$group_id . '-' . $employee_id] = $val;
            }
        }

        if(!empty($new_employee_list)) {
            foreach($new_employee_list as $val) {
                $group_id    = $val['group_id'];
                $employee_id = $val['employee_id'];

                $new_employee_tmp[$group_id . '-' . $employee_id] = $val;
            }
        }

        if(!empty($old_employee_tmp)) {
            foreach($old_employee_tmp as $key => $item) {
                if(isset($new_employee_tmp[$key]))
                    unset($new_employee_tmp[$key]);
                else
                    $nguoi_thua[$key] = $val;
            }
        }

        $nguoi_moi = $new_employee_tmp;

        if(empty($nguoi_moi) && empty($nguoi_thua))
            return 'no-change';
        else
            return 'changed';
    }

    function cart_by_category_ids($cart, $category_ids) {
        foreach($cart as $val) {
            $item_ids[] = $val['item_id'];
        }

        $this->CI->db->select("item_id, category_id")
                    ->from('sales_items')
                    ->where('item_id IN ('.implode(',', $item_ids).')')
                    ->where('category_id IN ('.implode(',', $category_ids).')');

        $query = $this->CI->db->get();
        $result_tmp = $query->result_array();
        $this->CI->db->flush_cache();

        $selected_item_ids = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $selected_item_ids[] = $val['item_id'];
        }

        foreach($cart as $key => $item) {
            if(!in_array($item['item_id'], $selected_item_ids))
                unset($cart[$key]);
        }

        return $cart;
    }

    function get_minus_liability(){ // trừ công nợ
        $result = 0;
        $payments = $this->get_payments();
        foreach($payments as $val) {
            if($val['payment_type'] == 'Trừ công nợ')
                $result = $result + $val['payment_amount'];
        }

        return $result;
    }

    function get_debit_payment() {
        $result = 0;
        $payments = $this->get_payments();
        foreach($payments as $val) {
            if(!empty($val['payment_type']) && $val['payment_type'] == 'Sổ ghi nợ')
                $result = $result + $val['payment_amount'];
        }

        return $result;
    }
		//---------------------------------------------------------------
		// for create a service contract only
		//---------------------------------------------------------------
	function add_more_customer_to_service_contract($list_customers)
    {
      if(!empty($list_customers) )
			{
				
				foreach($list_customers as $customer)
				{
					$save_customer = array(
					'person_id'    => $customer['person_id'],
					'last_name'    => $customer['last_name'],
					'first_name'   => $customer['first_name'],
					'passport'     => $customer['ho_chieu'],
					'mail'         => $customer['email'],
					'address'      => $customer['address_1'],
					'phone_number' => $customer['phone_number'],
					'sex'          => $customer['sex'],
					);

					$customers[]=$save_customer;
				}
						
						$this->set_customers($customers);
			}
			return true;
    }
    
    // Chỉ dùng cho khách hàng rèm Hà My
    public function updateCartWhenLineChanged()
    {
        $cart                    = $this->get_cart();
        $itemContainsLine        = $this->getItemContainsLine();
        $attributeCartItemsValue = $this->getCartItemsAttributeValue();
        $cartItemsCategory       = $this->getCartItemsCategory(); 
        $cartItemsAttributeSet   = $this->getCartItemsAttributeSet();

        
        // update số lượng của thanh khi xóa hoặc thêm mới vải
        //Hằng số dựa vào categories trong database 
        $CATEGORY_VAI_CHINH         = 174;
        $CATEGORY_VAI_PHU           = 175;
        $CATEGORY_THANH_PHU_KIEN    = 177;
        $CATEGORY_LOP_YEM_LOP_BEO   = 216;
        //Hằng số dựa vào attributes trong database 
        $ATTR_MET_VAI                = 13;
        $ATTR_WIDTH                  = 2;
        $ATTR_SET_REM_KEO_VAI_RONG_CC_260CM = 4;  
        $ATTR_DON_GIA_VAI            = 9;  
        $ATTR_DON_TREN_MET_NGANG     = 16;  
        $ATTR_DON_GIA_REM_M2         = 18;   
        $ATTR_DON_GIA_M2_HOAN_THIEN  = 19;
        $ATTR_DIEN_TICH_CUA          = 21;  
        $ATTR_GIA_VAI_YEM_BEO        = 37; 
        $ATTR_TONG_TIEN_YEM_BEO      = 38;  
        $ATTR_BEO_HOAC_YEM           = 39;  
        // Hằng số dựa vào measure
        $MEASURE_ID_M               = 4;  
        $MEASURE_M                  = 'M';  
        $MEASURE_ID_M2              = 5;  
        $MEASURE_M2                 = 'M2';  
        
        foreach($itemContainsLine as  $index => $value) {
            $numberOfVai         = 0;
            $numberOfThanh       = 0;
            $lineOfVaiChinh      = 0;
            $lineOfThanhPhuKien  = [];
            $lineOfLopYemLopBeo  = 0;
            foreach ($value['line'] as  $index1 => $value1) {
                $cartItemsAttributeSetInfo = $this->CI->Attribute_set->get_info($cartItemsAttributeSet[$value1]);
                if (strpos($cartItemsAttributeSetInfo->code,'RK') !== false ) {
                    $cartItemsAttributeSetType[$value1] = 'rem_keo';
                } elseif(strpos($cartItemsAttributeSetInfo->code,'RR') !== false ) {
                    $cartItemsAttributeSetType[$value1] = 'rem_roman';
                } else {
                    $cartItemsAttributeSetType[$value1] = 'non_type';
                }
                if($cartItemsCategory[$value1] == $CATEGORY_VAI_CHINH && $attributeCartItemsValue[$value1][$ATTR_BEO_HOAC_YEM]->entity_value != 'co') {
                    $lineOfVaiChinh = $value1;
                }
                if($cartItemsCategory[$value1] == $CATEGORY_VAI_PHU || $cartItemsCategory[$value1] == $CATEGORY_VAI_CHINH) {
                    if (empty($attributeCartItemsValue[$value1][$ATTR_BEO_HOAC_YEM]->entity_value) || $attributeCartItemsValue[$value1][$ATTR_BEO_HOAC_YEM]->entity_value  == 'khong' ) { 
                        $numberOfVai++;
                    } elseif($attributeCartItemsValue[$value1][$ATTR_BEO_HOAC_YEM]->entity_value == 'co') {
                        $lineOfLopYemLopBeo = $value1;
                    }
                }                
                if($cartItemsCategory[$value1] == $CATEGORY_THANH_PHU_KIEN) {
                    $lineOfThanhPhuKien[] = $value1;
                    $numberOfThanh++;
                }  
                if ($cartItemsCategory[$value1] == $CATEGORY_VAI_CHINH || $cartItemsCategory[$value1] == $CATEGORY_VAI_PHU) {
                    $price    = $cart[$value1]['price'];
                    $quantity = $cart[$value1]['quantity'];
                    
                    if ($cartItemsAttributeSetType[$value1] == 'rem_keo') {
                        if ($cartItemsAttributeSet[$value1] == $ATTR_SET_REM_KEO_VAI_RONG_CC_260CM ) {
                            $price = $attributeCartItemsValue[$value1][$ATTR_DON_TREN_MET_NGANG]->entity_value;
                            $quantity = $attributeCartItemsValue[$value1][$ATTR_WIDTH]->entity_value;
                        } else {
                            $quantity   = $attributeCartItemsValue[$value1][$ATTR_MET_VAI]->entity_value;
                            $price      = $attributeCartItemsValue[$value1][$ATTR_DON_GIA_VAI]->entity_value;   
                        }  
                        $cart[$value1]['measure']       = $MEASURE_M;                        
                    } else if ($cartItemsAttributeSetType[$value1] == 'rem_roman') {
                        $price      = $attributeCartItemsValue[$value1][$ATTR_DON_GIA_M2_HOAN_THIEN]->entity_value;
                        $quantity   = $attributeCartItemsValue[$value1][$ATTR_DIEN_TICH_CUA]->entity_value;
                        $cart[$value1]['measure']       = $MEASURE_M2;
                    }
                    $cart[$value1]['price']     = is_numeric($price)?round($price,2):0;
                    $cart[$value1]['calculatedPrice'] = is_numeric($price)?round($price,2):0;                            
                    $cart[$value1]['quantity'] 	= is_numeric($quantity)?$quantity:0; 
                }             
            }
            if(!empty($lineOfThanhPhuKien) && !empty($lineOfVaiChinh)) {
                if ( $numberOfThanh == $numberOfVai) {
                    foreach ($lineOfThanhPhuKien as $line) {
                        $quantity = $attributeCartItemsValue[$lineOfVaiChinh][$ATTR_WIDTH]->entity_value;
                        $cart[$line]['quantity'] = is_numeric($quantity)?round($quantity,2):0;
                    } 
                } else {
                    foreach ($lineOfThanhPhuKien as $line) {
                        $quantity = $attributeCartItemsValue[$lineOfVaiChinh][$ATTR_WIDTH]->entity_value* $numberOfVai;
                        $cart[$line]['quantity'] = is_numeric($quantity)?round($quantity,2):0;
                    }                     
                }
            }        
            if(!empty($lineOfLopYemLopBeo)) {

                $totalPrice = $attributeCartItemsValue[$lineOfLopYemLopBeo][$ATTR_TONG_TIEN_YEM_BEO]->entity_value;;
                $quantity   = $attributeCartItemsValue[$lineOfLopYemLopBeo][$ATTR_WIDTH]->entity_value;
                if ($quantity>0) {
                    $price      = $totalPrice/$quantity;
                } else {
                    $price = 0;
                }
                $cart[$lineOfLopYemLopBeo]['price']             = round($price,2);
                $cart[$lineOfLopYemLopBeo]['calculatedPrice']   = round($price,2);
                $cart[$lineOfLopYemLopBeo]['quantity']          = round($quantity,2);
                if (strpos($cart[$lineOfLopYemLopBeo]['name'],'(Lớp bèo, lớp yếm)') === false) {
                    $cart[$lineOfLopYemLopBeo]['name']      = $cart[$lineOfLopYemLopBeo]['name'].'(Lớp bèo, lớp yếm)';
                }
            } else { 
                foreach ($value['line'] as  $index1 => $value1) {
                    $cart[$value1]['name'] = str_replace('(Lớp bèo, lớp yếm)','',$cart[$value1]['name']);
                }
            }
        }
        $this->set_cart($cart);
    }
    
	function get_customers()
    {
        if($this->CI->session->userdata('more_customers_to_service_contract') === NULL){
            $this->set_customers(array());
        }
        return $this->CI->session->userdata('more_customers_to_service_contract');
    }

    function set_customers($more_customers_to_service_contract_data)
    {
        $this->CI->session->set_userdata('more_customers_to_service_contract',$more_customers_to_service_contract_data);
    }
	 function empty_customers()
    {
        $this->CI->session->unset_userdata('more_customers_to_service_contract');
    }
	
	
	
	//-----------------------------------------------------------------
	// 							FOR REM HA MY
	//-----------------------------------------------------------------
	
	private function itemContainsLineCopySale($sale_id)
	{
		$this->db->select('*')
			 ->from('sale_item_contains_line_rem_ha_my')
			 ->where('sale_id');
		$result_temp = $this->db->get()->result_array();
		$result = [];
		foreach ($result_temp as $temp) {
			$itemContainsLine['itemName'] = $temp;
		}
	}
	
	public function setCartItemsAttribute($attributeCartItemsData) 
	{
		$this->CI->session->set_userdata('attributeCartItems',$attributeCartItemsData);
	}	
	
	public function getCartItemsAttribute()
    {
        if($this->CI->session->userdata('attributeCartItems') === NULL){
            $this->setCartItemsAttribute([]);
        }
        return $this->CI->session->userdata('attributeCartItems');
    }
	
	public function clearCartItemsAttribute()
    {
        $this->CI->session->unset_userdata('attributeCartItems');
    }	
	
	public function setCartItemsAttributeValue($attributeCartItemsValueData) 
	{
		$this->CI->session->set_userdata('attributeCartItemsValue',$attributeCartItemsValueData);
	}	
	
	public function getCartItemsAttributeValue()
    {
        if($this->CI->session->userdata('attributeCartItemsValue') === NULL){
            $this->setCartItemsAttributeValue([]);
        }
        return $this->CI->session->userdata('attributeCartItemsValue');
    }
	
	public function clearCartItemsAttributeValue()
    {
        $this->CI->session->unset_userdata('attributeCartItemsValue');
    }
	
	public function setItemContainsLine($itemContainsLine) 
	{
		$this->CI->session->set_userdata('itemContainsLine',$itemContainsLine);
	}	
	
	public function getItemContainsLine()
    {
        if($this->CI->session->userdata('itemContainsLine') === NULL){
            $this->setItemContainsLine([]);
        }
        return $this->CI->session->userdata('itemContainsLine');
    }
	
	public function clearItemContainsLine()
    {
        $this->CI->session->unset_userdata('itemContainsLine');
    }	
	
	public function setCartItemsAttributeSet($cartItemsAttributeSet) 
	{
		$this->CI->session->set_userdata('cartItemsAttributeSet',$cartItemsAttributeSet);
	}	
	
	public function getCartItemsAttributeSet()
    {
        if($this->CI->session->userdata('cartItemsAttributeSet') === NULL){
            $this->setCartItemsAttributeSet([]);
        }
        return $this->CI->session->userdata('cartItemsAttributeSet');
    }
	
	public function clearCartItemsAttributeSet()
    {
        $this->CI->session->unset_userdata('cartItemsAttributeSet');
    }	
    
    
    public function setCartItemsCategory($cartItemsCategory) 
	{
		$this->CI->session->set_userdata('cartItemsCategory',$cartItemsCategory);
	}	
	
	public function getCartItemsCategory()
    {
        if($this->CI->session->userdata('cartItemsCategory') === NULL){
            $this->setCartItemsCategory([]);
        }
        return $this->CI->session->userdata('cartItemsCategory');
    }
	
	public function clearCartItemsCategory()
    {
        $this->CI->session->unset_userdata('cartItemsCategory');
    }
    
    // sale comment
    public function setSaleComments($saleComments) 
	{
		$this->CI->session->set_userdata('saleComments',$saleComments);
	}	
	
	public function getSaleComments()
    {
        if($this->CI->session->userdata('saleComments') === NULL){
            $this->setSaleComments([]);
        }
        return $this->CI->session->userdata('saleComments');
    }
	
	public function clearSaleComments()
    {
        $this->CI->session->unset_userdata('saleComments');
    }
    
    public function set_id_sale_return($sale_id)
    {
        $this->CI->session->set_userdata('id_sale_return',$sale_id);
    }
    
	public function get_id_sale_return()
    {
        if($this->CI->session->userdata('id_sale_return') === NULL){
            $this->set_id_sale_return([]);
        }
        return $this->CI->session->userdata('id_sale_return');
    }    
    
	public function clear_id_sale_return()
    {
        $this->CI->session->unset_userdata('id_sale_return');
    }    
}
?>