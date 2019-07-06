<?php

include_once ('BizNested2.php');

class BizTask extends BizNested2
{

    protected $_table = 'tasks';

    protected $_id_admin = null;

    protected $_user = null;

    protected $_task_permission = null;

    protected $_fields = array();

    protected $_prioty = null;

    protected $_trangthai = null;

    protected $_trangthai_type = null;

    public $_scopeOfView = 'view_scope_owner';



    public function __construct()
    {
        parent::__construct();
        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $user_info = $info->getInfo();
        $this->_user = $user_info;

        $this->_id_admin = $user_info['id'];
        $this->_task_permission = $user_info['task_permission'];
        $this->load->model('TaskUser');
        $this->_fields = array(
            'project' => 't.project_id',
            'progress' => 't.progress',
            'name' => 't.name',
            'prioty' => 't.prioty',
            'date_start' => 't.date_start',
            'date_end' => 't.date_end',
            'modified' => 't.modified',
            'username' => 'e.username',
            'last_name'=> 'p.last_name',
        );
        
        $this->_trangthai_type = array(
            'cancel' => array(
                '3'
            ), // đóng, dừng
            'not-done' => array(
                '4'
            ), // không thực hiện
            'unfulfilled' => array(
                '0'
            ), // chưa thực hiện
            'processing' => array(
                '1'
            ), // đang tiến hành
            'slow_proccessing' => array(
                '0',
                '1',
                '5'
            ), // chậm tiến độ
            'finish' => array(
                '2'
            ), // hoàn thành
            'slow-finish' => array(
                '2',
                '5'
            ), // hoàn thành nhưng chậm tiến độ
            'completed-on-schedule' => array(
                '2',
                '6'
            )
        ) // hoàn thành đúng tiến độ
        ;
        
        $this->_prioty = lang('task_prioty');
        $this->_trangthai = lang('task_trangthai');

        if ($this->Employee->has_module_action_permission(
            'tasks',
            'view_scope_location',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_location';
        }

        if ($this->Employee->has_module_action_permission(
            'tasks',
            'view_scope_all',
            $this->Employee->get_logged_in_employee_info()->person_id)
    ) {
            $this->_scopeOfView = 'view_scope_all';
        }
    }
    
    // check if a task is exists
    public function checkItemExist($id)
    {
        $this->db->select('COUNT(t.id) AS totalItem')
        ->from($this->_table . ' AS t')
        ->where('t.id', $id);
        
        $query = $this->db->get();
        $result = $query->row()->totalItem;
        $this->db->flush_cache();
        
        return $result;
    }
    
    // Check the parents is approved or not
    public function check_parent_appoval($task)
    {
        $lft = $task['lft'];
        $rgt = $task['rgt'];
        $project_id = $task['project_id'];
        $this->db->select("COUNT(t.id) AS totalItem")
        ->from($this->_table . ' AS t')
        ->where('t.lft < ' . $lft . ' AND rgt > ' . $rgt)
        ->where('t.pheduyet IN (-1, 0)')
        ->where('t.project_id', $project_id);
        
        $query = $this->db->get();
        
        $result = $query->row_array();
        $result = $result['totalItem'];
        
        if ($result > 0)
            return true;
        else
            return false;
    }

    public function getProjects($arrParam=array())
    {
        $this->db->select("t.*")
        ->from($this->_table . ' AS t')
        ->where('t.parent', 0);
        if(!empty($arrParam['limit']))
        {
            $this->db->limit($arrParam['limit']);
        }
        $query = $this->db->get();
        return !empty($query) ? $query->result_array() : [];
    }
    function get_top_project(){
        $project_ids = $this->get_task_join_implement($this->Employee->get_logged_in_employee_info()->id);
        $view = "view_owner";
        if($this->Employee->has_module_action_permission('tasks', 'view_scope_location', $this->Employee->get_logged_in_employee_info()->person_id))
            $view = "view_scope_location";
        if($this->Employee->has_module_action_permission('tasks', 'view_scope_all', $this->Employee->get_logged_in_employee_info()->person_id))
            $view = "view_scope_all";  

        $subquery = $this->db->select('SUM(IF(cp.vat="published", (cp.price / 1.1), cp.price)) 
            as value, cp.contract_id')
        ->from('phppos_contract_payment as cp')
        ->where('cp.c_status','done')
        ->or_where('cp.c_status','liquidated')
        ->group_by('cp.contract_id')
        ->get_compiled_select();
        $this->db->select('t.id,t.name,t.date_start,t.date_end,t.progress,cp.value');
        $this->db->from('phppos_tasks AS t');
        $this->db->join('phppos_sales as s', 's.task_id = t.id');
        $this->db->join('phppos_contract as c', 'c.sale_id = s.sale_id');
        $this->db->join("($subquery) as cp", 'cp.contract_id = c.id','left');
        if($view =="view_owner")
        { 
            if(empty($project_ids))
            {
                $this->db->get()->result_array();
                return [];
            }
            $this->db->where_in('t.id', $project_ids);
        }
        if($view=='view_scope_location'){
            $this->db->where('s.location_id', $this->Employee->get_logged_in_employee_current_location_id());
            if(!empty($project_ids))
                $this->db->or_where_in('t.id', $project_ids);
        }
        $this->db->where('t.parent', 0);
        $this->db->limit(10);
        $this->db->order_by('value', 'desc');
        $query = $this->db->get();
        // echo $this->db->last_query();die();
        return !empty($query) ? $query->result_array() : [];
    }
    
    public function check_lastest_lowel($task_id)
    {
        $this->db->select("COUNT(t.id) AS totalItem")
        ->from($this->_table . ' AS t')
        ->where('t.parent', $task_id);
        
        $query = $this->db->get();
        
        $result = $query->row_array();
        $result = $result['totalItem'];
        if ($result > 0)
            return false;
        else
            return true;
    }

    public function statistic($arrParams = null, $options = null)
    {
        $id_admin = !empty($arrParams['employee_id']) ? $arrParams['employee_id'] : $this->_id_admin;
        $tasks = $this->db->dbprefix($this->_table);
        $task_user_relations = $this->db->dbprefix(task_user_relations);
        if ($options['task'] == 'task-by-project') {
            $task_ids = $this->getTasksIdsByProject($arrParams['project']);
            $where = $this->get_where_from_filter($arrParams);
            
            $this->db->select("COUNT(t.id) AS totalItem")->from($this->_table . ' AS t');
            
            if ($task_ids == 'all') {
                $this->db->where('t.project_id', $arrParams['project_id']);
            } else {
                $this->db->where('t.id IN ' . implode(', ', $task_ids));
            }
            
            $this->db->where('t.id != ' . $arrParams['project_id']);
            
            if (! empty($where)) {
                foreach ($where as $wh)
                    $this->db->where($wh);
            }
            
            $query = $this->db->get();
            
            $result = $query->row_array();
            $result = $result['totalItem'];
        } elseif ($options['task'] == 'task-by-project-implement') {
            if (! empty($arrParams['implement'])) {
                $implement_arr = explode(',', $arrParams['implement']);
                if (in_array($this->_id_admin, $implement_arr))
                    $arrParams['implement'] = $this->_id_admin;
                else
                    $arrParams['implement'] = '-1';
            } else
            $arrParams['implement'] = $this->_id_admin;
            
            $task_ids = $this->getTasksIdsByProject($arrParams['project']);
            $where = $this->get_where_from_filter($arrParams);
            
            $this->db->select("COUNT(t.id) AS totalItem")->from($this->_table . ' AS t');
            
            if ($task_ids == 'all') {
                $this->db->where('t.project_id', $arrParams['project_id']);
            } else {
                $this->db->where('t.id IN ' . implode(', ', $task_ids));
            }
            
            $this->db->where('t.id != ' . $arrParams['project_id']);
            
            if (! empty($where)) {
                foreach ($where as $wh)
                    $this->db->where($wh);
            }
            
            $query = $this->db->get();
            
            $result = $query->row_array();
            $result = $result['totalItem'];
        } elseif ($options['task'] == 'task-by-project-cc') {
            if (! empty($arrParams['xem'])) {
                $xem_arr = explode(',', $arrParams['xem']);
                if (in_array($this->_id_admin, $xem_arr))
                    $arrParams['xem'] = $this->_id_admin;
                else
                    $arrParams['xem'] = '0';
            } else
            $arrParams['xem'] = $this->_id_admin;
            
            $where = $this->get_where_from_filter($arrParams);
            
            $task_ids = $this->getTasksIdsByProject($arrParams['project']);
            
            $this->db->select("COUNT(t.id) AS totalItem")->from($this->_table . ' AS t');
            
            if ($task_ids == 'all') {
                $this->db->where('t.project_id', $arrParams['project_id']);
            } else {
                $this->db->where('t.id IN ' . implode(', ', $task_ids));
            }
            
            $this->db->where('t.id != ' . $arrParams['project_id']);
            
            if (! empty($where)) {
                foreach ($where as $wh)
                    $this->db->where($wh);
            }
            
            $query = $this->db->get();
            
            $result = $query->row_array();
            $result = $result['totalItem'];
        } elseif ($options['task'] == 'task-by-project-trangthai') {
            $task_ids = $this->getTasksIdsByProject($arrParams['project']);
            $type = $this->_trangthai_type[$options['type']];
            if (! empty($arrParams['trangthai'])) {
                $trangthai = $arrParams['trangthai'];
                if ($trangthai == 'zero')
                    $trangthai = '0';
                
                $trangthai_arr = explode(',', $trangthai);
                if ((array_search('5', $trangthai_arr)) == false && (array_search('6', $trangthai_arr)) == false) {
                    $trangthai_arr[] = '5';
                    $trangthai_arr[] = '6';
                }
            } else
            $trangthai_arr = array(
                '0',
                '1',
                '2',
                '3',
                '4',
                '5',
                '6'
            );
            
            foreach ($trangthai_arr as $val) {
                if (in_array($val, $type)) {
                    if (($key = array_search($val, $type)) !== false) {
                        unset($type[$key]);
                    }
                }
            }
            
            if (count($type) == 0) {
                $arrParams['trangthai'] = implode(',', $this->_trangthai_type[$options['type']]);
                if ($arrParams['trangthai'] == '0')
                    $arrParams['trangthai'] = 'zero';
            } else
            $arrParams['trangthai'] = '-1';
            
            $where = $this->get_where_from_filter($arrParams);
            
            $this->db->select("COUNT(t.id) AS totalItem")->from($this->_table . ' AS t');
            
            if ($task_ids == 'all') {
                $this->db->where('t.project_id', $arrParams['project_id']);
            } else {
                $this->db->where('t.id IN ' . implode(', ', $task_ids));
            }
            
            $this->db->where('t.id != ' . $arrParams['project_id']);
            
            if (! empty($where)) {
                foreach ($where as $wh)
                    $this->db->where($wh);
            }
            
            $query = $this->db->get();
            
            $result = $query->row_array();
            $result = $result['totalItem'];
        } elseif ($options['task'] == 'task-by-all') {
            // clause
            $where = $this->get_where_from_filter($arrParams);
            if (! empty($where)) {
                $where = implode(' AND ', $where);
                $where = 'AND ' . $where;
            } else
            $where = '';
            
            $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
            FROM  $tasks AS t
            INNER JOIN
            (
            SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
            FROM $tasks
            INNER JOIN $task_user_relations
            ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
            ) AS tmp
            ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
            WHERE t.parent != 0
            $where";
            
            $query = $this->db->query($sql);
            $result = $query->row_array();
            $result = $result['totalItem'];
        } elseif ($options['task'] == 'task-by-all-implement') {

            if (! empty($arrParams['implement'])) {
                $implement_arr = explode(',', $arrParams['implement']);
                if (in_array($this->_id_admin, $implement_arr))
                    $arrParams['implement'] = $this->_id_admin;
                else
                    $arrParams['implement'] = '-1';
            } else
            $arrParams['implement'] = $this->_id_admin;

                // clause
            $where = $this->get_where_from_filter($arrParams);
            if (! empty($where)) {
                $where = implode(' AND ', $where);
                $where = 'AND ' . $where;
            } else
            $where = '';
            
            $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
            FROM  $tasks AS t
            INNER JOIN
            (
            SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
            FROM $tasks
            INNER JOIN $task_user_relations
            ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
            ) AS tmp
            ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
            WHERE t.parent != 0
            $where";
            
            $query = $this->db->query($sql);
            $result = $query->row_array();
            $result = $result['totalItem'];
        } elseif ($options['task'] == 'task-by-all-cc') {
            if (! empty($arrParams['xem'])) {
                $xem_arr = explode(',', $arrParams['xem']);
                if (in_array($this->_id_admin, $xem_arr))
                    $arrParams['xem'] = $this->_id_admin;
                else
                    $arrParams['xem'] = '0';
            } else
            $arrParams['xem'] = $this->_id_admin;

                // clause
            $where = $this->get_where_from_filter($arrParams);
            if (! empty($where)) {
                $where = implode(' AND ', $where);
                $where = 'AND ' . $where;
            } else
            $where = '';
            
            $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
            FROM  $tasks AS t
            INNER JOIN
            (
            SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
            FROM $tasks
            INNER JOIN $task_user_relations
            ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
            ) AS tmp
            ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
            WHERE t.parent != 0
            $where";
            
            $query = $this->db->query($sql);
            $result = $query->row_array();
            $result = $result['totalItem'];
        } elseif ($options['task'] == 'task-by-all-trangthai') {
            $type = $this->_trangthai_type[$options['type']];
            
            if (! empty($arrParams['trangthai'])) {
                $trangthai = $arrParams['trangthai'];
                if ($trangthai == 'zero')
                    $trangthai = '0';
                
                $trangthai_arr = explode(',', $trangthai);
                if ((array_search('5', $trangthai_arr)) == false && (array_search('6', $trangthai_arr)) == false) {
                    $trangthai_arr[] = '5';
                    $trangthai_arr[] = '6';
                }
            } else
            $trangthai_arr = array(
                '0',
                '1',
                '2',
                '3',
                '4',
                '5',
                '6'
            );
            
            foreach ($trangthai_arr as $val) {
                if (in_array($val, $type)) {
                    if (($key = array_search($val, $type)) !== false) {
                        unset($type[$key]);
                    }
                }
            }
            
            if (count($type) == 0) {
                $arrParams['trangthai'] = implode(',', $this->_trangthai_type[$options['type']]);
                if ($arrParams['trangthai'] == '0')
                    $arrParams['trangthai'] = 'zero';
            } else
            $arrParams['trangthai'] = '-1';

                // clause
            $where = $this->get_where_from_filter($arrParams);
            if (! empty($where)) {
                $where = implode(' AND ', $where);
                $where = 'AND ' . $where;
            } else
            $where = '';
            
            $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
            FROM  $tasks AS t
            INNER JOIN
            (
            SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
            FROM $tasks
            INNER JOIN $task_user_relations
            ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
            ) AS tmp
            ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
            WHERE t.parent != 0
            $where";
            
            $query = $this->db->query($sql);
            $result = $query->row_array();
            $result = $result['totalItem'];
        }
        // echo "<pre>"; echo $result; die();
        return $result;
    }

    public function itemSelectBox($arrParams = null, $options = null)
    {
        if ($options == null) {
            $this->db->select("t.id, t.name, t.level")
            ->from('tasks as t')
            ->where('t.lft >= ' . $arrParams['lft'] . ' AND rgt <= ' . $arrParams['rgt'])
            ->where('t.project_id', $arrParams['project_id']);
            
            $query = $this->db->get();
            $result = $query->result_array();
            $this->db->flush_cache();
            if (! empty($result)) {
                foreach ($result as &$val) {
                    $val['name'] = str_repeat('-', $val['level']) . ' ' . $val['name'];
                }
            }
        }
        
        return $result;
    }

    public function countItem($arrParams = null, $options = null)
    {

        if($this->Employee->has_module_action_permission('tasks','view_scope_all',$this->session->userdata('person_id')))
        {
            $view ='view_scope_all';
        }
        else if ($this->Employee->has_module_action_permission('tasks','view_scope_location',$this->session->userdata('person_id')))
        {
            $view ='view_scope_location';
        }
        else{
            $view ='view_owner';
        }


        if ($options == null || $options['task'] == 'grid-project') {
            $flagAll = $this->checkAllPermission();
            if ($flagAll == false) {
                if ($view == 'view_scope_all') {
                    $result = $this->db->count_all_results($this->_table);
                } else if($view=='view_scope_location') {
                    $location_id = $this->Employee->get_logged_in_employee_current_location_id();
                    $project_ids = $this->get_same_location_project_ids($_SESSION['person_id'], $location_id);
                    $this->db->from($this->_table);
                    $this->db->where_in('id', $project_ids);
                    $result = $this->db->count_all_results();
                } else {
                    // related project
                    $sql = 'SELECT COUNT(t.id) AS total_item
                    FROM ' . $this->db->dbprefix($this->_table) . ' AS t
                    WHERE t.id IN (SELECT task_id FROM ' . $this->db->dbprefix(task_user_relations) . ' WHERE user_id = ' . $this->_id_admin . ')
                    AND t.parent = 0';

                    $where = $this->get_where_from_filter($arrParams);
                    if (! empty($where)) {
                        $where = implode(' AND ', $where);
                        $sql = $sql . ' AND  ' . $where;
                    }
                    $query = $this->db->query($sql);
                    $result = $query->row()->total_item;
                }
            } else {
                $where = $this->get_where_from_filter($arrParams);
                $this->db->select('COUNT(t.id) AS totalItem')
                ->from($this->_table . ' AS t');
                $this->db->join('phppos_sales as s', 's.task_id = t.id');
                if($view=='view_scope_all')
                {

                }
                else if($view=='view_scope_location')
                {
                    $this->db->where('s.location_id', $this->Employee->get_logged_in_employee_current_location_id());
                }
                else
                {
                    $this->db->where('t.created_by', $this->Employee->get_logged_in_employee_info()->id);
                }

                $this->db->where('t.parent = 0');
                
                if (! empty($where)) {
                    foreach ($where as $wh)
                        $this->db->where($wh);
                }
                
                $query = $this->db->get();
                $result = $query->row()->totalItem;
            }
        }
        return $result;
    }
    
    
    public function saveTaskUserRelations($records = []) {
        $this->db->insert_batch('task_user_relations', $records);
    }

    public function saveItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'add') {

            $customer_ids = $arrParam['customer'];

            if ($arrParam['progress'] == 100)
                $date_finish = @date("Y-m-d H:i:s");
            else
                $date_finish = '0000/00/00 00:00:00';
            
            $data['sale_id'] = isset($arrParam['sale_id'])? $arrParam['sale_id'] : 0;

            if ($arrParam['parent'] == 0) { 

                $data['name'] = stripslashes($arrParam['name']);
                $data['detail'] = stripslashes($arrParam['detail']);
                $data['percent'] = 100;
                $data['progress'] = $arrParam['progress'];
                $data['lft'] = 0;
                $data['rgt'] = 1;
                $data['level'] = 0;
                $data['parent'] = $arrParam['parent'];
                $data['project_id'] = $arrParam['project_id'];
                $data['date_start'] = $arrParam['date_start'];
                $data['date_end'] = $arrParam['date_end'];
                $data['date_finish'] = $date_finish;
                $data['created'] = @date("Y-m-d H:i:s");
                $data['created_by'] = $this->_id_admin;
                $data['modified'] = @date("Y-m-d H:i:s");
                $data['modified_by'] = $this->_id_admin;
                $data['trangthai'] = $arrParam['trangthai'];
                $data['prioty'] = $arrParam['prioty'];
                $data['pheduyet'] = 2;
                $data['project_id'] = 0;
                $data['customer_ids'] = $customer_ids;
                $data['color'] = $arrParam['color'];
                //  echo "<pre>gggg";
                // var_dump($data);die();
                $this->db->insert($this->_table, $data);
                $lastId = $this->db->insert_id();
                
                if ($lastId > 0) {
                    $this->db->where("id", $lastId);
                    $data['project_id'] = $lastId;
                    $this->db->update($this->_table, $data);
                }
            } else {
                $data['name'] = stripslashes($arrParam['name']);
                $data['detail'] = stripslashes($arrParam['detail']);
                $data['percent'] = $arrParam['percent'];
                $data['progress'] = $arrParam['progress'];
                $data['date_start'] = $arrParam['date_start'];
                $data['date_end'] = $arrParam['date_end'];
                $data['date_finish'] = $date_finish;
                $data['created'] = @date("Y-m-d H:i:s");
                $data['created_by'] = $this->_id_admin;
                $data['modified'] = @date("Y-m-d H:i:s");
                $data['modified_by'] = $this->_id_admin;
                $data['trangthai'] = $arrParam['trangthai'];
                $data['prioty'] = $arrParam['prioty'];
                $data['pheduyet'] = $arrParam['pheduyet'];
                $data['project_id'] = $arrParam['project_id'];
                $data['customer_ids'] = $customer_ids;
                $data['color'] = $arrParam['color'];
                
                $lastId = $this->insertNode($data, $arrParam['parent'], $arrParam['project_id']);
            }
            
            if ($lastId > 0) {
                $xemArr = array();
                if (isset($arrParam['xem']))
                    $xemArr = $arrParam['xem'];

                $joinArr = array();
                if (isset($arrParam['join']))
                    $joinArr = $arrParam['join'];
                
                $implementArr = array();
                if (isset($arrParam['implement']))
                    $implementArr = $arrParam['implement'];
                
                $create_taskArr = array();
                if (isset($arrParam['create_task']))
                    $create_taskArr = $arrParam['create_task'];
                
                $pheduyet_taskArr = array();
                if (isset($arrParam['pheduyet_task']))
                    $pheduyet_taskArr = $arrParam['pheduyet_task'];
                
                $progress_taskArr = array();
                if (isset($arrParam['progress_task']))
                    $progress_taskArr = $arrParam['progress_task'];
                
                $array = $this->do_relation_information($lastId, $xemArr,$joinArr, $implementArr, $create_taskArr, $pheduyet_taskArr, $progress_taskArr);
                
                if (! empty($array)) {
                    $this->db->insert_batch('task_user_relations', $array);
                }
            }
            
            $this->db->flush_cache();
        } elseif ($options['task'] == 'edit') {
            $lastId = $arrParam['id'];
            if (isset($arrParam['customer'])) {
                $customer_ids = implode(',', $arrParam['customer']);
            }
            // var_dump($customer_ids);
            $this->db->where("id", $arrParam['id']);
            
            $data['name'] = stripslashes($arrParam['name']);
            $data['detail'] = stripslashes($arrParam['detail']);
            $data['date_start'] = date('Y-m-d', strtotime($arrParam['date_start']));
            $data['date_end'] = date('Y-m-d', strtotime($arrParam['date_end']));
            $data['modified'] = @date("Y-m-d H:i:s");
            $data['modified_by'] = $this->_id_admin;
            $data['color'] = $arrParam['color'];
            $data['customer_ids'] = $customer_ids;
            if ($arrParam['parent'] != 0)
                $data['percent'] = $arrParam['percent'];
           // var_dump($data);die();
            $this->db->update($this->_table, $data);
            $this->db->flush_cache();
            
            $tblRelation = $this->model_load_model('TasksRelation');
            $tblRelation->deleteItem(array(
                'cid' => array(
                    $arrParam['id']
                )
            ), array(
                'task' => 'delete-multi'
            ));
            
            $xemArr = array();
            if (isset($arrParam['xem']))
                $xemArr = $arrParam['xem'];


            $joinArr = array();
            if (isset($arrParam['join']))
                $joinArr = $arrParam['join'];
            
            $implementArr = array();
            if (isset($arrParam['implement']))
                $implementArr = $arrParam['implement'];
            
            $create_taskArr = array();
            if (isset($arrParam['create_task']))
                $create_taskArr = $arrParam['create_task'];
            
            $pheduyet_taskArr = array();
            if (isset($arrParam['pheduyet_task']))
                $pheduyet_taskArr = $arrParam['pheduyet_task'];
            
            $progress_taskArr = array();
            if (isset($arrParam['progress_task']))
                $progress_taskArr = $arrParam['progress_task'];
            
            $array = $this->do_relation_information($lastId, $xemArr,$joinArr, $implementArr, $create_taskArr, $pheduyet_taskArr, $progress_taskArr);
            
            if (! empty($array)) {
                $this->db->insert_batch('task_user_relations', $array);
            }
            
            $this->db->flush_cache();
        } elseif ($options['task'] == 'quick-update') {
            $this->db->where("id", $arrParam['id']);
            $data['date_start'] = $arrParam['date_start'];
            $data['date_end'] = $arrParam['date_end'];
            
            $this->db->update($this->_table, $data);
            
            $this->db->flush_cache();
            
            $lastId = $arrParam['id'];
        } elseif ($options['task'] == 'pheduyet') {
            $this->db->where("id", $arrParam['id']);
            $data['pheduyet'] = $arrParam['pheduyet_select'];
            $data['pheduyet_note'] = stripslashes($arrParam['pheduyet_note']);
            
            $this->db->update($this->_table, $data);
            
            $this->db->flush_cache();
            
            $lastId = $arrParam['id'];
        } elseif ($options['task'] == 'update-tiendo') {
            $this->db->where("id", $arrParam['id']);
            
            $data['trangthai'] = $arrParam['trangthai'];
            $data['prioty'] = $arrParam['prioty'];
            if ($arrParam['progress'] != - 1)
                $data['progress'] = $arrParam['progress'];
            
            if ($arrParam['progress'] == 100)
                $data['date_finish'] = @date("Y-m-d H:i:s");
            else
                $data['date_finish'] = '0000/00/00 00:00:00';
            
            $this->db->update($this->_table, $data);
            
            $this->db->flush_cache();
            
            $lastId = $arrParam['id'];
        } elseif ($options['task'] == 'update-progress') {
            $this->db->where("id", $arrParam['id']);
            $data['progress'] = $arrParam['progress'];
            $data['trangthai'] = $arrParam['trangthai'];
            $data['date_finish'] = $arrParam['date_finish'];
            
            $this->db->update($this->_table, $data);
            
            $this->db->flush_cache();
            
            $lastId = $arrParam['id'];
        } elseif ($options['task'] == 'custom') {
            $this->db->where("id", $arrParam['id']);
            
            foreach ($arrParam['fields'] as $key => $val)
                $data[$key] = $val;
            
            $this->db->update($this->_table, $data);
            
            $this->db->flush_cache();
            
            $lastId = $arrParam['id'];
        }
        
        return $lastId;
    }

