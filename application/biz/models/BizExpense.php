<?php
	require_once (APPPATH . "models/Expense.php");
	
	class BizExpense extends Expense 
	{
		
		protected $_items_fields = array(
		'id'                           => 'id',
		'expense_type'                 => 'expense_type',
		'expense_description'          => 'expense_description',
		'category'                     => 'category',
		'expense_date'                 => 'expense_date',
		'expense_amount'               => 'expense_amount',
		'expense_tax'                  => 'expense_tax',
		'employee_recv'                => 'employee_recv',
		'employee_appr'                => 'employee_appr',
		'r_receiving_id'               => 'r_receiving_id',
		's_sales_id'                   => 's_sales_id',
		'cus_name'					   => 'cus_name',
		'company_name'				   => 'company_name',
		);
		
		function full_item($arrParams = null, $options = null) {
			// $shift_category_id = $this->config->item('shift_category_id');
			$current_location=$this->Employee->get_logged_in_employee_current_location_id();
			$key_filter = isset($arrParams['key_filter'])?$arrParams['key_filter']:'';
			
			$this->db->select('phppos_expenses.contract_id,expenses.id, expenses.category_id, expenses.location_id as e_location_id, expenses.expense_type, expenses.expense_options, expenses.payment_type as e_payment_type, expenses.employee_id as e_employee_id, expenses.receiving_id as e_receiving_id, expenses.expense_description, expenses.expense_reason, expenses.expense_date, expenses.expense_amount, expenses.expense_tax, expenses.expense_note, CONCAT(recv.last_name, " ", recv.first_name) as employee_recv, CONCAT(appr.last_name, " ", appr.first_name) as employee_appr, categories.name as category, receivings.payment_type as r_payment_type, receivings.employee_id as r_employee_id, receivings.receiving_id as r_receiving_id,receivings.location_id as r_location_id, sup.company_name, sales.payment_type as s_payment_type, sales.employee_id as s_employee_id, sales.sale_id as s_sales_id, sales.location_id as s_location_id, CONCAT(cus.last_name, ", ", cus.first_name) as cus_name,appr.last_name as last_name_appr ,appr.first_name as first_name_appr, recv.last_name as last_name_recv ,recv.first_name as first_name_recv', false);
			$this->db->from('expenses');
			$this->db->join('people as recv', 'recv.person_id = expenses.employee_id','left');
			$this->db->join('people as appr', 'appr.person_id = expenses.approved_employee_id','left');
			$this->db->join('categories', 'categories.id = expenses.category_id','left');
			$this->db->join('sales', 'sales.sale_id = expenses.sale_id', 'left');
			// $this->db->join('customers', 'sales.customer_id = customers.id', 'left');
			$this->db->join('people as cus', 'cus.person_id = sales.customer_id', 'left');
			$this->db->join('receivings', 'expenses.receiving_id = receivings.receiving_id', 'left');
			$this->db->join('suppliers as sup', 'receivings.supplier_id = sup.person_id', 'left');
			
			if ($arrParams['key_filter'] == 'count_in_expense') {
				$this->db->where('expenses.expense_options = "other"');
			}
			if($arrParams['key_filter'] == 'count_sale_expense') {
			
				$this->db->where('expenses.expense_options = "sale"');
			}
			if($arrParams['key_filter'] == 'count_receiving_expense') {
				
				$this->db->where('expenses.expense_options = "receiving"');
			}
			if($arrParams['key_filter_expense_date'] == 'expense_date') {
				$this->db->like('expenses.expense_date',$arrParams['year'],'after');
			}
			
			if(!empty($arrParams['keywords']) && $arrParams['key_filter']) {
				$keywords = trim($arrParams['keywords']);
				if ($arrParams['key_filter'] == 'count_sale_expense') {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR  expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\' OR sup.company_name LIKE \'%'.$keywords.'%\' OR sales.sale_id LIKE \'%'.$keywords.'%\' )');
					} elseif ($arrParams['key_filter'] == 'count_receiving_expense') {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR  expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\' OR sup.company_name LIKE \'%'.$keywords.'%\' OR receivings.receiving_id LIKE \'%'.$keywords.'%\')');
					} else {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\')');
				}
				$_SESSION[$key_filter]['keywords'] = $keywords;
			}
						
			$this->db->where('expenses.deleted', 0);
			$this->db->where('expenses.location_id', $current_location);
			
			if(!empty($arrParams['col']) && !empty($arrParams['order'])){
				$col   = $this->_items_fields[$arrParams['col']];
				
				$order = $arrParams['order'];
				
				$this->db->order_by($col, $order);
				
				$_SESSION[$key_filter]['col']  = $arrParams['col'];
				$_SESSION[$key_filter]['order'] = $arrParams['order'];
			}
			
			$page = isset($arrParams['page'])?$arrParams['page']:1;
			
			if(!empty($arrParams['paginator']))
			{
				$paginator = $arrParams['paginator'];
				$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			} 
			
			$result = $this->db->get()->result_array();
			$this->db->flush_cache();
			return $result;
		}
		
		function count_full($arrParams = null, $options = null) {
			// $shift_category_id = $this->config->item('shift_category_id');
		      $current_location=$this->Employee->get_logged_in_employee_current_location_id();
		      $key_filter = isset($arrParams['key_filter'])?$arrParams['key_filter']:'';
					
		      $this->db->select('*');
		      $this->db->from('expenses');
		      $this->db->join('people as recv', 'recv.person_id = expenses.employee_id','left');
		      $this->db->join('people as appr', 'appr.person_id = expenses.approved_employee_id','left');
		      $this->db->join('categories', 'categories.id = expenses.category_id','left');
		      $this->db->join('sales', 'sales.sale_id = expenses.sale_id', 'left');
					// $this->db->join('customers', 'sales.customer_id = customers.id', 'left');
		      $this->db->join('people as cus', 'cus.person_id = sales.customer_id', 'left');
		      $this->db->join('receivings', 'expenses.receiving_id = receivings.receiving_id', 'left');
		      $this->db->join('suppliers as sup', 'receivings.supplier_id = sup.person_id', 'left');
			
				if ($arrParams['key_filter'] == 'count_in_expense') {
				$this->db->where('expenses.expense_options = "other"');
			}
			if($arrParams['key_filter'] == 'count_sale_expense') {
				$this->db->where('expenses.expense_options = "sale"');
			}
			if($arrParams['key_filter'] == 'count_receiving_expense') {
				$this->db->where('expenses.expense_options = "receiving"');
			}
			
			if(!empty($arrParams['keywords']) && $arrParams['key_filter']) {
				$keywords = trim($arrParams['keywords']);
				if ($arrParams['key_filter'] == 'count_sale_expense') {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR  expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\' OR sup.company_name LIKE \'%'.$keywords.'%\' OR sales.sale_id LIKE \'%'.$keywords.'%\' )');
					} elseif ($arrParams['key_filter'] == 'count_receiving_expense') {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR  expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\' OR sup.company_name LIKE \'%'.$keywords.'%\' OR receivings.receiving_id LIKE \'%'.$keywords.'%\')');
					} else {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\')');
				}
				$_SESSION[$key_filter]['keywords'] = $keywords;
			}
			
			
			$this->db->where('expenses.deleted', 0);
			$this->db->where('expenses.location_id', $current_location);
			$result = $this->db->count_all_results();
			
			$this->db->flush_cache();
		
			return $result;
		}
		
		function count_item_by_filter($arrParams = null, $options = null) {
			// $shift_category_id = $this->config->item('shift_category_id');
      $current_location=$this->Employee->get_logged_in_employee_current_location_id();
      $key_filter = isset($arrParams['key_filter'])?$arrParams['key_filter']:'';
			
      $this->db->select('*');
      $this->db->from('expenses');
      $this->db->join('people as recv', 'recv.person_id = expenses.employee_id','left');
      $this->db->join('people as appr', 'appr.person_id = expenses.approved_employee_id','left');
      $this->db->join('categories', 'categories.id = expenses.category_id','left');
      $this->db->join('sales', 'sales.sale_id = expenses.sale_id', 'left');
			// $this->db->join('customers', 'sales.customer_id = customers.id', 'left');
      $this->db->join('people as cus', 'cus.person_id = sales.customer_id', 'left');
      $this->db->join('receivings', 'expenses.receiving_id = receivings.receiving_id', 'left');
        $this->db->join('suppliers as sup', 'receivings.supplier_id = sup.person_id', 'left');
			
				if ($arrParams['key_filter'] == 'count_in_expense') {
				$this->db->where('expenses.expense_options = "other"');
			}
			if($arrParams['key_filter'] == 'count_sale_expense') {
				$this->db->where('expenses.expense_options = "sale"');
			}
			if($arrParams['key_filter'] == 'count_receiving_expense') {
				$this->db->where('expenses.expense_options = "receiving"');
			}
			
			if(!empty($arrParams['keywords']) && $arrParams['key_filter']) {
				$keywords = trim($arrParams['keywords']);
				if ($arrParams['key_filter'] == 'count_sale_expense') {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR  expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\' OR sup.company_name LIKE \'%'.$keywords.'%\' OR sales.sale_id LIKE \'%'.$keywords.'%\' )');
					} elseif ($arrParams['key_filter'] == 'count_receiving_expense') {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR  expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\' OR sup.company_name LIKE \'%'.$keywords.'%\' OR receivings.receiving_id LIKE \'%'.$keywords.'%\')');
					} else {
					$this->db->where('(expenses.id LIKE \'%'.$keywords.'%\' OR categories.name LIKE \'%'.$keywords.'%\' OR appr.first_name LIKE \'%'.$keywords.'%\' OR expenses.expense_amount LIKE \'%'.$keywords.'%\' OR  expenses.expense_date LIKE \'%'.$keywords.'%\')');
				}
				$_SESSION[$key_filter]['keywords'] = $keywords;
			}
			
			$this->db->where('expenses.deleted', 0);
			$this->db->where('expenses.location_id', $current_location);
			
			if(!empty($arrParams['col']) && !empty($arrParams['order'])){
				$col   = $this->_items_fields[$arrParams['col']];
				
				$order = $arrParams['order'];
				
				$this->db->order_by($col, $order);
				
				$_SESSION[$key_filter]['col']  = $arrParams['col'];
				$_SESSION[$key_filter]['order'] = $arrParams['order'];
			}
			
			$page = isset($arrParams['page'])?$arrParams['page']:1;
			
			if(!empty($arrParams['paginator']))
			{
				$paginator = $arrParams['paginator'];
				$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			} 
			
			$result = $this->db->count_all_results();
			
			$this->db->flush_cache();
	
			return $result;
		}
		
		 /*
    Deletes a list of attributes
    */
    function delete_list($cid)
    {
        $this->db->where_in('id', $cid);
        $success = $this->db->update('expenses', array('deleted' => 1));
        return $success;
    }

		
	}	