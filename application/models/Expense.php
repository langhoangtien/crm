<?php

class Expense extends CI_Model {
    /*
      Determines if a given person_id is a customer
     */

    protected $_fields = array(
        'id' 	                => 'e.id',
        'expense_amount' 	    => 'e.expense_amount',
        'expense_description'   => 'e.expense_description',
        'expense_date' 	        => 'e.expense_date',
        'employee_id' 	        => 'e.employee_id',

    );

    function exists($expense) {
        $this->db->from('expenses');
        $this->db->where('expenses.id', $expense);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    /*
      Returns all the Expenses
     */

    function get_all($limit = 10000, $offset = 0, $col = 'id', $order = 'desc') {
        $shift_category_id = $this->config->item('shift_category_id');
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();
		$this->db->select('expenses.*, CONCAT(recv.last_name, ", ", recv.first_name) as employee_recv, CONCAT(appr.last_name, ", ", appr.first_name) as employee_appr, categories.name as category', false);
		$this->db->from('expenses');
		$this->db->join('people as recv', 'recv.person_id = expenses.employee_id','left');
		$this->db->join('people as appr', 'appr.person_id = expenses.approved_employee_id','left');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
		$this->db->where('expenses.deleted', 0);
		$this->db->where('location_id', $current_location);
        if($shift_category_id > 0)
            $this->db->where('category_id != ' . $shift_category_id);
		$this->db->order_by($col, $order);
		$this->db->limit($limit);
		$this->db->offset($offset);
      return $this->db->get();
    }

    function count_all() {
        $shift_category_id = $this->config->item('shift_category_id');
 	    $current_location=$this->Employee->get_logged_in_employee_current_location_id();

        $this->db -> select('COUNT(e.id) AS totalItem')
                  -> from('expenses AS e')
                  -> where('e.location_id', $current_location)
                  -> where('e.deleted', 0);

        if($shift_category_id > 0) {
            $this->db->join('categories AS c', 'c.id = e.category_id','left');
            $this->db->where('category_id != ' . $shift_category_id);
        }

        $query = $this->db->get();
        $result = $query->row()->totalItem;
        $this->db->flush_cache();

        return $result;
    }

    /*
      Gets information about a particular expense
     */

    function get_info($expense_id) {
        $this->db->from('expenses');
        $this->db->where('expenses.id', $expense_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            //Get empty base parent object, as $supplier_id is NOT an supplier
            $fields = $this->db->list_fields('expenses');
            $expense_obj = new stdClass;
            //Get all the fields from Expenses table
            $fields = $this->db->list_fields('expenses');
            //append those fields to base parent object, we we have a complete empty object
            foreach ($fields as $field) {
                $expense_obj->$field = '';
            }
            return $expense_obj;
        }
    }

    function search_count_all($search, $limit = 10000) {
        $shift_category_id = $this->config->item('shift_category_id');
  		$this->db->from('expenses');
  		$this->db->join('people as recv', 'recv.person_id = expenses.employee_id','left');
  		$this->db->join('people as appr', 'appr.person_id = expenses.approved_employee_id','left');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
					 
 		if ($search)
 		{
   		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
			{
 				$this->db->where("(MATCH (expense_type,expense_description,expense_reason,expense_note) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or expense_amount = ".$this->db->escape($search)." or MATCH(".$this->db->dbprefix('categories').".name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".")) and ".$this->db->dbprefix('expenses').".deleted=0", NULL, FALSE);			
			}
			else
			{
				$this->db->where("(expense_type LIKE '%".$this->db->escape_like_str($search)."%' or 
				expense_description LIKE '%".$this->db->escape_like_str($search)."%' or 
				expense_reason LIKE '%".$this->db->escape_like_str($search)."%' or
				".$this->db->dbprefix('categories').".name LIKE '%".$this->db->escape_like_str($search)."%' or
				expense_note LIKE '%".$this->db->escape_like_str($search)."%'  or expense_amount = ".$this->db->escape($search).") and ".$this->db->dbprefix('expenses').".deleted=0");			
			}
		}
		else
		{
			$this->db->where('expenses.deleted',0);
		}

        if($shift_category_id > 0)
            $this->db->where('category_id != ' . $shift_category_id);

		$this->db->order_by($this->db->dbprefix('expenses').'.id','asc');
		$this->db->limit($limit);
      $result = $this->db->get();
      return $result->num_rows();
    }

    /*
      Preform a search on expenses
     */

    function search($search, $limit = 20, $offset = 0, $column = 'id', $orderby = 'asc') {
        $shift_category_id = $this->config->item('shift_category_id');
   		$this->db->select('expenses.*, CONCAT(recv.last_name, ", ", recv.first_name) as employee_recv, CONCAT(appr.last_name, ", ", appr.first_name) as employee_appr,categories.name as category', false);
   		$this->db->from('expenses');
   		$this->db->join('people as recv', 'recv.person_id = expenses.employee_id','left');
   		$this->db->join('people as appr', 'appr.person_id = expenses.approved_employee_id','left');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
        if ($search)
        {
        if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
            {
                $this->db->where("(MATCH (expense_type,expense_description,expense_reason,expense_note) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or expense_amount = ".$this->db->escape($search)." or MATCH(".$this->db->dbprefix('categories').".name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".")) and ".$this->db->dbprefix('expenses').".deleted=0", NULL, FALSE);
            }
            else
            {
                $this->db->where("(expense_type LIKE '%".$this->db->escape_like_str($search)."%' or
                expense_description LIKE '%".$this->db->escape_like_str($search)."%' or
                expense_reason LIKE '%".$this->db->escape_like_str($search)."%' or
                ".$this->db->dbprefix('categories').".name LIKE '%".$this->db->escape_like_str($search)."%' or
                expense_note LIKE '%".$this->db->escape_like_str($search)."%'  or expense_amount = ".$this->db->escape($search).") and ".$this->db->dbprefix('expenses').".deleted=0");
            }
        }
        else
        {
            $this->db->where('expenses.deleted',0);
        }

        if($shift_category_id > 0)
            $this->db->where('category_id != ' . $shift_category_id);

        $this->db->order_by($column,$orderby);
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }

    /*
      Gets information about multiple expenses
     */

    function get_multiple_info($expenses_ids) {
        $this->db->from('expenses');
        $this->db->where_in('expenses.id', $expenses_ids);
        $this->db->order_by("id", "asc");
        return $this->db->get();
    }

    /*
      Inserts or updates a expenses
     */

    function save(&$expense_data, $expense_id = false) {
    	// var_dump($expense_data);exit();
        if (!$expense_id or !$this->exists($expense_id)) {
            if ($this->db->insert('expenses', $expense_data)) {
                $expense_data['id'] = $this->db->insert_id();
                return true;
            }
            return false;
        }
        // echo $expense_id;
        
        $this->db->where('id', $expense_id);
        return $this->db->update('expenses', $expense_data);
         // echo $this->db->last_query();
    }


    /*
      Get search suggestions to find Expenses
     */

    function get_search_suggestions($search, $limit = 25) 
	 {
			if (!trim($search))
			{
				return array();
			}
		  
		  $suggestions = array();
		  
  		  if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		  {
			  $this->db->select("expense_type,MATCH (expense_type) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
	        $this->db->from('expenses');
	  		  $this->db->where("MATCH (expense_type) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) and deleted=0", NULL, FALSE);			
	        $this->db->limit($limit);
			  $this->db->order_by('rel DESC');
	        $by_type = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_type->result() as $row) {
	            $temp_suggestions[] = $row->expense_type;
	        }

        
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
		  
			  $this->db->select("expense_description,MATCH (expense_description) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
	        $this->db->from('expenses');
	  		  $this->db->where("MATCH (expense_description) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) and deleted=0", NULL, FALSE);			
	        $this->db->limit($limit);
			  $this->db->order_by('rel DESC');
		  
	        $by_expense_description = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_expense_description->result() as $row) {
	            $temp_suggestions[] = $row->expense_description;
	        }

        
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
		  
		  
			  $this->db->select("expense_reason,MATCH (expense_reason) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
	        $this->db->from('expenses');
	  		  $this->db->where("MATCH (expense_reason) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) and deleted=0", NULL, FALSE);			
	        $this->db->limit($limit);
			  $this->db->order_by('rel DESC');
		  
	        $by_expense_reason = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_expense_reason->result() as $row) {
	            $temp_suggestions[] = $row->expense_reason;
	        }

        
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
		  
	        $this->db->from('expenses');
	        $this->db->where("(expense_amount = ".$this->db->escape($search).") and deleted=0");
	        $this->db->limit($limit);
		  
	        $by_expense_amount = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_expense_amount->result() as $row) {
	            $temp_suggestions[] = to_currency_no_money($row->expense_amount);
	        }

        
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
		 
			$this->db->select("name,MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
	  		$this->db->from('categories');
			$this->db->where("MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) and deleted=0", NULL, FALSE);			
		
	  		$this->db->limit($limit);
		  $this->db->order_by('rel DESC');
		
	  		$by_category = $this->db->get();
		
	  		$temp_suggestions = array();
	  		foreach($by_category->result() as $row)
	  		{
	  			$temp_suggestions[] = $row->name;
	  		}
		
  		
	  		foreach($temp_suggestions as $temp_suggestion)
	  		{
	  			$suggestions[]=array('label'=> $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );		
	  		}
		}
		else
		{
			$this->db->select("expense_type");
	        $this->db->from('expenses');
	        $this->db->like('expense_type', $search);
	        $this->db->limit($limit);
	        $by_type = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_type->result() as $row) {
	            $temp_suggestions[] = $row->expense_type;
	        }

      	  sort($temp_suggestions);
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
	  
			  $this->db->select("expense_description");
	        $this->db->from('expenses');
	        $this->db->like('expense_description', $search);
	        $this->db->limit($limit);
	        $by_expense_description = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_expense_description->result() as $row) {
	            $temp_suggestions[] = $row->expense_description;
	        }
			  
      	  sort($temp_suggestions);
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
	  
	  
			  $this->db->select("expense_reason");
	        $this->db->from('expenses');
	        $this->db->like('expense_reason', $search);
	        $this->db->limit($limit);
	  
	        $by_expense_reason = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_expense_reason->result() as $row) {
	            $temp_suggestions[] = $row->expense_reason;
	        }

