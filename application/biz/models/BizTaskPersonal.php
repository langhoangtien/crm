<?php
class BizTaskPersonal extends CI_Model{

    protected $_table         = 'tasks_personal';
    protected $_fields        = array();
    protected $_id_admin      = null;

    protected $_prioty          = null;
    protected $_trangthai       = null;
    protected $_trangthai_type  = null;

    public function __construct(){
        parent::__construct();

        $this->load->library('MY_System_Info');
        $info 			 = new MY_System_Info();
        $user_info 		 = $info->getInfo();

        $this->_id_admin = $user_info['id'];

        $this->_fields 	 =  array(
            'name' 	 		=> 't.name',
            'prioty' 	 	=> 't.prioty',
            'date_start' 	=> 't.date_start',
            'date_end' 	 	=> 't.date_end',
            'modified' 	    => 't.modified',
            'username' 		=> 'e.username',
        );

        $this->_trangthai_type = array(
            'cancel' => array('3'), // đóng, dừng
            'not-done' => array('4'), // không thực hiện
            'unfulfilled' => array('0'), // chưa thực hiện
            'processing' => array('1'), // đang tiến hành
            'slow_proccessing' => array('0','1','5'), // chậm tiến độ
            'finish' => array('2'), // hoàn thành
            'slow-finish' => array('2','5'), // hoàn thành nhưng chậm tiến độ
            'completed-on-schedule'=> array('2','6'), // hoàn thành đúng tiến độ
        );

        $this->_prioty    = lang('task_prioty');
        $this->_trangthai = lang('task_trangthai');

    }

    public function statistic($arrParams = null, $options = null) {
        $id_admin = $this->_id_admin;
        if($options['task'] == 'task-by-project') {
            $where = $this->get_where_from_filter($arrParams);
            $this->db->select("COUNT(t.id) AS totalItem")
                     ->from($this->_table . ' AS t');

            if($arrParams['data_table'] == 'personal')
                $this->db->where("CONCAT(',',t.implements,',') LIKE '%,$id_admin,%'");
            elseif($arrParams['data_table'] == 'follow')
                $this->db->where("CONCAT(',',t.xems,',') LIKE '%,$id_admin,%'");

            if(!empty($where)) {
                foreach($where as $wh)
                    $this->db->where($wh);
            }

            $query 	   = $this->db->get();

            $result = $query->row_array();
            $result = $result['totalItem'];
        }elseif($options['task'] == 'task-by-project-trangthai') {
            $type = $this->_trangthai_type[$options['type']];
            if(!empty($arrParams['trangthai'])) {
                $trangthai = $arrParams['trangthai'];
                if($trangthai == 'zero')
                    $trangthai = '0';

                $trangthai_arr = explode(',', $trangthai);
                if((array_search('5', $trangthai_arr)) == false && (array_search('6', $trangthai_arr)) == false) {
                    $trangthai_arr[] = '5';
                    $trangthai_arr[] = '6';
                }
            }else
                $trangthai_arr = array('0','1','2','3','4','5','6');

            foreach($trangthai_arr as $val) {
                if(in_array($val, $type)) {
                    if(($key = array_search($val, $type)) !== false) {
                        unset($type[$key]);
                    }
                }
            }

            if(count($type) == 0) {
                $arrParams['trangthai'] = implode(',', $this->_trangthai_type[$options['type']]);
                if($arrParams['trangthai'] == '0'){
                    $arrParams['trangthai'] = 'zero';
                }

            }else
                $arrParams['trangthai'] = '-1';

            $where = $this->get_where_from_filter($arrParams);
            $this->db->select("COUNT(t.id) AS totalItem")
                     ->from($this->_table . ' AS t');

            if($arrParams['data_table'] == 'personal')
                $this->db->where("CONCAT(',',t.implements,',') LIKE '%,$id_admin,%'");
            elseif($arrParams['data_table'] == 'follow')
                $this->db->where("CONCAT(',',t.xems,',') LIKE '%,$id_admin,%'");

            if(!empty($where)) {
                foreach($where as $wh)
                    $this->db->where($wh);
            }

            $query 	   = $this->db->get();

            $result = $query->row_array();
            $result = $result['totalItem'];
        }

        return $result;
    }


