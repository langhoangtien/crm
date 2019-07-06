<?php

require_once (APPPATH . "models/Item.php");

class BizItem extends Item
{
	function lay_don_vi_goc($item_id)
	{
		$this->db->select('items.measure_id');
		$this->db->from('items');
		$this->db->where('item_id', $item_id);
		return $this->db->get()->result_array()[0]['measure_id'];
	}
	function add_image($item_id,$image_id)
	{
		$this->db->insert('item_images', array('item_id' => $item_id, 'image_id' => $image_id));
	}

	function link_image_to_ecommerce($image_id,$ecommerce_image_id)
	{
		$this->db->where('image_id', $image_id);
		$this->db->update('item_images', array('ecommerce_image_id' => $ecommerce_image_id));
		
	}

	function save_image_metadata($image_id, $title,$alt_text)
	{
		$this->db->where('image_id', $image_id);
		$this->db->update('item_images', array('title' => $title,'alt_text' => $alt_text));
	}

	function delete_image($image_id)
	{
		$this->db->where('image_id',$image_id);
		$this->db->delete('item_images');
		$this->load->model('Appfile');
		return $this->Appfile->delete($image_id);
	}

	function getNotAuditedInLocation($auditedIds = array(), $extra = array())
	{
		$this->db->select('items.*, categories.name as category, location_items.quantity as location_quantity');
		$this->db->from('items');
		$this->db->join('categories', 'categories.id = items.category_id');
		$this->db->join('location_items', 'location_items.item_id = items.item_id');
		$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
		if(!empty($auditedIds))
		{
			$this->db->where_not_in('items.item_id', $auditedIds);
		}
		
		if(isset($extra['category_id']) && $extra['category_id']) {
			$this->db->where('categories.id', $extra['category_id']);
		}
		return $this->db->get()->result_array();
	}
	
	function getTotalInAllLocation($item_id)
	{
		$location_items=$this->db->dbprefix('location_items');
		$items=$this->db->dbprefix('items');
		$locations=$this->db->dbprefix('locations');	
		$query = "select SUM(". $location_items .".quantity) as total_quantity from ". $location_items ." JOIN ". $locations ." ON ". $locations .".location_id = ". $location_items .".location_id where ". $location_items .".item_id = " . $item_id . " AND " . $locations . ".deleted =0";
		$result=$this->db->query($query);
		if($result->num_rows() > 0)
		{
			$row = $result->result();
			return $row[0]->total_quantity;
		}
		return null;
	}
	
