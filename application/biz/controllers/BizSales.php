<?php
require_once (APPPATH . "controllers/Sales.php");
require_once(BIZ_LIB_PATH . 'simple-html-dom/simple_html_dom.php');
class BizSales extends Sales
{
	protected $_prefixDocument = 'SALE#';
	protected $_paginator = array(
		'per_page' => 10,
		'uri_segment' => 3
	);
	const CATEGORY_VAI_PHU =  175;
	const CATEGORY_VAI_CHINH = 174;
	const CATEGORY_REM_CUON = 179;
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('sale');
		$this->load->helper('filterext');
		$this->load->model('ApproverGroup');
		$this->load->library('PHPWord');
		$this->load->helper('download_helper');
		$this->load->library("form_validation");
		$this->form_validation->set_message('required', '%s '.lang('required'));
		$this->form_validation->set_message('is_unique', '%s không được trùng lặp.');
	}

	public function orders()
	{


		$e = $this->session->userdata('person_id');
		$view ='view_scope_owner';
		if($this->Employee->has_module_action_permission('sales','view_scope_location',$e))
		{
			$view = 'view_scope_location';
		}
		if($this->Employee->has_module_action_permission('sales','view_scope_all',$e))
		{
			$view = 'view_scope_all';
		}

		$data = array();
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$status_id = $this->input->post('status_id');
		$employee_created_id = $this->input->post('employee_created_id');
		$employee_imp_id = $this->input->post('employee_imp_id');
		$customer_name = $this->input->post('customer_name');
		$service_id = $this->input->post('service_id');
		$status_list = $this->Sale_status->get_all(2);
		$employee_list = $this->Employee->get_list_employees_by_location($this->Employee->get_logged_in_employee_current_location_id());
		$service_list = $this->Item->get_all()->result();
		
		$data['from_date'] = $from_date;
		$data['to_date'] = $to_date;
		$data['status_id'] = $status_id;
		$data['status_list'] = $status_list;
		$data['employee_created_id'] = $employee_created_id;
		$data['employee_imp_id'] = $employee_imp_id;
		$data['employee_list'] = $employee_list;
		$data['customer_name'] = $customer_name;
		$data['service_list'] = $service_list;
		$data['service_id'] = $service_id;
		
		$search = array(

			'status_id' => $status_id,
			'employee_id' => $employee_created_id,
			'supporter_id' => $employee_imp_id,
			'customer_name' => $customer_name,
			'item_id' => $service_id,
			'status_type' => 2,
			'view'=>$view
		);
		if(!empty($from_date))
		{
			$search['from_date'] = date("Y/m/d",strtotime($from_date));
			
		}
		if(!empty($to_date))
		{
			$search['to_date'] = date("Y/m/d",strtotime($to_date));
		}
		$data['orders'] = $this->Sale->getOrders($search);
		// echo "<pre>";
		// var_dump($data['orders']);die();
		$this->load->view('sales/orders', $data);
	}
	
	public function set_sale_delivery_date()
	{
		$delivery_date = $this->input->post("delivery_date");
		
		$this->sale_lib->set_delivery_date($delivery_date);
		
		$this->_reload($data);
	}
	
	function deliverer_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Employee->get_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}
	
	function supporter_search()
	{
		//allow parallel searchs to improve performance.

		$suggestions = $this->Employee->goi_y_nhan_vien($this->input->get('term'),100);
		// session_write_close();
		// $suggestions = $this->Employee->get_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}

	function sales_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Sale->get_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}

	function select_deliverer()
	{
		$data = array();
		$deliverer_id = $this->input->post("deliverer");

		if ($this->Employee->exists($deliverer_id))
		{
			$this->sale_lib->set_deliverer($deliverer_id);
		} else
		{
			$data['error']=lang('sales_unable_to_add_customer');
		}
		$this->_reload($data);
	}
	
	function select_supporter()
	{
		$data = array();
		$supporter_id = $this->input->post("supporter");
		if ($this->Employee->exists($supporter_id))
		{
			$this->sale_lib->set_supporter_for_list($supporter_id);
		} else
		{
			$data['error']=lang('sales_unable_to_add_supporter');
		}
		$this->_reload($data);
	}
	
	function change_sale($sale_id)
	{
		$this->check_action_permission('edit_sale');
		$this->sale_lib->clear_all();
		$this->sale_lib->set_change_sale_id($sale_id);
		$this->sale_lib->copy_entire_sale($sale_id);
		if ($this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			$this->sale_lib->change_credit_card_payments_to_partial();
		}
		$this->_reload(array($sale_id), false);
	}


	function edit_item($line)
	{
		$data= array();
		$_POST['value'] = convert_number($_POST['value']);
		$this->form_validation->set_rules('price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('cost_price', 'lang:common_price', 'numeric');
// 		$this->form_validation->set_rules('quantity', 'lang:common_quantity', 'numeric');
// 		$this->form_validation->set_rules('discount', 'lang:common_discount_percent', 'numeric');

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

// 		if($this->sale_lib->is_kit_or_item($line) == 'item')
// 		{
// 			if($this->sale_lib->out_of_stock($this->sale_lib->get_item_id($line)))
// 			{
// 				$data['warning'] = lang('sales_quantity_less_than_zero');
// 			}

// 			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
// 			{
// 				if ($this->config->item('do_not_allow_below_cost'))
// 				{
// 					$can_edit = FALSE;
// 					$data['error'] = lang('sales_selling_item_below_cost');
// 				}
// 				else
// 				{
// 					$data['warning'] = lang('sales_selling_item_below_cost');
// 				}
// 			}
// 		}
// 		elseif($this->sale_lib->is_kit_or_item($line) == 'kit')
// 		{
// 			if($this->sale_lib->out_of_stock_kit($this->sale_lib->get_kit_id($line)))
// 			{
// 				$data['warning'] = lang('sales_quantity_less_than_zero');
// 			}

// 			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
// 			{
// 				if ($this->config->item('do_not_allow_below_cost'))
// 				{
// 					$can_edit = FALSE;
// 					$data['error'] = lang('sales_selling_item_below_cost');
// 				}
// 				else
// 				{
// 					$data['warning'] = lang('sales_selling_item_below_cost');
// 				}
// 			}
// 		} ToiNT

		if ($can_edit)
		{
			$this->sale_lib->edit_item(
				$line,
				isset($description) ? $description : NULL,
				isset($serialnumber) ? $serialnumber : NULL,
				isset($quantity) ? $quantity : NULL,
				isset($discount) ? $discount : NULL,
				isset($price) ? $price: NULL,
				isset($cost_price) ? $cost_price: NULL,
				isset($measure) ? $measure: NULL,
				isset($name) ? $name: NULL,
				isset($priceSelfInput) ? $priceSelfInput: NULL,
				isset($totalSelfInput) ? $totalSelfInput: NULL

			);
		}
		$this->_reload($data);
		
	}
	
	function delete_suspended_sale($sale_id = 0)
	{
		$this->check_action_permission('delete_sale');
		$suspended_sale_id = $this->input->post('suspended_sale_id') ? $this->input->post('suspended_sale_id') : $sale_id;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		if ($suspended_sale_id && empty($sale_info->is_stock_out))
		{
			$this->sale_lib->delete_suspended_sale_id();
			$this->Sale->delete($suspended_sale_id, true, false);
		}
		redirect('sales/suspended');
	}

	function add_employee() {
		$post = $this->input->post();
		if(!empty($post)) {
			$response = $this->sale_lib->set_employee_for_group($post['id'], $post['group_id']);
			echo json_encode($response);
		}
	}

	function delete_employee() {
		$post = $this->input->post();
		if(!empty($post)) {
			$this->sale_lib->delete_employee_from_group($post['id'], $post['group_id']);
		}
	}

	function set_service_id() {
		$post = $this->input->post();
		if(!empty($post)) {
			$service_id = (!empty($post['service_id'])) ? $post['service_id'] : NULL;
			$this->sale_lib->set_service_id($service_id);
		}
	}

	function set_store_account_payment_value() {
		$post = $this->input->post();
		if(!empty($post)) {
			$this->sale_lib->clear_sale_store_payment();
			$this->sale_lib->empty_payments();
			$this->sale_lib->set_store_account_payment_value($post['value']);
		}
	}

	function change_sale_time() {
		$post = $this->input->post();
		if(!empty($post)) {
			$this->sale_lib->set_sale_time_date($post['sale_time_date']);
		}
	}

	function check_before_complete() {
    	// echo "fgfg";
    	// die();
		$sale_status_id = $this->sale_lib->get_sale_status();
		$sale_payment 	= $this->sale_lib->get_payments();
		$sale_mode       = $this->sale_lib->get_mode();
		$debit_payment   = $this->sale_lib->get_debit_payment();
		$customer_id     = $this->sale_lib->get_customer();
		$sale_total      = $this->sale_lib->get_total();
		$payment_total   = $this->sale_lib->get_payments_totals();
		if(!empty($sale_payment) && $sale_payment[0]['is_stock_out'] == 2) {
			$response = array('flag'=>'false', 'msg'=>'Bạn chưa hoàn thành xuất kho dở dang, cần vào lại đơn hàng tạm dừng và hoàn tất việc xuất kho cho đơn hàng');
			echo json_encode($response);
			return;
		}

		if($sale_status_id==0) {
			$response = array('flag'=>'false', 'msg'=>'Bạn phải chọn trạng thái');
			echo json_encode($response);
			return;
		}
		if($sale_mode == 'sale') {
			$change_sale_id = $this->sale_lib->get_change_sale_id();
			if($change_sale_id > 0) {
				$old_debit_payment   = $this->Sale->get_debt_payment_amount_from_sale(array('sale_id'=>$change_sale_id));
				$old_payment_total   = $this->Sale->get_payment_total_from_sale(array('sale_id'=>$change_sale_id));

				if($debit_payment > 0) {
					if($customer_id == -1) {
						$response = array('flag'=>'false', 'msg'=>'Phải chọn khách hàng.');
						echo json_encode($response);
						return;
					}

					$customer_info = $this->Customer->get_info($customer_id);
					$balance_tmp   = $customer_info->balance - $old_debit_payment + $debit_payment;

					if($balance_tmp < 0) {
						$response = array('flag'=>'false', 'msg'=>$this->config->item('customer_balance') . ' : không thể ghi sổ nợ.');
						echo json_encode($response);
						return;
					}

					if($debit_payment > 0 && !empty($customer_info->credit_limit) && $balance_tmp  > $customer_info->credit_limit) {
						$response = array('flag'=>'false', 'msg'=>$this->config->item('customer_balance') . ' đã đặt hạn mức công nợ');
						echo json_encode($response);
						return;
					}
				}

				if($payment_total > $sale_total && $customer_id == -1) {
					$response = array('flag'=>'false', 'msg'=>'Phải chọn khách hàng.');
					echo json_encode($response);
					return;
				}

				$customer_info              = $this->Customer->get_info($customer_id);
				$old_order_info             = $this->sale_lib->get_sale($change_sale_id);
				$old_sale_order_total_value = $old_order_info['gia_tri_don_hang'];

				$subtraction_1 = $payment_total - $sale_total;
				$subtraction_2 = $old_payment_total - $old_sale_order_total_value;

				if($subtraction_1 !=  $subtraction_2) {

					if($payment_total > $sale_total) {


						if($old_payment_total > $old_sale_order_total_value) {

							$transaction_amount = - ($old_payment_total - $old_sale_order_total_value) + ($payment_total - $sale_total);
							$new_balance_2 = $customer_info->balance_2 + $transaction_amount;

						}else {

							$transaction_amount = $payment_total - $sale_total;
							$new_balance_2 = $customer_info->balance_2 + $transaction_amount;
						}

						if($new_balance_2 < 0) {
							$response = array('flag'=>'false', 'msg'=>'Công nợ không được âm.');
							echo json_encode($response);

							return;
						}

					}else {


						if($old_payment_total > $old_sale_order_total_value) {
							$transaction_amount = $old_sale_order_total_value - $old_payment_total;
							$new_balance_2 = $customer_info->balance_2 + $transaction_amount;

						} 
					}
				}
			}
			else {
				if($debit_payment > 0) {
					if($customer_id == -1) {
						$response = array('flag'=>'false', 'msg'=>'Phải chọn khách hàng.');
						echo json_encode($response);
						return;
					}

					$customer_info = $this->Customer->get_info($customer_id);
					if($debit_payment > 0 && !empty($customer_info->credit_limit) && $customer_info->balance + $debit_payment > $customer_info->credit_limit) {
						$response = array('flag'=>'false', 'msg'=>$this->config->item('customer_balance') . ' đã đặt hạn mức công nợ.');
						echo json_encode($response);
						return;
					}
				}

				if($payment_total > $sale_total && $customer_id == -1) {
					$response = array('flag'=>'false', 'msg'=>'Phải chọn khách hàng.');
					echo json_encode($response);
					return;
				}
			}

			if($this->config->item('config_vat_order') == 0 && $payment_total > $sale_total) {
				$response = array('flag'=>'false', 'msg'=>'Tổng tiền thanh toán không được lớn hơn giá trị đơn hàng.');
				echo json_encode($response);
				return;
			}
		}
		elseif($sale_mode == 'return') { 
			$change_sale_id = $this->sale_lib->get_change_sale_id();
			if($change_sale_id > 0) {
				$old_debit_payment   = $this->Sale->get_debt_payment_amount_from_sale(array('sale_id'=>$change_sale_id));

				if($debit_payment > 0) {
					if($customer_id == -1) {
						$response = array('flag'=>'false', 'msg'=>'Phải chọn khách hàng.');
						echo json_encode($response);
						return;
					}

					$customer_info = $this->Customer->get_info($customer_id);

					$balance_2_tmp = $customer_info->balance_2 - $old_debit_payment + $debit_payment;

					if($balance_2_tmp < 0) {
						$response = array('flag'=>'false', 'msg'=>$this->config->item('customer_balance') . ' :không thể ghi sổ nợ.');
						echo json_encode($response);
						return;
					}
				}
			}
			else {
				if($debit_payment > 0) {
					if($customer_id == -1) {
						$response = array('flag'=>'false', 'msg'=>'Phải chọn khách hàng.');
						echo json_encode($response);
						return;
					}

					$customer_info = $this->Customer->get_info($customer_id);
				}
			}

			if($payment_total > $sale_total) {
				$response = array('flag'=>'false', 'msg'=>'Tổng tiền thanh toán không được lớn hơn giá trị đơn hàng.');
				echo json_encode($response);
				return;
			}
		}
		elseif($sale_mode == 'store_account_payment') {
            // check công nợ
			$store_account_payment_amount = $this->sale_lib->get_total();

			$customer_id                 = $this->sale_lib->get_customer();
			$customer_info               = $this->Customer->get_info($customer_id);
			$store_account_payment_value = $this->sale_lib->get_store_account_payment_value();

			if($store_account_payment_value == 1 && $store_account_payment_amount > $customer_info->balance) {
				$response = array('flag'=>'false', 'msg'=>'Số tiền thanh toán công nợ không hợp lệ.');
				echo json_encode($response);
				return;
			}

			if($store_account_payment_value == 2 && $store_account_payment_amount > $customer_info->balance_2) {
				$response = array('flag'=>'false', 'msg'=>'Số tiền thanh toán công nợ không hợp lệ.');
				echo json_encode($response);
				return;
			}

			// thanh toán công nợ cho đơn hàng?
			$sale_store_payment = $this->sale_lib->get_sale_store_payment();
			if(empty($sale_store_payment)) {
				$response = array('flag'=>'true');
				echo json_encode($response);

				return;
			}

           // check xem tổng số tiền chi có bằng số tiền thanh toán ko
			$payment_debt = $this->sale_lib->get_debt_payment();
			$all_amount   = $this->sale_lib->get_all_amount_from_sale_store_payment();

			if($payment_debt != $all_amount) {
				$so_du = $payment_debt - $all_amount;
				$so_du = to_currency($so_du);
				$response = array('flag'=>'false', 'msg'=>'Còn dư ' . $so_du);
				echo json_encode($response);

				return;
			}

           	//check xem có đơn hàng nào k còn ghi nợ hay không?
			$sale_ids = array_keys($sale_store_payment);
			$debt_orders = $this->Sale->get_sale_store_payment_items(array('sale_ids'=>$sale_ids));

			if(count($sale_ids) != count($debt_orders)) {
				$response = array('flag'=>'false', 'msg'=>'1 trong những đơn hàng không còn ghi nợ.');
				echo json_encode($response);

				$this->sale_lib->clear_sale_store_payment();

				return;
			}

           		//check xem từng khoản chi có nhiều hơn tiền còn nợ ở đơn hàng không
			foreach($debt_orders as $item) {
				if($item['amount'] > $item['con_lai']) {
					$response = array('flag'=>'false', 'msg'=>$item['sale_code'] . ': Số tiền thanh toán thêm không được quá số tiền nợ còn lại.');
					echo json_encode($response);

					return;
				}
			}

			if($payment_total > $sale_total) {
				$response = array('flag'=>'false', 'msg'=>'Tổng tiền thanh toán không được lớn hơn giá trị đơn hàng.');
				echo json_encode($response);
				return;
			}
		}
		elseif($sale_mode == 'vat_order') {
			if($customer_id == -1) {
				$response = array('flag'=>'false', 'msg'=>'Phải chọn khách hàng.');
				echo json_encode($response);
				return;
			}

			$customer_info = $this->Customer->get_info($customer_id);

			if($sale_total > $customer_info->balance_2) {
				$response = array('flag'=>'false', 'msg'=>'Tổng giá trị đơn hàng không được lớn hơn công nợ.');
				echo json_encode($response);
				return;
			}

			if($payment_total > $sale_total) {
				$response = array('flag'=>'false', 'msg'=>'Tổng tiền thanh toán không được lớn hơn giá trị đơn hàng.');
				echo json_encode($response);
				return;
			}

			$sale_ids = $this->sale_lib->get_sale_vat_relationship();
			if(!empty($sale_ids)) {
				$flag = $this->Sale->valid_vat_order($sale_ids);
				if($flag == false) {
					$response = array('flag'=>'false', 'msg'=>'Không xử lý được đơn hàng.');
					echo json_encode($response);
					return;
				}
			}

		}

		echo json_encode(array('flag'=>'true'));
	}

	function complete()
	{

		$this->load->helper('sale');

		///Make sure we have actually processed a transaction before compelting sale
// 		if (is_sale_integrated_cc_processing() && !$this->session->userdata('CC_SUCCESS'))
// 		{
// 			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
// 			return;
// 		} // ToiNT
		
		$data['is_sale'] = TRUE;
		$data['cart']=$this->sale_lib->get_cart();

		# Sắp xếp lại mảng theo biến line đã được lưu ở session

		sap_xep_mang($data['cart'],'line',true);
		$data['sale_mode'] = $this->sale_lib->get_mode();
		
		if (empty($data['cart']))
		{
			redirect('sales');
		}

// 		if (!$this->_payments_cover_total())
// 		{
// 			$this->_reload(array('error' => lang('sales_cannot_complete_sale_as_payments_do_not_cover_total')), false);
// 			return;
// 		} //ToiNT

		$tier_id = $this->sale_lib->get_selected_tier_id();
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($this->Employee->get_logged_in_employee_current_register_id());
		
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();		
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$customer_id=$this->sale_lib->get_customer();

		// [4biz] Get customer balance before make other
		$cust_info = NULL;
		if($customer_id != -1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale_before'] = $cust_info->balance;
			}
		}

		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$data['service_id'] = $this->sale_lib->get_service_id();
		$data['comment'] = $this->sale_lib->get_comment();
		$data['show_comment_on_receipt'] = $this->sale_lib->get_comment_on_receipt();
		$emp_info=$this->Employee->get_info($employee_id);
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['add_more_customer_to_service'] = $this->sale_lib->get_customers();	
		$data['itemsContainsLine'] = $this->sale_lib->getItemContainsLine();	
		$data['saleComment']        = $this->sale_lib->getSaleComments();        
		#--------------------------------------------------------------------------------------------------------------
							# Lấy dữ liệu hóa đơn lưu

		$data['payments']=$this->sale_lib->get_payments();

		#--------------------------------------------------------------------------------------------------------------
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
				$data['payments'] = $this->sale_lib->get_payments();
			}
		}
            //		$suspended = $this->Sale->get_all_suspended();
		$data['change_sale_date'] = $this->sale_lib->get_change_sale_date();
		
		$old_date = $this->sale_lib->get_change_sale_id()  ? $this->Sale->get_info($this->sale_lib->get_change_sale_id())->row_array() : false;
		$old_date=  $old_date ? date(get_date_format().' '.get_time_format(), strtotime($old_date['sale_time'])) : date(get_date_format().' '.get_time_format());
		$data['transaction_time']= $this->sale_lib->get_change_sale_date_enable() ?  date(get_date_format().' '.get_time_format(), strtotime($this->sale_lib->get_change_sale_date())) : $old_date;

		$suspended_change_sale_id=$this->sale_lib->get_suspended_sale_id() ? $this->sale_lib->get_suspended_sale_id() : $this->sale_lib->get_change_sale_id() ;

		//If we have a suspended sale, update the date for the sale
		if ($this->sale_lib->get_suspended_sale_id() && $this->config->item('change_sale_date_when_completing_suspended_sale'))
		{
			$data['change_sale_date'] = date("Y-m-d H:i:s");		
		}

		$data['store_account_payment'] = ($sale_mode = $this->sale_lib->get_mode()) == 'store_account_payment' ? 1 : 0;
		
		$extraData['deliverer'] = $this->sale_lib->get_deliverer();
		$extraData['delivery_date'] = $this->sale_lib->get_delivery_date();
		$extraData['supporter'] = $this->sale_lib->get_supporter();

		$employee_groups = $this->sale_lib->get_group_employees();
		if(!empty($employee_groups)) {
			foreach($employee_groups as $group_id => $group) {
				if(!empty($group['list'])) {
					foreach($group['list'] as $item) {
						$tmp = array();
						$tmp['group_id']   = $group_id;
						$tmp['employee_id'] = $item['id'];
						$emp_group_arr[] = $tmp;
					}
				}
			}
		}
		$sale_code = $this->sale_lib->create_sale_code();





		$commission_info = $this->sale_lib->get_commission_info();
		$extraData['commission_time_method'] = $commission_info['commission_time_method'];
		$extraData['commission_method']      = $commission_info['commission_method'];
		$extraData['min_profit']             = $commission_info['min_profit'];
		$extraData['min_profit_commission']  = isset($commission_info['min_profit_commission']) ? $commission_info['min_profit_commission'] : '';

		if($sale_mode == 'assigment') {
			$data['assigment']  = 1;
		}else
		$data['assigment']  = 0;

		if($sale_mode == 'return' || $sale_mode == 'return_by_sales') {
			$data['return']  = 1;
		}else
		$data['return']  = 0;

		if($sale_mode == 'vat_order') {
			$data['is_vat']  = 1;
		}else
		$data['is_vat']  = 0;

		if($sale_mode == 'store_account_payment') {
			$data['store_account_payment'] = $this->sale_lib->get_store_account_payment_value();

			$sale_store_payment   = $this->sale_lib->get_sale_store_payment();
			$extraData['comment'] = $this->sale_lib->get_store_account_paymment_comment();
		}

		# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang# Lưu data banhang
		// var_dump($data);die();
		$sale_id_raw = $this->Sale->save(
			$data['cart'], 
			$customer_id, 
			$employee_id, 
			$sold_by_employee_id, 
			$data['comment'],
			$data['saleComment'],
			$data['show_comment_on_receipt'],
			$data['payments'],
			$data['add_more_customer_to_service'],
			$data['itemsContainsLine'], // for rem ha my only, 
			$suspended_change_sale_id, 
			$suspended = false, # biến suspended
			$data['change_sale_date'], 
			$data['balance'], 
			$data['store_account_payment'], 
			$extraData, 
			$data['temp_service_id'], 
			$sale_code, # Mã hóa đơn visa
			$data['assigment'], 
			$data['return'], 
			$data['is_vat'], 
			$cust_info,
			$data['sale_status_id']
		);

		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id_raw;
		$data['sale_id_raw']=$sale_id_raw;

		// save commission
