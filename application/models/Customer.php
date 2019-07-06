<?php
class Customer extends Person
{
	# bảng customers
    protected $import_fields = array(
        'account_number' => 'account_number',
        'balance' => 'balance',
        'balance_2' => 'balance_2',
        'credit_limit' => 'credit_limit',
        'points' => 'points',
        'sex' => 'sex',
        'created_by' => 'created_by',
        'tier_id' => 'tier_id',// phân cấp khách hàng
        'type_customer'=>'type_customer', // nhóm khách hàng
        'business_registration'=>'business_registration', // số đăng ký kinh doanh
        'code_tax'=>'code_tax',// mã số thuế
    );
    # bảng people
    protected $person_import_fields = array(
        'code' => 'code', // mã khách hàng
        'last_name' => 'last_name', // họ và tên
        'birth_date' => 'birth_date',
        'email' => 'email',
		'website'=>'website',					 
        'phone_number' => 'phone_number',
		'fax_number'=>'fax_number',						   
        'address_1' => 'address_1',
        'comments' => 'comments',
    );
    protected $customers_contract_info_add = array(
    	'name_more' => 'name_more', // tên người đại diện
    	'sdt' => 'sdt', // số điện thoại người đại diện
    	'email_more' => 'email_more', // emai người đại diện
    	'phongban' => 'phongban',
    	'note' => 'note',
    	);

