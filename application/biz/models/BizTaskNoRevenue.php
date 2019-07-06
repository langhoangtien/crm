<?php 
class BizTaskNoRevenue extends CI_Model{


	protected $_table = 'phppos_task_no_revenue';
	function __construct(){
		parent::__construct();
	}


	public function get_list($arrParams=null,$option=null)
	{
		#Mặc định option = ull lấy danh sách người
		$type = ($option==null) ? $option : 0;
		$subquery = $this->db->select('te.*, GROUP_CONCAT(e.username) as user_name')
		->from('phppos_task_no_revenue_relation as te')
		->join('phppos_employees as e', 'te.user_id = e.id')
		->where('type', $type)
		->group_by('task_id')
		->get_compiled_select();

		$this->db->select('tn.*, tr.user_name');
		$this->db->from('phppos_task_no_revenue as tn');
		$this->db->join("( $subquery ) as tr", 'tn.user_id = tr.task_id', 'left');
		return $this->db->get()->result_array();
	}


	
}