<?php
include_once ('BizNestedTemplate.php');

class BizTaskTemplate extends BizNestedTemplate
{

    protected $_table = 'task_template';

    protected $_id_admin = null;

    protected $_task_permission = null;

    protected $_fields = array();

    protected $danhmuc = [
        1 => 'Tiếp cận',
        2 => 'Triển khai',
        3 => 'Nghiệm thu',
        4 => 'Thanh toán'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->library('MY_System_Info');
        $info = new MY_System_Info();
        $user_info = $info->getInfo();
        
        $this->_id_admin = $user_info['id'];
        $this->_task_permission = $user_info['task_permission'];
        
        $this->_fields = array(
            'name' => 't.name',
            'modified' => 't.modified',
            'username' => 'e.username'
        );
    }

    public function getDanhMuc()
    {
        return $this->danhmuc;
    }

    public function countItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'template-list') {
            $this->db->select('COUNT(t.id) AS totalItem')
                ->from($this->_table . ' AS t')
                ->where('t.parent = 0');
            
            if (! empty($arrParam['keywords'])) {
                $this->db->where('t.name LIKE \'' . $arrParam['keywords'] . '\'');
            }
            
            $query = $this->db->get();
            
            $result = $query->row()->totalItem;
            
            $this->db->flush_cache();
        }
        
        return $result;
    }

    public function saveItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'add') {
            if ($arrParam['parent'] == 0) {
                $data['name'] = stripslashes($arrParam['name']);
                $data['danhmuc'] = ! empty($arrParam) ? $arrParam['danhmuc'] : null;
                $data['duration'] = ! empty($arrParam['duration']) ? $arrParam['duration'] : 0;
                $data['tile'] = ! empty($arrParam['tile']) ? $arrParam['tile'] : 0;
                
                $data['xemlist'] = ! empty($arrParam['xemlist']) ? $arrParam['xemlist'] : '';
                $data['approvelist'] = ! empty($arrParam['approvelist']) ? $arrParam['approvelist'] : '';
                $data['implementlist'] = ! empty($arrParam['implementlist']) ? $arrParam['implementlist'] : '';
                
                $data['parent'] = $arrParam['parent'];
                $data['lft'] = 0;
                $data['rgt'] = 1;
                $data['template_id'] = 0;
                $data['created'] = @date("Y-m-d H:i:s");
                $data['created_by'] = $this->_id_admin;
                $data['modified'] = @date("Y-m-d H:i:s");
                $data['modified_by'] = $this->_id_admin;
                
                $this->db->insert($this->_table, $data);
                $lastId = $this->db->insert_id();
                if ($lastId > 0) {
                    $this->db->where("id", $lastId);
                    $data['template_id'] = $lastId;
                    
                    $this->db->update($this->_table, $data);
                }
            } else {
                $data['name'] = stripslashes($arrParam['name']);
                $data['danhmuc'] = ! empty($arrParam) ? $arrParam['danhmuc'] : null;
                $data['duration'] = ! empty($arrParam['duration']) ? $arrParam['duration'] : 0;
                $data['tile'] = ! empty($arrParam['tile']) ? $arrParam['tile'] : 0;
                
                $data['xemlist'] = ! empty($arrParam['xemlist']) ? $arrParam['xemlist'] : '';
                $data['approvelist'] = ! empty($arrParam['approvelist']) ? $arrParam['approvelist'] : '';
                $data['implementlist'] = ! empty($arrParam['implementlist']) ? $arrParam['implementlist'] : '';
                
                $data['parent'] = $arrParam['parent'];
                $data['template_id'] = $arrParam['template_id'];
                $data['created'] = @date("Y-m-d H:i:s");
                $data['created_by'] = $this->_id_admin;
                $data['modified'] = @date("Y-m-d H:i:s");
                $data['modified_by'] = $this->_id_admin;
                
                $lastId = $this->insertNode($data, $arrParam['parent'], $arrParam['template_id']);
            }
        } elseif ($options['task'] == 'edit') {
            $lastId = $arrParam['id'];
            $this->db->where("id", $arrParam['id']);
            
            $data['name'] = stripslashes($arrParam['name']);
            $data['danhmuc'] = ! empty($arrParam) ? $arrParam['danhmuc'] : null;
            if (! empty($arrParam['duration'])) {
                $data['duration'] = $arrParam['duration'];
            }
            
            if (! empty($arrParam['tile'])) {
                $data['tile'] = $arrParam['tile'];
            }
            
            if (! empty($arrParam['xemlist'])) {
                $data['xemlist'] = $arrParam['xemlist'];
            }
            
            if (! empty($arrParam['approvelist'])) {
                $data['approvelist'] = $arrParam['approvelist'];
            }
            
            if (! empty($arrParam['implementlist'])) {
                $data['implementlist'] = $arrParam['implementlist'];
            }
            
            $data['lft'] = 0;
            $data['rgt'] = 1;
            $data['modified'] = @date("Y-m-d H:i:s");
            $data['modified_by'] = $this->_id_admin;
            
            $this->db->update($this->_table, $data);
            $this->db->flush_cache();
        }
        return $lastId;
    }

    public function itemSelectbox($arrParam = null, $options = null)
    {
        if ($options == null) {
            $this->db->select('t.id, t.name')
                ->from($this->_table . ' AS t')
                ->where('t.parent = 0')
                ->where('t.is_template = 1')
                ->order_by('t.id', 'DESC');
            
            $query = $this->db->get();
            
            $result = $query->result_array();
            $this->db->flush_cache();
        }
        return $result;
    }

    public function listItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'template-list') {
            $paginator = $arrParam['paginator'];
            $this->db->select("DATE_FORMAT(t.modified, '%d/%m/%Y %H:%i:%s') as modified", FALSE);
            $this->db->select('e.username as created_by');
            $this->db->select('t.id, t.name, e.username')
                ->from($this->_table . ' AS t')
                ->join('employees AS e', 'e.id = t.modified_by', 'left')
                ->where('t.parent = 0');
            
            $page = (empty($arrParam['start'])) ? 1 : $arrParam['start'];
            $this->db->limit($paginator['per_page'], ($page - 1) * $paginator['per_page']);
            
            if (! empty($arrParam['keywords'])) {
                $this->db->like('t.name', $arrParam['keywords'], 'BOTH');
            }
            
            if (! empty($arrParam['col']) && ! empty($arrParam['order'])) {
                $col = $this->_fields[$arrParam['col']];
                $order = $arrParam['order'];
                
                $this->db->order_by($col, $order);
            } else {
                $this->db->order_by('t.id', 'DESC');
            }
            
            $query = $this->db->get();
            
            $result = $query->result_array();
            $this->db->flush_cache();
        } elseif ($options['task'] == 'by-template') {
            $this->db->select('t.*')
                ->from($this->_table . ' AS t')
                ->where('t.parent != 0')
                ->where('t.template_id', $arrParam['template_id'])
                ->order_by('t.lft', 'ASC');
            
            $query = $this->db->get();
            
            $result = $query->result_array();
            $this->db->flush_cache();
        }
        
        return $result;
    }

    public function getItem($arrParam = null, $options = null)
    {
        if ($options == null) {
            $result = $this->getItem($arrParam, array(
                'task' => 'information'
            ));
            if (! empty($result)) {
                $lft = $result['lft'];
                $rgt = $result['rgt'];
                $template_id = $result['id'];
                $this->db->select('t.*')
                    ->from($this->_table . ' AS t')
                    ->where('t.lft > ' . $lft . ' AND rgt < ' . $rgt)
                    ->where('t.template_id', $template_id)
                    ->order_by('t.lft', 'ASC');
                
                $query = $this->db->get();
                
                $result['tasks'] = $query->result_array();
                $this->db->flush_cache();
            }
        } elseif ($options['task'] == 'information') {
            $this->db->select('t.*')
                ->from($this->_table . ' AS t')
                ->where('t.id', $arrParam['id']);
            
            $query = $this->db->get();
            
            $result = $query->row_array();
            $this->db->flush_cache();
        }
        
        return $result;
    }

    public function deleteItem($arrParam = null, $options = null)
    {
        if ($options['task'] == 'delete') {
            $this->db->where('template_id IN (' . implode(', ', $arrParam['template_ids']) . ')');
            $this->db->delete($this->_table);
            $this->db->flush_cache();
        } elseif ($options['task'] == 'delete-task-of-template') {
            $this->db->where('template_id = ' . (int) $arrParam['id'] . ' AND id != ' . (int) $arrParam['id']);
            $this->db->delete($this->_table);
            $this->db->flush_cache();
        }
    }
    
    public function getDurationOfProject($templateId = '') {
        $this->db->select('SUM(t.duration) as project_duration')
            ->from($this->_table . ' AS t')
            ->where('t.level', 1)
            ->where('t.template_id', $templateId);
        echo $this->db->get_compiled_select(); die;
        $query = $this->db->get();
        return !empty($query) ? $query->result_array() : [];
    }
    
    public function getDurationOfTaskTemplate($taskTempId = '') {
        $this->db->select('t.*')
        ->from($this->_table . ' AS t')
        ->where('t.id', $taskTempId);
        $query = $this->db->get();
        return !empty($query) ? $query->result_array() : [];	
    }
    
    public function clone_template($task_template_id, $name) {
    	$this->db->from('task_template');
    	$this->db->where('id', $task_template_id);
    	$result = $this->db->get()->result_array();
    	$new_template_id = 0;
    	if (!empty($result)) {
    		$template = $result[0];
    		$old_template_id = $template['id'];
    		// var_dump($template['id']);die();
    		//$template['name'] = $name;
    		$template['is_template'] = 0;
    		$template['template_id'] = 0;
    		$template['parent'] = 0;
    		
    		unset($template['id']);
    		$this->db->insert('task_template', $template);
    		$new_template_id = $this->db->insert_id();
    		$this->db->update('task_template', array(
    		    'template_id' => $new_template_id
    		), array(
    		    'id' => $new_template_id
    		));
    		
    		self::update_task_recursive($new_template_id, $new_template_id, $old_template_id, $old_template_id);
    	}
    	return $new_template_id;
    }
    
    public function update_task_recursive($new_template_id, $new_parent, $old_parent, $old_template_id) {
        $this->db->from('task_template');
        $this->db->where('template_id', $old_template_id);
        $this->db->where('parent', $old_parent);
        $result = $this->db->get()->result_array();
        if (!empty($result)) {
            foreach ($result as $task) {
                $tmp = $task;
                $tmp['parent'] = $new_parent;
                $tmp['template_id'] = $new_template_id;
                $tmp['is_template'] = 0;
                unset($tmp['id']);
                $this->db->insert('task_template', $tmp);
                $template_id = $this->db->insert_id();
                
                self::update_task_recursive($new_template_id, $template_id, $task['id'], $old_template_id);
            }
        }
    }

    function get_duration_template($id)
    {
        $this->db->select('t.duration');
        $this->db->from('phppos_task_template as t');
        $this->db->where('t.template_id', $id);
        $this->db->where('t.rgt = (t.lft +1)');
        return $this->db->get()->result_array();


    }


}