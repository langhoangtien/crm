<?php
require_once ("Secure_area.php");
class Sales extends Secure_area
{
	function __construct()
	{
		parent::__construct('sales');
		$this->load->library('sale_lib');
		$this->lang->load('sales');
		$this->lang->load('module');
		$this->lang->load('customers');
		$this->load->helper('items');
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Giftcard');
		$this->load->model('Tag');
		$this->load->model('Item');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_kit_taxes_finder');
        $this->load->model('Category');
        $this->load->model('Contract');
        $this->load->model('Location');
        $this->load->model('Service');
        $this->load->model('Sale_status');
		
		cache_item_and_item_kit_cart_info($this->sale_lib->get_cart());
	}

	function index($dont_switch_employee = 0)
	{
		if($_SESSION['edit_sale']){
			$this->sale_lib->clear_all();
			unset($_SESSION['edit_sale']);
		}
		
		// $this->Employee->get_employee_active();die();
		// $data['sale_status']= $this->db->get('phppos_sale_status')->result_array();
		// echo "<pre>";
		// var_dump($data['sale_status']);
		// die();
		$this->check_action_permission('edit_sale');
		$is_new = $this->input->post('r');
		if (!empty($is_new)) {
			$this->sale_lib->clear_all();
		}
		// die;
		if (count($this->sale_lib->get_cart()) > 0)
		{
			$dont_switch_employee = 1;
		}
		if($this->config->item('automatically_show_comments_on_receipt'))
		{
			$this->sale_lib->set_comment_on_receipt(1);
		}
		
		$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		
		$register_count = $this->Register->count_all($location_id);
		if ($register_count > 0)
		{
			if ($register_count == 1)
			{
				$registers = $this->Register->get_all($location_id);
				$register = $registers->row_array();
			
				if (isset($register['register_id']))
				{
					$this->Employee->set_employee_current_register_id($register['register_id']);
				}
			}
		
			if (!$this->Employee->get_logged_in_employee_current_register_id())
			{

				$this->load->view('sales/choose_register');		
				return;
			}
		}

		if ($this->config->item('track_cash')) 
		{
			if ($this->input->post('opening_amount') != '' && !$this->Register->is_register_log_open())  
			{
				$now = date('Y-m-d H:i:s');

				$cash_register = new stdClass();
				$cash_register->register_id = $this->Employee->get_logged_in_employee_current_register_id();
				$cash_register->employee_id_open = $this->session->userdata('person_id');
				$cash_register->shift_start = $now;
				$cash_register->open_amount = $this->input->post('opening_amount');
				$cash_register->close_amount = 0;
				$cash_register->cash_sales_amount = 0;
				$this->Register->insert_register($cash_register);

				redirect(site_url('sales'));
			}
			else if ($this->Register->is_register_log_open()) 
			{
				$this->_reload(array('dont_switch_employee' => $dont_switch_employee), false);
			} 
			else 
			{	
				$this->load->view('sales/opening_amount', array('denominations' => $this->Register->get_register_currency_denominations()->result_array()));
			}
		} 
		else 
		{
			// echo "1"; die();
			$this->_reload(array('dont_switch_employee' => $dont_switch_employee), false);
		}		
	}
	
	function choose_register($register_id)
	{
		if ($this->Register->exists($register_id))
		{
			$this->Employee->set_employee_current_register_id($register_id);
		}
		
		redirect(site_url('sales'));
		return;		
	}
	
	function clear_register()
	{
		//Clear out logged in register when we switch locations
		$this->Employee->set_employee_current_register_id(false);
		
		redirect(site_url('sales'));
		return;		
	}
	
	function register_add_subtract($mode,$return = 'sales')
	{
		$data = array();
		$data['mode'] = $mode;
		$data['return'] = $return;
		$cash_register = $this->Register->get_current_register_log();
		
		if (!$this->Register->is_register_log_open()) 
		{
			redirect(site_url('home'));
			return;
		}
		
		if ($this->input->post('amount') != '') 
		{
			$message = '';
			$amount = to_currency_no_money($this->input->post('amount'));
		
			if ($mode == 'add')
			{
				$cash_register->total_cash_additions+=$amount;
				$message = lang('sales_cash_successfully_added_to_drawer');
			}
			else
			{
				$cash_register->total_cash_subtractions+=$amount;
				$message = lang('sales_cash_successfully_removed_from_drawer');
			}
			$this->Register->update_register_log($cash_register);
						
						
			$employee_id_audit = $this->Employee->get_logged_in_employee_current_register_id();
			$register_audit_log_data = array(
				'register_log_id'=> $cash_register->register_log_id,
				'employee_id'=> $employee_id_audit,
				'date' => date('Y-m-d H:i:s'),
				'amount' => $mode == 'add' ? $amount : -$amount,
				'note' => $this->input->post('note'),
			);
			
			$this->Register->insert_audit_log($register_audit_log_data);
			
			$this->session->set_flashdata('cash_drawer_add_subtract_message', $message);
			
			if ($return == 'sales')
			{
				redirect('sales');	
			}
			elseif ($return == 'closeregister')
			{
				redirect('sales/closeregister?continue=home');
			}
		} 
		else
		{
			
			if ($mode == 'add')
			{
				$data['amount'] = to_currency($cash_register->total_cash_additions);
			}
			else
			{
				$data['amount'] = to_currency($cash_register->total_cash_subtractions);
				
			}
			
			$this->load->view('sales/register_add_subtract', $data);
		}
		
	}
	
	function closeregister() 
	{

		if (!$this->Register->is_register_log_open()) 
		{
			redirect(site_url('home'));
			return;
		}
		$cash_register = $this->Register->get_current_register_log();
		$register_log_id = $cash_register->register_log_id;
		
		$continueUrl = $this->input->get('continue');

        $shift_category_id = $this->config->item('shift_category_id');
        if($shift_category_id > 0) {
            $category = $this->Category->getItem($shift_category_id);
        }
		if ($this->input->post('closing_amount') != '') {
            $total = $this->input->post('closing_amount');
			$now = date('Y-m-d H:i:s');
			$cash_register->register_id = $this->Employee->get_logged_in_employee_current_register_id();
			$cash_register->employee_id_close = $this->session->userdata('person_id');
			$cash_register->shift_end = $now;
			$cash_register->close_amount = $total;
			$cash_register->cash_sales_amount = $this->Sale->get_cash_sales_total_for_shift($cash_register->shift_start, $cash_register->shift_end);
			unset($cash_register->register_log_id);
			$cash_register->notes = $this->input->post('notes');

            if($this->input->post('other_amount') != 0)
                $other_amount = $this->input->post('other_amount');
            else
                $other_amount = 0;

            $cash_register->cost = $other_amount;

            if(!empty($category)) {
                $cash_register->cost_name = $category['name'];
            }else
                $cash_register->cost_name = 'Chi phí';

		    $this->Register->update_register_log($cash_register);

            //update expense
            if($this->input->post('other_amount') != 0 && !empty($category)) {
                $expense_data = array(
                    'expense_type' => 1,
					'expense_options' => 'other',
					'payment_type' => 'Tiền mặt',
                    'expense_description' => $category['name'] . ' lúc ' . @date("Y-m-d H:i:s"),
                    'expense_reason' => $category['name'],
                    'expense_date' => @date("Y-m-d H:i:s"),
                    'expense_amount' => (float)$other_amount,
                    'expense_tax' => 0,
                    'expense_note' => $category['name'],
					'sale_id' => NULL,
					'receiving_id' => NULL,
                    'employee_id' => $this->config->item('shift_user_id'),
                    'approved_employee_id' => NULL,
                    'category_id' => $this->config->item('shift_category_id'),
                    'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
                );

                $this->Expense->save($expense_data);
            }

			if ($continueUrl == 'logout') 
			{
				redirect(site_url('home/logout'));
			} 
			elseif($continueUrl == 'timeclocks')
			{
				redirect(site_url('timeclocks'));				
			}
			elseif($continueUrl == 'closeoutreceipt')
			{
				redirect(site_url("reports/register_log_details/$register_log_id"));
				
			}
			else
			{
				redirect(site_url('home'));
			}
		} 
		else
		{
			$cash_sales = $this->Sale->get_cash_sales_total_for_shift($cash_register->shift_start, date("Y-m-d H:i:s"));
			
			$this->load->view('sales/closing_amount', array(
				'continue'=>$continueUrl ? "?continue=$continueUrl" : '',
				'open_amount' => $cash_register->open_amount,
				'closeout'=>$cash_register->open_amount + $cash_sales + $cash_register->total_cash_additions - $cash_register->total_cash_subtractions,
				'cash_sales' => $cash_sales,
				'total_cash_additions' => $cash_register->total_cash_additions,
				'total_cash_subtractions' => $cash_register->total_cash_subtractions,
				'denominations' => $this->Register->get_register_currency_denominations()->result_array(),
				'register_log_id' => $register_log_id,
                'category' => $category,
			));
		}
	}
	
	function item_search_n9() {
		//allow parallel searchs to improve performance.
// 		session_write_close();
// 		$suggestions = $this->Item->get_item_search_n9(trim($this->input->get('term')),100);
// 		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv_n9($this->input->get('term'),100));
		$suggestions = $this->Item->search_item_for_contract(trim($this->input->get('term')));
		echo json_encode($suggestions);
	}
	