    public function countItem($arrParams = null, $options = null) {
        if($options == null) {
            $id_admin = $this->_id_admin;
            $where = $this->get_where_from_filter($arrParams);
            $this->db -> select('COUNT(t.id) AS totalItem')
                      -> from($this->_table . ' AS t');

            $this->db->where("CONCAT(',',t.implements,',') LIKE '%,$id_admin,%'");

            if(!empty($where)) {
                foreach($where as $wh)
                    $this->db->where($wh);
            }

            $query = $this->db->get();

            $result = $query->row()->totalItem;
        }
        return $result;
    }

    public function getItem($arrParams = null, $options = null){
        if($options['task'] == 'public-info') {
            $tblCustomers = $this->model_load_model('TaskCustomers');
            $tblUsers     = $this->model_load_model('TaskUser');
            $tblFiles     = $this->model_load_model('TaskPersonalFiles');

            $this->db->select("t.*")
                     ->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y') as date_finish", FALSE)
                     ->select("DATE_FORMAT(t.date_start, '%d-%m-%Y %H:%i:%s') as date_start", FALSE)
                     ->select("DATE_FORMAT(t.date_end, '%d-%m-%Y %H:%i:%s') as date_end", FALSE)
                     ->from($this->_table . ' as t')
                     ->where('t.id',$arrParams['id']);

            $query = $this->db->get();
            $result =  $query->row_array();
            $this->db->flush_cache();
            if(!empty($result)) {
                $customers = array();

                if(!empty($result['customer_ids'])) {
                    $cid = explode(',', $result['customer_ids']);
                    $customers = $tblCustomers->getItems(array('cid'=>$cid));
                }

                $user_ids = $implement_ids = $xem_ids = $implements = $xems =$joins =$join_ids = array();
                if(!empty($result['implements'])) {
                    $implement_ids = explode(',', $result['implements']);
                    $user_ids = array_merge($user_ids, $implement_ids);
                }

                if(!empty($result['xems'])) {
                    $xem_ids = explode(',', $result['xems']);
                    $user_ids = array_merge($user_ids, $xem_ids);
                }
                if(!empty($result['joins'])) {
                    $join_ids = explode(',', $result['joins']);
                    $user_ids = array_merge($user_ids, $join_ids);
                }

                $user_ids[] = $result['created_by'];
                $user_ids = array_unique($user_ids);

                if(!empty($user_ids)) {
                    $users = $tblUsers->getItems(array('user_ids'=>$user_ids));
                }

                if(!empty($implement_ids)) {
                    foreach($implement_ids as $user_id)
                        $implements[$user_id] = $users[$user_id];
                }

                if(!empty($xem_ids)) {
                    foreach($xem_ids as $user_id)
                        $xems[$user_id] = $users[$user_id];
                }
                if(!empty($join_ids)) {
                    foreach($join_ids as $user_id)
                        $joins[$user_id] = $users[$user_id];
                }
                $result['customers']       = $customers;
                $result['implements']      = $implements;
                $result['xems']            = $xems;
                $result['joins']           = $joins;
                $result['implement_ids']   = $implement_ids;
                $result['xem_ids']         = $xem_ids;
                $result['join_ids']        = $join_ids;
                $result['created_by_name'] = $users[$user_id]['username'];
                $result['files']           = $tblFiles->getItems(array('task_ids'=>array($arrParams['id'])), array('task'=>'by-tasks'));
            }
        }elseif($options['task'] == 'information') {
            $this->db->select("t.*")
                    ->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y %H:%i') as date_finish", FALSE)
                    ->select("DATE_FORMAT(t.date_start, '%d-%m-%Y %H:%i') as date_start", FALSE)
                    ->select("DATE_FORMAT(t.date_end, '%d-%m-%Y %H:%i') as date_end", FALSE)
                    ->from($this->_table . ' as t')
                    ->where('t.id',$arrParams['id']);

            $query = $this->db->get();
            $result =  $query->row_array();
            $this->db->flush_cache();
        }
        return $result;
    }