      	  sort($temp_suggestions);
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
	  
	        $this->db->from('expenses');
	        $this->db->where("(expense_amount = ".$this->db->escape($search).") and deleted=0");
	        $this->db->limit($limit);
	  
	        $by_expense_amount = $this->db->get();
	        $temp_suggestions = array();
	        foreach ($by_expense_amount->result() as $row) {
	            $temp_suggestions[] = to_currency_no_money($row->expense_amount);
	        }

      	  sort($temp_suggestions);
	        foreach ($temp_suggestions as $temp_suggestion) {
	            $suggestions[] = array('label' => $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );
	        }
	 
	  		$this->db->select('name');
	  		$this->db->from('categories');
	      $this->db->like('name', $search);
		
	
	  		$this->db->limit($limit);
	
	  		$by_category = $this->db->get();
	
	  		$temp_suggestions = array();
	  		foreach($by_category->result() as $row)
	  		{
	  			$temp_suggestions[] = $row->name;
	  		}
	
			sort($temp_suggestions);
	  		foreach($temp_suggestions as $temp_suggestion)
	  		{
	  			$suggestions[]=array('label'=> $temp_suggestion,'subtitle' => '', 'avatar' => base_url()."assets/img/expense.png" );		
	  		}
			
		}
        //only return $limit suggestions
        if (count($suggestions > $limit)) {
            $suggestions = array_slice($suggestions, 0, $limit);
        }
        return $suggestions;
    }

    /*
      Deletes one Expense
     */

    function delete($expense_id) {
        $this->db->where('id', $expense_id);
        return $this->db->update('expenses', array('deleted' => 1));
    }

    /*
      Deletes a list of expeses
     */

    function delete_item($cid) {
        $this->load->model('Sale');
        $this->load->library('sale_lib');

        $expense_items = $this->get_items(array('cid'=>$cid, 'for_sale'=>true), array('task'=>'by-expenses-ids'));

        $this->db->where('id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('expenses');

        $this->db->flush_cache();

        if(!empty($expense_items)) {
            foreach($expense_items as $val) {
                $sale_id = $val['sale_id'];

                $this->Sale->delete_sale_commission($sale_id);
                $emp_group_arr = $this->Sale->get_employee_list_from_sale(array('sale_id'=>$sale_id));
                $this->sale_lib->update_employee_commission($emp_group_arr, $sale_id, true);
            }
        }
    }

    function get_item($arrParams = null, $options = null) {
			  $result = array();
        if($options['task'] == null) {
            $this->db -> select('*')
                      -> from('expenses');
      
            if(!empty($arrParams['id'])) {
       
                $this->db->where('id', $arrParams['id']);
            }
            if(isset($arrParams['deleted']) && $arrParams['deleted'] == true) {
                $this->db->where('deleted', 0);
            }
          
            if(!empty($arrParams['receiving_id']))  {
       
                $receiving_id = $arrParams['receiving_id'];
                $this->db->where('receiving_id',$receiving_id);
            }
            if(!empty($arrParams['sale_id'])) {
       
                $sale_id = $arrParams['sale_id'];
                $this->db->where('sale_id',$sale_id);
            }
           
        }
        if($options['task'] == 'sum_total_by_sale') {
            if(!empty($arrParams['start_date']) && !empty($arrParams['end_date'])) {
                $where = " AND phppos_sales.sale_time >= '".$arrParams['start_date']."' AND phppos_sales.sale_time <='".$arrParams['end_date']."'";
            }
            else {
                $where = '';
            }
            $this->db-> select('SUM(expense_amount*expense_type) as expense, expense_type')
                      -> from('(SELECT expense_amount, expense_type
                               FROM phppos_sales  
                               INNER JOIN phppos_expenses 
                               ON phppos_sales.sale_id = phppos_expenses.sale_id 
                               WHERE phppos_expenses.location_id IN ('.$arrParams['locations'].') 
                               AND phppos_sales.'.$arrParams['employee_col'].'='.$arrParams['person_id'].$where.') AS expense_employee');
        }
            


        $query = $this->db->get();
        if($query->num_rows == 1) {
            $result = $query->row_array();
             $this->db->flush_cache();
        }
        else {
            $result = $query->result_array();
             $this->db->flush_cache();
        }
        return $result;
    }

    function count_item($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $current_location=$this->Employee->get_logged_in_employee_current_location_id();
            $this->db -> select('COUNT(e.id) AS totalItem')
                      -> from('expenses AS e')
                      -> where('e.location_id', $current_location)
                      -> where('e.deleted', 0);

            if(!empty($arrParams['category_id']) > 0) {
                $this->db->where('e.category_id', $arrParams['category_id']);
            }

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where('e.expense_type LIKE \'%'.$keywords.'%\' OR e.expense_description LIKE \'%'.$keywords.'%\' OR e.expense_reason LIKE \'%'.$keywords.'%\' OR e.expense_note LIKE \'%'.$keywords.'%\'');
            }

            $query = $this->db->get();

            $result = $query->row()->totalItem;

            $this->db->flush_cache();
        }

        return $result;
    }

    function list_item($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $paginator = $arrParams['paginator'];
            $current_location = $this->Employee->get_logged_in_employee_current_location_id();
            $this->db -> select("e.id, e.expense_amount, e.expense_description, e.employee_id")
                      -> select("DATE_FORMAT(e.expense_date, '%d/%m/%Y %H:%i:%s') AS expenses_date", FALSE)
                      -> from('expenses AS e')
                      -> where('e.location_id', $current_location)
                      -> where('e.deleted', 0);

            if(!empty($arrParams['category_id']) > 0) {
                $this->db->where('e.category_id', $arrParams['category_id']);
            }

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where('e.expense_type LIKE \'%'.$keywords.'%\' OR e.expense_description LIKE \'%'.$keywords.'%\' OR e.expense_reason LIKE \'%'.$keywords.'%\' OR e.expense_note LIKE \'%'.$keywords.'%\'');
            }

            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);
            }else {
                $this->db->order_by('e.id', 'DESC');
            }

            $page = $arrParams['page'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();
            $result = $query->result_array();
            $this->db->flush_cache();

            if(!empty($result)) {
                foreach($result as $val)
                    $emp_ids[] = $val['employee_id'];

                $emp_ids = array_unique($emp_ids);
                $this->db-> select("e.id, p.first_name, p.last_name")
                         -> from('employees AS e')
                         -> join('people as p', 'e.person_id = p.person_id')
                         -> where('e.id IN ('.implode(',', $emp_ids).')');

                $query = $this->db->get();
                $employee_list = $query->result_array();

                $this->db->flush_cache();
                foreach($employee_list as $v) {
                    $employee_assoc[$v['id']] = $v['first_name'] . ' ' . $v['last_name'];
                }

                foreach($result as &$value) {
                    $value['fullname'] = $employee_assoc[$val['employee_id']];
                    $value['expense_amount'] = number_format($value['expense_amount'],'0','.',',') . ' VND';
                }
            }
        }

        return $result;
    }

    function get_items($arrParams = null, $options = null) {
        if($options['task'] == 'by-sale-or-receiving') {
            $this->db -> select("e.*, c.name AS category_name,(e.expense_amount+e.expense_tax)*e.expense_type AS final_amount")
                      -> from('expenses AS e')
                      -> join('categories AS c', 'e.category_id = c.id', 'left')
                      -> where('e.deleted', 0)
                      -> order_by('e.id', 'DESC');

            if($arrParams['sale_id'] > 0)
                $this->db->where('e.sale_id', $arrParams['sale_id']);

            $query = $this->db->get();
            $result = $query->result_array();

            $this->db->flush_cache();
        }elseif($options['task'] == 'by-expenses-ids') {
            $this->db -> select("e.id, e.sale_id")
                      -> from('expenses AS e')
                      -> where('e.id IN ('.implode(',', $arrParams['cid']).')');

            if($arrParams['for_sale'] == true)
                $this->db->where('e.sale_id > 0');

            $query = $this->db->get();
            $result = $query->result_array();

            $this->db->flush_cache();
        }

        return $result;
    }
     function get_contract($location_id=null){
     	$this->db->SELECT('id,name');
        $this->db->where('deleted',0);
        if($location_id){
        	$this->db->where('locations_id',$location_id);
        }
        $this->db->FROM('contract');
        $this->db->order_by('created','DESC');
        $query = $this->db->get();
        $row = $query->result_array();
        return $row;
    }
}

?>