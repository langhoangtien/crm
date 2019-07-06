<?php
class Tag extends CI_Model
{
	function count_all()
	{
		$this->db->from('tags');
		return $this->db->count_all_results();
	}
	
	
	function get_all($limit=10000, $offset=0,$col='name',$order='asc')
	{
		$this->db->from('tags');
		$this->db->where('deleted',0);
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $order);
		}
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $result)
		{
			$return[$result['id']] = array('name' => $result['name']);
		}
		
		return $return;
	}	
    public function getTagsByItemId($itemId)
	{
		$this->db->from('items_tags')
                ->join('tags', 'items_tags.tag_id = tags.id');
        $this->db->where('items_tags.item_id', $itemId);
		$return = array();
		
		foreach($this->db->get()->result_array() as $result) {
			$return['id'][] = $result['id'];
            $return['name'][] = $result['name'];
        }
		
		return $return;
	}
	
	function save($tag_name, $tag_id = FALSE)
	{
		if ($tag_id == FALSE)
		{
			if ($tag_name)
			{
				if($this->db->insert('tags',array('name' => $tag_name)))
				{
					return $this->db->insert_id();
				}
			}
			
			return FALSE;
		}
		else
		{
			$this->db->where('id', $tag_id);
			if ($this->db->update('tags',array('name' => $tag_name)))
			{
				return $tag_id;
			}
		}
		return FALSE;
	}
	
	/*
	Deletes one tag
	*/
	function delete($tag_id)
	{		
		$this->db->where('id', $tag_id);
		return $this->db->update('tags', array('deleted' => 1, 'name' => NULL));
	}
	
	
	function get_tags_for_item($item_id)
	{
		$this->db->select('tags.name, tags.id');
		$this->db->from('items_tags');
		$this->db->join('tags', 'items_tags.tag_id=tags.id');
		$this->db->where('items_tags.item_id', $item_id);
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $result)
		{
			$return[] = $result['name'];
		}
		
		return $return;
	}
	
	function get_tags_for_item_kit($item_kit_id)
	{
		$this->db->select('tags.name, tags.id');
		$this->db->from('item_kits_tags');
		$this->db->join('tags', 'item_kits_tags.tag_id=tags.id');
		$this->db->where('item_kits_tags.item_kit_id', $item_kit_id);
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $result)
		{
			$return[] = $result['name'];
		}
		
		return $return;
	}
	
	function tag_id_exists($tag_id)	
	{
		$this->db->from('tags');
		$this->db->where('id',$tag_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	function tag_name_exists($tag_name)
	{
		$this->db->from('tags');
		$this->db->where('name',$tag_name);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
		
	function get_tag_id_by_name($tag_name)
	{
		$this->db->from('tags');
		$this->db->where('name', $tag_name);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			$row = $query->row();
			return $row->id;
		}
		
		return FALSE;
		
	}
	
	function get_tag_suggestions($search, $limit = 25)
	{
		if (!trim($search))
		{
			return array();
		}
		
		if($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method'))
		{
			$this->db->select("id,name,MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE) as rel", FALSE);
			$this->db->from('tags');
			$this->db->order_by('name');
			$this->db->where("MATCH (name) AGAINST (".$this->db->escape(escape_full_text_boolean_search($search).'*')." IN BOOLEAN MODE)", NULL, FALSE);			
			$this->db->limit($limit);
			$this->db->order_by('rel DESC');
			$this->db->where('deleted',0);
		}
		else
		{
			$this->db->select("id,name", FALSE);
			$this->db->from('tags');
			$this->db->order_by('name');
			$this->db->like("name",$search);			
			$this->db->limit($limit);
			$this->db->where('deleted',0);
		}
		
		$return = array();
		
		foreach($this->db->get()->result_array() as $search_result)
		{
			$return[] = array('label' =>$search_result['name'], 'value' => $search_result['id']);
		}
		
		return $return;
	}
	
	function save_tags_for_item($item_id, $tags)
	{
		//Remove current tags for item
		$this->db->delete('items_tags', array('item_id' => $item_id));
		
		$tags = explode(',', $tags);
		foreach($tags as $tag)
		{
			if ($tag != '')
			{
				$tag = trim($tag);
				
				if (is_numeric($tag) && $this->tag_id_exists($tag)) //Numeric Tag ID
				{
					$this->db->insert('items_tags', array('item_id' => $item_id, 'tag_id' => $tag));
				}
				elseif ($this->tag_name_exists($tag)) //Named tag
				{
					$tag_id = $this->get_tag_id_by_name($tag);
					$this->db->insert('items_tags', array('item_id' => $item_id, 'tag_id' => $tag_id));
				}
				else //Create new tag
				{
					$this->db->insert('tags', array('name' => $tag));
					$tag_id = $this->db->insert_id();
				
					$this->db->insert('items_tags', array('item_id' => $item_id, 'tag_id' => $tag_id));
				}
			}
		}
		
		return TRUE;
	}
	
	function save_tags_for_item_kit($item_kit_id, $tags)
	{
		//Remove current tags for item
		$this->db->delete('item_kits_tags', array('item_kit_id' => $item_kit_id));
		
		$tags = explode(',', $tags);
		foreach($tags as $tag)
		{
			if ($tag != '')
			{
				$tag = trim($tag);
				
				if (is_numeric($tag) && $this->tag_id_exists($tag)) //Numeric Tag ID
				{
					$this->db->insert('item_kits_tags', array('item_kit_id' => $item_kit_id, 'tag_id' => $tag));
				}
				elseif ($this->tag_name_exists($tag)) //Named tag
				{
					$tag_id = $this->get_tag_id_by_name($tag);
					$this->db->insert('item_kits_tags', array('item_kit_id' => $item_kit_id, 'tag_id' => $tag_id));
				}
				else //Create new tag
				{
					$this->db->insert('tags', array('name' => $tag));
					$tag_id = $this->db->insert_id();
				
					$this->db->insert('item_kits_tags', array('item_kit_id' => $item_kit_id, 'tag_id' => $tag_id));
				}
			}
		}
		
		return TRUE;
	}
}