    public function listItem($arrParams = null, $options = null) {
        $paginator = $arrParams['paginator'];
        if($options == null) {
            $id_admin = $this->_id_admin;
            $where = $this->get_where_from_filter($arrParams);
            $this->db->select("DATE_FORMAT(t.date_start, '%d-%m-%Y ') as start_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_end, '%d-%m-%Y ') as end_date", FALSE);
            $this->db->select("DATE_FORMAT(t.date_finish, '%d-%m-%Y %H:%i:%s') as finish_date", FALSE);
            $this->db->select("t.id, t.name, t.created, t.prioty, t.trangthai,t.detail, t.progress,t.implements,t.joins,t.approved,t.type")
                     ->from($this->_table . ' AS t');

            $this->db->where("(t.approved=$id_admin OR t.created_by=$id_admin OR CONCAT(',',t.implements,',') LIKE '%,$id_admin,%' OR CONCAT(',',t.joins,',') LIKE '%,$id_admin,%')");
            if(empty($arrParams['type']))
            {
            $this->db->where('t.type', 1);
            }
            else
            {
            $this->db->where('t.type',2);

            }


            if(!empty($arrParams['col']) && !empty($arrParams['order'])){
                $col   = $this->_fields[$arrParams['col']];
                $order = $arrParams['order'];

                $this->db->order_by($col, $order);
                if($arrParams['col'] != 'date_start')
                    $this->db->order_by('t.date_start', 'DESC');
            }else {
                $this->db ->order_by("t.prioty",'ASC')
                          ->order_by('t.date_start', 'DESC');
            }

            if(!empty($where)) {
                foreach($where as $wh)
                    $this->db->where($wh);
            }

            $page = (empty($arrParams['start'])) ? 1 : $arrParams['start'];
            $this->db->limit($paginator['per_page'],($page - 1)*$paginator['per_page']);

            $query = $this->db->get();
            // echo $this->db->last_query(); exit();

            $result = $query->result_array();
// var_dump($result);die();
            if(!empty($result)) {
                foreach($result as &$val) {
                    if($val['trangthai'] == 0 || $val['trangthai'] == 1){	// chưa thực hiên + đang thực hiện
                        $now        = date('Y-m-d H:i:s', strtotime(date("d-m-Y H:i:s")));
                        $date_end   = date('Y-m-d H:i:s', strtotime($val['end_date']));

                        $datediff 	= strtotime($now) - strtotime($date_end);
                        $duration        = datediff(date("d-m-Y H:i:s"), $val['end_date']);
                        if($datediff <= 0){
                            $val['note'] 		= 'Còn '.$duration;
                            $val['p_color'] 	= '#4388c2';
                            $val['color'] 		= '#489ee7';

                        }else{
                            $val['note']    =  'Quá '.$duration;
                            $val['p_color'] = '#aa142f';
                            $val['color']   = '#c90d2f';
                        }

                    }elseif($val['trangthai'] == 2) {// hoàn thành
                        $end_date      = date('Y-m-d H:i:s', strtotime($val['end_date']));
                        $finish_date   = date('Y-m-d H:i:s', strtotime($val['finish_date']));

                        $datediff 	= strtotime($end_date) - strtotime($finish_date);
                        $duration        = datediff($val['end_date'], $val['finish_date']);

                        if($datediff < 0){
                            $val['note']    = 'Trễ '.$duration;
                            $val['p_color'] = '#4a6242';
                            $val['color']   = '#516e47';

                        }elseif($datediff > 0){
                            $val['p_color']   = '#a9fa01';
                            $val['color']     = '#91d20a';
                            $val['note']      = 'Sớm '.$duration;
                        }else{
                            $val['p_color'] = '#18c33e';
                            $val['color']   = '#12e841';
                        }

                    }elseif($val['trangthai'] == 3) {
                        $val['p_color'] = '#e0d91c';
                        $val['color']   = '#bdb720';
                    }elseif($val['trangthai'] == 4) {
                        $val['p_color'] = '#303020';
                        $val['color']   = '#303023';
                    }

                    $val['prioty']    = $this->_prioty[$val['prioty']];
                    $val['trangthai'] = $this->_trangthai[$val['trangthai']];


                    if($val['type']==2)
                    {
                        // var_dump($val['id']);die();
                          # Lấy ng tham gia, ng phụ trách
                           if(!empty($val['implements'])) {
                            $val['implements'] = explode(",", $val['implements']);
                            $val['implements'] = $this->Employee->get_username_by_ids($val['implements']);

                        }


                         if(!empty($val['joins'])) {
                            $val['joins'] = explode(",", $val['joins']);
                            $val['joins'] = $this->Employee->get_username_by_ids($val['joins']);

                        }
                         if(!empty($val['approved'])) {
                            $val['approved'] = $this->Employee->get_username_by_ids(array(0=>$val['approved']));

                        }
                    }

                }



            }

        }
// var_dump($result);die();
        return $result;
    }

