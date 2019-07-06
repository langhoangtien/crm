<?php
class Appfile extends CI_Model 
{		
	function get($file_id)
	{
		$query = $this->db->get_where('app_files', array('file_id' => $file_id), 1);
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		
		return "";
		
	}

	function get_file_timestamp($file_id)
	{
		$this->db->select('timestamp');
		$query = $this->db->get_where('app_files', array('file_id' => $file_id), 1);
		
		if(!empty($query) && $query->num_rows()==1)
		{
			return strtotime($query->row()->timestamp);
		}
		
		return "";
	}
	
	function get_url_for_file($file_id)
	{
		return app_file_url($file_id);
	}
	
	function save($file_name,$file_data, $file_id = false)
	{
		$file_data=array(
		'file_name'=>$file_name,
		'file_data'=>$file_data
		);
				
		if (!$file_id)
		{
			if ($this->db->insert('app_files',$file_data))
			{
				return $this->db->insert_id();
			}
			
			return false;
		}
		
		$this->db->where('file_id', $file_id);
		if ($this->db->update('app_files',$file_data))
		{
			return $file_id;
		}
		
		return false;
	}
		
	function delete($file_id)
	{
		return $this->db->delete('app_files', array('file_id' => $file_id)); 
	}
}

?>