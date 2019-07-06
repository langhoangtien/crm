<?php
class Supplier extends Person
{
    protected $import_fields = array(
        'company_name' => 'company_name',
        'account_number' => 'account_number',
        'balance' => 'balance',
        'balance_2' => 'balance_2',
    );

    protected $person_import_fields = array(
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'birth_date' => 'birth_date',
        'email' => 'email',
        'phone_number' => 'phone_number',
        'address_1' => 'address_1',
        'address_2' => 'address_2',
        'city' => 'city',
        'state' => 'state',
        'zip' => 'zip',
        'country' => 'country',
        'comments' => 'comments',
    );

	/*
	Determines if a given person_id is a customer
	*/
	function exists($person_id)
	{
		$this->db->from('suppliers');	
		$this->db->join('people', 'people.person_id = suppliers.person_id');
		$this->db->where('suppliers.person_id',$person_id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	/*
	Returns all the suppliers
	*/
	function get_all($limit=10000, $offset=0,$col='company_name',$order='asc',$unit_type_id="")
	{
	    $availableLocations = $this->Location->getLocationsWithChild();
	    $availableLocationIds = array_map(function($location){
	        return $location['location_id'];
	    }, $availableLocations);
        $availableLocationIds[] = 0;
		$order_by = '';
		if (!$this->config->item('speed_up_search_queries'))
		{
			$order_by = "ORDER BY ".$col." ".$order;
		}
		$where_unit_type_id = $unit_type_id ? " WHERE unit_type_id =".$unit_type_id : "";
		$people=$this->db->dbprefix('people');
		$suppliers=$this->db->dbprefix('suppliers');
		$unit_type =$this->db->dbprefix('unit_type');
		$location_id="";


		if ($this->session->userdata('employee_current_location_id')!==NULL)
		{
			$location_id = " WHERE location_id =".$this->session->userdata('employee_current_location_id');
		}


		$this->db->query('DROP TABLE IF EXISTS phppos_total_received');
		$this->db->query('DROP TABLE IF EXISTS  phppos_info_suppliers');
		$this->db->query('CREATE TEMPORARY TABLE phppos_total_received 
			SELECT phppos_receivings.supplier_id, SUM(phppos_receivings_items.item_unit_price * phppos_receivings_items.quantity_received) as total 
			FROM phppos_receivings RIGHT JOIN phppos_receivings_items 
			ON phppos_receivings.receiving_id = phppos_receivings_items.receiving_id'. $location_id.' GROUP BY supplier_id');
		$this->db->query("CREATE TEMPORARY TABLE phppos_info_suppliers
			SELECT phppos_suppliers_head.name_head AS head, phppos_people.person_id,phppos_unit_type.name AS name, phppos_suppliers.company_name,phppos_people.email,phppos_people.phone_number,phppos_suppliers.unit_type_id 
			FROM phppos_people RIGHT JOIN phppos_suppliers 
			ON phppos_people.person_id = phppos_suppliers.person_id LEFT JOIN phppos_unit_type 
			ON phppos_suppliers.unit_type_id= phppos_unit_type.id LEFT JOIN phppos_suppliers_head
			ON phppos_suppliers.person_id = phppos_suppliers_head.supplier_id 
			WHERE phppos_suppliers."."created_at_location IN (". implode(',', $availableLocationIds) .") AND deleted =0 GROUP BY person_id ");
		$data = $this->db->query("SELECT * FROM phppos_info_suppliers LEFT JOIN phppos_total_received
			ON phppos_total_received.supplier_id = phppos_info_suppliers.person_id ".$where_unit_type_id. $order_by .
			" LIMIT  ".$offset.",".$limit);
		return $data;
	
	}
	
	public function update($person_id, $supplier_data) {
		$this->db->where('person_id', $person_id);
		return $this->db->update('suppliers', $supplier_data);
	}
	
	function account_number_exists($account_number)
	{
		$this->db->from('suppliers');	
		$this->db->where('account_number',$account_number);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function supplier_id_from_account_number($account_number)
	{
		$this->db->from('suppliers');	
		$this->db->where('account_number',$account_number);
		$query = $this->db->get();
		
		if ($query->num_rows()==1)
		{
			return $query->row()->person_id;
		}
		
		return false;
	}
	
	function count_all()
	{
		$this->db->from('suppliers');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular supplier
	*/
	function get_info($supplier_id, $can_cache = FALSE)
	{
		if ($can_cache)
		{
			static $cache = array();
		
			if (isset($cache[$supplier_id]))
			{
				return $cache[$supplier_id];
			}
		}
		else
		{
			$cache = array();
		}

		$this->db->from('suppliers');	
		$this->db->join('people', 'people.person_id = suppliers.person_id');
		$this->db->where('suppliers.person_id',$supplier_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			$cache[$supplier_id] = $query->row();
			return $cache[$supplier_id];
		}
		else
		{
			//Get empty base parent object, as $supplier_id is NOT an supplier
			$person_obj=parent::get_info(-1);
			
			//Get all the fields from supplier table
			$fields = $this->db->list_fields('suppliers');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets information about multiple suppliers
	*/
	function get_multiple_info($suppliers_ids)
	{
		$this->db->from('suppliers');
		$this->db->join('people', 'people.person_id = suppliers.person_id');		
		$this->db->where_in('suppliers.person_id',$suppliers_ids);
		$this->db->order_by("last_name", "asc");
		return $this->db->get();		
	}
	
	/*
	Inserts or updates a suppliers
	*/
	function save_supplier(&$person_data, &$supplier_data,$supplier_id=false)
	{
		$success=false;
		
		if(parent::save($person_data,$supplier_id))
		{
			if (!$supplier_id or !$this->exists($supplier_id))
			{
				$supplier_data['created_at_location'] = $this->Employee->get_logged_in_employee_current_location_id();
				$supplier_data['person_id'] = $person_data['person_id'];
				$success = $this->db->insert('suppliers',$supplier_data);
                if ($success) {
                    $supplier_id = $supplier_data['person_id'];
                    $store_supplier_accounts_data[] = array(
                        'supplier_id'        => $supplier_id,
                        'receiving_id'       => NULL,
                        'transaction_amount' => 0,
                        'balance'            => $supplier_data['balance'],
                        'balance_2'          => $supplier_data['balance_2'],
                        'options'            => 1,
                        'comment'            => 'Thêm mới nhà cung cấp',
                        'date' => date('Y-m-d H:i:s'),
                        'bat_dau' => 1,
                    );

                    $store_supplier_accounts_data[] = array(
                        'supplier_id'        => $supplier_id,
                        'receiving_id'       => NULL,
                        'transaction_amount' => 0,
                        'balance'            => $supplier_data['balance'],
                        'balance_2'          => $supplier_data['balance_2'],
                        'options'            => 2,
                        'comment'            => 'Thêm mới nhà cung cấp',
                        'date' => date('Y-m-d H:i:s'),
                        'bat_dau' => 1,

                    );
     
                    $this->db->insert_batch('store_supplier_accounts',$store_supplier_accounts_data);
                   
                    return $supplier_data['person_id'];
                }
			}
			else
			{
                $supplier_info = $this->get_info($supplier_id);

				$this->db->where('person_id', $supplier_id);
				$success = $this->db->update('suppliers',$supplier_data);

                $store_supplier_accounts_data = array(
                    'supplier_id'        => $supplier_id,
                    'receiving_id'       => NULL,
                    'transaction_amount' => 0,
                    'balance'            => $supplier_data['balance'],
                    'balance_2'          => $supplier_data['balance_2'],
                    'options'            => 1,
                    'comment'            => 'Chỉnh sửa công nợ nhà cung cấp',
                    'date' => date('Y-m-d H:i:s'),
                    'bat_dau' => 1,
                    );

                // $this->db->delete('store_supplier_accounts', array('supplier_id' => $supplier_id,'sale_id' => NULL,'options' => 1));
                // $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);

                $store_supplier_accounts_data = array(
                    'supplier_id'        => $supplier_id,
                    'receiving_id'       => NULL,
                    'transaction_amount' => 0,
                    'balance'            => $supplier_data['balance'],
                    'balance_2'          => $supplier_data['balance_2'],
                    'options'            => 2,
                    'comment'            => 'Chỉnh sửa công nợ nhà cung cấp',
                    'date' => date('Y-m-d H:i:s'),
                    'bat_dau' => 1,
                );

                // $this->db->delete('store_supplier_accounts', array('supplier_id' => $supplier_id,'sale_id' => NULL,'options' => 2));
                // $this->db->insert('store_supplier_accounts',$store_supplier_accounts_data);
               
			}
			
		}
		
		return $success;
	}
	
	/*
	Deletes one supplier
	*/
	function delete($supplier_id)
	{
		$supplier_info = $this->Supplier->get_info($supplier_id);
	
		if ($supplier_info->image_id !== NULL)
		{
			$this->load->model('Appfile');
			$this->Person->update_image(NULL,$supplier_id);
			$this->Appfile->delete($supplier_info->image_id);			
		}

        $this->reset_attributes(array('entity_id' => $supplier_id, 'entity_type' => 'suppliers'));
		
		$this->db->where('person_id', $supplier_id);
		return $this->db->update('suppliers', array('deleted' => 1));
	}
	
	/*
	Deletes a list of suppliers
	*/
	function delete_list($supplier_ids)
	{
		foreach($supplier_ids as $supplier_id)
		{
			$supplier_info = $this->Supplier->get_info($supplier_id);
		
			if ($supplier_info->image_id !== NULL)
			{
				$this->load->model('Appfile');
				$this->Person->update_image(NULL,$supplier_id);
				$this->Appfile->delete($supplier_info->image_id);			
			}			
		}

        $this->mass_reset_attributes(array('entity_ids' => $supplier_ids, 'entity_type' => 'suppliers'));
		$this->db->where_in('person_id',$supplier_ids);
		return $this->db->update('suppliers', array('deleted' => 1));
 	}

	/*
	Get search suggestions to find suppliers
	*/
	function get_supplier_search_suggestions($search,$limit=25)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("company_name,email,image_id,suppliers.person_id, MATCH (company_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (company_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);	
			$this->db->order_by("rel DESC");
		
			$by_company_name = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_company_name->result() as $row)
			{
				$data = array(
						'name' => $row->company_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}

			$this->db->select("first_name,last_name,email,image_id,suppliers.person_id, MATCH (first_name,last_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
		
			$this->db->where("(MATCH (first_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) or MATCH (last_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) or MATCH (first_name,last_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)) and ".$this->db->dbprefix('suppliers').".deleted=0", NULL, FALSE);			
			$this->db->limit($limit);	
			$this->db->order_by("rel DESC");
		
			$by_name = $this->db->get();
				
			$temp_suggestions = array();
			foreach($by_name->result() as $row)
			{
				$data = array(
						'name' => $row->last_name.', '.$row->first_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
		
			$this->db->select("first_name, last_name,email,image_id,suppliers.person_id, MATCH (email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by("rel DESC");
		
			$by_email = $this->db->get();

			$temp_suggestions = array();
			foreach($by_email->result() as $row)
			{
				$data = array(
						'name' => $row->first_name.' '.$row->last_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}

			$this->db->select("phone_number,email,image_id,suppliers.person_id, MATCH (phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);	
			$this->db->order_by("rel DESC");
			
			$by_phone = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_phone->result() as $row)
			{
				$data = array(
						'name' => $row->phone_number,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
			$this->db->select("account_number,email,image_id,suppliers.person_id, MATCH (account_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (account_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by("rel DESC");
		
			$by_account_number = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_account_number->result() as $row)
			{
				$data = array(
						'name' => $row->account_number,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
			
		}
		else
		{
			$this->db->select("company_name,email,image_id,suppliers.person_id", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->like("company_name",$search);
			$this->db->limit($limit);
		
			$by_company_name = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_company_name->result() as $row)
			{
				$data = array(
						'name' => $row->company_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}

			$this->db->select("first_name,last_name,email,image_id,suppliers.person_id", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
		
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or
			CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");			

			$this->db->limit($limit);	
		
			$by_name = $this->db->get();
				
			$temp_suggestions = array();
			foreach($by_name->result() as $row)
			{
				$data = array(
						'name' => $row->last_name.', '.$row->first_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
		
			$this->db->select("first_name, last_name, email,image_id,suppliers.person_id", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->like('email', $search);			
			$this->db->limit($limit);
		
			$by_email = $this->db->get();

			$temp_suggestions = array();
			foreach($by_email->result() as $row)
			{
				$data = array(
						'name' => $row->first_name.' '.$row->last_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}

			$this->db->select("phone_number,email,image_id,suppliers.person_id", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->like('phone_number', $search);			
			$this->db->limit($limit);	
			
			$by_phone = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_phone->result() as $row)
			{
				$data = array(
						'name' => $row->phone_number,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
			$this->db->select("account_number,email,image_id,suppliers.person_id", false);
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->like('account_number', $search);			
			$this->db->limit($limit);
		
			$by_account_number = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_account_number->result() as $row)
			{
				$data = array(
						'name' => $row->account_number,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	
	}

	/*
	Perform a search on suppliers
	*/
	function search($search, $limit=20,$offset=0,$column='company_name',$orderby='asc')
	{
	    $availableLocations = $this->Location->getLocationsWithChild();
	    $availableLocationIds = array_map(function($location){
	        return $location['location_id'];
	    }, $availableLocations);
        $availableLocationIds[] = 0;
			$this->db->from('suppliers');
	 		$this->db->join('people','suppliers.person_id=people.person_id');
	 		$this->db->where_in('created_at_location', $availableLocationIds);
			if ($search)
			{
				if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
				{
					$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or MATCH(account_number, company_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('suppliers'). ".deleted=0", NULL, FALSE);		
				}
				else
				{
					$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					email LIKE '%".$this->db->escape_like_str($search)."%' or 
					phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					account_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
				}
			}	
			else
			{
				$this->db->where('deleted',0);
			}
			
			// if (!$this->config->item('speed_up_search_queries'))
			// {

			// 	$this->db->order_by($column, $orderby);
			

	 	// 	}
			$this->db->limit($limit);
	 		$this->db->offset($offset);
	 		return $this->db->get();	
	}
	
	function search_count_all($search, $limit=10000)
	{
			$this->db->from('suppliers');
	 		$this->db->join('people','suppliers.person_id=people.person_id');
			if ($search)
			{
				if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
				{
					$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or MATCH(account_number, company_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('suppliers'). ".deleted=0", NULL, FALSE);		
				}
				else
				{
					$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					email LIKE '%".$this->db->escape_like_str($search)."%' or 
					phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					account_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
				}
			}	
			else
			{
				$this->db->where('deleted',0);
			}
	 		$this->db->limit($limit);
			$result=$this->db->get();				
			return $result->num_rows();		 		
	}
	
	function find_supplier_id($search)
	{
		if ($search)
		{
			$this->db->select("suppliers.person_id");
			$this->db->from('suppliers');
			$this->db->join('people','suppliers.person_id=people.person_id');
				
			//Can't use full text index due to transactions not being able to use this info
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`first_name`,' ',`last_name`) LIKE '".$this->db->escape_like_str($search)."%' or
			company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			email LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
			
			if (!$this->config->item('speed_up_search_queries'))
			{
				$this->db->order_by("last_name", "asc");
			}
			$query = $this->db->get();
		
			if ($query->num_rows() > 0)
			{
				return $query->row()->person_id;
			}
		}
		
		return null;
	}
	
	function cleanup()
	{
		$supplier_data = array('account_number' => null);
		$this->db->where('deleted', 1);
		return $this->db->update('suppliers',$supplier_data);
	}

    public function get_person_import_fields()
    {
        return $this->person_import_fields;
    }

    function get_info_by_ids($supplier_ids) {
        $this->db->select("s.company_name, s.person_id")
                ->from('suppliers AS s')
                ->where('s.person_id IN ('.implode(',', $supplier_ids).')');

        $query = $this->db->get();
        $result_tmp = $query->result_array();

        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['person_id']] = $val;
        }

        return $result;
    }

    function get_information($supplier_id) {;
        $this->db->select("p.first_name, p.last_name, p.phone_number, p.address_1, p.address_2, s.company_name, s.id, s.person_id")
                ->from('suppliers AS s')
                ->join('people AS p', 's.person_id = p.person_id', 'left')
                ->where('s.person_id', $supplier_id);

        $query = $this->db->get();
        $result = $query->row_array();

        return $result;
    }

    function item_select_box($arrParams = null, $options = null) {
        $this->db->select("s.company_name, s.person_id")
                ->from('suppliers AS s');

        $query = $this->db->get();
        $result_tmp = $query->result_array();
        $this->db->flush_cache();

        $result[-1] = '-- Chọn Nhà cung cấp --';
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['person_id']] = $val['company_name'];
        }

        return $result;
    }
    function getSupplierByName($name) {
        $this->db->select("s.company_name, s.person_id")
                ->from('suppliers AS s')
                ->where('company_name',$name);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }





    // lấy danh mục bên thứ ba
 function get_danh_muc_ben_thu_ba($customers_table = false, $parrent_id = false, $iValid = false)
	{
		if ($customers_table) {
	            //chỉ lấy danh mục cha
			$this->db->from($customers_table);
			$this->db->where('parrent_id', 0);
			$result = $this->db->get();
			$data = !empty($result) ? $result->result_array() : [];
			if ($parrent_id) {
	                // lấy danh mục con
				$this->db->from($customers_table);
				$this->db->where('parrent_id', $parrent_id);
				$result = $this->db->get();
				$data = !empty($result) ? $result->result_array() : [];
			}
		} else {
			if ($iValid) {
				$this->db->from('business_type');
				$data['business_type'] = $this->db->get()->result_array();
				$this->db->from('geographical_area');
				$data['geographical_area'] = $this->db->get()->result_array();
			} else {
				$data = null;
			}

		}
		return $data;
	}

	 // kiểm tra để hiển thị dữ liệu danh mục nào đã được chọn
    function kiem_tra_danh_muc_supplier($table_name, $supplier_id, $id_danh_muc)
    {
        $this->db->select($table_name . '_id');
        $this->db->from('suppliers_' . $table_name);
        $this->db->where('supplier_id', $supplier_id);
        $result = $this->db->get()->result_array();
        $authed_suppliers = array();
        foreach ($result as $value) {
            $authed_suppliers[$value[$table_name . '_id']] = TRUE;
        }
        // kết quả trả về sẽ là true or false cho biến $has_access
        return isset($authed_suppliers[$id_danh_muc]) && $authed_suppliers[$id_danh_muc];
    }




    // Lấy danh sách loại đơn vị bên thứ 3
    function get_unit_type()
    {
    	$query = $this->db->get('unit_type');
        return $query->result_array();
    }


    //  Láy dnah sách hình thức công ty
    function get_company_form()
    {
    	$query = $this->db->get('company_form');
    	return $query->result_array();
    }


   // lưu bảng quan hệ với supplier, phần danh mục bên thứ ba
    public function thay_doi_bang_danh_muc_lien_ket($table_name = NULL, $supplier_danh_muc_data = NULL, $supplier_id = NULL)
    {
        /*
    	* xóa tất cả dữ liệu có liên quan tới customers đang updata
    	* lưu mới thành trường khác
    	*/
        $this->db->where('supplier_id', $supplier_id);
        $this->db->delete('suppliers_' . $table_name);
        /*
    	* chèn dữ liệu mới vào bảng
    	*/
        foreach ($supplier_danh_muc_data as $value) {
            // biến value tương ứng với business_type_id <> danh mục id
            $this->db->insert('suppliers_' . $table_name, array('supplier_id' => $supplier_id, $table_name . '_id' => $value));
        }

    }

     // lấy thông tin liên hệ
    public function lay_thong_tin_lien_he_them($supplier_id = NULL)
    {
        $this->db->from('suppliers_delegate');
        $this->db->where('supplier_id', $supplier_id);
        $results = $this->db->get()->row_array();
        return $results;
    }


    // Lấy đầu mối
    public function lay_thong_tin_dau_moi_them($supplier_id =null)
    {


    	$this->db->from('suppliers_head');
    	$this->db->where('supplier_id',$supplier_id);
    	$results = $this->db->get()->result_array();
    	return $results;

    }


    // Lưu thông tin người đại diện
    public function save_delegate($supplier_id = null,$delegate)
    {
    	$this->db->where('supplier_id',$supplier_id);
    	$this->db->delete('suppliers_delegate');
    	// Chèn db
    	$this->db->insert('suppliers_delegate',$delegate);

    }


    public function save_head($supplier_id=null, $head)
    {
    	$this->db->where('supplier_id',$supplier_id);
    	$this->db->delete('suppliers_head');
    	// Chèn db
    	$this->db->insert_batch('suppliers_head',$head);
    }


    /*
    Lấy toàn bộ thông tin của Loai đơn vị
    @return array
    */
    public function get_all_unit_type()
    {	
    	$query = $this->db->get('phppos_unit_type');
    	return $query->result_array();
    }


    public function report_supplier($suppiler_id)
    {
    	$this->db->select('phppos_receivings_items.receiving_id,phppos_tasks.name as task_name,phppos_contract.name as contract_name, 
phppos_suppliers.company_name as supplier_name, phppos_items.name as item_name, phppos_receivings_items.item_unit_price, phppos_receivings.receiving_time,
phppos_receivings.task_id,phppos_contract.status,phppos_employees.username,phppos_contract.id as contract_id');
    	$this->db->from('phppos_receivings_items');
    	$this->db->join('phppos_receivings', 'phppos_receivings.receiving_id = phppos_receivings_items.receiving_id', 'left');
    	$this->db->join('phppos_tasks', 'phppos_receivings.task_id = phppos_tasks.id', 'left');
    	$this->db->join('phppos_suppliers', 'phppos_receivings.supplier_id = phppos_suppliers.person_id', 'left');
    	$this->db->join('phppos_items', 'phppos_items.item_id = phppos_receivings_items.item_id', 'left');
    	$this->db->join('phppos_sales', 'phppos_receivings.task_id = phppos_sales.task_id', 'left');
    	$this->db->join('phppos_contract', 'phppos_sales.sale_id = phppos_contract.sale_id', 'left');
    	$this->db->join('phppos_employees', 'phppos_contract.created_by  = phppos_employees.id', 'left');
    	$this->db->where('phppos_receivings.supplier_id', $suppiler_id);
    	$this->db->order_by('receiving_time', 'desc');
    	return $this->db->get()->result_array();

    }







}






?>
