<?php
class Sale_lib
{
    var $CI;

    //This is used when we need to change the sale state and restore it before changing it (The case of showing a receipt in the middle of a sale)
    var $sale_state;
    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Register_cart');
        $this->CI->load->model('Item');
        $this->CI->load->model('Location');
        $this->CI->load->model('Employee');
        $this->CI->load->model('Group');
        $this->CI->load->model('Service');
        $this->CI->load->model('Measure');
        $this->CI->load->model('Expense');
        $this->CI->load->helper('items');

        $this->sale_state = array();
    }

    function set_commission_info($data) {
        $tmp = $this->CI->session->userdata('commission_info');

        if(!empty($data)) {
            foreach($data as $key => $val) {
                $tmp[$key] = $val;
            }
        }else {
            $tmp['commission_time_method'] = $this->CI->config->item('commission_time_method');
            $tmp['commission_method']      = $this->CI->config->item('commission_method');
            $tmp['min_profit']             = $this->CI->config->item('min_profit');
        }

        $this->CI->session->set_userdata('commission_info',$tmp);
    }

    function get_commission_info() {
        if($this->CI->session->userdata('commission_info') === NULL) {
            $tmp = array();
            $tmp['commission_time_method'] = $this->CI->config->item('commission_time_method');
            $tmp['commission_method']      = $this->CI->config->item('commission_method');
            $tmp['min_profit']             = $this->CI->config->item('min_profit');

            $this->CI->session->set_userdata('commission_info',$tmp);
        }

        return $this->CI->session->userdata('commission_info');
    }

    function get_cart()
    {
        if($this->CI->session->userdata('cart') === NULL)
            $this->set_cart(array(), false);
        
        return $this->CI->session->userdata('cart');
    }

    function set_cart($cart_data,$update_register_cart_data = TRUE)
    {
        $this->CI->session->set_userdata('cart',$cart_data);
        if ($update_register_cart_data)
        {
            $this->update_register_cart_data();

        }
    }

    function update_register_cart_data()
    {
        $data = array();
        $data['cart'] = $this->get_cart();
        $data['subtotal'] = $this->get_subtotal();
        $data['tax'] = $this->get_tax_total_amount();
        $data['amount_due'] = $this->get_amount_due();
        $customer_id = $this->get_customer();
        if($customer_id!=-1)
        {
            $info=$this->CI->Customer->get_info($customer_id);
            $data['customer']=$info->first_name.' '.$info->last_name.($info->company_name==''  ? '' :' ('.$info->company_name.')');
            $data['customer_email']=$info->email;
            $data['customer_balance'] = $info->balance;
            $data['avatar']=$info->image_id ?  site_url('app_files/view/'.$info->image_id) : base_url()."assets/img/user.png"; //can be changed to  base_url()."img/avatar.png" if it is required
        }
        else
        {
            $data['customer']= NULL;
        }

        $data['payments'] = $this->get_payments();
        $data['total'] = $this->get_total();
        $this->CI->Register_cart->set_data($data,$this->CI->Employee->get_logged_in_employee_current_register_id());

    }
    //Alain Multiple Payments
    function get_payments()
    {
        if($this->CI->session->userdata('payments') === NULL){
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

    function get_selected_payment()
    {
        if($this->CI->session->userdata('sale_selected_payment') === NULL)
            $this->set_selected_payment('');

        return $this->CI->session->userdata('sale_selected_payment');
    }

    function set_selected_payment($payment)
    {
        $this->CI->session->set_userdata('sale_selected_payment',$payment);
    }

    function clear_selected_payment()
    {
        $this->CI->session->unset_userdata('sale_selected_payment');
    }

    function change_credit_card_payments_to_partial()
    {
        $payments=$this->get_payments();

        foreach($payments as $payment_id=>$payment)
        {
            //If we have a credit payment, change it to partial credit card so we can process again
            if ($payment['payment_type'] == lang('common_credit'))
            {
                $payments[$payment_id]['payment_type'] =  lang('sales_partial_credit');
            }
        }

        $this->set_payments($payments);
    }

    function get_change_sale_date()
    {
        return $this->CI->session->userdata('change_sale_date') ? $this->CI->session->userdata('change_sale_date') : '';
    }
    function clear_change_sale_date()
    {
        $this->CI->session->unset_userdata('change_sale_date');

    }
    function clear_change_sale_date_enable()
    {
        $this->CI->session->unset_userdata('change_sale_date_enable');
    }
    function set_change_sale_date_enable($change_sale_date_enable)
    {
        $this->CI->session->set_userdata('change_sale_date_enable',$change_sale_date_enable);
    }

    function get_change_sale_date_enable()
    {
        return $this->CI->session->userdata('change_sale_date_enable') ? $this->CI->session->userdata('change_sale_date_enable') : '';
    }

    function set_change_sale_date($change_sale_date)
    {
        $this->CI->session->set_userdata('change_sale_date',$change_sale_date);
    }

    function get_comment()
    {
        return $this->CI->session->userdata('comment') ? $this->CI->session->userdata('comment') : '';
    }

    function get_comment_on_receipt()
    {
        return $this->CI->session->userdata('show_comment_on_receipt') ? $this->CI->session->userdata('show_comment_on_receipt') : '';
    }

    function set_comment($comment)
    {
        $this->CI->session->set_userdata('comment', $comment);
    }

    function get_selected_tier_id()
    {
        return $this->CI->session->userdata('selected_tier_id') ? $this->CI->session->userdata('selected_tier_id') : FALSE;
    }

    function get_previous_tier_id()
    {
        return $this->CI->session->userdata('previous_tier_id') ? $this->CI->session->userdata('previous_tier_id') : FALSE;
    }

    function set_selected_tier_id($tier_id, $change_price = true)
    {
        $this->CI->session->set_userdata('previous_tier_id', $this->get_selected_tier_id());
        $this->CI->session->set_userdata('selected_tier_id', $tier_id);

        if ($change_price == true)
        {
            $this->change_price();
        }
    }

    function clear_selected_tier_id()
    {
        $this->CI->session->unset_userdata('previous_tier_id');
        $this->CI->session->unset_userdata('selected_tier_id');
    }


    function set_comment_on_receipt($comment_on_receipt)
    {
        $this->CI->session->set_userdata('show_comment_on_receipt', $comment_on_receipt);
    }

    function clear_comment()
    {
        $this->CI->session->unset_userdata('comment');

    }

    function clear_show_comment_on_receipt()
    {
        $this->CI->session->unset_userdata('show_comment_on_receipt');

    }

    function get_email_receipt()
    {
        return $this->CI->session->userdata('email_receipt');
    }

    function set_email_receipt($email_receipt)
    {
        $this->CI->session->set_userdata('email_receipt', $email_receipt);
    }

    function clear_email_receipt()
    {
        $this->CI->session->unset_userdata('email_receipt');
    }

    function get_deleted_taxes()
    {
        $deleted_taxes = $this->CI->session->userdata('deleted_taxes') ? $this->CI->session->userdata('deleted_taxes') : array();
        return $deleted_taxes;
    }

    function add_deleted_tax($name)
    {
        $deleted_taxes = $this->CI->session->userdata('deleted_taxes') ? $this->CI->session->userdata('deleted_taxes') : array();

        if (!in_array($name, $deleted_taxes))
        {
            $deleted_taxes[] = $name;
        }
        $this->CI->session->set_userdata('deleted_taxes', $deleted_taxes);
    }

    function set_deleted_taxes($deleted_taxes)
    {
        $this->CI->session->set_userdata('deleted_taxes', $deleted_taxes);
    }

    function clear_deleted_taxes()
    {
        $this->CI->session->unset_userdata('deleted_taxes');
    }

    function get_save_credit_card_info()
    {
        return $this->CI->session->userdata('save_credit_card_info');
    }

    function set_save_credit_card_info($save_credit_card_info)
    {
        $this->CI->session->set_userdata('save_credit_card_info', $save_credit_card_info);
    }

    function clear_save_credit_card_info()
    {
        $this->CI->session->unset_userdata('save_credit_card_info');
    }

    function get_use_saved_cc_info()
    {
        return $this->CI->session->userdata('use_saved_cc_info');
    }

    function set_use_saved_cc_info($use_saved_cc_info)
    {
        $this->CI->session->set_userdata('use_saved_cc_info', $use_saved_cc_info);
    }

    function clear_use_saved_cc_info()
    {
        $this->CI->session->unset_userdata('use_saved_cc_info');
    }

    function clear_prompt_for_card()
    {
        $this->CI->session->unset_userdata('prompt_for_card');
    }

    function set_prompt_for_card($prompt_for_card)
    {
        $this->CI->session->set_userdata('prompt_for_card',$prompt_for_card);
    }

    function get_prompt_for_card()
    {
        return $this->CI->session->userdata('prompt_for_card') ? $this->CI->session->userdata('prompt_for_card') : '';
    }

    function get_partial_transactions()
    {
        return $this->CI->session->userdata('partial_transactions');
    }

    function set_partial_transactions($partial_transactions)
    {
        $this->CI->session->set_userdata('partial_transactions', $partial_transactions);
    }

    function add_partial_transaction($partial_transaction)
    {
        $partial_transactions = $this->CI->session->userdata('partial_transactions');
        $partial_transactions[] = $partial_transaction;
        $this->CI->session->set_userdata('partial_transactions', $partial_transactions);
    }

    function delete_partial_transactions()
    {
        $this->CI->session->unset_userdata('partial_transactions');
    }


    function get_sold_by_employee_id()
    {
        if ($this->CI->config->item('default_sales_person') != 'not_set' && !$this->CI->session->userdata('sold_by_employee_id'))
        {
            if($this->CI->config->item('default_sales_person') == 'logged_in_employee') {
                $employee_id=$this->CI->Employee->get_logged_in_employee_info()->person_id;
                $location_sale_employees = $this->CI->Location->get_employee_list(array('location_id'=>$this->CI->Employee->get_logged_in_employee_current_location_id(), 'group_id'=>1));
                if(!empty($location_sale_employees)) {
                    $tmp = explode(',', $location_sale_employees['employees']);
                    if(!in_array($employee_id, $tmp))
                        $employee_id = NULL;
                }else
                    $employee_id = NULL;
            }else {
                $location_sale_employees = $this->CI->Location->get_employee_list(array('location_id'=>$this->CI->Employee->get_logged_in_employee_current_location_id(), 'group_id'=>1));
                $default_list = $location_sale_employees['default_list'];
                if(!empty($default_list)) {
                    $employee_id = $default_list[0]['id'];
                }else
                    $employee_id = null;
            }

            return $employee_id;
        }
        return $this->CI->session->userdata('sold_by_employee_id') ? $this->CI->session->userdata('sold_by_employee_id') : NULL;
    }

    function set_sold_by_employee_id($sold_by_employee_id)
    {
        $this->CI->session->set_userdata('sold_by_employee_id', $sold_by_employee_id);
    }

    function clear_sold_by_employee_id()
    {
        $this->CI->session->unset_userdata('sold_by_employee_id');
    }

    function get_invoice_no()
    {
        return $this->CI->session->userdata('invoice_no');
    }

    function set_invoice_no($invoice_no)
    {
        $this->CI->session->set_userdata('invoice_no', $invoice_no);
    }

    function clear_invoice_no()
    {
        $this->CI->session->unset_userdata('invoice_no');
    }


    // function add_payment($payment_type,$payment_amount,$payment_date = false, $truncated_card = '', $card_issuer = '', $auth_code = '', $ref_no = '', $cc_token='', $acq_ref_data = '', $process_data = '', $entry_method='', $aid= '',$tvr='',$iad='', $tsi='',$arc='',$cvm='',$tran_type='',$application_label = '')
    // {
    //     $payments=$this->get_payments();
    //     $payment = array(
    //         'payment_type'=>$payment_type,
    //         'payment_amount'=>$payment_amount,
    //         'payment_date' => $payment_date !== FALSE ? $payment_date : date('Y-m-d H:i:s'),
    //         'truncated_card' => $truncated_card,
    //         'card_issuer' => $card_issuer,
    //         'auth_code' => $auth_code,
    //         'ref_no' => $ref_no,
    //         'cc_token' => $cc_token,
    //         'acq_ref_data' => $acq_ref_data,
    //         'process_data' => $process_data,
    //         'entry_method' => $entry_method,
    //         'aid' => $aid,
    //         'tvr' => $tvr,
    //         'iad' => $iad,
    //         'tsi' => $tsi,
    //         'arc' => $arc,
    //         'cvm' => $cvm,
    //         'tran_type' => $tran_type,
    //         'application_label' => $application_label,
    //     );

    //     $payments[]=$payment;
    //     $this->set_payments($payments);
    //     return true;
    // }

    function edit_payment($payment_id, $payment_type, $payment_amount,$payment_date = false, $truncated_card = '', $card_issuer = '', $auth_code = '', $ref_no = '', $cc_token='', $acq_ref_data = '', $process_data = '', $entry_method='', $aid= '',$tvr='',$iad='', $tsi='',$arc='',$cvm='',$tran_type='',$application_label = '')
    {
        $payments=$this->get_payments();
        $payment = array(
            'payment_type'=>$payment_type,
            'payment_amount'=>$payment_amount,
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
        );

        $payments[$payment_id]=$payment;
        $this->set_payments($payments);
        return true;
    }

    public function get_payment_ids($payment_type)
    {
        $payment_ids = array();

        $payments=$this->get_payments();

        for($k=0;$k<count($payments);$k++)
        {
            if (isset($payments[$k]) && $payments[$k]['payment_type'] == $payment_type)
            {
                $payment_ids[] = $k;
            }
        }

        return $payment_ids;
    }

    public function get_payment_amount($payment_type)
    {
        $payment_amount = 0;
        if (($payment_ids = $this->get_payment_ids($payment_type)) !== FALSE)
        {
            $payments=$this->get_payments();

            foreach($payment_ids as $payment_id)
            {
                $payment_amount += $payments[$payment_id]['payment_amount'];
            }
        }

        return $payment_amount;
    }

    //Alain Multiple Payments
    function delete_payment($payment_ids)
    {
        $payments=$this->get_payments();

        if (is_array($payment_ids))
        {
            foreach($payment_ids as $payment_id)
            {
                unset($payments[$payment_id]);
            }
        }
        else
        {
            unset($payments[$payment_ids]);
        }
        if (count($payments) == 1) {
            foreach($payments as $key => $payment) {
               if ($payment['payment_type'] == lang('common_refund_money')) {
                   unset($payments[$key]);
                   break;
               }
            }
        }
        $this->set_payments(array_values($payments));
    }

    function get_price_for_item($item_id, $tier_id = FALSE)
    {
        if ($tier_id === FALSE)
        {
            $tier_id = $this->get_selected_tier_id();
        }

        $item_info = $this->CI->Item->get_info($item_id);
        $item_location_info = $this->CI->Item_location->get_info($item_id);

        $item_tier_row = $this->CI->Item->get_tier_price_row($tier_id, $item_id);
        $item_location_tier_row = $this->CI->Item_location->get_tier_price_row($tier_id, $item_id, $this->CI->Employee->get_logged_in_employee_current_location_id());

        if (!empty($item_location_tier_row) && $item_location_tier_row->unit_price)
        {
            return to_currency_no_money($item_location_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        elseif (!empty($item_location_tier_row) && $item_location_tier_row->percent_off)
        {
            $item_unit_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
            return to_currency_no_money($item_unit_price *(1-($item_location_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        elseif (!empty($item_tier_row) && $item_tier_row->unit_price)
        {
            return to_currency_no_money($item_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        elseif (!empty($item_tier_row) && $item_tier_row->percent_off)
        {
            $item_unit_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
            return to_currency_no_money($item_unit_price *(1-($item_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        else
        {
            $today =  strtotime(date('Y-m-d'));
            $is_item_location_promo = ($item_location_info->start_date !== NULL && $item_location_info->end_date !== NULL) && (strtotime($item_location_info->start_date) <= $today && strtotime($item_location_info->end_date) >= $today);
            $is_item_promo = ($item_info->start_date !== NULL && $item_info->end_date !== NULL) && (strtotime($item_info->start_date) <= $today && strtotime($item_info->end_date) >= $today);

            if ($is_item_location_promo && $item_location_info->promo_price)
            {
                return to_currency_no_money($item_location_info->promo_price, 10);
            }
            elseif ($is_item_promo && $item_info->promo_price)
            {
                return to_currency_no_money($item_info->promo_price, 10);
            }
            else
            {
                $item_unit_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
                return to_currency_no_money($item_unit_price, 10);
            }
        }

    }

    function get_price_for_item_kit($item_kit_id, $tier_id = FALSE)
    {
        if ($tier_id === FALSE)
        {
            $tier_id = $this->get_selected_tier_id();
        }

        $item_kit_info = $this->CI->Item_kit->get_info($item_kit_id);
        $item_kit_location_info = $this->CI->Item_kit_location->get_info($item_kit_id);

        $item_kit_tier_row = $this->CI->Item_kit->get_tier_price_row($tier_id, $item_kit_id);
        $item_kit_location_tier_row = $this->CI->Item_kit_location->get_tier_price_row($tier_id, $item_kit_id, $this->CI->Employee->get_logged_in_employee_current_location_id());

        if (!empty($item_kit_location_tier_row) && $item_kit_location_tier_row->unit_price)
        {
            return to_currency_no_money($item_kit_location_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        elseif (!empty($item_kit_location_tier_row) && $item_kit_location_tier_row->percent_off)
        {
            $item_kit_unit_price = $item_kit_location_info->unit_price ? $item_kit_location_info->unit_price : $item_kit_info->unit_price;
            return to_currency_no_money($item_kit_unit_price *(1-($item_kit_location_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        elseif (!empty($item_kit_tier_row) && $item_kit_tier_row->unit_price)
        {
            return to_currency_no_money($item_kit_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        elseif (!empty($item_kit_tier_row) && $item_kit_tier_row->percent_off)
        {
            $item_kit_unit_price = $item_kit_location_info->unit_price ? $item_kit_location_info->unit_price : $item_kit_info->unit_price;
            return to_currency_no_money($item_kit_unit_price *(1-($item_kit_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
        }
        else
        {
            $item_kit_unit_price = $item_kit_location_info->unit_price ? $item_kit_location_info->unit_price : $item_kit_info->unit_price;
            return to_currency_no_money($item_kit_unit_price, 10);
        }
    }

    function empty_payments()
    {
        $this->CI->session->unset_userdata('payments');
    }

    //Alain Multiple Payments
    function get_payments_totals_excluding_store_account()
    {
        $subtotal = 0;
        foreach($this->get_payments() as $payments)
        {
            if($payments['payment_type'] != lang('common_store_account'))
            {
                $subtotal+=$payments['payment_amount'];
            }
        }
        return to_currency_no_money($subtotal);
    }

    function get_payments_totals()
    {
        $subtotal = 0;
        foreach($this->get_payments() as $payments)
        {
            if(!empty($payments['payment_type']) && $payments['payment_type'] ==  lang('common_debt_customer'))
                {
                    $subtotal-=$payments['payment_amount'];
                }
                elseif(!empty($payments['payment_amount']))
                {
                    $subtotal += $payments['payment_amount'];
                }
        }

        return to_currency_no_money($subtotal);
    }

    //Alain Multiple Payments
    function get_amount_due($sale_id = false)
    {
        $amount_due=0;
        $payment_total = $this->get_payments_totals();
        $sales_total=$this->get_total($sale_id);
        $amount_due=to_currency_no_money($sales_total - $payment_total);
        return $amount_due;
    }

    function get_amount_due_round($sale_id = false)
    {
        $amount_due=0;
        $payment_total = $this->get_payments_totals();
        $sales_total= $this->CI->config->item('round_cash_on_sales') ?  round_to_nearest_05($this->get_total($sale_id)) : $this->get_total($sale_id);
        $amount_due=to_currency_no_money($sales_total - $payment_total);
        return $amount_due;
    }

    function get_customer()
    {
        if(!$this->CI->session->userdata('customer'))
            $this->set_customer(-1, false);

        return $this->CI->session->userdata('customer');
    }

    function set_customer($customer_id, $change_price = true)
    {
        if (is_numeric($customer_id))
        {
            $this->CI->session->set_userdata('customer',$customer_id);

            if ($change_price == true)
            {
                $this->change_price();
            }
        }
    }

    function get_mode()
    {
        if(!$this->CI->session->userdata('sale_mode'))
            $this->set_mode('sale');

        return $this->CI->session->userdata('sale_mode');
    }

    function set_mode($mode)
    {
        $this->CI->session->set_userdata('sale_mode',$mode);
    }

    /*
    * This function is called when a customer added or tier changed
    * It scans item and item kits to see if there price is at a default value
    * If a price is at a default value, it is changed to match the tier
    */
    function change_price()
    {
        $items = $this->get_cart();
        foreach ($items as $item )
        {
            if (isset($item['item_id']))
            {
                $line=$item['line'];
                $price=$item['price'];
                $item_id=$item['item_id'];
                $item_info = $this->CI->Item->get_info($item_id);
                $item_location_info = $this->CI->Item_location->get_info($item_id);
                $previous_price = FALSE;

                if ($previous_tier_id = $this->get_previous_tier_id())
                {
                    $previous_price = $this->get_price_for_item($item_id, $previous_tier_id);
                }
                $previous_price = to_currency_no_money($previous_price, 10);
                $price = to_currency_no_money($price, 10);

                if($price==$item_info->unit_price || $price == $item_location_info->unit_price || (($price == $previous_price) && ($price !=0 && $previous_price!=0)))
                {
                    $items[$line]['price']= $this->get_price_for_item($item_id);
                }
            }
            elseif(isset($item['item_kit_id']))
            {
                $line=$item['line'];
                $price=$item['price'];
                $item_kit_id=$item['item_kit_id'];
                $item_kit_info = $this->CI->Item_kit->get_info($item_kit_id);
                $item_kit_location_info = $this->CI->Item_kit_location->get_info($item_kit_id);
                $previous_price = FALSE;

                if ($previous_tier_id = $this->get_previous_tier_id())
                {
                    $previous_price = $this->get_price_for_item_kit($item_kit_id, $previous_tier_id);
                }

                $previous_price = to_currency_no_money($previous_price, 10);
                $price = to_currency_no_money($price, 10);

                if($price==$item_kit_info->unit_price || $price == $item_kit_location_info->unit_price || (($price == $previous_price) && ($price !=0 && $previous_price!=0)))
                {
                    $items[$line]['price']= $this->get_price_for_item_kit($item_kit_id);
                }
            }
        }
        $this->set_cart($items);
    }
    function add_item($item_id,$quantity=1,$discount=0,$price=null,$cost_price = null, $description=null,$serialnumber=null, $force_add = FALSE, $line = FALSE, $update_register_cart_data = TRUE)
    {
        $store_account_item_id = $this->CI->Item->get_store_account_item_id();

        //Do NOT allow item to get added unless in store_account_payment mode
        if (!$force_add && $this->get_mode() !=='store_account_payment' && $store_account_item_id == $item_id)
        {
            return FALSE;
        }

        //make sure item exists
        if(!$this->CI->Item->exists(does_contain_only_digits($item_id) ? (int)$item_id : -1))
        {
            //try to get item id given an item_number
            $item_id = $this->CI->Item->get_item_id($item_id);

            if(!$item_id)
                return false;
        }
        else
        {
            $item_id = (int)$item_id;
        }

        if ($this->CI->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
        {
            if (!$force_add && $this->will_be_out_of_stock($item_id,$quantity))
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

        $cost_price_to_use = ($item_location_info && $item_location_info->cost_price) ? $item_location_info->cost_price : $item_info->cost_price;

        //array/cart records are identified by $insertkey and item_id is just another field.
        $item = array(($line === FALSE ? $insertkey : $line)=>
        array(
            'item_id'=>$item_id,
            'line'=>$line === FALSE ? $insertkey : $line,
            'name'=>$item_info->name,
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
            'cur_quantity' => $item_location_info->quantity,
            'discount'=>$discount,
            'price'=>$price!=null ? $price:$price_to_use,
            'tax_included'=> $item_info->tax_included,
        )
        );

        //Item already exists and is not serialized, add to quantity
        if($itemalreadyinsale && ($item_info->is_serialized ==0) && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
        {
            $items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
        }
        else
        {
            //add to existing array
            $items+=$item;
        }
        $this->set_cart($items,$update_register_cart_data);
        return true;

    }

    function add_item_kit($external_item_kit_id_or_item_number,$quantity=1,$discount=0,$price=null,$cost_price = null,$description=null, $force_add = FALSE, $line=FALSE,$update_register_cart_data = TRUE)
    {
        if (strpos(strtolower($external_item_kit_id_or_item_number), 'kit') !== FALSE)
        {
            //KIT #
            $pieces = explode(' ',$external_item_kit_id_or_item_number);
            $item_kit_id = (int)$pieces[1];
        }
        else
        {
            $item_kit_id = $this->CI->Item_kit->get_item_kit_id($external_item_kit_id_or_item_number);
        }


        if ($this->CI->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
        {
            if (!$force_add && $this->will_be_out_of_stock_kit($item_kit_id,$quantity))
            {
                return FALSE;
            }
        }

        //make sure item exists
        if(!$this->CI->Item_kit->exists($item_kit_id))
        {
            return false;
        }

        $item_kit_info = $this->CI->Item_kit->get_info($item_kit_id);
        $item_kit_location_info = $this->CI->Item_kit_location->get_info($item_kit_id);

        if ( $item_kit_info->unit_price == null)
        {
            foreach ($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
            {
                for($k=0;$k<$item_kit_item->quantity;$k++)
                {
                    $this->add_item($item_kit_item->item_id, $quantity,0,null,null, null,null,$force_add, FALSE);
                }
            }

            return true;
        }
        else
        {
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

                if(isset($item['item_kit_id']) && $item['item_kit_id']==$item_kit_id)
                {
                    $itemalreadyinsale=TRUE;
                    $updatekey=$item['line'];
                }
            }

            $insertkey=$maxkey+1;

            $price_to_use=$this->get_price_for_item_kit($item_kit_id);

            $cost_price_to_use = ($item_kit_location_info && $item_kit_location_info->cost_price) ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;

            //array/cart records are identified by $insertkey and item_id is just another field.
            $item = array(($line === FALSE ? $insertkey : $line)=>
            array(
                'item_kit_id'=>$item_kit_id,
                'line'=>$line === FALSE ? $insertkey : $line,
                'item_kit_number'=>$item_kit_info->item_kit_number,
                'product_id'=>$item_kit_info->product_id,
                'name'=>$item_kit_info->name,
                'change_cost_price' =>$item_kit_info->change_cost_price,
                'cost_price' => $cost_price!=null ? $cost_price : $cost_price_to_use,
                'size' => '',
                'description'=>$description!=null ? $description: $item_kit_info->description,
                'quantity'=>$quantity,
                'cur_quantity' => NULL,
                'discount'=>$discount,
                'price'=>$price!=null ? $price: $price_to_use,
                'tax_included'=> $item_kit_info->tax_included,
            )
            );

            //Item already exists and is not serialized, add to quantity
            if($itemalreadyinsale && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
            {
                $items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
            }
            else
            {
                //add to existing array
                $items+=$item;
            }

            $this->set_cart($items,$update_register_cart_data);
            return true;
        }
    }

    function discount_all($percent_discount)
    {
        $items = $this->get_cart();

        foreach(array_keys($items) as $key)
        {
            if ((isset($items[$key]['item_id']) && $items[$key]['item_id'] != $this->CI->Item->get_item_id_for_flat_discount_item()) || isset($items[$key]['item_kit_id']))
            {
                $items[$key]['discount'] = $percent_discount;
            }
        }
        $this->set_cart($items);
        return true;
    }

    function out_of_stock($item_id)
    {
        //make sure item exists
        if(!$this->CI->Item->exists(does_contain_only_digits($item_id) ? $item_id : -1))
        {
            //try to get item id given an item_number
            $item_id = $this->CI->Item->get_item_id($item_id);

            if(!$item_id)
                return false;
        }

        $suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;
        $quantity_in_sale = 0;

        if ($suspended_change_sale_id)
        {
            $suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;

            //Not an estiamte
            if ($suspended_type != 2)
            {
                $quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_in_sale($suspended_change_sale_id, $item_id);
            }
        }

        $item_location_quantity = $this->CI->Item_location->get_location_quantity($item_id);
        $quanity_added = $this->get_quantity_already_added($item_id);

        //If $item_location_quantity is NULL we don't track quantity
        if ($item_location_quantity !== NULL && $item_location_quantity - $quanity_added  + $quantity_in_sale < 0)
        {
            return true;
        }

        return false;
    }

    function will_be_out_of_stock($item_id, $additional_quantity)
    {
        $suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;

        if ($suspended_change_sale_id)
        {
            $suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;

            //Not an estiamte
            if ($suspended_type != 2)
            {
                $quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_in_sale($suspended_change_sale_id, $item_id);

                $additional_quantity -= $quantity_in_sale;
            }
        }

        //make sure item exists
        if(!$this->CI->Item->exists(does_contain_only_digits($item_id) ? $item_id : -1))
        {
            //try to get item id given an item_number
            $item_id = $this->CI->Item->get_item_id($item_id);

            if(!$item_id)
                return false;
        }

        $item_location_quantity = $this->CI->Item_location->get_location_quantity($item_id);
        $quanity_added = $this->get_quantity_already_added($item_id) + $additional_quantity;

        //If $item_location_quantity is NULL we don't track quantity
        if ($item_location_quantity !== NULL && $item_location_quantity - $quanity_added < 0)
        {
            return true;
        }

        return false;
    }

    function out_of_stock_kit($kit_id)
    {
        //Make sure Item kit exist
        if(!$this->CI->Item_kit->exists($kit_id)) return FALSE;

        $suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;
        $quantity_in_sale = 0;

        if ($suspended_change_sale_id)
        {
            $suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;

            //Not an estiamte
            if ($suspended_type != 2)
            {
                $quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_kit_in_sale($suspended_change_sale_id, $kit_id);
            }
        }

        //Get All Items for Kit
        $kit_items = $this->CI->Item_kit_items->get_info($kit_id);

        //Check each item
        foreach ($kit_items as $item)
        {
            $item_location_quantity = $this->CI->Item_location->get_location_quantity($item->item_id);
            $item_already_added = $this->get_quantity_already_added($item->item_id);

            if ($item_location_quantity !== NULL && $item_location_quantity - $item_already_added + $this->get_quantity_to_be_added_from_kit($kit_id, $item->item_id, $quantity_in_sale) < 0)
            {
                return true;
            }
        }
        return false;
    }

    function will_be_out_of_stock_kit($kit_id, $additional_quantity)
    {
        $suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;

        if ($suspended_change_sale_id)
        {
            $suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;

            //Not an estiamte
            if ($suspended_type != 2)
            {
                $quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_kit_in_sale($suspended_change_sale_id, $kit_id);

                $additional_quantity -= $quantity_in_sale;
            }
        }

        //Make sure Item kit exist
        if(!$this->CI->Item_kit->exists($kit_id)) return FALSE;

        //Get All Items for Kit
        $kit_items = $this->CI->Item_kit_items->get_info($kit_id);

        //Check each item
        foreach ($kit_items as $item)
        {
            $item_location_quantity = $this->CI->Item_location->get_location_quantity($item->item_id);
            $item_already_added = $this->get_quantity_already_added($item->item_id) + $this->get_quantity_to_be_added_from_kit($kit_id, $item->item_id, $additional_quantity);

            if ($item_location_quantity !== NULL && $item_location_quantity - $item_already_added < 0)
            {
                return true;
            }
        }
        return false;
    }


    function below_cost_price_item($line, $price = NULL, $discount = NULL, $cost_price = NULL)
    {
        $cart = $this->get_cart();

        if (isset($cart[$line]))
        {
            $line_item = $cart[$line];

            if ($price === NULL)
            {
                $price = $line_item['price'];
            }

            if ($discount === NULL)
            {
                $discount = $line_item['discount'];
            }

            if ($cost_price === NULL)
            {
                $cost_price = $line_item['cost_price'];
            }

            $total_for_one = $price-$price*$discount/100;
            return $total_for_one < $cost_price;
        }

        return FALSE;
    }

    function get_quantity_already_added($item_id)
    {
        $items = $this->get_cart();
        $quanity_already_added = 0;
        foreach ($items as $item)
        {
            if(isset($item['item_id']) && $item['item_id']==$item_id)
            {
                $quanity_already_added+=$item['quantity'];
            }
        }

        //Check Item Kist for this item
        $all_kits = $this->CI->Item_kit_items->get_kits_have_item($item_id);

        foreach($all_kits as $kits)
        {
            $kit_quantity = $this->get_kit_quantity_already_added($kits['item_kit_id']);
            if($kit_quantity > 0)
            {
                $quanity_already_added += ($kit_quantity * $kits['quantity']);
            }
        }
        return $quanity_already_added;
    }

    function get_kit_quantity_already_added($kit_id)
    {
        $items = $this->get_cart();
        $quanity_already_added = 0;
        foreach ($items as $item)
        {
            if(isset($item['item_kit_id']) && $item['item_kit_id']==$kit_id)
            {
                $quanity_already_added+=$item['quantity'];
            }
        }

        return $quanity_already_added;
    }

    function get_quantity_to_be_added_from_kit($kit_id, $item_id,$quantity)
    {
        $item_kit_items = $this->CI->Item_kit_items->get_info($kit_id);

        foreach ($item_kit_items as $item_kit_item)
        {
            if ($item_id == $item_kit_item->item_id)
            {
                return $quantity * $item_kit_item->quantity;
            }
        }

        return 0;
    }

    function get_item_id($line_to_get)
    {
        $items = $this->get_cart();

        foreach ($items as $line=>$item)
        {
            if($line==$line_to_get)
            {
                return isset($item['item_id']) ? $item['item_id'] : -1;
            }
        }

        return -1;
    }

    function get_last_item_added_price()
    {
        $items = $this->get_cart();

        if (!empty($items))
        {
            //Get last element then reset pointer so nothing gets messed
            $last_item = end($items);
            reset($items);
            return $last_item['price'];
        }

        return FALSE;
    }

    function get_last_item_line()
    {
        $items = $this->get_cart();

        if (!empty($items))
        {
            //Get last element then reset pointer so nothing gets messed
            $last_item = end($items);
            reset($items);
            return $last_item['line'];
        }

        return FALSE;

    }

    function get_quantity_at_line($line_to_get)
    {
        $items = $this->get_cart();

        foreach ($items as $line=>$item)
        {
            if($line==$line_to_get)
            {
                return isset($item['quantity']) ? $item['quantity'] : 0;
            }
        }
        return 0;
    }


    function get_kit_id($line_to_get)
    {
        $items = $this->get_cart();

        foreach ($items as $line=>$item)
        {
            if($line==$line_to_get)
            {
                return isset($item['item_kit_id']) ? $item['item_kit_id'] : -1;
            }
        }
        return -1;
    }

    function is_kit_or_item($line_to_get)
    {
        $items = $this->get_cart();
        foreach ($items as $line=>$item)
        {
            if($line==$line_to_get)
            {
                if(isset($item['item_id']))
                {
                    return 'item';
                }
                elseif ($item['item_kit_id'])
                {
                    return 'kit';
                }
            }
        }
        return -1;
    }

    function edit_item($line,$description = NULL,$serialnumber = NULL,$quantity = NULL,$discount = NULL,$price = NULL, $cost_price = NULL)
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
            if ($quantity !== NULL ) {
                $items[$line]['quantity'] = $quantity;
            }
            if ($discount !== NULL ) {
                $items[$line]['discount'] = $discount;
            }
            if ($price !== NULL ) {
                $items[$line]['price'] = $price;
            }
            if ($cost_price !== NULL ) {
                $items[$line]['cost_price'] = $cost_price;
            }

            $this->set_cart($items);

            return true;
        }

        return false;
    }

    function is_valid_receipt($receipt_sale_id)
    {
        //Valid receipt syntax
        if(strpos(strtolower($receipt_sale_id), strtolower($this->CI->config->item('sale_prefix')).' ') !== FALSE)
        {
            //Extract the id
            $sale_id = substr(strtolower($receipt_sale_id), strpos(strtolower($receipt_sale_id),$this->CI->config->item('sale_prefix').' ') + strlen(strtolower($this->CI->config->item('sale_prefix')).' '));
            return $this->CI->Sale->exists($sale_id);
        }

        return false;
    }

    function is_valid_item_kit($item_kit_id)
    {
        //KIT #
        $pieces = explode(' ',$item_kit_id);

        if(count($pieces)==2 && strtolower($pieces[0]) == 'kit')
        {
            return $this->CI->Item_kit->exists($pieces[1]);
        }
        else
        {
            return $this->CI->Item_kit->get_item_kit_id($item_kit_id) !== FALSE;
        }

    }

    function get_valid_item_kit_id($item_kit_id)
    {
        //KIT #
        $pieces = explode(' ',$item_kit_id);

        if(count($pieces)==2 && strtolower($pieces[0]) == 'kit')
        {
            return $pieces[1];
        }
        else
        {
            return $this->CI->Item_kit->get_item_kit_id($item_kit_id);
        }
    }

    function return_entire_sale($receipt_sale_id)
    {
        //POS #
        $sale_id = substr(strtolower($receipt_sale_id), strpos(strtolower($receipt_sale_id),$this->CI->config->item('sale_prefix').' ') + strlen(strtolower($this->CI->config->item('sale_prefix')).' '));

        $this->empty_cart();
        $this->delete_customer(false);
        $sale_taxes = $this->get_taxes($sale_id);

        foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
        {
            $item_info = $this->CI->Item->get_info($row->item_id);
            $price_to_use = $row->item_unit_price;
            //If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
            if ($item_info->tax_included && empty($sale_taxes))
            {
                $this->CI->load->helper('items');
                $price_to_use = get_price_for_item_including_taxes($row->item_id, $row->item_unit_price);
            }
            elseif($item_info->tax_included)
            {
                $this->CI->load->helper('items');

                $price_to_use = get_price_for_item_including_taxes($row->line, $row->item_unit_price,$sale_id);
            }

            $this->add_item($row->item_id,-$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->item_cost_price,$row->description,$row->serialnumber, TRUE, $row->line, FALSE);
        }
        foreach($this->CI->Sale->get_sale_item_kits($sale_id)->result() as $row)
        {
            $item_kit_info = $this->CI->Item_kit->get_info($row->item_kit_id);
            $price_to_use = $row->item_kit_unit_price;

            //If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
            if ($item_kit_info->tax_included && empty($sale_taxes))
            {
                $this->CI->load->helper('item_kits');
                $price_to_use = get_price_for_item_kit_including_taxes($row->item_kit_id, $row->item_kit_unit_price);
            }
            elseif ($item_kit_info->tax_included)
            {
                $this->CI->load->helper('item_kits');
                $price_to_use = get_price_for_item_kit_including_taxes($row->line, $row->item_kit_unit_price,$sale_id);
            }

            $this->add_item_kit('KIT '.$row->item_kit_id,-$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->item_kit_cost_price,$row->description, TRUE, $row->line, FALSE);
        }
        $this->update_register_cart_data();
        $this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id, false);
        $this->set_deleted_taxes($this->CI->Sale->get_deleted_taxes($sale_id));
    }

    function copy_entire_sale($sale_id, $is_receipt = false)
    {
        $this->empty_cart();
        $this->delete_customer(false);
        $sale_taxes = $this->get_taxes($sale_id);

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

            $this->add_item($row->item_id,$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->item_cost_price, $row->description,$row->serialnumber, TRUE, $row->line, FALSE);
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

            $this->add_item_kit('KIT '.$row->item_kit_id,$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->item_kit_cost_price,$row->description, TRUE, $row->line, FALSE);
        }
        foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
        {
            $this->add_payment($row->payment_type,$row->payment_amount, $row->payment_date, $row->truncated_card, $row->card_issuer, $row->auth_code, $row->ref_no, $row->cc_token, $row->acq_ref_data, $row->process_data, $row->entry_method, $row->aid, $row->tvr, $row->iad, $row->tsi, $row->arc, $row->cvm, $row->tran_type, $row->application_label);

        }
        $this->update_register_cart_data();

        $customer_info = $this->CI->Sale->get_customer($sale_id);
        $this->set_customer($customer_info->person_id, false);

        $this->set_comment($this->CI->Sale->get_comment($sale_id));
        $this->set_comment_on_receipt($this->CI->Sale->get_comment_on_receipt($sale_id));

        $this->set_sold_by_employee_id($this->CI->Sale->get_sold_by_employee_id($sale_id));
        $this->set_deleted_taxes($this->CI->Sale->get_deleted_taxes($sale_id));

    }

    function get_suspended_sale_id()
    {
        return $this->CI->session->userdata('suspended_sale_id');
    }

    function set_suspended_sale_id($suspended_sale_id)
    {
        $this->CI->session->set_userdata('suspended_sale_id',$suspended_sale_id);
    }

    function delete_suspended_sale_id()
    {
        $this->CI->session->unset_userdata('suspended_sale_id');
    }

    function get_change_sale_id()
    {
        return $this->CI->session->userdata('change_sale_id');
    }

    function set_change_sale_id($change_sale_id)
    {
        $this->CI->session->set_userdata('change_sale_id',$change_sale_id);
    }

    function delete_change_sale_id()
    {
        $this->CI->session->unset_userdata('change_sale_id');
    }
    function delete_item($line)
    {
        $items = $this->get_cart();
        // $items = sap_xep_mang($items,'line',true);
		$cartItemsAttribute = $this->getCartItemsAttribute(); 
		$cartItemsAttributeValue = $this->getCartItemsAttributeValue();
		$itemNameContainsLineSession 	= $this->getItemContainsLine();
		$cartItemsAttributeSet 	= $this->getCartItemsAttributeSet();

		foreach ($itemNameContainsLineSession as $key => $lines) {
			if (empty($lines['line'])) {
				unset($itemNameContainsLineSession[$key]);
				continue;
			}
			
			foreach ($lines['line'] as $keyLine => $lineNumber ) {
				if ($lineNumber == $line) {
					unset($itemNameContainsLineSession[$key]['line'][$keyLine]);
					break;
				}
			}
			if (empty($itemNameContainsLineSession[$key]['line'])) {
					unset($itemNameContainsLineSession[$key]);
			}

		}
        $_SESSION['items_bi_xoa'][$line] = $items[$line];
        $item_id=$this->get_item_id($line);
        unset($items[$line]);
		unset($cartItemsAttribute[$line]);
		unset($cartItemsAttributeValue[$line]);
		unset($cartItemsAttributeSet[$line]);
        $this->set_cart($items);
		$this->setCartItemsAttribute($cartItemsAttribute); 
		$this->setCartItemsAttributeValue($cartItemsAttributeValue); 
		$this->setItemContainsLine($itemNameContainsLineSession);
		$this->setCartItemsAttributeSet($cartItemsAttributeSet);
    }

    function empty_cart()
    {
        $this->CI->session->unset_userdata('cart');
        $this->CI->Register_cart->remove_data('cart',$this->CI->Employee->get_logged_in_employee_current_register_id());
    }

    function delete_customer($change_price = true)
    {
        $this->CI->session->unset_userdata('customer');

        if ($change_price == true)
        {
            $this->change_price();
        }
    }

    function clear_mode()
    {
        $this->CI->session->unset_userdata('sale_mode');
    }

    function clear_redeem()
    {
        $this->CI->session->unset_userdata('redeem');
    }

    function set_redeem($redeem)
    {
        $this->CI->session->set_userdata('redeem',$redeem);
    }

    function get_redeem()
    {
        return $this->CI->session->userdata('redeem');
    }


    function clear_cc_info()
    {
        $this->CI->session->unset_userdata('ref_no');
        $this->CI->session->unset_userdata('auth_code');
        $this->CI->session->unset_userdata('masked_account');
        $this->CI->session->unset_userdata('cc_token');
        $this->CI->session->unset_userdata('acq_ref_data');
        $this->CI->session->unset_userdata('process_data');
        $this->CI->session->unset_userdata('card_issuer');
        $this->CI->session->unset_userdata('entry_method');
        $this->CI->session->unset_userdata('aid');
        $this->CI->session->unset_userdata('tvr');
        $this->CI->session->unset_userdata('iad');
        $this->CI->session->unset_userdata('tsi');
        $this->CI->session->unset_userdata('arc');
        $this->CI->session->unset_userdata('cvm');
        $this->CI->session->unset_userdata('tran_type');
        $this->CI->session->unset_userdata('application_label');

        $this->CI->session->unset_userdata('CC_SUCCESS');
    }

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
    }

    function save_current_sale_state()
    {
        $this->sale_state = $this->CI->session->all_userdata();
    }

    function restore_current_sale_state()
    {
        if (isset($this->sale_state))
        {
            $this->CI->session->set_userdata($this->sale_state);
        }
    }

    function get_tax_total_amount($sale_id = false)
    {
        $taxes = $this->get_taxes($sale_id);
        $total_tax = 0;
        foreach($taxes as $name=>$value)
        {
            $total_tax+=$value;
        }

        return to_currency_no_money($total_tax);
    }

    function get_taxes($sale_id = false)
    {
        $taxes = array();

        if ($sale_id)
        {
            $taxes_from_sale = array_merge($this->CI->Sale->get_sale_items_taxes($sale_id), $this->CI->Sale->get_sale_item_kits_taxes($sale_id));
            foreach($taxes_from_sale as $key=>$tax_item)
            {
                $name = $tax_item['percent'].'% ' . $tax_item['name'];

                if ($tax_item['cumulative'])
                {
                    $prev_tax = ($tax_item['price']*$tax_item['quantity']-$tax_item['price']*$tax_item['quantity']*$tax_item['discount']/100)*(($taxes_from_sale[$key-1]['percent'])/100);
                    $tax_amount=(($tax_item['price']*$tax_item['quantity']-$tax_item['price']*$tax_item['quantity']*$tax_item['discount']/100) + $prev_tax)*(($tax_item['percent'])/100);
                }
                else
                {
                    $tax_amount=($tax_item['price']*$tax_item['quantity']-$tax_item['price']*$tax_item['quantity']*$tax_item['discount']/100)*(($tax_item['percent'])/100);
                }

                if (!isset($taxes[$name]))
                {
                    $taxes[$name] = 0;
                }
                $taxes[$name] += $tax_amount;
            }
        }
        else
        {
            $customer_id = $this->get_customer();
            $customer = $this->CI->Customer->get_info($customer_id);

            //Do not charge sales tax if we have a customer that is not taxable
            if (!$customer->taxable and $customer_id!=-1)
            {
                return array();
            }

            foreach($this->get_cart() as $line=>$item)
            {
                $price_to_use = $this->_get_price_for_item_in_cart($item);

                $tax_info = isset($item['item_id']) ? $this->CI->Item_taxes_finder->get_info($item['item_id']) : $this->CI->Item_kit_taxes_finder->get_info($item['item_kit_id']);
                foreach($tax_info as $key=>$tax)
                {
                    $name = $tax['percent'].'% ' . $tax['name'];

                    if ($tax['cumulative'])
                    {
                        $prev_tax = ($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100)*(($tax_info[$key-1]['percent'])/100);
                        $tax_amount=(($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100) + $prev_tax)*(($tax['percent'])/100);
                    }
                    else
                    {
                        $tax_amount=($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100)*(($tax['percent'])/100);
                    }

                    if (!in_array($name, $this->get_deleted_taxes()))
                    {
                        if (!isset($taxes[$name]))
                        {
                            $taxes[$name] = 0;
                        }

                        $taxes[$name] += $tax_amount;
                    }
                }
            }
        }
        return $taxes;
    }

    function get_items_in_cart()
    {
        $items_in_cart = 0;
        foreach($this->get_cart() as $item)
        {
            $items_in_cart+=$item['quantity'];
        }

        return $items_in_cart;
    }

    function get_subtotal($sale_id = FALSE, $arrParams = null, $options = null)
    {
        if($options['task'] == 'self') {
            $cart = $arrParams['cart'];
            $taxes = !empty($arrParams['taxes'])?$arrParams['taxes']:'';
            $subtotal = 0;

            foreach($cart as $item)
            {

                if (isset($item['tax_included']) && $item['tax_included'])
                {
                    $subtotal+=to_currency_no_money($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100,10);
                }
                else
                {
                    $subtotal+=to_currency_no_money($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100);
                }
            }

            return to_currency_no_money($subtotal);
        }else {
            $subtotal = 0;
            foreach($this->get_cart() as $item)
            {
                $price_to_use = $this->_get_price_for_item_in_cart($item, $sale_id);
                if (isset($item['tax_included']) && $item['tax_included'])
                {
                    $subtotal+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100,10);
                }
                else
                {
                    $subtotal+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
                }
            }

            return to_currency_no_money($subtotal);
        }

    }

    function _get_price_for_item_in_cart($item, $sale_id = FALSE)
    {
        $price_to_use = $item['price'];

        if (isset($item['item_id']))
        {
            $item_info = $this->CI->Item->get_info($item['item_id']);
            if($item_info->tax_included)
            {
                if ($sale_id)
                {
                    $this->CI->load->helper('items');
                    $price_to_use = get_price_for_item_excluding_taxes($item['line'], $item['price'], $sale_id);
                }
                else
                {
                    $this->CI->load->helper('items');
                    $price_to_use = get_price_for_item_excluding_taxes($item['item_id'], $item['price']);
                }
            }
        }
        elseif (isset($item['item_kit_id']))
        {
            $item_kit_info = $this->CI->Item_kit->get_info($item['item_kit_id']);
            if($item_kit_info->tax_included)
            {
                if ($sale_id)
                {
                    $this->CI->load->helper('item_kits');
                    $price_to_use = get_price_for_item_kit_excluding_taxes($item['line'], $item['price'], $sale_id);
                }
                else
                {
                    $this->CI->load->helper('item_kits');
                    $price_to_use = get_price_for_item_kit_excluding_taxes($item['item_kit_id'], $item['price']);
                }
            }
        }

        return $price_to_use;
    }

    function get_total($sale_id = false, $arrParams = null, $options = null)
    {
        $total = 0;

        if($options['task'] == 'self') {
            $cart = $arrParams['cart'];
            $taxes = $arrParams['taxes'];
            foreach($cart as $item)
            {

                if (isset($item['tax_included']) && $item['tax_included'])
                {
                    $total+=to_currency_no_money($item['unit_price']*$item['quantity']-$item['unit_price']*$item['quantity']*$item['discount']/100,10);
                }
                else
                {
                    $total+=to_currency_no_money($item['unit_price']*$item['quantity']-$item['unit_price']*$item['quantity']*$item['discount']/100);
                }
            }

            foreach($taxes as $tax)
            {
                $total+=$tax;
            }

            $total = $this->CI->config->item('round_cash_on_sales') && $this->is_sale_cash_payment() ?  round_to_nearest_05($total) : $total;
            return to_currency_no_money($total);

        }else {
            foreach($this->get_cart() as $item)
            {
                $price_to_use = $this->_get_price_for_item_in_cart($item, $sale_id);
                if (isset($item['tax_included']) && $item['tax_included'])
                {
                    $total+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100,10);
                }
                else
                {
                    $total+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
                }
            }

            foreach($this->get_taxes($sale_id) as $tax)
            {
                $total+=$tax;
            }

            $total = $this->CI->config->item('round_cash_on_sales') && $this->is_sale_cash_payment() ?  round_to_nearest_05($total) : $total;
            return to_currency_no_money($total);
        }
    }

    function is_sale_cash_payment()
    {
        foreach($this->get_payments() as $payment)
        {
            if(isset($payment['payment_type']) && $payment['payment_type'] ==  lang('common_cash'))
            {
                return true;
            }
        }

        return false;
    }

    function is_over_credit_limit()
    {
        $customer_id=$this->get_customer();
        if($customer_id!=-1)
        {
            $cust_info=$this->CI->Customer->get_info($customer_id);
            $current_sale_store_account_balance = $this->get_payment_amount(lang('common_store_account'));
            return $cust_info->credit_limit !== NULL && $cust_info->balance + $current_sale_store_account_balance > $cust_info->credit_limit;
        }

        return FALSE;
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

    function get_discount_all_percent()
    {
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
	
    // function set_sale_status($status){
    //     $this->CI->session->set_userdata('sale_status',$status);
    // }

    // function get_sale_status()
    // {
    //     if(!$this->CI->session->userdata('sale_status'))
    //         $this->set_sale_status(0);
    //     return $this->CI->session->userdata('sale_status');
    // }





}
?>