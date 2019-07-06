<?php 
class BizTasksRelation extends CI_Model{
   
	protected $_table = 'task_user_relations';
	public function __construct(){
		parent::__construct();
	}
	
	public function getItems($arrParams = null, $options = null) {
		if($options['task'] == 'by-task') {
			$this->db->select('r.user_id, r.is_xem, r.is_implement, r.is_create_task, r.is_pheduyet')
					  ->from($this->_table . ' as r')
					  ->where('r.task_id', $arrParams['task_id']);
			
			$query = $this->db->get();
			$result = $query->result_array();
		}elseif($options['task'] == 'by-multi-task') {
			$this->db->select('r.user_id, r.is_xem, r.is_implement, r.is_create_task, r.is_pheduyet')
				     ->from($this->_table . ' as r')
				     ->where('r.task_id IN ('.implode(', ', $arrParams['task_ids']).')');
				
			$query = $this->db->get();
			$result = $query->result_array();
		}
		
		return $result;
	}
	
	public function deleteItem($arrParam = null, $options = null) {
		if($options['task'] == 'delete-multi'){
			$this->db->where('task_id IN (' . implode(',', $arrParam['cid']) . ')');
			$this->db->delete($this->_table);
			$this->db->flush_cache();
		}
	}



	#Lấy danh sách ng tham gia hoặc ng phụ trách hoặc ng đc xem theo dự án
	function get_list_user_relation($option="is_implement")
	{
		$this->db->select('GROUP_CONCAT(e.username SEPARATOR ", ") as user_ids,t.task_id');
		$this->db->from('phppos_task_user_relations as t');
		$this->db->join('phppos_employees as e', 't.user_id = e.id', 'left');
		$this->db->where($option, 1);
		// $this->db->where('parent', 0);
		$this->db->group_by('t.task_id');
		$query = $this->db->get();
		$row = array();
		if(!empty($query))
		$row = $query->result_array();
		return $row;
	}


	function get_list_user_by_task($id,$task_id,$option='is_pheduyet')
	{
		$this->db->select();
		$this->db->from('phppos_task_user_relations as tu');
		$this->db->where($option,1);
		$this->db->where('user_id', $id);
		$this->db->where('task_id', $task_id);
		return $this->db->get()->result_array();
	}
}