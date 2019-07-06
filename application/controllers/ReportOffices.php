<?php
/**
 * 
 */
use PhpOffice\PhpWord\Shared\Converter;
require_once("Reports.php");
require_once (APPPATH . "biz/controllers/BizReports.php");
require_once ("Kpi.php");
class ReportOffices extends Kpi
{
	function __construct()
	{
// 		error_reporting(-1);
// ini_set('display_errors', 'On');
		parent::__construct();
		$this->load->model('Ratings');
		$this->load->library('PHPWord');
	}
	function view($id=-1)
	{
		if ($this->Employee->has_module_action_permission('reports','bc_quan_tri_phong', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
			$data['location_id'] = $id_location;
			$this->load->view('reports/contract/bao_cao_quan_tri_phong',$data);
		}else{
			$this->load->view('reports/contract/not_permission_reports.php');
		}
		
	}
	/*
	* 
	*/
	function export_data()
	{
		$data['check']='BCP';
		$data['check_tp'] = $this->input->post('check');
		$check = $this->input->post('check');
		$data['datetime'] =$this->input->post();
		$month = $this->input->post('month');
		$year = $this->input->post('year');

		$arrParam['check'] = 'BCP';
		$arrParam['contract_completed'] =true;
		$arrParam['location']=true;
		
		$data['contract_complete'] = $this->Contract->bao_cao_ca_nhan($arrParam);
		$data['task_all'] = $this->Contract->bao_cao_ca_nhan(array('check'=>true,'location'=>true));
		$data['contract_done'] = $this->Contract->get_contract_value(array('done'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'task'=>true));
		$data['contract_liquidated'] = $this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'task'=>true));

		$data['nguoi_tham_gia_da'] =$this->Contract->get_employee_join_tasks_contract();
		$data['du_an'] =  $this->Contract->bao_cao_ca_nhan(array('check'=>$check,'month'=>$month,'year'=>$year,'hoanthanh'=>true,'date_st'=>true,'location'=>true));
		// muc I.1
		$data['khach_hang_du_an'] =$this->Contract->get_customer_task();
		// muc I.2
		$data['giai_doan_dang_thuc_hien'] = $this->db->select('*')->from('phppos_tasks')->where('trangthai',1)->get()->result_array();
		$data['cong_viec_dang_thuc_hien'] =$this->Contract->cong_viec_dang_thuc_hien();
		$data['thong_ke_cong_viec_qua_han'] = $this->Contract->thong_ke_cong_viec_qua_han();
		$data['tasks'] = $this->db->select('*')->from('phppos_tasks')->where('level',0)->get()->result_array();
		if ($check=='NAM') {
			$data['task_child_delay'] = $this->Contract->get_task_child_delay(array('date_time'=>true,'check'=>$check,'year'=>$year,'location'=>true));
		}else{
			$data['task_child_delay'] = $this->Contract->get_task_child_delay(array('date_time'=>true,'check'=>$check,'month'=>$month, 'year'=>$year,'location'=>true));
		}
		

		$data['cong_viec_cham'] =$this->Contract->get_task_child_delay();
		if ($check==='QUY') {
			$data['year_sub']=$month;
		}
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$data['location_id'] =$id_location;
		if ($this->Employee->has_module_action_permission('reports','bc_quan_tri_phong', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			$this->load->view('reports/contract/table_report',$data);
		}else{
			$this->load->view('reports/contract/not_permission_reports.php');
		}
	}