    public function count_item($arrParams = null, $options = null)
    {
        if ($options == null) {
            if ($arrParams['employeed_id'] != 0) {
                $id_admin = !empty($arrParams['employeed_id']) ? $arrParams['employeed_id'] : $this->_id_admin;
            } else {
                $id_admin = 0;
            }
            $tasks = $this->db->dbprefix($this->_table);
            $task_user_relations = $this->db->dbprefix(task_user_relations);
            
            // clause
            $where = $this->get_where_from_filter($arrParams,'cong_viec_con');
            if (! empty($where)) {
                $where = implode(' AND ', $where);
                $where = 'AND ' . $where;
            } else
            $where = '';
            

            $where_bonus="";

            if (! empty($arrParams['person_id'])) {
                $person_id = $arrParams['person_id'];
                $where_bonus = 'AND c.id IN ('.$person_id.') ';
            }
            if ($id_admin != 0) {
                $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
                FROM  $tasks AS t
                LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                INNER JOIN
                (
                SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                FROM $tasks
                INNER JOIN $task_user_relations
                ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
                ) AS tmp
                ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                WHERE t.parent != 0 $where $where_bonus";
            } else {
                $task_permissions = $this->_task_permission;
                $view_scope = '';
                if (in_array('view_scope_location', $task_permissions)) {
                    $view_scope = 'location';
                }
                if (in_array('view_scope_all', $task_permissions)) {
                    $view_scope = 'all';
                }
                switch ($view_scope) {
                    case 'all':
                        $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
                        FROM  $tasks AS t
                        LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                        LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                        LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                        LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                        INNER JOIN
                        (
                        SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                        FROM $tasks
                        INNER JOIN $task_user_relations
                        ON $tasks.id = $task_user_relations.task_id
                        ) AS tmp
                        ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                        WHERE t.parent != 0 $where $where_bonus";
                        break;
                    case 'location':
                        $location_id = $this->Employee->get_logged_in_employee_current_location_id();
                        $sql = 'SELECT `employee_id` FROM ' . $this->db->dbprefix('employees_locations') . ' WHERE `location_id` = ' . $location_id;
                        $query = $this->db->query($sql);
                        $result = $query->result();
                        $query->free_result();
                        if (!empty($result)) {
                            $emp_ids = [];
                            foreach ($result as $row) {
                                $emp_ids[$row->employee_id] = $row->employee_id;
                            }
                            $emp_ids = implode(',', $emp_ids);
                        } else {
                            $emp_ids = '';
                        }
                        $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
                        FROM  $tasks AS t
                        LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                        LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                        LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                        LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                        INNER JOIN
                        (
                        SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                        FROM $tasks
                        INNER JOIN $task_user_relations
                        ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id IN ($emp_ids)
                        ) AS tmp
                        ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                        WHERE t.parent != 0 $where $where_bonus";
                        break;
                    default:
                        $sql = "SELECT COUNT(DISTINCT t.id) AS totalItem
                        FROM  $tasks AS t
                        LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                        LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                        LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                        LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                        INNER JOIN
                        (
                        SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                        FROM $tasks
                        INNER JOIN $task_user_relations
                        ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
                        ) AS tmp
                        ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                        WHERE t.parent != 0 $where $where_bonus";
                        break;
                }
            }
            $query = $this->db->query($sql);
            $result = $query->row_array();
            $result = $result['totalItem'];
        }
        return $result;
    }

