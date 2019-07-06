<?php
class Location extends CI_Model
{
    protected $_last_id;
    protected $types = [
        [
            'code' => 'cong_ty',
            'label' => 'Công ty'
        ],
        [
            'code' => 'nha_may',
            'label' => 'Nhà máy'
        ],
        [
            'code' => 'xuong_san_xuat',
            'label' => 'Xưởng sản xuất'
        ],
        [
            'code' => 'kho',
            'label' => 'Kho'
        ]
    ];
    
    public function checkAllowLocation($locationId)
    {
        $isAllow = false;
        $availableLocations = $this->getLocationsWithChild();
        foreach ($availableLocations as $location) {
            if ($location['location_id'] == $locationId) {
                $isAllow = true;
                break;
            }
        }
        return $isAllow;
    }
    
    protected function getChildLocations($parentId = 0, $locations = [], &$orderedLocations = [], $level)
    {
    	$level ++;
        $childLocations = [];
        foreach ($locations as $location) {
            if ($location['parent_id'] == $parentId) {
            	$location['level'] = $level;
                $orderedLocations[] = $location;
                $location['childs'] = $this->getChildLocations($location['location_id'], $locations, $orderedLocations, $level);
                $childLocations[] = $location;
            }
        }
        return $childLocations;
    }
    
    public function getLocationsWithChild($parentIds=[])
    {
        $locations = $this->getLocations();

        if (empty($parentIds)) {
        	$parentIds = $this->Employee->get_authenticated_location_ids($this->session->userdata('person_id'));
        }

        $treeLocations = [];
        $orderedLocations = [];
        foreach ($locations as $location) {
            if ((empty($parentIds) && $location['parent_id'] == 0) || in_array($location['location_id'], $parentIds)) {
            	$location['level'] = $level = 0;
                $orderedLocations[] = $location;
                $location['childs'] = $this->getChildLocations($location['location_id'], $locations, $orderedLocations, $level);
                $treeLocations[] = $location;
            }
        }
        $types = $this->getTypes();
        $orderedLocations = array_map(function($location) use ($types) {
            foreach ($types as $type) {
                if ($location['type'] == $type['code']) {
                    $location['type'] = $type['label'];
                    break;
                }
            }
            return $location;
        }, $orderedLocations);

        $locationIds = array_map(function($location){
        	return $location['location_id'];
        }, $orderedLocations);

        $orderedLocations = array_map(function($location) use ($locationIds){
        	$location['origin_parent_id'] = $location['parent_id'];
        	if (!in_array($location['parent_id'], $locationIds)) {
        		$location['parent_id'] = 0;
        	}
        	return $location;
        }, $orderedLocations);
        
        $result = [];
        foreach ($orderedLocations as $location) {
            $this->checkLevelLocation($result, $location);
        }
        return $result;
    }
    
    protected function checkLevelLocation(&$result, $location) {
        if (empty($result)) {
            $result[] = $location;
        } else {
            $addLocation = true;
            foreach ($result as $i => $record) {
                if ($record['location_id'] == $location['location_id']) {
                    if ($record['level'] < $location['level']) {
                        unset($result[$i]);
                    } else {
                        $addLocation = false;
                    }
                }
            }
            if ($addLocation) {
                $result[] = $location;
            }
        }
        return;
    }
    
    public function getTypes()
    {
        return $this->types;
    }
    public function getLocations($location_id=null)
    {
        $this->db->from('locations');
        $this->db->where('locations.deleted = ', 0);
        if($location_id){
        	$this->db->where('location_id', $location_id);
        }
        return $this->db->get()->result_array();
    }
	public function getAllQty($locationId, $itemId = 0) {
		
		$this->db->select('items.*, locations.name as location_name, location_items.quantity, categories.name as category, measures.name as measure_name, (phppos_items.cost_price * phppos_location_items.quantity) as total_cost_price, (phppos_items.unit_price * phppos_location_items.quantity) as total_unit_price');
		$this->db->from('locations');
		$this->db->join('location_items', 'locations.location_id = location_items.location_id');
		$this->db->join('items', 'items.item_id = location_items.item_id');
		$this->db->join('categories', 'categories.id = items.category_id');
		$this->db->join('measures', 'measures.id = items.measure_id', 'left');
		
		if (!empty($itemId) && $itemId > 0) {
			$this->db->where('items.item_id = ', $itemId);
		}
		
		$this->db->where('locations.deleted = ', 0);
		$this->db->where('locations. location_id = ', $locationId);
		return $this->db->get()->result_array();
	}