	function get_item_search_suggestions($search,$limit=25, $extra=array())
	{
		if (!trim($search))
		{
			return array();
		}

		$suggestions = array();

		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("items.*,categories.name as category, MATCH (".$this->db->dbprefix('items').".name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->where("MATCH (".$this->db->dbprefix('items').".name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->limit($limit);
			
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$this->db->order_by('rel DESC');
			$by_name = $this->db->get();

			$temp_suggestions = array();

			foreach($by_name->result() as $row)
			{
				$data = array(
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				if ($row->category && $row->size)
				{
					$data['label'] = $row->name . ' ('.$row->category.', '.$row->size.')';

					$temp_suggestions[$row->item_id] = $data;
				}
				elseif ($row->category)
				{
					$data['label'] = $row->name . ' ('.$row->category.')';

					$temp_suggestions[$row->item_id] =  $data;
				}
				elseif ($row->size)
				{
					$data['label'] = $row->name . ' ('.$row->size.')';

					$temp_suggestions[$row->item_id] =  $data;
				}
				else
				{
					$data['label'] = $row->name;

					$temp_suggestions[$row->item_id] = $data;
				}

			}

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}

			$this->db->select("items.*,categories.name as category, MATCH (item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->where("MATCH (item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_item_number = $this->db->get();

			$temp_suggestions = array();

			foreach($by_item_number->result() as $row)
			{
				$data = array(
					'label' => $row->item_number.' ('.$row->name.')',
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				$temp_suggestions[$row->item_id] = $data;
			}

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}

			$this->db->select("items.*,categories.name as category,MATCH (product_id) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where("MATCH (product_id) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->where('items.deleted',0);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_product_id = $this->db->get();

			$temp_suggestions = array();

			foreach($by_product_id->result() as $row)
			{
				$data = array(
					'label' => $row->product_id.' ('.$row->name.')',
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				$temp_suggestions[$row->item_id] = $data;
			}


			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}

			$this->db->select("additional_item_numbers.*, items.image_id, categories.name as category, MATCH (".$this->db->dbprefix('additional_item_numbers').".item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", false);
			$this->db->from('additional_item_numbers');
			$this->db->join('items', 'additional_item_numbers.item_id = items.item_id');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where("MATCH (".$this->db->dbprefix('additional_item_numbers').".item_number) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_additional_item_numbers = $this->db->get();
			$temp_suggestions = array();
			foreach($by_additional_item_numbers->result() as $row)
			{
				$data = array(
					'label' => $row->item_number,
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				$temp_suggestions[$row->item_id] = $data;
			}

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);

			}
		}
		else
		{
			$this->db->select("items.*,categories.name as category", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->like($this->db->dbprefix('items').'.name', $search);
			$this->db->limit($limit);
			
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_name = $this->db->get();

			$temp_suggestions = array();

			foreach($by_name->result() as $row)
			{
				$data = array(
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				if ($row->category && $row->size)
				{
					$data['label'] = $row->name . ' ('.$row->category.', '.$row->size.')';

					$temp_suggestions[$row->item_id] = $data;
				}
				elseif ($row->category)
				{
					$data['label'] = $row->name . ' ('.$row->category.')';

					$temp_suggestions[$row->item_id] =  $data;
				}
				elseif ($row->size)
				{
					$data['label'] = $row->name . ' ('.$row->size.')';

					$temp_suggestions[$row->item_id] =  $data;
				}
				else
				{
					$data['label'] = $row->name;

					$temp_suggestions[$row->item_id] = $data;
				}

			}
			
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_label');

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
			$this->db->select("items.*,categories.name as category", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->like($this->db->dbprefix('items').'.item_number', $search);
			$this->db->limit($limit);
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_item_number = $this->db->get();

			$temp_suggestions = array();

			foreach($by_item_number->result() as $row)
			{
				$data = array(
					'label' => $row->item_number.' ('.$row->name.')',
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				$temp_suggestions[$row->item_id] = $data;
			}

			uasort($temp_suggestions, 'sort_assoc_array_by_label');

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}
			
			$this->db->select("items.*,categories.name as category", false);
			$this->db->from('items');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->where('items.deleted',0);
			$this->db->like($this->db->dbprefix('items').'.product_id', $search);
			$this->db->limit($limit);
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_product_id = $this->db->get();

			$temp_suggestions = array();

			foreach($by_product_id->result() as $row)
			{
				$data = array(
					'label' => $row->product_id.' ('.$row->name.')',
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				$temp_suggestions[$row->item_id] = $data;
			}

			uasort($temp_suggestions, 'sort_assoc_array_by_label');

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
			}

			$this->db->select("additional_item_numbers.*, items.image_id, categories.name as category", false);
			$this->db->from('additional_item_numbers');
			$this->db->join('items', 'additional_item_numbers.item_id = items.item_id');
			$this->db->join('categories', 'categories.id = items.category_id','left');
			$this->db->like($this->db->dbprefix('additional_item_numbers').'.item_number', $search);

			$this->db->limit($limit);
			if(isset($extra['category_id']) && $extra['category_id']) {
				$this->db->where('categories.id', $extra['category_id']);
			}
			if(isset($extra['by_current_location']) && $extra['by_current_location']) {
				$this->db->join('location_items', 'location_items.item_id = items.item_id');
				$this->db->where('location_items.location_id', $this->Employee->get_logged_in_employee_current_location_id());
			}
			
			$by_additional_item_numbers = $this->db->get();
			$temp_suggestions = array();
			foreach($by_additional_item_numbers->result() as $row)
			{
				$data = array(
					'label' => $row->item_number,
					'image' => $row->image_id ?  site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
					'category' => $row->category,
					'item_number' => $row->item_number,
					);

				$temp_suggestions[$row->item_id] = $data;
			}

			uasort($temp_suggestions, 'sort_assoc_array_by_label');

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_number' => $value['item_number']);
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
		
		return $suggestions;

	}
	
	/*
	 Get an item id given an item number or product_id or additional item number
	 */
	 function getMeasureName($itemId)
	 {
	 	if (!$itemId)
	 	{
	 		return false;
	 	}
	 	$this->db->select('measures.name as measure_name');
	 	$this->db->from('items');
	 	$this->db->join('measures', 'measures.id = items.measure_id','left');
	 	$this->db->where('item_id',$itemId);
	 	$query = $this->db->get();
	 	if($query->num_rows() >= 1)
	 	{
	 		return $query->row()->measure_name;
	 	}
	 	return false;
	 }

	 function getSearchAll($search, $category_id = FALSE, $limit=10000, $fields = 'all')
	 {
	 	$current_location=$this->Employee->get_logged_in_employee_current_location_id();

	 	if (!$this->config->item('speed_up_search_queries'))
	 	{
	 		$this->db->distinct();
	 	}
	 	else
	 	{
	 		return $limit;
	 	}

	 	$this->db->select('items.*,categories.name as category,
	 		location_items.quantity as quantity,
	 		location_items.reorder_level as location_reorder_level,
	 		location_items.cost_price as location_cost_price,
	 		location_items.unit_price as location_unit_price');
	 	$this->db->from('items');

	 	if ($fields == $this->db->dbprefix('suppliers').'.company_name')
	 	{
	 		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left');
	 	}

	 	if ($fields ==  $this->db->dbprefix('tags').'.name')
	 	{
	 		$this->db->join('items_tags', 'items_tags.item_id = items.item_id', 'left');
	 		$this->db->join('tags', 'tags.id = items_tags.tag_id', 'left');
	 	}


	 	$this->db->join('categories', 'categories.id = items.category_id','left');
	 	$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id = '.$current_location, 'left');

	 	if ($fields == 'all')
	 	{
	 		if ($search)
	 		{
	 			if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
	 			{
	 				if ($this->config->item('speed_up_search_queries'))
	 				{
	 					$this->db->where("MATCH (".$this->db->dbprefix('items').".name, ".$this->db->dbprefix('items').".item_number, product_id, description) AGAINST ('\"".$this->db->escape_str(escape_full_text_boolean_search($search).'*')."\"' IN BOOLEAN MODE".") and ".$this->db->dbprefix('items'). ".deleted=0", NULL, FALSE);
	 				}
	 				else
	 				{
	 					$this->db->join('additional_item_numbers', 'additional_item_numbers.item_id = items.item_id', 'left');
	 					$this->db->join('items_tags', 'items_tags.item_id = items.item_id', 'left');
	 					$this->db->join('tags', 'tags.id = items_tags.tag_id', 'left');
	 					$this->db->where("(MATCH (".$this->db->dbprefix('items').".name, ".$this->db->dbprefix('items').".item_number, product_id, description) AGAINST ('\"".$this->db->escape_str(escape_full_text_boolean_search($search).'*')."\"' IN BOOLEAN MODE".") or MATCH(".$this->db->dbprefix('tags').".name) AGAINST ('\"".$this->db->escape_str(escape_full_text_boolean_search($search).'*')."\"' IN BOOLEAN MODE".") or MATCH(".$this->db->dbprefix('categories').".name) AGAINST ('\"".$this->db->escape_str(escape_full_text_boolean_search($search).'*')."\"' IN BOOLEAN MODE".") or MATCH(".$this->db->dbprefix('additional_item_numbers').".item_number) AGAINST ('\"".$this->db->escape_str(escape_full_text_boolean_search($search).'*')."\"' IN BOOLEAN MODE".")) and ".$this->db->dbprefix('items'). ".deleted=0", NULL, FALSE);
	 				}
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
	 					$this->db->dbprefix('items').".name LIKE '%".$this->db->escape_like_str($x)."%'";
	 					$search_name_criteria_counter++;
	 				}

	 				if ($this->config->item('speed_up_search_queries'))
	 				{
	 					$this->db->where("((".
	 						$sql_search_name_criteria. ") or
	 						item_number LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						"product_id LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						$this->db->dbprefix('items').".item_id LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						$this->db->dbprefix('categories').".name LIKE '%".$this->db->escape_like_str($search)."%') and ".$this->db->dbprefix('items').".deleted=0");
	 				}
	 				else
	 				{
	 					$this->db->join('additional_item_numbers', 'additional_item_numbers.item_id = items.item_id', 'left');
	 					$this->db->join('items_tags', 'items_tags.item_id = items.item_id', 'left');
	 					$this->db->join('tags', 'tags.id = items_tags.tag_id', 'left');

	 					$this->db->where("((".
	 						$sql_search_name_criteria. ") or ".
	 						$this->db->dbprefix('items').".item_number LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						"product_id LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						$this->db->dbprefix('items').".item_id LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						$this->db->dbprefix('tags').".name LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						$this->db->dbprefix('additional_item_numbers').".item_number LIKE '%".$this->db->escape_like_str($search)."%' or ".
	 						$this->db->dbprefix('categories').".name LIKE '%".$this->db->escape_like_str($search)."%'

	 						) and ".$this->db->dbprefix('items').".deleted=0");
	 				}
	 			}
	 		}
	 	}
	 	else
	 	{
	 		if ($search)
	 		{
				//Exact Match fields
	 			if ($fields == $this->db->dbprefix('items').'.item_id' || $fields == $this->db->dbprefix('items').'.reorder_level'
	 				|| $fields == $this->db->dbprefix('location_items').'.quantity'
	 				|| $fields == $this->db->dbprefix('items').'.cost_price' || $fields == $this->db->dbprefix('items').'.unit_price' || $fields == $this->db->dbprefix('items').'.promo_price' || $fields == $this->db->dbprefix('tags').'.name')
	 			{
	 				$this->db->where("$fields = ".$this->db->escape($search)." and ".$this->db->dbprefix('items').".deleted=0");
	 			}
	 			else
	 			{
	 				if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
	 				{
						//Fulltext
	 					$this->db->where("MATCH($fields) AGAINST ('\"".$this->db->escape_str(escape_full_text_boolean_search($search).'*')."\"' IN BOOLEAN MODE".") and ".$this->db->dbprefix('items').".deleted=0");
	 				}
	 				else
	 				{
	 					$this->db->like($fields,$search);
	 					$this->db->where($this->db->dbprefix('items').".deleted=0");
	 				}
	 			}
	 		}
	 	}

	 	if ($category_id)
	 	{
	 		$this->db->where('categories.id', $category_id);
	 	}

		if (!$search) //If we don't have a search make sure we filter out deleted items
		{
			$this->db->where('items.deleted', 0);
		}

		return $this->db->get();
	}

	function get_main_image($item_id)
	{
		$item_images = $this->get_item_images($item_id);
		
		if (isset($item_images[0]))
		{
			return $item_images[0];
		}
		
		return FALSE;
	}

	function get_item_images($item_id)
	{
		$this->db->from('item_images');
		$this->db->where('item_id',$item_id);
		$this->db->order_by('id');
		return $this->db->get()->result_array();
	}

	function get_items($arrParams = null, $options = null) {
		$discount_id = $this->get_item_id_for_flat_discount_item();
		$this->db->select("*")
		->from('items')
		->where('item_id != ' . $discount_id)
		->where('deleted', 0);

		if($arrParams['is_service'] == 1){
			$this->db->where('is_service', 1);
		}

		$query = $this->db->get();

		$result = $query->result_array();

		$this->db->flush_cache();

		return $result;
	}

     /*
    *luongpham
    *đếm số sp kho
    *02/06/2017
    */
     function count_item($arrParams = null, $options = null) {
     	$current_location=$this->Employee->get_logged_in_employee_current_location_id();
     	$key_filter = isset($arrParams['key_filter'])?$arrParams['key_filter']:'';

     	$this->db->select('*');
			// $this->db->select('COUNT(bang_kho.id_san_pham_2) AS totalItem');
     	$this->db->from('bang_kho');
     	$this->db->join('categories AS c', 'c.id = bang_kho.category_id', 'left');
     	$this->db->join('location_items AS l', 'l.item_id = bang_kho.id_san_pham_2 and location_id = '.$current_location, 'left');
     	$this->db->join('tags', 'tags.id = bang_kho.tag_id', 'left');
     	$this->db->join('suppliers', 'suppliers.person_id = bang_kho.supplier_id', 'left');
     	$this->db->where('bang_kho.deleted', 0);

     	if($arrParams['key_filter'] == 'count_low_inventory') {
     		$this->db->where('bang_kho.location_quantity <= 0');
     	}

     	if(!empty($arrParams['keywords'])) {
     		$keywords = trim($arrParams['keywords']);
     		$this->db->where('(bang_kho.name LIKE \'%'.$keywords.'%\' OR bang_kho.category LIKE \'%'.$keywords.'%\' OR bang_kho.product_id LIKE \'%'.$keywords.'%\' OR  bang_kho.cost_price LIKE \'%'.$keywords.'%\' OR  bang_kho.unit_price LIKE \'%'.$keywords.'%\')');
     		$_SESSION[$key_filter]['keywords'] = $keywords;
     	}

     	if (isset($arrParams['category_child']) && ($arrParams['category_child'] == -1) && isset($arrParams['categoryId']) && ($arrParams['categoryId'] != -1)) {
     		$this->db->where('c.id', $arrParams['categoryId']);
     		$_SESSION[$key_filter]['categoryId'] = $arrParams['categoryId'];
     	}
			// tìm theo danh mục con
     	if(isset($arrParams['category_child']) && ($arrParams['category_child'] != -1) && isset($arrParams['categoryId']))											  
     	{
     		$this->db->where('c.id', $arrParams['category_child']);
     		$_SESSION[$key_filter]['category_child'] = $arrParams['category_child'];
     	} 

     	$query = $this->db->get();

     	$result = $query->num_rows();
			// $result = $query->row()->totalItem;

     	$this->db->flush_cache();

     	return $result;

     }

		/*
	    *luongpham
	    *lấy sp kho
	    *02/06/2017
	    */
		function list_item($arrParams = null, $options = null) {

			$current_location=$this->Employee->get_logged_in_employee_current_location_id();
			$key_filter = isset($arrParams['key_filter'])?$arrParams['key_filter']:'';
			
			$this->db->select('so_luong_item,category, xoay_kho, stop_producing, category_id, total_quantity, image_id, product_id, item_number, size, id_san_pham_2 as item_id, bang_kho.name as ten_san_pham, bang_kho.cost_price as gia_von, bang_kho.unit_price as gia_ban, location_reorder_level');
			$this->db->from('bang_kho');
			$this->db->join('categories AS c', 'c.id = bang_kho.category_id', 'left');
			$this->db->join('location_items AS l', 'l.item_id = bang_kho.id_san_pham_2 and location_id = '.$current_location, 'left');
			$this->db->join('tags', 'tags.id = bang_kho.tag_id', 'left');
			$this->db->join('suppliers', 'suppliers.person_id = bang_kho.supplier_id', 'left');
			$this->db->where('bang_kho.deleted', 0);
			
			if(!empty($arrParams['keywords'])) {
				$keywords = trim($arrParams['keywords']);
				$this->db->where('(bang_kho.name LIKE \'%'.$keywords.'%\' OR bang_kho.category LIKE \'%'.$keywords.'%\' OR bang_kho.product_id LIKE \'%'.$keywords.'%\' OR  bang_kho.cost_price LIKE \'%'.$keywords.'%\' OR  bang_kho.unit_price LIKE \'%'.$keywords.'%\')');
				$_SESSION[$key_filter]['keywords'] = $keywords;
			}
			
			if($arrParams['key_filter'] == 'count_low_inventory') {
				$this->db->where('bang_kho.location_quantity <= 0');
			}
			
			if(!empty($arrParams['col']) && !empty($arrParams['order'])){
				$col   = $this->_items_fields[$arrParams['col']];

				$order = $arrParams['order'];

				$this->db->order_by($col, $order);

				$_SESSION[$key_filter]['col']  = $arrParams['col'];
				$_SESSION[$key_filter]['order'] = $arrParams['order'];
			}
			
			if (isset($arrParams['category_child']) && ($arrParams['category_child'] == -1) && isset($arrParams['categoryId']) && ($arrParams['categoryId'] != -1)) {
				$this->db->where('c.id', $arrParams['categoryId']);
				$_SESSION[$key_filter]['categoryId'] = $arrParams['categoryId'];
			}
			// tìm theo danh mục con
			if(isset($arrParams['category_child']) && ($arrParams['category_child'] != -1) && isset($arrParams['categoryId']))											  
			{
				$this->db->where('c.id', $arrParams['category_child']);
				$_SESSION[$key_filter]['category_child'] = $arrParams['category_child'];
			} 
			
			$page = isset($arrParams['page'])?$arrParams['page']:1;
			
			if(!empty($arrParams['paginator']))
			{
				$paginator = $arrParams['paginator'];
				$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			}
			
			$query = $this->db->get();
			$result = !empty($query) ? $query->result_array() : [];
			
			// $query = $this->db->get();
			// $result = $query->result_array();
			$this->db->flush_cache();
			
			return $result;
		}

		/*
	    *luongpham
	    *tao 1 bảng tạm join tất cả các bảng liên quan
	    *02/06/2017
	    */
		function _bang_tam($arrParams){
			
			$query = "
			CREATE TEMPORARY TABLE ".$this->db->dbprefix('bang_kho')." 
			(
			SELECT * FROM
			(
			(
			(
			SELECT * FROM
			(
			(
			(
			SELECT quantity as so_luong_item,item_id as location_item_id FROM phppos_location_items AS lo WHERE location_id = ".$arrParams['location_id']." 
			) 
			AS BANG_DIA_CHI
			
			
			
			INNER JOIN
			
			(
			SELECT * FROM
			(
			(
			(
			SELECT *,item_id AS id_san_pham_2 FROM phppos_items
			WHERE ('deleted' = 0)
			) 
			AS BANG_KHO_2
			
			
			
			INNER JOIN
			
			(
			SELECT quantity as location_quantity, reorder_level as location_reorder_level,item_id AS id_san_pham,
			(case when SUM(quantity) IS NULL then 0 ELSE SUM(quantity) end) AS total_quantity
			FROM phppos_location_items GROUP BY id_san_pham
			) 
			
			AS BANG_TONG_SO_LUONG_SAN_PHAM ON BANG_TONG_SO_LUONG_SAN_PHAM.id_san_pham = BANG_KHO_2.id_san_pham_2
			)
			)
			
			) 
			
			AS BANG_KHO ON BANG_KHO.id_san_pham = BANG_DIA_CHI.location_item_id
			)
			)
			) 
			AS BANG_KET_QUA
			
			LEFT JOIN
			
			(
			SELECT name as category,id as id_danh_muc from phppos_categories
			) 

			AS BANG_DANH_MUC ON BANG_DANH_MUC.id_danh_muc = BANG_KET_QUA.category_id

			LEFT JOIN

			(
			SELECT tag_id,item_id as items_tags_item_id from phppos_items_tags
			) 

			AS BANG_ITEM_TAG ON BANG_ITEM_TAG.items_tags_item_id = BANG_KET_QUA.id_san_pham_2
			)
			)
			)
			";
			log_message('error', $query);
			$this->db->query($query);

		}
		
		/*
	    *luongpham
	    *lấy số sp của các trường hợp khác trong kho
	    *02/06/2017
	    */
		function count_item_by_filter($arrParams = null) {
			$current_location=$this->Employee->get_logged_in_employee_current_location_id();
			$filter = isset($_SESSION[$arrParams['key_filter']])?$_SESSION[$arrParams['key_filter']]:array();

		// $this->db -> select('COUNT(i.item_id) AS totalItem')
			$this->db->select('*');
			$this->db->from('bang_kho');
			$this->db->join('categories AS c', 'c.id = bang_kho.category_id', 'left');
			$this->db->join('location_items AS l', 'l.item_id = bang_kho.id_san_pham_2 and location_id = '.$current_location, 'left');
			$this->db->join('tags', 'tags.id = bang_kho.tag_id', 'left');
			$this->db->join('suppliers', 'suppliers.person_id = bang_kho.supplier_id', 'left');
			$this->db->where('bang_kho.deleted', 0);

		// if($arrParams['key_filter'] == 'count_all_items') {
			// $this->db->where('bang_kho.location_quantity < 0');
		// }

			if($arrParams['key_filter'] == 'count_low_inventory') {
				$this->db->where('bang_kho.location_quantity <= 0');
			}

			if(!empty($arrParams['col']) && !empty($arrParams['order'])){
				$col   = $this->_items_fields[$arrParams['col']];

				$order = $arrParams['order'];

				$this->db->order_by($col, $order);

				$_SESSION[$key_filter]['col']  = $arrParams['col'];
				$_SESSION[$key_filter]['order'] = $arrParams['order'];
			} 
			// tìm theo danh mục con
			if(isset($arrParams['category_child']) && ($arrParams['category_child'] != -1) && isset($arrParams['categoryId']))											  
			{
				$this->db->where('categories', $arrParams['category_child']);
			}

			$page = isset($arrParams['page'])?$arrParams['page']:1;

			if(!empty($arrParams['paginator']))
			{
				$paginator = $arrParams['paginator'];
				$this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);
			} 

			$query = $this->db->get();

			$result = $query->num_rows();
		// $result = $query->row()->totalItem;

			$this->db->flush_cache();

			return $result;
		}
		function list_items_cate(){
			$this->db->select("phppos_items.*");
			$this->db->from("phppos_items");
			$this->db->join("phppos_categories","phppos_categories.id = phppos_items.category_id");
			// $this->db->JOIN("phppos_location_items","phppos_location_items.item_id = phppos_items.item_id");
			$this->db->where("phppos_items.deleted",0);
			$this->db->group_by('phppos_items.item_id');
			$query = $this->db->get();
			$row = $query->result_array();
			return $row;

		}
		public function search_item_for_contract($search) {
		    
		    
		    $this->db->select("items.*,categories.name as category", false);
		    $this->db->from('items');
		    $this->db->join('categories', 'categories.id = items.category_id','left');
		    $this->db->where('items.deleted',0);
		    // $this->db->where('items.item_id IN (select s.item_id from '. $this->db->dbprefix('sales_items') .' s join '. $this->db->dbprefix('sales') .' s1 ON s1.sale_id = s.sale_id )');
		    $this->db->like($this->db->dbprefix('items').'.name', $search);
		    $this->db->group_by('items.item_id');
		    $query = $this->db->get();

		    $temp_items = $query->result_array();
		    
		    foreach($temp_items as $item)
		    {
		        $items[]=array(
		            'value'=> $item['name'],
		            'item_id'=>$item['item_id'],
		            'label' => $item['name'],
		            'image' => $item['image_id']?site_url('app_files/view/'.$row->image_id) : base_url()."assets/img/item.png" ,
		            'category' => $item['category'],
		            'item_number' => $item['item_number']);
		    }
		    
		    return $items;
		    
		}
		
		/*
	    *luongpham
	    *các trường của sp sẽ xuất ra
	    *02/06/2017
	    */
		protected $_items_fields = array(
			'item_id' 	       			=> 'id_san_pham_2',
			'item_id'   						=> 'id_san_pham_2',
			'name' 									=> 'ten_san_pham',
			'category'    					=> 'category',
			'size' 		   						=> 'size',
			'cost_price'      			=> 'gia_von',
			'unit_price'    				=> 'gia_ban',
			'items_quantity'   			=> 'quantity',
			'items_total_quantity'  => 'total_quantity',
			);

		

	}
?>
