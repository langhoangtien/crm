<?php 
class Kpi_Person extends CI_Model
{
	function __construct(){
		parent::__construct();
		
	}
	public function get_all_task(){
		return $this->db->select('*')->from('phppos_tasks')->get()->result_array();
	}
	function auto_code_kpi(){  
		// $id_location = $this->session->employee_current_location_id;
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		// lấy tên location hiện tại
		$where = array('location_id'=>$id_location);
		$code_location = $this->db->select('code')
		->from('phppos_locations')
		->where($where)->get()->row()->code;
		// check location 
		$data = $this->db->select('*')
		->from('phppos_task_kpiperson_approve')
		->get()
		->result_array();
		if (count($data)>0) {
			foreach ($data as $key => $value) {
				$kpi[] = explode('_',$value['kpi_id']);
			}
			// mảng giá trị lớn nhất của location
			for ($i=0; $i <count($kpi) ; $i++) { 
				$arr_location[$kpi[$i][2]] = $kpi[$i][3];
			}
			$auto_number = $arr_location[$code_location]+1;
		}else{
			$auto_number =1;
		}

		return 'KPI_'.date('Y').'_'.$code_location.'_'.$auto_number;
	}
	/**
	*
	**/
	public function get_list($arrParam=null)
	{
		$user_id = $this->Employee->get_logged_in_employee_info()->id;
		$task_kpi = $this->db->get('phppos_task_kpiperson_approve')->result_array();
		foreach ($task_kpi as $key => $value) {
			$task_id_kpi[] = $value['task_id'];
		}
		$this->db->select('t.*')
		->from('phppos_tasks t')
		->join('phppos_sales s','s.task_id = t.id')
		->where('t.parent',0);
		if (!empty($arrParam['create']) && !empty($task_id_kpi)) {
			$this->db->where_not_in('t.id', $task_id_kpi);
		}
		if (!empty($arrParam['location'])) {
			$this->db->where('s.location_id',$arrParam['location']);
		}
		if (!empty($arrParam['join']) || !empty($arrParam['not_view'])) {
			$this->db->join('phppos_task_user_relations tu','tu.task_id = t.id');
		}
		if (!empty($arrParam['join'])) {
			$this->db->where('tu.user_id',$user_id);
		}
		if (!empty($arrParam['not_view'])) {
			$this->db->where('tu.is_xem',0);
		}

		$this->db->where('s.deleted',0);
		$this->db->group_by('t.id');
		return $this->db->get()->result_array();
		// if (!empty($arrParam['trangthai'])) {
		// 	// hoan thanh:2
		// 	$data = $this->db->select('task_id')
		// 	->from('phppos_task_kpiperson_approve')
		// 	->get()->result_array();

		// 	foreach ($data as $key => $value) {
		// 		$task_id[]=$value['task_id'];
		// 	}
		// 	if (!empty($task_id)) {
		// 		$this->db->select('t.*')
		// 		->from('phppos_tasks t')->where(array('t.parent'=>0));
		// 		$this->db->where_not_in('t.id',$task_id);
		// 	}else{
		// 		$this->db->select('t.*')
		// 		->from('phppos_tasks t')
		// 		->where(array('t.parent'=>0));
		// 		// $this->db->where(array('t.pheduyet'=>1,'t.trangthai'=>$arrParam['trangthai'],'t.progress'=>100))
		// 		$this->db->group_by('t.id');
		// 	}
		// 	return $this->db->get()->result_array();
		// }else{

		// }
	}
	function check_completed($task_id){
		$this->db->select('*')->from('phppos_task_kpiperson_approve');
		$this->db->where('task_id', $task_id);
		$this->db->where('history',1);
		return $this->db->get()->row();
	}

	function check_task($arrParam){
		$this->db->select('*')
		->from('phppos_task_user_relations');
		if (!empty($arrParam['is_progress'])) {
			$this->db->where('is_progress',1);
		}
		if (!empty($arrParam['user_id'])) {
			$this->db->where('user_id',$arrParam['user_id']);
		}
		$this->db->where('is_xem',0);		
		$this->db->group_by('task_id');
		return $this->db->get()->result_array();
	}