	/*
	Determines if a given location_id is an location
	*/
	function exists($location_id)
	{
		$this->db->from('locations');
		$this->db->where('location_id',$location_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	/*
	Returns all the locations
	*/
	function get_all($limit=10000, $offset=0,$col='location_id',$order='asc')
	{
		$this->db->from('locations');
		$this->db->where('deleted',0);
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $order);
		}
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}

    function list_item($arrParams = null, $options = null) {
        $this->db -> select('location_id, name, address, phone,code')
                  -> from('locations')
                  -> where('deleted', 0);
        if($arrParams['location_id']){
        	$this->db->where('location_id', $arrParams['location_id']);
        }          
        $query = $this->db->get();

        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

	function count_all()
	{
		$this->db->from('locations');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular location
	*/
	function get_info($location_id)
	{
		$this->db->from('locations');
		$this->db->where('location_id',$location_id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $location_id is NOT a location
			$location_obj=new stdClass();

			//Get all the fields from locations table
			$fields = $this->db->list_fields('locations');

			foreach ($fields as $field)
			{
				$location_obj->$field='';
			}

			return $location_obj;
		}
	}
	
	function get_info_for_key($key, $override_location_id = false)
	{
		static $location_info;
			
		if ($override_location_id !== FALSE)
		{
			$location_id = $override_location_id;
		}
		else
		{
			$location_id= $this->Employee->get_logged_in_employee_current_location_id();
		}
		
		if (!isset($location_info[$location_id]))
		{			
			$location_info[$location_id] = $this->get_info($location_id);
		}
		
		return $location_info[$location_id]->{$key};
	}

	/*
	Inserts or updates a location
	*/
	function save(&$location_data,$location_id=false)
	{	
		//Check for duplicate taxes
		for($k = 1;$k<=5;$k++)
		{
			if (isset($location_data["default_tax_${k}_name"]) && isset($location_data["default_tax_${k}_rate"]))
			{
				$current_tax = $location_data["default_tax_${k}_name"].$location_data["default_tax_${k}_rate"];
			
				for ($j = 1;$j<=5;$j++)
				{
					$check_tax = $location_data["default_tax_${j}_name"].$location_data["default_tax_${j}_rate"];
					if ($j!=$k && $current_tax != '' && $check_tax != '')
					{
						if ($current_tax == $check_tax)
						{
							return FALSE;
						}
					}
				}
			}
		}
		
		if (!$location_id or !$this->exists($location_id))
		{
			if($this->db->insert('locations',$location_data))
			{
				$location_data['location_id']=$this->db->insert_id();
                $this->_last_id = $this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('location_id', $location_id);
		return $this->db->update('locations',$location_data);
	}

    function get_last_id() {
        return $this->_last_id;
    }

	function search_count_all($search, $limit=10000)
	{
			$this->db->from('locations');
			if ($search)
			{
				if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
				{
					$this->db->where("MATCH (name, address,phone,email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") and deleted=0", NULL, FALSE);			
				}
				else
				{
					$search_terms_array=explode(" ", $this->db->escape_like_str($search));
					
					//to keep track of which search term of the array we're looking at now	
					$search_name_criteria_counter=0;
					$sql_search_name_criteria = '';
					//loop through array of search terms
					foreach ($search_terms_array as $x)
					{
						$sql_search_name_criteria.=
						($search_name_criteria_counter > 0 ? " AND " : "").
						"name LIKE '%".$this->db->escape_like_str($x)."%'";
				
						$search_name_criteria_counter++;
					}
	
					$this->db->where("((".
					$sql_search_name_criteria. ") or 
					address LIKE '%".$this->db->escape_like_str($search)."%' or 
					location_id LIKE '%".$this->db->escape_like_str($search)."%' or 
					phone LIKE '%".$this->db->escape_like_str($search)."%' or 
					email LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");
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

	/*
	Get search suggestions to find locations
	*/
	function get_search_suggestions($search,$limit=25)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();

		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("location_id, name,phone, MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('locations');
			$this->db->where("MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			$by_name = $this->db->get();
		
		
			$temp_suggestions = array();
			foreach($by_name->result() as $row)
			{
				$data = array(
					'name' => $row->name,
					'email' => $row->phone,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;
			}
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
				
		
			$this->db->select("location_id, address,phone, MATCH (address) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('locations');
			$this->db->where('deleted',0);
			$this->db->where("MATCH (address) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
		
			$by_address = $this->db->get();
			$temp_suggestions = array();
			foreach($by_address->result() as $row)
			{
				$data = array(
					'name' => $row->address,
					'email' => $row->phone,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;
			}
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}

		
			$this->db->from('locations');
			$this->db->where('location_id', $search);
			$this->db->where('deleted',0);
			$this->db->limit($limit);
		
			$by_location_id = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_location_id->result() as $row)
			{
				$data = array(
					'name' => $row->location_id,
					'email' => $row->phone,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;
			}
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}

		
			$this->db->select("location_id, phone,name, MATCH (phone) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('locations');
			$this->db->where("MATCH (phone) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
			$by_phone = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_phone->result() as $row)
			{
				$data = array(
					'name' => $row->phone,
					'email' => $row->name,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;
			}
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}

		
			$this->db->select("location_id,name, email, MATCH (email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('locations');
			$this->db->where("MATCH (email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
		
			$by_email = $this->db->get();
			$temp_suggestions = array();
			foreach($by_email->result() as $row)
			{
				$data = array(
					'name' => $row->email,
					'email' => $row->name,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;


			}
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
			
		}
		else
		{
			$this->db->from('locations');
			$this->db->like('name', $search);
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$by_name = $this->db->get();			
			
			$temp_suggestions = array();
			foreach($by_name->result() as $row)
			{
				$data = array(
					'name' => $row->name,
					'email' => $row->email,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;
			}
			
			asort($temp_suggestions);
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
		
			$this->db->from('locations');
			$this->db->where('deleted',0);
			$this->db->like('address', $search);
			$this->db->limit($limit);
		
			$by_address = $this->db->get();
			
			$temp_suggestions = array();
			foreach($by_address->result() as $row)
			{
				$data = array(
					'name' => $row->address,
					'email' => $row->name,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;

			}
			
			asort($temp_suggestions);
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}

			$this->db->from('locations');
			$this->db->where('location_id', $search);
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$by_location_id = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_location_id->result() as $row)
			{
				$data = array(
					'name' => $row->location_id,
					'email' => $row->name,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;

			}
			
			asort($temp_suggestions);
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
			$this->db->from('locations');
			$this->db->like('phone', $search);
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$by_phone = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_phone->result() as $row)
			{
				$data = array(
					'name' => $row->phone,
					'email' => $row->name,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;
			}
			
			asort($temp_suggestions);
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
			$this->db->from('locations');
			$this->db->like('email', $search);
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$by_email = $this->db->get();
			$temp_suggestions = array();
			foreach($by_email->result() as $row)
			{
				$data = array(
					'name' => $row->email,
					'email' => $row->name,
					'avatar' => base_url()."assets/img/user.png" 
				 );
				$temp_suggestions[$row->location_id] = $data;
			}
			
			asort($temp_suggestions);
		
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
	Preform a search on locations
	*/
	
	function search($search, $limit=20,$offset=0,$column='name',$orderby='asc')
	{
		$this->db->from('locations');
		
		if ($search)
		{
			if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
			{
				$this->db->where("MATCH (name, address,phone,email) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE".") and deleted=0", NULL, FALSE);			
			}
			else
			{
				$search_terms_array=explode(" ", $this->db->escape_like_str($search));
				
				//to keep track of which search term of the array we're looking at now	
				$search_name_criteria_counter=0;
				$sql_search_name_criteria = '';
				//loop through array of search terms
				foreach ($search_terms_array as $x)
				{
					$sql_search_name_criteria.=
					($search_name_criteria_counter > 0 ? " AND " : "").
					"name LIKE '%".$this->db->escape_like_str($x)."%'";
				
					$search_name_criteria_counter++;
				}
	
				$this->db->where("((".
				$sql_search_name_criteria. ") or 
				address LIKE '%".$this->db->escape_like_str($search)."%' or 
				location_id LIKE '%".$this->db->escape_like_str($search)."%' or 
				phone LIKE '%".$this->db->escape_like_str($search)."%' or 
				email LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");
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


	function get_locations_search_suggestions($search,$limit=25)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("location_id,name,color, MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('locations');
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);	
			$this->db->order_by('rel DESC');
		
			$by_name = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_name->result() as $row)
			{
				$data = array(
						'name' => $row->name,
						'color' => $row->color
						);

				$temp_suggestions[$row->location_id] = $data;
			}
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'color'=>$value['color']);		
			}

			$this->db->select("location_id, color,address, MATCH (address) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('locations');
			$this->db->where('deleted', 0);
			$this->db->where("MATCH (address) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			
			$by_address = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_address->result() as $row)
			{
				$data = array(
						'name' => $row->address,
						'color' => $row->color
						);

				$temp_suggestions[$row->location_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'color'=>$value['color']);
			}
		}
		else
		{
			$this->db->select("location_id,name,color", false);
			$this->db->from('locations');
			$this->db->where('deleted', 0);
			$this->db->like("name",$search);
			$this->db->limit($limit);	
		
			$by_name = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_name->result() as $row)
			{
				$data = array(
						'name' => $row->name,
						'color' => $row->color
						);

				$temp_suggestions[$row->location_id] = $data;
			}
		
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'color'=>$value['color']);		
			}

			$this->db->select("location_id, color,address", false);
			$this->db->from('locations');
			$this->db->where('deleted', 0);
			$this->db->like("address",$search);
			$this->db->limit($limit);
			
			$by_address = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_address->result() as $row)
			{
				$data = array(
						'name' => $row->address,
						'color' => $row->color
						);

				$temp_suggestions[$row->location_id] = $data;
			}
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'color'=>$value['color']);
			}
		}
		
		
		for($k=count($suggestions)-1;$k>=0;$k--)
		{
			if (!$suggestions[$k]['label'])
			{
				unset($suggestions[$k]);
			}
		}
		
		$suggestions = array_values($suggestions);
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		
		$availableLocations = $this->getLocationsWithChild();
		$availableLocationIds = array_map(function($location){
		    return $location['location_id'];
		}, $availableLocations);
        $suggestions = array_filter($suggestions, function($item) use ($availableLocationIds){
            return in_array($item['value'], $availableLocationIds);
        });
		return $suggestions;
	}

	/*
	Deletes one location
	*/
	function delete($location_id)
	{
		$current_location_id= $this->Employee->get_logged_in_employee_current_location_id();

		//Don't let current logged in location be deleted
		if($current_location_id == $location_id || !$location_id)
			return false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where('location_id', $location_id);
		$this->db->delete('employees_locations');

		$this->db->where('location_id', $location_id);
		$this->db->delete('location_items');

		$this->db->where('location_id', $location_id);
		$this->db->delete('location_items_taxes');

		$this->db->where('location_id', $location_id);
		$this->db->delete('location_items_tier_prices');

		$this->db->where('location_id', $location_id);
		$this->db->delete('location_item_kits');

		$this->db->where('location_id', $location_id);
		$this->db->delete('location_item_kits_taxes');

		$this->db->where('location_id', $location_id);
		$this->db->delete('location_item_kits_tier_prices');
		
		$this->db->where('location_id', $location_id);
		$this->db->update('locations', array('deleted' => 1));
		
		return $this->db->trans_complete();		
	}
	
	function delete_list($location_ids)
	{	
		$location_id= $this->Employee->get_logged_in_employee_current_location_id();

		//Don't let current logged in location be deleted
		if(in_array($location_id,$location_ids) || empty($location_ids))
			return false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->where_in('location_id',$location_ids);
		$this->db->delete('employees_locations');

		$this->db->where_in('location_id',$location_ids);
		$this->db->delete('location_items');

		$this->db->where_in('location_id',$location_ids);
		$this->db->delete('location_items_taxes');

		$this->db->where_in('location_id',$location_ids);
		$this->db->delete('location_items_tier_prices');

		$this->db->where_in('location_id',$location_ids);
		$this->db->delete('location_item_kits');

		$this->db->where_in('location_id',$location_ids);
		$this->db->delete('location_item_kits_taxes');

		$this->db->where_in('location_id',$location_ids);
		$this->db->delete('location_item_kits_tier_prices');
		
		$this->db->where_in('location_id',$location_ids);
		$this->db->update('locations', array('deleted' => 1));
		
		return $this->db->trans_complete();		
 	}
	
	function assign_employees_to_location($location_id,$employees)
	{
		$this->db->trans_start();
		
		$this->db->delete('employees_locations', array('location_id' => $location_id));
		foreach($employees as $employee_id)
		{
			$this->db->insert('employees_locations',
			array(
			'employee_id'=>$employee_id,
			'location_id'=>$location_id
			));
		}
		
		$this->db->trans_complete();
		return TRUE;
	}
	
	function get_merchant_id($override_location_id = FALSE)
	{		
		if ($this->agent->is_mobile())
		{
			return $this->get_info_for_key('hosted_checkout_merchant_id', $override_location_id);				
		}
	
		//EMV
		if ($this->get_info_for_key('emv_merchant_id') && $this->get_info_for_key('com_port') && $this->get_info_for_key('listener_port'))
		{
			return $this->get_info_for_key('emv_merchant_id', $override_location_id);				
		}
		else //Default hosted checkout
		{
			return $this->get_info_for_key('hosted_checkout_merchant_id', $override_location_id);				
		}
	}

    function item_select_location_group_employees($arrParams = null, $options = null) {
        $result_tmp = $this->get_groups_form_location($arrParams, $options);
        $result[-1] = '-- Chọn nhóm --';
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['group_id']] = $val['group_name'];
        }

        return $result;
    }

    function get_items($cid, $options = null) {
        $this->db -> select('*')
                  -> from('locations')
                  -> where('location_id IN ('.implode(',', $cid).')');

        $query = $this->db->get();

        $result_tmp = $query->result_array();
        $this->db->flush_cache();
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[$val['location_id']] = $val;
        }

        return $result;
    }

    function get_item($arrParams = null, $options = null) {
        if($options['task'] == 'by-name') {
            $this->db -> select('*')
                      -> from('locations')
                      -> where('name', trim($arrParams['name']))
                      -> where('deleted', 0);

            $query = $this->db->get();

            $result = $query->row_array();
            $this->db->flush_cache();
        }

        return $result;
    }

    function get_employee_ids($arrParams = null, $options = null) {
        if($options['task'] == 'location-group') {
            $location_id = $arrParams['location_id'];
            $group_id    = $arrParams['group_id'];
            $this->db -> select('l.*')
                      -> from('location_group_employees AS l')
                      -> where('l.location_id', $location_id)
                      -> where('l.group_id', $group_id);

            $query = $this->db->get();

            $result_tmp = $query->row_array();

            $this->db->flush_cache();
            $result = array();
            if(!empty($result_tmp['employees'])) {
                $result = explode(',', $result_tmp['employees']);
            }
        }
        return $result;
    }

    function get_employee_list($arrParams = null, $options = null) {
        if($options == null)  {
            global $location_emp;
            $location_id = $arrParams['location_id'];
            $group_id = $arrParams['group_id'];
            if(isset($location_emp[$location_id][$group_id]))
                $result = $location_emp[$location_id][$group_id];
            else {
                $this->db -> select('l.*')
                         -> from('location_group_employees AS l')
                         -> where('l.location_id', $arrParams['location_id'])
                         -> where('l.group_id', $arrParams['group_id']);

                $query = $this->db->get();

                $result = !empty($query) ? $query->row_array() : array();
                $this->db->flush_cache();

                if(!empty($result)) {
                    $employee_model = $this->model_load_model('Employee');
                    $employee_ids = array();

                    if(!empty($result['employees'])) {
                        $tmp = explode(',', $result['employees']);
                        $employee_ids = array_merge($employee_ids, $tmp);
                    }

                    if(!empty($result['default'])) {
                        $tmp = explode(',', $result['default']);
                        $employee_ids = array_merge($employee_ids, $tmp);
                    }
                    $employee_ids = array_unique($employee_ids);
                    $employee_list = $employee_model->get_info_by_ids($employee_ids);

                    if(!empty($result['employees'])) {
                        $tmp = explode(',', $result['employees']);
                        foreach($tmp as $em_id) {
							if(!empty($employee_list[$em_id])){
								$result['employee_list'][] = array(
									'id' =>  $em_id,
									'name'=> $employee_list[$em_id]['first_name'] . ' ' . $employee_list[$em_id]['last_name']
								);
							}

                        }
                    }

                    if(!empty($result['default'])) {
                        $tmp = explode(',', $result['default']);
                        foreach($tmp as $em_id) {
                            $result['default_list'][] = array(
                                'id' =>  $em_id,
                                'name'=> $employee_list[$em_id]['first_name'] . ' ' . $employee_list[$em_id]['last_name']
                            );
                        }
                    }

                    $location_emp[$location_id][$group_id] = $result;
                }
            }
        }

        return $result;
    }

    function location_group_employees_info($arrParams = null, $options = null) {
        $this->db -> select('*')
                  -> from('location_group_employees')
                  -> where('location_id', $arrParams['location_id'])
                  -> where('group_id', $arrParams['group_id']);

        $query = $this->db->get();

        $result = $query->row_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_location_group_employees($location_id, $options = null) {
        if(empty($options['task']) ||(!empty($options['task']) && $options['task'] == null)) {
            $this->db -> select('l.*,g.name AS group_name')
                     -> from('location_group_employees AS l')
                     -> join('groups AS g', 'l.group_id = g.group_id')
                     -> where('l.location_id', $location_id)
                     -> order_by('l.ord','ASC');

            if($options['active'] == true)
                $this->db->where('l.status', 'active');

            $query = $this->db->get();

            $result_tmp = !empty($query) ? $query->result_array() : array();
            $this->db->flush_cache();
            $result = array();

            if(!empty($result_tmp)) {
                $employee_model = $this->model_load_model('Employee');
                $employee_ids = array();
                foreach($result_tmp as $item) {
                    if(!empty($item['employees'])) {
                        $tmp = explode(',', $item['employees']);
                        $employee_ids = array_merge($employee_ids, $tmp);
                    }

                    if(!empty($item['default'])) {
                        $tmp = explode(',', $item['default']);
                        $employee_ids = array_merge($employee_ids, $tmp);
                    }
                    $employee_ids = array_unique($employee_ids);
                }

                $employee_list = $employee_model->get_info_by_ids($employee_ids);

                foreach($result_tmp as $item) {
                    if(!empty($item['employees'])) {
                        $tmp = explode(',', $item['employees']);
                        $item['employee_ids'] = $tmp;
                        foreach($tmp as $em_id) {
							if(!empty($employee_list[$em_id])){
								$item['employee_list'][] = array(
									'id' =>  $em_id,
									'name'=> $employee_list[$em_id]['first_name'] . ' ' . $employee_list[$em_id]['last_name']
								);
							}

                        }
                    }

                    if(!empty($item['default'])) {
                        $tmp = explode(',', $item['default']);
                        foreach($tmp as $em_id) {
                            $item['default_list'][] = array(
                                'id' =>  $em_id,
                                'name'=> $employee_list[$em_id]['first_name'] . ' ' . $employee_list[$em_id]['last_name']
                            );
                        }
                    }

                    $result[$item['group_id']] = $item;
                }
            }

        }elseif($options['task'] == 'change_sale_id') {
            $this->db -> select('l.*,g.name AS group_name')
                     -> from('location_group_employees AS l')
                     -> join('groups AS g', 'l.group_id = g.group_id')
                     -> where('l.location_id', $location_id)
                     -> order_by('l.ord','ASC');

            if($options['active'] == true)
                $this->db->where('l.status', 'active');

            $query = $this->db->get();

            $result_tmp = $query->result_array();
            $this->db->flush_cache();
            $result = array();
            if(!empty($result_tmp)) {
                foreach($result_tmp as $val)
                    $result[$val['group_id']] = $val;
            }
        }
        return $result;
    }

    function get_groups_form_location($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $this->db -> select('g.group_id, g.name AS group_name')
                      -> from('location_group_employees AS lg')
                      -> join('groups AS g', 'g.group_id = lg.group_id','left')
                      -> order_by('lg.ord', 'ASC');

            if($arrParams['location_id'] > 0)
                $this->db->where('lg.location_id', $arrParams['location_id']);

        }elseif($options['task'] == 'all-group-of-locations') {
            $this->db -> select('g.group_id, g.name AS group_name')
                      -> distinct()
                      -> from('location_group_employees AS lg')
                      -> join('groups AS g', 'g.group_id = lg.group_id','left')
                      -> order_by('lg.ord', 'ASC');
        }

        $query = $this->db->get();

        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_group_ids_from_location($arrParams = null, $options = null) {
        $this->db -> select('group_id')
                  -> from('location_group_employees');

        if($arrParams['location_id'] > 0)
            $this->db->where('location_id', $arrParams['location_id']);

        if($arrParams['employee_id'] > 0) {
            $employee_id = $arrParams['employee_id'];
            $this->db->where("CONCAT(',',employees,',') LIKE '%,$employee_id,%'");
        }

        $query = $this->db->get();

        $result_tmp = $query->result_array();
        $this->db->flush_cache();
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[] = $val['group_id'];
        }

        return $result;
    }

    function delete_location_group_employees($cid, $options = null) {
        $this->db->where('location_id IN (' . implode(', ', $cid) . ')');
        $this->db->delete('location_group_employees');

        $this->db->flush_cache();
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }
    	function ktra_ten_dang_ky($ten = '')
	{
		$this->db->where_in('name', $ten);
	    $query = $this->db->get('registers');
	   	return $query->num_rows(); 
	}

}
?>