    public function saveItem($arrParam = null, $options = null) {
         if(!empty($arrParam['approved']))
            {
                $data['approved'] = $arrParam['approved'];
            }
        if($options['task'] == 'add') {
            if(isset($arrParam['customer'])) {
                $customer_ids = implode(',', $arrParam['customer']);
            }
             if(isset($arrParam['join'])) {
                $joins = implode(',', $arrParam['join']);
            }

            if(isset($arrParam['implement'])) {
                $implements = implode(',', $arrParam['implement']);
            }

            if(isset($arrParam['xem'])) {
                $xems = implode(',', $arrParam['xem']);
            }

            if($arrParam['progress'] == 100)
                $date_finish = @date("Y-m-d H:i:s");
            else
                $date_finish = '0000/00/00 00:00:00';


              // echo "<pre>";var_dump($arrParam);die();


            if(!empty($arrParam['type_task']))
            $data['type'] =2;

            $data['name']				    =       stripslashes($arrParam['name']);
            $data['detail']				    =       stripslashes($arrParam['detail']);
            $data['progress']				= 		$arrParam['progress'];
            if (!empty($arrParam['date_start'])) {
                $data['date_start'] = @date('Y-m-d', strtotime($arrParam['date_start']));
            }
            if (!empty($arrParam['date_end'])) {
                $data['date_end'] = @date('Y-m-d', strtotime($arrParam['date_end']));
            }
            $data['date_finish'] = $date_finish;
            $data['created']				= 		@date("Y-m-d H:i:s");
            $data['created_by']				= 		$this->_id_admin;
            $data['modified']				= 		@date("Y-m-d H:i:s");
            $data['modified_by']			= 		$this->_id_admin;
            $data['trangthai']				= 		$arrParam['trangthai'];
            $data['prioty']					= 		$arrParam['prioty'];
            $data['customer_ids']			= 		$customer_ids;
            $data['implements']			    = 		$implements;
            $data['xems']			        = 		$xems;
            $data['joins']                  =       $joins;
            // echo "<pre>"; var_dump($data);die();
            $this->db->insert($this->_table,$data);
            $lastId = $this->db->insert_id();
            $this->norevenue_log($lastId,$data);
        }elseif($options['task'] == 'edit') {
			$lastId = $arrParam['id'];
            if(isset($arrParam['customer'])) {
                $customer_ids = implode(',', $arrParam['customer']);
            }
             if(isset($arrParam['join'])) {
                $joins = implode(',', $arrParam['join']);
            }
            if(isset($arrParam['implement'])) {
                $implements = implode(',', $arrParam['implement']);
            }

            if(isset($arrParam['xem'])) {
                $xems = implode(',', $arrParam['xem']);
            }
            // echo "<pre>";var_dump($arrParam);die();
            if(!empty($arrParam['task_id']))
            {
                $arrParam['id'] = $arrParam['task_id'];
            }
			$this->db->where("id",$arrParam['id']);

			if($arrParam['progress'] == 100)
				$date_finish = @date("Y-m-d H:i:s");
			else
				$date_finish = '0000/00/00 00:00:00';

            $data['name']				    =       stripslashes($arrParam['name']);
            $data['detail']				    =       stripslashes($arrParam['detail']);
            $data['progress']				= 		$arrParam['progress'];
            $data['date_start']				= 		$arrParam['date_start'];
            $data['date_end']				= 		$arrParam['date_end'];
            $data['date_finish']			= 		$date_finish;
            $data['modified']				= 		@date("Y-m-d H:i:s");
            $data['modified_by']			= 		$this->_id_admin;
            $data['customer_ids']			= 		$customer_ids;
            $data['prioty']                 =       $arrParam['prioty'];
            $data['trangthai']              =       $arrParam['trangthai'];
            $data['implements']			    = 		$implements;
            $data['xems']			        = 		$xems;
            $data['joins']                  =       $joins;

			$this->db->update($this->_table,$data);
			$this->db->flush_cache();
            $this->norevenue_log($arrParam['id'],$data);

		}elseif($options['task'] == 'update-progress') {
            if($arrParam['progress'] == 100)
                $date_finish = @date("Y-m-d H:i:s");
            else
                $date_finish = '0000/00/00 00:00:00';

            $this->db->where("id",$arrParam['id']);
            $data['progress'] 				= 				$arrParam['progress'];
            $data['trangthai'] 				= 				$arrParam['trangthai'];
            $data['date_finish'] 			= 				$date_finish;
            $data['modified']				= 				@date("Y-m-d H:i:s");
            $data['modified_by']     		=				$arrParam['adminInfo']['id'];

            $this->db->update($this->_table,$data);

            $this->db->flush_cache();

            $lastId = $arrParam['id'];
        }

        return $lastId;
    }


#Update lại công việc dự án
    function norevenue_log($task_id,$data){
        $dt['implements'] = json_encode(explode(",",$data['implements']));
        $dt['joins'] = json_encode(explode(",",$data['joins']));
        $dt['task_id'] =$task_id;
        $dt['time'] = date("Y-m-d H:i:s");
        $dt['person_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
        $dt['seens'] = '[""]';
        $where = array('task_id'=>$task_id,'joins'=>$dt['joins'],'implements'=>$dt['implements']);
   
        if(!empty($data['approved'])){
            $dt['approved'] = $data['approved'];
            $where['approved'] = $dt['approved'];
        }
        $this->db->where($where);
        $result = $this->db->get('phppos_task_no_revenue_log')->row_array();
        if(empty($result)){
            $this->db->insert('phppos_task_no_revenue_log', $dt);
        }
        
    }

    # Danh sách thông báo công việc không tạo doanh thu
    function get_list_notice_no_revenue($option=null){
        $id = $this->Employee->get_logged_in_employee_info()->id;
        $this->db->select('nl.task_id,nl.person_id,nl.approved,nl.joins,nl.id,nl.implements,nl.seens,e.username,tp.name,DATE_FORMAT(nl.time, "%d-%m-%Y ") as time');
        $this->db->from('phppos_task_no_revenue_log as nl');
        $this->db->join('phppos_employees as e', 'e.person_id = nl.person_id');
        $this->db->join('phppos_tasks_personal as tp', 'tp.id = nl.task_id');
        $this->db->where("(tp.approved=$id OR CONCAT(',',tp.implements,',') LIKE '%,$id,%' OR CONCAT(',',tp.joins,',') LIKE '%,$id,%')");
        if($option==null){
            $this->db->not_like('nl.seens', '"'.$id.'"');
        }
        
        $this->db->order_by('nl.id', 'desc');
        $result = $this->db->get();
        if (!$result) {
           return null;
        }
        $result = $result->result_array();
        // $result->free_result();
        return $result;
    }
    public function deleteItem($arrParam = null, $options = null){
        if($options['task'] == 'delete-multi'){
            $cid = implode(',', $arrParam['cid']);
            $this->db->where('id IN ('.$cid.')');
            $this->db->delete($this->_table);

            $this->db->flush_cache();
        }
    }

    protected function get_where_from_filter($arrParams, $options = null) {
        $where = array();
        if(!empty($arrParams['keywords'])) {
            $keywords = $arrParams['keywords'];
            $where[] = '(t.name LIKE \'%'.$keywords.'%\' OR t.detail LIKE \'%'.$keywords.'%\')';
        }

        if(!empty($arrParams['date_start_from'])) {
            $date_start_from = $arrParams['date_start_from'];
            $where[] 	     = 't.date_start >= \''.$date_start_from.'\'';

        }

        if(!empty($arrParams['date_start_to'])) {
            $date_start_to = $arrParams['date_start_to'];
            $where[] 	   = 't.date_start <= \''.$date_start_to.'\'';
        }

        if(!empty($arrParams['date_end_from'])) {
            $date_end_from = $arrParams['date_end_from'];
            $where[] 	   = 't.date_end >= \''.$date_end_from.'\'';
        }

        if(!empty($arrParams['date_end_to'])) {
            $date_end_to = $arrParams['date_end_to'];
            $where[] 	 = 't.date_end <= \''.$date_end_to.'\'';
        }

        if(!empty($arrParams['trangthai'])) {
            $current_now = date('Y-m-d H:i:s');
            if($arrParams['trangthai'] == 'zero')
                $arrParams['trangthai'] = '0';

            $trangthai_arr = explode(',', $arrParams['trangthai']);
            if(in_array(5, $trangthai_arr) && in_array(6, $trangthai_arr)) {
                if(($key = array_search(5, $trangthai_arr)) !== false) unset($trangthai_arr[$key]);
                if(($key = array_search(6, $trangthai_arr)) !== false) unset($trangthai_arr[$key]);
            }else {
                if(in_array(5, $trangthai_arr)) {
                    if(($key = array_search(5, $trangthai_arr)) !== false) unset($trangthai_arr[$key]);
                    $where_clause[] = "TIMESTAMPDIFF(SECOND, t.date_end, '$current_now') > 0";
                }
                if(in_array(6, $trangthai_arr)) {
                    if(($key = array_search(5, $trangthai_arr)) !== false) unset($trangthai_arr[$key]);
                    $where_clause[] = "TIMESTAMPDIFF(SECOND, t.date_end, '$current_now') <= 0";
                }
            }

            $where_clause[] = 't.trangthai IN ('.implode(',', $trangthai_arr).')';
            $where[] = '('.implode(' AND ', $where_clause).')';
        }

        if(!empty($arrParams['customers'])) {
            $customers = explode(',', $arrParams['customers']);
            $where_clause = array();
            foreach($customers as $cus_id) {
                $where_clause[] = "CONCAT(',',customer_ids,',') LIKE '%,$cus_id,%'";
            }

            $where[] = implode(' OR ', $where_clause);
        }

        if(!empty($arrParams['implement'])) {
            $implement = explode(',', $arrParams['implement']);
            $where_clause = array();
            foreach($implement as $id) {
                $where_clause[] = "CONCAT(',',t.implements,',') LIKE '%,$id,%'";
            }

            $where[] = implode(' OR ', $where_clause);
        }

        if(!empty($arrParams['xem'])) {
            $xem = explode(',', $arrParams['xem']);
            $where_clause = array();
            foreach($xem as $id) {
                $where_clause[] = "CONCAT(',',t.xems,',') LIKE '%,$id,%'";
            }

            $where[] = implode(' OR ', $where_clause);
        }

        return $where;
    }

    function model_load_model($model_name)
    {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
    }

    /**
     * Lấy Task cho calendar
     * @param date_start $start
     * @param date_end $end
     */
    function get_task_personal_for_calendar($start, $end,$option =null) {
    	$this->db->select("id, name as title, date_start as start, date_end as end");
    	$this->db->from('tasks_personal');
    	$this->db->where('date_start >= ', $start);
    	$this->db->where('date_end <= ', $end);
        // lay theo nguoi tao
        if ($option==1) {
            $this->db->where('created_by', $this->Employee->get_logged_in_employee_info()->id);
        }
        $this->db->where('type', 1);
    	$query = $this->db->get();
    	return $query->result_array();
    }

    // Lấy danh sách công việc không tạo doanh thu cần phê duyệt
    function get_list_norevenue(){
        $this->db->select();
        $this->db->from('phppos_tasks_personal as tp');
        $this->db->where('tp.approved', $this->Employee->get_logged_in_employee_info()->id);
        $this->db->where('tp.trangthai', 2);
        $this->db->where('tp.pheduyet IS NULL');
        $result =  $this->db->get()->result_array();
        return $result;
    }


    # Cập nhật những người đã xem
    function update_notice_norevenue($li){
        foreach ($li as $key => $value) {
            $value['seens'] = json_encode($value['seens']);
            $data['seens'] = $value['seens'];
            $this->db->where('id', $value['id']);
            $this->db->update('phppos_task_no_revenue_log', $data);
        }
    }

}