	function exportword(){
		$check='BCP';
		$check_tp = $this->input->post('check');
		$datetime =$this->input->post();
		$month = $this->input->post('input_month');
		$year = $this->input->post('input_year');

		$arrParam['check'] = 'BCP';
		$arrParam['contract_completed'] =true;
		$arrParam['location']=true;
		
		$contract_complete = $this->Contract->bao_cao_ca_nhan($arrParam);
		$task_all = $this->Contract->bao_cao_ca_nhan(array('check'=>true,'location'=>true));
		$contract_done = $this->Contract->get_contract_value(array('done'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'task'=>true));
		$contract_liquidated = $this->Contract->get_contract_value(array('liquidated'),null,array('month'=>$month,'year'=>$year,'date_payment'=>true,'task'=>true));

		$nguoi_tham_gia_da =$this->Contract->get_employee_join_tasks_contract();
		$du_an =  $this->Contract->bao_cao_ca_nhan(array('check'=>$check_tp,'month'=>$month,'year'=>$year,'hoanthanh'=>true,'date_st'=>true,'location'=>true));
		// muc I.1
		$khach_hang_du_an =$this->Contract->get_customer_task();
		// muc I.2
		$giai_doan_dang_thuc_hien = $this->db->select('*')->from('phppos_tasks')->where('trangthai',1)->get()->result_array();
		$cong_viec_dang_thuc_hien =$this->Contract->cong_viec_dang_thuc_hien();
		$thong_ke_cong_viec_qua_han = $this->Contract->thong_ke_cong_viec_qua_han();
		$tasks = $this->db->select('*')->from('phppos_tasks')->where('level',0)->get()->result_array();
		if ($check_tp=='NAM') {
			$task_child_delay = $this->Contract->get_task_child_delay(array('date_time'=>true,'check'=>$check_tp,'year'=>$year,'location'=>true));
		}else{
			$task_child_delay = $this->Contract->get_task_child_delay(array('date_time'=>true,'check'=>$check_tp,'month'=>$month, 'year'=>$year,'location'=>true));
		}
		

		$cong_viec_cham =$this->Contract->get_task_child_delay();
		
		$id_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->location_id;
		$name_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->name;

		if ($check_tp=='TD') {
			$title ="BÁO CÁO QUẢN TRỊ PHÒNG NGÀY $month";
		}elseif ($check_tp=='QUY') {
			$title ="BÁO CÁO QUẢN TRỊ PHÒNG QUÝ $month - NĂM $year";
		}elseif ($check_tp=='THANG') {
			if ($month!=0) {
				$title ="BÁO CÁO QUẢN TRỊ PHÒNG THÁNG $month - NĂM $year";
			}else{
				$title ="BÁO CÁO QUẢN TRỊ PHÒNG NĂM $year";
			}
		}else{
			$title ="BÁO CÁO QUẢN TRỊ PHÒNG $year";
		}

		if ($check_tp=='THANG') {
			$time = 'tháng '.$month.'/'.$year;
		}elseif($check_tp=='QUY'){
			$time = 'quý '.$month.'/'.$year;
			$kq = $this->Ratings->view_data($year,$month,'DDT','KQ');
			$doanhthu_KQ = json_decode($kq['data_room_rate'],true);
			$kh = $this->Ratings->view_data($year,$month,'DDT','KH');
			$doanhthu_KH = json_decode($kh['data_room_rate'],true);
			$report_contracts = $this->Contract->kpi_report_contracts($year);
		}else{
			$time = 'năm '.$year;
		}

		if ($check_tp=='THANG') {
			$date= date_create($year.'-'.$month);
			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date,"Y-m-d");
		}elseif ($check_tp=='QUY') {
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
		}else{
			$date = date_create($year.'-12');
			$date_ss = date_create($year.'-01');
			$date_e = date_format($date,"Y-m-t");
			$date_s = date_format($date_ss,"Y-m-d");
		}
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$phpWord->getCompatibility()->setOoxmlVersion(14);
		$phpWord->getCompatibility()->setOoxmlVersion(15);
		$section = $phpWord->addSection();
		$phpWord->setDefaultFontName('Times New Roman');
		$phpWord->setDefaultFontSize(12);
		$phpWord->addParagraphStyle('pStyle', ['align' => 'center', 'spaceAfter' => 100]);
		$phpWord->addFontStyle('StyleHeader', ['bold' => true, 'size' => 16]);
		$phpWord->addFontStyle('hStyle', ['bold' => true, 'size' => 13]);
		$section->addText($title, 'StyleHeader', 'pStyle');
		$section->addTextBreak();
		
		$section->addTextBreak();

