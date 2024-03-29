<?php
require_once (APPPATH . "models/Item_kit.php");

class BizItem_kit extends Item_kit
{
	public function getKitBomItems($kit_id = 0) {
		$this->db->from('item_kit_boms');
		$this->db->where('item_kit_boms.item_kit_id', $kit_id);
		return $this->db->get()->result();
	}
	
	public function get_bom_search_suggestions($search = '') {
		
		$this->db->from('item_kits');
		$this->db->where('item_kits.deleted', 0);
		$this->db->where('item_kits.type', 'bom');
		$this->db->like('name', $search);
		
		$temp_suggestions = [];
		
		foreach($this->db->get()->result() as $row)
		{
			$data = array(
					'value' => $row->item_kit_id,
					'label' => empty($row->item_kit_number) ? $row->name : $row->item_kit_number.'-'.$row->name,
					'image' => base_url()."assets/img/item.png" ,
					'category' => $row->category_id,
					'item_number' => $row->item_kit_number,
					'type' => 'kit'
			);
		
			$temp_suggestions[$row->item_id] = $data;
		}
		return $temp_suggestions;
	}
	
	public function countAvailableKits($items = null)
	{
		$availableKits = 0;
		$isCompare = false;
		if(!empty($items)) {
			foreach ($items as $item) {
				$measureConverted = $this->ItemMeasures->getConvertedValue($item['item_id'], $item['measure_id']);
				$qtyConverted = $item['quantity'];
				if ($measureConverted) {
					$qtyConverted = $measureConverted->qty_converted * $item['quantity'];
				}
				$qtyItem = $this->Item_location->get_location_quantity($item['item_id']);
				if( $qtyItem > 0 ) {
					if(!$isCompare) {
						$availableKits = (int) ($qtyItem / $qtyConverted);
					}
					
					if( $isCompare && $availableKits > (int) ($qtyItem / $qtyConverted) ) {
						$availableKits = (int) ($qtyItem / $qtyConverted);
					}
					$isCompare = true;
				} else {
					$availableKits = 0;
					break;
				}
			}
		}
		return $availableKits;
	}
	