	/*
	Determines if a given person_id is a customer
	*/
	function exists($person_id)
	{
		$this->db->from('customers');	
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id',$person_id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}

	function account_number_exists($account_number)
	{
		$this->db->from('customers');	
		$this->db->where('account_number',$account_number);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function customer_id_from_account_number($account_number)
	{
		$this->db->from('customers');	
		$this->db->where('account_number',$account_number);
		$query = $this->db->get();
		
		if ($query->num_rows()==1)
		{
			return $query->row()->person_id;
		}
		
		return false;
	}
	
	/*
	Returns all the customers
	*/
	function get_all($limit=10000, $offset=0,$col='last_name',$order='asc')
	{
		
		$order_by = '';
		
		if (!$this->config->item('speed_up_search_queries'))
		{
			$order_by="ORDER BY ".$col." ". $order;
		}
		
		$people=$this->db->dbprefix('people');
		$customers=$this->db->dbprefix('customers');
		$data=$this->db->query("SELECT * 
						FROM ".$people."
						STRAIGHT_JOIN ".$customers." ON 										                       
						".$people.".person_id = ".$customers.".person_id
						WHERE deleted =0 $order_by 
						LIMIT  ".$offset.",".$limit);		
						
		return $data;
	}
	
	function count_all()
	{
		$this->db->from('customers');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular customer
	*/
	function get_info($customer_id,$can_cache = FALSE)
	{
		if ($can_cache)
		{
			static $cache  = array();
		
			if (isset($cache[$customer_id]))
			{
				return $cache[$customer_id];
			}
		}
		else
		{
			$cache = array();
		}
		$this->db->from('customers');	
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id',$customer_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			$cache[$customer_id] = $query->row();
			return $cache[$customer_id];
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT an customer
			$person_obj=parent::get_info(-1);
			
			//Get all the fields from customer table
			$fields = $this->db->list_fields('customers');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}
			
			return $person_obj;
		}
	}
	
	// lấy thông tin khách hàng theo nhóm khách hàng
	function get_info_theo_nhom_khach_hang($type_customer,$can_cache = FALSE)
	{
		if ($can_cache)
		{
			static $cache  = array();
		
			if (isset($cache[$type_customer]))
			{
				return $cache[$type_customer];
			}
		}
		else
		{
			$cache = array();
		}
		$this->db->from('customers');	
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.type_customer',$type_customer);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			$cache[$type_customer] = $query->row();
			return $cache[$type_customer];
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT an customer
			$person_obj=parent::get_info(-1);
			
			//Get all the fields from customer table
			$fields = $this->db->list_fields('customers');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}
			
			return $person_obj;
		}
	}
	/*
	Gets information about multiple customers
	*/
	function get_multiple_info($customer_ids)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');		
		$this->db->where_in('customers.person_id',$customer_ids);
		$this->db->order_by("last_name", "asc");
		return $this->db->get();		
	}
	
	/*
	Inserts or updates a customer
	*/
	function save_customer(&$person_data, &$customer_data,$customer_id=false)
	{
		$success=false;
		//Run these queries as a transaction, we want to make sure we do all or nothing
		if(parent::save($person_data,$customer_id))
		{

			// echo 'ceht lun';
			
			if ($customer_id && $this->exists($customer_id))
			{
				$cust_info = $this->get_info($customer_id);
				
				$current_balance = $cust_info->balance;
                $current_balance_2 = $cust_info->balance_2;
				
				//Insert store balance transaction when manually editing
				    if (isset($customer_data['balance']) && $customer_data['balance'] != $current_balance)
				    {
                        $store_account_transaction = array(
                            'customer_id'=>$customer_id,
                            'sale_id'=>NULL,
                            'comment'=>lang('customers_manual_edit_of_balance'),
                            'transaction_amount'=>0,
                            'balance'=>$customer_data['balance'],
                            'balance_2'=>0,
                            'options' => 1,
                            'date' => date('Y-m-d H:i:s'),
                            'bat_dau' => 1,
				        );

				        $this->db->delete('store_accounts', array('customer_id' => $customer_id,'sale_id' => NULL,'options' => 1));
                        $this->db->insert('store_accounts',$store_account_transaction);
                         $store_account_transaction_manual_change_or_import_excel = array(
                            'customer_id'=>$customer_id,
                            'comment'=>lang('customers_manual_edit_of_balance'),
                            'transaction_amount'=>$customer_data['balance'] - $current_balance,
                            'options' => 1,
                            'date' => date('Y-m-d H:i:s'),
				        );
                        $this->db->insert('phppos_store_accounts_manual_change_or_import_excel',$store_account_transaction_manual_change_or_import_excel);
                    }

                    if (isset($customer_data['balance_2']) && $customer_data['balance_2'] != $current_balance_2)
                    {
                        $store_account_transaction = array(
                            'customer_id'=>$customer_id,
                            'sale_id'=>NULL,
                            'comment'=>lang('customers_manual_edit_of_balance'),
                            'transaction_amount'=>0,
                            'balance'=>0,
                            'balance_2'=>$customer_data['balance_2'],
                            'options' => 2,
                            'date' => date('Y-m-d H:i:s'),
                            'bat_dau' => 1,
                        );
                        $this->db->delete('store_accounts', array('customer_id' => $customer_id,'sale_id' => NULL,'options' => 2));
                        $this->db->insert('store_accounts',$store_account_transaction);
                         $store_account_transaction_manual_change_or_import_excel = array(
                            'customer_id'=>$customer_id,
                            'comment'=>lang('customers_manual_edit_of_balance'),
                            'transaction_amount'=>$customer_data['balance_2'] - $current_balance_2,
                            'options' => 1,
                            'date' => date('Y-m-d H:i:s'),
				        );
                        $this->db->insert('phppos_store_accounts_manual_change_or_import_excel',$store_account_transaction_manual_change_or_import_excel);

                    }
			}
						
			if (!$customer_id or !$this->exists($customer_id))
			{
				$customer_data['person_id'] = $person_data['person_id'];
				$success = $this->db->insert('customers',$customer_data);
                $customer_id = $customer_data['person_id'];
                $store_account_transaction_data = array();
                $store_account_transaction_data[] = array(
                    'customer_id'=>$customer_id,
                    'sale_id'=>NULL,
                    'comment'=>'import bằng file excel',
                    'transaction_amount'=>0,
                    'balance'=>isset($customer_data['balance']) ? $customer_data['balance']:0,
                    'balance_2'=>isset($customer_data['balance_2']) ? $customer_data['balance_2']:0,
                    'options' => 1,
                    'date' => date('Y-m-d H:i:s'),
                    'bat_dau' => 1,
                );

                $store_account_transaction_data[] = array(
                    'customer_id'=>$customer_id,
                    'sale_id'=>NULL,
                    'comment'=>'import bằng file excel',
                    'transaction_amount'=>0,
                    'balance'=>isset($customer_data['balance']) ? $customer_data['balance']:0,
                    'balance_2'=>isset($customer_data['balance_2']) ? $customer_data['balance_2']:0,
                    'options' => 2,
                    'date' => date('Y-m-d H:i:s'),
                    'bat_dau' => 1,
                );

                $this->db->delete('store_accounts', array('customer_id' => $customer_id,'sale_id' => NULL));
                $this->db->insert_batch('store_accounts',$store_account_transaction_data);
                
                $store_account_transaction_manual_change_or_import_excel[] = array(
                            'customer_id'=>$customer_id,
                            'comment'=>lang('customers_manual_edit_of_balance'),
                            'transaction_amount'=>isset($customer_data['balance']) ? $customer_data['balance']:0,
                            'options' => 1,
                            'date' => date('Y-m-d H:i:s'),
				        );
                $store_account_transaction_manual_change_or_import_excel[] = array(
                            'customer_id'=>$customer_id,
                            'comment'=>lang('customers_manual_edit_of_balance'),
                            'transaction_amount'=>isset($customer_data['balance_2']) ? $customer_data['balance_2']:0,
                            'options' => 2,
                            'date' => date('Y-m-d H:i:s'),
				        );
               $this->db->insert_batch('phppos_store_accounts_manual_change_or_import_excel',$store_account_transaction_manual_change_or_import_excel);
                $success = $customer_data['person_id'];
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$success = $this->db->update('customers',$customer_data);
			}			
		}

		return $success;
	}
	
	/*
	Deletes one customer
	*/
	function delete($customer_id)
	{
		$customer_info = $this->Customer->get_info($customer_id);
	
		if ($customer_info->image_id !== NULL)
		{
			$this->load->model('Appfile');
			$this->Person->update_image(NULL,$customer_id);
			$this->Appfile->delete($customer_info->image_id);			
		}

        $this->reset_attributes(array('entity_id' => $customer_id, 'entity_type' => 'customers'));
		
		$this->db->where('person_id', $customer_id);
		return $this->db->update('customers', array('deleted' => 1));
	}
	
	/*
	Deletes a list of customers
	*/
	function delete_list($customer_ids)
	{
		foreach($customer_ids as $customer_id)
		{
			$customer_info = $this->Customer->get_info($customer_id);
		
			if ($customer_info->image_id !== NULL)
			{
				$this->Person->update_image(NULL,$customer_id);
				$this->load->model('Appfile');
				$this->Appfile->delete($customer_info->image_id);			
			}			
		}

        $this->mass_reset_attributes(array('entity_ids' => $customer_ids, 'entity_type' => 'customers'));
		
		$this->db->where_in('person_id',$customer_ids);
		return $this->db->update('customers', array('deleted' => 2));
 	}

 	function deletes($customer_ids)
 	{
 		foreach($customer_ids as $customer_id)
		{
			$customer_info = $this->Customer->get_info($customer_id);
		
			if ($customer_info->image_id !== NULL)
			{
				$this->Person->update_image(NULL,$customer_id);
				$this->load->model('Appfile');
				$this->Appfile->delete($customer_info->image_id);			
			}			
		}

        $this->mass_reset_attributes(array('entity_ids' => $customer_ids, 'entity_type' => 'customers'));
		
		$this->db->where_in('person_id',$customer_ids);
		return $this->db->update('customers', array('deleted' => 1));

 	}
	
	function check_duplicate($name,$email,$phone_number, $id = 0)
	{
		if (!$email)
		{
			//Set to an email no one would have
			$email = 'no-reply@mg.4biz.vn';
		}
		
		if(!$phone_number)
		{
			//Set to phone number no one would have
			$phone_number = '555-555-5555';
		}
		
		$this->db->from('customers');
		$this->db->join('people','customers.person_id=people.person_id');	
		$this->db->where('deleted',0);		
		$this->db->where("CONCAT(first_name,' ',last_name) = ".$this->db->escape($name).' or email='.$this->db->escape($email).' or phone_number='.$this->db->escape($phone_number));
		$query=$this->db->get();
		if($query->num_rows()>0)
		{
			return true;
		}
		
		return false;
	}
	

	function goi_y_khach_hang($search,$limit=25){

		$lo = $this->Employee->get_logged_in_employee_current_location_id();		
		$this->db->select('p.last_name as label,p.person_id as value,p.code as subtitle,p.image_id as avatar,p.email');
		$this->db->from('phppos_customers as c');
		$this->db->join('phppos_people as p', 'c.person_id = p.person_id');
		$this->db->where('c.created_location_id', $lo);
		$this->db->group_start();
		$this->db->like('p.last_name', $search, 'BOTH');
		$this->db->or_like('p.email', $search, 'BOTH');
		$this->db->or_like('code', $search, 'BOTH');
		$this->db->group_end();
		$this->db->group_by('p.person_id');
		$this->db->limit($limit);
		$cs =  $this->db->get()->result_array();
		foreach ($cs as $key => &$value) {
			$value['avatar'] = ($value['avatar'])? (base_url('app_files/view/'.$value['avatar'])) : (base_url('/assets/img/user.png'));
		}
		return $cs;
	}
	function get_customer_search_suggestions($search,$limit=25)
	{
		
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("customers.person_id, phone_number, first_name, last_name, email, image_id,MATCH (first_name,last_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($searchText)."%' or 
				last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				email LIKE '%".$this->db->escape_like_str($search)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				account_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`last_name`,', ',`first_name`, ' (',".$this->db->dbprefix('customers').".person_id,')') LIKE '%".$this->db->escape_like_str($search)."%'
				) and deleted=0");				
		
			$this->db->limit($limit);	
			$this->db->order_by('rel DESC');
		
			$by_name = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_name->result() as $row)
			{
				$name_label = $row->last_name.', '.$row->first_name. ' ('.$row->person_id.')';
				
				if ($row->phone_number)
				{
					$name_label.=' ('.$row->phone_number.')';
				}
				
				$data = array(
					'name' => $name_label,
					'email' => $row->email,
					'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
					 );
				$temp_suggestions[$row->person_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
			$this->db->select("customers.person_id, account_number, image_id, email, MATCH (account_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->where("MATCH (account_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
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
				$suggestions[]=array('value'=> $key, 'label' => $value['account_number'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
		
			$this->db->select("customers.person_id, first_name, last_name,email, image_id,MATCH (account_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->where("MATCH (email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
			$by_email = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_email->result() as $row)
			{
				$data = array(
						'name' => $row->first_name.'&nbsp;'.$row->last_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['email'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
			$this->db->select("customers.person_id, phone_number, email, image_id, MATCH (phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->where("MATCH (phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
			$by_phone_number = $this->db->get();
		
		
			$temp_suggestions = array();
		
			foreach($by_phone_number->result() as $row)
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
		
			$this->db->select("customers.person_id, company_name, email, image_id, MATCH (company_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->where("MATCH (company_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
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
		}
		else
		{
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
		
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or
			CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");			
		
			$this->db->limit($limit);	
			$by_name = $this->db->get();
			$temp_suggestions = array();
		
			foreach($by_name->result() as $row)
			{
				$name_label = $row->last_name.', '.$row->first_name.' ('.$row->person_id.')';
				
				if ($row->phone_number)
				{
					$name_label.=' ('.$row->phone_number.')';
				}
				
				$data = array(
					'name' => $name_label,
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
		
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("account_number",$search);
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
				$suggestions[]=array('value'=> $key, 'label' => $value['account_number'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
		
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("email",$search);
			$this->db->limit($limit);
			$by_email = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_email->result() as $row)
			{
				$data = array(
						'name' => $row->first_name.'&nbsp;'.$row->last_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['email'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
			
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("phone_number",$search);
			$this->db->limit($limit);
			$by_phone_number = $this->db->get();
		
		
			$temp_suggestions = array();
		
			foreach($by_phone_number->result() as $row)
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
		
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
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
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		}
			// echo $search;
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("code",$search);
			$this->db->limit($limit);
			$code = $this->db->get();
		// var_dump($code->result());
		
			$temp_suggestions = array();
		
			foreach($code->result() as $row)
			{
				$data = array(
						'name' => $row->last_name,
						'code' => $row->code,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;

			}

			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['code']);
			}

		//Cleanup blank entries
		for($k=count($suggestions)-1;$k>=0;$k--)
		{
			if (!$suggestions[$k]['label'])
			{
				unset($suggestions[$k]);
			}
		}
		
		//Probably not needed; but doesn't hurt
		$suggestions = array_values($suggestions);
		
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}
	/*
	Preform a search on customers
	*/
	function search($search, $limit=20,$offset=0,$column='last_name',$orderby='asc')
	{
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			
			if ($search)
			{
				if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
				{
					$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or MATCH(account_number, company_name, tax_certificate) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('customers'). ".deleted=0", NULL, FALSE);		
				}
				else
				{
					$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					email LIKE '%".$this->db->escape_like_str($search)."%' or 
					phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					account_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`last_name`,', ',`first_name`, ' (',".$this->db->dbprefix('customers').".person_id,')') LIKE '%".$this->db->escape_like_str($search)."%'
					) and deleted=0");		
				}
			}
			else
			{
				$this->db->where('deleted',0);
			}	
						
			if (!$this->config->item('speed_up_search_queries'))
			{
				$this->db->order_by($column,$orderby);				
			}
			$this->db->limit($limit);
			$this->db->offset($offset);
			return $this->db->get();
	}
	
	function search_count_all($search, $limit=10000)
	{
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');		

			if ($search)
			{
				if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
				{
					$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or MATCH(account_number, company_name, tax_certificate) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('customers'). ".deleted=0", NULL, FALSE);		
				}
				else
				{
					$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					email LIKE '%".$this->db->escape_like_str($search)."%' or 
					phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					account_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
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
		
	function cleanup()
	{
		$customer_data = array('account_number' => null,'type_customer' => null);
		
		$this->db->where('deleted', 1);
		$iValid = $this->db->update('customers',$customer_data);
		if($iValid) {
			$person_data = array(
			'phone_number' => '',
			'fax_number' => '',
			'email' => '',
			'first_name' => '',
			'last_name' => null,
			'code' => null,
			);
			$this->db->where('xoa', 1);
			$iValid = $this->db->update('people',$person_data);
		}
		return $iValid;
	}

    public function get_person_import_fields() {
        return $this->person_import_fields;
    }

    public function customers_contract_info_add() {
        return $this->customers_contract_info_add;
    }

}
?>
