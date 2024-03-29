<?php
require_once ("Secure_area.php");
class Receivings extends Secure_area
{
	  
public $_scopeOfView = 'view_scope_owner';      
	function __construct()
	{	
		parent::__construct('receivings');
		$this->load->library('receiving_lib');
		$this->lang->load('receivings');
		$this->lang->load('module');
		$this->load->helper('items');
		$this->load->model('Receiving');
		$this->load->model('Supplier');
		$this->load->model('Category');
		$this->load->model('Tag');
		$this->load->model('Item');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_kit_taxes_finder');
        $this->load->model('Employee');
		cache_item_and_item_kit_cart_info($this->receiving_lib->get_cart());

		 $this->_scopeOfView = 'view_scope_owner';
        if ($this->Employee->has_module_action_permission(
            'receivings',
            'view_scope_location',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_location';
        } 

        if($this->Employee->has_module_action_permission(
            'receivings',
            'view_scope_all',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_all';
        }


	}

	function index($test = 0)
	{
		
	
		// var_dump($_SESSION['cartRecv']);
		// var_dump($_SESSION['recv_mode']);recv_payments
		// $this->sale_lib->clear_all();
		$this->_reload(array(), false);
	}
	
	function item_search_n9() {
		session_write_close();
		$suggestions = $this->Item->get_item_search_n9(trim($this->input->get('term')),100);
		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),100));
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

	function supplier_search()
	{
		//allow parallel searchs to improve performance.

		$suggestions = $this->Supplier->goi_y_ben_thu_ba($this->input->get('term'));
		// echo $this->db->last_query();die();
		// session_write_close();
		// $suggestions = $this->Supplier->get_supplier_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}

	function select_supplier()
	{
		$data = array();
		$supplier_id = $this->input->post("supplier");
		
		if ($this->Supplier->account_number_exists($supplier_id))
		{
			$supplier_id = $this->Supplier->supplier_id_from_account_number($supplier_id);
		}
		
		if ($this->Supplier->exists($supplier_id))
		{
			$this->receiving_lib->set_supplier($supplier_id);
		}
		else
		{
			$data['error']=lang('receivings_unable_to_add_supplier');
		}
		$this->_reload($data);
	}

	function location_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Location->get_locations_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}

	function select_location()
	{
		$data = array();
		$location_id = $this->input->post("location");
		
		if ($this->Location->exists($location_id))
		{
			$this->receiving_lib->set_location($location_id);
		}
		else
		{
			$data['error']=lang('receivings_unable_to_add_location');
		}
		$this->_reload($data);
	}
	
	function set_change_receiving_date() 
	{
 	  $this->receiving_lib->set_change_receiving_date($this->input->post('change_receiving_date'));
	}
	
	function set_email_receipt()
	{
 	  $this->receiving_lib->set_email_receipt($this->input->post('email_receipt'));
	}
	
	
	function set_change_receiving_date_enable() 
	{
 	  $this->receiving_lib->set_change_receiving_date_enable($this->input->post('change_receiving_date_enable'));
	  if (!$this->receiving_lib->get_change_receiving_date())
	  {
	 	  $this->receiving_lib->set_change_receiving_date(date(get_date_format()));
	  }
	}
	

	function delete_location()
	{
		$this->receiving_lib->delete_location();
		$this->_reload();
	}

	function change_mode()
	{
		$mode = $this->input->post("mode");
        $this->receiving_lib->clear_all();
		$this->receiving_lib->set_mode($mode);

        if($mode == 'store_account_payment') {
            $store_account_payment_item_id = $this->Item->create_or_update_store_account_item();
            $this->receiving_lib->add_item($store_account_payment_item_id);
            $this->receiving_lib->set_store_account_payment_value(1);

        }

		$this->_reload();
	}

	function set_selected_payment()
	{
		$supplier_id = $this->receiving_lib->get_supplier();
		
		if($supplier_id <= 0 && $this->input->post('payment') == 'Sổ ghi nợ')
		{
			echo 0; exit;
		}
		$this->receiving_lib->set_selected_payment($this->input->post('payment'));
		echo 1; exit;
	}
	
	function set_comment() 
	{
 	  $this->receiving_lib->set_comment($this->input->post('comment'));
	}
	