	function search($search, $category_id = false, $limit = 20, $offset = 0, $column = 'name', $orderby = 'asc', $fields = 'all', $type = 'all')
	{
		$current_location = $this->Employee->get_logged_in_employee_current_location_id();
	
		if (!$this->config->item('speed_up_search_queries')) {
			$this->db->distinct();
		}
	
		$this->db->select('item_kits.*,
		location_item_kits.unit_price as location_unit_price,
		location_item_kits.cost_price as location_cost_price');
		$this->db->from('item_kits');
		$this->db->join('location_item_kits', 'location_item_kits.item_kit_id = item_kits.item_kit_id and location_id = ' . $current_location, 'left');
		$this->db->join('item_kits_tags', 'item_kits_tags.item_kit_id = item_kits.item_kit_id', 'left');
		$this->db->join('tags', 'tags.id = item_kits_tags.tag_id', 'left');
	
		$this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
	
		if ($fields == 'all') {
			if ($search) {
				if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
					$this->db->where("(MATCH (" . $this->db->dbprefix('item_kits') . ".name, item_kit_number, product_id, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ") or MATCH(" . $this->db->dbprefix('tags') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . "))and " . $this->db->dbprefix('item_kits') . ".deleted=0", NULL, FALSE);
				} else {
					$this->db->where("(" . $this->db->dbprefix('item_kits') . ".name LIKE '%" . $this->db->escape_like_str($search) .
							"%' or item_kit_number LIKE '%" . $this->db->escape_like_str($search) . "%'" .
							"or product_id LIKE '%" . $this->db->escape_like_str($search) . "%' or
					description LIKE '%" . $this->db->escape_like_str($search) . "%') and " . $this->db->dbprefix('item_kits') . ".deleted=0");
				}
			}
		} else {
			if ($search) {
				//Exact Match fields
				if ($fields == $this->db->dbprefix('item_kits') . '.item_kit_id' || $fields == $this->db->dbprefix('item_kits') . '.cost_price'
						|| $fields == $this->db->dbprefix('item_kits') . '.unit_price' || $fields == $this->db->dbprefix('tags') . '.name'
						) {
							$this->db->where("$fields = " . $this->db->escape($search) . " and " . $this->db->dbprefix('item_kits') . ".deleted=0");
						} else {
							if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
								//Fulltext
								$this->db->where("MATCH($fields) AGAINST ('\"" . $this->db->escape_str(escape_full_text_boolean_search($search) . '*') . "\"' IN BOOLEAN MODE" . ") and " . $this->db->dbprefix('item_kits') . ".deleted=0");
							} else {
								$this->db->like($fields, $search);
								$this->db->where($this->db->dbprefix('item_kits') . ".deleted=0");
							}
						}
			}
		}
	
		if ($category_id) {
			$this->db->where('categories.id', $category_id);
		}
	
		if (!$this->config->item('speed_up_search_queries')) {
			$this->db->order_by($column, $orderby);
		}
	
		if (!$search) //If we don't have a search make sure we filter out deleted items
		{
			$this->db->where('item_kits.deleted', 0);
		}
		
		if (!empty($type) && $type == 'bom') {
			$this->db->where('item_kits.type', 'bom');
		} elseif (!empty($type) && $type == 'kit') {
			$this->db->where('( item_kits.type IS NULL OR item_kits.type = "")');
		}
	
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function search_count_all($search, $limit = 10000, $type = 'all')
	{
		if ($this->config->item('speed_up_search_queries')) {
			return $limit;
		}
		$this->db->from('item_kits');
		$this->db->join('item_kits_tags', 'item_kits_tags.item_kit_id = item_kits.item_kit_id', 'left');
		$this->db->join('tags', 'tags.id = item_kits_tags.tag_id', 'left');
	
		if ($search) {
			if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
				$this->db->where("(MATCH (" . $this->db->dbprefix('item_kits') . ".name, item_kit_number, product_id, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ") or MATCH(" . $this->db->dbprefix('tags') . ".name) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . "))and " . $this->db->dbprefix('item_kits') . ".deleted=0", NULL, FALSE);
			} else {
				$this->db->where("(" . $this->db->dbprefix('item_kits') . ".name LIKE '%" . $this->db->escape_like_str($search) .
						"%' or item_kit_number LIKE '%" . $this->db->escape_like_str($search) . "%' or
				description LIKE '%" . $this->db->escape_like_str($search) . "%') and " . $this->db->dbprefix('item_kits') . ".deleted=0");
			}
	
		} else {
			$this->db->where('item_kits.deleted', 0);
		}
		
		if (!empty($type) && $type == 'bom') {
			$this->db->where('item_kits.type', 'bom');
		} elseif (!empty($type) && $type == 'kit') {
			$this->db->where('( item_kits.type IS NULL OR item_kits.type = "")');
		}
		
		$result = $this->db->get();
		return $result->num_rows();
	}
	
	/*
	 Returns all the item kits
	 */
	function get_all($limit = 10000, $offset = 0, $col = 'name', $ord = 'asc', $type = 'all')
	{
		$current_location = $this->Employee->get_logged_in_employee_current_location_id();
	
		$this->db->select('item_kits.*, categories.name as category,
		location_item_kits.unit_price as location_unit_price,
		location_item_kits.cost_price as location_cost_price');
		$this->db->from('item_kits');
		$this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
		$this->db->join('location_item_kits', 'location_item_kits.item_kit_id = item_kits.item_kit_id and location_id = ' . $current_location, 'left');
		$this->db->where('item_kits.deleted', 0);
	
		if (!$this->config->item('speed_up_search_queries')) {
			$this->db->order_by($col, $ord);
		}
		
		if (!empty($type) && $type == 'bom') {
			$this->db->where('item_kits.type', 'bom');
		} elseif (!empty($type) && $type == 'kit') {
			$this->db->where('( item_kits.type IS NULL OR item_kits.type = "")');
		}
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function count_all($type = 'all')
	{
		$this->db->from('item_kits');
		$this->db->where('deleted', 0);
		
		if (!empty($type) && $type == 'bom') {
			$this->db->where('item_kits.type', 'bom');
		} elseif (!empty($type) && $type == 'kit') {
			$this->db->where('( item_kits.type IS NULL OR item_kits.type = "")');
		}
		
		return $this->db->count_all_results();
	}

    function getInformation($arrParams = null, $options = null) {
        $this->db -> select('*')
                  -> from('item_kits')
                  -> where('product_id', $arrParams['product_id']);

        $query = $this->db->get();

        $result = $query->row_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_items_from_item_kit($arrPrams = null, $options = null) {
        $this->db -> select('k.*, i.name as item_name')
                 -> from('item_kit_items AS k')
                 -> join('items AS i', 'k.item_id = i.item_id')
                 -> where('item_kit_id', $arrPrams['item_kit_id']);

        $query = $this->db->get();

        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_bom_from_item_kit($arrPrams = null, $options = null) {
        $this->db -> select('b.item_kit_id, b.bom_id, b.quantity, k.name AS bom_name')
                -> from('item_kit_boms AS b')
                -> join('item_kits AS k', 'b.bom_id = k.item_kit_id')
                -> where('b.item_kit_id', $arrPrams['item_kit_id']);

        $query = $this->db->get();


        $result = $query->result_array();
        $this->db->flush_cache();

        return $result;
    }
}
?>
