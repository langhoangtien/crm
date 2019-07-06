<?php
require_once("Secure_area.php");

Class KpiPerson extends Secure_area 
{
	function __construct()
	{
		parent::__construct('kpi_person');
		$this->load->model('Kpi_Person');
	}

	public function index() {

		$data['person_id'] = $this->Employee->get_logged_in_employee_info()->person_id; 
		$data['employee_id'] = $this->Employee->get_logged_in_employee_info()->id; 

		// $this->check_action_permission('kpi_person','view_scope_all');

		$location_id = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;

		$data['controller_name'] = 'KpiPerson';
		$data['code_kpi'] = $this->Kpi_Person->auto_code_kpi();
		if (!empty($this->input->post('create'))) {
			if ($this->Employee->has_module_action_permission('kpi_person','view_scope_all', $this->Employee->get_logged_in_employee_info()->person_id)) {
				$data['tasks'] = $this->Kpi_Person->get_list(array('create'=>true,'not_view'=>true));
			}elseif ($this->Employee->has_module_action_permission('kpi_person','view_scope_location ', $this->Employee->get_logged_in_employee_info()->person_id)) {
				$data['tasks'] = $this->Kpi_Person->get_list(array('location'=>$location_id,'create'=>true,'not_view'=>true));
			}elseif ($this->Employee->has_module_action_permission('kpi_person','add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {
				$data['tasks'] = $this->Kpi_Person->get_list(array('join'=>true,'not_view'=>true));
			}else{
				$data['tasks'] =null;
			}
			echo json_encode($data);
		}else{
			if ($this->Employee->has_module_action_permission('kpi_person','view_scope_all', $this->Employee->get_logged_in_employee_info()->person_id)) {

				$data['tasks'] = $this->Kpi_Person->get_list();
			}elseif ($this->Employee->has_module_action_permission('kpi_person','view_scope_location', $this->Employee->get_logged_in_employee_info()->person_id)){

				$data['tasks'] = $this->Kpi_Person->get_list(array('location'=>$location_id));

			}elseif ($this->Employee->has_module_action_permission('kpi_person','add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {
				$data['tasks'] = $this->Kpi_Person->get_list(array('join'=>true));
			}else{
				$data['thongbao'] = 'Not';
			}
			$this->load->view("kpi/person/view",$data);
		}
	}
	/**
	* View form detail kpi
	**/
	public function detail()
	{
		$data['nguoi_phe_duyet'] = $this->Kpi_Person->nguoi_phe_duyet_du_an($this->input->post('project_id'));
		$data['check_completed'] = $this->Kpi_Person->check_completed($this->input->post('project_id'));
		if (!empty($data['check_completed'])) {
			$employee_id = json_decode($check_completed->employee_id);
			$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$this->input->post('project_id')))->where('(tu.is_join=1 OR tu.is_implement=1)')->where_in('e.id',$employee_id)->group_by('e.id')->get()->result_array();
			$data['nguoi_phu_trach'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$this->input->post('project_id')))->where('(tu.is_join=1 OR tu.is_implement=1)')->where_in('e.id',$employee_id)->group_by('e.id')->get()->result_array();
			
		}else{
			$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$this->input->post('project_id')))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
			$data['nguoi_phu_trach'] =$this->Kpi_Person->danh_sach_nguoi_phu_trach($this->input->post('project_id'));
		}
		
		$data['kpi_approve'] =$this->Kpi_Person->update(array('task_id'=>$this->input->post('project_id')));

		$this->load->view("kpi/person/detail_kpi_person",$data);
	}
	/**
	* Hiện thị form phê duyệt dự án
	* return view
	**/
	public function duyetDA($id,$arrParam=null) {
		$data['tasks_update'] 		= $this->Kpi_Person->update(array('task_id'=>$id));
		$data['name_tasks'] = $this->Kpi_Person->find_name_task($id);
		$data['code_kpi'] = $this->Kpi_Person->auto_code_kpi();
		$data['check_duyet_da']	= $this->Kpi_Person->get_kpi_task($id);
		$data['nguoi_tham_gia_da_all'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$id))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();

		if (!empty($arrParam['update'])) {
			$employee_id =$this->Kpi_Person->update(array('task_id'=>$arrParam['task_id']))->employee_id;
			$data['nguoi_tham_gia_da'] =$this->Kpi_Person->update(array('task_id'=>$id,'employee_id'=>$employee_id,'update'=>true));
			$data['nguoi_phu_trach'] =$this->Kpi_Person->danh_sach_nguoi_phu_trach($id);
			// echo "<pre>";
			// print_r($data['nguoi_tham_gia_da_all']); die();
		}else{
			$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$id))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
			$data['nguoi_phu_trach'] =$this->Kpi_Person->danh_sach_nguoi_phu_trach($id);
			
		}
		return $this->load->view('kpi/person/form_approve',$data);
	}

	public function update($id){
		$arrParam['task_id'] = $id;
		$arrParam['update']  = true;
		if (!empty($this->Kpi_Person->check_completed($id))) {
			$this->index();
		}else{
			return $this->duyetDA($id,$arrParam);
		}
		
	}

	public function pheduyet(){
		$table 	= 'phppos_task_kpiperson_approve';
		$flag = true;
		$kpi_id 	= $this->Kpi_Person->get_info_table('phppos_task_kpiperson_approve',array('task_id'=>$this->input->post('task_id')))->id;
		$data= array(
			'id' =>$kpi_id,
			'history' => 1, // da phe duyet
			'date_approve'=>Date('Y-m-d h:i:s')
		);
		if($this->Kpi_Person->save_data($table,$data,0)){
			$flag=true;
		}else{
			$flag = false;
		}
		echo json_encode(array('success'=>$flag));
	}
	public function luu_phe_duyet(){
		$table 	= 'phppos_task_kpiperson_approve';
		$flag 	= true;
		$kpi_id 	= $this->Kpi_Person->get_info_table('phppos_task_kpiperson_approve',array('task_id'=>$this->input->post('task_id')))->id;

		$data = array(
			'id'			=> $kpi_id,
			'user_approve'	=> $this->Employee->get_logged_in_employee_info()->person_id,
			'ratio'			=> json_encode($this->input->post('arr_ty_le')),
			'employee_id'	=> json_encode($this->input->post('arr_employee_id')),
			'date_approve'	=> date('Y-m-d h:i:s'),
		);
		if ($this->input->post('check')==0) {
			$data = array_replace($data,array('updated_at'=>date('Y-m-d h:i:s')));
			if($this->Kpi_Person->save_data($table,$data,0)){
				$flag=true;
			}else{
				$flag = false;
			}
		}
		if ($this->input->post('check')==1) {
			$data = array_replace($data,array('updated_at'=>date('Y-m-d h:i:s')));
			if($this->Kpi_Person->save_data($table,$data,1)){
				$flag=true;
			}else{
				$flag = false;
			}
		}

		
		echo json_encode(array('success'=>$flag));
	}


	
	
	public function save_create_new_kpi(){
		$table = 'phppos_task_kpiperson_approve';
		$data = array(
			'kpi_id'=>$this->input->post('kpi_id'),
			'task_id'=>$this->input->post('task_id'),
			'created_at' => Date('Y-m-d h:i:s'),
		);
		if ($this->Kpi_Person->count_row($table,array('task_id'=>$data['task_id'])) >0) {
			echo json_encode(array('exists'=>true));
		}else{
			if($this->Kpi_Person->save_data($table,$data,1)){
				echo json_encode(array('success'=>true,'exists'=>false));
			}else{
				echo json_decode(array('success'=>false,'exists'=>false));
			}
		}
		
	}
	public function delete(){
		$task_id = $this->input->post('task_id');
		$arrParam['id'] = 'task_id';
		$data[] = $task_id;
		if ($this->Kpi_Person->delete('phppos_task_kpiperson_approve','task_id',$data)) {
			$success = true;
		}else{
			$success = false;
		}
		echo json_encode(array('success'=>$success));
	}

	// Xử lý form thống kê
	/**
	* View form thống kê
	**/
	public function statistic(){
		$dat = $this->Kpi_Person->get_all_task();
		foreach ($dat as $key => $value) {
			if ($value['lft']>1 && $value['rgt']<38) {
				$name_cv[] = $value['name'];
			}
		}

		$data['employees'] = $this->Kpi_Person->get_employee_join_tasks()->select('CONCAT(pp.first_name," ",pp.last_name),tu.created')->group_by('employee_id')->get()->result_array();
		// echo "<pre>";
		// print_r($data['employees']); die();
		foreach ($data['employees'] as $key => $value) {
			$date[]= date('Y',strtotime($value['created']));
		}
		for ($i=min($date); $i <=max($date)+1 ; $i++) { 
			$tmp[]= $i;
		}
		$data['year'] =$tmp;
		return $this->load->view('kpi/person/form_statistics',$data);
	}

	public function load_data_statstics(){
		$data = array(
			'id_employee'	=> $this->input->post('ma_nv'),
			'thang'			=> $this->input->post('thoi_gian'),
			'year'			=> $this->input->post('nam')
		);
		// danh sach dự án được join
		$data['du_an'] = $this->Kpi_Person->get_employee_join_tasks()->where(array('e.id'=>$data['id_employee'],'t.parent'=>0))->get()->result_array();
		$name ='<ul>';
		foreach ($data['du_an'] as $key => $value) {
			$name.='<li>'.$value['tenda'].'</li>';
		}
		$name .='<ul>';
		$data['name'] =$name;

		foreach ($data['du_an'] as $key => $value) {
			
		}
		echo json_encode($data);
	}


}