	function add_n9() {
		$data=array();
		$mode = $this->receiving_lib->get_mode();
		$item_id_or_number = $this->input->post("item");
		$info = $this->Item->getInformation(array('item_id_or_number'=>$item_id_or_number));
		$item_id_or_number_or_item_kit_or_receipt = $info['item_id'];


		//$quantity = $mode=="receive" || $mode=="purchase_order" ? 1:-1;
        $quantity = 1;

        $config_price_imported = $this->config->item('config_price_imported');
        if($config_price_imported == 'unit_price')
            $price_value = $info['cost_price_interval'];
        else
            $price_value = NULL;

        // var_dump($info);die();
		if($this->receiving_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return')
		{
			$this->receiving_lib->return_entire_receiving($item_id_or_number_or_item_kit_or_receipt);
			
		}
		elseif($this->receiving_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			if($this->Item_kit->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item_kit->get_info($this->Item_kit->get_item_kit_id($item_id_or_number_or_item_kit_or_receipt))->deleted)
			{
				$data['error']=lang('receivings_unable_to_add_item');
			}
			else
			{
				$this->receiving_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt);
			}
		}

		elseif(
			$info['deleted'] || 
			!$this->receiving_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity, NULL, 0, $price_value)
			)
		{
			$data['error']=lang('receivings_unable_to_add_item');
		}
		// echo "<pre>";
		// var_dump($this->session->userdata('cartRecv'));die();
		$this->_reload($data);
	}

	function add()
	{
		$data=array();
		$mode = $this->receiving_lib->get_mode();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post("item");
		$quantity = $mode=="receive" || $mode=="purchase_order" ? 1:1;

		if($this->receiving_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return')
		{
			$this->receiving_lib->return_entire_receiving($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif($this->receiving_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			if($this->Item_kit->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item_kit->get_info($this->Item_kit->get_item_kit_id($item_id_or_number_or_item_kit_or_receipt))->deleted)
			{
				$data['error']=lang('receivings_unable_to_add_item');			
			}
			else
			{
				$this->receiving_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt);
			}
		}
		elseif($this->Item->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item->get_info($this->Item->get_item_id($item_id_or_number_or_item_kit_or_receipt))->deleted || !$this->receiving_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity))
		{
			$data['error']=lang('receivings_unable_to_add_item');
		}
		
		$this->_reload($data);
	}
		
	function delete_tax($name)
	{
		$this->check_action_permission('delete_taxes');
		$name = rawurldecode($name);
		$this->receiving_lib->add_deleted_tax($name);
		$this->_reload();
	}
	
	

	function edit_item($line)
	{
		$data= array();

		$this->form_validation->set_rules('price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('quantity', 'lang:common_quantity', 'numeric');
		$this->form_validation->set_rules('quantity_received', 'lang:receivings_qty_received', 'numeric');
		$this->form_validation->set_rules('discount', 'lang:common_discount_percent', 'numeric');

    	$description = NULL;
    	$serialnumber = NULL;
		$price = NULL;
		$quantity = NULL;
		$discount = NULL;
		$expire_date = NULL;
		$quantity_received = NULL;
		
		if($this->input->post("name"))
		{
			$variable = $this->input->post("name");
			$$variable = $this->input->post("value");
		}

		if ($discount !== NULL && $discount == '')
		{
			$discount = 0;
		}

		if ($quantity !==NULL && $quantity == '')
		{
			$quantity = 0;
		}
		
		if ($quantity_received !== NULL && $quantity_received == '')
		{
			$quantity_received = 0;
		}
		
		if ($this->form_validation->run() != FALSE)
		{
			$this->receiving_lib->edit_item($line,$description,$serialnumber,$expire_date,$quantity,$quantity_received,$discount,$price);
		}
		else
		{
			$data['error']=lang('receivings_error_editing_item');
		}

		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->receiving_lib->delete_item($item_number);
		
		if (count($this->receiving_lib->get_cart()) == 0)
		{
			$this->receiving_lib->clear_all();
		}
		if(count($this->receiving_lib->get_cart()) == 1)
		{
			foreach($this->receiving_lib->get_cart() as $cart)
			{
				if($cart['name']== lang('common_discount'))
				{
					$this->receiving_lib->clear_all();
					break;
				}
			}
		
		}
		
		$this->_reload();
	}

	function delete_supplier()
	{
		$this->receiving_lib->delete_supplier();
		$this->_reload();
	}



	function delete_task()
    {
        $this->receiving_lib->delete_task();
        $this->_reload();
    }

	function complete()
	{
		$data['cart']=$this->receiving_lib->get_cart();
		if (empty($data['cart']))
		{
			redirect('receivings');
		}		
		$data['taxes']=$this->receiving_lib->get_taxes();		
		$data['subtotal']=$this->receiving_lib->get_subtotal();		
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=lang('receivings_receipt');
		$supplier_id=$this->receiving_lib->get_supplier();
		$location_id=$this->receiving_lib->get_location();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment') ? $this->input->post('comment') : '';
		$data['comment'] = $comment;
		$emp_info=$this->Employee->get_info($employee_id);
		$payment_type = $this->input->post('payment_type');
		$data['payment_type']=$this->input->post('payment_type');
		$data['mode']=$this->receiving_lib->get_mode();
		$data['change_receiving_date'] =$this->receiving_lib->get_change_receiving_date_enable() ?  $this->receiving_lib->get_change_receiving_date() : false;
		$old_date = $this->receiving_lib->get_change_recv_id()  ? $this->Receiving->get_info($this->receiving_lib->get_change_recv_id())->row_array() : false;
		$old_date=  $old_date ? date(get_date_format().' '.get_time_format(), strtotime($old_date['receiving_time'])) : date(get_date_format().' '.get_time_format());
		$data['transaction_time']= $this->receiving_lib->get_change_receiving_date_enable() ?  date(get_date_format().' '.get_time_format(), strtotime($this->receiving_lib->get_change_receiving_date())) : $old_date;
		$data['suspended']  = 0;
		$data['is_po'] = 0;
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		

		if ($this->input->post('amount_tendered'))
		{
			$data['amount_tendered'] = $this->input->post('amount_tendered');
			$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
			
			$data['amount_change'] = to_currency($data['amount_tendered'] - round($data['total'], $decimals));
		}
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{	
			$suppl_info=$this->Supplier->get_info($supplier_id);		
			$data['supplier']=$suppl_info->company_name;
			if ($suppl_info->first_name || $suppl_info->last_name)
			{
				$data['supplier'] .= ' ('.$suppl_info->first_name.' '.$suppl_info->last_name.')';
			}
			
			$data['supplier_address_1'] = $suppl_info->address_1;
			$data['supplier_address_2'] = $suppl_info->address_2;
			$data['supplier_city'] = $suppl_info->city;
			$data['supplier_state'] = $suppl_info->state;
			$data['supplier_zip'] = $suppl_info->zip;
			$data['supplier_country'] = $suppl_info->country;
			$data['supplier_phone'] = $suppl_info->phone_number;
			$data['supplier_email'] = $suppl_info->email;
			
		}
		
		if ($this->config->item('charge_tax_on_recv'))
		{
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
				}
			}
		}

		$suspended_change_recv_id=$this->receiving_lib->get_suspended_receiving_id() ? $this->receiving_lib->get_suspended_receiving_id() : $this->receiving_lib->get_change_recv_id();

		//SAVE receiving to database		
		$receiving_id_raw = $this->Receiving->save($data, $supplier_id,$employee_id,$comment,$payment_type,$suspended_change_recv_id,0,$data['mode'], $data['change_receiving_date'],0, $location_id);
		$data['receiving_id']='RECV '.$receiving_id_raw;
		$data['receiving_id_raw']=$receiving_id_raw;
		
		if ($data['receiving_id'] == 'RECV -1')
		{
			$data['error_message'] = '';
			$data['error_message'] .= '<span class="text-danger">'.lang('receivings_transaction_failed').'</span>';
			$data['error_message'] .= '<br /><br />'.anchor('receivings','&laquo; '.lang('receivings_register'));			
			$data['error_message'] .= '<br /><br />'.anchor('receivings/complete',lang('common_try_again'). ' &raquo;');
		}
		else
		{
			if ($this->receiving_lib->get_email_receipt() && !empty($suppl_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';				
				$this->email->initialize($config);
				$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
				$this->email->to($suppl_info->email); 

				$this->email->subject(lang('receivings_receipt'));
				$this->email->message($this->load->view("receivings/receipt_email",$data, true));	
				$this->email->send();
		
			}
		}
		
		$current_location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$current_location = $this->Location->get_info($current_location_id);
		$data['transfer_from_location'] = $current_location->name;
		
		if ($location_id > 0)
		{
			$transfer_to_location = $this->Location->get_info($location_id);
			$data['transfer_to_location'] = $transfer_to_location->name;
		}

		$this->load->view("receivings/receipt",$data);
		if ($data['receiving_id'] != 'RECV -1')
		{
			$this->receiving_lib->clear_all();
		}
	}
	
	function email_receipt($receiving_id)
	{
		//Before changing the recv session data, we need to save our current state in case they were in the middle of a recv
		$this->receiving_lib->save_current_recv_state();
		
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();		
		$this->receiving_lib->copy_entire_receiving($receiving_id, TRUE);
		$data['cart']=$this->receiving_lib->get_cart();
		$data['subtotal']=$this->receiving_lib->get_subtotal($receiving_id);
		$data['taxes']=$this->receiving_lib->get_taxes($receiving_id);
		$data['total']=$this->receiving_lib->get_total($receiving_id);
		$data['receipt_title']=lang('receivings_receipt');
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($receiving_info['receiving_time']));
		$supplier_id=$this->receiving_lib->get_supplier();
		$emp_info=$this->Employee->get_info($receiving_info['employee_id']);
		$data['payment_type']=$receiving_info['payment_type'];
		$data['override_location_id'] = $receiving_info['location_id'];
		$data['suspended'] = $receiving_info['suspended'];
		$data['comment'] = $receiving_info['comment'];
		$data['is_po'] = $receiving_info['is_po'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{
			$supplier_info=$this->Supplier->get_info($supplier_id);
						
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
		$data['receiving_id']='RECV '.$receiving_id;
		$data['receiving_id_raw']=$receiving_id;
		
		$current_location = $this->Location->get_info($receiving_info['location_id']);
		$data['transfer_from_location'] = $current_location->name;
		
		if ($receiving_info['transfer_to_location_id'] > 0)
		{
			$transfer_to_location = $this->Location->get_info($receiving_info['transfer_to_location_id']);
			$data['transfer_to_location'] = $transfer_to_location->name;
		}
		
		
		if (!empty($supplier_info->email))
		{
			$this->load->library('email');
			$config['mailtype'] = 'html';				
			$this->email->initialize($config);
			$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
			$this->email->to($supplier_info->email); 

			$this->email->subject($receiving_info['is_po'] ? lang('receivings_purchase_order') : lang('receivings_receipt'));
			$this->email->message($this->load->view("receivings/receipt_email",$data, true));	
			$this->email->send();
		}
		
		$this->receiving_lib->clear_all();
		
		//Restore previous state saved above
		$this->receiving_lib->restore_current_recv_state();
	}
	
	function suspend()
	{
		$data['cart']=$this->receiving_lib->get_cart();		
		$data['subtotal']=$this->receiving_lib->get_subtotal();
		$data['taxes']=$this->receiving_lib->get_taxes();
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=lang('receivings_receipt');
		$data['transaction_time']= date(get_date_format().' '.get_time_format());
		$supplier_id=$this->receiving_lib->get_supplier();
		$location_id=$this->receiving_lib->get_location();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->receiving_lib->get_comment();
		$emp_info=$this->Employee->get_info($employee_id);
		$data['payment_type'] = $payment_type = $this->receiving_lib->get_selected_payment();
		$data['payments'] = $this->receiving_lib->get_payments();
		$data['mode']=$this->receiving_lib->get_mode();
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
		$data['change_receiving_date'] =$this->receiving_lib->get_change_receiving_date_enable() ?  $this->receiving_lib->get_change_receiving_date() : false;
		$is_po =  $this->receiving_lib->get_po();
		$data['is_po'] = $is_po;

        $suppl_info = NULL;
		if($supplier_id!=-1)
		{	
			$suppl_info=$this->Supplier->get_info($supplier_id);		
			$data['supplier']=$suppl_info->company_name;
			$data['supplier_id'] = $supplier_id;
			if ($suppl_info->first_name || $suppl_info->last_name)
			{
				$data['supplier'] .= ' ('.$suppl_info->first_name.' '.$suppl_info->last_name.')';
			}
			
			$data['supplier_address_1'] = $suppl_info->address_1;
			$data['supplier_address_2'] = $suppl_info->address_2;
			$data['supplier_city'] = $suppl_info->city;
			$data['supplier_state'] = $suppl_info->state;
			$data['supplier_zip'] = $suppl_info->zip;
			$data['supplier_country'] = $suppl_info->country;
			$data['supplier_phone'] = $suppl_info->phone_number;
			$data['supplier_email'] = $suppl_info->email;
		}

		//SAVE receiving to database
		$receiving_id_raw =$this->Receiving->save($data, $supplier_id,$employee_id,$comment,$payment_type,$this->receiving_lib->get_suspended_receiving_id(), 1, $data['mode'],$data['change_receiving_date'], $is_po ? 1 : 0, $location_id, 0, 0, 0,$suppl_info);
		$data['receiving_id']='RECV '.$receiving_id_raw;
		$data['receiving_id_raw']=$receiving_id_raw;
		
		if ($data['receiving_id'] == 'RECV -1')
		{
			$this->_reload(array('error' => lang('receivings_transaction_failed')));
			return;
		}
		
		if ($this->config->item('show_receipt_after_suspending_sale') || $is_po)
		{
			//Email receipt if is PO
			if ($is_po && $this->receiving_lib->get_email_receipt() && !empty($suppl_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';				
				$this->email->initialize($config);
				$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.4biz.vn', $this->config->item('company'));
				$this->email->to($suppl_info->email); 

				$this->email->subject(lang('receivings_purchase_order'));
				$this->email->message($this->load->view("receivings/receipt_email",$data, true));	
				$this->email->send();
			}
			$this->receiving_lib->clear_all();
			redirect('receivings/receipt/'.$receiving_id_raw);
		}
		else
		{
			$this->receiving_lib->clear_all();
			$this->_reload(array('success' => lang('receivings_successfully_suspended_receiving')));
		}
		
	}
	
	function suspended()
	{
		$data = array();
		$data['suspended_receivings'] = $this->Receiving->get_all_suspended();
		$this->load->view('receivings/suspended', $data);
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
						$price = null;;
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
					
					if($this->receiving_lib->is_valid_item_kit($item_id))
					{
						if(!$this->receiving_lib->add_item_kit($item_id))
						{
							$this->receiving_lib->empty_cart();
							echo json_encode( array('success'=>false,'message'=>lang('batch_receivings_error')));
							return;
						}
					}
					elseif(!$this->receiving_lib->add_item($item_id,$quantity,NULL,$discount,$price))
					{	
						$this->receiving_lib->empty_cart();
						echo json_encode( array('success'=>false,'message'=>lang('batch_receivings_error')));
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
		echo json_encode(array('success'=>true,'message'=>lang('receivings_import_successfull')));
		
	}
	
	function _excel_get_header_row()
	{
		return array(lang('common_item_id').'/'.lang('common_item_number').'/'.lang('common_product_id'),lang('cost_price'),lang('quantity'),lang('discount_percent'));
	}
	
	function batch_receiving()
	{
		
		$this->load->view('receivings/batch');
	}
	
	function excel()
	{	
		$this->load->helper('report');
		$header_row = $this->_excel_get_header_row();
		$this->load->helper('spreadsheet');
		$content = array_to_spreadsheet(array($header_row));
		$this->load->helper('download');
		force_download('batch_receiving_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), $content);
	}
	
	function unsuspend($recv_id = 0)
	{
		$oldSuspendedReceivingId = $this->receiving_lib->get_suspended_receiving_id();
		$receiving_id = $this->input->post('suspended_receiving_id') ? $this->input->post('suspended_receiving_id') : $recv_id;
		
		if ($oldSuspendedReceivingId != $receiving_id)
		{
		
			$this->receiving_lib->clear_all();
			$this->receiving_lib->copy_entire_receiving($receiving_id);
			$this->receiving_lib->set_suspended_receiving_id($receiving_id);		
		}
		$this->_reload(array(), false);
	}
	
	function delete_suspended_receiving()
	{
		$this->check_action_permission('delete_receiving');
		
		$suspended_recv_id = $this->input->post('suspended_receiving_id');
		if ($suspended_recv_id)
		{
			$this->receiving_lib->delete_suspended_receiving_id();
			$this->Receiving->delete($suspended_recv_id, false);
		}
    	redirect('receivings/suspended');
	}
	
	function change_recv($receiving_id)
	{
		$this->check_action_permission('edit_receiving');
		$this->receiving_lib->clear_all();
		$this->receiving_lib->copy_entire_receiving($receiving_id);
		$this->receiving_lib->set_change_recv_id($receiving_id);		
    	$this->_reload(array(), false);
	}

	function receipt($receiving_id)
	{
		//Before changing the recv session data, we need to save our current state in case they were in the middle of a recv
		$this->receiving_lib->save_current_recv_state();
		
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();		
		$this->receiving_lib->copy_entire_receiving($receiving_id, TRUE);
		$data['cart']=$this->receiving_lib->get_cart();
		$data['subtotal']=$this->receiving_lib->get_subtotal($receiving_id);
		$data['taxes']=$this->receiving_lib->get_taxes($receiving_id);
		$data['total']=$this->receiving_lib->get_total($receiving_id);
		$data['receipt_title']=lang('receivings_receipt');
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($receiving_info['receiving_time']));
		$supplier_id=$this->receiving_lib->get_supplier();
		$emp_info=$this->Employee->get_info($receiving_info['employee_id']);
		$data['payment_type']=$receiving_info['payment_type'];
		$data['override_location_id'] = $receiving_info['location_id'];
		$data['suspended'] = $receiving_info['suspended'];
		$data['comment'] = $receiving_info['comment'];
		$data['is_po'] = $receiving_info['is_po'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{
			$supplier_info=$this->Supplier->get_info($supplier_id);
						
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
		$data['receiving_id']='RECV '.$receiving_id;
		$data['receiving_id_raw']=$receiving_id;
		
		$current_location = $this->Location->get_info($receiving_info['location_id']);
		$data['transfer_from_location'] = $current_location->name;
		
		if ($receiving_info['transfer_to_location_id'] > 0)
		{
			$transfer_to_location = $this->Location->get_info($receiving_info['transfer_to_location_id']);
			$data['transfer_to_location'] = $transfer_to_location->name;
		}
		
		$this->load->view("receivings/receipt",$data);
		$this->receiving_lib->clear_all();
		
		//Restore previous state saved above
		$this->receiving_lib->restore_current_recv_state();
		
	}
	
	function edit($receiving_id)
	{
		if(!$this->Employee->has_module_action_permission('receivings', 'edit_receiving', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		$data = array();

		$data['suppliers'] = array('' => lang('receivings_no_supplier'));
		foreach ($this->Supplier->get_all()->result() as $supplier)
		{
			$data['suppliers'][$supplier->person_id] = $supplier->company_name.' ('.$supplier->first_name . ' '. $supplier->last_name.')';
		}

		$data['employees'] = array();
		foreach ($this->Employee->get_all()->result() as $employee)
		{
			$data['employees'][$employee->person_id] = $employee->first_name . ' '. $employee->last_name;
		}

		$data['receiving_info'] = $this->Receiving->get_info($receiving_id)->row_array();
		$this->load->view('receivings/edit', $data);
	}

	function edit_modal($receiving_id)
	{
		if(!$this->Employee->has_module_action_permission('receivings', 'edit_receiving', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		$data = array();

		$data['suppliers'] = array('' => lang('receivings_no_supplier'));
		foreach ($this->Supplier->get_all()->result() as $supplier)
		{
			$data['suppliers'][$supplier->person_id] = $supplier->company_name.' ('.$supplier->first_name . ' '. $supplier->last_name.')';
		}

		$data['employees'] = array();
		foreach ($this->Employee->get_all()->result() as $employee)
		{
			$data['employees'][$employee->person_id] = $employee->first_name . ' '. $employee->last_name;
		}

		$data['receiving_info'] = $this->Receiving->get_info($receiving_id)->row_array();
		$edit_modal_html = $this->load->view('receivings/modal_nhap_hang', $data,TRUE);
		echo json_encode(array('html'=>$edit_modal_html));
	}
	
	function delete($receiving_id)
	{
		$this->check_action_permission('delete_receiving');
		
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		$data = array();
		if($receiving_info['deleted'] == 0)
		{
		if ($this->Receiving->delete($receiving_id, false))
		{
			$data['success'] = true;
		}
		else
		{
			$data['success'] = false;
		}
		}

		
		// $this->load->view('receivings/delete', $data);
		
	}
	
	function undelete($receiving_id)
	{
		$data = array();
		$receiving_deleted = $this->Receiving->get_info($receiving_id)->row_array()['deleted'];
		
		if($receiving_deleted == 1)
		{
			if($this->Receiving->undelete($receiving_id))
				{
			$data['success'] = true;
		}
		else
		{
			$data['success'] = false;
		}
		
		}
		else
		{
			$data['success'] = true;
		}
		$this->load->view('receivings/undelete', $data);
		
	
		
	}
	
	function save($receiving_id)
	{ 
		$receiving_data = array(
			'receiving_time' => date('Y-m-d H:i:s', strtotime($this->input->post('date'))),
			'supplier_id' => $this->input->post('supplier_id') ? $this->input->post('supplier_id') : null,
			'employee_id' => $this->input->post('employee_id'),
			'comment' => $this->input->post('comment'),
			'no_dau' => $this->input->post('no_dau'),
			'no_cuoi' => $this->input->post('no_cuoi')
		);
		
		if ($this->Receiving->update($receiving_data, $receiving_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('receivings_successfully_updated')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('receivings_unsuccessfully_updated')));
		}
	}

	function _reload($data=array(), $is_ajax = true)
	{		
die();
		$person_info = $this->Employee->get_logged_in_employee_info();
		
		$data['cart']=$this->receiving_lib->get_cart();
		$data['modes']=array('receive'=>lang('receivings_receiving'),'return'=>lang('receivings_return'),'purchase_order'=>lang('receivings_purchase_order'));
		$data['comment'] = $this->receiving_lib->get_comment();
		if ($this->Location->count_all() > 1)
		{
			$data['modes']['transfer']= lang('receivings_transfer');
		}
		$data['mode']=$this->receiving_lib->get_mode();
		$data['selected_payment'] = $this->receiving_lib->get_selected_payment();
		$data['subtotal']=$this->receiving_lib->get_subtotal();
		$data['taxes']=$this->receiving_lib->get_taxes();
		$data['total']=$this->receiving_lib->get_total();
		$data['items_in_cart'] = $this->receiving_lib->get_items_in_cart();
		$data['change_recv_date_enable'] = $this->receiving_lib->get_change_receiving_date_enable();
		$data['change_receiving_date'] = $this->receiving_lib->get_change_receiving_date();
		$data['email_receipt'] = $this->receiving_lib->get_email_receipt();
		
		$data['items_module_allowed'] = $this->Employee->has_module_permission('items', $person_info->person_id);
		$data['payment_options']=array(
			lang('common_cash') => lang('common_cash'),
			lang('common_check') => lang('common_check'),
			lang('common_debit') => lang('common_debit'),
			lang('common_credit') => lang('common_credit')
		);
		$data['fullscreen'] = $this->session->userdata('fullscreen');
		
		foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
		{
			$data['payment_options'][$additional_payment_type] = $additional_payment_type;
		}
		
		$deleted_payment_types = $this->config->item('deleted_payment_types');
		$deleted_payment_types = explode(',',$deleted_payment_types);
		
		foreach($deleted_payment_types as $deleted_payment_type)
		{
			foreach($data['payment_options'] as $payment_option)
			{
				if ($payment_option == $deleted_payment_type)
				{
					unset($data['payment_options'][$payment_option]);
				}
			}
		}
		
		$supplier_id=$this->receiving_lib->get_supplier();
		if($supplier_id!=-1)
		{
			$info=$this->Supplier->get_info($supplier_id);
			$data['supplier']=$info->company_name;
			if ($info->first_name || $info->last_name)
			{
				$data['supplier'] .= ' ('.$info->first_name.' '.$info->last_name.')';
			}
			
			$data['supplier_email']=$info->email;
			$data['avatar']=$info->image_id ?  site_url('app_files/view/'.$info->image_id) : base_url()."assets/img/user.png";
			
			
			$data['supplier_id']=$supplier_id;
		}

		$location_id=$this->receiving_lib->get_location();
		if($location_id!=-1)
		{
			$info=$this->Location->get_info($location_id);
			$data['location']=$info->name;
			$data['location_id']=$location_id;
		}
		
		$data['is_po'] = $this->receiving_lib->get_po();

		if ($is_ajax)
		{
			$this->load->view("receivings/receiving",$data);
		}
		else
		{
			$this->load->view("receivings/receiving_initial",$data);
		}
	}

    function cancel_receiving()
    {
    	$this->receiving_lib->clear_all();
    	$this->_reload();
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
			$categories = $this->Category->get_all($parent_id, FALSE, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14, $offset);
			
			$categories_count = $this->Category->count_all($parent_id);		
			$config['base_url'] = site_url('receivings/categories/'.($parent_id ? $parent_id : 0));
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
			$config['base_url'] = site_url('receivings/tags');
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
		
			$config['base_url'] = site_url('receivings/tag_items/'.($tag_id ? $tag_id : 0));
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
					$price_to_use = FALSE;
				}
				else
				{
					$cur_item_info = $this->Item->get_info($item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item->item_id);
		
					$price_to_use = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
				}
				
				
				$items[] = array(
					'id' => $item->item_id,
					'name' => character_limiter($item->name, 58),				
					'image_src' => 	$img_src,
					'type' => 'item',		
					'price' => $price_to_use !== FALSE ? to_currency($price_to_use) : FALSE,		

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
			$config['base_url'] = site_url('receivings/categories_and_items/'.($category_id ? $category_id : 0));
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
					$price_to_use = FALSE;
				}
				else
				{
					$cur_item_info = $this->Item->get_info($item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item->item_id);
		
					$price_to_use = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
				}

				$categories_and_items_response[] = array(
					'id' => $item->item_id,
					'name' => character_limiter($item->name, 58).$size,				
					'image_src' => 	$img_src,
					'type' => 'item',		
					'price' => $price_to_use !== FALSE ? to_currency($price_to_use) : FALSE,		
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
		
		function po()
		{
			$data = array();
			$suppliers = array();
			foreach($this->Supplier->get_all()->result_array() as $row)
			{
				$suppliers[$row['person_id']] = $row['company_name'] .' ('.$row['first_name'] .' '. $row['last_name'].')';
			}
			$data['suppliers'] = $suppliers;
			$data['selected_supplier'] = $this->receiving_lib->get_supplier();
			
			$data['criterias'] = array(
				'below_reorder_level' => lang('receivings_below_reorder_level'),
				'below_reorder_level_and_out_of_stock' => lang('receivings_below_reorder_level_and_out_of_stock'),
				'sales_past_week' => lang('receivings_sales_in_past_week'),
				'sales_past_month' => lang('receivings_sales_in_past_month'),
				'all_items_for_supplier' => lang('receivings_all_items_for_supplier'),
			);
			$this->load->view("receivings/po",$data);
		}
		
		function create_po()
		{
			$supplier_id = $this->input->post('supplier_id');
			$criteria = $this->input->post('criteria');
			
			$item_ids = array();
			switch($criteria)
			{
				case 'below_reorder_level':
				case 'below_reorder_level_and_out_of_stock':
				
				$this->load->model('reports/Inventory_low');
				$model = $this->Inventory_low;
				$model->setParams(array('supplier'=>$supplier_id,'category_id' => -1, 'export_excel' => 1, 'offset'=>0, 'inventory' => $criteria == 'below_reorder_level' ? 'all' : 'out_of_stock' ,'reorder_only' => true, 'location_id' => $this->Employee->get_logged_in_employee_current_location_id()));
				
				$low_inventory = $report_data = $model->getData();
				
				foreach($low_inventory as $row)
				{
					$item_ids[] = $row['item_id'];
				}
				break;

				case 'sales_past_week': 
				case 'sales_past_month':
				
				$start_date = false;
				$end_date = false;
				
				if ($criteria == 'sales_past_week')
				{
					$start_date = date("Y-m-d",strtotime('-7 days'));
					$end_date = date("Y-m-d  23:59:59");
					
				}
				elseif('sales_past_month')
				{
					$start_date = date("Y-m-d",strtotime('-31 days'));
					$end_date = date("Y-m-d 23:59:59");
				}
				$this->load->model('Sale');
				$item_ids = $this->Sale->get_item_ids_sold_for_date_range($start_date, $end_date, $supplier_id);
				break;
				
				case 'all_items_for_supplier':
					foreach($this->Item->get_all_by_supplier($supplier_id) as $row)
					{
						$item_ids[] = $row['item_id'];
					}
				break;
				
			}
			
			if ($this->input->post('clear_current_items_in_cart'))
			{
				$this->receiving_lib->empty_cart();			
			}
			
			foreach($item_ids as $item_id)
			{
				
				$quantity_to_add= 1;
				
				if ($criteria == 'below_reorder_level' || $criteria == 'below_reorder_level_and_out_of_stock')
				{
		        	$cur_item_location_info = $this->Item_location->get_info($item_id);
		        	$cur_item_info = $this->Item->get_info($item_id);
					$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;
					$quantity_to_add = $reorder_level - $cur_item_location_info->quantity;
				}
				
				$this->receiving_lib->add_item($item_id,max(1,$quantity_to_add));
				
			}
			
			$this->receiving_lib->set_supplier($supplier_id);
			$this->receiving_lib->set_po(TRUE);
			$this->receiving_lib->set_mode('purchase_order');
			redirect('receivings');
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
	
		
}
?>