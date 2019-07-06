<?php
require_once (APPPATH . "models/Employee.php");
class BizEmployee extends Employee
{
    function getByPeopleId($peopleId = '') {
        $this->db->from('employees');
        $this->db->where_in('employees.person_id', $peopleId);
        $result = $this->db->get();
        $result = !empty($result) ? $result->result_array() : [];
        return !empty($result) ? reset($result) : [];
    }
    
	function getEmployeesByCurrentLocation()
	{
		$this->db->select('employees.*');
		$this->db->from('employees');
		$this->db->join('employees_locations', 'employees_locations.employee_id = employees.person_id');
		$this->db->where('location_id', $this->get_logged_in_employee_current_location_id());
        $this->db->where('deleted', 0);
        // $this->db->group_by('employees.id');
		return $this->db->get()->result_array();
	}

    function item_select_box($arrParams = null, $options = null) {
        if($options['task'] == null) {
            $this->db->select("p.first_name, p.last_name, e.person_id")
                    ->from('employees AS e')
                    ->join('people AS p', 'e.person_id = p.person_id', 'left')
                    ->where('e.deleted', 0)
                    ->order_by('e.id', 'ASC');

            $query = $this->db->get();

            $result_tmp = $query->result_array();
            $this->db->flush_cache();
            $result[-1] = '-- Chọn nhân viên --';
            if(!empty($result_tmp)) {
                foreach($result_tmp as $val)
                    $result[$val['person_id']] = $val['first_name'] . ' ' . $val['last_name'];
            }
        }

        return $result;
    }

    function selectBoxById($arrParams = null, $options = null)
    {
        $lo = $this->get_logged_in_employee_current_location_id();
        $this->db->select("e.id, p.first_name, p.last_name, e.person_id")
            ->from('employees AS e')
            ->join('people AS p', 'e.person_id = p.person_id', 'left')
            ->join('employees_locations as el','el.employee_id = e.person_id')
            ->where('el.location_id',$lo)
            ->where('e.deleted', 0)
            ->order_by('e.id', 'ASC');
        $query = $this->db->get();
        $result_tmp = !empty($query) ? $query->result_array(): [];
        $this->db->flush_cache();
        $result[-1] = '-- Chọn nhân viên --';
        // Check Permissions
        $permissions = (new MY_System_Info())->get_permissions('tasks');
        foreach ($permissions as $permission) {
            if (in_array($permission, ['view_scope_all', 'view_scope_location'])) {
                $result[0] = 'Tất cả';
            }
        }
        if (! empty($result_tmp)) {
            foreach ($result_tmp as $val)
                $result[$val['id']] = $val['first_name'] . ' ' . $val['last_name'];
        }
        return $result;
    }

    function get_items($arrParams = array(), $options = null) {
        if($options == null) {
            if(isset($arrParams['location_id']) && $arrParams['location_id'] > 0 && $arrParams['group_id'] > 0) {
                $location_model = $this->model_load_model('Location');
                $emp_ids = $location_model->get_employee_ids($arrParams, array('task'=>'location-group'));
            }

            $this->db->select("p.first_name, p.last_name, p.image_id, p.email, e.person_id")
                    ->from('employees AS e')
                    ->join('people AS p', 'e.person_id = p.person_id', 'left')
                    ->where('e.deleted', 0)
                    ->order_by('e.id', 'ASC')
                    ->limit(10);

            if(!empty($arrParams['keywords'])) {
                $keywords = trim($arrParams['keywords']);
                $this->db->where("CONCAT_WS(p.first_name, ' ', p.last_name) LIKE '%$keywords%'");
            }

            if(isset($arrParams['location_id'])&& $arrParams['location_id'] > 0 && $arrParams['group_id'] > 0) {
                if(!empty($emp_ids))
                    $this->db->where('e.person_id IN ('.implode(',', $emp_ids).')');
                else
                    $this->db->where('e.id', -1);
            }

            $query = $this->db->get();

            $result = $query->result_array();

            if(!empty($result)) {
                foreach($result as &$item) {
                    if($item['image_id'] == NULL)
                        $item['image'] = base_url() . 'assets/img/user.png';
                    else
                        $item['image'] = base_url() . 'app_files/view/'.$item['image_id'];
                }
            }
            return $result;
        }
    }

