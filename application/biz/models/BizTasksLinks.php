<?php 
class BizTasksLinks extends CI_Model{
   
	protected $_table = 'task_links';
	public function __construct(){
        $this->load->library('MY_System_Info');
        $info 			 = new MY_System_Info();
        $user_info 		 = $info->getInfo();

        $this->_id_admin = $user_info['id'];

		parent::__construct();
	}
	
	public function listItem($arrParams = null, $options = null) {
		return;
		if($options['task'] == 'by-source') {
			// echo "<pre>";
			// var_dump($arrParams['task_ids']);die();
			$this->db->select("l.*")
					->from($this->_table . ' AS l')
					->where('l.source IN ('.implode(',', $arrParams['task_ids']).')');

			$query = $this->db->get();

			$result = $query->result_array();
		}
		return $result;
	}
	
	public function saveItem($arrParam = null, $options = null) {
		if($options['task'] == 'add') {
			$data['source']					= 		$arrParam['source'];
			$data['target']					= 		$arrParam['target'];
			$data['type']					= 		$arrParam['type'];
			$data['created']				= 		@date("Y-m-d H:i:s");
			$data['created_by']				= 		$this->_id_admin;

			$this->db->insert($this->_table,$data);
			$lastId = $this->db->insert_id();
		}
	}

	public function deleteItem($arrParam = null, $options = null) {
		if($options['task'] == 'delete'){
			$this->db->where('id = ' . $arrParam['id']);
			$this->db->delete($this->_table);
			$this->db->flush_cache();
		}elseif($options['task'] == 'delete-multi-by-task') {
            $tblTasks = $this->model_load_model('Task');
            $task_ids = $tblTasks->getIds($arrParam, array('task'=>'by-task-ids'));
            if(!empty($task_ids)) {
                $this->db->where('task_id IN ('.implode(',', $task_ids).')');
                $this->db->delete($this->_table);
                $this->db->flush_cache();
            }
        }
	}

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }
}