<?php 
class BizTaskCustomers extends CI_Model{
	protected $_table="customers";
	public function __construct(){
		parent::__construct();
	}
	
	public function getItems($arrParams = null, $options = null) {
		if($options == null) {
			$this->db->select("CONCAT_WS(' ', p.first_name, p.last_name) AS name", FALSE);
			$this->db->select("c.id")
					->from($this->_table . ' AS c')
					->join('people as p', 'c.person_id = p.person_id', 'left')
					->where_in('c.id',$arrParams['cid'])
					->where('c.deleted = 0')
					->order_by("c.id",'DESC');
				
			$query = $this->db->get();
	
			$result = $query->result_array();
			$this->db->flush_cache();
		}
	
		return $result;
	}
	
	public function listItem($arrParam = null, $options = null){
		$lo = $this->Employee->get_logged_in_employee_current_location_id();
		if($options['task'] == null) {
			$this->db->select("c.id, CONCAT_WS(' ',p.first_name,p.last_name) AS name")
					->from($this->_table . ' AS c')
					->join('people as p', 'c.person_id = p.person_id', 'left')
					->where('c.created_location_id', $lo)
					->order_by("c.id",'DESC');
			
			if(!empty($arrParam['keywords'])){
				$keywords = trim($arrParam['keywords']);
				$keywordsArr = explode(' ', $keywords);
				foreach($keywordsArr as $keyword) {
					$where[] = " ( CONCAT_WS(' ',p.first_name,p.last_name) LIKE '%$keyword%' ) ";
				}
					
				$where = implode(' OR ', $where);
				$this->db->where($where);
			}
			$this->db->group_by('c.person_id');	
			$this->db->limit(10);
			
			$query = $this->db->get();
			
			$result = $query->result_array();
			$this->db->flush_cache();
		}
		
		// echo $this->db->last_query();die();
		return $result;
	}
}