    function get_employee_by_id($id)
    {
        $this->db->select('e.username,p.first_name as name,e.person_id,e.id');
        $this->db->from('phppos_employees as e');
        $this->db->join('phppos_people as p', 'p.person_id = e.person_id', 'left');
        $this->db->where('e.id', $id);
        return $this->db->get()->row_array();
    }
    function get_information($id) {
        $this->db->select("p.first_name, p.last_name, p.phone_number, p.email, p.address_1, p.address_2, e.person_id,e.id")
                ->from('employees AS e')
                ->join('people AS p', 'e.person_id = p.person_id', 'left')
                ->where("e.person_id IN ($id)");

        $query = $this->db->get();

        $result = $query->row_array();
        $this->db->flush_cache();

        return $result;
    }

    function get_info_by_ids($cid) {
        $this->db->select("p.first_name, p.last_name, p.phone_number, p.email, p.address_1, p.address_2, e.person_id")
                ->from('employees AS e')
                ->join('people AS p', 'e.person_id = p.person_id', 'left');
								
			if(is_array($cid))
			{
				$cid = implode(',', $cid);
			}
			if(!empty($cid))
			{
				$this->db->where('e.person_id IN ('.$cid.')');
			}
			$query = $this->db->get();

			$result_tmp = $query->result_array();
			$this->db->flush_cache();

			$result = array();
			if(!empty($result_tmp)) {
					foreach($result_tmp as $val)
							$result[$val['person_id']] = $val;
			}

        return $result;
    }

    function get_employee_location_commission_info($arrParams = null, $options = null) {
        if($options == null) {
            $location_id = $arrParams['location_id'];
            $group_id    = $arrParams['group_id'];
            $employee_id = $arrParams['employee_id'];
            $key = $location_id . '-' . $group_id . '-' . $employee_id;
            global $employee_location_commission;

            if(!isset($employee_location_commission[$key])) {
                $this->db->select("*")
                         ->from('employee_location_commission')
                         ->where('location_id', $location_id)
                         ->where('group_id', $group_id)
                         ->where('employee_id', $employee_id);

                $query = $this->db->get();

                $result = $query->row_array();
                $this->db->flush_cache();
                if(!empty($result))
                    $employee_location_commission[$key] = $result;
            }else
                $result = $employee_location_commission[$key];
        }

        return $result;
    }

    function get_employee_location_commission($arrParams = null, $options = null) {
        if($options == null) {
            $this->db->select("*")
                     ->from('employee_location_commission');

            if(!empty($arrParams['employee_ids']))
                $this->db->where('employee_id IN ('.implode(',', $arrParams['employee_ids']).')');

            if($arrParams['location_id'] > 0)
                $this->db->where('location_id', $arrParams['location_id']);

            $query = $this->db->get();

            $result = $query->result_array();
            $this->db->flush_cache();
        }

        return $result;
    }

    function get_group_id_from_location_commision($arrParams = null, $options = null) {
        $this->db->select("group_id")
                 ->from('employee_location_commission');

        if($arrParams['location_id']>0)
            $this->db->where('location_id', $arrParams['location_id']);

        if($arrParams['employee_id']>0)
            $this->db->where('employee_id', $arrParams['employee_id']);

        $query = $this->db->get();

        $result_tmp = $query->result_array();
        $this->db->flush_cache();
        $result = array();
        if(!empty($result_tmp)) {
            foreach($result_tmp as $val)
                $result[] = $val['group_id'];
        }

        return $result;
    }

