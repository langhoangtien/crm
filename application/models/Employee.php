<?php
class Employee extends Person
{
    protected $import_fields = array(
        'username' => 'username',
        'password' => 'password',
        'employee_number' => 'employee_number',
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
	Determines if a given person_id is an employee
	*/
	function exists($person_id)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id',$person_id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
		
	function employee_username_exists($username)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.username',$username);
		$query = $this->db->get();
		
		
		if($query->num_rows()==1)
		{
			return $query->row()->username;
		}
	}	
	
	/*
	Returns all the employees
	*/
	function get_all($limit=10000, $offset=0,$col='last_name',$order='asc')
	{	
		$order_by = '';
		if (!$this->config->item('speed_up_search_queries'))
		{
			$order_by = "ORDER BY ".$col." ". $order;
		}
		
		$employees=$this->db->dbprefix('employees');
		$people=$this->db->dbprefix('people');
		$data=$this->db->query("SELECT * 
						FROM ".$people."
						JOIN ".$employees." ON 										                       
						".$people.".person_id = ".$employees.".person_id
						WHERE deleted =0 $order_by 
						LIMIT  ".$offset.",".$limit);		
						
		return $data;
	}
	
	function count_all()
	{
		$this->db->from('employees');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular employee
	*/
	function get_info($employee_id, $can_cache = TRUE)
	{
		if ($can_cache)
		{
			static $cache = array();
		
			if (isset($cache[$employee_id]))
			{
				return $cache[$employee_id];
			}
		}
		else
		{
			$cache = array();
		}
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id',$employee_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			$cache[$employee_id] = $query->row();
			return $cache[$employee_id];
		}
		else
		{
			//Get empty base parent object, as $employee_id is NOT an employee
			$person_obj=parent::get_info(-1);
			
			//Get all the fields from employee table
			$fields = $this->db->list_fields('employees');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets information about multiple employees
	*/
	function get_multiple_info($employee_ids)
	{
		$this->db->from('employees');
		$this->db->join('people', 'people.person_id = employees.person_id');		
		$this->db->where_in('employees.person_id',$employee_ids);
		$this->db->order_by("last_name", "asc");
		return $this->db->get();		
	}

	
	/*
	Gets information about multiple employees from multiple locations
	*/
	function get_multiple_locations_employees($location_ids)
	{
		$this->db->select('employee_id');
		$this->db->from('employees_locations');
		$this->db->where_in('location_id',$location_ids);
		$this->db->distinct();
		return $this->db->get();		
	}
	
	function save_profile(&$person_data, &$employee_data, $employee_id)
	{
		$success=false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
			
		if(parent::save($person_data,$employee_id))
		{
			if (!$employee_id or !$this->exists($employee_id))
			{
				$employee_data['person_id'] = $employee_id = $person_data['person_id'];
				$success = $this->db->insert('employees',$employee_data);
			}
			else
			{
				$this->db->where('person_id', $employee_id);
				$success = $this->db->update('employees',$employee_data);		
			}	
		}		
		$this->db->trans_complete();		
		return $success;	
	}
	/*
	Inserts or updates an employee
	*/
	function save_employee(&$person_data, &$employee_data,&$permission_data, &$permission_action_data, &$location_data, $employee_id=false)
	{
		$success=false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
			
		if(parent::save($person_data,$employee_id))
		{
			if (!$employee_id or !$this->exists($employee_id))
			{
				$employee_data['person_id'] = $employee_id = $person_data['person_id'];
				$success = $this->db->insert('employees',$employee_data);
			}
			else
			{
				$this->db->where('person_id', $employee_id);
				$success = $this->db->update('employees',$employee_data);		
			}
			
			//We have either inserted or updated a new employee, now lets set permissions. 
			if($success)
			{
				//First lets clear out any permissions the employee currently has.
				$success=$this->db->delete('permissions', array('person_id' => $employee_id));
				
				//Now insert the new permissions
				if($success)
				{
					foreach($permission_data as $allowed_module)
					{
						$success = $this->db->insert('permissions',
						array(
						'module_id'=>$allowed_module,
						'person_id'=>$employee_id));
					}
				}
				
				//First lets clear out any permissions actions the employee currently has.
				$success=$this->db->delete('permissions_actions', array('person_id' => $employee_id));
				
				//Now insert the new permissions actions
				if($success)
				{
					foreach($permission_action_data as $permission_action)
					{
						list($module, $action) = explode('|', $permission_action);
						if($module ==  'customers' &&($action == 'view_scope_all' || $action == 'view_scope_owner' || $action == 'view_scope_location'))
						{
							$success = $this->db->insert('permissions_actions',
							array(
							'module_id'=>'all',
							'action_id'=>$action,
							'person_id'=>$employee_id));
						}
						$success = $this->db->insert('permissions_actions',
						array(
						'module_id'=>$module,
						'action_id'=>$action,
						'person_id'=>$employee_id));
					}
				}
				
				$success=$this->db->delete('employees_locations', array('employee_id' => $employee_id));
				
				//Now insert the new employee locations
				if($success)
				{
					if ($location_data !== FALSE)
					{
						foreach($location_data as $location_id)
						{
							$success = $this->db->insert('employees_locations',
							array(
							'employee_id'=>$employee_id,
							'location_id'=>$location_id
							));
						}
				
					}
				}
				
			}
			
		}

		$this->db->trans_complete();
        if (!$success) {
            return $employee_id;
        }
		return $success;
	}
	
	function set_language($language_id,$employee_id)
	{

		$this->db->where('person_id', $employee_id);
		return $this->db->update('employees', array('language' => $language_id));
	}
	/*
	Deletes one employee
	*/
	function delete($employee_id)
	{
		$success=false;
		
		//Don't let employee delete their self
		if($employee_id==$this->get_logged_in_employee_info()->person_id)
			return false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		$employee_info = $this->Employee->get_info($employee_id);
	
		if ($employee_info->image_id !== NULL)
		{
			$this->load->model('Appfile');
			$this->Person->update_image(NULL,$employee_id);
			$this->Appfile->delete($employee_info->image_id);			
		}			
		
		//Delete permissions
		if($this->db->delete('permissions', array('person_id' => $employee_id)) && $this->db->delete('permissions_actions', array('person_id' => $employee_id)))
		{	
			$this->db->where('person_id', $employee_id);
			$success = $this->db->update('employees', array('deleted' => 1));
		}
		$this->db->trans_complete();
        $this->reset_attributes(array('entity_id' => $employee_id, 'entity_type' => 'employees'));
		return $success;
	}
	
	/*
	Deletes a list of employees
	*/
	function delete_list($employee_ids)
	{
		$success=false;
		
		//Don't let employee delete their self
		if(in_array($this->get_logged_in_employee_info()->person_id,$employee_ids))
			return false;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		foreach($employee_ids as $employee_id)
		{
			$employee_info = $this->Employee->get_info($employee_id);
		
			if ($employee_info->image_id !== NULL)
			{
				$this->load->model('Appfile');
				$this->Person->update_image(NULL,$employee_id);
				$this->Appfile->delete($employee_info->image_id);			
			}			
		}
		
		$this->db->where_in('person_id',$employee_ids);
		//Delete permissions
		if ($this->db->delete('permissions'))
		{
			//delete from employee table
			$this->db->where_in('person_id',$employee_ids);
			$success = $this->db->update('employees', array('deleted' => 1));
		}
		$this->db->trans_complete();
        $this->mass_reset_attributes(array('entity_ids' => $employee_ids, 'entity_type' => 'employees'));
		return $success;
 	}
	
		
	function check_duplicate($term)
	{
		$this->db->from('employees');
		$this->db->join('people','employees.person_id=people.person_id');	
		$this->db->where('deleted',0);		
		$query = $this->db->where("CONCAT(first_name,' ',last_name) = ".$this->db->escape($term));
		$query=$this->db->get();
		
		if($query->num_rows()>0)
		{
			return true;
		}	
	}
	
	/*
	Get search suggestions to find employees
	*/


	   function goi_y_nhan_vien($search,$limit=25){
        $lo = $this->Employee->get_logged_in_employee_current_location_id();    
        $this->db->select('p.person_id as value,p.email as subtitle,p.image_id as avatar,p.email,p.first_name as label');
        $this->db->from('phppos_employees as e');
        $this->db->join('phppos_people as p', 'p.person_id =e.person_id ');
        $this->db->join('phppos_employees_locations as el', 'el.employee_id = e.person_id');
        $this->db->where('el.location_id', $lo);
        $this->db->group_start();
        $this->db->like('p.first_name', $search, 'BOTH');
        $this->db->or_like('p.email', $search, 'BOTH');
        $this->db->or_like('e.username', $search, 'BOTH');
        $this->db->group_end();
        $this->db->group_by('p.person_id');
        $this->db->limit($limit);
        $se =  $this->db->get()->result_array();
        foreach ($se as $key => &$value) {
            $value['avatar'] = ($value['avatar'])? (base_url('app_files/view/'.$value['avatar'])) : (base_url('/assets/img/user.png'));
        }
        return $se;
    }
	function get_search_suggestions($search,$limit=5)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("first_name, last_name, email,image_id,employees.person_id,MATCH (first_name,last_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');
		
			$this->db->where("(MATCH (first_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) or MATCH (last_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) or MATCH (first_name,last_name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)) and ".$this->db->dbprefix('employees').".deleted=0", NULL, FALSE);			
		
			$this->db->limit($limit);	
			$this->db->order_by('rel DESC');

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
		
			$this->db->select("first_name, last_name, email,image_id,employees.person_id,MATCH (email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
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
		
			$this->db->select("username, email,image_id,employees.person_id,MATCH (username) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (username) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
			$by_username = $this->db->get();
			$temp_suggestions = array();
			foreach($by_username->result() as $row)
			{
				$data = array(
						'name' => $row->username,
						'email' => $row->email,
						'avatar' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;	
			}

		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}


			$this->db->select("phone_number, email,image_id,employees.person_id,MATCH (username) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
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
		}
		else
		{
			$this->db->select("first_name, last_name, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');
		
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
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
			$this->db->select("first_name, last_name, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');
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
		
			$this->db->select("username, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->like('username', $search);			
			$this->db->limit($limit);
		
			$by_username = $this->db->get();
			$temp_suggestions = array();
			foreach($by_username->result() as $row)
			{
				$data = array(
						'name' => $row->username,
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


			$this->db->select("phone_number, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');	
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
		}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	
	}
	
	
	
	/*
	Preform a search on employees
	*/
	function search($search, $limit=20,$offset=0,$column='last_name',$orderby='asc')
	{		
		$this->db->from('employees');
		$this->db->join('people','employees.person_id=people.person_id');		
		if ($search)
		{
			if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
			{
				$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or MATCH(username) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('employees'). ".deleted=0", NULL, FALSE);		
			}
			else
			{
				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				username LIKE '%".$this->db->escape_like_str($search)."%' or 
				email LIKE '%".$this->db->escape_like_str($search)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
			}
		}	
		else
		{
			$this->db->where('deleted',0);
		}
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($column, $orderby);
		}
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function search_count_all($search, $limit=10000)
	{
		$this->db->from('employees');
		$this->db->join('people','employees.person_id=people.person_id');		
		if ($search)
		{
			if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
			{
				$this->db->where("(MATCH (first_name, last_name, email, phone_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") or MATCH(username) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE"."))and ".$this->db->dbprefix('employees'). ".deleted=0", NULL, FALSE);		
			}
			else
			{
				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				username LIKE '%".$this->db->escape_like_str($search)."%' or 
				email LIKE '%".$this->db->escape_like_str($search)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
			}
		}	
		else
		{
			$this->db->where('deleted',0);
		}
		// $this->db->limit($limit);
		$result=$this->db->get();				
		return $result->num_rows();
	}
	
	/*
	Attempts to login employee and set session. Returns boolean based on outcome.
	*/
	function login($username, $password)
	{
		//Username Query
		$query = $this->db->get_where('employees', array('username' => $username,'password'=>md5($password), 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			$this->log_employee($row->person_id);
			return true;
		}
		
		//Employee Number Query
		$query = $this->db->get_where('employees', array('employee_number' => $username,'password'=>md5($password), 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);echo $this->last_query;
			return true;
		}
		
		return false;
	}
	
	function login_no_password($username)
	{
		//Username Query
		$query = $this->db->get_where('employees', array('username' => $username, 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			return true;
		}
		
		//Employee Number Query
		$query = $this->db->get_where('employees', array('employee_number' => $username, 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			return true;
		}
		
		return false;
	}
	
	/*
	Logs out a user by destorying all session data and redirect to login
	*/
	function logout($redirect_to_login = TRUE)
	{
		$this->log_employee($this->session->userdata('person_id'),2);
		$this->session->sess_destroy();
		
		if ($redirect_to_login)
		{
			redirect('login');
		}
	}
	
	/*
	Determins if a employee is logged in
	*/
	function is_logged_in()
	{
		return $this->session->userdata('person_id')!=false;
	}
	
	/*
	Gets information about the currently logged in employee.
	*/
	function get_logged_in_employee_info()
	{
		if($this->is_logged_in())
		{
			return $this->get_info($this->session->userdata('person_id'));
		}
		
		return false;
	}
	
	
	/*
	Gets the current employee's location. If they have more than 1, then a user can change during session
	*/
	function get_logged_in_employee_current_location_id()
	{
		if($this->is_logged_in())
		{
			//If we have a location in the session
			if ($this->session->userdata('employee_current_location_id')!==NULL)
			{
				return $this->session->userdata('employee_current_location_id');
			}
			
			//Return the first location user is authenticated for
			return current($this->get_authenticated_location_ids($this->session->userdata('person_id')));
		}
		
		return FALSE;
	}
	
	function get_current_location_info()
	{
		return $this->Location->get_info($this->get_logged_in_employee_current_location_id());
	}
		
	function set_employee_current_location_id($location_id)
	{
		if ($this->is_location_authenticated($location_id))
		{
			$this->session->set_userdata('employee_current_location_id', $location_id);
		}
	}
	
	/*
	Gets the current employee's register id (if set)
	*/
	function get_logged_in_employee_current_register_id()
	{
		if($this->is_logged_in())
		{
			//If we have a register in the session
			if ($this->session->userdata('employee_current_register_id')!==NULL)
			{
				return $this->session->userdata('employee_current_register_id');
			}
			
			return NULL;
		}
		
		return NULL;
	}
	
	function set_employee_current_register_id($register_id)
	{
		$this->session->set_userdata('employee_current_register_id', $register_id);
	}
	
	
	/*
	    Determines whether the employee specified employee has access the specific module.
	*/
	function has_module_permission($module_id, $person_id, $group_permission = true)
	{
		/* If no module_id is null, allow access */
		if ($module_id == null) {
			return true;
		}
		
		static $cache;
		
		if (isset($cache[$module_id . '|' . $person_id]))
		{
			return $cache[$module_id . '|' . $person_id];
		}

        /* Check Person Level */
		$query = $this->db->get_where('permissions', array('person_id' => $person_id,'module_id'=>$module_id), 1);
		$person_permission_allowed = ($query->num_rows() == 1);

        /* Check Group Level */
        $group_permission_allowed = false;
        if ($group_permission) {
            $person = $this->get_info($person_id);
            if (!empty($person->group_id)) {
                $group_permission_allowed = $this->has_module_group_permission($module_id, $person->group_id);
            }
        }

        /* Combined */
        $cache[$module_id.'|'.$person_id] = ($group_permission_allowed || $person_permission_allowed);

		return $cache[$module_id.'|'.$person_id];
	}
	
	function has_module_action_permission($module_id, $action_id, $person_id, $group_permission = true)
	{
		/* If no module_id is null, allow access */
		if ($module_id == null) {
			return true;
		}
		
		static $cache;
		
		if (isset($cache[$module_id . '|' . $action_id . '|' . $person_id])) {
			return $cache[$module_id . '|' . $action_id . '|' . $person_id];
		}

        /* Check Person Level */
		$query = $this->db->get_where('permissions_actions', array('person_id' => $person_id, 'module_id' => $module_id, 'action_id' => $action_id), 1);
        $person_permission_allowed = ($query->num_rows() == 1);

        /* Check Group Level */
        $group_permission_allowed = false;
        if ($group_permission) {
            $person = $this->get_info($person_id);
            if (!empty($person->group_id)) {
                $group_permission_allowed = $this->has_module_group_action_permission($module_id, $action_id, $person->group_id);
            }
        }

        /* Combined */
        $cache[$module_id . '|' . $action_id . '|' . $person_id] = ($group_permission_allowed || $person_permission_allowed);
        return $cache[$module_id.'|'.$action_id.'|'.$person_id];
	}

    /*
        Determines whether the employee specified employee has access the specific module.
    */
    function has_module_group_permission($module_id, $group_id)
    {
        /* If no module_id is null, allow access */
        if ($module_id == null) {
            return true;
        }

        static $cache;

        if (isset($cache['group|' . $module_id.'|'.$group_id])) {
            return $cache['group|' . $module_id.'|'.$group_id];
        }

        $query = $this->db->get_where('group_permissions', array('group_id' => $group_id, 'module_id' => $module_id), 1);
        $cache['group|' . $module_id . '|' . $group_id] = ($query->num_rows() == 1);

        return $cache['group|' . $module_id . '|' . $group_id];
    }

    function has_module_group_action_permission($module_id, $action_id, $group_id)
    {
        /* If no module_id is null, allow access */
        if ($module_id == null) {
            return true;
        }

        static $cache;

        if (isset($cache['group|' . $module_id . '|' . $action_id . '|' . $group_id])) {
            return $cache['group|' . $module_id . '|' . $action_id . '|' . $group_id];
        }

        $query = $this->db->get_where('group_permissions_actions', array('group_id' => $group_id,'module_id'=>$module_id,'action_id'=>$action_id), 1);
        $cache['group|' . $module_id . '|' . $action_id . '|' . $group_id] = ($query->num_rows() == 1);

        return $cache['group|' . $module_id . '|' . $action_id.'|' . $group_id];
    }
	
	function get_employee_by_username_or_email($username_or_email)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('username',$username_or_email);
		$this->db->or_where('email',$username_or_email);
		$query = $this->db->get();
		
		if ($query->num_rows() == 1)
		{
			return $query->row();
		}
		
		return false;
	}
	
	function update_employee_password($employee_id, $password, $force_password_change = 0)
	{
		$employee_data = array('password' => $password, 'force_password_change' => $force_password_change);
		$this->db->where('person_id', $employee_id);
		$success = $this->db->update('employees',$employee_data);
		
		return $success;
	}
		
	function cleanup()
	{
		$employee_data = array('username' => null);
		$this->db->where('deleted', 1);
		return $this->db->update('employees',$employee_data);
	}
		
	function get_employee_id($username)
	{
		$query = $this->db->get_where('employees', array('username' => $username, 'deleted'=>0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			return $row->person_id;
		}
		return false;
	}
	
	function get_authenticated_location_ids($employee_id)
	{
		static $cache;
		
		if (isset($cache[$employee_id]))
		{
			return $cache[$employee_id];
		}
		
		$this->db->select('employees_locations.location_id');
		$this->db->from('employees_locations');
		$this->db->join('locations', 'locations.location_id = employees_locations.location_id');
		$this->db->where('employee_id', $employee_id);
		$this->db->where('deleted', 0);
		$this->db->order_by('location_id', 'asc');
		
		$location_ids = array();
		
		foreach($this->db->get()->result_array() as $location)
		{
			$location_ids[] = $location['location_id'];
		}
		$cache[$employee_id] = $location_ids;
		return $location_ids;
	}
	
	function is_location_authenticated($location_id)
	{
		if ($employee = $this->get_logged_in_employee_info())
		{
			$this->db->select('location_id');
			$this->db->from('employees_locations');
			$this->db->where('employee_id', $employee->person_id);
			$this->db->where('location_id', $location_id);
			$result = $this->db->get();

			return $result->num_rows() == 1;
		}
		
		return FALSE;
	}
	
	function is_employee_authenticated($employee_id, $location_id)
	{
		static $authed_employees;
		
		if (!$authed_employees)
		{
			$this->db->select('employee_id');
			$this->db->from('employees_locations');
			$this->db->where('location_id', $location_id);
			$result = $this->db->get();
			$authed_employees = array();
			
			foreach($result->result_array() as $employee)
			{
				$authed_employees[$employee['employee_id']] = TRUE;
			}	
		}
		return isset($authed_employees[$employee_id]) && $authed_employees[$employee_id]; 
	}
	
	function clock_in($comment, $employee_id = false, $location_id = false)
	{
		if ($employee_id === FALSE)
		{
			$employee_id = $this->get_logged_in_employee_info()->person_id;
		}
		
		if ($location_id === FALSE)
		{
			$location_id = $this->get_logged_in_employee_current_location_id();
		}
		
		return $this->db->insert('employees_time_clock', array(
			'employee_id' => $employee_id,
			'location_id' => $location_id,
			'clock_in' => date('Y-m-d H:i:s'),
			'clock_in_comment' => $comment,
			'clock_out_comment' => '',
		));
		
	}
	
	function clock_out($comment, $employee_id = false, $location_id = false)
	{
		if ($employee_id === FALSE)
		{
			$employee_id = $this->get_logged_in_employee_info()->person_id;
		}
		
		$cur_emp_info = $this->get_info($employee_id);
		
		if ($location_id === FALSE)
		{
			$location_id = $this->get_logged_in_employee_current_location_id();
		}
		
		if ($this->is_clocked_in($employee_id, $location_id))
		{
			$this->db->limit(1);
			$this->db->where('clock_in !=','0000-00-00 00:00:00');
			$this->db->where('clock_out','0000-00-00 00:00:00');
			$this->db->where('employee_id',$employee_id);
			$this->db->where('location_id',$location_id);
			return $this->db->update('employees_time_clock', array('clock_out' => date('Y-m-d H:i:s'), 'clock_out_comment' => $comment, 'hourly_pay_rate' => $cur_emp_info->hourly_pay_rate));
		}
		
		return FALSE;
	}
	
	function is_clocked_in($employee_id = false, $location_id = false)
	{
		if ($employee_id === FALSE)
		{
			$employee_id = $this->get_logged_in_employee_info()->person_id;
		}
		
		if ($location_id === FALSE)
		{
			$location_id = $this->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('employees_time_clock');
		$this->db->where('clock_in !=','0000-00-00 00:00:00');
		$this->db->where('clock_out','0000-00-00 00:00:00');
		$this->db->where('employee_id',$employee_id);
		$this->db->where('location_id',$location_id);
		
		$query = $this->db->get();
		if($query->num_rows())
		return true	;
		else
		return false;
	
	 }
	 
	 function delete_timeclock($id)
	 {
		 return $this->db->delete('employees_time_clock', array('id' => $id));
	 }
	 
	 function get_timeclock($id)
	 {
 		$this->db->from('employees_time_clock');	
		$this->db->where('id', $id);
 		$query = $this->db->get();
		
 		if($query->num_rows()==1)
 		{
 			return $query->row();
 		}
		else
		{
			//Get empty object
			$timeclock_obj=new stdClass();
			
			//Get all the fields from employee table
			$fields = $this->db->list_fields('employees_time_clock');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$timeclock_obj->$field='';
			}
			
			return $timeclock_obj;
		}
		
		
		return false;
	 }
	 
	function save_timeclock($data)
	{
		$save_data = array();
		
		$clock_in_time = strtotime($data['clock_in']);
		$clock_out_time = strtotime($data['clock_out']);
		
		if ($clock_in_time !== FALSE)
		{
			$save_data['clock_in'] = date('Y-m-d H:i:s', $clock_in_time);
		}
		
		if ($clock_out_time !== FALSE)
		{
			$save_data['clock_out'] = date('Y-m-d H:i:s', $clock_out_time);
		}
		
		$save_data['employee_id'] = $data['employee_id'];
		$save_data['location_id'] = $data['location_id'];
		$save_data['clock_in_comment'] = $data['clock_in_comment'];
		$save_data['clock_out_comment'] = $data['clock_out_comment'];
		$save_data['hourly_pay_rate'] = $data['hourly_pay_rate'];
		if ($this->exists($save_data['employee_id']))
		{
			if ($data['id'] == -1)
			{
				return $this->db->insert('employees_time_clock', $save_data);
			}
			else
			{
				$this->db->where('id', $data['id']);
				return $this->db->update('employees_time_clock', $save_data);
			}
		}	
		
		return FALSE;
	}

	function save_message($data)
	{
		$message_data = array(
		'message'=>$data['message'],
		'created_at' => date('Y-m-d H:i:s'),
		'sender_id'=>$this->get_logged_in_employee_info()->person_id,
		);
		

			if($this->db->insert('messages', $message_data))
			{
				$message_id = $this->db->insert_id();


				if($data['all_employees']=="all")
				{
					
					if($data["all_locations"]=="all")
					{
						$employee_ids = array();

						foreach ($this->Location->get_all()->result() as $location)
						{
							$location_ids[] = $location->location_id;
						}

						$employee_ids = $this->get_multiple_locations_employees($location_ids)->result_array();

					}
					else
					{
						$employee_ids = $this->get_multiple_locations_employees($data['locations'])->result_array();

					}

					//Prepare the employees ids format 
					$person_ids = array();
					foreach ($employee_ids as $value) {

						$message_receiver = array(
						'message_id'=>$message_id,
						'receiver_id'=>$value['employee_id'],
					);	
						
						$this->db->insert('message_receiver',$message_receiver);		

					}

					return true;

				}
				else
				{
					foreach ($data["employees"] as $employee_id) {
							$message_receiver = array(
								'message_id'=>$message_id,
								'receiver_id'=>$employee_id,
							);	
								
								$this->db->insert('message_receiver',$message_receiver);	
					}

					return true;
				}

				return false;

				
			}
		
		
	}

	function get_messages($limit=20, $offset=0)
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;

		$this->db->from('messages');
		$this->db->join('message_receiver','messages.id=message_receiver.message_id');	
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->limit($limit,$offset);		
		$this->db->where('messages.deleted',0);		
		$this->db->order_by("created_at", "desc");
		$this->db->limit($limit);
		$this->db->offset($offset);
		$query=$this->db->get();

		return $query->result_array();
	}

	function get_messages_count()
	{
		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		
		$this->db->from('messages');
		$this->db->join('message_receiver','messages.id=message_receiver.message_id');	
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->where('messages.deleted',0);
		
		return $this->db->count_all_results();
	}
	
	function get_sent_messages($limit=20, $offset=0)
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->select('messages.*, GROUP_CONCAT('.$this->db->dbprefix('people').'.first_name, " ",'.$this->db->dbprefix('people').'.last_name SEPARATOR ", ") as sent_to', false);
		$this->db->from('messages');
		$this->db->join('message_receiver', 'message_receiver.message_id = messages.id');
		$this->db->join('people', 'people.person_id = message_receiver.receiver_id');
		$this->db->where('sender_id',$logged_employee_id);		
		$this->db->where('messages.deleted',0);		
		$this->db->order_by("created_at", "desc");
		$this->db->group_by('messages.id');
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		$query=$this->db->get();
		return $query->result_array();
	}
	
	function get_sent_messages_count()
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->from('messages');
		$this->db->where('sender_id',$logged_employee_id);		
		$this->db->where('messages.deleted',0);		
		
		return $this->db->count_all_results();
	}

	function get_unread_messages_count($limit=20, $offset=0)
	{
		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->from('message_receiver');
		$this->db->join('messages','messages.id=message_receiver.message_id');	
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->where('message_read',0);		
		$this->db->where('deleted',0);
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		return $this->db->count_all_results();
	}	 

	function read_message($message_id)
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->where('id', $message_id);
		return $this->db->update('message_receiver', array('message_read' => 1));		
	}

	function delete_message($message_id)
	{
		$this->db->where('id', $message_id);
		return $this->db->update('messages', array('deleted' => 1));		
	}

    public function get_person_import_fields()
    {
        return $this->person_import_fields;
    }


# cập nhật lại trạng thái đăng nhập của người dùng
    public function last_active($m=10)
    {

    	$s = $m*60;
    	$id = $this->get_logged_in_employee_info()->person_id;
    	$this->db->select('e.last_active');
    	$this->db->where('person_id',$id );
    	$time = $this->db->get('phppos_employees as e')->row()->last_active;
    	$now = date("Y-m-d H:i:s");
    	$diff = strtotime($now) - strtotime($time);
    	if($diff>$s)
    	{
    		$this->db->where('person_id', $id);
    		$this->db->update('phppos_employees', array('last_active'=>$now));
    	}


    }

    function get_employee_active($m=10)
    {
    	$s=$m*60;
        $now = date("Y-m-d H:i:s");
        $time = date("Y-m-d H:i:s",strtotime("$now -$s second"));
        $this->db->select('e.username,p.first_name,e.last_active');
        $this->db->from('phppos_employees as e');
        $this->db->join('phppos_people as p', 'p.person_id = e.person_id');
        // $this->db->where("DATE_ADD(last_active, INTERVAL 10 MINUTE) >= NOW()");
    	$this->db->where('e.last_active >=', $time);
    	$numer =  $this->db->get()->result_array();
    	foreach ($numer as $key => &$value) {
    		$value['last_active'] = $this->convert_time($value['last_active']);
    	}
    	return $numer;
    }


    function convert_time($time){
    	$t = (strtotime(date("Y-m-d H:i:s")) - strtotime($time))/60;
    	if($t<1)
    	$t=1;
    	$t = ceil($t);
    	if($t<60)
    		return $t." phút trước";
    	else{
    		$t = $t/60;
    		$t = ceil($t);
    		if($t<24)
    			return $t. "giờ trước";
    		else{
    			$t = $t/24;
    			$t=ceil($t);
    			return $t." ngày trước";
    		}
    	}

    }


    function get_contract_status_changing($id=null,$arrParam=null)
    {
    	$this->db->select('ac.contract_id,ac.time,ac.person_id,p.first_name as fullname,ac.type,c.name,e.username,ac.new_status as status');
    	$this->db->from('phppos_contract_employee_action ac');
    	$this->db->join('phppos_contract as c', 'ac.contract_id = c.id');
    	$this->db->join('phppos_people as p', 'ac.person_id = p.person_id');
    	$this->db->join('phppos_employees as e', 'e.person_id = ac.person_id');
    	if(!empty($id))
    	{
	    	$this->db->join('phppos_sales as s', 's.sale_id = c.sale_id');
	    	$this->db->join('phppos_task_user_relations as tu', 'tu.task_id = s.task_id');
	    	$this->db->where('tu.user_id', $id);
	    	$this->db->group_start();
	    	$this->db->where('tu.is_implement', 1);
	    	$this->db->or_where('tu.is_join', 1);
	    	$this->db->group_end();
    	}
    	if(!empty($arrParam['time']))
    	{
    		$t = $arrParam['time'];
    		$this->db->where('ac.time >=', $t);
    		$this->db->where('ac.time <', date("Y-m-d",strtotime("$t +1 day")));
    	}
    	$this->db->order_by('ac.id', 'desc');
    	if(!empty($arrParam['limit']))
    	{
    		$this->db->limit($arrParam['limit'],$arrParam['offset']);
    	}
    	$list =  $this->db->get()->result_array();
    	 $array = lang('contract_status');
    	 $array2=array(1=>"Thay đổi trạng thái giai đoạn",2=>"Thay đổi trạng thái hợp đồng");
    	     foreach ($list as $key => &$value) {
			        ($value['status'] = $array[$value['status']]);
			        $value['type'] =$array2[$value['type']];
			    }

			    return $list;
    }


    function update_contract_action($id,$new_status,$type){
    	$data['contract_id'] = $id;
    	$data['time'] = date("Y-m-d H:i:s");
    	$data['person_id'] = $this->session->userdata('person_id');
    	$data['new_status']= $new_status;
    	$data['type'] = $type;
    	$this->db->insert('phppos_contract_employee_action', $data);
    }


    function get_task_alert($id,$time=null){
    	if($time==null)
    	{

    	$time = intval($this->config->item('alert_time'));
        $time = $time ? $time :10;
        if($time>1000)
           $time =10;
    	}
    	$t = date("Y-m-d H:i:s");
    	$this->db->select('t.name,t.date_end,t.id,t.trangthai');
    	$this->db->from('phppos_tasks as t');
    	$this->db->join('phppos_task_user_relations as tu', 'tu.task_id = t.project_id');
    	$this->db->where('tu.user_id', $id);
    	$this->db->group_start();
    		$this->db->where('tu.is_implement', 1);
    		$this->db->or_where('tu.is_join', 1);
    		$this->db->or_where('tu.is_pheduyet',1);
    	$this->db->group_end();
    	// $this->db->where('t.date_end >',date("Y-m-d"));
    	$this->db->where('t.date_end <=',date("Y-m-d",strtotime("$t +$time day")));
    	$this->db->where('t.level', 1);
    	$this->db->where_in('t.trangthai',array(0,1));
    	$list = $this->db->get()->result_array();
    	$arr = lang('task_trangthai');
    	foreach ($list as $key => &$value) {
    		$value['trangthai'] = $arr[$value['trangthai']];
    		$value['date_end'] = date("d-m-Y",strtotime($value['date_end']));
    	}
    	return $list;

    }


    function log_employee($id,$type=1){
    	$data['person_id'] =$id;
    	$data['log_type'] =$type;
    	$data['ip'] = $this->get_ip();
    	$this->db->set('time', 'NOW()', FALSE);
    	$this->db->insert('phppos_employees_log', $data);
    }



	function get_ip()
	{
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';


	return  $ipaddress;
	

	}


	function get_list_log($arrParam=null)
	{
		$this->db->select('e.username,el.log_type,el.time,el.ip');
		$this->db->from('phppos_employees_log as el');
		$this->db->join('phppos_employees as e', 'e.person_id = el.person_id');
		$this->db->order_by('el.id', 'desc');
    	if(!empty($arrParam['limit']))
    	{
    		$this->db->limit($arrParam['limit'],$arrParam['offset']);
    	}
    	$list =  $this->db->get()->result_array();

    	$arr = array(1=>'<p class="alert alert-info">Đăng nhập</p>',2=>'<p class="alert alert-warning">Đăng xuất</p>');
    	foreach ($list as $key => &$value) {
    		$value['log_type'] = $arr[$value['log_type']];
    		$value['time'] = date("d-m-Y H:i:s",strtotime($value['time']));

	}
	return $list;

}

}
?>