	public function get_list_status($trang_thai){
		$task_trangthai = lang('task_trangthai');
		$data = $this->Kpi_Person->get_list();
		$result = array();
		foreach ($task_trangthai as $key => $value) {
			if($trang_thai==$key){
				$result = array(
					'name' => $value
				);
			}
		}
		return $result;
	}
	// kiem tra truong hop phe duyet
	public function check_task_approve(){
		$data =$this->db->select('a.*')
		->from('phppos_task_kpiperson_approve a')
		->join('phppos_tasks t','a.task_id=t.id')
		->where(array('t.parent'=>0))
		->get()
		->result_array();
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$task_id[] = $value['task_id'];
			}
			if (!empty($task_id)) {
				$this->delete('phppos_task_kpiperson_approve','task_id',$task_id);
			}
		}
	}

	public function find_name_task($id){
		return $this->db->select('name,id')
		->from('phppos_tasks')
		->where(array('id'=>$id))
		->get()
		->row();
	}

	public function get_employee_join_tasks($arrParam=null){
		if (empty($arrParam['task_id'])) {
			$where = array();
		}else{
			$where = array('t.id'=>$arrParam['task_id']);
		}
		$this->db->select('t.id,e.username,e.id as employee_id, t.name as tenda,g.name as chucvu,tu.is_progress,tu.is_join, t.trangthai as trangthai_da, tu.is_implement,pp.first_name,pp.last_name')
		->from('phppos_task_user_relations as tu')
		->join('phppos_tasks as t','tu.task_id = t.id')
		->join('phppos_employees as e', 'e.id = tu.user_id')
		->join('phppos_people as pp','pp.person_id = e.person_id')
		->join('phppos_groups as g','g.group_id = e.group_id');
		// $this->db->or_where('tu.is_implement',1);
		return $this->db->where($where);
	}

	public function update($arrParam){
		if (empty($arrParam['update'])) {
			$this->db->select('*')
			->from('phppos_task_kpiperson_approve')
			->join('phppos_tasks','phppos_tasks.id=phppos_task_kpiperson_approve.task_id');
			if (!empty($arrParam['task_id'])) {
				$this->db->where('task_id',$arrParam['task_id']);
			}
			return $this->db->get()->row();
		}else{
			$this->get_employee_join_tasks(array('task_id'=>$arrParam['task_id']))->join('phppos_task_kpiperson_approve kpi','kpi.task_id = t.id')->where('(tu.is_join=1 OR tu.is_implement=1)')->where_in('e.id',json_decode($arrParam['employee_id'],true));
			return $this->db->get()->result_array();
		}
	}

	public function duyet_du_an($task_id){
		return $this->get_employee_join_tasks(array('task_id'=>$task_id))
		->get()
		->result_array();
	}

	/**
	* Luu du lieu sau khi phe duyet kpi
	* table: phpos_tasks_approve
	**/
	public function save_data($table,$data=array(),$check){
		if ($check ==0) {
			$this->db->where('id', $data['id']);
			return $this->db->update($table,$data);
		}else{
			$this->db->insert($table,$data);
			return true;
		}
	}
	/**
	* lấy thông tin người phê duyệt dự án
	**/
	public function nguoi_phe_duyet_du_an($task_id){
		return $this->db->select('a.*,e.username, phppos_tasks.name')
		->from('phppos_task_kpiperson_approve as a')
		->join('phppos_employees e','e.id = a.user_approve')
		->join('phppos_tasks','phppos_tasks.id=a.task_id')
		->where('a.task_id',$task_id)
		->group_by('a.user_approve')
		->get()
		->result_array();
	}
	/**
	* đến số bản ghi của bảng
	**/
	public function count_row($table,$where=array()){
		return $this->db->select('*')
		->from($table)
		->where($where)
		->count_all_results();
	}
	/**
	* lấy thông tin khách hàng của dự án
	**/
	public function get_info_customer($task_id){
		return $this->db->select('t.id, pp.first_name, pp.last_name')
		->from('phppos_tasks t')
		->join('phppos_sales s','s.sale_id=t.sale_id')
		->join('phppos_people pp','pp.person_id=s.customer_id')
		->where('t.id',$task_id)
		->group_by('t.id')
		->get()
		->row();
	}

	public function get_kpi_task($task_id){
		return $this->db->select('*')
		->from('phppos_task_kpiperson_approve')
		->where('task_id',$task_id)
		->get()->row();
	}

	public function get_status_complete($task_id){
		$data = $this->db->select('*')
		->from('phppos_tasks')
		->where(array('id'=>$task_id,'trangthai'=>2,'progress'=>100,'pheduyet'=>1))
		->group_by('id')
		->get()
		->row();
		if (!empty($data)) {
			if ($data->pheduyet==1) {
				echo '<span class="text-success">Đã hoàn thành</span>';
			}else{
				echo '<span>Chưa hoàn thành</span>';
			}
		}else{
			// $data[]=$task_id;
			// $this->delete('phppos_task_kpiperson_approve','task_id',$data);
			echo '<span>Chưa hoàn thành</span>';
		}
	}

	public function get_info_table($table, $where =array()){
		return $this->db->select('*')
		->from($table)
		->where($where)
		->get()
		->row();
	}

	public function danh_sach_nguoi_phu_trach($task_id){
		$arr_1 = $this->db->select('tu.task_id,tu.user_id')
		->from('phppos_task_user_relations as tu')
		->where(array('tu.task_id'=>$task_id, 'is_implement'=>1))
		->get()
		->result_array();

		if (!empty($arr_1)) {
			for ($i=0; $i < count($arr_1); $i++) { 
				$arr_user_id[] = $arr_1[$i]['user_id'];
			}
			$employee = $this->db->select('*')
			->from('phppos_employees')
			->join('phppos_groups','phppos_employees.group_id = phppos_groups.group_id')
			->where_in('id',$arr_user_id)
			->get()
			->result_array();
		}
		return $employee;
	}
	public function delete($table,$id,$where=array()){
		$this->db->where_in($id,$where);
		return $this->db->delete($table);
	}


}