//         if(!empty($emp_group_arr)) {
//             $sale_employees = array();

//             foreach($emp_group_arr as $val) {
//                 $tmp = array();
//                 $tmp['sale_id']     = $sale_id_raw;
//                 $tmp['employee_id'] = $val['employee_id'];
//                 $tmp['group_id']    = $val['group_id'];

//                 $sale_employees[] = $tmp;
//             }
//         }

		$supporter_list = $data['supporter_list'];

		if (!empty($supporter_list)) {
			$sale_employees = array();
			foreach ($supporter_list as $val) {
				$tmp = array(
					'sale_id' => $sale_id_raw,
					'employee_id' => $val['id'],
					'group_id' => -1
				);
				$sale_employees[] = $tmp;
			}
		}



        if(!isset($_SESSION['change_sale_id']) && !isset($_SESSION['suspended_sale_id'])) { // add + tạm dừng => đơn hàng

        	$this->Sale->delete_sale_employee(array('sale_id'=>$sale_id_raw), array('task'=>'by-sale'));
        	if(!empty($sale_employees)) {

        		$this->db->insert_batch('sales_employees', $sale_employees);
//                 if($sale_mode == 'sale' || empty($sale_mode)){

//                     $this->sale_lib->update_employee_commission($emp_group_arr, $sale_id_raw);
//                 }
        	}
        }
            else { // edit
            	if(isset($sale_employees)){

            		$this->Sale->delete_sale_employee(array('sale_id'=>$sale_id_raw), array('task'=>'by-sale'));
            		$this->db->insert_batch('sales_employees', $sale_employees);
            	}
            	if(isset($emp_group_arr)) {
            		if($sale_mode == 'sale'){
            			if(isset($_SESSION['suspended_sale_id'])) {
            				$this->Sale->delete_sale_commission($sale_id_raw);
            				$this->sale_lib->update_employee_commission($emp_group_arr, $sale_id_raw, true, true);
            			}
            			else {
            				$this->Sale->delete_sale_commission($sale_id_raw);
            				$this->sale_lib->update_employee_commission($emp_group_arr, $sale_id_raw, true);
            			}

            		}
            	}

            }
            
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
            	$data['customer_company_name'] = $cust_info->company_name;	
            	$data['customer_position']     = isset($cust_info->position) ? $cust_info->position:'';
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

            
            
		# Nếu có mã visa rồi thì lấy mã visa cũ
            if(isset($_SESSION['visa_code'])){
            	$data['service_code'] = $_SESSION['visa_code'];
            } else $data['service_code'] = $sale_code;

            if ($data['sale_id'] != $this->config->item('sale_prefix').' -1')
            {
            	$this->sale_lib->clear_all();
            }
            
            if($data['service_id']> 0) {
            	$service_info = $this->Service->get_item(array('id'=>$data['service_id']));
            	$data['sale_document'] =  $service_info['document_list'];
            }
            
            $data_tmp         = $data;
            $data['data_tmp'] = $data_tmp;
            
            if(!empty($data['sale_document'])) {
            	foreach($data['sale_document'] as &$val) {

            		$val['content_quotes_contract'] = $this->convertTemplate($data, $val['content_quotes_contract'], $sale_info);
            	}
            }
            $_SESSION['notice'] = 'Tạo Nhu cầu khách hàng thành công';
            redirect(base_url('sales'),'refresh');
		// $this->load->view("sales/receipt",$data);
        }


        protected function convertTemplate($data, $content_string, $sale_info = null) {
        	$html = str_get_html($content_string);
		# Nếu dữ liệu body bị lỗi thì break = FALSE
		$break = TRUE; # Nếu dữ liệu body bị lỗi thì break = FALSE
		# Nếu dữ liệu body bị lỗi thì break = FALSE

		$tableData  = $html->find('table[class=DATA_TABLE]',0);

		if(!empty($tableData)) {
			$tr_element = $html->find('tr');
            //     var_dump( $tr_element->outertext);
            // die;
			$array_class = explode(' ', $tableData->class);
			$category_ids = array();
			foreach($array_class as $class_item){
				if(is_numeric($class_item))
					$category_ids[] = $class_item;
			}

            # START
            # Phần xử lý dữ liệu body
            # Phần xử lý dữ liệu body
            # Phần xử lý dữ liệu body
            # Phần xử lý dữ liệu body
			if(!empty($tr_element)) {
				$total_money = 0;
				$total_discount = 0;
				foreach($tr_element as $key => $element) {
					$body_table_string = $element->outertext;
					$description = $tr_element[$key+1]->outertext;


					if (strpos($body_table_string, '{STT}') !== false || strpos($body_table_string, '{TEN_HH}') !== false || strpos($body_table_string, '{MA_HH}') !== false ) {

						$new_tr = array();
                            // echo $body_table_string;
						if(!empty($data['cart'])) {
							$i = 1;
							foreach($data['cart'] as $_key => $item) {


								if ($item['name'] != 'Giảm giá') {

									$total_discount = $item['price'] * $item['quantity'];
									$total_discount = NumberFormatToCurrency($total_discount);
									$dg_ck = $item['price'] - $total_discount;

									$pattern = $body_table_string;
									$pattern = str_replace("{STT}",$i,$pattern); 
									$pattern = str_replace("{CHIET_KHAU}",(float)($item['discount']),$pattern);
									$pattern = str_replace("{THUE}",(float)($item['tax_included']),$pattern);
									$pattern = str_replace("{TEN_HH}",$item['name'],$pattern);  

									$pattern = str_replace("{MA_HH}",$item['product_id'],$pattern);
									$pattern = str_replace("{DVT}",$item['measure'],$pattern);
									$pattern = str_replace("{SL}",(float)$item['quantity'],$pattern);
									$pattern = str_replace("{DG-CK}",NumberFormatToCurrency($dg_ck),$pattern);
									$pattern = str_replace("{DON_GIA}",NumberFormatToCurrency(($item['price'])),$pattern);
									$pattern = str_replace("{THANH_TIEN}",NumberFormatToCurrency((float)(abs($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100))),$pattern);
									$description_ ='';
									if(strpos($description, '{MO_TA_HH}') == TRUE){
										if (!empty($item['description'])) {
											$description_ = str_replace("{MO_TA_HH}",$item['description'],$description);
										} else {
											$description_ ='';
										} 
									}

									$new_tr[] = $pattern.$description_;

									$total_money +=($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100);
									$i++;
								} else {

                                        // $total_discount = ($item['price'] * $item['discount']) / 100;
									$total_discount = $item['price'] * $item['quantity'];
									$total_discount = NumberFormatToCurrency($total_discount);

								}
							}
						}

						$new_tr = implode('', $new_tr);
						$element->outertext = $new_tr;
						$tr_element[$key+1]->outertext = '';

                            break; # Nếu k có mô tả sản phẩm
                            
                            
                        } elseif(strpos($body_table_string, '{STT_1}') !== false || strpos($body_table_string, '{TEN_HH_1}') !== false || strpos($body_table_string, '{MA_HH_1}') !== false) {
                        	$new_tr = array();
                        	if(!empty($data['cart'])) {

                        		$pieces = array_chunk($data['cart'], ceil(count($data['cart']) / 2));
                        		$part_1 = $pieces[0];
                        		$part_2 = isset($pieces[1]) ? $pieces[1] : array();

                        		$i = 1;
                        		foreach($part_1 as $key => $item_1) {
                        			$stt_1  = $i;
                        			$stt_2  = $stt_1 + count($part_1);
                        			$item_2 = $part_2[$key];

                        			$pattern = $body_table_string;
                        			$pattern = str_replace("{STT_1}",$stt_1,$pattern);
                        			$pattern = str_replace("{TEN_HH_1}",$item_1['name'],$pattern);
                        			$pattern = str_replace("{SL_1}",(float)$item_1['quantity'],$pattern);
                        			$pattern = str_replace("{MO_TA_1}",$item_1['description'],$pattern);
                        			$pattern = str_replace("{STT_2}",$stt_2,$pattern);
                        			$pattern = str_replace("{TEN_HH_2}",$item_2['name'],$pattern);
                        			$pattern = str_replace("{SL_2}",(float)$item_2['quantity'],$pattern);
                        			$pattern = str_replace("{MO_TA_2}",$item_2['description'],$pattern);

                        			$new_tr[] = $pattern;

                        			$i++;
                        		}
                        	}
                        	if (is_array($new_tr))
                        		$new_tr = implode('', $new_tr);
                        	$element->outertext = $new_tr;
                        	break;

                        } 
                        else 
                        {
                        	$new_tr = array();

                        	$pattern = $body_table_string;
                        	$pattern = str_replace("{THUE}","Trường {THUE} bắt buộc phải nằm cùng dòng với {STT} hoặc {TEN_HH} hoặc {MA_HH}",$pattern);
                        	$pattern = str_replace("{MO_TA}","Trường {MO_TA} bắt buộc phải nằm cùng dòng với {STT} hoặc {TEN_HH} hoặc {MA_HH}",$pattern);
                        	$pattern = str_replace("{DVT}","Trường {DVT} bắt buộc phải nằm cùng dòng với {STT} hoặc {TEN_HH} hoặc {MA_HH}",$pattern);
                        	$pattern = str_replace("{SL}","Trường {SL} bắt buộc phải nằm cùng dòng với {STT} hoặc {TEN_HH} hoặc {MA_HH}",$pattern);
                        	$pattern = str_replace("{DG-CK}","Trường {DG-CK} bắt buộc phải nằm cùng dòng với {STT} hoặc {TEN_HH} hoặc {MA_HH}",$pattern);
                        	$pattern = str_replace("{DON_GIA}","Trường {DON_GIA} bắt buộc phải nằm cùng dòng với {STT} hoặc {TEN_HH} hoặc {MA_HH}",$pattern);
                        	$pattern = str_replace("{THANH_TIEN}","Trường {THANH_TIEN} bắt buộc phải nằm cùng dòng với {STT} hoặc {TEN_HH} hoặc {MA_HH}",$pattern);




                        	$pattern = str_replace("{SL_1}","Trường {SL_1} bắt buộc phải nằm cùng dòng với {STT_1} hoặc {TEN_HH_1} hoặc {MA_HH_1}",$pattern);
                        	$pattern = str_replace("{MO_TA_1}","Trường {MO_TA_1} bắt buộc phải nằm cùng dòng với {STT_1} hoặc {TEN_HH_1} hoặc {MA_HH_1}",$pattern);
                        	$pattern = str_replace("{SL_2}","Trường {SL_2} bắt buộc phải nằm cùng dòng với {STT_2} hoặc {TEN_HH_2} hoặc {MA_HH_2}",$pattern);
                        	$pattern = str_replace("{MO_TA_2}","Trường {MO_TA_2} bắt buộc phải nằm cùng dòng với {STT_1} hoặc {TEN_HH_1} hoặc {MA_HH_1}",$pattern);

                        	$new_tr[] = $pattern;
                        	$new_tr = implode('', $new_tr);
                        	$element->outertext = $new_tr;
                        }

                        
                        
                } # end foreach


            }
        }
			# END
			# Phần xử lý dữ liệu body
        
        
		#---------------------------------------------------------------------------------------------------------#

									# Phần xử lý dữ liệu cuối bảng
        
        #---------------------------------------------------------------------------------------------------------#\

        $html_string = $html->outertext;

			# tổng tiền
        $tong_tien = NumberFormatToCurrency(abs($total_money));
			# tổng giá trị đơn hàng
        $tong_dh = $this->config->item('round_cash_on_sales') && $data['is_sale_cash_payment'] ? NumberFormatToCurrency(round_to_nearest_05($data['total'])) : NumberFormatToCurrency($data['total']);
			# tiền bằng chữ
        $bang_chu = getStringNumberComma($tong_dh);
			# tổng tiền thanh toán
        $tien_da_thanh_toan = 0;
        $tien_coc = 0;
        
        
        $ghi_no = 0;
        
        if(!empty($data['payments'])) {
        	foreach($data['payments'] as $val) {

        		$tien_da_thanh_toan = $tien_da_thanh_toan +$val['payment_amount'];
        	}

        	foreach($data['payments'] as $val) {
        		if($val['payment_type'] == 'Sổ ghi nợ')
        			$ghi_no = $ghi_no + $val['payment_amount'];
        	}

        	$tien_da_thanh_toan = $tien_da_thanh_toan - $ghi_no;
        	$tien_da_thanh_toan_tmp = $tien_da_thanh_toan;

        	$tien_da_thanh_toan = NumberFormatToCurrency($tien_da_thanh_toan);
        }
        
        
        $con_lai = NumberFormatToCurrency($data['total'] - $tien_da_thanh_toan_tmp); 
			// tiền trả lại
        $tien_tra_lai = 0;
        if ($data['amount_change'] >= 0)
        	$tien_tra_lai = $this->config->item('round_cash_on_sales')  && $data['is_sale_cash_payment'] ?  NumberFormatToCurrency(round_to_nearest_05($data['amount_change'])) : NumberFormatToCurrency($data['amount_change']);
        else
        	$tien_tra_lai = $this->config->item('round_cash_on_sales')  && $data['is_sale_cash_payment'] ?  NumberFormatToCurrency(round_to_nearest_05($data['amount_change'] * -1)) : NumberFormatToCurrency($data['amount_change'] * -1);

			// vat
        $vat = 0;
        if(!empty($data['taxes'])) {
        	foreach($data['taxes'] as $key => $val){
        		if (strpos($key, 'VAT') !== false) {
        			$vat = $vat + $val;
        		}
        	}
        }

        $vat = NumberFormatToCurrency(abs($vat),1);

			// ngày - tháng - năm
        $day   = date('d');
        $month = date('m');
        $year  = date('Y');

			//replace string
        $html_string = $html->outertext;
 	// echo "<pre>"; print_r($customer_info); die();
			//thông tin khách hàng
        $customer_info = array(
        	'TEN_KH' 	   => $data['customer'],
        	'CT_KH' 	   => isset($data['customer_company_name'])?$data['customer_company_name']:'',
        	'DIA_CHI_1_KH' => $data['customer_address_1'],
        	'DIA_CHI_2_KH' => $data['customer_address_2'],
        	'SDT_KH' 	   => $data['customer_phone'],
        	'CHUCVU_KH'    => isset($data['customer_position'])?$data['customer_position']:'',
        	'TKNH_KH' 	   => isset($data['customer_account_number'])?$data['customer_account_number']:'',
        	'EMAIL_KH'    => $data['customer_email']
        );


			// kho - công ty
        $sale_emp_info = isset($data['sale_emp_info'])?$data['sale_emp_info']:'';
        
        
        if(!empty($sale_emp_info)){
        	$sale_emp_name = $sale_emp_info->first_name . ' ' . $sale_emp_info->last_name;
        } else $sale_emp_name = '';

        
        $localtion_info = array(
        	'LOGO' 				   => '<img src="'.$this->Appconfig->get_logo_image().'" />',
        	'NAME_COMPANY' 		   => $this->config->item('company'),
        	'ADDRESS_COMPANY' 	   => nl2br($this->Location->get_info_for_key('address', isset($data['override_location_id']) ? $data['override_location_id'] : FALSE)),
        	'EMAIL_COMPANY' 	   => $this->Location->get_info_for_key('email'),
        	'TEL_COMPANY' 		   => nl2br($this->Location->get_info_for_key('phone', isset($data['override_location_id']) ? $data['override_location_id'] : FALSE)),
        	'FAX_COMPANY' 		   => $this->Location->get_info_for_key('fax'),
        	'WEBSITE_COMPANY' 	   => $this->config->item('website'),
        	'SALE_OFFICE_COMPANY'  => $this->Location->get_info_for_key('sale_office'),
        	'ACCOUNT_BANK_COMPANY' => $this->Location->get_info_for_key('account_bank'),
        	'SALE_EMP_NAME'		   => $sale_emp_name,
        	'SALE_EMP_PHONE'	   => !empty($sale_emp_info->phone_number)?$sale_emp_info->phone_number:'',
        	'SALE_EMP_EMAIL'	   => !empty($sale_emp_info->email)?$sale_emp_info->email:'',
        );

			// merge thông tin
        $info_merge = array_merge($localtion_info, $customer_info);

			// covert hoa
        $info_upper = array();
        foreach($info_merge as $key => $val)
        	$info_merge[$key . '_U'] = $val = mb_strtoupper($val, 'UTF-8');


        foreach($info_merge as $key => $val) {
        	$html_string = str_replace('{'.$key.'}',$val,$html_string);
        }
        
			#---------------------------------------------------------------------------------------------------------#
										# Phần xử lý dữ liệu cuối bảng
			#---------------------------------------------------------------------------------------------------------#
        $html_string = str_replace("{ORDER_CODE}",$data['sale_id'],$html_string);
        $html_string = str_replace("{TONG_TIEN}",$tong_tien,$html_string);
        if ($data['show_comment_on_receipt'] == 1) {
        	$html_string = str_replace("{GHI_CHU}",!empty($data['comment'])?$data['comment']:'',$html_string);
        } else {
        	$html_string = str_replace("{GHI_CHU}",'',$html_string);
        }

        $html_string = str_replace("{TONG_DH}",$tong_dh,$html_string);
        $html_string = str_replace("{CON_LAI}",$con_lai,$html_string);
        $html_string = str_replace("{TIEN_DA_THANH_TOAN}",$tien_da_thanh_toan,$html_string);
        $html_string = str_replace("{DAT_COC}",$tien_da_thanh_toan,$html_string);
        $html_string = str_replace("{TIEN_TRA_LAI}",$tien_tra_lai,$html_string);
        $html_string = str_replace("{VAT}",$vat,$html_string);
        $html_string = str_replace("{DATE}",$day,$html_string);
        $html_string = str_replace("{MONTH}",$month,$html_string);
        $html_string = str_replace("{YEAR}",$year,$html_string);
        $html_string = str_replace("{MA_DV}",$data['code'],$html_string);
        $html_string = str_replace("{BANGCHU}",$bang_chu,$html_string);
        $html_string = str_replace("{HD_BANG_CHU}",$bang_chu,$html_string);
        $html_string = str_replace("{HD_BANG_SO}",$tong_dh,$html_string);
        $html_string = str_replace("{GIAM_GIA}",$total_discount,$html_string);                 



			//---------------------------------------------------------------------
			// Add extra people to a invoice(for VISA only)
			// 
			// Created by LK
			//---------------------------------------------------------------------
        $more_customers_in_service        = array();
        $more_customers_in_service_string ='';
        $i =1;
        if(!empty($data['more_customers_in_service'])){
        	foreach($data['more_customers_in_service'] as $customer)
        	{
        		$more_customers_in_service['name'][]= ($customer['sex'] == 2)?$i.'. '.lang('common_female').': '.$customer['last_name'].' '.$customer['first_name']:$i.'. '.lang('common_male').': '.$customer['last_name'].' '.$customer['first_name'];
        		$more_customers_in_service['passport'][]=lang('common_passport').': '.$customer['passport'];
        		$i++;
        	}
        }
        $more_customers_in_service_body = '';
        if(!empty($more_customers_in_service['name'])){
        	foreach($more_customers_in_service['name'] as $key=>$name)
        	{
        		$more_customers_in_service_body.='<tr><td><span style="color: rgb(0, 0, 0); font-family: Roboto, Arial, sans-serif;">'.$name.'</span></td>
        		<td><span style="color: rgb(0, 0, 0); font-family: Roboto, Arial, sans-serif;">'.$more_customers_in_service['passport'][$key].'</span></td></tr>';
        	}
        }

        $more_customers_in_service_string .= '<table cellpadding="1" cellspacing="1" style="width:300px;">
        <tbody>
        '.$more_customers_in_service_body.'
        <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        </tbody>
        </table>
        
        <p>&nbsp;</p>';
        ;

        
        
        $html_string = str_replace("{MORE_CUSTOMER_IN_SERVICE}",$more_customers_in_service_string,$html_string);

		//-------------------------------------------------------------------------
		// End extra people to a invoice(for VISA only)
    //----------------------------------------------------------------------------






		#---------------------------------------------------------------------------------------------------------#
										# END Phần xử lý dữ liệu cuối bảng
		#---------------------------------------------------------------------------------------------------------#
        // echo $html_string;
		// } # end if (break);

		//Lấy thông tin hợp đồng
        return $html_string;
    }

    protected function getTypeOfOrder($payments = array(), $mode = '', $suspended = 0,$fulfillment=0 , $otherView = '')
    {
    	if($fulfillment==0) {
    		$typeOfView = 'order';
    		foreach ($payments as $payment) {
    			if( $payment['payment_type'] == lang('common_store_account') )
    			{
    				$typeOfView = 'order_debit';
    			}
    		}

    		if($mode == 'return')
    		{
    			$typeOfView = 'order_return';
    		}
    		if($mode == 'store_account_payment')
    		{
    			$typeOfView = 'order_liabilities';
    		}

    		if($mode == 'vat_order') {
    			$typeOfView = 'order_vat';
    		}

    		if($suspended == 1) {
    			$typeOfView = 'order_booked';
    		} elseif ($suspended == 2) {
    			if ($otherView == 'general_receipt') {
    				$typeOfView = 'order_show_price_general';
    			} elseif($otherView == 'detail_receipt') {
    				$typeOfView = 'order_show_price_detail';
    			} else {
    				$typeOfView = 'order_show_price';
    			}

    		}
    	} else {
    		$typeOfView = 'order_fulfillment';
    		foreach ($payments as $payment) {
    			if( $payment['payment_type'] == lang('common_store_account') )
    			{
    				$typeOfView = 'order_debit_fulfillment';
    			}
    		}

    		if($mode == 'return')
    		{
    			$typeOfView = 'order_return_fulfillment';
    		}
    		if($mode == 'store_account_payment')
    		{
    			$typeOfView = 'order_liabilities_fulfillment';
    		}

    		if($suspended == 1)
    		{
    			$typeOfView = 'order_booked_fulfillment';
    		} elseif ($suspended == 2) {
    			$typeOfView = 'order_show_price_fulfillment';
    		}

    	}

    	return $typeOfView;
    }
    
    function receipt($sale_id, $type_receipt = 'default')
    {
    	$this->load->helper('sale');
    	$fulfillment = $this->input->get('fulfillment');
    	$type        = $this->input->get('type');
    	$template    = $this->input->get('template');
		//Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
    	$this->sale_lib->save_current_sale_state();

    	$data['is_sale'] = FALSE;
    	$sale_info = $this->Sale->get_sale_info($sale_id);

    	$this->sale_lib->clear_all();
    	$this->sale_lib->copy_entire_sale($sale_id, true);
    	$data['cart']=$this->sale_lib->get_cart();
		# Sắp xếp mảng theo biến line đã được lưu tại session

    	sap_xep_mang($data['cart'],'line',true);


    	$customer_id=$this->sale_lib->get_customer();

		// [4biz] Get customer balance before make orther
    	if($customer_id != -1)
    	{
    		$cust_info=$this->Customer->get_info($customer_id);

    		if ($cust_info->balance !=0)
    		{
    			$data['customer_balance_for_sale_before'] = $cust_info->balance;
    		}
    	}
    	$data['payments']=$this->sale_lib->get_payments();
    	$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
    	$data['show_payment_times'] = TRUE;
    	$data['signature_file_id'] = $sale_info['signature_image_id'];
    	$data['service_code'] = $sale_info['code'];
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
    	$data['saleComment'] = $this->Sale->getSaleComments($sale_id);
    	$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
    	$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
    	$customer_id=$this->sale_lib->get_customer();

    	$emp_info=$this->Employee->get_info($sale_info['employee_id']);
    	$sold_by_employee_id=$sale_info['sold_by_employee_id'];
    	$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
    	$data['payment_type']=$sale_info['payment_type'];
    	$data['amount_change']=$this->sale_lib->get_amount_due($sale_id) * -1;
    	$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
    	$data['employee_phone']=$emp_info->phone_number;
    	$data['employee_email']=$emp_info->email;
    	$data['employee_address']=$emp_info->address_1;
    	$data['ref_no'] = $sale_info['cc_ref_no'];
    	$data['auth_code'] = $sale_info['auth_code'];
    	$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
    	$data['type_receipt'] = $type_receipt;
    	$data['suspend']         = $sale_info['suspended'];
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

    	$data['sale_document'] =  $sale_info['document'];
    	$data['sale_mode']     =  $sale_info['sale_mode'];
    	$data_tmp         = $data;
    	$data['template'] = $template;

    	$data['data_tmp'] = $data_tmp;
        // if(!empty($data['sale_document'])) {
        //     foreach($data['sale_document'] as &$val) {
        //         $val['content_quotes_contract'] = $this->convertTemplate($data, $val['content_quotes_contract'], $sale_info);
        //     }
        // }

    	$this->load->view("sales/receipt",$data);

    	$this->sale_lib->clear_all();

		//Restore previous state saved above
    	$this->sale_lib->restore_current_sale_state();
    }
    
    
    
    function sale_section($sale_id, $type_receipt = 'default', $option = ''){
    	$data = array();
    	$this->load->helper('sale');
    	$fulfillment = $this->input->get('fulfillment');
    	$type        = $this->input->get('type');
    	$template        = $this->input->get('template');


        //Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
    	$this->sale_lib->save_current_sale_state();
        #----------------------------------------------------------------------------------------------- 
								# Lấy số liệu cho hóa đơn
        #-----------------------------------------------------------------------------------------------
    	$data['is_sale'] = FALSE;
    	$sale_info = $this->Sale->get_sale_info($sale_id);
    	$this->sale_lib->clear_all();
        # Lấy dữ liệu cho hóa đơn
    	$this->sale_lib->copy_entire_sale($sale_id, true);
    	$data['cart']=$this->sale_lib->get_cart();
    	$data['itemsContainsLine'] = $this->sale_lib->getItemContainsLine();
        # sắp xếp lại mảng đã được lưu Session
		// sap_xep_mang($data['cart'],'line',true);

    	$data['service_id'] = $sale_info['service_id'];
    	$data['code']  = $sale_info['code'];
    	$data['service_code'] = '';
    	if(!empty($data['service_id'])) {

    		$data['service_code'] = $this->sale_lib->create_sale_code() ;
    	}

    	$customer_id=$this->sale_lib->get_customer();

    	$data['payments']=$this->sale_lib->get_payments();

      	#----------------------------------------------------------------------------------------------- 
								# Lấy số liệu cho hóa đơn
        #-----------------------------------------------------------------------------------------------

        // [4biz] Get customer balance before make orther
    	if($customer_id != -1)
    	{
    		$cust_info=$this->Customer->get_info($customer_id);
            # Nợ đầu, nợ cuối
    		if ($cust_info->balance !=0)
    		{
    			$data['customer_balance_for_sale_before'] = !empty($data['payments'][0]['no_dau'])?$data['payments'][0]['no_dau']:0;
    			$data['tong_no_cuoi'] = !empty($data['payments'][0]['no_cuoi'])?$data['payments'][0]['no_cuoi']:0;
    		}
    	}

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
    	$data['saleComments'] = $this->Sale->getSaleComments($sale_id);
    	$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
    	$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
    	$customer_id=$this->sale_lib->get_customer();

    	$emp_info=$this->Employee->get_info($sale_info['employee_id']);
    	$sold_by_employee_id=$sale_info['sold_by_employee_id'];
    	$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
    	$data['payment_type']=$sale_info['payment_type'];
    	$data['amount_change']=$this->sale_lib->get_amount_due($sale_id) * -1;
    	$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
    	$data['employee_phone']=$emp_info->phone_number;
    	$data['employee_email']=$emp_info->email;
    	$data['employee_address']=$emp_info->address_1;
    	$data['sale_person'] = $sale_emp_info->first_name.' '.$sale_emp_info->last_name;
    	$data['ref_no'] = $sale_info['cc_ref_no'];
    	$data['auth_code'] = $sale_info['auth_code'];
    	$data['discount_exists'] = $this->_does_discount_exists($data['cart']);


        //chỉ dùng cho rèm Hà My
    	$cartItemsAttributeSet = $this->sale_lib->getCartItemsAttributeSet();


    	if($customer_id!=-1)
    	{
    		$cust_info=$this->Customer->get_info($customer_id);
    		$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->phone_number==''  ? '' :' - '.$cust_info->phone_number);
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
    			$data['customer_balance_for_sale'] = !empty($data['payments'][0]['no_dau'])?$data['payments'][0]['no_dau']:0;
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

        // only for rem Ha My
        //=====================================================================================================


    	$data['listVai'] = [];
    	$data['listRemCuon'] = [];
    	$data['listThanh_DongCo_DieuKhien'] = [];

    	foreach($data['itemsContainsLine'] as $itemContainsLine) {
    		$listVai                    = [];
    		$listRemCuon                = [];    
    		$listThanh_DongCo_DieuKhien  = [];          
    		$listVai['itemName'] =  $itemContainsLine['itemName'];
    		foreach($data['cart'] as $item) {
    			if (in_array($item['line'], $itemContainsLine['line'])) {
    				if ($item['item_category_id'] == $this::CATEGORY_VAI_PHU  ) {
    					$listVai['item'][] = $item;
    				} elseif ($item['item_category_id'] == $this::CATEGORY_VAI_CHINH) {
    					$listVai['item'][] = $item;  
    				} elseif ($item['item_category_id'] == $this::CATEGORY_REM_CUON) {
    					$listRemCuon['item'][] = $item;
    				} else {
    					$listThanh_DongCo_DieuKhien['item'][] = $item;
    				}
    			}
    		}
    		if (!empty($listVai)) {
    			$listRemCuon['itemName'] =  $itemContainsLine['itemName'];
    			$data['listVai'][] = $listVai;
    		}
    		if (!empty($listRemCuon['item'])) {
    			$listRemCuon['itemName'] =  $itemContainsLine['itemName'];
    			$data['listRemCuon'][] = $listRemCuon;
    		}
    		if (!empty($listThanh_DongCo_DieuKhien['item'])) {
    			$listThanh_DongCo_DieuKhien['itemName'] =  $itemContainsLine['itemName'];
    			$data['listThanh_DongCo_DieuKhien'][] = $listThanh_DongCo_DieuKhien;
    		}
    	}
    	foreach($data['cart'] as $line => $item) {
    		if ($item['item_category_id'] == $this::CATEGORY_REM_CUON) {
    			$data['listRemCuon'][] = $item;
    		}
    		$cartItemsAttributeSetInfo = $this->Attribute_set->get_info($cartItemsAttributeSet[$line]);
    		if (strpos($cartItemsAttributeSetInfo->code,'RK') !== false ) {
    			$cartItemsAttributeSetType[$line] = 'rem_keo';
    		} elseif(strpos($cartItemsAttributeSetInfo->code,'RR') !== false ) {
    			$cartItemsAttributeSetType[$line] = 'rem_roman';
    		} else {
    			$cartItemsAttributeSetType[$line] = 'non_type';
    		}
    	}

    	$data['cartItemsAttributeSetType'] = $cartItemsAttributeSetType;
    	$data['cartItemsAttributeValue'] = $this->sale_lib->getCartItemsAttributeValue();
    	$data['cartItemsAttributeSet'] = $this->sale_lib->getCartItemsAttributeSet();

        //===================================================================================================



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

    	$sale_mode = $this->sale_lib->get_mode();
    	$flagDefault = true;
    	if($sale_info['suspended'] == 1 && $sale_info['was_layaway'] == 1 && $this->config->item('config_template_order_sale') != 0) {
    		$flagDefault = false;

    	} elseif($sale_mode == 'sale' && $this->config->item('config_template_sale') != 0 ) {
    		$flagDefault = false;

    	}
    	if(!empty($template) && $template == 'default') $flagDefault = true;

    	if($sale_mode == 'store_account_payment') {
    		$data['store_account_payment_value'] = $this->sale_lib->get_store_account_payment_value();
    	}

    	if($flagDefault == true) {
    		$typeOfView = $this->getTypeOfOrder($data['payments'], $sale_mode, $sale_info['suspended'],$fulfillment, $type_receipt);
    		$typeOfViewFix = $typeOfView;

    		if(($this->config->item('config_sales_receipt_pdf_size')=='a8'||($this->config->item('config_sales_receipt_pdf_size')=='a58'))&&  !strpos($typeOfView, '_fulfillment'))
    			$typeOfViewFix = $typeOfView .'_fulfillment';

    		if($fulfillment!=NULL && $fulfillment==0)
    			$typeOfViewFix = $typeOfView;

    		if($sale_mode == 'vat_order' || $sale_mode == 'store_account_payment') {
    			$store_account         = $this->Sale->get_store_accounts(array('sale_id'=>$sale_id), array('task'=>'by-sale-id'));
    			$data['store_account'] = $store_account;
    		}
            # view hóa don
    		$data['pdf_block_html'] = $this->load->view('sales/partials/' . $typeOfViewFix, $data, TRUE);
    	}else {

    		if(!empty($sale_info['document'])) {
    			foreach($sale_info['document'] as &$val) {
    				$constract_id = $val['id_quotes_contract'];

    			}
    		}
    		elseif($sale_info['suspended'] == 1 && $sale_info['was_layaway'] == 1){
    			$constract_id = $this->config->item('config_template_order_sale');
    		}

    		else $constract_id = $this->config->item('config_template_sale');

    		if($constract_id == 0) $constract_id = $this->config->item('config_template_sale');
    		$this->load->model('QuotesConstract');

    		$item = $this->QuotesConstract->getItem($constract_id);

    		$content_string 	   = $item['content_quotes_contract'];
    		$html_string 		   = $this->convertTemplate($data, $content_string, $sale_info);

            # view hóa don
    		$data['pdf_block_html'] = $this->load->view('sales/partials/custom', array('html_string'=>$html_string), TRUE);
    	}
    	if ($option == 'exportExcel') {

    	} else {
    		$this->load->view('sales/sale_section', $data);
    	}

    	$this->sale_lib->clear_all();
        //Restore previous state saved above
    	$this->sale_lib->restore_current_sale_state();
    }
    
    
    
    
    function ajax_order() {
    	$post = $this->input->post();
    	$arrParams = array_merge($post, $this->input->get());
    	if(!empty($post)) {
    		if($arrParams['type'] == 'sale') {
    			$discount_id = $this->Item->get_item_id_for_flat_discount_item();
    			$data = $this->getSale($arrParams['sale_id']);
    			$data['discount_id'] = $discount_id;

    			$this->load->view("sales/ajax_order",$data);
    		}elseif($arrParams['type'] == 'customer') {
    			$sale_info = $this->Sale->getInfo($arrParams['sale_id']);
    			$result = $this->Customer->get_information($sale_info['customer_id']);

    			if(!empty($result)) {
    				$customer_info['fullname']     = $result['first_name'] . ' ' . $result['last_name'];
    				$customer_info['company_name'] = $result['company_name'];
    				$customer_info['address']      = $result['address_1'];
    				$customer_info['phone_number'] = $result['phone_number'];
    				if(empty($customer_info['address']))
    					$customer_info['address']  = $result['address_2'];
    			}
    			echo json_encode($customer_info);
    		}
    	}
    }
    
    function contract_payment_store() {
    	$post  = $this->input->post();
    	if(!empty($post)) {
    		$arrParam                            = array_merge($post, $this->input->get());
    		$arrParam['paginator']               = $this->_paginator;
    		$arrParam['page']                    =  $this->uri->segment(3, 1);

    		$contract_info = $this->Contract->get_item(array('id'=>$arrParam['contract_id']));
    		$sale_info = $this->getSale($contract_info['sale_id']);
    		$arrParam['contract_info'] = $contract_info;
    		$arrParam['sale_info']    = $sale_info;

    		$config['base_url'] = base_url() . 'customers/contract_payment_store';
    		$config['total_rows'] = $this->Contract->count_contract_payment($arrParam);

    		$config['per_page'] = $arrParam['paginator']['per_page'] = 5;

    		$config['uri_segment'] = 3;
    		$config['use_page_numbers'] = TRUE;

    		$items = $this->Contract->list_contract_payment($arrParam);

    		$this->load->library("pagination");
    		$this->pagination->initialize($config);
    		$this->pagination->createConfig('front-end');

    		$pagination = $this->pagination->create_ajax();

    		$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
    		echo json_encode($result);
    	}
    }
    
    function contract_delivery_form() {
    	$arrParams = array_merge($this->input->post(), $this->input->get());
    	$data      = $this->input->post();
    	$data['slb_contract_payment'] = $this->Contract->items_selectbox_contract_payment($arrParams['contract_id']);

    	$contract_info = $this->Contract->get_item(array('id'=>$arrParams['contract_id']));
    	if(empty($contract_info))
    		return;

    	$arrParams['sale_id'] = $data['sale_id'] = $contract_info['sale_id'];

    	if($arrParams['delivery_id'] == -1) {
    		$delivery = $this->Contract->get_sum_quantity_from_delivery($arrParams['sale_id']);
    		$order    = $this->getSale($arrParams['sale_id']);

    		$items = $this->sale_lib->get_delivery_items($order['cart'], $delivery);

    		$data['items'] = $items;
    	}else {
    		$info = $this->Contract->get_contract_delivery_detail_info(array('id'=>$arrParams['delivery_id']));
    		$data['information'] = $info;
    	}

    	$this->load->view('sales/form_contract_delivery', $data);
    }
    
    protected function getSale($sale_id)
    {
    	$this->load->helper('sale');
    	$fulfillment = $this->input->get('fulfillment');
    	$type        = $this->input->get('type');

		//Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
    	$this->sale_lib->save_current_sale_state();

    	$data['is_sale'] = FALSE;
    	$sale_info = $this->Sale->get_info($sale_id)->row_array();

    	$this->sale_lib->clear_all();
    	$this->sale_lib->copy_entire_sale($sale_id, true);
    	$data['cart']=$this->sale_lib->get_cart();

    	$customer_id=$this->sale_lib->get_customer();

		// [4biz] Get customer balance before make orther
    	if($customer_id != -1)
    	{
    		$cust_info=$this->Customer->get_info($customer_id);

    		if ($cust_info->balance !=0)
    		{
    			$data['customer_balance_for_sale_before'] = $cust_info->balance;
    		}
    	}
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
    	$data['sale_emp_info'] = $sale_emp_info;
    	$data['payment_type']=$sale_info['payment_type'];
    	$data['amount_change']=$this->sale_lib->get_amount_due($sale_id) * -1;
    	$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
    	$data['ref_no'] = $sale_info['cc_ref_no'];
    	$data['auth_code'] = $sale_info['auth_code'];
    	$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
    	if($customer_id!=-1)
    	{
    		$cust_info=$this->Customer->get_info($customer_id);
    		// echo "<pre>"; print_r($cust_info); die();
    		$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
    		$data['customer_address_1']       = $cust_info->address_1;
    		$data['customer_address_2']       = $cust_info->address_2;
    		$data['customer_company_name']    = $cust_info->first_name.' '.$cust_info->last_name;
    		$data['customer_person_id']       = $cust_info->person_id;
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
    	$this->sale_lib->clear_all();

		//Restore previous state saved above
    	$this->sale_lib->restore_current_sale_state();

    	return $data;
    }
    
    function suspend($suspend_type = 1, $type_receipt ='')
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
    	$more_comment= $this->sale_lib->getSaleComments();
    	$show_comment_on_receipt = $this->sale_lib->get_comment_on_receipt();
    	$emp_info=$this->Employee->get_info($employee_id);
		//Alain Multiple payments
    	$data['payments']=$this->sale_lib->get_payments();
    	$data['amount_change']=$this->sale_lib->get_amount_due() * -1;
    	$data['balance']=$this->sale_lib->get_payment_amount(lang('common_store_account'));
    	$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
    	$data['add_more_customer_to_service'] = $this->sale_lib->get_customers();
    	$data['itemsContainsLine'] = $this->sale_lib->getItemContainsLine();
    	$data['saleComment']        = $this->sale_lib->getSaleComments();

    	$cust_info = NULL;
    	if($customer_id!=-1)
    	{
    		$cust_info=$this->Customer->get_info($customer_id);
    		$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' - '.$cust_info->company_name).($cust_info->account_number==''  ? '' :' - '.$cust_info->account_number);
    	}

    	$total_payments = 0;

    	foreach($data['payments'] as $payment)
    	{
    		$total_payments += !empty($payment['payment_amount'])?$payment['payment_amount']:0;
    	}

    	$sale_id = $this->sale_lib->get_suspended_sale_id();

    	$extraData['deliverer'] = $this->sale_lib->get_deliverer();
    	$extraData['delivery_date'] = $this->sale_lib->get_delivery_date();

    	$data['service_id'] = $this->sale_lib->get_service_id();

    	$employee_groups = $this->sale_lib->get_group_employees();

    	if(!empty($employee_groups)) {
    		foreach($employee_groups as $group_id => $group) {
    			if(!empty($group['list'])) {
    				foreach($group['list'] as $item) {
    					$tmp = array();
    					$tmp['group_id']   = $group_id;
    					$tmp['employee_id'] = $item['id'];
    					$emp_group_arr[] = $tmp;
    				}
    			}
    		}
    	}

    	$commission_info = $this->sale_lib->get_commission_info();
    	$sale_code = $this->sale_lib->create_sale_code();

    	$extraData['commission_time_method'] = $commission_info['commission_time_method'];
    	$extraData['commission_method']      = $commission_info['commission_method'];
    	$extraData['min_profit']             = $commission_info['min_profit'];
    	$extraData['min_profit_commission']  = !empty($commission_info['min_profit_commission'])?$commission_info['min_profit_commission']:0;

		# Lưu dữ liệu bán hàng # Lưu dữ liệu bán hàng # Lưu dữ liệu bán hàng # Lưu dữ liệu bán hàng
    	$data['temp_service_id'] = $this->sale_lib->get_temp_service();
    	$data['sale_status_id'] = empty($this->sale_lib->get_sale_status())? -1 : $this->sale_lib->get_sale_status() ;
    	$data['supporter_list'] = $this->sale_lib->get_supporter_list();
    	$data['change_sale_date'] = empty($_SESSION['sale_time_date']) ? date("Y-m-d H:i:s") : date("Y-m-d H:i:s",strtotime($_SESSION['sale_time_date']));
        // echo "<pre>";
    	// echo "<pre>"; print_r($this->sale_lib->get_sale($sale_id));die();
    	// echo $sale_id; die();
    	if ($sale_id!=null) {
    		$suspend_type = $this->sale_lib->get_sale($sale_id)['cart'][1]['suspended'];
    	}

    	$sale_id = $this->Sale->save(
    		$data['cart'],
    		$customer_id,
    		$employee_id,
    		$sold_by_employee_id,
    		$comment,
    		$more_comment,
    		$show_comment_on_receipt,
    		$data['payments'],
    		$data['add_more_customer_to_service'],
				$data['itemsContainsLine'], // for rem hamy only
				$sale_id,
				$suspend_type,
				$data['change_sale_date'],
				$data['balance'],
				0,
				$extraData, 
				$data['temp_service_id'], 
				$sale_code, 
				0, 
				0, 
				0,
				$cust_info,
				$data['sale_status_id']
			);

		// Handle approver feature
		// 1. dat_hang, 2. bao_gia, 5. tinh_gia
    	if ($suspend_type == 2) {
    		$stepCode = 'bao_gia';
    		$this->ApproverGroup->initApproverStatus($stepCode, $sale_id);
    	} elseif ($suspend_type == 5) {
    		$stepCode = 'tinh_gia';
    		$this->ApproverGroup->initApproverStatus($stepCode, $sale_id);
    	}

    	$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
