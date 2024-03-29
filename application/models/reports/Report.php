<?php
abstract class Report extends CI_Model 
{
	var $params	= array();
	function __construct()
	{
		parent::__construct();
		$this->report_limit = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		
		//Make sure the report is not cached by the browser
		$this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");		
	}
	
	public function getTotalRows()
	{
		$this->db->select("COUNT(DISTINCT(sale_id)) as sale_count");
		$this->db->from('sales_items_temp');
		$ret = $this->db->get()->row_array();
		return $ret['sale_count'];
	}
	
	public function setParams(array $params)
	{
		$this->params = $params;
	}
	
	public static function get_selected_location_ids()
	{
		$CI =& get_instance();
		
		if ($CI->session->userdata('reports_selected_location_ids'))
		{
			return $CI->session->userdata('reports_selected_location_ids');
		}
		else
		{
			return array($CI->Employee->get_logged_in_employee_current_location_id());
		}
	}
	
	//Returns the column names used for the report
	// public abstract function getDataColumns();
	
	// //Returns all the data to be populated into the report
	// public abstract function getData();
	
	// //Returns key=>value pairing of summary data for the report
	// public abstract function getSummaryData();

	public function getTotalRowsMailHitory($pid = 0)
	{
		$this->db->select("COUNT(*) as mail_count");
		$this->db->from('mail_history');
		$this->db->where('person_id='.$pid);
		$ret = $this->db->get()->row_array();
		return $ret['mail_count'];
	}
	



	
                            	


}
?>