    function get_location_group_commission($id, $options = null) {
        $this->db->select("l.location_id, l.name")
                 ->distinct()
                 ->from('location_group_employees AS lg')
                 ->join('locations AS l', 'lg.location_id = l.location_id')
                 ->where("CONCAT(',',lg.employees,',') LIKE '%,$id,%'")
                 ->where('l.deleted', 0)
                 ->order_by('lg.ord', 'ASC');

        $query = $this->db->get();

        $location_list = $query->result_array();
        $this->db->flush_cache();
        $result = array();
        if(!empty($location_list)) {
            $location_ids = array();
            foreach($location_list as $location) {
                $location_ids[] = $location['location_id'];
            }

            $this->db->select("ec.*, g.name AS group_name")
                    ->from('employee_location_commission AS ec')
                    ->join('groups AS g', 'ec.group_id = g.group_id')
                    ->where('ec.location_id IN ('.implode(',', $location_ids).')');

            $query = $this->db->get();

            $location_commission_tmp = $query->result_array();
            $this->db->flush_cache();

            $location_commission = array();
            if(!empty($location_commission_tmp)) {
                foreach($location_commission_tmp as $val)
                    $location_commission[$val['location_id']][] = $val;
            }

            foreach($location_list as $location) {
                $tmp = array();
                $tmp['location_id'] = $location['location_id'];
                $tmp['location_name'] = $location['name'];
                $tmp['commission'] = array();
                if(isset($location_commission[$location['location_id']])) {
                    $tmp['commission'] = $location_commission[$location['location_id']];
                }

                $result[] = $tmp;
            }
        }
        return $result;
    }

    public function delete_employee_location_commission($arrParams = null, $options = null) {
        if($options['task'] == null) {
            foreach($arrParams['group_emp_commission'] as $val) {
                $location_id = $val['location_id'];
                $group_id    = $val['group_id'];
                $employee_id = $val['employee_id'];
            }
            $this->db->where("location_id = $location_id AND group_id = $group_id AND employee_id = $employee_id");
            $this->db->delete('employee_location_commission');
        }

        $this->db->flush_cache();
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }


/*
Lấy tên và id của ngạch
@return array
*/
    public function get_ranks()
    {
        $query = $this->db->get('phppos_employees_ranks');
        return $query->result_array();
    }

/*
Lấy thông tin của cấp bậc dựa theo id ngạch
@param int
@return array
*/
    public function get_level_by_rank($id="")
    {   
        $this->db->select();
        if($id!=="")
        {
            $this->db->where('parent_id', $id);
        }
        $this->db->order_by('name', 'ASC');
        
        return  $this->db->get('phppos_employees_level')->result_array();

    }


/*
Lấy thông tin của rank theo parent_id level
@param int
@return int
*/
    public function get_rank_by_level($id="")
    {
        $this->db->select('parent_id');
        $this->db->where('id', $id);
        $result= $this->db->get('phppos_employees_level')->row_array();
       
        return isset($result['parent_id']) ? $result['parent_id']: "";
    }

    public function get_level_ranks()
    {
        $this->db->select("phppos_employees_ranks.id,phppos_employees_ranks.name as rank_name,phppos_employees_level.name as level_name,phppos_employees_level.parent_id,phppos_employees_level.salary");
        $this->db->from('phppos_employees_ranks');
        $this->db->join('phppos_employees_level', 'phppos_employees_ranks.id = phppos_employees_level.parent_id');
        $this->db->order_by('rank_name ASC, level_name ASC');
        return $this->db->get()->result_array();
    }



  /*
    lấy thông tin của level theo id
    @param int
    @return array thông tin của level hoặc false
    */

    public function get_level_info($id)
    {
        $this->db->where('id', $id);
        if ($this->db->get('phppos_employees_level')->row_array())
        {

           return $this->db->get('phppos_employees_level')->row_array();
        }
        else 
        {
        return false;
        }
    }



    public function level_save($id,$data="")
    {   
        // echo "<pre>";
        // var_dump($data);die();
        $this->db->where('parent_id', $id);
        $this->db->delete('phppos_employees_level');

       foreach ($data as $key => $value) 
       {

           if ($value['id'])
            {
           
              $this->db->insert('phppos_employees_level',
                array(
                'id' => $value['id'],
                'name' => $value['name'],
                'salary' => floatval($value['salary']),
                'parent_id'=>$value['parent_id']));
            }
            else
            {
                $this->db->insert('phppos_employees_level', array( 
                'name' => $value['name'],
                'salary' => $value['salary'],
                'parent_id'=>$value['parent_id']));
            }
       }
            return true;
    }



  
    # Lấy thông tin của employees
    #Trả về mảng thông tin của employee

