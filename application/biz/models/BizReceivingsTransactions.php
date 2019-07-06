<?php
class BizReceivingsTransactions extends CI_Model
{
	/*
	 Inserts or updates a item
	 */
	function save($data = [])
	{
		if($this->db->insert('receivings_transactions', $data))
		{
			return true;
		}
		return false;
	}
	
}