	function item_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),100);
		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),100));
		echo json_encode($suggestions);
	}

	function customer_search()
	{
		//allow parallel searchs to improve performance.

		$suggestions =$this->Customer->goi_y_khach_hang(trim($this->input->get('term')));
		// echo $this->db->last_query();die();
		// session_write_close();
		// $suggestions = $this->Customer->get_customer_search_suggestions(trim($this->input->get('term')),100);
		// echo "<pre>";
		// var_dump($suggestions);die();
		echo json_encode($suggestions);
	}

	function select_customer()
	{
		$data = array();
		$customer_id = $this->input->post("customer");
			
		if ($this->Customer->account_number_exists($customer_id))
		{
			$customer_id = $this->Customer->customer_id_from_account_number($customer_id);
		}
		
		if ($this->Customer->exists($customer_id))
		{
			$customer_info=$this->Customer->get_info($customer_id);
		
			if ($customer_info->tier_id)
			{
				$this->sale_lib->set_selected_tier_id($customer_info->tier_id);
			}
			
			$this->sale_lib->set_customer($customer_id);
			if($this->config->item('automatically_email_receipt'))
			{
				$this->sale_lib->set_email_receipt(1);
			}
		}
		else
		{
			$data['error']=lang('sales_unable_to_add_customer');
		}
		$this->_reload($data);
	}

	function change_mode($mode = false, $redirect = false)
	{
        $this->sale_lib->clear_all();
		$previous_mode = $this->sale_lib->get_mode();
		
		$mode = $mode === FALSE ? $this->input->post("mode") : $mode;
		
		if ($previous_mode == 'store_account_payment' && ($mode == 'sale' || $mode = 'return' || $mode = 'vat_order')) {
			$this->sale_lib->empty_cart();
		}
		
		$this->sale_lib->set_mode($mode);
		
		if ($mode == 'store_account_payment') {
			$store_account_payment_item_id = $this->Item->create_or_update_store_account_item();
			$this->sale_lib->empty_cart();
			$this->sale_lib->add_item($store_account_payment_item_id,1);
            $this->sale_lib->set_store_account_payment_value(1);
		}
		if ($redirect) {
			redirect('sales');
		} else {
			$this->_reload();
		}
	}
	
	function set_comment() 
	{
 	  $this->sale_lib->set_comment($this->input->post('comment'));
	}

	function set_selected_payment()
	{
		$this->sale_lib->set_selected_payment($this->input->post('payment'));
	}
	
	function set_change_sale_date() 
	{
 	  $this->sale_lib->set_change_sale_date($this->input->post('change_sale_date'));
	}
	
	function set_change_sale_date_enable() 
	{
 	  $this->sale_lib->set_change_sale_date_enable($this->input->post('change_sale_date_enable'));
	  if (!$this->sale_lib->get_change_sale_date())
	  {
	 	  $this->sale_lib->set_change_sale_date(date(get_date_format()));
	  }
	}
	
	function set_comment_on_receipt() 
	{
 	  $this->sale_lib->set_comment_on_receipt($this->input->post('show_comment_on_receipt'));
	}
	
	function set_email_receipt()
	{
 	  $this->sale_lib->set_email_receipt($this->input->post('email_receipt'));
	}

	function set_save_credit_card_info() 
	{
 	  $this->sale_lib->set_save_credit_card_info($this->input->post('save_credit_card_info'));
	}
	
	function set_use_saved_cc_info()
	{
 	  $this->sale_lib->set_use_saved_cc_info($this->input->post('use_saved_cc_info'));
	}
	
	function set_prompt_for_card()
	{
 	  $this->sale_lib->set_prompt_for_card($this->input->post('prompt_for_card'));
	}
	
	function set_tier_id() 
	{
	  $data = array();
		
 	  $this->sale_lib->set_selected_tier_id($this->input->post('tier_id'));
	  
	  foreach(array_keys($this->sale_lib->get_cart()) as $line)
	  {
	  	  if ($this->sale_lib->below_cost_price_item($line))
	  	  { 
			  if ($this->config->item('do_not_allow_below_cost'))
			  {
				  $this->sale_lib->set_selected_tier_id(0);
				  $data['error'] = lang('sales_selling_item_below_cost');
			  }
			  else
			  {
				  $data['warning'] = lang('sales_selling_item_below_cost');
			  }
			  $this->_reload($data);
			  return;
		  }
	  }
	  
	  $this->_reload($data);
	}

	function set_sold_by_employee_id() 
	{
 	  $sale_employee = $this->sale_lib->set_sold_by_employee_id($this->input->post('sold_by_employee_id') ? $this->input->post('sold_by_employee_id') : NULL);
      if(isset($_SESSION['group_employees'][1]) && $sale_employee > 0) {
          $employee_info = $this->Employee->get_information($this->input->post('sold_by_employee_id'));
          $tmp = array(
              'id'=> $this->input->post('sold_by_employee_id'),
              'name' => $employee_info['first_name']
          );

          $_SESSION['group_employees'][1]['list'] = array($tmp);
      }
	}

	function payment_check($amount)
	{
		return $amount != '0' || $this->sale_lib->get_total() == 0;
	}
		

	# Gửi lỗi về cho trang chủ
	function add_payment()
	{
                                                                
        $_POST['amount_tendered'] = convert_number($_POST['amount_tendered']);
        // nợ đầu
        $customer_balance = convert_number((int)$_POST['customer_balance']);


		$data=array();
		$this->form_validation->set_rules('amount_tendered', 'lang:sales_amount_tendered', 'required|callback_payment_check');
		$sale_mode = $this->sale_lib->get_mode();
		
		//determine what button is clicked , quick sale
		$data['is_add_payment_click'] =  $_POST['is_add_payment_click'];

		if ($this->form_validation->run() == FALSE)
		{
			if ( $this->input->post('payment_type') == lang('common_giftcard') )
			{
				$data['error']=lang('sales_must_enter_numeric_giftcard');
			}
			elseif($this->input->post('amount_tendered') == '0' && $this->sale_lib->get_total() != 0)
			{
				$data['error']=lang('sales_cannot_add_zero_payment');
			}
			else
			{
				$data['error']=lang('sales_must_enter_numeric');
			}
 			$this->_reload($data);
 			return;
		}

		# nếu ghi nợ thì sẽ vào đây  'Sổ ghi nợ'
		if (($this->input->post('payment_type') == lang('common_store_account') && $this->sale_lib->get_customer() == -1) ||
			($this->sale_lib->get_mode() == 'store_account_payment' && $this->sale_lib->get_customer() == -1)
			) 
		{
				$data['error']=lang('sales_customer_required_store_account');
				$this->_reload($data);
				return;
		}

		$store_account_payment_amount = $this->sale_lib->get_total();

		if ($this->sale_lib->get_mode() == 'store_account_payment'  && $store_account_payment_amount == 0) 
		{
          $data['error']=lang('sales_store_account_payment_item_must_not_be_0');
          $this->_reload($data);
          return;
		}
		$this->load->helper('sale');
		if(is_sale_integrated_cc_processing() && $this->input->post('payment_type') ==lang('common_credit'))
		{
			$data['error']=lang('sales_unable_to_add_payment');
			$this->_reload($data);
			return;
		}
		
		$payment_type=$this->input->post('payment_type');
		$payment_amount = $this->input->post('amount_tendered');


        $payments = $this->sale_lib->get_payments();
        if(!empty($payments)) {
            $payment_type = $this->input->post('payment_type');
            $amount_tendered = $this->input->post('amount_tendered');

            $payment_total = $amount_tendered;
            foreach($payments as $val) {
                $payment_total = $payment_total + $val['payment_amount'];
            }

            $sale_total = $this->sale_lib->get_total();

            if($payment_total > $sale_total) {
                $data['error'] = 'Tổng giá trị thanh toán không được lớn hơn giá trị đơn hàng.';
                
                $this->_reload($data);
                return;
            }

        }

        # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng # lưu thông tin bán hàng 
        // echo $payment_amount;
        // echo $payment_type;
        // die;
		if( !$this->sale_lib->add_payment($payment_type,$payment_amount,$customer_balance))
		{
			$data['error']=lang('sales_unable_to_add_payment');
		}

		$this->_reload($data);
	}

	//Alain Multiple Payments
	function delete_payment($payment_id)
	{
		$this->sale_lib->delete_payment($payment_id);
		$this->_reload();
	}
	
	function add_n9() {
		$data=array();
		$mode = $this->sale_lib->get_mode();
		
		$item_id_or_number = $this->input->post("item");
		$info = $this->Item->getInformation(array('item_id_or_number'=>$item_id_or_number));
        if(!empty($info)){
		    $item_id_or_number_or_item_kit_or_receipt = $info['item_id'];

        }
        else {
            $info = $this->Item_kit->getInformation(array('item_id'=>$item_id));
            $item_id_or_number_or_item_kit_or_receipt = 'KIT ' . $info['item_kit_id'];
            $item_kit = $info['item_kit_id'];
            $item_kit_peel = $info['peel'];
        }
        if($this->config->item('config_default_sale_quantity') != 1){
					 $quantity = $this->config->item('config_default_sale_quantity');
				}
        else
				{
					$quantity = 1;
				}
		if($this->sale_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return')
		{
			$this->sale_lib->return_entire_sale($item_id_or_number_or_item_kit_or_receipt);
		} elseif($this->sale_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt)) {
            if($item_kit_peel == 1) {
                $items_from_item_kit = $this->Item_kit->get_items_from_item_kit(array('item_kit_id'=>$item_kit));
                if(!empty($items_from_item_kit)) {
                    foreach($items_from_item_kit as $item) {
                        $flag = $this->sale_lib->add_item($item['item_id'],$item['quantity']);
                        if($flag == false)
                            $error_items[] = $item['item_name'];
                    }
                }

                $boms_from_item_kit = $this->Item_kit->get_bom_from_item_kit(array('item_kit_id'=>$item_kit));
                if(!empty($boms_from_item_kit)) {
                    foreach($boms_from_item_kit as $bom) {
                        $flag = $this->sale_lib->add_item_kit($bom['bom_id'],$bom['quantity']);
                        if($flag == false)
                            $error_items[] = $bom['bom_name'];
                    }
                }

                if(count($error_items)>0) {
                    $error_items_name = implode(', ', $error_items);
                    $data['error'] = $error_items_name . ' không thêm được vào giỏ hàng. Vui lòng kiểm tra lại.';
                }
            }else {
                if($this->Item_kit->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item_kit->get_info($this->Item_kit->get_item_kit_id($item_id_or_number_or_item_kit_or_receipt))->deleted)
                {
                    $data['error']=lang('sales_unable_to_add_item');
                }
                else
                {
                    if (!$this->sale_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt, $quantity))
                    {
                        $out_of_stock_check = FALSE;

                        if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
                        {
                            if (strpos(strtolower($item_id_or_number_or_item_kit_or_receipt), 'kit') !== FALSE)
                            {
                                //KIT #
                                $pieces = explode(' ',$item_id_or_number_or_item_kit_or_receipt);
                                $current_item_kit_id = (int)$pieces[1];
                            }
                            else
                            {
                                $current_item_kit_id = $this->Item_kit->get_item_kit_id($item_id_or_number_or_item_kit_or_receipt);
                            }

                            if ($current_item_kit_id !== FALSE && $this->sale_lib->will_be_out_of_stock_kit($current_item_kit_id, $quantity))
                            {
                                $out_of_stock_check = TRUE;
                            }
                        }

                        if ($out_of_stock_check)
                        {
                            $data['error']=lang('sales_unable_to_add_item_out_of_stock');
                        }
                        else
                        {
                            $data['error']=lang('sales_unable_to_add_item');
                        }
                    }
                    else
                    {
                        //As surely a Kit item , do out of stock check
//                         $item_kit_id = $this->sale_lib->get_valid_item_kit_id($item_id_or_number_or_item_kit_or_receipt);

//                         if($this->sale_lib->out_of_stock_kit($item_kit_id))
//                         {
//                             $data['warning'] = lang('sales_quantity_less_than_zero');
//                         }
//                         else
//                         {
//                             $data['success']= TRUE;
//                             $data['success_no_message']= TRUE;
//                         }ToiNT
                        $data['success']= TRUE;
                        $data['success_no_message']= TRUE;
                    }
                    //Not doing check for price being less than cost price for performace reasons AND a user should know its below cost price
                }
            }
		}
		elseif(!$this->sale_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity))
		{
			$out_of_stock_check = FALSE;
			if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
			{
				//make sure item exists
				if(!$this->Item->exists(does_contain_only_digits($item_id_or_number_or_item_kit_or_receipt) ? (int)$item_id_or_number_or_item_kit_or_receipt : -1))
				{
					//try to get item id given an item_number
					$current_item_id = $this->Item->get_item_id($item_id_or_number_or_item_kit_or_receipt);
				}
				else
				{
					$current_item_id = (int)$item_id_or_number_or_item_kit_or_receipt;
				}

				if ($current_item_id !== FALSE && $this->sale_lib->will_be_out_of_stock($current_item_id, $quantity))
				{
					$out_of_stock_check = TRUE;
				}
			}
				
			if ($out_of_stock_check)
			{
				$data['error']=lang('sales_unable_to_add_item_out_of_stock');
			}
			else
			{
				$data['error']=lang('sales_unable_to_add_item');
			}
		}
		else
		{
			$data['success']= TRUE;
			$data['success_no_message']= TRUE;
		}
		