// 		if(!empty($emp_group_arr)) {
//             $sale_employees = array();
//             foreach($emp_group_arr as $val) {
//                 $tmp = array();
//                 $tmp['sale_id']     = $sale_id;
//                 $tmp['employee_id'] = $val['employee_id'];
//                 $tmp['group_id']    = $val['group_id'];

//                 $sale_employees[] = $tmp;
//             }
//         }

    	$supporter_list = $data['supporter_list'];

    	if (!empty($supporter_list)) {
    		$sale_employees = array();
    		foreach ($supporter_list as $val) {
    			$tmp = array(
    				'sale_id' => $sale_id,
    				'employee_id' => $val['id'],
    				'group_id' => -1
    			);
    			$sale_employees[] = $tmp;
    		}
    	}

    	if(!empty($sale_employees)) {
    		$this->db->where('sale_id',$sale_id);
    		$this->db->delete('sales_employees');
    		$this->db->insert_batch('sales_employees', $sale_employees);
    	}
    	else {
    		$this->db->where('sale_id',$sale_id);
    		$this->db->delete('sales_employees');
    	}       


    	if ($data['sale_id'] == $this->config->item('sale_prefix').' -1')
    	{
    		$this->_reload(array('error' => lang('sales_transaction_failed')));
    		return;
    	}
    	$this->sale_lib->clear_all();
    	if ($this->config->item('show_receipt_after_suspending_sale'))
    	{
    		$_SESSION['notice'] = 'Tạo Nhu cầu khách hàng thành công';
    		redirect(base_url('sales'),'refresh');
			// redirect('sales/receipt/'.$sale_id.'/'.$type_receipt);
    	}
    	else
    	{
    		$this->_reload(array('success' => lang('sales_successfully_suspended_sale')));
    	}
    }
    
    function str_to_date($str) {
    	if (empty($str)) {
    		return null;
    	} else {
    		return DateTime::createFromFormat("d-m-Y", $str)->format("Y-m-d");
    	}
    }
    
    function suspended($id=null)
    {

    	$e = $this->session->userdata('person_id');
    	$view ='view_scope_owner';
    	if($this->Employee->has_module_action_permission('sales','view_scope_location',$e))
    	{
    		$view = 'view_scope_location';
    	}
    	if($this->Employee->has_module_action_permission('sales','view_scope_all',$e))
    	{
    		$view = 'view_scope_all';
    	}


    	$data = array();
    	$suspended_types = [
    		1 => 'dat_hang',
    		2 => 'bao_gia',
    		5 => 'tinh_gia'
    	];

    	$from_date = $this->input->post('from_date');
    	$to_date = $this->input->post('to_date');
    	$status_id = $this->input->post('status_id');
    	$employee_created_id = $this->input->post('employee_created_id');
    	$employee_imp_id = $this->input->post('employee_imp_id');
    	$customer_name = $this->input->post('customer_name');
    	$service_id = $this->input->post('service_id');
    	if ($this->Employee->has_module_action_permission('sales','view_scope_all',$this->Employee->get_logged_in_employee_info()->person_id) || $this->Employee->has_module_action_permission('sales','view_scope_location',$this->Employee->get_logged_in_employee_info()->person_id)) {
    		$implement = null;
    	}else{
    		$implement = $this->Employee->get_logged_in_employee_info()->person_id;
    	}
    	$params = array(
    		'from_date' => $this->str_to_date($from_date),
    		'to_date' => $this->str_to_date($to_date),
    		'status_id' => $status_id,
    		'employee_id' => $employee_created_id,
    		'supporter_id' => $implement,
    		'customer_name' => $customer_name,
    		'item_id' => $service_id,
    		'status_type' => 1,
    		'view'=>$view,
    		// 'implement'=>$implement,
    	);

    	$suspended_sales =$this->Sale->get_all_suspended($params);
		// echo  "<pre>";var_dump($params);die();
		// echo  "<pre>";print_r($suspended_sales);die();
    	$total_approval    = [];
    	$total_approved    = [];
    	$isUseForContract  = [];
    	$disapprove        = [];

    	foreach ($suspended_sales as $suspended_sale) {
    		$total_approved[$suspended_sale['sale_id']]  = 0;
    		$approve_statuses = $this->ApproverGroup->getApproveStatuses($suspended_types[$suspended_sale['suspended']], $suspended_sale['sale_id']);
    		$disapprove[$suspended_sale['sale_id']] =  $this->ApproverGroup->lastestDisapproved($suspended_sale['suspended'], $suspended_sale['sale_id']);
    		foreach ($approve_statuses as $approve_status ) {
    			if ($approve_status['approved'] == 1 || $approve_status['approved'] == -1) {
    				$total_approved[$suspended_sale['sale_id']] += 1;
    			}
    		}
    		$total_approval[$suspended_sale['sale_id']] = count($approve_statuses);

    		$contract_info = $this->Contract->get_item_by_sale($suspended_sale['sale_id']);
    		if(!empty($contract_info)) {
    			$isUseForContract[$suspended_sale['sale_id']] = $contract_info['name'];
    		}
    	}
    	$years = $this->Sale->get_years_create();
    	$status_list = $this->Sale_status->get_all(1);
    	$employee_list = $this->Employee->get_list_employees_by_location($this->Employee->get_logged_in_employee_current_location_id());
    	$service_list = $this->Item->get_all()->result();

    	$data['years'] = $years;
    	$data['disapprove']       = $disapprove;
    	$data['isUseForContract'] = $isUseForContract;
    	$data['suspended_sales']  = $suspended_sales;
    	$data['total_approved']   = $total_approved;
    	$data['total_approval']   = $total_approval;
    	$data['from_date'] = $from_date;
    	$data['to_date'] = $to_date;
    	$data['status_id'] = $status_id;
    	$data['status_list'] = $status_list;
    	$data['employee_created_id'] = $employee_created_id;
    	$data['employee_imp_id'] = $employee_imp_id;
    	$data['employee_list'] = $employee_list;
    	$data['customer_name'] = $customer_name;
    	$data['service_list'] = $service_list;
    	$data['service_id'] = $service_id;
    	// echo "<pre>"; print_r($data); die();
    	$this->load->view('sales/suspended', $data);
    }

    function report_quotes($sale_id) 
    {
    	$data = array();
    	$data['sale_id'] = $sale_id;
    	$data['list_quotes'] = $this->Customer->get_list_template_quotes_contract(2);
    	$customer_id = $this->sale_lib->get_customer();
    	$cust_info = $this->Customer->get_info($customer_id);
    	$data['email'] = $cust_info->email;

    	$this->load->view('sales/form_report_quotes', $data);
    }
    
    
    function do_make_quotes($sale_id) {

		# Lấy id form mẫu
    	$id_quotes_contract = $this->input->post("quotes_id");
		# Lấy ra form mẫu
    	$data['info_quotes_contract'] = $this->Customer->get_info_quotes_contract($id_quotes_contract);
    	$data['is_sale'] = FALSE;
    	$sale_info = $this->Sale->get_info($sale_id)->row_array();
    	$this->sale_lib->copy_entire_sale($sale_id);
    	$data['cart'] = $this->sale_lib->get_cart();
    	$data['payments'] = $this->sale_lib->get_payments();
    	$data['subtotal'] = $this->sale_lib->get_subtotal();
    	$data['taxes'] = $this->sale_lib->get_taxes($sale_id);
    	$data['total'] = $this->sale_lib->get_total($sale_id);
    	$data['receipt_title'] = lang('sales_receipt');
    	$data['comment'] = $this->Sale->get_comment($sale_id);
    	$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
    	$data['transaction_time'] = date(get_date_format() . ' ' . get_time_format(), strtotime($sale_info['sale_time']));
    	$customer_id = $this->sale_lib->get_customer();
    	$emp_info = $this->Employee->get_info($sale_info['employee_id']);
    	$data['payment_type'] = $sale_info['payment_type'];
    	$data['amount_change'] = $this->sale_lib->get_amount_due($sale_id) * -1;
    	$data['employee'] = $emp_info->first_name . ' ' . $emp_info->last_name;
    	$data['phone_number'] = $emp_info->phone_number;
    	$data['email'] = $emp_info->email;
    	$data['ref_no'] = $sale_info['cc_ref_no'];
    	$this->load->helper('string');
    	$data['payment_type'] = str_replace(array('<sup>VNÄ�</sup><br />', ''), ' .VNÄ�', $sale_info['payment_type']);
    	$data['amount_due'] = $this->sale_lib->get_amount_due();
    	if ($customer_id != -1) {
    		$cust_info = $this->Customer->get_info($customer_id);
    		$data['customer'] = $cust_info->first_name . ' ' . $cust_info->last_name;
    		$data['cus_name'] = $cust_info->company_name == '' ? '' : $cust_info->company_name;
    		$data['code_tax'] = $cust_info->code_tax ? $cust_info->code_tax : '';
    		$data['address'] = $cust_info->address_1;
    		$data['account_number'] = $cust_info->account_number;
    	} else $data['customer'] = 'ducanh';
    	$data['sale_id'] = $sale_id;
    	$type = $this->input->post('quotes_type');
    	$cat_baogia = $this->input->post("sales_quotes_type");
    	$data['word'] = $type;
    	$data['cat_baogia'] = '';


    	$file_name = "BG_" . $sale_id . "_" . str_replace(" ", "", replace_character($data['customer'])) . "_" . date('dmYHis') . ".doc";

    	if (!file_exists(APPPATH. 'excel_materials')) {
    		mkdir(APPPATH. 'excel_materials/', 0777, true);
    	}
    	$fp = fopen(APPPATH . "excel_materials/" . $file_name, 'w+');
    	$arr_item = array();
    	$arr_service = array();
    	foreach ($data['cart'] as $line => $val) {
    		if ($val['item_id']) {
    			$info_item = $this->Item->get_info($val['item_id']);
    			if ($info_item->is_service == 0) {
    				$arr_item[] = array(
    					'item_id' => $val['item_id'],
    					'line' => $line,
    					'name' => $val['name'],
    					'item_number' => $val['item_number'],
    					'description' => $val['description'],
    					'serialnumber' => $val['serialnumber'],
    					'allow_alt_description' => $val['allow_alt_description'],
    					'is_serialized' => $val['is_serialized'],
    					'quantity' => $val['quantity'],
    					'stored_id' => isset($val['stored_id'])?$val['stored_id']:'',
    					'discount' => $val['discount'],
    					'price' => isset($val['stored_id'])?$val['stored_id']:'',
    					'price_rate' => isset($val['price_rate'])?$val['price_rate']:'',
    					'taxes' => isset($val['taxes'])?$val['taxes']:'',
    					'unit' => isset($val['unit'])?$val['unit']:'',
    				);
    			} else {
    				$arr_service[] = array(
    					'item_id' => $val['item_id'],
    					'line' => $line,
    					'name' => $val['name'],
    					'item_number' => $val['item_number'],
    					'description' => $val['description'],
    					'serialnumber' => $val['serialnumber'],
    					'allow_alt_description' => $val['allow_alt_description'],
    					'is_serialized' => $val['is_serialized'],
    					'quantity' => $val['quantity'],
    					'stored_id' => $val['stored_id'],
    					'discount' => $val['discount'],
    					'price' => $val['price'],
    					'price_rate' => $val['price_rate'],
    					'taxes' => $val['taxes'],
    					'unit' => $val['unit']
    				);
    			}
    		} else {
    			$arr_item[] = array(
    				'pack_id' => $val['pack_id'],
    				'line' => $val['line'],
    				'pack_number' => $val['pack_number'],
    				'name' => $val['name'],
    				'description' => $val['description'],
    				'quantity' => $val['quantity'],
    				'discount' => $val['discount'],
    				'price' => $val['price'],
    				'taxes' => $val['taxes'],
    				'unit' => $val['unit']
    			);
    		}
    	}

    	$str = "";

    	$stt = 1;
    	$total = 0;


    	if ($cat_baogia == 1) {

    		foreach ($arr_item as $line => $item) {
    			if ($item['pack_id']) {
    				$info_pack = $this->Pack->get_info($item['pack_id']);
    				$pack_item = $this->Pack_items->get_info($item['pack_id']);
    				$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
    				$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
    				$str .= "<tr>";
    				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
    				$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(GÃ³i SP)</strong><br>";
    				foreach ($pack_item as $val) {
    					$info_item = $this->Item->get_info($val->item_id);
    					$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
    				}

    				$str .= "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
    				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
    				if ($info_pack->images) {
    					$str .= "<img src='" . base_url('packs/' . $info_pack->images) . "' style='width:45px; height:45px'/>";
    				}
    				$str .= "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
    				$str .= "</tr>";
    				$stt++;
    				$total += $thanh_tien;
    			} else {
    				$info_item = $this->Item->get_info($item['item_id']);
    				$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
    				$thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
    				$str .= "<tr>";
    				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
    				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
    				if ($info_item->images) {
    					$str .= "<img src='" . base_url('item/' . $info_item->images) . "' style='width:45px; height:45px'/>";
    				}
    				$str .= "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
    				$str .= "</tr>";
    				$stt++;
    				$total += $thanh_tien;
    			}
    		}
    	} else if ($cat_baogia == 2) {
    		foreach ($arr_service as $line => $item) {
    			$info_item = $this->Item->get_info($item['item_id']);
    			$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                    //$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
    			$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
    			$str .= "<tr>";
    			$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
    			$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong/" . $info_item->name . "</td>";
    			$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
    			$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
    			if ($info_item->images) {
    				$str .= "<img src='" . base_url('item/' . $info_item->images) . "' style='width:45px; height:45px'/>";
    			}
    			$str .= "</td>";
    			$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
    			$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
    			$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
    			$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
    			$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
    			$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
    			$str .= "</tr>";
    			$stt ++;
    			$total += $thanh_tien;
    		}
    	} else {
				# export ra file word báo giá
    		foreach ($data['cart'] as $line => $item) {

    			if (!empty($item['pack_id'])) {
    				$info_pack = $this->Pack->get_info($item['pack_id']);
    				$pack_item = $this->Pack_items->get_info($item['pack_id']);
    				$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
    				$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
    				$str .= "<tr>";
    				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
    				$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(GÃ³i SP)</strong><br>";
    				foreach ($pack_item as $val) {
    					$info_item = $this->Item->get_info($val->item_id);
    					$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
    				}

    				$str .= "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
    				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
    				if ($info_pack->images) {
    					$str .= "<img src='" . base_url('packs/' . $info_pack->images) . "' width='20px' height='20px'/>";
    				}
    				$str .= "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . ' ' . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
    				$str .= "</tr>";
    				$total += $thanh_tien;
    			} else {


    				$info_item = $this->Item->get_info($item['item_id']);
    				$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                        //$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
    				$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
    				$str .= "<tr>";
    				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>" . $info_item->name . "</td>";

    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>" . $info_item->item_id . "</td>";

    				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $info_item->don_vi_tinh . "</td>";

    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format((isset($item['unit']) && $item['unit'] == 'unit_from') ? $item['price_rate'] : $item['price']) . "</td>";
    				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
    				$str .= "</tr>";

    				$total += $thanh_tien;
    			}
    			$stt++;
    		}
    	}
           // echo $total;
    	$content1 = "<html>";
    	$content1 .= "<meta charset='utf-8'/>";
    	$content1 .= "<body style='font-size: 100% !important'>";
    	$content1 .= $data['info_quotes_contract']['content_quotes_contract'];
    	$content1 .= "</body>";
    	$content1 .= "</html>";
            // echo $data['info_quotes_contract']['content_quotes_contract'];
            // die;
    	$info_sale = $this->Sale->get_info_sale_order($sale_id);

    	$d = $info_sale->delivery_date != '0000-00-00' ? date('d', strtotime($info_sale->delivery_date)) : '...';
    	$m = $info_sale->delivery_date != '0000-00-00' ? date('m', strtotime($info_sale->delivery_date)) : '...';
    	$y = $info_sale->delivery_date != '0000-00-00' ? date('Y', strtotime($info_sale->delivery_date)) : '...';
    	$content1 = str_replace('{TITLE}', $data['info_quotes_contract']['title_quotes_contract'], $content1);
    	$content1 = str_replace('{TEN_HH}', 'ducanh', $content1);

    	$content1 = str_replace('{TABLE_DATA}', $str, $content1);
    	$content1 = str_replace('{LOGO}', "<img src='" . base_url('images/logoreport/' . $this->config->item('report_logo')) . "'/>", $content1);
    	$content1 = str_replace('{NAME_COMPANY_U}', $this->config->item('company'), $content1);
    	$content1 = str_replace('{ADDRESS_COMPANY}', $this->config->item('address'), $content1);
    	$content1 = str_replace('{TEL_COMPANY}', $this->config->item('phone'), $content1);
    	$content1 = str_replace('{WEBSITE_COMPANY}', $this->config->item('website'), $content1);
    	$content1 = str_replace('{CHUCVU_NCC}', '', $content1);
    	$content1 = str_replace('{TKNH_NCC}', $this->config->item('corp_number_account'), $content1);
    	$content1 = str_replace('{NH_NCC}', $this->config->item('corp_bank_name'), $content1);
    	$content1 = str_replace('{TEN_KH}', $info_sale->ten_khach_hang, $content1);
    	$content1 = str_replace('{DIA_CHI_1_KH}', $info_sale->dia_chi, $content1);
    	$content1 = str_replace('{TONG_TIEN}', number_format($total), $content1);
    	$content1 = str_replace('{VAT}', '0', $content1);
    	$content1 = str_replace('{TONG_DH}', number_format($total), $content1);
    	$content1 = str_replace('{CT_KH}','', $content1);
    	$content1 = str_replace('{SDT_KH}', '', $content1);
    	$content1 = str_replace('{DD_KH}', $data['customer'], $content1);
    	$content1 = str_replace('{TKNH_KH}', $data['code_tax'], $content1);
    	$content1 = str_replace('{NH_KH}', '', $content1);
    	$content1 = str_replace('{CODE}', $sale_id, $content1);
    	$content1 = str_replace('{DATE}', $d, $content1);
    	$content1 = str_replace('{MONTH}', $m, $content1);
    	$content1 = str_replace('{YEAR}', $y, $content1);
    	fwrite($fp, $content1);
    	fclose($fp);
    	$file_url = APPPATH . "excel_materials/" . $file_name;
    	header('Content-Type: application/octet-stream');
    	header("Content-Transfer-Encoding: Binary"); 
    	header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
    	readfile($file_url);
            // echo $file;
        //             var_dump($content1);
        //                    // var_dump($data['cart']);

    	if ($type == 3) {
    		/* phan lam mail */
    		$cust_info = $this->Customer->get_info($customer_id);
    		$content = "<p>Dear anh/chá»‹:" . $data['customer'] . "</p>";
    		$content .= "<p>Dá»±a vÃ o nhu cáº§u cá»§a QuÃ½ khÃ¡ch hÃ ng.</p>";
    		$content .= "<p><b>" . $this->config->item('company') . "</b> xin phÃ©p Ä‘Æ°á»£c gá»­i tá»›i QuÃ½ khÃ¡ch hÃ ng bÃ¡o giÃ¡ chi tiáº¿t nhÆ° sau:</p>";
    		$content .= "<p>Xin vui lÃ²ng xem á»Ÿ file Ä‘Ã­nh kÃ¨m</p>";
    		$content .= "<p><i>Äá»ƒ biáº¿t thÃªm thÃ´ng tin, vui lÃ²ng liÃªn há»‡ Dá»‹ch vá»¥ khÃ¡ch hÃ ng theo sá»‘ Ä‘iá»‡n thoáº¡i: " . $this->config->item("phone") . "</i></p>";
    		$content .= "<i>(Xin vui lÃ²ng khÃ´ng pháº£n há»“i email nÃ y. ÄÃ¢y lÃ  email Ä‘Æ°á»£c tá»± Ä‘á»™ng gá»­i Ä‘i tá»« há»‡ thá»‘ng cá»§a chÃºng tÃ´i).</i>";
    		$content .= "<p>-----</p>";
    		$content .= "<p><i>Thanks and Regards!</i></p>";
    		if (!empty($sale_info['employees_id']) && $sale_info['employees_id'] != 0) {
    			$content .= "<p><i>" . $data['employees_id'] . "</i></p>";
    			$content .= "<p>Mobile: " . $data['phone_number1'] . "</p>";
    			$content .= "<p>Email: " . $data['email1'] . "</p>";
    		} else {
    			$content .= "<p><i>" . $data['employee'] . "</i></p>";
    			$content .= "<p>Mobile: " . $data['phone_number'] . "</p>";
    			$content .= "<p>Email: " . $data['email'] . "</p>";
    		}
    		$content .= "<p style='text-transform: uppercase;'>" . $this->config->item("company") . "</p>";
    		$content .= "<p>Rep Off  :" . $this->config->item('address') . "</p>";
    		$content .= "<p>Email    :" . $this->config->item('email') . "</p>";
    		$content .= "<p>Tel      :" . $this->config->item('phone') . " | Fax: " . $this->config->item('fax') . "</p>";
    		$content .= "<p>Web      :" . $this->config->item('website') . "</p>";
    		$address_list[] = array(
    			'AddAddress' => $cust_info->email,
    			'AddAddress_name' => $cust_info->last_name,
    		);

    		$body_list[] = array($content);

    		$mail['from_name']    = $this->config->item('company');
    		$mail['address_list'] = serialize($address_list);
    		$mail['subject']      = $this->config->item('company') . " xin trÃ¢n trá»ng gá»­i tá»›i khÃ¡ch hÃ ng báº£ng bÃ¡o giÃ¡.";
    		$mail['body']         = serialize($body_list);
    		$mail['type']         = 'sequence';
                //biz_send_mail($mail, $file);
    		biz_send_mail($mail);
    		/* end phan lam mail */
    	} elseif ($type == '1') {
    		$this->load->view("sales/report_quotes", $data);
    	}

    	$this->sale_lib->clear_all();

    }

    function _reload($data = array(), $is_ajax = true)
    {

    	$sale_mode = $this->sale_lib->get_mode();
    	$data['is_tax_inclusive'] = $this->_is_tax_inclusive();
    	if(empty($data['is_add_payment_click']))
    	{
    		$data['is_add_payment_click'] = 0;
    	} 
    	if ($data['is_tax_inclusive'] && count($this->sale_lib->get_deleted_taxes()) > 0)
    	{
    		$this->sale_lib->clear_deleted_taxes();
    	}

    	$person_info = $this->Employee->get_logged_in_employee_info();
    	$modes = array('sale'=>lang('sales_sale'),'return'=>lang('sales_return'));

    	if($this->config->item('customers_store_accounts'))
    	{
    		$modes['store_account_payment'] = lang('sales_store_account_payment');
    	}

    	if($this->config->item('config_vat_order') == 1)
    		$modes['vat_order'] = 'Hóa đơn VAT';

    	$modes['return_by_sales']           = lang('sales_return_by_sales');
    	$data['list_customer']              = $this->Customer->list_item(); 	
    	$data['cart']                       = $this->sale_lib->get_cart();
    	$data['commission_info']            = $this->sale_lib->get_commission_info();
    	$data['modes']                      = $modes;
    	$data['mode']                       = $this->sale_lib->get_mode();
    	$data['more_customers_in_service']  = $this->sale_lib->get_customers();
    	$data['items_in_cart']              = $this->sale_lib->get_items_in_cart();
    	$data['subtotal']                   = $this->sale_lib->get_subtotal();
    	$data['taxes']                      = $this->sale_lib->get_taxes();
    	$data['total']                      = $this->sale_lib->get_total();
    	$data['line_for_flat_discount_item']= $this->sale_lib->get_line_for_flat_discount_item();
    	$data['discount_all_percent']       = $this->sale_lib->get_discount_all_percent();
    	$data['discount_all_fixed']         = $this->sale_lib->get_discount_all_fixed();
    	$data['items_module_allowed']       = $this->Employee->has_module_permission('items', $person_info->person_id);
    	$data['comment']                    = $this->sale_lib->get_comment();
    	$data['show_comment_on_receipt']    = $this->sale_lib->get_comment_on_receipt();
    	$data['email_receipt']              = $this->sale_lib->get_email_receipt();
    	$data['payments_total']             = $this->sale_lib->get_payments_totals_excluding_store_account();
    	$data['selected_payment']           = $this->sale_lib->get_selected_payment();
    	$data['amount_due']                 = $this->sale_lib->get_amount_due();
    	$data['payments']                   = $this->sale_lib->get_payments();
    	$data['change_sale_date_enable']    = $this->sale_lib->get_change_sale_date_enable();
    	$data['change_sale_date']           = $this->sale_lib->get_change_sale_date();
    	$data['selected_tier_id']           = $this->sale_lib->get_selected_tier_id();
    	$data['is_over_credit_limit']       = false;
    	$data['fullscreen']                 = $this->session->userdata('fullscreen');
    	$data['redeem']                     = $this->sale_lib->get_redeem();
    	$data['deliverer']                  = $this->Employee->get_info($this->sale_lib->get_deliverer());
    	$data['supporter']                  = $this->Employee->get_info($this->sale_lib->get_supporter());

    	$data['delivery_date']              = $this->sale_lib->get_delivery_date();
    	$data['cartItemsAttribute']         = $this->sale_lib->getCartItemsAttribute();
    	$data['attributeCartItemsValue']    = $this->sale_lib->getCartItemsAttributeValue();
    	$data['itemsContainsLine']          = $this->sale_lib->getItemContainsLine();
    	$data['saleComment']                = $this->sale_lib->getSaleComments();
    	$sale_total                         = $this->sale_lib->get_total();
    	$payment_total                      = $this->sale_lib->get_payments_totals();
    	$totalItems                         = 0;
    	$totalQty                           = 0;
    	$data['status_list']                  = $this->Sale_status->get_all();
    	$data['supporter_list']               = $this->sale_lib->get_supporter_list();
    	$data['temp_service_id']                   = $this->sale_lib->get_temp_service();
    	$data['status_id']                    = $this->sale_lib->get_sale_status();
    	$data['sale_creator']                 = $this->sale_lib->get_sale_creator();

    	foreach ($data['cart'] as $item) {
    		$totalQty += $item['quantity'];
    		$totalItems ++;
    	}

    	$data['total_items'] = $totalItems;
    	$data['total_qty'] = $totalQty;		

    	$customer_id=$this->sale_lib->get_customer();

    	if ($customer_id!=-1)
    	{
    		$cust_info=$this->Customer->get_info($customer_id);
    	}

    	$data['prompt_for_card'] = $this->sale_lib->get_prompt_for_card();
    	$data['cc_processor_class_name'] = $this->_get_cc_processor() ? strtoupper(get_class($this->_get_cc_processor())) : '';

    	$location_sale_employees = $this->Location->get_employee_list(array('location_id'=>$this->Employee->get_logged_in_employee_current_location_id(), 'group_id'=>1));

    	if ($this->config->item('select_sales_person_during_sale'))
    	{
    		$employees = array('' => lang('common_not_set'));


    		if(!empty($location_sale_employees['employee_list'])) {
    			foreach($location_sale_employees['employee_list'] as $item)
    				$employees[$item['id']] = $item['name'];
    		}

    		$data['employees'] = $employees;
    		$data['selected_sold_by_employee_id'] = $this->sale_lib->get_sold_by_employee_id();
    	}

    	$sale_change_id    = $this->sale_lib->get_change_sale_id();
    	$data['sale_change_id'] = $sale_change_id;
    	$suspended_sale_id = $this->sale_lib->get_suspended_sale_id();

        //payment history
    	if($sale_change_id > 0 || $suspended_sale_id > 0) {
    		$sale_or_change_id = ($sale_change_id > 0) ? $sale_change_id : $suspended_sale_id;
            $this->sale_lib->set_sale_payment_history($sale_or_change_id); // Debt payment history
        }
        
        $data['sale_payment_history'] = $this->sale_lib->get_sale_payment_history();
        
//         if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0 ) {
//             if($sale_change_id > 0 || $suspended_sale_id > 0) {
//                 if($sale_change_id > 0)
//                     $location_groups = $this->Sale->get_employee_list_from_sale(array('sale_id' => $sale_change_id));

//                 if($suspended_sale_id > 0)
//                     $location_groups = $this->Sale->get_employee_list_from_sale($suspended_sale_id);

//                 $location_groups_two = $this->Location->get_location_group_employees($this->Employee->get_logged_in_employee_current_location_id(), array('active'=>true, 'task'=>'change_sale_id'));

//                 foreach($location_groups_two as $key => $val) {
//                     if(!isset($location_groups[$key])) {
//                         $location_groups[$key] = array(
//                             'group_id' => $val['group_id'],
//                             'location_id' => $val['location_id'],
//                             'group_name' => $val['group_name'],
//                             'list' => array()
//                         );
//                     }
//                 }

//                 $data['location_groups'] = $location_groups;

//                 $this->sale_lib->set_group_employees($location_groups, array('task'=>'change_sale'));

                // remove sale information if sale mode = store_account_payment or vat_order
//                 if($sale_mode == 'store_account_payment' || $sale_mode == 'vat_order') {
//                     $this->sale_lib->clear_all();
//                     redirect();
//                 }
//             }else {
//                 $location_groups = $this->Location->get_location_group_employees($this->Employee->get_logged_in_employee_current_location_id(), array('active'=>true));

//                 if($this->sale_lib->get_mode() == 'store_account_payment') {
//                     foreach($location_groups as $key => $val) {
//                         if($key != 1)
//                             unset($location_groups[$key]);
//                     }
//                 }
//                 $data['location_groups'] = $location_groups;
//                 $this->sale_lib->set_group_employees($location_groups);

//                 $sale_vat_relationship = $this->sale_lib->get_sale_vat_relationship();
//                 if(!empty($sale_vat_relationship)) {
//                     $comment = array();
//                     $sale_order_relationship_items = $this->Sale->get_items(array('sale_ids'=>$sale_vat_relationship));
//                     foreach($sale_order_relationship_items as $val) {
//                         if(!empty($val['code']))
//                             $comment[] = $val['code'];
//                         else
//                             $comment[] = $this->config->item('sale_prefix') . ' ' . $val['sale_id'];
//                     }
//                     $data['comment'] = implode(', ', $comment);
//                 }else
//                     $data['comment'] = '';
//             }
//         }
        
        $tiers = array();

        $tiers[0] = lang('common_none');
        foreach($this->Tier->get_all()->result() as $tier)
        {
        	$tiers[$tier->id]=$tier->name;
        }

        $data['tiers'] = $tiers;

        if ($this->Location->get_info_for_key('enable_credit_card_processing'))
        {
        	$data['payment_options']=array(
        		lang('common_cash') => lang('common_cash'),
        		lang('common_check') => lang('common_check'),
        		lang('common_credit') => lang('common_credit'),
        		lang('common_giftcard') => lang('common_giftcard'));

        	if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment')
        	{
        		$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_store_account') => lang('common_store_account')
        	));
        	}

        	if($payment_total>$sale_total)
        	{
        		$data['payment_options'] = array(	
        			lang('common_refund_money') =>lang('common_refund_money'),
        			lang('common_debt_customer') =>lang('common_debt_customer')
        		);
        	}
        	if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
        	{
        		$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_points') => lang('common_points')));
        	}
        }
        else
        {
        	$data['payment_options']=array(
        		lang('common_cash') => lang('common_cash'),
        		lang('common_check') => lang('common_check'),
        		lang('common_giftcard') => lang('common_giftcard'),
        		lang('common_debit') => lang('common_debit'),
        	);

        	if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment')
        	{
        		$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_store_account') => lang('common_store_account')
        	));
        	}

        	if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
        	{
        		$data['payment_options']=array_merge($data['payment_options'],	array(lang('common_points') => lang('common_points')));
        	}
        	if($payment_total>$sale_total)
        	{
        		$data['payment_options'] = array(	
        			lang('common_refund_money')  =>lang('common_refund_money'),
        			lang('common_debt_customer') =>lang('common_debt_customer')

        		);
        	}

        }

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
        
        
        $flag_remove_payment_options = false;
        
        if($sale_mode == 'return' || $sale_mode == 'return_by_sales') {
        	$flag_remove_payment_options = true;
        }
        
        if($sale_mode == 'store_account_payment') {
        	$store_account_payment_value = $this->sale_lib->get_store_account_payment_value();
        	if($store_account_payment_value == 2) {
        		$flag_remove_payment_options = true;
        	}
        }
        
        if($flag_remove_payment_options == true) {
        	if(($key = array_search('Thẻ quà tặng', $data['payment_options'])) !== false) {
        		unset($data['payment_options'][$key]);
        	}

        	if(($key = array_search('Ðiểm', $data['payment_options'])) !== false) {
        		unset($data['payment_options'][$key]);
        	}
        }
        
        if($sale_mode == 'vat_order') {
        	if(($key = array_search('Sổ ghi nợ', $data['payment_options'])) !== false) {
        		unset($data['payment_options'][$key]);
        	}
        }
        
        if($customer_id!=-1)
        {
        	$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '' :' ('.$cust_info->company_name.')');
        	$data['customer_email']=$cust_info->email;
        	$data['customer_balance'] = $cust_info->balance;
        	$data['customer_balance_2'] = $cust_info->balance_2;
        	$data['customer_credit_limit'] = $cust_info->credit_limit;
        	$data['is_over_credit_limit'] = $this->sale_lib->is_over_credit_limit();
        	$data['customer_id']=$customer_id;
        	$data['customer_cc_token'] = $cust_info->cc_token;
        	$data['customer_cc_preview'] = $cust_info->cc_preview;
        	$data['save_credit_card_info'] = $this->sale_lib->get_save_credit_card_info();
        	$data['use_saved_cc_info'] = $this->sale_lib->get_use_saved_cc_info();
			$data['avatar']=$cust_info->image_id ?  site_url('app_files/view/'.$cust_info->image_id) : base_url()."assets/img/user.png"; //can be changed to  base_url()."img/avatar.png" if it is required

			$data['points'] = to_currency_no_money($cust_info->points);
			$data['sales_until_discount'] = $this->config->item('number_of_sales_for_discount') - $cust_info->current_sales_for_discount;
		}
		$data['customer_required_check'] = (!$this->config->item('require_customer_for_sale') || ($this->config->item('require_customer_for_sale') && isset($customer_id) && $customer_id!=-1));
		$data['suspended_sale_customer_required_check'] = (!$this->config->item('require_customer_for_suspended_sale') || ($this->config->item('require_customer_for_suspended_sale') && isset($customer_id) && $customer_id!=-1));
		$data['payments_cover_total'] = $this->_payments_cover_total();

		$data['discount_editable_placement'] = $this->agent->is_mobile() && !$this->agent->is_tablet() ? 'top' : 'left';

		$saleId = $this->sale_lib->get_suspended_sale_id() ? $this->sale_lib->get_suspended_sale_id() : 0;

		$sale_change_id = $this->sale_lib->get_change_sale_id();
		if($sale_change_id > 0) {
			$saleInfo = $this->Sale->get_info($sale_change_id)->row();
		}else
		$saleInfo = $this->Sale->get_info($saleId)->row();

		if(!isset($_SESSION['sale_time_date'])) {
			if(!empty($saleInfo)) {
				$sale_time = date('d-m-Y H:i:s', strtotime($saleInfo->sale_time));
				$this->sale_lib->set_sale_time_date($sale_time);
			} else {

				$sale_time = date('d-m-Y H:i:s', time());
				$this->sale_lib->set_sale_time_date($sale_time);
			}
		}
        // var_dump($_SESSION['sale_time_date']);
		$data['isStockOut'] = !empty($saleInfo->is_stock_out) ? $saleInfo->is_stock_out : 0;
		
		if (!empty($saleId)) {
			$stockOutItems = $this->StockOut->getStockOutItems($saleId);
			$data['stockOutItems'] = $stockOutItems;
		}

		if($sale_change_id > 0) {
			$contract_info = $this->Contract->get_item_by_sale($sale_change_id);
			if(!empty($contract_info)) {
				$data['contract_name'] = $contract_info['name'];
			}
			$this->sale_lib->set_service_id($saleInfo->service_id);
		}

		$data['service_id']   = $this->sale_lib->get_service_id();
		
		if($this->sale_lib->get_mode() == 'sale'){
			if($sale_change_id > 0) {
				$params = array();
				if($data['service_id'] > 0)
					$params['or_ids'] = array($data['service_id']);

				$data['service_list'] = $this->Service->list_item($params, array('task'=>'all'));
			}else {
				if($this->config->item('sale_order_has_service') == 1)
					$data['service_list'] = $this->Service->list_item(null, array('task'=>'all'));
			}
		}

		if ($is_ajax) {  
			if ($this->input->post('itemContainsLine') || 
				$this->input->post('ajax_reload') == true) {
				echo json_encode($this->load->view("sales/register",$data, true)); 
		} else {
			switch ($sale_mode) {
				case 'return_by_sales':
				$this->load->view("sales/register_return_by_sales",$data);
				break;
				default:
				$this->load->view("sales/register",$data);
			}
		}
	}
	else
	{
		if (!empty($data['action']) && $data['action'] == 'view') {
			return $this->load->view("sales/view", $data);
		}
		$this->load->view("sales/register_initial",$data);
	}
}