		$styleTable = ['borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80];
		$styleFirstRow = ['borderBottomSize' => 5];
		$styleCell = ['valign' => 'center'];
		$fontStyle_thead = ['bold' => true, 'align' => 'center'];
		$fontStyle = ['align' => 'center'];
		$fontR = ['align' => 'right'];
		if ($check_tp=='THANG') {
			$section->addText('I. Tình hình thực hiện các hợp đồng trong '.$time, null, 'hStyle');
			$phpWord->addTableStyle('table1', $styleTable,null);
			$table1 = $section->addTable('table1');
			$table1->addRow();
			$table1->addCell(500)->addText('STT', $fontStyle_thead);
			$table1->addCell(2000)->addText('Tên dự án', $fontStyle_thead);
			$table1->addCell(2000)->addText('Tên hợp đồng', $fontStyle_thead);
			$table1->addCell(2000)->addText('Tên khách hàng', $fontStyle_thead);
			$table1->addCell(2000)->addText('Loại dịch vụ', $fontStyle_thead);
			$table1->addCell(2000)->addText('Người phụ trách', $fontStyle_thead);
			$table1->addCell(2000)->addText('Người tham gia', $fontStyle_thead);
			$table1->addCell(2000)->addText('Tình trạng của hợp đồng', $fontStyle_thead);
			
			$stt=1;
			if (!empty($contract_complete)) {
				foreach ($contract_complete as $key => $value) {
					if ($value['status']=='progress') {
						if (strtotime($value['ct_date_start'])-strtotime($date_e)<=0) {
							
							$nguoi_phu_trach='';
							$nguoi_tham_gia='';
							if (!empty($nguoi_tham_gia_da)) {
								foreach ($nguoi_tham_gia_da as $key => $value_tg) {
									if ($value_tg['id'] == $value['task_id']) {
										if ($value_tg['is_implement']==1) {
											$nguoi_phu_trach .= '- '.$value_tg['username'];
										}
										if ($value_tg['is_join']==1) {
											$nguoi_tham_gia .='- '.$value_tg['username'];
										}
									}
								}
							}
							$table1->addRow();
							$table1->addCell(500)->addText($stt, $fontStyle);
							$table1->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
							$table1->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
							$table1->addCell(2000)->addText(htmlspecialchars($value['ten_doi_tac']), $fontStyle);
							$table1->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
							$table1->addCell(2000)->addText($nguoi_phu_trach, $fontStyle);
							$table1->addCell(2000)->addText($nguoi_tham_gia, $fontStyle);
							$table1->addCell(2000)->addText('Đang thực hiện', $fontStyle);
							$stt++;
						}
					}
				}
			}
			if (!empty($contract_done)) {
				foreach ($contract_done as $key => $value) {
					$nguoi_phu_trach ='';
					$nguoi_tham_gia  ='';
					if (!empty($nguoi_tham_gia_da)) {
						foreach ($nguoi_tham_gia_da as $key => $value_tg) {
							if ($value_tg['id'] == $value['task_id']) {
								if ($value_tg['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$value_tg['username'].', ';
								}
								if ($value_tg['is_join']==1) {
									$nguoi_tham_gia .= '- '.$value_tg['username'].', ';
								}
							}
						}
					}
					$table1->addRow();
					$table1->addCell(500)->addText($stt, $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['ten_doi_tac']), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($nguoi_phu_trach), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
					$table1->addCell(2000)->addText('Đã nghiệm thu', $fontStyle);
					$stt++;
				}
			}

			if (!empty($contract_liquidated)) {
				foreach ($contract_liquidated as $key => $value) {
					$nguoi_phu_trach='';
					$nguoi_tham_gia='';
					if (!empty($nguoi_tham_gia_da)) {
						foreach ($nguoi_tham_gia_da as $key => $value_tg) {
							if ($value_tg['id'] == $value['task_id']) {
								if ($value_tg['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$value_tg['username'];
								}
								if ($value_tg['is_join']==1) {
									$nguoi_tham_gia .='- '.$value_tg['username'];
								}
							}
						}
					}
					$table1->addRow();
					$table1->addCell(500)->addText($stt, $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['name_contract']), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['ten_doi_tac']), $fontStyle);
					$table1->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
					$table1->addCell(2000)->addText($nguoi_phu_trach, $fontStyle);
					$table1->addCell(2000)->addText($nguoi_tham_gia, $fontStyle);
					$table1->addCell(2000)->addText('Đã thanh lý', $fontStyle);
					$stt++;
				}
			}
			$section->addTextBreak();
			$section->addText('II.	Tình hình thực hiện các dự án, công việc trong '.$time, null, 'hStyle');
		}else{
			$section->addTextBreak();
			$section->addText('I.	Tình hình thực hiện các dự án, công việc trong '.$time, null, 'hStyle');
		}
		
		$section->addText('1. Thông tin các dự án, công việc đang thực hiện ', null, 'hStyle');
		$phpWord->addTableStyle('table2', $styleTable,null);
		$table2 = $section->addTable('table2');
		$table2->addRow();
		$table2->addCell(500)->addText('STT', $fontStyle_thead);
		$table2->addCell(2000)->addText('Tên dự án', $fontStyle_thead);
		if ($check_tp=='THANG') {
			$table2->addCell(2000)->addText('Khách hàng', $fontStyle_thead);
		}else{
			$table2->addCell(2000)->addText('Hợp đồng liên quan', $fontStyle_thead);
		}
		

		$table2->addCell(2000)->addText('Loại dịch vụ', $fontStyle_thead);
		$table2->addCell(2000)->addText('Người phụ trách', $fontStyle_thead);
		$table2->addCell(2000)->addText('Người tham gia', $fontStyle_thead);
		$table2->addCell(2000)->addText('Tình trạng của dự án', $fontStyle_thead);

		

		$st =1;
		if (!empty($task_all)) {
			foreach ($task_all as $key => $value) 
			{
				$date_finish = strtotime($value['date_finish']);
				$date_end = strtotime($value['date_end']);
				$date_start = strtotime($value['date_start']);
				$date_pheduyet =  strtotime($value['date_pheduyet']);
				$data['nguoi_tham_gia_da'] =$this->Kpi_Person->get_employee_join_tasks(array('task_id'=>$value['task_id']))->where('(tu.is_join=1 OR tu.is_implement=1)')->group_by('e.id')->get()->result_array();
				if ($value['pheduyet']!=1) {
					if (($date_start - strtotime($date_e))<=0) 
					{
						$ten_kh = '';
						if ($check_tp=='THANG') {
							if (!empty($khach_hang_du_an)) {
								foreach ($khach_hang_du_an as $key => $valuekh) {
									if ($value['task_id']==$valuekh['task_id']) {
										$ten_kh = $valuekh['customer_name'];
									}
								}
							}
						}else{
							$ten_kh = $value['name_contract'];
						}
						$nguoi_tham_gia ='';
						$nguoi_phu_trach ='';
						if (!empty($data['nguoi_tham_gia_da'])) {
							foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
								if ($valpt['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$valpt['username'].',';
								}
								if ($valpt['is_join']==1) {
									$nguoi_tham_gia .= '- '.$valpt['username'].',';
								}

							}
						}
						$table2->addRow();
						$table2->addCell(500)->addText($st, $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($ten_kh), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($nguoi_phu_trach), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
						$table2->addCell(2000)->addText('Đang thực hiện ('.round($value['progress'],2).'%)', $fontStyle);
						$st++;
					}
				}elseif ($value['pheduyet']==1){
					if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
						$ten_kh = '';
						if ($check_tp=='THANG') {
							if (!empty($khach_hang_du_an)) {
								foreach ($khach_hang_du_an as $key => $valuekh) {
									if ($value['task_id']==$valuekh['task_id']) {
										$ten_kh = $valuekh['customer_name'];
									}
								}
							}
						}else{
							$ten_kh = $value['name_contract'];
						}
						$nguoi_tham_gia ='';
						$nguoi_phu_trach ='';
						if (!empty($data['nguoi_tham_gia_da'])) {
							foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
								if ($valpt['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$valpt['username'].',';
								}
								if ($valpt['is_join']==1) {
									$nguoi_tham_gia .= '- '.$valpt['username'].',';
								}

							}
						}
						$table2->addRow();
						$table2->addCell(500)->addText($st, $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($ten_kh), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($nguoi_phu_trach), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
						$table2->addCell(2000)->addText('Đã hoàn thành ('.round($value['progress'],2).'%)', $fontStyle);
						$st++;
					}
					if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0) {
						$ten_kh = '';
						if ($check_tp=='THANG') {
							if (!empty($khach_hang_du_an)) {
								foreach ($khach_hang_du_an as $key => $valuekh) {
									if ($value['task_id']==$valuekh['task_id']) {
										$ten_kh = $valuekh['customer_name'];
									}
								}
							}
						}else{
							$ten_kh = $value['name_contract'];
						}
						$nguoi_tham_gia ='';
						$nguoi_phu_trach ='';
						if (!empty($data['nguoi_tham_gia_da'])) {
							foreach ($data['nguoi_tham_gia_da'] as $key => $valpt) {
								if ($valpt['is_implement']==1) {
									$nguoi_phu_trach .= '- '.$valpt['username'].',';
								}
								if ($valpt['is_join']==1) {
									$nguoi_tham_gia .= '- '.$valpt['username'].',';
								}

							}
						}
						$table2->addRow();
						$table2->addCell(500)->addText($st, $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($ten_kh), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($value['ten_dv']), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($nguoi_phu_trach), $fontStyle);
						$table2->addCell(2000)->addText(htmlspecialchars($nguoi_tham_gia), $fontStyle);
						$table2->addCell(2000)->addText('Đang thực hiện ('.round($value['progress'],2).'%)', $fontStyle);
						$st++;
					}
				}
			}
		}



		$section->addText('2. Tình hình hoàn thành các dự án, công việc ', null, 'hStyle');
		$phpWord->addTableStyle('table3', $styleTable,null);
		$table3 = $section->addTable('table3');
		$table3->addRow();
		$table3->addCell(500)->addText('STT', $fontStyle_thead);
		$table3->addCell(2000)->addText('Tên dự án', $fontStyle_thead);
		$table3->addCell(2000)->addText('Giai đoạn đang thực hiện', $fontStyle_thead);
		$table3->addCell(2000)->addText('Công việc đang thực hiện', $fontStyle_thead);
		$table3->addCell(2000)->addText('Tỷ lệ hoàn thành dự án', $fontStyle_thead);
		$table3->addCell(2000)->addText('Phần trăm công việc thực hiện chậm', $fontStyle_thead);
		$stt=1; 
		if (!empty($task_all))
		{
			foreach ($task_all as $key => $value)
			{
				$date_finish = strtotime($value['date_finish']);
				$date_end = strtotime($value['date_end']);
				$date_start = strtotime($value['date_start']);
				$date_pheduyet =  strtotime($value['date_pheduyet']);
				if ($value['pheduyet']!=1) {
					if (($date_start - strtotime($date_e))<=0) 
					{
						$giai_doan ='';
						if (!empty($giai_doan_dang_thuc_hien)) {
							foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
								if ($value_gd['parent'] == $value['task_id']) {
									$giai_doan .=  '- '.$value_gd['name'].', ';
								}
							}
						}
						$cong_viec ='';
						if (!empty($cong_viec_dang_thuc_hien)) {
							foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
								if ($value_cv['project_id']==$value['task_id']) {
									$cong_viec .= '- '.$value_cv['name'].', ';
								}
							}
						}
						$cv_delay='';
						if (!empty($cong_viec_cham)) {
							foreach ($cong_viec_cham as $key => $value_delay) {
								if ($value_delay['project_id'] == $value['task_id']) {
									if ($value_delay['progress']==100) {
										if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
											$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}else{
										if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
											$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}
								}
							}
						}
						$table3->addRow();
						$table3->addCell(500)->addText($stt, $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($giai_doan), $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($cong_viec), $fontStyle);
						$table3->addCell(2000)->addText(round($value['progress'],2).'(%)', $fontR);
						$table3->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
						$stt++; 
					}
				}elseif ($value['pheduyet']==1){
					if (($date_pheduyet - strtotime($date_e))<=0 && ($date_pheduyet - strtotime($date_s)) >=0) {
						$giai_doan ='';
						if (!empty($giai_doan_dang_thuc_hien)) {
							foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
								if ($value_gd['parent'] == $value['task_id']) {
									$giai_doan .=  '- '.$value_gd['name'].', ';
								}
							}
						}
						$cong_viec ='';
						if (!empty($cong_viec_dang_thuc_hien)) {
							foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
								if ($value_cv['project_id']==$value['task_id']) {
									$cong_viec .= '- '.$value_cv['name'].', ';
								}
							}
						}
						$cv_delay='';
						if (!empty($cong_viec_cham)) {
							foreach ($cong_viec_cham as $key => $value_delay) {
								if ($value_delay['project_id'] == $value['task_id']) {
									if ($value_delay['progress']==100) {
										if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
											$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}else{
										if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
											$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}
								}
							}
						}
						$table3->addRow();
						$table3->addCell(500)->addText($stt, $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($giai_doan), $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($cong_viec), $fontStyle);
						$table3->addCell(2000)->addText(round($value['progress'],2).'(%)', $fontR);
						$table3->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
						$stt++; 
					}
					if (strtotime($date_e)-$date_start>=0 && $date_pheduyet-strtotime($date_e)>=0){
						$giai_doan ='';
						if (!empty($giai_doan_dang_thuc_hien)) {
							foreach ($giai_doan_dang_thuc_hien as $key => $value_gd) {
								if ($value_gd['parent'] == $value['task_id']) {
									$giai_doan .=  '- '.$value_gd['name'].', ';
								}
							}
						}
						$cong_viec ='';
						if (!empty($cong_viec_dang_thuc_hien)) {
							foreach ($cong_viec_dang_thuc_hien as $key => $value_cv) {
								if ($value_cv['project_id']==$value['task_id']) {
									$cong_viec .= '- '.$value_cv['name'].', ';
								}
							}
						}
						$cv_delay='';
						if (!empty($cong_viec_cham)) {
							foreach ($cong_viec_cham as $key => $value_delay) {
								if ($value_delay['project_id'] == $value['task_id']) {
									if ($value_delay['progress']==100) {
										if ((strtotime($value_delay['date_finish'])-strtotime($value_delay['date_end']))>0) {
											$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}else{
										if ((strtotime($value_delay['date_end']) - strtotime(Date('Y-m-d')))<0) {
											$cv_delay .= '- '.$value_delay['name'].' ('.round($value_delay['progress'],2) .'%), ';
										}
									}
								}
							}
						}
						$table3->addRow();
						$table3->addCell(500)->addText($stt, $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($value['name_task']), $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($giai_doan), $fontStyle);
						$table3->addCell(2000)->addText(htmlspecialchars($cong_viec), $fontStyle);
						$table3->addCell(2000)->addText(round($value['progress'],2).'(%)', $fontR);
						$table3->addCell(2000)->addText(htmlspecialchars($cv_delay), $fontStyle);
						$stt++; 
					}
				}
			}
		}






		if ($check_tp=='THANG') {
			$section->addText('III. Các hoạt động khác ', null, 'hStyle');
			$section->addText('...', null, 'hStyle');
			$section->addText('IV. Phân tích đánh giá', null, 'hStyle');
		}else{
			$section->addText('II. Các hoạt động khác ', null, 'hStyle');
			$section->addText('...', null, 'hStyle');
			$section->addText('III. Phân tích đánh giá', null, 'hStyle');
		}


		$section->addText('1. Biểu đồ doanh thu, lợi nhuận', null, 'hStyle');

		$phpWord->addTableStyle('table4', $styleTable,null);
		$table4 = $section->addTable('table4');
		$table4->addRow();
		$table4->addCell(2000)->addText('Chỉ tiêu chính', $fontStyle_thead);
		$table4->addCell(2000)->addText('Tỷ trọng', $fontStyle_thead);
		$table4->addCell(2000)->addText('Tỷ trọng thành phần', $fontStyle_thead);
		$table4->addCell(2000)->addText('Thực tế', $fontStyle_thead);
		$table4->addCell(2000)->addText('Kế hoạch', $fontStyle_thead);
		$table4->addCell(2000)->addText('Thực tế/Kế hoạch', $fontStyle_thead);
		$table4->addCell(2000)->addText('Điểm', $fontStyle_thead);
		if ($check_tp=='THANG') {
			if ($month<=3) {
				$month = 1;
			}else if ($month>3&&$month<=6) {
				$month =2;
			}else if ($month>6 && $month<=9) {
				$month =3;
			}else if ($month>9 && $month <=12) {
				$month =4;
			}
		}
		$plan = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'plan','year'=>$year,'quater'=>$month))['dt'];
		if (!empty($plan)) {
			for ($i=0; $i < count($plan) ; $i++) { 
				if ($plan[$i]['location_id'] == $id_location) {
					$location_data = $plan[$i]['location_data'];
					$plan_tp = $location_data[1]['value'];
					$plan_cp = $location_data[2]['value'];
					$plan_ma = $location_data[3]['value'];
					$plan_tvk = $location_data[4]['value'];
					$sum_plan = $plan_tp +$plan_cp+$plan_ma+$plan_tvk;
				}
			}
		}
		$result = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>$month))['dt'];
		$density = parent::kpi_room_data_d7(array('select'=>'revenue','tp'=>'result','year'=>$year,'quater'=>$month))['density'];
		// echo "<pre>"; print_r($result); die();
		if (!empty($result)) {
			for ($i=0; $i < count($result) ; $i++) { 
				if ($result[$i]['location_id'] == $id_location) {
					$location_data = $result[$i]['location_data'];
					$result_tp = $location_data[1];
					$result_cp = $location_data[2];
					$result_ma = $location_data[3];
					$result_tvk = $location_data[4];
					$sum_result = $result_tp +$result_cp+$result_ma+$result_tvk;
				}
			}
		}

		if ($plan_tp>0) {
			$percent_tp = round(($result_tp/$plan_tp)*100,2);
		}else{
			$percent_tp =0;
		}
		if ($plan_cp>0) {
			$percent_cp = round(($result_cp/$plan_cp)*100,2);
		}else{
			$percent_cp =0;
		}
		if ($plan_ma>0) {
			$percent_ma = round(($result_ma/$plan_ma)*100,2);
		}else{
			$percent_ma =0;
		}
		if ($plan_tvk>0) {
			$percent_tvk = round(($result_tvk/$plan_tvk)*100,2);
		}else{
			$percent_tvk =0;
		}
		if ($sum_plan>0) {
			$sum_percent = round(($sum_result/$sum_plan)*100,2);
		}else{
			$sum_percent =0;
		}

		$table4->addRow();
		$table4->addCell(2000)->addText('Doanh thu', $fontStyle_thead);
		$table4->addCell(2000)->addText('20%', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addRow();
		$table4->addCell(2000)->addText('Doanh thu bảo lãnh, đại lý phát hành trái phiếu', $fontStyle_thead);
		$table4->addCell(2000)->addText('40%', $fontStyle);
		$table4->addCell(2000)->addText($density[1], $fontStyle);
		$table4->addCell(2000)->addText(number_format($result_tp), $fontStyle);
		$table4->addCell(2000)->addText(number_format($plan_tp), $fontStyle);
		$table4->addCell(2000)->addText($percent_tp, $fontStyle);
		$table4->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_tp)), $fontStyle);
		$table4->addRow();
		$table4->addCell(2000)->addText('Doanh thu bảo lãnh, đại lý phát hành cổ phiếu', $fontStyle_thead);
		$table4->addCell(2000)->addText('20%', $fontStyle);
		$table4->addCell(2000)->addText($density[2], $fontStyle);
		$table4->addCell(2000)->addText(number_format($result_cp), $fontStyle);
		$table4->addCell(2000)->addText(number_format($plan_cp), $fontStyle);
		$table4->addCell(2000)->addText($percent_cp, $fontStyle);
		$table4->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_cp)), $fontStyle);
		$table4->addRow();
		$table4->addCell(2000)->addText('Doanh thu MA', $fontStyle_thead);
		$table4->addCell(2000)->addText('20%', $fontStyle);
		$table4->addCell(2000)->addText($density[3], $fontStyle);
		$table4->addCell(2000)->addText(number_format($result_ma), $fontStyle);
		$table4->addCell(2000)->addText(number_format($plan_ma), $fontStyle);
		$table4->addCell(2000)->addText($percent_ma, $fontStyle);
		$table4->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_ma)), $fontStyle);
		$table4->addRow();
		$table4->addCell(2000)->addText('Doanh thu tư vấn khác', $fontStyle_thead);
		$table4->addCell(2000)->addText('20%', $fontStyle);
		$table4->addCell(2000)->addText($density[3], $fontStyle);
		$table4->addCell(2000)->addText(number_format($result_tvk), $fontStyle);
		$table4->addCell(2000)->addText(number_format($plan_tvk), $fontStyle);
		$table4->addCell(2000)->addText($percent_tvk, $fontStyle);
		$table4->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$percent_tvk)), $fontStyle);
		$table4->addRow();
		$table4->addCell(2000)->addText('Lợi nhuận', $fontStyle_thead);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addRow();
		$table4->addCell(2000)->addText('Tổng', $fontStyle_thead);
		$table4->addCell(2000)->addText('', $fontStyle);
		$table4->addCell(2000)->addText($density[1]+$density[2]+$density[3]+$density[4], $fontStyle);
		$table4->addCell(2000)->addText(number_format($sum_result), $fontStyle);
		$table4->addCell(2000)->addText(number_format($sum_plan), $fontStyle);
		$table4->addCell(2000)->addText($sum_percent, $fontStyle);
		$table4->addCell(2000)->addText(parent::convert_rate_d7(array('rate'=>$sum_percent)), $fontStyle);


		$rate = $this->Kpi_model->get_rate();

		$section->addText('* Ghi chú:', null, 'hStyle');
		$phpWord->addTableStyle('table5', $styleTable,null);
		$table5 = $section->addTable('table5');
		$table5->addRow();
		$table5->addCell(2000)->addText('Khung đánh giá', $fontStyle_thead);
		$table5->addCell(2000)->addText('Điểm', $fontStyle_thead);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Tỷ lệ TH/KH nhỏ hơn 70%', $fontStyle);
		$table5->addCell(2000)->addText($rate[0]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Từ 70% đến dưới 90%', $fontStyle);
		$table5->addCell(2000)->addText($rate[1]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Từ 90% đến dưới 110%', $fontStyle);
		$table5->addCell(2000)->addText($rate[2]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Từ 110% đến dưới 130%', $fontStyle);
		$table5->addCell(2000)->addText($rate[3]['point'], $fontStyle);
		$table5->addRow();
		$table5->addCell(2000)->addText('- Lớn hơn 130%', $fontStyle);
		$table5->addCell(2000)->addText($rate[4]['point'], $fontStyle);
		$section->addTextBreak();
		$section->addText('2. Biểu đồ năng lực', null, 'hStyle');
		$section->addText('...', null, 'hStyle');
		if ($check_tp=='THANG') {
			$section->addText('V. Thông tin các vấn đề phát sinh ', null, 'hStyle');
		}else{
			$section->addText('IV. Thông tin các vấn đề phát sinh ', null, 'hStyle');
		}

		$section->addText('1. Các vấn đề phát sinh đã xử lý', null, 'hStyle');
		$section->addText('...', null, 'hStyle');
		$section->addText('2. Các vấn đề phát sinh chưa xử lý', null, 'hStyle');
		$section->addText('...', null, 'hStyle');
		$section->addText('3. Các công việc quá hạn ', null, 'hStyle');
		$phpWord->addTableStyle('table6', $styleTable,null);
		$table6 = $section->addTable('table6');
		$table6->addRow();
		$table6->addCell(2000)->addText('STT', $fontStyle_thead);
		$table6->addCell(3000)->addText('Tên dự án', $fontStyle_thead);
		$table6->addCell(5000)->addText('Công việc', $fontStyle_thead);

		$arr_project1 = $arr_project2 = $arr_project3 = $arr_project= array();
		if (!empty($task_child_delay)) {
			foreach ($task_child_delay as $key => $value) {
				if ((strtotime($value['modified'])-strtotime($value['date_end']))>0)
				{
					$date = date_diff(date_create($value['modified']), date_create($value['date_end']));

				}elseif ((strtotime($value['date_finish']) - strtotime($value['date_end']))>0)
				{
					$date = date_diff(date_create($value['date_finish']), date_create($value['date_end']));
				}

				if ((int)$date->format('%m')>0) {
					$arr_project1[]= $value['project_id'];
				}elseif((int)$date->format('%y')>0){
					$arr_project2[]= $value['project_id'];
				}else{
					if ((int)$date->format('%d')>0) {
						$arr_project3[]= $value['project_id'];
					}
				}
			}
			$arr_project = array_unique(array_merge($arr_project1, $arr_project2,$arr_project3));

			$task_delay = $this->Contract->get_task($arr_project);
		}

		if (!empty($task_delay)) {
			$stt=1;
			foreach ($task_delay as $key => $val) {
				$table6->addRow();
				$table6->addCell(2000)->addText($stt, $fontStyle);
				$table6->addCell(3000)->addText(htmlspecialchars($val['name']), $fontStyle);
				
				$cv_delay='';
				if (!empty($task_child_delay)) {
					foreach ($task_child_delay as $key => $value) {
						if ($value['project_id']==$val['id']) {
							if ((strtotime($value['modified'])-strtotime($value['date_end']))>0)
							{
								$date = date_diff(date_create($value['modified']), date_create($value['date_end']));

							}elseif ((strtotime($value['date_finish']) - strtotime($value['date_end']))>0)
							{
								$date = date_diff(date_create($value['date_finish']), date_create($value['date_end']));
							}

							if ((int)$date->format('%m')>0) {
								if ((int)$date->format('%d')>0) {
									$cv_delay .= '- '.$value['name'].' ('.$date->format('%m tháng %d ngày').')';
								}else{
									$cv_delay .= '- '.$value['name'].' ('.$date->format('%m tháng').')';
								}
							}else{
								if ((int)$date->format('%d')>0) {
									$cv_delay .= '- '.$value['name'].' ('.$date->format('%d ngày').')';
								}
							}
						}
					} 
				}
				$table6->addCell(5000)->addText(htmlspecialchars($cv_delay), $fontStyle);
				$stt++;
			}
		}

		$section->addTextBreak();
		$code_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id())->code;
		$phpWord->addTableStyle('table_footer', NULL,null);
		$table_footer = $section->addTable('table_footer');
		$table_footer->addRow();
		$table_footer->addCell(6000)->addText('',NULL);
		$table_footer->addCell(4000)->addText('TRƯỞNG PHÒNG TVTCDN-'.mb_strtoupper($name_location), $fontStyle);

		$employee = $this->Employee->get_list_employees_by_location($id_location);

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