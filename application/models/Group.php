<?php

class Group extends CI_Model
{
    /*
    Determines if a given group_id is an group
    */
    function exists($group_id)
    {
        $this->db->from('groups');
        $this->db->where('groups.group_id', $group_id);
        $query = $this->db->get();

        return ($query->num_rows() == 1);
    }

    function group_name_exists($name)
    {
        $this->db->from('groups');
        $this->db->where('groups.name', $name);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row()->name;
        }
    }

    /*
    Returns all the groups
    */
    // function get_all($limit = 10000, $offset = 0, $col = 'name', $order = 'asc')
    // {
    //     $order_by = '';
    //     if (!$this->config->item('speed_up_search_queries')) {
    //         $order_by = "ORDER BY " . $col . " " . $order;
    //     }
    //     $groups = $this->db->dbprefix('groups');
    //     $data = $this->db->query("SELECT *
				// 		FROM " . $groups . "
				// 		WHERE deleted =0 $order_by
				// 		LIMIT  " . $offset . "," . $limit);
    //     return $data;
    // }



    function get_all($limit = 10000, $offset = 0, $col = 'name', $order = 'asc')
    {
        $order_by = '';
        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }
      $this->db->select('phppos_groups.group_id, phppos_groups.name as name,phppos_groups.description, GROUP_CONCAT(phppos_locations.name SEPARATOR " , ") as location_name,  GROUP_CONCAT(phppos_people.first_name SEPARATOR " , " ) as employee_name FROM phppos_groups');
      $this->db->join('phppos_employees', 'phppos_groups.group_id = phppos_employees.group_id', 'left');
      $this->db->join('phppos_people', 'phppos_people.person_id = phppos_employees.person_id', 'left');
      $this->db->join('phppos_employees_locations', 'phppos_employees.person_id = phppos_employees_locations.employee_id', 'left');
      $this->db->join('phppos_locations', 'phppos_employees_locations.location_id = phppos_locations.location_id', 'left');
      $this->db->where('phppos_groups.deleted', 0);
      $this->db->where('phppos_employees.deleted', 0);
      $this->db->order_by($col, $order);
      $this->db->limit($limit,$offset);
      $this->db->group_by('name');
      $data = $this->db->get();
        return $data;
    }
    function get_all_group()
    {
        $order_by = '';
        if (!$this->config->item('speed_up_search_queries')) {
            $order_by = "ORDER BY " . $col . " " . $order;
        }
      $this->db->select('phppos_groups.group_id, phppos_groups.name as name,phppos_groups.description, GROUP_CONCAT(phppos_locations.name SEPARATOR " , ") as location_name,  GROUP_CONCAT(phppos_people.first_name SEPARATOR " , " ) as employee_name FROM phppos_groups');
      $this->db->join('phppos_employees', 'phppos_groups.group_id = phppos_employees.group_id', 'left');
      $this->db->join('phppos_people', 'phppos_people.person_id = phppos_employees.person_id', 'left');
      $this->db->join('phppos_employees_locations', 'phppos_employees.person_id = phppos_employees_locations.employee_id', 'left');
      $this->db->join('phppos_locations', 'phppos_employees_locations.location_id = phppos_locations.location_id', 'left');
      $this->db->where('phppos_groups.deleted', 0);
      // $this->db->where('phppos_employees.deleted', 0);
      $this->db->order_by($col, $order);
      $this->db->limit($limit,$offset);
      $this->db->group_by('name');
      $data = $this->db->get();
        return $data;
    }
    function count_all()
    {
        $this->db->from('groups');
        $this->db->where('deleted', 0);
        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular group
    */
    function get_info($group_id, $can_cache = TRUE)
    {
        if ($can_cache) {
            static $cache = array();
            if (isset($cache[$group_id])) {
                return $cache[$group_id];
            }
        } else {
            $cache = array();
        }
        $this->db->from('groups');
        $this->db->where('groups.group_id', $group_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $cache[$group_id] = $query->row();
            return $cache[$group_id];
        } else {
            /* Get empty base parent object */
            $group_obj = new stdClass();

            /* Get all the fields from group table */
            $fields = $this->db->list_fields('groups');

            /* Append those fields to base parent object, we have a complete empty object */
            foreach ($fields as $field) {
                $group_obj->$field = '';
            }

            return $group_obj;
        }
    }

    /*
    Gets information about multiple groups
    */
    function get_multiple_info($group_ids)
    {
        $this->db->from('groups');
        $this->db->where_in('groups.group_id', $group_ids);
        $this->db->order_by("name", "asc");
        return $this->db->get();
    }

    /*
    Inserts or updates an group
    */
    function save($data, $group_id = false, $permission_data = null, $permission_action_data = null)
    {
        $success = false;

        if (!$group_id) {
            if ($this->db->insert('groups', $data)) {

                $data['group_id'] = $this->db->insert_id();

                // We have either inserted or updated a new employee, now lets set permissions.
                if ($data['group_id'] && !empty($permission_data) && !empty($permission_action_data)) {

                    // Run these queries as a transaction, we want to make sure we do all or nothing
                    $this->db->trans_start();

                    // First lets clear out any permissions the employee currently has.
                    $success = $this->db->delete('group_permissions', array('group_id' => $data['group_id']));

                    // Now insert the new permissions
                    if ($success) {
                        foreach ($permission_data as $allowed_module) {
                            $this->db->insert('group_permissions', array('module_id' => $allowed_module, 'group_id' => $data['group_id']));
                        }
                    }

                    // First lets clear out any permissions actions the employee currently has.
                    $success = $this->db->delete('group_permissions_actions', array('group_id' => $data['group_id']));

                    // Now insert the new permissions actions
                    if ($success) {
                        foreach ($permission_action_data as $permission_action) {
                            list($module, $action) = explode('|', $permission_action);
							// if($action == 'view_scope_all' || $action == 'view_scope_owner' || $action == 'view_scope_location')
							// {
							// $this->db->insert('group_permissions_actions', array('module_id' => 'all', 'action_id' => $action, 'group_id' => $data['group_id']));
							// }
                            $this->db->insert('group_permissions_actions', array('module_id' => $module, 'action_id' => $action, 'group_id' => $data['group_id']));
                        }
                    }

                    $this->db->trans_complete();
                }

                return $data['group_id'];
            }
            return $success;
        }

        /* Update Group */
        $this->db->where('group_id', $group_id);
        $success = $this->db->update('groups', $data);

        if ($success && !empty($permission_data) && !empty($permission_action_data)) {

            // Run these queries as a transaction, we want to make sure we do all or nothing
            $this->db->trans_start();

            // First lets clear out any permissions the employee currently has.
            $success = $this->db->delete('group_permissions', array('group_id' => $group_id));

            // Now insert the new permissions
            if ($success) {
                foreach ($permission_data as $allowed_module) {
                    $this->db->insert('group_permissions', array('module_id' => $allowed_module, 'group_id' => $group_id));
                }
            }

            // First lets clear out any permissions actions the employee currently has.
            $success = $this->db->delete('group_permissions_actions', array('group_id' => $group_id));

            // Now insert the new permissions actions
            if ($success) {
                foreach ($permission_action_data as $permission_action) {
                    list($module, $action) = explode('|', $permission_action);
										// if($action == 'view_scope_all' || $action == 'view_scope_owner' || $action == 'view_scope_location')
										// {
										// 	$this->db->insert('group_permissions_actions', array('module_id' => 'all', 'action_id' => $action, 'group_id' => $group_id));
										// }
                    $this->db->insert('group_permissions_actions', array('module_id' => $module, 'action_id' => $action, 'group_id' => $group_id));
                }
            }

            $this->db->trans_complete();
        }

        return $success;
    }

    /*
    Deletes one group
    */
    function delete($group_id)
    {
        $success = false;

        //Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->trans_start();

        //Delete permissions
        if ($this->db->delete('permissions', array('group_id' => $group_id)) && $this->db->delete('permissions_actions', array('group_id' => $group_id))) {
            $this->db->where('group_id', $group_id);
            $success = $this->db->update('groups', array('deleted' => 1));
        }
        $this->db->trans_complete();
        return $success;
    }

    /*
    Deletes a list of groups
    */
    function delete_list($group_ids)
    {
        $this->db->where_in('group_id', $group_ids);
        $success = $this->db->update('groups', array('deleted' => 1));
        return $success;
    }

    function check_duplicate($term)
    {
        $this->db->from('groups');
        $this->db->where('deleted', 0);
        $this->db->where("CONCAT(name) = " . $this->db->escape($term));
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return true;
        }
    }

    /*
	Get search suggestions to find groups
	*/
    function get_search_suggestions($search, $limit = 25)
    {
        if (!trim($search)) {
            return array();
        }

        $suggestions = array();

        if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
            $this->db->select("groups.*, MATCH (`name`, `description`) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE) as rel", false);
            $this->db->from('groups');
            $this->db->where("MATCH (`name`, `description`) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)", NULL, FALSE);
            $this->db->where('deleted', 0);
            $this->db->limit($limit);
            $this->db->order_by('rel ASC');
            $by_number = $this->db->get();

            $temp_suggestions = array();
            foreach ($by_number->result() as $row) {
                $data = array(
                    'name' => H($row->name),
                    'description' => H($row->description),
                    'avatar' => base_url() . "assets/img/user.png"
                );

                $temp_suggestions[$row->group_id] = $data;
            }

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['description']);
            }
        } else {
            $this->db->from('groups');
            $this->db->like('name', $search);
            $this->db->or_like('description', $search);
            $this->db->where('deleted', 0);
            $this->db->limit($limit);
            $by_number = $this->db->get();

            $temp_suggestions = array();
            foreach ($by_number->result() as $row) {
                $data = array(
                    'name' => H($row->name),
                    'description' => H($row->description),
                    'avatar' => base_url() . "assets/img/user.png"
                );

                $temp_suggestions[$row->group_id] = $data;
            }

            $this->load->helper('array');
            uasort($temp_suggestions, 'sort_assoc_array_by_name');

            foreach ($temp_suggestions as $key => $value) {
                $suggestions[] = array('value' => $key, 'label' => $value['name'], 'avatar' => $value['avatar'], 'subtitle' => $value['description']);
            }

        }
        //only return $limit suggestions
        if (count($suggestions > $limit)) {
            $suggestions = array_slice($suggestions, 0, $limit);
        }
        return $suggestions;
    }

    /*
    Preform a search on groups
    */
    function search($search, $limit = 20, $offset = 0, $column = 'name', $orderby = 'asc')
    {
        $this->db->from('groups');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE)) and " . $this->db->dbprefix('groups') . ".deleted=0", NULL, FALSE);
            } else {
                $this->db->where("(name LIKE '%" . $this->db->escape_like_str($search) . "%' or
				description LIKE '%" . $this->db->escape_like_str($search) . "%') and deleted=0");
            }
        } else {
            $this->db->where('deleted', 0);
        }
        if (!$this->config->item('speed_up_search_queries')) {
            $this->db->order_by($column, $orderby);
        }
        $this->db->limit($limit);
        $this->db->offset($offset);
        return $this->db->get();
    }

    function search_count_all($search, $limit = 10000)
    {
        $this->db->from('groups');
        if ($search) {
            if ($this->config->item('supports_full_text') && !$this->config->item('legacy_search_method')) {
                $this->db->where("(MATCH (name, description) AGAINST (" . $this->db->escape(escape_full_text_boolean_search($search) . '*') . " IN BOOLEAN MODE" . ")) and " . $this->db->dbprefix('groups') . ".deleted=0", NULL, FALSE);
            } else {
                $this->db->where("(name LIKE '%" . $this->db->escape_like_str($search) . "%' or
				description LIKE '%" . $this->db->escape_like_str($search) . "%') and deleted=0");
            }
        } else {
            $this->db->where('deleted', 0);
        }
        $this->db->limit($limit);
        $result = $this->db->get();
        return $result->num_rows();
    }

    /*
	Determins whether the group specified employee has access the specific module.
	*/
    function has_module_permission($module_id, $group_id)
    {
        //if no module_id is null, allow access
        if ($module_id == null) {
            return true;
        }

        static $cache;

        if (isset($cache[$module_id . '|' . $group_id])) {
            return $cache[$module_id . '|' . $group_id];
        }

        $query = $this->db->get_where('group_permissions', array('group_id' => $group_id, 'module_id' => $module_id), 1);
        $cache[$module_id . '|' . $group_id] = $query->num_rows() == 1;
        return $cache[$module_id . '|' . $group_id];
    }

    function has_module_action_permission($module_id, $action_id, $group_id)
    {
        //if no module_id is null, allow access
        if ($module_id == null) {
            return true;
        }

        static $cache;

        if (isset($cache[$module_id . '|' . $action_id . '|' . $group_id])) {
            return $cache[$module_id . '|' . $action_id . '|' . $group_id];
        }


        $query = $this->db->get_where('group_permissions_actions', array('group_id' => $group_id, 'module_id' => $module_id, 'action_id' => $action_id), 1);
        $cache[$module_id . '|' . $action_id . '|' . $group_id] = $query->num_rows() == 1;
        return $cache[$module_id . '|' . $action_id . '|' . $group_id];
    }

    function cleanup()
    {
        $group_data = array('name' => null, 'description' => null);
        $this->db->where('deleted', 1);
        return $this->db->update('groups', $group_data);
    }

    function get_items($arrParams = null, $options = null) {
        if($options == null) {
            $this->db->select("g.*")
                    ->from('groups AS g')
                    ->where('g.group_id IN ('.implode(',', $arrParams['cid']).')');

            if($arrParams['include_deleted'] == true)
                $this->db->where('g.deleted IN (0,1)');
            else
                $this->db->where('g.deleted', 0);

            $query = $this->db->get();
            $result_tmp = $query->result_array();

            $this->db->flush_cache();
            $result = array();
            if(!empty($result_tmp)) {
                foreach($result_tmp as $val)
                    $result[$val['group_id']] = $val;
            }
        }

        return $result;
    }

    function item_select_box($arrParams = null, $options = null) {
        if($options == null)  {
            $this->db->select("g.group_id, g.name")
                    ->from('groups AS g')
                    ->where('g.deleted', 0)
                    ->order_by('g.group_id', 'ASC');

            if(!empty($arrParams['cid'])) {
                $this->db->where('g.group_id IN ('.implode(',', $arrParams['cid']).')');
            }

            if(!empty($arrParams['not_cid'])) {
                $this->db->where('g.group_id NOT IN ('.implode(',', $arrParams['not_cid']).')');
            }

            $query = $this->db->get();
            $result_tmp = $query->result_array();

            $this->db->flush_cache();
            $result[-1] = 'Chọn nhóm';
            if(!empty($result_tmp)) {
                foreach($result_tmp as $val) {
                    $result[$val['group_id']] = $val['name'];
                }
            }
        }

        return $result;
    }

    function get_item($arrParams = null, $options = null) {
        $this->db->select("g.group_id, g.name")
                 ->from('groups AS g')
                 ->where('g.group_id', $arrParams['id']);

        if($arrParams['all'] != true)
            $this->db->where('g.deleted', 0);

        $query = $this->db->get();
        $result = $query->row_array();

        $this->db->flush_cache();

        return $result;
    }

    function set_view_type($id){

        $list = array('contracts','customers','home','kpi_person','reports','sales','tasks','receivings');
        if($id==1){
            foreach ($list as $key => $value) {
                $this->update_permission($id,$value);
            }
            
        }
        if($id==0){
            $where = array('group_id'=>1,'action_id'=>'view_scope_all');
            $this->db->where($where);
            $this->db->delete('phppos_group_permissions_actions');

            $where2 = array('action_id'=>'view_scope_all','person_id'=>$this->Employee->get_logged_in_employee_info()->person_id);
            $this->db->where($where2);
            $this->db->delete('phppos_permissions_actions');
        }
    }


    function update_permission($id,$value){
        $where = array('module_id'=>$value,'group_id'=>1,'action_id'=>'view_scope_all');
        $this->db->where($where);
        $result = $this->db->get('phppos_group_permissions_actions')->row();
        if(empty($result)){
            $this->db->insert('phppos_group_permissions_actions', $where);
        }
    }

    function check_view(){
        $group_id = $this->Employee->get_logged_in_employee_info()->group_id;
        if($group_id==1){
            $this->db->where(array('action_id'=>'view_scope_all','group_id'=>1));
            $gr = $this->db->get('phppos_group_permissions_actions')->row_array();
            if($gr){
                $result['name'] = "Hệ thống";
                $result['id'] =0;
            }
            else{
                $result['name'] = "Khu vực";
                $result['id'] =1;
            }
            return $result;
        }else{
            return false;
        }
    }
}

?>