function report_contract($sale_id)
{
	$data                   = array();
	$data['sale_id']        = $sale_id;
	$data['list_contract']  = $this->Customer->get_list_template_quotes_contract(1);
	$customer_id            = $this->sale_lib->get_customer();
	$cust_info              = $this->Customer->get_info($customer_id);
	$data['email']          = $cust_info->email;
	
	$this->load->view('sales/form_report_contract', $data);
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

		$data = [];


		if ($return == 'sales')
		{
			$data['next_url'] = site_url('sales');
		} elseif ($return == 'closeregister')
		{
			$data['next_url'] = site_url('sales/closeregister?continue=logout');
		}

		$data['id'] = $cash_register->register_log_id;
		$data['amount'] = $amount;
		$data['mode'] = $mode;
		$data['note'] = $this->input->post('note');
		$typeOfPrint = 'added_A4.php';
		$data['print_block_html'] = $this->load->view('sales/partials/cash_drawer/' . $typeOfPrint, $data, TRUE);
		$this->load->view('sales/cash_added_drawer', $data);
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


	//add report quotes contract
function report_quotes_contract_all($id = -1){
	$data['qc_type'] = $this->Customer->get_all_quotes_contract_type();
	$data['sale_id'] = $id;

	if ($id == -1) {
		$data['sale_id'] 	= $_GET['sale_id'];
		$data['ds_id'] 		= $_GET['ds'];			
	}else{
		$data['ds_id'] = 0;
	}

	$this->load->view('sales/form_report_quotes_contract_all', $data);
}

function load_template_mail() {
	$this->load->library('sale_lib');
	$post = $this->input->post();
	if(!empty($post)) {
		$item = $this->Customer->constract_list(array('id_quotes_contract'=>$post['template_id']));
		if(!empty($item[0]['content'])) {
			$data = $this->sale_lib->getSale($post['sale_id'], $this->config);
			$html_string = $this->convertTemplate($data, $item[0]['content']);
		}else
		$html_string = '';

		echo $html_string;
	}

}

function validate_email($value) {
	$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
	if (preg_match($pattern, $value) === 1) {
		return true;
	}else{
		$this->form_validation->set_message('validate_email', "Sai định dạng email");
		return false;
	}

}

function send_sale_mail() {
	$this->load->library('email');
	$this->load->helper('filterext');
	$this->load->library('sale_lib');

	$fileError = array(
		'<p>The filetype you are attempting to upload is not allowed.</p>'=>lang('file_error_format'),
		'<p>The file you are attempting to upload is larger than the permitted size.</p>' => 'File táº£i lÃªn khÃ´ng Ä‘Æ°á»£c quÃ¡ 20 Mb'
	);

	$post = $this->input->post();
	$upload_dir = DOCUMENT_PATH . 'files/store_' . $this->session->userdata('person_id');

	if(!is_dir($upload_dir)) {
		mkdir($upload_dir,0777,true);
	}

	if(!empty($post)) {
		$post['content_email'] = trim($post['content_email']);
		$post['email']         = trim($post['email']);
		$this->input->post     = $post;
		$arrParam = array_merge($post, $this->input->get());
		$flagError = false;
		$this->sale_lib->copy_entire_sale($post['sale_id'], true);						
		$this->form_validation->set_rules('title', 'TiÃªu Ä‘á»', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|callback_validate_email');
		$this->form_validation->set_rules('content_email', 'Nội dung mail', 'required');

		if($_FILES["file_upload"]['name'] != ""){
			$this->form_validation->set_rules('file_name', 'TÃªn File', 'required');
		}

		if($this->form_validation->run($this) == FALSE){
			$errors = $this->form_validation->error_array();
			$flagError = true;
		}else {
			$sale_info = $this->sale_lib->getSale($post['sale_id'], $this->config);
			if($_FILES["file_upload"]['name'] != ""){
				$ext = pathinfo($_FILES["file_upload"]['name'], PATHINFO_EXTENSION);
				$file_name = rewriteUrl($post['file_name']);

				$config['upload_path'] = $upload_dir;
				$config['allowed_types'] = 'jpg|png|pdf|docx|doc|xls|xlsx|zip|zar|txt';
				$config['max_size']	= '20480';
				$config['encrypt_name'] = FALSE;

				$config['file_name'] = $file_name . '.' . $ext;
				if (file_exists($upload_dir . '/' . $file_name . '.' . $ext)) {
					$config['file_name'] = $file_name . time() . '.' . $ext;
				}

				$this->load->library('upload', $config);

				if($this->upload->do_upload("file_upload")){
					$file_info             = $this->upload->data();
					$arrParam['size']      = $_FILES['file_upload']['size'];
					$arrParam['extension'] = $ext;
					$arrParam['file_name'] = $config['file_name'];

				}else{
					$flagError = true;
					$err = $this->upload->display_errors();
					if(isset($fileError[$err]))
						$errors['file_upload'] = $fileError[$err];
					else
						$errors['file_upload'] = strip_tags($err);
				}
			}

			$address_list[] = array(
				'AddAddress' => $post['email'],
				'AddAddress_name' => $sale_info['customer'],
			);

			$body_list[] = $post['content_email'];

			$mail['from_name']    = $this->config->item('company');
			$mail['address_list'] = serialize($address_list);
			$mail['subject']      = $post['title'];
			$mail['body']         = serialize($body_list);
			$mail['type']         = 'sequence';

			if($_FILES["file_upload"]['name'] != "")  {
				$mail['file_name']    = $arrParam['file_name'];
				biz_send_mail($mail, $_FILES);
			}else
			biz_send_mail($mail);
		}

		if($flagError == false){
			$file_save = !empty($config['file_name']) ? $config['file_name'] : '';
			$extension = !empty($ext) ?$ext : '';

			$data_history[] = array(
				'person_id'    => $sale_info['customer_id'],
				'employee_id'  => $this->session->userdata('person_id'),
				'email'        => $post['email'],
				'title' 	   => $post['title'],
				'content' 	   => $post['content_email'],
				'note'				 =>empty($sale_info['code'])?'BAOGIA|'.$sale_info['sale_id'].'|'.$this->sale_lib->get_total($post['sale_id']):'BAOGIA|'.$sale_info['code'].'|'.$this->sale_lib->get_total($post['sale_id']),																				
				'time'		   => date('Y-m-d H:i:s'),
				'file' 	       => $file_save,
				'extension'    => $extension,
				'status' 	   => 1,
			);

			$this->Customer->save_mail_history($data_history, array('task'=>'update-multi'));

			$_SESSION['notice'] = 'Gửi mail thành công.';
			echo json_encode(array('flag'=>'true'));
		}else {
			echo json_encode(array('flag'=>'false', 'errors' => $errors));
		}
	}
}

function suspended_email($sale_id) {
	$data = array();
	$data['sale_id'] = $sale_id;

	$data['sale_info'] = $this->sale_lib->getSale($sale_id, $this->config);
	$data['list_mail'] = $this->Customer->constract_list(array());

	$this->load->helper('ckeditor');
        #Ckeditor's configuration
	$data['ckeditor'] = array(
        #ID of the textarea that will be replaced
		'id' => 'content_email',
		'path' => 'assets/js/biz/ckeditor/',
		'value' => isset($_POST['content_quotes_contract']) ? $_POST['content_quotes_contract'] : '',
        #Optionnal values
		'config' => array(
        'toolbar' => "Full",    #Using the Full toolbar
        'width' 	=> "100%",     #Setting a custom width
        'height' 	=> '500px',    #Setting a custom height
        'language'=> 'vi',
        'filebrowserBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html',
        'filebrowserImageBrowseUrl' => base_url() . 'assets/js/biz/ckfinder/ckfinder.html?Type=Images',
        'filebrowserImageUploadUrl' => base_url() . 'assets/js/biz/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
    ),
        #Replacing styles from the "Styles tool"
		'styles' => array(
        #Creating a new style named "style 1"
			'style 1' => array(
				'name' => 'Blue Title',
				'element' => 'h2',
				'styles' => array(
					'color' => 'Blue',
					'font-weight' => 'bold'
				)
			),
        #Creating a new style named "style 2"
			'style 2' => array(
				'name' => 'Red Title',
				'element' => 'h2',
				'styles' => array(
					'color' => 'Red',
					'font-weight' => 'bold',
					'text-decoration' => 'underline'
				)
			)
		)
	);																
	$this->load->view('sales/suspended_email', $data);
}

	//get id val quotes contract all
function quotes_contract_type_get($id = -1){
	$area_id = $this->input->post('area');
	$areas =  $this->Customer->get_area_list($area_id);

	$this->load->view('sales/quotes_contract_type_get', array('areas'=>$areas));
}

function export_constract() {


$sale_id = $this->input->get('sale_id');
$id = $this->input->get('select_quote');
$ext = '.docx';
// // var_dump($sale_id);die();
if(!empty($sale_id)){
	 $sale = $this->getSale($sale_id);
	 $file = 'document/bieumau/'.$id.$ext;

  	// var_dump($file);die();
	$phpword = new \PhpOffice\PhpWord\TemplateProcessor($file);
	// var_dump($phpword);die();
	$phpword->setValue('{TEN_KH}',$sale['customer']);
	$phpword->setValue('{CT_KH}',$sale['customer_company_name']);
	$phpword->setValue('{DIA_CHI_1_KH}',$sale['customer_address_1']);
	$phpword->setValue('{DIA_CHI_2_KH}',$sale['customer_address_2']);
	$phpword->setValue('{SDT_KH}',$sale['customer_phone']);
	$phpword->setValue('{CHUCVU_KH}',isset($sale['customer_position'])?$sale['customer_position']:'');
	$phpword->setValue('{TKNH_KH}',isset($data['customer_account_number'])?$data['customer_account_number']:'');
	$phpword->setValue('{CT_KH}',$sale['customer_email']);
	$phpword->saveAs('document/edited'.$ext);
}
else{
	$file = 'document/bieumau/'.$id.$ext;
	$phpword = new \PhpOffice\PhpWord\TemplateProcessor($file);
	$phpword->setValue('{TEN_KH}','...');
	$phpword->setValue('{CT_KH}','...');
	$phpword->setValue('{DIA_CHI_1_KH}','...');
	$phpword->setValue('{DIA_CHI_2_KH}','...');
	$phpword->setValue('{SDT_KH}','...');
	$phpword->setValue('{CHUCVU_KH}','...');
	$phpword->setValue('{TKNH_KH}','...');
	$phpword->setValue('{CT_KH}','...');
	$phpword->setValue('{DD_KH}','...');
	$phpword->setValue('{DIA_CHI_KH}','...');
	$phpword->saveAs('document/edited'.$ext);
}




  
	// var_dump('document/edited'.$ext);die();
	force_download(FCPATH.'document/edited'.$ext,null);
	// $post = $this->input->post();
	// if(!empty($post)) {
	// 	if($post['select_quotes_contract'] == 0) 
	// 		$errors['select_quotes_contract'] = lang('sale_select_quotes_contract_required');
	// 	elseif($post['select_quote'] == 0)
	// 		$errors['select_quote'] = lang('sale_select_quote_required');
		
	// 	if(!empty($errors)) {
	// 		$respon = array('flag'=>'false', 'errors'=>$errors);
	// 	}else {
	// 		$respon = array('flag'=>'true', 'quote'=> $post['select_quote'], 'sale'=> $post['sale_id'], 'msg'=>lang('sale_export_file'), 'ds_id' => $post['ds_id_hd']);
	// 	}

	// 	echo json_encode($respon);

	// }
}

function do_make_quotes_contract_type() {

	
	$sale_id 	  = $this->input->get('sale');
	$constract_id = $this->input->get('quote');
	if ($sale_id!=1) {
		$data 		  = $this->getSale($sale_id);
	}

		// echo $this->db->last_query(); die();
	// echo "<pre>"; print_r($data); die();
		// 
		// var_dump($sale_id) ; die();

		//L?y thông tin h?p d?ng
	if ($this->input->get('ds_id')) {
		$data['ds_id'] = $this->input->get('ds_id');
		$data['sale'] = $this->input->get('sale');
	}
		// echo "<pre>"; var_dump($data); die();
	$this->load->model('QuotesConstract');
	$item 					= $this->QuotesConstract->getItem($constract_id);
	$item['alias_title'] 	= rewriteUrl($item['title_quotes_contract']);

	$content_string 		= $item['content_quotes_contract'];
	$html_string 			= $this->convertTemplate($data, $content_string);

	$this->load->view("sales/report_contract_all", array('html_string'=>$html_string, 'alias_title'=>$item['alias_title']));
	
}

function do_make_contract($sale_id) {
	$id_quotes_contract = $this->input->get("contract");
	$data['info_quotes_contract'] = $this->Customer->get_info_quotes_contract($id_quotes_contract);
	$data['is_sale'] = FALSE;
	$sale_info = $this->Sale->get_info($sale_id)->row_array();
	$this->sale_lib->copy_entire_sale($sale_id);
	$data['cart'] = $this->sale_lib->get_cart();
	$data['payments'] = $this->sale_lib->get_payments();
	$data['subtotal'] = $this->sale_lib->get_subtotal();
	$data['taxes'] = $this->sale_lib->get_taxes($sale_id);
	$data['total'] = $this->sale_lib->get_total($sale_id);
	$data['receipt_title'] = lang('sales_receipt');
	$data['comment'] = $this->Sale->get_comment($sale_id);
	$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
	$data['transaction_time'] = date(get_date_format() . ' ' . get_time_format(), strtotime($sale_info['sale_time']));
	$customer_id = $this->sale_lib->get_customer();
	$emp_info = $this->Employee->get_info($sale_info['employee_id']);
	
	$data['payment_type'] = $sale_info['payment_type'];
	$data['amount_change'] = $this->sale_lib->get_amount_due($sale_id) * -1;
	$data['employee'] = $emp_info->first_name . ' ' . $emp_info->last_name;
	$data['phone'] = $emp_info->phone_number;
	$data['email'] = $emp_info->email;
	$data['ref_no'] = $sale_info['cc_ref_no'];
	$this->load->helper('string');
	$data['payment_type'] = str_replace(array('<sup>VNÃ?</sup><br />', ''), ' .VNÃ?', $sale_info['payment_type']);
	$data['amount_due'] = $this->sale_lib->get_amount_due();
	foreach ($data['payments'] as $payment_id => $payment) {
		$payment_amount = $payment['payment_amount'];
	}
	$k = 28;
	$tongtienhang = 0;
	foreach (array_reverse($data['cart'], true) as $line => $item) {
		$tongtienhang_1 += $item['price'] * $item['quantity'] - $item['price'] * $item['quantity'] * $item['discount'] / 100;
		$k++;
	}
	$payments_cost = $tongtienhang_1 - $payment_amount;
	if ($customer_id != -1) {
		$cust_info = $this->Customer->get_info($customer_id);
		$data['customer'] = $cust_info->first_name . ' ' . $cust_info->last_name;
		$data['cus_name'] = $cust_info->company_name == '' ? '' : $cust_info->company_name;
		$data['code_tax'] = $cust_info->code_tax;
		$data['address'] = $cust_info->address_1;
		$data['account_number'] = $cust_info->account_number;
		$data['positions'] = $cust_info->positions;
	}
	$data['sale_id'] = $sale_id;
	
	$type = $this->input->post('contract_type');
	$data['word'] = $type;
	$data['cat_baogia'] = '';
	
	if ($type == '1') {
		$this->load->view("sales/report_contract", $data);
		header("Refresh:0");
	} elseif ($type == '3') {
		$file_name = "HD_" . $sale_id . "_" . str_replace(" ", "", replace_character($data['customer'])) . "_" . date('dmYHis') . ".doc";

		if (!file_exists(APPPATH. '/excel_materials')) {
			mkdir(APPPATH. '/excel_materials/', 0777, true);
		}
		$fp = fopen(APPPATH . "/excel_materials/" . $file_name, 'w+');
		$arr_item = array();
		$arr_service = array();
		foreach ($data['cart'] as $line => $val) {
			if ($val['item_id']) {
				$info_item = $this->Item->get_info($val['item_id']);
				if ($info_item->service == 0) {
					$arr_item[] = array(
						'item_id' => $val['item_id'],
						'line' => $line,
						'name' => $val['name'],
						'item_number' => $val['item_number'],
						'description' => $val['description'],
						'serialnumber' => $val['serialnumber'],
						'allow_alt_description' => $val['allow_alt_description'],
						'is_serialized' => $val['is_serialized'],
						'quantity' => $val['quantity'],
						'stored_id' => $val['stored_id'],
						'discount' => $val['discount'],
						'price' => $val['price'],
						'price_rate' => $val['price_rate'],
						'taxes' => $val['taxes'],
						'unit' => $val['unit']
					);
				} else {
					$arr_service[] = array(
						'item_id' => $val['item_id'],
						'line' => $line,
						'name' => $val['name'],
						'item_number' => $val['item_number'],
						'description' => $val['description'],
						'serialnumber' => $val['serialnumber'],
						'allow_alt_description' => $val['allow_alt_description'],
						'is_serialized' => $val['is_serialized'],
						'quantity' => $val['quantity'],
						'stored_id' => $val['stored_id'],
						'discount' => $val['discount'],
						'price' => $val['price'],
						'price_rate' => $val['price_rate'],
						'taxes' => $val['taxes'],
						'unit' => $val['unit']
					);
				}
			} else {
				$arr_item[] = array(
					'pack_id' => $val['pack_id'],
					'line' => $val['line'],
					'pack_number' => $val['pack_number'],
					'name' => $val['name'],
					'description' => $val['description'],
					'quantity' => $val['quantity'],
					'discount' => $val['discount'],
					'price' => $val['price'],
					'taxes' => $val['taxes'],
					'unit' => $val['unit']
				);
			}
		}
		$str .= "<table style='width: 100%; border-collapse: collapse'>";
		$str .= "<tr>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 5%'>STT</th>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 30%'>TÃªn hÃ ng</th>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 5%'>Ã?VT</th>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 8%'>SL</th>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Ä?Æ¡n giÃ¡</th>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>CK(%)</th>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Thuáº¿(%)</th>";
		$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>ThÃ nh tiá»?n</th>";
		$str .= "</tr>";

		$stt = 1;
		$total = 0;
		if ($cat_hopdong == 1) {
			foreach ($arr_item as $line => $item) {
				if ($item['pack_id']) {
					$info_pack = $this->Pack->get_info($item['pack_id']);
					$pack_item = $this->Pack_items->get_info($item['pack_id']);
					$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
						//$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
					$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
					$str .= "<tr>";
					$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
					$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(GÃ³i SP)</strong><br>";
					foreach ($pack_item as $val) {
						$info_item = $this->Item->get_info($val->item_id);
						$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
					}

					$str .= "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
					$str .= "</tr>";
					$total += $thanh_tien;
				} else {
					$info_item = $this->Item->get_info($item['item_id']);
					$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
						//$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
					$thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
					$str .= "<tr>";
					$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
					$str .= "</tr>";
					$total += $thanh_tien;
				}
				$stt++;
			}
		} else if ($cat_hopdong == 2) {
			foreach ($arr_service as $line => $item) {
				$info_item = $this->Item->get_info($item['item_id']);
				$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
					//$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
				$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
				$str .= "<tr>";
				$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
				$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
				$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
				$str .= "</tr>";
				$total += $thanh_tien;
				$stt++;
			}
		} else {
			foreach ($data['cart'] as $line => $item) {
				if ($item['pack_id']) {
					$info_pack = $this->Pack->get_info($item['pack_id']);
					$pack_item = $this->Pack_items->get_info($item['pack_id']);
					$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
						//$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
					$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
					$str .= "<tr>";
					$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
					$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(GÃ³i SP)</strong><br>";
					foreach ($pack_item as $val) {
						$info_item = $this->Item->get_info($val->item_id);
						$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
					}

					$str .= "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
					$str .= "</tr>";
					$total += $thanh_tien;
				} else {
					$info_item = $this->Item->get_info($item['item_id']);
					$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
						//$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
					$thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
					$str .= "<tr>";
					$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
					$str .= "</tr>";
					$total += $thanh_tien;
				}
				$stt++;
			}
		}
		$str .= "<tr>";
		$str .= "<td colspan='3' style='text-align: center; font-weight: bold; border: 1px solid #000000; padding: 10px 5px'>T?ng</td>";
		$str .= "<td colspan='5' style='text-align: right; font-weight: bold; border: 1px solid #000000; padding: 10px 5px'>" . number_format($total) . "</td>";
		$str .= "</tr>";
		$str .= "</table>";
		$str .= "<p>Tá»•ng giÃ¡ trá»‹ (Báº±ng chá»¯): <strong><em>" . $total . "</em></strong></p>";
		$content1 = "<html>";
		$content1 .= "<meta charset='utf-8'/>";
		$content1 .= "<body style='font-size: 100% !important'>";
		$content1 .= $data['info_quotes_contract']->content_quotes_contract;
		$content1 .= "</body>";
		$content1 .= "</html>";
		$info_sale = $this->Sale->get_info_sale_order($sale_id);
		$d = $info_sale->date_debt != '0000-00-00' ? date('d', strtotime($info_sale->date_debt)) : '...';
		$m = $info_sale->date_debt != '0000-00-00' ? date('m', strtotime($info_sale->date_debt)) : '...';
		$y = $info_sale->date_debt != '0000-00-00' ? date('Y', strtotime($info_sale->date_debt)) : '...';
		$content1 = str_replace('{TITLE}', $data['info_quotes_contract']->title_quotes_contract, $content1);
		$content1 = str_replace('{TABLE_DATA}', $str, $content1);
		$content1 = str_replace('{LOGO}', "<img src='" . base_url('images/logoreport/' . $this->config->item('report_logo')) . "'/>", $content1);
		$content1 = str_replace('{TEN_NCC}', $this->config->item('company'), $content1);
		$content1 = str_replace('{DIA_CHI_NCC}', $this->config->item('address'), $content1);
		$content1 = str_replace('{SDT_NCC}', $this->config->item('phone'), $content1);
		$content1 = str_replace('{DD_NCC}', $this->config->item('corp_master_account'), $content1);
		$content1 = str_replace('{CHUCVU_NCC}', '', $content1);
		$content1 = str_replace('{TKNH_NCC}', $this->config->item('corp_number_account'), $content1);
		$content1 = str_replace('{NH_NCC}', $this->config->item('corp_bank_name'), $content1);
		$content1 = str_replace('{TEN_KH}', $data['cus_name'], $content1);
		$content1 = str_replace('{DIA_CHI_KH}', $data['address'], $content1);
		$content1 = str_replace('{SDT_KH}', '', $content1);
		$content1 = str_replace('{DD_KH}', $data['customer'], $content1);
		$content1 = str_replace('{CHUCVU_KH}', $data['positions'], $content1);
		$content1 = str_replace('{TKNH_KH}', $data['code_tax'], $content1);
		$content1 = str_replace('{NH_KH}', '', $content1);
		$content1 = str_replace('{CODE}', $sale_id, $content1);
		$content1 = str_replace('{DATE}', $d, $content1);
		$content1 = str_replace('{MONTH}', $m, $content1);
		$content1 = str_replace('{YEAR}', $y, $content1);
		fwrite($fp, $content1);
		fclose($fp);
		/* phan lam mail */
		$cust_info = $this->Customer->get_info($customer_id);
		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => $this->config->item('config_email_account'),
			'smtp_pass' => $this->config->item('config_email_pass'),
			'charset' => 'utf-8',
			'mailtype' => 'html'
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from($this->config->item('email'), $this->config->item('company'));

		$this->email->to('daominhtam.mt@gmail.com');

		$this->email->subject($this->config->item('company') . " xin trÃ¢n trá»?ng gá»­i tá»›i quÃ½ khÃ¡ch há»£p Ä‘á»“ng");
		$content = "<p>Dear anh/ch?:" . $data['customer'] . "</p>";
		$content .= "<p>D?a vÃ o nhu c?u c?a QuÃ½ khÃ¡ch hÃ ng.</p>";
		$content .= "<p><b>" . $this->config->item('company') . "</b> xin phÃ©p Ä‘Æ°á»£c gá»­i tá»›i quÃ½ khÃ¡ch chi tiáº¿t há»£p Ä‘á»“ng nhÆ° sau:</p>";
		$content .= "<p>Xin vui lÃ²ng xem ? file dÃ­nh kÃ¨m</p>";
		$content .= "<p><i>Ä?á»ƒ biáº¿t thÃªm thÃ´ng tin, vui lÃ²ng liÃªn há»‡ dá»‹ch vá»¥ theo sá»‘ Ä‘iá»‡n thoáº¡i: " . $this->config->item("phone") . "</i></p>";
		$content .= "<i>(Xin vui lÃ²ng khÃ´ng ph?n h?i email nÃ y. Ã?Ã¢y lÃ  email Ä‘Æ°á»£c gá»­i Ä‘i tá»« chÃºng tÃ´i).</i>";
		$content .= "<p>-----</p>";
		$content .= "<p><i>Thanks and Regards!</i></p>";
		$content .= "<p><i>" . $data['employee'] . "</i></p>";
		$content .= "<p>Mobile: " . $data['phone'] . "</p>";
		$content .= "<p>Email: " . $data['email'] . "</p>";

		$content .= "------------------------------------------------------------------------";
		$content .= "<img src='" . base_url() . "images/logoreport/11.png'>";
		$content .= "<p style='text-transform: uppercase;'>" . $this->config->item("company") . "</p>";
		$content .= "<p>Rep Off  :" . $this->config->item('address') . "</p>";
		$content .= "<p>Email    :" . $this->config->item('email') . "</p>";
		$content .= "<p>Tel      :" . $this->config->item('phone') . " | Fax: " . $this->config->item('fax') . "</p>";
		$content .= "<p>Web      :" . $this->config->item('website') . "</p>";
		$this->email->message($content);
		$file = APPPATH . "/../excel_materials/" . $file_name;
		$this->email->attach($file);
		if ($this->email->send()) {
			$send_success[] = $cust_info->email;
			$data_history = array(
				'person_id' => $customer_id,
				'employee_id' => $this->session->userdata('person_id'),
				'title' => 'H?p d?ng',
				'content' => $content,
				'time' => date('Y-m-d H:i:s'),
				'file' => $file_name,
				'status' => 1,
			);
			$this->Customer->add_mail_history($data_history);
			$this->sale_lib->clear_all();
			$_SESSION['send_ok'] = 'Gá»­i mail thÃ nh cÃ´ng';
			$this->session->set_flashdata('send_mail_cutomers', 'Gá»­i mail thÃ nh cÃ´ng');
			redirect('sales');
		} else {
			$send_fail[] = $cust_info->email;
			$data_history = array(
				'person_id' => $customer_id,
				'employee_id' => $this->session->userdata('person_id'),
				'title' => 'H?p d?ng',
				'content' => $content,
				'time' => date('Y-m-d H:i:s'),
				'file' => $file_name,
				'status' => 0,
			);
			$this->Customer->add_mail_history($data_history);
			show_error($this->email->print_debugger());
		}
		/* end phan lam mail */
	}

	$this->sale_lib->clear_all();
}

function modal_vat_order() {
	$sale_mode = $this->sale_lib->get_mode();
	$flag_error = false;
	if($sale_mode != 'vat_order') {
		$flag_error = true;
		echo 'other-sale';
	}

	$customer_id = $this->sale_lib->get_customer();
	if($customer_id > 0)
	{}
else {
	$flag_error = true;
	echo 'no-customer';
}

if($flag_error == false) {
	$data = array();
	$this->load->view('sales/modal/sale_vat_order', $data);
}
}

function modal_store_payment() {
	$sale_mode = $this->sale_lib->get_mode();
	$flag_error = false;
	if($sale_mode != 'store_account_payment') {
		$flag_error = true;
		echo 'other-sale';
	}

	$customer_id = $this->sale_lib->get_customer();
	if($customer_id > 0)
	{}
else {
	$flag_error = true;
	echo 'no-customer';
}

if($flag_error == false) {
	$data = array();
	$this->load->view('sales/modal/sale_store_payment', $data);
}
}

function set_sale_vat_relationship() {
	$post = $this->input->post();
	if(!empty($post)) {
		$sale_id = $post['sale_id'];
		if($post['type'] == 'add') {
			$this->sale_lib->set_sale_vat_relationship($sale_id);
		}elseif($post['type'] == 'remove') {
			$this->sale_lib->clear_sale_vat_relationship($sale_id);
		}
	}
}

function update_payment_store() {
	$post = $this->input->post();
	if(!empty($post)) {
		$sale_id = $post['pk'];
		$amount = (float)convert_number($post['value']);

		$sale_mode = $this->sale_lib->get_mode();
		if($sale_mode != 'store_account_payment') {
			$response = array('flag'=>'false', 'msg'=>'B?n dang ? ch? d? bán hàng khác.');
			echo json_encode($response);

			return;
		}

		if($amount < 0) {
			$response = array('flag'=>'false', 'msg'=>'Giá tr? không du?c nh? hon 0.');
			echo json_encode($response);

			return;
		}

		$this->sale_lib->update_sale_store_payment($sale_id, $amount);

		$response = array('flag'=>'true');
		echo json_encode($response);
	}
}

function modal_store_payment_store() {
	$post        =  $this->input->post();
        //        $post['col'] = 'sale_id';
        //        $post['order'] = 'asc';
	$arrParam    =  array_merge($post, $this->input->get());

	if(!empty($post)) {
		$_SESSION['sale_store_payment_modal_filter'] = array();
		$arrParam['paginator']             = $this->_paginator;
		$arrParam['page']                  =  $this->uri->segment(3, 1);

		$customer_id = $this->sale_lib->get_customer();
		$arrParam['customer_id'] = $customer_id;

		$config['base_url'] = base_url() . 'sales/modal_store';

		$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 5;
		$config['uri_segment'] = 3;
		$config['use_page_numbers'] = TRUE;

		$items = $this->Sale->list_sale_store_payment($arrParam);

		if(!empty($items)) {
			$data = array();
			$sale_prefix = $this->config->item('sale_prefix');
			foreach($items as $key => $item) {
				if(!empty($item['code']))
					$array['code'] = $item['code'];
				else
					$array['code'] = $sale_prefix . ' ' . $item['sale_id'];

				$array['link_order_detail'] = base_url() . 'sales/receipt/' . $item['sale_id'];
				$array['sale_id'] =  $item['sale_id'];
				$array['sale_time']     = $item['sale_time_format'];
				$array['so_tien_no']    = to_currency($item['so_tien_no']);
				$array['da_thanh_toan'] = to_currency($item['da_thanh_toan']);
				$array['con_lai_value'] = $item['so_tien_no'] - $item['da_thanh_toan'];
				$array['con_lai']       = to_currency($item['so_tien_no'] - $item['da_thanh_toan']);
				$array['amount']        = $item['amount'];

				if ($array['con_lai'] == 0) {
					continue;
				}  

				$data[] = $array;
			}
		}

		$config['total_rows'] = count($data);
        // var_dump($config['total_rows']);die;
		$html = $this->load->view('sales/row/sale_store_payment', array('data'=>$data), true);

		$this->load->library("pagination");
		$this->pagination->initialize($config);
		$this->pagination->createConfig('front-end');

		$pagination = $this->pagination->create_ajax();

		$con_lai = $this->sale_lib->get_last_amount_from_sale_store_payment();
		$con_lai = to_currency($con_lai);

		$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'con_lai'=>$con_lai,'pagination'=>$pagination);
		echo json_encode($result);
	}
}

function modal_vat_order_store() {
	$post        =  $this->input->post();
        //        $post['col'] = 'sale_id';
        //        $post['order'] = 'asc';
	$arrParam    =  array_merge($post, $this->input->get());

	if(!empty($post)) {
		$_SESSION['sale_vat_order_modal_filter'] = array();
		$arrParam['paginator']             = $this->_paginator;
		$arrParam['page']                  =  $this->uri->segment(3, 1);

		$customer_id = $this->sale_lib->get_customer();
		$arrParam['customer_id'] = $customer_id;

		$location_id     = $this->Employee->get_logged_in_employee_current_location_id();

            // create sale tmp
		$where = "s.suspended = 0 AND s.store_account_payment = 0 AND s.is_vat = 0 AND s.location_id IN ($location_id) AND s.deleted = 0 AND s.customer_id = $customer_id";
		$this->Sale->create_sales_items_temp_table_n9(array('where'=>$where));

		$config['base_url'] = base_url() . 'sales/modal_vat_order_store';
		$config['total_rows'] = $this->Sale->count_item($arrParam, array('task'=>'vat_order'));

		$config['per_page'] = $arrParam['paginator']['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
            //$config['per_page'] = $arrParam['paginator']['per_page'] = 5;
		$config['uri_segment'] = 3;
		$config['use_page_numbers'] = TRUE;

		$items = $this->Sale->list_item($arrParam, array('task'=>'vat_order'));
		$html = $this->load->view('sales/row/sale_vat_order', array('items'=>$items), true);

		$this->load->library("pagination");
		$this->pagination->initialize($config);
		$this->pagination->createConfig('front-end');

		$pagination = $this->pagination->create_ajax();

		$result = array('count'=> $config['total_rows'], 'html_string'=>$html, 'pagination'=>$pagination);
		echo json_encode($result);
	}
}

function modal_ds() {
	$data = array();
	$this->load->view('sales/modal/sale_ds', $data);
}

function modal_ds_store() {
	$post        =  $this->input->post();
	$arrParam    =  array_merge($post, $this->input->get());
	if(!empty($post)) {
		$_SESSION['sales_model_filter'] = array();
		$arrParam['paginator']             = $this->_paginator;
		$arrParam['page']                  =  $this->uri->segment(3, 1);

		$config['base_url'] = base_url() . 'sales/modal_store';
		$config['total_rows'] = $this->Sale->count_item($arrParam);
		$config['per_page'] = $arrParam['paginator']['per_page'] = 5;
		$config['uri_segment'] = 3;
		$config['use_page_numbers'] = TRUE;

		$items = $this->Sale->list_item($arrParam);

		$this->load->library("pagination");
		$this->pagination->initialize($config);
		$this->pagination->createConfig('front-end');

		$pagination = $this->pagination->create_ajax();
		$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
		echo json_encode($result);
	}
}

function modal_list() {
	$data = array();
	$this->load->view('sales/modal/sale_list', $data);
}

function modal_store() {
	$post        =  $this->input->post();
        //        $post['col'] = 'sale_id';
        //        $post['order'] = 'asc';
	$arrParam    =  array_merge($post, $this->input->get());
	if(!empty($post)) {
		$_SESSION['sales_model_filter'] = array();
		$arrParam['paginator']             = $this->_paginator;
		$arrParam['page']                  =  $this->uri->segment(3, 1);

		$config['base_url'] = base_url() . 'sales/modal_store';
		$config['total_rows'] = $this->Sale->count_item($arrParam);
		$config['per_page'] = $arrParam['paginator']['per_page'] = 5;
		$config['uri_segment'] = 3;
		$config['use_page_numbers'] = TRUE;

		$items = $this->Sale->list_item($arrParam, array('validate_contract'=>true));

		$this->load->library("pagination");
		$this->pagination->initialize($config);
		$this->pagination->createConfig('front-end');

		$pagination = $this->pagination->create_ajax();

		$result = array('count'=> $config['total_rows'], 'items'=>$items, 'pagination'=>$pagination);
		echo json_encode($result);
	}
}

function contract_other_info() {
	$post = $this->input->post();
	if(!empty($post)) {
		$item = $this->Contract->get_item(array('id'=>$post['contract_id']));
		if(!empty($item['file'])) {
			$item['file_without_ext']      = filter_remove_extension($item['file']);
			$item['link_file']             = base_url() . 'document/files/store_' . $item['created_by'] . '/' . $item['file'];
		}

		echo json_encode($item);
	}
}

function contract_sale_info() {
	$post = $this->input->post();
	$get = $this->input->get();
	$arrParams = array_merge($post, $this->input->get());
	if(!empty($post)) {
		$contract_info     = $this->Contract->get_item(array('id'=>$arrParams['contract_id']));
		$sale_info         = $this->getSale($contract_info['sale_id']);
		$data['info']      = $arrParams['info'];
		$data['sale_info'] = $sale_info;

		$this->load->view('sales/partials/contract_sale_info', $data);
	}
}

		//---------------------------------------------------------------
		// for create a service contract only
		//---------------------------------------------------------------
function add_more_customer_to_service()
{
	if($this->input->post('post_customers'))
	{
		$list_customer_ids = implode(',',$this->input->post('post_customers'));
		$list_customers    = $this->Customer->list_item(array('list_id'=>$list_customer_ids));
		$this->sale_lib->add_more_customer_to_service_contract($list_customers);
	}
	else
	{
		$this->sale_lib->empty_customers();
	}

}


	// RÈM HÀ MY	
public function selectedAttribute()
{
	$line      = $this->input->post('line');
	$attributeId = $this->input->post('attributeId');

	if ($this->session->userdata('attributeCartItemsValue')) {
		echo $this->session->userdata('attributeCartItemsValue')[$line][$attributeId]->entity_value;
	} else {
		echo '';
	}

}

public function groupCartItem() 
{
	$line 		= $this->input->post('line');
	$itemName 	= $this->input->post('itemName');
	$itemNameContainsLine['itemName']	= $itemName;
	$itemNameContainsLine['line'] 		= $line;

	$itemNameContainsLineSession 	= $this->sale_lib->getItemContainsLine();

	foreach ($itemNameContainsLineSession as $key => $lines) {
		if (empty($lines['line'])) {
			unset($itemNameContainsLineSession[$key]);
		}
		$itemNameContainsLineSession[$key]['line'] = array_diff($lines['line'], $line);
		if (empty($itemNameContainsLineSession[$key]['line'])) {
			unset($itemNameContainsLineSession[$key]);
		}
	}
	$itemNameContainsLineSession[]	= $itemNameContainsLine;
	$this->sale_lib->setItemContainsLine($itemNameContainsLineSession);
	$this->sale_lib->updateCartWhenLineChanged();
	$this->_reload();
}

public function setCommentTerm() 
{
	$saleCommentId 	= $this->input->post('saleCommentId');
	$saleCommentVal = $this->input->post('saleCommentVal');
	$saleComment 	= $this->sale_lib->getSaleComments();
	$saleComment[$saleCommentId] = $saleCommentVal;
	$this->sale_lib->setSaleComments($saleComment);;
}

public function changeItemContainsLineName() 
{
	$itemContainsLinePosition = $this->input->post('position');
	$itemContainsLineName     = $this->input->post('newName');
	$itemContainsLine = $this->sale_lib->getItemContainsLine();
	if (!empty($itemContainsLineName)) { 
		foreach ($itemContainsLinePosition as $position) {
			if (!empty($itemContainsLineName[$position])) {
				$itemContainsLine[$position]['itemName'] = $itemContainsLineName[$position];
			}
		}
		$this->sale_lib->setItemContainsLine($itemContainsLine);
	}
	$this->_reload();
}

public function updateAttributeItem()
{

	$this->sale_lib->updateCartWhenLineChanged();
	$this->_reload([]);
}

public function editCompleteProductItem($line) 
{
	$value      = $this->input->post('value');
	$editItem   = false;
	$cart       = $this->sale_lib->get_cart();
	$item_name  = $this->Item->get_info($cart[$line]['item_id'])->name;
	if ($value['width'] > 0 && $value['height']>0 ) {  
		$quantities     =    $value['width']*0.01*$value['height']*0.01;
		$quantities     = $quantities>1? $quantities:1;
		$name           =   $item_name.' R'.$value['width'].'x'.$value['height'];
		$editItem       = true;
	} elseif ($value['mSquare'] > 0) {
		$quantities     =    $value['mSquare']<1? 1:$value['mSquare'];
		$editItem       = true;
	}

	if ($editItem) {
		$this->sale_lib->edit_item(
			$line,
			isset($description) ? $description : NULL,
			isset($serialnumber) ? $serialnumber : NULL,
			isset($quantities) ? $quantities : NULL,
			isset($discount) ? $discount : NULL,
			isset($price) ? $price: NULL,
			isset($cost_price) ? $cost_price: NULL,
			isset($measure) ? $measure: NULL,
			isset($name) ? $name: NULL
		);
		$this->_reload([]);
	}
}

public function editItemName($line) 
{
	$name      = $this->input->post('value');
	if ($name != '') {
		$this->sale_lib->edit_item(
			$line,
			isset($description) ? $description : NULL,
			isset($serialnumber) ? $serialnumber : NULL,
			isset($quantities) ? $quantities : NULL,
			isset($discount) ? $discount : NULL,
			isset($price) ? $price: NULL,
			isset($cost_price) ? $cost_price: NULL,
			isset($measure) ? $measure: NULL,
			isset($name) ? $name: NULL
		);
		$this->_reload([]);
	}
}


public function return_by_sales() {
	$mode = $this->sale_lib->get_mode();
	$this->sale_lib->clear_all();
	$sale_id = $this->input->post('item');
	if ($this->Sale->is_return_sale_exists($sale_id)) {
		$data['error']=lang('sales_return_exists');
	} else {
		$this->sale_lib->copy_entire_sale($sale_id);
		$this->sale_lib->set_id_sale_return($sale_id);
		$this->sale_lib->set_mode($mode);

	}


	$this->_reload($data);
}

    /**
     * @author ToiNT
     */
    public function select_service_temp() {
    	$temp_id = $this->input->post('temp_id');
    	if (!empty($temp_id)) {
    		$this->sale_lib->set_temp_service($temp_id);
    	}
    	$this->_reload([]);
    }
    
    /**
     * @author ToiNT
     */
    public function select_sale_status() {
    	$status_id = $this->input->post('status_id');
    	if (!empty($status_id)) {
    		$this->sale_lib->set_sale_status($status_id);
    	}
    	$this->_reload([]);
    }
    
    public function delete_supporter() {
    	$supporter_id = $this->input->post('supporter_id');
    	if (!empty($supporter_id)) {
    		$this->sale_lib->delete_supporter_list($supporter_id);
    	}
    	$this->_reload([]);
    }
    
    public function approve() {
    	$sale_id = $this->input->post('sale_id');
    	if (!empty($sale_id)) {
    		$this->Sale->update_suspended(array(
    			'suspended' => 1,
    			'sale_id' => $sale_id
    		));
    	}
    }
    
    function item_search_for_contract() {
    	//allow parallel searchs to improve performance.
    	$items = $this->Item->search_item_for_contract(trim($this->input->get('term')));
    	
    	echo json_encode($items);
    }




    function edit_item_d13($line)
    {

        // var_dump($this->input->post());die();
    	$d = $this->input->post();
    	$data = $this->sale_lib->get_cart();


    	foreach ($data as $key => &$value) {
    		if($value['line'] == $line)
    		{
    			$value['calculatedPrice'] = str_replace(',', '', $d['value']);
    		}
    	}
    	$this->sale_lib->set_cart($data);
    	$this->_reload($data);
    }




}
?>