// 		if(!$this->config->item('do_not_allow_out_of_stock_items_to_be_sold') && $this->sale_lib->out_of_stock($item_id_or_number_or_item_kit_or_receipt))
// 		{
// 			$data['warning'] = lang('sales_quantity_less_than_zero');
// 		}
		
		//Not doing check for price being less than cost price for performace reasons AND a user should know its below cost price
		
		if ($this->_is_tax_inclusive() && count($this->sale_lib->get_deleted_taxes()) > 0)
		{
			$data['warning'] = lang('sales_cannot_delete_taxes_if_using_tax_inclusive_items');
		}
		
		if ($this->config->item('edit_item_price_if_zero_after_adding'))
		{
			$last_item_price = $this->sale_lib->get_last_item_added_price();
			if ($last_item_price == 0 && $last_item_price !== FALSE)
			{
				$data['price_zero'] = TRUE;
			}
		}
		
		//We were able to add; now check if the last $line is below cost price
		if (isset($data['success']) && $data['success'])
		{
			$line = $this->sale_lib->get_last_item_line();
			
			if ($this->sale_lib->below_cost_price_item($line))
			{
				if ($this->config->item('do_not_allow_below_cost'))
				{
					$this->sale_lib->delete_item($line);
					$data['error'] = lang('sales_selling_item_below_cost');
					$data['success'] = FALSE;
				}
				else
				{
					$data['warning'] = lang('sales_selling_item_below_cost');
				}
			}
		}
        if ($this->config->item('company_user') == 'remHaMy') {
            $line = $this->sale_lib->get_last_item_line();
            
            $item_info = $this->Item->get_info($item_id_or_number_or_item_kit_or_receipt);
            $itemAttributeSetId = $item_info->attribute_set_id;
		
            if (!empty($itemAttributeSetId)) {
                
                $itemAttributes     = $this->Attribute_set->get_attributes($itemAttributeSetId);
                $cartItemsAttribute = $this->sale_lib->getCartItemsAttribute();
                $cartItemsAttribute[$line] = $itemAttributes;	
                $this->sale_lib->setCartItemsAttribute($cartItemsAttribute);
            }
            $this->load->helper('convert_formula');
            $attributeValues = [];
            $attribute_values  = $this->Attribute->get_entity_attributes(array('entity_id' => $info['item_id'], 'entity_type' => 'items'));
            // set default value forr rem haMy only
            // sử dụng id thuộc tính trong database
            $attribute_values[8] = new stdClass(); // attr 8: khổ vải
            $attribute_values[8]->entity_value =  $item_info->size;
            $attribute_values[9] = new stdClass(); // attr 9: đơn giá vải
            $attribute_values[9]->entity_value = $item_info->unit_price;
            $attribute_values[10] = new stdClass(); 
            $attribute_values[10]->entity_value = 280000;
            $attribute_values[11] = new stdClass();
           $attribute_values[11]->entity_value = 100000;
            $CT_RK_VAI_KHO_RONG_CC_260CM = 4;
            if ($itemAttributeSetId == $CT_RK_VAI_KHO_RONG_CC_260CM) {
               $attribute_values[3] = new stdClass(); // attr 3 chiều cao cửa
               $attribute_values[3]->entity_value = 260;
               $attribute_values_key_code_['ccc'] = 260;
            }
            $attribute_values_key_code_ = $this->Attribute->get_entity_attributes(array('entity_id' => $info['item_id'], 'entity_type' => 'items', 'key_code' =>true));
            foreach ($attribute_values as $attribute_id => $attribute_value ) {
                 $attributeValues[$attribute_id] = new stdClass();
                if((substr(trim($attribute_value->entity_value),0,1)== "=") && !empty($attribute_value->entity_value)) {
                   $convertedFormula = new ConvertFormula($attribute_value->entity_value, $attribute_values_key_code_);
                   $attributeValues[$attribute_id]->entity_value = $convertedFormula->executeFormula();
                } else {
                    $attributeValues[$attribute_id]->entity_value = $attribute_value->entity_value;
                }
            }
            $cartItemsAttributeValue = $this->sale_lib->getCartItemsAttributeValue();
            $cartItemsAttributeValue[$line] = $attributeValues;
            $this->sale_lib->setCartItemsAttributeValue($cartItemsAttributeValue);
            $cartItemsCategory = $this->sale_lib->getCartItemsCategory();
            $cartItemsCategory[$line] = $item_info->category_id;
            $this->sale_lib->setCartItemsCategory($cartItemsCategory);
           
        }
            $this->_reload($data);
	}

	function add()
	{		
		$data=array();
		$mode = $this->sale_lib->get_mode();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post("item");
		$quantity = $mode=="sale" ? 1:1;

		if($this->sale_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return')
		{
			$this->sale_lib->return_entire_sale($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif($this->sale_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			if($this->Item_kit->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item_kit->get_info($this->Item_kit->get_item_kit_id($item_id_or_number_or_item_kit_or_receipt))->deleted)
			{
				$data['error']=lang('sales_unable_to_add_item');			
			}
			else
			{
				if (!$this->sale_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt, $quantity))
				{
					$out_of_stock_check = FALSE;
			
					if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
					{
						if (strpos(strtolower($item_id_or_number_or_item_kit_or_receipt), 'kit') !== FALSE)
						{
							//KIT #
							$pieces = explode(' ',$item_id_or_number_or_item_kit_or_receipt);
							$current_item_kit_id = (int)$pieces[1];	
						}
						else
						{
							$current_item_kit_id = $this->Item_kit->get_item_kit_id($item_id_or_number_or_item_kit_or_receipt);
						}
						
						if ($current_item_kit_id !== FALSE && $this->sale_lib->will_be_out_of_stock_kit($current_item_kit_id, $quantity))
						{
							$out_of_stock_check = TRUE;
						}
					}
			
					if ($out_of_stock_check)
					{
						$data['error']=lang('sales_unable_to_add_item_out_of_stock');
					}
					else
					{
						$data['error']=lang('sales_unable_to_add_item');
					}
				}
				else
				{
					//As surely a Kit item , do out of stock check
					$item_kit_id = $this->sale_lib->get_valid_item_kit_id($item_id_or_number_or_item_kit_or_receipt);

					if($this->sale_lib->out_of_stock_kit($item_kit_id))
					{
						$data['warning'] = lang('sales_quantity_less_than_zero');
					}
					else
					{
						$data['success']= TRUE;
						$data['success_no_message']= TRUE;	
					}
				}
				//Not doing check for price being less than cost price for performace reasons AND a user should know its below cost price
			}	
		}
		elseif($this->Item->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item->get_info($this->Item->get_item_id($item_id_or_number_or_item_kit_or_receipt))->deleted || !$this->sale_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity))
		{
			$out_of_stock_check = FALSE;
			
			if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
			{
				//make sure item exists
				if(!$this->Item->exists(does_contain_only_digits($item_id_or_number_or_item_kit_or_receipt) ? (int)$item_id_or_number_or_item_kit_or_receipt : -1))	
				{
					//try to get item id given an item_number
					$current_item_id = $this->Item->get_item_id($item_id_or_number_or_item_kit_or_receipt);
				}
				else
				{
					$current_item_id = (int)$item_id_or_number_or_item_kit_or_receipt;
				}
				
				if ($current_item_id !== FALSE && $this->sale_lib->will_be_out_of_stock($current_item_id, $quantity))
				{
					$out_of_stock_check = TRUE;
				}
			}
			
			if ($out_of_stock_check)
			{
				$data['error']=lang('sales_unable_to_add_item_out_of_stock');
			}
			else
			{
				$data['error']=lang('sales_unable_to_add_item');
			}
		}
		else
		{
			$data['success']= TRUE;
			$data['success_no_message']= TRUE;
		}
		
		if(!$this->config->item('do_not_allow_out_of_stock_items_to_be_sold') && $this->sale_lib->out_of_stock($item_id_or_number_or_item_kit_or_receipt))
		{
			$data['warning'] = lang('sales_quantity_less_than_zero');
		}		
		
		//Not doing check for price being less than cost price for performace reasons AND a user should know its below cost price
		
		if ($this->_is_tax_inclusive() && count($this->sale_lib->get_deleted_taxes()) > 0)
		{
			$data['warning'] = lang('sales_cannot_delete_taxes_if_using_tax_inclusive_items');
		}
				
		if ($this->config->item('edit_item_price_if_zero_after_adding'))
		{
			$last_item_price = $this->sale_lib->get_last_item_added_price();
			if ($last_item_price == 0 && $last_item_price !== FALSE)
			{
				$data['price_zero'] = TRUE;
			}
		}
		
		//We were able to add; now check if the last $line is below cost price
		if (isset($data['success']) && $data['success'])
		{
		  $line = $this->sale_lib->get_last_item_line();
			
 	  	  if ($this->sale_lib->below_cost_price_item($line))
 	  	  { 
 			  if ($this->config->item('do_not_allow_below_cost'))
 			  {
				  $this->sale_lib->delete_item($line);
 				  $data['error'] = lang('sales_selling_item_below_cost');
				  $data['success'] = FALSE;
 			  }
 			  else
 			  {
 				  $data['warning'] = lang('sales_selling_item_below_cost');
 			  }
 		  }
		}

		$this->_reload($data);
	}
	
	function _is_tax_inclusive()
	{
		$is_tax_inclusive = FALSE;
		foreach($this->sale_lib->get_cart() as $item)
		{
			if (isset($item['item_id']))
			{
				$cur_item_info = $this->Item->get_info($item['item_id']);
				if ($cur_item_info->tax_included)
				{
					$is_tax_inclusive = TRUE;
					break;
				}
			}
			else //item kit
			{
				$cur_item_kit_info = $this->Item_kit->get_info($item['item_kit_id']);
				
				if ($cur_item_kit_info->tax_included)
				{
					$is_tax_inclusive = TRUE;
					break;
				}
				
			}
		}
		
		return $is_tax_inclusive;		
	}

	function edit_item($line)
	{
		$data= array();

		$this->form_validation->set_rules('price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('cost_price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('quantity', 'lang:common_quantity', 'numeric');
		$this->form_validation->set_rules('discount', 'lang:common_discount_percent', 'numeric');
		
		if($this->input->post("name"))
		{
			$variable = $this->input->post("name");
			$$variable = $this->input->post("value");
		}
		
		if (isset($discount) && $discount !== NULL && $discount == '')
		{
			$discount = 0;
		}

		$can_edit = TRUE;

		if ($this->form_validation->run() != FALSE)
		{			
			if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
			{
				if (isset($quantity) && $this->sale_lib->is_kit_or_item($line) == 'item')
				{
					$current_item_id = $this->sale_lib->get_item_id($line);
					$before_quantity = $this->sale_lib->get_quantity_at_line($line);
					
					if ($this->sale_lib->will_be_out_of_stock($current_item_id, isset($quantity) ? $quantity - $before_quantity : 0))
					{
						$can_edit = FALSE;
					}
				}
				elseif (isset($quantity) && $this->sale_lib->is_kit_or_item($line) == 'kit')
				{
					$current_item_kit_id = $this->sale_lib->get_kit_id($line);
					$before_quantity = $this->sale_lib->get_quantity_at_line($line);
					
					if ($this->sale_lib->will_be_out_of_stock_kit($current_item_kit_id, isset($quantity) ? $quantity - $before_quantity : 0))
					{
						$can_edit = FALSE;
					}
				}
				
				if (!$can_edit)
				{
					$data['error']=lang('sales_unable_to_add_item_out_of_stock');
				}
			}			
		}
		else
		{
			$can_edit = FALSE;
			$data['error']=lang('sales_error_editing_item');
		}
		
		if($this->sale_lib->is_kit_or_item($line) == 'item')
		{
			if($this->sale_lib->out_of_stock($this->sale_lib->get_item_id($line)))
			{
				$data['warning'] = lang('sales_quantity_less_than_zero');
			}
			
			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
			{
				if ($this->config->item('do_not_allow_below_cost'))
				{
					$can_edit = FALSE;
					$data['error'] = lang('sales_selling_item_below_cost');
				}
				else
				{
					$data['warning'] = lang('sales_selling_item_below_cost');
				}
			}
		}
		elseif($this->sale_lib->is_kit_or_item($line) == 'kit')
		{
		    if($this->sale_lib->out_of_stock_kit($this->sale_lib->get_kit_id($line)))
		    {
			    $data['warning'] = lang('sales_quantity_less_than_zero');
		    }
			 
 			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
 			{
				if ($this->config->item('do_not_allow_below_cost'))
				{
					$can_edit = FALSE;
					$data['error'] = lang('sales_selling_item_below_cost');
				}
				else
				{
					$data['warning'] = lang('sales_selling_item_below_cost');
				}
 			}
		}
		
		if ($can_edit)
		{
			$this->sale_lib->edit_item($line,isset($description) ? $description : NULL,isset($serialnumber) ? $serialnumber : NULL, isset($quantity) ? $quantity : NULL,isset($discount) ? $discount : NULL,isset($price) ? $price: NULL,isset($cost_price) ? $cost_price: NULL);
		}
		
		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->sale_lib->delete_item($item_number);
        if ($this->config->item('company_user') == 'remHaMy') {
            $this->sale_lib->updateCartWhenLineChanged();
        }
		
		if (count($this->sale_lib->get_cart()) == 0)
		{
			$this->sale_lib->clear_all();
		}
		$this->_reload();
	}

	function delete_customer()
	{
		$this->sale_lib->delete_customer();
   	  	$this->sale_lib->set_selected_tier_id(0);
        $this->sale_lib->clear_sale_store_payment();
		$this->_reload();
	}
	
	function _get_cc_processor()
	{
		if (!$this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			return false;
		}
		
		//If we have setup Mercury....or if it is not set then default to Mercury
		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			//Mobile always uses hosted checkout as we do NOT have mobile support for EMV
			if ($this->agent->is_mobile())
			{
				require_once (APPPATH.'libraries/Mercuryhostedcheckoutprocessor.php');
				$credit_card_processor = new Mercuryhostedcheckoutprocessor($this);	
				return $credit_card_processor;
			}
		
			//EMV
			if ($this->Location->get_info_for_key('emv_merchant_id') && $this->Location->get_info_for_key('com_port') && $this->Location->get_info_for_key('listener_port'))
			{
				require_once (APPPATH.'libraries/Mercuryemvusbprocessor.php');
				$credit_card_processor = new Mercuryemvusbprocessor($this);
				return $credit_card_processor;
			}
			else //Default hosted checkout
			{
				require_once (APPPATH.'libraries/Mercuryhostedcheckoutprocessor.php');
				$credit_card_processor = new Mercuryhostedcheckoutprocessor($this);
				return $credit_card_processor;
			}
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			require_once (APPPATH.'libraries/Stripeprocessor.php');
			$credit_card_processor = new Stripeprocessor($this);
			return $credit_card_processor;
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			require_once (APPPATH.'libraries/Braintreeprocessor.php');
			$credit_card_processor = new Braintreeprocessor($this);
			return $credit_card_processor;
		}
		return false;
	}
	
	function start_cc_processing()
	{
		if ($this->config->item('test_mode'))
		{
			$this->_reload(array('error' => lang('common_in_test_mode')), false);
			return;
		}
		
		$cc_amount = round($this->sale_lib->get_payment_amount(lang('common_credit')),2);
		$total = round($this->sale_lib->get_total(),2);		
		
		if ($total >=0 && $cc_amount > $total)
		{
			$this->_reload(array('error' => lang('sales_credit_card_payment_is_greater_than_total_cannot_complete')), false);
		}
		elseif ($total < 0 && $cc_amount < $total)
		{
			$this->_reload(array('error' => lang('sales_cannot_refund_more_than_sale_total')), false);
		}
		else
		{
			$credit_card_processor = $this->_get_cc_processor();
			$credit_card_processor->start_cc_processing();
		}		
	}
	
	function finish_cc_processing()
	{
		$credit_card_processor = $this->_get_cc_processor();
		$credit_card_processor->finish_cc_processing();
	}
	
	function finish_cc_processing_saved_card()
	{
		$credit_card_processor = $this->_get_cc_processor();
		$credit_card_processor->finish_cc_processing_saved_card();
	}
	
	function cancel_cc_processing()
	{
		$credit_card_processor = $this->_get_cc_processor();
		$credit_card_processor->cancel_cc_processing();
	}
	
	function set_sequence_no_emv()
	{
		if ($this->input->post('sequence_no'))
		{
			$this->session->set_userdata('sequence_no',$this->input->post('sequence_no'));
		}
	}
	
	function declined()
	{
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$emp_info=$this->Employee->get_info($employee_id);
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		
		$data['is_sale'] = FALSE;
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['transaction_time'] = date(get_date_format().' '.get_time_format());
		$data['payments']=$this->sale_lib->get_payments();
		$data['register_name'] = $this->Register->get_register_name($this->Employee->get_logged_in_employee_current_register_id());
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $employee_id ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;			
		}
		
		$data['auth_code'] = $this->session->userdata('auth_code') ? $this->session->userdata('auth_code') : '';
		$data['ref_no'] = $this->session->userdata('ref_no') ? $this->session->userdata('ref_no') : '';
		$data['entry_method'] = $this->session->userdata('entry_method') ? $this->session->userdata('entry_method') : '';
		$data['aid'] = $this->session->userdata('aid') ? $this->session->userdata('aid') : '';
		$data['tvr'] = $this->session->userdata('tvr') ? $this->session->userdata('tvr') : '';
		$data['iad'] = $this->session->userdata('iad') ? $this->session->userdata('iad') : '';
		$data['tsi'] = $this->session->userdata('tsi') ? $this->session->userdata('tsi') : '';
		$data['arc'] = $this->session->userdata('arc') ? $this->session->userdata('arc') : '';
		$data['cvm'] = $this->session->userdata('cvm') ? $this->session->userdata('cvm') : '';
		$data['tran_type'] = $this->session->userdata('tran_type') ? $this->session->userdata('tran_type') : '';
		$data['application_label'] = $this->session->userdata('application_label') ? $this->session->userdata('application_label') : '';
		$data['masked_account'] = $this->session->userdata('masked_account') ? $this->session->userdata('masked_account') : '';
		$this->sale_lib->clear_cc_info();
		$this->load->view("sales/receipt_decline",$data);
	}
	
	function complete()
	{
		$this->load->helper('sale');
		///Make sure we have actually processed a transaction before compelting sale
		if (is_sale_integrated_cc_processing() && !$this->session->userdata('CC_SUCCESS'))
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
		
		$data['is_sale'] = TRUE;
		$data['cart']=$this->sale_lib->get_cart();
		
		if (empty($data['cart']))
		{
			redirect('sales');
		}
			
		if (!$this->_payments_cover_total())
		{
			$this->_reload(array('error' => lang('sales_cannot_complete_sale_as_payments_do_not_cover_total')), false);
			return;
		}
		$tier_id = $this->sale_lib->get_selected_tier_id();
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($this->Employee->get_logged_in_employee_current_register_id());
		
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();		
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$data['comment'] = $this->sale_lib->get_comment();
		$data['show_comment_on_receipt'] = $this->sale_lib->get_comment_on_receipt();
		$emp_info=$this->Employee->get_info($employee_id);
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$data['amount_change']=$this->sale_lib->get_amount_due() * -1;
		$data['balance']=$this->sale_lib->get_payment_amount(lang('common_store_account'));
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $employee_id ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$data['ref_no'] = '';
		$data['auth_code'] = '';
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		$data['temp_service_id'] = $this->sale_lib->get_temp_service();
		$data['sale_status_id'] = $this->sale_lib->get_sale_status();
		$data['supporter_list'] = $this->sale_lib->get_supporter_list();
		
		
		$masked_account = $this->session->userdata('masked_account') ? $this->session->userdata('masked_account') : '';
		$card_issuer = $this->session->userdata('card_issuer') ? $this->session->userdata('card_issuer') : '';
		$auth_code = $this->session->userdata('auth_code') ? $this->session->userdata('auth_code') : '';
		$ref_no = $this->session->userdata('ref_no') ? $this->session->userdata('ref_no') : '';
		$cc_token = $this->session->userdata('cc_token') ? $this->session->userdata('cc_token') : '';
		$acq_ref_data = $this->session->userdata('acq_ref_data') ? $this->session->userdata('acq_ref_data') : '';
		$process_data = $this->session->userdata('process_data') ? $this->session->userdata('process_data') : '';
		$entry_method = $this->session->userdata('entry_method') ? $this->session->userdata('entry_method') : '';
		$aid = $this->session->userdata('aid') ? $this->session->userdata('aid') : '';
		$tvr = $this->session->userdata('tvr') ? $this->session->userdata('tvr') : '';
		$iad = $this->session->userdata('iad') ? $this->session->userdata('iad') : '';
		$tsi = $this->session->userdata('tsi') ? $this->session->userdata('tsi') : '';
		$arc = $this->session->userdata('arc') ? $this->session->userdata('arc') : '';
		$cvm = $this->session->userdata('cvm') ? $this->session->userdata('cvm') : '';
		$tran_type = $this->session->userdata('tran_type') ? $this->session->userdata('tran_type') : '';
		$application_label = $this->session->userdata('application_label') ? $this->session->userdata('application_label') : '';
				
		if ($masked_account)
		{
			if (count($this->sale_lib->get_payment_ids(lang('common_credit'))))
			{
				$cc_payment_id = current($this->sale_lib->get_payment_ids(lang('common_credit')));
				$cc_payment = $data['payments'][$cc_payment_id];
				$this->sale_lib->edit_payment($cc_payment_id, $cc_payment['payment_type'], $cc_payment['payment_amount'],$cc_payment['payment_date'], $masked_account, $card_issuer,$auth_code, $ref_no, $cc_token, $acq_ref_data, $process_data, $entry_method, $aid,$tvr,$iad, $tsi,$arc,$cvm,$tran_type,$application_label);
			
				//Make sure our payments has the latest change to masked_account
				$data['payments'] = $this->sale_lib->get_payments();
			}
		}
		
		$data['change_sale_date'] =$this->sale_lib->get_change_sale_date_enable() ?  $this->sale_lib->get_change_sale_date() : false;
		
		$old_date = $this->sale_lib->get_change_sale_id()  ? $this->Sale->get_info($this->sale_lib->get_change_sale_id())->row_array() : false;
		$old_date=  $old_date ? date(get_date_format().' '.get_time_format(), strtotime($old_date['sale_time'])) : date(get_date_format().' '.get_time_format());
		$data['transaction_time']= $this->sale_lib->get_change_sale_date_enable() ?  date(get_date_format().' '.get_time_format(), strtotime($this->sale_lib->get_change_sale_date())) : $old_date;
	
		
		$suspended_change_sale_id=$this->sale_lib->get_suspended_sale_id() ? $this->sale_lib->get_suspended_sale_id() : $this->sale_lib->get_change_sale_id() ;
				
		//If we have a suspended sale, update the date for the sale
		if ($this->sale_lib->get_suspended_sale_id() && $this->config->item('change_sale_date_when_completing_suspended_sale'))
		{
			$data['change_sale_date'] = date('Y-m-d H:i:s');
		}
		
		$data['store_account_payment'] = $this->sale_lib->get_mode() == 'store_account_payment' ? 1 : 0;
		$data['add_more_customer_to_service'] = null;
		$data['itemContainsLine'] = [];
		$data['extraData'] = array();
		//SAVE sale to database
		// echo $suspended_change_sale_id; die();
		$sale_id_raw = $this->Sale->save($data['cart'], 
		                                $customer_id, 
		                                $employee_id, 
		                                $sold_by_employee_id, 
		                                $data['comment'],
		                                $data['more_comment'],
		                                $data['show_comment_on_receipt'],
                            		    $data['payments'], 
		                                $data['add_more_customer_to_service'], 
		                                $data['itemContainsLine'],
                            		    $suspended_change_sale_id, 
                            		    0, 
                            		    $data['change_sale_date'], 
                            		    $data['balance'],                    
                            		    $data['store_account_payment'], 
		                                $data['extraData'],                        
                            		    data['temp_service_id'], 
                            		    null, 
                            		    0, 
                            		    0, 
                            		    0, 
                            		    null, 
                            		    data['sale_status_id']);
		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id_raw;
		$data['sale_id_raw']=$sale_id_raw;
		
		
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;			
			$data['customer_points'] = $cust_info->points;			
		   $data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
			
		}
		
		$this->Register_cart->add_data(array('can_email' => !$this->sale_lib->get_email_receipt(), 'sale_id' => $sale_id_raw),$this->Employee->get_logged_in_employee_current_register_id());		
		
		
		if($customer_id != -1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}
		
		//If we don't have any taxes, run a check for items so we don't show the price including tax on receipt
		if (empty($data['taxes']))
		{
			foreach(array_keys($data['cart']) as $key)
			{
				if (isset($data['cart'][$key]['item_id']))
				{
					$item_info = $this->Item->get_info($data['cart'][$key]['item_id']);
					if($item_info->tax_included)
					{
						$this->load->helper('items');
						$price_to_use = get_price_for_item_excluding_taxes($data['cart'][$key]['item_id'], $data['cart'][$key]['price']);
						$data['cart'][$key]['price'] = $price_to_use;
					}					
				}
				elseif (isset($data['cart'][$key]['item_kit_id']))
				{
					$item_info = $this->Item_kit->get_info($data['cart'][$key]['item_kit_id']);
					if($item_info->tax_included)
					{
						$price_to_use = get_price_for_item_kit_excluding_taxes($data['cart'][$key]['item_kit_id'], $data['cart'][$key]['price']);
						$data['cart'][$key]['price'] = $price_to_use;
					}					
				}
				
			}
			
		}
		
		if ($data['sale_id'] == $this->config->item('sale_prefix').' -1')
		{
			$data['error_message'] = '';
			$this->load->helper('sale');
			if (is_sale_integrated_cc_processing())
			{
				$this->sale_lib->change_credit_card_payments_to_partial();
				$data['error_message'].='<span class="text-success">'.lang('sales_credit_card_transaction_completed_successfully').'. </span><br /<br />';
			}
			$data['error_message'] .= '<span class="text-danger">'.lang('sales_transaction_failed').'</span>';
			$data['error_message'] .= '<br /><br />'.anchor('sales','&laquo; '.lang('sales_register'));
			$data['error_message'] .= '<br /><br />'.anchor('sales/complete',lang('common_try_again'). ' &raquo;');
		}
		else
		{			
			if ($this->sale_lib->get_email_receipt() && !empty($cust_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';				
				$this->email->initialize($config);
				$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
				$this->email->to($cust_info->email); 

				$this->email->subject(lang('sales_receipt'));
				$this->email->message($this->load->view("sales/receipt_email",$data, true));	
				$this->email->send();
			}
			
			if ($this->session->userdata('CC_SUCCESS'))
			{
				$credit_card_processor = $this->_get_cc_processor();
		
				if ($credit_card_processor)
				{
					$cc_processor_class_name = strtoupper(get_class($credit_card_processor));
			
					if ($cc_processor_class_name =='MERCURYEMVUSBPROCESSOR')
					{
						$data['reset_params'] = $credit_card_processor->get_emv_pad_reset_params();
					}
				}		
			}
		}
				
		$this->load->view("sales/receipt",$data);
		
		if ($data['sale_id'] != $this->config->item('sale_prefix').' -1')
		{
			$this->sale_lib->clear_all();
		}
	}
	
	function email_receipt($sale_id)
	{
		//Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
		$this->sale_lib->save_current_sale_state();
		
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id, true);
		$data['cart']=$this->sale_lib->get_cart();
		$data['deleted'] = $sale_info['deleted'];
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$tier_id = $sale_info['tier_id'];
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
		$data['subtotal']=$this->sale_lib->get_subtotal($sale_id);
		$data['taxes']=$this->sale_lib->get_taxes($sale_id);
		$data['total']=$this->sale_lib->get_total($sale_id);
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['comment'] = $sale_info['comment'];
		$data['show_comment_on_receipt'] = $sale_info['show_comment_on_receipt'];
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$data['override_location_id'] = $sale_info['location_id'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		$customer_id=$this->sale_lib->get_customer();
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$sold_by_employee_id=$sale_info['sold_by_employee_id'];
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=$this->sale_lib->get_amount_due_round($sale_id) * -1;
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$data['auth_code'] = $sale_info['auth_code'];
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
			$data['customer_points'] = $cust_info->points;
		   $data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
						
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}
				
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
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
		
		if (!empty($cust_info->email))
		{
			$this->load->library('email');
			$config['mailtype'] = 'html';				
			$this->email->initialize($config);
			$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
			$this->email->to($cust_info->email); 

			$this->email->subject($sale_info['suspended'] == 2 ? lang('common_estimate') : lang('sales_receipt'));
			$this->email->message($this->load->view("sales/receipt_email",$data, true));	
			$this->email->send();
		}

		$this->sale_lib->clear_all();
		
		//Restore previous state saved above
		$this->sale_lib->restore_current_sale_state();
	}
	
	function receipt_validate()
	{
		$sale_id = $this->input->post('sale_id');
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		if(!$sale_info)
		{
			echo json_encode(array('success'=>false,'message'=>lang('sales_sale_id_not_found')));
			die();
		}
		else
		{
			echo json_encode(array('success'=>true));
			die();
		}
	}
	
	function receipt($sale_id)
	{
		//Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
		$this->sale_lib->save_current_sale_state();
		
		$data['is_sale'] = FALSE;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id, true);
		$data['cart']=$this->sale_lib->get_cart();
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$data['show_payment_times'] = TRUE;
		$data['signature_file_id'] = $sale_info['signature_image_id'];
		
		$tier_id = $sale_info['tier_id'];
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
		$data['override_location_id'] = $sale_info['location_id'];
		$data['deleted'] = $sale_info['deleted'];

		$data['subtotal']=$this->sale_lib->get_subtotal($sale_id);
		$data['taxes']=$this->sale_lib->get_taxes($sale_id);
		$data['total']=$this->sale_lib->get_total($sale_id);
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id=$this->sale_lib->get_customer();
		
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$sold_by_employee_id=$sale_info['sold_by_employee_id'];
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=$this->sale_lib->get_amount_due($sale_id) * -1;
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$data['auth_code'] = $sale_info['auth_code'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
			$data['customer_points'] = $cust_info->points;
		   $data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
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
		
		$this->load->view("sales/receipt",$data);
		$this->sale_lib->clear_all();
		
		//Restore previous state saved above
		$this->sale_lib->restore_current_sale_state();
	}
	
	function fulfillment($sale_id)
	{
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$data['override_location_id'] = $sale_info['location_id'];
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id=$sale_info['customer_id'];
		
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
		if($customer_id)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
		}		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		$data['sale_id_raw']=$sale_id;
		$data['sales_items'] = $this->Sale->get_sale_items_ordered_by_category($sale_id)->result_array();
		$data['sales_item_kits'] = $this->Sale->get_sale_item_kits_ordered_by_category($sale_id)->result_array();
		$data['discount_exists'] = $this->_does_discount_exists($data['sales_items']) || $this->_does_discount_exists($data['sales_item_kits']);		
		$this->load->view("sales/fulfillment",$data);
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
	
	function edit($sale_id)
	{
		if(!$this->Employee->has_module_action_permission('sales', 'edit_sale', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		$data = array();

		$data['sale_info'] = $this->Sale->get_info($sale_id)->row_array();
				
		if ($data['sale_info']['customer_id'])
		{
			$customer = $this->Customer->get_info($data['sale_info']['customer_id']);			
			$data['selected_customer_name'] = $customer->first_name . ' '. $customer->last_name;
			$data['selected_customer_email'] = $customer->email;
		}
		else
		{
			$data['selected_customer_name'] = lang('common_none');
		}
		
		$data['employees'] = array();
		foreach ($this->Employee->get_all()->result() as $employee)
		{
			$data['employees'][$employee->person_id] = $employee->first_name . ' '. $employee->last_name;
		}

		
		$data['store_account_payment'] = FALSE;
		
		foreach($this->Sale->get_sale_items($sale_id)->result_array() as $row)
		{
			$item_info = $this->Item->get_info($row['item_id']);
			
			if ($item_info->name == lang('sales_store_account_payment'))
			{
				$data['store_account_payment'] = TRUE;
				break;
			}
		}
		
		$data['store_account_charge'] = $this->Sale->get_store_account_payment_total($sale_id) > 0 ? true : false;

		$this->load->view('sales/edit', $data);
	}
	
	function delete_sale_only($sale_id)
	{
		$this->check_action_permission('delete_sale');
		if ($this->Sale->delete($sale_id))
		{			
			echo json_encode(array('success'=>true,'message'=>lang('sales_successfully_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>true,'message'=>lang('sales_unsuccessfully_deleted')));
		}
	}
	
	function delete($sale_id)
	{
		$this->check_action_permission('delete_sale');
		
		if (!$this->input->post('do_delete'))
		{
			$this->load->view('sales/delete', array('success' => false));
			return;
		}
		
		$data = array();
				
		$can_delete = TRUE;
		
		if ($this->input->post('sales_void_and_refund_credit_card') || $this->input->post('sales_void_and_cancel_return'))
		{		
			$credit_card_processor = $this->_get_cc_processor();
			
			if ($credit_card_processor)
			{
				$cc_processor_class_name = strtoupper(get_class($credit_card_processor));
				
				if ($cc_processor_class_name == 'MERCURYHOSTEDCHECKOUTPROCESSOR' || $cc_processor_class_name=='STRIPEPROCESSOR' || $cc_processor_class_name=='BRAINTREEPROCESSOR')
				{
					if ($this->input->post('sales_void_and_refund_credit_card'))
					{
						$can_delete = $credit_card_processor->void_sale($sale_id);
					}
					elseif($this->input->post('sales_void_and_cancel_return'))
					{
						$can_delete = $credit_card_processor->void_return($sale_id);
					}
					
					if ($can_delete && $this->Sale->delete($sale_id))
					{			
						$data['success'] = true;
						$data['sale_id'] = $sale_id;
					}
					else
					{
						$data['success'] = false;
					}
		
					$this->load->view('sales/delete', $data);
				}
				elseif($cc_processor_class_name == 'MERCURYEMVUSBPROCESSOR')
				{					
					if ($this->input->post('sales_void_and_refund_credit_card'))
					{
						$credit_card_processor->void_sale($sale_id);
					}
					elseif($this->input->post('sales_void_and_cancel_return'))
					{
						$credit_card_processor->void_return($sale_id);
					}	
				}
			}
		}
		else
		{
			if ($this->Sale->delete($sale_id))
			{			
				$data['success'] = true;
				$data['sale_id'] = $sale_id;
			}
			else
			{
				$data['success'] = false;
			}

			$this->load->view('sales/delete', $data);
		}
	}
	
	function undelete($sale_id)
	{
		if (!$this->input->post('do_undelete'))
		{
			$this->load->view('sales/undelete', array('success' => false));
			return;
		}
		$data = array();
		
		if ($this->Sale->undelete($sale_id))
		{
			$data['success'] = true;
		}
		else
		{
			$data['success'] = false;
		}
		
		$this->load->view('sales/undelete', $data);
		
	}
	
	function save($sale_id)
	{
		$sale_data = array(
			'sale_time' => date('Y-m-d H:i:s', strtotime($this->input->post('date'))),
			'customer_id' => $this->input->post('customer_id') ? $this->input->post('customer_id') : null,
			'employee_id' => $this->input->post('employee_id'),
			'comment' => $this->input->post('comment'),
			'show_comment_on_receipt' => $this->input->post('show_comment_on_receipt') ? 1 : 0
		);
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
        
        $this->Sale->updateStoreAccount(array('date' =>date('Y-m-d H:i:s', strtotime($this->input->post('date')))),$sale_id);
		if ($this->Sale->update($sale_data, $sale_id)) {            
            $this->db->where("sale_id", $sale_id);

            $data['payment_date']  = 	date('Y-m-d H:i:s', strtotime($this->input->post('date')));

            $this->db->update('sales_payments',$data);
            $this->db->flush_cache();
			echo json_encode(array('success'=>true,'message'=>lang('sales_successfully_updated')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('sales_unsuccessfully_updated')));
		}
	}
	
	function _payments_cover_total()
	{
		$total_payments = 0;

		foreach($this->sale_lib->get_payments() as $payment)
		{
			if(!empty($payment['payment_amount']))
			$total_payments += $payment['payment_amount'];
		}

		/* Changed the conditional to account for floating point rounding */
		if ( ( $this->sale_lib->get_mode() == 'sale' || $this->sale_lib->get_mode() == 'store_account_payment' ) && ( ( to_currency_no_money( $this->sale_lib->get_total() ) - $total_payments ) > 1e-6 ) )
		{
			return false;
		}
		
		return true;
	}
	
	function redeem_discount()
	{
		$customer_id = $this->sale_lib->get_customer();
		
		if ($customer_id != -1)
		{
			$cust_info = $this->Customer->get_info($customer_id);
		   $sales_until_discount = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
			
			if ($sales_until_discount <= 0)
			{
				$discount_all_percent = $this->config->item('discount_percent_earned');
				$this->sale_lib->set_redeem('1');
				$this->sale_lib->discount_all($discount_all_percent);
				
	 	 	  foreach(array_keys($this->sale_lib->get_cart()) as $line)
	 	 	  {
	 	 	  	  if ($this->sale_lib->below_cost_price_item($line))
	 	 	  	  { 
	 	 			  if ($this->config->item('do_not_allow_below_cost'))
	 	 			  {
	 	 				  $this->sale_lib->discount_all(0);
		  				  $this->sale_lib->set_redeem('0');
	 	 				  $data['error'] = lang('sales_selling_item_below_cost');
	 	 			  }
	 	 			  else
	 	 			  {
	 	 				  $data['warning'] = lang('sales_selling_item_below_cost');
	 	 			  }
	 	 			  $this->_reload($data);
	 	 			  return;
	 	 		  }
	 	 	  }
				
			}
		}
		$this->_reload();
	}
	
	function unredeem_discount()
	{
		$this->sale_lib->set_redeem('0');
		$this->sale_lib->discount_all(0);
		$this->_reload();
	}
	
	
	function reload()
	{
		$this->_reload();
	}
	
		
	function customer_recent_sales($customer_id)
	{
		$data['customer'] = $this->Customer->get_info($customer_id)->first_name.' '.$this->Customer->get_info($customer_id)->last_name;
		$data['recent_sales'] = $this->Sale->get_recent_sales_for_customer($customer_id);
		$this->load->view("sales/customer_recent_sales", $data);
	}


    function cancel_sale()
    {
		 if ($this->Location->get_info_for_key('enable_credit_card_processing'))
		 {
	 		$credit_card_processor = $this->_get_cc_processor();
			 
			 if (method_exists($credit_card_processor, 'void_partial_transactions'))
			 {
				 if (!$credit_card_processor->void_partial_transactions())
				 {
		     		 $this->sale_lib->clear_all();
					 $this->_reload(array('error' => lang('sales_attempted_to_reverse_transactions_failed_please_contact_support')), true);
					 return;
				 }
			}
		 }
		
     	$this->sale_lib->clear_all();
     	$this->_reload();
	}
	function suspend($suspend_type = 1)
	{
		$data['cart']=$this->sale_lib->get_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['transaction_time']= date(get_date_format().' '.get_time_format());
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$comment = $this->sale_lib->get_comment();
		$show_comment_on_receipt = $this->sale_lib->get_comment_on_receipt();
		$emp_info=$this->Employee->get_info($employee_id);
		//Alain Multiple payments
		$data['payments']=$this->sale_lib->get_payments();
		$data['amount_change']=$this->sale_lib->get_amount_due() * -1;
		$data['balance']=$this->sale_lib->get_payment_amount(lang('common_store_account'));
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
		}

		$total_payments = 0;

		foreach($data['payments'] as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}
		// echo "<pre>"; print_r($suspend_type); die();
		$sale_id = $this->sale_lib->get_suspended_sale_id();
		//SAVE sale to database
		$sale_id = $this->Sale->save($data['cart'], $customer_id,$employee_id, $sold_by_employee_id, $comment,$show_comment_on_receipt,$data['payments'], $sale_id, $suspend_type,$this->config->item('change_sale_date_when_suspending') ? date('Y-m-d H:i:s') : FALSE, $data['balance']);
		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		if ($data['sale_id'] == $this->config->item('sale_prefix').' -1')
		{
			$this->_reload(array('error' => lang('sales_transaction_failed')));
			return;
		}
		$this->sale_lib->clear_all();
		
		if ($this->config->item('show_receipt_after_suspending_sale'))
		{
			redirect('sales/receipt/'.$sale_id);
		}
		else
		{
			$this->_reload(array('success' => lang('sales_successfully_suspended_sale')));
		}
	}
	
	
	function batch_sale()
	{
		$this->load->view("sales/batch");
	}
	
	function _excel_get_header_row()
	{
		return array(lang('common_item_id').'/'.lang('common_item_number').'/'.lang('common_product_id'),lang('common_unit_price'),lang('common_quantity'),lang('common_discount_percent'));
	}
	
	function excel()
	{	
		$this->load->helper('report');
		$header_row = $this->_excel_get_header_row();
		$this->load->helper('spreadsheet');
		$content = array_to_spreadsheet(array($header_row));
		$this->load->helper('download');
		force_download('batch_sale_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
	}
	
	
	function do_excel_import()
	{
		$this->load->helper('demo');
		if (is_on_demo_host())
		{
			$msg = lang('common_excel_import_disabled_on_demo');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}

		$file_info = pathinfo($_FILES['file_path']['name']);
		if($file_info['extension'] != 'xlsx')
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
			return;
		}
		
		set_time_limit(0);
		//$this->check_action_permission('add_update');
		$this->db->trans_start();
		
		$msg = 'do_excel_import';
		$failCodes = array();
		
		if ($_FILES['file_path']['error']!=UPLOAD_ERR_OK)
		{
			$msg = lang('common_excel_import_failed');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}
		else
		{
			if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE)
			{
				$this->load->helper('spreadsheet');
				$objPHPExcel = file_to_obj_php_excel($_FILES['file_path']['tmp_name']);
				$sheet = $objPHPExcel->getActiveSheet();
				$num_rows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
				
				//Loop through rows, skip header row
				for($k = 2;$k<=$num_rows; $k++)
				{
					
					$item_id = $sheet->getCellByColumnAndRow(0, $k)->getValue();
					if (!$item_id)
					{
						continue;
					}
					
					$price = $sheet->getCellByColumnAndRow(1, $k)->getValue();
					if (!$price)
					{
						$price = null;
					}
				
					$quantity = $sheet->getCellByColumnAndRow(2, $k)->getValue();
					if (!$quantity)
					{
						continue;
					}

					$discount = $sheet->getCellByColumnAndRow(3, $k)->getValue();
					if (!$discount)
					{
						$discount = 0;
					}
					
					if($this->sale_lib->is_valid_item_kit($item_id))
					{
						if(!$this->sale_lib->add_item_kit($item_id,$quantity,$discount,$price))
						{
							$this->sale_lib->empty_cart();
							echo json_encode( array('success'=>false,'message'=>lang('batch_sales_error')));
							return;
						}
					}
					elseif(!$this->sale_lib->add_item($item_id,$quantity,$discount,$price))
					{	
						$this->sale_lib->empty_cart();
						echo json_encode( array('success'=>false,'message'=>lang('batch_sales_error')));
						return;
					}
				}
			}
			else 
			{
				echo json_encode( array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
				return;
			}
		}
		$this->db->trans_complete();
		echo json_encode(array('success'=>true,'message'=>lang('sales_import_successfull')));
		
	}
	
	
	function new_giftcard()
	{
		if (!$this->Employee->has_module_action_permission('giftcards', 'add_update', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		$data = array();
		$data['item_id']=$this->Item->get_item_id(lang('common_giftcard'));
		$this->load->view("sales/giftcard_form",$data);
	}
	
	function suspended()
	{
		$data = array();
		$data['suspended_sales'] = $this->Sale->get_all_suspended();
		$this->load->view('sales/suspended', $data);
	}
	
	function change_sale($sale_id)
	{
		$this->check_action_permission('edit_sale');
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id);
		$this->sale_lib->set_change_sale_id($sale_id);
		
		if ($this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			$this->sale_lib->change_credit_card_payments_to_partial();				
		}
    	$this->_reload(array(), false);
	}
		
	function unsuspend($sale_id = 0)
	{
		// echo "<pre>"; print_r($this->session->all_userdata()); die();
        // Check View Permission
        $_SESSION['edit_sale'] =1;
        $permissions = (new MY_System_Info())->get_permissions('contracts');
        $edit_permissions = ['edit_sale', 'add_update'];
        $view_permissions = ['view_scope_all', 'view_scope_location'];
        $redirect = true;
        foreach ($view_permissions as $permission) {
            if (in_array($permission, $permissions)) {
                $data['action'] = 'view';
                $redirect = false;
            }
        }
        foreach ($edit_permissions as $permission) {
            if (in_array($permission, $permissions)) {
                $data['action'] = 'edit';
                $redirect = false;
            }
        }

		// Get Related Project ID
		$this->load->model('Task');
		$project_id = $this->Task->get_task_id_by_sale_id($sale_id);

        // Check Assigned Permission
        $assigned_permission = (new MY_System_Info())->get_task_assigned_permission($project_id);
        if (!empty($assigned_permission)) {
            // Check assigned view permission
            if (intval($assigned_permission->is_xem) == 1) {
                $data['action'] = 'view';
            }
            // Check assigned edit permission
            if (intval($assigned_permission->is_implement) == 1 || intval($assigned_permission->is_join) == 1 || intval($assigned_permission->is_pheduyet) == 1) {
                $data['action'] = 'edit';
            }
            $redirect = false;
        }

        // Check Spec Groups (Group 1, 2)
        // Get Group ID
        $is_super_group = (new MY_System_Info())->is_super_group();
        if ($is_super_group) {
            $data['action'] = 'edit';
            $redirect = false;
        }

        // Check Basic Edit Permission If Not Assigning
        if ($redirect) {
            $this->check_action_permission('edit_sale');
        }
		$sale_id = $this->input->post('suspended_sale_id') ? $this->input->post('suspended_sale_id') : $sale_id;
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id);
		$this->sale_lib->set_suspended_sale_id($sale_id);
		
		
		if ($this->sale_lib->get_customer())
		{
			$customer_info=$this->Customer->get_info($this->sale_lib->get_customer());
	
			if ($customer_info->tier_id)
			{
				$this->sale_lib->set_selected_tier_id($customer_info->tier_id);
			}
		}

    	$this->_reload($data, false);
	}
	
	function delete_suspended_sale()
	{
		$this->check_action_permission('delete_suspended_sale');
		$suspended_sale_id = $this->input->post('suspended_sale_id');
		if ($suspended_sale_id)
		{
			$this->sale_lib->delete_suspended_sale_id();
			$this->Sale->delete($suspended_sale_id, true, false);
		}
    	redirect('sales/suspended');
	}
	
	function discount_all()
	{
        $post = $this->input->post();
        $post['value'] = convert_number($post['value']);
        $_POST['value'] = convert_number($_POST['value']);

        $this->input->post = $post;

		$discount_all_percent = (float)$this->input->post('discount_all_percent');

		if($this->input->post('name')=="discount_all_percent")
		{
			$discount_all_percent = (float)$this->input->post('value');
			$this->sale_lib->discount_all($discount_all_percent);
			
	 	  foreach(array_keys($this->sale_lib->get_cart()) as $line)
	 	  {
	 	  	  if ($this->sale_lib->below_cost_price_item($line))
	 	  	  { 
	 			  if ($this->config->item('do_not_allow_below_cost'))
	 			  {
	 				  $this->sale_lib->discount_all(0);
	 				  $data['error'] = lang('sales_selling_item_below_cost');
	 			  }
	 			  else
	 			  {
	 				  $data['warning'] = lang('sales_selling_item_below_cost');
	 			  }
	 			  $this->_reload($data);
	 			  return;
	 		  }
	 	  }
			
		}
		elseif ($this->input->post('name') == 'discount_all_flat')
		{
			$discount_amount = strpos($this->input->post('value'), '%',0) !== FALSE ? (($this->sale_lib->get_total() + $this->sale_lib->get_discount_all_fixed()) * ((float)$this->input->post('value')/100)) : (float)$this->input->post('value');
			$this->sale_lib->delete_item($this->sale_lib->get_line_for_flat_discount_item());
            		
			$item_id = $this->Item->create_or_update_flat_discount_item();

			$description =  strpos($this->input->post('value'), '%',0) ? lang('sales_discount_percent').': '.$this->input->post('value') : '';
			$this->sale_lib->add_item($item_id,-1,0,to_currency_no_money($discount_amount),0,$description);
		}
		
		
		$this->_reload();
	}
	
	function categories_and_items($category_id = NULL, $offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		//If a false value, make sure it is NULL
		if (!$category_id)
		{
			$category_id = NULL;
		}
		
		//Categories
		$categories = $this->Category->get_all($category_id);
		$categories_count = count($categories);		
		$config['base_url'] = site_url('sales/categories_and_items/'.($category_id ? $category_id : 0));
		$config['uri_segment'] = 4;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		
		$categories_and_items_response = array();
		
		foreach($categories as $id=>$value)
		{
			$categories_and_items_response[] = array('id' => $id, 'name' => $value['name'], 'type' => 'category');
		}
		
		//Items
		$items = array();
		
		$items_offset = ($offset - $categories_count > 0 ? $offset - $categories_count : 0);		
		$items_result = $this->Item->get_all_by_category($category_id, $this->config->item('hide_out_of_stock_grid') ? TRUE : FALSE, $items_offset, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14)->result();
		
		foreach($items_result as $item)
		{
			$img_src = "";
			if ($item->image_id != 'no_image' && trim($item->image_id) != '') {
				$img_src = site_url('app_files/view/'.$item->image_id);
			}
			
			$size = $item->size ? ' - '.$item->size : '';
			
			if (strpos($item->item_id, 'KIT') === 0)
			{
				$price_to_use = $this->sale_lib->get_price_for_item_kit(str_replace('KIT','',$item->item_id));
			}
			else
			{
				$price_to_use = $this->sale_lib->get_price_for_item($item->item_id);
			}
			
			$categories_and_items_response[] = array(
				'id' => $item->item_id,
				'name' => character_limiter($item->name, 58).$size,				
				'image_src' => 	$img_src,
				'type' => 'item',		
				'price' => $price_to_use != '0.00' ? to_currency($price_to_use) : FALSE,
				'regular_price' => to_currency($item->unit_price),	
				'different_price' => $price_to_use != $item->unit_price,	
			);	
		}
	
		$items_count = $this->Item->count_all_by_category($category_id);		
		$categories_and_items_response = array_slice($categories_and_items_response, $offset > $categories_count ? $categories_count : $offset, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14);
		
		$data = array();
		$data['categories_and_items'] = $categories_and_items_response;
		$config['total_rows'] = $categories_count + $items_count;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);
	}
	
	function categories($parent_id = NULL, $offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		//If a false value, make sure it is NULL
		if (!$parent_id)
		{
				$parent_id = NULL;
		}
		$categories = $this->Category->get_all($parent_id,FALSE, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14, $offset);
		
		$categories_count = $this->Category->count_all($parent_id);		
		$config['base_url'] = site_url('sales/categories/'.($parent_id ? $parent_id : 0));
		$config['uri_segment'] = 4;
		$config['total_rows'] = $categories_count;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$categories_response = array();
		
		foreach($categories as $id=>$value)
		{
				$categories_response[] = array('id' => $id, 'name' => $value['name']);
		}
		

		$data = array();
		$data['categories'] = $categories_response;
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);	
	}
	
	function tags($offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		$tags = $this->Tag->get_all($this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14, $offset);
		
		$tags_count = $this->Tag->count_all();		
		$config['base_url'] = site_url('sales/tags');
		$config['uri_segment'] = 3;
		$config['total_rows'] = $tags_count;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tags_response = array();
		
		foreach($tags as $id=>$value)
		{
				$tags_response[] = array('id' => $id, 'name' => $value['name']);
		}
		

		$data = array();
		$data['tags'] = $tags_response;
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);	
	}
	
	
	function tag_items($tag_id, $offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		$config['base_url'] = site_url('sales/tag_items/'.($tag_id ? $tag_id : 0));
		$config['uri_segment'] = 4;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		
				
		//Items
		$items = array();
		
		$items_result = $this->Item->get_all_by_tag($tag_id,$this->config->item('hide_out_of_stock_grid') ? TRUE : FALSE, $offset, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14)->result();
		
		
		foreach($items_result as $item)
		{
			$img_src = "";
			if ($item->image_id != 'no_image' && trim($item->image_id) != '') {
				$img_src = site_url('app_files/view/'.$item->image_id);
			}

			if (strpos($item->item_id, 'KIT') === 0)
			{
				$price_to_use = $this->sale_lib->get_price_for_item_kit(str_replace('KIT','',$item->item_id));
			}
			else
			{
				$price_to_use = $this->sale_lib->get_price_for_item($item->item_id);
			}

			$items[] = array(
				'id' => $item->item_id,
				'name' => character_limiter($item->name, 58),				
				'image_src' => 	$img_src,
				'type' => 'item',		
				'price' => $price_to_use != '0.00' ? to_currency($price_to_use) : FALSE,
				'regular_price' => to_currency($item->unit_price),	
				'different_price' => $price_to_use != $item->unit_price,	
			);	
		}
	
		$items_count = $this->Item->count_all_by_tag($tag_id);		
		
		$data = array();
		$data['items'] = $items;
		$config['total_rows'] = $items_count;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);
	}
	
		
	function delete_tax($name)
	{
		$this->check_action_permission('delete_taxes');
		$name = rawurldecode($name);
		$this->sale_lib->add_deleted_tax($name);
		$this->sale_lib->update_register_cart_data();
		$this->_reload();
	}

	function view_receipt_modal()
	{
		$this->load->view('sales/lookup_modal');
	}
	
	function sig_save()
	{
		$this->load->model('Appfile');
		$sale_id = $this->input->post('sale_id');
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		
		//If we have a signature delete it
		if ($sale_info['signature_image_id'])
		{
			$this->Sale->update(array('signature_image_id' => NULL), $sale_id);
			$this->Appfile->delete($sale_info['signature_image_id']);
		}
		
		$image = base64_decode($this->input->post('image'));
    	$image_file_id = $this->Appfile->save('signature_'.$sale_id.'.png', $image);
		$this->Sale->update(array('signature_image_id' => $image_file_id), $sale_id);
		
		echo json_encode(array('file_id' => $image_file_id));
	}
	
	function customer_display($register_id = false)
	{
		if (!$register_id)
		{
			$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		}
		
		if ($this->Register->exists($register_id))
		{
			
			$this->load->view('sales/customer_display_initial', array('register_id' => $register_id,'fullscreen_customer_display'=> $this->session->userdata('fullscreen_customer_display')));
		}
	}
	
	function customer_display_update($register_id = false)
	{
		if (!$register_id)
		{
			$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		}
		
		if ($this->Register->exists($register_id))
		{
			$data = $this->Register_cart->get_data($register_id);
			$data['mode'] = "sale";
			
			if (isset($data['sale_id']))
			{
				$sale_info = $this->Sale->get_info($data['sale_id'])->row_array();
				$customer_id = $sale_info['customer_id'];
				
				if($customer_id)
				{
					$cust_info=$this->Customer->get_info($customer_id);
					$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
					$data['customer_email'] = $cust_info->email;			
				}		
				
				
			}
		
			$data['fullscreen_customer_display'] = $this->session->userdata('fullscreen_customer_display');
			
			$this->load->view("sales/customer_display",$data);
		}
	}
	
	function open_drawer()
	{
		$this->load->view('sales/open_drawer');
	}
	
	function disable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','0');
		}
		
		redirect(site_url('sales'));	
	}
	
	function enable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','1');
		}
		redirect(site_url('sales'));	
	}
	function calculatedPrice(){ 

		$post_customers = $this->input->post('post_customers');
		// var_dump($post_customers);die();
		$line = $this->input->post('line');
		$data = $this->sale_lib->get_cart();
		foreach ($data as $key => $value) {
			if($key==$line)
				$data[$line]['calculatedPrice'] = $post_customers;
		}
		$this->sale_lib->set_cart($data);
    }
	
}
?>
