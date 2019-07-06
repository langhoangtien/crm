<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_System_Info{
	protected $_CI;
	protected $_id;
	protected $_group_id;
	protected $_super_groups = [7, 8];

	public function __construct(){
		$this->_CI= &get_instance();
	}

	public function getInfo() {
		$task_permission = array();
		$person_id = $_SESSION['person_id'];
		$this->_CI->db-> select('*')
				       ->where('person_id', (int)$person_id);
        $query = $this->_CI->db->get('employees');
        if (!$query) {
            return false;
        }
		$result =  $query->row_array();
		$this->_CI->db->flush_cache();

		$group_id = $result['group_id'];

		$this->_CI->db->select('*')
					  ->from('group_permissions_actions')
			  		  ->where('group_id', $group_id)
					  ->where('module_id', 'tasks');

		$query = $this->_CI->db->get();

		$resultTmp = $query->result_array();

		$this->_CI->db->flush_cache();
		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$task_permission[] = $val['action_id'];
		}

		$this->_CI->db->select('*')
					->from('permissions_actions')
					->where('person_id', $person_id);

		$query = $this->_CI->db->get();

		$resultTmp = $query->result_array();

		$this->_CI->db->flush_cache();

		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$task_permission[] = $val['action_id'];
		}

		$array['task_permission'] = $task_permission;
		$array['id'] 			  = $result['id'];
		$array['username'] 		  = $result['username'];

		return $array;
	}

	public function get_permissions($module_id) {
		$task_permission = array();
		$person_id = $_SESSION['person_id'];
		$this->_CI->db-> select('*')
				       ->where('person_id', (int)$person_id);
        $query = $this->_CI->db->get('employees');
        if (!$query) {
            return false;
        }
		$result =  $query->row_array();
		$this->_CI->db->flush_cache();

		$group_id = $result['group_id'];

		$this->_CI->db->select('*')
					  ->from('group_permissions_actions')
			  		  ->where('group_id', $group_id)
					  ->where('module_id', $module_id);

		$query = $this->_CI->db->get();
		// echo $this->_CI->db->last_query(); exit();

		$resultTmp = $query->result_array();

		$this->_CI->db->flush_cache();
		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$task_permission[] = $val['action_id'];
		}

		$this->_CI->db->select('*')
					->from('permissions_actions')
					->where('person_id', $person_id);

		$query = $this->_CI->db->get();

		$resultTmp = $query->result_array();

		$this->_CI->db->flush_cache();

		if(!empty($resultTmp)) {
			foreach($resultTmp as $val)
				$task_permission[] = $val['action_id'];
		}

		return $task_permission;
	}

    /**
     * @param $module_id
     * @return bool
     */
	public function has_access_module_permission($module_id) {
        $person_id = $_SESSION['person_id'];
        $this->_CI->db->select('*')->where('person_id', (int)$person_id);
        $query = $this->_CI->db->get('employees');
        if (!$query) {
            return false;
        }
        $result =  $query->row_array();
        $this->_CI->db->flush_cache();
        $group_id = $result['group_id'];
        $this->_CI->db->select('*')->from('group_permissions')->where('group_id', $group_id)->where('module_id', $module_id);
        $query = $this->_CI->db->get();
        $is_access = ($query->num_rows() > 0);
        $query->free_result();
        if (!$is_access) {
            $this->_CI->db->flush_cache();
            $this->_CI->db->select('*')->from('permissions')->where('person_id', $person_id)->where('module_id', $module_id);
            $query = $this->_CI->db->get();
            $is_access = ($query->num_rows() > 0);
            $query->free_result();
        }
        return $is_access;
    }

    /**
     * Get task assigned permission
     * @param $task_id
     * @return mixed
     */
	public function get_task_assigned_permission($task_id) {
        $person_id = $_SESSION['person_id'];
        $this->_CI->db->select('task_user_relations.*');
        $this->_CI->db->from('task_user_relations');
        $this->_CI->db->join('employees', 'task_user_relations.user_id = employees.id');
        $this->_CI->db->where('person_id', $person_id);
        $this->_CI->db->where('task_id', $task_id);
        $query = $this->_CI->db->get();
        $result = $query->row();
        $query->free_result();
        $this->_CI->db->flush_cache();
        return $result;
    }

    /**
     * Get group id
     * @return mixed
     */
    public function get_group_id() {
        $person_id = $_SESSION['person_id'];
        $this->_CI->db->select('group_id');
        $this->_CI->db->where('person_id', $person_id);
        $query = $this->_CI->db->get('employees');
        $row =  $query->row();
        $this->_CI->db->flush_cache();
        return $row->group_id;
    }

    /**
     * @return array
     */
    public function get_super_groups() {
        return $this->_super_groups;
    }

    public function is_super_group() {
        $group_id = $this->get_group_id();
        $super_groups = $this->get_super_groups();
        if (in_array($group_id, $super_groups)) {
            return true;
        }
    }
}