     function get_employees($search="",$rank="",$level="",$group="",$hire_date="",$order="id",$order_by="desc",$limit="",$offset="", $arrParam=null)
    {
        $view = "view_scope_location";
       if($this->has_module_action_permission('employees','view_scope_all',$this->get_logged_in_employee_info()->person_id)){
        $view="view_scope_all";
       }
        $this->db->select('phppos_employees.person_id AS id,phppos_employees.id as employee_id, phppos_people.first_name,phppos_people.email, 
        phppos_people.phone_number,phppos_employees.hire_date,phppos_people.image_id,
        phppos_employees.username,phppos_employees_level.name AS level,phppos_employees_ranks.name AS rank,
        phppos_groups.name AS group_name, GROUP_CONCAT(phppos_locations.name SEPARATOR " - " ) AS location_name');
        $this->db->from('phppos_employees');
        $this->db->join('phppos_people', 'phppos_employees.person_id = phppos_people.person_id', 'left');
        $this->db->join('phppos_employees_level', 'phppos_employees.level_id = phppos_employees_level.id', 'left');
        $this->db->join('phppos_employees_ranks', 'phppos_employees_level.parent_id = phppos_employees_ranks.id', 'left');
        $this->db->join('phppos_groups', 'phppos_employees.group_id = phppos_groups.group_id', 'left');
        $this->db->join('phppos_employees_locations', 'phppos_employees.person_id = phppos_employees_locations.employee_id');
        $this->db->join('phppos_locations', 'phppos_employees_locations.location_id = phppos_locations.location_id', 'left');
        $this->db->where('phppos_employees.deleted', 0);
        $this->db->where('phppos_locations.deleted', 0);
        if($rank!=="")
        {
            $this->db->where('phppos_employees_ranks.id', $rank);
        }
        if($level!=="")
        {
            $this->db->where('phppos_employees.level_id', $level);
        }
         if($group!=="")
        {
            $this->db->where('phppos_employees.group_id', $group);
        }
        if($view == "view_scope_location"){
            $this->db->where('phppos_employees_locations.location_id', $this->get_logged_in_employee_current_location_id());
        }
        if($hire_date!=="")
        {
                $hire = explode("-",$hire_date);
                $this->db->where("phppos_employees.hire_date < DATE_SUB(IF(termination_date,termination_date,NOW()),INTERVAL ".$hire[0]." YEAR )");
                $this->db->where("phppos_employees.hire_date > DATE_SUB(IF(termination_date,termination_date,NOW()),INTERVAL ".$hire[1]." YEAR)");
        }
        if($search!=="")
        {
            $this->db->like('phppos_people.first_name', $search);
            $this->db->or_like('phppos_locations.name', $search);
            $this->db->or_like('phppos_people.email', $search);
            $this->db->or_like('phppos_groups.name', $search);
            $this->db->or_like('phppos_people.phone_number', $search);
        }
     
        $this->db->group_by('id');
        $this->db->order_by($order, $order_by);
        if($limit !=="" && $offset !=="")
        {
        $this->db->limit($limit,$offset);
        }
        if (!empty($arrParam['id_employee'])) {
            $this->db->where('phppos_employees.id',$arrParam['id_employee']);
        }
        $result = $this->db->get()->result_array();
        return $result;

   
    }


#Trả về mảng thông tin của employe theo location
    public function get_list_employees_by_location($location_id =null,$id=null,$arrParam=null)
    {
        $this->db->select('e.person_id,e.group_id,p.first_name as employee_name,l.name,l.location_id,e.id, p.image_id,e.username,p.code,e.department_id,e.employee_number');
        $this->db->from('phppos_employees as e');
        $this->db->join('phppos_people as p', 'e.person_id = p.person_id', 'left');
        $this->db->join('phppos_employees_locations as el', 'e.person_id = el.employee_id', 'left');
        $this->db->join('phppos_locations as l', 'el.location_id = l.location_id');
        if(!empty($location_id))
        {
            $this->db->where('l.location_id', $location_id); 
        }
        
        $this->db->where('e.deleted', 0);
        $this->db->where('l.deleted', 0);

        if(!empty($id))
        {
            $this->db->where('e.id =',$id);
        }

        if (!empty($arrParam['arr_id'])) {
            $this->db->where_in('e.id',$arrParam['arr_id']);
        }

        $this->db->group_by('e.id');
        return $this->db->get()->result_array();



    }



                        #                       DEV 13 - REPORT EMPLOYEE                                    #

                        #Lấy tổng số doanh thu mà nhân viên đã thực hiện theo khoảng thời gian                                                                                #
                        #return array gồm tổng doanh thu theo thời gian, tên                                                                                 #


    public function get_employee_total_revenue($id,$location_id="",$start_date="",$end_date="") 
    {
        $name = $this->get_information($id);
        $start_date = ($start_date == "") ? date("Y-m-d",strtotime(0)) : $start_date;
        $end_date = ($end_date == "") ? date("Y-m-d") : $end_date;
        $this->db->select('phppos_sales.sold_by_employee_id, SUM(phppos_sales_payments.payment_amount) AS total ');
        $this->db->from('phppos_sales');
        $this->db->join('phppos_sales_payments','phppos_sales.sale_id = phppos_sales_payments.sale_id', 'left');
        if($location_id !=="")
        {
         $this->db->where('phppos_sales.location_id', $location_id);
        }
        $this->db->where('phppos_sales.sold_by_employee_id', $id);
        $this->db->where('phppos_sales.sale_time BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
        $this->db->where('phppos_sales.suspended', 0);
        $this->db->group_by('phppos_sales.sold_by_employee_id ');
        $result =  $this->db->get()->row_array();
        $result['first_name'] = $name['first_name'];
        // echo $this->db->last_query();die();
        return $result;

      
    } 

    #Lấy tổng số dự án tham gia của employee theo thời gian
    #return int
    
    public function get_employee_task_number($id,$start_date="",$end_date="")
    {
        $start_date = ($start_date == "") ? date("Y-m-d h:i:s",strtotime(0)) : $start_date;
        $end_date = ($end_date == "") ? date("Y-m-d h:i:s") : $end_date;
        $this->db->select('phppos_task_user_relations.task_id,phppos_employees.person_id, phppos_employees.username, 
phppos_task_user_relations.user_id,phppos_people.first_name,phppos_task_user_relations.created');
        $this->db->from('phppos_task_user_relations');
        $this->db->join('phppos_employees', 'phppos_task_user_relations.user_id = phppos_employees.id', 'left');
        $this->db->join('phppos_people', 'phppos_employees.person_id = phppos_people.person_id', 'left');
        $this->db->where('phppos_employees.person_id', $id);
        $this->db->where('phppos_task_user_relations.created BETWEEN "'.date('Y-m-d H:i:s', strtotime($start_date)). '" and "'. date('Y-m-d H:i:s', strtotime($end_date)).'"');
        $result = $this->db->get()->result_array();
        return count($result);
        
    }


     # Lưu ngạch

    public function save_rank($action,$id,$data)
    {
        if($action=='edit')
        {
            $this->db->where('id', $id);
            if($this->db->update('phppos_employees_ranks', $data))
            {
                return true;       
            }
            
            else
            {
                return false;
            }

        }
    
    if($action=='add')
        {
            if($this->db->insert('phppos_employees_ranks', $data))
            {
            return true;
            }
            else
            {
            return false;
            }
        }


    if($action=='delete')
        {
            
            $this->db->delete('phppos_employees_level', array('parent_id' => $id));
            $this->db->where('id', $id);
            if($this->db->delete('phppos_employees_ranks'))
            {

            return true;
            }
            else
            {
            return false;
            }
        }


    }


    function get_username_by_ids($ids)
    {
        // var_dump($ids);die();
        $this->db->select('e.username');
        $this->db->where_in('e.id', $ids);
        $this->db->where('e.deleted', 0);
        $es = $this->db->get('phppos_employees as e')->result_array();
        // var_dump($e);die();
        foreach ($es as  &$e) {
            $e = $e['username'];
        }
        return (implode(", ", $es));

    }    
    function get_employees_value_contract($id=1,$arrParam=null){
        $this->db->select('SUM(IF(p.vat="published",(p.price / 1.1),p.price)) as value, p.contract_id as contract_id');
        $this->db->from('phppos_contract_payment as p');
        $this->db->where('(p.c_status ="done" OR p.c_status ="liquidated")');
        if (!empty($arrParam['start_date'])) 
        {
        $this->db->where('p.date_payment >=', $arrParam['start_date']);
        }
        if (!empty($arrParam['end_date'])) 
        {
        $this->db->where('p.date_payment <', $arrParam['end_date']);
        }
       
        $this->db->group_by('p.contract_id');
        $sub = $this->db->get_compiled_select();

        $this->db->select('cp.contract_id,tk.kpi_id,cp.value,tk.employee_id,tk.ratio, 
cp.`value` as sub_value ');
        $this->db->from('phppos_task_kpiperson_approve as tk ');
        $this->db->join('contract as c', 'c.project_id = tk.task_id');
        $this->db->join("($sub) as cp" , 'cp.contract_id = c.id');
        $this->db->like('tk.employee_id', '"'.$id.'"');
        $result = $this->db->get()->result_array();
        // echo $this->db->last_query();die();
        $tt= 0;
        foreach ($result as  &$value) {
              $value['ratio'] = json_decode($value['ratio']);
              $value['employee_id'] = json_decode($value['employee_id']);
              $t = array_search($id, $value['employee_id']);
              $value['t'] = $value['sub_value']*$value['ratio'][$t]/100;
              $tt += $value['t'];

        }
        // echo $this->db->last_query();die();
       return round($tt);
    }






    # lấy dữ liệu theo năm tháng quý
function get_employee_data($id,$arrParam=null,$option=null)
{   
    $y = date('Y');
    $name = $this->get_employee_by_id($id)['name'];
    $arrParam['time'] = empty($arrParam['time']) ? 'month' : $arrParam['time'];
    $arrParam['value'] = empty($arrParam['value']) ? $y : $arrParam['value'];

    $m = 12;
    $y = $arrParam['value'];
    # Lấy số ngày của tháng
    $d = date('t',strtotime("01-$m-$y"));
    $month =array();
    // $arrParam['time'] ='quater';
    $number = empty($arrParam['number']) ? 12 : $arrParam['number'];
    # Theo tháng
    // echo $m;
    // echo $arrParam['time']; die();
    if($arrParam['time']=="month")
    {                   
        #Lấy doanh thu 11 tháng trc còn lại
        for ($i=0; $i < $number ; $i++) 
        { 
            $j =$i+1;
            $arr['start_date'] = date("Y-m-d",strtotime("$y-$m-$d -$j month"));
            $arr['end_date'] = date("Y-m-d",strtotime("$y-$m-$d -$i month"));
            // echo $arrParam['end_date'] ;die();

            if($option == "task")
            {
               $tt = $this->get_number_task($id,$arr);
           }
           else
           {
            $tt = $this->get_employee_value_contract_d13($id,$arr);
        }
        $month['data'][] = ($tt == null) ? 0 : round($tt);
        $month['categories'][] = "Tháng ".date("m/Y",strtotime("$y-$m-01 -$i month"));
    }

}

    #Theo quý
elseif($arrParam['time'] =='quater')
{
        // echo "string"; die();

 for ($i=0; $i <$number ; $i++) 
 { 
    $k=$i*3;
    $j =($i+1)*3;
    $arr['start_date'] = date("Y-m-d",strtotime("$y-$m-$d -$j month"));
    $arr['end_date'] = date("Y-m-d",strtotime("$y-$m-$d -$k month"));
    if($option == "task")
    {
       $tt = $this->get_number_task($id,$arr);
   }
   else
   {
    $tt = $this->get_employee_value_contract_d13($id,$arr);
}
$month['data'][] = ($tt == null) ? 0 : round($tt);
$month['categories'][] = $this->convert_quater(date("m",strtotime("$y-$m-01 -$k month")))."/".date("Y",strtotime("$y-$m-01 -$k month"));
}

         #Lấy dữ liệu quý hiên tại this quater
}

elseif($arrParam['time'] =='year')
{
        // echo "string"; die();

    for ($i=0; $i <$number ; $i++) 
    { 

        $j =$i+1;
        $arr['start_date'] = date("Y-m-d",strtotime("$y-$m-$d -$j year"));
        $arr['end_date'] = date("Y-m-d",strtotime("$y-$m-$d -$i year"));
        if($option == "task")
        {
           $tt = $this->get_number_task($id,$arr);
       }
       else
       {
        $tt = $this->get_employee_value_contract_d13($id,$arr);
    }
    $month['data'][] = ($tt == null) ? 0 : round($tt);
    $month['categories'][] = "Năm ".date("Y",strtotime("$y-$m-01 -$i year"));

}

}

$result['series'] = array('name'=>$name,'data'=>array_reverse($month['data']));
$result['categories'] =array_reverse($month['categories']);
return $result;
}




       #Lấy sô dự án theo thời gian
public function get_number_task($id,$arrParam)

{
    $this->db->select('tr.task_id,e.person_id, e.username,tr.user_id,p.first_name,tr.created');
    $this->db->from('phppos_task_user_relations AS tr');
    $this->db->join('phppos_tasks as t', 't.id = tr.task_id');
    $this->db->join('phppos_employees AS e', 'tr.user_id = e.id', 'left');
    $this->db->join('phppos_people AS p', 'e.person_id = p.person_id', 'left');
    $this->db->where('tr.user_id', $id);
    $this->db->where('t.parent', 0);
    $this->db->where('(tr.is_implement =1 OR tr.is_join =1)');
    if (!empty($arrParam['start_date'])) 
    {
        $this->db->where('tr.created >=', $arrParam['start_date']);
    }
    if (!empty($arrParam['end_date'])) 
    {
        $this->db->where('tr.created <=', $arrParam['end_date']);
    }

    return count($this->db->get()->result_array());

}




function get_employee_value_contract_d13($id=1,$arrParam=null)
{

    $this->db->select('SUM(IF(p.vat="published",(p.price / 1.1),p.price)) as value, p.contract_id as contract_id');
    $this->db->from('phppos_contract_payment as p');
    $this->db->where('(p.c_status ="done" OR p.c_status ="liquidated")');
    if (!empty($arrParam['start_date'])) 
    {
        $this->db->where('p.date_payment >=', $arrParam['start_date']);
    }
    if (!empty($arrParam['end_date'])) 
    {
        $this->db->where('p.date_payment <', $arrParam['end_date']);
    }

    $this->db->group_by('p.contract_id');
    $sub = $this->db->get_compiled_select();

    $this->db->select('cp.contract_id,tk.kpi_id,cp.value,tk.employee_id,tk.ratio, 
        cp.`value` as sub_value ');
    $this->db->from('phppos_task_kpiperson_approve as tk ');
    $this->db->join('contract as c', 'c.project_id = tk.task_id');
    $this->db->join("($sub) as cp" , 'cp.contract_id = c.id');
    $this->db->like('tk.employee_id', '"'.$id.'"');
    $this->db->where('tk.history', 1);
    $result = $this->db->get()->result_array();
        // echo $this->db->last_query();die();
    $tt= 0;
    foreach ($result as  &$value) {
      $value['ratio'] = json_decode($value['ratio']);
      $value['employee_id'] = json_decode($value['employee_id']);
      $t = array_search($id, $value['employee_id']);
      $value['t'] = $value['sub_value']*$value['ratio'][$t]/100;
      $tt += $value['t'];

  }
        // echo $this->db->last_query();die();
  return round($tt);
}


protected function convert_quater($t)
{

    if ($t <= 3) return "Quý I";
    if ($t <= 6) return "Quý II";
    if ($t <= 9) return "Quý III";

    return "Quý IV";
}


   

}
?>