    public function list_item($arrParams = null, $options = null)
    {
        // echo "<pre>";
        // var_dump($arrParams);die();
        // Thằng nào viết cái này ngu vl
        $paginator = $arrParams['paginator'];
        if ($options == null) {
            $flagLevel = false;
            if ($arrParams['employeed_id'] != 0) {
                $id_admin = !empty($arrParams['employeed_id']) ? $arrParams['employeed_id'] : $this->_id_admin;
            } else {
                $id_admin = 0;
            }
            $tasks = $this->db->dbprefix($this->_table);
            $task_user_relations = $this->db->dbprefix(task_user_relations);
            
            // clause
            $where = $this->get_where_from_filter($arrParams,'cong_viec_con');
            if (! empty($where)) {
                $where = implode(' AND ', $where);
                $where = 'AND ' . $where;

            } else
            $where = '';
                     // var_dump($where);die();
                // order by
            if (! empty($arrParams['col']) && ! empty($arrParams['order'])) {
                $col = $this->_fields[$arrParams['col']];
                if($col ==null)
                    $col='date_start';
                // var_dump($col);die();
                $order = $arrParams['order'];
                
                $order_by = $col . ' ' . $order;
                if ($col != 't.date_start')
                    // $order_by = $order_by . ',t.date_start ASC';



                    if ($col == 't.lft' && $order == 'ASC')
                        $flagLevel = true;
                } else {
                    $flagLevel = true;
                    $order_by = 't.lft ASC, t.date_start ASC';
                }

                $page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];

                $where_bonus="";

                if (! empty($arrParams['person_id'])) {
                    $person_id = $arrParams['person_id'];
                    $where_bonus = 'AND c.id IN ('.$person_id.') ';
                }
            $limit = $paginator['per_page'];
            $offset = ($page - 1) * $paginator['per_page'];
            if ($id_admin != 0) {
                $sql = "SELECT DISTINCT t.id, t.name,t.customer_ids,p.last_name,p.person_id, t.project_id, t.parent, t.progress,t.trangthai, t.prioty, DATE_FORMAT(t.date_start, '%d-%m-%Y') as start_date, DATE_FORMAT(t.date_end, '%d-%m-%Y') as end_date, DATE_FORMAT(t.date_finish, '%d-%m-%Y') as finish_date, t.lft, t.rgt, t.project_id,tmp_tasks.name_task_parent
                FROM  $tasks AS t
                LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                INNER JOIN
                (
                SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                FROM $tasks
                INNER JOIN $task_user_relations
                ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
                ) AS tmp
                ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                LEFT JOIN (
                SELECT tas.id, tas.name as name_task_parent FROM phppos_tasks as tas where parent =0 
                ) as tmp_tasks
                ON tmp_tasks.id = t.project_id            
                WHERE t.parent != 0
                $where $where_bonus ORDER BY $order_by
                LIMIT $offset, $limit";
            } else {
                $task_permissions = $this->_task_permission;
                $view_scope = '';
                if (in_array('view_scope_location', $task_permissions)) {
                    $view_scope = 'location';
                }
                if (in_array('view_scope_all', $task_permissions)) {
                    $view_scope = 'all';
                }
                switch ($view_scope) {
                    case 'all':
                        $sql = "SELECT DISTINCT t.id, t.name,t.customer_ids,p.last_name,p.person_id, t.project_id, t.parent, t.progress,t.trangthai, t.prioty, DATE_FORMAT(t.date_start, '%d-%m-%Y') as start_date, DATE_FORMAT(t.date_end, '%d-%m-%Y') as end_date, DATE_FORMAT(t.date_finish, '%d-%m-%Y') as finish_date, t.lft, t.rgt, t.project_id,tmp_tasks.name_task_parent
                        FROM  $tasks AS t
                        LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                        LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                        LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                        LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                        INNER JOIN
                        (
                        SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                        FROM $tasks
                        INNER JOIN $task_user_relations
                        ON $tasks.id = $task_user_relations.task_id
                        ) AS tmp
                        ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                        LEFT JOIN (
                        SELECT tas.id, tas.name as name_task_parent FROM phppos_tasks as tas where parent =0 
                        ) as tmp_tasks
                        ON tmp_tasks.id = t.project_id            
                        WHERE t.parent != 0
                        $where $where_bonus ORDER BY $order_by
                        LIMIT $offset, $limit";
                        break;
                    case 'location':
                        $location_id = $this->Employee->get_logged_in_employee_current_location_id();
                        $sql = 'SELECT `employee_id` FROM ' . $this->db->dbprefix('employees_locations') . ' WHERE `location_id` = ' . $location_id;
                        $query = $this->db->query($sql);
                        $result = $query->result();
                        $query->free_result();
                        if (!empty($result)) {
                            $emp_ids = [];
                            foreach ($result as $row) {
                                $emp_ids[$row->employee_id] = $row->employee_id;
                            }
                            $emp_ids = implode(',', $emp_ids);
                        } else {
                            $emp_ids = '';
                        }
                        $sql = "SELECT DISTINCT t.id, t.name,t.customer_ids,p.last_name,p.person_id, t.project_id, t.parent, t.progress,t.trangthai, t.prioty, DATE_FORMAT(t.date_start, '%d-%m-%Y') as start_date, DATE_FORMAT(t.date_end, '%d-%m-%Y') as end_date, DATE_FORMAT(t.date_finish, '%d-%m-%Y') as finish_date, t.lft, t.rgt, t.project_id,tmp_tasks.name_task_parent
                        FROM  $tasks AS t
                        LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                        LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                        LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                        LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                        INNER JOIN
                        (
                        SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                        FROM $tasks
                        INNER JOIN $task_user_relations
                        ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id IN ($emp_ids)
                        ) AS tmp
                        ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                        LEFT JOIN (
                        SELECT tas.id, tas.name as name_task_parent FROM phppos_tasks as tas where parent =0 
                        ) as tmp_tasks
                        ON tmp_tasks.id = t.project_id            
                        WHERE t.parent != 0
                        $where $where_bonus ORDER BY $order_by
                        LIMIT $offset, $limit";
                        break;
                    default:
                        $sql = "SELECT DISTINCT t.id, t.name,t.customer_ids,p.last_name,p.person_id, t.project_id, t.parent, t.progress,t.trangthai, t.prioty, DATE_FORMAT(t.date_start, '%d-%m-%Y') as start_date, DATE_FORMAT(t.date_end, '%d-%m-%Y') as end_date, DATE_FORMAT(t.date_finish, '%d-%m-%Y') as finish_date, t.lft, t.rgt, t.project_id,tmp_tasks.name_task_parent
                        FROM  $tasks AS t
                        LEFT JOIN phppos_sales as s ON t.project_id  = s.task_id
                        LEFT JOIN phppos_customers as c ON c.person_id = s.customer_id
                        LEFT JOIN phppos_people as p ON s.customer_id = p.person_id
                        LEFT JOIN phppos_tasks as tt ON t.project_id = tt.id
                        INNER JOIN
                        (
                        SELECT $tasks.id, $tasks.lft, $tasks.rgt, $tasks.project_id, $tasks.level
                        FROM $tasks
                        INNER JOIN $task_user_relations
                        ON $tasks.id = $task_user_relations.task_id AND $task_user_relations.user_id = $id_admin
                        ) AS tmp
                        ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt
                        LEFT JOIN (
                        SELECT tas.id, tas.name as name_task_parent FROM phppos_tasks as tas where parent =0 
                        ) as tmp_tasks
                        ON tmp_tasks.id = t.project_id            
                        WHERE t.parent != 0
                        $where $where_bonus ORDER BY $order_by
                        LIMIT $offset, $limit";
                        break;
                }
            }

            $query = $this->db->query($sql);
            $result = $query->result_array();


            // echo $this->db->last_query();die();

                if (! empty($result)) {

                    $data_task_parent = $this->db->query("SELECT * FROM phppos_tasks where parent =0")->result_array();

                // echo "<pre>"; print_r($result); die();
                    $task_implements = $project_ids = $task_joins = array();
                    foreach ($result as $val)
                        $task_ids[] = $val['id'];

                    $resultTmp = $this->getUsersRelation($task_ids);
                    $task_implements_origin = $this->task_implements($resultTmp);

                    $task_joins_origin = $this->task_joins($resultTmp);
                    $sort_lft_items = $this->sort_lft_items($result);

                    foreach ($sort_lft_items as $val) {
                        $task_id = $val['id'];
                        $origin = (isset($task_implements_origin[$task_id])) ? $task_implements_origin[$task_id] : array();
                        if (! in_array($val['project_id'], $project_ids))
                            $project_ids[] = $val['project_id'];


                        $tmpj = $this->get_relation_by_task_id($task_id);
                    // var_dump($tmpj);die();
                        if(!empty($tmpj))
                        {
                            $task_joins[$task_id] =$tmpj;
                        }
                        $parent = $val['parent'];

                        if (! isset($task_implements[$parent])) {
                            $parent_item = $this->getItem(array(
                                'id' => $parent
                            ), array(
                                'task' => 'information'
                            ));
                            $task_ids = $this->getIds(array(
                                'lft' => $parent_item['lft'],
                                'rgt' => $parent_item['rgt'],
                                'project_id' => $parent_item['project_id']
                            ), array(
                                'task' => 'up-branch'
                            ));
                            $tmp = $this->getUsersRelation($task_ids, 'implement');
                            $task_implements[$task_id] = array_merge($tmp, $origin);

                        } else {
                            $task_implements[$task_id] = array_merge($task_implements[$parent], $origin);
                        }

                        $task_implements[$task_id] = array_unique($task_implements[$task_id]);
                    }

                // get project information
                    $project_informations = $this->getItems(array(
                        'cid' => $project_ids
                    ), array(
                        'task' => 'public-info'
                    ));

                    $user_ids = array();
                    if (! empty($task_implements)) {
                        foreach ($task_implements as $val)
                            $user_ids = array_merge($user_ids, $val);
                    }

                // get users list by ids
                    if (! empty($user_ids)) {
                        $userTable = $this->model_load_model('TaskUser');
                        $usersInfo = $userTable->getItems(array(
                            'user_ids' => $user_ids
                        ));
                    }

                    foreach ($result as &$val) {
                        $task_id = $val['id'];
                        $val['project_name'] = $project_informations[$val['project_id']]['name'];
                        $val['implement_ids'] = $task_implements[$val['id']];
                        $val['implement'] = '';
                        $val['join_ids'] = $task_joins[$val['id']];
                        $val['join'] = '';
                        if (! empty($val['implement_ids'])) {
                            foreach ($val['implement_ids'] as $user_id) {
                            // var_dump($task_implements_origin[$task_id]);die();
                                if(!empty($task_implements_origin[$task_id]))
                                {
                                    if (in_array($user_id, $task_implements_origin[$task_id]))
                                        $val['implement'][] = '<strong>' . $usersInfo[$user_id]['username'] . '</strong>';
                                    else
                                        $val['implement'][] = $usersInfo[$user_id]['username'];
                                }
                            }

                            if(!empty($task_implements_origin[$task_id]))   
                                $val['implement'] = implode(', ', $val['implement']);
                        }

                    #JOIN
                        if (! empty($val['join_ids'])) {
                            foreach ($val['join_ids'] as $user_id) {                                                         

                                $val['join'][] = $this->TaskUser->get_username_by_id($user_id);
                                // echo $this->db->last_query();die();
                                // echo "<pre>";
                                // print_r($val['join']);die();
                            }

                            $val['join'] = implode(', ', $val['join']);
                        }

                    if ($val['trangthai'] == 0 || $val['trangthai'] == 1) { // chưa thực hiện + đang thực hiện
                        $now = date('Y-m-d', strtotime(date("d-m-Y")));
                        $date_end = date('Y-m-d', strtotime($val['end_date']));
                        
                        $datediff = strtotime($now) - strtotime($date_end);
                        $duration = datediff(date("d-m-Y"), $val['end_date']);
                        // var_dump(datediff(date("d-m-Y"), $val['end_date']));die();
                        if ($datediff <= 0) {
                            $val['note'] = 'Còn ' . $duration;
                            $val['p_color'] = '#3f76a5';
                            $val['color'] = '#4388c2';
                        } else {
                            $val['note'] = 'Quá ' . $duration;
                            $val['p_color'] = '#aa142f';
                            $val['color'] = '#c90d2f';
                        }
                    } elseif ($val['trangthai'] == 2) { // hoàn thành
                        $end_date = date('Y-m-d', strtotime($val['end_date']));
                        $finish_date = date('Y-m-d', strtotime($val['finish_date']));
                        
                        $datediff = strtotime($end_date) - strtotime($finish_date);
                        $duration = datediff($val['end_date'], $val['finish_date']);
                        
                        if ($datediff < 0) {
                            $val['note'] = 'Trễ ' . $duration;
                            $val['p_color'] = '#4a6242';
                            $val['color'] = '#516e47';
                        } elseif ($datediff > 0) {
                            $val['p_color'] = '#91d20a';
                            $val['color'] = '#a9fa01';
                            $val['note'] = 'Sớm ' . $duration;
                        } else {
                            $val['p_color'] = '#18c33e';
                            $val['color'] = '#12e841';
                        }
                    } elseif ($val['trangthai'] == 3) {
                        $val['p_color'] = '#bdb720';
                        $val['color'] = '#e0d91c';
                    } elseif ($val['trangthai'] == 4) {
                        $val['p_color'] = '#303023';
                        $val['color'] = '#303020';
                    }
                    
                    $val['prioty'] = $this->_prioty[$val['prioty']];
                    $val['trangthai'] = $this->_trangthai[$val['trangthai']];
                }
                
                if ($flagLevel == true) {
                    $resultTmp = array();
                    foreach ($result as $value)
                        $resultTmp[$value['id']] = $value;
                    
                    $result = $resultTmp;
                    foreach ($result as &$val) {
                        if (! isset($result[$val['parent']]))
                            $val['space'] = '';
                        else
                            $val['space'] = $val['space'] . '&nbsp&nbsp&nbsp';
                    }
                    
                    $resultTmp = array();
                    foreach ($result as $value) {
                        $resultTmp[] = $value;
                    }
                    
                    $result = $resultTmp;
                }
            }
        }
        // echo "<pre>";
        // print_r($result);die();
        return $result;
    }

    public function listItem($arrParams = null, $options = null)
    {   
        $paginator = $arrParams['paginator'];
        $this->load->model('Employee');
        $location_id = $this->Employee->get_logged_in_employee_current_location_id();
        // Begin view permissions
        $task_permissions = $this->_task_permission;
        $view_all_permissions = ['view_scope_all'];
        $has_view_all_permission = false;
        foreach ($view_all_permissions as $permission) {
            if (in_array($permission, $task_permissions)) {
                $has_view_all_permission = true;
            }
        }
   
        if ($options == null) {
            $flagAll = $this->checkAllPermission();

            // no full permission
            if ($flagAll == false) {
                $project_ids = $this->getProjectRelation();
                if (empty($project_ids))
                    $project_ids = array(
                        - 1
                    );
            }

            // View Projects In Same Location
            if (in_array('view_scope_location', $task_permissions)) {
                $same_location_project_ids = $this->get_same_location_project_ids($_SESSION['person_id'], $location_id);
                if (empty($project_ids)){
                    $project_ids = [];
                }
                $project_ids = array_merge($project_ids, $same_location_project_ids);
            }

            // View All Projects
            if (in_array('view_scope_all', $task_permissions)) {
                $project_ids = [];
            }
            // End view permissions

            $where = $this->get_where_from_filter($arrParams);
            $this->db->select("t.id")
            ->from($this->_table . ' AS t')
            ->where('t.parent = 0')
            ->order_by("t.date_start", 'ASC')
            ->order_by('t.sort', 'ASC');

            if (! empty($project_ids)) {
                $this->db->where('project_id IN (' . implode(', ', $project_ids) . ')');
            }

            if (! empty($where)) {
                foreach ($where as $wh)
                    $this->db->where($wh);
            }

            $page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
            $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);

            $query = $this->db->get();

            $project_ids = array();

            $resultTmp = $query->result_array();

            $this->db->flush_cache();

            if (! empty($resultTmp)) {
                foreach ($resultTmp as $val)
                    $project_ids[] = $val['id'];
            }

            if (! empty($project_ids)) {
                // task list
                $task_ids = array();
                $project_ids = array_unique($project_ids);
                if (! empty($project_ids)) {
                    $this->db->select("DATE_FORMAT(date_start, '%d-%m-%Y') as start_date", FALSE);
                    $this->db->select("DATE_FORMAT(date_end, '%d-%m-%Y') as end_date", FALSE);
                    $this->db->select("DATE_FORMAT(date_finish, '%d-%m-%Y') as finish_date", FALSE);
                    $this->db->select("TIMESTAMPDIFF(DAY, date_start, date_end) AS days", FALSE);
                    $this->db->select("id, name as text, name, percent, progress, level, parent, project_id, lft, rgt, created, pheduyet, color, prioty, trangthai")
                    ->from($this->_table)
                    ->where('project_id IN (' . implode(', ', $project_ids) . ')')
                    ->order_by("lft", 'ASC');

                    $query = $this->db->get();

                    $res_tmp = $query->result_array();

                    $this->db->flush_cache();

                    $task_list_tmp = array();

                    if (! empty($res_tmp)) {
                        foreach ($res_tmp as $val) {
                            $resultTmp[$val['id']] = $val;
                        }

                        $stt = 1;

                        foreach ($project_ids as $project_id) {
                            $taskTmp = $resultTmp[$project_id];
                            $taskTmp['order'] = $stt;

                            $task_list_tmp[$project_id] = $taskTmp;
                            $task_ids[] = $project_id;
                            $stt = $stt + 1;

                            unset($resultTmp[$project_id]);
                            if (! empty($resultTmp)) {
                                foreach ($resultTmp as $val) {
                                    if ($val['project_id'] == $project_id) {
                                        $taskTmp = $resultTmp[$val['id']];
                                        $taskTmp['order'] = $stt;

                                        $task_list_tmp[$val['id']] = $taskTmp;
                                        $task_ids[] = $val['id'];

                                        unset($resultTmp[$val['id']]);
                                        $stt = $stt + 1;
                                    }
                                }
                            }
                        }
                    }
                } else
                $task_list_tmp = array();
            }

            $task_list = array();
            if (! empty($task_list_tmp)) {

                $implement_ids = $create_task_ids = $is_xem_ids = $task_implements = array();
                $resultTmp = $this->getUsersRelation($task_ids);
                $task_implements_origin = $this->task_implements($resultTmp);
                $sort_lft_items = $this->sort_lft_items($res_tmp);

                $task_implements[0] = array();

                foreach ($sort_lft_items as $val) {
                    $task_id = $val['id'];
                    $origin = (isset($task_implements_origin[$task_id])) ? $task_implements_origin[$task_id] : array();

                    $parent = $val['parent'];

                    if (! isset($task_implements[$parent])) {
                        $parent_item = $this->getItem(array(
                            'id' => $parent
                        ), array(
                            'task' => 'information'
                        ));
                        $task_ids = $this->getIds(array(
                            'lft' => $parent_item['lft'],
                            'rgt' => $parent_item['rgt'],
                            'project_id' => $parent_item['project_id']
                        ), array(
                            'task' => 'up-branch'
                        ));
                        $tmp = $this->getUsersRelation($task_ids, 'implement');
                        $task_implements[$task_id] = array_merge($tmp, $origin);
                    } else {
                        $task_implements[$task_id] = array_merge($task_implements[$parent], $origin);
                    }

                    $task_implements[$task_id] = array_unique($task_implements[$task_id]);
                }

                if (! empty($resultTmp)) {
                    foreach ($resultTmp as $val) {
                        if ($val['is_implement'] == 1) {
                            $implement_ids[] = $val['task_id'];
                        }

                        if ($val['is_create_task'] == 1)
                            $create_task_ids[] = $val['task_id'];

                        if ($val['is_xem'] == 1)
                            $is_xem_ids[] = $val['task_id'];

                        if ($val['is_join'] == 1)
                            $is_join_ids[] = $val['task_id'];

                        $user_ids[] = $val['user_id'];
                    }
                }

                // user list based on ids
                if (! empty($user_ids)) {
                    $userTable = $this->model_load_model('TaskUser');
                    $usersInfo = $userTable->getItems(array(
                        'user_ids' => $user_ids
                    ));
                }

                foreach ($project_ids as $project_id) {
                    $task_list[$project_id] = $task_list_tmp[$project_id];
                    unset($task_list_tmp[$project_id]);

                    foreach ($task_list_tmp as $val) {
                        if ($val['project_id'] == $project_id) {
                            $task_list[$val['id']] = $val;
                            unset($task_list_tmp[$val['id']]);
                        }
                    }
                }

                foreach ($task_list as &$val) {
                    $val['open'] = false;
                    $val['text'] = $val['text'] . ' (' . round($val['progress'],2) . '%)';
                    if ($val['pheduyet'] == - 1)
                        $val['text'] = $val['text'] . ' - Chờ phê duyệt';
                    elseif ($val['pheduyet'] == 0)
                        $val['text'] = $val['text'] . ' - Không phê duyệt';

                    $val['implement_ids'] = $task_implements[$val['id']];
                    $val['implement'] = '';
                    if (! empty($val['implement_ids'])) {
                        foreach ($val['implement_ids'] as $user_id) {
                            if (! empty($task_implements_origin[$val['id']]) && in_array($user_id, $task_implements_origin[$val['id']]))
                                $val['implement'][] = '<strong>' . $usersInfo[$user_id]['username'] . '</strong>';
                            else
                                $val['implement'][] = $usersInfo[$user_id]['username'];
                        }

                        $val['implement'] = implode(', ', $val['implement']);
                    }


                    #Bắt đầu Thêm vào người phụ tham gian


                    if (! empty($val['is_join_ids'])) {
                        foreach ($val['is_join_ids'] as $user_id)
                        {
                            $val['join'][] = $usersInfo[$user_id]['username'];
                        }

                        $val['join'] = implode(', ', $val['join']);
                    }


                    #Kết thúc




                    $val['progress'] = $val['progress'] / 100;
                    // tooltip information
                    $tyle = '';
                    if ($val['parent'] > 0)
                        $tyle = ($val['percent']) . '% <strong> ' . $task_list[$val['parent']]['name'] . '</strong>';

                    $date_time = $val['start_date'] . ' đến ' . $val['end_date'];
                    $date_finish = '';
                    if ($val['trangthai'] == 0 || $val['trangthai'] == 1) { // chưa thực hiên + đang thực hiện
                        $now = date('Y-m-d', strtotime(date("d-m-Y")));
                        $date_end = date('Y-m-d H:i:s', strtotime($val['end_date']));

                        $datediff = strtotime($now) - strtotime($date_end);
                        $duration = datediff(date("d-m-Y"), $val['end_date']);

                        if ($datediff <= 0) {
                            $val['color'] = '#4388c2';
                            $date_time = $date_time . '<br /><strong>Còn:</strong> ' . $duration;
                        } else {
                            $date_time = $date_time . '<br /><strong>Quá:</strong> ' . $duration;
                            $val['color'] = '#c90d2f';
                        }
                    } elseif ($val['trangthai'] == 2) { // hoàn thành
                        $end_date = date('Y-m-d', strtotime($val['end_date']));
                        $finish_date = date('Y-m-d', strtotime($val['finish_date']));

                        $datediff = strtotime($end_date) - strtotime($finish_date);
                        $duration = dateDiff($val['end_date'], $val['finish_date']);

                        if ($datediff < 0) {
                            $date_finish = $val['finish_date'] . '<br /><strong>Trễ:</strong> ' . $duration;
                            $val['color'] = '#516e47';
                        } elseif ($datediff > 0) {
                            $val['color'] = '#a9fa01';
                            $date_finish = $val['finish_date'] . '<br /><strong>Sớm:</strong> ' . $duration;
                        } else {
                            $val['color'] = '#12e841';
                            $date_finish = $val['finish_date'];
                        }
                    } elseif ($val['trangthai'] == 3) {
                        $val['color'] = '#e0d91c';
                        $date_time = $date_time . ' (Đóng/ Dừng)';
                    } elseif ($val['trangthai'] == 4) {
                        $val['color'] = '#303020';
                        $date_time = $date_time . ' (Không thực hiện)';
                    }

                    $tooltip = array();
                    if (! empty($tyle))
                        $tooltip[] = '<strong>Tỷ lệ: </strong>' . $tyle;
                    $tooltip[] = '<strong>Thời gian: </strong>' . $date_time;

                    if (! empty($date_finish))
                        $tooltip[] = '<strong>Hoàn thành: </strong>' . $date_finish;

                    $tooltip[] = '<strong>Phụ trách</strong>: ' . $val['implement'];
                    $val['tooltip'] = implode('<br />', $tooltip);
                }
            }

            if (! empty($task_list)) {
                // allow task
                $allow_tasks = $deny_task = $drag_task = $click_task = array();
                if ($flagAll == true) {
                    $allow_tasks = $drag_task = $click_task = array_keys($task_list);
                } else {
                    // cập nhật project
                    if (in_array('update_project', $this->_task_permission)) {
                        $allow_tasks = $drag_task = $click_task = $project_ids;
                    } else
                    $deny_task[] = "0";

                    // quyền cập nhật tất cả các task
                    if (in_array('update_all_task', $this->_task_permission)) {
                        $tmp = array();
                        foreach ($resultTmp as $val) {
                            if ($val['parent'] != 0)
                                $tmp[] = $val['id'];
                        }

                        $allow_tasks = array_merge($allow_tasks, $tmp);
                        $drag_task = array_merge($drag_task, $tmp);
                        $click_task = array_merge($click_task, $tmp);
                    }

                    // quyền cập nhật trên nhánh
                    if (! empty($implement_ids)) {
                        foreach ($task_list as $task_id => $task_detail) {
                            foreach ($implement_ids as $t_id) {
                                if ($task_detail['lft'] >= $task_list[$t_id]['lft'] && $task_detail['rgt'] <= $task_list[$t_id]['rgt']) {
                                    if (in_array('update_brand_task', $this->_task_permission))
                                        $allow_tasks[] = $task_detail['id'];

                                    $click_task[] = $task_detail['id'];
                                }
                            }
                        }
                    }

                    // create_task
                    if (! empty($create_task_ids)) {
                        foreach ($task_list as $task_id => $task_detail) {
                            foreach ($create_task_ids as $t_id) {
                                if ($task_detail['lft'] >= $task_list[$t_id]['lft'] && $task_detail['rgt'] <= $task_list[$t_id]['rgt']) {
                                    $allow_tasks[] = $task_detail['id'];
                                }
                            }
                        }
                    }

                    // is xem
                    if (! empty($is_xem_ids)) {
                        foreach ($task_list as $task_id => $task_detail) {
                            foreach ($is_xem_ids as $t_id) {
                                if ($task_detail['lft'] >= $task_list[$t_id]['lft'] && $task_detail['rgt'] <= $task_list[$t_id]['rgt']) {
                                    $click_task[] = $task_detail['id'];
                                }
                            }
                        }
                    }

                    foreach ($task_list as $value) {
                        if (! in_array($value['id'], $allow_tasks))
                            $deny_task[] = $value['id'];
                    }

                    $drag_task = array_unique($drag_task);
                }

                foreach ($task_list as &$val) {
                    if (! in_array($val['id'], $click_task))
                        $val['color'] = '#cccccc';
                }
                $task_list = array_merge($task_list, array());

                $result = array(
                    'ketqua' => $task_list,
                    'deny' => $deny_task,
                    'drag_task' => $drag_task
                );
            } else {
                $deny_task = array();
                if (! in_array('update_project', $this->_task_permission))
                    $deny_task[] = "0";

                $result = array(
                    'ketqua' => array(),
                    'deny' => $deny_task,
                    'drag_task' => array()
                );
            }
        } elseif ($options['task'] == 'public-list') {
            $prioty_arr = array(
                'Rất cao',
                'Cao',
                'Trung bình',
                'Thấp',
                'Rất thấp'
            );

            $this->db->select("DATE_FORMAT(t.modified, '%d-%m-%Y') as modified", FALSE);
            $this->db->select("t.*, e.username")
            ->from($this->_table . ' AS t')
            ->join('employees AS e', 'e.id = t.modified_by', 'left')
            ->where('t.parent = 0');

            $page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
            $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);

            if (! empty($arrParams['keywords'])) {
                $this->db->where('t.name LIKE \'%' . $arrParams['keywords'] . '%\'');
            }

            if (! empty($arrParams['col']) && ! empty($arrParams['order'])) {
                $col = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order)->order_by('t.sort', 'ASC');
            } else {
                $this->db->order_by('t.prioty', 'ASC')->order_by('t.sort', 'ASC');
            }

            $query = $this->db->get();

            $result = $query->result_array();
            if (! empty($result)) {
                foreach ($result as &$val)
                    $val['prioty'] = $this->_prioty[$val['prioty']];
            }
            $this->db->flush_cache();
        } elseif ($options['task'] == 'grid-project') {
            $user_ids = array();
            $flagAll = $this->checkAllPermission();
            if ($flagAll == false) {
                $project_ids = $this->getProjectRelation();
                if (empty($project_ids))
                    $project_ids = array(
                        - 1
                    );
            }
            $task_permissions = $this->_task_permission;

            // View Projects In Same Location
            if (in_array('view_scope_location', $task_permissions)) {
                $same_location_project_ids = $this->get_same_location_project_ids($_SESSION['person_id'], $location_id);
                if (empty($project_ids)){
                    $project_ids = [];
                }
                $project_ids = array_merge($project_ids, $same_location_project_ids);
                if(empty($project_ids))
                    return [];
            }

            // View All Projects
            if (in_array('view_scope_all', $task_permissions)) {
                $project_ids = [];
            }
            # Get number task
            if($options['get_project_ids']==1){
                return $project_ids;
            }
            $where = $this->get_where_from_filter($arrParams);

            $subqery = $this->db->select('si.sale_id,GROUP_CONCAT(si.item_name) as sale, s.task_id')
            ->from('phppos_sales as s')
            ->join('phppos_sales_items as si', 's.sale_id = si.sale_id','left')
            ->group_by('si.sale_id')
            ->get_compiled_select();

            $this->db->select('s.sale, s.sale_id, c.name as contract, c.id as contract_id');
            $this->db->select("DATE_FORMAT(t.date_start, '%d-%m-%Y') as start_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_end, '%d-%m-%Y') as end_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_pheduyet, '%d-%m-%Y') as finish_date", FALSE);
            $this->db->select("t.id, t.name, t.percent, t.progress, t.level, t.parent, t.project_id, t.lft, t.rgt, t.created, t.pheduyet, t.color, t.prioty, t.trangthai")
            ->from($this->_table . ' AS t');
            $this->db->join("($subqery) as s", 't.id = s.task_id', 'left');
            $this->db->join('phppos_contract as c', 'c.sale_id = s.sale_id', 'left')
            ->where('t.parent = 0');
            if (! empty($arrParams['col']) && ! empty($arrParams['order'])) {
                $col = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);
            } else {
                // $this->db->order_by("t.prioty", 'ASC')->order_by('t.sort', 'ASC');
                $this->db->order_by("t.date_start", 'DESC');
            }

            if (!empty($project_ids)) {
                $this->db->where_in('t.project_id', $project_ids);
            }

            if (! empty($where)) {
                foreach ($where as $wh)
                    $this->db->where($wh);
            }

            $page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
            $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);

            $query = $this->db->get();
            // echo $this->db->last_query(); exit();

            $result = $query->result_array();
            if (! empty($result)) {
                foreach ($result as $val)
                    $task_ids[] = $val['id'];

                // get all related users
                $resultTmp = $this->getUsersRelation($task_ids);

                $task_implements = $user_ids = array();
                $task_join = array();
                if (! empty($resultTmp)) {
                    foreach ($resultTmp as $val) {
                        if ($val['is_implement'] == 1) {
                            $implement_ids[] = $val['task_id'];
                            $task_implements[$val['task_id']][] = $val['user_id'];
                        }

                        $user_ids[] = $val['user_id'];
                    }



                    #TASK JOIN
                    foreach ($resultTmp as $value) {
                        if ($value['is_join'] == 1) {
                            $join_ids[] = $value['task_id'];
                            $task_joins[$value['task_id']][] = $value['user_id'];
                        }

                    }


                }


                // get users list by ids
                if (! empty($user_ids)) {
                    $userTable = $this->model_load_model('TaskUser');
                    $usersInfo = $userTable->getItems(array(
                        'user_ids' => $user_ids
                    ));
                }

                foreach ($result as &$val) {
                    $val['implement_ids'] = $task_implements[$val['id']];
                    $val['implement'] = '';
                    $val['join_ids'] = $task_joins[$val['id']];
                    $val['join'] = '';
                    if (! empty($val['implement_ids'])) {
                        foreach ($val['implement_ids'] as $user_id) {

                            $val['implement'][] = '<strong>' . $usersInfo[$user_id]['username'] . '</strong>';
                        }

                        $val['implement'] = implode(', ', $val['implement']);
                    }
                    if (! empty($val['join_ids'])) {
                        foreach ($val['join_ids'] as $user_id) {

                            $val['join'][] = '<strong>' . $usersInfo[$user_id]['username'] . '</strong>';
                        }

                        $val['join'] = implode(', ', $val['join']);
                    }

                    if ($val['trangthai'] == 0 || $val['trangthai'] == 1) { // chưa thực hiện + đang thực hiện
                        $now = date('Y-m-d H:i:s', strtotime(date("d-m-Y H:i:s")));
                        $date_end = date('Y-m-d H:i:s', strtotime($val['end_date']));

                        $datediff = strtotime($now) - strtotime($date_end);
                        $duration = datediff(date("d-m-Y H:i:s"), $val['end_date']);

                        if ($datediff <= 0) {
                            $val['note'] = 'Còn ' . $duration;
                            $val['p_color'] = '#3f76a5';
                            $val['color'] = '#4388c2';
                        } else {
                            $val['note'] = 'Quá ' . $duration;
                            $val['p_color'] = '#aa142f';
                            $val['color'] = '#c90d2f';
                        }
                    } elseif ($val['trangthai'] == 2) { // hoàn thành
                        $end_date = date('Y-m-d', strtotime($val['end_date']));
                        $finish_date = date('Y-m-d', strtotime($val['finish_date']));

                        $datediff = strtotime($end_date) - strtotime($finish_date);
                        $duration = datediff($val['end_date'], $val['finish_date']);

                        if ($datediff < 0) {
                            $val['note'] = 'Trễ ' . $duration;
                            $val['p_color'] = '#4a6242';
                            $val['color'] = '#516e47';
                        } elseif ($datediff > 0) {
                            $val['p_color'] = '#91d20a';
                            $val['color'] = '#a9fa01';
                            $val['note'] = 'Sớm ' . $duration;
                        } else {
                            $val['p_color'] = '#18c33e';
                            $val['color'] = '#12e841';
                        }
                    } elseif ($val['trangthai'] == 3) {
                        $val['p_color'] = '#bdb720';
                        $val['color'] = '#e0d91c';
                    } elseif ($val['trangthai'] == 4) {
                        $val['p_color'] = '#303023';
                        $val['color'] = '#303020';
                    }

                    $val['prioty'] = $this->_prioty[$val['prioty']];
                    $val['trangthai'] = $this->_trangthai[$val['trangthai']];
                }
            } 
        } elseif ($options['task'] == 'task-by-project') {
            $task_ids = $this->getTasksIdsByProject(array(
                'id' => $arrParams['project_id']
            ));

            // filter
            $where = $this->get_where_from_filter($arrParams);

            $this->db->select("DATE_FORMAT(t.date_start, '%d-%m-%Y %H:%i') as start_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_end, '%d-%m-%Y %H:%i') as end_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y %H:%i') as finish_date", FALSE);
            $this->db->select("TIMESTAMPDIFF(DAY, date_start, date_end) AS days", FALSE);
            $this->db->select("t.id, t.name, t.percent, t.progress, t.level, t.parent, t.project_id, t.lft, t.rgt, t.created, t.pheduyet, t.color, t.prioty, t.trangthai")->from($this->_table . ' AS t');

            if ($task_ids == 'all') {
                $this->db->where('t.project_id', $arrParams['project_id']);
            } else {
                $this->db->where('t.id IN (' . implode(', ', $task_ids) . ')');
            }

            if (! empty($where)) {
                foreach ($where as $wh)
                    $this->db->where($wh);
            }

            $this->db->or_where('t.id', $arrParams['project_id']);

            $flagLevel = false;
            if (! empty($arrParams['col']) && ! empty($arrParams['order'])) {
                $col = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);
            } else {
                $flagLevel = true;
                $this->db->order_by("t.lft", 'ASC');
            }

            $query = $this->db->get();

            $resultTmp = $query->result_array();

            $result = array();

            if (! empty($resultTmp)) {
                $task_ids = array();
                foreach ($resultTmp as $val) {
                    $result[$val['id']] = $val;
                    $task_ids[] = $val['id'];
                }
            }

            if (! empty($result)) {
                $task_implements = array();
                $task_implements[0] = array();
                $resultTmp = $this->getUsersRelation($task_ids);
                $task_implements_origin = $this->task_implements($resultTmp);
                $sort_lft_items = $this->sort_lft_items($result);

                foreach ($sort_lft_items as $val) {
                    $task_id = $val['id'];
                    $origin = (isset($task_implements_origin[$task_id])) ? $task_implements_origin[$task_id] : array();
                    $parent = $val['parent'];

                    if (! isset($task_implements[$parent])) {
                        $parent_item = $this->getItem(array(
                            'id' => $parent
                        ), array(
                            'task' => 'information'
                        ));
                        $task_ids = $this->getIds(array(
                            'lft' => $parent_item['lft'],
                            'rgt' => $parent_item['rgt'],
                            'project_id' => $parent_item['project_id']
                        ), array(
                            'task' => 'up-branch'
                        ));
                        $tmp = $this->getUsersRelation($task_ids, 'implement');
                        $task_implements[$task_id] = array_merge($tmp, $origin);
                    } else {
                        $task_implements[$task_id] = array_merge($task_implements[$parent], $origin);
                    }

                    $task_implements[$task_id] = array_unique($task_implements[$task_id]);
                }

                $user_ids = array();
                if (! empty($task_implements)) {
                    foreach ($task_implements as $val)
                        $user_ids = array_merge($user_ids, $val);
                }

                // users list
                if (! empty($user_ids)) {
                    $userTable = $this->model_load_model('TaskUser');
                    $usersInfo = $userTable->getItems(array(
                        'user_ids' => $user_ids
                    ));
                }

                foreach ($result as &$val) {
                    $task_id = $val['id'];

                    $val['implement_ids'] = $task_implements[$val['id']];
                    $val['implement'] = '';
                    if (! empty($val['implement_ids'])) {
                        foreach ($val['implement_ids'] as $user_id) {
                            if (! empty($task_implements_origin[$task_id]) && in_array($user_id, $task_implements_origin[$task_id]))
                                $val['implement'][] = '<strong>' . $usersInfo[$user_id]['username'] . '</strong>';
                            else
                                $val['implement'][] = $usersInfo[$user_id]['username'];
                        }

                        $val['implement'] = implode(', ', $val['implement']);
                    }

                    if ($val['trangthai'] == 0 || $val['trangthai'] == 1) { // chưa thực hiện + đang thực hiện
                        $now = date('Y-m-d H:i:s', strtotime(date("d-m-Y H:i:s")));
                        $date_end = date('Y-m-d H:i:s', strtotime($val['end_date']));

                        $datediff = strtotime($now) - strtotime($date_end);
                        $duration = datediff(date("d-m-Y H:i:s"), $val['end_date']);

                        if ($datediff <= 0) {
                            $val['note'] = 'Còn ' . $duration;
                            $val['p_color'] = '#3f76a5';
                            $val['color'] = '#4388c2';
                        } else {
                            $val['note'] = 'Quá ' . $duration;
                            $val['p_color'] = '#aa142f';
                            $val['color'] = '#c90d2f';
                        }
                    } elseif ($val['trangthai'] == 2) { // hoàn thành
                        $end_date = date('Y-m-d', strtotime($val['end_date']));
                        $finish_date = date('Y-m-d', strtotime($val['finish_date']));

                        $datediff = strtotime($end_date) - strtotime($finish_date);
                        $duration = datediff($val['end_date'], $val['finish_date']);

                        if ($datediff < 0) {
                            $val['note'] = 'Trễ ' . $duration;
                            $val['p_color'] = '#4a6242';
                            $val['color'] = '#516e47';
                        } elseif ($datediff > 0) {
                            $val['p_color'] = '#91d20a';
                            $val['color'] = '#a9fa01';
                            $val['note'] = 'Sớm ' . $duration;
                        } else {
                            $val['p_color'] = '#18c33e';
                            $val['color'] = '#12e841';
                        }
                    } elseif ($val['trangthai'] == 3) {
                        $val['p_color'] = '#bdb720';
                        $val['color'] = '#e0d91c';
                    } elseif ($val['trangthai'] == 4) {
                        $val['p_color'] = '#303023';
                        $val['color'] = '#303020';
                    }

                    $val['prioty'] = $this->_prioty[$val['prioty']];
                    $val['trangthai'] = $this->_trangthai[$val['trangthai']];
                }

                $project = $result[$arrParams['project_id']];
                unset($result[$arrParams['project_id']]);
                $ketqua = $result;

                if ($flagLevel == true) {
                    foreach ($ketqua as &$val) {
                        if (! isset($ketqua[$val['parent']]))
                            $val['space'] = '';
                        else
                            $val['space'] = $val['space'] . '&nbsp&nbsp&nbsp';
                    }
                }

                $result = array(
                    'project' => $project,
                    'ketqua' => $ketqua
                );
            }
        }
        return $result;
    }

    public function getInfo($arrParam = null, $options = null)
    {
        if ($options['task'] == 'create-task') {
            $this->db->select("t.id, t.created")
            ->from($this->_table . ' as t')
            ->where('t.lft <= ' . $arrParam['lft'] . ' AND rgt >= ' . $arrParam['rgt'])
            ->where('t.project_id', $arrParam['project_id']);
            
            $query = $this->db->get();
            $resultTmp = $query->result_array();
            $this->db->flush_cache();
            
            if (! empty($resultTmp)) {
                $task_ids = $created = array();
                foreach ($resultTmp as $val) {
                    $task_ids[] = $val['id'];
                    $created[] = $val['created'];
                }
                
                $result['task_ids'] = $task_ids;
                $result['created'] = $created;
            } else
            $result = array();
        }
        
        return $result;
    }

    public function getIds($arrParam = null, $options = null)
    {
        if ($options['task'] == null) {
            $this->db->select("t.id")
            ->from($this->_table . ' as t')
            ->where('t.project_id', $arrParam['project_id']);
            
            if ($options['type'] == 'un-root')
                $this->db->where('t.lft > ' . $arrParam['lft'] . ' AND t.rgt < ' . $arrParam['rgt']);
            else
                $this->db->where('t.lft >= ' . $arrParam['lft'] . ' AND t.rgt <= ' . $arrParam['rgt']);
            
            $query = $this->db->get();
            $resultTmp = $query->result_array();
        } elseif ($options['task'] == 'up-branch') {
            $this->db->select("t.id")
            ->from($this->_table . ' as t')
            ->where('t.lft <= ' . $arrParam['lft'] . ' AND t.rgt >= ' . $arrParam['rgt'])
            ->where('t.project_id', $arrParam['project_id']);
            
            $query = $this->db->get();
            $resultTmp = $query->result_array();
        } elseif ($options['task'] == 'by-task-ids') {
            $phppos_tasks = $this->db->dbprefix($this->_table);
            $task_ids = implode(',', $arrParam['task_ids']);
            $sql = "SELECT t.id
            FROM $phppos_tasks AS t
            INNER JOIN (
            SELECT id, lft, rgt, project_id
            FROM $phppos_tasks
            WHERE id IN ($task_ids)
            ) AS tmp
            ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt";
            
            $query = $this->db->query($sql);
            $resultTmp = $query->result_array();
            $this->db->flush_cache();
        }
        
        $result = array();
        if (! empty($resultTmp)) {
            foreach ($resultTmp as $val)
                $result[] = $val['id'];
        }
        
        return $result;
    }

    public function getItems($arrParam = null, $options = null)
    {
        if ($options['task'] == 'public-info') {
            $this->db->select("t.*")
            ->from($this->_table . ' as t')
            ->where('t.id IN (' . implode(', ', $arrParam['cid']) . ')');
            
            $this->db->select("DATE_FORMAT(t.date_start, '%d/%m/%Y') as date_start", FALSE);
            $this->db->select("DATE_FORMAT(t.date_end, '%d/%m/%Y') as date_end", FALSE);
            
            $query = $this->db->get();
            
            $resultTmp = $query->result_array();
            $result = array();
            if (! empty($resultTmp)) {
                foreach ($resultTmp as $val)
                    $result[$val['id']][] = $val;
            }
            $this->db->flush_cache();
        } elseif ($options['task'] == 'by-project') {
            $this->db->select("DATE_FORMAT(t.date_start, '%d/%m/%Y') as date_start", FALSE);
            $this->db->select("DATE_FORMAT(t.date_end, '%d/%m/%Y') as date_end", FALSE);
            $this->db->select("t.*")
            ->from($this->_table . ' as t')
            ->where('t.project_id', $arrParam['project_id']);
            
            if (! empty($arrParam['level']))
                $this->db->where('t.level <= ' . $arrParam['level']);
            
            $query = $this->db->get();
            
            $resultTmp = $query->result_array();
            $this->db->flush_cache();
        } elseif ($options['task'] == 'update-task') {
            $date_start = $arrParam['date_start'];
            $date_end = $arrParam['date_end'];
            
            $this->db->select("t.id, t.name, t.date_start, t.date_end, t.lft, t.rgt")
            ->from($this->_table . ' as t')
            ->where('t.lft > ' . $arrParam['lft'] . ' AND t.rgt < ' . $arrParam['rgt'])
            ->where('t.project_id', $arrParam['project_id'])
            ->where("(t.date_start < '$date_start' OR t.date_end > '$date_end')");
            
            $query = $this->db->get();
            $resultTmp = $query->result_array();
            $this->db->flush_cache();
        }
        
        $result = array();
        if (! empty($resultTmp)) {
            foreach ($resultTmp as $val)
                $result[$val['id']] = $val;
        }
        
        return $result;
    }

    public function getItem($arrParams = null, $options = null)
    {
        if ($options['task'] == 'public-info') {
            $this->db->select("t.*")
            ->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y') as date_finish", FALSE)
            ->select("DATE_FORMAT(t.date_start, '%d-%m-%Y') as date_start", FALSE)
            ->select("DATE_FORMAT(t.date_end, '%d-%m-%Y') as date_end", FALSE)
            ->from($this->_table . ' as t')
            ->where('t.id', $arrParams['id']);
            
            $query = $this->db->get();
            
            $result = $query->row_array();

            $this->db->flush_cache();
            if ($options['brand'] == 'detail' || $options['brand'] == 'full') {
                if (! empty($result)) {
                    // tất cả task bao gồm task ở bên trên
                    if ($options['brand'] == 'full')
                        $task_ids = $this->getIds(array(
                            'lft' => $result['lft'],
                            'rgt' => $result['rgt'],
                            'project_id' => $result['project_id']
                        ), array(
                            'task' => 'up-branch'
                        ));
                    elseif ($options['brand'] == 'detail')
                        $task_ids = $this->getIds(array(
                            'lft' => $result['lft'],
                            'rgt' => $result['rgt'],
                            'project_id' => $result['project_id']
                        ));

                        // file list
                    $this->db->select('f.*')
                    ->from('task_files as f')
                    ->where('f.task_id IN (' . implode(',', $task_ids) . ')')
                    ->order_by('f.modified', 'DESC');
                    
                    $query = $this->db->get();
                    $result['files'] = $query->result_array();
                    
                    $this->db->flush_cache();
                    // end file list
                    if (! empty($result['customer_ids'])) {
                        $cid = explode(',', $result['customer_ids']);
                        // var_dump($cid);die();
                        $this->load->model('TaskCustomers','TaskCustomers');
                        $result['customers'] = $this->TaskCustomers->getItems(array(
                            'cid' => $cid
                        ));
                        // echo $this->db->last_query();die();
                        // var_dump($result['customers']);die();
                    }
                    
                    $this->db->select('r.*')
                    ->from('task_user_relations as r')
                    ->where('r.task_id IN (' . implode(',', $task_ids) . ')')
                    ->order_by('r.user_id', 'ASC');
                    
                    $query = $this->db->get();
                    $resultTmp = $query->result_array();
                    
                    $this->db->flush_cache();
                    $user_ids = array();
                    
                    $user_ids = array(
                        $result['created_by']
                    );
                    
                    if (! empty($resultTmp)) {
                        foreach ($resultTmp as $val)
                            $user_ids[] = $val['user_id'];
                        
                        $user_ids = array_unique($user_ids);
                        $this->load->model('TaskUser', 'TaskUser');
                        $tblUser = $this->TaskUser;
                        $users = $tblUser->getItems(array(
                            'user_ids' => $user_ids
                        ));
                        
                        $result['created_by_name'] = $users[$result['created_by']]['username'];
                        
                        foreach ($resultTmp as $val) {
                            $user_id = $val['user_id'];
                            $keywords = $val['task_id'] . '-' . $val['user_id'];
                            
                            if (isset($users[$user_id])) {
                                if ($val['is_xem'] == 1)
                                    $result['is_xem'][$keywords] = $users[$user_id];
                                
                                if ($val['is_implement'] == 1)
                                    $result['is_implement'][$keywords] = $users[$user_id];
                                
                                if ($val['is_create_task'] == 1)
                                    $result['is_create_task'][$keywords] = $users[$user_id];
                                
                                if ($val['is_pheduyet'] == 1)
                                    $result['is_pheduyet'][$keywords] = $users[$user_id];
                                
                                if ($val['is_progress'] == 1)
                                    $result['is_progress'][$keywords] = $users[$user_id];

                                if ($val['is_join'] == 1)
                                    $result['is_join'][$keywords] = $users[$user_id];

                            }
                        }
                    } else {
                        $this->load->model('TaskUser', 'TaskUser');
                        $tblUser = $this->TaskUser;
                        $users = $tblUser->getItems(array(
                            'user_ids' => $user_ids
                        ));
                        
                        $result['created_by_name'] = $users[$result['created_by']]['username'];
                    }
                }
            }
        } elseif ($options['task'] == 'information') {
            $this->db->select("t.*")
            ->from($this->_table . ' as t')
            ->where('t.id', $arrParams['id']);
            
            $query = $this->db->get();
            $result = $query->row_array();
            $this->db->flush_cache();
        }
        
        return $result;
    }

    public function sort($arrParam = null, $options = null)
    {
        if ($options == null) {
            $this->db->where("id", $arrParam['id']);
            
            $data['sort'] = (int) $arrParam['sort'];
            
            $this->db->update($this->_table, $data);
            
            $this->db->flush_cache();
        }
    }

    public function getMaxPercent($parent_id, $project_id, $id = null)
    {
        $this->db->select("t.percent")
        ->from($this->_table . ' as t')
        ->where('t.parent', $parent_id)
        ->where('t.project_id', $project_id);
        
        if ($id > 0)
            $this->db->where('t.id != ' . $id);
        
        $query = $this->db->get();
        $result = $query->result_array();
        $this->db->flush_cache();
        
        if (empty($result))
            $percent = 0;
        else {
            $percent = 0;
            foreach ($result as $value)
                $percent = $percent + $value['percent'];
        }
        
        $percent = round(100 - $percent,2);
        
        return $percent;
    }

    public function deleteItem($id)
    {
        $this->removeNode($id);
    }

    protected function getUsersRelation($task_ids, $options = null)
    {
        $this->db->select("r.task_id, r.is_implement, r.is_create_task, r.is_pheduyet, r.is_progress, r.is_xem, r.user_id, r.is_join")
        ->from('task_user_relations as r')
        ->where('r.task_id IN (' . implode(', ', $task_ids) . ')');
        
        if ($options == 'implement')
            $this->db->where('r.is_implement', 1);
        if ($options == 'join')
            $this->db->where('r.is_join', 1);
        
        $query = $this->db->get();
        
        $resultTmp = $query->result_array();

        if ($options == 'join') {
            $result = array();
            foreach ($resultTmp as $val)
                $result[] = $val['user_id'];
        } 
        else
            $result = $resultTmp;
        if ($options == 'implement') {
            $result = array();
            foreach ($resultTmp as $val)
                $result[] = $val['user_id'];
        } else
        $result = $resultTmp;

        
        $this->db->flush_cache();
        
        return $result;
    }

    protected function task_implements($items)
    {
        $result = array();
        if (! empty($items)) {
            foreach ($items as $val) {
                if ($val['is_implement'] == 1) {
                    $result[$val['task_id']][] = $val['user_id'];
                }
            }
        }
        
        return $result;
    }
    

    protected function task_joins($items)
    {
        $result = array();
        if (! empty($items)) {
            foreach ($items as $val) {
                if ($val['is_join'] == 1) {
                    $result[$val['task_id']][] = $val['user_id'];
                }
            }
        }
        
        return $result;
    }
    // support function
    protected function getProjectRelation()
    {
        // related project
        $sql = 'SELECT t.id, t.project_id
        FROM ' . $this->db->dbprefix($this->_table) . ' AS t
        WHERE t.id IN (SELECT task_id FROM ' . $this->db->dbprefix(task_user_relations) . ' WHERE user_id = ' . $this->_id_admin . ')' . ' ORDER BY t.prioty ASC, t.id DESC';
        
        $query = $this->db->query($sql);
        $resultTmp = $query->result_array();
        $project_ids = [];
        if (! empty($resultTmp)) {
            foreach ($resultTmp as $val) {
                $project_ids[$val['project_id']] = $val['project_id'];
            }
        }
        $this->db->flush_cache();

        // Get Projects By Assigning
        $this->db->select('task_id');
        $this->db->from('task_user_relations');
        $this->db->where('user_id', $this->_id_admin);
        $query = $this->db->get();
        if ($query) {
            $collection = $query->result_array();
            $query->free_result();
            $this->db->flush_cache();
            foreach ($collection as $row) {
                $project_ids[$row['task_id']] = $row['task_id'];
            }
        }

        // Get Projects By Owner
        $this->db->distinct();
        $this->db->select('tasks.project_id');
        $this->db->from('tasks');
        $this->db->where('created_by', $this->_id_admin);
        $query = $this->db->get();
        if ($query) {
            $collection = $query->result();
            $query->free_result();
            $this->db->flush_cache();
            foreach ($collection as $row) {
                $project_ids[$row->project_id] = $row->project_id;
            }
        }
        return $project_ids;
    }

    /**
     * @param $project_id
     * @return bool
     */
    function has_view_edit_permission($project_id) {
        $admin_id = $this->_id_admin;
        // Get Project Info
        $this->db->flush_cache();
        $this->db->from('phppos_tasks');
        $this->db->where('id', $project_id);
        $this->db->where('created_by', $admin_id);
        $query = $this->db->get();
        $mode_view = 0;
        $mode_edit = 1;
        $no_permission = -1;
        if ($query->num_rows() == 0) {
            $query->free_result();
            // Get Project Assigning Info
            $this->db->flush_cache();
            $this->db->from('task_user_relations');
            $this->db->join('employees', 'task_user_relations.user_id = employees.id');
            $this->db->where('task_id', $project_id);
            $this->db->where('person_id', $admin_id);
            $query = $this->db->get();
            if ($query->num_rows() == 0) {
                $query->free_result();
                return $no_permission;
            }
            $result = $query->row();
            $query->free_result();
            // Edit
            if ($result->is_implement == 1 || $result->is_pheduyet == 1 || $result->is_create_task == 1 || $result->is_join == 1) {
                return $mode_edit;
            }
            // View
            if ($result->is_xem == 1) {
                // Check Update Permission
                if (!empty($this->_task_permission)) {
                    // If Has One Of These Permissions:
                    // 1. Update Project
                    // 2. Update Brand Task
                    // 3. Update All Task
                    $permissions = ['update_project', 'update_brand_task', 'update_all_task'];
                    foreach ($permissions as $permission) {
                        if (in_array($permission, $this->_task_permission)) {
                            return $mode_edit;
                        }
                    }
                }
                return $mode_view;
            }
            // Check Update Permission
            if (!empty($this->_task_permission)) {
                // If Has One Of These Permissions:
                // 1. Update Project
                // 2. Update Brand Task
                // 3. Update All Task
                $permissions = ['update_project', 'update_brand_task', 'update_all_task'];
                foreach ($permissions as $permission) {
                    if (in_array($permission, $this->_task_permission)) {
                        return $mode_edit;
                    }
                }
            }
            return $mode_view;
        } else {
            $query->free_result();
        }
        return $mode_edit;
    }

    protected function checkAllPermission()
    {
        $flagAll = true;
        if (! (in_array('update_project', $this->_task_permission) && in_array('update_all_task', $this->_task_permission))) {
            $flagAll = false;
        }
        
        return $flagAll;
    }

    protected function getTasksIdsByProject($project)
    {
        if (in_array('update_all_task', $this->_task_permission))
            $task_ids = 'all';
        elseif (in_array('update_brand_task', $this->_task_permission)) {
            $task_ids = $this->getIds(array(
                'lft' => $project['lft'],
                'rgt' => $project['rgt'],
                'project_id' => $project['id']
            ));
        } else {
            $phppos_tasks = $this->db->dbprefix($this->_table);
            
            $this->db->select("t.id")
            ->from($this->_table . ' AS t')
            ->join('task_user_relations AS r', 't.id = r.task_id AND r.user_id = ' . $this->_id_admin, 'left')
            ->where('t.project_id', $project['id']);
            
            $query = $this->db->get();
            $resultTmp = $query->result_array();
            
            $task_ids = array();
            if (! empty($resultTmp)) {
                foreach ($resultTmp as $value) {
                    $task_ids[] = $value['id'];
                }
                
                $task_ids = implode(',', $task_ids);
                
                $sql = "SELECT t.id
                FROM $phppos_tasks AS t
                INNER JOIN (
                SELECT id, lft, rgt, project_id
                FROM $phppos_tasks
                WHERE id IN ($task_ids)
                ) AS tmp
                ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt";
                
                $query = $this->db->query($sql);
                $resTmp = $query->result_array();
                $this->db->flush_cache();
                
                $task_ids = array();
                if (! empty($resTmp)) {
                    foreach ($resTmp as $val)
                        $task_ids[] = $val['id'];
                }
            }
        }
        
        return $task_ids;
    }

    public function do_relation_information($lastId, $xemArr,$joinArr, $implementArr, $create_taskArr, $pheduyet_taskArr, $progress_taskArr)
    {
        $array = array();
        if (isset($xemArr)) {
            foreach ($xemArr as $user_id) {
                $tmp = array();
                $tmp['task_id'] = $lastId;
                $tmp['user_id'] = $user_id;
                
                $tmp['is_xem'] = 1;
                if (($key = array_search($user_id, $xemArr)) !== false) {
                    unset($xemArr[$key]);
                }
                
                if (in_array($user_id, $joinArr)) {
                    $tmp['is_join'] = 1;
                    if (($key = array_search($user_id, $joinArr)) !== false) {
                        unset($joinArr[$key]);
                    }
                } else
                $tmp['is_join'] = 0;

                if (in_array($user_id, $implementArr)) {
                    $tmp['is_implement'] = 1;
                    if (($key = array_search($user_id, $implementArr)) !== false) {
                        unset($implementArr[$key]);
                    }
                } else
                $tmp['is_implement'] = 0;
                
                if (in_array($user_id, $create_taskArr)) {
                    $tmp['is_create_task'] = 1;
                    if (($key = array_search($user_id, $create_taskArr)) !== false) {
                        unset($create_taskArr[$key]);
                    }
                } else
                $tmp['is_create_task'] = 0;
                
                if (in_array($user_id, $pheduyet_taskArr)) {
                    $tmp['is_pheduyet'] = 1;
                    if (($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
                        unset($pheduyet_taskArr[$key]);
                    }
                } else
                $tmp['is_pheduyet'] = 0;
                
                if (in_array($user_id, $progress_taskArr)) {
                    $tmp['is_progress'] = 1;
                    if (($key = array_search($user_id, $progress_taskArr)) !== false) {
                        unset($progress_taskArr[$key]);
                    }
                } else
                $tmp['is_progress'] = 0;
                
                $tmp['created'] = @date("Y-m-d H:i:s");
                
                $array[] = $tmp;
            }
        }
        

        #JOIN
        if (! empty($joinArr)) {
            foreach ($joinArr as $user_id) {
                $tmp = array();
                $tmp['task_id'] = $lastId;
                $tmp['user_id'] = $user_id;
                
                if (in_array($user_id, $xemArr)) {
                    $tmp['is_xem'] = 1;
                    if (($key = array_search($user_id, $xemArr)) !== false) {
                        unset($xemArr[$key]);
                    }
                } else
                $tmp['is_xem'] = 0;
                
                $tmp['is_join'] = 1;
                if (($key = array_search($user_id, $joinArr)) !== false) {
                    unset($joinArr[$key]);
                }
                
                if (in_array($user_id, $implementArr)) {
                    $tmp['is_implement'] = 1;
                    if (($key = array_search($user_id, $implementArr)) !== false) {
                        unset($implementArr[$key]);
                    }
                } else
                $tmp['is_implement'] = 0;


                if (in_array($user_id, $create_taskArr)) {
                    $tmp['is_create_task'] = 1;
                    if (($key = array_search($user_id, $create_taskArr)) !== false) {
                        unset($create_taskArr[$key]);
                    }
                } else
                $tmp['is_create_task'] = 0;
                
                if (in_array($user_id, $pheduyet_taskArr)) {
                    $tmp['is_pheduyet'] = 1;
                    if (($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
                        unset($pheduyet_taskArr[$key]);
                    }
                } else
                $tmp['is_pheduyet'] = 0;
                
                if (in_array($user_id, $progress_taskArr)) {
                    $tmp['is_progress'] = 1;
                    if (($key = array_search($user_id, $progress_taskArr)) !== false) {
                        unset($progress_taskArr[$key]);
                    }
                } else
                $tmp['is_progress'] = 0;
                
                $tmp['created'] = @date("Y-m-d H:i:s");
                
                $array[] = $tmp;
            }
        }


        #END JOIN




        if (! empty($implementArr)) {
            foreach ($implementArr as $user_id) {
                $tmp = array();
                $tmp['task_id'] = $lastId;
                $tmp['user_id'] = $user_id;
                
                if (in_array($user_id, $xemArr)) {
                    $tmp['is_xem'] = 1;
                    if (($key = array_search($user_id, $xemArr)) !== false) {
                        unset($xemArr[$key]);
                    }
                } else
                $tmp['is_xem'] = 0;

                if (in_array($user_id, $joinArr)) {
                    $tmp['is_join'] = 1;
                    if (($key = array_search($user_id, $joinArr)) !== false) {
                        unset($joinArr[$key]);
                    }
                } else
                $tmp['is_join'] = 0;
                
                $tmp['is_implement'] = 1;
                if (($key = array_search($user_id, $implementArr)) !== false) {
                    unset($implementArr[$key]);
                }
                
                if (in_array($user_id, $create_taskArr)) {
                    $tmp['is_create_task'] = 1;
                    if (($key = array_search($user_id, $create_taskArr)) !== false) {
                        unset($create_taskArr[$key]);
                    }
                } else
                $tmp['is_create_task'] = 0;
                
                if (in_array($user_id, $pheduyet_taskArr)) {
                    $tmp['is_pheduyet'] = 1;
                    if (($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
                        unset($pheduyet_taskArr[$key]);
                    }
                } else
                $tmp['is_pheduyet'] = 0;
                
                if (in_array($user_id, $progress_taskArr)) {
                    $tmp['is_progress'] = 1;
                    if (($key = array_search($user_id, $progress_taskArr)) !== false) {
                        unset($progress_taskArr[$key]);
                    }
                } else
                $tmp['is_progress'] = 0;
                
                $tmp['created'] = @date("Y-m-d H:i:s");
                
                $array[] = $tmp;
            }
        }
        
        if (! empty($create_taskArr)) {
            foreach ($create_taskArr as $user_id) {
                $tmp = array();
                $tmp['task_id'] = $lastId;
                $tmp['user_id'] = $user_id;
                
                if (in_array($user_id, $xemArr)) {
                    $tmp['is_xem'] = 1;
                    if (($key = array_search($user_id, $xemArr)) !== false) {
                        unset($xemArr[$key]);
                    }
                } else
                $tmp['is_xem'] = 0;


                if (in_array($user_id, $joinArr)) {
                    $tmp['is_join'] = 1;
                    if (($key = array_search($user_id, $joinArr)) !== false) {
                        unset($joinArr[$key]);
                    }
                } else
                $tmp['is_join'] = 0;
                
                if (in_array($user_id, $implementArr)) {
                    $tmp['is_implement'] = 1;
                    if (($key = array_search($user_id, $implementArr)) !== false) {
                        unset($implementArr[$key]);
                    }
                } else
                $tmp['is_implement'] = 0;
                
                $tmp['is_create_task'] = 1;
                if (($key = array_search($user_id, $create_taskArr)) !== false) {
                    unset($create_taskArr[$key]);
                }
                
                if (in_array($user_id, $pheduyet_taskArr)) {
                    $tmp['is_pheduyet'] = 1;
                    if (($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
                        unset($pheduyet_taskArr[$key]);
                    }
                } else
                $tmp['is_pheduyet'] = 0;
                
                if (in_array($user_id, $progress_taskArr)) {
                    $tmp['is_progress'] = 1;
                    if (($key = array_search($user_id, $progress_taskArr)) !== false) {
                        unset($progress_taskArr[$key]);
                    }
                } else
                $tmp['is_progress'] = 0;
                
                $tmp['created'] = @date("Y-m-d H:i:s");
                
                $array[] = $tmp;
            }
        }
        
        if (! empty($pheduyet_taskArr)) {
            foreach ($pheduyet_taskArr as $user_id) {
                $tmp = array();
                $tmp['task_id'] = $lastId;
                $tmp['user_id'] = $user_id;
                
                if (in_array($user_id, $xemArr)) {
                    $tmp['is_xem'] = 1;
                    if (($key = array_search($user_id, $xemArr)) !== false) {
                        unset($xemArr[$key]);
                    }
                } else
                $tmp['is_xem'] = 0;

                if (in_array($user_id, $joinArr)) {
                    $tmp['is_join'] = 1;
                    if (($key = array_search($user_id, $joinArr)) !== false) {
                        unset($joinArr[$key]);
                    }
                } else
                $tmp['is_join'] = 0;
                
                if (in_array($user_id, $implementArr)) {
                    $tmp['is_implement'] = 1;
                    if (($key = array_search($user_id, $implementArr)) !== false) {
                        unset($implementArr[$key]);
                    }
                } else
                $tmp['is_implement'] = 0;
                
                if (in_array($user_id, $create_taskArr)) {
                    $tmp['is_create_task'] = 1;
                    if (($key = array_search($user_id, $create_taskArr)) !== false) {
                        unset($create_taskArr[$key]);
                    }
                } else
                $tmp['is_create_task'] = 0;
                
                $tmp['is_pheduyet'] = 1;
                if (($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
                    unset($pheduyet_taskArr[$key]);
                }
                
                if (in_array($user_id, $progress_taskArr)) {
                    $tmp['is_progress'] = 1;
                    if (($key = array_search($user_id, $progress_taskArr)) !== false) {
                        unset($progress_taskArr[$key]);
                    }
                } else
                $tmp['is_progress'] = 0;
                
                $tmp['created'] = @date("Y-m-d H:i:s");
                
                $array[] = $tmp;
            }
        }
        
        if (! empty($progress_taskArr)) {
            foreach ($progress_taskArr as $user_id) {
                $tmp = array();
                $tmp['task_id'] = $lastId;
                $tmp['user_id'] = $user_id;
                
                if (in_array($user_id, $xemArr)) {
                    $tmp['is_xem'] = 1;
                    if (($key = array_search($user_id, $xemArr)) !== false) {
                        unset($xemArr[$key]);
                    }
                } else
                $tmp['is_xem'] = 0;


                if (in_array($user_id, $joinArr)) {
                    $tmp['is_join'] = 1;
                    if (($key = array_search($user_id, $joinArr)) !== false) {
                        unset($joinArr[$key]);
                    }
                } else
                $tmp['is_join'] = 0;

                
                if (in_array($user_id, $implementArr)) {
                    $tmp['is_implement'] = 1;
                    if (($key = array_search($user_id, $implementArr)) !== false) {
                        unset($implementArr[$key]);
                    }
                } else
                $tmp['is_implement'] = 0;
                
                if (in_array($user_id, $create_taskArr)) {
                    $tmp['is_create_task'] = 1;
                    if (($key = array_search($user_id, $create_taskArr)) !== false) {
                        unset($create_taskArr[$key]);
                    }
                } else
                $tmp['is_create_task'] = 0;
                
                if (in_array($user_id, $pheduyet_taskArr)) {
                    $tmp['is_pheduyet'] = 1;
                    if (($key = array_search($user_id, $pheduyet_taskArr)) !== false) {
                        unset($pheduyet_taskArr[$key]);
                    }
                } else
                $tmp['is_pheduyet'] = 0;
                
                $tmp['is_progress'] = 1;
                
                if (($key = array_search($user_id, $progress_taskArr)) !== false) {
                    unset($progress_taskArr[$key]);
                }
                
                $tmp['created'] = @date("Y-m-d H:i:s");
                
                $array[] = $tmp;
            }
        }
        
        return $array;
    }

    protected function sort_lft_items($items)
    {
        $result = array();
        foreach ($items as $item)
            $items_project[$item['project_id']][] = $item;
        
        foreach ($items_project as $item_s) {
            $tmp = sort_items($item_s, 'lft', 'ASC');
            $result = array_merge($result, $tmp);
        }
        
        return $result;
    }

    protected function get_where_from_filter($arrParams,$option=null)
    {
        $where = array();
        // var_dump($arrParams);die();

        if (! empty($arrParams['keywords'])) {

             if($option == 'cong_viec_con'){
                $keywords = $arrParams['keywords'];
                $where[] = '(t.name LIKE \'%' . $keywords . '%\' OR t.detail LIKE \'%' . $keywords . '%\' OR tt.name LIKE \'%' . $keywords . '%\')';
            }
            else{
                $keywords = $arrParams['keywords'];
                $where[] = '(t.name LIKE \'%' . $keywords . '%\' OR t.detail LIKE \'%' . $keywords . '%\')';
            }
            
        }


        if ($arrParams['project_id'] > 0) {
            $where[] = 't.project_id = ' . (int) $arrParams['project_id'];
        }
        
        if (! empty($arrParams['date_start_from'])) {
            $date_start_from = $arrParams['date_start_from'];
            $where[] = 't.date_start >= \'' . $date_start_from . '\'';
        }
        
        if (! empty($arrParams['date_start_to'])) {
            $date_start_to = $arrParams['date_start_to'];
            $where[] = 't.date_start <= \'' . $date_start_to . '\'';
        }
        
        if (! empty($arrParams['date_end_from'])) {
            $date_end_from = $arrParams['date_end_from'];
            $where[] = 't.date_end >= \'' . $date_end_from . '\'';
        }
        
        if (! empty($arrParams['date_end_to'])) {
            $date_end_to = $arrParams['date_end_to'];
            $where[] = 't.date_end <= \'' . $date_end_to . '\'';
        }
        
        if (! empty($arrParams['trangthai'])) {
            $current_now = date('Y-m-d H:i:s');
            if ($arrParams['trangthai'] == 'zero')
                $arrParams['trangthai'] = '0';
            
            $trangthai_arr = explode(',', $arrParams['trangthai']);
            if (in_array(5, $trangthai_arr) && in_array(6, $trangthai_arr)) {
                if (($key = array_search(5, $trangthai_arr)) !== false)
                    unset($trangthai_arr[$key]);
                if (($key = array_search(6, $trangthai_arr)) !== false)
                    unset($trangthai_arr[$key]);
            } else {
                if (in_array(5, $trangthai_arr)) {
                    if (($key = array_search(5, $trangthai_arr)) !== false)
                        unset($trangthai_arr[$key]);
                    $where_clause[] = "TIMESTAMPDIFF(SECOND, t.date_end, '$current_now') > 0";
                }
                if (in_array(6, $trangthai_arr)) {
                    if (($key = array_search(5, $trangthai_arr)) !== false)
                        unset($trangthai_arr[$key]);
                    $where_clause[] = "TIMESTAMPDIFF(SECOND, t.date_end, '$current_now') <= 0";
                }
            }
            if(empty($trangthai_arr))
            {
                $trangthai_arr[] =2;
            }
            $where_clause[] = 't.trangthai IN (' . implode(',', $trangthai_arr) . ')';
            $where[] = '(' . implode(' AND ', $where_clause) . ')';
        }
        
        if (! empty($arrParams['customers'])) {
            $customers = explode(',', $arrParams['customers']);
            $where_clause = array();
            foreach ($customers as $cus_id) {
                $where_clause[] = "CONCAT(',',customer_ids,',') LIKE '%,$cus_id,%'";
            }
            
            $where[] = implode(' OR ', $where_clause);
        }
        
        if (! empty($arrParams['pheduyet']) && $arrParams['pheduyet'] != '-1,0,1,2') {
            if ($arrParams['pheduyet'] == 'zero')
                $arrParams['pheduyet'] = '0';
            
            $pheduyet = $arrParams['pheduyet'];
            $where[] = 't.pheduyet IN (' . $pheduyet . ')';
        }
        
        

        $phppos_tasks = $this->db->dbprefix($this->_table);
        if (!empty($arrParams['implement']) || !empty($arrParams['related_to'])) {
            $implement = !empty($arrParams['implement']) ? $arrParams['implement'] : $arrParams['related_to'];
            $this->db->select("r.task_id")
            ->from('task_user_relations AS r')
            ->where('r.user_id IN (' . $implement . ')');
            
            if (!empty($arrParams['implement'])) {
                $this->db->where('r.is_implement = 1');
            }

            $query = $this->db->get();
            $resultTmp = $query->result_array();
            $this->db->flush_cache();
            if (! empty($resultTmp)) {
                $task_ids = array();
                foreach ($resultTmp as $val)
                    $task_ids[] = $val['task_id'];
                
                $task_ids = implode(', ', $task_ids);
                $sql = "SELECT t.id
                FROM $phppos_tasks AS t
                INNER JOIN (
                SELECT id, lft, rgt, project_id
                FROM $phppos_tasks
                WHERE id IN ($task_ids)
                ) AS tmp
                ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt";
                
                $query = $this->db->query($sql);
                $resTmp = $query->result_array();
                $this->db->flush_cache();
                
                $task_ids = array();
                foreach ($resTmp as $val)
                    $task_ids[] = $val['id'];
                
                $where[] = 't.id IN (' . implode(',', $task_ids) . ')';
            } else
            $where[] = 't.id = -1';
        }
        
        if (! empty($arrParams['xem'])) {
            $xem = $arrParams['xem'];
            $this->db->select("r.task_id")
            ->from('task_user_relations AS r')
            ->where('r.user_id IN (' . $xem . ')')
            ->where('r.is_xem = 1');
            
            $query = $this->db->get();
            $resultTmp = $query->result_array();
            $this->db->flush_cache();
            if (! empty($resultTmp)) {
                $task_ids = array();
                foreach ($resultTmp as $val)
                    $task_ids[] = $val['task_id'];
                
                $task_ids = implode(', ', $task_ids);
                $sql = "SELECT t.id
                FROM $phppos_tasks AS t
                INNER JOIN (
                SELECT id, lft, rgt, project_id
                FROM $phppos_tasks
                WHERE id IN ($task_ids)
                ) AS tmp
                ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt";
                
                $query = $this->db->query($sql);
                $resTmp = $query->result_array();
                $this->db->flush_cache();
                
                $task_ids = array();
                foreach ($resTmp as $val)
                    $task_ids[] = $val['id'];
                
                $where[] = 't.id IN (' . implode(',', $task_ids) . ')';
            } else
            $where[] = 't.id = -1';
        }


        #JOIN

        if (! empty($arrParams['join'])) {
            $join = $arrParams['join'];
            $this->db->select("r.task_id")
            ->from('task_user_relations AS r')
            ->where('r.user_id IN (' . $join . ')')
            ->where('r.is_join = 1');
            
            $query = $this->db->get();
            $resultTmp = $query->result_array();
            $this->db->flush_cache();
            if (! empty($resultTmp)) {
                $task_ids = array();
                foreach ($resultTmp as $val)
                    $task_ids[] = $val['task_id'];
                
                $task_ids = implode(', ', $task_ids);
                $sql = "SELECT t.id
                FROM $phppos_tasks AS t
                INNER JOIN (
                SELECT id, lft, rgt, project_id
                FROM $phppos_tasks
                WHERE id IN ($task_ids)
                ) AS tmp
                ON t.project_id = tmp.project_id AND t.lft >= tmp.lft AND t.rgt <= tmp.rgt";
                
                $query = $this->db->query($sql);
                $resTmp = $query->result_array();
                $this->db->flush_cache();
                
                $task_ids = array();
                foreach ($resTmp as $val)
                    $task_ids[] = $val['id'];
                
                $where[] = 't.id IN (' . implode(',', $task_ids) . ')';
            } else
            $where[] = 't.id = -1';
        }
        

        #END JOIN
        
        if (! empty($arrParams['progress']) && $arrParams['progress'] != '-1,0,1,2') {
            if ($arrParams['progress'] == 'zero')
                $arrParams['progress'] = '0';
            
            $sql = 'SELECT task_id
            FROM ' . $this->db->dbprefix(task_progress) . ' WHERE trangthai IN (' . $arrParams['progress'] . ')';
            
            $where[] = 't.id IN (' . $sql . ')';
        }
        
        // var_dump($where);die();
        return $where;
    }

    function model_load_model($model_name)
    {
        $CI = & get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }
    
    function get_project_by_customer($customer_id) {
        $this->db->from('tasks');
        $this->db->where('customer_ids', $customer_id);
        $this->db->where('parent', 0);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            return $result[0];
        } else {
            return NULL;
        }
    }
    
    function get_project_by_parent_id($parent_id) {
        $this->db->from('tasks');
        $this->db->where('parent', $parent_id);
        $this->db->order_by('lft', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_task_item($id) {
        $this->db->from('tasks');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result_array()[0];
    } 
    
    function update_item_pos($params, $where) {
        $this->db->update('tasks', $params, $where);
    }
    
    function find_project_by_customer($params,$option="") {
       $list = $list = $this->Receiving->get_list_task($this->Employee->get_logged_in_employee_info()->id);
       // var_dump($this->db->last_query()); die();
       $this->db->select("project_id, name as label, name as value,id as task_id");
       $this->db->from("tasks");
        // $this->db->where("project_id not in (select m.project_id from ". $this->db->dbprefix('contract') ." m )");
       $this->db->join('phppos_sales as s', 's.task_id = tasks.id');
       if($this->_scopeOfView == 'view_scope_owner')
       {
        $this->db->where_in('tasks.id', $list);
    }
    if($this->_scopeOfView == 'view_scope_location')
    {
        $this->db->where('s.location_id', $this->Employee->get_logged_in_employee_current_location_id());
        $this->db->or_where_in('tasks.id', $list);
    }

    if($offtion=="for_contract")
    {
        $this->db->where("task_id not in (select m.project_id from ". $this->db->dbprefix('contract') ." m )");
    }
    $this->db->where("lower(name) like '%" . strtolower($params['name']) . "%'");
    $this->db->where("parent", 0);
    $query = $this->db->get();
    return $query->result_array();
}

function get_task_by_project_id($project_id) {
    $this->db->from('tasks');
    $this->db->where('project_id', $project_id);
    $this->db->where('parent > 0');
    $this->db->order_by('level,rgt', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
}


    #Lấy id người tham gia theo task_id
protected function get_relation_by_task_id($task_id,$option='is_join')
{
    $this->db->select('user_id');
    $this->db->where('task_id', $task_id);
    $this->db->where($option, 1);
    $r = $this->db->get('task_user_relations')->result_array();
    $result = array();
    foreach ($r as  $v) {
        $result[] = $v['user_id'];
    }
    return $result;
}


    #Lấy thông tin usename của toàn bộ nhân viên ngoài trừ mảng =$option
function get_info_users($option=null)
{   
    $this->db->select('id,username');
    $this->db->from('phppos_employees');
    $this->db->where('deleted', 0);
    if($option)  
    {
      $this->db->where_not_in('id',$option);
  }
  return $this->db->get()->result_array();

}



    #Lấy danh sách người thay thế theo task_id
function get_list_task_tranfer($task_id)
{

    $this->db->select('tt.username as ngdtt,tt.id as id_dtt');
    $this->db->from('phppos_employees as tt');
    $this->db->where('tt.deleted', 0);
    $subquery = $this->db->get_compiled_select();

    $this->db->select('phppos_tranfer.time, phppos_tranfer.id, phppos_task_tranfer.*, phppos_employees.username as tranfer_person,  GROUP_CONCAT(tranfer.ngdtt SEPARATOR ", ") as list_tranfer');
    $this->db->from('phppos_task_tranfer');
    $this->db->join('phppos_tranfer', 'phppos_task_tranfer.tranfer_id = phppos_tranfer.id', 'left');
    $this->db->join('phppos_employees', 'phppos_task_tranfer.user_id = phppos_employees.id', 'left');
    $this->db->join("($subquery) as tranfer", 'phppos_task_tranfer.user_id_tranfer = tranfer.id_dtt', 'left');
    $this->db->where('task_id', $task_id);
    $this->db->group_by('id');
    $this->db->order_by('tranfer_id', 'desc');
    return $this->db->get()->result_array();
}


    #Lấy danh sách chi phí và bên thứ 3 theo task_id

function get_list_item_by_task_id($task_id)
{
    $this->db->select('phppos_tasks.name,phppos_items.`name` as item_name,phppos_items.cost_price_interval,phppos_receivings_items.item_cost_price,phppos_receivings_items.item_cost_price, phppos_tasks.id as task_id,phppos_receivings.supplier_id,phppos_suppliers.company_name, phppos_receivings.receiving_id,phppos_receivings.receiving_time,phppos_receivings_items.item_unit_price as cost');
    $this->db->from('phppos_receivings');
    $this->db->join('phppos_receivings_items', 'phppos_receivings.receiving_id = phppos_receivings_items.receiving_id');
    $this->db->join('phppos_items', 'phppos_receivings_items.item_id = phppos_items.item_id');
    $this->db->join('phppos_tasks', 'phppos_tasks.id = phppos_receivings.task_id');
    $this->db->join('phppos_suppliers', 'phppos_receivings.supplier_id = phppos_suppliers.person_id');
    $this->db->where('phppos_receivings.task_id', $task_id);

    $this->db->order_by('phppos_receivings.receiving_time', 'desc');
    return $this->db->get()->result_array();

}


protected function check_user_by_task($user_id,$task_id)
{

    $this->db->where('task_id', $task_id);
    $this->db->where('user_id', $user_id);
    return $this->db->get('phppos_task_user_relations')->row_array();
}


#Mặc định option là người tham gia
function update_user_relation($user_id,$task_id,$option='is_join',$set=0)
{

    if($this->check_user_by_task($user_id,$task_id))
    {
        $this->db->where('task_id', $task_id);
        $this->db->where('user_id', $user_id);
        $this->db->update('phppos_task_user_relations', array($option=>$set));
    }

    else
    {
        $data = array(
            'is_implement'=>0,
            'is_xem'=>0,
            'is_join'=>0,
            'is_create_task'=>0,
            'is_pheduyet'=>0,
            'is_progress'=>0,
            'user_id'=>$user_id,
            'created'=>date('Y-m-d H:m:s'),
            'task_id'=>$task_id);
        $data[$option] = $set;
        $this->db->insert('phppos_task_user_relations', $data);
    }
}

    #Lấy ds dự án
function getlisttask()
{
    $this->db->select('t.id, t.name,t.parent, t.date_end ,t.date_start as start_date');
    $this->db->from('phppos_tasks as t');
    $this->db->limit(200);

    return $this->db->get()->result_array();
}


function add_percent_for_child($id)
{
    $this->db->select('t.id,t.date_start,t.date_end,t.parent');
    $this->db->from('phppos_tasks as t');
    $this->db->where('parent', $id);
    $r = $this->db->get()->result_array();
    if(empty($r))
    {
        return;
    }
    $t =0;
        # Lấy tổng thời gian
    foreach ($r as $key => $value) {
        $d1 = date_create($value['date_start']);
        $d2 = date_create($value['date_end']);
        $d = date_diff($d1,$d2)->format('%a');
        $t += $d;
        $r[$key]['d'] = $d;

    }
    if($t>=1)
    {   
        $i=1;
        $c=count($r);
        $tt=0;
        foreach ($r as $key => $value) {  
            if($i==$c)
            {
                $p = 100-$tt;
            }

            else{
                $p = round($r[$key]['d']/$t*100,2);
                $tt +=$p;
                $i++;
            }  
            $this->db->where('id', $value['id']);
            $this->db->update('phppos_tasks', array('percent'=>$p));
            $this->add_percent_for_child($value['id']);
        }
    }


}


    # Lấy tất cả con cháu của node
function get_list_node($id)
{

        // $tt = array();
    static $tt=array();
    $this->db->select('t.name,t.id,t.parent,t.prioty');
    $this->db->from('phppos_tasks as t');
    $this->db->where('parent', $id);
    $result = $this->db->get()->result_array();
    if(empty($result))
    {
        return;
    }

    foreach ($result as $key => $value) {
        $tt[] = $value['id'];
        $this->get_list_node($value['id']);
    }  
    return $tt;


}

function get_customer_by_task_id($task_id)
{
    $this->db->select('p.last_name');
    $this->db->from('phppos_tasks as t');
    $this->db->join('phppos_sales as s', 's.task_id = t.id', 'left');
    $this->db->join('phppos_people as p', 'p.person_id = s.customer_id', 'left');
    $this->db->where('t.id', $task_id);
    return $this->db->get()->row_array();
}



#CẬP nhật thời gian cho công việc sau khi tạo template
function update_time_for_task($id){
    $this->db->select('t.id,t.date_start,t.date_end,t.rgt,t.lft');
    $this->db->from('phppos_tasks as t');
    $this->db->where('t.project_id', $id);
    $this->db->where('level >',0);
    $this->db->where('t.rgt > (t.lft +1)');
    $this->db->order_by('t.rgt', 'asc');

    $result = $this->db->get()->result_array();
 // echo $this->db->last_query();die();
    if(empty($result))
        return;
    foreach ($result as $key => $value) {
        $this->update_time_for_child($value['id']);
    }
}


protected function update_time_for_child($id){
    $this->db->select('t.date_start,t.date_end,t.id');
    $this->db->from('phppos_tasks as t');
    $this->db->where('t.parent', $id);
    $this->db->order_by('t.rgt', 'asc');          
    $r = $this->db->get()->result_array();
         // echo $this->db->last_query();die();
    $start = date("Y-m-d",strtotime($r[0]['date_start']));
    $end = date("Y-m-d",strtotime($r[count($r)-1]['date_end']));
    $this->db->where('id', $id);
    $this->db->update('phppos_tasks', array('date_start'=>$start,'date_end'=>$end));
}



function get_list_task(){
        // echo 1; die();
  $employee = $this->Employee->get_logged_in_employee_info();
  $this->db->select('t.id,t.name,t.date_finish');
  $this->db->from('phppos_task_user_relations as tr');
  $this->db->join('phppos_tasks as t', 't.id = tr.task_id');
  $this->db->where('tr.user_id', $employee->id);
  $this->db->where('tr.is_progress', 1);
  $this->db->where('t.trangthai', 2);
  $this->db->where('t.pheduyet', 2);
  $this->db->where('t.parent', 0);
  $this->db->order_by('t.date_start', 'DESC');
  return  $this->db->get()->result_array();
}

function add_log($data){
    $data['person_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
    $data['time'] = date("Y-m-d H:i:s");
    $this->db->insert('phppos_task_log',$data);
}

function list_log($arrParam=array()){
    $this->db->select('e.username,t.name,DATE_FORMAT(tl.time,"%d-%m-%Y %H:%i:%s") as time,tl.trangthai,tl.progress,tl.prioty');
    $this->db->from('phppos_task_log as tl');
    $this->db->join('phppos_tasks as t', 't.id = tl.task_id');
    $this->db->join('phppos_employees as e', 'e.person_id = tl.person_id');
    if(!empty($arrParam['limit']))
    {
        $this->db->limit($arrParam['limit'],$arrParam['offset']);
    }
    if(!empty($arrParam['project_id']))
    {
        $this->db->where('t.project_id', $arrParam['project_id']);
    }
    $this->db->order_by('tl.id', 'desc');
    $query = $this->db->get();
        // echo $this->db->last_query();die();
    if (!$query) {
        return null;
    }
    $list = $query->result_array();
    $query->free_result();
    $arr = lang('task_trangthai');
    $arrp = lang('task_prioty');
    foreach ($list as $key => &$value) {
        $value['trangthai'] = $arr[$value['trangthai']];
        $value['prioty'] = $arrp[$value['prioty']];

    }

    return $list;
}

    /**
     * @param $person_id
     * @return mixed
     */
    public function get_location_id($person_id) {
        $this->db->select('location_id');
        $this->db->from('employees_locations');
        $this->db->where('employee_id', $person_id);
        $query = $this->db->get();
        if (!$query) {
            return null;
        }
        $row = $query->row();
        $query->free_result();
        $this->db->flush_cache();
        return $row->location_id;
    }

    /**
     * @param $person_id
     * @return null
     */
    function get_same_location_project_ids($person_id, $location_id = null) {
        if (empty($location_id)) {
            $location_id = $this->get_location_id($person_id);
        }
        $result = [];

        // Get Projects By Owner
        $this->db->distinct();
        $this->db->select('tasks.project_id');
        $this->db->from('tasks');
        $this->db->join('employees', 'tasks.created_by = employees.id');
        $this->db->join('people', 'employees.person_id = people.person_id');
        $this->db->join('employees_locations', 'employees_locations.employee_id = people.person_id');
        $this->db->where('employees_locations.location_id', $location_id);
        $query = $this->db->get();
        $collection = $query->result();
        $query->free_result();
        $this->db->flush_cache();
        foreach ($collection as $row) {
            $result[$row->project_id] = $row->project_id;
        }

        // Get Projects By Assigning
        $this->db->distinct();
        $this->db->select('task_user_relations.task_id');
        $this->db->from('task_user_relations');
        $this->db->join('employees', 'task_user_relations.user_id = employees.id');
        $this->db->join('people', 'employees.person_id = people.person_id');
        $this->db->join('employees_locations', 'employees_locations.employee_id = people.person_id');
        $this->db->where('employees_locations.location_id', $location_id);
        $query = $this->db->get();
        $collection = $query->result();
        $query->free_result();
        $this->db->flush_cache();
        foreach ($collection as $row) {
            $result[$row->task_id] = $row->task_id;
        }
        return $result;
    }

    /**
     * Get task id by sale id
     * @param $sale_id
     * @return mixed
     */
    public function get_task_id_by_sale_id($sale_id) {
        $this->db->distinct();
        $this->db->select('id');
        $this->db->from('tasks');
        $this->db->where('sale_id', $sale_id);
        $query = $this->db->get();
        $row = $query->row();
        $query->free_result();
        $this->db->flush_cache();
        return $row->id;
    }

    function get_number_task_alert(){
        $id =$this->Employee->get_logged_in_employee_info()->id;
        $time = intval($this->config->item('alert_time'));
        $time = $time ? $time :10;
        if($time>1000)
            $time =10;
        return count($this->Employee->get_task_alert($id,$time));
    }

    # Lấy danh sách  công việc mà người này tham gia hoặc phụ trách theo EMPLOYEE_ID(không phải person_id) :)))
    function get_task_join_implement($id){

        $this->db->select('t.id');
        $this->db->from('phppos_tasks as t');
        $this->db->join('phppos_task_user_relations as tu', 'tu.task_id = t.id');
        $this->db->join('phppos_sales as s', 's.task_id = t.id');
        $this->db->join('phppos_contract as c', 'c.sale_id = s.sale_id');
        $this->db->where('t.parent', 0);
        $this->db->group_start();
        $this->db->where('tu.is_join', 1);
        $this->db->or_where('tu.is_implement', 1);
        $this->db->group_end();
        $this->db->where('tu.user_id',$id);
        $result = $this->db->get()->result_array();
        foreach ($result as $key => $value) {
            $result[$key] = $value['id'];
        }

        $this->db->select('t.id');
        if(!empty($result)){
          $this->db->where_in('t.project_id', $result);
          $result =$this->db->get('phppos_tasks as t')->result_array();
          foreach ($result as $key => $value) {
            $result[$key] = $value['id'];
        }
        return $result;
    }
    else{
        $result =$this->db->get('phppos_tasks as t')->row_array();
        return [];
    }


}
function get_task_stage(){
    $id = $this->Employee->get_logged_in_employee_info()->id;
    $list = $this->get_task_join_implement($id);
    $this->db->select('cp.contract_id,DATE_FORMAT(t.date_finish,"%d-%m-%Y") as date_finish,DATE_FORMAT(t.date_end,"%d-%m-%Y") as date_end,cp.id,cp.task_id,t.name as task_name,cp.name as contract_payment_name,cp.c_status,c.name as contract_name');
    $this->db->from('phppos_tasks as t');
    $this->db->join('phppos_contract_payment as cp', 't.id = cp.task_id','left');
    $this->db->join('phppos_contract as c', 'c.id=cp.contract_id');
    $this->db->where('cp.c_status','');
    if(!empty($list)){
        $this->db->where_in('t.id', $list);
    }
    else{
        $this->db->get()->result_array();
        return [];
    }

    $this->db->group_start();
    $this->db->group_start();
                // $this->db->where('DATE(t.date_finish)=CURDATE()'); 
    $this->db->where('t.trangthai', 2);
    $this->db->group_end();
    $this->db->or_group_start();
                // $this->db->where('DATE(t.date_end)=CURDATE()');
    $this->db->where('(t.trangthai = 1 OR t.trangthai =0)');
    $this->db->group_end();   

    $this->db->group_end();
    $result =  $this->db->get()->result_array(); 
        // echo $this->db->last_query();die();
    return $result;


}

function get_project_info($project_id,$option=null){
    $this->db->select('t.id,t.name,t.progress,t.trangthai,t.prioty,DATE_FORMAT(t.date_end,"%d-%m-%Y") as date_end,DATE_FORMAT(t.date_start,"%d-%m-%Y") as date_start,t.date_finish,t.level');
    $this->db->from('phppos_tasks as t');
    $this->db->where('project_id', $project_id);
    $this->db->order_by('rgt', 'desc');
    $result = $this->db->get()->result_array();
    foreach ($result as $key => $value) {
        $tasks[$key] = $value['id'];
    }
    # Lấy người tham gia
    $this->db->select('task_id,GROUP_CONCAT(e.username) as joins');
    $this->db->from('phppos_task_user_relations as tu');
    $this->db->join('phppos_employees as e', 'tu.user_id = e.id');
    $this->db->where('is_join', 1);
    $this->db->where_in('task_id', $tasks);
    $this->db->group_by('tu.task_id');
    $joins = $this->db->get()->result_array();
    // echo $this->db->last_query();die();
    # Lấy người phụ trách
    $this->db->select('task_id,GROUP_CONCAT(e.username) as implements');
    $this->db->from('phppos_task_user_relations as tu');
    $this->db->join('phppos_employees as e', 'tu.user_id = e.id');
    $this->db->where('is_implement', 1);
    $this->db->where_in('task_id', $tasks);
    $this->db->group_by('tu.task_id');
    $implements = $this->db->get()->result_array();
    // var_dump($implements);die();
    foreach ($result as $key => $value) {
        foreach ($joins as $key2 => $value2) {
         if($value['id']==$value2['task_id'])
            $result[$key]['joins'] = $value2['joins'];
    }
    foreach ($implements as $key3 => $value3) {
     if($value['id']==$value3['task_id'])
        $result[$key]['implements'] = $value3['implements'];
}
}

$arr_trangthai = lang('task_trangthai');
$arr_prioty = lang('task_prioty');

foreach ($result as $key => &$value) {
    $value['trangthai'] = $arr_trangthai[$value['trangthai']];
    $value['prioty'] = $arr_prioty[$value['prioty']];
}
return $result;
}

# Lấy danh sách dư án ng này tham gia, phụ trách
function get_list_project_by_id($id){

    $this->db->select('t.id,t.name,t.progress,t.trangthai,t.prioty,DATE_FORMAT(t.date_end,"%d-%m-%Y") as date_end,DATE_FORMAT(t.date_start,"%d-%m-%Y") as date_start,t.date_finish,t.level,c.name as contract_name,c.id as contract_id');
    $this->db->from('phppos_tasks as t');
    $this->db->join('phppos_task_user_relations as tu', 'tu.task_id = t.id');
    $this->db->join('phppos_sales as s', 's.task_id = t.id','left');
    $this->db->join('phppos_contract as c', 'c.sale_id = s.sale_id','left');
    $this->db->where('t.parent', 0);
    $this->db->group_start();
    $this->db->where('tu.is_join', 1);
    $this->db->or_where('tu.is_implement', 1);
    $this->db->group_end();
    $this->db->where('tu.user_id',$id);
    $result = $this->db->get()->result_array();


    foreach ($result as $key => $value) {
        $tasks[$key] = $value['id'];
    }
    # Lấy người tham gia
    $this->db->select('task_id,GROUP_CONCAT(e.username) as joins');
    $this->db->from('phppos_task_user_relations as tu');
    $this->db->join('phppos_employees as e', 'tu.user_id = e.id');
    $this->db->where('is_join', 1);
    $this->db->where_in('task_id', $tasks);
    $this->db->group_by('tu.task_id');
    $joins = $this->db->get()->result_array();
    // echo $this->db->last_query();die();
    # Lấy người phụ trách
    $this->db->select('task_id,GROUP_CONCAT(e.username) as implements');
    $this->db->from('phppos_task_user_relations as tu');
    $this->db->join('phppos_employees as e', 'tu.user_id = e.id');
    $this->db->where('is_implement', 1);
    $this->db->where_in('task_id', $tasks);
    $this->db->group_by('tu.task_id');
    $implements = $this->db->get()->result_array();

    foreach ($result as $key => $value) {
        foreach ($joins as $key2 => $value2) {
         if($value['id']==$value2['task_id'])
            $result[$key]['joins'] = $value2['joins'];
    }
    foreach ($implements as $key3 => $value3) {
     if($value['id']==$value3['task_id'])
        $result[$key]['implements'] = $value3['implements'];
}
}

$arr_trangthai = lang('task_trangthai');
$arr_prioty = lang('task_prioty');

foreach ($result as $key => &$value) {
    $value['trangthai'] = $arr_trangthai[$value['trangthai']];
    $value['prioty'] = $arr_prioty[$value['prioty']];
}
return $result;


}


#Đếm số dự án thực hiện
function count_task($option=null){
    $this->db->select('COUNT(t.id) as task');
    $this->db->from('phppos_tasks as t');
    $this->db->join('phppos_sales as s', 's.task_id = t.id');
    if($this->_scopeOfView=='view_scope_owner'){
        $this->db->join('phppos_task_user_relations as tu', 't.id = tu.task_id');
        $this->db->where('(tu.is_join =1 OR tu.is_implement=1)');
        $this->db->where('tu.user_id', $this->Employee->get_logged_in_employee_info()->id);
    }
    if($this->_scopeOfView=='view_scope_location'){
        $this->db->where('s.location_id', $this->Employee->get_logged_in_employee_current_location_id());
    }
    $this->db->where('t.trangthai !=', 3);
    $this->db->where('t.pheduyet', 2);
    $this->db->where('t.parent', 0);
    $result = $this->db->get()->row()->task;
    // echo $this->db->last_query();die();
    return $result;
}

function get_task_user_relation($task_id,$user_id){
    $this->db->select('tu.is_join,tu.is_xem,tu.is_implement,tu.is_pheduyet,tu.is_create_task');
    $this->db->where('task_id', $task_id);
    $this->db->where('user_id', $user_id);
    return $this->db->get('phppos_task_user_relations as tu')->row_array();
}

function task_notice_log($task_id,$data){
    // var_dump($data);die();
    $dt['implements'] = !empty($data['implement']) ? json_encode($data['implement']):'[""]';
    $dt['joins'] = !empty($data['join']) ? json_encode($data['join']):'[""]';
    $dt['task_id'] =$task_id;
    $dt['time'] = date("Y-m-d H:i:s");
    $dt['person_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
    $dt['approved'] = !empty($data['progress_task']) ? json_encode($data['progress_task']):'[""]';
    $dt['seens'] = '[""]';
    $where = array('task_id'=>$task_id,'joins'=>$dt['joins'],'implements'=>$dt['implements'],'approved'=>$dt['approved']);
    $this->db->where($where);
    $result = $this->db->get('phppos_task_log_notice')->row_array();
    if(empty($result)){
        $this->db->insert('phppos_task_log_notice', $dt);
    }
}


    # Danh sách thông báo công việc không tạo doanh thu
    function get_list_notice_task($option=null){
        $id = $this->Employee->get_logged_in_employee_info()->id;
        $this->db->select('nl.task_id,nl.person_id,nl.approved,nl.joins,nl.id,nl.implements,nl.seens,e.username,t.name,DATE_FORMAT(nl.time, "%d-%m-%Y ") as time');
        $this->db->from('phppos_task_log_notice as nl');
        $this->db->join('phppos_employees as e', 'e.person_id = nl.person_id');
        $this->db->join('phppos_task_user_relations as tp', 'tp.task_id = nl.task_id');
        $this->db->join('phppos_tasks as t', 't.id= tp.task_id');
        $this->db->where("(tp.is_implement=1 OR tp.is_join=1 OR tp.is_pheduyet=1)");
        if($option==null){
            $this->db->not_like('nl.seens', '"'.$id.'"');
        }
        $this->db->where('tp.user_id', $id);
        $this->db->order_by('nl.id', 'desc');
        $result = $this->db->get();
        if (!$result) {
           return null;
        }
        $result = $result->result_array();
        // $result->free_result();
        return $result;
    }


   # Cập nhật những người đã xem
    function update_notice($li){
        foreach ($li as $key => $value) {
            $value['seens'] = json_encode($value['seens']);
            $data['seens'] = $value['seens'];
            $this->db->where('id', $value['id']);
            $this->db->update('phppos_task_log_notice', $data);
        }
    }
}