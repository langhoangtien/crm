<?php
/**
 * 
 */
require_once("Reports.php");
require_once (APPPATH . "biz/controllers/BizReports.php");
class ReportPersons extends BizReports
{
	function __construct()
	{
		// error_reporting(-1);
		// ini_set('display_errors', 'On');
		parent::__construct('');
		$this->load->library('session');
	}
	function view($id=-1){
		$data['get_all'] = $this->Contract->bao_cao_ca_nhan();
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$code_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
		$employee_logged = $this->Employee->get_logged_in_employee_info();

		// echo "<pre>"; print_r($data['employee']); die();
		$data['location_id'] = $id_location;
		if ($this->Employee->has_module_action_permission('reports','bc_ca_nhan', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			if ($this->Employee->has_module_action_permission('reports','view_scope_location', $this->Employee->get_logged_in_employee_info()->person_id) && $this->Employee->has_module_action_permission('reports','view_scope_all', $this->Employee->get_logged_in_employee_info()->person_id)) {
				$data['employee'] = $this->Employee->get_list_employees_by_location();
			}elseif ($this->Employee->has_module_action_permission('reports','view_scope_all', $this->Employee->get_logged_in_employee_info()->person_id)) {
				$data['employee'] = $this->Employee->get_list_employees_by_location();
			}elseif ($this->Employee->has_module_action_permission('reports','view_scope_location', $this->Employee->get_logged_in_employee_info()->person_id)) {
				$data['employee'] = $this->Employee->get_list_employees_by_location($id_location);
				// echo "<pre>"; print_r($data['employee']); die();
			}else{
				$data['employee_all'] = $this->Employee->get_list_employees_by_location();
				for ($i=0; $i < count($data['employee_all']); $i++) { 
					if ($data['employee_all'][$i]['id'] ==$employee_logged->id) {
						$data['employee'][] = $data['employee_all'][$i];
					}
				}
			}
			$this->load->view('reports/contract/bao_cao_hoat_dong_kinh_doanh_ca_nhan',$data);
		}else{
			$this->load->view('reports/contract/not_permission_reports.php');
		}
	}

	function export_data(){
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;

		$data['input'] = $this->input->post();
		$year =$this->input->post('year');
		$month = $this->input->post('month');
		$check = $data['input']['check'];
		$date_start = $this->input->post('date_start');
		$date_end = $this->input->post('date_end');
		$arrParam['check'] = $check;

		if ($check=='TD') {
			$title_2 = "II.	Tình hình thực hiện các dự án, công việc từ ngày $date_start đến ngày $date_end";
			$title ="BÁO CÁO CÁ NHÂN NGÀY TỪ $date_start ĐẾN $date_end";
		}elseif ($check=='QUY') {
			$title_2 = "II.	Tình hình thực hiện các dự án, công việc trong quý $month/$year";
			$title ="BÁO CÁO CÁ NHÂN QUÝ $month - NĂM $year";
		}elseif ($check=='THANG') {
			if ($month!=0) {
				$title_2 = "II.	Tình hình thực hiện các dự án, công việc trong tháng $month/$year";
				$title ="BÁO CÁO CÁ NHÂN THÁNG $month - NĂM $year";
			}else{
				$title_2 = "II.	Tình hình thực hiện các dự án, công việc trong năm $year";
				$title ="BÁO CÁO CÁ NHÂN NĂM $year";
			}
		}else{
			$title_2 = "II. Tình hình thực hiện các dự án, công việc trong năm $year";
			$title ="BÁO CÁO CÁ NHÂN NĂM $year";
		}

		
		$data['title'] =$title;
		$data['title_2'] =$title_2;
		$data['task_all'] = $this->Contract->bao_cao_ca_nhan(array('check'=>true,'location'=>true,'employee_id'=>$data['input']['id_employee']));

		if ($check=='TD') {
			$data['task_not_revenue'] = $this->Contract->task_not_revenue(array('employee_id'=>$data['input']['id_employee'],'month'=>$month,'year'=>$year,'check'=>$check,'date_start'=>$date_start,'date_end'=>$date_end));
		}else{
			if ($check=='NAM') {
				// dang thuc hien
				$data['task_not_revenue'] = $this->Contract->task_not_revenue(array('employee_id'=>$data['input']['id_employee'],'year'=>$year,'check'=>'NAM'));

			}else{
				$data['task_not_revenue'] = $this->Contract->task_not_revenue(array('employee_id'=>$data['input']['id_employee'],'month'=>$month,'year'=>$year,'check'=>$check));
			}
		}
		

		$data['task_child_delay'] = $this->Contract->get_task_child_delay();

		$data['giai_doan_dang_thuc_hien'] = $this->db->select('*')->from('phppos_tasks')->where('trangthai',1)->get()->result_array();
		$data['cong_viec_dang_thuc_hien'] =$this->Contract->cong_viec_dang_thuc_hien();
		$data['kpi'] = $this->Contract->get_ratio_employee_task();
		$data['check']='1';
		$data['thoigian']=$check;
		$data['employee_id'] = $data['input']['id_employee'];
		// $data['task_child_delay'] = $this->Contract->cong_viec_dang_thuc_hien();
		// cong viec khong tao danh thu

		$data['info_employee'] = $this->Employee->get_employees();

		$this->load->view('reports/contract/table_report',$data);
	}

	function download(){
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->getCompatibility()->setOoxmlVersion(14);
		$phpWord->getCompatibility()->setOoxmlVersion(15);
		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(12);


		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;


		$id_employee= $this->input->post('employee_id');
		$month =$this->input->post('input_month');
		$year =$this->input->post('input_year');
		$check = $this->input->post('check');
		
		$task_all = $this->Contract->bao_cao_ca_nhan(array('check'=>true,'location'=>true,'employee_id'=>$id_employee));
		// echo "<pre>"; print_r($task_all); die();
		if ($check=='TD') {
			$date_start = $this->input->post('date_start');
			$date_end = $this->input->post('date_end');
			$task_not_revenue = $this->Contract->task_not_revenue(array('employee_id'=>$id_employee,'month'=>$month,'year'=>$year,'check'=>$check,'date_start'=>$date_start,'date_end'=>$date_end));
		}else{
			if ($check=='NAM') {
				// dang thuc hien
				$task_not_revenue = $this->Contract->task_not_revenue(array('employee_id'=>$id_employee,'year'=>$year,'check'=>'NAM'));

			}else{
				$task_not_revenue = $this->Contract->task_not_revenue(array('employee_id'=>$id_employee,'month'=>$month,'year'=>$year,'check'=>$check));
			}
		}
		$task_child_delay = $this->Contract->get_task_child_delay();

		$giai_doan_dang_thuc_hien = $this->db->select('*')->from('phppos_tasks')->where('trangthai',1)->get()->result_array();
		$cong_viec_dang_thuc_hien =$this->Contract->cong_viec_dang_thuc_hien();
		$kpi = $this->Contract->get_ratio_employee_task();
		// echo "<pre>";
		// print_r($task_child_delay); die();
		$data['info_employee'] = $this->Employee->get_employees();
		foreach ($data['info_employee'] as $key => $value) {
			if ($value['employee_id']==$id_employee) {
				$name = 'Tên nhân viên: '.$value['first_name'];
				$location_name =' Khu vực: '.$value['location_name'];
				$rank =' Ngạch: '.$value['rank'];
				$level = ' Cấp bậc: '.$value['level'];
				$phone_number = ' Số điện thoại: '.$value['phone_number'];
				$email = ' Email: '.$value['email'];
				$image_id = $value['image_id'];
			}
		}
		$url_image = base_url()."app_files/view/$image_id";
		if ($check=='TD') {
			$title_2 = "II.	Tình hình thực hiện các dự án, công việc trong từ ngày $date_start đến ngày $date_end";
			$title ="BÁO CÁO CÁ NHÂN NGÀY TỪ $date_start ĐẾN $date_end";
		}elseif ($check=='QUY') {
			$title_2 = "II.	Tình hình thực hiện các dự án, công việc trong quý $month/$year";
			$title ="BÁO CÁO CÁ NHÂN QUÝ $month - NĂM $year";
		}elseif ($check=='THANG') {
			if ($month!=0) {
				$title_2 = "II.	Tình hình thực hiện các dự án, công việc trong tháng $month/$year";
				$title ="BÁO CÁO CÁ NHÂN THÁNG $month - NĂM $year";
			}else{
				$title_2 = "II.	Tình hình thực hiện các dự án, công việc trong năm $year";
				$title ="BÁO CÁO CÁ NHÂN NĂM $year";
			}
		}else{
			$title_2 = "II. Tình hình thực hiện các dự án, công việc trong năm $year";
			$title ="BÁO CÁO CÁ NHÂN NĂM $year";
		}
		if ($check=='THANG') {
			$date= date_create($year.'-'.$month);
			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date,"Y-m-d");
		}elseif ($check=='QUY') {
			switch ($month) {
				case 1:
				$date_ss = date_create($year.'-01');
				$date = date_create($year.'-03');
				break;
				case 2:
				$date_ss = date_create($year.'-04');
				$date = date_create($year.'-06');
				break;
				case 3:
				$date_ss = date_create($year.'-07');
				$date = date_create($year.'-09');
				break;
				case 4:
				$date_ss = date_create($year.'-10');
				$date = date_create($year.'-12');
				break;
			}
			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date_ss,"Y-m-d");
		}elseif ($check=='NAM') {
			$date = date_create($year.'-12');
			$date_ss = date_create($year.'-01');

			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date_ss,"Y-m-d");
		}else{
			$date1 = date_create($date_start);
			$date2 = date_create($date_end);
			$date_e = date_format($date2,"Y-m-d");
			$date_s = date_format($date1,"Y-m-d");
		}
		
		
    // add style settings for the title and paragraph
		$style_header = array(
			'font'=>true, 
			'size'=>16,
		);
		$section = $phpWord->addSection();

		$phpWord->addParagraphStyle('pStyle', ['align' => 'center', 'spaceAfter' => 100]);
		$phpWord->addFontStyle('StyleHeader', ['bold' => true, 'size' => 16]);
		$phpWord->addFontStyle('hStyle', ['bold' => true, 'size' => 13]);
		$section->addText($title, 'StyleHeader', 'pStyle');
		$section->addTextBreak();
		$section->addText('I. Thông tin nhân viên', null, 'hStyle');
		$count_arr_img = count(explode('/',$url_image));
		if (explode('/',$url_image)[$count_arr_img-1]!=null) {
			$section->addImage(
				$url_image,
				array(
					'width'         => 150,
					'height'        => 160,
					'marginTop'     => -1,
					'marginLeft'    => -1,
					'wrappingStyle' => 'behind'
				)
			);
		}

		
		
		$phpWord->addTableStyle('info_employee',null, null);
		$table_info = $section->addTable('info_employee');
		$table_info->addRow();
		$table_info->addCell(6000)->addText($name);
		$table_info->addRow();
		$table_info->addCell(6000)->addText($location_name);
		$table_info->addRow();
		$table_info->addCell(6000)->addText($rank);
		$table_info->addRow();
		$table_info->addCell(6000)->addText($level);
		$table_info->addRow();
		$table_info->addCell(6000)->addText($phone_number);
		$table_info->addRow();
		$table_info->addCell(6000)->addText($email);

		$section->addText($title_2, null, 'hStyle');
		$section->addText('1. Thông tin các dự án, công việc đang thực hiện', null, 'hStyle');
		// Define table style arrays
		$styleTable = ['borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80];
		$styleFirstRow = ['borderBottomSize' => 5];
		// $styleFirstRow = ['borderBottomSize' => 5, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF'];
		$styleCell = ['valign' => 'center'];
		$fontStyle_thead = ['bold' => true, 'align' => 'center'];
		$font_bold = array('bold'=>true);
		$fontStyle = array('align' => 'center');

		$style_number = array('align' => 'right');

		$phpWord->addTableStyle('myOwnTableStyle', $styleTable,null);
		$table = $section->addTable('myOwnTableStyle');
		$table->addRow();
		$table->addCell(500)->addText('STT', $fontStyle_thead);
		$table->addCell(2000)->addText('Tên dự án', $fontStyle_thead);
		$table->addCell(2000)->addText('Mã Hợp đồng', $fontStyle_thead);
		$table->addCell(2000)->addText('Hợp đồng liên quan', $fontStyle_thead);
		$table->addCell(2000)->addText('Tên dịch vụ', $fontStyle_thead);
		$table->addCell(2000)->addText('Người phụ trách', $fontStyle_thead);
		$table->addCell(2000)->addText('Người tham gia', $fontStyle_thead);
		$table->addCell(2000)->addText('Tình trạng của dự án', $fontStyle_thead);

		// echo "<pre>";
		// print_r($month); die();

		if ($check=='NAM') {
			$stt=1;
			if (!empty($task_all)) {
				foreach ($task_all as $key => $value) {
					$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
					$date_finish = strtotime($value['date_finish']);
					$date_end = strtotime($value['date_end']);
					$date_start = strtotime($value['date_start']);
					$date_pheduyet =  strtotime($value['date_pheduyet']);
					if ($value['pheduyet']!=1){
						if (($date_start - strtotime($date_e))<=0)
						{
							$nguoi_tham_gia ='';
							$nguoi_pt ='';
							if (!empty($data['nguoi_tham_gia_da'])) {
								foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
									if ($valpt['is_implement']==1) {
										$nguoi_pt .= '- '.$valpt['username'];
									}
									$nguoi_tham_gia .= '- '.$valpt['username'];
								}
							}
							$table->addRow();
							$table->addCell(500)->addText($stt, $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['code']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_pt), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
							$tien_do = round($value['progress'],2);
							$table->addCell(2000)->addText(htmlspecialchars('Đang thực hiện ('.$tien_do.'%)'), $fontStyle);
							$stt++;
						}
					}
					elseif ($value['pheduyet']==1) {
						if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0)
						{
							$nguoi_tham_gia ='';
							$nguoi_pt ='';
							if (!empty($data['nguoi_tham_gia_da'])) {
								foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
									if ($valpt['is_implement']==1) {
										$nguoi_pt .= '- '.$valpt['username'].'';
									}
									$nguoi_tham_gia .= '- '.$valpt['username'].'';
								}
							}
							$table->addRow();
							$table->addCell(500)->addText($stt, $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['code']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_pt), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
							$tien_do = round($value['progress'],2);
							$table->addCell(2000)->addText('Đã nghiệm thu ('.$tien_do.'%)', $fontStyle);
							$stt++;
						}
						if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
							$nguoi_tham_gia ='';
							$nguoi_pt ='';
							if (!empty($data['nguoi_tham_gia_da'])) {
								foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
									if ($valpt['is_implement']==1) {
										$nguoi_pt .= '- '.$valpt['username'];
									}
									$nguoi_tham_gia .= '- '.$valpt['username'];
								}
							}
							$table->addRow();
							$table->addCell(500)->addText($stt, $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['code']), $fontStyle);
							$table->addCell(2000)->addText( htmlspecialchars($value['name_contract']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_pt), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
							$tien_do = round($value['progress'],2);
							$table->addCell(2000)->addText('Đang thực hiện ('.$tien_do.'%)', $fontStyle);
							$stt++;
						}
					}

				}
			}
		}else{
			if (!empty($task_all)) {
				$stt=1;
				foreach ($task_all as $key => $value) {
					$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
					$date_finish = strtotime($value['date_finish']);
					$date_end = strtotime($value['date_end']);
					$date_start = strtotime($value['date_start']);
					$date_pheduyet =  strtotime($value['date_pheduyet']);
					if ($value['pheduyet']!=1)
					{
						if (($date_start - strtotime($date_e))<=0)
						{
							$nguoi_tham_gia ='';
							$nguoi_pt ='';
							if (!empty($data['nguoi_tham_gia_da'])) {
								foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
									if ($valpt['is_implement']==1) {
										$nguoi_pt .= '- '.$valpt['username'];
									}
									$nguoi_tham_gia .= '- '.$valpt['username'];
								}
							}
							$table->addRow();
							$table->addCell(500)->addText($stt, $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['code']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_pt), $fontStyle);
							$table->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
							$tien_do = round($value['progress'],2);
							$table->addCell(2000)->addText('Đang thực hiện ('.$$tien_do.'%)', $fontStyle);
							$stt++;
						}

					}
				}
			}
		}
		
		
		$section->addText('2. Tình hình hoàn thành các dự án, công việc ', null, 'hStyle');
		$phpWord->addTableStyle('congviec2', $styleTable,null);
		$table2 = $section->addTable('congviec2');
		$table2->addRow();
		$table2->addCell(500)->addText('STT', $fontStyle_thead);
		$table2->addCell(2000)->addText('Tên dự án', $fontStyle_thead);
		$table2->addCell(2000)->addText('Giai đoạn đang thực hiện	', $fontStyle_thead);
		$table2->addCell(2000)->addText('Công việc đang thực hiện	', $fontStyle_thead);
		$table2->addCell(2000)->addText('Tỷ lệ đã hoàn thành của dự án	', $fontStyle_thead);
		$table2->addCell(2000)->addText('% Đóng góp của nhân viên vào cả dự án', $fontStyle_thead);
		$table2->addCell(2000)->addText('% Công việc thực hiện chậm', $fontStyle_thead);
		// echo "<pre>"; print_r($task_all); die();
		if (!empty($task_all)) { 
			$stt=1; 
			foreach ($task_all as $key => $value)
			{
				$date_finish = strtotime($value['date_finish']);
				$date_end = strtotime($value['date_end']);
				$date_start = strtotime($value['date_start']);
				$date_pheduyet =  strtotime($value['date_pheduyet']);
				if ($value['pheduyet']!=1){
					if (($date_start - strtotime($date_e))<=0){
						$gd='';
						if (!empty($giai_doan_dang_thuc_hien)) {
							foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
								if ($value_gd['parent'] == $value['task_id']) {
									$gd .= '- '.$value_gd['name'];
								}
							}
						}
						$cv='';
						if (!empty($cong_viec_dang_thuc_hien)) {
							foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
								if ($value_cv['project_id']==$value['task_id']) {
									$cv.='- '.$value_cv['name'];
								}
							}
						}

						$td = round($value['progress'],2).'(%)';
						$tl='';
						if (!empty($kpi)) {
							foreach ($kpi as $key => $value_kpi) {
								if ($value_kpi['task_id'] == $value['task_id']) {
									$arr_ratio = json_decode($value_kpi['ratio'],true);
									$arr_employeeID = json_decode($value_kpi['employee_id'],true);
									for($i=0;$i<count($arr_employeeID);$i++){
										if ($arr_employeeID[$i] == $employee_id) {
											$tl = $arr_ratio[$i].'%';
										}
									}
								}
							} 
						}
						$cv_delay='';
						if (!empty($task_child_delay)) {
							foreach ($task_child_delay as $key => $value_delay) {
								if ($value_delay['project_id'] == $value['task_id']) {
									if ($value_delay['progress']==100) {
										if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
											$cv_delay.= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)';
										}
									}else{
										if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
											$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)';
										}
									}
								}
							}
						}
						$table2->addRow();
						$table2->addCell(500)->addText($stt, $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($gd), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($td), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($tl), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
						$stt++; 
					}
				}
				if ($check=='NAM'|| $check=='THANG'|| $check=='QUY') {
					if ($value['pheduyet']==1)
					{
						if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0)
						{
							$gd='';
							if (!empty($giai_doan_dang_thuc_hien)) {
								foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
									if ($value_gd['parent'] == $value['task_id']) {
										$gd .= '- '.$value_gd['name'];
									}
								}
							}
							$cv='';
							if (!empty($cong_viec_dang_thuc_hien)) {
								foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
									if ($value_cv['project_id']==$value['task_id']) {
										$cv.='- '.$value_cv['name'];
									}
								}
							}

							$td = round($value['progress'],2).'(%)';
							$tl='';
							if (!empty($kpi)) {
								foreach ($kpi as $key => $value_kpi) {
									if ($value_kpi['task_id'] == $value['task_id']) {
										$arr_ratio = json_decode($value_kpi['ratio'],true);
										$arr_employeeID = json_decode($value_kpi['employee_id'],true);
										for($i=0;$i<count($arr_employeeID);$i++){
											if ($arr_employeeID[$i] == $employee_id) {
												$tl = $arr_ratio[$i].'%';
											}
										}
									}
								} 
							}
							$cv_delay='';
							if (!empty($task_child_delay)) {
								foreach ($task_child_delay as $key => $value_delay) {
									if ($value_delay['project_id'] == $value['task_id']) {
										if ($value_delay['progress']==100) {
											if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
												$cv_delay.= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)';
											}
										}else{
											if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
												$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)';
											}
										}
									}
								}
							}
							$table2->addRow();
							$table2->addCell(500)->addText($stt, $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($gd), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($cv), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($td), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($tl), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
							$stt++; 
						}
						if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0)
						{
							$gd='';
							if (!empty($giai_doan_dang_thuc_hien)) {
								foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
									if ($value_gd['parent'] == $value['task_id']) {
										$gd .= '- '.$value_gd['name'];
									}
								}
							}
							$cv='';
							if (!empty($cong_viec_dang_thuc_hien)) {
								foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
									if ($value_cv['project_id']==$value['task_id']) {
										$cv.='- '.$value_cv['name'];
									}
								}
							}

							$td = round($value['progress'],2).'(%)';
							$tl='';
							if (!empty($kpi)) {
								foreach ($kpi as $key => $value_kpi) {
									if ($value_kpi['task_id'] == $value['task_id']) {
										$arr_ratio = json_decode($value_kpi['ratio'],true);
										$arr_employeeID = json_decode($value_kpi['employee_id'],true);
										for($i=0;$i<count($arr_employeeID);$i++){
											if ($arr_employeeID[$i] == $employee_id) {
												$tl = $arr_ratio[$i].'%';
											}
										}
									}
								} 
							}
							$cv_delay='';
							if (!empty($task_child_delay)) {
								foreach ($task_child_delay as $key => $value_delay) {
									if ($value_delay['project_id'] == $value['task_id']) {
										if ($value_delay['progress']==100) {
											if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
												$cv_delay.= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)';
											}
										}else{
											if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
												$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%)';
											}
										}
									}
								}
							}
							$table2->addRow();
							$table2->addCell(500)->addText($stt, $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($gd), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($cv), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($td), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($tl), $fontStyle);
							$table2->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
							$stt++; 
						}

					}
				}
			}
		}


		$section->addText('III. Phân tích, đánh giá nhân viên', null, 'hStyle');
		$section->addText('1. Doanh thu nhân viên', null, 'hStyle');
		if ($check=='THANG' || $check=='QUY' || $check=='NAM') {
			$phpWord->addTableStyle('bang_doanh_thu', $styleTable,null);
			$bang_doanh_thu = $section->addTable('bang_doanh_thu');
			$bang_doanh_thu->addRow();
			if ($check=='THANG') {
				
				$time='month';
				$title_dt = 'Tháng';
				$number = 12;
			}elseif ($check=='QUY') {
				$time='quater';
				$title_dt = 'Quý';
				$number = 4;
			}else{
				$time='year';
				$title_dt = 'Năm';
				$number = 5;
				$month =5;
				
			}
			$revenue_person = parent::employee_graph_filter_word(array('time'=>$time, 'location'=>$id_location,'colleague'=>$id_employee,'number'=>$number,'value'=>$year));
			$bang_doanh_thu->addCell(4000)->addText($title_dt, $fontStyle_thead);
			$bang_doanh_thu->addCell(4000)->addText('Doanh thu', $fontStyle_thead);
			$data_series = $revenue_person['series'];
			for ($i=0; $i < $month ; $i++) { 
				$bang_doanh_thu->addRow();
				$bang_doanh_thu->addCell(4000)->addText($revenue_person['categories'][$i], $fontStyle);
				$bang_doanh_thu->addCell(4000)->addText($data_series['data'][$i], $style_number,$fontStyle);
			}
		}

		$section->addTextBreak();
		$section->addText('2. Biểu đồ năng lực', null, 'hStyle');
		$section->addTextBreak();
		$section->addText('3. Thống kê các công việc không tạo doanh thu', null, 'hStyle');
		$section->addTextBreak();
		// // cong viec khong tao doanh thu
		$phpWord->addTableStyle('tb_not_revenue', $styleTable,null);
		$tb_not_revenue = $section->addTable('tb_not_revenue');

		if (!empty($task_not_revenue)) {
			$stt=1;
			foreach ($task_not_revenue as $key => $value) {
				$tb_not_revenue->addRow();
				$tb_not_revenue->addCell(500)->addText($stt, $fontStyle);
				$tb_not_revenue->addCell(4000)->addText($value['name'], $fontStyle);
				$it = date_diff(date_create($value['modified']), date_create($value['date_end']));

				$m = $it->format('%m');
				$y = $it->format('%y');
				$d = $it->format('%d');
				if ($y>0) {
					$tb_not_revenue->addCell(4000)->addText($it->format('%y năm %m tháng %d ngày'), $fontStyle);
				}elseif ($m>0) {
					$tb_not_revenue->addCell(4000)->addText($it->format('%m tháng %d ngày'), $fontStyle);
				}elseif($d>0){
					$tb_not_revenue->addCell(4000)->addText($d.' ngày', $fontStyle);
				}else{
					$tb_not_revenue->addCell(4000)->addText($it->format('%h giờ'), $fontStyle);
				}
				$stt++;
			}
		}

		
		$section->addText('IV. Phân tích, đánh giá nhan viên', null, 'hStyle');
		$section->addText('1. Biểu đồ đóng góp doanh thu', null, 'hStyle');
		$section->addText('...', null, 'hStyle');
		
		$section->addText('2. Đánh giá của lãnh đạo', null, 'hStyle');
		$section->addTextBreak();
		$section->addText('………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………………', null, null);

		$section->addTextBreak();
		$phpWord->addTableStyle('footer',null, null);
		$footer = $section->addTable('footer');
		$footer->addRow();
		$footer->addCell(9000)->addText();
		$footer->addCell(3000)->addText('Người lập báo cáo',null,$fontStyle_thead);


		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

		$filename = $title.'.docx';
		$objWriter->save($filename);
		// send results to browser to download
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		flush();
		readfile($filename);
		unlink($filename); 
		exit;
	}
}
?>